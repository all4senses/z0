// $Id: gmaps-factory-icon-configurer.js,v 1.1 2008/11/19 12:16:08 xmarket Exp $

function GMapsFactoryIconConfigurer() {
  var form = $('#gmaps_factory_icon_color_form .color-form');
  var inputs = [];
  var hooks = [];
  var locks = [];
  var focused = null;
  var baseUrl = "http://chart.apis.google.com/chart?cht=mm";

  // Add Farbtastic
  $(form).prepend('<div id="placeholder"></div>');
  var farb = $.farbtastic('#placeholder');

  // Decode reference colors to HSL
  var reference = Drupal.settings.gmapsFactoryColors.reference;
  for (i in reference) {
    reference[i] = farb.RGBToHSL(farb.unpack(reference[i]));
  }

  /**
   * Render the preview.
   */
  function preview() {
    var iconUrl = baseUrl + "&chs=" + $('#edit-icon-width').get(0).value + "x" + $('#edit-icon-height').get(0).value + 
      "&chco=" + $('#edit-cornercolor').get(0).value.replace("#", "") + "," + $('#edit-primarycolor').get(0).value.replace("#", "") + "," +
      $('#edit-strokecolor').get(0).value.replace("#", "") + "&ext=.png";
    $('#gmaps-factory-icon-preview').attr('src', iconUrl);
  }

  function shift_color(given, ref1, ref2) {
    // Convert to HSL
    given = farb.RGBToHSL(farb.unpack(given));

    // Hue: apply delta
    given[0] += ref2[0] - ref1[0];

    // Saturation: interpolate
    if (ref1[1] == 0 || ref2[1] == 0) {
      given[1] = ref2[1];
    }
    else {
      var d = ref1[1] / ref2[1];
      if (d > 1) {
        given[1] /= d;
      }
      else {
        given[1] = 1 - (1 - given[1]) * d;
      }
    }

    // Luminance: interpolate
    if (ref1[2] == 0 || ref2[2] == 0) {
      given[2] = ref2[2];
    }
    else {
      var d = ref1[2] / ref2[2];
      if (d > 1) {
        given[2] /= d;
      }
      else {
        given[2] = 1 - (1 - given[2]) * d;
      }
    }

    return farb.pack(farb.HSLToRGB(given));
  }

  /**
   * Callback for Farbtastic when a new color is chosen.
   */
  function callback(input, color, propagate, colorscheme) {
    // Set background/foreground color
    $(input).css({
      backgroundColor: color,
      color: farb.RGBToHSL(farb.unpack(color))[2] > 0.5 ? '#000' : '#fff'
    });

    // Change input value
    if (input.value && input.value != color) {
      input.value = color;

      // Update locked values
      if (propagate) {
        var i = input.i;
        for (j = i + 1; ; ++j) {
          if (!locks[j - 1] || $(locks[j - 1]).is('.unlocked')) break;
          var matched = shift_color(color, reference[input.key], reference[inputs[j].key]);
          callback(inputs[j], matched, false);
        }
        for (j = i - 1; ; --j) {
          if (!locks[j] || $(locks[j]).is('.unlocked')) break;
          var matched = shift_color(color, reference[input.key], reference[inputs[j].key]);
          callback(inputs[j], matched, false);
        }

        // Update preview
        preview();
      }
    }
  }

  // Focus the Farbtastic on a particular field.
  function focus() {
    var input = this;
    // Remove old bindings
    focused && $(focused).unbind('keyup', farb.updateValue)
      .unbind('keyup', preview).parent().removeClass('item-selected');

    // Add new bindings
    focused = this;
    farb.linkTo(function (color) { callback(input, color, true, false) });
    farb.setColor(this.value);
    $(focused).keyup(farb.updateValue).keyup(preview).parent().addClass('item-selected');
  }

  // Initialize color fields
  $('#palette input.form-text', form)
  .each(function () {
    // Extract palette field name
    this.key = this.id.substring(5);

    // Link to color picker temporarily to initialize.
    farb.linkTo(function () {}).setColor('#000').linkTo(this);

    // Add lock
    var i = inputs.length;
    if (inputs.length) {
      var lock = $('<div class="lock"></div>').toggle(
        function () {
          $(this).addClass('unlocked');
          $(hooks[i - 1]).attr('class',
            locks[i - 2] && $(locks[i - 2]).is(':not(.unlocked)') ? 'hook up' : 'hook'
          );
          $(hooks[i]).attr('class',
            locks[i] && $(locks[i]).is(':not(.unlocked)') ? 'hook down' : 'hook'
          );
        },
        function () {
          $(this).removeClass('unlocked');
          $(hooks[i - 1]).attr('class',
            locks[i - 2] && $(locks[i - 2]).is(':not(.unlocked)') ? 'hook both' : 'hook down'
          );
          $(hooks[i]).attr('class',
            locks[i] && $(locks[i]).is(':not(.unlocked)') ? 'hook both' : 'hook up'
          );
        }
      );
      $(this).after(lock);
      locks.push(lock);
    }

    // Add hook
    var hook = $('<div class="hook"></div>');
    $(this).after(hook);
    hooks.push(hook);

    $(this).parent().find('.lock').click();
    this.i = i;
    inputs.push(this);
  })
  .focus(focus);
  
  $('input.gmaps-factory-icon-size-field').keyup(preview);

  $('#palette label', form)

  // Focus first color
  focus.call(inputs[0]);

  // Render preview
  preview();
}

// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready( function () {
    GMapsFactoryIconConfigurer();
  });
}
