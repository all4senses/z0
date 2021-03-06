<?php 



// получим список статей из того же раздела, что и текущая статья и выдадим запрашивающей функии по ajax
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
			echo '<div class="i_teaser">' . db_result(db_query("SELECT field_issue_teaser_value from {content_field_issue_teaser} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
		
			while($issues_nid = db_fetch_array($issues_nids))
			{
				echo '<div class="i_title">' . l(db_result(db_query("SELECT title FROM {node} WHERE nid = %d", $issues_nid['nid'])), 'node/' . $issues_nid['nid'], array('title' => 'Перейти на страницу со статьёй')) . '</div>';
				echo '<div class="i_teaser">' . db_result(db_query("SELECT field_issue_teaser_value from {content_field_issue_teaser} WHERE nid = %d", $issues_nid['nid'])) . '</div>';
			}
		}
		else 
		{
			echo 'В этом разделе пока нет других справочных данных или статей.';	
		}

	echo '</div>';
	
	return;
	
}

// конец формирования данных для выдачи ajax-функции












if ($teaser == 1): 



// ---------------------------------------------------------------------------------------------------------




echo '<div class="i_teaser catalog">';

	

	echo l($node->title, $node->path, array('class' => 'i_title catalog')); 
	echo '<div class="i_teaser catalog">' . $node->field_issue_teaser[0]['value'] . '</div>'; 
	
	global $user;
	if($user->uid == 1)
	{
		echo '<div class="keywords">Ключевые слова: ';
			foreach ($node->taxonomy as $term)
			{
				if($term->vid == 6) // 6 - словарь с ключевыми словами по данной статье
	 				echo $term->name . ', ';
			}
		echo '</div>';
	}

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






echo '<div class="i_body">';


	foreach ($node->taxonomy as $term)
		{
			if($term->vid == 6) // 6 - словарь с ключевыми словами по данной статье
				$keywords .= $term->name . ', ';
			else
				if($term->vid == 7) // 7 - словарь с темами статей (с типами товаров и услуг)
					$type = $term->name;
		}
		
	//echo '<div class="title2">Справочные данные / ' . $type . ':</div>';
	echo '<div class="title2">' . l('Справочные данные', MY_ISSUES_SPRAVKA_LIST_NODE) . ' / ' . $type . ':</div>';
	
	
	echo '<div class="title">' . $title . '</div>';

	$link = zp_functions_continue_shopping_link();
  	echo '<div class="links">' . l('Перейти к списку всех справочных данных, статей', MY_ISSUES_SPRAVKA_LIST_NODE, array('class' => 'links', 'title' => 'Пожалуйста, ознакомьтесь также и с другими статьями о товарах и услугах!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьёй Вы можете вернуться к покупкам...')) . '</div>'; 
  	
	
	
  
  
  
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


 	global $user;
	if($user->uid == 1)
	{
		echo '<div class="keywords">Ключевые слова: ' . $keywords . '</div>';
	
		echo '<a class="add_node" href="' . base_path() . 'node/add/' . $node->type . '" title="Добавить новую статью">';
			echo '> Добавить новую статью';
		echo '</a>';
	}
	
 	echo '<div class="body">' . $node->content['body']['#value'] . '</div>'; 
	
	//print '<div class="author date">' . $node->name . ', ' . format_date($node->created, $format="%m/%d/%Y") . '</div>';

  	
	
	
	
	// выведем через ajax список остальных статей из этого же раздела
	echo '<div class="others_in_topic">';
	echo '<fieldset class="collapsible collapsed"><legend><a href="#" id="issues_button">Остальные статьи из данного раздела</a></legend>';

	
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
     			
     			
     			
	
	
	
	echo '<div class="links">' . l('Перейти к списку всех справочных данных, статей', MY_ISSUES_SPRAVKA_LIST_NODE, array('class' => 'links', 'title' => 'Пожалуйста, ознакомьтесь также и с другими статьями о товарах и услугах!')) . '</div>'; 	
  	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьёй Вы можете вернуться к покупкам...')) . '</div>'; 
 	

echo '</div>';
	
endif; // end of body 

?>
