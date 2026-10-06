document.addEventListener('DOMContentLoaded', function () {
  const panel = document.getElementById('wt-translation');
  const button = document.getElementById('wt-translate');
  if (!panel || !button) return;
  const error = document.getElementById('wt-error');
  const progress = document.getElementById('wt-progress');
  button.addEventListener('click', async function () {
    button.disabled = true;
    error.hidden = true;
    progress.hidden = false;
    const rows = Array.from(panel.querySelectorAll('tr[data-product]'));
    let fields = 0;
    let done = 0;
    try {
      for (const row of rows) {
        progress.textContent = `Traduction : ${done}/${rows.length} produits, ${fields} champs enregistrés.`;
        const body = new URLSearchParams({ ajax: '1', action: 'Translate', id_product: row.dataset.product });
        const response = await fetch(panel.dataset.url, { method: 'POST', body, credentials: 'same-origin' });
        if (!response.ok) throw new Error(`Erreur serveur (HTTP ${response.status}).`);
        const result = await response.json();
        if (!result.ok) throw new Error(result.error || 'La traduction a échoué.');
        fields += result.fields;
        done++;
        row.remove();
      }
      progress.textContent = `Terminé : ${done} produits traités, ${fields} champs traduits. Actualisation…`;
      window.location.reload();
    } catch (e) {
      error.textContent = `${e.message} ${fields} champs ont déjà été enregistrés. Relancez pour reprendre les champs manquants.`;
      error.hidden = false;
      progress.textContent = `${done}/${rows.length} produits traités.`;
      button.disabled = false;
    }
  });
});
