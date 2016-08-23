<?php

/**
 * Return function for the 'block' content type. Outputs a block
 * based on the module and delta supplied in the configuration.
 */
function panels_content_block_block($conf) {
  $block = (object) module_invoke($conf['module'], 'block', 'view', $conf['delta']);
  $block->module = $conf['module'];
  $block->delta = $conf['delta'];
  if ($conf['override_title']) {
    $block->subject = check_plain($conf['override_title_text']);
  }

  // Test for block visibility
  $result = db_query("SELECT pages, visibility FROM {blocks} WHERE module = '%s' AND delta = %d", $block->module, $block->delta);
  $block_visibility = db_fetch_object($result);

  if ($block_visibility && $block_visibility->pages) {
    if ($block_visibility->visibility < 2) {
      $path = drupal_get_path_alias($_GET['q']);
      $regexp = '/^('. preg_replace(array('/(\r\n?|\n)/', '/\\\\\*/', '/(^|\|)\\\\<front\\\\>($|\|)/'), array('|', '.*', '\1'. preg_quote(variable_get('site_frontpage', 'node'), '/') .'\2'), preg_quote($block->pages, '/')) .')$/';
      $page_match = !($block->visibility xor preg_match($regexp, $path));
    } else {
      $page_match = drupal_eval($block->pages);
    }
  } else {
    $page_match = TRUE;
  }

  if ($page_match) {
    return array(
      'title' => $block->subject,
      'body' => $block->content,
    );
  }
  return array();
}

