<!-- <div class="test">zp shop_descr catalog via tpl</div> -->

<?php 
if ($teaser == 1): 

// ---------------------------------------------------------------------------------------------------------

  return; // ничего не выводим, если тизер
endif; // end of teaser 










if ($page == 1): 

// ---------------------------------------------------------------------------------------------------------

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


echo '<div class="c_mc">';

//echo '<div class="title">' . $title . '</div>';
echo '<div class="title2">Описания и контакты:</div><div class="title">Магазины и заведения</div>';


$link = zp_functions_continue_shopping_link();
echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 
  	
//echo '<div class="body">' . $node->body . '</div>'; 
echo '<div class="body">' . $node->content['body']['#value'] . '</div>'; 
 

/*	
$view = views_get_view('zp_issues_all');
if($view)
{
	$limit = 0;
	echo views_build_view('embed', $view, array(), FALSE, $limit);
}
*/

// выясним тид корневого терма для содержания статей о товарах и услугах
$vid = 7; // словарь с содержанием
$tids_search = taxonomy_get_term_by_name('Магазины / заведения');
$typed_term_tid = NULL; // tid match, if any.
foreach ($tids_search as $tid_search) 
	{
    	if ($tid_search->vid == $vid)
          	$current_tid = $tid_search->tid;
	}
          

drupal_add_js('misc/collapse.js'); // схлопывающиеся филдсеты	

//$limit = 1;

if($_GET['sort'] == 'alfa')
{
	
	//echo 'Сортировка:' . l(' по дате побликации', url('node/' . arg(1)) . '?sort=date') . '; ' . l('По типу товаров', url('node/' . arg(1)) . '?sort=type') . '<br>';
	echo '<div class="sort_link">Сортировка:  <a class="active" href="' . url('node/' . arg(1)) . '?sort=alfa">по алфавиту и городам</a>' . ' / ' . '<a href="' . url('node/' . arg(1)) . '?sort=type">по городам и районам</a></div>';
	
	cache_clear_all('*', 'cache_views', true);
	$view = views_get_view('shop_descr_all');
	
	//zp_functions_show($view);
	
	if($view)
	{
		//$limit = 1;
		//echo views_build_view('embed', $view, array(), FALSE, $limit);
		echo views_build_view('embed', $view, array(), $view->use_pager, $view->nodes_per_page);
	}
	
	
}
else
if(!$_GET['sort'] OR $_GET['sort'] == 'type')
{
	//echo get_issues_content($current_tid = NULL, $parent_tid = NULL, $vid = NULL, $step = 0);
	
	//echo 'Сортировка:' . l(' по дате побликации', url('node/' . arg(1)) . '?sort=date') . '; ' . l('По типу товаров', url('node/' . arg(1)) . '?sort=type') . '<br>';
	echo '<div class="sort_link">Сортировка:  <a href="' . url('node/' . arg(1)) . '?sort=alfa">по алфавиту и городам</a>' . ' / ' . '<a class="active" href="' . url('node/' . arg(1)) . '?sort=type">по городам и районам</a></div>';
	echo zp_functions_get_issues_content('mc_descr_all', $current_tid, NULL, $vid, NULL);
}


echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления с материалами Вы можете вернуться к покупкам...')) . '</div>'; 

echo '</div>';


endif; // end of body















?>

