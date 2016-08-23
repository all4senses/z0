<?php
// $Id: gmaps-content-field-views.inc,v 1.3 2009/01/13 23:25:04 xmarket Exp $

function gmaps_content_field_views_table($field) {
  $field_types = _content_field_types();

  $table['name'] = $field['type'] == 'gmaps_address' ? 'gmaps_addresses' : 'gmaps_points';
  $table['join'] = array(
    'left' => array(
      'table' => 'node',
      'field' => 'vid',
    ),
    'right' => array(
      'field' => 'vid',
    ),
    'extra' => array(
      "field_name = '". db_escape_string($field['field_name']) ."'" => NULL,
    ),
  );

  $formatters = array();
  if (is_array($field_types[$field['type']]['formatters'])) {
    foreach ($field_types[$field['type']]['formatters'] as $name => $info) {
      $formatters[$name] = t($info['label']);
    }
  }
  
  $table['fields'] = array();
  $table['fields'][$field['type']] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
    'field' => 'delta',
    'sortable' => $field['multiple'],
    'query_handler' => 'gmaps_content_views_query_handler',
    'sort_handler' => 'gmaps_content_field_views_sort_handler',
    'validate' => 'gmaps_content_field_views_field_validator',
    'handler' => array(
      'gmaps_content_field_views_field_handler_first' => t('Show first value only'),
      'gmaps_content_field_views_field_handler_last' => t('Show last value only'),
      'gmaps_content_field_views_field_handler_group' => t('Group multiple values'),
      'gmaps_content_field_views_field_handler_ungroup' => t('Do not group multiple values'),
    ),
    'option' => array('#type' => 'select', '#options' => $formatters),
    'help' => t('Multiple, ungroupped values can be table sorted on their creation order.'),
    'gmaps_content_field' => $field,
  );

  return array($field['type'] .'_'. $field['field_name'] => $table);
}

function gmaps_content_views_query_handler($field, &$fieldinfo, &$query) {
  if (array_pop(explode('_', $field['handler'])) != 'ungroup') {
    $query->ensure_table($field['tablename']);
    if ($fieldinfo['gmaps_content_field']['multiple']) {
      $fieldinfo['notafield'] = TRUE;
    }
  }
  $query->add_field('vid', 'node');
  $query->add_field('type', 'node');
}

function gmaps_content_field_views_field_validator($fielddata, $view, $form) {
  foreach ($form['field'] as $key => $field_data) {
    if (is_numeric($key) && in_array($field_data['field']['#value'], array('gmaps_point', 'gmaps_marker', 'gmaps_gaddress', 'gmaps_gaddress_marker'))) {
      $op = &$field_data['options']['#value'];
      if (strpos($op, '_map') !== FALSE && $field_data['handler']['#value'] == 'gmaps_content_field_views_field_handler_ungroup') {
        form_error($form['field'][$key]['handler'], t('Map formatters can be used only on groupped values.'));
      } else if (strpos($op, '_sm') !== FALSE && $field_data['handler']['#value'] == 'gmaps_content_field_views_field_handler_ungroup') {
        form_error($form['field'][$key]['handler'], t('Static map formatters must be handled by the "Group multiple values" handler.'));
      }
    }
  }
}

function gmaps_content_field_views_sort_handler($fielddata, $fieldinfo) {
  return $fielddata['tablename'] .'.delta';  
}

function gmaps_content_field_views_field_handler_group($field_info, $field_data, $value, $data, $from = 0, $count = 'all', $order = 'ASC') {
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];
    if ($field['multiple']) {
      if ($field['type'] == 'gmaps_address') {
        $items = _gmaps_content_address_field_load($data, $field, $from, $count, $order);
      } else {
        $items = _gmaps_content_point_field_load($data, $field, $from, $count, $order);
      }

      if (!empty($items)) {
        $items = ($count == 1) ? array($items) : $items;
        //if (strpos($field_data['options'], '_sm') !== FALSE || strpos($field_data['options'], '_map') !== FALSE) {
          $data->$field['field_name'] = $items;
          $type = content_types($data->type);
          $field = $type['fields'][$field['field_name']];
        //}
        foreach($items as $delta => $item) {
          $fields[] = content_format($field, $item, $field_data['options'], $data);
          if (isset($type)) {
            break;
          }
        }
        return theme('content_view_multiple_field', $fields, $field, $data);
      }
    }
    else {
      return gmaps_content_field_views_field_handler_ungroup($field_info, $field_data, 0, $data);
    }
  }
  return '';
}

function gmaps_content_field_views_field_handler_first($field_info, $field_data, $value, $data) {
  return gmaps_content_field_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'ASC');
}

function gmaps_content_field_views_field_handler_last($field_info, $field_data, $value, $data) {
  return gmaps_content_field_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'DESC');
}

function gmaps_content_field_views_field_handler_ungroup($field_info, $field_data, $value, $data) {
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];
    
    if ($field['type'] == 'gmaps_address') {
      $item = _gmaps_content_address_field_load($data, $field, $value, 1);
    } else {
      $item = _gmaps_content_point_field_load($data, $field, $value, 1);
    }

    if (!empty($item)) {
      //if (strpos($field_data['options'], '_sm') !== FALSE || strpos($field_data['options'], '_map') !== FALSE) {
        $data->$field['field_name'] = array($item);
        $type = content_types($data->type);
        $field = $type['fields'][$field['field_name']];
      //}
      return content_format($field, $item, $field_data['options'], $data);
    }
  }

  return ''; 
}

