
jQuery(document).ready(function() {
    
    
    $('div.p-photos.google-images .caption').html('Показать подходящие фото из поиска Google...');
    $('div.p-photos.google-images .wait').html('Пожалуйста, подождите...');
    
    processed = false;
    
    jQuery('div.p-photos.google-images .caption').click(function() 
          {
                
                if (!processed) {
                
                    processed = true;
                    
                    $('div.p-photos.google-images .caption').toggleClass('hidden');
                    $('div.p-photos.google-images .wait').toggleClass('hidden');
                    
                    (jQuery).ajax
                        ({
                              url: '/get_gooimages', 
                              data: {
                                      op: 'get',
                                      url: window.location.href,
                                      bar: Drupal.settings.zp_functions.bar,
                                      title: Drupal.settings.zp_functions.title,
                                      title_corrected: Drupal.settings.zp_functions.title_corrected,
                                      title_rus: Drupal.settings.zp_functions.title_rus,
                                      podgruppa: Drupal.settings.zp_functions.podgruppa,
                                      ipath: Drupal.settings.zp_functions.ipath,
                                      nid: Drupal.settings.zp_functions.nid
                                    }, 
                                  type: 'POST', 
                                  dataType: 'json', 
                                  success: function(data) 
                                          { 
                                              $('div.p-photos.google-images .caption').toggleClass('hidden');
                                              $('div.p-photos.google-images .wait').toggleClass('hidden');
                    
                                              if(!data.error && data.status) {
                                                //console.log('The header is arrived!');
                                                
                                                  if (Drupal.settings.zp_functions.uid == 1) {
                                                    $('div.p-photos.google-images .clicks').toggleClass('hidden');
                                                    $('div.p-photos.google-images .clicks').html('Кликов: ' + data.clicks);
                                                  }
                                                  
                                                  $('div.p-photos.google-images .results-caption').toggleClass('hidden');
                                                  $('div.p-photos.google-images .results-caption.r0').html(data.out_0_title);
                                                  $('div.p-photos.google-images .results-caption.r1').html(data.out_1_title);
                                                  
                                                  if (data.out_0 || data.out_1) {
                                                    $('div.p-photos.google-images .results-present-explain').toggleClass('hidden');
                                                    $('div.p-photos.google-images .results-present-explain').html('<strong>Внимание!</strong><br/> Некоторые или даже все найденные и показанные тут изображения <strong>могут не вполне или совсем не соответствовать</strong> реальному фото товара и <strong>не могут быть основанием для претензий</strong> к виду/дизайну/форме/размеру и т.д. реального товара!!!');
                                                  }
                                                  
                                                  if (data.out_0) {
                                                    $('div.p-photos.google-images .results.r0').html(data.out_0);
                                                    $('div.p-photos.google-images .bad-button.r0').toggleClass('hidden');
                                                    if (data.out_0_hidden) {
                                                      $('div.p-photos.google-images .bad-button.r0').html('Фото скрыты! Открыть?');
                                                      $('div.p-photos.google-images .bad-button.r0').addClass('unhide');
                                                    }
                                                    else {
                                                      $('div.p-photos.google-images .bad-button.r0').html('Нет ни одного подходящего фото?');
                                                    }
                                                  }
                                                  else {
                                                    $('div.p-photos.google-images .no-results.r0').toggleClass('hidden');
                                                    $('div.p-photos.google-images .no-results.r0').html('Нет совпадений.');
                                                  } 
                                                  
                                                  if (data.out_1) {
                                                    $('div.p-photos.google-images .results.r1').html(data.out_1);
                                                    $('div.p-photos.google-images .bad-button.r1').toggleClass('hidden');
                                                    
                                                    if (data.out_1_hidden) {
                                                      $('div.p-photos.google-images .bad-button.r1').html('Фото скрыты! Открыть?');
                                                      $('div.p-photos.google-images .bad-button.r1').addClass('unhide');
                                                    }
                                                    else {
                                                      $('div.p-photos.google-images .bad-button.r1').html('Нет ни одного подходящего фото?');
                                                    }
                                                    
                                                  }
                                                  else {
                                                    $('div.p-photos.google-images .no-results.r1').toggleClass('hidden');
                                                    $('div.p-photos.google-images .no-results.r1').html('Нет совпадений.');
                                                  }
                                                
                                                
                                              }
                                              else {
                                                ;//alert(data.error);
                                              }
                                                  
                                              return false;
                                          } 

                          }); // end of (jQuery).ajax
    
    
                }
                else {
                    $('div.p-photos.google-images .all-results').toggleClass('hidden');
                }
    
    
          }); // End of jQuery('div.p-photos.google-images .google-notice .caption').click(function() 
    

    
    
    jQuery('div.p-photos.google-images .bad-button').click(function() {
      
      
      
      
      
      
      
      if (jQuery(this).hasClass('unhide')) {
        op = 'unhide';
        alert('Фото будут опять открыты. Спасибо тебе, дорогой админ!');
      }
      else {
        op = 'hide';
        alert('Благодарим Вас за участие. Указанные Вами плохие изображения больше не будут показываться на нашем сайте.');
        jQuery(this).parent().hide();
      }
      
      if (jQuery(this).hasClass('r0')) {
        results_index = 0;
      }
      else if (jQuery(this).hasClass('r1')) {
        results_index = 1;
      }
      
      (jQuery).ajax
                        ({
                              url: '/get_gooimages', 
                              data: {
                                      op: op,
                                      index_to_hide: results_index,
                                      url: window.location.href,
                                      bar: Drupal.settings.zp_functions.bar,
                                      title: Drupal.settings.zp_functions.title,
                                      title_corrected: Drupal.settings.zp_functions.title_corrected,
                                      title_rus: Drupal.settings.zp_functions.title_rus,
                                      podgruppa: Drupal.settings.zp_functions.podgruppa,
                                      ipath: Drupal.settings.zp_functions.ipath,
                                      nid: Drupal.settings.zp_functions.nid
                                    }, 
                                  type: 'POST', 
                                  dataType: 'json', 
                                  success: function(data) 
                                          { 
                                              if(!data.error && data.status) {
                                                //console.log('The header is arrived!');
                                                if (op == 'unhide') {
                                                  //top.location.reload();
                                                  $('div.p-photos.google-images .bad-button.r' + results_index).toggleClass('unhide');
                                                  $('div.p-photos.google-images .bad-button.r' + results_index).html('Нет ни одного подходящего фото?');

                                                }
                                                
                                              }
                                              else {
                                                ;//alert(data.error);
                                              }
                                                  
                                              return false;
                                          } 

                          }); // end of (jQuery).ajax
    
    }); // End of jQuery('div.p-photos.google-images .google-notice .caption').click(function() 
    
    
    
});
