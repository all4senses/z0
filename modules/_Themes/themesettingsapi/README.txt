THEME SETTINGS API

  This module simply extends an old, undocumented API that is already in Drupal
  (introduced in Drupal 4.2):

    If a theme is its own theme engine (a "Plain PHP theme"), it can add its own
    settings to the /admin/build/themes/settings/mytheme page by adding a
    mytheme_settings() function to its mytheme.theme file.

  The API for this module has been added to the core of Drupal in version 6.x,
  so using this module is forward-compatible with the next version of Drupal.


DOCUMENTATION

  Full documentation on how to integrate your theme with this API, can be found
  in the Theme Developer's Handbook:

    http://drupal.org/node/177868

  Also, see the included sample files, theme-settings.php and template.php.


ABOUT THE PROJECT

  It is currently impossible for themes (like PHPtemplate-based ones) to add
  settings to the theme settings page without coding a module. There needs to be
  an API to facilitate that. No one has successfully implemented this (See
  issues 54990, 56713 and 57676.)

  The Theme Settings API project created a fully functioning implementation of
  a custom theme settings API for Drupal. This API should reside in core and
  this project successfully championed the addition of an updated API into
  Drupal 6 (see issue 57676 at http://drupal.org/node/57676.)