function gmaps_content_address_views_table($field) {
  $field_types = _content_field_types();

  $table = array();
  $left_table = $field['type'] .'_'. $field['field_name'];
  $right_table = 'gmaps_a_'. $field['field_name'];
  if ($field['type'] != 'gmaps_address') {
    $table['name'] = 'gmaps_addresses';
    $table['join'] = array(
      'left' => array(
        'table' => $field['type'] .'_'. $field['field_name'],
        'field' => 'vid',
      ),
      'right' => array(
        'field' => 'vid',
      ),
      'extra' => array(
        "field_name = $left_table.field_name AND $left_table.delta = $right_table.delta" => NULL,
      ),
    );
  }

  $table['fields'] = array();
  $table['fields']['country']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Country';
  $table['fields']['adminarea']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Admin. area';
  $table['fields']['subadminarea']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Sub-admin. area';
  $table['fields']['locality']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Locality';
  $table['fields']['delocality']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Dep. locality';
  $table['fields']['thoroughfare']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Thoroughfare';
  $table['fields']['postalcode']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Postal code';
  $table['fields']['privacy']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Address privacy';
  $table['fields']['directions']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions links';
  $table['fields']['directions_from']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions from link';
  $table['fields']['directions_to']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions to link';

  foreach ($table['fields'] as $detail => $field_info) {
    $table['fields'][$detail] = array_merge($field_info, array(
      'field' => 'delta',
      'sortable' => strpos($detail, 'directions') === FALSE,
      'query_handler' => 'gmaps_content_views_query_handler',
      'handler' => array(
        'gmaps_content_address_views_field_handler_group' => t('Group multiple values'),
        'gmaps_content_address_views_field_handler_ungroup' => t('Do not group multiple values'),
        'gmaps_content_address_views_field_handler_first' => t('Show first value only'),
        'gmaps_content_address_views_field_handler_last' => t('Show last value only'),
      ),
      //TODO: add link/no link, 'option' => array('#type' => 'select', '#options' => $formatters),
      'gmaps_content_field' => $field,
    ));
  }
  $table['fields']['directions_from']['option'] = $table['fields']['directions_to']['option'] = 'string';

  $table['sorts'] = array();
  $table['sorts']['address'] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - All fields',
    'handler' => 'gmaps_content_address_views_sort_handler',
    'option' => array(
      '#type' => 'select',
      '#options' => array_reverse(_gmaps_content_address_privacy_levels()),
    ),
  );

  $table['filters'] = array();

  foreach ($table['fields'] as $detail => $field_info) {
    $table['sorts'][$detail] = array(
      'name' => $field_info['name'],
    );
    $table['filters'][$detail] = array(
      'name' => $field_info['name'],
      'operator' => 'views_handler_operator_like',
      'gmaps_content_field' => $field,
      'help' => t('Text field filter for address detail field.'),
    );
  }
  
  $table['filters']['country'] = array(
    'name' => $table['fields']['country']['name'],
    'operator' => 'views_handler_operator_andor',
    'value' => array(
      '#type' => 'select',
      '#options' => '_gmaps_content_filter_countries',
      '#multiple' => TRUE,
    ),
    'gmaps_content_field' => $field,
  );
  $table['filters']['privacy'] = array(
    'name' => $table['fields']['privacy']['name'],
    'operator' => 'views_handler_operator_gtlt',
    'value' => array(
      '#type' => 'select',
      '#options' => array_reverse(_gmaps_content_address_privacy_levels()),
    ),
    'gmaps_content_field' => $field,
  );
  $table['filters']['city_in_country'] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - City in country',
    'operator' => 'views_handler_operator_like',
    'value' => array(
      'city' => array(
        '#type' => 'textfield',
      ),
      'country' => array(
        '#type' => 'select',
        '#options' => '_gmaps_content_filter_countries',
      ),
      '#tree' => true,
      '#type' => 'gmaps_content_city_in_country_views_filter_value',
      '#process' => array('_gmaps_content_city_in_country_views_filter_value_process' => array($field)),
      '#after_build' => array('_gmaps_content_city_in_country_views_filter_value_after_build'),
    ),
    'handler' => 'gmaps_content_city_in_country_views_filter_handler',
    'gmaps_content_field' => $field,
  );
  $table['filters']['address_field'] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Address - Field',
    'operator' => 'views_handler_operator_like',
    'value',
    'handler' => 'gmaps_content_address_field_views_filter_handler',
  );
  $table['filters']['address_address'] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Address - Address',
    'operator' => 'views_handler_operator_like',
    'value' => array(
      '#type' => 'address',
      '#address_allowed_countries' => '_gmaps_content_filter_countries',
      '#process' => array('_gmaps_content_address_address_views_filter_value_process' => array($field)),
      '#after_build' => array('_gmaps_content_address_address_views_filter_value_after_build'),
    ),
    'handler' => 'gmaps_content_address_address_views_filter_handler',
  );

  return array(($field['type'] == 'gmaps_address' ? $left_table : $right_table) => $table);
}

function gmaps_content_address_views_field_handler_group($field_info, $field_data, $value, $data, $from = 0, $count = 'all', $order = 'ASC') {
  static $countries = array();
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];
    if ($field['multiple']) {
      if (empty($countries)) {
        $countries = _gmaps_country_list();
      }

      $items = _gmaps_content_address_field_load($data, $field, $from, $count, $order);

      if (!empty($items)) {
        if ($field_data['field'] == 'privacy') {
          $privacy_levels = _gmaps_content_address_privacy_levels();
          foreach($items as $delta => $item) {
            if (isset($privacy_levels[$item['address_privacy']])) {
              $fields[] = $privacy_levels[$item['address_privacy']];
            }
          }
        }
        else if (strpos($field_data['field'], 'directions') !== FALSE) {
          foreach($items as $delta => $item) {
            $from = gmaps_maps_google_com_directions_url($item['address']);
            $to = gmaps_maps_google_com_directions_url($item['address'], TRUE);
            if (strpos($field_data['field'], '_from') !== FALSE) {
              $fields[] = l(!empty($field_data['options']) ? $field_data['options'] : t('From here'), $from);
            }
            else if (strpos($field_data['field'], '_to') !== FALSE) {
              $fields[] = l(!empty($field_data['options']) ? $field_data['options'] : t('To here'), $to);
            }
            else {
              $fields[] = theme('gmaps_directions_links', $from, $to);
            }
          }
        }
        else {
          foreach($items as $delta => $item) {
            _gmaps_content_apply_address_privacy($item['address'], $item['address_privacy']);
            if (isset($item['address'][$field_data['field']]) && !empty($item['address'][$field_data['field']])) {
              if ($field_data['field'] == 'country') {
                $fields[] = $countries[$item['address'][$field_data['field']]];
              } else {
                $fields[] = check_plain($item['address'][$field_data['field']]);
              }
            }
          }
        }
        if (!empty($fields)) {
          return theme('content_view_multiple_field', $fields, $field, $data);
        }
      }
    }
    else {
      return gmaps_content_address_views_field_handler_ungroup($field_info, $field_data, 0, $data);
    }
  }
  return '';
}

function gmaps_content_address_views_field_handler_first($field_info, $field_data, $value, $data) {
  return gmaps_content_address_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'ASC');
}

function gmaps_content_address_views_field_handler_last($field_info, $field_data, $value, $data) {
  return gmaps_content_address_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'DESC');
}

