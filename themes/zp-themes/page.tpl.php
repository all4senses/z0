<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">

<head>
  <title><?php 
  //print str_replace('&amp;quot;', '"', $head_title);
  print $head_title;
  ?></title>
  <?php print $head ?>
  <meta name="google-translate-customization" content="da5def51822c590d-52306a432417a001-g019760249c69b8e3-13"></meta>

  <?php print $styles ?>
  
  <?php
  if(module_exists('javascript_aggregator')) {
    $scripts = javascript_aggregator_cache($scripts);
  }
  
  ?>

  <?php print $scripts ?>
  <!--[if lt IE 7]>
  <style type="text/css" media="all">@import "<?php print base_path() . path_to_theme() ?>/fix-ie.css";</style>
  <![endif]-->
</head>

<!-- toodoo-key: aWb9sDkWMN0CAxbPJ5EMz -->
<body<?php print phptemplate_body_class($sidebar_left, $sidebar_right); ?>>

<?php 
global $user;
//echo 'arg(0) = ' . arg(0);


// если браузер MS Internet Explorer 7.0 или ниже, не грузить сайт (из-за поломанной вёрстки)
/*
$browser = zp_functions_browser_info();
//echo '<PRE>';print_r($browser);echo '</PRE>';
//echo 'ver = ' . $browser[1][0] . '<br>';
if($browser[0] == 'IE' AND $browser[1][0] <= 6)
//if($browser[0] == 'Mozilla Firefox') // для теста
{
	echo '<div id="wrong_browser">Вы пытаетесь загрузить сайт с помощью браузера Internet Explorer версии 6.0 или ниже. <br><br>Для удобной работы с сайтом рекомендуем установить и использовать для работы с сайтом <a href="http://microsoft.com/rus/windows/internet-explorer/">более свежую версию браузера MS Internet Explorer</a> или какой-либо другой браузер, например, <a href="http://mozilla-russia.org/products/">Mozilla Firefox</a>, <a href="http://opera.com/">Opera</a> или <a href="http://google.com/chrome?hl=ru">Google Chrome</a>... </div>';
	return;
}
*/


//if(!$user->uid)
if(0)
{

 $output = '<div id="user-bar-notlogged" class="hid">';
        $output .= t('<p class="login-invite">Для входа на сайт введите свои данные...</p>');
        $output .= '<div class="input">' . drupal_get_form('custom_user_login_blocks') . '</div>'; 
        $output .= '</div>';

print $output;

}
else
{
//echo 'arg(0) = ' . arg(0);
if($user->uid != 1)
{
	switch(arg(0))	
	{
		case 'admin':
		case 'user':
		case 'taxonomy':
		case 'tracker':
		case 'revocation':
		case 'gtct':
		case 'themes':
                case 'filter':
			drupal_goto('http://www.zapokupkami.com/');
			
		case 'node':
			if(!arg(1) OR arg(1) == 'add' OR arg(2) == 'edit')
				drupal_goto('http://www.zapokupkami.com/');
	}

        $current_url = $_SERVER['REQUEST_URI'];
        if(
          strpos($current_url, 'admin/') !== false
           OR
          strpos($current_url, 'users/') !== false
           OR
          strpos($current_url, 'zpwrk/') !== false
           OR
          strpos($current_url, 'orderswrk/') !== false
           OR
          strpos($current_url , 'sites/') !== false
           OR
          strpos($current_url , 'inside/') !== false
           OR
          strpos($current_url , 'zpmaps/') !== false
           OR
          (
          !$user->uid
             AND //http://www.zapokupkami.com/private/vosstanovlenie-zabytogo-parolya?xdestination=/ 
            (strpos($current_url, 'private/') !== false AND strpos($current_url, 'private/vosstanovlenie-zabytogo-parolya') === false)
           )     
                
         )
         {
            drupal_goto('http://www.zapokupkami.com/');
         }
}
	
//if((arg(0) == 'admin' OR arg(0) == 'user' OR arg(0) == 'users' OR arg(0) == 'taxonomy' OR arg(0) == 'tracker' OR arg(0) == 'revocation'  OR arg(0) == 'gtct') AND $user->uid != 1)
	//drupal_goto('http://www.zapokupkami.com');

if(arg(1) == 1) // если первая страница
	print '<div id="head_back0" class="home"></div>';
else 
	print '<div id="head_back0"></div>';
?>

<div id="pagewrapper">
  <?php 
    if($sidebar_left)
    {
      print phptemplate_adminwidget($scripts); 
      print '<div id="headline"></div>';
    }   
  
/*  
    foreach($primary_links as $key => $value ) {
      if (ereg('active', $key)) {
        $primary_links[$key]['attributes']['class'] = "active";
      }
    } 
*/    
  
  if ($sidebar_left)
    print '<div id="header-image"> </div>';
 print $header 
 
 ?>

   <?php if ($sidebar_left): ?>
      <div id="sidebar-left" class="sidebar">
         <?php print $sidebar_left ?>
      </div>
    <?php endif; ?>
	<?php if($sidebar_left OR $sidebar_right): ?>
    	<div id="middle-content">
    <?php endif; ?>
    
        <?php 
        if($sidebar_left OR arg(0) == 'admin')  // показываем стандартные табы, только если включены левые блоки или мы в админской части
        {
          if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; 
          if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif;
          if ($tabs): print $tabs .'</div>'; endif;
          if (isset($tabs2)): print $tabs2; endif; 
        } 
        ?>
          
        <?php print $content ?>
    
    	<?php if ($sidebar_right): ?>
      		<div id="sidebar-right" class="sidebar">
        		<?php print $sidebar_right ?>
      		</div>
    	<?php endif; ?>
    	
  	<?php if($sidebar_left OR $sidebar_right): ?>
    	</div>
    <?php endif; ?>
   <div style="clear:both;"></div>

  <?php
  if($footer_message AND $sidebar_right)
    print '<div id="footer">' . $footer_message .'</div>';
?>

</div>
 
<div id="footer_back2"></div>

<?php
} // end of else ( if(!$user->uid) )

 echo $closure 
 
 ?>

</body>
</html>