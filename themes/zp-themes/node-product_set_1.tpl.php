<?php 


// получим справочные статьи с пересекающимися с этим товаром ключевыми словами и выдадим запрашивающей функии по ajax
// эти данные будут встроены с помощью ajax во вкладку со статьями

if($_GET['source'] == 'ajax')
{

 $photo_data = explode(';', $node->field_prodtype_pic_src_n_num[0]['view']);
  //$photo_data[0] - type of product
  //$photo_data[1] - source of pics
  //$photo_data[2] - num of pics
  

  // получим номер словаря c ключевыми словами, имеющимися в статьях, по названию словаря
  //$issue_vid = db_result(db_query("SELECT vid from {vocabulary} WHERE name = '%s'", 'Issues keywords'));
  $issue_vid = 6; // зададим явно номер словаря, чтоб быстрее
  
  
  // ключевые слова должны браться из поля с ключевыми словами, но пока что для ускорения считаем ключевым словом тип товара (и название директории с картинками) 
  // раскомментировать строку ниже для использования поля с ключевыми словами (и закомментировать)
  
  if($node->field_zp_bar_world[0]['view'] AND $node->field_zp_bar_world[0]['view'] != '' AND $node->field_zp_bar_world[0]['view'][0] != '2')
  	$keywords_bw = db_result(db_query("SELECT keywords FROM {p_descr_bw} WHERE p_bw = %s", $node->field_zp_bar_world[0]['view']));
  

  if($keywords_bw AND $keywords_bw != '')
  	$keywords = $keywords_bw;
  
  if($node->field_keywords[0]['value'])
  {
  	if($keywords AND $keywords != '')
  		$keywords .= ',' . $node->field_keywords[0]['value'];
  	else
  		$keywords = $node->field_keywords[0]['value'];
  }
  	

  if($keywords AND $keywords != '')
  	//$keywords = explode(',', $node->field_keywords[0]['value']);
  	$keywords = explode(',', $keywords);
  
  $keywords[] = $photo_data[0];

 if($keywords)
  foreach ($keywords as $keyword)
  {
  	
  	if(($keyword = trim($keyword)) != '')
  		$tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' AND vid = %d", $keyword, $issue_vid));
  	else 
  		continue;
  		
  	$issues_nids = db_query("SELECT nid from {term_node} WHERE tid = %d", $tid);
  	
    while($issue = db_fetch_array($issues_nids))
    {
    	$issue_name = db_result(db_query("SELECT title from {node} WHERE nid = %d", $issue['nid']));
    	//$issue_teaser = db_result(db_query("SELECT field_issue_teaser_value from {content_type_zp_issue} WHERE nid = %d", $issue['nid']));
    	$issue_teaser = db_result(db_query("SELECT field_issue_teaser_value from {content_field_issue_teaser} WHERE nid = %d", $issue['nid']));
    	
    	
    	//$issues[$issue['nid']] = $issue_name;  
    	$issues[$issue['nid']]['title'] = $issue_name;
    	$issues[$issue['nid']]['teaser'] = $issue_teaser; 
    }
  }
  

	echo '<div class="ajaxed">';

		if($issues)
		{
			foreach ($issues as $nid => $issue)
			{
  				//echo l($issue, 'node/' . $nid, array('class' => 'issue', 'target' => '_blank')) . '<br>';
  				
  				//echo l($issue, 'node/' . $nid, array('class' => 'issue')) . '<br>';
  				
  				//echo l($issue['title'], 'node/' . $nid, array('class' => 'i_title', 'title' => 'Перейти на страницу со статьёй "' . $issue['title'] . '".')) . '<br>';
  				echo '<div class="i_title">' . l($issue['title'], 'node/' . $nid, array('title' => 'Перейти на страницу со статьёй "' . $issue['title'] . '".')) . '</div>';
  				if($issue['teaser']) 
  					echo '<div class="i_teaser">' . $issue['teaser'] . '</div>';
			}
		}
		else 
		{
			echo 'По данному товару пока нет справочных данных или статей.';
		}
		
	echo '</div>';
	
	return;
	
}

// конец формирования данных для выдачи ajax-функции













// заменяем кавычки
$title_corrected = str_replace('&quot;', "'", $title);


global $user;


//if teaser ---------------------------------------------------------------------------------



if ($teaser == 1): 



//echo 'show_price = ' . $show_price . '<br>';

// показываем спрятанный товар только админу, а позже указываем особый вид для этих товаров (через css)

if($user->uid != 1)
{
	// скрытые и дорогие товары, которые не должеы показываться, показываем только админу, а позже указываем особый вид для этих товаров (через css)
	if($node->field_status[0]['view'] == '0' OR ($is_costly AND $show_costly == 4))
		return;
}

	
	
if($node->content['field_weight_ingroup']['#value'] === NULL)	
	$node->content['field_weight_ingroup']['#value'] = 0;	
	

