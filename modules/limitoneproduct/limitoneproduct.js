document.addEventListener('DOMContentLoaded', function () {
  console.log('Script LimitOne chargé', limitOneRefs);


// --- PAGE PRODUIT ---
const idElem = document.querySelector('input[name="id_product"]');
if (idElem) {
  //const productId = parseInt(idElem.value, 10);
  const productId = idElem.value.trim(); // reste une string

  console.log('ID produit détecté sur la page produit :', productId);

  if (limitOneRefs.includes(productId)) {
    console.log('Produit limité détecté :', productId);

    const qtyInput = document.querySelector('input[name="qty"]');
    const addButton = document.querySelector('.add-to-cart');

    if (qtyInput) {
      qtyInput.value = 1;
      qtyInput.setAttribute('readonly', true);
      qtyInput.style.cursor = 'not-allowed';
      qtyInput.style.pointerEvents = 'none'; // ⚡ bloque toute interaction


      // Supprime les boutons TouchSpin après init
      setTimeout(() => {
        const wrapper = qtyInput.closest('.bootstrap-touchspin');
        if (wrapper) {
          const upBtn = wrapper.querySelector('.bootstrap-touchspin-up');
          const downBtn = wrapper.querySelector('.bootstrap-touchspin-down');
          if (upBtn) upBtn.remove();
          if (downBtn) downBtn.remove();
        }
      }, 50); // délai un peu plus long si nécessaire
    }

    if (addButton) {
      prestashop.on('updateCart', function (event) {
        if (event && event.reason && event.reason.linkAction === 'add-to-cart') {
          // On attend que le produit soit vraiment rechargé
          prestashop.on('updatedProduct', function () {
            const qtyInput = document.querySelector('input[name="qty"]');
            if (qtyInput) {
              qtyInput.value = 0;
              qtyInput.setAttribute('readonly', true);
              qtyInput.style.cursor = 'not-allowed';
                qtyInput.style.pointerEvents = 'none'; // ⚡ bloque toute interaction

              console.log('Produit limité ajouté — quantité bloquée à 0 après reload.');
            }
          });
        }
      });
    }

      // ✅ Vérifie si le produit est déjà dans le panier au chargement (via HTML)
      const cartUrl = prestashop?.urls?.pages?.cart;
      if (cartUrl) {
        console.log('Vérification panier via :', cartUrl);
        fetch(cartUrl + '?ajax=1&action=refresh')
          .then(res => res.json())
          .then(data => {
            console.log('Réponse reçue (HTML) :', data);

            if (!data || !data.cart_detailed) {
              console.warn('⚠️ Pas de "cart_detailed" dans la réponse.');
              return;
            }

            const cartHtml = data.cart_detailed;

            // --- Nouvelle portion de code pour parser les IDs en string ---
            const parser = new DOMParser();
            const doc = parser.parseFromString(cartHtml, 'text/html');
            const cartInputs = doc.querySelectorAll('input[data-product-id]');
            const cartProductIds = Array.from(cartInputs).map(input => input.dataset.productId); // string
            const isAlreadyInCart = cartProductIds.includes(productId); // comparaison string → string

            console.log(`Produits dans le panier (IDs) :`, cartProductIds);
            console.log(`Recherche de l'ID "${productId}" dans le panier → ${isAlreadyInCart ? 'TROUVÉ' : 'non trouvé'}`);

            if (isAlreadyInCart) {
              const qtyInput = document.querySelector('input[name="qty"]');
              if (qtyInput) {
                qtyInput.value = 0;
                qtyInput.setAttribute('readonly', true);
                qtyInput.style.cursor = 'not-allowed';
                  qtyInput.style.pointerEvents = 'none'; // ⚡ bloque toute interaction

                console.log('✅ Produit limité déjà présent dans le panier — input bloqué à 0.');
              }
            }
          })
          .catch(err => console.warn('Erreur vérification panier :', err));
      } else {
        console.warn('Impossible de récupérer prestashop.urls.pages.cart');
      }


  }
}

// PAGE PANIER

function applyLimitOneOnCart() {
  const cartRows = document.querySelectorAll('.cart-item');
  if (cartRows.length === 0) return;

  cartRows.forEach(row => {
    const qtyInput = row.querySelector('input.js-cart-line-product-quantity');
    if (!qtyInput) return;

    const productIdInCart = qtyInput.dataset.productId?.trim();
    if (!productIdInCart) return;

    if (limitOneRefs.includes(productIdInCart)) {
      // Bloque le champ quantité
      //qtyInput.value = 1;
      qtyInput.setAttribute('readonly', true);
      qtyInput.style.cursor = 'not-allowed';
      qtyInput.style.pointerEvents = 'none'; // ⚡ bloque toute interaction


      // Supprime les boutons TouchSpin après init
      setTimeout(() => {
        const wrapper = qtyInput.closest('.bootstrap-touchspin');
        if (wrapper) {
          const upBtn = wrapper.querySelector('.bootstrap-touchspin-up');
          const downBtn = wrapper.querySelector('.bootstrap-touchspin-down');
          if (upBtn) upBtn.remove();
          if (downBtn) downBtn.remove();
        }
      }, 50);
    }
  });
}

// --- Page panier initial load ---
applyLimitOneOnCart();

// --- Réapplique après chaque updateCart ---
prestashop.on('updateCart', function () {
  setTimeout(() => {
    applyLimitOneOnCart();
  }, 400); // petit délai pour laisser le DOM se mettre à jour
});

// Catalogue ----

function applyLimitOneOnListing() {
  // Sélectionne toutes les cartes produits du catalogue
  const productCards = document.querySelectorAll('.product-miniature, .product-container'); // adapte le sélecteur selon ton thème
  if (productCards.length === 0) return;

  // On peut récupérer le panier actuel pour savoir quels produits sont dedans
  const cartProductIds = []; // si tu veux faire via HTML du panier, sinon juste limitOneRefs

  productCards.forEach(card => {
    const idElem = card.querySelector('input[name="id_product"]');
    if (!idElem) return;

    const productId = idElem.value.trim();
    if (limitOneRefs.includes(productId)) {
      // On empêche l'affichage du bouton "Ajouter au panier"
      const addBtn = card.querySelector('.add-to-cart, .ajax_add_to_cart_button');
      if (addBtn) {
        addBtn.style.display = 'none';
        // ou addBtn.disabled = true;
        console.log(`Produit limité dans le catalogue (ID: ${productId}) → bouton ajouté masqué`);
      }
    }
  });
}

// --- Au chargement de la page catalogue ---
applyLimitOneOnListing();

// --- Si ton catalogue charge dynamiquement plus de produits (scroll infini) ---
document.addEventListener('mouseover', function(e) {
  const target = e.target.closest('.product-miniature, .product-container');
  if (target) {
    applyLimitOneOnListing();
  }
});







});
