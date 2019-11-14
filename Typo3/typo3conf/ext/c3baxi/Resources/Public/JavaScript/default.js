'use strict';
/**
 ****     Definitions and Customization */

/**
 *
 * @param obj
 * @param predicate
 * @returns {*}
 */

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

Object.filter = function (obj, predicate) {
  return Object.keys(obj).filter(function (key) {
    return predicate(obj[key], key);
  }).reduce(function (res, key) {
    return res[key] = obj[key], res;
  }, {});
};

var dialogDefaults = Object.freeze({
  classes: {
    "ui-dialog": "boxed",
    "ui-dialog-titlebar": "hidden",
    "ui-dialog-content": "box"
  },
  resizable: false,
  title: null,
  minHeight: 400,
  height: 400,
  width: function width() {
    return Math.min(400, parseInt(window.innerWidth) - 40);
  },
  show: {
    effect: "fade",
    duration: 300
  },
  hide: {
    effect: "fade",
    duration: 300
  },
  open: function open(evt, ui) {
    var parent = jQuery(evt.target).closest(".ui-dialog"),
        removeOverlay = false,
        overlay = null;
    parent.css("z-index", 500);
    parent.find(".ui-dialog-titlebar").hide();

    if (parent.siblings(".ui-widget-overlay").length === 0) {
      overlay = jQuery("<div class='ui-widget-overlay'>").appendTo(jQuery("body"));
      removeOverlay = true;
    } else {
      overlay = parent.siblings(".ui-widget-overlay");
    }

    parent.siblings(".ui-widget-overlay").css("z-index", 490).addClass("help-overlay");

    var closeHelp = function closeHelp() {
      if (removeOverlay) {
        overlay.remove();
      } else {
        parent.siblings(".ui-widget-overlay").css("z-index", 90).removeClass("help-overlay");
      }

      jQuery(parent.find("[id*='Dialog']")).dialog("destroy").remove();
    };

    jQuery("[data-action='closeDialog']", parent).on("click", closeHelp);
    jQuery(overlay).on("click", closeHelp);
  }
});
/** Favorites */

function BaxiFavorites(params) {
  var BF = this,
      defaults = {
    maxItems: 5,
    storeKey: "favorites"
  };
  BF.items = [];
  BF.template = document.createElement("LI");
  BF.settings = Object.assign({}, defaults, params);

  BF.add = function () {
    var uid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var stationName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var result = false;
    jQuery.ajax({
      url: baxiSearchSettings.ajaxUrl.favorites,
      //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
      method: "POST",
      data: {
        tx_c3baxi_baxisuche: {
          doAction: "add",
          uid: uid,
          name: stationName
        }
      }
    }).then(function (response) {
      if (response.status === "added") {
        BF.items.push({
          station: uid,
          name: stationName
        });
        Store.set(BF.settings.storeKey, BF.items);
        result = true;
      }
    });
    return result;
  };

  BF.remove = function () {
    var uid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var result = false;
    jQuery.ajax({
      url: baxiSearchSettings.ajaxUrl.favorites,
      //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
      method: "POST",
      data: {
        tx_c3baxi_baxisuche: {
          doAction: "remove",
          uid: uid
        }
      }
    }).then(function (response) {
      BF.items = BF.items.filter(function (item) {
        return item.station !== uid;
      }).values();
      Store.set(BF.settings.storeKey, BF.items);
      result = true;
    });
    return result;
  };
  /**
   *
   * @param {Integer} uid
   */


  BF.contains = function () {
    var uid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    return BF.items.filter(function (item) {
      return parseInt(item.station) === parseInt(uid);
    }).length;
  };

  BF.init = function () {
    if (Store) {
      var storedFavorites = Store.get(BF.settings.storeKey);

      if (typeof storedFavorites === "string") {
        try {
          BF.items = JSON.parse(storedFavorites);
        } catch (e) {
          BF.items = [];
        }
      } else {
        BF.items = storedFavorites;
      }
    }
  };
  /**
   *
   * @param {HTMLElement} parent
   * @param {HTMLElement} template
   */


  BF.appendTo = function () {
    var parent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var template = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : BF.template;
    if (!parent) return false;
    var list = document.createDocumentFragment();

    for (var i = 0; i < BF.items.length; i++) {
      var child = template.cloneNode(true);
      child.innerHTML = child.innerHTML + BF.items[i].name;
      child.setAttribute("data-key", BF.items[i].station);
      child.setAttribute("data-name", BF.items[i].name);
      list.append(child);
    }

    parent.append(list);
  };

  BF.init();
}

