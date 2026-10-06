document.addEventListener('DOMContentLoaded', async function () {
  const config = window.wtTranslationMenu;
  if (!config) return;
  const links = Array.from(document.querySelectorAll('a[href*="controller=AdminWtTranslation"]'));
  if (!links.length) return;
  window.wtUpdateTranslationBadge = function (count) {
    for (const link of links) {
      let badge = link.querySelector('.wt-translation-badge');
      if (count <= 0) {
        if (badge) badge.remove();
        continue;
      }
      if (!badge) {
        badge = document.createElement('span');
        badge.className = 'wt-translation-badge';
        link.appendChild(badge);
      }
      badge.textContent = String(count);
      badge.setAttribute('aria-label', `${count} produits à traduire`);
      badge.title = `${count} produits à traduire`;
    }
    try {
      sessionStorage.setItem(config.cacheKey, JSON.stringify({ count, time: Date.now() }));
    } catch (_) { /* Counting still works when browser storage is unavailable. */ }
  };
  const panel = document.getElementById('wt-translation');
  if (panel) {
    window.wtUpdateTranslationBadge(Number(panel.dataset.count));
    return;
  }
  try {
    const cached = JSON.parse(sessionStorage.getItem(config.cacheKey) || 'null');
    if (cached && Date.now() - cached.time < 60000) {
      window.wtUpdateTranslationBadge(cached.count);
      // Keep the original expiry: navigation must not prolong a stale count.
      sessionStorage.setItem(config.cacheKey, JSON.stringify(cached));
      return;
    }
  } catch (_) { /* Read the current count instead. */ }
  try {
    const url = new URL(config.url, window.location.href);
    url.searchParams.set('ajax', '1');
    url.searchParams.set('action', 'PendingCount');
    const response = await fetch(url, { credentials: 'same-origin' });
    if (!response.ok) return;
    const result = await response.json();
    if (result.ok) window.wtUpdateTranslationBadge(result.count);
  } catch (_) { /* A counter failure must not interrupt Back Office navigation. */ }
});
