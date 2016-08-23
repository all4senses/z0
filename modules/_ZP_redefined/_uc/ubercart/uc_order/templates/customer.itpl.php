<?php
// $Id: customer.itpl.php,v 1.4.2.4 2008/07/29 22:22:44 rszrama Exp $

/**
 * This file is the default customer invoice template for Ubercart.
 */
?>

<table width="95%" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#006699" style="font-family: verdana, arial, helvetica; font-size: small;">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="5" align="center" bgcolor="#FFFFFF" style="font-family: verdana, arial, helvetica; font-size: small;">
        <?php if ($business_header) { ?>
        <tr valign="top">
          <td>
            <table width="100%" style="font-family: verdana, arial, helvetica; font-size: small;">
              <tr>
                <td>
                  <?php //[site-logo] ?>
                </td>
                <td width="98%">
                  <div style="padding-left: 1em;">
                  <span style="font-size: large;">"[site-name]"</span><br/>
                  [site-slogan]
                  </div>
                </td>
                
<?php
//<td nowrap="nowrap">
// my changes
/*

[store-address]<br />[store-phone]
*/

?>
				<td nowrap="nowrap" align="right">
				
<?php echo 'Ваш заказ <br> на покупку и доставку товаров из:<br> <strong>' . $order->data['shop_name'] . '</strong>'; ?>                
                
                  
                  
                  
                  
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <?php } ?>

        <tr valign="top">
          <td>

            <?php if ($thank_you_message) { ?>
            <p><b><?php echo t('Thanks for your order, [order-first-name]!'); ?></b></p>

            <?php if (isset($_SESSION['new_user'])) { ?>
            <p><b><?php echo t('An account has been created for you with the following details:'); ?></b></p>
            <p><b><?php echo t('Username:'); ?></b> [new-username]<br/>
            <b><?php echo t('Password:'); ?></b> [new-password]</p>
            <?php } ?>

            <p><b><?php echo t('Want to manage your order online?'); ?></b><br />
            <?php echo t('If you need to check the status of your order, please visit our home page at [store-link] and click on "My account" in the menu or login with the following link:'); ?>
            <br /><br />[site-login]</p>
            <?php } ?>

            <table cellpadding="4" cellspacing="0" border="0" width="100%" style="font-family: verdana, arial, helvetica; font-size: small;">
              <tr>
                <td colspan="2" bgcolor="#006699">
                  <b><?php echo '<div style="color:white">' . t('Purchasing Information:') . '</div>'; ?></b>
	              </td>
              </tr>
              <tr>
	              <td nowrap="nowrap">
             	    <b><?php echo t('E-mail Address:'); ?></b>
                </td>
                <td width="98%">
                  [order-email]
                </td>
              </tr>
              <tr>
                <td colspan="2">

                  <table width="100%" cellspacing="0" cellpadding="0" style="font-family: verdana, arial, helvetica; font-size: small;">
                    <tr>
                      
                 <!--    <td valign="top" width="50%">
                        <b><?php //echo t('Billing Address:'); ?></b><br />
                        [order-billing-address]<br />
                        <br />
                        <b><?php //echo t('Billing Phone:'); ?></b><br />
                        [order-billing-phone]<br />
                      </td>
                  --> 
                      <?php if (uc_order_is_shippable($order)) { ?>
                      <td valign="top" width="50%">
                        <b><?php echo t('Shipping Address:'); ?></b><br />
                        [order-shipping-address]<br />
                        <br />
                        <b><?php echo t('Shipping Phone:'); ?></b><br />
                        [order-shipping-phone]<br />
                      </td>
                      <?php } ?>
                    </tr>
                  </table>

                </td>
              </tr>
              <tr>
                <td nowrap="nowrap">
                  <b><?php echo t('Order Grand Total:'); ?></b>
                </td>
                <td width="98%">
                  <b>[order-total]</b>
                </td>
              </tr>
              <tr>
                <td nowrap="nowrap">
                  <b><?php echo t('Payment Method:'); ?></b>
                </td>
                <td width="98%">
                  [order-payment-method]
                </td>
              </tr>

              <tr>
                <td colspan="2" bgcolor="#006699">
                  <b><?php echo '<div style="color:white">' . t('Order Summary:') . '</div>'; ?></b>
                </td>
              </tr>

              <?php if (uc_order_is_shippable($order)) { ?>
              <tr>
                <td colspan="2" bgcolor="#EEEEEE">
                  <font color="#CC6600"><b><?php echo t('Shipping Details:'); ?></b></font>
                </td>
              </tr>
              <?php } ?>

              <tr>
                <td colspan="2">

                  <table border="0" cellpadding="1" cellspacing="0" width="100%" style="font-family: verdana, arial, helvetica; font-size: small;">
                    <tr>
                      <td nowrap="nowrap">
                        <b><?php echo t('Order #:'); ?></b>
                      </td>
                      <td width="98%">
                        [order-link] <?php if($order->data['order_name']) echo ' (' . $order->data['order_name'] .')'; ?>
                      </td>
                    </tr>

                    
                    
                    
                    
                    
                    
                    
                    
                    
                    

                    <tr>
                      <td colspan="2">
                        <br /><br /><b><?php echo t('Products on order:'); ?>&nbsp;</b>

                        <table width="100%" style="font-family: verdana, arial, helvetica; font-size: small;">

                          <?php if (is_array($order->products)) 
                          {


                          	// my changes --------------------------------

                          	$subtotal = 0;
                          	$total_qty = 0;
                          	$order_opt_price_total = 0;
                          	$dostavka_total = 0;
                          	$i = 0;

                          	// максимальный коэф доставки из продуктов в корзине, нужен для определения минимальной стоимости доставки
							$max_d_factor_otdel = 0;
							

                          	$output = '<table class="cart-review"><thead>'
                          	.'<tr class="first last odd"><td class="even products">'. t('Products')
                          	.'</td><td class="last odd price">'. t('Price')
                          	.'</td><td class="first odd qty">'. t('Qty')
                          	.'</td><td class="last odd price">'. t('Сумма <br>(базовая)')
                          	.'</td></tr></thead><tbody>';


                          	// ----------------------------

                          	
                          	
                          	foreach ($order->products as $product)
                            { ?>
                            
                            
<?php  // start of product cycle                           

                            /*



                            <tr>
                            <td valign="top" nowrap="nowrap">



                            <b><?php echo $product->qty; ?> x </b>
                            </td>
                            <td width="98%">






                            // my changes ----------------------------------------------






                            // original version
                            //<b><?php echo $product->title .' - '. uc_currency_format($product->price * $product->qty); ?></b>


                            <b><?php echo $product->title .' - '. uc_currency_format($product->price * $product->qty); ?></b>
                            <?php if ($product->qty > 1) {
                            echo t('(!price each)', array('!price' => uc_currency_format($product->price)));
                            } ?>
                            <br />
                            <?php echo t('Model: ') . $product->model; ?><br />
                            <?php if (is_array($product->data['attributes']) && count($product->data['attributes']) > 0) {?>
                            <?php foreach ($product->data['attributes'] as $key => $value) {
                            echo '<li>'. $key .': '. $value .'</li>';
                            } ?>
                            <?php } ?>
                            <br />
                            </td>
                            </tr>





                            */






                            // my changes -----------------------

                            // new version -------------------



                            $i++;
                            $qty = ($product->qty) ? $product->qty : '';
                            $tr_class = ($i % 2 == 0) ? 'even' : 'odd';
                            if (/*$show_subtotal && */$i == count($order->products)) 
                            {
                            	$tr_class .= ' last';
                            }


                            
                            $order_opt_price_total += $product->data['#opt_price'] * $qty;
                            $dostavka_total += $product->data['#dost_price'] * $qty;
                            
                            // всего без доставки, с дополнениями
                            $total += $product->price * $qty;
                            // всего с доставкой и дополнениями
                            $subtotal += ($product->price + $product->data['#dost_price']) * $qty;
                            
                            
                            
                            //$total_qty += $qty;
							if(strpos($product->data['sell_measure'], 'шт') === FALSE)
								$total_qty += 1;
							else
								$total_qty += $product->qty;
						
						
							// найдём максимальный коэффициент доставки среди всех товаров
							// и на основе него вычислим коэффициент для минимальной стоимости доставки
							// которую определим как произведение минимальной стоимости доставки по умолчанию на этот коэффициент
							if($max_d_factor_otdel < $product->data['#d_factor'])
								$max_d_factor_otdel = $product->data['#d_factor'];
                            
                            
                            

                            /*
                            $output .= '<tr class="'. $tr_class .'"><td class="qty">'
                            . t('!qtyx', array('!qty' => $qty)) .'</td><td class="products">'
                            . $desc .'</td><td class="price">'. uc_currency_format($total)
                            .'</td></tr>';
                            */






                            // my changes ----------------------------------------


                            // options - attributes-------------------------

                            // тут я корректирую так, чтобы атрибуты не выводились, если значение опции равно "Нет" или пусто "",
                            // то есть, например, если комментариев "Нет" или выбран вариант с упаковкой "Нет"
                            // тогда просто не показываем этот атрибут

                            // также убираем из названия атрибута пояснения, то есть, то, что в скобках



                            foreach($product->data['attributes'] as $attr => $option)
                            {
                            	$attr_name = explode('(', $attr);
                            	$attr_name = rtrim($attr_name[0]);



                            	if($option != 'Нет' AND $option != '') // my change
                            	{

                            		// сохраним общую стоимость опций, чтобы показать её в соседней колонке, вместе со стоимостью доставки
                            		// !!!кстати, стоимость доставки должна начисляться на стоимость товара со всеми опциями!!!!!!!

                            		// неправильно
                            		//$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name  = '%s'", $attr));
                            		//$o_price = db_result(db_query("SELECT price from {uc_attribute_options} WHERE name  = '%s' AND aid = %d", $option, $aid));



                            		$o_price = 0;
                            		$aids = db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $attr);
                            		while($aid = db_fetch_array($aids))
                            		{
                            			if($oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s' AND aid = %d", $option, $aid['aid'])))
                            			{
                            				if($o_price = db_result(db_query("SELECT price FROM {uc_product_options} WHERE oid = %d AND nid = %d", $oid, $product->nid)))
                            				break;
                            			}
                            		}





                            		if($o_price) // если у опции есть цена, указываем её в скобках
                            			$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option . ' (+' . uc_currency_format($o_price). ')'  )); // my change
                            			//$rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
                            		else  // иначе ничего про цену не пишем
                            			$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option )); // my change

                            	}
                            	//$rows[] = $option['attribute'] .': '. $option['name'];
                            }
                            
                            $desc = check_plain($product->title) . theme('item_list', $rows, NULL, 'ul', array('class' => 'product-options'));


                            // добавим колонку с ценой за единицу, так как до этого была только колонка Total

                            // если не накопилось стоимости за опции, не показываем и пометку о стоимости выбранных опций
                            if($product->data['#opt_price'] > 0)
                            	$price_descr = '<br> (в т.ч. цена: ' . uc_currency_format($product->price - $product->data['#opt_price']) . ', выбр. дополн.: ' . uc_currency_format($product->data['#opt_price']) . ',<br>дост.: ' . uc_currency_format($product->data['#dost_price']) . ' - ' . round((100 * $product->data['#dost_price'])  /  $product->price, 2) . '%)';
                            else
                            	$price_descr = '<br> (в т.ч.  цена: ' . uc_currency_format($product->price) . ',<br> дост.: ' . uc_currency_format($product->data['#dost_price']) . ' - ' . round((100 * $product->data['#dost_price'])  /  $product->price, 2) . '%)';


                            $output .= '<tr class="'. $tr_class .'"><td class="products">'
                            . $desc .'</td><td class="price">'
                            . uc_currency_format($product->price + $product->data['#dost_price']) . $price_descr .'</td><td class="qty">'
                            . t('-!qty' . $product->data['sell_measure'] .'-', array('!qty' => $qty)) .'</td><td class="price">'. uc_currency_format(($product->price + $product->data['#dost_price']) * $qty)
                            .'</td></tr>';


