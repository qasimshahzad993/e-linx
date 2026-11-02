<?php

declare(strict_types=1);

$localOrigin = rtrim(getenv('STATIC_EXPORT_ORIGIN') ?: 'http://127.0.0.1:8000', '/');
$pagesBaseUrl = rtrim(getenv('GITHUB_PAGES_BASE_URL') ?: 'https://qasimshahzad993.github.io/e-linx', '/');
$outputDirectory = dirname(__DIR__);
$localOriginParts = parse_url($localOrigin);

if (! is_array($localOriginParts) || ! isset($localOriginParts['scheme'], $localOriginParts['host'])) {
    throw new InvalidArgumentException('STATIC_EXPORT_ORIGIN must be an absolute URL.');
}

$request = static function (string $url): string {
    $context = stream_context_create([
        'http' => [
            'follow_location' => 1,
            'ignore_errors' => true,
            'timeout' => 30,
        ],
    ]);

    $content = file_get_contents($url, false, $context);

    if ($content === false) {
        throw new RuntimeException("Unable to fetch {$url}.");
    }

    $status = 0;

    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $header, $matches)) {
            $status = (int) $matches[1];
        }
    }

    if ($status < 200 || $status >= 300) {
        throw new RuntimeException("Request to {$url} returned HTTP {$status}.");
    }

    return $content;
};

$sitemap = $request($localOrigin.'/sitemap.xml');

if (! preg_match_all('/<loc>(.*?)<\/loc>/s', $sitemap, $matches) || $matches[1] === []) {
    throw new RuntimeException('The local sitemap contains no public pages to export.');
}

$rewriteUrl = static function (string $url) use ($localOriginParts, $localOrigin, $pagesBaseUrl): string {
    $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5);

    if ($url === '' || str_starts_with($url, '#') || preg_match('/^(mailto|tel|javascript|data):/i', $url)) {
        return $url;
    }

    $parts = parse_url($url);

    if (! is_array($parts)) {
        return $url;
    }

    if (isset($parts['host'])) {
        $sameHost = strtolower($parts['host']) === strtolower($localOriginParts['host']);
        $sameScheme = strtolower($parts['scheme'] ?? '') === strtolower($localOriginParts['scheme']);
        $loopbackHosts = ['127.0.0.1', '::1', 'localhost'];
        $bothLoopback = in_array(strtolower($parts['host']), $loopbackHosts, true)
            && in_array(strtolower($localOriginParts['host']), $loopbackHosts, true);

        if ((! $sameHost || ! $sameScheme) && ! $bothLoopback) {
            return $url;
        }
    } elseif (! str_starts_with($url, '/')) {
        return $url;
    }

    $path = $parts['path'] ?? '/';

    if (str_starts_with($path, '/assets/')) {
        $path = '/public'.$path;
    } elseif ($path === '/favicon.svg') {
        $path = '/public/favicon.svg';
    } elseif (str_starts_with($path, '/storage/')) {
        $path = '/pages-assets'.$path;
    }

    return $pagesBaseUrl.$path
        .(isset($parts['query']) ? '?'.$parts['query'] : '')
        .(isset($parts['fragment']) ? '#'.$parts['fragment'] : '');
};

$savedPages = [];
$storageAssets = [];

