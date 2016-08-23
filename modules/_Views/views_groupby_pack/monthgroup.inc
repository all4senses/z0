<?php

/**
 * @author Ryan Hughes <ryan@linuxbox.com>
 */

function theme_monthgroup_group_teaser($view, $nodes, $type) {
  return theme("monthgroup_group", $view, $nodes, $type, true);
} // funciton theme_monthgroup_monthgroup_teaser


function theme_monthgroup_group($view, $nodes, $type, $teaser=false) {
  $output = "";

  drupal_add_css(drupal_get_path('module', 'views_group_pack').'/monthgroup.css');

  if (isset($view->sort[0]['field'])) {
    $sort_field = $view->sort[0]['field'];
    $matches = array();
    if (strpos($sort_field, "node.") === 0) {
      $sort_field = substr($sort_field, 5);
    } else if (preg_match('/^[^\.]*\.(.*?)_value$/', $sort_field, $matches)) {
      $sort_field = $matches[1];
    }
  } // if it's got a sort field

  if (!isset($sort_field)) {
    drupal_set_message(t("The view must be sorted first by a date field, in order to be Grouped By Month."), 'error');
    return theme('views_view_teasers', $view, $nodes, $type);
  } // if the sort field isn't set

  $by_month = array();
  include_once(drupal_get_path('module', 'date_api') .'/date.inc');
  foreach ($nodes as $proto_node) {
    $node = node_load($proto_node->nid);
    $val = $node->$sort_field;
    if (is_array($val)) {
      if (function_exists('date_custom2unix')) {
        $val = date_custom2unix($val[0]['value'], "Y-m-dTH:i:s");
      } else {
        $val = strtotime($val[0]['value']);
      }
    }
    // if it's not an array, it's probably a simple unix timestamp.

    // format_date doesn't handle pre-1970 dates, so we'll use date_format_date
    // in that case.  But both of these provide translation.
    if (function_exists('date_format_date')) {
      $year = date_format_date('Y', $val);
      $month = date_format_date('F', $val);
    } else {
      $year  = format_date($val, 'custom', 'Y', 0);
      $month = format_date($val, 'custom', 'F', 0);
    }


    $by_month[$year][$month][] = $node;
  } // foreach node
  
  return theme("monthgroup_monthgroup", $view, $by_month, $type, $teaser);
}

function theme_monthgroup_monthgroup($view, $by_month, $type, $teaser=false) {
  $output = "<div id=\"monthgroup\" class=\"monthgroup\">";
  foreach ($by_month as $year => $monthlist) {
    $output .= theme('monthgroup_year', $monthlist, $year, $teaser);
  } // foreach year
  $output .= "</div><!-- monthgroup -->";

  return $output;
} // function theme_monthgroup_monthgroup


function theme_monthgroup_year($monthlist, $year, $teaser=false) {
  $output = "";
  $output .= "<div id=\"yeargroup-{$year}\" class=\"yeargroup yeargroup-{$year}\">";
  foreach ($monthlist as $month => $nodelist) {
    $output .= theme('monthgroup_month', $nodelist, $month, $year, $teaser);
  } // foreach month
  $output .= "</div><!-- yeargroup-{$year} -->";
  return $output;
} // function theme_monthgroup_year

function theme_monthgroup_month($nodelist, $month, $year, $teaser) {
  $output .= "<div id=\"monthgroup-{$year}-{$month}\" class=\"monthgroup monthgroup-{$month} monthgroup-{$year}-{$month}\">";
  $output .= "<div id=\"monthgroup-{$year}-{$month}-header\" class=\"monthgroup-header monthgroup-{$year}-{$month}-header\">{$month} {$year}</div>";
  $output .= "<div class=\"monthgroup-content monthgroup-{$year}-{$month}-content\">";
  foreach ($nodelist as $node) {
    $output .= node_view($node, $teaser, false, true);
  } // foreach node
  $output .= "</div><!-- monthgroup-content -->";
  $output .= "</div> <!-- monthgroup -->";
  return $output;
} // function theme_monthgroup_month
