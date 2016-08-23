<?php
// $Id: gmaps-content-field-views-arguments.inc,v 1.1 2008/11/19 12:16:08 xmarket Exp $

function gmaps_content_address_views_arguments($field) {
  $field_types = _content_field_types();
  $arguments = array(
    'proximity_address_node_'. $field['field_name'] => array(
      'name' => t('Proximity by address of node').' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
      'handler' => 'gmaps_content_address_arg_handler_proximity_node',
      'option' => array(
        '#type' => 'select',
        '#options' => array_reverse(_gmaps_content_address_privacy_levels()),
      ),
      'help' => t('This argument requires a node ID and filters the content by the addresses of the given node, set the option to the depth to check.'),
    ),
  );
  return $arguments;
}

function gmaps_content_address_arg_handler_proximity_node($op, &$query, $argtype, $arg = '') {
  switch($op) {
    case 'summary':
      $query->add_field("vid");
      $fieldinfo['field'] = "node.title";
      return $fieldinfo;
      break;
    case 'sort':
      $query->add_orderby('node', 'title', $argtype);
      break;
    case 'filter':
      $node = node_load(intval($arg));
      if ($node) {
        $levels = array_reverse(_gmaps_content_address_privacy_levels());
        array_shift($levels);
        $map = _gmaps_content_get_address_detail_fields();
        foreach($map as $key => $field) {
          if ($key <= $argtype['options']) {
            $fields[] = $field;
          }
        }

        $result = db_query('SELECT DISTINCT(country)'. (!empty($field) ? ', '. implode(',', $fields) : '') .' FROM {gmaps_addresses} WHERE nid = %d', $node->nid);
        if (!db_num_rows($result)) {
          $query->add_where('node.nid = 0');
          return;
        }
        $thnum = $query->add_table('gmaps_addresses', false, 1, array('type' => 'inner', 'left' => array('table' => 'node', 'field' => 'vid'), 'right' => array('field' => 'vid')));
        $tablename = $query->get_table_name('gmaps_addresses', $thnum);
        $where = array();
        while($a = db_fetch_array($result)) {
          $sql = array();
          foreach($a as $field => $value) {
            $sql[] = "$tablename.$field = '%s'";
            $where_args[] = $value;
          }
          if (count($sql)) {
            $where[] = '('. implode(' AND ', $sql) .')';
          }
        }
        $where = implode(' OR ', $where);
        $field_name = substr($argtype['type'], 23);
        if (!empty($field_name)) {
          $where = "$tablename.field_name = '%s' AND ($where)";
          array_unshift($where_args, $field_name);
        }
        array_unshift($where_args, $node->nid);
        $query->add_where("node.nid != %d AND ($where)", $where_args);
      }
      break;
    case 'link':
      $node = node_load(array('vid' => $query->vid));
      return l($query->node_title, $arg .'/'. $node->nid);
    case 'title':
      $node = node_load(intval($query));
      return $node ? check_plain($node->title) : '';
  }
}

function gmaps_content_point_views_arguments($field) {
  $field_types = _content_field_types();
  $arguments = array(
    'proximity_distance_node_'. $field['field_name'] => array(
      'name' => t('Proximity by distance from node').' - '. $field_types[$field['type']]['label'] .': '. $field['widget']['label'] .' ('. $field['field_name'] .')',
      'handler' => 'gmaps_content_point_arg_handler_proximity_node',
      'option' => array(
        'maxdist' => array('#type' => 'distance',
          '#title' => t('Max. distance'),
        ),
        'operator' => array('#type' => 'select',
          '#title' => t('Operator'),
          '#options' => 'views_handler_operator_gtlt',
        ),
        'orderby' => array('#type' => 'select',
          '#title' => t('Order by'),
          '#options' => array(0 => t('Disabled'), 'ASC' => t('Ascending'), 'DESC' => t('Descending')),
        ),
        'distlink' => array('#type' => 'select',
          '#title' => t('Distance link'),
          '#options' => array(0 => t('Disabled'), 'LEAST' => t('Least'), 'GREATEST' => t('Greatest')),
        ),
        '#tree' => true,
        '#type' => 'gmaps_content_proximity_distance_node_option_value',
        '#process' => array('_gmaps_content_proximity_distance_node_option_value_process' => array($field)),
        '#after_build' => array('_gmaps_content_proximity_distance_node_option_value_after_build'),
      ),
      'help' => t('This argument requires a node ID and filters the content by the distance from the given node.'),
    ),
  );
  return $arguments;
}

