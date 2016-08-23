<?php
// $Id: gmaps-content-field-tokens.inc,v 1.2 2008/12/13 19:47:56 xmarket Exp $

/**
 * Implementation of hook_token_list()
 */
function gmaps_content_token_list($type = 'all') {
  $tokens = $address_tokens = $point_tokens = $marker_tokens = array();
  if ($type == 'node' || $type == 'all') {

    $address_tokens['country-code'] = t('The address of the node: the ISO 3166 alpha 2 code of the country.');
    $address_tokens['country'] = t('The address of the node: the name of the country.');
    $address_tokens['adminarea'] = t('The address of the node: administrative area.');
    $address_tokens['adminarea-raw'] = t('The address of the node: Unfiltered administrative area. WARNING - raw user input.');
    $address_tokens['subadminarea'] = t('The address of the node: sub-administrative area.');
    $address_tokens['subadminarea-raw'] = t('The address of the node: Unfiltered sub-administrative area. WARNING - raw user input.');
    $address_tokens['locality'] = t('The address of the node: locality.');
    $address_tokens['locality-raw'] = t('The address of the node: Unfiltered locality. WARNING - raw user input.');
    $address_tokens['deplocality'] = t('The address of the node: dependent locality.');
    $address_tokens['deplocality-raw'] = t('The address of the node: Unfiltered dependent locality. WARNING - raw user input.');
    $address_tokens['thoroughfare'] = t('The address of the node: thoroughfare.');
    $address_tokens['thoroughfare-raw'] = t('The address of the node: Unfiltered thoroughfare. WARNING - raw user input.');
    $address_tokens['postalcode'] = t('The address of the node: postalcode.');
    $address_tokens['postalcode-raw'] = t('The address of the node: Unfiltered postalcode. WARNING - raw user input.');
    $address_tokens['address'] = t('The address of the node: full address.');
    $address_tokens['address-links'] = t('The address of the node: full address with taxonomy links.'); 
    //TODO: add last, nth support
    //$address_tokens['address-last'] = t('The address of the node: full address.'); 

    $point_tokens['latitude'] = t('The coordinates of the node: latitude.');
    $point_tokens['latitude-raw'] = t('The coordinates of the node: Unfiltered latitude. WARNING - raw user input.');
    $point_tokens['longitude'] = t('The coordinates of the node: longitude.');
    $point_tokens['longitude-raw'] = t('The coordinates of the node: Unfiltered longitude. WARNING - raw user input.');
    $point_tokens['x'] = t('The coordinates of the node: Cartesian X coordinate.');
    $point_tokens['y'] = t('The coordinates of the node: Cartesian Y coordinate.');
    $point_tokens['z'] = t('The coordinates of the node: Cartesian Z coordinate.');
    $point_tokens['coords'] = t('The coordinates of the node: Plain coordinates.');
    $point_tokens['coords-formatted'] = t('The coordinates of the node: Formatted coordinates.');
    $point_tokens['coords-cartesian'] = t('The coordinates of the node: Cartesian coordinates.');

    //TODO: implement marker token_values.......
    /*$marker_tokens['marker-title'] = t('The coordinates of the node: '); 
    $marker_tokens['marker-title-raw'] = t('The coordinates of the node: '); 
    $marker_tokens['marker-title-content'] = t('The coordinates of the node: '); 
    $marker_tokens['marker-url'] = t('The coordinates of the node: ');*/ 

    foreach (content_fields() as $field) {
      switch($field['type']) {
        case 'gmaps_address':
          foreach($address_tokens as $key => $desc) {
            $tokens['GMaps address field'][$field['field_name'] .'-'. $key] = $desc;
          }
          break;
        case 'gmaps_point':
          foreach($point_tokens as $key => $desc) {
            $tokens['GMaps point field'][$field['field_name'] .'-'. $key] = $desc;
          }
          break;
        case 'gmaps_marker':
          foreach($point_tokens as $key => $desc) {
            $tokens['GMaps marker field'][$field['field_name'] .'-'. $key] = $desc;
          }
          break;
        case 'gmaps_gaddress':
          foreach($address_tokens as $key => $desc) {
            $tokens['GMaps geocoded address field'][$field['field_name'] .'-'. $key] = $desc;
          }
          foreach($point_tokens as $key => $desc) {
            $tokens['GMaps geocoded address field'][$field['field_name'] .'-'. $key] = $desc;
          }
          break;
        case 'gmaps_gaddress_marker':
          foreach($address_tokens as $key => $desc) {
            $tokens['GMaps geocoded address marker field'][$field['field_name'] .'-'. $key] = $desc;
          }
          foreach($point_tokens as $key => $desc) {
            $tokens['GMaps geocoded address marker field'][$field['field_name'] .'-'. $key] = $desc;
          }
          break;
      }
    }

  }
  return $tokens;
}

/**
 * Implementation of hook_token_values()
 */
