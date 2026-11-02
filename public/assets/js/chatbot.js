(() => {
  const widget = document.querySelector('[data-chatbot]');
  if (!widget) return;

  const toggle = widget.querySelector('[data-chatbot-toggle]');
  const panel = widget.querySelector('[data-chatbot-panel]');
  const close = widget.querySelector('[data-chatbot-close]');
  const form = widget.querySelector('[data-chatbot-form]');
  const input = widget.querySelector('[data-chatbot-input]');
  const send = widget.querySelector('[data-chatbot-send]');
  const messages = widget.querySelector('[data-chatbot-messages]');
  const status = widget.querySelector('[data-chatbot-status]');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  const history = [];

  const addMessage = (role, content) => {
    const item = document.createElement('div');
    item.className = `chatbot-message chatbot-message-${role}`;
    item.textContent = content;
    messages.append(item);
    messages.scrollTop = messages.scrollHeight;
    return item;
  };

  const openChat = () => {
    panel.hidden = false;
    toggle.setAttribute('aria-expanded', 'true');
    input.focus();
  };

  const closeChat = () => {
    panel.hidden = true;
    toggle.setAttribute('aria-expanded', 'false');
    toggle.focus();
  };

  toggle.addEventListener('click', () => {
    if (panel.hidden) openChat();
    else closeChat();
  });

  close.addEventListener('click', closeChat);

  if (widget.dataset.chatbotConfigured !== 'true') {
    status.textContent = 'AI replies are not connected yet. Please contact us by WhatsApp or email.';
    input.disabled = true;
    send.disabled = true;
    input.placeholder = 'AI setup is required';
    return;
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const content = input.value.trim();
    if (!content || send.disabled) return;

    history.push({ role: 'user', content });
    addMessage('user', content);
    input.value = '';
    input.disabled = true;
    send.disabled = true;
    status.textContent = 'e-Linx assistant is replying…';
    const pending = addMessage('assistant', 'Thinking…');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ messages: history.slice(-10) }),
      });
      const result = await response.json();

      if (!response.ok) throw new Error(result.message || 'The assistant could not reply. Please try again.');

      pending.textContent = result.reply;
      history.push({ role: 'assistant', content: result.reply });
      status.textContent = 'Replies are AI-generated. Please confirm important details with our team.';
    } catch (error) {
      pending.remove();
      history.pop();
      addMessage('assistant', error.message || 'The assistant is unavailable right now. Please contact our team.');
      status.textContent = 'Reply failed. Please try again or contact our team.';
    } finally {
      input.disabled = false;
      send.disabled = false;
      input.focus();
    }
  });
})();
