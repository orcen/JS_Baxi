requirejs([ 'jquery', 'jquery-ui/draggable', 'jquery-ui/droppable', 'jquery-ui/sortable' ], function ( jQuery ) {

	$(function() {
	jQuery('[data-toggle="filter"]').on('input', function ( evt ) {
		let s = evt.currentTarget.value,
			target = jQuery(evt.currentTarget.dataset.target),
			items = [];

		if ( target.length === 0 ) return;

		if ( target.prop('tagName') === 'SELECT' ) { // SelectBox
			items = target.find('option');
		}
		else {
			items = target.find('li');
		}

		items.show(); // show all
		// min length 3
		if ( s < 3 ) return;

		items.map(function () {
			var txt = jQuery(this).text();
			if ( false === txt.toLowerCase().includes(s.toLowerCase()) ) jQuery(this).hide();
		});
	});


	function dragClone( evt ) {
		// console.log(evt);
		let clone = jQuery(evt.currentTarget).clone(true);
		clone.appendTo('body');
		return clone;
	}

	var ajaxTarget = jQuery('form').data('ajax');



	jQuery('[data-draggable="true"]').draggable({revert: "invalid", helper: dragClone});
	jQuery('[data-droppable="true"]').droppable({
		drop: ( evt, ui ) => {
			let dropArea = jQuery(evt.target),
				item = jQuery(ui.draggable[ 0 ]),
				pid = dropArea.data('uid');

			//

			let i = 0;
			if( dropArea.attr('id') !== 'freie_zonen') {
				dropArea.children('.list-item').each(function () {
					if ( jQuery(this).offset().top >= ui.offset.top ) {
						i = 1;
						item.insertBefore(jQuery(this));
						return false;
					}
				});
			}

			if( i !== 1) {
				item.appendTo(dropArea);
			}

			item.draggable({revert: "invalid", helper: dragClone});
			// 	stellenId = ui.draggable[ 0 ].dataset.id;

			if ( pid !== '' ) {
				jQuery.ajax(
					TYPO3.settings.ajaxUrls[ ajaxTarget ],
					{
						data: {
							'tx_c3baxi_baxisuche': {
								parentUid: pid,
								childUid: item.data('uid')
							}
							// 'type': 666
						},
						method: 'get',
						complete: function ( result ) {
							console.log( result );
							if( result.status === 200) {
								if( result.responseJSON.success === true ) {

									if( dropArea.data('field') !== undefined ) {
										let list = [];
										dropArea.children('.list-item').each(function(){
											list.push( jQuery(this).data('uid') );
										});

										jQuery('[name="'+dropArea.data('field')+'"]').val( list.join(',') );
									}
								}
							}
						}
					});
			}
			else {
				let list = jQuery('[name="tx_c3baxi_web_c3baxibaxi[liste]"]');
				list.val(list.val() + ',' + item.data('uid'));
			}
		}
	});
	});
});
