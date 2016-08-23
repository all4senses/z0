<?php
// $Id: uc_cart_checkout_pane.inc,v 1.4.2.11 2008/10/07 15:47:57 rszrama Exp $

/**
 * @file
 * This file contains the callbacks for the default checkout panes supplied with
 * Ubercart and their corresponding helper functions.
 *
 * Checkout panes are defined using hook_checkout_pane() and use a callback to
 * handle the different processes involved in completing the checkout form.  The
 * default checkout panes are defined in uc_cart_checkout_pane() in
 * uc_cart.module.
 */




/**
 * My version of a simple Forms API textfield generator...
 */
function uc_textfield2($title, $default = NULL, $required = TRUE, $description = NULL, $maxlength = 32, $size = 32, $disabled = FALSE) {
  if (is_null($title) || empty($title))
    return NULL;

    
// my changes ----------------------------
// тут по умолчанию задано значени "неактивно" (запрещено редактирование) для генерируемого текстового поля
// можно использовать параметр $disabled для динамического изменения значения в зависимости от вызывающей функции
    
  $textfield = array(
    '#disabled' => TRUE, //$disabled
    '#type' => 'textfield',
    '#title' => $title,
    '#description' => $description,
    '#size' => $size,
    '#maxlength' => $maxlength,
    '#required' => $required,
    '#default_value' => $default,
  );

  return $textfield;
}








/*

// оригинальная версия


//**
// * Create a zone select box for a form.
// * $display can be 'code' or 'name'.
// *
function uc_zone_select($title, $default = NULL, $description = NULL, $country_id = NULL, $display = 'name', $required = FALSE) {
  if (empty($country_id)) {
    $country_id = uc_store_default_country();
  }

  $result = db_query("SELECT * FROM {uc_zones} WHERE zone_country_id = %d ORDER BY %s", $country_id, ($display == 'code') ? 'zone_code' : 'zone_name');

  if (db_num_rows($result) == 0) {
    $options[-1] = t('Not applicable');
  }
  else {
    $options[''] = t('Please select');
    while ($zone = db_fetch_object($result)) {
      $options[$zone->zone_id] = ($display == 'code') ? $zone->zone_code : $zone->zone_name;
    }
  }

  $select = array(
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => $default,
    '#required' => $required,
    '#disabled' => TRUE, //isset($options[-1]) ? TRUE : FALSE,
    '#suffix' => '<span class="zone-throbber"></span>',
  );

   
  return $select;
}


*/













// моя версия, но старая
/*
function uc_zone_select2($title, $default = NULL, $description = NULL, $country_id = NULL, $display = 'name', $required = FALSE, $noselect = FALSE) {
  
	
// my change--------------------------------------------
	
  if($noselect)	// если установлен этот флаг, показываем значение не в выпадающем списке с выбором, а в простом текстовом поле без выбора
  {
  	$zone_name = db_result(db_query("SELECT zone_name FROM {uc_zones} WHERE zone_id = %d AND zone_country_id = %d", $default, $country_id));
  	
  	return uc_textfield2($title, $zone_name, $required);
  }
	
	
// иначе всё происходит как обычно	
	
	
	
	
	
	
	
  if (empty($country_id)) {
    $country_id = uc_store_default_country();
  }

  $result = db_query("SELECT * FROM {uc_zones} WHERE zone_country_id = %d ORDER BY %s", $country_id, ($display == 'code') ? 'zone_code' : 'zone_name');

  if (db_num_rows($result) == 0) {
    $options[-1] = t('Not applicable');
  }
  else {
    $options[''] = t('Please select');
    while ($zone = db_fetch_object($result)) {
      $options[$zone->zone_id] = ($display == 'code') ? $zone->zone_code : $zone->zone_name;
    }
  }

  $select = array(
    '#disabled' => TRUE, //isset($options[-1]) ? TRUE : FALSE,
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => $default,
    '#required' => $required,
    '#suffix' => '<span class="zone-throbber"></span>',
  );

  return $select;
}


*/




// моя версия новая, изменен только запрет редактирования

function uc_zone_select2($title, $default = NULL, $description = NULL, $country_id = NULL, $display = 'name', $required = FALSE) {
  if (empty($country_id)) {
    $country_id = uc_store_default_country();
  }

  $result = db_query("SELECT * FROM {uc_zones} WHERE zone_country_id = %d ORDER BY %s", $country_id, ($display == 'code') ? 'zone_code' : 'zone_name');

  if (db_num_rows($result) == 0) {
    $options[-1] = t('Not applicable');
  }
  else {
    $options[''] = t('Please select');
    while ($zone = db_fetch_object($result)) {
      $options[$zone->zone_id] = ($display == 'code') ? $zone->zone_code : $zone->zone_name;
    }
  }

  $select = array(
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => $default,
    '#required' => $required,
    '#disabled' => TRUE, //isset($options[-1]) ? TRUE : FALSE, //
    '#suffix' => '<span class="zone-throbber"></span>',
  );

  return $select;
}








/*


// ---------------------------------------------------------------

// оригинальная версия

//**
// * Create a country select box for a form.
// * $display can be 'name', 'code2' for the 2-digit code, or 'code3' for the 3-digit code.
// *


function uc_country_select($title, $default = NULL, $description = NULL, $display = 'name', $required = FALSE) {
  if ($display == 'name') {
    $order_by = 'country_name';
  }
  elseif ($display == 'code2') {
    $order_by = 'country_iso_code_2';
  }
  elseif ($display == 'code3') {
    $order_by = 'country_iso_code_3';
  }

  $result = db_query("SELECT * FROM {uc_countries} WHERE version > 0 ORDER BY %s", $order_by);

  $options = array();
  while ($country = db_fetch_array($result)) {
    $options[$country['country_id']] = $country[$order_by];
  }
  if (count($options) == 0) {
    $options[] = t('No countries found.');
  }

  $default = db_result(db_query("SELECT country_id FROM {uc_countries} WHERE country_id = %d AND version > 0", empty($default) ? 0 : intval($default)));

  $select = array(
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => empty($default) ? uc_store_default_country() : $default,
    '#required' => $required,
  );

  
  uc_add_js(drupal_get_path('module', 'uc_store') .'/uc_country_select.js');
   
  return $select;
}

*/








/*


//------------------------------------------------------------------------------
// моя версия, но старая

function uc_country_select2($title, $default = NULL, $description = NULL, $display = 'name', $required = FALSE, $noselect = FALSE) {

	

// my change--------------------------------------------
	
  if($noselect)	// если установлен этот флаг, показываем значение не в выпадающем списке с выбором, а в простом текстовом поле без выбора
  {
  	$country_name = db_result(db_query("SELECT country_name FROM {uc_countries} WHERE country_id = %d", $default));

  	return uc_textfield2($title, $country_name, $required);
  }

// иначе всё происходит как обычно
  
  
  
  
  if ($display == 'name') {
    $order_by = 'country_name';
  }
  elseif ($display == 'code2') {
    $order_by = 'country_iso_code_2';
  }
  elseif ($display == 'code3') {
    $order_by = 'country_iso_code_3';
  }

  $result = db_query("SELECT * FROM {uc_countries} WHERE version > 0 ORDER BY %s", $order_by);

  $options = array();
  while ($country = db_fetch_array($result)) {
    $options[$country['country_id']] = $country[$order_by];
  }
  if (count($options) == 0) {
    $options[] = t('No countries found.');
  }

  $default = db_result(db_query("SELECT country_id FROM {uc_countries} WHERE country_id = %d AND version > 0", empty($default) ? 0 : intval($default)));
  
  $select = array(
    '#disabled' => TRUE, //$disabled,
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => empty($default) ? uc_store_default_country() : $default,
    '#required' => $required,
  );

  //uc_add_js(drupal_get_path('module', 'uc_store') .'/uc_country_select.js');

  return $select;
}

*/










// моя версия новая
// добавлен изапрет редактирования и ВНИМАНИЕ  - убрано добавление скрипта!!!