function gmaps_content_point_arg_handler_proximity_node($op, &$query, $argtype, $arg = '') {
  switch($op) {
    case 'summary':
      $query->add_field("vid");
      $fieldinfo['field'] = "node.title";
      return $fieldinfo;
      break;
    case 'sort':
      $query->add_orderby('node', 'title', $argtype);
      break;
    case 'filter':
      $node = node_load(intval($arg));
      if ($node) {
        if (!is_array($argtype['options'])) {
          $argtype['options'] = unserialize($argtype['options']);
        }
        $result = db_query('SELECT x, y, z FROM {gmaps_coordinates} WHERE nid = %d', $node->nid);
        //disable result if the given node doesn't have any coordinates
        if (!db_num_rows($result)) {
          $query->add_where('node.nid = 0');
          return;
        }

        $field_name = substr($argtype['type'], 24);

        $thnum = $query->add_table('gmaps_coordinates', false, 1, array('type' => 'inner', 'left' => array('table' => 'node', 'field' => 'vid'), 'right' => array('field' => 'vid')));
        $table = $query->get_table_name('gmaps_coordinates', $thnum);

        $distance = gmaps_convert_distance($argtype['options']['maxdist'], 'm');
        $where = $where_args = $orders = $coords = array();
        while($c = db_fetch_array($result)) {
          $coords[] = $c;
          $orders[] = "SQRT(POWER($table.x - ". (float)$c['x'] .", 2) + POWER($table.y - ". (float)$c['y'] .", 2) + POWER($table.z - ". (float)$c['z'] .", 2))";
          $where[] = "SQRT(POWER($table.x - %f, 2) + POWER($table.y - %f, 2) + POWER($table.z - %f, 2)) %s %f";
          $where_args[] = $c['x'];
          $where_args[] = $c['y'];
          $where_args[] = $c['z'];
          $where_args[] = $argtype['options']['operator'];
          $where_args[] = $distance;
        }
        if (!empty($argtype['options']['maxdist']['distance'])) {
          $where = implode(' OR ', $where);
          if (!empty($field_name)) {
            $where = "$table.field_name = '%s' AND ($where)";
            array_unshift($where_args, $field_name);
          }
          array_unshift($where_args, $node->nid);
          $query->add_where("node.nid != %d AND ($where)", $where_args);
        }
        if (!empty($argtype['options']['orderby'])) {
          if (count($orders) > 1) {
            if ($argtype['options']['orderby'] == 'ASC') {
              $orders = 'LEAST('. implode(',', $orders) .')';
            } else {
              $orders = 'GREATEST('. implode(',', $orders) .')';
            }
          } else {
            $orders = array_shift($orders);
          }
          $query->add_orderby('', $orders, $argtype['options']['orderby'], $argtype['type'] .'_orderby');
          $query->add_groupby('node.nid');
        }
        if (!empty($argtype['options']['distlink'])) {
          $_SESSION['gmaps_content_proximity_distance_node'] = array(
            'nid' => $node->nid,
            'field' => $field_name,
            'coords' => $coords,
            'distlink' => $argtype['options']['distlink'],
            'unit' => $argtype['options']['maxdist']['unit'],
          );
        }
      }
      break;
    case 'link':
      $node = node_load(array('vid' => $query->vid));
      return l($query->node_title, $arg .'/'. $node->nid);
    case 'title':
      $node = node_load(intval($query));
      return $node ? check_plain($node->title) : '';
  }
}

function _gmaps_content_proximity_distance_node_option_value_process($element) {
  if (!empty($element['#default_value'])) {
    if (!is_array($element['#default_value'])) {
      $values = unserialize($element['#default_value']);
      if (empty($values)) {
        list($values['maxdist'], $values['operator'], $values['orderby'], $values['distlink']) = explode(',', $element['#default_value']);
      }
      $element['maxdist']['#default_value'] = $values['maxdist'];
      $element['operator']['#default_value'] = $values['operator'];
      $element['orderby']['#default_value'] = $values['orderby'];
      $element['distlink']['#default_value'] = $values['distlink'];
    } else {
      $element['maxdist']['#default_value'] = $element['#default_value']['maxdist'];
      $element['operator']['#default_value'] = $element['#default_value']['operator'];
      $element['orderby']['#default_value'] = $element['#default_value']['orderby'];
      $element['distlink']['#default_value'] = $element['#default_value']['distlink'];
    }
  }
  
  $element['operator']['#options'] = $element['operator']['#options']();

  return $element;
}

function _gmaps_content_proximity_distance_node_option_value_after_build($element) {
  $values = array();
  $values['maxdist'] = $element['maxdist']['#value'];
  $values['operator'] = $element['operator']['#value'];
  $values['orderby'] = $element['orderby']['#value'];
  $values['distlink'] = $element['distlink']['#value'];
  $element['#value'] = serialize($values);
  form_set_value($element, $element['#value']);
  return $element;
}

/**
 * Themeables
 */
function theme_gmaps_content_proximity_distance_node_option_value($element) {
  return $element['#children'];
}
