<?php 


/*

// почему-то загруженная отдельно карта не работает :(

// загрузим карту гугля и выдадим её запрашивающей функии по ajax
// эти данные будут встроены с помощью ajax во вкладку с картой


if($_GET['source'] == 'ajax')
{

	echo '<div class="ajaxed">';

		if($map)
		{
     		echo theme('gmap', array('#settings' => $map)); 
		}
		else 
		{
			echo 'К сожалению, карта недоступна.';
		}
		
	echo '</div>';
	
	return;
	
}

// конец формирования данных для выдачи ajax-функции

*/











$title_corrected = str_replace(array('&quot;', '&amp;'), array("'", '&'), $title);

echo '<div class="test">shop</div>';


//if teaser ---------------------------------------------------------------------------------



if ($teaser == 1): ?>
 <div class="place_teaser shop">

    <a href="<?php echo $node_url ?>">

    
      <?php 
           
          $data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);
      
      	  $type = explode('***', $data[0]);
          echo '<table><tbody><tr>'
           . '<td class="caption">'
           . '<div class="type">' . $type[0] . '</div>'
           . '<div class="title">' . '<a href="' . $node_url . '">' . $title . '</a>' .'</div>'
           . '</td>';
           
           $logopath = base_path() . 'files/shops/' . $node->field_zp_art_place[0]['value'] . '/' . $node->field_zp_art_place[0]['value'] . '-logo.jpg';
           if(file_exists($_SERVER['DOCUMENT_ROOT'] . $logopath))
				echo '<td class="logo">' . theme('imagecache', 'place_logo_teaser', $logopath, $title_corrected . ", Служба доставки 'За покупками'") . '</td>';

           
          echo '<td class="description">'
           . '<div class="address">' . $node->field_place_address[0]['value'] . '</div>' 
           . '<div class="descr">' . $node->field_descr_teaser[0]['value'] . '</div>'
           . '</td>'
           . '</tr></tbody></table>';
      ?>   
    
    </a>



 
 </div>
<?php endif; // end of teaser ?>











<?php 

