document.addEventListener('DOMContentLoaded', function () {
  const limitedIds = Array.isArray(window.limitOneProductIds)
    ? window.limitOneProductIds.map(String)
    : [];
  const cartLimitedIds = new Set(
    Array.isArray(window.limitOneCartProductIds)
      ? window.limitOneCartProductIds.map((id) => String(id).trim())
      : []
  );
  const limitMessage = String(window.limitOnePerCustomerMessage || '').trim();

  if (!limitedIds.length) {
    return;
  }

  function isLimitedProduct(id) {
    return limitedIds.includes(String(id).trim());
  }

  function isLimitedProductInCart(id) {
    return cartLimitedIds.has(String(id).trim());
  }

  function setAddToCartDisabled(button, disabled) {
    if (!button) {
      return;
    }

    button.disabled = disabled;
    button.classList.toggle('disabled', disabled);
    button.setAttribute('aria-disabled', disabled ? 'true' : 'false');
    button.style.pointerEvents = disabled ? 'none' : '';
    button.style.opacity = disabled ? '0.5' : '';
  }

  function hideTouchSpinButtons(qtyInput) {
    const wrapper = qtyInput?.closest('.bootstrap-touchspin');
    if (!wrapper) {
      return;
    }

    wrapper.querySelectorAll('.bootstrap-touchspin-up, .bootstrap-touchspin-down').forEach((button) => {
      button.style.display = 'none';
      button.style.pointerEvents = 'none';
      button.setAttribute('aria-hidden', 'true');
      button.disabled = true;
    });
  }

  function observeTouchSpin(qtyInput) {
    const parent = qtyInput?.parentElement;
    if (!qtyInput || !parent || parent.dataset.limitOneObserved === '1') {
      return;
    }

    parent.dataset.limitOneObserved = '1';
    const observer = new MutationObserver(function () {
      hideTouchSpinButtons(qtyInput);
    });

    observer.observe(parent, { childList: true, subtree: true });
    hideTouchSpinButtons(qtyInput);
    setTimeout(() => hideTouchSpinButtons(qtyInput), 50);
    setTimeout(() => hideTouchSpinButtons(qtyInput), 300);
    setTimeout(() => hideTouchSpinButtons(qtyInput), 800);
  }

  function lockQuantityInput(qtyInput, value, disabled) {
    if (!qtyInput) {
      return;
    }

    qtyInput.value = String(value);
    qtyInput.setAttribute('min', value === 0 ? 0 : 1);
    qtyInput.setAttribute('max', 1);
    qtyInput.setAttribute('readonly', 'readonly');
    if (disabled) {
      qtyInput.setAttribute('disabled', 'disabled');
    } else {
      qtyInput.removeAttribute('disabled');
    }
    qtyInput.style.cursor = 'not-allowed';
    qtyInput.style.pointerEvents = 'none';

    observeTouchSpin(qtyInput);
  }

  function ensureLimitMessage() {
    if (!limitMessage) {
      return;
    }

    if (document.querySelector('.limit-oneproduct-message')) {
      return;
    }

    const target =
      document.querySelector('#product-availability') ||
      document.querySelector('.product-add-to-cart') ||
      document.querySelector('.product-quantity');

    if (!target) {
      return;
    }

    const message = document.createElement('div');
    message.className = 'limit-oneproduct-message';
    message.textContent = limitMessage;
    message.style.marginTop = '8px';
    message.style.fontSize = '0.95rem';
    message.style.fontWeight = '600';
    message.style.color = '#9a7b20';

    target.insertAdjacentElement('afterend', message);
  }

  function scheduleProductPageRefresh() {
    setTimeout(applyLimitOneOnProductPage, 50);
    setTimeout(applyLimitOneOnProductPage, 300);
    setTimeout(applyLimitOneOnProductPage, 800);
  }

  function observeProductPageChanges() {
    const root =
      document.querySelector('.product-add-to-cart') ||
      document.querySelector('#add-to-cart-or-refresh') ||
      document.querySelector('form.add-to-cart-or-refresh');

    if (!root || root.dataset.limitOneObserved === '1') {
      return;
    }

    root.dataset.limitOneObserved = '1';
    const observer = new MutationObserver(function () {
      scheduleProductPageRefresh();
    });

    observer.observe(root, { childList: true, subtree: true });
  }

  function applyLimitOneOnProductPage() {
    const idElem = document.querySelector('input[name="id_product"]');
    if (!idElem) {
      return;
    }

    const productId = idElem.value.trim();
    if (!isLimitedProduct(productId)) {
      return;
    }

    const qtyInput = document.querySelector('input[name="qty"], #quantity_wanted');
    const addButton = document.querySelector('.add-to-cart, [data-button-action="add-to-cart"]');
    const alreadyInCart = isLimitedProductInCart(productId);

    lockQuantityInput(qtyInput, alreadyInCart ? 0 : 1, alreadyInCart);
    setAddToCartDisabled(addButton, alreadyInCart);
    ensureLimitMessage();
    observeProductPageChanges();
  }

  function applyLimitOneOnCart() {
    document.querySelectorAll('.cart-item').forEach((row) => {
      const qtyInput = row.querySelector('input.js-cart-line-product-quantity');
      if (!qtyInput) {
        return;
      }

      const productId = String(qtyInput.dataset.productId || '').trim();
      if (!productId || !isLimitedProduct(productId)) {
        return;
      }

      lockQuantityInput(qtyInput, qtyInput.value || 1, true);
    });
  }

  function applyLimitOneOnListing() {
    document.querySelectorAll('.product-miniature, .product-container').forEach((card) => {
      const productId =
        String(card.dataset.idProduct || '').trim() ||
        String(card.querySelector('input[name="id_product"]')?.value || '').trim();

      if (!productId || !isLimitedProduct(productId)) {
        return;
      }

      const addBtn = card.querySelector('.add-to-cart, .ajax_add_to_cart_button, [data-button-action="add-to-cart"]');
      if (addBtn) {
        addBtn.style.display = 'none';
      }
    });
  }

  applyLimitOneOnProductPage();
  applyLimitOneOnCart();
  applyLimitOneOnListing();

  if (window.prestashop && typeof prestashop.on === 'function') {
    prestashop.on('updateCart', function (event) {
      const pageIdElem = document.querySelector('input[name="id_product"]');
      const pageProductId = pageIdElem ? String(pageIdElem.value || '').trim() : '';

      if (
        pageProductId &&
        isLimitedProduct(pageProductId) &&
        event &&
        event.reason &&
        event.reason.linkAction === 'add-to-cart'
      ) {
        cartLimitedIds.add(pageProductId);
      }

      setTimeout(applyLimitOneOnCart, 300);
      scheduleProductPageRefresh();
    });

    prestashop.on('updatedProduct', function () {
      scheduleProductPageRefresh();
    });
  }

  document.addEventListener('mouseover', function (event) {
    const target = event.target.closest('.product-miniature, .product-container');
    if (target) {
      applyLimitOneOnListing();
    }
  });
});
