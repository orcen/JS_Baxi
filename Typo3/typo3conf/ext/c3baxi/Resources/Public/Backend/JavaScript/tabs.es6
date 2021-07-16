require([ "jquery" ], function ( jQuery ) {



	function createTabs() {
		jQuery('[data-target]', '.baxi_tabs-header').on('click', function (evt) {
			let grp = jQuery(evt.currentTarget).closest('.baxi_tabs');
			jQuery('.baxi_tabs-tab', grp).removeClass('is-active');

			jQuery('.baxi_tabs-tab[data-tabid="' + jQuery(evt.target).data('target') + '"]', grp).addClass('is-active');

			jQuery('.baxi_tabs-header [data-target]', grp).removeClass('is-active');
			jQuery(evt.target, grp).addClass('is-active');
		});
	}

	if( navigator.userAgent.indexOf('Chrome') > 0 ) {
		jQuery(window).on('load', function () {
			createTabs();
		});
	}
	else {
		createTabs();
	}

});