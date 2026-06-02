document.addEventListener('DOMContentLoaded', function () {
  var viewport = document.querySelector('[data-wtbanner-cropper]');
  var inputX = document.querySelector('input[name="WTBANNER_FOCAL_X"]');
  var inputY = document.querySelector('input[name="WTBANNER_FOCAL_Y"]');
  var marker = document.querySelector('[data-wtbanner-marker]');
  var image = document.querySelector('[data-wtbanner-preview-image]');
  var uploadInput = document.querySelector('input[name="WTBANNER_IMAGE_UPLOAD"]');

  if (!viewport || !inputX || !inputY || !marker || !image) {
    return;
  }

  var dragging = false;

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function applyFocal(x, y) {
    var focalX = clamp(Math.round(x), 0, 100);
    var focalY = clamp(Math.round(y), 0, 100);

    inputX.value = focalX;
    inputY.value = focalY;
    image.style.objectPosition = focalX + '% ' + focalY + '%';
    marker.style.left = focalX + '%';
    marker.style.top = focalY + '%';
  }

  function updateFromPointer(event) {
    var rect = viewport.getBoundingClientRect();
    var clientX = event.clientX;
    var clientY = event.clientY;

    if (typeof clientX !== 'number' || typeof clientY !== 'number') {
      return;
    }

    var x = ((clientX - rect.left) / rect.width) * 100;
    var y = ((clientY - rect.top) / rect.height) * 100;
    applyFocal(x, y);
  }

  applyFocal(
    parseInt(viewport.getAttribute('data-focal-x') || inputX.value || '50', 10),
    parseInt(viewport.getAttribute('data-focal-y') || inputY.value || '50', 10)
  );

  viewport.addEventListener('pointerdown', function (event) {
    dragging = true;
    viewport.setPointerCapture(event.pointerId);
    updateFromPointer(event);
  });

  viewport.addEventListener('pointermove', function (event) {
    if (!dragging) {
      return;
    }
    updateFromPointer(event);
  });

  viewport.addEventListener('pointerup', function (event) {
    dragging = false;
    viewport.releasePointerCapture(event.pointerId);
    updateFromPointer(event);
  });

  viewport.addEventListener('pointerleave', function () {
    dragging = false;
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
});
