<?php
// $Id: node_add_form.inc,v 1.1.2.6 2008/07/29 10:08:14 sdboyer Exp $


/**
 * @file contexts/node_add_form.inc
 *
 * Plugin to provide a node_add_form context
 */

function panels_node_add_form_panels_contexts() {
  $args['node_add_form'] = array(
    'title' => t("Node add form"),
    'description' => t('A node add form.'),
    'context' => 'panels_context_create_node_add_form',
    'settings form' => 'panels_context_node_add_form_settings_form',
    'keyword' => 'node_add',
    'context name' => 'node_add_form',
  );
  return $args;
}

/**
 * It's important to remember that $conf is optional here, because contexts
 * are not always created from the UI.
 */
function panels_context_create_node_add_form($empty, $data = NULL, $conf = FALSE) {
  $context = new panels_context(array('form', 'node_add', 'node_form'));
  $context->plugin = 'node_add_form';

  if ($empty) {
    return $context;
  }

  if ($conf) {
    $data = $data['type'];
  }

  if (!empty($data)) {
    $types = node_get_types();
    $type = str_replace('-', '_', $data);

    // Validate the node type exists.
    if (isset($types[$type]) && node_access('create', $type)) {
      // Initialize settings:
      global $user;
      $node = array('uid' => $user->uid, 'name' => $user->name, 'type' => $type);

      $form = drupal_retrieve_form($type . '_node_form', $node);
      drupal_process_form($type . '_node_form', $form);
      // In a form, $data is the object being edited.
      $context->data     = $type;
      $context->title    = $types[$type]->name;
      $context->argument = $type;

      // These are specific pieces of data to this form.
      // All forms should place the form here.
      $context->form       = $form;
      $context->form_id    = $type . '_node_form';
      $context->form_title = t('Submit @name', array('@name' => $types[$type]->name));
      $context->node_type  = $type;
      return $context;
    }
  }
}

function panels_context_node_add_form_settings_form($conf, $external = FALSE) {
  if ($external) {
    $options[0] = t('External source');
  }

  foreach (node_get_types() as $type => $info) {
    $options[$type] = $info->name;
  }

  $form['type'] = array(
    '#title' => t('Node type'),
    '#type' => 'select',
    '#options' => $options,
    '#default_value' => $conf['type'],
    '#description' => t('Select the node type for this form.'),
  );

  if ($external) {
    $form['type']['#description'] .= ' ' . t('Select external to require this from an external source (such as a containing panel page).');
  }

  return $form;
}

