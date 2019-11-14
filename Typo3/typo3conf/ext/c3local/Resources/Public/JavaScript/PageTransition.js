"use strict";

jQuery.fn.PageTransition = function (params) {
  if (this.length == 0) return this;
  /** support mutltiple elements */

  if (this.length > 1) {
    this.each(function () {
      $(this).PageTransition(params);
    });
    return this;
  }

  var transitioner = false,
      el = jQuery(this),
      defaults = {
    selector: 'a[data-transition]'
  },
      settings = Object.assign({}, defaults, params);

  function init() {
    el.on('click', settings.selector, transitionTo);
    setup();
  }

  function setup() {
    transitioner = jQuery('<div id="offscreen">').prependTo('body');
  }

  function transitionTo(evt) {
    evt.preventDefault();
    var target = jQuery(this).attr('href');
    transitioner.load(target + ' .page_wrap', function (response, status, xhr) {
      afterLoad(target, response, status, xhr);
    });
    return false;
  }

  function afterLoad(target) {
    var footer = jQuery('<div class="offscreen-footer"><a href="/" class="js-transition">zurueck</a></div>');
    transitioner.append(footer);
    jQuery('body > .page_wrap').addClass('slide-out is-reversed');
    transitioner.addClass('in');
    transitioner.unwrap('#offscreen');
    window.history.pushState("", "", target);
  }

  init();
};

//# sourceMappingURL=PageTransition.js.map