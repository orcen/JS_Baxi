require([ "jquery" ], function ( $ ) {



    jQuery.fn.C3Graph = function ( params ) {
        if ( this.length == 0 ) return this;
        /** support mutltiple elements */
            // if ( this.length > 1 ) {
            // 	this.each(function () {
            // 		$(this).GeoPicker(params);
            // 	});
            // 	return this;
            // }

        let el = jQuery(this),
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
        const init = function () {
            let max = Math.max.apply(Math, Object.keys(settings.data).map(( key ) => settings.data[ key ].value));
            setup();
        };

        const setup = function () {
            el.css(settings.wrapper.style);
            let mostCount = Math.max.apply(Math, Object.values(settings.data).map(( i ) => i.count));

            Object.keys(settings.data).map(( key ) => {
                let totalBlock = document.createElement('DIV'),
                    positiveBlock = document.createElement('DIV'),
                    heightTotal = 100 * settings.data[ key ].count,
                    heightPositive = settings.data[ key ].value / (heightTotal / 100);

                totalBlock.classList.add(settings.classes.bar);
                positiveBlock.classList.add(settings.classes.positiveBar);

                totalBlock.style.width = '45px';
                totalBlock.style.height = _getHeight(mostCount, settings.data[ key ].count) + 'px';
                // totalBlock.style.background = 'red';

                // totalBlock.dataset.value = (Math.ceil(heightPositive * 10) / 10) + '%';
                totalBlock.dataset.count = settings.data[ key ].count;

                let date = '', line = '', fahrtId = '';
                [ date, line, fahrtId ] = key.split('_');
                totalBlock.dataset.date = date;
                totalBlock.dataset.name = `${line} - ${fahrtId}`;
				positiveBlock.dataset.value = (Math.ceil(heightPositive * 10) / 10) + '%';
                positiveBlock.style.height = heightPositive + '%';

                totalBlock.appendChild(positiveBlock);
                el.append(totalBlock);
            });
        };

        init();

        function _getHeight( mostParts, act ) {
            let maxValue = el.innerHeight() * .8;
            return maxValue / (100 / (act / (mostParts / 100)));
        }
    };
    if( typeof graphData !== 'undefined'){
        jQuery('#rating').C3Graph({
            data: graphData
        });
    }
})
;