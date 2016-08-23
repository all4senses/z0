This module adds a simple .js aggregation to Drupal 5.x.

It increases the performance of your site with less server requests per pageload. Especially in shared hosting environments this can be a huge boost.

This approach requires NO core-patching.

Please download the official release for the most stable version.

It basically does the following:

    * takes the $scripts variable and removes .js files using a regular expression
    * generates a unique filename for any set of .js files
    * creates one file containing all .js files in files/js
    * returns a modified $scripts variable with link to cached file 

To aggregate .js files

   1. Make sure you have site specific files directories setup in admin/settings/file-system.
   2. enable the module
   3. go to admin/settings/performance and enable the js cache (you'll also find an option to clear js cache)
   4a. place the following code inside your page.tpl.php file just before <?php print $scripts; ?> 

<?php
  if(module_exists('javascript_aggregator')) {
    $scripts = javascript_aggregator_cache($scripts);
  }
?>

   4b. If you prefer to use template.php instead of page.tpl.php, place the following code in the variables function: 
<?php
  function _phptemplate_variables($hook, $vars) {
    if(module_exists('javascript_aggregator') && $vars['scripts']) {
      $vars['scripts'] = javascript_aggregator_cache($vars['scripts']);
    }
    return $vars;
  }
?>

   5. If you'd like to compress the aggregated JavaScript file, enable "Minify with JSMin" or "GZip JavaScript" in the performance settings.


Comments welcome.

If you have suggestions, bugs, feature requests or if just like the module, you can create an issue.

Initial release was created for: http://routebook.com