//$data['qty'][] = array('data' => '- ' . $product->qty .  ' ' . $product->data['sell_measure'] .  ' -', 'align' => 'right');




?>
                          
                          
                          
                          <?php } // end of product cycle

                              }   //end of if(is_array(...        ?>


                              
                              
                              
<?php 




// если общая стоимость заказа меньше минимальной суммы заказа для этого магазина и клиента
				// с учётом максимального коэффициента доставки товаров этой корзины
				// то заменяем стоимость доставки на минимальную стоимость доставки, умноженную на максимальный коэфициент доставки

				// найдём минимальную стоимость доставки по умолчанию
				$zp_default_set = zp_functions_get_zp_default_set();

				$max_d_factor_otdel =  $max_d_factor_otdel * 10;

			
				$c_shop_tids = zp_functions_get_cart_shop_data($products[0]->data['order_uid'], $products[0]->data['order_uid'], $products[0]->nid); 

				
				//echo 'products[0]->nid = ' . $products[0]->nid . '<br>';
				//echo 'products[0]->data[order_uid] = ' . $products[0]->data['order_uid'] . '<br>';
				//echo 'total = ' . $total . '<br>';
				//echo 'total_dost_price = ' . $total_dost_price . '<br>';
				//echo 'max_d_factor_otdel = ' . $max_d_factor_otdel . '<br>';
				//echo 'shop_min_sum = ' . $c_shop_tids['shop_min_sum'] . '<br>';
				
				
				//if($subtotal < $c_shop_tids['shop_min_sum'])
				if( ($subtotal - $dostavka_total) < $c_shop_tids['shop_min_sum'])
				{
					// изменяем стоимость доставки на миниманую по умолчанию и корректируем общую стоимость
					$subtotal = $subtotal - $dostavka_total;
					$dostavka_total = $zp_default_set['min_dost_price_default']*$max_d_factor_otdel;
					$subtotal = $total + $dostavka_total;
					$flag_min_dost_price = 1;

				}
				
				

