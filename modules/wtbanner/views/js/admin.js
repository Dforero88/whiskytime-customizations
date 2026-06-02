document.addEventListener('DOMContentLoaded', function () {
  var viewport = document.querySelector('[data-wtbanner-cropper]');
  var image = document.querySelector('[data-wtbanner-preview-image]');
  var cropBox = document.querySelector('[data-wtbanner-crop-box]');
  var handle = document.querySelector('[data-wtbanner-handle]');
  var inputX = document.querySelector('input[name="WTBANNER_CROP_X"]');
  var inputY = document.querySelector('input[name="WTBANNER_CROP_Y"]');
  var inputW = document.querySelector('input[name="WTBANNER_CROP_W"]');
  var inputH = document.querySelector('input[name="WTBANNER_CROP_H"]');
  var uploadInput = document.querySelector('input[name="WTBANNER_IMAGE_UPLOAD"]');

  if (!viewport || !image || !cropBox || !handle || !inputX || !inputY || !inputW || !inputH) {
    return;
  }

  var ratio = 4 / 1;
  var interaction = null;

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
    if (!image.naturalWidth || !image.naturalHeight) {
      return;
    }

    var imageRatio = image.naturalWidth / image.naturalHeight;
    var crop;

    if (imageRatio > ratio) {
      crop = {
        x: ((100 - ((ratio / imageRatio) * 100)) / 2),
        y: 0,
        w: (ratio / imageRatio) * 100,
        h: 100
      };
    } else {
      crop = {
        x: 0,
        y: ((100 - ((imageRatio / ratio) * 100)) / 2),
        w: 100,
        h: (imageRatio / ratio) * 100
      };
    }

    setCrop(crop);
  }

  function startMove(event) {
    var rect = getViewportRect();
    var crop = getCrop();
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
    interaction = {
      mode: 'resize',
      pointerId: event.pointerId,
      startX: event.clientX,
      startY: event.clientY,
      crop: crop,
      rect: rect
    };
    handle.setPointerCapture(event.pointerId);
  }

  function onPointerMove(event) {
    if (!interaction) {
      return;
    }

    var deltaX = ((event.clientX - interaction.startX) / interaction.rect.width) * 100;
    var deltaY = ((event.clientY - interaction.startY) / interaction.rect.height) * 100;

    if (interaction.mode === 'move') {
      setCrop({
        x: interaction.crop.x + deltaX,
        y: interaction.crop.y + deltaY,
        w: interaction.crop.w,
        h: interaction.crop.h
      });
      return;
    }

    var nextWidth = clamp(interaction.crop.w + deltaX, 5, 100 - interaction.crop.x);
    var nextHeight = nextWidth / ratio;
    var maxHeight = 100 - interaction.crop.y;

    if (nextHeight > maxHeight) {
      nextHeight = maxHeight;
      nextWidth = nextHeight * ratio;
    }

    setCrop({
      x: interaction.crop.x,
      y: interaction.crop.y,
      w: nextWidth,
      h: nextHeight
    });
  }

  function endInteraction(event) {
    if (!interaction) {
      return;
    }

    if (interaction.mode === 'move') {
      cropBox.releasePointerCapture(event.pointerId);
    } else {
      handle.releasePointerCapture(event.pointerId);
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
  handle.addEventListener('pointerdown', startResize);
  window.addEventListener('pointermove', onPointerMove);
  window.addEventListener('pointerup', endInteraction);

  viewport.addEventListener('click', function (event) {
    if (event.target === cropBox || event.target === handle) {
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
