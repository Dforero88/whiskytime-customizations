document.addEventListener('DOMContentLoaded', function () {
  var viewport = document.querySelector('[data-wtbanner-cropper]');
  var image = document.querySelector('[data-wtbanner-preview-image]');
  var cropBox = document.querySelector('[data-wtbanner-crop-box]');
  var handles = Array.prototype.slice.call(document.querySelectorAll('[data-wtbanner-handle]'));
  var inputX = document.querySelector('input[name="WTBANNER_CROP_X"]');
  var inputY = document.querySelector('input[name="WTBANNER_CROP_Y"]');
  var inputW = document.querySelector('input[name="WTBANNER_CROP_W"]');
  var inputH = document.querySelector('input[name="WTBANNER_CROP_H"]');
  var uploadInput = document.querySelector('input[name="WTBANNER_IMAGE_UPLOAD"]');

  if (!viewport || !image || !cropBox || !handles.length || !inputX || !inputY || !inputW || !inputH) {
    return;
  }

  var interaction = null;
  var suppressClick = false;

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function getViewportRect() {
    return viewport.getBoundingClientRect();
  }

  function getCrop() {
    return {
      x: parseFloat(inputX.value || viewport.getAttribute('data-crop-x') || '0'),
      y: parseFloat(inputY.value || viewport.getAttribute('data-crop-y') || '0'),
      w: parseFloat(inputW.value || viewport.getAttribute('data-crop-w') || '100'),
      h: parseFloat(inputH.value || viewport.getAttribute('data-crop-h') || '100')
    };
  }

  function setCrop(crop) {
    var normalized = {
      x: clamp(crop.x, 0, 100),
      y: clamp(crop.y, 0, 100),
      w: clamp(crop.w, 5, 100),
      h: clamp(crop.h, 5, 100)
    };

    if (normalized.x + normalized.w > 100) {
      normalized.x = 100 - normalized.w;
    }

    if (normalized.y + normalized.h > 100) {
      normalized.y = 100 - normalized.h;
    }

    inputX.value = normalized.x.toFixed(4);
    inputY.value = normalized.y.toFixed(4);
    inputW.value = normalized.w.toFixed(4);
    inputH.value = normalized.h.toFixed(4);

    cropBox.style.left = normalized.x + '%';
    cropBox.style.top = normalized.y + '%';
    cropBox.style.width = normalized.w + '%';
    cropBox.style.height = normalized.h + '%';
  }

  function fitDefaultCrop() {
    setCrop({
      x: 0,
      y: 0,
      w: 100,
      h: 100
    });
  }

  function startMove(event) {
    var rect = getViewportRect();
    var crop = getCrop();
    suppressClick = false;
    interaction = {
      mode: 'move',
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      crop: crop,
      rect: rect
    };
    cropBox.setPointerCapture(event.pointerId);
  }

  function startResize(event) {
    event.stopPropagation();
    var rect = getViewportRect();
    var crop = getCrop();
    var direction = event.currentTarget.getAttribute('data-wtbanner-handle');
    suppressClick = false;
    interaction = {
      mode: 'resize',
      direction: direction,
      handle: event.currentTarget,
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      crop: crop,
      rect: rect
    };
    event.currentTarget.setPointerCapture(event.pointerId);
  }

  function onPointerMove(event) {
    if (!interaction) {
      return;
    }

    var deltaX = ((event.clientX - interaction.startX) / interaction.rect.width) * 100;
    var deltaY = ((event.clientY - interaction.startY) / interaction.rect.height) * 100;
    if (Math.abs(deltaX) > 0.01 || Math.abs(deltaY) > 0.01) {
      suppressClick = true;
    }

    if (interaction.mode === 'move') {
      setCrop({
        x: interaction.crop.x + deltaX,
        y: interaction.crop.y + deltaY,
        w: interaction.crop.w,
        h: interaction.crop.h
      });
      return;
    }

    var crop = interaction.crop;
    var direction = interaction.direction || 'se';
    var left = crop.x;
    var top = crop.y;
    var right = crop.x + crop.w;
    var bottom = crop.y + crop.h;

    if (direction.indexOf('e') !== -1) {
      right = clamp(right + deltaX, left + 5, 100);
    }

    if (direction.indexOf('w') !== -1) {
      left = clamp(left + deltaX, 0, right - 5);
    }

    if (direction.indexOf('s') !== -1) {
      bottom = clamp(bottom + deltaY, top + 5, 100);
    }

    if (direction.indexOf('n') !== -1) {
      top = clamp(top + deltaY, 0, bottom - 5);
    }

    setCrop({
      x: left,
      y: top,
      w: right - left,
      h: bottom - top
    });
  }

  function endInteraction(event) {
    if (!interaction) {
      return;
    }

    if (interaction.mode === 'move') {
      cropBox.releasePointerCapture(event.pointerId);
    } else {
      interaction.handle.releasePointerCapture(event.pointerId);
    }

    interaction = null;
  }

  image.addEventListener('load', function () {
    var crop = getCrop();
    if (!crop.w || !crop.h || crop.w === 100 || crop.h === 100) {
      fitDefaultCrop();
      return;
    }

    setCrop(crop);
  });

  cropBox.addEventListener('pointerdown', startMove);
  handles.forEach(function (handle) {
    handle.addEventListener('pointerdown', startResize);
  });
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', endInteraction);

  viewport.addEventListener('click', function (event) {
    if (suppressClick) {
      suppressClick = false;
      return;
    }

    if (event.target.closest('[data-wtbanner-crop-box]')) {
      return;
    }

    var rect = getViewportRect();
    var crop = getCrop();
    var width = crop.w;
    var height = crop.h;
    var x = (((event.clientX - rect.left) / rect.width) * 100) - (width / 2);
    var y = (((event.clientY - rect.top) / rect.height) * 100) - (height / 2);

    setCrop({
      x: x,
      y: y,
      w: width,
      h: height
    });
  });

  if (uploadInput) {
    uploadInput.addEventListener('change', function () {
      var file = uploadInput.files && uploadInput.files[0];
      if (!file) {
        return;
      }

      var reader = new FileReader();
      reader.onload = function (loadEvent) {
        if (loadEvent.target && loadEvent.target.result) {
          image.src = loadEvent.target.result;
        }
      };
      reader.readAsDataURL(file);
    });
  }

  if (image.complete && image.naturalWidth) {
    var initialCrop = getCrop();
    if (!initialCrop.w || !initialCrop.h || initialCrop.w === 100 || initialCrop.h === 100) {
      fitDefaultCrop();
    } else {
      setCrop(initialCrop);
    }
  }
});