function gmaps_content_address_views_field_handler_ungroup($field_info, $field_data, $value, $data) {
  static $addresses = array();
  static $countries = array();
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];
    if (empty($countries)) {
      $countries = _gmaps_country_list();
    }

    $item = _gmaps_content_address_field_load($data, $field, $value, 1);

    if ($field_data['field'] == 'privacy') {
      $privacy_levels = _gmaps_content_address_privacy_levels();
      if (isset($privacy_levels[$item['address_privacy']])) {
        return $privacy_levels[$item['address_privacy']];
      }
    }
    else if (strpos($field_data['field'], 'directions') !== FALSE) {
      $from = gmaps_maps_google_com_directions_url($item['address']);
      $to = gmaps_maps_google_com_directions_url($item['address'], TRUE);
      if (strpos($field_data['field'], '_from') !== FALSE) {
        return l(!empty($field_data['options']) ? $field_data['options'] : t('From here'), $from);
      }
      else if (strpos($field_data['field'], '_to') !== FALSE) {
        return l(!empty($field_data['options']) ? $field_data['options'] : t('To here'), $to);
      }
      else {
        return theme('gmaps_directions_links', $from, $to);
      }
    }
    else {
      _gmaps_content_apply_address_privacy($item['address'], $item['address_privacy']);
      if (isset($item['address'][$field_data['field']]) && !empty($item['address'][$field_data['field']])) {
        if ($field_data['field'] == 'country') {
          return $countries[$item['address'][$field_data['field']]];
        } else {
          return check_plain($item['address'][$field_data['field']]);
        }
      }
    }
  }

  return ''; 
}

function gmaps_content_address_views_sort_handler($op, &$query, $sortinfo, $sort) {
  $map = _gmaps_content_get_address_detail_fields();

  $fields = array();
  foreach($map as $key => $field) {
    if ($key <= $sort['options']) {
      $fields[] = $field;
    }
  }
  $query->add_orderby($sortinfo['table'], $fields, $sort['sortorder']);
}

function _gmaps_content_filter_countries($op, $filterinfo) {
  $result = db_query("SELECT DISTINCT country FROM {gmaps_addresses} a WHERE field_name = '%s'", $filterinfo['gmaps_content_field']['field_name']);
  $country_names = _gmaps_country_list();
  $countries = array();
  while ($item = db_fetch_array($result)) {
    $countries[$item['country']] = $country_names[$item['country']];
  }
  if (empty($countries)) {
    $countries[''] = t('<None>');
  } else {
    asort($countries);
  }
  return $countries;
}

function _gmaps_content_city_in_country_views_filter_value_process($element) {
  $args = func_get_args();
  $info['gmaps_content_field'] = array_pop($args);
  if (!empty($element['#default_value'])) {
    if (!is_array($element['#default_value'])) {
      $values = unserialize($element['#default_value']);
      if (empty($values)) {
        list($values['city'], $values['country']) = explode(',', $element['#default_value']);
      }
      $element['city']['#default_value'] = $values['city'];
      $element['country']['#default_value'] = $values['country'];
    } else {
      $element['city']['#default_value'] = $element['#default_value']['city'];
      $element['country']['#default_value'] = $element['#default_value']['country'];
    }
  }

  $element['country']['#options'] = $element['country']['#options']('', $info);
  return $element;
}

function _gmaps_content_city_in_country_views_filter_value_after_build($element) {
  $values = array();
  $values['city'] = $element['city']['#value'];
  $values['country'] = $element['country']['#value'];
  $element['#value'] = serialize($values);
  form_set_value($element, $element['#value']);
  return $element;
}

function _gmaps_content_address_address_views_filter_value_process($element) {
  $args = func_get_args();
  $info['gmaps_content_field'] = array_pop($args);
  if (!empty($element ['#default_value']) && !is_array($element['#default_value'])) {
      $element['#default_value'] = unserialize($element['#default_value']);
  }
  $element['#address_allowed_countries'] = array_keys($element['#address_allowed_countries']('', $info));
  if (!empty($element ['#value']) && !is_array($element['#value'])) {
      $element['#value'] = unserialize($element['#value']);
  }
  return _gmaps_address_expand($element);
}

function _gmaps_content_address_address_views_filter_value_after_build($element) {
  if (is_array($element['#value'])) {
    $element['#value'] = serialize($element['#value']);
    form_set_value($element, $element['#value']);
  }
  return $element;
}

function gmaps_content_city_in_country_views_filter_handler($op, $filter, $filterinfo, &$query) {
  if (!is_array($filter['value'])) {
    $filter['value'] = unserialize($filter['value']);
  }
  switch (trim($filter['value']['city'])) {
    case (''):
      return;
      break;
  }
  switch ($op) {
    case 'handler':
      $table = $filterinfo['table'];
      $query->ensure_table($table);
      $field = "$table.locality";
      $all_values = array($filter['value']['country']);

      list ($where, $values) = _gmaps_content_views_like_filter_parser($filter['operator'], $filter['value']['city']);
      if (is_array($values)) {
        $filter_values = array();
        foreach($values as $value) {
          $filter_values[] = $field;
          $filter_values[] = $value;
        }
        $all_values = array_merge($all_values, $filter_values);
      } else {
        $all_values[] = $field;
        $all_values[] = $values;
      }

      $query->add_where("$table.country ". ($filter['operator'] == 'not' ? '!' : '') ."= '%s' AND $where", $all_values);
    break;
  }
}

function gmaps_content_address_field_views_filter_handler($op, $filter, $filterinfo, &$query) {
  switch (trim($filter['value'])) {
    case (''):
      return;
      break;
  }
  switch ($op) {
    case 'handler':
      $table = $filterinfo['table'];
      $query->ensure_table($table);

      $all_wheres = $all_values = array();
      $country_codes = _gmaps_content_countryname_views_like_filter_parser($filter['operator'], $filter['value']);
      if ($filter['operator'] == 'not') {
        $all_wheres[] = "$table.country NOT IN ('%s')";
        $all_values[] = implode("', '", $country_codes);
      } else {
        foreach ($country_codes as $code) {
          $all_wheres[] = "$table.country = '%s'";
          $all_values[] = $code;
        }
      }

      list ($where, $values) = _gmaps_content_views_like_filter_parser($filter['operator'], $filter['value']);

      foreach (array('adminarea', 'subadminarea', 'locality', 'deplocality', 'thoroughfare', 'postalcode') as $column) {
        $field = "$table.$column";
        $all_wheres[] = $where;
        if (is_array($values)) {
          $filter_values = array();
          foreach($values as $value) {
            $filter_values[] = $field;
            $filter_values[] = $value;
          }
          $all_values = array_merge($all_values, $filter_values);
        } else {
          $all_values[] = $field;
          $all_values[] = $values;
        }
      }
      $glue = $filter['operator'] == 'not' ? ' AND ' : ' OR ';
      $query->add_where('('. implode($glue, $all_wheres) .')', $all_values);
    break;
  }
}