$tr_class = ($tr_class == 'even') ? 'odd' : 'even';

if($order_opt_price_total > 0)
	$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
	.'colspan="4" align = "right"><span id="subtotal-title">'. t('Итого (за ' . $total_qty . ' шт) с обычной доставкой (предварительная, базовая сумма): ')
	.'</span> '. uc_currency_format($subtotal) .'</td></tr>';
else
	$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
	.'colspan="4" align = "right"><span id="subtotal-title">'. t('Итого (за ' . $total_qty . ' шт) с обычной доставкой (предварительная, базовая сумма): ')
	.'</span> '. uc_currency_format($subtotal) .'</td></tr>';

$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
.'colspan="4" align = "right"><span id="subtotal-title">'. t('В т.ч. стоимость с выбр.дополнениями, без доставки: ')
.'</span> '. uc_currency_format($total) .'</td></tr>';



/* // пока не будем показывать отдельно стоимость дополнений
if($order_opt_price_total > 0)
$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
.'colspan="4" align = "right"><span id="subtotal-title">'.'В т.ч. выбранные дополнения: '
.'</span> '. uc_currency_format($order_opt_price_total) .'</td></tr>';
*/

// ещё добавим общую сумму за доставку
// не будем указывать, сколько процентов стоит общая доставка, так как проценты для разных отделов одного магазина могут различаться
// таким образом общий процент в общем случае неизвестен

