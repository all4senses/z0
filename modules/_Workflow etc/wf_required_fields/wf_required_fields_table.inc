<?php

// $Id: wf_required_fields_table.inc,v 1.1 2007/11/05 15:23:39 traxer Exp $

/**
 * This function is called from _element_info() while bilding form elements.
 * 
 * The result is merged to elements of appropriate types (e.g. if '#type' is
 * 'table', $types['table'] is merged into the element). Most important is
 * the '#process' element, this names the function that is called by
 * form_builder() when building the form.
 */
function wf_required_fields_elements() {
  $types = array(
    'wf_required_fields_table' => array(
      '#input' => false,
      '#process' => array('wf_required_fields_element_table' => array()),
      '#header' => array(
        '#type' => 'header',
      )
    ),
    'wf_required_fields_header' => array(
      '#input' => false,
      '#process' => array('wf_required_fields_element_header' => array()),
    ),
    'wf_required_fields_row' => array(
      '#input' => false,
      '#process' => array('wf_required_fields_element_row' => array()),
    ),
    'wf_required_fields_cell' => array(
      '#input' => false,
      '#rowspan' => 1,
      '#colspan' => 1,
      '#header' => null,
      '#process' => array('wf_required_fields_element_cell' => array()),
    ),
  );
  return $types;
}

/* 
** Processing functions
*/

/**
 * This function is called by form_builder when building a form element, that
 * has '#process' set to 'wf_required_fields_table_form_table'.
 * 
 * It returns the built form element. Most notably it specifies the function
 * used to theme the form element.
 */
function wf_required_fields_element_table($element) {
  $element['#theme'] = 'wf_required_fields_table_table';
  $element['#header'] = form_builder('wf_required_fields_element_header', $element['#header']);
  return $element;
}

/**
 * @see wf_required_fields_element_table()
 */
function wf_required_fields_element_header($element) {
  $element['#theme'] = 'wf_required_fields_table_row';
  foreach(element_children($element) as $key) {
    if ($element[$key]['#type'] == 'wf_required_fields_cell') {
      $element[$key]['#header'] = true;
    }
  }
  return $element;
}

/**
 * @see wf_required_fields_element_table()
 */
function wf_required_fields_element_row($element) {
  $element['#theme'] = 'wf_required_fields_table_row';
  return $element;
}

/**
 * @see wf_required_fields_element_table()
 */
function wf_required_fields_element_cell($element) {
  $element['#theme'] = 'wf_required_fields_table_cell';
  return $element;
}

/* 
** Themeing functions
*/

/**
 * This function themes a form element of '#type' 'wf_required_fields_table' 
 */
function theme_wf_required_fields_table_table($element) {
  $rows = array();

  $head = theme($element['#header']['#theme'], $element['#header']);

  foreach (element_children($element) as $key) {
    if (isset($element[$key]['#type']) && $element[$key]['#type'] == 'wf_required_fields_row') {
      $rows[] = theme($element[$key]['#theme'], $element[$key]);
    }
  }
  
  $title = null;
  if (isset($element['#title']) && $element['#title'] != '' ) {
    $title = $element['#title'];
  }

  $attributes = array();
  if (isset($element['#attributes']) && is_array($element['#attributes'])) {
    $attributes = $element['#attributes'];
  }
  if (isset($attributes['class']) && isset($attributes['class']) != '') {
    $attributes['class'] .= ' form-table';
  } else {
    $attributes['class'] = 'form-table';
  }

  $result = theme('table', $head['data'], $rows, $attributes, $title);
  return $result;
}

/**
 * This function themes a form element of '#type' 'wf_required_fields_row' and
 * 'wf_required_fields_header'.
 */
function theme_wf_required_fields_table_row($element) {
  $data = array();
  foreach(element_children($element) as $key) {
    if (isset($element[$key]['#type']) && $element[$key]['#type'] == 'wf_required_fields_cell') {
      $data[] = theme($element[$key]['#theme'], $element[$key]);
    }
  }
  $row = array( 'data' => $data, );
  $attributes = array();
  if (isset($element['#attributes']) && is_array($element['#attributes'])) {
    unset($element['#attributes']['data']);
    $attributes = $element['#attributes'];
  }
  $row = array_merge($row, $attributes);
  return $row;
}

/**
 * This function themes a form element of '#type' 'wf_required_fields_cell'
 */
function theme_wf_required_fields_table_cell($element) {
  $data = '';
  foreach (element_children($element) as $key) {
    $data .= drupal_render($element[$key]);
  }
  $cell = array(
    'data' => $data,
    'header' => $element['#header'],
  );
  $attributes = array();
  if ($element['#rowspan'] != 1) {
    $attributes ['rowspan'] = $element['#rowspan'];
  }  
  if ($element['#colspan'] != 1) {
    $attributes ['colspan'] = $element['#colspan'];
  }
  if (isset($element['#attributes']) && is_array($element['#attributes'])) {
    unset($element['#attributes']['data']);
    unset($element['#attributes']['header']);
  }
  $cell = array_merge($cell, $attributes, $element['#attributes']);
  return $cell;
}

/**
 * Theme the table
 */
function theme_wf_required_fields_table($element) {
  return $element['#children'];
}
