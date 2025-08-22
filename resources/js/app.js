import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Notifications unread count polling
document.addEventListener('DOMContentLoaded', () => {
  const badges = Array.from(document.querySelectorAll('#unread-badge, #unread-badge-mobile'));
  if (badges.length === 0) return;

  // Use the first badge's URL
  const url = badges[0].getAttribute('data-url');
  if (!url) return;

  const render = (count) => {
    for (const b of badges) {
      b.textContent = String(count);
    }
  };

  const updateBadge = async () => {
    try {
      const response = await window.axios.get(url, { headers: { 'Accept': 'application/json' } });
      const count = Number(response?.data?.count ?? 0);
      render(count);
    } catch (e) {
      // ignore
    }
  };

  updateBadge();
  setInterval(updateBadge, 10000); // every 10s
});