var Favorites = new BaxiFavorites();
/** Prompt jQuery.UI */

jQuery.widget("custom.prompt", jQuery.ui.dialog, {
  options: {
    modal: true,
    classes: {
      "ui-dialog": "boxed prompt",
      "ui-dialog-titlebar": "hidden",
      "ui-dialog-content": "box prompt-content",
      "ui-dialog-buttonpane": "prompt-buttonpanel"
    },
    question: "Test Frage",
    defaultText: "",
    resizable: false,
    buttons: {
      accept: {
        text: "OK",
        "class": "prompt-btn prompt-btn--accept",
        id: "promptAccept",
        click: function click() {
          return true;
        }
      },
      cancel: {
        text: "Abbrechen",
        "class": "prompt-btn prompt-btn--cancel",
        id: "promptCancel",
        click: function click() {
          return true;
        }
      }
    },
    confirm: false,
    submit: function submit(val) {}
  },
  submit: function submit(val) {
    if (typeof this.options.submit === "function") {
      this.options.submit(val);
    }

    jQuery(this.parentNode).dialog().dialog("destroy").remove();
  },
  _create: function _create() {
    if (this.options.confirm) {
      this.options.classes['ui-dialog'] = this.options.classes['ui-dialog'].concat(' prompt--confirm');
      this.options.classes['ui-dialog-buttonpane'] = this.options.classes['ui-dialog-buttonpane'].concat(' is-centered');
      this.options.buttons.accept["class"] = this.options.buttons.accept["class"].concat(' btn btn--cancel');
    }

    this._super();
  },
  open: function create() {
    var html = document.createDocumentFragment();

    if (this.options.question !== "") {
      var question = document.createElement("H2");
      question.innerText = this.options.question;
      html.appendChild(question);
    }

    if (this.options.confirm === false) {
      /** Prompt Form */
      var _form = document.createElement("FORM"),
          field = document.createElement("INPUT");

      field.setAttribute("type", "text");
      field.classList.add("prompt-field");
      field.setAttribute("value", this.options.defaultText);

      _form.appendChild(field);

      html.appendChild(_form);
    } else {
      var info = document.createElement('P');
      info.innerText = this.options.defaultText;
      html.appendChild(info);
    }

    this.element.html(html);
    this.uiDialogTitlebar.hide();
    this.uiDialogButtonPane.appendTo(this.element);
    var promptThat = this;
    this.uiDialogButtonPane.find("#promptAccept").on("click", function (evt) {
      var d = jQuery(evt.target).closest(".ui-dialog"),
          f = d.find(".prompt-field");
      promptThat.options.submit(f.val());
      d.dialog().dialog("destroy").remove();
    });
    this.uiDialogButtonPane.find("#promptCancel").on("click", function (evt) {
      jQuery(evt.target).closest(".ui-dialog").dialog().dialog("destroy").remove();
    });

    this._super();
  }
});
/**
 ****     Actual Scripts */