function uc_country_select2($title, $default = NULL, $description = NULL, $display = 'name', $required = FALSE) {
  if ($display == 'name') {
    $order_by = 'country_name';
  }
  elseif ($display == 'code2') {
    $order_by = 'country_iso_code_2';
  }
  elseif ($display == 'code3') {
    $order_by = 'country_iso_code_3';
  }

  $result = db_query("SELECT * FROM {uc_countries} WHERE version > 0 ORDER BY %s", $order_by);

  $options = array();
  while ($country = db_fetch_array($result)) {
    $options[$country['country_id']] = $country[$order_by];
  }
  if (count($options) == 0) {
    $options[] = t('No countries found.');
  }

  $default = db_result(db_query("SELECT country_id FROM {uc_countries} WHERE country_id = %d AND version > 0", empty($default) ? 0 : intval($default)));

  $select = array(
    '#type' => 'select',
    '#title' => $title,
    '#description' => $description,
    '#options' => $options,
    '#default_value' => empty($default) ? uc_store_default_country() : $default,
    '#required' => $required,
    '#disabled' => TRUE,
  );

  
  // ибираем этот вызов, так как из-за него иногда глючит выбор зоны почему-то
  // желательно с этим разобраться
  
  //uc_add_js(drupal_get_path('module', 'uc_store') .'/uc_country_select.js');
   
  return $select;
}








// my changes ---------------------------------------------



/**
 * Display the cart contents for review during checkout.
 */


//function uc_checkout_pane_cart($op) {
function uc_checkout_pane_cart($op, $order = NULL) {	
  
	
// my changes ---------------------------------------------
// тут я добавил новый принимаемый аргумент $order, так как этот параметр всё равно передаётся в функцию (может и не всегда, но для случая op = 'review' точно передаётся)

	
  switch ($op) {
    case 'view':
      $contents['cart_review_table'] = array(
        '#value' => theme('cart_review_table'),
        '#weight' => variable_get('uc_pane_cart_field_cart_weight', 2),
      );
      return array('contents' => $contents, 'next-button' => FALSE);


      
      
    case 'review':
      
// my changes ---------------------------------------------




      // раз уж всё равно сюда передаётся параметром $order, 
      // то зачем заново лезть в корзину и заново формировать товары??? 
      // поэтому будем использовать не uc_cart_get_contents(), а полученный аргументом $order

	  // оригинальная версия
      //$items = uc_cart_get_contents();


      $items = $order->products;
      
      //zp_functions_show($order);
      
      // максимальный коэф доставки из продуктов в корзине, нужен для определения минимальной стоимости доставки
      $max_d_factor_otdel = 0; 
      
      $output = '<table>';
      
      // шапка типа
      $output .= '<tr valign="top"><td ">' . 'Наименование'
                  .'</td><td>'. 'Цена' .'</td><td>'. 'Кол-во' .'</td><td nowrap="nowrap">'. 'Сумма' .'</td></tr>';  
      
      
      foreach ($items as $item) {
        $rows = array();

        	
        	
        
        //foreach ($item->options as $option) {
        foreach ($item->data['attributes'] as $attr => $option) 
        {  

        	
// my changes ---------------------------------------------------------------

// тут я корректирую так, чтобы атрибуты не выводились, если значение опции равно "Нет", 
// то есть, например, если комментариев "Нет" или выбран вариант с упаковкой "Нет"
// тогда просто не показываем этот атрибут
        	
// также убираем из названия атрибута пояснения, то есть, то, что в скобках


    		//$attr_name = explode('(', $option['attribute']);
    		$attr_name = explode('(', $attr); 
			$attr_name = rtrim($attr_name[0]);
			
			/*
			// определим надбавку к цене за опцию
			// ---
        	$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $attr));
    		$oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s'", $option));  
    		$o_price = db_result(db_query("SELECT price from {uc_product_options} WHERE nid = %d AND oid = %d ", $item->nid, $oid));
        	// ---
        	*/

			
        	/*
        	// ---
        	$o_price = 0;
        	$aids = db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $attr);
        	while($aid = db_fetch_array($aids))
        	{
        		if($oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s' AND aid = %d", $option, $aid['aid'])))
        		{
        			if($o_price = db_result(db_query("SELECT price FROM {uc_product_options} WHERE oid = %d AND nid = %d", $oid, $item->nid)))
        				break;
        		}
        		
        	}
        	*/
        	
    		//$oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s'", $option));  
    		//$o_price = db_result(db_query("SELECT price from {uc_product_options} WHERE nid = %d AND oid = %d ", $item->nid, $oid));
        	// ---
        
        	
        	
        	
        	
        	//if($option['name'] != 'Нет' AND $option['name'] != '')  // my change
        	if($option != 'Нет' AND $option != '')  // my change
        	{

        		$o_price = 0;
        		$aids = db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $attr);
        		while($aid = db_fetch_array($aids))
        		{
        			if($oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s' AND aid = %d", $option, $aid['aid'])))
        			{
        				if($o_price = db_result(db_query("SELECT price FROM {uc_product_options} WHERE oid = %d AND nid = %d", $oid, $item->nid)))
        					break;
        			}
        		
        		}
        		
        		if($o_price)
        	   		$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option . ' (+' . uc_currency_format($o_price). ')' )); // my change
        	  	else 
        	  		$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option)); // my change
        	  		
        	   //$rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
        	   
        	}   
        }
        
        
        $desc = check_plain($item->title) . theme('item_list', $rows, NULL, 'ul', array('class' => 'product-options'));
        //$output .= '<tr valign="top"><td>'. $item->qty .'x</td><td width="100%">'. $desc
          //        .'</td><td nowrap="nowrap">'. uc_currency_format($item->price * $item->qty) .'</td></tr>';
        
        
          
        if($item->data['#opt_price'] > 0)  
           $output .= '<tr valign="top"><td ">'. $desc .'</td><td>'. uc_currency_format($item->price + $item->data['#dost_price']) .'<br/> (в т.ч.  ' . 'цена: ' . uc_currency_format($item->price - $item->data['#opt_price']) . ',<br>' . 'выбр. дополн.: ' . uc_currency_format($item->data['#opt_price'])  . ',<br>доставка: ' . uc_currency_format($item->data['#dost_price']) . ')</td><td>-'. $item->qty .'шт.-</td><td nowrap="nowrap">'. uc_currency_format(($item->price + $item->data['#dost_price']) * $item->qty) .'</td></tr>';  
         else 
		   $output .= '<tr valign="top"><td ">'. $desc .'</td><td>'. uc_currency_format($item->price + $item->data['#dost_price']) .'<br/> (в т.ч. ' . 'цена: ' . uc_currency_format($item->price) . ',<br>' . 'доставка: ' . uc_currency_format($item->data['#dost_price']) . ')</td><td>-'. $item->qty .'шт.-</td><td nowrap="nowrap">'. uc_currency_format(($item->price + $item->data['#dost_price']) * $item->qty) .'</td></tr>';                      
		   
		   
		 $order_total += $item->price * $item->qty;   
		 $dost_total += $item->data['#dost_price'] * $item->qty;
		 
	 
		 // найдём максимальный коэффициент доставки среди всех товаров
         // и на основе него вычислим коэффициент для минимальной стоимости доставки
         // которую определим как произведение минимальной стоимости доставки по умолчанию на этот коэффициент
         if($max_d_factor_otdel < $item->data['#d_factor'])
            $max_d_factor_otdel = $item->data['#d_factor'];
            	
      }
      
      //$output .= '<td colspan = "6" align = "right"><tr>' . '<strong>В т.ч. стоимость выбранных дополнений (напр., упаковки): </strong>' . uc_currency_format($order->data['opt_price_total']) . '</tr>';
      //$output .= '<tr>' . '<strong>В т.ч. стоимость доставки: </strong>' . uc_currency_format($order->data['dost_price_total']) . '</tr></td>';
      
       // если общая стоимость заказа меньше минимальной суммы заказа для этого магазина и клиента
      // с учётом максимального коэффициента доставки товаров этой корзины
      // то заменяем стоимость доставки на минимальную стоимость доставки, умноженную на максимальный коэфициент доставки
      
      // найдём минимальную стоимость доставки по умолчанию
      $zp_default_set = zp_functions_get_zp_default_set();
      
      $max_d_factor_otdel =  $max_d_factor_otdel * 10;
       

      
      $c_shop_tids = zp_functions_get_cart_shop_data(); // параметры не передаём, так что они устанавливаются в фунцкии по текущему юзеру
      
      //if($order_total < $c_shop_tids['shop_min_sum'])
      if( ($order_total - $dost_total) < $c_shop_tids['shop_min_sum'])
      {
      	// изменяем стоимость доставки на миниманую по умолчанию и корректируем общую стоимость
      	//$subtotal = $subtotal - $dost_price_total;
      	$dost_total = $order->data['dost_price_total'] = $zp_default_set['min_dost_price_default']*$max_d_factor_otdel;
      	//$subtotal = $subtotal + $dost_price_total;
      	
      }
      	
      	
      
      if($order->data['opt_price_total'] > 0)
      {
      
      $output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">'. t('Всего с дополнениями и с обычной (не срочной) доставкой (за ' . $order->data['qty_total'] . ' шт.): ')
             // .'</span> '. uc_currency_format($order->order_total) .'</td></tr>';
              .'</span> '. uc_currency_format($order_total + $order->data['dost_price_total']) .'</td></tr>';	
      	
      	
      $output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость c выбранными дополнениями, без доставки: '
              .'</span> '. uc_currency_format($order_total) .'</td></tr>';
      
      	
      /*	 
      $output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость без дополнений и доставки: '
              .'</span> '. uc_currency_format($order_total - $order->data['opt_price_total']) .'</td></tr>';
              
       $output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость выбранных дополнений: '
              .'</span> '. uc_currency_format($order->data['opt_price_total']) .'</td></tr>';       
       */       
      } 
      else 
      {
        
      	$output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">'. t('Всего с обычной (не срочной) доставкой (за ' . $order->data['qty_total'] . ' шт.): ')
             // .'</span> '. uc_currency_format($order->order_total) .'</td></tr>';
              .'</span> '. uc_currency_format($order_total + $order->data['dost_price_total']) .'</td></tr>';
      	
      	
      	$output .= '<tr class="last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость без доставки: '
              .'</span> '. uc_currency_format($order_total - $order->data['opt_price_total']) .'</td></tr>';       
     
      }         
              
      
     
      //if($order_total < $c_shop_tids['shop_min_sum'])
      if( ($order_total - $dost_total) < $c_shop_tids['shop_min_sum'])
      {
	      $output .= '<tr class="last"><td class="subtotal" '
    	          .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость обычной (не срочной) доставки (которая была увеличена до минимальной стоимости доставки по умолчанию, так как пока что общая сумма заказа (без учёта доставки)<br> меньше минимальной суммы заказа для Вас в этом заведении): '
        	      .'</span> '. uc_currency_format($dost_total) .'</td></tr>';
      }
      else 
      {
	      $output .= '<tr class="last"><td class="subtotal" '
    	          .'colspan="4"><span id="subtotal-title">' . 'В т.ч. стоимость обычной (не срочной) доставки: '
        	      .'</span> '. uc_currency_format($dost_total) .'</td></tr>';
      	
      }
        
      $output .= '</table>';
      $review[] = $output;
      return $review;
  }
}








