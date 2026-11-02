/* ---------- Toasts ----------
   Use anywhere: Toast.show('success', 'Saved');
   Types: success | error | warning | info                */
const Toast = {
  icons: { success:'bi-check-circle-fill', error:'bi-x-octagon-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill' },
  show(type, msg, ms = 4500) {
    const box = document.getElementById('toasts');
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.setAttribute('role', type === 'error' ? 'alert' : 'status');
    t.style.setProperty('--dur', ms + 'ms');
    t.innerHTML = '<i class="bi t-ico ' + (this.icons[type] || this.icons.info) + '"></i><p></p>' +
                  '<button aria-label="Dismiss"><i class="bi bi-x-lg"></i></button><span class="bar"></span>';
    t.querySelector('p').textContent = msg;           // textContent = safe from XSS
    const close = () => { t.classList.add('out'); setTimeout(() => t.remove(), 200); };
    t.querySelector('button').onclick = close;
    box.appendChild(t);
    setTimeout(close, ms);
  }
};

/* Messages queued by PHP flash() */
(window.__flashes || []).forEach(f => Toast.show(f.type, f.msg));

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
