<?php

/**
 * @author Ryan Hughes <ryan@linuxbox.com>
 */

function theme_taxonomygroup_group_teaser($view, $nodes, $type) {
  return theme("taxonomygroup_group", $view, $nodes, $type, true);
} // funciton theme_taxonomygroup_taxonomygroup_teaser


function theme_taxonomygroup_group($view, $nodes, $type, $teaser=false) {
  drupal_add_css(drupal_get_path('module', 'views_group_pack').'/taxonomygroup.css');

  if (isset($view->sort[0]['field'])) {
    $sort_field = $view->sort[0]['field'];
  } // if it's got a sort field

  if ($sort_field != 'term_data.weight') {
    drupal_set_message(t("The view must be sorted first by a 'taxonomy term' field, in order to be Grouped By Taxonomy Term."), 'error');
    return theme('views_view_teasers', $view, $nodes, $type);
  } // if the sort field isn't set

  $by_taxonomy = array();
  foreach ($nodes as $proto_node) {
    $node = node_load($proto_node->nid);
    $val = array_values($node->taxonomy);
    $term = $val[0]->name;
    $by_taxonomy[$term][] = $node;
  } // foreach node

  return theme("taxonomygroup_taxonomygroup", $view, $by_taxonomy, $type, $teaser);
} // function theme_taxonomygroup_taxonomygroup


/**
 * Theme all the groups.
 * @param object $view The view that's showing these nodes.
 * @param array $by_taxonomy array('term' => array(node1, node2, ...), ...)
 * @param mixed $type ...
 * @param boolean $teaser Are we printing full nodes (t) or teasers (f)?
 */
function theme_taxonomygroup_taxonomygroup($view, $by_taxonomy, $type, $teaser) {
  $output = "<div id=\"taxonomygroup\" class=\"taxonomygroup\">";
  foreach ($by_taxonomy as $term => $termlist) {
    $output .= theme('taxonomygroup_term', $termlist, $term, $teaser);
  } // foreach term
  $output .= "</div><!-- taxonomygroup -->";

  return $output;
}


/**
 * Theme a list of nodes associated with a term.
 * @param array $termlist array(node1, node2, ...)
 * @param string $term The name of the taxonomy term.
 * @param boolean $teaser Are we printing full nodes (t) or teasers (f)?
 */
function theme_taxonomygroup_term($termlist, $term, $teaser=false) {
  $output = "";
  $output .= "<div id=\"taxonomygroup-{$term}-header\" class=\"taxonomygroup-header taxonomygroup-{$term}-header\">{$term}</div>";
  $output .= "<div class=\"taxonomygroup-content taxonomygroup-{$term}-content\">";
  foreach ($termlist as $node) {
    $output .= node_view($node, $teaser, false, true);
  } // foreach month
  $output .= "</div>";
  return $output;
} // function theme_taxonomygroup_term
