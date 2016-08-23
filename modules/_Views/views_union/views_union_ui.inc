<?php

/**
 * Implementation of hook_form_alter.
 */
function views_union_form_alter($form_id, &$form) {
  if ($form_id == 'views_edit_view') {
    $form['#submit']['views_union_edit_view_submit'] = array();

    $form['views-union'] = array(
      '#type' => 'fieldset',
      '#title' => t("Union"),
      '#theme' => 'views_union_edit_views',
      '#validate' => array('views_union_edit_view_validate' => array()),
      '#description' => "<p>".t("Using this interface, you can cause this view to be constructed by the UNION of two other views.  If you do this, then the filters you enter here will only be used if they are exposed in the subviews.  Also, the fields you enter here will be the ones that are shown, regardless of which fields are shown in the subviews.")."</p> <p>".t("If you set a limit that is greater than 0, then only that many rows will be pulled from that view into the union.")."</p> <p>".t("If you check 'ignore on filter', then that view will not be a part of the union if the user uses any of the exposed filters.")."</p>",
      '#collapsible' => true,
      '#collapsed' => false,
      '#tree' => true,
      '#parents' => array('views-union'),
      '#weight' => -1,
      'add_view' => array(
        '#tree' => true,
        '#parents' => array('views-union', 'add_view'),
        'view_to_add' => array(
          '#tree' => true,
          '#parents' => array('views-union', 'add_view', 'view_to_add'),
          '#type' => 'select',
          '#title' => t("Add a View"),
          '#options' => array(),
        ),
        'addview' => array(
          '#tree' => true,
          '#parents' => array('views-union', 'add_view', 'addview'),
          '#type' => 'button',
          '#value' => t("Add View"),
        ),
      ),
    );

    $master_view_id = $form['#parameters'][1]->vid;
    $result = db_query("SELECT vid, name, description, menu_title, page_title, block_title, url, page, menu, block FROM {view_view} WHERE vid != '%d' ORDER BY name", $master_view_id);

    $views = array();
    while ($row = db_fetch_array($result)) {
      $form['views-union']['add_view']['view_to_add']['#options'][$row['vid']] = $row['name'];
    } // while there's views to fetch

    $result = db_query(
      "SELECT id, 
          U.parent, P.name AS parent_name, 
          U.child, C.name AS child_name,
          U.lim,
          U.ignore_on_filter
       FROM {views_union} U
          LEFT JOIN {view_view} P ON U.parent=P.vid
          LEFT JOIN {view_view} C ON U.child=C.vid
       WHERE U.parent='%s'",
       $master_view_id);


    // Construct the list of things that we'll add when they hit save.
    $all_to_add = array();
    if (is_array($_POST['views-union']['add_view']['list_to_add'])) {
      $all_to_add = array_merge(
          $all_to_add, 
          $_POST['views-union']['add_view']['list_to_add']);
    } // if there's stuff, add it


    if (db_num_rows($result) <= 0 
        && count($all_to_add) <= 0 
        && !isset($_POST['views-union']['add_view']['view_to_add'])) 
    {
      $form['views-union']['#collapsed'] = true;
    } // if there's nothing to show, collapse it.

    $form['views-union']['unioned_views'] = array(
      '#type' => 'fieldset',
      '#collapsible' => false,
      '#tree' => true,
      '#parents' => array('views-union', 'unioned-views'),
    );
    while ($row = db_fetch_array($result)) {
      if (!$row['lim']) { $row['lim'] = 0; }

      _views_union_attach_view(
          $form, $row['id'], $row['child_name'], 
          $row['lim'], $row['ignore_on_filter']);
    } // while there are views unioned with this one


    // If they wanted to add a new one to the list, add it on.
    if ($_POST['op'] == t('Add View')) {
      $view_to_add = $_POST['views-union']['add_view']['view_to_add'];

      $all_to_add['add-'.$view_to_add.'-'.count($all_to_add)] = $view_to_add;

      drupal_set_message(t("You have modified this view; changes will not be recorded until you Save the form."));
    } // if we're supposed to add a view


    // Carry along all previous requests to add, to the next iteration of the
    // form.
    $form['views-union']['add_view']['list_to_add'] = array(
      '#tree' => true,
      '#parents' => array('views-union', 'add_view', 'list_to_add'),
    );
    $to_add = array();
    $add_templates = array();
    foreach ($all_to_add as $key => $val)
    {
      $add_ids[] = $val;
      $add_templates[] = "%s";
      $form['views-union']['add_view']['list_to_add'][$key] = array(
        '#type' => 'hidden',
        '#value' => $val
      );
    } // foreach one to carry

    // Get the names of all the views we were asked to carry.
    if ($add_ids) {
      $add_template = "'".implode("', '", $add_templates)."'";
      $view_names = array();
      $result = db_query(
        "SELECT V.vid, V.name
         FROM {view_view} V
         WHERE V.vid IN (".$add_template.")",
        $add_ids);
      while ($row = db_fetch_array($result)) {
        $view_names[$row['vid']] = $row['name'];
      } // while there's views to add

      // Make sure they get a table row outputted for them.
      foreach ($all_to_add as $key => $val) {
        _views_union_attach_view($form, $key, $view_names[$val]);
      } // foreach one to add
    } // if there's ids to add
  } // if it's the right form
} // function views_union_form_alter


/**
 * Themes the form 'views_edit_views'.
 * It puts the rows dealing with each unioned view into a table.
 */