if($flag_min_dost_price == 1)
{
	$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
		.'colspan="4" align = "right"><span id="subtotal-title">'. t('В т.ч. стоимость обычной (не срочной) доставки</strong><br> (которая была увеличена до минимальной стоимости доставки по умолчанию,<br> так как пока что общая сумма заказа (без учёта доставки) меньше минимальной суммы заказа<br> для Вас в этом заведении):')
		//.'colspan="4"><span id="subtotal-title">'. t('В том числе стоимость доставки (' . $d_factor_otdel*100 . '%):')
		.'</span> '. uc_currency_format($dostavka_total) .'</td></tr>';
}
else 
{
	$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
		.'colspan="4" align = "right"><span id="subtotal-title">'. t('В т.ч. стоимость обычной (не срочной) доставки:')
		//.'colspan="4"><span id="subtotal-title">'. t('В том числе стоимость доставки (' . $d_factor_otdel*100 . '%):')
		.'</span> '. uc_currency_format($dostavka_total) .'</td></tr>';
	
}



print $output;


//print '<PRE>';
//print_r($order);
//print '<PRE>';


?>                              
                              
                      </td>

                    </tr>
                  </table>

                </td>
              </tr>
              
              
              

              
              
                    <?php if ($shipping_method && uc_order_is_shippable($order)) { ?>
                    <tr>
                       <!--<td  nowrap="nowrap"> -->
                       <td >
                        <b><?php echo t('Shipping Method:'); ?></b>
          <!--           </td>
                      <td width="98%"> -->
                        [order-shipping-method]
                      </td>
                    </tr>
                    <?php } ?>
                    
                    <tr><td><p></td></tr>
                    <tr><td><b><?php echo t('Расчёт стоимости заказа...'); ?></b>&nbsp;</td></tr>

                    <tr>
                      <td nowrap="nowrap">
                        <?php echo t('Стоимость товаров, без доставки:'); ?>&nbsp;
                      </td>
                      <td width="98%">
                        [order-subtotal]
                      </td>
                    </tr>

                    <?php foreach ($line_items as $item) {
                    	if ($item['line_item_id'] == 'subtotal' || $item['line_item_id'] == 'total') {
                    		continue;
                    }?>

                    <tr>
                      <!-- <td nowrap="nowrap"> -->
                      <td>
                        <?php echo $item['title']; ?>: 
                      </td>
                      <td>
                        <?php echo uc_currency_format($item['amount']); ?>
                      </td>
                    </tr>
                    
                    <?php } ?>

                    <tr>
                      <td>&nbsp;</td>
                      <td>------</td>
                    </tr>

                    <tr>
                      <td nowrap="nowrap">
                        <b><?php echo t('Total for this Order:'); ?>&nbsp;</b>
                      </td>            
                      <td>
                        <b>[order-total]</b>
                      </td>
                    </tr>
              
                    
                    
                    
                    
                    
                    
                    
                    
                    
              
                <table width="100%" style="font-family: verdana, arial, helvetica; font-size: small;">
              <tr>
                <td>
					<?php 
					echo '<b>Желаемое клиентом время доставки:</b> <br>' . $order->data['delivery_time'];
					$comments = db_result(db_query("SELECT message FROM {uc_order_comments} WHERE order_id = %d", $order->order_id));
					if($comments AND $comments != '-')
					echo '<br><br><b>Комментарии клиента относительно заказа:</b> <br>' . $comments;
					//uc_order_comments_load($arg1->order_id);



					?>
                </td>
					
				<td nowrap="nowrap" align="right">
				
<?php  



?>                
                
                  
                  
                  
                  
                </td>
              </tr>
            </table>
            
            
            
            
            
            
            
                
                    
              
              <?php if ($help_text || $email_text || $store_footer) { ?>
              <tr>
                <td colspan="2">
                  <hr noshade="noshade" size="1" /><br />

                  <?php if ($help_text) { ?>
                  <p><b><?php echo t('Where can I get help with reviewing my order?'); ?></b><br />
                  <?php echo t('To learn more about managing your orders on [store-link], please visit our <a href="[store-help-url]">help page</a>.'); ?>
                  <br /></p>
                  <?php } ?>

                  <?php if ($email_text) { ?>
                  <p><?php echo t('Please note: This e-mail message is an automated notification. Please do not reply to this message.'); ?></p>

                  <p><?php echo t('Thanks again for shopping with us.'); ?></p>
                  <?php } ?>

                  <?php if ($store_footer) { ?>
                  <p><b>[store-link]</b><br /><b>[site-slogan]</b></p>
                  <?php } ?>
                </td>
              </tr>
              <?php } ?>

            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</td>
</tr>
</table>
