"use strict";

function BaxiFavorites() {
  var BF = this;
  BF.items = [];
  BF.template = document.createElement('LI');
  BF.settings = {
    maxItems: 5
  };

  BF.add = function () {
    var uid = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var stationName = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var result = false;
    jQuery.ajax({
      url: baxiSearchSettings.ajaxUrl.favorites,
      //'/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
      method: 'POST',
      data: {
        tx_c3baxi_baxisuche: {
          doAction: 'add',
          uid: uid,
          name: stationName
        }
      }
    }).then(function (response) {
      if (response.status === 'added') {
        BF.items.push({
          station: uid,
          name: stationName
        });
        Store.set('favorites', BF.items);
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
      //'/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
      method: 'POST',
      data: {
        tx_c3baxi_baxisuche: {
          doAction: 'remove',
          uid: uid
        }
      }
    }).then(function (response) {
      BF.items = BF.items.filter(function (item) {
        return item.station !== uid;
      }).values();
      Store.set('favorites', BF.items);
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
      var storedFavorites = Store.get('favorites');

      if (typeof storedFavorites === 'string') {
        BF.items = JSON.parse(storedFavorites) || [];
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
      child.setAttribute('data-key', BF.items[i].station);
      child.setAttribute('data-name', BF.items[i].name);
      list.append(child);
    }

    parent.append(list);
  };

  BF.init();
}

//# sourceMappingURL=Favorites.js.map