/**
 * Get the user's email address for login.
 */
function uc_checkout_pane_customer($op, &$arg1, $arg2) {
  global $user;

  switch ($op) {
    case 'view':
      $email = (is_null($arg1) || empty($arg1->primary_email)) ? $user->mail : $arg1->primary_email;

      if ($user->uid) {
        
      	
      	
      	
      	
      	
      	
      	
      	
      	
      	
      	
      	
// my changes  -------------------------------------------

// убираем ссылку на редактирования емейла
      	
      	
      	
      	
      	$description = t('Order information will be sent to your account e-mail:');// .'<br />'
      	
      	// original version
      	//$description = t('Order information will be sent to your account e-mail listed below.');// .'<br />'
        
      	
      	
      	$contents['primary_email'] = array('#type' => 'hidden', '#value' => check_plain($email));
        $contents['email_text'] = array(
          
        
        
        
        
          // original version
          //'#value' => '<div>'. t('<b>E-mail address:</b> @email (<a href="!url">edit</a>)', array('@email' => $email, '!url' => url('user/'. $user->uid .'/edit', 'destination=cart/checkout'))) .'</div>',
          '#value' => '<div>'. t('@email ', array('@email' => $email)) .'</div>',
        );
      }
      else {
        $description = t('Enter a valid email address for this order or <a href="!url">click here</a> to login with an existing account and return to checkout.', array('!url' => url('user/login')));
        $contents['primary_email'] = uc_textfield(t('E-mail address'), $email, TRUE, NULL, 64);
      }

      if (variable_get('uc_cart_email_validation', FALSE) && !$user->uid) {
        $contents['primary_email_confirm'] = uc_textfield(t('Confirm e-mail address'), $_SESSION['email_match'] === FALSE ? '' : $email, TRUE, NULL, 64);
        if ($_SESSION['email_match'] === FALSE) {
          $contents['primary_email_confirm']['#attributes'] = array('class' => 'error');
          unset($_SESSION['email_match']);
        }
      }

      if ($user->uid == 0) {
        $contents['new_account'] = array();

        if (variable_get('uc_cart_new_account_name', FALSE)) {
          $contents['new_account']['name'] = array(
            '#type' => 'textfield',
            '#title' => t('Username'),
            '#default_value' => $arg1->data['new_user']['name'],
            '#maxlength' => 60,
            '#size' => 32,
          );
        }
        if (variable_get('uc_cart_new_account_password', FALSE)) {
          $contents['new_account']['pass'] = array(
            '#type' => 'password',
            '#title' => t('Password'),
            '#maxlength' => 32,
            '#size' => 32,
          );
          $contents['new_account']['pass_confirm'] = array(
            '#type' => 'password',
            '#title' => t('Confirm password'),
            '#description' => t('Passwords must match to proceed.'),
            '#maxlength' => 32,
            '#size' => 32,
          );
        }

        if (!empty($contents['new_account'])) {
          $array = array(
            '#type' => 'fieldset',
            '#title' => t('New account details'),
            '#description' => variable_get('uc_cart_new_account_details', t('<b>Optional.</b> New customers may supply custom account details.<br />We will create these for you if no values are entered.')),
            '#collapsible' => FALSE,
          );
          $contents['new_account'] = array_merge($array, $contents['new_account']);
        }

        /**
         * This code adds profile fields required for registration to the
         * customer checkout pane.  However, I don't have the time to fool with
         * validation/submission stuff, so I'm postponing this feature. -RS
        $null = NULL;
        $extra = _user_forms($null, NULL, NULL, 'register');
        if (!empty($extra)) {
          $contents = array_merge($contents, $extra);
        }*/
      }

      
      return array('description' => $description, 'contents' => $contents);

    case 'process':
      if (!empty($arg2['primary_email']) && !valid_email_address($arg2['primary_email'])) {
        drupal_set_message(t('You must enter a valid e-mail address.'), 'error');
        return FALSE;
      }

      $arg1->primary_email = $arg2['primary_email'];

      if (variable_get('uc_cart_email_validation', FALSE) && !$user->uid &&
          $arg2['primary_email'] !== $arg2['primary_email_confirm']) {
        drupal_set_message(t('The e-mail address did not match.'), 'error');
        $_SESSION['email_match'] = FALSE;
        return FALSE;
      }
      unset($_SESSION['email_match']);

      // If new users can specify names or passwords then...
      if ((variable_get('uc_cart_new_account_name', FALSE) ||
          variable_get('uc_cart_new_account_password', FALSE)) &&
          $user->uid == 0) {
        // Skip if an account already exists for this e-mail address.
        if (db_num_rows(db_query("SELECT uid FROM {users} WHERE LOWER(mail) = LOWER('%s')", $arg2['primary_email'])) > 0) {
          drupal_set_message(t('An account already exists for your e-mail address. The new account details you entered will be disregarded.'));
        }
        else {
          // Validate the username.
          if (variable_get('uc_cart_new_account_name', FALSE) && !empty($arg2['new_account']['name'])) {
            $message = user_validate_name($arg2['new_account']['name']);
            if (!empty($message)) {
              drupal_set_message($message, 'error');
              return FALSE;
            }
            if (db_num_rows(db_query("SELECT uid FROM {users} WHERE LOWER(name) = LOWER('%s')", $arg2['new_account']['name'])) > 0) {
              drupal_set_message(t('The username %name is already taken. Please enter a different name or leave the field blank for your username to be your e-mail address.', array('%name' => $arg2['new_account']['name'])), 'error');
              return FALSE;
            }
            $arg1->data['new_user']['name'] = $arg2['new_account']['name'];
          }
          // Validate the password.
          if (variable_get('uc_cart_new_account_password', FALSE)) {
            if ($arg2['new_account']['pass'] != $arg2['new_account']['pass_confirm']) {
              drupal_set_message(t('The passwords you entered did not match. Please try again.'), 'error');
              return FALSE;
            }
            $arg1->data['new_user']['pass'] = $arg2['new_account']['pass'];
          }
        }
      }

      if ($user->uid) {
        $arg1->uid = $user->uid;
      }
      return TRUE;

    case 'review':
      $review[] = array('title' => t('E-mail'), 'data' => check_plain($arg1->primary_email));
      return $review;

    case 'settings':
      $form['uc_cart_email_validation'] = array(
        '#type' => 'checkbox',
        '#title' => t('Require e-mail validation for anonymous customers.'),
        '#default_value' => variable_get('uc_cart_email_validation', FALSE),
      );
      $form['uc_cart_new_account_name'] = array(
        '#type' => 'checkbox',
        '#title' => t('Allow anonymous customers to specify a new user account name.'),
        '#default_value' => variable_get('uc_cart_new_account_name', FALSE),
      );
      $form['uc_cart_new_account_password'] = array(
        '#type' => 'checkbox',
        '#title' => t('Allow anonymous customers to specify a new user account password.'),
        '#default_value' => variable_get('uc_cart_new_account_password', FALSE),
      );
      $form['uc_cart_new_account_details'] = array(
        '#type' => 'textarea',
        '#title' => t('New account details help message'),
        '#description' => t('Enter the help message displayed in the new account details fieldset when shown.'),
        '#default_value' => variable_get('uc_cart_new_account_details', t('<b>Optional.</b> New customers may supply custom account details.<br />We will create these for you if no values are entered.')),
      );
      return $form;
  }
}

