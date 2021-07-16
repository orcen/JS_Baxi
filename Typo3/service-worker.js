"use strict";

self.addEventListener('install', function ( event ) {

	// let cacheList = [
	// 	'/index.html'
	// ];
	//
	// event.waitUntil(
	// 	caches.open(CACHE_NAME)
	// 		  .then(function ( cache ) {
	// 			  return cache.addAll(cacheList);
	// 		  })
	// );
});

self.addEventListener('activate', function ( event ) {
	event.waitUntil(
		caches.keys().then(function ( keyList ) {
			return Promise.all(keyList.map(function ( key ) {
				if ( CACHE_NAME.indexOf(key) === -1 ) {
					return caches.delete(key);
				}
			}));
		})
	);
});