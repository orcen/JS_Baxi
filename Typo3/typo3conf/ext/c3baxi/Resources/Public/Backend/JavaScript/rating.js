"use strict";

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance"); }

function _iterableToArrayLimit(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

require(["jquery"], function ($) {
  jQuery.fn.C3Graph = function (params) {
    if (this.length == 0) return this;
    /** support mutltiple elements */
    // if ( this.length > 1 ) {
    // 	this.each(function () {
    // 		$(this).GeoPicker(params);
    // 	});
    // 	return this;
    // }

    var el = jQuery(this),
        defaults = {
      data: {},
      wrapper: {
        style: {
          width: '100%',
          height: 300
        }
      },
      classes: {
        bar: 'rating-bar',
        positiveBar: 'rating-bar_inner'
      }
    },
        settings = jQuery.extend({}, defaults, params);

    var init = function init() {
      var max = Math.max.apply(Math, Object.keys(settings.data).map(function (key) {
        return settings.data[key].value;
      }));
      setup();
    };

    var setup = function setup() {
      el.css(settings.wrapper.style);
      var mostCount = Math.max.apply(Math, Object.values(settings.data).map(function (i) {
        return i.count;
      }));
      Object.keys(settings.data).map(function (key) {
        var totalBlock = document.createElement('DIV'),
            positiveBlock = document.createElement('DIV'),
            heightTotal = 100 * settings.data[key].count,
            heightPositive = settings.data[key].value / (heightTotal / 100);
        totalBlock.classList.add(settings.classes.bar);
        positiveBlock.classList.add(settings.classes.positiveBar);
        totalBlock.style.width = '45px';
        totalBlock.style.height = _getHeight(mostCount, settings.data[key].count) + 'px'; // totalBlock.style.background = 'red';
        // totalBlock.dataset.value = (Math.ceil(heightPositive * 10) / 10) + '%';

        totalBlock.dataset.count = settings.data[key].count;
        var date = '',
            line = '',
            fahrtId = '';

        var _key$split = key.split('_');

        var _key$split2 = _slicedToArray(_key$split, 3);

        date = _key$split2[0];
        line = _key$split2[1];
        fahrtId = _key$split2[2];
        totalBlock.dataset.date = date;
        totalBlock.dataset.name = "".concat(line, " - ").concat(fahrtId);
        positiveBlock.dataset.value = Math.ceil(heightPositive * 10) / 10 + '%';
        positiveBlock.style.height = heightPositive + '%';
        totalBlock.appendChild(positiveBlock);
        el.append(totalBlock);
      });
    };

    init();

    function _getHeight(mostParts, act) {
      var maxValue = el.innerHeight() * .8;
      return maxValue / (100 / (act / (mostParts / 100)));
    }
  };

  jQuery('#rating').C3Graph({
    data: graphData
  });
});

//# sourceMappingURL=rating.js.map