function gmaps_content_address_address_views_filter_handler($op, $filter, $filterinfo, &$query) {
  if (!is_array($filter['value'])) {
    $filter['value'] = unserialize($filter['value']);
  }
  if (gmaps_is_address_empty($filter['value'])) {
    return;
  }
  switch ($op) {
    case 'handler':
      $table = $filterinfo['table'];
      $query->ensure_table($table);
      $address = &$filter['value'];

      $all_wheres = $all_values = array();
      if (!empty($address['country'])) {
        $country_where = "$table.country ". ($filter['operator'] == 'not' ? '!' : '') ."= '%s'";
        $all_values[] = $address['country'];
      }

      foreach (array('adminarea', 'subadminarea', 'locality', 'deplocality', 'thoroughfare', 'postalcode') as $column) {
        if (empty($address[$column]) && $address[$column] !== 0) {
          continue;
        }
        $field = "$table.$column";

        list ($where, $values) = _gmaps_content_views_like_filter_parser($filter['operator'], $address[$column]);
        if (is_array($values)) {
          $filter_values = array();
          foreach($values as $value) {
            $filter_values[] = $field;
            $filter_values[] = $value;
          }
          $all_values = array_merge($all_values, $filter_values);
        } else {
          $all_values[] = $field;
          $all_values[] = $values;
        }
        $all_wheres[] = $where;
      }

      if (empty($all_wheres)) {
        $query->add_where($country_where, $all_values);
      } else {
        $glue = $filter['operator'] == 'not' ? ' AND ' : ' OR ';
        $country_where .= !empty($country_where) ? ' AND ' : '';
        $query->add_where("$country_where (". implode($glue, $all_wheres) .')', $all_values);
      }
    break;
  }
}