?>
 <div class="product_teaser type_1 
	<?php 
		//if ($show_price < 0) echo ' no_dost';  
		
		
		if ($show_price < 0 OR ($is_costly AND $show_costly == 3)) echo ' no_price'; // не показывать цену  если юзер не авторизован или если товар дорогой с установками системы не показывать цену и доставку дорогим товарам ---> означает также не показывать и кнопку доставки, т.е. сообщается, что товар есть, но узнать цену и доставить нельзя
		
		if ($is_costly) echo ' costly';  // флаг, что товар дорогостоящий
		
		//if ($is_costly AND $show_costly == 2) echo ' only_price';  // если нужно показать только цену, не разрешая доставку
		if ($is_costly AND $show_costly != 1) echo ' no_dostav';  // цена может и показываться (предыдущее условие), но не показывается стоимость доставки и кнопка доставки
		
		if($u_costly) // если у юзера персональные настройки по отображению дорогих товаров
 			echo ' u_costly';
		
		//if($node->field_status[0]['view'] == '0' OR ($user->uid == 1 AND $is_costly AND ( $show_costly == 2 OR $show_costly == 3 OR $show_costly == 4 )) ) echo ' hidden admin';
		
		//if($node->field_status[0]['view'] == '0' OR ($user->uid == 1 AND $is_costly AND ($show_costly == 3 OR $show_costly == 4 )) ) echo ' hidden';	
		if($node->field_status[0]['view'] == '0' OR ($is_costly AND $show_costly == 4)) echo ' hidden';	
		
		if($user->uid == 1) echo ' admin';
		
		?>">

    <a href="<?php print $node_url ?>" title="Перейти к подробному описанию товара <?php echo $title; if($node->weight) echo ', ' . $node->weight . ' ' . $node->weight_units ?>">
      <div class="title">
       <?php 
        //print check_plain($title);
        echo $title;
        
        //if($node->weight)
        	//echo ', <span class="ves">' . $node->weight . ' ' . $node->weight_units . '</span>';
       ?>
      </div>
      
      
	  <div class="image">
	  <table><td>
	  
	  <?php
   
	  
	    $photo_data = explode(';', $node->field_prodtype_pic_src_n_num[0]['view']);
   		//$photo_data[0] - type of product
   		//$photo_data[1] - source of pics
   		//$photo_data[2] - num of pics
   		
   		$files_p_base_path = base_path() . 'files/p/';
                $photo_base_path = $files_p_base_path . $photo_data[0] . '/';
   		
   		switch ($photo_data[1]) //$photo_data[1] - source of pics
   		{
   			/*
   			case '2': // 2 = источник названия картинок - штрих-код производителя
   				$proizv_id = explode(';', $node->field_proizv[0]['view']); // считаем, что если источником картинок является поставщик, то данная переменная заполнена (т.е. для товара указан ид и название производителя)
	     		$proizv_id = trim($proizv_id[0]);
	     		
  				//$photo_base_name = $photo_base_path . 'b' . $proizv_id . '-' . $node->field_zp_bar_proizv[0]['view']; //'bm = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bm0034-2298094850938-1.jpg
  				$photo_base_name = 'b' . $proizv_id . '/b' . $proizv_id . '-' . $node->field_zp_bar_proizv[0]['view']; //'bm = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bm0034-2298094850938-1.jpg
   				break;
   			*/
   			
   			case '3': // 3 = источник названия картинок - арт производителя
   				$proizv_id = explode(';', $node->field_proizv[0]['view']); // считаем, что если источником картинок является поставщик, то данная переменная заполнена (т.е. для товара указан ид и название производителя)
	     		$proizv_id = trim($proizv_id[0]);
	     		
  				//$photo_base_name = $photo_base_path . 'b' . $proizv_id . '-' . $node->field_zp_bar_proizv[0]['view']; //'bm = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bm0034-2298094850938-1.jpg
  				
  				//$photo_base_name = 'a' . $proizv_id . '/a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view']; //'bm = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bm0034-2298094850938-1.jpg
  				$photo_base_name = $proizv_id . '/a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view'] . '/a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view']; //'bm = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bm0034-2298094850938-1.jpg
   				break;
   			
   			/*	
   			case '4': // 4 = источник названия картинок - штрих-код поставщика
   				$postav_id = explode(';', $node->field_postav[0]['view']); // считаем, что если источником картинок является поставщик, то данная переменная заполнена (т.е. для товара указан ид и название поставщика)
     			$postav_id = trim($postav_id[0]);
     		
   			 	//$photo_base_name = $photo_base_path . 'b' . $postav_id . '-' . $node->field_zp_bar_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   			 	$photo_base_name = 'b' . $postav_id . '/b' . $postav_id . '-' . $node->field_zp_bar_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   				break;
   			*/	
   			
   			case '5': // 5 = источник названия картинок - арт поставщика
   				$postav_id = explode(';', $node->field_postav[0]['view']); // считаем, что если источником картинок является поставщик, то данная переменная заполнена (т.е. для товара указан ид и название поставщика)
     			$postav_id = trim($postav_id[0]);
     		
   			 	//$photo_base_name = $photo_base_path . 'b' . $postav_id . '-' . $node->field_zp_bar_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   			 	$photo_base_name = $postav_id . '/a' . $postav_id . '-' . $node->field_zp_art_postav[0]['view'] . '/a' . $postav_id . '-' . $node->field_zp_art_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   				break;
   			/*
			case '6': //6 = источник названия картинок - штрих-код магазина
   				//$photo_base_name = $photo_base_path  . 'b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				$photo_base_name = 'b' . substr($node->model, 0, 8) . '/b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				break;
   			*/
   				
   			case '7': //7 = источник названия картинок -  артикул магазина
   				//$photo_base_name = $photo_base_path  . 'b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				$photo_base_name = substr($node->model, 0, 8) . '/a' . substr($node->model, 0, 8) . '-' . $node->field_zp_art_shop[0]['view'] . '/a' . substr($node->model, 0, 8) . '-' . $node->field_zp_art_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				break;
   				
   			case '1': // 1 = источник названия картинок - штрих-код мировой
   			default:
   				//$photo_base_name = $photo_base_path . $node->field_zp_bar_world[0]['view']; 
   				
   				//$photo_base_name = $node->field_zp_bar_world[0]['view']; 
   				$photo_base_name = $node->field_zp_bar_world[0]['view'] . '/' . $node->field_zp_bar_world[0]['view']; 
   				break;

   		}
   		
   		
   		//$photo_base_name = $photo_base_path . $photo_base_name . '/' . $photo_base_name;
   		$photo_base_name = $photo_base_path . $photo_base_name;
   		
   		//echo 'photo_base_name = ' . $photo_base_name . '<BR>';
   		
   		//echo '<div class="image_big">';
   		$photo_exist = 0; // первая (или первая существующая на сервере) картинка большая, остальные маленькие
   		//for($i = 0; $i < $photo_data[1];) //$photo_data[1] - num of pics
   		
                
                
                //////////for($i = 0; $i < $photo_data[2];) //$photo_data[1] - num of pics
                for($i = 0; $i < 1;) //$photo_data[1] - num of pics
   		{
   			$i++;
   			$next_photo = $photo_base_name . '-' . $i . '.jpg';
   			//echo '<div class="photo">' . 'photo_base_name = ' . $next_photo . '</div>';
   			//clearstatcache(); // вроде как очищает кеш, но кажется это не нужно тут
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $next_photo))
   				{
   					//echo '<a href="' . $next_photo . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip]['. $title . ' ' . $i .']">';
   					if($photo_data[3] == 'h')
   						echo theme('imagecache', 'product_teas_type1-h', $next_photo, $title_corrected . ' ' . $i . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'"); // третий аргумент - alt
   					else 
   						echo theme('imagecache', 'product_teas_type1-v', $next_photo, $title_corrected . ' ' . $i . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'"); // третий аргумент - alt
   					//echo '<a/>;
   					$photo_exist = 1;	
   					break;
   				}
   				// закомментировать, если захочется проверять, есть ли вторая картинка при отсутствующей первой
   				break;
   		}
   		
   		if(!$photo_exist)
      {
          /*
          if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photo_base_path . '/default.jpg'))
            //echo theme('imagecache', 'product_teas_type1-v', $photo_base_path . '/default.jpg');
            echo theme('imagecache', 'product_teas_type1-default-v', $photo_base_path . '/default.jpg', $title_corrected . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'");
          else
          */
              echo '<div class="image_big">'
          . theme('imagecache', 'product_teas_type1-default-v',  $files_p_base_path . 'default_all.jpg', $title_corrected . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'" ) // третий аргумент - alt
          . '</div>';

      }
                
   		//echo '</div>'; 
   		  
   		// old version 	
   		//echo theme('imagecache', 'product_img_teaser', $node->field_product_img[0]['filepath'], $node->field_product_img[0]['alt'], $node->field_product_img[0]['title']); //, $attributes); 
   		?> 
   		
   	   </td></table>
   	   </div> <?php /* end of image */ ?>
       
   	</a>
   	


   	
   	
    <?php 
    
    
    
       print '<div class="a_to_c_fixed"><table><td class="a_to_c_td">';
    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 
		
       
       //echo 'is_costly = ' . $is_costly . '<br>';
       //echo 'show_costly = ' . $show_costly . '<br>';
       //echo 'user->uid = ' . $user->uid . '<br>';
       //echo 'show_price = ' . $show_price . '<br>';
       //echo 'wrong_shop = ' . $wrong_shop . '<br>';
       
       //$show_price = 1;
       if($show_price < 0 and $user->uid != 1)
        {
         	//print $c_form1 . 'no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('< ? >'), 'user/'.$user->uid, array('title' => t('Почему?'))) . '</div></div></form></div>';    
                /*
         	if($show_price == -3) // клиент, НЕ не имеющий права на покупку в ЭТОМ магазине
				echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny', array('title' => t('Почему?'))) . '</div>';
         	else if($is_costly AND $show_costly > 1)
        	{
				if($user->uid) // если это клиент, возможно, имеющий (а может и не имеющий) право на покупку в данном магазине, но товар не доставляемый
					echo '<div class="no_dost_descr">' . 'Доставка этого элитного товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
				else // если это не зарегистрированный клиент
					echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
        	}
			else
         		echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
		*/
                
                if($show_price == -3) // клиент, НЕ не имеющий права на покупку в ЭТОМ магазине
                {
                    if($user->uid)
                        echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny', array('title' => t('Почему?'))) . '</div>';
                    else
                        $regfirst = true;

                }
         	else if($is_costly AND $show_costly > 1)
        	{
                        if($user->uid) // если это клиент, возможно, имеющий (а может и не имеющий) право на покупку в данном магазине, но товар не доставляемый
                                echo '<div class="no_dost_descr">' . 'Доставка этого элитного товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
                        else // если это не зарегистрированный клиент
                                //echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
                                $regfirst = true;
        	}
                else
                    //echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
                    $regfirst = true;
			
		if($regfirst)		
                {
                            //echo '<div class="no_dost_descr"><a href="/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom" title="Зарегистрируйтесь и войдите на сайт под своим ником, чтобы добавить товар в корзину">Станьте нашим клиентом</a><br>и закажите доставку этого товара!</div>';

                            //echo '<div class="no_dost_descr"><a href="/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom" title="Зарегистрируйтесь и войдите на сайт под своим ником, чтобы добавить товар в корзину">Станьте нашим клиентом</a><br>чтобы заказать доставку этого товара...</div>';
                             
                             $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
                             $c_form2 = str_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);

                             // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
                             $c_form2 = explode('</div>', $c_form2, 2);
                             $c_form2 = $c_form2[0] .'</div><div class="sell_measure"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];

                             // change action to '/feedback/stat-nashim-klientom-prosto'
                             preg_match('{<form.*action="(.*)"}', $c_form1, $result2);
                             $pos_temp = strpos($action_origin = trim($result2[1]), '"', 2);
                             if( $pos_temp !== false)
                                $action_origin = substr($action_origin, 0, $pos_temp);
                             $c_form1 = str_replace($action_origin, '/feedback/stat-nashim-klientom-prosto', $c_form1);
                             
                             //print $c_form1 . 'sell_price"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div><div class="edit-qty-wrapper' . $c_form2;
                             // hide price, etc... and show all the block
                             print '<div class="regfirst">' . $c_form1 . 'sell_price"' . ' ></div><div class="dost_prise regfirst"' . ' ></div><div class="edit-qty-wrapper' . $c_form2 . '</div>';                    
                    
                }
        } 
       /* 
       // если ни цена, ни доставка не показывается... Но Админу показывается и цена, так что срабатывает следующее условие
       else if( ($is_costly AND $show_costly == 3 AND $user->uid != 1))// and $user->uid != 1) 
        {
         	//print $c_form1 . 'no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('< ? >'), 'user/'.$user->uid, array('title' => t('Почему?'))) . '</div></div></form></div>';    
        	print '<div class="no_dost_descr">' . 'Доставка этого элитного товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
         
        }
        */
	   else if(
                        ($node->field_status[0]['view'] == '0')// AND $user->uid == 1) // если товар скрыт и дошло до этого шага, то и так понятно, что это админ
                        OR
                        ($is_costly AND ($show_costly == 2 OR (($show_costly == 3 OR $show_costly == 4)AND $user->uid == 1))) 
                  )
	   // если нужно показать только цену, не разрешая доставку... Админу показываем цену даже если для всех цена закрыта (как и в варианте с показом только цены для всех)
		{
			
			// убираем метку
                        //$c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
                        $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
                        //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);
                        $c_form2 = str_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);

                        // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
                        $c_form2 = explode('</div>', $c_form2, 2);
                        $c_form2 = $c_form2[0] .'</div><div class="sell_measure"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). "' . ' >' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];

                        if(!$user->uid)
                        {
                            // change action to '/feedback/stat-nashim-klientom-prosto'
                             preg_match('{<form.*action="(.*)"}', $c_form1, $result2);
                             $pos_temp = strpos($action_origin = trim($result2[1]), '"', 2);
                             if( $pos_temp !== false)
                                $action_origin = substr($action_origin, 0, $pos_temp);
                             $c_form1 = str_replace($action_origin, '/feedback/stat-nashim-klientom-prosto', $c_form1);
                             
                             //print $c_form1 . 'sell_price"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div><div class="edit-qty-wrapper' . $c_form2;
                             // hide price, etc... and show all the block
                             print '<div class="regfirst">' . $c_form1 . 'sell_price"' . ' ></div><div class="dost_prise regfirst"' . ' ></div><div class="edit-qty-wrapper' . $c_form2 . '</div>';                    
                            
                        }
                        else
                        {
                                //print $c_form1 . 'sell_price"' . ' title="Стоимость без надбавки за дополнения/опции (если они доступны). "' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div><div class="edit-qty-wrapper' . $c_form2;
                                print $c_form1 . 'sell_price"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). "' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"></div><div class="edit-qty-wrapper' . $c_form2;


                                // кнопка доставки и количество будут скрыты через css, а будет показано сообщение о невозможности доставки
                                if($node->field_status[0]['view'] == '0')
                                        print '<div class="no_dost_descr">' . 'Товар скрыт, возможно, его нет в наличии.</div>';					
                                /*
                                else if($show_costly == 2 AND $user->uid == 1)
                                        //для всех, кроме админа, цена скрыта..делаем об этом пометку в сообщении
                                        print '<div class="no_dost_descr">' . 'Доставка этого элитного товара не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
                                else if($show_costly == 3 AND $user->uid == 1)
                                        print '<div class="no_dost_descr">' . 'Доставка этого элитного товара не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . ' (цена скрыта от просмотра)</div>';		
                                else if($show_costly == 4 AND $user->uid == 1)
                                        print '<div class="no_dost_descr">' . 'Доставка этого элитного товара не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . ' (товар скрыт от просмотра)</div>';		
                                else 
                                        print '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';		
                                */
                                elseif($user->uid == 1)
                                {
                                    //для всех, кроме админа, цена скрыта..делаем об этом пометку в сообщении
                                    if($show_costly == 2) print '<div class="no_dost_descr">Доставка этого элитного товара не доступна <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki" title="Почему?">(?)</a></div>';
                                    elseif($show_costly == 3) print '<div class="no_dost_descr">Доставка этого элитного товара не доступна <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki" title="Почему?">(?)</a> (цена скрыта от просмотра)</div>';		
                                    elseif($show_costly == 4) print '<div class="no_dost_descr">Доставка этого элитного товара не доступна <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki" title="Почему?">(?)</a> (товар скрыт от просмотра)</div>';		
                                }
                                else
                                    echo '<div class="no_dost_descr">Доставка этого товара для Вас не доступна <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki" title="Почему?">(?)</a></div>';		
                        }
				
		}
       else 
       { 
		 // убираем метку
       	 //$c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
         $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
       	 //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);
         $c_form2 = str_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 print $c_form1 . 'sell_price"' . ' title="Стоимость без доставки и надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div><div class="edit-qty-wrapper' . $c_form2;           

       	 //print '<div class="sell_price">' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div>';
       	 //print $c_form1 . 'xxx"></div><div class="edit-qty-wrapper' . $c_form2;           
        
       } 

       print '</td></table></div>';
       
       //print $node->content['add_to_cart']['#value'] 
    
    
    ?>
  



 
 </div>
