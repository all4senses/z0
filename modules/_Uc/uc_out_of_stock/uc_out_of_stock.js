// $Id: uc_out_of_stock.js,v 1.1.2.9 2009/05/06 19:51:25 hanoii Exp $

$(document).ready(function(){
  // Your code here
  attrid = 'edit-attributes';

  function checkStock(form) {
    var product = new Object();
    var attributes = new Object();
    var formid_data = new Array();
    
    formid_data = form.attr('id').split('-')
    product.nid = formid_data[formid_data.length-1];
    attributes.found = new Object();
    attributes.value = new Object();

    $(":input[@name*=attributes]:not(:text)", form).each(function(index){
      id = $(this).attr('name').substring(11,$(this).attr('name').length-1);
      if ($(this).is(':radio')) {
        attributes.found['attr'+id] = 1;
        if ($(this).is(':checked')) {
          if ($(this).val()) {
            attributes.value['attr'+id] = 1;
            product['attr'+id] = $(this).val();
          }
        }
      } else {
      attributes.found['attr'+id] = 1;
        if ($(this).val()) {
          attributes.value['attr'+id] = 1;
          product['attr'+id] = $(this).val();
        }
      }
    });

    // finding if attributes are found with no value
    attributes.found.length = attributes.value.length = 0;
    for (var i in attributes.found) {
      if (i!='length') {
        attributes.found.length++;
      }
    }
    for (var i in attributes.value) {
      if (i!='length') {
        attributes.value.length++;
      }
    }
    if (attributes.found.length != attributes.value.length) {
      // Put back the normal HTML of the add to cart form
      $(".uc_out_of_stock_html", form).html('');
      $("input:submit", form).show();
      return;
    }

    $(".uc_out_of_stock_throbbing", form).addClass('uc_oos_throbbing');
    $.post(Drupal.settings.base_path+'uc_out_of_stock/query', product, function (data, textStatus) {
      // textStatus can be one of:
      //   "timeout"
      //   "error"
      //   "notmodified"
      //   "success"
      //   "parsererror"
      data = data.split('|');
      stock = data[0];
      if (stock == parseInt(stock) && stock <= 0 && data.length == 2) {
        html = data[1];
        $("input:submit", form).hide();
        $(".uc_out_of_stock_html", form).html(html);
      } else {
        // Put back the normal HTML of the add to cart form
        $(".uc_out_of_stock_html", form).html('');
        $("input:submit", form).show();
      }

      $(".uc_out_of_stock_throbbing", form).removeClass('uc_oos_throbbing');
    });
  }

  $("form[@id*=uc-product-add-to-cart-form]").each(function(index) {
    var eachForm;
    $("input:submit", $(this)).before('<div class="uc_out_of_stock_throbbing">&nbsp;&nbsp;&nbsp;&nbsp;</div> ');
    $("input:submit", $(this)).after('<div class="uc_out_of_stock_html"></div');

    eachForm = $(this);
    checkStock(eachForm);

    $(":input[@name*=attributes]:not(:text)", $(this)).change(function(){
      checkStock(eachForm);
    });
  });

  $("form[@id*=uc-catalog-buy-it-now-form]").each(function(index) {
    var eachForm;
    $("input:submit", $(this)).before('<div class="uc_out_of_stock_throbbing">&nbsp;&nbsp;&nbsp;&nbsp;</div> ');
    $("input:submit", $(this)).after('<div class="uc_out_of_stock_html"></div');

    eachForm = $(this);
    checkStock(eachForm);

    // This form has no possible attributes
  });
});