var form = jQuery("#routeFinder");
var ignoreZone = false;
jQuery("[data-autocomplete='haltestelle']").on("click", function (evt) {
  var modal, isStart, callFnc, positionPickerParams;
  modal = jQuery("<div id='mapDialog'></div>").appendTo("body");
  isStart = evt.target.getAttribute("name").includes("[start]");
  callFnc = "haltestelle";
  positionPickerParams = {
    source: baxiSearchSettings.ajaxUrl[callFnc],
    currentPosition: baxiSearchSettings.mapCenter,
    closeOnSelect: false,
    customSearchButtons: function customSearchButtons(picker, el, callback) {
      var favoriteButtons = jQuery("<ul class=\"favorites\">"),
          tmpl = document.createElement("LI");
      tmpl.setAttribute("class", "favorites-item");
      tmpl.innerHTML = "<span class='btn btn--marble has-icon'><svg class='icon'><use xlink:href='#Favoriten_kontur'/></svg></span>";
      Favorites.appendTo(favoriteButtons, tmpl);
      favoriteButtons.children().each(function (idx, item) {
        var fav = jQuery(item);
        fav.on("click", function () {
          jQuery.ajax(baxiSearchSettings.ajaxUrl.station, {
            method: "post",
            data: {
              tx_c3baxi_baxisuche: {
                uid: fav.data("key")
              }
            }
          }).then(function (response) {
            response.key = response.uid;
            var evt = {
              data: response
            };
            callback(evt);
          });
        });
      });
      return favoriteButtons;
    },
    customButtons: function customButtons(picker) {
      var buttons = document.createDocumentFragment(),
          favBtn = jQuery("<span class=\"controls-btn\">").appendTo(buttons);
      favBtn.html("<span class=\"btn btn--glass btn--round has-icon\"><svg class=\"icon\" width=\"30\" height=\"30\"><use xlink:href=\"#Favoriten_kontur\"></use></svg></span>Zu Favoriten");

      if (!baxiSearchSettings.loggedIn) {
        favBtn.toggleClass("is-disabled", true);
      } else {
        if (Favorites.contains(picker.selected.key)) {
          favBtn.html("<span class=\"btn btn--marble has-icon\">\n\t\t\t\t\t\t\t\t\t<svg class=\"icon\" width=\"24\" height=\"24\"><use xlink:href=\"#delete\"></use></svg>\n\t\t\t\t\t\t\t\t\t</span>von Favoriten entfernen");
          favBtn.on("click", function () {
            Favorites.remove(picker.selected.key);
          });
        } else {
          favBtn.on("click", function () {
            jQuery("<div />").prompt({
              question: "Geben Sie einen eigenen Namen ein",
              defaultText: picker.selected.title,
              submit: function submit(name) {
                Favorites.add(picker.selected.key, name);
              }
            });
          });
        }
      }

      return buttons;
    },
    onSelect: function onSelect(data, picker) {
      jQuery.ajax({
        url: baxiSearchSettings.ajaxUrl.station,
        method: "POST",
        data: {
          tx_c3baxi_baxisuche: {
            uid: data.key
          }
        }
      }).then(function (response) {
        var subtitle = picker.content.find(".subtitle");
        subtitle.append(jQuery("<span>").text(response.linie.join(", ")));
        ignoreZone = response.zoneId;
      });
    },
    onSubmit: function onSubmit(data) {
      var field = jQuery(evt.target);

      if (field.prop("name") === "tx_c3baxi_baxisuche[routeShow][start]") {
        Store.set("start", data.key);
      } else {
        Store.set("stop", data.key);
      }

      field.val(data.title);
      field.nextAll("input[type='hidden'][name^='tx_c3baxi_baxisuche[route]']").val(data.key);
    }
  };
  if (isStart && ignoreZone) ignoreZone = false;

  if (!("GeoPicker" in jQuery.fn)) {
    $("<link/>", {
      rel: "stylesheet",
      type: "text/css",
      href: window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.css"
    }).appendTo("head");
    jQuery.getScript(window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.js", function () {
      jQuery(modal).GeoPicker(positionPickerParams);
    });
  } else {
    jQuery(modal).GeoPicker(positionPickerParams);
  }

  var dialog = modal.dialog({
    width: window.innerWidth,
    height: window.innerHeight,
    resizable: false,
    draggable: false,
    modal: false,
    classes: {
      "ui-dialog": "is-fullscreen slide-from-right"
    },
    open: function open() {
      var titlebar = jQuery(this).parent().find(".ui-dialog-titlebar"),
          title = titlebar.find(".ui-dialog-title");

      if (title.length) {
        if (isStart) {
          title.html("Start wählen");
        } else {
          title.html("Ende wählen");
        }

        var helpBtn = jQuery("<span class=\"ui-dialog-titlebar-help ui-dialog-titlebar-btn\">Hilfe \n\t\t\t\t\t<svg class=\"icon\" width=\"30\" height=\"30\"><use xlink:href=\"#Hilfe_Klein\"></use></svg>\n\t\t\t\t\t</span>").insertAfter(title).on("click", function (evt) {
          openHelp(3);
        }); // remove standard button

        jQuery(this).parent().find(".ui-dialog-titlebar-close").remove();
        var closeBtn = jQuery("<span class='ui-dialog-titlebar-customclose ui-dialog-titlebar-btn'>").html("<svg class=\"icon\" width=\"30\" height=\"30\"><use xlink:href=\"#Pfeil_L\"></use></svg>Zur\xFCck").insertBefore(title).on("click", function (evt) {
          evt.preventDefault(); // this.close(event);

          modal.dialog("close");
        });
      }

      titlebar.append(jQuery("<span class='bubble'>"));
    },
    close: function close() {
      jQuery(this).dialog("destroy").remove();
    }
  });
  jQuery(window).on("resize", function () {
    dialog.dialog("option", "width", window.innerWidth);
    dialog.dialog("option", "height", window.innerHeight);
  });
});
jQuery("[data-autocomplete='favorites']").on("click", function (evt) {
  var modal = jQuery("<div id='mapDialog'></div>").appendTo("body"),
      // isStart = evt.target.getAttribute("name").includes("[start]"),
  callFnc = "haltestelle",
      positionPickerParams = {
    source: baxiSearchSettings.ajaxUrl[callFnc],
    currentPosition: {
      // default is Tirschenreuth, Germany
      lat: 49.8817161,
      lng: 12.3303441
    },
    closeOnSelect: false,
    lng: {
      btnConfirm: "zu den Favoriten"
    },
    onSelect: function onSelect(data, picker) {
      jQuery.ajax({
        url: baxiSearchSettings.ajaxUrl.station,
        //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
        method: "POST",
        data: {
          tx_c3baxi_baxisuche: {
            uid: data.key
          }
        }
      }).then(function (response) {
        var subtitle = picker.content.find(".subtitle");
        subtitle.append(jQuery("<span>").text(response.linie.join(", "))); // ignoreZone = response.zoneId;
      });
    },
    onSubmit: function onSubmit(item) {
      jQuery("<div />").prompt({
        question: "Geben Sie einen eigenen Namen ein",
        defaultText: item.title,
        submit: function submit(name) {
          Favorites.add(item.key, name);
          var shownFavorites = jQuery(".box.box--favorite"),
              clone = shownFavorites.last().clone();
          clone.find(".box-title").text(name);
          clone.find(".box-subtitle").text(item.title);
          clone.find("[data-action='deleteFavorite']").attr("data-uid", item.key);
          clone.css("opacity", 0);
          clone.insertAfter(shownFavorites.last());
          clone.animate({
            opacity: 1
          }, 300);
        }
      });
    }
  };

  if (!("GeoPicker" in jQuery.fn)) {
    $("<link/>", {
      rel: "stylesheet",
      type: "text/css",
      href: window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.css"
    }).appendTo("head");
    jQuery.getScript(window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.js", function () {
      jQuery(modal).GeoPicker(positionPickerParams);
    });
  } else {
    jQuery(modal).GeoPicker(positionPickerParams);
  }

  modal.dialog({
    width: window.innerWidth,
    height: window.innerHeight,
    resizable: false,
    draggable: false,
    modal: true,
    classes: {
      "ui-dialog": "is-fullscreen"
    },
    open: function open() {
      var titlebar = jQuery(this).parent().find(".ui-dialog-titlebar"),
          title = titlebar.find(".ui-dialog-title");

      if (title.length) {
        title.html("Favorit hinzufügen");
        var helpBtn = jQuery("<span class='ui-dialog-titlebar-help ui-dialog-titlebar-btn'>Hilfe <svg class=icon width=30 height='30'><use xlink:href='Hilfe_Klein'></use></svg></span>").insertAfter(title).on("click", function (evt) {
          alert("help");
        }); // remove standard button

        jQuery(this).parent().find(".ui-dialog-titlebar-close").remove();
        var closeBtn = jQuery("<span class='ui-dialog-titlebar-customclose ui-dialog-titlebar-btn'>").html("<svg class=\"icon\" width=\"30\" height=\"30\"><use xlink:href=\"#Pfeil_L\"></use></svg>Zur\xFCck").insertBefore(title).on("click", function (evt) {
          evt.preventDefault(); // this.close(event);

          modal.dialog("close");
        });
      }

      titlebar.append(jQuery("<span class='bubble'>"));
    },
    close: function close() {
      jQuery(this).dialog("destroy").remove();
    }
  });
});
jQuery("#kontoFavorites").on("click", "[data-action='deleteFavorite']", function (evt) {
  if (confirm("Wirklich loeschen")) {
    var target = jQuery(this),
        stationId = target.data("uid");
    jQuery.ajax({
      url: baxiSearchSettings.ajaxUrl.favorites,
      //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
      method: "POST",
      data: {
        tx_c3baxi_baxisuche: {
          doAction: "remove",
          uid: stationId
        }
      }
    }).then(function (response) {
      if (response.status === "removed") {
        target.closest(".box.box--favorite").animate({
          opacity: 0
        }, 300).slideUp(150, function () {
          jQuery(this).remove();
        });
      }
    });
  }
});
var datepickerFields = jQuery("[data-picker='time']", form);

