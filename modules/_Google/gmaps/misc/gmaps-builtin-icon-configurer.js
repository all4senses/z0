// $Id: gmaps-builtin-icon-configurer.js,v 1.1 2008/11/19 12:16:08 xmarket Exp $

function GMapsBuiltinIconConfigurer() {
  var preview = function(force) {
    var icons = $('#edit-builtinid').get(0);
    var id = icons.options[icons.selectedIndex].value
    $('#gmaps-builtin-icon-preview').attr('src', Drupal.settings.gmapsBuiltinIconsBasePath + Drupal.settings.gmapsBuiltinIcons[id].image);
    if ($('#edit-name').get(0).value == '' || force) {
      $('#edit-name', $('#gmaps-builtin-icon-preview').parent().parent()).get(0).value = Drupal.settings.gmapsBuiltinIcons[id].name;
    }
  }
  $('#edit-builtinid').change( function() {preview(true)} )
    .keyup( function(event) {
      if (!event) {
        event = window.event;
      }
      switch (event.keyCode) {
        case 33: // page up
        case 34: // page down
        case 35: // end
        case 36: // home
        case 37: // left arrow
        case 38: // up arrow
        case 39: // right arrow
        case 40: // down arrow
          preview(true);
          return true;
        default: // all other keys
          return true;
      }
    } );

  preview();
}
// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready( function () {
    GMapsBuiltinIconConfigurer();
  });
}
