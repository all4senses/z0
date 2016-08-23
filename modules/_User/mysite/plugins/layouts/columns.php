<?php
// $Id: columns.php,v 1.17 2008/04/06 23:08:27 agentken Exp $

/**
 * @file
 * A simple two-column page layout file for MySite.  Optional.
 *
 * @ingroup mysite_plugins
 */

/**
 * Implements theme_mysite_hook_layout()
 */
function theme_mysite_columns_layout($content) {
  // break the array into pieces
  $owner = $content['owner'];
  $mysite = $content['mysite'];
  $columns = mysite_layout_columns();
  $data = mysite_prepare_columns($mysite, $content['data'], $columns['count']);
  $header = $content['header'];
  // print the header message, if present
  if (isset($header)) {
    $output = '<div class="messages">'.  $header .'</div>';
  }
  // ajax-generated message class
  $output .= '<div class="mysite-ajax"></div>';
  // cycle through the data sets and make columns
  foreach ($data as $col => $set) {
    $output .= '<div class="mysite-sortable mysite-half-width" id="mysite-sort'. $col .'">';
    foreach ($set as $key => $value) {
    if ($value['mid'] && !$value['locked']) {
        $output .= '<div class="mysite-group collapsible sortable-item" id="m'. $value['mid'] .'">';
      }
      else {
        $output .= '<div class="mysite-group collapsible">';
      }
      $output .= '<span class="mysite-header">'. $value['title'] .'</span>';
      $output .= '<div class="mysite-content">';
      if (!empty($value['output'])) {
        if (!empty($value['output']['image'])) {
          $output .= $value['output']['image'];
        }
        $output .= theme('mysite_'. $value['format'] .'_item', $value['output']['items']);
      }
      else {
        $output .= t('<p>No content found.</p>');
      }
      $output .= '</div>';
      $output .= '<div class="mysite-footer">';
      if (!empty($value['output']['base'])) {
        $output .= '<div class="mysite-footer-left">'. l(t('Read more'), $value['output']['base']) .'</div>';
      }
      $output .= ' <div class="mysite-footer-right">'. $value['actions'] .'</div> ';
      $output .= '</div>';
      $output .= '</div>';
    }
    $output .= '</div>';
  }
  print theme('page', $output, variable_get('mysite_fullscreen', 1));
  return;
}

/**
 * Implements mysite_layout_hook()
 */
function mysite_layout_columns() {
  $data = array();
  $data['regions'] = array('0' => t('Left'), '1' => t('Right'));
  $data['count'] = count($data['regions']);
  $data['name'] = t('Two column');
  $data['description'] = t('Two equal-width columns.');
  $data['image'] = 'columns.png';
  return $data;
}
