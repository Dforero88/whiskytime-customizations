(function () {
  function scrollToHoursAnchor() {
    if (window.location.hash !== '#wthours-hours') {
      return;
    }

    var target = document.getElementById('wthours-hours');
    if (!target) {
      return;
    }

    window.setTimeout(function () {
      target.scrollIntoView({ block: 'start' });
    }, 60);
  }

  window.addEventListener('load', scrollToHoursAnchor);
  window.addEventListener('hashchange', scrollToHoursAnchor);
})();
