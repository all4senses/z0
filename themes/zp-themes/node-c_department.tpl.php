<?php 


// получим справочные статьи с пересекающимися с этим товаром ключевыми словами и выдадим запрашивающей функии по ajax
// эти данные будут встроены с помощью ajax во вкладку со статьями

if($_GET['source'] == 'ajax')
{

  // получим справочные статьи с пересекающимися с этим товаром ключевыми словами
  
  $data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	

  // получим номер словаря c ключевыми словами, имеющимися в статьях, по названию словаря
  //$issue_vid = db_result(db_query("SELECT vid from {vocabulary} WHERE name = '%s'", 'Issues keywords'));
  $issue_vid = 6; // зададим явно номер словаря, чтоб быстрее

  if($node->field_keywords[0]['view'])
  	$keywords = explode(',', $node->field_keywords[0]['view']);
	
  $keywords[] = $data[2]; // также используем в качестве ключевого слова название каталога с картинками
  

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
    		$issue_teaser = db_result(db_query("SELECT field_issue_teaser_value from {content_field_issue_teaser} WHERE nid = %d", $issue['nid']));
    		
    		$issues[$issue['nid']]['title'] = $issue_name;
    		$issues[$issue['nid']]['teaser'] = $issue_teaser;
	    }
  	}
  
  
  	
  	echo '<div class="ajaxed">';

		if($issues)
		{
			foreach ($issues as $nid => $issue)
                        {
                            echo '<div class="i_title">' . l($issue['title'], 'node/' . $nid, array('title' => 'Перейти на страницу со статьёй "' . $issue['title'] . '".')) . '</div>';

                            if($issue['teaser']) 
                                    echo '<div class="i_teaser">' . $issue['teaser'] . '</div>';
                        }
		}
		else 
		{
			echo 'По данному отделу пока нет справочных данных или статей.';
		}
		
	echo '</div>';
	
	return;
	


}

// конец формирования данных для выдачи ajax-функции










echo '<div class="test">shop</div>';




//if teaser ---------------------------------------------------------------------------------

if ($teaser == 1): ?>

 <div class="place_teaser dept">

    <a href="<?php print $node_url ?>">

    
      <?php 
      		
       	  $data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	 	   
      
      	  echo '<table><tbody><tr>'
           . '<td class="caption">'
           . '<div class="type">' . $data[0] . '</div>'
           . '<div class="title">' . '<a href="' . $node_url . '">' . $title . '</a>' .'</div>'
           . '</td>';
      
          
           $logopath = base_path() . 'files/shops/' . substr($node->field_zp_art_place[0]['value'], 0, 8) . '/' . $node->field_zp_art_place[0]['value'] . '-logo.jpg';
           if(file_exists($_SERVER['DOCUMENT_ROOT'] . $logopath))
				echo '<td class="logo">' . theme('imagecache', 'place_logo_teaser', $logopath, $title) . '</td>';

          echo '<td class="description">'
           . '<div class="address">' . $node->field_place_address[0]['value'] . '</div>'
           . '<div class="descr">';
           
          if($node->field_descr_teaser[0]['value'])
          	echo $node->field_descr_teaser[0]['value'];
          else 
          	echo $node->content['body']['#value'];
           
//            global $user;
//            if ($user->uid == 1) {
//              _dpr($node);
//            }
            
          echo '</div>'
           . '</td>'
           . '</tr></tbody></table>';
                      
      ?>   
    
    </a>
    
    
    
 </div>
<?php endif; // end of teaser ?>











<?php 

