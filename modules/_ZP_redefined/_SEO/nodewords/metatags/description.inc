<?php
// $Id: description.inc,v 1.1.2.2 2008/06/18 09:58:13 robrechtj Exp $

/**
 * @file
 * Support file for DESCRIPTION meta tag.
 */

function nodewords_description_prepare($type, $ids, $value, $settings) {
    if ((!isset($value) || empty($value)) && $settings['use_teaser'] && count($ids) == 1) {
        switch ($type) {
            case 'node':
                $node = node_load($ids[0]);
                if ($node && node_access('view', $node)) {
                    // HACK: see http://drupal.org/node/79315 (can't use php functions in
                    // body of nodes). Note that this still won't work if you have a
                    // CCK field with PHP format.
                    if (filter_format_allowcache($node->format)) {
                        // We would like to use node_view($node, TRUE, FALSE), unfortunately
                        // this uses theme_node() which, by default, adds 'Posted by ...'
                        // information (which we don't want). The code below calls all
                        // functions node_view() does, without the theme.
                        //$node = node_build_content($node, TRUE, FALSE);
                        //$content = drupal_render($node->content);
                        //$node->teaser = $content;

                        node_invoke_nodeapi($node, 'alter', TRUE, FALSE);
                    }

                    /*
                      echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-------------------------';
                      echo '<PRE>';
                      print_r($node);
                      echo '</PRE>';
                      echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-------------------------';
                     */
                    //echo '11111111111 <br>';
                    if (strpos($node->type, 'product') !== FALSE) {
                        foreach ($node->taxonomy as $product_group_tid => $product_group)
                            $_SESSION['cur_parent_podrguppa_name'] = $product_group = $product_group->name;


                        $cur_parent_otdel_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $product_group_tid));
                        $_SESSION['cur_parent_otdel_name'] = $cur_parent_otdel_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d", $cur_parent_otdel_tid));

                        global $user;
                        if (!$_SESSION['current_shop_type_sp2']) {
                            
                            //$cur_shop_info = zp_functions_shop_info($_SESSION['current_city'], $user->uid);

                            $target_zp_id = db_result(db_query("SELECT model FROM {uc_products} WHERE nid = %d", $node->nid));
                            $shop_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", substr($target_zp_id, 0, 8)));
                            $cur_shop_info = zp_functions_shop_info(0, 0, $shop_nid);

                            $cur_city_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", substr($target_zp_id, 0, 4)));
                            $cur_city_name = db_result(db_query("SELECT title from {node} WHERE nid  = %d", $cur_city_nid));
                            $_SESSION['current_city_name'] = $cur_city_name;

                            $_SESSION['current_shop_name'] = $cur_shop_info['shop_name'];
                            $_SESSION['current_shop_type_sp2'] = $cur_shop_info['shop_type_spell_2'];
                        }
                            
                        //$value = "Служба доставки 'За Покупками!' оперативно доставит Вам товар '" . str_replace('"', "``", $node->title) . "' из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . "." . $node->teaser;
                        
                        
                        /*
                         
                        // translate the title from uk to ru for description
                        
                        $tovar = explode(';', $node->field_prodtype_pic_src_n_num[0]['value']);
                        if($tovar[3] AND $tovar[3] != '=')
                            $value = $tovar[3] . ". Служба доставки 'За Покупками!' оперативно доставит Вам этот товар из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . "." . $node->teaser;
                        else
                            $value = "Служба доставки 'За Покупками!' оперативно доставит Вам товар '" . str_replace('"', "``", $node->title) . "' из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . "." . $node->teaser;
                        */  
                        
                        if(!$translate_object = db_fetch_object(db_query("SELECT * FROM {titles_translations} WHERE nid = %d", $node->nid))
                                OR 
                            !$translate_object->ru
                                OR
                            $translate_object->ru == '='
                          )
                            $value = "Служба доставки 'За Покупками!' оперативно доставит Вам товар '" . str_replace('"', "``", $node->title) . "' из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . "." . $node->teaser;
                        else
                           $value = $translate_object->ru . ". Служба доставки 'За Покупками!' оперативно доставит Вам этот товар из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . "." . $node->teaser;
                            
                        //$value = "Служба доставки 'За Покупками!' - доставка на дом и в офис товара '" . str_replace('"', "``", $node->title) . "' из подгруппы '" . $product_group . "' отдела '" . $cur_parent_otdel_name .  "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "'. " . $node->teaser;
                    } else if ($node->type == 'c_department') {
                        global $user;
                        if (!$_SESSION['current_shop_type_sp2']) {

                            //$cur_shop_info = zp_functions_shop_info($_SESSION['current_city'], $user->uid);
                            //echo 'shop = ' . $_SESSION['current_shop'];


                            $target_zp_id = db_result(db_query("SELECT field_zp_art_place_value FROM {content_field_zp_art_place} WHERE nid = %d", $node->nid));
                            $shop_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", substr($target_zp_id, 0, 8)));
                            $cur_shop_info = zp_functions_shop_info(0, 0, $shop_nid);

                            $cur_city_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", substr($target_zp_id, 0, 4)));
                            $cur_city_name = db_result(db_query("SELECT title from {node} WHERE nid  = %d", $cur_city_nid));
                            $_SESSION['current_city_name'] = $cur_city_name;

                            //$cur_shop_info = zp_functions_shop_info($_SESSION['current_shop'], $user->uid);

                            $_SESSION['current_shop_name'] = $cur_shop_info['shop_name'];
                            $_SESSION['current_shop_type_sp2'] = $cur_shop_info['shop_type_spell_2'];
                        }

                        //if($user->uid == 1){}

                        if ($_GET['page'] AND $_GET['tf'])
                            $value = "Служба доставки 'За Покупками!' доставит Вам лучшие товары из отдела '" . $node->title . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . " (ИД:" . $_GET['tf'] . ", cтр." . ($_GET['page'] + 1) . ').' . $node->teaser;
                        else if ($_GET['tf'])
                            $value = "Служба доставки 'За Покупками!' доставит Вам лучшие товары из отдела '" . $node->title . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . " (ИД:" . $_GET['tf'] . ').' . $node->teaser;
                        else if ($_GET['page'])
                            $value = "Служба доставки 'За Покупками!' доставит Вам лучшие товары из отдела '" . $node->title . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . " (cтр." . ($_GET['page'] + 1) . ').' . $node->teaser;
                        else
                            $value = "Служба доставки 'За Покупками!' доставит Вам лучшие товары из отдела '" . $node->title . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "', " . $_SESSION['current_city_name'] . ". " . $node->teaser;
                        //$value = "Служба доставки 'За Покупками!' - доставка на дом и в офис лучших товаров из отдела '" . $node->title . "' " . $_SESSION['current_shop_type_sp2'] . " '" . $_SESSION['current_shop_name'] . "'. " . $node->teaser;
                    }
                    else if ($node->type == 'zp_issue' OR $node->type == 'zp_issue_spravka') {
                        if ($node->field_issue_teaser[0]['value'])
                            $value = $node->field_issue_teaser[0]['value'];
                        else
                            $value = $node->teaser;
                    }
                    else if ($node->type == 'mc_descr') {
                        if ($node->field_mc_teaser[0]['value'])
                            $value = $node->field_mc_teaser[0]['value'];
                        else
                            $value = $node->teaser;
                    }
                    else if ($node->type == 'shop_descr') {
                        if ($node->field_s_teaser[0]['value'])
                            $value = $node->field_s_teaser[0]['value'];
                        else
                            $value = $node->teaser;
                    }


                    else
                        $value = $node->teaser;
                }
                break;

            case 'term':
                // TODO: probably we have to do a db_rewrite_sql() query here so access is restricted
                $term = taxonomy_get_term($ids[0]);
                if ($term) {
                    $value = $term->description;
                }
                break;
            case 'vocabulary':
                // TODO: probably we have to do a db_rewrite_sql() query here so access is restricted
                $voc = taxonomy_get_vocabulary($ids[0]);
                if ($voc) {
                    $value = $voc->description;
                }
                break;
        }
    }
    return $value;
}

function nodewords_description_form($type, $value, $settings) {
    return array(
        '#type' => 'textarea',
        '#title' => t('Description'),
        '#default_value' => t($value),
        '#cols' => 60,
        '#rows' => 6,
        '#description' => t('Enter a description for this page. Limit your description to about 20 words, with a maximum of %count characters. It should not contain any HTML tags or other formatting.', array('%count' => $settings['max_size'])) . ($settings['use_teaser'] ? ' ' . t('When you leave this field empty, the teaser will be used as description.') : ''),
    );
}
