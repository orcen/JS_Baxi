require(["jquery", "TYPO3/CMS/Backend/Modal"], function ($) {

    function select2() {
        let field = jQuery(this),
            searchField = jQuery('<input type="text" class="select2-search__field" size="40" />'),
            wrap = jQuery('<span class="select2 form-control">'),
            id = 'list_' + field.attr('name'),
            datalist = jQuery('<datalist id="' + id + '">'),
            reset = jQuery('<span class="reset">');

        searchField.attr('list', id);
        field.wrap(wrap);

        datalist.append(field.find('option').clone());
        datalist.find('option').each(function () {
            jQuery(this)
                .attr('data-value', jQuery(this).attr('value'))
                .attr('value', jQuery(this).text())
                .text(null);
        });

        searchField.insertAfter(field);
        reset.insertAfter(searchField);
        datalist.insertAfter(field);

        reset.on('click', function () {
            searchField.val(null);
            jQuery('option', 'select[name="tx_c3baxi_tools_c3baxibaxi[start]"], datalist[id="list_tx_c3baxi_tools_c3baxibaxi[start]"],select[name="tx_c3baxi_tools_c3baxibaxi[end]"], datalist[id="list_tx_c3baxi_tools_c3baxibaxi[end]"]').attr('disabled', false);
        });

        if (jQuery('option[selected]', field)) {
            if (jQuery('option[selected]', field).val() !== '0') {
                searchField.val(jQuery('option[selected]', field).text().toString().trim());
                jQuery(field).change();
            }
        }

        searchField.on('input change', function (evt) {
            let target = jQuery(this),
                options = jQuery(target[0].list).find('option').not(':disabled'),
                selected = false;

            for (let i = 0; i < options.length; i++) {
                if (options[i].value === target.val()) {
                    selected = options[i];
                    break;
                }
            }

            if (selected) {
                searchField.val(selected.value);

                // selected.prop('selected', true);
                field.val(selected.dataset.value).change();
            }
        });

    }

    function prepareFilterForm() {
        jQuery('.js-filterForm').each(function () {
            let form = jQuery(this),
                lineList = [],
                clientList = [],
                listing = form.closest('.baxi_tabs-tab').find('.booking-fahrt'),
                linienSelect = jQuery('select[name="linie"]', form),
                clientSelect = jQuery('select[name="client"]', form),
                bookings = form.closest('.baxi_tabs-tab').find('.booking-client,.subscriptions-item');


            listing.each(function () {
                let linie = jQuery(this).data('linie');
                if (lineList.indexOf(linie) === -1) {
                    lineList.push(linie);
                }
            });

            for (let l = 0; l < lineList.length; l++) {
                linienSelect.append(jQuery('<option value="' + lineList[l] + '">' + lineList[l] + '</option>'));
            }
            if (bookings.length > 0)
                bookings.each(function (idx, el) {
                    clientList[el.dataset.clientid] = el.dataset.client;
                });

            // if( subscriptions.length > 0 )
            //     subscriptions.each(function (idx, el) {
            //         clientList[el.dataset.clientid] = el.dataset.client;
            //     });

            clientList.forEach(function (val, idx) {
                clientSelect.append(jQuery('<option value="' + idx + '">' + val + ' (' + idx + ')' + '</option>'));
            });
        });

    }

    $('[data-toggle="collapse"][data-target]').on('click', (evt) => {

        let target = $(evt.currentTarget).data('target');
        $(evt.currentTarget).toggleClass('is-toggled');
        if (target[0] === '#') {
            $(target).toggle();
        } else {
            $(target, evt.currentTarget).toggle();
        }
    });

    $('[data-action="selectStation"]').on('change', function () {

        let el = $(this),
            target = $(el.data('target')),
            stationId = parseInt(el.val()),
            selectedZone = el.find('[value="' + el.val() + '"]').data('zone'),
            selectedLinien = el.find('[value="' + el.val() + '"]').data('linie'),
            selectName = (el.data('target') === '#start_station') ? 'end' : 'start',
            options = jQuery('option', 'select[name="tx_c3baxi_tools_c3baxibaxi[' + selectName + ']"], datalist[id="list_tx_c3baxi_tools_c3baxibaxi[' + selectName + ']"]')
                .attr('disabled', false),
            isCityLine = true,
            cityLines = [8319, 8320];

        if (el.closest('#findCityRoute').length > 0) {
            isCityLine = true;
        }

        options.filter(function (idx, el) {

            let that = jQuery(el),
                uid = (typeof that.data('value') ==='undefined' ? that.val() : that.data('value')), //( that.parentElement.tagName === 'datalist' ? that.data('value') : that.val()),
                zone = that.data('zone'),
                linien = that.data('linie') || [];

            if (uid === 0) return false;
           /** same Station */
           if( stationId === uid ) {
               return true;
           }
            if (typeof linien !== 'undefined') {


                /** same zone and not a cityline */
                // if (selectedZone === zone && cityLines.filter((val) => linien.includes(val)).length === 0) {
                //     return true;
                // }
                /** durch das aukomentieren ist die gleiche Zone fuer einstieg und ausstieg erlaubt **/

                /** diferent Line */
                return selectedLinien.filter(function (val) {
                    return linien.includes(val)
                }).length === 0;
            }
            return false;
        })
            .attr('disabled', true);


    });



    // jQuery('#findCityRoute').on('submit', function (evt) {
    //     evt.preventDefault();
    //     jQuery('.error', '.output').remove();
    //     jQuery('.list', '.output').html(null);
    //
    //     let form = this;
    //     $.ajax({
    //         url: '/?tx_c3baxi_baxisuche[action]=ride&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
    //         method: 'post',
    //         data: {
    //             tx_c3baxi_baxisuche: {
    //                 doAction: 'findCityRoute',
    //                 startStation: form['tx_c3baxi_tools_c3baxibaxi[start]'].value,
    //                 endStation: form['tx_c3baxi_tools_c3baxibaxi[end]'].value,
    //                 date: form['tx_c3baxi_tools_c3baxibaxi[date]'].value,
    //                 time: form['tx_c3baxi_tools_c3baxibaxi[time]'].value
    //             }
    //         }
    //     }).then(createRideList).fail(function (response) {
    //         let error = jQuery('<div class="error">').text('Es ist ein Fehler aufgetreten!');
    //         jQuery('.output').append(error);
    //     });
    // });


    jQuery('.reactive').on('change', '.newUserToggle', function () {
        let form = jQuery(this).closest('form');
        if (jQuery(this).is(':checked')) {
            form.find('[name="tx_c3baxi_tools_c3baxibaxi[user]"]').attr('required', false);
            form.find('[name="tx_c3baxi_tools_c3baxibaxi[newUser][name]"]').attr('required', true);
        } else {
            form.find('[name="tx_c3baxi_tools_c3baxibaxi[user]"]').attr('required', true);
            form.find('[name="tx_c3baxi_tools_c3baxibaxi[newUser][name]"]').attr('required', false);
        }
        form.find('.newUser').toggle();
    });

    $('[pattern="([0-9][0-9]:[0-9][0-9])?"]').on('keyup', function () {
        jQuery(this).val(jQuery(this).val().toString().replace(/[,\. ]/i, ':'));
    });

    // find geoLocation by Haltestelle
    jQuery('[data-action="geolocate"]').on('click', function (evt) {
        evt.preventDefault();
        let gMap = jQuery('#gmap'),
            adresse = jQuery(this).data('target');

        gMap.parent().show();
        gMap.css({width: '600px', height: '400px'});

        if (parseFloat(jQuery('[name="tx_c3baxi_tools_c3baxibaxi[longitude]"]').val()) > 0 && parseFloat(jQuery('[name="tx_c3baxi_tools_c3baxibaxi[latitude]"]').val()) > 0) {

            let longitude = parseFloat(jQuery('[name="tx_c3baxi_tools_c3baxibaxi[longitude]"]').val()),
                latitude = parseFloat(jQuery('[name="tx_c3baxi_tools_c3baxibaxi[latitude]"]').val()),
                mapSettings = {
                    center: {lat: latitude, lng: longitude},
                    zoom: 12,
                    disableDefaultUI: false,
                    // gestureHandling: 'greedy'
                },
                map = new google.maps.Map(gMap[0], mapSettings),
                marker = new google.maps.Marker({
                    position: {lat: latitude, lng: longitude},
                    map: map,
                    draggable: true
                });
            google.maps.event.addListener(marker, 'dragend', function (evt) {
                jQuery('[data-geo="latitude"]').val(evt.latLng.lat());
                jQuery('[data-geo="longitude"]').val(evt.latLng.lng());
            });
        } else {
            jQuery.ajax('https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(adresse) + '&key=AIzaSyC0xlK753ERQJ9vXnmhKD8k3cGZ885-gJ8', {region: 'DE'}).then(function (responseRaw) {

                let response = null,
                    mapSettings = {
                        center: {lat: 49.8790723, lng: 12.3312861},
                        zoom: 12,
                        disableDefaultUI: false,
                        // gestureHandling: 'greedy'
                    },
                    map = new google.maps.Map(gMap[0], mapSettings);

                if (typeof responseRaw != 'object') {
                    response = JSON.parse(responseRaw);
                } else {
                    response = responseRaw;
                }
                if (response.status === 'OK') {

                    let marker = new google.maps.Marker({
                        position: response.results[0].geometry.location,
                        map: map,
                        draggable: true
                    });

                    map.setCenter(response.results[0].geometry.location);

                    jQuery('[data-geo="latitude"]').val(response.results[0].geometry.location.lat);
                    jQuery('[data-geo="longitude"]').val(response.results[0].geometry.location.lng);

                    google.maps.event.addListener(marker, 'dragend', function (evt) {
                        jQuery('[data-geo="latitude"]').val(evt.latLng.lat());
                        jQuery('[data-geo="longitude"]').val(evt.latLng.lng());
                    });


                }
            });
        }

    });

    // Table Sorting
    jQuery('[data-action="sort"]', '.baxi_table').on('click', function () {
        let table = jQuery(this).closest('.baxi_table'),
            tbody = table.children('tbody'),
            sortby = jQuery(this).data('sortby'),
            direction = 1;

        // if( jQuery(this).data('direction' ) != 'asc' ) {
        // 	jQuery(this).data('direction', direction);
        //
        // }
        // else{
        // 	direction = -1;
        // 	jQuery(this).data('direction', direction);
        //
        // }

        let rows = tbody.children().detach();
        // tbody.html( null );

        rows.sort(function (a, b) {
            let aVal = jQuery(a).data(sortby),
                bVal = jQuery(b).data(sortby);

            if (['number', 'latitude', 'longitude'].indexOf(sortby) != -1) {
                aVal = parseInt(aVal);
                bVal = parseInt(bVal);

                if ((aVal) == (bVal)) return 0;
                return (((aVal) < (bVal) ? -1 : 1));

            } else {
                if (typeof aVal !== 'undefined')
                    return aVal.localeCompare(bVal);
                else
                    return 0;
            }
        });

        rows.appendTo(tbody);
    });

    jQuery('[data-action="search"]').on('input', function(evt){


        let val = jQuery(this).val().toString().toLowerCase(),
            rows = jQuery('tbody tr','.baxi_table');

        if( val.length < 3 ) {
            rows.show();
            return false;
        }

        rows.hide().filter( (idx,el) => jQuery(el).text().toString().toLowerCase().includes( val ) ).show();

    });

    jQuery('.js-map').on('click', function (evt) {
        evt.preventDefault();

        let target = evt.currentTarget.dataset.target,
            gMap = jQuery('#map'),
            search = jQuery('#searchField'),
            btn = jQuery('#searchBtn');

        gMap.parent().show();

        gMap.css({width: '600px', height: '400px'});

        let mapSettings = {
                center: {lat: 49.8790723, lng: 12.3312861},
                zoom: 12,
                disableDefaultUI: false,
                // gestureHandling: 'greedy'
            },
            map = new google.maps.Map(gMap[0], mapSettings);

        btn.on('click', function () {
            let adresse = search.val();

            jQuery.ajax('https://maps.googleapis.com/maps/api/geocode/json?address=' + encodeURIComponent(adresse) + '&key=AIzaSyC0xlK753ERQJ9vXnmhKD8k3cGZ885-gJ8', {region: 'DE'}).then(function (responseRaw) {
                let response;

                if (typeof responseRaw != 'object') {
                    response = JSON.parse(responseRaw);
                } else {
                    response = responseRaw;
                }
                let markers = [];

                if (response.status === 'OK') {
                    let marker = new google.maps.Marker({
                        position: response.results[0].geometry.location,
                        map: map,
                        // draggable: true
                    });

                    map.setCenter(response.results[0].geometry.location);
                    map.setZoom(16);

                    let allowedStations = [];
                    jQuery('[name="tx_c3baxi_tools_c3baxibaxi[' + target + ']"] option[data-linie]').each(function () {
                        allowedStations.push(parseInt(jQuery(this).attr('value')));
                    });

                    if (markers.length > 0) {
                        for (let i = 0; i < markers.length; i++) {
                            markers[i].setMap(null);
                        }
                        markers = [];
                    }

                    $.ajax({
                        url: '/?tx_c3baxi_baxisuche[action]=nearestStation&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
                        method: 'post',
                        data: {
                            tx_c3baxi_baxisuche: {
                                location: response.results[0].geometry.location
                            }
                        }
                    }).then(function (responseRaw) {
                            let response;

                            if (typeof responseRaw != 'object') {
                                response = JSON.parse(responseRaw);
                            } else {
                                response = responseRaw;
                            }

                            if (response.status === 'OK') {

                                for (let i = 0; i < response.data.length; i++) {
                                    let pos = response.data[i];

                                    if (allowedStations.indexOf(pos.uid) !== -1) {
                                        let mrkr = new google.maps.Marker({
                                            position: {lat: pos.lat, lng: pos.lng},
                                            map: map,
                                            title: pos.name
                                            // draggable: true
                                        });
                                        mrkr.location = pos.uid;
                                        mrkr.addListener('click', function () {
                                            console.log(pos);
                                            jQuery('input[list="list_tx_c3baxi_tools_c3baxibaxi[' + target + ']"]').val(pos.uid).trigger('change');
                                        });

                                        markers.push(mrkr);
                                    }
                                }
                            }
                        }
                    )

                }
            });
        });

    });

    jQuery('.map-wrap .close').on('click', function () {
        jQuery(this).closest('.map-wrap').hide();
    });

    jQuery('select.select2').each(select2);


    prepareFilterForm();

    jQuery('.js-filterForm').on('change', function (evt) {
        let listing = jQuery(this).closest('.baxi_tabs-tab').find('.booking');

        switch (evt.originalEvent.target.name) {
            case 'confirmed':
                if (evt.originalEvent.target.value === 'all') {
                    listing.find('.booking-fahrt').show();
                } else {
                    listing.find('.booking-fahrt').hide();
                    if (evt.originalEvent.target.value < 2) {
                        listing.find('.booking-fahrt').filter('[data-confirmed="' + evt.originalEvent.target.value + '"]').show();
                    } else {
                        listing.find('.booking-fahrt').filter('[data-confirmed="0"][data-remindersend="1"]').show();
                    }
                }

                break;
            case 'approved':
                if (evt.originalEvent.target.value === 'all') {
                    listing.find('.booking-fahrt').show();
                } else {
                    listing.find('.booking-fahrt').hide();
                    listing.find('.booking-fahrt').filter('[data-approved="' + evt.originalEvent.target.value + '"]').show();
                }

                break;

            case 'linie':
                if (evt.originalEvent.target.value === 'all') {
                    listing.find('.booking-fahrt').show();
                } else {
                    listing.find('.booking-fahrt').hide();
                    listing.find('.booking-fahrt').filter('[data-linie="' + evt.originalEvent.target.value + '"]').show();
                }
                break;

            case 'client':
                if (evt.originalEvent.target.value === 'all') {
                    listing.find('.booking-fahrt').show();
                } else {
                    listing.find('.booking-fahrt').hide();
                    listing.find('.booking-client[data-clientid="' + evt.originalEvent.target.value + '"]').closest('.booking-fahrt').show();
                }
                break;
        }
    });


    jQuery('button[data-action="addException"]').on('click', function (evt) {
        let tmpl = jQuery(jQuery('#exceptionTemplate')[0].innerHTML),
            exceptionList = jQuery('.exception-list');

        let exception = tmpl.clone();
        exception.appendTo(exceptionList);
    });
    jQuery('.exception-list').on('click', '[data-action="removeException"]', function () {
        jQuery(this).closest('.row').remove();
    });

    jQuery('button[data-action="switchStations"]').on('click', function (evt) {
        let startField = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[start]"]'),
            endField = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[end]"]'),
            tmpStart = startField.val(),
            tmpEnd = endField.val();

        startField.find('[disabled]').attr('disabled', false);
        endField.find('[disabled]').attr('disabled', false);

        startField.val(endField.val()).trigger('change');
        jQuery('[list="list_tx_c3baxi_tools_c3baxibaxi[start]"]').val(startField.find(':selected').text().toString().trim());
        endField.val(tmpStart).trigger('change');
        jQuery('[list="list_tx_c3baxi_tools_c3baxibaxi[end]"]').val(endField.find(':selected').text().toString().trim());
    });



    let bookingForm = jQuery('#findRoute, #findSubscriptionRoute');
    if (bookingForm.length === 1) {

        function createRideList(response) {
            let output = jQuery('.list', '.output');

            if (!response.status) {

                if (response.status_text === 'no_rides_found') {
                    let error = jQuery('<div class="error">').text('Es wurden keine Fahrten gefunden!');
                    output.append(error);
                    return false;
                } else if (response.status_text === 'error') {

                    let error = jQuery('<div class="error">').text('Es ist ein Fehler aufgetreten!');
                    output.append(error);
                    return false;
                }
            }

            let data = response.data,
                list = response.data.routes,
                now = new Date(),
                warning = jQuery('<span class="box-warning">');

            function generateList(rides, data, template, target) {

                for (let r = 0; r < rides.length; r++) {
                    let ride = template,
                        rDate = (data.date !== false ? new Date(Date.parse(data.date)) : new Date()),
                        departure = new Date(Date.parse(rides[r].abfahrt)),
                        arrival = new Date(Date.parse(rides[r].ankunft)),
                        buchbarBis = new Date(Date.parse(rides[r].buchbarBis)),
                        departureStr = String(departure.getHours()).padStart(2, '0') + ':' + String(departure.getMinutes()).padStart(2, '0'),
                        arrivalStr = String(arrival.getHours()).padStart(2, '0') + ':' + String(arrival.getMinutes()).padStart(2, '0'),
                        bookingTimeStr = String(rDate.getHours()).padStart(2, '0') + ':' + String(rDate.getMinutes()).padStart(2, '0');

                    ride = ride.replace(/##line##/g, rides[r].linie)
                        .replace(/##fahrtName##/g, rides[r].fahrtName)
                        .replace(/##ride##/g, rides[r].uid)
                        .replace(/##duration##/g, rides[r].dauer)
                        .replace(/##departure##/g, departureStr)
                        .replace(/##arrival##/g, arrivalStr)
                        .replace(/%23%23start%23%23/g, data.start)
                        .replace(/%23%23end%23%23/g, data.end)
                        .replace(/%23%23fahrt%23%23/g, rides[r].uid)
                        .replace(/##time##/g, bookingTimeStr);

                    rDate.setHours(departure.getHours());
                    rDate.setMinutes(departure.getMinutes());

                    ride = ride.replace(/##date##/g, rDate.toLocaleDateString('de-DE'))
                        .replace(/%%date%%/g, (rDate.getTime() / 1000))
                        .replace(/%23%23date%23%23/g, (rDate.getTime() / 1000));


                    ride = jQuery(ride);

                    /**
                     * @var DateTime buchbarBis
                     */
                    buchbarBis.setMinutes(buchbarBis.getMinutes() + 15);

                    if (now > buchbarBis && data.date !== false) {
                        buchbarBis.setMinutes(buchbarBis.getMinutes() - 15);
                        warning.attr('title', 'Buchbar bis: ' + String(buchbarBis.getHours()).padStart(2, '0') + ':' + String(buchbarBis.getMinutes()).padStart(2, '0'));
                        ride.prepend(warning.clone());
                    }

                    ride.find('input[name="tx_c3baxi_tools_c3baxibaxi[fahrt]"]').val(rides[r].uid);
                    ride.find('input[name="tx_c3baxi_tools_c3baxibaxi[fahrt][]"]').val(rides[r].uid);
                    ride.find('input[name="tx_c3baxi_tools_c3baxibaxi[date]"]').val((rDate.getTime() / 1000));
                    ride.find('input[name="tx_c3baxi_tools_c3baxibaxi[startStation]"]').val(data.start);
                    ride.find('input[name="tx_c3baxi_tools_c3baxibaxi[endStation]"]').val(data.end);

                    target.append(ride);
                }
            }

            output.html(null);

            if (typeof list == 'object' && typeof list.regular != 'undefined') {
                let regList = jQuery('<div id="hinfahrt"><div class="list"></div></div>'),
                    backList = jQuery('<div id="rueckfahrt"><div class="list"></div></div>'),
                    template = jQuery('#multiRide')[0].innerHTML;

                generateList(list['regular'], data, template, regList);
                generateList(list['return'], data, template, backList);

                regList.prepend( jQuery('<h2>Hinfahrt</h2>'));
                backList.prepend( jQuery('<h2>Rueckfahrt</h2>'));

                regList.find('input[type="radio"][name="tx_c3baxi_tools_c3baxibaxi[fahrt][]"]')
                    .attr('name','tx_c3baxi_tools_c3baxibaxi[fahrt][to]');
                backList.find('input[type="radio"][name="tx_c3baxi_tools_c3baxibaxi[fahrt][]"]')
                    .attr('name','tx_c3baxi_tools_c3baxibaxi[fahrt][from]');

                let form = jQuery(jQuery('#multiForm')[0].innerHTML);
                form.find('#rideList').append(regList);
                form.find('#rideList').append(backList);

                // form.find('input[name="tx_c3baxi_tools_c3baxibaxi[date]"]').val((rideDate.getTime() / 1000));
                form.find('input[name="tx_c3baxi_tools_c3baxibaxi[startStation]"]').val(data.start);
                form.find('input[name="tx_c3baxi_tools_c3baxibaxi[endStation]"]').val(data.end);

                // output.addClass('2col');

                output.prepend(form);

            } else {
                let template = jQuery('#ride')[0].innerHTML;
                generateList(list, data, template, output);
            }
            jQuery('select.select2', '.output').each(select2);
        }



        jQuery('#findRoute').on('submit', function (evt) {
            evt.preventDefault();
            jQuery('.error', '.output').remove();
            jQuery('.list', '.output').html(null);

            let form = this,
                postData = {
                    tx_c3baxi_baxisuche: {
                        doAction: 'find',
                        startStation: form['tx_c3baxi_tools_c3baxibaxi[start]'].value,
                        endStation: form['tx_c3baxi_tools_c3baxibaxi[end]'].value,
                        date: form['tx_c3baxi_tools_c3baxibaxi[date]'].value,
                        time: form['tx_c3baxi_tools_c3baxibaxi[time]'].value,
                        returnRide: jQuery(form).find('#returnRide').prop('checked')
                    }
                };

            jQuery.ajax({
                url: '/?tx_c3baxi_baxisuche[action]=ride&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
                method: 'post',
                data: postData
            })
                .then(createRideList)
                .fail(function () {
                    let error = jQuery('<div class="error">').text('Es ist ein Fehler aufgetreten!');
                    jQuery('.output').append(error);
                });
        });

        jQuery('button[data-action="findRoutes"]', '#findSubscriptionRoute').on('click', function (evt) {
            evt.preventDefault();
            jQuery('.error', '.output').remove();
            let form = jQuery(this).closest('form'),
                days = [],
                start = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[start]"]', form).val(),
                end = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[end]"]', form).val();

            if( typeof start === 'undefined' && jQuery('[name="tx_c3baxi_tools_c3baxibaxi[start][__identity]"]').length === 1 ) {
                start = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[start][__identity]"]').val();
                end = jQuery('[name="tx_c3baxi_tools_c3baxibaxi[end][__identity]"]').val();
            }

            jQuery('[name="tx_c3baxi_tools_c3baxibaxi[tage][]"]', form).each(function () {
                if (jQuery(this).prop('checked')) {
                    days.push(jQuery(this).val());
                }
            });
            $.ajax({
                url: '/?tx_c3baxi_baxisuche[action]=ride&tx_c3baxi_baxisuche[controller]=Ajax&type=666',
                method: 'post',
                data: {
                    tx_c3baxi_baxisuche: {
                        doAction: 'findByDays',
                        startStation: start,
                        endStation: end,
                        tage: days
                    }
                }
            }).then(createRideList).fail(function (response) {
                let error = jQuery('<div class="error">').text('Es ist ein Fehler aufgetreten!');
                jQuery('.output').append(error);
            });
        });

    }
});