if($page == 1)
{

//zp_functions_show_DrupalStatusMessages();

 echo '<div class="place_body shop">
   <div class="txt">';

   		$data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	
		
   		$type = explode('***', $data[0]);
    	echo '<div class="type">'. $type[0] . '</div>';
     	echo '<div class="title">' . (strlen($title)> 30? str_replace(' /', ',<br>',$title): $title) . '</div>';
     	echo '<div class="address">' . $node->field_place_address[0]['value'] . '</div>';
     
     
     
        
    	
        echo '<div id="tabs">';

        drupal_add_css('sites/all/modules/_Jstools/jstab/jquery.tabs.css');
    	drupal_add_js('sites/all/modules/_Jstools/jstab/jquery.tabs.js'); 
    	
    	drupal_add_js('$(function(){
    		basePath = "'. base_path() .'";
    		$("#tabs").tabs({ initial: 0, fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal",  remote: 0 });
    		})', 'inline');
    	?>  
    		<ul>
        		<li><a href="#descr">Описание</a></li>
        		<li><a href="#map" id="map_button">Положение на карте</a></li>
    		</ul>

		    <div id="descr">
		    <?php 
		    	echo $node->content['body']['#value']; 
		    	
		    	echo '<p>Воспользуйтесь меню отделов магазина (вверху на синей полоске) или боковым меню текущего отдела для выбора отдела или группы товара!</p>';
		    	
		    	global $user;
		    	if(!$user->uid)
		    		echo '<p><a href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto">Станьте нашим клиентом уже сегодня</a>, чтобы начать делать покупки с помощью службы доставки "За Покупками!"</p><br>';
		    	
		    	echo '<div><a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#stoimost-i-min-summa-korziny">Стоимость доставки и минимальная сумма корзины</a>...</div><br>';
		    	
		    	// если у этого магазина есть отдельная страница с описанием (конкретно этого магазина или сети, в которую этот магазин входит)
		    	// тогда покажем ссылку на эту страницу
		    	if($descr_page_nid = db_result(db_query("SELECT nid from {content_field_s_zp_artikuls} WHERE field_s_zp_artikuls_value = '%s'", $node->field_zp_art_place[0]['value'])))
				{
					echo '<div class="todescrlink">' . '<a href="' . url('node/' . $descr_page_nid) . '">Развёрнутое описание <br> ' . $type[1] .  ' ' . $title . '...</a></div><br>';					
				}
		    ?>
		    </div>
		    
		    
     		<div id="map">
     		
     		<?php 
     		
     				if($map) 
     					echo theme('gmap', array('#settings' => $map)); 
     			
     			 	// статическая карта, загружается быстрее
     				//if($static_map_img)
     					//echo $static_map_img;
     					
     				/*
     					
     				// почему-то загруженная отдельно, через ajax, карта не работает :(
     				
     				
     				// вместо того, чтобы сразу загружать карту, откладываем генерацию списка до нажатия на вкладку со статьями
  					// список выдаём по запросу ajax-функции (вначале этого файла)
  					
  					drupal_add_js("

						$(document).ready(function()
						{
							//$('#button').click
							$('#map_button').click
 							(
 								function() 
 								{
 									//if($('#map').html() == '')
 									if(1)
 									{
 										$('#map').html('<div class=".'"wait"' . ">Пожалуйста, подождите. <br>Идёт загрузка данных...</div><div class=" . '"loader"' . "></div>');
 										$('#map').load
 										(
 											window.location.pathname + '?source=ajax .ajaxed', 
 											function(response, status, xhr) 
 											{
  												if($('#map').children().html() == '' || status == 'error')
													$('#map').html('Нет данных');
											}
										);
		 							}
		 						}
							);
 						});
 	
						", 'inline');
     			
					*/
     				
     		?>
     		
     		</div>
     		
     		
	    </div>
   
            
        <?php 
        // Social links
        //echo zp_functions_get_social_links(); 
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


   </div>
   
   
   
   
   
   
   
   
   
   <div class="images">

      
	   <?php 
	   
	   $photopath = base_path() . 'files/shops/' . $node->field_zp_art_place[0]['value'] . '/' . $node->field_zp_art_place[0]['value'];
       
	   if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-logo.jpg'))
                echo '<div class="shop_logo">' . theme('imagecache', 'place_logo_body', $photopath . '-logo.jpg', $title_corrected . " logo, Служба доставки 'За покупками'") . '</div>';

		
				
	   $big_photo_num = 1; // первая (или первая существующая на сервере) картинка большая, остальные маленькие
	   		
	   for($i=1; $i <= $data[1]; $i++)
	   {
	   		if($i == $big_photo_num)	
	   		{
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-photo-' . $i . '.jpg'))		
	   			{
		   			$img_scr = $photopath . '-photo-' . $i  . '.jpg';
                                        echo '<div class="image big">';
		   			//echo '<a href="' . $photopath . '-photo-' . $i  . '.jpg" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
		   			echo '<a href="' . $img_scr . '" title="' . $title_corrected . '" rel="lightbox[roadtrip][' . $title_corrected . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
                                        echo theme('imagecache', 'place_body_type1', $img_scr, $title_corrected . ' ' . $i . ", Служба доставки 'За покупками'");
		  			echo '</a></div>';
                                        
                                        $image_src_for_meta = '/files/imagecache/place_body_type1' . $img_scr;
                                        $photo_exist = 1;
	   			}
	   			else 
	   				$big_photo_num = $i + 1;
	   		}
	   		else 
	   		{
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-photo-' . $i . '.jpg'))		
	   			{
		   			$img_scr = $photopath . '-photo-' . $i  . '.jpg';
                                        echo '<div class="image small ' . $i .'">';
		   			//echo '<a href="' . $photopath . '-photo-' . $i  . '.jpg" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
		   			echo '<a href="' . $img_scr . '" title="' . $title_corrected . '" rel="lightbox[roadtrip][' . $title_corrected . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
                                        echo theme('imagecache', 'place_body_type2', $img_scr, $title_corrected . ' ' . $i . ", Служба доставки 'За покупками'");
		  			echo '</a></div>';
                                        
                                        if(!$photo_exist)
                                        {
                                            $image_src_for_meta = '/files/imagecache/place_body_type1' . $img_scr;
                                            $photo_exist = 1;
                                        }
	   			}
	   			
	   		}
	   } // end of for($i=1; $i <= $data[1]; $i++)
			 
           if(!$photo_exist)
               $image_src_for_meta = "/sites/all/themes/zp-themes/zp-two/img4/zaPokupkami.com.jpg";
	  
           // set meta tag for search and social
           drupal_set_html_head('<link rel="image_src" href="' . $image_src_for_meta. '" />');  
	
		
	echo '</div>'; //<!-- // end of body-images -->
 
	  
    echo '<div class="list_title">Выберите отдел:</div>';
    
 echo '</div>';
 
 
 
 
 
 
 
 
 // -----------------------------------------------------------------------
 // покажем вьюс с потомками данной ноды (каталог тизеров, список отделов магазина)
 
 cache_clear_all('*', 'cache_views', true);
			
 $view = views_get_view('nodes_of_node_subterms_list');
		
		
 if($view)
	{
		//zp_functions_show($view);

		

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

    			//$childdd = $child->tid;
   				//drupal_set_message("child = $childdd", 'error');
			}
 		}
 		else 
 			return;



		// изменяем порядок сортировки
		if ($_GET['sort']== "title") 
		{
  			$view->sort[1]['vid'] = $view->vid;
  			$view->sort[1]['position'] = 1;
  			$view->sort[1]['field'] = 'node.title';

  			if($_GET['order'] AND $_GET['order'] != '')
     			$view->sort[1]['sortorder'] = $_GET['order'];
  			else
     			$view->sort[1]['sortorder'] = 'ASC';

  			$view->sort[1]['options'] = '';
  			$view->sort[1]['tablename'] = '';
  			$view->sort[1]['id'] = 'node.title'; 
		}
		else 
		if ($_GET['sort']== "date") 
		{
  			$view->sort[1]['vid'] = $view->vid;
  			$view->sort[1]['position'] = 1;
  			$view->sort[1]['field'] = 'node.created';
  
  			if($_GET['order'] AND $_GET['order'] != '')
     			$view->sort[1]['sortorder'] = $_GET['order'];
  			else
     			$view->sort[1]['sortorder'] = 'ASC';

  			$view->sort[1]['options'] = '';
  			$view->sort[1]['tablename'] = '';
  			$view->sort[1]['id'] = 'node.created'; 
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
		
		echo views_build_view('embed', $view, $args, $view->use_pager, $view->nodes_per_page);
		
	} // end of if($view)

 
 
if(!$user->uid)
	echo '<br><br><div class="become_client dept"><a href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto"><strong>Стать клиентом</strong> службы доставки <strong>"За Покупками!"</strong> очень просто!</a></div><br>';


 
} // end of if($page == 1)


?>