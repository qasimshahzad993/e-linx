<?php

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php docs/generate-pdf.php <source.md> <output.pdf>\n");
    exit(1);
}

$lines = file($argv[1], FILE_IGNORE_NEW_LINES);
$pages = [];
$stream = '';
$y = 758;
$pageNumber = 1;
$inCode = false;

function pdfText(string &$stream, string $text, float $x, float $y, int $size = 9, string $font = 'F1', string $color = '0.10 0.15 0.22'): void
{
    $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
    $text = str_replace(["\\", '(', ')', "\r", "\n"], ["\\\\", '\(', '\)', '', ''], $text);
    $stream .= "BT /$font $size Tf $color rg 1 0 0 1 $x $y Tm ($text) Tj ET\n";
}

function startPdfPage(string &$stream, int &$y, int &$pageNumber): void
{
    $stream = "0.035 0.055 0.095 rg 0 782 595 60 re f\n0.11 0.73 0.93 rg 0 782 6 60 re f\n";
    pdfText($stream, 'e-LINX TECHNOLOGY', 44, 813, 10, 'F2', '1 1 1');
    pdfText($stream, 'Website and Admin User Documentation', 44, 796, 8, 'F1', '0.72 0.80 0.89');
    $stream .= "0.84 0.88 0.92 RG 44 43 m 551 43 l S\n";
    pdfText($stream, 'e-Linx | Updated 12 October 2026', 44, 27, 8, 'F1', '0.38 0.45 0.54');
    pdfText($stream, 'Page '.$pageNumber, 515, 27, 8, 'F1', '0.38 0.45 0.54');
    $y = 758;
    $pageNumber++;
}

function newPdfPage(array &$pages, string &$stream, int &$y, int &$pageNumber): void
{
    $pages[] = $stream;
    startPdfPage($stream, $y, $pageNumber);
}

function wrapPdfText(string $text, int $limit): array
{
    return explode("\n", wordwrap($text, $limit, "\n", false));
}

startPdfPage($stream, $y, $pageNumber);

foreach ($lines as $line) {
    $trimmed = trim($line);

    if ($trimmed === '') {
        $y -= 5;
        continue;
    }

    if (str_starts_with($trimmed, '```')) {
        $inCode = ! $inCode;
        $y -= 4;
        continue;
    }

    if (preg_match('/^## ([478])\./', $trimmed) && $y < 700) {
        newPdfPage($pages, $stream, $y, $pageNumber);
    }

    if (preg_match('/^# (.+)$/', $trimmed, $match)) {
        pdfText($stream, $match[1], 44, $y, 22, 'F2', '0.04 0.12 0.23');
        $y -= 25;
        $stream .= "0.11 0.73 0.93 rg 44 ".($y + 4)." 95 3 re f\n";
        continue;
    }

    if (preg_match('/^## (.+)$/', $trimmed, $match)) {
        if ($y < 95) {
            newPdfPage($pages, $stream, $y, $pageNumber);
        }
        $y -= 6;
        pdfText($stream, $match[1], 44, $y, 13, 'F2', '0.04 0.20 0.35');
        $y -= 17;
        $stream .= "0.11 0.73 0.93 RG 44 $y m 551 $y l S\n";
        $y -= 8;
        continue;
    }

    if (preg_match('/^### (.+)$/', $trimmed, $match)) {
        if ($y < 92) {
            newPdfPage($pages, $stream, $y, $pageNumber);
        }
        pdfText($stream, $match[1], 44, $y, 10, 'F2', '0.05 0.27 0.42');
        $y -= 14;
        continue;
    }

    $prefix = '';
    if (str_starts_with($trimmed, '|')) {
        if (str_contains($trimmed, '---')) {
            continue;
        }
        $cells = array_map('trim', explode('|', trim($trimmed, '|')));
        $text = implode('  -  ', array_filter($cells, fn ($cell) => $cell !== ''));
    } elseif (preg_match('/^[-*] (.+)$/', $trimmed, $match)) {
        $text = $match[1];
        $prefix = '- ';
    } elseif (preg_match('/^(\d+)\. (.+)$/', $trimmed, $match)) {
        $text = $match[2];
        $prefix = $match[1].'. ';
    } else {
        $text = str_replace('**', '', $trimmed);
    }

    if ($inCode) {
        $text = $trimmed;
        if ($y < 80) {
            newPdfPage($pages, $stream, $y, $pageNumber);
        }
        $stream .= "0.94 0.96 0.98 rg 44 ".($y - 3)." 507 13 re f\n";
        pdfText($stream, $text, 52, $y, 8, 'F3', '0.10 0.22 0.34');
        $y -= 14;
        continue;
    }

    $limit = str_starts_with($prefix, '-') ? 94 : (str_contains($prefix, '.') ? 91 : 100);
    foreach (wrapPdfText($text, $limit) as $index => $part) {
        if ($y < 76) {
            newPdfPage($pages, $stream, $y, $pageNumber);
        }
        $x = 44;
        if ($index === 0 && $prefix !== '') {
            pdfText($stream, rtrim($prefix), $x, $y, 9, 'F2', '0.03 0.52 0.70');
            $x += strlen($prefix) * 5.2;
        } elseif ($index > 0 && $prefix !== '') {
            $x += strlen($prefix) * 5.2;
        }
        pdfText($stream, $part, $x, $y, 9, 'F1', '0.10 0.15 0.22');
        $y -= 12;
    }
}

$pages[] = $stream;
$objects = [
    1 => '<< /Type /Catalog /Pages 2 0 R >>',
    3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
    4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
    5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Courier /Encoding /WinAnsiEncoding >>',
];
$pageReferences = [];

foreach ($pages as $index => $content) {
    $pageObject = 6 + $index * 2;
    $contentObject = $pageObject + 1;
    $pageReferences[] = "$pageObject 0 R";
    $objects[$pageObject] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R >> >> /Contents $contentObject 0 R >>";
    $objects[$contentObject] = '<< /Length '.strlen($content).">>\nstream\n".$content."endstream";
}

$objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageReferences).'] /Count '.count($pages).' >>';
ksort($objects);
$pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
$offsets = [0];

foreach ($objects as $number => $object) {
    $offsets[$number] = strlen($pdf);
    $pdf .= "$number 0 obj\n$object\nendobj\n";
}

$xref = strlen($pdf);
$objectCount = max(array_keys($objects)) + 1;
$pdf .= "xref\n0 $objectCount\n0000000000 65535 f \n";

for ($number = 1; $number < $objectCount; $number++) {
    $pdf .= sprintf('%010d 00000 n ', $offsets[$number] ?? 0)."\n";
}

$pdf .= "trailer\n<< /Size $objectCount /Root 1 0 R >>\nstartxref\n$xref\n%%EOF\n";

if (file_put_contents($argv[2], $pdf) === false) {
    fwrite(STDERR, "Could not write PDF: {$argv[2]}\n");
    exit(1);
}

echo 'Created '.$argv[2].' ('.count($pages).' pages, '.strlen($pdf)." bytes)\n";
