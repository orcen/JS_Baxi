require([ "jquery" ], function ( $ ) {

	$('[data-toggle="collapse"][data-target]').on('click', ( evt ) => {

		let target = $(evt.currentTarget).data('target');
		if ( target[ 0 ] === '#' ) {
			$(target).toggle();
		}
		else {
			$(target, evt.currentTarget).toggle();
		}
	});

	$('[data-action="selectStation"]').on('change', function () {

		let el = $(this),
			target = $(el.data('target')),
			selectedZone = el.find('[value="' + el.val() + '"]').data('zone'),
			selectName = (el.data('target') === '#start_station') ? 'end' : 'start';

		jQuery('option', 'select[name="tx_c3baxi_web_c3baxibaxi[' + selectName + ']"]')
			.attr('disabled', false)
			.filter('[data-zone="' + selectedZone + '"]')
			.attr('disabled', true);


		$.ajax({
			url: '/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
			method: 'post',
			data: {
				tx_c3baxi_baxisuche: {
					uid: el.val()
				}
			}
		}).then(( response ) => {

			let tmpl = $('#boxTemplate')[ 0 ].innerHTML,
				html = '';
			html = tmpl.replace(/##name##/g, response.name)
			           .replace(/##zone##/g, response.zone)
			           .replace(/##linie##/g, response.linie.join(','));
			target.html(html);
		});

	});

	jQuery('#findRoute').on('submit', function ( evt ) {
		evt.preventDefault();
		jQuery('.error', '.output').remove();
		let form = this;

		$.ajax({
			url: '/?tx_c3baxi_baxisuche[action]=ride&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
			method: 'post',
			data: {
				tx_c3baxi_baxisuche: {
					doAction: 'find',
					startStation: form[ 'tx_c3baxi_web_c3baxibaxi[start]' ].value,
					endStation: form[ 'tx_c3baxi_web_c3baxibaxi[end]' ].value,
					date: form[ 'tx_c3baxi_web_c3baxibaxi[date]' ].value,
					time: form[ 'tx_c3baxi_web_c3baxibaxi[time]' ].value
				}
			}
		}).then(createRideList);
	});

	function createRideList( response ) {
		if ( !response.status ) {
			let error = jQuery('<div class="error">').text('Es ist ein Fehler aufgetreten!');
			jQuery('.output').append(error);
			return false;
		}

		if ( response.status === 'not_found' ) {
			let error = jQuery('<div class="error">').text('Es wurden keine Fahrten gefunden!');
			jQuery('.output').append(error);
			return false;
		}

		let output = jQuery('.list', '.output'),
			data = response.data,
			list = response.data.routes,
			template = jQuery('#ride')[ 0 ].innerHTML;

		output.html( null );

		for ( let r = 0; r < list.length; r++ ) {
			let ride = template,
				rideDate = new Date(data.date.date),
				departure = new Date(list[ r ].abfahrt.date),
				arrival = new Date(list[ r ].ankunft.date),
				departureStr = String(departure.getHours()).padStart(2, '0') + ':' + String(departure.getMinutes()).padStart(2, '0'),
				arrivalStr = String(arrival.getHours()).padStart(2, '0') + ':' + String(arrival.getMinutes()).padStart(2, '0'),
				bookingTimeStr = String(rideDate.getHours()).padStart(2, '0') + ':' + String(rideDate.getMinutes()).padStart(2, '0');


			ride = ride.replace(/##line##/g, list[ r ].linie)
			           .replace(/##duration##/g, list[ r ].dauer)
			           .replace(/##departure##/g, departureStr)
			           .replace(/##arrival##/g, arrivalStr)
			           .replace(/##date##/g, rideDate.toLocaleDateString('de-DE') )
			           .replace(/##time##/g, bookingTimeStr);


			ride = jQuery(ride);
			ride.find('input[name="tx_c3baxi_web_c3baxibaxi[fahrt]"]').val( list[r].uid);
			ride.find('input[name="tx_c3baxi_web_c3baxibaxi[date]"]').val( (rideDate.getTime() / 1000) );
			ride.find('input[name="tx_c3baxi_web_c3baxibaxi[startStation]"]').val( data.start);
			ride.find('input[name="tx_c3baxi_web_c3baxibaxi[endStation]"]').val( data.end);

			output.append( ride );
		}
	}
});

