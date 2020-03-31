!(function (t) {
  function s(e) {
    t('#light-mode-switch').prop('checked') == 1 && e === 'light-mode-switch' ? (t('#dark-mode-switch').prop('checked', !1), t('#rtl-mode-switch').prop('checked', !1), t('#bootstrap-style').attr('href', 'assets/css/bootstrap.min.css'), t('#app-style').attr('href', 'assets/css/app.min.css'), sessionStorage.setItem('is_visited', 'light-mode-switch')) : t('#dark-mode-switch').prop('checked') == 1 && e === 'dark-mode-switch' ? (t('#light-mode-switch').prop('checked', !1), t('#rtl-mode-switch').prop('checked', !1), t('#bootstrap-style').attr('href', 'assets/css/bootstrap-dark.min.css'), t('#app-style').attr('href', 'assets/css/app-dark.min.css'), sessionStorage.setItem('is_visited', 'dark-mode-switch')) : t('#rtl-mode-switch').prop('checked') == 1 && e === 'rtl-mode-switch' && (t('#light-mode-switch').prop('checked', !1), t('#dark-mode-switch').prop('checked', !1), t('#bootstrap-style').attr('href', 'assets/css/bootstrap.min.css'), t('#app-style').attr('href', 'assets/css/app-rtl.min.css'), sessionStorage.setItem('is_visited', 'rtl-mode-switch'));
  }

  function e() {
    document.webkitIsFullScreen || document.mozFullScreen || document.msFullscreenElement || (console.log('pressed'), t('body').removeClass('fullscreen-enable'));
  }
  t('#side-menu').metisMenu(), t('#vertical-menu-btn').on('click', (e) => {
    e.preventDefault(), t('body').toggleClass('sidebar-enable'), t(window).width() >= 992 ? t('body').toggleClass('vertical-collpsed') : t('body').removeClass('vertical-collpsed');
  }), t('#sidebar-menu a').each(function () {
    const e = window.location.href.split(/[?#]/)[0];
    this.href == e && (t(this).addClass('active'), t(this).parent().addClass('mm-active'), t(this).parent().parent().addClass('mm-show'), t(this).parent().parent().prev()
      .addClass('mm-active'), t(this).parent().parent().parent()
      .addClass('mm-active'), t(this).parent().parent().parent()
      .parent()
      .addClass('mm-show'), t(this).parent().parent().parent()
      .parent()
      .parent()
      .addClass('mm-active'));
  }), t('.navbar-nav a').each(function () {
    const e = window.location.href.split(/[?#]/)[0];
    this.href == e && (t(this).addClass('active'), t(this).parent().addClass('active'), t(this).parent().parent().addClass('active'), t(this).parent().parent().parent()
      .addClass('active'), t(this).parent().parent().parent()
      .parent()
      .addClass('active'), t(this).parent().parent().parent()
      .parent()
      .parent()
      .addClass('active'));
  }), t('[data-toggle="fullscreen"]').on('click', (e) => {
    e.preventDefault(), t('body').toggleClass('fullscreen-enable'), document.fullscreenElement || document.mozFullScreenElement || document.webkitFullscreenElement ? document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen() : document.documentElement.requestFullscreen ? document.documentElement.requestFullscreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullscreen && document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
  }), document.addEventListener('fullscreenchange', e), document.addEventListener('webkitfullscreenchange', e), document.addEventListener('mozfullscreenchange', e), t('.right-bar-toggle').on('click', (e) => {
    t('body').toggleClass('right-bar-enabled');
  }), t(document).on('click', 'body', (e) => {
    t(e.target).closest(".right-bar-toggle, .right-bar").length > 0 || t('body').removeClass('right-bar-enabled');
  }), t('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
    return t(this).next().hasClass('show') || t(this).parents('.dropdown-menu').first().find('.show')
      .removeClass('show'), t(this).next('.dropdown-menu').toggleClass('show'), !1;
  }), t(() => {
    t('[data-toggle="tooltip"]').tooltip();
  }), t(() => {
    t('[data-toggle="popover"]').popover();
  }),
  (function () {
    if (window.sessionStorage) {
      let e = sessionStorage.getItem('is_visited');
      e ? (t('.right-bar input:checkbox').prop('checked', !1), t('#' + e).prop('checked', !0), s(e)) : sessionStorage.setItem('is_visited', 'light-mode-switch');
    }
    t('#light-mode-switch, #dark-mode-switch, #rtl-mode-switch').on('change', (e) => {
                s(e.target.id)
            });
  }()), t(window).on('load', () => {
    t('#status').fadeOut(), t('#preloader').delay(350).fadeOut('slow');
  }), Waves.init();
}(jQuery));