if ($page == 1)
{

$data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	
 

 // deny add to cart for .regfirst class
 drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_regfirst.js');

 echo '<div class="place_body dept">
   <div class="txt">';
   
   		

   	echo '<div class="type">'. $data[0] . '</div>';
     	echo '<div class="title"><h1>' . $title . '</h1></div>';


     	// адрес отдела
     	echo '<div class="address">' . $node->field_place_address[0]['value'] . '</div>';
     	
        
   
   		// если есть статьи по теме этогот  товара, создаём группу вкладок (tabs), на первой из которых будет оригинальное описание
   		// а на второй ссылки на статьи
   
		// здесь, если есть статьи, создаём "шапку" для табов и открываем таб-контейнер для оригинального описания
   		
		//if($issues)
   		// всегда показываем вкладку со ссылками на статьи, а содержание генерируем с помощью ajax при нажатии пользователем на вкладку
 		{
                    echo '<div id="tabs">';

    	
        	
                    drupal_add_css('sites/all/modules/_Jstools/jstab/jquery.tabs.css');
                    drupal_add_js('sites/all/modules/_Jstools/jstools/tabs/jquery.tabs.js'); 


                    drupal_add_js('$(function(){
                            basePath = "'. base_path() .'";
                            $("#tabs").tabs({ initial: 0, fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal",  remote: 0 });
                            })', 'inline');



                    //echo '<ul>';
                    echo '<ul class="tabs-nav">';
                            echo '<li><a href="#main_tab" class="tabs-selected">Описание</a></li>';
                            echo '<li><a href="#issues" id="issues_button">Справочные данные, статьи</a></li>';
                    echo '</ul>';


                    echo '<div id="main_tab">';
 		}
    
 		
 		// далее идёт оригинальное описание
 		
 		// если бы статей не было, массив с вкладками не создавался бы и шапка для табов также не создавалась бы
 		// и описание шло само по себе, без всяких вкладок
 		
     	
     	
     	
     	
     	// покажем админу некоторые данные по отделу
     	global $user;
     	if ($user->uid == 1)
    	 {
    		echo '<div class="art_and_bar"><div class="label">Каталог с картинками: </div>' . $data[2] . '</div>';
     		if($node->field_keywords[0]['value'])
     			echo '<div class="art_and_bar"><div class="label">Ключевые слова: </div>' . $node->field_keywords[0]['value'] . '</div>';
     		echo '<br>';
    	 }	

     	// описание отдела
     	echo '<div class="descr">' . $node->content['body']['#value'] . '</div>';
        echo '<div class="art_and_bar"><div class="label">Последнее обновление раздела: </div>' . date('d-m-Y, H:i:s', $node->changed + 25200) . '</div>';
     	
    	
	// всегда показываем вкладку со ссылками на статьи, а содержание генерируем с помощью ajax при нажатии пользователем на вкладку
    	//if(1)  
         { 
                echo '</div>'; // end of div main_tab	

                        echo '<div id="issues">';
                                drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_p_getRelatedIssues.js');
                        echo '</div>'; // end of div issues

                echo '</div>'; // end if div tabs
         }
	 
	// Social links
	echo zp_functions_get_social_links();


     	
   echo '</div>';  // end of div txt
   

   
   
   
   
   
  echo '<div class="images">';
  

           $base_p = base_path();
	   $photopath = $base_p . 'files/shops/' . substr($node->field_zp_art_place[0]['value'], 0, 8) . '/' . $node->field_zp_art_place[0]['value'] . '/' . $node->field_zp_art_place[0]['value'];
       
	   if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-logo.jpg'))
			echo '<div class="shop_logo">' . theme('imagecache', 'place_logo_body', $photopath . '-logo.jpg', $title . ' logo') . '</div>';

		
	   $big_photo_num = 1; // первая (или первая существующая на сервере) картинка большая, остальные маленькие
	   		
	   for($i=1; $i <= $data[1]; $i++)
	   {
                        $img_src = $photopath . '-' . $i  . '.jpg';
                        
	   		if($i == $big_photo_num)	
	   		{
	   			
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $img_src))		
	   			{
                                        echo '<div class="image big">';
		   			echo '<a href="' . $img_src . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
		   			echo theme('imagecache', 'place_body_type1', $img_src, $title . ' ' . $i);
		  			echo '</a></div>';
                                        
                                        $image_src_for_meta = '/files/imagecache/place_body_type1' . $img_src;
		  			$photo_exist = 1;	
	   			}
	   			else 
	   				$big_photo_num = $i + 1;
	   		}
	   		else 
	   		{
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $img_src))		
	   			{
                                        echo '<div class="image small ' . $i .'">';
		   			echo '<a href="' . $img_src . '" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
                                        echo theme('imagecache', 'place_body_type2', $img_src, $title . ' ' . $i);
		  			echo '</a></div>';
                                        
                                        if(!$photo_exist)
                                        {   
                                            $image_src_for_meta = '/files/imagecache/place_body_type1' . $img_src;
                                            $photo_exist = 1;
                                        }
	   			}
                                
	   			
	   		}
	   }
	   
	   if(!$photo_exist)
           {
                if($data[2] AND file_exists($_SERVER['DOCUMENT_ROOT'] . $base_p . 'files/p/' . $data[2] . '/default.jpg'))
   		{
   			$photo_default = $base_p . 'files/p/' . $data[2] . '/default.jpg';
   			
   			echo '<div class="image_big">'
   					. theme('imagecache', 'product_body_type1-default', $photo_default, $title . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'" ) // третий аргумент - alt
   					. '</div>'; 
                        
                        $image_src_for_meta = $base_p . 'files/imagecache/product_body_type1-default' . $photo_default;
   		}
                else
                {
                    echo '<div class="image_big">'
   					. theme('imagecache', 'product_body_type1-default', $base_p . 'files/p/default_all.jpg', $title . ' в ' . $_SESSION['current_shop_name'] . ", Служба доставки 'За покупками'" ) // третий аргумент - alt
   					. '</div>';
                    
                    $image_src_for_meta = "/sites/all/themes/zp-themes/zp-two/img4/zaPokupkami.com.jpg";
                }
           }
           
           // set meta tag for search and social
           drupal_set_html_head('<link rel="image_src" href="' . $image_src_for_meta. '" />');    
 
            $qr = zp_functions_get_qr($node->field_zp_art_place[0]['value'], 'http://www.zapokupkami.com' . $_SERVER['REQUEST_URI']);
            echo '<div style="overflow:hidden; width: 160px"><img alt="' . $title .' qr-код" title="QR-код для отдела ' . $title . '" src="/' . $qr . '" width="170px" style="position:relative; left:-14px; top:-10px"></div>';

		
	echo '</div>'; //<!-- // end of body-images -->
	  
	  
    
    echo '<div class="list_title">Сделайте свой выбор:</div>';

 echo '</div>';
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 // -----------------------------------------------------------------------
 // покажем вьюс с потомками данной ноды (каталог тизеров, список подотделов или товаров данного отдела)
 
 cache_clear_all('*', 'cache_views', true);
			
 if($user->uid == 1)
 	$view = views_get_view('nodes_of_node_subterms_list');
 else 
 	$view = views_get_view('nodes_of_node_subterms_list_sell');
		
		
 if($view)
	{
		//zp_functions_show($view);

		if($_GET['tf'])
  		{
    		//$args[0] = $args[3]; // вариант во вьюсах при распознавании аргументов в url через слеш /
    		$args[0] = $_GET['tf']; 
			
    		//$view->page_header = '<div class="filter_label">Текущий фильтр:</div> из всего отдела показаны только товары подгруппы <div class="filter_group">"' . db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $args[0])) . '"</div>'; // . ' (для смены фильтра выберите новый подраздел в меню)';
			echo '<div class="views_filter"><span class="filter_label">Текущий фильтр:</span> из всего отдела показаны только товары подгруппы <span class="filter_group">"' . db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $args[0])) . '"</span>'; // . ' (для смены фильтра выберите новый подраздел в меню)';
   			
			//if($_GET['sort'] OR $_GET['order']) // если была задана сортировка, сохранить её при сбросе фильтра
			if($_GET['sort-type'] OR $_POST['sort-type']) // если была задана сортировка, сохранить её при сбросе фильтра
			{
				$srt = '?';
				if($_GET['sort-type']) 
					$srt .= 'sort-type=' . $_GET['sort-type'];
				else
					$srt .= 'sort-type=' . $_POST['sort-type'];
				
				
				//$view->page_header .= '. Вы можете <div class="filter_reset">'. l('>>сбросить фильтр<<', 'http://www.zapokupkami.com' . url('node/' . arg(1)) . $srt) 
				//. '</div> и увидеть товары всех подгрупп данного отдела или же выбрать другую подгруппу или раздел в меню слева или вверху.';
				
				echo '. Вы можете <span class="filter_reset">'. l('>>сбросить фильтр<<', 'http://www.zapokupkami.com' . url('node/' . arg(1)) . $srt) 
				. '</span> и увидеть товары всех подгрупп данного отдела или же выбрать другую подгруппу или раздел в меню слева или вверху.';
			}
			else 
				//$view->page_header .= '. Вы можете <div class="filter_reset">'. l('>>сбросить фильтр<<', 'http://www.zapokupkami.com' . url('node/' . arg(1))) . '</div> и увидеть товары всех подгрупп данного отдела или же выбрать другую подгруппу или раздел в меню слева или вверху.';
				echo '. Вы можете <span class="filter_reset">'. l('>>сбросить фильтр<<', 'http://www.zapokupkami.com' . url('node/' . arg(1))) . '</span> и увидеть товары всех подгрупп данного отдела или же выбрать другую подгруппу или раздел в меню слева или вверху.';
			
			echo '</div>';
  		}
  		else 
  		{
			//$result = db_result(db_query("SELECT tid from {term_node} WHERE nid  = '%s'", $args[0]));
			// предыдущая строка не работала, так как тогда путались словари и выдавался неправильный результат (так как у магазина - несколько словарей и несколько термов)
			//$node_tids = taxonomy_node_get_terms_by_vocabulary($args[0], 1); // $args[0] - аргументы в синтаксисе вьюсов в интерфейсе вьюсов
			$node_tids = taxonomy_node_get_terms_by_vocabulary(arg(1), 1);  // $args(1) - номер ноды из строки с url ($args(0) - 'node')
			foreach($node_tids as $node_tid)
        		$result = $node_tid->tid;

			if (($children = taxonomy_get_children($result))!=0) 
 			{
  				$args[0] = '';
	  			foreach ($children as $child) 
   				{
                                    if($args[0] != '')
                                            $args[0] = $args[0] . '+';
                                    $args[0] = $args[0] . $child->tid;

   				}
	 		}
 			else 
 				return;
		}


		// изменяем порядок сортировки
		//if ($_GET['sort']== "title") 
		if($_GET['sort-type'])
			$sort_type = $_GET['sort-type'];
		else 
		if($_POST['sort-type'])
			$sort_type = $_POST['sort-type'];
			
		//if($_GET['sort']== "title") 
		if(strpos($sort_type, 'title') !== FALSE)
		{
  			$view->sort[1]['vid'] = $view->vid;
  			$view->sort[1]['position'] = 1;
  			$view->sort[1]['field'] = 'node.title';

  			/*
  			if($_GET['order'] AND $_GET['order'] != '')
     			$view->sort[1]['sortorder'] = $_GET['order'];
  			else
     			$view->sort[1]['sortorder'] = 'ASC';
                        */
  			if($sort_type == 'title-asc')
  				$view->sort[1]['sortorder'] = 'ASC';
  			else 
  				$view->sort[1]['sortorder'] = 'DESC';
  				

  			$view->sort[1]['options'] = '';
  			$view->sort[1]['tablename'] = '';
  			$view->sort[1]['id'] = 'node.title'; 
		}
		else 
		//if ($_GET['sort']== "date") 
		if(strpos($sort_type, 'date') !== FALSE)
		{
  			$view->sort[1]['vid'] = $view->vid;
  			$view->sort[1]['position'] = 1;
  			$view->sort[1]['field'] = 'node.created';
  
  			/*
  			if($_GET['order'] AND $_GET['order'] != '')
     			$view->sort[1]['sortorder'] = $_GET['order'];
  			else
     			$view->sort[1]['sortorder'] = 'ASC';
     		*/
  			if($sort_type == 'date-asc')
  				$view->sort[1]['sortorder'] = 'ASC';
  			else 
  				$view->sort[1]['sortorder'] = 'DESC';

  			$view->sort[1]['options'] = '';
  			$view->sort[1]['tablename'] = '';
  			$view->sort[1]['id'] = 'node.created'; 
		}
		else 
		//if (!$_GET['sort'] OR $_GET['sort']== "price")
		if(strpos($sort_type, 'price') !== FALSE)
		{
  			// иначе остаётся сортировка по цене, но учитывается порядок
  			/*
  			if($_GET['order'] AND $_GET['order'] != '')
     			$view->sort[1]['sortorder'] = $_GET['order'];
  			else
     			$view->sort[1]['sortorder'] = 'ASC';
     		*/
  			
     		if($sort_type == 'price-asc')
  				$view->sort[1]['sortorder'] = 'ASC';
  			else 
  				$view->sort[1]['sortorder'] = 'DESC';

  			//$view->sort[1]['position'] = 1;
  			//$view->sort[1]['options'] = '';
  			//$view->sort[1]['field'] = 'sell_price';
		}

		//return $args; // в коде обработки вьюсов аргументы возвращались во вьюс для дальнейшей обработки, а тут мы их передаём в функцию формирования результатов вьюсов


		/*
		// для примера фильтра, взято из какой-то другой функции
		$view->filter[1] = array (
     		//'vid' => 3,
    		'tablename' => '',
      		'field' => 'term_node_7.tid',
      		'value' => array (
  					0 => $current_tid, 
					),
      		'operator' => 'AND',
      		'options' => '',
      		'position' => 1,
      		'id' => 'term_node_7.tid',
      			
    		);	
		*/
		
		//$limit = 0;
		//$out .= views_build_view('embed', $view, array(), FALSE, $limit);
		//echo views_build_view('embed', $view, array(), $view->use_pager, $view->nodes_per_page);
		
		
		
		
		
		// если подотдел содержит товары (а не другие подотделы)
		// покажем выбор порядка сортировки товаров:
		// - по цене, по названию / по возрастанию, по убыванию
		
		if($_GET['tf'] OR !db_result(db_query("SELECT tid from {term_hierarchy} WHERE parent  = %d", $child->tid))) // если у указанных потомком есть ещё потомки (т.е. мы показываем не товары, а отделы с подотделами)
		{
			
			// выделим текущий метод сортировки
			if($sort_type == 'price-asc')
			{
				$class1 = 'class="active"';
				$radio1 = 'checked';
			}
			else 
			if($sort_type == 'price-desc')
			{
				$class2 = 'class="active"';
				$radio2 = 'checked';
			}
			else 
			if($sort_type == 'title-asc')
			{
				$class3 = 'class="active"';
				$radio3 = 'checked';
			}
			else 
			if($sort_type == 'title-desc')
			{
				$class4 = 'class="active"';
				$radio4 = 'checked';
			}
			else 
			if($sort_type == 'date-asc')
			{
				$class5 = 'class="active"'; // пока эта опция не показывается
				$radio5 = 'checked';
			}
			else 
			if($sort_type == 'date-desc')
			{
				$class6 = 'class="active"'; // пока эта опция не показывается
				$radio6 = 'checked';
			}
			else
			{
				$class1 = 'class="active"'; // если в параметрах метод сортировки не задан, считаем, что выбран по умолчанию по цене по возрастанию
				$radio1 = 'checked';
			}
			
			echo '<div class="sort_block"><div class="sort_label">Сортировать товары:</div>';

			$p_url = explode('?', $_SERVER['REQUEST_URI']);
			$p_url = $p_url[0];
			if($_GET['tf'])
			{
				
				$p_url = $p_url . '?tf=' . $_GET['tf'];

			}		
					?>
					<form action="<?php echo $p_url ?>" method="post" enctype="application/x-www-form-urlencoded">

                    			<div class="sort-radio">
 									<input type="radio" name="sort-type" value="price-asc" onClick="this.form.action=this.form.action;this.form.submit()" title="Показать товары, отсортированные с возрастанием цены" <?php echo $radio1?>>по цене, с возраст.</input>
									<input type="radio" name="sort-type" value="price-desc" onClick="this.form.action=this.form.action;this.form.submit()" title="Показать товары, отсортированные с убыванием цены" <?php echo $radio2?>>по цене, с убыв.</input>
									<input type="radio" name="sort-type" value="title-asc" onClick="this.form.action=this.form.action;this.form.submit()" title="Показать товары, отсортированные по названию" <?php echo $radio3?>>по назв., с возраст.</input>
									<input type="radio" name="sort-type" value="title-desc" onClick="this.form.action=this.form.action;this.form.submit()" title="Показать товары, отсортированные по названию в обратном порядке" <?php echo $radio4?>>по назв., с убыв.</input>
								</div>

					<!-- <input type="submit" name="op" id="x2" value="Submit"  class="form-submit" /> -->
					</form>
				
					<?php



			echo '</div>';
		
		} // end of if($products)
		
		// выведем вьюс
		echo views_build_view('embed', $view, $args, $view->use_pager, $view->nodes_per_page);
		
	} // end of if($view)

  
	
	
