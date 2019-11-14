"use strict";

function C3Store() {
  var storageKey = 'c3baxi';
  this.store = {};

  this.init = function () {
    if (localStorage.getItem(storageKey) === null) {
      localStorage.setItem(storageKey, JSON.stringify(this.store));
    } else {
      this.store = JSON.parse(localStorage.getItem(storageKey));
    }
  };

  this.set = function (name, val) {
    this.store[name] = val;
    this.updateStorage();
  };

  this.get = function (name) {
    if (typeof this.store[name] == 'undefined') return '';
    return this.store[name];
  };

  this.updateStorage = function () {
    this.store['tstamp'] = Date.now();
    localStorage.setItem(storageKey, JSON.stringify(this.store));
  };

  function isStorageSupported() {
    return 'localStorage' in window;
  }

  if (isStorageSupported()) {
    this.init();
  }
}

var Store = new C3Store();

//# sourceMappingURL=Store.js.map