if (form.length && datepickerFields.length) {
  jQuery.datetimepicker.setLocale("de");
  datepickerFields.datetimepicker({
    minDate: -1,
    minTime: "04:00",
    step: 30,
    format: "d.m.Y H:i",
    dayOfWeekStart: 1,
    onSelectTime: function onSelectTime(currentTime, field) {
      jQuery(field).nextAll("input").val(parseInt(currentTime.getTime() / 1000).toFixed(0));
    }
  });
}
/** Rating */


jQuery("[data-action='rate']").on("click", function (evt) {
  var el = jQuery(this),
      rateModal = jQuery("<div id='rateDialog'></div>").appendTo("body");
  rateModal.hide();
  var rateHTML = "\n<form method=\"post\" id=\"rateForm\" class=\"\">\n\t<input type=\"hidden\" name=\"uid\" value=\"".concat(el.data("booking"), "\">\n\t<div>\n\t\t<p class=\"h4\">Feedback</p>\n\t\t<h3>Fahrt bewerten</h3>\n\t\t<div class=\"align-center\">\n\t\t\t<p>Warst du zufrieden mit deiner Fahrt?</p>\n\t\t\t<input type=\"radio\" class=\"is-hidden\" name=\"rating\" value=\"0\" id=\"ratingNo\" />\n\t\t\t<input type=\"radio\" class=\"is-hidden\" name=\"rating\" value=\"1\" checked id=\"ratingYes\" />\n\t\t\t<label for=\"ratingNo\" style=\"display: inline-block; width: 66px;\"><span class=\"btn btn--marble\"><svg class=\"icon\"><use xlink:href=\"#dislike\"></use></svg></span>Nein</label>\n\t\t\t<label for=\"ratingYes\" style=\"display: inline-block; width: 66px;\"><span class=\"btn btn--marble\"><svg class=\"icon\"><use xlink:href=\"#like\"></use></svg></span>Ja</label>\n\t\t</div>\n\t\t<p class=\"align-center\">\n\t\t\t<label>Kommentar (optional)</label><br />\n\t\t\t<textarea name=\"comment\" cols=\"60\" rows=\"4\" placeholder=\"Kommentar hinzuf\xFCgen\"></textarea>\n\t\t</p>\n\t</div>\n\t<div class=\"box-footer box-footer--with-button\">\n\t\t<button type=\"submit\" class=\"btn btn--next\">Bewerten</button>\n\t</div>\n</form>");
  rateModal.append(rateHTML);

  var ratingOpen = function ratingOpen(evt, ui) {
    dialogDefaults.open(evt, ui);
    var parent = jQuery(evt.target);
    jQuery(".btn", parent).off("click");
    jQuery("#rateForm", parent).on("submit", function (evt) {
      evt.preventDefault();
      var rating = evt.target.elements.rating.value,
          comment = evt.target.elements.comment.value,
          id = evt.target.elements.uid.value;
      rating = rating * 100; // convert to percent values

      jQuery.ajax(baxiSearchSettings.ajaxUrl.rating, {
        method: "post",
        data: {
          "tx_c3baxi_baxisuche": {
            doAction: "addRating",
            rating: rating,
            comment: comment,
            id: id,
            type: "booking"
          }
        }
      }).then(function (response) {
        response = JSON.parse(response);
        parent.find("#rateForm").html("").hide();

        if (response.status === true) {
          var successHTML = "\n<div>\n\t<p class=\"h4\">Feedback</p>\n\t<h3>Danke f\xFCr deine Bewertung</h3>\n\t<p>Deine Bewertung wurde abgesendet. Vielen Dank, dass du uns hilfst, Baxi zu verbessern.</p>\n\t<div class=\"box-footer box-footer--with-button\">\n\t\t<span class=\"btn btn--back\">Schlie\xDFen</span>\n\t</div>\n</div>";
          parent.find("#rateForm").append(successHTML).show("slow");
          parent.on("click", ".box-footer .btn", function () {
            jQuery("#rateDialog").dialog("destroy").remove();
            jQuery(".ui-widget-overlay").remove();
          });
        }
      });
    });
  };

  var dialogParams = Object.assign({}, dialogDefaults, {
    open: ratingOpen,
    test: true
  });
  rateModal.dialog(dialogParams);
});
/** Booking */

