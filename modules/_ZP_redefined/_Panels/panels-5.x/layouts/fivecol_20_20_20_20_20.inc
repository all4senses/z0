<?php
// $Id: fivecol_20_20_20_20_20.inc,v 1.5.6.7 2008/07/18 22:53:51 sdboyer Exp $


/**
 * implementation of hook_panels_layouts
 */
function panels_fivecol_20_20_20_20_20_panels_layouts() {
  $items['fivecol_20_20_20_20_20'] = array(
    'title' => t('Five column 20/20/20/20/20'),
    'icon' => 'layouts/threecol_33_34_33.png',
    'theme' => 'panels_fivecol_20_20_20_20_20',
    'css' => 'layouts/fivecol_20_20_20_20_20.css',
    'panels' => array(
      'left' => t('Left side'),
      'middle1' => t('Middle1'),
      'middle2' => t('Middle2'),
      'middle3' => t('Middle3'),
      'right' => t('Right side')
    ),
  );

  return $items;
}

/**
 * This function uses heredoc notation to make it easier to convert
 * to a template.
 */
function theme_panels_fivecol_20_20_20_20_20($id, $content) {
  if ($id) {
    $idstr = " id='$id'";
  }

  $output = <<<EOT
<div class="panel-display panel-5col-20 clear-block" $idstr>
  <div class="panel-panel panel-col-first">
    <div class="inside">$content[left]</div>
  </div>

  <div class="panel-panel panel-col">
    <div class="inside">$content[middle1]</div>
  </div>

  <div class="panel-panel panel-col">
    <div class="inside">$content[middle2]</div>
  </div>

  <div class="panel-panel panel-col">
    <div class="inside">$content[middle3]</div>
  </div>

  <div class="panel-panel panel-col-last">
    <div class="inside">$content[right]</div>
  </div>
</div>
EOT;
  return $output;
}