/**
 * Get the delivery information.
 */















function uc_checkout_pane_delivery($op, &$arg1, $arg2) {

  global $user;

  
  switch ($op) {
    case 'view':
    	
    	
//drupal_set_message("it's a view", 'error'); //----------------------------------------------------------------------    	




      $description = t('Enter your delivery address and information here.');

      if ((uc_cart_is_shippable() || !variable_get('uc_cart_delivery_not_shippable', TRUE)) &&
           _checkout_pane_data('billing', 'weight') < _checkout_pane_data('delivery', 'weight') &&
           _checkout_pane_data('billing', 'enabled')) {
        
        /*   	
        $contents['copy_address'] = array(
          '#type' => 'checkbox',
          '#title' => t('My delivery information is the same as my billing information.'),
          '#attributes' => array('onclick' => "uc_cart_copy_address(this.checked, 'billing', 'delivery');"),
        );
        
        */
      }



     if ($user->uid) {
        $addresses = uc_select_address($user->uid, 'delivery', 'apply_address(\'delivery\', this.value);', t('Saved addresses'), TRUE);
        if (!empty($addresses)) {
          $contents['delivery_address_select'] = $addresses;
        }
      }


     if (uc_address_field_enabled('last_name')) {
        $contents['delivery_last_name'] = uc_textfield2(uc_get_field_name('last_name'), $arg1->delivery_last_name, uc_address_field_required('last_name'));
      }
      if (uc_address_field_enabled('first_name')) {
        $contents['delivery_first_name'] = uc_textfield2(uc_get_field_name('first_name'), $arg1->delivery_first_name, uc_address_field_required('first_name'));
      }

      if (uc_address_field_enabled('company')) {
        $contents['delivery_company'] = uc_textfield2(uc_get_field_name('company'), $arg1->delivery_company, uc_address_field_required('company'), NULL, 64);
      }
      if (uc_address_field_enabled('street1')) {
        $contents['delivery_street1'] = uc_textfield2(uc_get_field_name('street1'), $arg1->delivery_street1, uc_address_field_required('street1'), NULL, 64);
      }
      if (uc_address_field_enabled('street2')) {
        $contents['delivery_street2'] = uc_textfield2(uc_get_field_name('street2'), $arg1->delivery_street2, uc_address_field_required('street2'), NULL, 64);
      }
      if (uc_address_field_enabled('city')) {
        $contents['delivery_city'] = uc_textfield2(uc_get_field_name('city'), $arg1->delivery_city, uc_address_field_required('city'));
      }
      

      

      
      
      
      
/*
// оригинал
      
      if (uc_address_field_enabled('country')) {
        $contents['delivery_country'] = uc_country_select(uc_get_field_name('country'), $arg1->delivery_country, NULL, 'name', uc_address_field_required('country'));
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['panes']['delivery']['delivery_country'])) {
          $country_id = intval($_POST['panes']['delivery']['delivery_country']);
        }
        else {
          $country_id = $arg1->delivery_country;
        }
        $contents['delivery_zone'] = uc_zone_select(uc_get_field_name('zone'), $arg1->delivery_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));
        if (isset($_POST['panes']) && count($contents['delivery_zone']['#options']) == 1) {
          $contents['delivery_zone']['#required'] = FALSE;
        }
      }

*/



// small changes

      if (uc_address_field_enabled('country')) {
        $contents['delivery_country'] = uc_country_select2(uc_get_field_name('country'), $arg1->delivery_country, NULL, 'name', uc_address_field_required('country'));
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['panes']['delivery']['delivery_country'])) {
          $country_id = intval($_POST['panes']['delivery']['delivery_country']);
        }
        else {
          $country_id = $arg1->delivery_country;
        }
        $contents['delivery_zone'] = uc_zone_select2(uc_get_field_name('zone'), $arg1->delivery_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));
        if (isset($_POST['panes']) && count($contents['delivery_zone']['#options']) == 1) {
          $contents['delivery_zone']['#required'] = FALSE;
        }
      }
      
      
   
      
      
