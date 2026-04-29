document.addEventListener('DOMContentLoaded', function () {
  var popup = document.getElementById('popupimage-overlay');
  var closeBtn = document.getElementById('popupimage-close');
  var seenKey = 'popupimage_seen';

  if (!popup || !closeBtn) {
    return;
  }

  if (sessionStorage.getItem(seenKey) === 'true') {
    popup.hidden = true;
    return;
  }

  popup.hidden = false;

  var closePopup = function () {
    popup.hidden = true;
    sessionStorage.setItem(seenKey, 'true');
  };

  closeBtn.addEventListener('click', closePopup);

  popup.addEventListener('click', function (event) {
    if (event.target === popup) {
      closePopup();
    }
  });

  popup.addEventListener('click', function (event) {
    if (event.target.closest('a')) {
      sessionStorage.setItem(seenKey, 'true');
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !popup.hidden) {
      closePopup();
    }
  });
});
