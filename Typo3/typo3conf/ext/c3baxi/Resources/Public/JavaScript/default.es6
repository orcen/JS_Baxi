'use strict';
/**
 ****     Definitions and Customization */

/**
 *
 * @param obj
 * @param predicate
 * @returns {*}
 */
Object.filter = ( obj, predicate ) =>
	Object.keys(obj)
		  .filter(key => predicate(obj[ key ], key))
		  .reduce(( res, key ) => (res[ key ] = obj[ key ], res), {});

let dialogDefaults = Object.freeze({
	classes: {
		"ui-dialog": "boxed",
		"ui-dialog-titlebar": "hidden",
		"ui-dialog-content": "box"
	},
	resizable: false,
	title: null,
	minHeight: 400,
	height: 400,
	width: () => Math.min(400, parseInt(window.innerWidth) - 40),
	show: {effect: "fade", duration: 300},
	hide: {effect: "fade", duration: 300},
	open: function ( evt, ui ) {
		let parent = jQuery(evt.target).closest(".ui-dialog"),
			removeOverlay = false,
			overlay = null;
		parent.css("z-index", 500);
		parent.find(".ui-dialog-titlebar").hide();
		if ( parent.siblings(".ui-widget-overlay").length === 0 ) {
			overlay = jQuery(`<div class='ui-widget-overlay'>`).appendTo(jQuery("body"));
			removeOverlay = true;
		}
		else {
			overlay = parent.siblings(".ui-widget-overlay");
		}
		parent.siblings(".ui-widget-overlay").css("z-index", 490).addClass("help-overlay");
		let closeHelp = () => {
			if ( removeOverlay ) {
				overlay.remove();
			}
			else {
				parent.siblings(".ui-widget-overlay").css("z-index", 90).removeClass("help-overlay");
			}
			jQuery(parent.find("[id*='Dialog']")).dialog("destroy").remove();

		};
		jQuery("[data-action='closeDialog']", parent).on("click", closeHelp);
		jQuery(overlay).on("click", closeHelp);
	}
});