function _gmaps_content_parse_filter_words($value) {
  $values = array();
  preg_match_all('/ (-?)("[^"]+"|[^" ]+)/i', ' '. $value, $matches, PREG_SET_ORDER);
  foreach ($matches as $match) {
    $phrase = false;
    // Strip off phrase quotes
    if ($match[2]{0} == '"') {
      $match[2] = substr($match[2], 1, -1);
      $phrase = true;
    }
    $words = trim($match[2], ',?!();:-');
    $words = $phrase ? array($words) : preg_split('/ /', $words, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($words as $word) {
      $values[] = trim($word, " ,!?");
    }
  }
  return $values;
}

function _gmaps_content_countryname_views_like_filter_parser($operator, $value) {
  $countries = _gmaps_country_list();
  $codes = array();
  if ($operator == 'word' || $operator == 'allwords') {
    $values = _gmaps_content_parse_filter_words($value);
  }
  foreach ($countries as $code => $name) {
    switch($operator) {
      case 'contains':
      case 'not'://handled through NOT IN
        if (preg_match("|.*$value.*|i", $name)) {
          $codes[] = $code;
        }
        break;
      case 'word':
      case 'allwords':
        foreach($values as $word) {
          if (preg_match("|.*$word.*|i", $name)) {
            $codes[] = $code;
          }
        }
        break;
      case 'starts':
        if (preg_match("|^$value.*|i", $name)) {
          $codes[] = $code;
        }
        break;
      case 'ends':
        if (preg_match("|.*$value" . '$|i', $name)) {
          $codes[] = $code;
        }
        break;
      case '=':
        if (strtoupper($value) == strtoupper($name)) {
          $codes[] = $code;
        }
        break;
    }
  }
  return $codes;
}

function _gmaps_content_views_like_filter_parser($operator, $value) {
  switch ($operator) {
    case 'contains':
      $where = "UPPER(%s) LIKE UPPER('%%%s%%')";
      break;
    case 'word':
    case 'allwords':
      $where = "UPPER(%s) LIKE UPPER('%%%s%%')";
      $values = _gmaps_content_parse_filter_words($value);
      if (count($values) > 1) {
        $where = array_fill(0, count($values), $where);
        $glue = $operator == 'word' ? ' OR ' : ' AND ';
        $where = '('. implode($glue, $where) .')';
      }
      break;
    case 'starts':
      $where = "UPPER(%s) LIKE UPPER('%s%%')";
      break;
    case 'ends':
      $where = "UPPER(%s) LIKE UPPER('%%%s')";
      break;
    case 'not':
      $where = "UPPER(%s) NOT LIKE UPPER('%%%s%%')";
      break;
    case '=':
      $where = "UPPER(%s) = UPPER('%s')";
      break;
  }
  if (!isset($values)) {
    $values = $value;
  }
  return array($where, $values);
}

function gmaps_content_point_views_table($field) {
  $field_types = _content_field_types();

  $table = array();
  $left_table = $field['type'] .'_'. $field['field_name'];
  $right_table = 'gmaps_p_'. $field['field_name'];

  $table['name'] = 'gmaps_coordinates';
  $table['join'] = array(
    'left' => array(
      'table' => $field['type'] .'_'. $field['field_name'],
      'field' => 'vid',
    ),
    'right' => array(
      'field' => 'vid',
    ),
    'extra' => array(
      "field_name = $left_table.field_name AND $left_table.delta = $right_table.delta AND $right_table.subdelta = 0" => NULL,
    ),
  );


  $table['fields'] = array();
  $table['fields']['latitude']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Latitude';
  $table['fields']['longitude']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Longitude';
  //$table['fields']['height_m']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Height (m)';
  $table['fields']['x']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - X coordinate';
  $table['fields']['y']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Y coordinate';
  $table['fields']['z']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Z coordinate';
  $table['fields']['privacy']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Coordinate privacy';
  if (in_array($field['type'], array('gmaps_point', 'gmaps_marker'))) {
    $table['fields']['directions']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions links';
    $table['fields']['directions_from']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions from link';
    $table['fields']['directions_to']['name'] = $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Directions to link';
  }

  foreach ($table['fields'] as $detail => $field_info) {
    $table['fields'][$detail] = array_merge($field_info, array(
      'field' => 'delta',
      'sortable' => strpos($detail, 'directions') === FALSE,
      'query_handler' => 'gmaps_content_views_query_handler',
      'handler' => array(
        'gmaps_content_point_views_field_handler_group' => t('Group multiple values'),
        'gmaps_content_point_views_field_handler_ungroup' => t('Do not group multiple values'),
        'gmaps_content_point_views_field_handler_first' => t('Show first value only'),
        'gmaps_content_point_views_field_handler_last' => t('Show last value only'),
      ),
      //TODO: add link/no link, 'option' => array('#type' => 'select', '#options' => $formatters),
      'gmaps_content_field' => $field,
    ));
  }
  $table['fields']['privacy']['sortable'] = FALSE;
  if (in_array($field['type'], array('gmaps_point', 'gmaps_marker'))) {
    $table['fields']['directions_from']['option'] = $table['fields']['directions_to']['option'] = 'string';
  }

  $table['sorts'] = array();
  $table['filters'] = array();
  foreach ($table['fields'] as $detail => $field_info) {
    $table['sorts'][$detail] = array(
      'name' => $field_info['name'],
    );
    $table['filters'][$detail] = array(
      'name' => $field_info['name'],
      'operator' => 'views_handler_operator_gtlt',
      'value' => array(),
    );
  }
  unset($table['sorts']['privacy'], $table['filters']['privacy']);
  
  $table['filters']['distance'] = array(
    'name' => $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .') - Distance from point',
    'operator' => 'views_handler_operator_gtlt',
    'value' => array(
      '#tree' => true,
      '#type' => 'latlon',
      '#process' => array('_gmaps_content_distance_views_filter_value_process' => array($field)),
      '#validate' => array('_gmaps_content_distance_views_filter_value_validate' => array()),
      '#after_build' => array('_gmaps_content_distance_views_filter_value_after_build'),
    ),
    'handler' => 'gmaps_content_distance_views_filter_handler',
    'gmaps_content_field' => $field,
  );

  if (module_exists('gmaps_views')) {
    $map_types = gmaps_get_map_types();
    $table['fields'][$field['type'] .'_node_link_marker'] = array(
      'name' => 'Node link marker - '. $map_types[$field['point_map_type']]->name .' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
      'field' => 'delta',
      'query_handler' => 'gmaps_content_views_query_handler',
      'handler' => array(
        'gmaps_content_node_link_marker_views_field_handler_first' => t('Show on first value only'),
        'gmaps_content_node_link_marker_views_field_handler_last' => t('Show on last value only'),
        'gmaps_content_node_link_marker_views_field_handler_group' => t('Show on all values'),
      ),
      'gmaps_content_field' => $field,
      'gmaps_views_map_type' => $field['point_map_type'],
    );
    $table['fields'][$field['type'] .'_teaser_marker'] = array(
      'name' => 'Node teaser marker - '. $map_types[$field['point_map_type']]->name .' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
      'field' => 'delta',
      'query_handler' => 'gmaps_content_views_query_handler',
      'handler' => array(
        'gmaps_content_teaser_marker_views_field_handler_first' => t('Show on first value only'),
        'gmaps_content_teaser_marker_views_field_handler_last' => t('Show on last value only'),
        'gmaps_content_teaser_marker_views_field_handler_group' => t('Show on all values'),
      ),
      'gmaps_content_field' => $field,
      'gmaps_views_map_type' => $field['point_map_type'],
    );
    $table['fields'][$field['type'] .'_field_list_marker'] = array(
      'name' => 'Field list marker - '. $map_types[$field['point_map_type']]->name .' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
      'field' => 'delta',
      'query_handler' => 'gmaps_content_views_query_handler',
      'validate' => 'gmaps_content_field_list_marker_views_field_validator',
      'handler' => array(
        'gmaps_content_field_list_marker_views_field_handler_first' => t('Show on first value only'),
        'gmaps_content_field_list_marker_views_field_handler_last' => t('Show on last value only'),
        'gmaps_content_field_list_marker_views_field_handler_group' => t('Show on all values'),
      ),
      'gmaps_content_field' => $field,
      'gmaps_views_map_type' => $field['point_map_type'],
      'gmaps_views_record_handler' => 'gmaps_content_field_list_marker_gmaps_views_record_handler',
    );
    if ($field['type'] == 'gmaps_marker' || $field['type'] == 'gmaps_gaddress_marker') {
      $table['fields'][$field['type'] .'_content_marker'] = array(
        'name' => 'Content marker - '. $map_types[$field['point_map_type']]->name .' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
        'field' => 'delta',
        'query_handler' => 'gmaps_content_views_query_handler',
        'handler' => array(
          'gmaps_content_content_marker_views_field_handler_first' => t('Show on first value only'),
          'gmaps_content_content_marker_views_field_handler_last' => t('Show on last value only'),
          'gmaps_content_content_marker_views_field_handler_group' => t('Show on all values'),
        ),
        'gmaps_content_field' => $field,
        'gmaps_views_map_type' => $field['point_map_type'],
      );
    }
  }

  return array($right_table => $table);
}

function gmaps_content_point_views_query_handler($field, &$fieldinfo, &$query) {
  if ($field['handler'] != 'gmaps_content_point_views_field_handler_ungroup') {
    $query->ensure_table($field['tablename']);
    if ($fieldinfo['gmaps_content_field']['multiple']) {
      $fieldinfo['notafield'] = TRUE;
    }
  }
  $query->add_field('vid', 'node');
}

function gmaps_content_point_views_field_handler_group($field_info, $field_data, $value, $data, $from = 0, $count = 'all', $order = 'ASC') {
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];
    if ($field['multiple']) {
      $items = _gmaps_content_point_field_load($data, $field, $from, $count, $order);

      if (!empty($items)) {
        if ($field_data['field'] == 'privacy') {
          foreach($items as $delta => $item) {
            check_plain($item['point_privacy']['distance']);
            $fields[] = theme('gmaps_distance', $item['point_privacy'], 3);
          }
        }
        else if (strpos($field_data['field'], 'directions') !== FALSE) {
          foreach($items as $delta => $item) {
            $from = gmaps_maps_google_com_directions_url(null, FALSE, $item['coordinates']);
            $to = gmaps_maps_google_com_directions_url(null, TRUE, $item['coordinates']);
            if (strpos($field_data['field'], '_from') !== FALSE) {
              $fields[] = l(!empty($field_data['options']) ? $field_data['options'] : t('From here'), $from);
            }
            else if (strpos($field_data['field'], '_to') !== FALSE) {
              $fields[] = l(!empty($field_data['options']) ? $field_data['options'] : t('To here'), $to);
            }
            else {
              $fields[] = theme('gmaps_directions_links', $from, $to);
            }
          }
        }
        else {
          foreach($items as $delta => $item) {
            _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);
            $acc = ($field_data['field'] == 'latitude' || $field_data['field'] == 'longitude') ? 6 : 3;
            $fields[] = number_format(check_plain($item['coordinates'][$field_data['field']]), $acc, '.', '');
          }
        }
        if (!empty($fields)) {
          return theme('content_view_multiple_field', $fields, $field, $data);
        }
      }
    }
    else {
      return gmaps_content_point_views_field_handler_ungroup($field_info, $field_data, 0, $data);
    }
  }
  return '';
}

