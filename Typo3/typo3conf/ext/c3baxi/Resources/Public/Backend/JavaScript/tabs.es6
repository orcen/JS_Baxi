require([ "jquery" ], function ( $ ) {

	jQuery('[data-target]', '.baxi_tabs-header').bind('click', function ( evt ) {
		let grp = jQuery(evt.currentTarget).closest('.baxi_tabs');
		jQuery('.baxi_tabs-tab', grp).removeClass('is-active');

		jQuery('.baxi_tabs-tab[data-tabid="' + jQuery(evt.target).data('target') + '"]', grp).addClass('is-active');

		jQuery('.baxi_tabs-header [data-target]', grp).removeClass('is-active');
		jQuery(evt.target, grp).addClass('is-active');
	});
});