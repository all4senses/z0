<?php

/**
 * Return function for the 'custom' content type.
 */
function panels_content_custom_block($conf) {
  $title      = filter_xss_admin($conf['title']);
  $css_id     = filter_xss_admin($conf['css_id']);
  $css_class  = filter_xss_admin($conf['css_class']);
  $body       = check_markup($conf['body'], $conf['format'], FALSE);
  return array(
    'title' => $title,
    'body' => $body,
    'css_id' => $css_id,
    'css_class' => $css_class,
  );
}
