<!-- <div class="test">city</div> -->

<?php 

//if teaser ---------------------------------------------------------------------------------

?>

<?php if ($teaser == 1): ?>
 <div class="place_teaser city">
   
   
    <a href="<?php print $node_url ?>">
     <?php 
     		
     	   $data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	 	   
     
           echo '<table><tbody>'
           . '<tr>'
           . '<td class="caption">'
           . '<div class="type">' . $data[0] . '</div>'
           . '<div class="title">' . '<a href="' . $node_url . '">' . $title . '</a>' .'</div>'
           . '</td>'
           . '<td class="description">'
           . '<div class="address">' . $node->field_place_address[0]['value'] . '</div>'
           . '<div class="descr">' . $node->field_descr_teaser[0]['value'] . '</div>'
           . '</td>'
           . '</tr>'
           . '</tbody></table>';
     
      ?>       
    </a>         
       
   
   
 
 </div>
<?php endif; // end of teaser ?>



















<?php 

//if body (page) ---------------------------------------------------------------------------------


if($page == 1)
{


/*

if($_SESSION['messages'])
 foreach($_SESSION['messages'] as $key => $values)
 {
   switch($key)
    {
	case 'error':
		foreach($values as $value)
		  print '<div class="message_error">' . $value . '</div>';
		break;

	case 'warning':
        foreach($values as $value)
           print '<div class="message_warning">' . $value . '</div>';
		break;	

	default:
		foreach($values as $value)
		  print '<div class="message_status">' . $value . '</div>';
		break;	
    }
 }
*/





 echo '<div class="place_body city">
   <div class="txt">';
   
   
 		$data = explode(';', $node->field_placetype_n_numofphotos[0]['view']);	

   		echo '<div class="type">'. $data[0] . '</div>';
     	echo '<div class="title">' . $title . '</div>';
     	echo '<div class="address">' . $node->field_place_address[0]['value'] . '</div>';
     	echo '<div class="descr">' . $node->content['body']['#value'] . '</div>';

     
   echo '</div>';  // end of div txt

   

   
   
  echo '<div class="images">';
  

   
	   $photopath = base_path() . 'files/cities/' . $node->field_zp_art_place[0]['value'] . '/' . $node->field_zp_art_place[0]['value'];
       
				
	   $big_photo_num = 1; // первая (или первая существующая на сервере) картинка большая, остальные маленькие
	   		
	   for($i=1; $i <= $data[1]; $i++)
	   {
	   		if($i == $big_photo_num)	
	   		{
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-photo-' . $i . '.jpg'))		
	   			{
		   			echo '<div class="image big">';
		   			echo '<a href="' . $photopath . '-photo-' . $i  . '.jpg" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
			    	echo theme('imagecache', 'place_body_type1', $photopath . '-photo-' . $i . '.jpg', $title . ' ' . $i);
		  			echo '</a></div>';
	   			}
	   			else 
	   				$big_photo_num = $i + 1;
	   		}
	   		else 
	   		{
	   			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $photopath . '-photo-' . $i . '.jpg'))		
	   			{
		   			echo '<div class="image small ' . $i .'">';
		   			echo '<a href="' . $photopath . '-photo-' . $i  . '.jpg" title="Увеличить и посмотреть другие фото..." rel="lightbox[roadtrip][' . $title . ' ' . $i . ']" rel2="body-images">';// class="lightbox_hide_image"
			    	echo theme('imagecache', 'place_body_type2', $photopath . '-photo-' . $i . '.jpg', $title . ' ' . $i);
		  			echo '</a></div>';
	   			}
	   			
	   		}
	   }
			 

		
	echo '</div>'; //<!-- // end of body-images -->

    echo '<div class="list_title">Выберите район города:</div>';

    
 echo '</div>';

 
 
 
 
 
 // -----------------------------------------------------------------------
 // покажем вьюс с потомками данной ноды (каталог тизеров, список магазинов данного района)
 
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
 
 
 
} // end of if($page == 1)


?>