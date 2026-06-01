(function () {
  var supportedHashes = ['#wthours-hours', '#wthours-address', '#wthours-contact'];

  function scrollToHoursAnchor() {
    if (supportedHashes.indexOf(window.location.hash) === -1) {
      return;
    }

    var target = document.getElementById(window.location.hash.substring(1));
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