<?php endif; // end of teaser ?>















<?php 

//if body (page) ---------------------------------------------------------------------------------

 if ($page == 1):

 /* 
 // товар всё же будем всегда показывать, так как он индексируется гуглем. 
 // Но в зависимости от установок, будет показана цена и возможность доставки или нет
 // а также будет указано, если товара нет в наличии (hidden) или товар не доставляется по причине элитности или другой причине
 
 if($node->field_status[0]['view'] == '0') 
 {
 	echo '<div class="p_hidden">К сожалению, данный товар в настоящее время не доставляется.</div>';
 	if($user->uid != 1)
	 	return;
 }
 
if($is_costly AND $show_costly >1) 
 {
 	if($user->uid != 1 AND $show_costly == 4)
	 	return;
 	if($user->uid == 1)
	 	echo '<div class="p_hidden">Данный товар не доступен через нашу службу доставки.</div>';
 	
 }
*/
 
 
// эта операция должна проделываться ниже при формировании картинок, но задаётся тут, так как тут используются данные по типу товара в качестве ключевого слова (временно)
  /////$photo_data = explode(';', $node->field_prodtype_pic_src_n_num[0]['view']);
  // was set in template.php
  //$photo_data[0] - type of product
  //$photo_data[1] - source of pics
  //$photo_data[2] - num of pics
 

  
