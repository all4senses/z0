<?php 



// получим список статей из того же раздела, что и текущая статья и выдадим запрашивающей функии по ajax
// эти данные будут встроены на страницу с помощью ajax 

if($_GET['source'] == 'ajax')
{
	echo '<div class="ajaxed">';
            zp_functions_show_other_issues_list($node->nid, $node->taxonomy);
	echo '</div>';
	
	return;
}

// конец формирования данных для выдачи ajax-функции











// получим список магазинов и отделов, где продаются товары, указанные в данной статье (определим по ключевым словам статьи, совпадающим с названиями каталогов отделов и ключевыми словами отделов и товаров).... и выдадим запрашивающей функии по ajax
// эти данные будут встроены на страницу с помощью ajax 

if($_GET['source'] == 'ajax_where_to_buy')
{
	
	$vid = 6; // словарь с ключевыми словами статьи, в которых указываются, кроме собственно ключевых слов, названия каталогов с картинками товаров и отделов - по ним тоже связываются статьи с товарами и отделами
	foreach ($node->taxonomy as $keyword)
	{
		if($keyword->vid == $vid)
		{
			//$keywords[] = $keyword->tid;
			$keywords[] = $keyword->name;
		}
	}
 
  	foreach ($keywords as $pic_catalog)
  	{
  		if(!$pic_catalogs)
  			$pic_catalogs = "field_placetype_n_numofphotos_value LIKE '%%" . ';' . $pic_catalog . "%'";
  		else 
  			$pic_catalogs .= " OR field_placetype_n_numofphotos_value LIKE '%%" . ';' . $pic_catalog . "%'";
  	}
	
        $otdels_nids = db_query("SELECT nid FROM {content_field_placetype_n_numofphotos} WHERE " . $pic_catalogs);

	echo '<div class="ajaxed">';
	
	while($otdel_nid = db_fetch_array($otdels_nids))
	{
		$linage = array();
		
		$vid = 1; // каталог
		$terms = taxonomy_node_get_terms_by_vocabulary($otdel_nid['nid'], $vid);

		foreach($terms as $value)
		{
			$term['name'] = $value->name;
			$term['tid'] = $value->tid;
			
		}
		$term['leaf'] = 1; // отдел не имеет детей, т.е. подотделов
		$term['nid'] = $otdel_nid['nid'];
		$term['link'] = l($term['name'], 'node/'.$term['nid'], array('title' => 'Перейти к покупкам в этом отделе'));//, array('rel' => 'nofollow'));
		
		$linage[$term['tid']] = $term;

		$prev_term_tid = $term['tid'];
		$prev_term_name = $term['name'];
		while(($term['tid'] = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $term['tid']))) != 0)
		{

			// не ищем нид, так как это всё равно родительский терм, а с него мы ссылку (переход к родительскому отделу) не делаем
			//$term['nid'] = db_result(db_query("SELECT nid from {term_node} WHERE tid  = '%s'", $term['tid']));
			
			$term['name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = '%s'", $term['tid']));

			if($term['nid'])
				$term['link'] = l($term['name'], 'node/'.$term['nid']);//, array('rel' => 'nofollow'));
			else
				$term['link'] = t($term['name']);
			
			$term['leaf'] = 0;	// терм имеет детей... все термы в этом цикле имеют детей, так как самое последнее дитё обозначено до цикла
				
			$linage[$term['tid']] = $term;
			
			$linage[$prev_term_tid]['parent_tid'] = $term['tid'];
			$linage[$prev_term_tid]['parent_name'] = $term['name'];
			
			$prev_term_tid = $term['tid'];
			$prev_term_name = $term['name'];
		}
		
		// переворачиваем массив наоборот, чтобы было направление от страны к отделу (пока - наоборот)
		$linage = array_reverse($linage, true);
	
		foreach ($linage as $key => $otdel)
		{
			$flag_dubl = 0;
			if($linages[$key])
			{
				$save_leaf = $linages[$key]['leaf'];
				$flag_dubl = 1;
			}
			$linages[$key] = $otdel;
			if($flag_dubl == 1 AND $save_leaf == 0 AND $otdel['leaf'] == 1)
				$linages[$key]['leaf'] = 0; // если терм уже был обозначен как имеющий потомков, а потом появляется вариант без потомков, восстанавливаем значение "с потомками"!
		}


	}
	
	if($linages) // если были найдены свящанные со статьёй отделы
	{
		// сбрасываем названия ключей
		$linages = array_merge(array(), $linages);

		zp_functions_make_where_to_buy_list($linages, count($linages), 0);
		
		echo 'Пожалуйста, выберите в вашем городе и районе заинтересовавший Вас магазин / заведение и отдел.';	
	}
	else 
		echo 'В каталогах магазинов на нашем сайте пока нет ни одного отдела, в котором бы продавались товары или оказывались услуги, описанные в данной статье.<br> Загляните к нам позже!';	
		

	echo '</div>'; // end of echo '<div class="ajaxed">';
	
	return;
	
}