function gmaps_content_point_views_field_handler_first($field_info, $field_data, $value, $data) {
  return gmaps_content_point_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'ASC');
}

function gmaps_content_point_views_field_handler_last($field_info, $field_data, $value, $data) {
  return gmaps_content_point_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'DESC');
}

function gmaps_content_point_views_field_handler_ungroup($field_info, $field_data, $value, $data) {
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    $field = $field_info['gmaps_content_field'];

    $item = _gmaps_content_point_field_load($data, $field, $value, 1);

    if (!empty($item)) {
      if ($field_data['field'] == 'privacy') {
        check_plain($$item['point_privacy']['distance']);
        return theme('gmaps_distance', $item['point_privacy'], 3);
      }
      else if (strpos($field_data['field'], 'directions') !== FALSE) {
        $from = gmaps_maps_google_com_directions_url(null, FALSE, $item['coordinates']);
        $to = gmaps_maps_google_com_directions_url(null, TRUE, $item['coordinates']);
        if (strpos($field_data['field'], '_from') !== FALSE) {
          return l(!empty($field_data['options']) ? $field_data['options'] : t('From here'), $from);
        }
        else if (strpos($field_data['field'], '_to') !== FALSE) {
          return l(!empty($field_data['options']) ? $field_data['options'] : t('To here'), $to);
        }
        else {
          return theme('gmaps_directions_links', $from, $to);
        }
      }
      else {
        _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);
        $acc = ($field_data['field'] == 'latitude' || $field_data['field'] == 'longitude') ? 6 : 3;
        return number_format(check_plain($item['coordinates'][$field_data['field']]), $acc, '.', '');
      }
    }
  }

  return ''; 
}

function gmaps_content_node_link_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config = NULL, $from = 0, $count = 'all', $order = 'ASC') {
  if (is_null($map_config)) {
    return '';
  }
  static $types = array();
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    if (empty($types)) {
      $types = content_types();
    }
    $field = $types[$data->type]['fields'][$field_info['gmaps_content_field']['field_name']];

    $items = array();
    if ($field['multiple']) {
      $items = _gmaps_content_point_field_load($data, $field, $from, $count, $order);
    } else {
      $items = _gmaps_content_point_field_load($data, $field, 0, 1);
      $count = 1;
    }

    if (!empty($items)) {
      $items = ($count == 1) ? array($items) : $items;
      $node = node_load($data->nid);
      foreach($items as $delta => $item) {
        _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);

        $marker = array(
          'latitude' => $item['coordinates']['latitude'],
          'longitude' => $item['coordinates']['longitude'],
          'id' => $data->vid .'-'. $field['field_name'] .'-'. $delta,
          'title' => $node->title,
          'url' => 'node/'. $node->nid,
        );
      
        _gmaps_content_prepare_marker($marker, $item, $field, $map_config);

        $items[$delta] = $marker;
      }
      return array('marker' => $items);
    }
  }
  return '';
}


function gmaps_content_node_link_marker_views_field_handler_first($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_node_link_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config, 0, 1, 'ASC');
}


function gmaps_content_node_link_marker_views_field_handler_last($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_node_link_marker_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'DESC');
}

function gmaps_content_teaser_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config = NULL, $from = 0, $count = 'all', $order = 'ASC') {
  if (is_null($map_config)) {
    return '';
  }
  static $types = array();
  static $ac_prefix = 'gmaps-content/teaser-marker/autocomplete';
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    if (empty($types)) {
      $types = content_types();
    }
    $field = $types[$data->type]['fields'][$field_info['gmaps_content_field']['field_name']];

    $items = array();
    if ($field['multiple']) {
      $items = _gmaps_content_point_field_load($data, $field, $from, $count, $order);
    } else {
      $items = _gmaps_content_point_field_load($data, $field, 0, 1);
      $count = 1;
    }

    if (!empty($items)) {
      $items = ($count == 1) ? array($items) : $items;
      $node = node_load($data->nid);
      if (!$map_config->marker_options['delayed_iw_content']) {
        $content = node_view($node, TRUE, TRUE);
      } else {
        $content = NULL;
      }
      foreach($items as $delta => $item) {
        _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);

        $marker = array(
          'latitude' => $item['coordinates']['latitude'],
          'longitude' => $item['coordinates']['longitude'],
          'id' => $data->vid .'-'. $field['field_name'] .'-'. $delta,
          'title' => $node->title,
        );
      
        _gmaps_content_prepare_marker($marker, $item, $field, $map_config);

        if ($map_config->marker_options['delayed_iw_content']) {
          $marker['iw_autocomplete'] = $ac_prefix .'/'. $node->nid;
        } else {
          if (!empty($content)) {
            $marker['iw_content'] = theme('gmaps_content_single_marker_content', 0, l($node->title, 'node/'. $node->nid), $content);
          }
        }
        $items[$delta] = $marker;
      }
      return array('marker' => $items);
    }
  }
  return '';
}


function gmaps_content_teaser_marker_views_field_handler_first($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_teaser_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config, 0, 1, 'ASC');
}


function gmaps_content_teaser_marker_views_field_handler_last($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_teaser_marker_views_field_handler_group($field_info, $field_data, $value, $data, 0, 1, 'DESC');
}

function gmaps_content_field_list_marker_views_field_handler_group($field_info, $field_data, $value, $data) {
  return '';
}



function gmaps_content_field_list_marker_views_field_handler_first($field_info, $field_data, $value, $data) {
  return '';
}