function gmaps_content_token_values($type, $node = NULL, $options = array()) {
  $tokens = array();
  if ($type == 'node' && !is_null($node)) {
    $type = content_types($node->type);
    foreach($type['fields'] as $field) {
      if ($field['type'] == 'gmaps_address') {
        if (isset($node->$field['field_name']) && is_array($node->$field['field_name']) && count($node->$field['field_name'])) {
          $item = array_shift($node->$field['field_name']);
          if ($item['address_privacy']) {
            _gmaps_content_apply_address_privacy($item['address'], $item['address_privacy']);
          }
          $tokens = array_merge($tokens, _gmaps_content_address_token_values($item, $field['field_name'] .'-', '', $node));
        }
      }
      if (in_array($field['type'], array('gmaps_point', 'gmaps_marker'))) {
        if (isset($node->$field['field_name']) && is_array($node->$field['field_name']) && count($node->$field['field_name'])) {
          $item = array_shift($node->$field['field_name']);
          if ($item['point_privacy']) {
            _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);
          }
          $tokens = array_merge($tokens, _gmaps_content_point_token_values($item, $field['field_name'] .'-', ''));
        }
      }
      if (in_array($field['type'], array('gmaps_gaddress', 'gmaps_gaddress_marker'))) {
        if (isset($node->$field['field_name']) && is_array($node->$field['field_name']) && count($node->$field['field_name'])) {
          $item = array_shift($node->$field['field_name']);
          $item = $item['gaddress'];
          if ($item['address_privacy']) {
            _gmaps_content_apply_address_privacy($item['address'], $item['address_privacy']);
          }
          $tokens = array_merge($tokens, _gmaps_content_address_token_values($item, $field['field_name'] .'-', '', $node));
          if ($item['point_privacy']) {
            _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);
          }
          $tokens = array_merge($tokens, _gmaps_content_point_token_values($item, $field['field_name'] .'-', ''));
        }
      }
    }
  }
  return $tokens;
}

function _gmaps_content_address_token_values($item, $prefix = '', $suffix = '', $node = NULL) {
  static $countries = NULL;
  $values = array();

  if (is_null($countries)) {
    $countries = _gmaps_country_list();
  }

  $values[$prefix .'country-code'. $suffix] = $item['address']['country'];
  $values[$prefix .'country'. $suffix] = $countries[$item['address']['country']];
  if (!empty($item['address']['adminarea'])) {
    $values[$prefix .'adminarea'. $suffix] = check_plain($item['address']['adminarea']);
    $values[$prefix .'adminarea-raw'. $suffix] = $item['address']['adminarea'];
  } else {
    $values[$prefix .'adminarea'. $suffix] = '';
    $values[$prefix .'adminarea-raw'. $suffix] = '';
  }
  if (!empty($item['address']['subadminarea'])) {
    $values[$prefix .'subadminarea'. $suffix] = check_plain($item['address']['subadminarea']);
    $values[$prefix .'subadminarea-raw'. $suffix] = $item['address']['subadminarea'];
  } else {
    $values[$prefix .'subadminarea'. $suffix] = '';
    $values[$prefix .'subadminarea-raw'. $suffix] = '';
  }
  if (!empty($item['address']['locality'])) {
    $values[$prefix .'locality'. $suffix] = check_plain($item['address']['locality']);
    $values[$prefix .'locality-raw'. $suffix] = $item['address']['locality'];
  } else {
    $values[$prefix .'locality'. $suffix] = '';
    $values[$prefix .'locality-raw'. $suffix] = '';
  }
  if (!empty($item['address']['deplocality'])) {
    $values[$prefix .'deplocality'. $suffix] = check_plain($item['address']['deplocality']);
    $values[$prefix .'deplocality-raw'. $suffix] = $item['address']['deplocality'];
  } else {
    $values[$prefix .'deplocality'. $suffix] = '';
    $values[$prefix .'deplocality-raw'. $suffix] = '';
  }
  if (!empty($item['address']['thoroughfare'])) {
    $values[$prefix .'thoroughfare'. $suffix] = check_plain($item['address']['thoroughfare']);
    $values[$prefix .'thoroughfare-raw'. $suffix] = $item['address']['thoroughfare'];
  } else {
    $values[$prefix .'thoroughfare'. $suffix] = '';
    $values[$prefix .'thoroughfare-raw'. $suffix] = '';
  }
  if (!empty($item['address']['postalcode'])) {
    $values[$prefix .'postalcode'. $suffix] = check_plain($item['address']['postalcode']);
    $values[$prefix .'postalcode-raw'. $suffix] = $item['address']['postalcode'];
  } else {
    $values[$prefix .'postalcode'. $suffix] = '';
    $values[$prefix .'postalcode-raw'. $suffix] = '';
  }

  $values[$prefix .'address'. $suffix] = _gmaps_content_address_formatter($item['address'], NULL);

  if (!is_null($node)) {
    $values[$prefix .'address-links'. $suffix] = _gmaps_content_address_formatter($item['address'], $node);
  } else {
    $values[$prefix .'address-links'. $suffix] = $values[$prefix .'address'. $suffix];
  }

  return $values;
}

function _gmaps_content_point_token_values($item, $prefix = '', $suffix = '') {
  $values = array();

  $values[$prefix .'latitude'. $suffix] = check_plain($item['coordinates']['latitude']);
  $values[$prefix .'latitude-raw'. $suffix] = $item['coordinates']['latitude'];
  $values[$prefix .'longitude'. $suffix] = check_plain($item['coordinates']['longitude']);
  $values[$prefix .'longitude-raw'. $suffix] = $item['coordinates']['longitude'];

  $values[$prefix .'x'. $suffix] = $item['coordinates']['x'];
  $values[$prefix .'y'. $suffix] = $item['coordinates']['y'];
  $values[$prefix .'z'. $suffix] = $item['coordinates']['z'];

  $values[$prefix .'coords'. $suffix] = _gmaps_content_coordinate_formatter($item['coordinates'], 'plain');
  $values[$prefix .'coords-formatted'. $suffix] = _gmaps_content_coordinate_formatter($item['coordinates'], 'formatted');
  $values[$prefix .'coords-cartesian'. $suffix] = _gmaps_content_coordinate_formatter($item['coordinates'], 'cartesian');

  return $values;
}
