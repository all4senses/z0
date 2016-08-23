<?php
// Only valid if views module loaded.
if (module_exists('views')) {

  /**
   * Output function for the 'views' content type. Outputs a views
   * based on the module and delta supplied in the configuration.
   */
  function panels_content_views_block($conf) {
    $view = views_get_view($conf['view']);
    if ($view) {

      if (function_exists('views_access') && !views_access($view)) {
        return NULL;
      }

      $arguments = explode('/', $_GET['q']);
      $args = $conf['args'];
      
      foreach ($arguments as $id => $arg) {
        $args = str_replace("%$id", $arg, $args);
      }

      $args = preg_replace('/\\/%\d/', '', $args);
      $args = $args ? explode('/', $args) : array();

      if ($conf['url']) {
        $view->url = $conf['url'];
      }

      $content = views_build_view($conf['type'], $view, $args, intval($conf['pager_id']), intval($conf['nodes_per_page']));

      $title = views_get_title($view, $conf['type']);

    }
    $block = array(
      'title' => $title ? $title : 'title',
      'body' => $content,
    );

    return $block;
  }
}
