<?php 




// получим список заведений из того же района, что и текущее заведение и выдадим запрашивающей функии по ajax
// эти данные будут встроены на страницу с помощью ajax 

if($_GET['source'] == 'ajax')
{
	
	$vid = 7; // словарь с содержанием (списком) статей
	foreach ($node->taxonomy as $category)
	{
		// так как магазин принадлежать и к категории района, и к категории сетей, выбираем только терм с категорией района
		if($category->vid == $vid AND $category->name != 'Сети магазинов и заведений')
		{
			$cur_term_tid = $category->tid;
			//$cur_term['name'] = $category->name;
		}
	}
 
  	
	// в общем списке статей этого же раздела не показываем ссылку на текущую статью
	$issues_nids = db_query("SELECT nid FROM {term_node} WHERE tid = %d AND nid <> %d", $cur_term_tid, $node->nid);


	echo '<div class="ajaxed">';
	
		if($issues_nid = db_fetch_array($issues_nids))
		{
			echo '<div class="i_title other-issues">' . l(db_result(db_query("SELECT title FROM {node} WHERE nid = %d", $issues_nid['nid'])), 'node/' . $issues_nid['nid'], array('title' => 'Перейти на страницу со статьёй')) . '</div>';
			echo '<div class="i_teaser">' . db_result(db_query("SELECT field_s_teaser_value from {content_type_shop_descr} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
		
			while($issues_nid = db_fetch_array($issues_nids))
			{
				echo '<div class="i_title">' . l(db_result(db_query("SELECT title FROM {node} WHERE nid = %d", $issues_nid['nid'])), 'node/' . $issues_nid['nid'], array('title' => 'Перейти на страницу со статьёй')) . '</div>';
				echo '<div class="i_teaser">' . db_result(db_query("SELECT field_s_teaser_value from {content_type_shop_descr} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
			}
		}
		else 
		{
			echo 'В этом районе пока нет других партнёров службы доставки "За Покупками!"';	
		}

	echo '</div>';
	
	return;
	
}

// конец формирования данных для выдачи ajax-функции







if ($teaser == 1): 

echo '<div class="mc_teaser">';

	

	//echo l($node->title . '. ' . $node->field_s_place[0]['value'], $node->path, array('class' => 'mc_title catalog')); 
	echo '<div class="mc_title catalog">' . l($node->title, $node->path, array('class' => 'mc_title catalog')) . ' / ' . $node->field_s_place[0]['value'] . '</div>'; 
	echo '<div class="teaser">' . $node->field_s_teaser[0]['value'] . '</div>'; 

	?>
	
</div>

<?php endif; // end of teaser










	
	
	
// ---------------------------------------------------------------------------------------------------------


if ($page == 1): 


require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');

drupal_add_js('misc/collapse.js'); // схлопывающиеся филдсеты	


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






echo '<div class="mc_body">';


	foreach ($node->taxonomy as $term)
		{
			if($term->vid == 7) // 7 - словарь с содержанием
				$mctype .= $term->name . ', ';
		}
		
	//echo '<div class="title2">Статьи, описания / Магазины и заведения <br>/ ' . $mctype . ':</div>';
	echo '<div class="title2">' . l('Статьи, описания / Магазины и заведения ', MY_SHOPSLIST_NODE) . '<br>/ ' . $mctype . ':</div>';
	
	
	echo '<div class="title">' . $title . '</div>';

	$link = zp_functions_continue_shopping_link();
  	echo '<div class="links">' . l('Перейти к списку магазинов / заведений', MY_SHOPSLIST_NODE, array('title' => 'Пожалуйста, ознакомьтесь также и с другими производителями и брендами!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 
  	
	// найдём ноды всех перечисленных в магазине (по внутренним zp артикулам) магазинов
	// для ссылок на покупки в этих магазинах
	$nid_exist = 0;
	foreach ($node->field_s_zp_artikuls as $s_zp_artikul)
	{
		if($s_nid = db_result(db_query("SELECT nid from {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", $s_zp_artikul['value'])))
		{
			
			// найдём название и адрес магазина с товарами для покупок через систему
			$s_title = db_result(db_query("SELECT title from {node} WHERE nid = %d", $s_nid));
			$s_address = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid = %d", $s_nid));
			if(!$nid_exist)
				//echo '<div class="toshop">Перейти к покупкам в:</div>';
				$shops_link .= '<div class="toshop">Перейти к покупкам в:</div>';
			//echo '<div class="toshoplink">' . l($s_title . ' (' . $s_address . ')', 'node/' . $s_nid) . '</div>';
			
			//$shops_link .= '<div class="toshoplink">' . l('<strong>' . $s_title . '</strong>' . ' (' . $s_address . ')', 'node/' . $s_nid) . '</div>';
			
			$shops_link .= '<div class="toshoplink">' . '<a href="' . url('node/' . $s_nid) . '"><strong>' . $s_title . '</strong> (' . $s_address . ')</a></div>';
			
			//echo '<br>s_zp_artikul[value] = ' . $s_zp_artikul['value'] . 's_nid = ' . $s_nid .  ', s_title = ' . $s_title . ', s_address = ' . $s_address;
			$nid_exist = 1;
		}
		
	}

	echo $shops_link;
 	
 	//echo '<div class="body">' . $body . '</div>';
	echo '<div class="body">' . $node->content['body']['#value'] . '</div>'; 
	
	//print '<div class="author date">' . $node->name . ', ' . format_date($node->created, $format="%m/%d/%Y") . '</div>';

	echo $shops_link;

	
	
	
	
	
	
	// если это описание сети (магазины по разным адресам)
	// то не показываем кнопку с выпадающим списком остальных магазинов в этом районе 
	// (так как нет одного конкретного района, в котором расположен этот магазин)
	// а наче показываем кнопку и список
	foreach ($node->taxonomy as $term)
	{
		if($term->name == 'Сети магазинов и заведений')
		{
			$is_network = 1;
			break;
		}
		
	}
	
	if(!$is_network)
	{
		// выведем через ajax список остальных производителей из этого же раздела (направления)
		echo '<div class="others_in_topic">';
		echo '<fieldset class="collapsible collapsed"><legend><a href="#" id="issues_button">Другие магазины / заведения в этом районе</a></legend>';


		echo '<div id="issues">';


		// вместо того, чтобы сразу генерировать список соответствующих материалов, откладываем генерацию списка до нажатия на вкладку со статьями
		// список выдаём по запросу ajax-функции (вначале этого файла)

		drupal_add_js("

						$(document).ready(function()
						{
							//$('#button').click
							$('#issues_button').click
 							(
 								function() 
 								{
 									if($('#issues').html() == '')
 									//if(1)
 									{
 										$('#issues').html('<div class=".'"wait"' . ">Пожалуйста, подождите. <br>Идёт загрузка данных...</div><div class=" . '"loader"' . "></div>');
 										$('#issues').load
 										(
 											window.location.pathname + '?source=ajax .ajaxed', 
 											function(response, status, xhr) 
 											{
  												if($('#issues').children().html() == '' || status == 'error')
													$('#issues').html('Нет данных');
											}
										);
		 							}
		 						}
							);
 						});
 	
						", 'inline');



		echo '</div>'; // end of div issues

		echo '</fieldset>';
		echo '</div>';

	} // end of if(!$is_network)
	
	
	
	
	
  	echo '<div class="links bottom">' . l('Перейти к списку магазинов / заведений', MY_SHOPSLIST_NODE, array('title' => 'Пожалуйста, ознакомьтесь также и с другими производителями и брендами!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 
 	

echo '</div>';
	
endif; // end of body 

?>
