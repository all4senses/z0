<?php 



// получим список контрагентов из того же раздела, что и текущий контрагент и выдадим запрашивающей функии по ajax
// эти данные будут встроены на страницу с помощью ajax 

if($_GET['source'] == 'ajax')
{
	
	$vid = 7; // словарь с содержанием (списком) статей
	foreach ($node->taxonomy as $category)
	{
		if($category->vid == $vid)
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
			echo '<div class="i_teaser">' . db_result(db_query("SELECT field_mc_teaser_value from {content_type_mc_descr} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
		
			while($issues_nid = db_fetch_array($issues_nids))
			{
				echo '<div class="i_title">' . l(db_result(db_query("SELECT title FROM {node} WHERE nid = %d", $issues_nid['nid'])), 'node/' . $issues_nid['nid'], array('title' => 'Перейти на страницу со статьёй')) . '</div>';
				echo '<div class="i_teaser">' . db_result(db_query("SELECT field_mc_teaser_value from {content_type_mc_descr} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
			}
		}
		else 
		{
			echo 'По этому направлению пока нет данных о других производителях/поставщиках.';
		}

	echo '</div>';
	
	return;
	
}

// конец формирования данных для выдачи ajax-функции














if ($teaser == 1): 


echo '<div class="mc_teaser">';

	

	echo l($node->title . ', ' . $node->field_mc_country[0]['value'], $node->path, array('class' => 'mc_title catalog')); 
	echo '<div class="teaser">' . $node->field_mc_teaser[0]['value'] . '</div>'; 
	
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
		
	//echo '<div class="title2">Статьи, описания / Производители и поставщики <br>/ ' . $mctype . ':</div>';
	echo '<div class="title2">' . l('Статьи, описания / Производители и поставщики', MY_MCLIST_NODE) . '<br>/ ' . $mctype . ':</div>';
	
	
	echo '<div class="title">' . $title . '</div>';

	$link = zp_functions_continue_shopping_link();
  	echo '<div class="links">' . l('Перейти к списку всех производителей / поставщиков', MY_MCLIST_NODE, array('title' => 'Пожалуйста, ознакомьтесь также и с другими производителями и брендами!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 
  	
	
	
	
	//zp_functions_show($node);
	
	if($node->field_kontragent_type[0]['value'])
     	echo '<div class="art_and_bar"><div class="label">Тип: </div>' . $node->field_kontragent_type[0]['view'] . '</div>';
    if($node->field_types_of_products[0]['value'])
     	echo '<div class="art_and_bar"><div class="label">Основная продукция: </div>' . $node->field_types_of_products[0]['view'] . '</div>';
    if($node->field_brand_names[0]['value'])
     	echo '<div class="art_and_bar"><div class="label">Основные бренды: </div>' . $node->field_brand_names[0]['view'] . '</div>';
	if($node->field_mc_addresses[0]['value'])
     	echo '<div class="art_and_bar"><div class="label">Адрес(а): </div>' . $node->field_mc_addresses[0]['view'] . '</div>';
 	
	// Описание
 	//echo '<div class="body">' . $body . '</div>';
	echo '<div class="body">' . $node->content['body']['#value'] . '</div>'; 
	
	//print '<div class="author date">' . $node->name . ', ' . format_date($node->created, $format="%m/%d/%Y") . '</div>';

  	
	
	
	

	
	// выведем через ajax список остальных производителей из этого же раздела (направления)
	echo '<div class="others_in_topic">';
	echo '<fieldset class="collapsible collapsed"><legend><a href="#" id="issues_button">Другие производители / поставщики по этому направлению</a></legend>';

	
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
    
    
    
    
    	
	
	echo '<div class="links">' . l('Перейти к списку всех производителей / поставщиков', MY_MCLIST_NODE, array('title' => 'Пожалуйста, ознакомьтесь также и с другими производителями и брендами!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 
 	

echo '</div>';
	
endif; // end of body 

?>
