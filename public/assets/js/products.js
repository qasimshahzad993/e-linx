(() => {
    const search = document.getElementById('product-search');
    const grid = document.getElementById('product-grid');

    if (!search || !grid) return;

    const filters = [...document.querySelectorAll('.product-filter')];
    const cards = [...grid.querySelectorAll('.product-item')];
    const clear = document.getElementById('clear-product-search');
    const noResults = document.getElementById('product-no-results');
    let activeCategory = 'all';

    const update = () => {
        const query = search.value.trim().toLowerCase();
        let visible = 0;

        cards.forEach((card) => {
            const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            const matchesSearch = !query || card.dataset.search.includes(query);
            card.hidden = !(matchesCategory && matchesSearch);
            if (!card.hidden) visible += 1;
        });

        clear.hidden = !query;
        noResults.hidden = visible > 0;
    };

    filters.forEach((filter) => filter.addEventListener('click', () => {
        activeCategory = filter.dataset.filter;
        filters.forEach((item) => {
            const active = item === filter;
            item.classList.toggle('active', active);
            item.setAttribute('aria-pressed', String(active));
        });
        update();
    }));

    search.addEventListener('input', update);
    clear.addEventListener('click', () => {
        search.value = '';
        search.focus();
        update();
    });

    document.getElementById('reset-product-filters')?.addEventListener('click', () => {
        search.value = '';
        filters[0]?.click();
        update();
    });
})();
