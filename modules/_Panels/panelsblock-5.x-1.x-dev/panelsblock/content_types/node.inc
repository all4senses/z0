<?php

/**
 * Output function for the 'node' content type. Outputs a node
 * based on the module and delta supplied in the configuration.
 */
function panels_content_node_block($conf) {
  $node = node_load($conf['nid']);
  if (!node_access('view', $node)) {
    return;
  }

  if ($conf['suppress_title']) {
    // $node->title = '';
  }

  $output = node_view($node, $conf['teaser'], FALSE, $conf['links']);
  return array(
    'title' => $node->title,
    'body' => $output,
  );
}