/*
      
// my changes ---------------------------------      
      
    
      if (uc_address_field_enabled('country')) {
      	//$contents['delivery_country'] = uc_country_select2(uc_get_field_name('country'), $arg1->delivery_country, NULL, 'name', uc_address_field_required('country'));
      	//$contents['delivery_country'] = uc_textfield2(uc_get_field_name('country'), $arg1->delivery_country, uc_address_field_required('country'));
      	
      	//$contents['delivery_country'] = uc_country_select2(uc_get_field_name('country'), $arg1['delivery_country']['#default_value'], NULL, 'name', uc_address_field_required('country'), TRUE);
      	$contents['delivery_country'] = uc_country_select2(uc_get_field_name('country'), $arg1->delivery_country, NULL, 'name', uc_address_field_required('country'), TRUE);
      }

      
      
      if (uc_address_field_enabled('zone')) 
       {

      	 if (isset($_POST['panes']['delivery']['delivery_country'])) 
      	   {
             $country_id = intval($_POST['panes']['delivery']['delivery_country']);
           }
         else 
            {
             $country_id = $arg1->delivery_country;
            }
        

         if (isset($_POST['panes']) && count($contents['delivery_zone']['#options']) == 1) 
           {
             $contents['delivery_zone']['#required'] = FALSE;
           }
        
         $contents['delivery_zone']['#disabled'] = TRUE;
            
         //$contents['delivery_zone'] = uc_zone_select2(uc_get_field_name('zone'), $arg1->delivery_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));   
         $contents['delivery_zone'] = uc_zone_select2(uc_get_field_name('zone'), $arg1->delivery_zone, NULL, $country_id, 'name', uc_address_field_required('zone'), TRUE);
         
       } 
    
       
  //-----------------------------------------------------


*/  
  
  
   
       
       
       
      
      if (uc_address_field_enabled('postal_code')) {
        $contents['delivery_postal_code'] = uc_textfield2(uc_get_field_name('postal_code'), $arg1->delivery_postal_code, uc_address_field_required('postal_code'), NULL, 10, 10);
      }
      if (uc_address_field_enabled('phone')) {
        $contents['delivery_phone'] = uc_textfield2(uc_get_field_name('phone'), $arg1->delivery_phone, uc_address_field_required('phone'), NULL, 32, 16);
      }
      
      return array('description' => $description, 'contents' => $contents, 'theme' => 'address_pane');

      
      
    case 'process':
    	
//drupal_set_message("it's a process", 'error'); //----------------------------------------------------------------------
      //$arg2['delivery_zone']['#disabled'] = FALSE;
    	
      $arg1->delivery_first_name = $arg2['delivery_first_name'];
      $arg1->delivery_last_name = $arg2['delivery_last_name'];
      $arg1->delivery_company = $arg2['delivery_company'];
      $arg1->delivery_street1 = $arg2['delivery_street1'];
      $arg1->delivery_street2 = $arg2['delivery_street2'];
      $arg1->delivery_city = $arg2['delivery_city'];
      $arg1->delivery_zone = $arg2['delivery_zone'];
      $arg1->delivery_postal_code = $arg2['delivery_postal_code'];
	  $arg1->delivery_country = $arg2['delivery_country'];	
      $arg1->delivery_phone = $arg2['delivery_phone'];
      return TRUE;

      
      
      
    case 'review':
    	
//drupal_set_message("it's a review", 'error'); //----------------------------------------------------------------------    	

      $review[] = array('title' => t('Address'), 'data' => uc_order_address($arg1, 'delivery', FALSE));
      if (uc_address_field_enabled('phone') && !empty($arg1->delivery_phone)) {
        $review[] = array('title' => t('Phone'), 'data' => check_plain($arg1->delivery_phone));
      }
      return $review;
  }
}














/*  
//оригинальный вариант

function uc_checkout_pane_delivery_original($op, &$arg1, $arg2) {
  global $user;

  switch ($op) {
    case 'view':
      $description = t('Enter your delivery address and information here.');

      if ((uc_cart_is_shippable() || !variable_get('uc_cart_delivery_not_shippable', TRUE)) &&
           _checkout_pane_data('billing', 'weight') < _checkout_pane_data('delivery', 'weight') &&
           _checkout_pane_data('billing', 'enabled')) {
        $contents['copy_address'] = array(
          '#type' => 'checkbox',
          '#title' => t('My delivery information is the same as my billing information.'),
          '#attributes' => array('onclick' => "uc_cart_copy_address(this.checked, 'billing', 'delivery');"),
        );
      }

      if ($user->uid) {
        $addresses = uc_select_address($user->uid, 'delivery', 'apply_address(\'delivery\', this.value);', t('Saved addresses'), TRUE);
        if (!empty($addresses)) {
          $contents['delivery_address_select'] = $addresses;
        }
      }

      if (uc_address_field_enabled('first_name')) {
        $contents['delivery_first_name'] = uc_textfield(uc_get_field_name('first_name'), $arg1->delivery_first_name, uc_address_field_required('first_name'));
      }
      if (uc_address_field_enabled('last_name')) {
        $contents['delivery_last_name'] = uc_textfield(uc_get_field_name('last_name'), $arg1->delivery_last_name, uc_address_field_required('last_name'));
      }
      if (uc_address_field_enabled('company')) {
        $contents['delivery_company'] = uc_textfield(uc_get_field_name('company'), $arg1->delivery_company, uc_address_field_required('company'), NULL, 64);
      }
      if (uc_address_field_enabled('street1')) {
        $contents['delivery_street1'] = uc_textfield(uc_get_field_name('street1'), $arg1->delivery_street1, uc_address_field_required('street1'), NULL, 64);
      }
      if (uc_address_field_enabled('street2')) {
        $contents['delivery_street2'] = uc_textfield(uc_get_field_name('street2'), $arg1->delivery_street2, uc_address_field_required('street2'), NULL, 64);
      }
      if (uc_address_field_enabled('city')) {
        $contents['delivery_city'] = uc_textfield(uc_get_field_name('city'), $arg1->delivery_city, uc_address_field_required('city'));
      }
      if (uc_address_field_enabled('country')) {
        $contents['delivery_country'] = uc_country_select(uc_get_field_name('country'), $arg1->delivery_country, NULL, 'name', uc_address_field_required('country'));
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['panes']['delivery']['delivery_country'])) {
          $country_id = intval($_POST['panes']['delivery']['delivery_country']);
        }
        else {
          $country_id = $arg1->delivery_country;
        }
        $contents['delivery_zone'] = uc_zone_select(uc_get_field_name('zone'), $arg1->delivery_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));
        if (isset($_POST['panes']) && count($contents['delivery_zone']['#options']) == 1) {
          $contents['delivery_zone']['#required'] = FALSE;
        }
      }
      if (uc_address_field_enabled('postal_code')) {
        $contents['delivery_postal_code'] = uc_textfield(uc_get_field_name('postal_code'), $arg1->delivery_postal_code, uc_address_field_required('postal_code'), NULL, 10, 10);
      }
      if (uc_address_field_enabled('phone')) {
        $contents['delivery_phone'] = uc_textfield(uc_get_field_name('phone'), $arg1->delivery_phone, uc_address_field_required('phone'), NULL, 32, 16);
      }

      return array('description' => $description, 'contents' => $contents, 'theme' => 'address_pane');

    case 'process':
      $arg1->delivery_first_name = $arg2['delivery_first_name'];
      $arg1->delivery_last_name = $arg2['delivery_last_name'];
      $arg1->delivery_company = $arg2['delivery_company'];
      $arg1->delivery_street1 = $arg2['delivery_street1'];
      $arg1->delivery_street2 = $arg2['delivery_street2'];
      $arg1->delivery_city = $arg2['delivery_city'];
      $arg1->delivery_zone = $arg2['delivery_zone'];
      $arg1->delivery_postal_code = $arg2['delivery_postal_code'];
      $arg1->delivery_country = $arg2['delivery_country'];
      $arg1->delivery_phone = $arg2['delivery_phone'];
      return TRUE;

    case 'review':
      $review[] = array('title' => t('Address'), 'data' => uc_order_address($arg1, 'delivery', FALSE));
      if (uc_address_field_enabled('phone') && !empty($arg1->delivery_phone)) {
        $review[] = array('title' => t('Phone'), 'data' => check_plain($arg1->delivery_phone));
      }
      return $review;
  }
}


*/
















/**
 * Get the billing information.
 */


