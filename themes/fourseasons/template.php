<?php
// $Id: template.php,v 1.1.2.9 2008/03/09 18:39:08 derjochenmeyer Exp $

/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function phptemplate_body_class($sidebar_left, $sidebar_right) {
  if ($sidebar_left != '' && $sidebar_right != '') {
    $class = 'sidebars';
  }
  else {
    if ($sidebar_left != '') {
      $class = 'sidebar-left';
    }
    if ($sidebar_right != '') {
      $class = 'sidebar-right';
    }
  }

  if (isset($class)) {
    print ' class="'. $class .'"';
  }
}


function fourseasons_adminwidget($scripts) {

  if (empty($scripts)) { 
    print '
    <script type="text/javascript" src="'.base_path().'misc/jquery.js"></script>';
  }
    
  print '
    <script type="text/javascript">
    function toggle_style(color) {
      $("#header-image").css("background-color", color);
      $("#header-image").css("background-image", "none");
      $("h1").css("color", color);
      $("h2").css("color", color);
      $("h3").css("color", color);
      $("#headline a").css("color", color);
    }
    </script>
  
    <div id="farben">
      <span>try another color: </span>
      <a href="#" style="background-color:#FF9900;" onclick="toggle_style(\'#FF9900\');"></a>
      <a href="#" style="background-color:#003366;" onclick="toggle_style(\'#003366\');"></a>
      <a href="#" style="background-color:#990000;" onclick="toggle_style(\'#990000\');"></a>
      <a href="#" style="background-color:#CCCCCC;" onclick="toggle_style(\'#CCCCCC\');"></a>
      <a href="#" style="background-color:#006699;" onclick="toggle_style(\'#006699\');"></a>
      <a href="#" style="background-color:#000000;" onclick="toggle_style(\'#000000\');"></a>
    </div>

    <div id="font">
      <span style="margin-left:20px;">try another fontsize: </span>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'60%\');">60%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'70%\');">70%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'80%\');">80%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'90%\');">90%</a>
    </div>
  ';
  
  if (arg(0) == 'admin' && arg(1) == 'build' && arg(2) == 'themes') { 
    print '<img src="http://www.kletterfotos.de/autor.php">';
  }

}


/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' &rsaquo; ', $breadcrumb) .'</div>';
  }
}


/**
* Allow themable wrapping of all comments.
*/
function phptemplate_comment_wrapper($content, $type = null) {
    return '<div id="comments">'. $content . '</div>';
}


/**
 * Override or insert PHPTemplate variables into the templates.
 */
function _phptemplate_variables($hook, $vars) {
  if ($hook == 'page') {

    if ($secondary = menu_secondary_local_tasks()) {
      $output = '<span class="clear"></span>';
      $output .= "<ul class=\"tabs secondary\">\n". $secondary ."</ul>\n";
      $vars['tabs2'] = $output;
    }

    return $vars;
  }
  return array();
}


/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  $output = '';

  if ($primary = menu_primary_local_tasks()) {
    $output .= "<ul class=\"tabs primary\">\n". $primary ."</ul>\n";
  }

  return $output;
}


/*
 * theme_table($header, $rows, $attributes = array(), $caption = NULL)
 * includes/theme.inc, line 757
 * we modify this to give each table a class and to wrap it into a div
 * thus we can add "overflow: auto" to show scrollbars
 */
function phptemplate_table($header, $rows, $attributes = array(), $caption = NULL) {

  $output = '<div class="tablewrapper">';
  $output .= '<table'. drupal_attributes($attributes) ." class=\"tableclass\">\n";

  if (isset($caption)) {
    $output .= '<caption>'. $caption ."</caption>\n";
  }

  // Format the table header:
  if (count($header)) {
    $ts = tablesort_init($header);
    $output .= ' <thead><tr>';
    foreach ($header as $cell) {
      $cell = tablesort_header($cell, $header, $ts);
      $output .= _theme_table_cell($cell, TRUE);
    }
    $output .= " </tr></thead>\n";
  }

  // Format the table rows:
  $output .= "<tbody>\n";
  if (count($rows)) {
    $flip = array('even' => 'odd', 'odd' => 'even');
    $class = 'even';
    foreach ($rows as $number => $row) {
      $attributes = array();

      // Check if we're dealing with a simple or complex row
      if (isset($row['data'])) {
        foreach ($row as $key => $value) {
          if ($key == 'data') {
            $cells = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $cells = $row;
      }

      // Add odd/even class
      $class = $flip[$class];
      if (isset($attributes['class'])) {
        $attributes['class'] .= ' '. $class;
      }
      else {
        $attributes['class'] = $class;
      }

      // Build row
      $output .= ' <tr'. drupal_attributes($attributes) .'>';
      $i = 0;
      foreach ($cells as $cell) {
        $cell = tablesort_cell($cell, $header, $ts, $i++);
        $output .= _theme_table_cell($cell);
      }
      $output .= " </tr>\n";
    }
  }

  $output .= "</tbody></table>\n";
  $output .= "</div>\n";
  return $output;
} 







// my userbar

function custom_user_login_blocks() {
  $form = array(
    '#action' => url($_GET['q'], drupal_get_destination()),
    '#id' => 'user-login-form',
    '#base' => 'user_login',
  );
  $form['name'] = array('#type' => 'textfield',
    '#title' => t('Логин'),
    '#maxlength' => USERNAME_MAX_LENGTH,
    '#size' => 15,
    '#required' => TRUE,
  );
  $form['pass'] = array('#type' => 'password',
    '#title' => t('Пароль'),
    '#maxlength' => 60,
    '#size' => 15,
    '#required' => TRUE,
  );
  $form['submit'] = array('#type' => 'submit',
    '#value' => t('Ок!'),
  );
  /*
  $items = array();
  if (variable_get('user_register', 1)) {
    //$items[] = l(t('Create new account'), 'user/register', array('title' => t('Create a new user account.')));
  }
  //$items[] = l(t('Request new password'), 'user/password', array('title' => t('Request new password via e-mail.')));
  $form['links'] = array('#value' => theme('item_list', $items));
  */


  return $form;
}

