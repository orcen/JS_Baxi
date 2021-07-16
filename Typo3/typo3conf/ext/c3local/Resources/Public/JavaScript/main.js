jQuery.fn.Number2 = function ( params ) {
	if ( this.length == 0 ) return this;
	/** support mutltiple elements */
	if ( this.length > 1 ) {
		this.each(function () {
			jQuery(this).Number2(params);
		});
		return this;
	}

	var el = jQuery(this),
		defaults = {
			wrapper: {
				tag: 'span',
				class: 'field-number'
			},
			spinner: {
				tag: 'span',
				class: 'btn-spinner'
			},
			step : 1,
			min : 0,
			max : ''
		},
		settings = jQuery.extend({}, defaults, params);

	function init() {

		settings.step = ( el.prop('step') );
		settings.min = ( el.prop('min') );
		settings.max = ( el.prop('max') );

		setup();

	}

	function setup() {
		var wrapper = jQuery('<' + settings.wrapper.tag + '>').addClass(settings.wrapper.class);

		var btnUp = jQuery('<' + settings.spinner.tag + '>')
			.addClass(settings.spinner.class + ' ' + settings.spinner.class + '--up')
			.text('+')
			.on('click', increase),
			btnDown = jQuery('<' + settings.spinner.tag + '>')
				.addClass(settings.spinner.class + ' ' + settings.spinner.class + '--down')
				.text('-')
				.on('click', decrease);


		var spinnerStyle = {
			'position': 'absolute',
			'top': '50%',
			'width': '40px',
			'height': '40px',
			'line-height' : '40px',
			'font-size' : '20px',
			'font-weight' : 'bold',
			// 'border': '1px solid red',
			'transform' : 'translate(-50%,-50%)',
			'color' : '#95C11F',
			'text-align' : 'center',
			'selection' : 'none'
		};
		btnUp.css(spinnerStyle).css('left', '66.6%');
		btnDown.css(spinnerStyle).css('left', '33.3%');

		wrapper.css({
			'position': 'relative'
		});

		el.wrap(wrapper);
		btnUp.insertAfter(el);
		btnDown.insertAfter(el);

	}

	function increase(evt){
		var val =  parseInt( el.val() );

		val = val + 1;
		if( settings.max ) {
			el.val(Math.min(val, settings.max));
		}
		else {
			el.val( val );
		}
	}
	function decrease(evt){
		var val =  parseInt( el.val() );

		val = val - 1;

		if( settings.min ) {
			el.val(Math.max(val, settings.min));
		}
		else {
			el.val( val );
		}
	}

	init();
};


jQuery('input[type="number"]').Number2();

// jQuery('.femanager_new').find('input').val(null);

jQuery('i.icon[title],span.icon[title]').each( function(){
	var icon = jQuery(this);
	var svg = jQuery('<svg class="icon"><use xlink:href="#'+icon.attr('title')+'"></svg>');
	svg.insertAfter( icon );
	icon.remove();
});

jQuery(function () {
	if ( 'PageTransition' in jQuery.fn ) {
		// jQuery('body').PageTransition({selector: '.js-transition'});
	}

	// jQuery('body').on('click', '.js-transition', function ( evt ) {
	// 	evt.preventDefault();
	// 	var target = jQuery(this).attr('href');
	//
	// 	var offscreen = jQuery('<div id="offscreen">').appendTo('body');
	// 	offscreen.load(target + ' .page_wrap', function ( ) {
	// 		jQuery('body > .page_wrap').addClass('slide-out is-reversed');
	// 		jQuery(this).addClass('in');
	// 	});
	// 	return false;
	// });


	jQuery('.js-modal').each( function(){

		jQuery(this).dialog(dialogDefaults)
	});

	jQuery('body').on('click','[data-dismiss="alert"]',function(evt){
		jQuery(this).closest('.alert').slideUp(150,function(){
			var alert = jQuery(this);
			alert.next('input').focus();
			alert.remove();
		});
	});
	jQuery("body").bind("DOMSubtreeModified", function() {
		jQuery('iframe.keep-ratio').each(function(){
			this.style.setProperty('--width', jQuery(this).width() + 'px' );
		});
	});

});

jQuery(window).on("load", function ( e ) {

});

jQuery(window).on("resize", function ( e ) {

});