function uc_checkout_pane_billing($op, &$arg1, $arg2) {
  global $user;

  switch ($op) {
    case 'view':
    	
    	
      $description = t('Enter your billing address and information here.');


// запрет редактирования чекбокса использования того же адреса, что и доставки
// по умолчанию установлено "копировать" и изменить нельзя  ( '#disabled' => TRUE,)

      if ((uc_cart_is_shippable() || !variable_get('uc_cart_delivery_not_shippable', TRUE)) &&
           _checkout_pane_data('delivery', 'weight') < _checkout_pane_data('billing', 'weight') &&
           _checkout_pane_data('delivery', 'enabled')) {
        $contents['copy_address'] = array(

        
          '#disabled' => FALSE, //TRUE,
          '#type' => 'checkbox',
          '#value' => 1,
          '#title' => t('My billing information is the same as my delivery information.'),
          
          //'#attributes' => array('onclick' => "uc_cart_copy_address(this.checked, 'delivery', 'billing');", // 
          '#attributes' => array('onclick' => "uc_cart_copy_address(this.checked, 'delivery', 'billing');",
          //'onload' => "uc_cart_copy_address(1, 'delivery', 'billing');",
          ),
        );
      }






// можно убирать возможность выбора адреса для биллинга ----------------------------------------

      /**/
      if ($user->uid) {
        $addresses = uc_select_address($user->uid, 'billing', 'apply_address(\'billing\', this.value);', t('Saved addresses'), TRUE);
        if (!empty($addresses)) {
          $contents['billing_address_select'] = $addresses;
        }
      }
        /**/
        
        
        
        
        

      if (uc_address_field_enabled('first_name')) {
        $contents['billing_first_name'] = uc_textfield2(uc_get_field_name('first_name'), $arg1->billing_first_name, uc_address_field_required('first_name'));
      }
      if (uc_address_field_enabled('last_name')) {
        $contents['billing_last_name'] = uc_textfield2(uc_get_field_name('last_name'), $arg1->billing_last_name, uc_address_field_required('last_name'));
      }
      if (uc_address_field_enabled('company')) {
        $contents['billing_company'] = uc_textfield2(uc_get_field_name('company'), $arg1->billing_company, uc_address_field_required('company'), NULL, 64);
      }
      if (uc_address_field_enabled('street1')) {
        $contents['billing_street1'] = uc_textfield2(uc_get_field_name('street1'), $arg1->billing_street1, uc_address_field_required('street1'), NULL, 64);
      }
      if (uc_address_field_enabled('street2')) {
        $contents['billing_street2'] = uc_textfield2(uc_get_field_name('street2'), $arg1->billing_street2, uc_address_field_required('street2'), NULL, 64);
      }
      if (uc_address_field_enabled('city')) {
        $contents['billing_city'] = uc_textfield2(uc_get_field_name('city'), $arg1->billing_city, uc_address_field_required('city'));
      }
      
      
      
      
      
      
      
      
      
      
            
// my changes ---------------------------------      
      
      
      if (uc_address_field_enabled('country')) {
      	//$contents['billing_country'] = uc_country_select2(uc_get_field_name('country'), $arg1['billing_country']['#default_value'], NULL, 'name', uc_address_field_required('country'), TRUE);
      	$contents['billing_country'] = uc_country_select2(uc_get_field_name('country'), $arg1->billing_country, NULL, 'name', uc_address_field_required('country'), TRUE);
      }

      
      
      if (uc_address_field_enabled('zone')) 
       {

      	 if (isset($_POST['panes']['billing']['billing_country'])) 
      	   {
             $country_id = intval($_POST['panes']['billing']['billing_country']);
           }
         else 
            {
             $country_id = $arg1->billing_country;
            }
        

         if (isset($_POST['panes']) && count($contents['billing_zone']['#options']) == 1) 
           {
             $contents['billing_zone']['#required'] = FALSE;
           }
        
         $contents['billing_zone']['#disabled'] = TRUE;
            
         $contents['billing_zone'] = uc_zone_select2(uc_get_field_name('zone'), $arg1->billing_zone, NULL, $country_id, 'name', uc_address_field_required('zone'), TRUE);
         
       } 
    
       
  //-----------------------------------------------------
      
      
      
           
/*
// original version
      
      
      if (uc_address_field_enabled('country')) {
        $contents['billing_country'] = uc_country_select2(uc_get_field_name('country'), $arg1->billing_country, NULL, 'name', uc_address_field_required('country'));
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['panes']['billing']['billing_country'])) {
          $country_id = intval($_POST['panes']['billing']['billing_country']);
        }
        else {
          $country_id = $arg1->billing_country;
        }
        $contents['billing_zone'] = uc_zone_select2(uc_get_field_name('zone'), $arg1->billing_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));
        if (isset($_POST['panes']) && count($contents['billing_zone']['#options']) == 1) {
          $contents['billing_zone']['#required'] = FALSE;
        }
        $contents['billing_zone']['#disabled'] = TRUE;
      }
      
      
*/      
      
      


      
      
      
      if (uc_address_field_enabled('postal_code')) {
        $contents['billing_postal_code'] = uc_textfield2(uc_get_field_name('postal_code'), $arg1->billing_postal_code, uc_address_field_required('postal_code'), NULL, 10, 10);
      }
      if (uc_address_field_enabled('phone')) {
        $contents['billing_phone'] = uc_textfield2(uc_get_field_name('phone'), $arg1->billing_phone, uc_address_field_required('phone'), NULL, 32, 16);
      }

      return array('description' => $description, 'contents' => $contents, 'theme' => 'address_pane');

    case 'process':
    	
      $arg1->billing_first_name = $arg2['billing_first_name'];
      $arg1->billing_last_name = $arg2['billing_last_name'];
      $arg1->billing_company = $arg2['billing_company'];
      $arg1->billing_street1 = $arg2['billing_street1'];
      $arg1->billing_street2 = $arg2['billing_street2'];
      $arg1->billing_city = $arg2['billing_city'];
      $arg1->billing_zone = $arg2['billing_zone'];
      $arg1->billing_postal_code = $arg2['billing_postal_code'];
      $arg1->billing_country = $arg2['billing_country'];
      $arg1->billing_phone = $arg2['billing_phone'];
      return TRUE;





// уюираем показ адреса биллинга на превью (показывается только адрес доставки)

    /**/
    case 'review':
    	
      $review[] = array('title' => t('Address'), 'data' => uc_order_address($arg1, 'billing', FALSE));
      if (uc_address_field_enabled('phone') && !empty($arg1->billing_phone)) {
        $review[] = array('title' => t('Phone'), 'data' => check_plain($arg1->billing_phone));
      }
      return $review;
    /**/


  }
}















