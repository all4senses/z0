<?php
// $Id: uc_stock_workflow.inc,v 1.6.4.2 2008/07/29 22:22:46 rszrama Exp $

/**
 * @file
 * This file contains all the Workflow-NG hooks that are neccesary for Workflow
 * integeration with the uc_stock module
 */


/*******************************************************************************
 *  Workflow-ng Hooks                                                          *
 ******************************************************************************/

/**
 * Implementation of hook_configuration().
 */





// my changes

// -----------------------------------------------

// комментируем эту функцию, чтобы уменьшение количества не происходило автоматически при чекауте
// но вручную устанавливаем в воркфлоу правило на уменьшение товара при установке того статуса ордера, который нам нужен
// например, можем его уменьшать при подтверждении заказа (по умолчанию это происходит при чекауте, т.е. сразу при постановке заказа в очередь на исполнение)


/*
function uc_stock_configuration() {
  $configurations = array();
  $configurations['uc_stock_decrement_on_order'] = array(
    '#label' => t('Decrement stock upon order submission'),
    '#event' => 'checkout_complete',
    '#module' => 'uc_stock',
  );
  $action = workflow_ng_use_action('uc_stock_action_decrement_stock', array(
    '#label' => t('Decrement stock of products in order'),
    '#arguments' => array(
      'order' => array('#entity' => 'order', '#label' => t('Order')),
    ),
  ));
  $configurations['uc_stock_decrement_on_order'] = workflow_ng_configure($configurations['uc_stock_decrement_on_order'], $action);
  return $configurations;
}

*/






/**
 * Implementation of hook_action_info().
 */

/*
// original version 

function uc_stock_action_info() {
  return array(
    'uc_stock_action_decrement_stock' => array(
      '#label' => t('Decrement stock of products on the order with tracking activated.'),
      '#arguments' => array(
                             'order' => array('#entity' => 'order', 
                             '#label' => t('Order')),
                             ),
      '#module' => t('Stock'),
       ),
       
  );
}

*/

// my version with increment action

function uc_stock_action_info() {
  return array(
    'uc_stock_action_decrement_stock' => array(
      '#label' => t('Decrement stock of products on the order with tracking activated.'),
      '#arguments' => array(
                             'order' => array('#entity' => 'order', 
                             '#label' => t('Order')),
                             ),
      '#module' => t('Stock'),
       ),
       
       
       
// my changes -----------------------------------------------------
       
       
     'uc_stock_action_increment_stock' => array(
      '#label' => t('Increment stock of products on the order with tracking activated.'),
      '#arguments' => array(
                             'order' => array('#entity' => 'order', 
                             '#label' => t('Order')),
                             ),
      '#module' => t('Stock'),
       ),   
  );
}









/*******************************************************************************
 *  Workflow-ng Action Callbacks and Forms                                     *
 ******************************************************************************/

function uc_stock_action_decrement_stock($order, $settings) {

//print_r($settings);	
// my changes ---------------------------------	

//$prev_status = db_result(db_query("SELECT order_status FROM {uc_orders} WHERE order_id = %d", $order->order_id));


//$status = $order->order_status;
//drupal_set_message("decr status = $status, prev_status = $prev_status", 'error');	



//if($order->order_status == 'pending' OR $order->order_status == 'canceled' OR $order->order_status == 'stopped' OR $order->order_status == 'in_checkout')  // проверяем на то, что предыдущий статус был вот такой... То есть, уменьшаем только тогда если предыдущий статус был более низкий, чем тот, который сейчас
	
 if (is_array($order->products)) {
    $stock_warnings = array();
    foreach ($order->products as $product) {
      if (($stock = uc_stock_level($product->model)) !== FALSE) {
        $stock_level = db_fetch_object(db_query("SELECT * FROM {uc_product_stock} WHERE sku = '%s'", $product->model));
        if ((($stock - $product->qty) <= $stock_level->threshold) && !in_array($product->model, array_keys($stock_warnings))) {
          $stock_level->stock -= $product->qty;
          $stock_warnings[$product->model] = $stock_level;
        }
        uc_stock_adjust($product->model, -$product->qty);
        
        // эти комментарии не сильно нужны, вероятно, только засоряют эфир
        //uc_order_comment_save($order->order_id, 0, t('The stock level for %model_name has been decreased to !qty.', array('%model_name' => $product->model, '!qty' => ($stock - $product->qty))));
      }
    }
    if (!empty($stock_warnings) && variable_get('uc_stock_threshold_notification', FALSE)) {
      foreach ($stock_warnings as $model => $stock_level) {
        _uc_stock_send_mail($order, $stock_level);
      }
    }
  }
}


// my function for increment action (look above)


function uc_stock_action_increment_stock($order, $settings) {

	
	
	
	
	
// считаем, что в статусе сейчас указан предыдущий статус ордера, ещё не изменённый
	
	
//$status = $order->order_status;
//drupal_set_message("inc status = $status", 'error');	

if($order->order_status == 'processing' OR $order->order_status == 'completed')  // проверяем на то, что предыдущий статус был вот такой... То есть, если он изменился с завершённого или процесса, то значит возвращаем уменьшенные количества продуктов, которые ранее уменьшились при переходе в статус процессинг

  if (is_array($order->products)) {
   //$stock_warnings = array();
    foreach ($order->products as $product) {
     // if (($stock = uc_stock_level($product->model)) !== FALSE) {
     
     
// my changes     

     //$stock = uc_stock_level($product->model);    // почему-то не показывает реальное знаечение $stock
     //drupal_set_message("stock = $stock", 'error');	
     
     
     
     //$stock_level = db_fetch_object(db_query("SELECT * FROM {uc_product_stock} WHERE sku = '%s'", $product->model));
        //if ((($stock - $product->qty) <= $stock_level->threshold) && !in_array($product->model, array_keys($stock_warnings))) {
          //$stock_level->stock -= $product->qty;
          //$stock_warnings[$product->model] = $stock_level;
        //}
        //uc_stock_adjust($product->model, -$product->qty);
        uc_stock_adjust($product->model, $product->qty);
        
        // почему-то не показывает реальное знаечение $stock
        // поэтому просто закомментируем вывод комментария... да и вообще эти комментарии не нужны, только засоряют эфир
        //uc_order_comment_save($order->order_id, 0, t('The stock level for %model_name has been increased to !qty.', array('%model_name' => $product->model, '!qty' => ($stock + $product->qty))));
        
        
      //}
    }
    //if (!empty($stock_warnings) && variable_get('uc_stock_threshold_notification', FALSE)) {
      //foreach ($stock_warnings as $model => $stock_level) {
       // _uc_stock_send_mail($order, $stock_level);
      //}
    //}
  }
}
