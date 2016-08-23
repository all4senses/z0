// $Id: uc_cart.js,v 1.7.2.6 2008/11/03 21:26:35 rszrama Exp $

var copy_box_checked = false;

/**
 * Scan the DOM and display the cancel and continue buttons.
 */
$(document).ready(








  function() {
    $('.show-onload').show();



// my changes

// ��� �������� ����� �������� ������� ����������� ������ �������� � ����� ��������

uc_cart_copy_address(1, 'delivery', 'billing');

$('#edit-continue').click(function () { 
//$(this).hide("slow");
$('#edit-panes-delivery-delivery-zone').removeAttr('disabled'); 
$('#edit-panes-billing-billing-zone').removeAttr('disabled'); 

    });




    $('form#uc-cart-checkout-review-form input#edit-submit').click(function() {
      $(this).clone().insertAfter(this).attr('disabled', true).after('<span id=\"submit-throbber\" style=\"background: url(' + Drupal.settings['base_path'] + 'misc/throbber.gif) no-repeat 100% -20px;\">&nbsp;&nbsp;&nbsp;&nbsp;</span>').end().hide();
      $('#uc-cart-checkout-review-form #edit-back').attr('disabled', true);
    });
  }
);

/**
 * When a customer clicks a Next button, expand the next pane, remove the
 * button, and don't let it collapse again.
 */
function uc_cart_next_button_click(button, pane_id, current) {
  if (current !== 'false') {
    $('#' + current + '-pane legend a').click();
  }
  else {
    button.disabled = true;
  }

  if ($('#' + pane_id + '-pane').attr('class').indexOf('collapsed') > -1 && $('#' + pane_id + '-pane').html() !== null) {
    $('#' + pane_id + '-pane legend a').click();
  }

  return false;
}

/**
 * Copy the delivery information to the payment information on the checkout
 * screen if corresponding fields exist.
 */
function uc_cart_copy_address(checked, source, target) {

if (!checked) {
    $('#' + target + '-pane div.address-pane-table').slideDown();
    copy_box_checked = false;
    return false;
  }

  if (target == 'billing') {
    var x = 28;
  }
  else {
    var x = 26;
  }

  // Hide the target information fields.
  $('#' + target + '-pane div.address-pane-table').slideUp();
  copy_box_checked = true;

  // Copy over the zone options manually.
  if ($('#edit-panes-' + target + '-' + target + '-zone').html() != $('#edit-panes-' + source + '-' + source + '-zone').html()) {
    $('#edit-panes-' + target + '-' + target + '-zone').empty().append($('#edit-panes-' + source + '-' + source + '-zone').children().clone());
    $('#edit-panes-' + target + '-' + target + '-zone').attr('disabled', $('#edit-panes-' + source + '-' + source + '-zone').attr('disabled'));
  }

  // Copy over the information and set it to update if delivery info changes.
  $('#' + source + '-pane input, select, textarea').each(
    function() {
      if (this.id.substring(0, x) == 'edit-panes-' + source + '-' + source) {
        $('#edit-panes-' + target + '-' + target + this.id.substring(x)).val($(this).val());
        if (target == 'billing') {
          $(this).change(function () { update_billing_field(this); });
        }
        else {
          $(this).change(function () { update_delivery_field(this); });
        }
      }
    }
  );

  return false;
}

function update_billing_field(field) {
  if (copy_box_checked) {
    if (field.id.substring(29) == 'zone') {
      $('#edit-panes-billing-billing-zone').empty().append($('#edit-panes-delivery-delivery-zone').children().clone());
      $('#edit-panes-billing-billing-zone').attr('disabled', $('#edit-panes-delivery-delivery-zone').attr('disabled'));
    }

    $('#edit-panes-billing-billing' + field.id.substring(28)).val($(field).val());
  }
}

function update_delivery_field(field) {
 
if (copy_box_checked) {
    if (field.id.substring(27) == 'zone') {
      $('#edit-panes-delivery-delivery-zone').empty().append($('#edit-panes-billing-billing-zone').children().clone());
      $('#edit-panes-delivery-delivery-zone').attr('disabled', $('#edit-panes-billing-billing-zone').attr('disabled'));
    }

    $('#edit-panes-delivery-delivery' + field.id.substring(26)).val($(field).val());
  }
}

/**
 * Apply the selected address to the appropriate fields in the cart form.
 */
function apply_address(type, address_str) {

 if (address_str == '0') {
    return;
  }

  eval('var address = ' + address_str + ';');
  var temp = type + '-' + type;

  $('#edit-panes-' + temp + '-first-name').val(address.first_name).trigger('change');
  $('#edit-panes-' + temp + '-last-name').val(address.last_name).trigger('change');
  $('#edit-panes-' + temp + '-phone').val(address.phone).trigger('change');
  $('#edit-panes-' + temp + '-company').val(address.company).trigger('change');
  $('#edit-panes-' + temp + '-street1').val(address.street1).trigger('change');
  $('#edit-panes-' + temp + '-street2').val(address.street2).trigger('change');
  $('#edit-panes-' + temp + '-city').val(address.city).trigger('change');
  $('#edit-panes-' + temp + '-postal-code').val(address.postal_code).trigger('change');

  if ($('#edit-panes-' + temp + '-country').val() != address.country) {
    $('#edit-panes-' + temp + '-country').val(address.country).trigger('change');
    try {
      uc_update_zone_select('edit-panes-' + temp + '-country', address.zone);
      

    }
    catch (err) { }
  }

  $('#edit-panes-' + temp + '-zone').val(address.zone).trigger('change');
//$('#edit-panes-' + temp + '-zone').attr('disabled','disabled');


//$('#edit-panes-' + temp + '-zone').removeAttr('disabled');






// my changes

// ���� �������� ����� ��������, ��������� �������� �� �� �� �������� � ����� ��������


if(type == 'delivery')
{


type = 'billing';


if (address_str == '0') {
    return;
  }

  eval('var address = ' + address_str + ';');
  var temp = type + '-' + type;

  $('#edit-panes-' + temp + '-first-name').val(address.first_name).trigger('change');
  $('#edit-panes-' + temp + '-last-name').val(address.last_name).trigger('change');
  $('#edit-panes-' + temp + '-phone').val(address.phone).trigger('change');
  $('#edit-panes-' + temp + '-company').val(address.company).trigger('change');
  $('#edit-panes-' + temp + '-street1').val(address.street1).trigger('change');
  $('#edit-panes-' + temp + '-street2').val(address.street2).trigger('change');
  $('#edit-panes-' + temp + '-city').val(address.city).trigger('change');
  $('#edit-panes-' + temp + '-postal-code').val(address.postal_code).trigger('change');

  if ($('#edit-panes-' + temp + '-country').val() != address.country) {
    $('#edit-panes-' + temp + '-country').val(address.country).trigger('change');
    try {
      uc_update_zone_select('edit-panes-' + temp + '-country', address.zone);
    }
    catch (err) { }
  }

  $('#edit-panes-' + temp + '-zone').val(address.zone).trigger('change');



}






}