/*
//original

function uc_checkout_pane_billing_original($op, &$arg1, $arg2) {
  global $user;

  switch ($op) {
    case 'view':
      $description = t('Enter your billing address and information here.');

      if ((uc_cart_is_shippable() || !variable_get('uc_cart_delivery_not_shippable', TRUE)) &&
           _checkout_pane_data('delivery', 'weight') < _checkout_pane_data('billing', 'weight') &&
           _checkout_pane_data('delivery', 'enabled')) {
        $contents['copy_address'] = array(
          '#type' => 'checkbox',
          '#title' => t('My billing information is the same as my delivery information.'),
          '#attributes' => array('onclick' => "uc_cart_copy_address(this.checked, 'delivery', 'billing');"),
        );
      }

      if ($user->uid) {
        $addresses = uc_select_address($user->uid, 'billing', 'apply_address(\'billing\', this.value);', t('Saved addresses'), TRUE);
        if (!empty($addresses)) {
          $contents['billing_address_select'] = $addresses;
        }
      }
      if (uc_address_field_enabled('first_name')) {
        $contents['billing_first_name'] = uc_textfield(uc_get_field_name('first_name'), $arg1->billing_first_name, uc_address_field_required('first_name'));
      }
      if (uc_address_field_enabled('last_name')) {
        $contents['billing_last_name'] = uc_textfield(uc_get_field_name('last_name'), $arg1->billing_last_name, uc_address_field_required('last_name'));
      }
      if (uc_address_field_enabled('company')) {
        $contents['billing_company'] = uc_textfield(uc_get_field_name('company'), $arg1->billing_company, uc_address_field_required('company'), NULL, 64);
      }
      if (uc_address_field_enabled('street1')) {
        $contents['billing_street1'] = uc_textfield(uc_get_field_name('street1'), $arg1->billing_street1, uc_address_field_required('street1'), NULL, 64);
      }
      if (uc_address_field_enabled('street2')) {
        $contents['billing_street2'] = uc_textfield(uc_get_field_name('street2'), $arg1->billing_street2, uc_address_field_required('street2'), NULL, 64);
      }
      if (uc_address_field_enabled('city')) {
        $contents['billing_city'] = uc_textfield(uc_get_field_name('city'), $arg1->billing_city, uc_address_field_required('city'));
      }
      if (uc_address_field_enabled('country')) {
        $contents['billing_country'] = uc_country_select(uc_get_field_name('country'), $arg1->billing_country, NULL, 'name', uc_address_field_required('country'));
      }
      if (uc_address_field_enabled('zone')) {
        if (isset($_POST['panes']['billing']['billing_country'])) {
          $country_id = intval($_POST['panes']['billing']['billing_country']);
        }
        else {
          $country_id = $arg1->billing_country;
        }
        $contents['billing_zone'] = uc_zone_select(uc_get_field_name('zone'), $arg1->billing_zone, NULL, $country_id, 'name', uc_address_field_required('zone'));
        if (isset($_POST['panes']) && count($contents['billing_zone']['#options']) == 1) {
          $contents['billing_zone']['#required'] = FALSE;
        }
      }
      if (uc_address_field_enabled('postal_code')) {
        $contents['billing_postal_code'] = uc_textfield(uc_get_field_name('postal_code'), $arg1->billing_postal_code, uc_address_field_required('postal_code'), NULL, 10, 10);
      }
      if (uc_address_field_enabled('phone')) {
        $contents['billing_phone'] = uc_textfield(uc_get_field_name('phone'), $arg1->billing_phone, uc_address_field_required('phone'), NULL, 32, 16);
      }

      return array('description' => $description, 'contents' => $contents, 'theme' => 'address_pane');

    case 'process':
      $arg1->billing_first_name = $arg2['billing_first_name'];
      $arg1->billing_last_name = $arg2['billing_last_name'];
      $arg1->billing_company = $arg2['billing_company'];
      $arg1->billing_street1 = $arg2['billing_street1'];
      $arg1->billing_street2 = $arg2['billing_street2'];
      $arg1->billing_city = $arg2['billing_city'];
      $arg1->billing_zone = $arg2['billing_zone'];
      $arg1->billing_postal_code = $arg2['billing_postal_code'];
      $arg1->billing_country = $arg2['billing_country'];
      $arg1->billing_phone = $arg2['billing_phone'];
      return TRUE;

    case 'review':
      $review[] = array('title' => t('Address'), 'data' => uc_order_address($arg1, 'billing', FALSE));
      if (uc_address_field_enabled('phone') && !empty($arg1->billing_phone)) {
        $review[] = array('title' => t('Phone'), 'data' => check_plain($arg1->billing_phone));
      }
      return $review;
  }
}

*/







/**
 * Allow a customer to make comments on the order.
 */
function uc_checkout_pane_comments($op, &$arg1, $arg2) {
  switch ($op) {
    case 'view':
      $description = t('Use this area for special instructions or questions regarding your order.');

      if (!empty($arg1->order_id)) {
        $default = db_result(db_query("SELECT message FROM {uc_order_comments} WHERE order_id = %d", $arg1->order_id));
      }
      $contents['comments'] = array(
        '#type' => 'textarea',
        '#title' => t('Order comments'),
        '#default_value' => $default,
      );

      return array('description' => $description, 'contents' => $contents);

    case 'process':
      if (strlen($arg2['comments']) > 0) {
        db_query("DELETE FROM {uc_order_comments} WHERE order_id = %d", $arg1->order_id);
        uc_order_comment_save($arg1->order_id, 0, $arg2['comments'], 'order', uc_order_state_default('post_checkout'), TRUE);
      }
      return TRUE;

    case 'review':
      $result = db_query("SELECT message FROM {uc_order_comments} WHERE order_id = %d", $arg1->order_id);
      if ($comment = db_fetch_object($result)) {
        $review[] = array('title' => t('Comment'), 'data' => check_plain($comment->message));
      }
      return $review;
  }
}

// Theme the delivery/billing address forms in tables.
function theme_address_pane($form) {
  $req = '<span class="form-required">*</span>';

  if (isset($form['copy_address'])) {
    $output = drupal_render($form['copy_address']);
  }

  $output .= '<div class="address-pane-table"><table>';

  foreach (element_children($form) as $field) {
    if (substr($field, 0, 9) == 'delivery_' || substr($field, 0, 8) == 'billing_') {
      $title = $form[$field]['#title'] .':';
      unset($form[$field]['#title']);
      if (substr($field, -7) == 'street1') {
        $title = uc_get_field_name('street') .':';
      }
      elseif (substr($field, -7) == 'street2') {
        $title = ' ';
      }
      $output .= '<tr><td class="field-label">';
      if ($form[$field]['#required']) {
        $output .= $req;
      }
      $output .= $title .'</td><td>'. drupal_render($form[$field]) .'</td></tr>';
    }
  }
  $output .= '</table></div>';

  foreach (element_children($form) as $element) {
    $output .= drupal_render($form[$element]);
  }

  return $output;
}

/**
 * Find the collapsible pane displayed above the pane with an ID of $pane_id.
 */
function _uc_cart_checkout_prev_pane($panes, $pane_id = NULL) {
  if (is_null($pane_id)) {
    return FALSE;
  }

  $prev = FALSE;
  foreach ($panes as $target) {
    if ($target['id'] == $pane_id) {
      return $prev;
    }
    if ($target['collapsible'] && variable_get('uc_pane_'. $target['id'] .'_enabled', TRUE)) {
      $prev = $target['id'];
    }
  }

  return FALSE;
}

/**
 * Find the pane that displays below the pane with an ID of $pane_id.
 */
function _uc_cart_checkout_next_pane($panes, $pane_id = NULL) {
  if (is_null($pane_id)) {
    return FALSE;
  }

  $next = FALSE;
  foreach ($panes as $target) {
    if ($next) {
      if ($target['collapsible'] && variable_get('uc_pane_'. $target['id'] .'_enabled', TRUE)) {
        return $target['id'];
      }
    }
    if ($target['id'] == $pane_id) {
      $next = TRUE;
    }
  }

  return FALSE;
}

/**
 * Build a list of checkout panes defined in the enabled modules.
 */
function _checkout_pane_list($action = NULL) {
  static $panes;

  if (count($panes) > 0 && $action !== 'rebuild') {
    return $panes;
  }

  $panes = module_invoke_all('checkout_pane', NULL);
  foreach ($panes as $i => $value) {
    $panes[$i]['enabled'] = variable_get('uc_pane_'. $panes[$i]['id'] .'_enabled', (!isset($panes[$i]['enabled']) ? TRUE : $panes[$i]['enabled']));
    $panes[$i]['weight'] = variable_get('uc_pane_'. $panes[$i]['id'] .'_weight', (!isset($panes[$i]['weight']) ? 0 : $panes[$i]['weight']));
    $panes[$i]['review'] = !isset($panes[$i]['review']) ? TRUE : $panes[$i]['review'];
    $panes[$i]['process'] = !isset($panes[$i]['process']) ? TRUE : $panes[$i]['process'];
    $panes[$i]['collapsible'] = !isset($panes[$i]['collapsible']) ? TRUE : $panes[$i]['collapsible'];
  }
  usort($panes, 'uc_weight_sort');

  return $panes;
}

/**
 * Return data from a checkout pane by pane ID and the array key.
 */
function _checkout_pane_data($pane_id, $key) {
  $panes = _checkout_pane_list();
  foreach ($panes as $pane) {
    if ($pane['id'] == $pane_id) {
      return $pane[$key];
    }
  }
}







// my changes -----------------------------------------------------