function gmaps_content_field_list_marker_views_field_handler_last($field_info, $field_data, $value, $data) {
  return '';
}

function gmaps_content_field_list_marker_gmaps_views_record_handler($view, $map_config, $field_info, $field_data, $view_fields_info, $view_fields_data, $data) {
  static $types = array();
  static $fields = array();
  static $ac_prefix = 'gmaps-content/field-list-marker/autocomplete';
  static $cache_cleared = FALSE;
  if (empty($types)) {
    $types = content_types();
  }
  if (empty($fields)) {
    $fields = _views_get_fields();
  }
  if (!$cache_cleared) {
    unset($_SESSION['gmaps_content_field_list_marker'][$view->vid][$field_data['id']]);
    if ($map_config->marker_options['delayed_iw_content']) {
      $_SESSION['gmaps_content_field_list_marker'][$view->vid][$field_data['id']] = array(
        'descriptor' => array(
          'view' => $view,
          'view_fields_data' => $view_fields_data,
        ),
      );
    }
    $cache_cleared = TRUE;
  }
  $field = $types[$data->type]['fields'][$field_info['gmaps_content_field']['field_name']];
  switch (array_pop(explode('_', $field_data['handler']))) {
    case 'group':
      $count = 'all';
      $order = 'ASC';
      break;
    case 'first':
      $count = 1;
      $order = 'ASC';
      break;
    case 'last':
      $count = 1;
      $order = 'DESC';
      break;
  }

    $items = array();
    if ($field['multiple']) {
      $items = _gmaps_content_point_field_load($data, $field, 0, $count, $order);
    } else {
      $items = _gmaps_content_point_field_load($data, $field, 0, 1);
      $count = 1;
    }

    if (!empty($items)) {
      $items = ($count == 1) ? array($items) : $items;
      $title = !empty($field_data['label']) ? $field_data['label'] : '';
      $content = '';
      if (!$map_config->marker_options['delayed_iw_content']) {
        foreach ($view_fields_data as $view_field) {
          if (!isset($view_fields_info[$view_field['id']]['visible']) && $view_fields_info[$view_field['id']]['visible'] !== FALSE) {
            if ($view_field['label']) {
              $content .= "<div class='view-label ". views_css_safe('view-label-'. $view_field['queryname']) ."'>" . $view_field['label'] . "</div>";
            }
            $content .= "<div class='view-field ". views_css_safe('view-data-'. $view_field['queryname']) ."'>" . views_theme_field('views_handle_field', $view_field['queryname'], $fields, $view_field, $data, $view) . "</div>";
          }
        }
        if (!empty($content)) {
          $content = "<div class='view-item ". views_css_safe('view-item-'. $view->name) ."'>$content</div>\n";
        }
      }
      foreach($items as $delta => $item) {
        _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);

        $marker = array(
          'latitude' => $item['coordinates']['latitude'],
          'longitude' => $item['coordinates']['longitude'],
          'id' => $data->vid .'-'. $field['field_name'] .'-'. $delta,
          'title' => $title,
        );
      
        $item['map_icon_config'] = $item['map_icon_config'] == 'i' ? $field['widget']['map_icon_config'] : $item['map_icon_config'];
        $marker['icon'] = $item['map_icon_config'];

        $item['map_zoomrange_min'] = $item['map_zoomrange_min'] == 'i' ? $field['widget']['map_zoomrange_min'] : $item['map_zoomrnage_min'];
        if ($item['map_zoomrange_min'] != 'd') {
          $marker['zoomrange']['min'] = $item['map_zoomrange_min'];

          $item['map_zoomrange_max'] = $item['map_zoomrange_max'] == 'i' ? $field['widget']['map_zoomrange_max'] : $item['map_zoomrnage_max'];
          if ($item['map_zoomrange_max'] == 'i' && $map_config->marker_manager_options['maxzoom'] != 'u') {
            $marker['zoomrange']['max'] = $map_config->marker_manager_options['maxzoom'];
          }
          else if ($item['map_zoomrange_max'] != 'u') {
            $marker['zoomrange']['max'] = $item['map_zoomrange_max'];
          }
        }

        if ($map_config->marker_options['delayed_iw_content']) {
          $content = md5(mt_rand());
          $_SESSION['gmaps_content_field_list_marker'][$view->vid][$field_data['id']]['content'][$content] = array(
            'data' => $data,
            'title' => $title,
          );
          $marker['iw_autocomplete'] = $ac_prefix .'/'. $view->vid .'/'. $field_data['id'] .'/'. $content;
        } else {
          if (!empty($content)) {
            $marker['iw_content'] = theme('gmaps_content_single_marker_content', 0, $title, $content);
          }
        }
        $items[$delta] = $marker;
      }
      return array('marker' => $items);
    }
  return '';
}

function gmaps_content_field_list_marker_views_field_validator($fielddata, $view, $form) {
  $fields = _views_get_fields();
  $has_content = FALSE;
  foreach ($form['field'] as $key => $field_data) {
    if (is_numeric($key) && $form['field'][$key]['id'][' #value'] != $fielddata['id']) {
      if (!isset($fields[$form['field'][$key]['id']['#value']]['gmaps_views_map_type'])) {
        $has_content = TRUE;
        break;
      } else if (is_numeric($key)) {
        $self_key = $key;
      }
    }
  }
  if (!$has_content) {
    form_error($form['field'][$self_key]['label'], t('Field list marker requires at least one non map overlay field to be added.'));
  }
}

