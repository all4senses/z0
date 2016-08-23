<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language ?>" lang="<?php print $language ?>">
  <head>
    <?php print $head ?>
    <title><?php print $head_title ?></title>
    <?php print $styles ?>
    <?php print $scripts ?>
    <!--<style type="text/css" media="print">@import "<?php print base_path() . path_to_theme() ?>/print.css";</style>-->
    <!--[if lt IE 7]>
    <style type="text/css" media="all">@import "<?php print base_path() . path_to_theme() ?>/fix-ie.css";</style>
    <![endif]-->
  </head>
  <body<?php print phptemplate_body_class($sidebar_left, $sidebar_right); ?>>

<!-- Layout -->


<?php 
global $user;

if(!$user->uid)
{


 $output = '<div id="user-bar-notlogged">';
        $output .= t('<p class="login-invite">Для пользования сервисом введите свои данные...</p>');
        $output .= '<div class="input">' . drupal_get_form('custom_user_login_blocks') . '</div>'; 
        $output .= '</div>';
        
     	




print $output;

}
else
{

?>





<div id="pagewrapper">

  <?php print fourseasons_adminwidget($scripts); ?>

  <div id="headline">
    <?php
      if ($site_slogan) {
        $site_slogan = '<div id="site-slogan">'.$site_slogan.'</div>';
      } 
      else {
        $site_slogan = '';
      }
      
      if ($logo || $site_name) {
        print '<a href="'. check_url($base_path) .'" title="'. $site_name .'">';
        if ($logo) {
          //print '<img src="'. check_url($logo) .'" alt="'. $site_title .'" id="logo" />';
        }
        print $site_name .'</a>';
        print $site_slogan;
      } else {
        print '<div style="clear:both; height:20px;"></div>';
      }
    ?>
  </div>
  
  <?php 
    foreach($primary_links as $key => $value ) {

// my changes

      //if (ereg('active', $key)) {
      if (preg_match('/active/', $key)) {


        $primary_links[$key]['attributes']['class'] = "active";
      }
    } 
  ?>

  <div id="navigation-primary">
        <?php if (isset($primary_links)) : ?>
          <?php print theme('links', $primary_links, array('class' => 'links primary-links')) ?>
        <?php endif; ?>
    <div style="clear:both;"></div>
  </div>

  <div id="navigation-secondary">
        <?php 
          if (isset($secondary_links) && !empty($secondary_links)) {
            print theme('links', $secondary_links, array('class' => 'links secondary-links'));
          }
          else {
            print '<ul class="links secondary-links"><li style="border:none;">&nbsp;</li></ul>';
          }
        ?>
    <div style="clear:both;"></div>
  </div>

  <div id="header-image">
    <?php
      if (!empty($mission)) {
        print '<div id="site-mission">'.$mission.'</div>';
      }
    ?>
  </div>


  <div id="navigation-breadcrumb">
    <?php if ($breadcrumb) { print $breadcrumb; } else { print '<div class="breadcrumb"><a href="#">&nbsp;</a></div>'; } ?>
  </div>

  <div style="clear:both;"></div>

  <div id="contentwrapper">
    <?php if ($sidebar_left): ?>
      <div id="sidebar-left" class="sidebar">
          <?php if ($search_box): ?><div class="block block-theme"><?php print $search_box ?></div><?php endif; ?>
          <?php print $sidebar_left ?>
      </div>
    <?php endif; ?>

    <div id="middle-content">
      <div class="content-padding">
          <?php if ($tabs): print '<div id="tabs-wrapper" class="clear-block">'; endif; ?>
          <?php if ($title): print '<h2'. ($tabs ? ' class="with-tabs"' : '') .'>'. $title .'</h2>'; endif; ?>
          <?php if ($tabs): print $tabs .'</div>'; endif; ?>

          <?php if (isset($tabs2)): print $tabs2; endif; ?>

          <?php if ($help): print $help; endif; ?>
          <?php if ($messages): print $messages; endif; ?>
          <?php print $content ?>
          <span class="clear"></span>
          <?php print $feed_icons ?>

          <div style="clear:both;"></div>
      </div>
    </div>

    <?php if ($sidebar_right): ?>
      <div id="sidebar-right" class="sidebar">
        <?php if (!$sidebar_left && $search_box): ?><div class="block block-theme"><?php print $search_box ?></div><?php endif; ?>
        <?php print $sidebar_right ?>
      </div>
    <?php endif; ?>
  </div>

  <div style="clear:both;"></div>

  <div id="footer"><?php print $footer_message ?></div> 

</div>

<?php print $closure ?>




<?php 

} // end of else ( if(!$user->uid) )

?>






  </body>
</html>
