"use strict";

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _construct(Parent, args, Class) { if (isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

// import * as JSON from 'json5';
jQuery.fn.GeoPicker = function (params) {
  if (!this.length) return this;
  var picker = {
    /** Elements */
    wrapper: false,
    ticker: false,
    list: false,
    map: false,
    form: false,
    overlay: false,
    history: false,
    suggest: false,
    markers: {},

    /** Status controllers */
    mapMoved: false,
    searchOpen: false,
    selected: false,
    autoComplete: false
  },
      el = jQuery(this),
      defaults = {
    debug: false,
    useGeolocation: true,
    id: 'GeoPicker',
    source: false,
    positions: false,
    listLength: 5,
    closeOnSelect: true,
    currentPosition: {
      lat: 0,
      lng: 0
    },
    markerIcon: window.location.origin + '/typo3conf/ext/c3baxi/Resources/Public/Icons/Hst1@2x.png',
    markerIconSelected: window.location.origin + '/typo3conf/ext/c3baxi/Resources/Public/Icons/Hst1_selected@2x.png' + '#selected',
    positionIcon: window.location.origin + '/typo3conf/ext/c3baxi/Resources/Public/Icons/Position@2x.png' + '#selected',
    defaultMapZoom: 16,
    boundsOnSelect: false,

    /** Templates */
    templates: {
      wraper: '<div class="position_picker"></div>',
      map: '<div id="map" class="position_picker-map"></div>',
      list: '<ul class="position_picker-list"></ul>',
      listItem: '<li class="position_picker-list_item"></li>',
      picker: '<span class="position_picker-picker"></span>',
      content: '<div class="position_picker-content"></div>'
    },
    lng: {
      btnConfirm: 'Haltestelle wählen'
    },
    customSearchButtons: function customSearchButtons() {},
    customButtons: function customButtons() {},

    /** Callbacks */
    onSelect: false,
    onSubmit: false
  },
      settings = jQuery.extend({}, defaults, params);

  var init = function init() {
    /** load positions from source */
    if (settings.source) {
      if (typeof settings.source === 'string') {
        settings.positions = getPositions();
      }
    }
    /** activate geolocation */


    if (settings.useGeolocation) {
      el.on('position-changed', updateMap);

      if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
          setCurrentPosition(position);
          navigator.geolocation.watchPosition(setCurrentPosition);
        });
      }
    }
    /** build structure */


    setup();
  };

  var setup = function setup() {
    picker.wrapper = jQuery(settings.templates.wraper).attr('id', settings.id).appendTo(el);
    picker.overlay = jQuery('<div class="position_picker-overlay"></div>').appendTo(picker.wrapper);

    if (settings.debug) {
      picker.ticker = jQuery(settings.templates.picker).appendTo(picker.wrapper);
      picker.ticker.text(settings.currentPosition.lat + ', ' + settings.currentPosition.lng);
    }

    initMap(function () {
      picker.content = jQuery(settings.templates.content).appendTo(picker.wrapper);
      buildContent();
    });
  };

  function close() {
    el.remove();
  }

  function setCurrentPosition(position) {
    var newPosition = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };

    if (JSON.stringify(newPosition) !== JSON.stringify(settings.currentPosition)) {
      settings.currentPosition = newPosition;
      el.trigger('position-changed', position);
    }
  }

  function initMap() {
    var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : function () {};

    if (typeof google === 'undefined') {
      callback();
    } else {
      picker.map = new google.maps.Map(jQuery(settings.templates.map).appendTo(picker.wrapper)[0], {
        center: settings.currentPosition,
        zoom: settings.defaultMapZoom,
        disableDefaultUI: true,
        gestureHandling: 'greedy'
      });
      /** create marker for user */

      picker.currentPosition = new google.maps.Marker({
        position: settings.currentPosition,
        map: picker.map,
        title: 'mein Standort',
        icon: settings.positionIcon + '#selected'
      });
      /** move center up */

      picker.map.panTo(settings.currentPosition);
      picker.map.panBy(0, 100);
      /** set control attribute */

      picker.map.addListener('drag', function (evt) {
        picker.mapMoved = true;
      });
      /** create marker for positions */

      settings.positions.then(function (positions) {
        picker.markers = {};
        Object.keys(positions).map(function (key) {
          var marker = new google.maps.Marker({
            // map: picker.map,
            map: null,
            position: _construct(google.maps.LatLng, _toConsumableArray(positions[key].latLng.split(','))),
            title: positions[key].name,
            key: key,
            data: positions[key],
            icon: settings.markerIcon,
            animation: google.maps.Animation.DROP
          });
          marker.addListener('click', select);
          picker.markers[key] = marker;
        });

        var showVisibleMarkers = function showVisibleMarkers(evt) {
          // console.log(showVisibleMarkers, evt);
          if (picker.map) {
            var bounds = picker.map.getBounds();

            if (typeof bounds != 'undefined') {
              Object.values(picker.markers).forEach(function (item) {
                if (bounds.contains(item.position) && item.map !== picker.map) {
                  item.setMap(picker.map);
                } else if (false === bounds.contains(item.position)) {
                  item.setMap(null);
                }
              });
            }
          }
        };
        /** toggle markers if in/out viewport */


        google.maps.event.addListener(picker.map, 'tilesloaded', showVisibleMarkers); // google.maps.event.addListener(picker.map, 'idle', showVisibleMarkers );

        callback();
      });
    }
  }

  function switchAnimation() {
    Object.values(picker.markers).forEach(function (mark) {
      return setTimeout(mark.setAnimation(google.maps.Animation.BOUNCE), 50);
    });
  }

  var updateMap = function updateMap() {
    if (settings.debug) {
      picker.ticker.text(Number(settings.currentPosition.lat).toPrecision(8) + ', ' + Number(settings.currentPosition.lng).toPrecision(8));
    }

    if (picker.map || picker.currentPosition) {
      // console.log( picker.mapMoved );
      if (picker.map && !picker.mapMoved) {
        // center the map, if user not dragged
        picker.map.panTo(settings.currentPosition);
        picker.map.panBy(0, 100);
      }
      /** update user position */


      if (picker.currentPosition) {
        picker.currentPosition.setPosition(settings.currentPosition);
      }
    }
  };

  var buildContent = function buildContent() {
    picker.content.html(null);
    picker.history = jQuery('<div class="position_picker-list position_picker-list--history"><h3>Verlauf</h3></div>');

    if ((typeof Store === "undefined" ? "undefined" : _typeof(Store)) !== undefined) {
      var history = Store.get('history');
    }

    buildSearch();
    buildSelected();
    picker.search.show();
  };

  var buildSearch = function buildSearch() {
    picker.search = jQuery('<div class="position_picker-content position_picker-content--search">').appendTo(picker.wrapper).hide();
    picker.field = jQuery('<input type="text" class="input-field" name="geoPicker_search" placeholder="Haltestelle suchen" />').appendTo(picker.search).on('focus', function (evt) {
      if (!picker.searchOpen) {
        openSearch(); // getSupportActionBar().hide();
      }
    }).on('input', function (evt) {
      if (picker.autoComplete === false) {
        picker.autoComplete = true;
        picker.history.hide();
        picker.suggest = jQuery('<div class="position_picker-list position_picker-list--suggest"><h3>Gefundene Haltestellen</h3></div>').insertAfter(picker.field);
        picker.suggestList = jQuery('<ul >').insertAfter(picker.suggest.find('h3'));
      } else {
        var searchWord = evt.target.value,
            list = {},
            found = {};

        if (settings.source) {
          picker.suggestList.empty();
          settings.positions.then(function (positions) {
            found = Object.filter(positions, function (el, idx) {
              if (el.name.toLowerCase().includes(searchWord.toLowerCase())) {
                var latLng = el.latLng.split(','),
                    position = new google.maps.LatLng(latLng[0], latLng[1]);
                el.key = idx;
                el.distance = google.maps.geometry.spherical.computeDistanceBetween(position, picker.currentPosition.position);
                return true;
              }

              return false;
            });
            found = Object.values(found).sort(function (a, b) {
              return a.distance - b.distance;
            }).slice(0, 8);
            jQuery.each(found, function (idx, item) {
              var res = jQuery('<li>').html(item.name);

              if (item.distance) {
                res.append(jQuery('<small>').text(formatDistance(item.distance)));
              }

              res.on('click', function (evt) {
                select(jQuery.extend(evt, {
                  data: item
                })); //.bind(this);
              });
              picker.suggestList.append(res);
            });
          });
        } // else {
        // 	list = settings.positions;
        // }

      }
    });
    picker.search.append(settings.customSearchButtons(picker, el, select));
  };

  function buildSelected() {
    picker.selectedBlock = jQuery('<div>').appendTo(picker.content).hide();
  }

  function openSearch() {
    picker.overlay.show(); // picker.content

    picker.searchOpen = true;
    var closeBtn = jQuery('<span class="position_picker-content-close">').hide().appendTo(picker.search).on('click', closeSearch);
    picker.search.toggleClass('is-extended', true).removeClass('slidedown').addClass('slideup');
    closeBtn.show(400); // picker.history.insertAfter(picker.field);
  }

  var closeSearch = function closeSearch() {
    picker.content.toggleClass('is-extended', false).removeClass('slideup').addClass('slidedown').find('span.position_picker-content-close').hide();
    picker.history.hide();
    picker.overlay.hide();
    if (picker.suggest) picker.suggest.html(null);
    if (picker.field) picker.field.val(null);
    picker.searchOpen = picker.autoComplete = false;
  }; // function buildList() {
  //
  // 	let list = settings.positions,
  // 		cnt = 0;
  // 	jQuery.each(list, function ( idx, item ) {
  //
  // 		let listItem = jQuery(settings.templates.listItem)
  // 			.text(item.name)
  // 			.appendTo(picker.list);
  //
  // 		listItem.data({id: idx, coords: item.latLng});
  // 		cnt++;
  // 		if ( cnt === settings.listLength )
  // 			return false;
  // 	});
  // 	picker.list.removeClass('is-loading');
  // }


  function select(evt) {
    if (typeof this !== 'undefined') {
      /** mark as selected */
      // console.log( picker.selected !== false , typeof picker.selected.setIcon === 'function'  );
      if (picker.selected !== false && typeof picker.selected.setIcon === 'function') {
        picker.selected.setIcon(window.location.origin + '/typo3conf/ext/c3baxi/Resources/Public/Icons/Hst1@2x.png');
      }

      picker.selected = this;
      this.setIcon(window.location.origin + '/typo3conf/ext/c3baxi/Resources/Public/Icons/Hst1_selected@2x.png' + '#selected');
    } else {
      picker.selected = evt.data;
      var latLng = evt.data.latLng.split(',');
      picker.selected = picker.markers[evt.data.key];
      if (picker.selected.setIcon(settings.markerIconSelected)) closeSearch();
    }

    if (settings.boundsOnSelect === true) {
      var bounds = new google.maps.LatLngBounds();
      bounds.extend(picker.selected.position);
      bounds.extend(picker.currentPosition.position);
      picker.map.fitBounds(bounds);
      picker.map.panBy(0, 100);
    } // console.log( picker.selected ,typeof this !== 'undefined' );


    el.trigger('selected', picker.selected);

    if (settings.closeOnSelect) {
      if (settings.onSubmit !== false) {
        settings.onSubmit(picker.selected, picker);
      }

      close();
    } else {
      picker.map.setZoom(picker.map.getZoom() + 1);

      if (settings.onSelect !== false) {
        settings.onSelect(picker.selected, picker);
      }

      showSelected();
    }
  }

  function showSelected() {
    picker.search.hide();
    picker.selectedBlock.hide().html(null); // picker.content
    //       .removeClass('is-extended')
    //       .toggleClass('is-selected', true);

    picker.selectedBlock = jQuery('<div class="position_picker-content position_picker-content--selected">').appendTo(picker.wrapper);
    /** Title */

    var title = jQuery('<h3>').html(picker.selected.title);
    title.appendTo(picker.selectedBlock);
    /** Subtitle */

    var subTitle = jQuery('<p>').addClass('subtitle');
    subTitle.appendTo(picker.selectedBlock);
    var distance = google.maps.geometry.spherical.computeDistanceBetween(picker.selected.position, picker.currentPosition.position);

    if (distance) {
      subTitle.append(jQuery('<span>').html(Math.round(distance).toFixed(0).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ') + ' m'));
    }
    /** confirm and close */


    var confirm = jQuery('<button>').addClass('btn btn--next').html(settings.lng.btnConfirm + ' ' + '<svg class="icon" width="30" height="30"><use xlink:href="#Pfeil_R"></use></svg>');
    confirm.appendTo(picker.selectedBlock);
    confirm.on('click', function () {
      if (settings.onSubmit !== false) {
        settings.onSubmit(picker.selected, picker);
      }

      close();
    });
    /** Other Buttons and Controls */

    var controls = jQuery('<div class="controls">').appendTo(picker.selectedBlock);
    controls.append(settings.customButtons(picker));
    var cancelBtn = jQuery('<span class="controls-btn">').html('<span class="btn btn--marble has-icon">' + '<svg class="icon" width="24" height="24">' + '<use xlink:href="#Abbrechen"></use></svg>' + '</span> Abbrechen').appendTo(controls);
    cancelBtn.on('click', function () {
      if (typeof picker.selected.setIcon === 'function') {
        picker.selected.setIcon(settings.markerIcon);
      }

      picker.map.setZoom(settings.defaultMapZoom);
      picker.selected = false;
      picker.content.toggleClass('is-selected', false);
      picker.selectedBlock.hide();
      picker.search.show(300);
    });

    if (picker.map) {
      picker.map.panTo(picker.selected.position);
      picker.map.panBy(0, 100);
    }

    var contentHeight = picker.selectedBlock.height() + parseInt(picker.content.css('padding-top')) + parseInt(picker.content.css('padding-bottom'));
    picker.selectedBlock[0].style.setProperty('--content-height', contentHeight + 'px'); // picker.content.slideUp();
    // picker.selectedBlock.slideDown();
  }

  function formatDistance(dst) {
    var precision = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
    var units = ['m', 'km', 'etwas ist falsch :D'],
        uIdx = 0;

    while (dst > 1000) {
      dst = dst / 1000;
      uIdx++;
    }

    return Math.round(dst, precision).toString() + ' ' + units[uIdx];
  }

  function getPositions() {
    return new Promise(function (resolve, reject) {
      jQuery.ajax({
        url: settings.source,
        method: 'POST',
        data: {
          tx_c3baxi_baxisuche: {
            'ignoreZone': ignoreZone
          }
        }
      }).then(function (response) {
        resolve(response);
      })["catch"](function (error) {
        reject([]);
      });
    });
  }

  init();
};

//# sourceMappingURL=GeoPicker.js.map