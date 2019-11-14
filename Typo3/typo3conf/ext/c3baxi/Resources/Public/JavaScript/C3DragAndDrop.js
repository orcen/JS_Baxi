"use strict";

requirejs(['jquery', 'jquery-ui/draggable', 'jquery-ui/droppable', 'jquery-ui/sortable'], function (jQuery) {
  jQuery('[data-toggle="filter"]').on('input', function (evt) {
    var s = evt.currentTarget.value,
        target = jQuery(evt.currentTarget.dataset.target),
        items = [];
    if (target.length === 0) return;

    if (target.prop('tagName') === 'SELECT') {
      // SelectBox
      items = target.find('option');
    } else {
      items = target.find('li');
    }

    items.show(); // show all
    // min length 3

    if (s < 3) return;
    items.map(function () {
      var txt = jQuery(this).text();
      if (false === txt.toLowerCase().includes(s.toLowerCase())) jQuery(this).hide();
    });
  });

  function dragClone(evt) {
    // console.log(evt);
    var clone = jQuery(evt.currentTarget).clone(true);
    clone.appendTo('body');
    return clone;
  }

  var ajaxTarget = jQuery('form').data('ajax');
  console.log(ajaxTarget);
  jQuery('[data-draggable="true"]').draggable({
    revert: "invalid",
    helper: dragClone
  });
  jQuery('[data-droppable="true"]').droppable({
    drop: function drop(evt, ui) {
      var dropArea = jQuery(evt.target),
          item = jQuery(ui.draggable[0]); // if( jQuery( evt.originalEvent.target ).hasClass('list-item') ) {
      // 	item.insertBefore( evt.originalEvent.target );
      // }
      // else {

      item.appendTo(dropArea); // }

      item.draggable({
        revert: "invalid",
        helper: dragClone
      });
      var pid = dropArea.data('uid'); // 	stellenId = ui.draggable[ 0 ].dataset.id;

      if (pid != '') {
        jQuery.ajax(TYPO3.settings.ajaxUrls[ajaxTarget], {
          data: {
            'tx_c3baxi_baxisuche': {
              parentUid: pid,
              childUid: item.data('uid') // 'type': 666

            }
          },
          method: 'get',
          complete: function complete(result) {
            console.log(result); // window.location.reload();
          }
        });
      } else {
        var list = jQuery('[name="tx_c3baxi_web_c3baxibaxi[liste]"]');
        list.val(list.val() + ',' + item.data('uid'));
      }
    }
  });
});

//# sourceMappingURL=C3DragAndDrop.js.map