/** Favorites */
function BaxiFavorites( params ) {
	let BF = this,
		defaults = {
			maxItems: 5,
			storeKey: "favorites"
		};
	BF.items = [];
	BF.template = document.createElement("LI");
	if ( typeof Object.assign == 'function' ) {
		BF.settings = Object.assign({}, defaults, params);
	}
	else {
		BF.settings = jQuery.extend({}, defaults, params);
	}

	BF.add = function ( uid = 0, stationName = null ) {
		let result = false;

		jQuery.ajax({
			url: baxiSearchSettings.ajaxUrl.favorites, //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
			method: "POST",
			data: {
				tx_c3baxi_baxisuche: {doAction: "add", uid: uid, name: stationName}
			}
		}).then(function ( response ) {
			if ( response.status === "added" ) {
				BF.items.push({
					station: uid,
					name: stationName
				});
				Store.set(BF.settings.storeKey, BF.items);
				result = true;
			}
			else if( response.status === "not_logged_in" ) {
				let infoModal = jQuery("<div id='infoDialog'></div>").appendTo("body");

				infoModal.append(`
				<div class="">
				<p>Fúr das speichern der Favoriten müssen Sie sich erst anmelden.</p>
</div>`);
				infoModal.dialog(dialogDefaults);
			}
		});

		return result;
	};

	BF.remove = function ( uid = 0 ) {
		let result = false;
		jQuery.ajax({
			url: baxiSearchSettings.ajaxUrl.favorites, //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
			method: "POST",
			data: {
				tx_c3baxi_baxisuche: {doAction: "remove", uid: uid}
			}
		}).then(function ( response ) {
			BF.items = BF.items.filter(( item ) => item.station !== uid).values();
			Store.set(BF.settings.storeKey, BF.items);
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
			let storedFavorites = Store.get(BF.settings.storeKey);

			if ( typeof storedFavorites === "string" ) {
				try {
					BF.items = JSON.parse(storedFavorites);
				} catch ( e ) {
					BF.items = [];
				}
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
			let child = template.cloneNode(true);
			child.innerHTML = child.innerHTML + BF.items[ i ].name;
			child.setAttribute("data-key", BF.items[ i ].station);
			child.setAttribute("data-name", BF.items[ i ].name);
			list.append(child);
		}
		parent.append(list);
	};

	BF.init();
}

var Favorites = new BaxiFavorites();

/** Prompt jQuery.UI */
jQuery.widget("custom.prompt", jQuery.ui.dialog, {

	options: {
		modal: true,
		classes: {
			"ui-dialog": "boxed prompt",
			"ui-dialog-titlebar": "hidden",
			"ui-dialog-content": "box prompt-content",
			"ui-dialog-buttonpane": "prompt-buttonpanel"
		},
		question: "Test Frage",
		defaultText: "",
		resizable: false,
		buttons: {
			accept: {
				text: "OK",
				class: "prompt-btn prompt-btn--accept",
				id: "promptAccept",
				click: () => true
			},
			cancel: {
				text: "Abbrechen",
				class: "prompt-btn prompt-btn--cancel",
				id: "promptCancel",
				click: () => true

			}
		},
		confirm: false,
		submit: function ( val ) {
		}
	},
	submit: function ( val ) {

		if ( typeof this.options.submit === "function" ) {
			this.options.submit(val);
		}
		jQuery(this.parentNode).dialog().dialog("destroy").remove();
	},
	_create: function () {
		if ( this.options.confirm ) {
			this.options.classes[ 'ui-dialog' ] = this.options.classes[ 'ui-dialog' ].concat(' prompt--confirm');
			this.options.classes[ 'ui-dialog-buttonpane' ] = this.options.classes[ 'ui-dialog-buttonpane' ].concat(' is-centered');
			this.options.buttons.accept.class = this.options.buttons.accept.class.concat(' btn btn--cancel');
		}
		this._super();
	},
	open: function create() {

		let html = document.createDocumentFragment();

		if ( this.options.question !== "" ) {
			let question = document.createElement("H2");
			question.innerText = this.options.question;
			html.appendChild(question);
		}

		if ( this.options.confirm === false ) {
			/** Prompt Form */
			let form = document.createElement("FORM"),
				field = document.createElement("INPUT");


			field.setAttribute("type", "text");
			field.classList.add("prompt-field");
			field.setAttribute("value", this.options.defaultText);
			form.appendChild(field);

			html.appendChild(form);
		}
		else {
			let info = document.createElement('P');
			info.innerText = this.options.defaultText;
			html.appendChild(info);
		}

		this.element.html(html);

		this.uiDialogTitlebar.hide();
		this.uiDialogButtonPane.appendTo(this.element);
		let promptThat = this;
		this.uiDialogButtonPane.find("#promptAccept").on("click", ( evt ) => {
			let d = jQuery(evt.target).closest(".ui-dialog"),
				f = d.find(".prompt-field");
			promptThat.options.submit(f.val());

			d.dialog().dialog("destroy").remove();
		});

		this.uiDialogButtonPane.find("#promptCancel").on("click", ( evt ) => {
			jQuery(evt.target).closest(".ui-dialog").dialog().dialog("destroy").remove();
		});

		this._super();

	}
});


/**
 ****     Actual Scripts */


let form = jQuery("#routeFinder");
var ignoreZone = false;

jQuery("[data-autocomplete='haltestelle']").on("click", ( evt ) => {
	let modal, isStart, callFnc, positionPickerParams;
	modal = jQuery("<div id='mapDialog'></div>").appendTo("body");
	isStart = evt.target.getAttribute("name").includes("[start]");
	callFnc = "haltestelle";
	positionPickerParams = {
		source: baxiSearchSettings.ajaxUrl[ callFnc ],
		currentPosition: baxiSearchSettings.mapCenter,
		closeOnSelect: false,
		customSearchButtons: function ( picker, el, callback ) {
			let favoriteButtons = jQuery(`<ul class="favorites">`),
				tmpl = document.createElement("LI");
			tmpl.setAttribute("class", "favorites-item");
			tmpl.innerHTML = `<span class='btn btn--marble has-icon'><svg class='icon'><use xlink:href='#Favoriten_kontur'/></svg></span>`;
			Favorites.appendTo(favoriteButtons, tmpl);
			favoriteButtons.children().each(( idx, item ) => {
				let fav = jQuery(item);
				fav.on("click", function () {
					jQuery.ajax(baxiSearchSettings.ajaxUrl.station, {
						method: "post",
						data: {
							tx_c3baxi_baxisuche: {uid: fav.data("key")}
						}
					}).then(response => {
						response.key = response.uid;
						let evt = {
							data: response
						};
						callback(evt);
					});
				});
			});
			return favoriteButtons;
		},
		customButtons: function ( picker ) {
			let buttons = document.createDocumentFragment(),
				favBtn = jQuery(`<span class="controls-btn">`).appendTo( buttons );

			favBtn.html(`<span class="btn btn--glass btn--round has-icon"><svg class="icon" width="30" height="30"><use xlink:href="#Favoriten_kontur"></use></svg></span>Zu Favoriten`);

			if ( baxiSearchSettings.loggedIn === false ) {
				favBtn.toggleClass("is-disabled", true);
			}
			else {
				if ( Favorites.contains(picker.selected.key) ) {
					favBtn.html(`<span class="btn btn--marble has-icon">
									<svg class="icon" width="24" height="24"><use xlink:href="#delete"></use></svg>
									</span>von Favoriten entfernen`);
					favBtn.on("click", () => {
						Favorites.remove(picker.selected.key);
					});
				}
				else {
					favBtn.on("click", () => {
						jQuery("<div />").prompt({
							question: "Geben Sie einen eigenen Namen ein",
							defaultText: picker.selected.title,
							submit: ( name ) => {
								Favorites.add(picker.selected.key, name);
							}
						});
					});
				}
			}
			return buttons;
		},
		onSelect: function ( data, picker ) {
			jQuery.ajax({
					  url: baxiSearchSettings.ajaxUrl.station,
					  method: "POST",
					  data: {
						  tx_c3baxi_baxisuche: {uid: data.key}
					  }
				  })
				  .then(( response ) => {
					  let subtitle = picker.content.find(".subtitle");
					  subtitle.append(jQuery("<span>").text(response.linie.join(", ")));
					  picker.overlay.hide();
					  ignoreZone = response.zoneId;
				  });
		},
		onSubmit: function ( data ) {
			let field = jQuery(evt.target);
			if ( field.prop("name") === "tx_c3baxi_baxisuche[routeShow][start]" ) {
				Store.set("start", data.key);
			}
			else {
				Store.set("stop", data.key);
			}
			field.val(data.title);
			field.nextAll("input[type='hidden'][name^='tx_c3baxi_baxisuche[route]']").val(data.key);
		}
	};

	if ( isStart && ignoreZone ) ignoreZone = false;

	if ( !("GeoPicker" in jQuery.fn) ) {

		$("<link/>", {
			rel: "stylesheet",
			type: "text/css",
			href: window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.css"
		}).appendTo("head");


		jQuery.getScript(window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.js", function () {
			jQuery(modal).GeoPicker(positionPickerParams);
		});
	}
	else {
		jQuery(modal).GeoPicker(positionPickerParams);
	}

	var dialog = modal.dialog({
		width: window.innerWidth,
		height: window.innerHeight,
		resizable: false,
		draggable: false,
		modal: false,
		classes: {
			"ui-dialog": "is-fullscreen slide-from-right"
		},
		open: function () {
			let titlebar = jQuery(this).parent().find(".ui-dialog-titlebar"),
				title = titlebar.find(".ui-dialog-title");

			if ( title.length ) {
				if ( isStart ) {
					title.html("Start wählen");
				}
				else {
					title.html("Ende wählen");
				}

				let helpBtn = jQuery(`<span class="ui-dialog-titlebar-help ui-dialog-titlebar-btn"><span>Hilfe</span> 
					<svg class="icon" width="30" height="30"><use xlink:href="#Hilfe_Klein"></use></svg>
					</span>`)
					.insertAfter(title)
					.on("click", function ( evt ) {
						openHelp(3);
					});

				// remove standard button
				jQuery(this).parent().find(".ui-dialog-titlebar-close").remove();
				let closeBtn = jQuery("<span class='ui-dialog-titlebar-customclose ui-dialog-titlebar-btn'>")
					.html(
						`<svg class="icon" width="30" height="30"><use xlink:href="#Pfeil_L"></use></svg><span>Zurück</span>`
					)
					.insertBefore(title)
					.on("click", function ( evt ) {
						evt.preventDefault();
						// this.close(event);
						modal.dialog("close");
					});
			}
			titlebar.append(jQuery("<span class='bubble'>"));

		},
		close: function () {
			jQuery(this).dialog("destroy").remove();
		}
	});

	jQuery(window).on("resize", function () {
		dialog.dialog("option", "width", window.innerWidth);
		dialog.dialog("option", "height", window.innerHeight);
	});

});

jQuery("[data-autocomplete='favorites']").on("click", ( evt ) => {
	let modal = jQuery("<div id='mapDialog'></div>").appendTo("body"),
		// isStart = evt.target.getAttribute("name").includes("[start]"),
		callFnc = "haltestelle",

		positionPickerParams = {
			source: baxiSearchSettings.ajaxUrl[ callFnc ],
			currentPosition: { // default is Tirschenreuth, Germany
				lat: 49.8817161,
				lng: 12.3303441
			},
			closeOnSelect: false,
			lng: {
				btnConfirm: "zu den Favoriten"
			},
			onSelect: function ( data, picker ) {
				jQuery.ajax({
						  url: baxiSearchSettings.ajaxUrl.station, //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
						  method: "POST",
						  data: {
							  tx_c3baxi_baxisuche: {uid: data.key}
						  }
					  })
					  .then(( response ) => {
						  let subtitle = picker.content.find(".subtitle");
						  subtitle.append(jQuery("<span>").text(response.linie.join(", ")));
						  picker.overlay.hide();
						  // ignoreZone = response.zoneId;
					  });
			},
			onSubmit: function ( item ) {
				jQuery("<div />").prompt({
					question: "Geben Sie einen eigenen Namen ein",
					defaultText: item.title,
					submit: ( name ) => {
						Favorites.add(item.key, name);
						let shownFavorites = jQuery(".box.box--favorite"),
							clone = shownFavorites.last().clone();
						clone.find(".box-title").text(name);
						clone.find(".box-subtitle").text(item.title);
						clone.find("[data-action='deleteFavorite']").attr("data-uid", item.key);
						clone.css("opacity", 0);
						clone.insertAfter(shownFavorites.last());
						clone.animate({opacity: 1}, 300);

					}
				});
			}
		};

	if ( !("GeoPicker" in jQuery.fn) ) {

		$("<link/>", {
			rel: "stylesheet",
			type: "text/css",
			href: window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.css"
		}).appendTo("head");


		jQuery.getScript(window.location.origin + "/typo3conf/ext/c3baxi/Resources/Public/JavaScript/GeoPicker.js", function () {
			jQuery(modal).GeoPicker(positionPickerParams);
		});
	}
	else {
		jQuery(modal).GeoPicker(positionPickerParams);
	}

	modal.dialog({
		width: window.innerWidth,
		height: window.innerHeight,
		resizable: false,
		draggable: false,
		modal: true,
		classes: {
			"ui-dialog": "is-fullscreen"
		},
		open: function () {
			let titlebar = jQuery(this).parent().find(".ui-dialog-titlebar"),
				title = titlebar.find(".ui-dialog-title");


			if ( title.length ) {
				title.html("Favorit hinzufügen");

				let helpBtn = jQuery(`<span class='ui-dialog-titlebar-help ui-dialog-titlebar-btn'><span>Hilfe</span><svg class=icon width=30 height='30'><use xlink:href='Hilfe_Klein'></use></svg></span>`)
					.insertAfter(title)
					.on("click", function ( evt ) {
						alert("help");
					});

				// remove standard button
				jQuery(this).parent().find(".ui-dialog-titlebar-close").remove();
				let closeBtn = jQuery("<span class='ui-dialog-titlebar-customclose ui-dialog-titlebar-btn'>")
					.html(
						`<svg class="icon" width="30" height="30"><use xlink:href="#Pfeil_L"></use></svg>Zurück`
					)
					.insertBefore(title)
					.on("click", function ( evt ) {
						evt.preventDefault();
						// this.close(event);
						modal.dialog("close");
					});
			}
			titlebar.append(jQuery("<span class='bubble'>"));

		},
		close: function () {
			jQuery(this).dialog("destroy").remove();
		}
	});
});

jQuery("#kontoFavorites").on("click", "[data-action='deleteFavorite']", function ( evt ) {

	if ( confirm("Wirklich löschen") ) {
		let target = jQuery(this),
			stationId = target.data("uid");
		jQuery.ajax({
				  url: baxiSearchSettings.ajaxUrl.favorites, //"/?tx_c3baxi_baxisuche[action]=stationDetail&tx_c3baxi_baxisuche[controller]=Ajax&type=666",
				  method: "POST",
				  data: {
					  tx_c3baxi_baxisuche: {
						  doAction: "remove",
						  uid: stationId
					  }
				  }
			  })
			  .then(function ( response ) {
				  if ( response.status === "removed" ) {
					  target.closest(".box.box--favorite").animate({
								opacity: 0
							}, 300)
							.slideUp(150, function () {
								jQuery(this).remove();
							});
				  }
			  });
	}

});
let datepickerFields = jQuery("[data-picker='time']", form);
if ( form.length && datepickerFields.length ) {
	jQuery.datetimepicker.setLocale("de");
	datepickerFields.datetimepicker({
		minDate: -1,
		minTime: "04:00",
		step: 30,
		format: "d.m.Y H:i",
		dayOfWeekStart: 1,
		onSelectTime: function ( currentTime, field ) {
			let time = parseInt(currentTime.getTime() / 1000).toFixed(0);
			time = time - (new Date().getTimezoneOffset()*60);
			jQuery(field).nextAll("input").val(time);
		}
	});
}

/** Rating */

jQuery("[data-action='rate']").on("click", function ( evt ) {
	let el = jQuery(this),
		rateModal = jQuery("<div id='rateDialog'></div>").appendTo("body");

	rateModal.hide();

	let rateHTML = `
<form method="post" id="rateForm" class="">
	<input type="hidden" name="uid" value="${el.data("booking")}">
	<div>
		<p class="h4">Feedback</p>
		<h3>Fahrt bewerten</h3>
		<div class="align-center">
			<p>Warst du zufrieden mit deiner Fahrt?</p>
			<input type="radio" class="is-hidden" name="rating" value="0" id="ratingNo" />
			<input type="radio" class="is-hidden" name="rating" value="1" checked id="ratingYes" />
			<label for="ratingNo" style="display: inline-block; width: 66px;"><span class="btn btn--marble"><svg class="icon"><use xlink:href="#dislike"></use></svg></span>Nein</label>
			<label for="ratingYes" style="display: inline-block; width: 66px;"><span class="btn btn--marble"><svg class="icon"><use xlink:href="#like"></use></svg></span>Ja</label>
		</div>
		<p class="align-center">
			<label>Kommentar (optional)</label><br />
			<textarea name="comment" cols="60" rows="4" placeholder="Kommentar hinzufügen"></textarea>
		</p>
	</div>
	<div class="box-footer box-footer--with-button">
		<button type="submit" class="btn btn--next">Bewerten</button>
	</div>
</form>`;
	rateModal.append(rateHTML);
	let ratingOpen = function ( evt, ui ) {

		dialogDefaults.open(evt, ui);
		let parent = jQuery(evt.target);
		jQuery(".btn", parent).off("click");
		jQuery("#rateForm", parent).on("submit", function ( evt ) {
			evt.preventDefault();

			let rating = evt.target.elements.rating.value,
				comment = evt.target.elements.comment.value,
				id = evt.target.elements.uid.value;

			rating = rating * 100; // convert to percent values

			jQuery.ajax(baxiSearchSettings.ajaxUrl.rating, {
				method: "post",
				data: {
					"tx_c3baxi_baxisuche": {
						doAction: "addRating",
						rating: rating,
						comment: comment,
						id: id,
						type: "booking"
					}
				}
			}).then(( response ) => {
				response = JSON.parse(response);
				parent.find("#rateForm").html("").hide();

				if ( response.status === true ) {
					let successHTML = `
<div>
	<p class="h4">Feedback</p>
	<h3>Danke für deine Bewertung</h3>
	<p>Deine Bewertung wurde abgesendet. Vielen Dank, dass du uns hilfst, Baxi zu verbessern.</p>
	<div class="box-footer box-footer--with-button">
		<span class="btn btn--back">Schließen</span>
	</div>
</div>`;
					parent.find("#rateForm").append(successHTML).show("slow");
					parent.on("click", ".box-footer .btn", function () {
						jQuery("#rateDialog").dialog("destroy").remove();
						jQuery(".ui-widget-overlay").remove();
					});
				}
			});
		});
	};

	let dialogParams = {};


	if ( typeof Object.assign == 'function' ) {
		dialogParams = Object.assign({}, dialogDefaults, {
			open: ratingOpen,
			test: true
		});
	}
	else {
		dialogParams = jQuery.extend({}, dialogDefaults, {
			open: ratingOpen,
			test: true
		});
	}

	rateModal.dialog(dialogParams);
});


/** Booking */
jQuery("[data-action='cancel']").on('click', function ( evt ) {
	evt.preventDefault();

	jQuery('<div />').prompt({
		confirm: true,
		question: 'Fahrt stornieren?',
		defaultText: 'Wollen Sie die Bestellung wirklich stornieren?',
		buttons: {
			cancel: {
				text: 'Nein, Abbrechen'
			}
		},
		submit: function () {
			window.location.href = evt.target.href;
			return true;
		}
	});
});


/** Help Modals */
jQuery("a[href^='/hilfe?question']").on("click", function ( evt ) {
	evt.preventDefault();

	let link = jQuery(this),
		page = "",
		params = "",
		qid = false;
	[ page, params ] = link[ 0 ].href.split("?");

	params = params.split("&");

	for ( let i = 0; i < params.length; i++ ) {
		let par = params[ i ].split("=");
		if ( par[ 0 ] === "question" ) {
			qid = par[ 1 ];
		}
	}


	if ( qid ) {
		openHelp(qid);
	}
	return false;
});

jQuery("[data-action='help']").on("click", function ( evt ) {
	let qid = jQuery(this).data("question");
	if ( typeof qid !== "undefined" ) {
		openHelp(qid);
	}
});

function openHelp( uid = 0 ) {
	let helpModal = jQuery("<div id='helpDialog'></div>").appendTo("body");

	jQuery.ajax({
		url: baxiSearchSettings.ajaxUrl.help,
		method: "POST",
		data: {
			tx_c3baxi_baxisuche: {
				question: uid
			}
		}
	}).done(( response ) => {
		let FAQContent = `
<p class="box-title">
<svg class="icon"><use xlink:href="#Hilfe_Klein"></use></svg><span>Hilfe</span></p>
<h3>${response.question}</h3>
<div class="box-content">
${response.answer}`;

		if ( response.media ) {
			if ( response.media.images.length ) {
				for ( let i = 0; i < response.media.images.length; i++ )
					FAQContent += `<figure><img src='${response.media.images[ i ].url}' width='${response.media.images[ i ].size[ 0 ]}'height='${response.media.images[ i ].size[ 1 ]}' /></figure>`;
			}
			if ( response.media.downloads.length ) {
				for ( let y = 0; y < response.media.downloads.length; y++ )
					FAQContent += "";
			}
			if ( response.media.youtube.length ) {
				for ( let y = 0; y < response.media.youtube.length; y++ )
					FAQContent += `<iframe class='keep-ratio ratio-16/9' src='//www.youtube.com/embed/${response.media.youtube[ y ]}' width='800'></iframe>`;
			}
		}

		FAQContent += `
</div>
<div class="box-footer box-footer--with-button">
	<span class="btn btn--back" data-action="closeDialog">Schliessen</span>
</div>
<span class="bubble"></span>
<div id="helpInfo">
	<h3>Du kommst nicht weiter?</h3><p>Ruf uns einfach an. Wir helfen dir gerne.</p>
	<p><a href="tel:096317929899"><svg class="icon"><use xlink:href="#call"></use></svg> 0 96 31 / 79 29 899</a></p>
</div>`;

		// let FAQ = jQuery(FAQContent);
		helpModal.html(FAQContent);
		let dialogParams = Object.assign({}, dialogDefaults, {/*custom settings*/});
		helpModal.dialog(dialogParams);
	});
}


jQuery('[data-action="content"]').on('click', function(evt){

	let lightbox = jQuery('<div id="lightboxDialog"></div>').load(evt.target.dataset.uri + ' main.content', function(response, statusText,xhr){
		
		lightbox.append(jQuery('<div class="box-footer box-footer--with-button"><span class="btn btn--back" data-action="closeDialog">Schliessen</span></div>'));

		lightbox.find('.content').children().unwrap();

		let dialogParams = Object.assign({}, dialogDefaults, {});
		lightbox.dialog(dialogParams);

	});

});
/** StepForm */
jQuery.fn.stepForm = function ( params ) {

	if ( this.length === 0 ) return this;

	// support mutltiple elements
	if ( this.length > 1 ) {
		this.each(function () {
			$(this).stepForm(params);
		});
		return this;
	}

	let form = {
			steps: [],
			stepCount: 0,
			stepper: {},
			active: 0,
			previous: false,
			innerWrap: null,
			touch: {
				start: null,
				direction: null
			}
		},
		el = this,
		defaults = {
			controls: true,
			buttons: {
				next: ".stepForm-next",
				prev: ".stepForm-prev"
			},
			animation: false,
			swipe: false,
			swipeTreshold: 30
		},
		settings = jQuery.extend({}, defaults, params);


	function init() {
		// get all steps
		el.addClass("stepForm");
		form.steps = el.children();

		form.stepCount = form.steps.length;

		form.stepper = {};
		form.active = 0;
		form.first = true;


		let div = document.createElement('DIV');
		if ( !('onanimationend' in div) ) {
			settings.animation = false;
		}
		setup();

		if ( settings.swipe ) {
			el.on('touchstart', _touchStart);
			el.on('touchend', _touchEnd);

			el.on('swipeleft', next);
			el.on('swiperight', prev);
		}
	}

	function setup() {
		form.innerWrap = jQuery("<div class='stepForm-inner'></div>");
		form.innerWrap.css({
			overflowX: "hidden",
			position: "relative"
		});

		_setWrapperHeight();
		jQuery(window).on('resize', _setWrapperHeight);

		el.wrapInner(form.innerWrap);
		form.steps.addClass("stepForm-step");
		// hide all except first
		form.steps.hide();

		if ( settings.controls === true ) {
			form.stepper.el = jQuery("<div class='stepForm-stepper'></div>").appendTo(el);
			form.stepper.el.css({textAlign: "center", margin: "16px auto 0"});

			form.stepper.steps = [];

			for ( let i = 0; i < form.stepCount; i++ ) {
				let step = jQuery(`<span data-idx='${i}'></span>`);
				step.data("idx", i)
					.css({
						display: "inline-block",
						height: "10px",
						width: "10px",
						borderRadius: "100%",
						border: "2px solid #FFF",
						margin: "5px 5px"
					});

				if ( i === form.active ) {
					step.addClass("is-active");
				}

				step.on("click", function ( evt ) {
					form.previous = form.active;
					form.active = parseInt(this.dataset.idx);

					show();
				});

				step.appendTo(form.stepper.el);
				form.stepper.steps[ i ] = step;
			}
		} // controls end

		jQuery(settings.buttons.next).on("click", next);
		jQuery(settings.buttons.prev).on("click", prev);

		show();
		form.first = false;
	}

	function _setWrapperHeight( evt = false ) {
		let wrapHeight = 0;
		form.steps.each(( idx, stepElement ) => {
			wrapHeight = Math.max(wrapHeight, jQuery(stepElement).height());
		});
		form.innerWrap.css('height', wrapHeight + 'px');
	}

	function show() {
		if ( !form.first ) {
			/** laeuft ohne animation*/
			if ( settings.animation === false ) {
				form.steps.hide().toggleClass("is-active", false);
				jQuery(form.steps[ form.active ]).toggleClass('is-active', true).show();
			}
			else {
				/** animation */
				let prev = jQuery(form.steps[ form.previous ]),
					act = jQuery(form.steps[ form.active ]);
				prev.addClass('animate-out');
				act.addClass('animate-in').show();

				if ( form.previous > form.active ) {
					prev.addClass('is-reverse');
					act.addClass('is-reverse');
				}

				prev.one('animationend', function () {
					jQuery(this).hide().removeClass('is-reverse animate-out is-active');
				});
				act.one('animationend', function () {
					act.removeClass('is-reverse').toggleClass('animate-in is-active');
				});

				// jQuery(form.steps[ form.active ]).toggleClass("is-active", true).css({animation: "slideInRight .5s linear forwards"}).show();
			}
		}
		else {
			jQuery(form.steps[ form.active ]).toggleClass("is-active", true).show();
		}

		setStepper();
	}

	function next( evt ) {
		evt.preventDefault();
		if ( form.active < (form.stepCount - 1) ) {
			form.previous = form.active;
			form.active++;

			show();
		}

		return false;
	}

	function prev( evt ) {
		evt.preventDefault();
		if ( form.active > 0 ) {
			form.previous = form.active;
			form.active--;

			show();
		}

		return false;
	}

	function setStepper() {
		if ( settings.controls ) {

			for ( let s = 0; s < form.stepCount; s++ ) {
				form.stepper.steps[ s ].removeClass("is-active");
				if ( s === form.active )
					jQuery(form.stepper.steps[ s ]).addClass("is-active");
			}

		}
	}

	function _touchStart( evt ) {
		// evt.preventDefault();
		form.touch.start = evt.changedTouches[ 0 ].screenX;

	}

	function _touchEnd( evt ) {
		// evt.preventDefault();

		let treshold = Math.abs(evt.changedTouches[ 0 ].screenX - form.touch.start);

		if ( treshold >= settings.swipeTreshold ) {

			if ( form.touch.start < evt.changedTouches[ 0 ].screenX ) {
				form.touch.direction = 'right';
				el.trigger('swiperight');
			}
			else {
				el.trigger('swipeleft');
			}
			form.touch.start = null;
		}

	}

	init();

	return this;

};
jQuery(".js-steps").stepForm({animation: true, swipe: true});


jQuery('form', '.femanager_new').on('submit', function ( evt ) {
	evt.preventDefault();

	let form = jQuery(this);
	if ( jQuery('.error', form).length === 0 && typeof evt.originalEvent !== 'undefined' ) {

		jQuery.ajax(form.attr('action'), {
			method: form.attr('method'),
			data: form.serialize()
		}).then(function ( response ) {

			let result = JSON.parse(response);
			// let html = jQuery('div').html(response);
			if ( result.status === 'SUCCESS' ) {


				let el = jQuery(this),
					registrationModal = jQuery("<div id='registrationDialog'></div>").appendTo("body");

				registrationModal.hide();

				let rateHTML = `<div>
<p>Dein BAXI-Konto</p>
<p class="h1">E-Mail Bestätigen</p>
<div>${result.message}</div>
<div class="help">
<h4><strong>Keine E-Mail erhalten?</strong></h4>
<p>Du hast noch keine E-Mail von uns erhalten? Es kann ggf. ein paar Minuten dauern, bis sie bei dir angezeigt wird. Bitte prüfe auch deinen Spam-Ordner. Hat das nicht geholfen? E-Mail erneut senden</p>
</div>
<div class="box-footer box-footer--with-button">
		<a href="/" type="button" class="btn btn--next" data-action="closeDialog">Zum Startbildschirm</a>
	</div>
</div>`;
				registrationModal.append(rateHTML);

				let dialogParams = {};

				if ( typeof Object.assign == 'function' ) {
					dialogParams = Object.assign({}, dialogDefaults, {});
				}
				else {
					dialogParams = jQuery.extend({}, dialogDefaults, {});
				}

				registrationModal.dialog(dialogParams);

			}
			else {

			}

		});
	}
});

$(window).on("load", function ( e ) {

	if ( Store && typeof baxiSearchSettings === "object" ) {
		if ( typeof baxiSearchSettings.favorites === "string" ) {
			Store.set("favorites", JSON.parse(baxiSearchSettings.favorites));
		}
		Store.set("settings", baxiSearchSettings);
	}
});
