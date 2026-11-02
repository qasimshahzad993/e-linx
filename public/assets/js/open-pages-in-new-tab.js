document.querySelectorAll('a[href]').forEach(link => {
    const href = link.getAttribute('href');

    if (!href || href.startsWith('#') || /^(mailto|tel|javascript):/i.test(href) || link.hasAttribute('target')) {
        return;
    }

    link.target = '_blank';
    link.relList.add('noopener');
});