// ссылка "Стать клиентом"	


if(!$user->uid)
	echo '<br><br><div class="become_client dept"><a href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto"><strong>Стать клиентом</strong> службы доставки <strong>"За Покупками!"</strong> очень просто!</a></div><br>';



	
	
	
     	
     	
     	
     	// попробуем добавить отслеживание названия через Гугль Аналитикс
     	
     	 /*	   
    	 drupal_add_js('
     
     				pageTracker._setCustomVar(
      					1,                   // This custom var is set to slot #1
      					"Otdel",           // The top-level name for your online content categories
      					//"Life & Style",      // Sets the value of "Section" to "Life & Style" for this particular aricle
      					"' . str_replace('&quot;', "'", $title) . '",
      					3                    // Sets the scope to page-level 
   					);
    				  
    			   ', 
    			   'inline');
    			   
    */		
     	 
     	 /*
     	 drupal_add_js("

						$(document).ready(function()
						{
						
							_gaq.push(['_setCustomVar',
      									2,                   // This custom var is set to slot #1
      									'Otdel',           // The top-level name for your online content categories
      									'" . str_replace('&quot;', '"', $title) . "',  // Sets the value of  for this particular aricle
      									3                    // Sets the scope to page-level 
   									]);
							
 						});
 	
				   ", 'inline');
     	 
     	 
     	 
     	
     	 drupal_add_js('
     	 				//alert("xxx!");
     	 				
     	 				pageTracker._setCustomVar(
      								2,                   // This custom var is set to slot #2
      								"Otdel",           // The top-level name for your online content categories
      								//"Life & Style",      // Sets the value of "Section" to "Life & Style" for this particular aricle
      								"' . str_replace('&quot;', "'", $title) . '",
      								3                    // Sets the scope to page-level 
   								);
   								

   								
   								
   								
   					    // OR
   					    
   					    
   					    
   					    
   					    
						$(document).ready(function()
						{
						
							pageTracker._setCustomVar(
      								2,                   // This custom var is set to slot #1
      								"Otdel",           // The top-level name for your online content categories
      								//"Life & Style",      // Sets the value of "Section" to "Life & Style" for this particular aricle
      								"' . str_replace('&quot;', "'", $title) . '",
      								3                    // Sets the scope to page-level 
   								);
   								
   							pageTracker._trackPageview();  // is it needed to launch?
						
 						});
 	
				   	', 'inline');
     	 
     	 
     	 */
     	 
     	 
	
	

     	 
     	 
     	 
 
} // end of if ($page == 1)

?>