foreach ($matches[1] as $location) {
    $pageUrl = html_entity_decode(trim($location), ENT_QUOTES | ENT_XML1);
    $pageParts = parse_url($pageUrl);
    $path = $pageParts['path'] ?? '/';

    if (! is_array($pageParts) || $path === '') {
        throw new RuntimeException("Invalid sitemap URL: {$pageUrl}");
    }

    $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn (string $part): bool => $part !== ''));
    $decodedSegments = array_map('rawurldecode', $segments);

    foreach ($decodedSegments as $segment) {
        if ($segment === '.' || $segment === '..' || str_contains($segment, '/') || str_contains($segment, '\\')) {
            throw new RuntimeException("Unsafe sitemap path: {$path}");
        }
    }

    $relativePath = $decodedSegments === []
        ? 'index.html'
        : implode(DIRECTORY_SEPARATOR, $decodedSegments).DIRECTORY_SEPARATOR.'index.html';
    $destination = $outputDirectory.DIRECTORY_SEPARATOR.$relativePath;
    $pagePath = '/'.implode('/', $segments);
    $html = $request($localOrigin.($pagePath === '/' ? '/' : $pagePath));
    $email = preg_match('/href=["\']mailto:([^"\']+)["\']/i', $html, $emailMatch)
        ? html_entity_decode($emailMatch[1], ENT_QUOTES | ENT_HTML5)
        : '';
    if (preg_match_all('~(?:https?://[^/]+)?/storage/([^"\'?#\s<>]+)~i', $html, $storageMatches)) {
        foreach ($storageMatches[1] as $storagePath) {
            $storageAssets[] = rawurldecode($storagePath);
        }
    }

    $html = preg_replace('/<meta\s+name=["\']csrf-token["\'][^>]*>\s*/i', '', $html);
    $html = preg_replace('/<input\b(?=[^>]*\bname=["\']_token["\'])[^>]*>\s*/i', '', $html);
    $html = preg_replace_callback(
        '/\b(href|src|action|formaction|poster|content)\s*=\s*(["\'])(.*?)\2/i',
        static function (array $attribute) use ($rewriteUrl): string {
            return $attribute[1].'='.$attribute[2].htmlspecialchars(
                $rewriteUrl($attribute[3]),
                ENT_QUOTES | ENT_HTML5,
            ).$attribute[2];
        },
        $html,
    );

    if (! is_string($html)) {
        throw new RuntimeException("Unable to rewrite page links for {$pageUrl}.");
    }

    $html = str_replace(
        [
            str_replace('/', '\\/', $localOrigin.'/assets/'),
            str_replace('/', '\\/', $localOrigin.'/storage/'),
            $localOrigin.'/assets/',
            $localOrigin.'/storage/',
            $localOrigin,
            str_replace('/', '\\/', $localOrigin),
        ],
        [
            str_replace('/', '\\/', $pagesBaseUrl.'/public/assets/'),
            str_replace('/', '\\/', $pagesBaseUrl.'/pages-assets/storage/'),
            $pagesBaseUrl.'/public/assets/',
            $pagesBaseUrl.'/pages-assets/storage/',
            $pagesBaseUrl,
            str_replace('/', '\\/', $pagesBaseUrl),
        ],
        $html,
    );

    $emailLink = $email === ''
        ? ''
        : ' Contact us at <a href="mailto:'.htmlspecialchars($email, ENT_QUOTES | ENT_HTML5).'">'
            .htmlspecialchars($email, ENT_QUOTES | ENT_HTML5).'</a>.';
    $banner = '<div class="alert alert-info text-center mb-0 rounded-0" role="status">'
        .'GitHub Pages static preview: forms and admin actions need the live Laravel backend.'
        .$emailLink.'</div>';
    $html = preg_replace('/(<body\b[^>]*>)/i', '$1'.$banner, $html, 1, $bodyCount);
    $staticFormHandler = <<<'HTML'
<script>
document.addEventListener('submit', event => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.method.toLowerCase() !== 'post') return;
    event.preventDefault();
    event.stopImmediatePropagation();
    let notice = form.querySelector('[data-static-preview-notice]');
    if (!notice) {
        notice = document.createElement('p');
        notice.className = 'alert alert-warning mt-3';
        notice.dataset.staticPreviewNotice = '';
        notice.setAttribute('role', 'status');
        form.append(notice);
    }
    notice.textContent = 'This GitHub Pages preview cannot submit forms. Please use the email link above.';
}, true);
</script>
HTML;
    $html = preg_replace('/<\/body>/i', $staticFormHandler.'</body>', $html, 1, $bodyEndCount);

    if ($bodyCount !== 1 || $bodyEndCount !== 1 || ! is_string($html)) {
        throw new RuntimeException("The response for {$pageUrl} is not a complete HTML page.");
    }

    if (! is_dir(dirname($destination)) && ! mkdir(dirname($destination), 0775, true) && ! is_dir(dirname($destination))) {
        throw new RuntimeException("Unable to create the output folder for {$pageUrl}.");
    }

    if (file_put_contents($destination, $html) === false) {
        throw new RuntimeException("Unable to write the exported page for {$pageUrl}.");
    }

    $savedPages[] = $relativePath;
}

if ($storageAssets !== []) {
    $storageRoot = realpath($outputDirectory.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'storage');

    if ($storageRoot === false) {
        throw new RuntimeException('Public storage assets are referenced but public/storage does not exist.');
    }

    $storageRootPrefix = rtrim($storageRoot, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

    foreach (array_unique($storageAssets) as $storagePath) {
        $segments = explode('/', $storagePath);

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..' || str_contains($segment, '\\')) {
                throw new RuntimeException("Unsafe public storage path: {$storagePath}");
            }
        }

        $source = realpath($storageRoot.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $segments));

        if ($source === false || ! is_file($source) || ! str_starts_with($source, $storageRootPrefix)) {
            throw new RuntimeException("A published page references a missing public storage file: {$storagePath}");
        }

        $destination = $outputDirectory.DIRECTORY_SEPARATOR.'pages-assets'.DIRECTORY_SEPARATOR.'storage'
            .DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $segments);

        if (! is_dir(dirname($destination)) && ! mkdir(dirname($destination), 0775, true) && ! is_dir(dirname($destination))) {
            throw new RuntimeException("Unable to create the storage output folder for {$storagePath}.");
        }

        if (! copy($source, $destination)) {
            throw new RuntimeException("Unable to copy the published storage file: {$storagePath}");
        }
    }
}

if (file_put_contents($outputDirectory.DIRECTORY_SEPARATOR.'.nojekyll', '') === false) {
    throw new RuntimeException('Unable to write the GitHub Pages .nojekyll marker.');
}

fwrite(STDOUT, 'Exported '.count($savedPages)." public pages:\n".implode("\n", $savedPages)."\n");
