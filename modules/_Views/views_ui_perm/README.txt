$Id: README.txt,v 1.4.2.2 2008/09/02 19:03:00 swentel Exp $

Access control
--------------
Important: If you give someone the 'restricted ui' permission, DO NOT give him
administer views permissions.

Theming override
----------------
You need to copy following php code to your template.php to override theme_fieldset.
If you allready have a fieldset override, you only need to copy the first 9 lines
and add them at the top of the function.

/**
 * Override theme_fieldset.
 */
function phptemplate_fieldset($element) {
  // If we are editing a view, check if fieldset is enabled.
  if ($GLOBALS['views_ui_edit_page']) {    
    $available_fields = views_ui_perm_available_fields();
    $ui_permissions = variable_get('views_ui_permissions', views_ui_perm_default_values($available_fields));
    // Return nothing if it isn't enabled
    if (in_array($element['#title'], $GLOBALS['views_fieldsets'])) {
      if ($ui_permissions[$element['#title']] == '0') return;
    }
  }
  
  // render normal fieldset
  if ($element['#collapsible']) {
    drupal_add_js('misc/collapse.js');

    if (!isset($element['#attributes']['class'])) {
      $element['#attributes']['class'] = '';
    }

    $element['#attributes']['class'] .= ' collapsible';
    if ($element['#collapsed']) {
     $element['#attributes']['class'] .= ' collapsed';
    }
  }
  
  return '<fieldset' . drupal_attributes($element['#attributes']) .'>' . ($element['#title'] ? '<legend>'. $element['#title'] .'</legend>' : '') . ($element['#description'] ? '<div class="description">'. $element['#description'] .'</div>' : '') . $element['#children'] . $element['#value'] . "</fieldset>\n";
}