// конец формирования списка магазинов и отделов, где продаются товары, указанные в данной статье данных для выдачи ajax-функции











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

//zp_functions_show_DrupalStatusMessages();


echo '<div class="i_body">';


	foreach ($node->taxonomy as $term)
		{
			if($term->vid == 6) // 6 - словарь с ключевыми словами по данной статье
                        {
				$keywords_array[] = $term->name;
                                $keywords .= $term->name . ', ';
                        }
			else
				if($term->vid == 7) // 7 - словарь с темами статей (с типами товаров и услуг)
				{
					if(!$issue_type)
						$issue_type = $term->name;
					else
						$issue_type .=  ', ' . $term->name;
				}
		}
		

	echo '<div class="title2">' . l('Статьи и описания / Товары и услуги', MY_ISSUESLIST_NODE) . ' //<br>' . $issue_type . ':</div>';
	
	echo '<div class="title">' . $title . '</div>';

	$link = zp_functions_continue_shopping_link();
  	echo '<div class="links">' . l('Перейти к списку всех статей', MY_ISSUESLIST_NODE, array('class' => 'links', 'title' => 'Пожалуйста, ознакомьтесь также и с другими статьями о товарах и услугах!')) . '</div>'; 
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьёй Вы можете вернуться к покупкам...')) . '</div>'; 
  	
  
  
  
  
  
  
	// Social links
  //	echo zp_functions_get_social_links();
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


        $query = "SELECT n.nid, n.field_prodtype_pic_src_n_num_value, e.field_zp_art_shop_value, w.field_zp_bar_world_value, x.field_zp_art_postav_value, f.field_postav_value, v.field_zp_art_proizv_value, z.field_proizv_value, t.tid, h.parent AS parenttid, p.nid AS parentnid, o.title AS parenttitle
                    FROM {content_field_prodtype_pic_src_n_num} n
                    LEFT JOIN {content_field_proizv} z ON z.nid = n.nid                            
                    LEFT JOIN {content_field_zp_art_proizv} v ON v.nid = n.nid
                    LEFT JOIN {content_field_postav} f ON f.nid = n.nid
                    LEFT JOIN {content_field_zp_art_postav} x ON x.nid = n.nid
                    LEFT JOIN {content_field_zp_bar_world} w ON w.nid = n.nid
                    LEFT JOIN {content_field_zp_art_shop} e ON e.nid = n.nid

                    LEFT JOIN {term_node} t ON t.nid = n.nid
                    LEFT JOIN {term_hierarchy} h ON h.tid = t.tid
                    LEFT JOIN {term_node} p ON p.tid = h.parent
                    LEFT JOIN {node} o ON o.nid = p.nid

                    WHERE n.field_prodtype_pic_src_n_num_value LIKE '%%" . $keywords_array[0] . "%'" . ($keywords_array[1]?" OR n.field_prodtype_pic_src_n_num_value LIKE '%%" . $keywords_array[1] . "%'":"") . ($keywords_array[2]?" OR n.field_prodtype_pic_src_n_num_value LIKE '%%" . $keywords_array[2] . "%'":"")
                   ."ORDER BY RAND()
                    LIMIT 40    
                    ";

        $results = db_query($query);

        for($count = 1; $result = db_fetch_array($results); $count++)
        {
            if($image = zp_functions_get_product_teaser_picture_path($result['field_prodtype_pic_src_n_num_value'], $result['field_proizv_value'], $result['field_zp_art_proizv_value'], $result['field_postav_value'], $result['field_zp_art_postav_value'], $model, $result['field_zp_art_shop_value'], $result['field_zp_bar_world_value'], 'top-list', $result['parenttitle'], false))
            {
                $top_list[] = '<image src="' . $image . '" alt="' . $result['parenttitle'] . ' ' . $count . ". Статья, советы, рекомендации. Служба доставки 'За покупками'" . '" title="' . $result['parenttitle'] . '" />';
                $image_src_for_meta = '/files/imagecache/product_body_type1' . $image;
            }

        }

        if(is_array($top_list))
        {
            shuffle($top_list);

            echo '<div class="issue_pics">';
                echo '<div class="left">';
                for($count = 0; $count <=2; $count++)
                {
                   if($count == 0)
                   {
                        $qr = zp_functions_get_qr('issue' . $node->nid, 'http://www.zapokupkami.com' . $_SERVER['REQUEST_URI']);
                        echo '<div class="ip_qr" style="overflow:hidden; width: 150px"><img alt="' . $title .' qr-код" title="QR-код для статьи ' . $title . ". Служба доставки 'За покупками'" . '" src="/' . $qr . '" width="180px" style="position:relative; left:-14px; top:-10px"></div>';
                   }
                   else
                        echo '<div class="ip_' . ($count+1) . '">' . $top_list[$count] . '</div>';


                   if($count == 1)
                       echo '</div>';
                }
            echo '</div>';
        }
        else
            $image_src_for_meta = "/sites/all/themes/zp-themes/zp-two/img4/zaPokupkami.com.jpg";

        // set meta tag for search and social
        drupal_set_html_head('<link rel="image_src" href="' . $image_src_for_meta. '" />');
        
 	echo '<div class="body">' . $node->content['body']['#value'] . '</div>'; 
 	
 	/*
 	Выводим автора и/или источник статьи,
 	которые хранятся в поле field_iss_auth_n_src
 	в формате <источник***опция***автор>, где опция может быть: 
	0 - не показывать ни автора, ни источник
	1 - показывать и то, и другое
	2 - показывать только источник
	3 - показывать только автора
	
	Если нужно указать только источник, формат может быть упрощён до просто источника, без взяких опция и ***
	
 	*/
 	

 	if($node->field_iss_auth_n_src[0]['value']) // если в поле с автором и источником хоть что-то есть
 	{
 		$auth_src = explode('***', $node->field_iss_auth_n_src[0]['value']);

 		if(isset($auth_src[1])) // если опция определена (она может быть и нулём)
 		{

 			switch($auth_src[1])
 			{
 				case 1:
 					if($auth_src[2])
 					echo  '<div class="auth_src">' . $auth_src[2] . '</div>';

 				case 2:
 					if($auth_src[0])
 					echo  '<div class="auth_src">Источник: ' . $auth_src[0] . '</div>';
 					break;

 				case 3:
 					if($auth_src[2])
 					echo  '<div class="auth_src">' . $auth_src[2] . '</div>';
 					break;

 			}
 		}
 		else // если поле не пустое и опция не определена, значит считаем, что в поле просто указан источник, который надо показать
                    echo  '<div class="auth_src">Источник: ' . $node->field_iss_auth_n_src[0]['value'] . '</div>';


 	}

	
	// выведем через ajax список остальных статей из этого же раздела
	echo '<div class="others_in_topic">';
	echo '<fieldset class="collapsible collapsed other_issues_title"><legend><a href="#" id="issues_button">Другие статьи по этой теме</a></legend>';

	
	echo '<div id="issues">';
	
		if(1)
		{
			echo '<div class="ajaxed">';
				zp_functions_show_other_issues_list($node->nid, $node->taxonomy);
			echo '</div>';
		}
		else
                {
			// вместо того, чтобы сразу генерировать список соответствующих материалов, можно откложить генерацию списка до нажатия на вкладку со статьями
			// список выдаём по запросу ajax-функции (вначале этого файла)
                    
                        drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_issues_getRelatedIssues.js');
                }
                
	echo '</div>'; // end of div issues
     			
    echo '</fieldset>';			
    echo '</div>';
     			

    
    // выведем через ajax список отделов магазинов, которые имеют названия каталогов с картинками такие же, как ключевые слова у этой стать
    // позже нужно будет сделать, чтобы выводились также отделы, у которых ключевые слова совпадают с ключевыми словами этой статьи
    echo '<div class="where_buy others_in_topic">';
        echo '<fieldset class="collapsible collapsed where_buy_title"><legend><a href="#" id="where_to_buy_button">Где купить или заказать доставку на дом?</a></legend>';
            echo '<div class="where_buy_hint">Отделы магазинов / заведений на нашем сайте, в которых можно купить товар или заказать услугу, описанные в статье (или имеющие какое-либо отношение к теме статьи) на этой странице.</div>';

            echo '<div id="shops_otdels">';

                // вместо того, чтобы сразу генерировать список соответствующих материалов, откладываем генерацию списка до нажатия на вкладку со статьями
                // список выдаём по запросу ajax-функции (вначале этого файла)
                drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_issues_WhereToBuy.js');

            echo '</div>'; // end of div issues

        echo '</fieldset>';			
    echo '</div>';
    
	
    echo '<div class="links">' . l('Перейти к списку всех статей', MY_ISSUESLIST_NODE, array('class' => 'links', 'title' => 'Пожалуйста, ознакомьтесь также и с другими статьями о товарах и услугах!')) . '</div>'; 	
    echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьёй Вы можете вернуться к покупкам...')) . '</div>'; 
 	

echo '</div>';
	
endif; // end of body 

?>
