function BaxiFavorites() {
	let BF = this;
	BF.items = [];
	BF.template = document.createElement('LI');
	BF.settings = {
		maxItems: 5
	};

	BF.add = function ( uid = 0, stationName = null ) {
		let result = false;
		jQuery.ajax({
			url: baxiSearchSettings.ajaxUrl.favorites, //'/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
			method: 'POST',
			data: {
				tx_c3baxi_baxisuche: {doAction: 'add', uid: uid, name: stationName}
			}
		}).then(function ( response ) {
			if ( response.status === 'added' ) {
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

	BF.remove = function ( uid = 0 ) {
		let result = false;
		jQuery.ajax({
			url: baxiSearchSettings.ajaxUrl.favorites, //'/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
			method: 'POST',
			data: {
				tx_c3baxi_baxisuche: {doAction: 'remove', uid: uid}
			}
		}).then(function ( response ) {
			BF.items = BF.items.filter(( item ) => item.station !== uid).values();
			Store.set('favorites', BF.items);
			result = true;
		});

		return result;
	};
	/**
	 *
	 * @param {Integer} uid
	 */
	BF.contains = function ( uid = 0 ) {
		return BF.items.filter(( item ) => parseInt(item.station) === parseInt(uid)).length;
	};

	BF.init = function () {
		if ( Store ) {
			let storedFavorites = Store.get('favorites');

			if ( typeof storedFavorites === 'string' ) {
				BF.items = JSON.parse(storedFavorites) || [];
			}
			else {
				BF.items = storedFavorites;
			}
		}
	};

	/**
	 *
	 * @param {HTMLElement} parent
	 * @param {HTMLElement} template
	 */
	BF.appendTo = function ( parent = false, template = BF.template ) {
		if ( !parent ) return false;
		let list = document.createDocumentFragment();

		for ( let i = 0; i < BF.items.length; i++ ) {
			let child = template.cloneNode( true );
			child.innerHTML = child.innerHTML + BF.items[ i ].name;
			child.setAttribute('data-key', BF.items[ i ].station);
			child.setAttribute('data-name', BF.items[ i ].name);
			list.append(child);
		}
		parent.append(list);
	};

	BF.init();
}