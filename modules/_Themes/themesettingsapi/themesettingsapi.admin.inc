<?php
// $Id: themesettingsapi.admin.inc,v 1.1.6.3 2007/12/04 01:09:26 johnalbin Exp $

/**
 * @file
 * Implements the module's admin forms and the Theme Settings API on the Theme
 * settings forms.
 */

/**
 * Implementation of hook_form_alter().
 *
 * @param string $form_id
 * @param &array $form
 * @return void
 */
function themesettingsapi_form_alter($form_id, &$form) {
  switch ($form_id) {
    case 'system_theme_settings':
      // Grab the specific name of the theme settings form
      $key = $form['var']['#value'];
      $key = ($key == 'theme_settings') ? '' : preg_replace('/(^theme_|_settings$)/', '', $key);

      // Since we are allowing more settings, make logo and favicon collapsible
      if (empty($key)) {
        // Fix for small bug in Drupal 5.1
        $form['theme_settings']['#prefix'] = '<div class="clear-block">'. $form['theme_settings']['#prefix'];
        $form['node_info']['#suffix'] = $form['node_info']['#suffix'] .'</div>';
        if (isset($form['logo'])) {
          unset($form['logo']['#attributes']['class']);
        }
      }
      if (isset($form['logo'])) {
        $form['logo']['#collapsible'] = TRUE;
        $form['logo']['#collapsed'] = FALSE;
      }
      if (isset($form['favicon'])) {
        $form['favicon']['#collapsible'] = TRUE;
        $form['favicon']['#collapsed'] = FALSE;
        // Fix for small bug in Drupal 5.1
        if (isset($form['favicon']['text']['#value'])) {
          $form['favicon']['#descripton'] = $form['favicon']['text']['#value'];
          unset($form['favicon']['text']);
        }
      }

      // Move submit buttons to bottom
      $form['buttons']['#weight'] = 1;

      // Template-specific settings
      if ($key) {
        // If the administration theme is not used, switch themes when displaying the theme settings.
        if (variable_get('admin_theme', '0') == '0' or variable_get('theme_settings_admin_theme', '1') == '0') {
          global $custom_theme;
          $custom_theme = $key;
          init_theme();
        }

        // Include the theme's theme-settings.php file
        $themes = system_theme_data();
        // if (!empty($themes[$key]->base_theme)
        $filename = './'. str_replace('/'. $themes[$key]->basename, '', $themes[$key]->filename) .'/theme-settings.php';
        if (!file_exists($filename)) {
          // If the theme doesn't have a theme-settings.php file, use the base theme's.
          $base = explode('/', strrev($themes[$key]->owner), 2);
          $filename = './'. strrev($base[1]) .'/theme-settings.php';
        }
        if (file_exists($filename)) {
          require_once $filename;
        }
        else {
          // Backwards compatibility with 5.x-2.0: allow a settings.php file.
          $filename = './'. str_replace('/'. $themes[$key]->basename, '', $themes[$key]->filename) .'/settings.php';
          if (!file_exists($filename)) {
            // If the theme doesn't have a settings.php file, use the base theme's.
            $base = explode('/', strrev($themes[$key]->owner), 2);
            $filename = './'. strrev($base[1]) .'/settings.php';
          }
          if (file_exists($filename)) {
            require_once $filename;
          }
        }

        // Since we are adding more settings, make logo and favicon collapsed
        if (isset($form['logo'])) {
          $form['logo']['#collapsed'] = TRUE;
        }
        if (isset($form['favicon'])) {
          $form['favicon']['#collapsed'] = TRUE;
        }

        // Get the theme settings
        $settings = theme_get_settings($key);

        // Unset the results of the broken 5.x API.
        unset($form['specific']);

        // Call engine-specific settings.
        $function = $themes[$key]->prefix .'_engine_settings';
        if (function_exists($function)) {
          $group = $function($settings);
          if (!empty($group)) {
            $form['engine_specific'] = array('#type' => 'fieldset', '#title' => t('Theme-engine-specific settings'), '#description' => t('These settings only exist for all the templates and styles based on the %engine theme engine.', array('%engine' => $themes[$key]->prefix)));
            $form['engine_specific'] = array_merge($form['engine_specific'], $group);
          }
        }

        // Call theme-specific settings.
        $function = $key .'_settings';
        if (!function_exists($function)) {
          $function = $themes[$key]->prefix .'_settings';
        }
        if (function_exists($function)) {
          $group = $function($settings);
          if (!empty($group)) {
            $form['theme_specific'] = array('#type' => 'fieldset', '#title' => t('Theme-specific settings'), '#description' => t('These settings only exist for the %theme theme and all the styles based on it.', array('%theme' => $key)));
            $form['theme_specific'] = array_merge($form['theme_specific'], $group);
          }
        }
      }
      break;

    case 'system_admin_theme_settings':
      // Add a setting to allow theme switching when editing a node (backport of D6 setting)
      $form['node_admin_theme'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use administration theme for content editing'),
        '#description' => t('Use the administration theme when editing existing posts or creating new ones.'),
        '#default_value' => variable_get('node_admin_theme', '0'),
      );

      // Add a setting to allow theme switching even with an admin theme
      $form['theme_settings_admin_theme'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use administration theme when configuring theme settings'),
        '#description' => t('If this setting is disabled or if using the "System default" theme, the theme settings pages will be switched to the theme being configured.'),
        '#default_value' => variable_get('theme_settings_admin_theme', '1'),
      );

      // Move submit buttons to bottom
      $form['buttons']['#weight'] = 1;
      break;
  }
}