jQuery("[data-action='cancel']").on('click', function (evt) {
  evt.preventDefault();
  jQuery('<div />').prompt({
    confirm: true,
    question: 'Fahrt stornieren?',
    defaultText: 'Wollen Sie die Bestellung wirklich stornieren?',
    buttons: {
      cancel: {
        text: 'Nein, Abbrechen'
      }
    },
    submit: function submit() {
      console.log('confirm - true');
    }
  });
});
/** Help Modals */

jQuery("a[href^='/hilfe?question']").on("click", function (evt) {
  evt.preventDefault();
  var link = jQuery(this),
      page = "",
      params = "",
      qid = false;

  var _link$0$href$split = link[0].href.split("?");

  var _link$0$href$split2 = _slicedToArray(_link$0$href$split, 2);

  page = _link$0$href$split2[0];
  params = _link$0$href$split2[1];
  params = params.split("&");

  for (var i = 0; i < params.length; i++) {
    var par = params[i].split("=");

    if (par[0] === "question") {
      qid = par[1];
    }
  }

  if (qid) {
    openHelp(qid);
  }

  return false;
});
jQuery("[data-action='help']").on("click", function (evt) {
  var qid = jQuery(this).data("question");

  if (typeof qid !== "undefined") {
    openHelp(qid);
  }
});