function theme_views_union_edit_views(&$form) {
  $table = array();
  $hidden_deletes = array();

  $any = false;
  foreach ($form['unioned_views'] as $key => $val) {
    if (strpos($key, "#") === 0) { continue; }

    unset($val['limit']['#title']);
    unset($val['ignore_on_filter']['#title']);

    $subview_name = urlencode($val['name']['#prefix']);
    $deletebutton = $val['deletebutton'];
    if ($val['deletebutton']['#type'] == 'hidden') {
      $hidden_deletes[] = $val['deletebutton'];
    } else {
      $any = true;
      $row = array(
        l(check_plain($val['name']['#prefix']), 
            "admin/build/views/{$subview_name}/edit"),
        theme('textfield', $val['limit']),
        theme('checkbox', $val['ignore_on_filter']),
        theme_views_imagebutton($deletebutton),
      );
      $table[] = $row;
    } // if whether it was already deleted
  } // foreach form element

  if (!$any) {
    $row = array(
        array(
          'data' => t('(This view is not composed of any subviews)'),
          'colspan' => 4));
    $table[] = $row;
  } // if it's empty

  $headers = array(t("View Name"), t("Limit"), t("Ignore On Filter"), t("Ops"));

  $output = theme('table', $headers, $table);

  foreach ($hidden_deletes as $hidden_delete) {
    $output .= theme('hidden', $hidden_delete);
  } // foreach hidden_delete

  unset($form['add_view']['view_to_add']['#title']);
  $table = array(
    array(
      theme('select', $form['add_view']['view_to_add']),
      theme('button', $form['add_view']['addview']),
    ),
  );

  $headers = array(
    array(
      'data' => "Add a View",
      'colspan' => 2,
    ),
  );

  $output .= theme('table', $headers, $table);

  $addbythis .= drupal_render($form['add_view']['list_to_add']);
  $output .= $addbythis;
  
  return $output;
} // function theme_views


function views_union_edit_view_validate($form) {
  // form_set_error('', "Just a test.");

  // I should validate:
  // 1. That you're not trying to add a view to itself.
} // function views_union_validate

function views_union_edit_view_submit($form_id, $form) {
  if ($form_id != 'views_edit_view') {
    return;
  } // if it's not the right form

  if ($form['op'] == t('Add View')) {
    // Don't actually save it just now
    return;
  } // if they said to add a view

  if (!is_array($form['views-union']['add_view']['list_to_add'])) {
    $form['views-union']['add_view']['list_to_add'] = array();
  } // if it's not even an array, make sure it's just empty

  foreach ($form['views-union']['add_view']['list_to_add'] as $key => $val) {
    // Should be iterating over the list of ones to add
    $limit = $form['views-union']['unioned-views'][$key]['limit'];
    $ignore_on_filter = 
        $form['views-union']['unioned-views'][$key]['ignore_on_filter'];
        
    foreach ($form['views-union']['unioned-views'] as $ukey => $unioned_view) {
      if ($ukey == $key) {
        if (!$unioned_view['deletebutton']) {
          db_query(
            "INSERT INTO {views_union} (parent, child, lim, ignore_on_filter)
             VALUES ('%d', '%d', '%d', '%d')",
            array($form['vid'], $val, $limit, $ignore_on_filter));
        } // if it's been deleted previously
      } // if this unioned-view matches the key of list-to-add
    } // foreach unioned view
  } // foreach child to insert

  if (is_array($form['views-union']['unioned-views'])) {
    foreach ($form['views-union']['unioned-views'] as $key => $unioned_view) {
      if ($unioned_view['deletebutton']) {
        db_query(
          "DELETE FROM {views_union} WHERE id='%d'",
          array($key));
      } // if we should delete it
      else {
        db_query(
          "UPDATE {views_union} SET lim='%d', ignore_on_filter='%d'
           WHERE id='%d'",
          array($unioned_view['limit'], $unioned_view['ignore_on_filter'],
                $key));
      } // else we should update it
    } // foreach unioned view
  } // if it's an array
} // function views_union_edit_view_submit


function _views_union_attach_view(
    &$form, $id, $child_name, $lim=0, $ignore_on_filter=false)
{
  $form['views-union']['unioned_views'][$id] = array(
    '#type' => 'fieldset',
    '#collapsible' => false,
    '#tree' => true,
    '#parents' => array('views-union', 'unioned-views', $id),
    'name' => array(
      '#type' => 'hidden',
      '#prefix' => $child_name,
      '#default_value' => $id,
    ),
    'limit' => array(
      '#type' => 'textfield',
      '#size' => 4,
      '#title' => "Limit",
      '#default_value' => $lim,
    ),
    'ignore_on_filter' => array(
      '#type' => 'checkbox',
      '#title' => 'Ignore On Filter',
      '#default_value' => $ignore_on_filter,
    ),
  );

  $deletebutton = views_ui_add_button('user-trash.png', t('Delete'), t('Delete this item.'));
  if (strpos($_POST['op'], t("Save")) !== 0
   && $_POST['views-union']['unioned-views'][$id]['deletebutton'])
  {
    $deletebutton = array(
      '#type' => 'hidden',
      '#value' => 'delete',
    );
    drupal_set_message(t("You have modified this view; changes will not be recorded until you Save the form."));
  } // if the delete button was pressed

  $form['views-union']['unioned_views'][$id]['deletebutton'] = 
      $deletebutton;
} // function _views_union_attach_view
