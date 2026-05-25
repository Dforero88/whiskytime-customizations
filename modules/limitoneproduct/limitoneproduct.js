document.addEventListener('DOMContentLoaded', function () {
  const limitedLimits =
    window.limitOneProductLimits && typeof window.limitOneProductLimits === 'object'
      ? Object.fromEntries(
          Object.entries(window.limitOneProductLimits).map(([id, limit]) => [
            String(id).trim(),
            Math.max(1, parseInt(limit, 10) || 1),
          ])
        )
      : {};
  const cartQuantities =
    window.limitOneCartQuantities && typeof window.limitOneCartQuantities === 'object'
      ? Object.fromEntries(
          Object.entries(window.limitOneCartQuantities).map(([id, qty]) => [
            String(id).trim(),
            Math.max(0, parseInt(qty, 10) || 0),
          ])
        )
      : {};
  const limitMessageTemplate = String(window.limitOnePerCustomerMessageTemplate || '').trim();
  const limitReachedTemplate = String(window.limitOneReachedMessageTemplate || '').trim();
  const limitedIds = Object.keys(limitedLimits);

  if (!limitedIds.length) {
    return;
  }

  function isLimitedProduct(id) {
    return Object.prototype.hasOwnProperty.call(limitedLimits, String(id).trim());
  }

  function getLimitForProduct(id) {
    return limitedLimits[String(id).trim()] || 1;
  }

  function getCartQuantityForLimitedProduct(id) {
    return cartQuantities[String(id).trim()] || 0;
  }

  function setCartQuantityForLimitedProduct(id, qty) {
    cartQuantities[String(id).trim()] = Math.max(0, parseInt(qty, 10) || 0);
  }

  function isLimitReached(id) {
    const productId = String(id).trim();
    return getCartQuantityForLimitedProduct(productId) >= getLimitForProduct(productId);
  }

  function buildLimitMessage(limit, reached) {
    const template = reached ? limitReachedTemplate : limitMessageTemplate;
    return template ? template.replace(/%limit%/g, String(limit)) : '';
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

  function syncTouchSpinButtons(qtyInput, disabled) {
    const wrapper = qtyInput?.closest('.bootstrap-touchspin');
    if (!wrapper) {
      return;
    }

    wrapper.querySelectorAll('.bootstrap-touchspin-up, .bootstrap-touchspin-down').forEach((button) => {
      button.style.display = '';
      button.style.pointerEvents = disabled ? 'none' : '';
      button.setAttribute('aria-hidden', 'false');
      button.disabled = disabled;
      button.style.opacity = disabled ? '0.4' : '';
    });
  }

  function observeTouchSpin(qtyInput, disabled) {
    const parent = qtyInput?.parentElement;
    if (!qtyInput || !parent) {
      return;
    }

    syncTouchSpinButtons(qtyInput, disabled);

    if (parent.dataset.limitOneObserved === '1') {
      return;
    }

    parent.dataset.limitOneObserved = '1';
    const observer = new MutationObserver(function () {
      syncTouchSpinButtons(qtyInput, disabled);
    });

    observer.observe(parent, { childList: true, subtree: true });
    setTimeout(() => syncTouchSpinButtons(qtyInput, disabled), 50);
    setTimeout(() => syncTouchSpinButtons(qtyInput, disabled), 300);
    setTimeout(() => syncTouchSpinButtons(qtyInput, disabled), 800);
  }

  function applyQuantityInputLimit(qtyInput, value, max, disabled, readonly) {
    if (!qtyInput) {
      return;
    }

    const normalizedMax = Math.max(1, parseInt(max, 10) || 1);
    const normalizedValue = Math.min(normalizedMax, Math.max(1, parseInt(value, 10) || 1));

    qtyInput.value = String(normalizedValue);
    qtyInput.setAttribute('min', 1);
    qtyInput.setAttribute('max', normalizedMax);
    if (readonly) {
      qtyInput.setAttribute('readonly', 'readonly');
    } else {
      qtyInput.removeAttribute('readonly');
    }
    if (disabled) {
      qtyInput.setAttribute('disabled', 'disabled');
    } else {
      qtyInput.removeAttribute('disabled');
    }
    qtyInput.style.cursor = disabled ? 'not-allowed' : '';
    qtyInput.style.pointerEvents = disabled ? 'none' : '';

    observeTouchSpin(qtyInput, disabled);
  }

  function ensureLimitMessage(limit, reached) {
    const messageText = buildLimitMessage(limit, reached);
    if (!messageText) {
      return;
    }

    const target =
      document.querySelector('#product-availability') ||
      document.querySelector('.product-add-to-cart') ||
      document.querySelector('.product-quantity');

    if (!target) {
      return;
    }

    let message = document.querySelector('.limit-oneproduct-message');
    if (!message) {
      message = document.createElement('div');
      message.className = 'limit-oneproduct-message';
      message.style.marginTop = '8px';
      message.style.fontSize = '0.95rem';
      message.style.fontWeight = '600';
      message.style.color = '#9a7b20';
      target.insertAdjacentElement('afterend', message);
    }

    message.textContent = messageText;
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

    const limit = getLimitForProduct(productId);
    const cartQty = getCartQuantityForLimitedProduct(productId);
    const remainingQty = Math.max(0, limit - cartQty);
    const qtyInput = document.querySelector('input[name="qty"], #quantity_wanted');
    const addButton = document.querySelector('.add-to-cart, [data-button-action="add-to-cart"]');
    const desiredValue = Math.max(1, Math.min(parseInt(qtyInput?.value, 10) || 1, remainingQty || 1));

    if (remainingQty <= 0) {
      applyQuantityInputLimit(qtyInput, 1, 1, true, true);
      setAddToCartDisabled(addButton, true);
    } else {
      applyQuantityInputLimit(qtyInput, desiredValue, remainingQty, false, false);
      setAddToCartDisabled(addButton, false);
    }

    ensureLimitMessage(limit, remainingQty <= 0);
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

      const limit = getLimitForProduct(productId);
      const currentQty = Math.min(limit, Math.max(1, parseInt(qtyInput.value, 10) || 1));

      applyQuantityInputLimit(qtyInput, currentQty, limit, false, false);
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
        addBtn.style.display = isLimitReached(productId) ? 'none' : '';
      }
    });
  }

  function syncCartQuantitiesFromDom() {
    document.querySelectorAll('.cart-item input.js-cart-line-product-quantity').forEach((qtyInput) => {
      const productId = String(qtyInput.dataset.productId || '').trim();
      if (!productId || !isLimitedProduct(productId)) {
        return;
      }

      setCartQuantityForLimitedProduct(productId, qtyInput.value);
    });
  }

  applyLimitOneOnProductPage();
  syncCartQuantitiesFromDom();
  applyLimitOneOnCart();
  applyLimitOneOnListing();

  if (window.prestashop && typeof prestashop.on === 'function') {
    prestashop.on('updateCart', function (event) {
      const reason = event && event.reason ? event.reason : {};
      const candidateId =
        String(reason.idProduct || reason.id_product || reason.productId || '').trim();

      if (candidateId && isLimitedProduct(candidateId) && reason.linkAction === 'add-to-cart') {
        const nextQty = getCartQuantityForLimitedProduct(candidateId) + 1;
        setCartQuantityForLimitedProduct(candidateId, Math.min(nextQty, getLimitForProduct(candidateId)));
      }

      setTimeout(syncCartQuantitiesFromDom, 100);
      setTimeout(applyLimitOneOnCart, 300);
      setTimeout(applyLimitOneOnListing, 300);
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
