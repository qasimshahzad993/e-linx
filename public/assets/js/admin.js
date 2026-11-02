/* Use anywhere: Toast.show('success', 'Saved'); */
window.Toast = {
  show(type, msg, ms = 4500) {
    const toastType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
    if (typeof window.Toastify === 'function') {
      window.Toastify({
        text: String(msg),
        duration: ms,
        gravity: 'top',
        position: 'right',
        close: true,
        stopOnFocus: true,
        escapeMarkup: true,
        className: `elinx-toast elinx-toast-${toastType}`,
        ariaLive: toastType === 'error' ? 'assertive' : 'polite',
      }).showToast();
      return;
    }

    console.error('Toastify did not load; showing the message in the built-in notification fallback.');
    const fallback = document.getElementById('toast-fallback');
    if (!fallback) return;
    fallback.textContent = String(msg);
    fallback.dataset.type = toastType;
    fallback.hidden = false;
    window.clearTimeout(fallback.hideTimer);
    fallback.hideTimer = window.setTimeout(() => { fallback.hidden = true; }, ms);
  },
};

/* Messages queued by PHP flash() */
(window.__flashes || []).forEach(f => Toast.show(f.type, f.msg));

/* ---------- Blog writing helpers ---------- */
(() => {
  const editor = document.querySelector('[data-markdown-editor]');
  const toolbar = document.querySelector('.blog-markdown-toolbar');
  const stats = document.getElementById('blog-writing-stats');
  if (!editor || !toolbar || !stats) return;

  const updateStats = () => {
    const words = editor.value.trim().match(/\S+/gu)?.length || 0;
    const headings = editor.value.match(/^#{1,3}\s+\S/gm)?.length || 0;
    const readingTime = words ? Math.max(1, Math.ceil(words / 220)) : 0;
    stats.textContent = `${words} words · ${readingTime} min read · ${headings} section headings`;
  };

  toolbar.addEventListener('click', event => {
    const button = event.target.closest('button[data-markdown-action]');
    if (!button) return;

    const action = button.dataset.markdownAction;
    const start = editor.selectionStart;
    const end = editor.selectionEnd;
    const selected = editor.value.slice(start, end);
    const prefix = {
      heading: '## ',
      subheading: '### ',
      bullet: '- ',
      number: '1. ',
      quote: '> ',
    }[action];
    let replacement;
    let selectionStart = start;
    let selectionEnd;

    if (prefix) {
      replacement = selected
        ? selected.split('\n').map(line => prefix + line).join('\n')
        : prefix + (action === 'heading' ? 'Section heading' : action === 'subheading' ? 'Subheading' : 'List item');
      selectionStart = start;
      selectionEnd = start + replacement.length;
    } else if (action === 'bold') {
      const text = selected || 'bold text';
      replacement = `**${text}**`;
      selectionStart = start + 2;
      selectionEnd = selectionStart + text.length;
    } else if (action === 'link') {
      const text = selected || 'link text';
      replacement = `[${text}](https://example.com)`;
      selectionStart = start + 1;
      selectionEnd = selectionStart + text.length;
    }

    if (!replacement) return;
    editor.setRangeText(replacement, start, end, 'end');
    editor.focus();
    editor.setSelectionRange(selectionStart, selectionEnd);
    editor.dispatchEvent(new Event('input', { bubbles: true }));
  });

  editor.addEventListener('input', updateStats);
  updateStats();
})();

/* ---------- Profile dropdown ---------- */
(() => {
  const btn = document.getElementById('profileBtn'), menu = document.getElementById('profileMenu');
  if (!btn) return;
  const set = open => { menu.classList.toggle('open', open); btn.setAttribute('aria-expanded', open); };
  btn.addEventListener('click', e => { e.stopPropagation(); set(!menu.classList.contains('open')); });
  document.addEventListener('click', e => { if (!menu.contains(e.target)) set(false); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') set(false); });
})();

/* ---------- Mobile sidebar ---------- */
(() => {
  const sb = document.getElementById('sidebar'), sc = document.getElementById('scrim'), mb = document.getElementById('menuBtn');
  if (!mb) return;
  const toggle = open => { sb.classList.toggle('open', open); sc.classList.toggle('show', open); };
  mb.onclick = () => toggle(!sb.classList.contains('open'));
  sc.onclick = () => toggle(false);
})();

/* ---------- Confirm dialog ----------
   <form method="post" data-confirm="Delete this project?"> ... </form>      */
(() => {
  const modal = document.getElementById('confirmModal');
  if (!modal) return;
  const text = document.getElementById('confirmText');
  let pending = null;
  const close = () => { modal.hidden = true; pending = null; };
  document.addEventListener('submit', e => {
    const f = e.target.closest('form[data-confirm]');
    if (!f) return;
    e.preventDefault();
    pending = f; text.textContent = f.dataset.confirm;
    modal.hidden = false; document.getElementById('confirmNo').focus();
  });
  document.getElementById('confirmYes').onclick = () => { const f = pending; close(); f && f.submit(); };
  document.getElementById('confirmNo').onclick = close;
  modal.addEventListener('click', e => { if (e.target === modal) close(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });
})();