function gmaps_content_content_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config = NULL, $from = 0, $count = 'all', $order = 'ASC') {
  if (is_null($map_config)) {
    return '';
  }
  static $types = array();
  static $ac_prefix = 'gmaps-content/field/autocomplete';
  if ((!empty($value) || $value == 0) && isset($field_info['gmaps_content_field']) && is_array($field_info['gmaps_content_field'])) {
    if (empty($types)) {
      $types = content_types();
    }
    $field = $types[$data->type]['fields'][$field_info['gmaps_content_field']['field_name']];

    if ($field['multiple']) {
      $items = _gmaps_content_point_field_load($data, $field, $from, $count, $order);
    } else {
      $items = _gmaps_content_point_field_load($data, $field);
      $count == 'all';
    }

    if (!empty($items)) {
      $items = ($count == 1) ? array($items) : $items;
      foreach($items as $delta => $item) {
        _gmaps_content_apply_coordinate_privacy($item['coordinates'], $item['point_privacy'], $field['point_map_type']);

        $title = check_plain($item['marker_content'][0]['title']);
        $marker = array(
          'latitude' => $item['coordinates']['latitude'],
          'longitude' => $item['coordinates']['longitude'],
          'id' => $data->vid .'-'. $field['field_name'] .'-'. $delta,
          'title' => $title,
        );
      
        _gmaps_content_prepare_marker($marker, $item, $field, $map_config);

        $item['marker_action'] = $item['marker_action'] == 'default' ? $field['widget']['marker_action'] : $item['marker_action'];
        if ($item['marker_action'] == 'url') {
          $marker['url'] = $item['marker_url'];
        }
        else if ($item['marker_action'] == 'iw') {
          //TODO: add multi tab support
          if ($map_config->marker_options['delayed_iw_content']) {
            $marker['iw_autocomplete'] = $ac_prefix .'/'. $data->nid .'/'. $field['field_name'] .'/'. $delta .'/_mc/'. $order;
          } else {
            $content = check_markup($item['marker_content'][0]['body'], $item['marker_content'][0]['format'], FALSE);
            if (!empty($content)) {
              $marker['iw_content'] = theme('gmaps_content_single_marker_content', 0, $title, $content);
            }
          }
        }
        $items[$delta] = $marker;
      }
      return array('marker' => $items);
    }
  }
  return '';
}

function gmaps_content_content_marker_views_field_handler_first($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_content_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config, 0, 1, 'ASC');
}

function gmaps_content_content_marker_views_field_handler_last($field_info, $field_data, $value, $data, $map_config = NULL) {
  return gmaps_content_content_marker_views_field_handler_group($field_info, $field_data, $value, $data, $map_config, 0, 1, 'DESC');
}

function gmaps_content_teaser_marker_autocomplete($nid) {
  $node = node_load($nid);
  $content = node_view($node, TRUE, TRUE);
  if (!empty($content)) {
    $content = theme('gmaps_content_single_marker_content', 0, l($node->title, 'node/'. $node->nid), $content);
  }

  print drupal_to_js($content);
  exit;
}

function gmaps_content_field_list_marker_autocomplete($view_id, $field_id, $content_id) {
  include_once(drupal_get_path('module', 'views') .'/views_cache.inc');
  $fields = _views_get_fields();
  $content = '';
  $view_fields_data = $_SESSION['gmaps_content_field_list_marker'][$view_id][$field_id]['descriptor']['view_fields_data'];
  $view = $_SESSION['gmaps_content_field_list_marker'][$view_id][$field_id]['descriptor']['view'];
  $data = $_SESSION['gmaps_content_field_list_marker'][$view_id][$field_id]['content'][$content_id]['data'];
  $title = $_SESSION['gmaps_content_field_list_marker'][$view_id][$field_id]['content'][$content_id]['title'];
  unset($_SESSION['gmaps_content_field_list_marker'][$view_id][$field_id]['content'][$content_id]);
  foreach ($view_fields_data as $view_field) {
    if (!isset($fields[$view_field['id']]['visible']) && $fields[$view_field['id']]['visible'] !== FALSE) {
      if ($view_field['label']) {
        $content .= "<div class='view-label ". views_css_safe('view-label-'. $view_field['queryname']) ."'>" . $view_field['label'] . "</div>";
      }
      $content .= "<div class='view-field ". views_css_safe('view-data-'. $view_field['queryname']) ."'>" . views_theme_field('views_handle_field', $view_field['queryname'], $fields, $view_field, $data, $view) . "</div>";
    }
  }
  if (!empty($content)) {
    $content = "<div class='view-item ". views_css_safe('view-item-'. $view->name) ."'>$content</div>\n";
    $content = theme('gmaps_content_single_marker_content', 0, $title, $content);
  }

  print drupal_to_js($content);
  exit;
}

function _gmaps_content_distance_views_filter_value_process($element) {
  $args = func_get_args();
  $field = array_pop($args);
  if (!empty($element['#default_value']) && !is_array($element['#default_value'])) {
    $element['#default_value'] = unserialize($element['#default_value']);
  }
  if (!empty($element['#value']) && !is_array($element['#value'])) {
    $element['#value'] = unserialize($element['#value']);
  }
  $element['#use_map_config'] = $field['point_map_config'];

  $latlon = _gmaps_latlon_expand($element);
  $dist = _gmaps_distance_expand($element);
  $latlon['distance'] = $dist['distance'];
  $latlon['unit'] = $dist['unit'];
  $latlon['distance']['#prefix'] = '<div class="distance-container">';
  $latlon['unit']['#suffix'] = '</div>';
  return $latlon;
}

function _gmaps_content_distance_views_filter_value_after_build($element) {
  if (is_array($element['#value'])) {
    $element['#value'] = serialize($element['#value']);
    form_set_value($element, $element['#value']);
  }
  return $element;
}

function _gmaps_content_distance_views_filter_value_validate($element) {
  //print_r($element);
  if (!empty($element['#value']) && !is_array($element['#value'])) {
    $element['#value'] = unserialize($element['#value']);
    form_set_value($element, $element['#value']);
  }
  _gmaps_latlon_validate($element);
  _gmaps_distance_validate($element);
}

function gmaps_content_distance_views_filter_handler($op, $filter, $filterinfo, &$query) {
  if (!is_array($filter['value'])) {
    $filter['value'] = unserialize($filter['value']);
  }
  if (gmaps_is_latlon_empty($filter['value'])) {
    return;
  }
  if (gmaps_is_distance_empty($filter['value'])) {
    $filter['value']['ditance'] = 0;
    $filter['value']['unit'] = 'm';
  }
  switch ($op) {
    case 'handler':
      $field = $filterinfo['gmaps_content_field'];
      $table = $filterinfo['table'];
      $query->ensure_table($table);
      
      $distance = gmaps_convert_distance($filter['value'], 'm');
      list($x, $y, $z) = gmaps_latlon_to_xyz($filter['value']['latitude'], $filter['value']['longitude'], $field['point_map_type']);
      if (is_null($x)) {
        return;
      }
      $op = $filter['operator'];
      $query->where[] = "SQRT(POWER($table.x - $x, 2) + POWER($table.y - $y, 2) + POWER($table.z - $z, 2)) $op $distance";
    break;
  }
}

/**
 * Themeables
 */

function theme_gmaps_content_city_in_country_views_filter_value($element) {
  return $element['#children'];
}