function openHelp() {
  var uid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
  var helpModal = jQuery("<div id='helpDialog'></div>").appendTo("body");
  jQuery.ajax({
    url: baxiSearchSettings.ajaxUrl.help,
    method: "POST",
    data: {
      tx_c3baxi_baxisuche: {
        question: uid
      }
    }
  }).done(function (response) {
    var FAQContent = "\n<p class=\"box-title\">\n<svg class=\"icon\"><use xlink:href=\"#Hilfe_Klein\"></use></svg> Hilfe</p>\n<h3>".concat(response.question, "</h3><div>\n<p>").concat(response.answer, "</p>");

    if (response.media) {
      if (response.media.images.length) {
        for (var i = 0; i < response.media.images.length; i++) {
          FAQContent += "<figure><img src='".concat(response.media.images[i].url, "' width='").concat(response.media.images[i].size[0], "'height='").concat(response.media.images[i].size[1], "' /></figure>");
        }
      }

      if (response.media.downloads.length) {
        for (var y = 0; y < response.media.downloads.length; y++) {
          FAQContent += "";
        }
      }

      if (response.media.youtube.length) {
        for (var _y = 0; _y < response.media.youtube.length; _y++) {
          FAQContent += "<iframe class='keep-ratio ratio-16/9' src='//www.youtube.com/embed/".concat(response.media.youtube[_y], "' width='800'></iframe>");
        }
      }
    }

    FAQContent += "\n</div>\n<div class=\"box-footer box-footer--with-button\">\n\t<span class=\"btn btn--back\" data-action=\"closeDialog\">Schliessen</span>\n</div>\n<span class=\"bubble\"></span>\n<div id=\"helpInfo\">\n\t<h3>Du kommst nicht weiter?</h3><p>Ruf uns einfach an. Wir helfen dir gerne.</p>\n\t<p><a href=\"tel:096317929899\"><svg class=\"icon\"><use xlink:href=\"#call\"></use></svg> 0 96 31 / 79 29 899</a></p>\n</div>"; // let FAQ = jQuery(FAQContent);

    helpModal.html(FAQContent);
    var dialogParams = Object.assign({}, dialogDefaults, {
      /*custom settings*/
    });
    helpModal.dialog(dialogParams);
  });
}
/** StepForm */