function theme_cart_review_table($show_subtotal = TRUE) {


// эта функция переопределена в теме, так что всё, что описано в этой функции, не имеет смысла!

// !!!!!!!!!!!!!!!!!!!!!!!!!!!!















// моё вмешательство 1

// вводим возможность подсчёта стоимости упаковки, суммируя стоимости упаковки отдельных товаров
// определим номер атрибута, описывающий упаковку
// положим, что название мы знаем, и оно меняться не будет

    $gift_attr_name = 'Gift wrap (input)'; //"Подарочная упаковка (выберите)";

    // если бы кодировка была неправильная, можно было бы её поправить вот такой функцией (она определна в моём модуле uc_import_csv_m.module)
    //$gift_attr_name = cp1251_to_utf8_recursive($gift_attr_name);


    $gift_attr_aid = db_result(db_query("SELECT aid FROM {uc_attributes} WHERE name = '%s'", $gift_attr_name));

    //drupal_set_message('=> gift_attr_name = ' . $gift_attr_name);
    //drupal_set_message('=> gift_attr_aid = ' . $gift_attr_aid);



// конец моего вмешательства










  $items = uc_cart_get_contents();
  $subtotal = 0;
  $total_qty = 0;
  
  $output = '<table class="cart-review"><thead>'
           .'<tr class="first last odd"><td class="first odd qty">'. t('Qty')
           .'</td><td class="even products">'. t('Products')
           .'</td><td class="last odd price">'. t('Price')
           .'</td></tr></thead><tbody>';

  $row = 1;
  for ($i = 0; $i < count($items); $i++) {
    $item = $items[$i];

    $rows = array();

    foreach ($item->options as $option) {
      // $rows[] = $option['attribute'] .': '. $option['name'];
      $rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
    }
    $desc = check_plain($item->title) . theme('item_list', $rows, NULL, 'ul', array('class' => 'product-options'));

    $total = ($item->qty) ? $item->qty * $item->price : $item->price;
    $subtotal += $total;












// моё вмешательство
// подсчёт стоимости упаковки

   /*
     if(isset($item->data['attributes'][$gift_attr_aid]))
      {
        //foreach($_POST['cpid' . $item['cpid'] ] as $aid => $option)
          //drupal_set_message('---------aid => option =' . $aid . '=>' . $option);
          drupal_set_message('---------=>item->data[attributes][2] = ' . $item->data['attributes'][$gift_attr_aid]);

        $gift_wrap_price = 5;

        $gift_wrap_total = ($item->qty) ? $item->qty * $gift_wrap_price : $gift_wrap_price;
        $gift_wrap_subtotal +=  $gift_wrap_total;


        $gift_wrap_amount_total = ($item->qty) ? $item->qty  : 0;
        $gift_wrap_amount_subtotal += $gift_wrap_amount_total;
      }

     */

   
   // до добавления этой проверки выдавало странную ошибку  Fatal error: Cannot use string offset as an array in Z:\home\zapokupkami.com\www\sites\all\modules\_uc\uc_gift_wrap\uc_gift_wrap.module on line 86
   if(isset($item->data['attributes'][$gift_attr_aid]))
   {   
   
      switch($item->data['attributes'][$gift_attr_aid])
       {
       	 case 12:
       	    $gift_wrap_price = 0;  // обёртка
       	    break;

         case 13:
            $gift_wrap_price = 5; // коробка
       	    break;

         case 14:
       	 default:
            $gift_wrap_price = 10;  //  нет
       }
   }
   else 
    $gift_wrap_price = 10;  //  нет
   
   
      $gift_wrap_total = ($item->qty) ? $item->qty * $gift_wrap_price : $gift_wrap_price;
      $gift_wrap_subtotal +=  $gift_wrap_total;


      if($gift_wrap_price > 0 AND $item->qty)
        $gift_wrap_amount_total = $item->qty;
       else
        $gift_wrap_amount_total =0;

      $gift_wrap_amount_subtotal += $gift_wrap_amount_total;













    $qty = ($item->qty) ? $item->qty : '';
    $tr_class = ($i % 2 == 0) ? 'even' : 'odd';
    if ($show_subtotal && $i == count($items)) {
      $tr_class .= ' last';
    }

    $output .= '<tr class="'. $tr_class .'"><td class="qty">'
             . t('!qtyx', array('!qty' => $qty)) .'</td><td class="products">'
             . $desc .'</td><td class="price">'. uc_currency_format($total)
              .'</td></tr>';
  }
  if ($show_subtotal) {
    $tr_class = ($tr_class == 'even') ? 'odd' : 'even';
    $output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">'. t('Subtotall:')
              .'</span> '. uc_currency_format($subtotal) .'</td></tr>';





// моё вмешательство 2
// выведем общую сумму за все упаковки

    
    $output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
              .'colspan="4"><span name="gift" id="subtotal-title">'. t('Gift wrap subtotal (for '. $gift_wrap_amount_subtotal . ' items):')
              .'</span> '. uc_currency_format($gift_wrap_subtotal) .'</td></tr>';

    

























     //$_POST['gift'] = $gift_wrap_subtotal;


    
    
  //drupal_set_message('--------POST = ' . $_POST);  

// ------------------------------  зачем-то я это выводил, когда тестировал этот модуль
      //foreach ($_POST as $key => $value) 
        //drupal_set_message('POST as key = ' . $key . '=> value = ' . $value);



  //if (is_array($_POST)) {

// ------------------------------  зачем-то я это выводил, когда тестировал этот модуль
      //foreach ($_POST as $key => $value) 
     // {
        //drupal_set_message('POST as key = ' . $key . '=> value = ' . $value);
        //$totals[$key] = explode(';', $value);
     // }
 // }



  if (isset($_POST['gift'])){
      $totals['gift'] = explode(';', $value);
  }

    $line = $totals['gift'];
      if (!empty($line[2])) 
      {
//        $output .= '<tr><td align="right"><b>'. $line[2] .':</b></td>'
  //                .'<td align="right">'. uc_currency_format($line[1]) .'</td></tr>';

        //if ($line[3])
          //$grand_total += round($line[1], 2);





    
    $output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
              .'colspan="4"><span name="gift" id="subtotal-title">'. t('Gift wrap subtotal (for '. $gift_wrap_amount_subtotal . ' items):')
              .'</span> '. uc_currency_format($line[1]) .'</td></tr>';

     }
    


  }
  $output .= '</tbody></table>';

  return $output;
}
















// original

function theme_cart_review_table_original($show_subtotal = TRUE) {
  $items = uc_cart_get_contents();
  $subtotal = 0;

  $output = '<table class="cart-review"><thead>'
           .'<tr class="first last odd"><td class="first odd qty">'. t('Qty')
           .'</td><td class="even products">'. t('Products')
           .'</td><td class="last odd price">'. t('Price')
           .'</td></tr></thead><tbody>';

  $row = 1;
  for ($i = 0; $i < count($items); $i++) {
    $item = $items[$i];

    $rows = array();
    foreach ($item->options as $option) {
      // $rows[] = $option['attribute'] .': '. $option['name'];
      $rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
    }
    $desc = check_plain($item->title) . theme('item_list', $rows, NULL, 'ul', array('class' => 'product-options'));

    $total = ($item->qty) ? $item->qty * $item->price : $item->price;
    $subtotal += $total;
    $qty = ($item->qty) ? $item->qty : '';
    $tr_class = ($i % 2 == 0) ? 'even' : 'odd';
    if ($show_subtotal && $i == count($items)) {
      $tr_class .= ' last';
    }

    $output .= '<tr class="'. $tr_class .'"><td class="qty">'
             . t('!qtyx', array('!qty' => $qty)) .'</td><td class="products">'
             . $desc .'</td><td class="price">'. uc_currency_format($total)
              .'</td></tr>';
  }
  if ($show_subtotal) {
    $tr_class = ($tr_class == 'even') ? 'odd' : 'even';
    $output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
              .'colspan="4"><span id="subtotal-title">'. t('Subtotal:')
              .'</span> '. uc_currency_format($subtotal) .'</td></tr>';
  }
  $output .= '</tbody></table>';

  return $output;
}

