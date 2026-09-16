(function($){
  'use strict';

  function updateLowStock(variation){
    var $message = $('[data-knk-low-stock]');
    if(!$message.length) return;

    var qty = parseInt(variation && variation.max_qty, 10);

    if(Number.isFinite(qty) && qty > 0 && qty <= 5){
      $message.text('残り ' + qty + ' 点').prop('hidden', false);
    }else{
      $message.text('').prop('hidden', true);
    }
  }

  $(document)
    .on('found_variation', '.variations_form', function(event, variation){
      updateLowStock(variation);
    })
    .on('reset_data hide_variation', '.variations_form', function(){
      $('[data-knk-low-stock]').text('').prop('hidden', true);
    });

  function cleanupNavigation(){
    var homeUrl = window.location.origin + '/';

    document.querySelectorAll(
      'header nav a, header .wp-block-navigation a, .wp-block-navigation__container a'
    ).forEach(function(link){
      var text = (link.textContent || '').trim();
      var href = link.href || '';

      var isSample =
        text === 'Sample Page' ||
        text === 'サンプルページ' ||
        href.indexOf('/sample-page') !== -1;

      if(isSample){
        var item = link.closest('li');
        if(item){ item.remove(); } else { link.remove(); }
        return;
      }

      var normalized = href.replace(/\/+$/, '') + '/';
      var normalizedHome = homeUrl.replace(/\/+$/, '') + '/';
      if(
        normalized === normalizedHome &&
        (text === 'KNKJOB STORE' || text === 'knkjob-store')
      ){
        var duplicateItem = link.closest('li');
        if(duplicateItem){ duplicateItem.remove(); }
      }
    });
  }

  cleanupNavigation();
  setTimeout(cleanupNavigation, 250);
  setTimeout(cleanupNavigation, 1000);

})(jQuery);