?>  


 <div itemscope itemtype="http://data-vocabulary.org/Product" class="product_body type_1 
 
 	<?php 
		
 		/*
 		//if ($show_price < 0 OR ($user->uid != 1 AND $is_costly AND $show_costly > 1)) echo ' no_dost';  
		if ($show_price < 0 OR ($user->uid != 1 AND $is_costly AND $show_costly > 1)) echo ' no_price';  
		
		if($node->field_status[0]['view'] == '0') echo ' hidden admin';
		
		//if($user->uid != 1 AND $is_costly AND $show_costly == 2)  echo ' only_price';
		if($user->uid != 1 AND $is_costly AND $show_costly == 2)  echo ' no_dostav';
		*/
 		
 		// из всего оставляем только в css один флаг "нет доставки" и "дорогой недоставляемый товар", 
 		// чтобы, например, пометить название отсуствующего товара сереньким цветом, а недоставляемого дорогого каким-то другим цветом
 		if($node->field_status[0]['view'] == '0' OR ($is_costly AND $show_costly > 1) ) 
 			echo ' no_dostav';
 		
    if($node->field_status[0]['view'] == '0')
      echo ' outofstock';        
    
 		if($is_costly AND $show_costly > 1) 
 			echo ' costly';
 		
 		if($u_costly) // если у юзера персональные настройки по отображению дорогих товаров
 			echo ' u_costly';
 			
 		if($user->uid == 1) echo ' admin';
 		
		?>">
  
   <div class="txt">
     
     <div class="title">
     
     	<h1 itemprop="name"><?php echo $title ?></h1>
        <?php
        

            //zp_functions_show($ukToRu_translate);
            if($ukToRu_translate AND $ukToRu_translate != '=')
            {
                echo '<h2 class="rus">' . $ukToRu_translate . '</h2>';
                
                //if($photo_data[4] != 'zp' OR $user->uid == 1)
                if($translate_suggested != 'zp' OR $user->uid == 1)
                {
                    
                    echo '<a class="fix_tr" href="#" title="Считаете, что перевод не вполне корректный? Пожалуйста, укажите свой вариант перевода.">?</a>';
                    //if($user->uid == 1 AND $photo_data[4] AND $photo_data[4] != 'zp')
                    if($user->uid == 1 AND $translate_suggested AND $translate_suggested != 'zp')
                        //echo '<div class="suggested_tr">' . $photo_data[4] . '<a href="#" class="agree_tr" title="Согласиться с предложенным переводом">+</a></div>';
                        echo '<div class="suggested_tr">' . $translate_suggested . '<a href="#" class="agree_tr" title="Согласиться с предложенным переводом">+</a></div>';

                    echo '<div class="enter_tr" style="display:none;">' 
                        . '<div>Пожалуйста, введите корректный перевод:</div>'
                        . '<input class="' . $node->nid . '" type="text" maxlength="128" name="tr" id="tr" size="20" value="" class="form-text"/>' 
                        . '<a href="#" class="send_tr">Да!</a><a href="#" class="cancel_tr">Нет.</a>' 
                        . '</div>';

                    drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_functions_fixTranslate.js');
                    
                    
                }
                else
                    echo '<div class="no_fix_tr" style="clear: both;"></div>';

            }


        /*
                echo '<div class="transl">';
                // заменяем знак процентов на слово, т.к. гугль с процентом не воспринимает строку
                $translate = str_replace('%', 'проц.', $title_corrected);
                //echo l('Перевести название (укр->рус)', "http://translate.google.com.ua/?hl=ru&tab=wT#uk|ru|" . $translate, array('title' => 'Перевести название на русский язык с помощью переводчика Google', 'rel' => 'lightframe[|width:700px; height:300px; scrolling: auto;][Перевод названия на русский язык]'));
                echo l('Перевести название (укр->рус)', "http://translate.google.com.ua/?hl=ru&tab=wT#uk|ru|" . $translate, array('title' => 'Перевести название на русский язык с помощью переводчика Google', 'rel' => 'nofollow'));
                echo '</div>';
         */
        ?> 

     	
     </div>
    
     <?php 
     
     

   // всегда показываем вкладку со ссылками на статьи, а содержание генерируем с помощью ajax при нажатии пользователем на вкладку

 		echo '<div id="tabs">';

                drupal_add_css('sites/all/modules/_Jstools/jstab/jquery.tabs.css');
                drupal_add_js('sites/all/modules/_Jstools/jstools/tabs/jquery.tabs.js'); 

                drupal_add_js('$(function(){
    		basePath = "'. base_path() .'";
    		$("#tabs").tabs({ initial: 0, fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal",  remote: 0 });
    		})', 'inline');
    	
    		echo '<ul class="tabs-nav">';
        		echo '<li><a href="#main_tab">О товаре</a></li>';
        		echo '<li><a href="#issues" id="issues_button">Справочные данные, статьи</a></li>';
    		echo '</ul>';
    		
    		echo '<div id="main_tab">';
 		
        // далее идёт описание товара

        echo '<div class="art_and_bar"><div class="label">Отдел: </div><span itemprop="category" content="' . $_SESSION['cur_parent_otdel_name'] . '">' . $_SESSION['cur_parent_otdel_name'] . '</span></div>';
        
        if ($_SESSION['cur_parent_podrguppa_name'] != 'Прочее') {
          echo '<div class="art_and_bar"><div class="label">Подгруппа: </div><span itemprop="brand">' . $_SESSION['cur_parent_podrguppa_name'] . '</span></div><br>';
        }
        else {
          echo '<div class="art_and_bar"><div class="label">Подгруппа: </div>' . $_SESSION['cur_parent_podrguppa_name'] . '</div><br>';
        }
 		 
     	if($node->weight)
     		echo '<div class="ves"><div class="label">Вес/объём: </div>' . $node->weight . ' ' . $node->weight_units . '</div>';
         
     	if($node->field_proizv[0]['value'])
     	{
     		$proizv_id = explode(';', $node->field_proizv[0]['value']);
     		$proizv_name = trim($proizv_id[1]);
     		$proizv_id = trim($proizv_id[0]);
     		if($proizv_name)
     		{
     			if($proizv_id AND $proizv_id != '')
     			{
     				if($mc_nid = db_result(db_query("SELECT nid from {content_type_mc_descr} WHERE field_zp_mc_artikul_value  = '%s'", $proizv_id)))
     					echo '<div class="proizv"><div class="label">Производитель/бренд: </div><a title="Перейти к описанию производителя" href="' . url('node/' . $mc_nid) .'"><span itemprop="brand">' . $proizv_name . '</span></a></div>';      
     			}
     			else 
     				echo '<div class="proizv"><div class="label">Производитель/бренд: </div><span itemprop="brand">' . $proizv_name . '</span></div>';      
     		}
     	}
     		
     	if($node->field_postav[0]['value'])
     	{
     		$postav_id = explode(';', $node->field_postav[0]['value']);
     		$postav_name = trim($postav_id[1]);
     		$postav_id = trim($postav_id[0]);
     		if($postav_name)
     		{
     			if($postav_id AND $postav_id != '')
     			{
     				if($mc_nid = db_result(db_query("SELECT nid from {content_type_mc_descr} WHERE field_zp_mc_artikul_value  = '%s'", $postav_id)))
     					echo '<div class="proizv"><div class="label">Поставщик/бренд: </div><a title="Перейти к описанию поставщика" href="' . url('node/' . $mc_nid) .'"><span itemprop="brand">' . $postav_name . '</span></a></div>';      
     			}
     			else 
     				echo '<div class="proizv"><div class="label">Поставщик/бренд: </div><span itemprop="brand">' . $postav_name . '</span></div>';      
     		}
     	}
     	
     	
     	
     	// покажем описание товара, причём если есть описание по международному ш-коду или ид производителя/поставщика, показываем его
     	/*
     	if(
     		$node->field_zp_bar_world[0]['value'] 
     		AND 
     		$d = db_result(db_query("SELECT descr FROM {p_descr_bw} WHERE p_bw = %s", $node->field_zp_bar_world[0]['view']))
     	   )
     		echo '<div class="descr">' . $d . '</div>';
     	*/
     	
     	
     	
     	if($node->field_zp_bar_world[0]['value'])
     	{
     		$descr_bw = db_fetch_array(db_query("SELECT descr, keywords FROM {p_descr_bw} WHERE p_bw = %s", $node->field_zp_bar_world[0]['view']));
     	   	if($descr_bw['descr'] AND $descr_bw['descr'] != '')
     	   	{
     			//echo '<div class="descr">' . $descr_bw['descr'] . '</div>';
     			echo '<div class="descr"><div class="label">Описание: </div>' . $descr_bw['descr'];
     			$descr_open = 1;
     			$full_descr = $descr_bw['descr'];
     	   	}
     	}
     	else if(
     			$node->field_zp_bar_proizv[0]['value']
     			AND
     			$d = db_result(db_query("SELECT descr FROM {p_descr_mc} WHERE p_id = %s AND seller_id = %s", $node->field_zp_bar_proizv[0]['view'], $proizv_id))
     		   )
     		   {
     			//echo '<div class="descr">' . $d . '</div>';
     			echo '<div class="descr"><div class="label">Описание: </div>' . $d ;
     			$descr_open = 1;
     			$full_descr = $d;
     		   }
     	else if(
     			$node->field_zp_bar_postav[0]['value']
     			AND 
     			$d = db_result(db_query("SELECT descr FROM {p_descr_mc} WHERE p_id = %s AND seller_id = %s", $node->field_zp_bar_postav[0]['view'], $postav_id))
     		   )
     		   {
     			//echo '<div class="descr"><div class="label">Описание: </div>' . $d . '</div>';	
     			echo '<div class="descr"><div class="label">Описание: </div>' . $d ;	
     			$descr_open = 1;
     			$full_descr = $d;
     		   }
     			
     	//else if($node->content['body']['#value'])
     	if($node->content['body']['#value'])
     	{
     		if($descr_open == 1)
     		{
     			echo '<br><br>' . $node->content['body']['#value']; // слово Описание пока уберём
     			$full_descr .= $node->content['body']['#value'];
     		}
     		else 
     		{
     			echo '<div class="art_and_bar"><div class="label">Описание: </div>' . $node->content['body']['#value'];
     			$descr_open = 1;
     			$full_descr = $node->content['body']['#value'];
     		}
     		//echo '<div class="descr"><div class="label">Описание: </div>' . $node->content['body']['#value'] . '</div>';
     	}

     	if($descr_open == 1)
     	{
     		// ссылка на перевод
     		echo '<div class="transl">';
     			// заменяем кавычки
     			$translate = str_replace('&quot;', '"', strip_tags($full_descr));
     			// заменяем знак процентов на слово, т.к. гугль с процентом не воспринимает строку
     			$translate = str_replace('%', 'проц.', $translate);
     			//echo l('Перевести название (укр->рус)', "http://translate.google.com.ua/?hl=ru&tab=wT#uk|ru|" . $translate, array('title' => 'Перевести название на русский язык с помощью переводчика Google', 'rel' => 'lightframe[|width:700px; height:300px; scrolling: auto;][Перевод названия на русский язык]'));
     			echo l('Перевести описание (укр->рус)', "http://translate.google.com.ua/?hl=ru&tab=wT#uk|ru|" . $translate, array('title' => 'Перевести описание на русский язык с помощью переводчика Google', 'rel' => 'nofollow'));
     		echo '</div>';
     		
     		echo '</div>';	
     	}
     			
     		
     
     
     
     // покажем международный код всем
     if($node->field_zp_bar_world[0]['value'])
     {
     		
     		if($country = zp_functions_get_country_by_bar($node->field_zp_bar_world[0]['value']))
     		{
     			echo '<div class="art_and_bar" itemprop="identifier" content="upc:' . $node->field_zp_bar_world[0]['view'] . '"><div class="label">Штрих-код международный: </div>' . $node->field_zp_bar_world[0]['view'] . '</div>';
     			echo '<div class="art_and_bar"><div class="label">Страна-производитель: </div>' . $country . '</div><br>';
     		}
     }	
     
     //echo '<div class="art_and_bar"><div class="label">Последнее обновление цены/статуса: </div><br>' . date('d-m-Y, H:i:s', $node->changed + 25200) . '</div><br>';
     		
     				
     // админу показываем скрытые данные - штрих-коды, артикулы и т.д.
     
     if ($user->uid == 1)
     {

     	echo '<div class="art_and_bar"><div class="label">Каталог с картинками: </div>' . $photo_data[0] . '</div>';
     	
     	if($descr_bw['keywords'] AND $descr_bw['keywords'] != '')
			echo '<div class="art_and_bar"><div class="label">Ключевые слова по международному штрих-коду: </div>' . $descr_bw['keywords'] . '</div>';
     	if($node->field_keywords[0]['value'])
     		echo '<div class="art_and_bar"><div class="label">Ключевые слова локальные: </div>' . $node->field_keywords[0]['value'] . '</div>';
     	echo '<br>';
     	 
     	echo '<div class="art_and_bar"><div class="label">Внутренний артикул ZP (SKU, model): </div>' . $node->model . '</div>';

        //Штрих-код магазина
     	if($node->field_zp_bar_world[0]['value'] AND !$country)
     		echo '<div class="art_and_bar"><div class="label">Штрих-код магазина: </div>' . $node->field_zp_bar_world[0]['view'] . '</div>';
     		
	    if($node->field_zp_bar_proizv[0]['value'])
    	 	echo '<div class="art_and_bar"><div class="label">Штрих-код производителя: </div>' . $node->field_zp_bar_proizv[0]['view'] . '</div>';
	    if($node->field_zp_bar_postav[0]['value'])
    	 	echo '<div class="art_and_bar"><div class="label">Штрих-код поставщика: </div>' . $node->field_zp_bar_postav[0]['view'] . '</div>';
	    if($node->field_zp_bar_shop[0]['value'])
    	 	echo '<div class="art_and_bar"><div class="label">Штрих-код магазина: </div>' . $node->field_zp_bar_shop[0]['view'] . '</div>';
	    if($node->field_zp_art_postav[0]['value']) 
    	 	echo '<div class="art_and_bar"><div class="label">Артикул поставщика: </div>' . $node->field_zp_art_postav[0]['view'] . '</div>';
     	if($node->field_zp_art_proizv[0]['value'])
     		echo '<div class="art_and_bar"><div class="label">Артикул производителя: </div>' . $node->field_zp_art_proizv[0]['view'] . '</div>';
	    if($node->field_zp_art_shop[0]['value'])
    	 	echo '<div class="art_and_bar"><div class="label">Артикул магазина: </div>' . $node->field_zp_art_shop[0]['view'] . '</div>';

     	// if($node->field_source_of_pics[0]['view'])
    	// 		echo '<br><div class="art_and_bar"><div class="label">Zp-артикул элемента-источника картинок: </div>' . $node->field_source_of_pics[0]['view'] . '</div><br>';

     } // end of if ($user->uid == 1)
   

     
     
     // статус товара
    echo '<div itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">';
    
     	if($node->field_status[0]['value'] == '0') 
        echo '<div class="art_and_bar" itemprop="availability" content="out_of_stock"><div class="label p_hidden">Статус: </div>Товара нет в продаже</div>';
	    else if(!$user->uid)
	    	//echo '<div class="art_and_bar alert"><div class="label">Статус: </div>Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
        echo '<div class="art_and_bar alert" itemprop="availability" content="in_stock"><div class="label">Статус: </div>' . l('Зарегистрируйтесь, чтобы увидеть цену и заказать товар', 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom') . '</div>';
	    else if($show_price == -3 AND $user->uid != 1) // не любимый магазин клиента
	    	//echo '<div class="art_and_bar alert"><div class="label">Статус: </div>Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny', array('title' => t('Почему?'))) . '</div>';
        echo '<div class="art_and_bar alert"><div class="label" itemprop="availability" content="in_stock">Статус: </div>' . l('Заказ товаров из этого заведения пока не доступен для Вас', 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny') . '</div>';
	    elseif($is_costly AND $show_costly > 1) 
	    {
	    	// товар дорогой
        if($user->uid == 1)
        {
            if($node->field_status[0]['value'] != '')
				echo '<div class="art_and_bar alert" itemprop="availability" content="in_stock"><div class="label">Статус товара: </div>' . $node->field_status[0]['view'] . ' Это элитный дорогой товар. Его доставка по умолчанию не осуществляется (если не заданы персональные настройки по конкретному клиенту). Для добавления возможности доставки внесите необходимые изменения в настройки системы ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
	    		else	
		    		echo '<div class="art_and_bar alert" itemprop="availability" content="in_stock"><div class="label">Статус: </div>Это элитный дорогой товар. Его доставка по умолчанию не осуществляется (если не заданы персональные настройки по конкретному клиенту). Для добавления возможности доставки внесите необходимые изменения в настройки системы.</div>';
                }
	    	else
	    	{
	    		if($node->field_status[0]['value'] != '')
	    			echo '<div class="art_and_bar alert" itemprop="availability" content="in_stock"><div class="label">Статус товара: </div>' . $node->field_status[0]['view'] . '. Доставка этого товара для Вас не доступна.'  . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '. Возможно, товара нет в наличии.</div>';
	    		else
	    			echo '<div class="art_and_bar alert" itemprop="availability" content="in_stock"><div class="label">Статус: </div>' . $node->field_status[0]['view'] . '. Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
	    	}

	    }
	    elseif($node->field_status[0]['value'] != '')
                echo '<div class="art_and_bar" itemprop="availability" content="in_stock"><div class="label">Статус товара: </div>' . $node->field_status[0]['view'] . '</div>';	    		   

			
	 echo '</div>'; // End of echo '<div itemprop="offerDetails" itemscope itemtype="http://data-vocabulary.org/Offer">';
	  
	    	
	    	/*
	    	
	    	{
         		if($show_price == -2) // клиент, НЕ не имеющий права на покупку в ЭТОМ магазине
					echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny', array('title' => t('Почему?'))) . '</div>';
				else if($user->uid) // если это клиент, имеющий право на покупку в данном магазине, но товар не доставляемый
					echo '<div class="no_dost_descr">' . 'Доставка этого элитного товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#vozmozhnost-dostavki', array('title' => t('Почему?'))) . '</div>';
				else // если это не зарегистрированный клиент
					echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
        	}
         	else if($show_price == -2) // клиент, НЕ не имеющий права на покупку в ЭТОМ магазине
				echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny', array('title' => t('Почему?'))) . '</div>';
			else
         		echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-stat-klientom', array('title' => t('Почему?'))) . '</div>';
				
	    	
	    	*/
   
    // всегда показываем вкладку со ссылками на статьи, а содержание генерируем с помощью ajax при нажатии пользователем на вкладку
            echo '</div>'; // end of div main_tab	

                    echo '<div id="issues">';
                            // вместо того, чтобы сразу генерировать список статей, откладываем генерацию списка до нажатия на вкладку со статьями
                            // список выдаём по запросу ajax-функции (вначале этого файла)
                            drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_p_getRelatedIssues.js');
                    echo '</div>'; // end of div issues

            echo '</div>'; // end if div tabs
      
           
      
       // deny add to cart for .regfirst class
       drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_regfirst.js');
                                    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 

       //$show_price = 1;
       if($show_price < 0 OR ($user->uid != 1 AND $is_costly AND $show_costly > 2))
       {
         //echo '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'user/'.$user->uid, array('title' => t('Почему?'))) . '</div>';
         
         // не показываем форму заказа, если не показывается цена и/или доставка
         //echo $c_form1 . 'no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'user/'.$user->uid, array('title' => t('Почему?'))) .'</div></div></form></div>';    
           

                            
                             // убираем метку
                             $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
                             //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину"', $c_form2);


                             // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
                             $c_form2 = explode('</div>', $c_form2, 2);
                             $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];

                                                          
                             // change action to '/feedback/stat-nashim-klientom-prosto'
                             preg_match('{<form.*action="(.*)"}', $c_form1, $result2);
                             $pos_temp = strpos($action_origin = trim($result2[1]), '"', 2);
                             if( $pos_temp !== false)
                                $action_origin = substr($action_origin, 0, $pos_temp);
                             $c_form1 = str_replace($action_origin, '/feedback/stat-nashim-klientom-prosto', $c_form1);
                             
                             //print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           

                             //echo '<div class="regfirst">' . $c_form1 . 'sell_price"  title="' . $dost_descr .'"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' + доставка ' . $show_price . ' (за 1 ' . $node->field_sell_measure[0]['view'] .')</div><div class="atc" title="Добавить товар в корзину в указанном количестве"><div class="edit-qty-wrapper' . $c_form2 . '</div></div>';
                             echo '<div class="regfirst">' . $c_form1 . 'sell_price"></div><div class="atc" title="Добавить товар в корзину в указанном количестве"><div class="edit-qty-wrapper' . $c_form2 . '</div></div>';


                        
           
       }
       else if(($user->uid != 1 AND $is_costly AND $show_costly == 2)) // если показываем цену, но не разрешаем доставку
       {
       	
       	// убираем метку
       	 //$c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
         $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
       	 //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину"', $c_form2);
       	 
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 //print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           
       	 
       	 // всю форму не показываем, показываем только цену
       	 //echo $c_form1 . 'sell_price"  title="' . $dost_descr .'"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' + доставка ' . $show_price . ' (за 1 ' . $node->field_sell_measure[0]['view'] .')</div><div class="atc" title="Добавить товар в корзину в указанном количестве"><div class="edit-qty-wrapper' . $c_form2 . '</div>';

///////////////
       	 if(!$user->uid)
         {
            
             
             echo '<div id="sell_price"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' (+ стоимость доставки)</div>';
         }
         else
             echo '<div id="sell_price"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' (+ доставка ' . $show_price . ' за 1 ' . $node->field_sell_measure[0]['view'] .', если бы была доступна доставка)</div>';
       	       	
       	//echo '<div class="no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'user/'.$user->uid, array('title' => t('Почему?'))) .'</div>';
       }
       else 
       { 
		 // убираем метку
       	 //$c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
         $c_form2 = str_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
       	 //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину"', $c_form2);
       	 
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 //print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           
       	 
       	 echo $c_form1 . 'sell_price"  title="' . $dost_descr .'"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' + доставка ' . $show_price . ' (за 1 ' . $node->field_sell_measure[0]['view'] .')</div><div class="atc" title="Добавить товар в корзину в указанном количестве"><div class="edit-qty-wrapper' . $c_form2 . '</div>';
     	 
         
         
         
        
       } 

       //$back_url = zp_functions_continue_shopping_link();
       //echo l('Продолжить покупки / вернуться...', 'node/' . $back_url['nid']);
       echo l('Продолжить покупки / вернуться...', $_SERVER['HTTP_REFERER'], array('class' => 'back'));

       
       //if (1) 
       {
       //if ($user->uid == 1) {

          //echo '<div class="socialite-product">' . zp_functions_getSocialiteButtons() . '</div>';
          //echo zp_functions_getSocialiteButtons();
          
          ?>
          
           <div class="share">

                <?php $url = 'http://' . $_SERVER['SERVER_NAME'] . url('node/' . $node->nid); ?>

                <div class="main">
                    <?php echo zp_functions_getSocialiteButtons($url, $title_corrected); ?> 
                </div> <!-- main share buttons -->

                <div class="others">
                  <!-- ADDTHIS BUTTON BEGIN -->
                  <script type="text/javascript">
                  var addthis_config = {
                      //pubid: "all4senses"
                  }
                  var addthis_share =
                  {
                    // ... members go here
                    url: "<?php echo $url?>"
                  }
                  </script>

                  <div class="addthis_toolbox addthis_default_style" addthis:url="<?php echo $url?>">
                    <a href="http://addthis.com/bookmark.php?v=250&amp;pub=all4senses"></a>
                    <a class="addthis_button_compact">Другие сервисы</a>
                  </div>
                  <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=all4senses"></script>
                  <!-- ADDTHIS BUTTON END -->

                </div>

              <div class="bottom_clear"></div>

          </div> <!-- all share buttons -->


          <?php
                    

       }

       
     ?>

   </div> <!-- /* end of txt ? */ -->  
   
   
    
   <div class="images">
	
   	<?php
   
                $base_p = base_path();
                $files_p_base_path = $base_p . 'files/p/';
   		//$photo_base_path = base_path() . 'files/p/' . $photo_data[0] . '/';
                $photo_base_path = $files_p_base_path . $photo_data[0] . '/';
   		
   		switch ($photo_data[1]) //$photo_data[1] - source of pics
   		{
   			/*
   			case '2': // 2 = источник названия картинок - штрих-код производителя
   				$photo_base_name = 'b' . $proizv_id . '/b' . $proizv_id . '-' . $node->field_zp_bar_proizv[0]['view']; //'bmc = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа bmc0034-2298094850938-1.jpg
   				break;
   			*/
   					
   			case '3': // 3 = источник названия картинок - артикул производителя
   				//$photo_base_name = $proizv_id . '/a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view'] . '/a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view']; //'amc = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа amc0034-2298094850938-1.jpg
                                $p_id = 'a' . $proizv_id . '-' . $node->field_zp_art_proizv[0]['view'];
                                $photo_base_name = $proizv_id . '/' . $p_id  . '/' . $p_id; //'amc = bar of manufacturer', вернее, внутренний номер производителя имеет вид m0034, т.е. название картинки имеет вид типа amc0034-2298094850938-1.jpg
   				break;
   				
   			/*
   			case '4': // 4 = источник названия картинок - штрих-код поставщика
   				$photo_base_name = 'b' . $postav_id . '/b' . $postav_id . '-' . $node->field_zp_bar_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   				break;
   			*/
   			
   			case '5': // 5 = источник названия картинок - арт поставщика
                                //$photo_base_name = $postav_id . '/a' . $postav_id . '-' . $node->field_zp_art_postav[0]['view'] . '/a' . $postav_id . '-' . $node->field_zp_art_postav[0]['view']; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
                                $p_id =  'a' . $postav_id . '-' . $node->field_zp_art_postav[0]['view'];
                                $photo_base_name = $postav_id . '/' . $p_id . '/' . $p_id; //'bc = bar of caterer', вернее, внутренний номер поставшика имеет вид c0034, т.е. название картинки имеет вид типа bc0034-2298094850938-1.jpg
   				break;
   				
			/*
   			case '6': //6 = источник названия картинок - штрих-код магазина
   				//$photo_base_name = $photo_base_path  . 'b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				$photo_base_name = 'b' . substr($node->model, 0, 8) . '/b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				break;
   			*/
			
   			case '7': //7 = источник названия картинок - арт магазина
   				//$photo_base_name = $photo_base_path  . 'b' . substr($node->model, 0, 8) . '-' . $node->field_zp_bar_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				
                                //$photo_base_name = substr($node->model, 0, 8) . '/a' . substr($node->model, 0, 8) . '-' . $node->field_zp_art_shop[0]['view'] . '/a' . substr($node->model, 0, 8) . '-' . $node->field_zp_art_shop[0]['view']; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
                                $p_id = 'a' . substr($node->model, 0, 8) . '-' . $node->field_zp_art_shop[0]['view'];
                                $photo_base_name = substr($node->model, 0, 8) . '/' . $p_id . '/' . $p_id; // bsh = bar of shop, вернее, в качестве номера магазина берём zp номер, т.е. получается картинка имеет название bz010014980-21394300900-1.jpg
   				break;
   				
   			case '1': // 1 = источник названия картинок - штрих-код международный
   			default:
   				//$photo_base_name = $photo_base_path . $node->field_zp_bar_world[0]['view']; 
   				//$photo_base_name = $node->field_zp_bar_world[0]['view']; 
   				
                                //$photo_base_name = $node->field_zp_bar_world[0]['view'] . '/' . $node->field_zp_bar_world[0]['view']; 
                                $p_id = $node->field_zp_bar_world[0]['view'];
                                $photo_base_name = $p_id . '/' . $p_id; 
   				break;
   		}
   		
   		//echo '<PRE>';
  		//print_r($photo_base_name);
  		//echo '</PRE>';
  		
  		//$photo_base_name = $photo_base_path . $photo_base_name . '/' . $photo_base_name;
  		$photo_base_name = $photo_base_path . $photo_base_name;
   		
   		$big_photo_num = 1; // первая (или первая существующая на сервере) картинка большая, остальные маленькие
   		
   		
                
                
      /////////for($i = 0; $i < $photo_data[2];) //$photo_data[2] - num of pics
      for($i = 0; $i < 1;) //$photo_data[2] - num of pics
   		{
   			$i++;
   			$next_photo = $photo_base_name . '-' . $i . '.jpg';
   			//clearstatcache(); // вроде как очищает кеш, но кажется это не нужно тут
   			if($i == $big_photo_num)
   			{
   				if(file_exists($_SERVER['DOCUMENT_ROOT'] . $next_photo))
   				{
   					if($photo_data[3] == 'h')
   						$next_photo_popup = imagecache_create_url('product_body_type1-popup-h', $next_photo);
   					else 
   						$next_photo_popup = imagecache_create_url('product_body_type1-popup-v', $next_photo);
   						
   					// раскомментировать, чтобы показывать оригинальную картинку, а не скорректированную имиджкешем
   					//$next_photo_popup = $next_photo;
   					
   					//echo '<div class="image_big"><a href="' . $next_photo . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip]['. $title . ' ' . $i .']">'
   					
   					//echo '<div class="image_big"><a href="' . $next_photo_popup . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip]['. $title /*. ' ' . $i*/ .']">'
   					echo '<div class="image_big"><a href="' . $next_photo_popup . '" title="' . $title_corrected . '" rel="lightbox[roadtrip]['. $title_corrected /*. ' ' . $i*/ .']">'
   					. theme('imagecache', 'product_body_type1', $next_photo, $title_corrected . ' ' . $i . ", Служба доставки 'За покупками'", NULL, array('itemprop' => 'image')) // третий аргумент - alt
   					. '<a/></div>'; 
                                        
                                        $image_src_for_meta = '/files/imagecache/product_body_type1' . $next_photo;
   					$photo_exist = 1;	
   					
   					
   				}
   				else
   				{
   					//print theme('imagecache', 'product_body_type1', $photo_base_path . '/default.jpg');
   					
   					//раскомментировать, чтобы главной картинкой могла выступать вторая, при отсутствии первой
   					//$big_photo_num = $i+1;
   					//закомментировать, чтобы главной картинкой могла выступать вторая, при отсутствии первой
   					break;
   				}
   			}
   			else
   			{
   				if(file_exists($_SERVER['DOCUMENT_ROOT'] . $next_photo))
   				{
   					if($photo_data[3] == 'h')
   						$next_photo_popup = imagecache_create_url('product_body_type1-popup-h', $next_photo);
   					else 
   						$next_photo_popup = imagecache_create_url('product_body_type1-popup-v', $next_photo);
   						
   					// раскомментировать, чтобы показывать оригинальную картинку, а не скорректированную имиджкешем
   					//$next_photo_popup = $next_photo;
   					
   					//echo '<div class="image_small ' . $i . '"><a href="' . $next_photo . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip]['. $title . ' ' . $i .']">'
   					echo '<div class="image_small ' . $i . '"><a href="' . $next_photo_popup . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip]['. $title_corrected . ' ' . $i .']">'
   					.  theme('imagecache', 'product_body_type2', $next_photo, $title_corrected . ' ' . $i . ", Служба доставки 'За покупками'") // третий аргумент - alt
   					. '<a/></div>'; 
                                        
                                        if(!$photo_exist)
                                        {
                                            $image_src_for_meta = '/files/imagecache/product_body_type1' . $next_photo;
                                            $photo_exist = 1;
                                        }
   				}
   			}
   		}
   		
   		if(!$photo_exist)
      {
          /*
          if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photo_base_path . '/default.jpg'))
          {
              echo '<div class="image_big">'
  . theme('imagecache', 'product_body_type1-default', $photo_base_path . '/default.jpg', $title_corrected . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'") // третий аргумент - alt
  . '</div>'; 

              $image_src_for_meta = $base_p . 'files/imagecache/product_body_type1-default' . $photo_base_path . 'default.jpg';
          }
          else
          */
          {
              echo '<div class="image_big">'
  . theme('imagecache', 'product_body_type1-default',  $files_p_base_path . 'default_all.jpg', $title_corrected . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'") // третий аргумент - alt
  . '</div>'; 

              $image_src_for_meta = "/sites/all/themes/zp-themes/zp-two/img4/zaPokupkami.com.jpg";
          }
          
          
                if (1) {
                //if ($user->uid == 1) {
                  
                    if (!empty($node->field_zp_bar_world[0]['view'])) {
                      
                        drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_getGooImages.js');

                        if ($user->uid == 1) {
                        //_dpr('$podgruppa = ' . $_SESSION['cur_parent_podrguppa_name']);
                        //_dpr('$podgruppa = ' . $podgruppa);
                        }
                        
                        $p_title_rus = (($ukToRu_translate AND $ukToRu_translate != '=') ? $ukToRu_translate : NULL);
                        //$p_title = str_replace(array('&amp;', '&quot;', '"', "'"), '', $p_title);
                        
                        
                        //_dpr('$ukToRu_translate = ' . $ukToRu_translate);
                        //_dpr('$p_title = ' . $p_title);
                        
                        //_dpr('url = ' . urlencode($podgruppa . ' ' . $p_title));
                        
                        //$p_title = $node->title;
                        
                        drupal_add_js(array('zp_functions' => array('uid' => $user->uid, 'ipath' => $photo_data[0] . '/' . $node->field_zp_bar_world[0]['view'], 'nid' => $node->nid, 'title' => $node->title, 'title_corrected' => $title_corrected,  'title_rus' => $p_title_rus, 'podgruppa' => $_SESSION['cur_parent_podrguppa_name'], 'bar' => $node->field_zp_bar_world[0]['view'])), 'setting');
                      

                        /*
                        if ($user->uid == 1) {

                          $url = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=' . urlencode($node->field_zp_bar_world[0]['view']);

                          $json = zp_functions_curl_get($url);
                          $google_data = json_decode($json);

                          
                          _dpr($photo_base_path);
                          _dpr($photo_base_name);
                          _dpr($photo_data);
                          
                          _dpr($google_data);

                         
                        }
                        */
                        
                        //if ($user->uid == 1) 
                          {
                          echo 
                            '<div class="p-photos google-images">',
                                  
                              '<div class="caption"></div>',
                              '<div class="all-results">',
                                  
                                '<div class="wait hidden"></div>',
                                '<div class="clicks hidden"></div>',
                                '<div class="results-present-explain hidden"></div>',
                                
                                 
                                '<div>',
                                  '<div class="results-caption r1 hidden"></div>',
                                  '<div class="no-results r1 hidden"></div>',
                                  '<div class="results r1"></div>',
                                  '<div class="bad-button r1 hidden"></div>',
                                '</div>',
                                  
                                '<div>',
                                  '<div class="results-caption r0 hidden"></div>',
                                  '<div class="no-results r0 hidden"></div>',
                                  '<div class="results r0"></div>',
                                  '<div class="bad-button r0 hidden"></div>',
                                '</div>',
                                  
                              '</div>',
                                  
                            '</div>';
                        }
//                        else {
//                          echo 
//                            '<div class="p-photos google-images">',
//                        
//                              '<div class="caption"></div>',
//                              '<div class="wait hidden"></div>',
//                              '<div class="results"></div>',
//                         
//                            '</div>';
//                        }
                    }
                }
                
                
                
   		//if($node->nid == 12021 AND $user->uid ==1)
                {   
                    $qr = zp_functions_get_qr($p_id, 'http://www.zapokupkami.com' . $_SERVER['REQUEST_URI']);
                    //echo 'p_id = ' . $p_id . '<br>';
                    //zp_functions_show($_SERVER);
                    echo '<div style="overflow:hidden; width: 160px"><img alt="' . $title_corrected .' qr-код" title="QR-код для ' . $title_corrected . '" src="/' . $qr . '" width="195px" style="position:relative; left:-19px; top:-10px"></div>';
                   
                }

      } // end of if(!$photo_exist)

      
      // set meta tag for search and social
      drupal_set_html_head('<link rel="image_src" href="' . $image_src_for_meta. '" />');    
   		   	
   	?>
   
   
	  
    </div> <!-- /* end of images */ -->    
    

        
</div>     <!-- /* end of body */ -->  
    
  


  
  
 <?php
 
 // покажем форму отзыва по данному продукту и имеющиеся по продукту отзывы
 // форму ввода отзыва показываем только зарегистрированному клиенту
 
 if($user->uid)
 {
 // сформируем необходимые данные для сохранения мнения в базе данных
 /*
 $opinion_info = array(
  	'target_table' => $target_table,
  	'p_bw' => $p_bw,
  	'pr_id' => $pr_id,
  	'uid' => $uid,
  	'uname' => $uname,
  	`shop_id` => $shop_id,
    `shop_name` => $shop_name,
    `seller_id` => $seller_id,
    `seller_name` => $seller_name,
  );
 */
 
 
 $opinion_info = array();
 
 if($node->field_zp_bar_world[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_bw';
 	$opinion_info['p_bw'] = $node->field_zp_bar_world[0]['view'];
 }
 else if($node->field_zp_bar_proizv[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_mc';
 	$opinion_info['p_id'] = $node->field_zp_bar_proizv[0]['view'];
 	$opinion_info['seller_id'] = $proizv_id;
 	$opinion_info['seller_name'] = $proizv_name;	
 }
 else if($node->field_zp_bar_postav[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_mc';
 	$opinion_info['p_id'] = $node->field_zp_bar_postav[0]['view'];
 	$opinion_info['seller_id'] = $postav_id;
 	$opinion_info['seller_name'] = $postav_name;	
 }
 else if($node->field_zp_bar_shop[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_sh';
 	$opinion_info['p_id'] = $node->field_zp_bar_shop[0]['view'];
 }
 /*
 else if($node->field_zp_art_shop[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_sh';
 	$opinion_info['p_id'] = $node->field_zp_art_shop[0]['view'];
 }
 else if($node->field_zp_art_proizv[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_mc';
 	$opinion_info['p_id'] = $node->field_zp_art_proizv[0]['view'];
 	$opinion_info['seller_id'] = '';
 	$opinion_info['seller_name'] = '';	
 }
  else if($node->field_zp_art_postav[0]['view'])
 {
 	$opinion_info['target_table'] = 'p_opinions_mc';
 	$opinion_info['p_id'] = $node->field_zp_art_postav[0]['view'];
 	$opinion_info['seller_id'] = '';
 	$opinion_info['seller_name'] = '';	
 }
 */
 else 
 {
 	$opinion_info['target_table'] = 'p_opinions_sh';
 	$opinion_info['p_id'] = 'ns-' . 'product_name'; // если не задано никаких id для продукта, запоминаем его под названием (в латинице) для текущего магазина
 }
 
 
 $opinion_info['uid'] = $user->uid;
 $opinion_info['uname'] = $user->name;
 $opinion_info['shop_id'] = substr($node->model, 0, 8);
 $opinion_info['shop_name'] = $_SESSION['c_shop_tids']['shop_name'];
 $opinion_info['shop_address'] = $_SESSION['c_shop_tids']['shop_address'];
 
 
 
 //echo '<div class="add_opinion">' . drupal_get_form('zp_opinions_form') . '</div>';
 
 //echo '<div class="add_opinion">' . drupal_get_form('zp_opinions_form', $opinion_info) . '</div>';
 
 // если переменная 'opinion_info' передаётся в форму в виде массива, то друпал сообщает в логах 
 // об ошибке проверки этой переменной на валидность текста этой переменной... Конечно, это не простой текст, так что было решено преобразовать массив в строку (с помощью serialize() ) при передаче переменной в форму,
 // а потом при использовании значений этой переменной (в функции  zp_opinions_form_submit) разворачивать её обратно в массив.
		
 echo '<div class="add_opinion">' . drupal_get_form('zp_opinions_form', serialize($opinion_info), $user->name) . '</div>';
 
 
 } // end of if()$user->uid
	
  
 
 

 

 // Покажем все отзывы по данному продукту любому зашедшему пользователю
 $count = 0;
 if($node->field_zp_bar_world[0]['view'])
  if($opinions = db_query("SELECT opinion, shop_name, shop_address FROM {p_opinions_bw} WHERE p_bw = %s", $node->field_zp_bar_world[0]['view']))
  {					    
  	while($opinion = db_fetch_object($opinions))
		{
			if(!$count)
				echo '<div class="opinions"><div class="title">Мнения наших покупателей об этом товаре: </div>';
				
			echo '<div class="opinion">' . $opinion->opinion . '</div><div class="info"> /Отзыв покупателя из магазина ' . $opinion->shop_name . ' (' . $opinion->shop_address . ')</div>';
		  	$count++;	
		}
	if($count) echo '</div>'; // and of <div class="opinions">
  }
 else if($node->field_zp_bar_proizv[0]['view'])
  if($opinions = db_query("SELECT opinion, shop_name, shop_address FROM {p_opinions_mc} WHERE p_id = %s AND seller_id = %s", $node->field_zp_bar_proizv[0]['view'], $proizv_id))
  {					    
  	while($opinion = db_fetch_object($opinions))
		{
			if(!$count)
				echo '<div class="opinions"><div class="title">Мнения наших покупателей об этом товаре: </div>';
				
			echo '<div class="opinion">' . $opinion->opinion . '</div><div class="info"> /Отзыв покупателя из магазина ' . $opinion->shop_name . ' (' . $opinion->shop_address . ')</div>';
		  	$count++;	
		}
	if($count) echo '</div>'; // and of <div class="opinions">
  }
 else if($node->field_zp_bar_postav[0]['view'])
  if($opinions = db_query("SELECT opinion, shop_name, shop_address FROM {p_opinions_mc} WHERE p_id = %s AND seller_id = %s", $node->field_zp_bar_postav[0]['view'], $postav_id))
  {					    
  	while($opinion = db_fetch_object($opinions))
		{
			if(!$count)
				echo '<div class="opinions"><div class="title">Мнения наших покупателей об этом товаре: </div>';
				
			echo '<div class="opinion">' . $opinion->opinion . '</div><div class="info"> /Отзыв покупателя из магазина ' . $opinion->shop_name . ' (' . $opinion->shop_address . ')</div>';
		  	$count++;	
		}
	if($count) echo '</div>'; // and of <div class="opinions">
  }
  else if($node->field_zp_bar_shop[0]['view'])
  if($opinions = db_query("SELECT opinion, shop_name, shop_address FROM {p_opinions_sh} WHERE p_id = %s AND shop_id = %s", $node->field_zp_bar_shop[0]['view'], $opinion_info['shop_id']))
  {					    
  	while($opinion = db_fetch_object($opinions))
		{
			if(!$count)
				echo '<div class="opinions"><div class="title">Мнения наших покупателей об этом товаре: </div>';
				
			echo '<div class="opinion">' . $opinion->opinion . '</div><div class="info"> /Отзыв покупателя из магазина ' . $opinion->shop_name . ' (' . $opinion->shop_address . ')</div>';
		  	$count++;	
		}
	if($count) echo '</div>'; // and of <div class="opinions">
  }
				

 
  
  
  
  
  
       // попробуем добавить отслеживание названия через Гугль Аналитикс
     /*
     drupal_add_js('$(function(){
    		basePath = "'. base_path() .'";
    		$("#tabs").tabs({ initial: 0, fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal",  remote: 0 });
    		})', 'inline');
     
    
     drupal_add_js('
     
     				$(
     					function()
     					{
    						basePath = "'. base_path() .'";
    						$("#tabs").tabs({ initial: 0, fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal",  remote: 0 });
    					}
    				  )
    				  
    			   ', 
    			   'inline');
      */

     /*	   
     drupal_add_js('
     
     				pageTracker._setCustomVar(
      					1,                   // This custom var is set to slot #1
      					"Title",           // The top-level name for your online content categories
      					//"Life & Style",      // Sets the value of "Section" to "Life & Style" for this particular aricle
      					"' . str_replace('&quot;', "'", $title) . '",
      					3                    // Sets the scope to page-level 
   					);
    				  
    			   ', 
    			   'inline');
    			   
    		   
    			   
    			   
  	 drupal_add_js("
						
  	 
  	 					_gaq.push(['_setCustomVar',
      									1,                   // This custom var is set to slot #1
      									'Title',           // The top-level name for your online content categories
      									'" . str_replace('&quot;', '"', $title) . "',  // Sets the value of  for this particular aricle
      									3                    // Sets the scope to page-level 
   									]);
   									
   						
   						// OR
   												
						$(document).ready(function()
						{
							
							_gaq.push(['_setCustomVar',
      									1,                   // This custom var is set to slot #1
      									'Title',           // The top-level name for your online content categories
      									'" . str_replace('&quot;', '"', $title) . "',  // Sets the value of  for this particular aricle
      									3                    // Sets the scope to page-level 
   									]);
							
							
 						});
 	
				   ", 'inline');
  
  	 */
  	 

     
     
     
	
// ссылка "Стать клиентом"	

if(!$user->uid)
	echo '<br><br><div class="become_client product"><a href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto"><strong>Стать клиентом</strong> службы доставки <strong>"За Покупками!"</strong> очень просто!</a></div><br>';


     
     
     
  	////if($user->uid == 1)
  	////{
  	 // запустить один раз для обновления алиасов для xmlsitemap!
  	 //zp_functions_update_xmlsitemap_pid();
  	 
  	 // запустить один раз для создания следующих ста редиректов со старых алиасов
  	 //zp_functions_make_link_redirects();
  	 
  	 /*
  	 for($count = 1; $count <= 5; $count++)
	  	 $next_order_id = db_next_id('{uc_orders}_order_id'); 
	  	 
  	 echo 'next_order_id = ' . $next_order_id . '<br>';
  	 */
  	 
  	 //$rid = db_next_id('{path_redirect}_rid');
  	 //echo 'rid = ' . $rid . '<br>';
  	 
  	////} 
  	 
    
endif; // end of if body 


?>  