jQuery.fn.stepForm = function (params) {
  if (this.length === 0) return this; // support mutltiple elements

  if (this.length > 1) {
    this.each(function () {
      $(this).stepForm(params);
    });
    return this;
  }

  var form = {
    steps: [],
    stepCount: 0,
    stepper: {},
    active: 0,
    previous: false,
    innerWrap: null
  },
      el = this,
      defaults = {
    controls: true,
    buttons: {
      next: ".stepForm-next",
      prev: ".stepForm-prev"
    },
    animation: false
  },
      settings = jQuery.extend({}, defaults, params);

  function init() {
    // get all steps
    el.addClass("stepForm");
    form.steps = el.children();
    form.stepCount = form.steps.length;
    form.stepper = {};
    form.active = 0;
    form.first = true;
    setup();
  }

  function setup() {
    form.innerWrap = jQuery("<div class='stepForm-inner'></div>");
    form.innerWrap.css({
      overflowX: "hidden",
      position: "relative"
    });

    _setWrapperHeight();

    jQuery(window).on('resize', _setWrapperHeight);
    el.wrapInner(form.innerWrap);
    form.steps.addClass("stepForm-step"); // hide all except first

    form.steps.hide();

    if (settings.controls === true) {
      form.stepper.el = jQuery("<div class='stepForm-stepper'></div>").appendTo(el);
      form.stepper.el.css({
        textAlign: "center",
        margin: "16px auto 0"
      });
      form.stepper.steps = [];

      for (var i = 0; i < form.stepCount; i++) {
        var step = jQuery("<span data-idx='".concat(i, "'></span>"));
        step.data("idx", i).css({
          display: "inline-block",
          height: "10px",
          width: "10px",
          borderRadius: "100%",
          border: "2px solid #FFF",
          margin: "5px 5px"
        });

        if (i === form.active) {
          step.addClass("is-active");
        }

        step.on("click", function (evt) {
          form.previous = form.active;
          form.active = parseInt(this.dataset.idx);
          show();
        });
        step.appendTo(form.stepper.el);
        form.stepper.steps[i] = step;
      }
    } // controls end


    jQuery(settings.buttons.next).on("click", next);
    jQuery(settings.buttons.prev).on("click", prev);
    show();
    form.first = false;
  }

  function _setWrapperHeight() {
    var evt = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
    var wrapHeight = 0;
    form.steps.each(function (idx, stepElement) {
      wrapHeight = Math.max(wrapHeight, jQuery(stepElement).height());
    });
    form.innerWrap.css('height', wrapHeight + 'px');
  }

  function show() {
    if (!form.first) {
      /** laeuft ohne animation*/
      if (settings.animation === false) {
        form.steps.hide().toggleClass("is-active", false);
        jQuery(form.steps[form.active]).toggleClass('is-active', true).show();
      } else {
        /** animation */
        var _prev = jQuery(form.steps[form.previous]),
            act = jQuery(form.steps[form.active]);

        _prev.addClass('animate-out');

        act.addClass('animate-in').show();

        if (form.previous > form.active) {
          _prev.addClass('is-reverse');

          act.addClass('is-reverse');
        }

        _prev.one('animationend', function () {
          jQuery(this).hide().removeClass('is-reverse animate-out is-active');
        });

        act.one('animationend', function () {
          act.removeClass('is-reverse').toggleClass('animate-in is-active');
        }); // jQuery(form.steps[ form.active ]).toggleClass("is-active", true).css({animation: "slideInRight .5s linear forwards"}).show();
      }
    } else {
      jQuery(form.steps[form.active]).toggleClass("is-active", true).show();
    }

    setStepper();
  }

  function next(evt) {
    evt.preventDefault();

    if (form.active < form.stepCount - 1) {
      form.previous = form.active;
      form.active++;
      show();
    }

    return false;
  }

  function prev(evt) {
    evt.preventDefault();

    if (form.active > 0) {
      form.previous = form.active;
      form.active--;
      show();
    }

    return false;
  }

  function setStepper() {
    if (settings.controls) {
      for (var s = 0; s < form.stepCount; s++) {
        form.stepper.steps[s].removeClass("is-active");
        if (s === form.active) jQuery(form.stepper.steps[s]).addClass("is-active");
      }
    }
  }

  init();
  return this;
};

jQuery(".js-steps").stepForm({
  animation: true
});
$(window).on("load", function (e) {
  if (Store && (typeof baxiSearchSettings === "undefined" ? "undefined" : _typeof(baxiSearchSettings)) === "object") {
    if (typeof baxiSearchSettings.favorites === "string") {
      Store.set("favorites", JSON.parse(baxiSearchSettings.favorites));
    }

    Store.set("settings", baxiSearchSettings);
  }
});

//# sourceMappingURL=default.js.map