<?php
// $Id: gmaps-admin.inc,v 1.2 2008/12/13 19:47:56 xmarket Exp $

function _gmaps_map_config_form($config) {
  //TODO: add priview map, hook_gmaps_map_types('form', $config), hook_gmaps_overlay_handlers('form', $config), default_icon,
  //googlebar, gadsmanager, auto_center_zoom
  $form = array();
  
  $maptype = gmaps_get_map_types('type', $config->type);
  
  $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);

  $form['identity'] = array(
    '#type' => 'fieldset',
    '#title' => t('Identification'),
  );
  if (isset($config->mcid)) {
    $form['identity']['mcid'] = array('#type' => 'value', '#value' => $config->mcid);
  }
  $form['identity']['name'] = array('#type' => 'textfield',
    '#title' => t('Configuration name'),
    '#default_value' => $config->name,
    '#description' => t('The human-readable name of this configuration. This name must be unique.'),
    '#required' => TRUE,
  );
  $form['identity']['type'] = array('#type' => 'value', '#value' => $config->type);
  $form['identity']['type_display'] = array('#type' => 'item', '#title' => t('Map type'), '#value' => theme('placeholder', $maptype->name));

  $form['map'] = array(
    '#type' => 'fieldset',
    '#title' => t('Map settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['map']['center'] = array('#type' => 'latlon',
    '#title' => t('Default center'),
    '#default_value' => $config->center,
    '#use_map_config' => $config,
    '#required' => TRUE,
  );
  $form['map']['width'] = array('#type' => 'textfield',
    '#title' => t('Map width'),
    '#default_value' => $config->width,
    '#maxlength' => 4,
    '#size' => 6,
    '#field_suffix' => 'px',
    '#description' => t('If you leave it zero, the map will fill the entire available space.'),
  );
  $form['map']['height'] = array('#type' => 'textfield',
    '#title' => t('Map height'),
    '#default_value' => $config->height,
    '#maxlength' => 4,
    '#size' => 6,
    '#field_suffix' => 'px',
  );
  //will be available if the preview map implmented
  $form['map']['latitude'] = array('#type' => 'value', '#value' => $config->latitude);
  $form['map']['longitude'] = array('#type' => 'value', '#value' => $config->longitude);
  $form['map']['default_zoom'] = array('#type' => 'select',
    '#title' => t('Default zoom level'),
    '#options' => drupal_map_assoc(array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19)),
    '#default_value' => $config->default_zoom,
  );
  $form['map']['allowed_tiles'] = array('#type' => 'checkboxes',
    '#title' => t('Allowed tiles'),
    '#options' => $maptype->tiles,
    '#default_value' => $config->allowed_tiles,
  );
  $form['map']['default_tile'] = array('#type' => 'select',
    '#title' => t('Default tile'),
    '#options' => $maptype->tiles,
    '#default_value' => $config->default_tile,
  );
  $form['map']['methods'] = array('#type' => 'checkboxes',
    '#title' => t('Methods'),
    '#options' => array(
      'dragging' => t('Enable dragging'),
      'infowindow' => t('Enable info window'),
      'doubleclickzoom' => t('Enable double click zoom'),
      'continuouszoom' => t('Enable continous zoom'),
      'scrollwheelzoom' => t('Enable scroll wheel zoom'),
      'googlebar' => t('Enable Google search bar'),
      'auto_center_zoom' => t('Enable automatic center and zoom'),
      'keyboardhandler' => t('Enable keyboard handler'),
      'resize' => t('Enable height resize'),
    ),
    '#default_value' => $config->methods,
  );
  $form['map']['map_control'] = array('#type' => 'select',
    '#title' => t('Map control'),
    '#options' => array(t('Disabled'), t('Small zoom control'), t('Small map control'), t('Large map control')),
    '#default_value' => $config->map_control,
  );
  $form['map']['type_control'] = array('#type' => 'select',
    '#title' => t('Map type control'),
    '#options' => array(t('Disabled'), t('Standard'), t('Hierarchical'), t('Menu')),
    '#default_value' => $config->type_control,
  );
  $form['map']['type_control_shortnames'] = array('#type' => 'checkbox',
    '#title' => t('Use short names in the type control'),
    '#default_value' => $config->type_control_shortnames,
  );
  $form['map']['scale_control'] = array('#type' => 'checkbox',
    '#title' => t('Enable scale control'),
    '#default_value' => $config->scale_control,
  );
  $form['map']['overview_control'] = array('#type' => 'checkbox',
    '#title' => t('Enable overview control'),
    '#default_value' => $config->overview_control,
  );
  $form['map']['googlebar_options'] = array('#type' => 'fieldset',
    '#title' => t('Google search bar options'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => !$config->methods['googlebar'],
  );
  $form['map']['googlebar_options']['showonload'] = array('#type' => 'checkbox',
    '#title' => t('Show search bar by default'),
    '#default_value' => $config->googlebar_options['showonload'],
  );
  $form['map']['googlebar_options']['linktarget'] = array('#type' => 'select',
    '#title' => t('Target for links in search results'),
    '#options' => array(
      'G_GOOGLEBAR_LINK_TARGET_BLANK' => '_blank',
      'G_GOOGLEBAR_LINK_TARGET_SELF' => '_self',
      'G_GOOGLEBAR_LINK_TARGET_PARENT' => '_parent',
      'G_GOOGLEBAR_LINK_TARGET_TOP' => '_top',
    ),
    '#default_value' => $config->googlebar_options['linktarget'],
  );
  $form['map']['googlebar_options']['resultlist'] = array('#type' => 'select',
    '#title' => t('Result list style'),
    '#options' => array(
      'G_GOOGLEBAR_RESULT_LIST_INLINE' => t('Inline'),
      'G_GOOGLEBAR_RESULT_LIST_SUPPRESS' => t('Suppressed'),
      'element' => t('External'),
    ),
    '#default_value' => $config->googlebar_options['resultlist'],
  );
  $form['map']['googlebar_options']['suppress_selection'] = array('#type' => 'checkbox',
    '#title' => t('Suppress initial result selection'),
    '#default_value' => $config->googlebar_options['suppress_selection'],
  );
  $form['map']['googlebar_options']['suppress_zoom'] = array('#type' => 'checkbox',
    '#title' => t('Suppress zoom to bounds'),
    '#default_value' => $config->googlebar_options['suppress_zoom'],
  );
  //TODO: implement onidle, oncomplete, ongeneratemarkerhtml, onmarkers
  $form['map']['display_help'] = array('#type' => 'checkbox',
    '#title' => t('Display help'),
    '#default_value' => $config->display_help,
  );

  $form['marker'] = array(
    '#type' => 'fieldset',
    '#title' => t('Marker settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['marker']['marker_options'] = array('#type' => 'fieldset',
    '#title' => t('Marker options'),
    '#tree' => TRUE,
  );
  $form['marker']['marker_options']['default_icon'] = array('#type' => 'select',
    '#title' => t('Default icon'),
    '#options' => array(0 => t('Default API icon')) + _gmaps_get_icon_config_options(),
    '#default_value' => $config->marker_options['default_icon'],
  );
  $form['marker']['marker_options']['delayed_iw_content'] = array('#type' => 'checkbox',
    '#title' => t('Enable delayed info window content loading'),
    '#default_value' => $config->marker_options['delayed_iw_content'],
  );
  $form['marker']['marker_options']['linktarget'] = array('#type' => 'select',
    '#title' => t('Target for links'),
    '#options' => drupal_map_assoc(array('_blank', '_self', '_parent', '_top')),
    '#default_value' => $config->marker_options['linktarget'],
  );
  $form['marker']['marker_options']['iw_maxwidth'] = array('#type' => 'textfield',
    '#title' => t('Maximum width of the info window'),
    '#default_value' => $config->marker_options['iw_maxwidth'],
    '#maxlength' => 4,
    '#size' => 6,
    '#field_suffix' => 'px',
    '#required' => TRUE,
  );
  $form['marker']['marker_options']['iw_nocloseonclick'] = array('#type' => 'checkbox',
    '#title' => t('Do not close info window on map click'),
    '#default_value' => $config->marker_options['iw_nocloseonclick'],
  );
  $form['marker']['marker_options']['marker_manager'] = array('#type' => 'checkbox',
    '#title' => t('Enable marker manager'),
    '#default_value' => $config->marker_options['marker_manager'],
  );
  $form['marker']['marker_manager_options'] = array('#type' => 'fieldset',
    '#title' => t('Marker manager options'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => !$config->marker_options['marker_manager'],
  );
  $form['marker']['marker_manager_options']['borderpadding'] = array('#type' => 'textfield',
    '#title' => t('Border padding'),
    '#default_value' => $config->marker_manager_options['borderpadding'],
    '#maxlength' => 3,
    '#size' => 5,
    '#field_suffix' => 'px',
  );
  $opts = array('u' => t('Unlimited')) + drupal_map_assoc(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19));
  $form['marker']['marker_manager_options']['maxzoom'] = array('#type' => 'select',
    '#title' => t('Maximum zoom level'),
    '#options' => $opts,
    '#default_value' => $config->marker_manager_options['maxzoom'],
    '#description' => t('Set it to unlimited to use the maximum of the map type.'),
  );
  $form['marker']['marker_manager_options']['trackmarkers'] = array('#type' => 'checkbox',
    '#title' => t('Track markers'),
    '#default_value' => $config->marker_manager_options['trackmarkers'],
  );

  $form['buttons']['submit'] = array('#type' => 'submit', '#value' => t('Submit'));
  if (isset($control->mcid) && $control->mcid != 1) {
    $form['buttons']['delete'] = array('#type' => 'submit', '#value' => t('Delete'));
  }

  return $form;
}

function _gmaps_map_config_form_validate($form_id, $form_values) {
  if (isset($form_values['mcid'])) {
    $result = db_query("SELECT name FROM {gmaps_map_config} WHERE mcid != %d AND name = '%s'", $form_values['mcid'], $form_values['name']);
  } else {
    $result = db_query("SELECT name FROM {gmaps_map_config} WHERE name = '%s'", $form_values['name']);
  }
  if (db_num_rows($result)) {
    form_set_error('name', t('The name already taken by another map configuration.'));
  }
  $form_values['allowed_tiles'] = array_filter($form_values['allowed_tiles']);
  if (empty($form_values['allowed_tiles'])) {
    form_set_error('allowed_tiles', t('You should enable at least one tile.'));
  }
  else if (!in_array($form_values['default_tile'], array_keys($form_values['allowed_tiles']))) {
    form_set_error('default_tile', t('The default tile must be one of the allowed tiles.'));
  }
  if (!is_numeric($form_values['width']) || intval($form_values['width']) != $form_values['width'] || $form_values['width'] < 0) {
    form_set_error('width', t('The width must be a positive integer or zero.'));
  }
  if (!is_numeric($form_values['height']) || intval($form_values['height']) != $form_values['height'] || $form_values['height'] <= 0) {
    form_set_error('height', t('The height must be a positive integer.'));
  }
  if ($form_values['scale_control'] && $form_values['methods']['googlebar']) {
    form_set_error('scale_control', t('You cannot use scale control and Google bar together.'));
  }
  if ($form_values['methods']['googlebar'] && $form_values['type'] != 'earth') {
    form_set_error('googlebar', t('You can use the Google bar on earth maps only.'));
  }
  if (!is_numeric($form_values['marker_options']['iw_maxwidth']) || intval($form_values['marker_options']['iw_maxwidth']) != $form_values['marker_options']['iw_maxwidth'] || $form_values['marker_options']['iw_maxwidth'] < 1) {
    form_set_error('iw_maxwidth', t('The width of the info window must be a positive integer.'));
  }
  //TODO:
  /**
   * hook_gmaps_map_types('validate', $mcid);
   * hook_gmaps_overlay_handlers('validate', $mcid);
   */
}

function _gmaps_map_config_form_submit($form_id, $form_values) {
  $form_values['allowed_tiles'] = array_keys(array_filter($form_values['allowed_tiles']));
  //TODO:
  /**
   * hook_gmaps_map_types('submit', $mcid);
   * hook_gmaps_overlay_handlers('submit', $mcid);
   */
  gmaps_map_config_save($form_values);
  
  if (!isset($form_values['mcid'])) {
    drupal_set_message(t('The map configuration has been created.'));
  } else {
    drupal_set_message(t('The map configuration has been updated.'));
    return 'admin/build/gmaps-map-config';
  }
  
}

/**
 * Present a form for deleting a map config.
 */
function _gmaps_map_config_delete_form($config) {

  $form = array();
  $form['mcid'] = array(
    '#type' => 'value',
    '#value' => $config->mcid,
  );

  $output = confirm_form($form,
    t('Are you sure you want to delete the map configuration?'),
    'admin/build/gmaps-map-config'
  );

  return $output;
}

/**
 * Delete a map configuration.
 */
function _gmaps_map_config_delete_form_submit($form_id, $form_values) {

  if ($form_values['mcid'] && $form_values['confirm']) {
    //TODO: hook: $op = 'delete';
    db_query('DELETE FROM {gmaps_map_config} WHERE mcid = %d', $form_values['mcid']);
    drupal_set_message(t('The map configuration has been deleted.'));
    return 'admin/build/gmaps-map-config';
  }
}


function _gmaps_builtin_icon_config_form($config) {
  $form = array();
  
  $form['name'] = array('#type' => 'textfield',
    '#title' => t('Icon name'),
    '#default_value' => $config->name,
    '#description' => t('The human-readable name of this icon <strong>in english</strong>. This name must be unique.'),
    '#required' => TRUE,
  );
  if (isset($config->icid)) {
    $form['icid'] = array('#type' => 'value', '#value' => $config->icid);
    $form['builtinid'] = array('#type' => 'hidden', '#value' => $config->builtinid);
  }
  else {
    include_once(drupal_get_path('module', 'gmaps') . '/includes/gmaps-builtin-icons.inc');
    $icons = _gmaps_builtin_icons();
    $options = $settings = $added = array();

    $result = db_query("SELECT builtinid FROM {gmaps_icon_config} WHERE type = 'builtin'");
    while($rec = db_fetch_object($result)) {
      $added[$rec->builtinid] = 1;
    }
  
    foreach($icons as $group => $items) {
      foreach($items as $key => $info) {
        if (!isset($added[$key])) {
          $options[$group][$key] = $info['name'];
          $settings[$key] = $info;
        } 
      }
    }
    drupal_add_js(array('gmapsBuiltinIcons' => $settings, 'gmapsBuiltinIconsBasePath' => base_path()), 'setting');
    drupal_add_js(drupal_get_path('module', 'gmaps') . '/misc/gmaps-builtin-icon-configurer.js');

    $form['builtinid'] = array('#type' => 'select',
      '#title' => t('Built-in icon'),
      '#options' => $options,
      '#default_value' => 0,
    );
  }
  $form['iconpreview'] = array('#type' => 'item',
    '#title' => t('Icon preview'),
    '#value' => theme('image', isset($config->icid) ? $config->image : $icons[0]['image'], '', '', array('id' => 'gmaps-builtin-icon-preview'), FALSE),
  );

  $form['type'] = array('#type' => 'value', '#value' => 'builtin');
  $form['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

  return $form;
}

function _gmaps_builtin_icon_config_form_validate($form_id, $form_values) {
  if (isset($form_values['icid'])) {
    $result = db_query("SELECT name FROM {gmaps_icon_config} WHERE icid != %d AND name = '%s'", $form_values['icid'], $form_values['name']);
  } else {
    $result = db_query("SELECT name FROM {gmaps_icon_config} WHERE name = '%s'", $form_values['name']);
  }
  if (db_num_rows($result)) {
    form_set_error('name', t('The name already taken by another icon configuration.'));
  }
}

function _gmaps_builtin_icon_config_form_submit($form_id, $form_values) {
  if (isset($form_values['icid'])) {
    $config = &$form_values;
  } else {
    $config = _gmaps_get_builtin_icon_info($form_values['builtinid']);
    $config['type'] = $form_values['type'];
    $config['name'] = $form_values['name'];
  }
  gmaps_icon_config_save($config);
  if (isset($form_values['icid'])) {
    drupal_set_message(t('The icon configuration has been updated.'));
    return 'admin/build/gmaps-icon-config';
  } else {
    drupal_set_message(t('The icon configuration has been created.'));
  }
}

function _gmaps_factory_icon_config_form($config) {
  $form = array();
  
  $base = drupal_get_path('module', 'gmaps');
  
  // Add Farbtastic color picker
  drupal_add_css('misc/farbtastic/farbtastic.css', 'module', 'all', FALSE);
  drupal_add_js('misc/farbtastic/farbtastic.js');

  drupal_add_js($base . '/misc/gmaps-factory-icon-configurer.js');
  drupal_add_css($base .'/misc/gmaps-factory-icon-color-form.css', 'module', 'all', FALSE);
  drupal_add_js(array('gmapsFactoryColors' => array(
    'reference' => array(
      'primarycolor' => $config->primarycolor,
      'strokecolor' => $config->strokecolor,
      'cornercolor' => $config->cornercolor,
    )
  )), 'setting');

  if (isset($config->icid)) {
    $form['icid'] = array('#type' => 'value', '#value' => $config->icid);
  }
  $form['name'] = array('#type' => 'textfield',
    '#title' => t('Icon name'),
    '#default_value' => $config->name,
    '#description' => t('The human-readable name of this icon <strong>in english</strong>. This name must be unique.'),
    '#required' => TRUE,
  );
  $form['icon_width'] = array('#type' => 'textfield',
    '#title' => t('Icon width'),
    '#default_value' => $config->icon_width,
    '#required' => TRUE,
    '#maxlength' => 4,
    '#size' => 6,
    '#field_suffix' => 'px',
    '#attributes' => array('class' => 'gmaps-factory-icon-size-field'),
  );
  $form['icon_height'] = array('#type' => 'textfield',
    '#title' => t('Icon height'),
    '#default_value' => $config->icon_height,
    '#required' => TRUE,
    '#maxlength' => 4,
    '#size' => 6,
    '#field_suffix' => 'px',
    '#attributes' => array('class' => 'gmaps-factory-icon-size-field'),
  );
  //color form
  $form['color'] = array(
    '#type' => 'fieldset',
    '#title' => t('Colors'),
    '#attributes' => array('id' => 'gmaps_factory_icon_color_form'),
    '#theme' => 'gmaps_factory_icon_color_form',
  );
  $form['color']['primarycolor'] = array(
    '#type' => 'textfield',
    '#title' => t('Primary color'),
    '#default_value' => $config->primarycolor,
    '#size' => 8,
    '#required' => TRUE,
  );
  $form['color']['strokecolor'] = array(
    '#type' => 'textfield',
    '#title' => t('Stroke color'),
    '#default_value' => $config->strokecolor,
    '#size' => 8,
    '#required' => TRUE,
  );
  $form['color']['cornercolor'] = array(
    '#type' => 'textfield',
    '#title' => t('Corner color'),
    '#default_value' => $config->cornercolor,
    '#size' => 8,
    '#required' => TRUE,
  );
  $form['color']['factorypreview'] = array('#type' => 'item',
    '#title' => t('Preview'),
    '#value' => theme('image', $config->image, '', '', array('id' => 'gmaps-factory-icon-preview'), FALSE),
  );

  $form['type'] = array('#type' => 'value', '#value' => 'factory');
  $form['submit'] = array('#type' => 'submit', '#value' => t('Submit'));

  return $form;
}

/**
 * Present a form for deleting an icon config.
 */
function _gmaps_icon_config_delete_form($config) {

  $form = array();
  $form['icid'] = array(
    '#type' => 'value',
    '#value' => $config->icid,
  );
  $form['type'] = array(
    '#type' => 'value',
    '#value' => $config->type,
  );
  if ($config->type == 'builtin') {
    $form['delete_files'] = array(
      '#type' => 'value',
      '#value' => 0,
    );
  } else {
    $form['delete_files'] = array(
      '#type' => 'checkbox',
      '#title' => t('Delete icon files'),
      '#default_value' => 1,
    );
  }

  $output = confirm_form($form,
    t('Are you sure you want to delete the icon configuration?'),
    'admin/build/gmaps-icon-config'
  );

  return $output;
}

/**
 * Delete an icon configuration.
 */
function _gmaps_icon_config_delete_form_submit($form_id, $form_values) {

  if ($form_values['icid'] && $form_values['confirm']) {
    //TODO: hook: $op = 'delete';
    db_query('DELETE FROM {gmaps_icon_config} WHERE icid = %d', $form_values['icid']);
    if ($form_values['type'] != 'builtin' && $form_values['delete_files']) {
      $dir = variable_get('gmaps_icon_path', 'gmaps_icons');
      $mask = $form_values['icid'] .'-.+\..+';
      file_scan_directory(file_create_path($dir), $mask, array('.', '..', 'CVS'), 'file_delete');
    }
    drupal_set_message(t('The icon configuration has been deleted.'));
    return 'admin/build/gmaps-icon-config';
  }
}


/**
 * Theme gmaps factory icon color form.
 */
function theme_gmaps_factory_icon_color_form($form) {
  // Wrapper
  $output .= '<div class="color-form clear-block">';

  // Palette
  $output .= '<div id="palette" class="clear-block">';
  $output .= drupal_render($form['primarycolor']);
  $output .= drupal_render($form['strokecolor']);
  $output .= drupal_render($form['cornercolor']);
  $output .= drupal_render($form['factorypreview']);
  $output .= '</div>';

  // Preview
  $output .= drupal_render($form);

  // Close wrapper
  $output .= '</div>';

  return $output;
}

function _gmaps_factory_icon_config_form_validate($form_id, $form_values) {
  if (isset($form_values['icid'])) {
    $result = db_query("SELECT name FROM {gmaps_icon_config} WHERE icid != %d AND name = '%s'", $form_values['icid'], $form_values['name']);
  } else {
    $result = db_query("SELECT name FROM {gmaps_icon_config} WHERE name = '%s'", $form_values['name']);
  }
  if (db_num_rows($result)) {
    form_set_error('name', t('The name already taken by another icon configuration.'));
  }
  if (!is_numeric($form_values['icon_width']) || $form_values['icon_width'] < 1) {
    form_set_error('icon_width', t('The icon width must be an integer greater than zero.'));
  }
  if (!is_numeric($form_values['icon_height']) || $form_values['icon_height'] < 1) {
    form_set_error('icon_height', t('The icon height must be an integer greater than zero.'));
  }
  if (!preg_match('|#[0-9a-z]|', $form_values['primarycolor']) || strlen($form_values['primarycolor']) != 7) {
    form_set_error('primarycolor', t('The primary color must be given as a hexadecimal RGB value prepended by a hashmark.'));
  }
  if (!preg_match('|#[0-9a-z]|', $form_values['strokecolor']) || strlen($form_values['strokecolor']) != 7) {
    form_set_error('strokecolor', t('The stroke color must be given as a hexadecimal RGB value prepended by a hashmark.'));
  }
  if (!preg_match('|#[0-9a-z]|', $form_values['cornercolor']) || strlen($form_values['cornercolor']) != 7) {
    form_set_error('cornercolor', t('The corner color must be given as a hexadecimal RGB value prepended by a hashmark.'));
  }
}

function _gmaps_factory_icon_config_form_submit($form_id, $form_values) {
  gmaps_icon_config_save($form_values);
  if (isset($form_values['icid'])) {
    drupal_set_message(t('The icon configuration has been updated.'));
    return 'admin/build/gmaps-icon-config';
  } else {
    drupal_set_message(t('The icon configuration has been created.'));
  }
}
