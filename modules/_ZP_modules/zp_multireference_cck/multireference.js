// $Id: multireference.js,v 1.1 2008/03/31 21:12:52 stevem Exp $

if (Drupal.jsEnabled) {
  $(document).ready(function () {
    $("a.multireference_add_fields").click(function() {
      // determine field_name + next_item
      var new_item   = $(this).attr("next_item");
      var field_name = $(this).attr("field_name");

      // select correct row to duplicate
      var old_item = new_item-1;
      var row_id  = "#" + field_name + "_" + old_item;
      var new_row = $(row_id).html();

      // replace row ids, etc.
      var regex  = new RegExp( "(item[\_\-])" + old_item, "ig" );
      var newex  = "$1" + new_item;
      var new_id = field_name + "_" + new_item;
      new_row = new_row.replace(regex,newex);
      new_row = '<tr id="' + new_id + '">' + new_row + '</tr>';

      // add row
      $(row_id).after(new_row);

      // reset values
      new_id = "#" + new_id;
      $(new_id).clearForm();

      // update next_item value
      $(this).attr("next_item",++new_item);

      // reload autocomplete JS
       $(document).each(Drupal.autocompleteAutoAttach);
    });
  });

  $.fn.clearForm = function() {
    return this.each(function() {
    var type = this.type, tag = this.tagName.toLowerCase();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = -1;
    else
      $(this).children().clearForm();
    });
  };
}

