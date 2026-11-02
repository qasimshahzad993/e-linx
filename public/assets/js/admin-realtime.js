(() => {
  const config = window.__realtimeConfig;
  const count = document.getElementById('notification-count');
  if (!config || !count) return;

  const storageKey = 'elinx:last-notified-lead-id';
  let lastId = Number(config.latestId) || 0;
  try {
    const savedId = Number(localStorage.getItem(storageKey));
    if (Number.isSafeInteger(savedId) && savedId > 0) lastId = Math.min(savedId, lastId);
    else localStorage.setItem(storageKey, String(lastId));
  } catch (error) {
    console.warn('Live lead notifications could not access local storage.', error);
  }

  const updateCount = (value) => {
    const total = Number(value) || 0;
    count.textContent = total > 99 ? '99+' : String(total);
    count.hidden = total === 0;
  };

  let failed = false;
  const poll = async () => {
    if (document.hidden) return;
    try {
      const response = await fetch(`${config.endpoint}?after=${encodeURIComponent(lastId)}`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
        cache: 'no-store',
      });
      if (!response.ok) throw new Error(`Notification request failed (${response.status}).`);
      const data = await response.json();
      updateCount(data.new_count);
      for (const lead of data.leads) {
        lastId = Math.max(lastId, Number(lead.id) || lastId);
        if (window.Toast) {
          const kind = lead.type === 'quote' ? 'quote request' : 'contact message';
          Toast.show('info', `New ${kind} from ${lead.name}${lead.subject ? ` · ${lead.subject}` : ''}`);
        }
      }
      try {
        localStorage.setItem(storageKey, String(lastId));
      } catch (error) {
        console.warn('Live lead notifications could not save their cursor.', error);
      }
      failed = false;
    } catch (error) {
      console.error('Live lead notification polling failed.', error);
      if (!failed && window.Toast) Toast.show('error', 'Live notifications are temporarily unavailable.');
      failed = true;
    }
  };

  updateCount(config.newCount);
  window.setInterval(poll, 5000);
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) poll();
  });
})();
