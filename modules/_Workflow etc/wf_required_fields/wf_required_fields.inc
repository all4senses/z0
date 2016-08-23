<?php

// $Id: wf_required_fields.inc,v 1.1.2.2 2008/01/03 18:24:38 traxer Exp $

/**
 * Retrieves applicable content types.
 * 
 * Content types can be used with this module if they come from cck and have a
 * workflow configured. 
 *
 * @return array Array of content type names, indexed by content type
 */
function wf_required_fields_get_types_applicable() {
  static $cache = null;
  if ($cache === null) {
    $cache = array();
    $types = content_types();
    foreach ($types as $key => $value) {
      if (workflow_get_workflow_for_type($key) !== false) {
        $cache[$key] = $value['name'];
      }
    }
  }
  return $cache;
}

/**
 * Retrieves the configured content types.
 * 
 * @return array Array of content types, indexed by content type
 */
function wf_required_fields_get_types_configured() {
    $settings = variable_get(
    	'wf_required_fields',
      array('types' => array('config' => array()))
    );
    return $settings['types']['config'];
}

/**
 * Checks whether a certain field is configured to be required in a node of a
 * certain content type in a certain state.
 * 
 * @param string $type The content type to check
 * @param string $field The name of the field to check
 * @param int $sid The sid of the state to check
 * @return bool true: field is required; false otherwise
 */
function wf_required_fields_is_required($type, $field, $sid) {
  $settings = variable_get( 'wf_required_fields', array() );
  if (! isset($settings['settings'][$type]['table'][$field][$sid]['required'])) {
    return false;
  }
  return $settings['settings'][$type]['table'][$field][$sid]['required'];
}

/**
 * Finds the array that has a '#required' key inside a Forms API array that has
 * $field as distant parent.
 *
 * @param array $array The Forms API array
 * @param string $field
 * @return array The array that has a '#required' key as distant parent; null
 *  if none found
 */
function &wf_required_fields_find_required(&$array, $field) {
  if (! is_array($array)) {
    return null;
  }
  if (array_key_exists($field, $array)) {
    return wf_required_fields_get_if_recursive_subkey($array[$field], '#required');
  }
  foreach (array_keys($array) as $key) {
    $child =& wf_required_fields_find_required($array[$key], $field);
    if ($child !== null) {
      return $child;
    }
  }
  return null;
}

/**
 * Looks for a $key in a given &$array recursively.
 *
 * @param array $array The array to search
 * @param string $key The key
 * @return array The array that has the $key; null if none found
 */
function &wf_required_fields_get_if_recursive_subkey(&$array, $key) {
  if (! is_array($array)) {
    return null;
  }
  if (array_key_exists($key, $array)) {
    return $array;
  }
  foreach (array_keys($array) as $i) {
    if (is_array($array[$i])) {
      $child =& wf_required_fields_get_if_recursive_subkey($array[$i], $key);
      if ($child !== null) {
        return $child;
      }
    }
  }
  return null;
}

/**
 * Check which fields are not filled out properly for the specified node to be
 * promoted to the specified state.
 * 
 * If the type of the node is not configured to be handled by this module,
 * this function produces errors at random.   
 *
 * @param stdClass $node The node to check
 * @param int $sid The state to check
 * @return array Array of field names (indexed by field type)
 */
function wf_required_fields_get_missing_fields($node, $sid) {
  $non_empty = array('0', 0);
  $missing = array();
  $type = $node->type;
  $all_fields = content_fields();
  foreach ($all_fields as $key => $value) {
    if (isset($node->$key)) {
      $fields[$key] = $value;
    }
  }
  foreach (array_keys($fields) as $field_name) {
    $info = content_database_info($fields[$field_name]);
    foreach (array_keys($info['columns']) as $column) {
      $field = $node->$field_name;
      if (wf_required_fields_is_required($type, $field_name, $sid)) {
        $value = $field[0][$column];
        if (!in_array($value, $non_empty, true) && empty($value)) {
          $missing[$field_name] = $all_fields[$field_name]['widget']['label'];
        }
      }
    }
  }
  return $missing;
}

/**
 * Sets the given Forms API field to required
 *
 * @param array $array The Forms API element
 */
function wf_required_fields_set_required(&$element) {
  $element['#required'] = true;
  switch ($element['#type']) {
    case 'select':
      $index = array_search(t('<none>'), $element['#options']);
      if ($index !== false) {
        unset ($element['#options'][$index]);
      }
      break;

    case 'radios':
        unset ($element['#options']['']);
  }
}