<?php 



// paths for my cart and order nodes
//MY_CART_NODE, MY_CART_CHECKOUT_NODE, MY_CART_REVIEW_NODE, MY_CART_COMPLETE_NODE
//MY_ORDER_HISTORY_NODE, MY_ORDER_REVIEW_NODE

require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');

//zp_functions_show_DrupalStatusMessages();
 
echo '<div class="title"><h1>Доставка продуктов <br />и других товаров к Вам <br />на дом и в офис</h1></div>';

// Social links
//echo zp_functions_get_social_links();


// jcarousel
echo '
<div id="mycarousel" class="jcarousel-skin-tango">
  <ul class="jcarousel-list jcarousel-list-horizontal">
';
 
 	 for($i=0; $node->field_zp_reklama[$i]['view']; $i++) 
		    	echo '<li class="jcarousel-item-' . ($i+1) . '"><img src="' . base_path() . $node->field_zp_reklama[$i]['filepath'] . '" alt="' . $node->field_zp_reklama[$i]['alt'] . '" /></li>';
 echo '
 </ul>
 <div class="jcarousel-control">
 ';
 
 	 for($j=1; $j<=$i; $j++) 
      echo '<a href="#">' . $j . '</a>';

echo '      
 </div>
</div>
';



// Тизер / краткое описание службы доставки для первой страницы
// текст содержится в ноде с заголовком "Анонс службы доставки для домашней страницы"

 //if($anons = db_result(db_query("SELECT body from {node_revisions} WHERE title = '%s'", 'Анонс службы доставки для домашней страницы')))
 //	echo '<div class="zp_anons">' . $anons . '</div>';

 
echo  '<div class="zp_anons">' . $node->content['body']['#value'] . '</div>';



//
//global $user;
//
//
//
//     //$query = "SELECT e.field_zp_art_shop_value, w.field_zp_bar_world_value, x.field_zp_art_postav_value, f.field_postav_value, v.field_zp_art_proizv_value, z.field_proizv_value, i.field_prodtype_pic_src_n_num_value, n.title, n.nid, s.viewcount, u.model, u.sell_price, t.tid, h.parent AS parenttid, p.nid AS parentnid, o.title AS parenttitle
//     $query = "SELECT w.field_zp_bar_world_value, i.field_prodtype_pic_src_n_num_value, n.title, n.nid, s.viewcount, u.model, u.sell_price, t.tid, h.parent AS parenttid, p.nid AS parentnid, o.title AS parenttitle
//                FROM {node} n
//                INNER JOIN {node_extended_stats_summary} s ON s.nid = n.nid
//                INNER JOIN {uc_products} u ON u.nid = n.nid
//
//                LEFT JOIN {content_field_prodtype_pic_src_n_num} i ON i.nid = n.nid "
//                /*
//                ."LEFT JOIN {content_field_proizv} z ON z.nid = n.nid
//                LEFT JOIN {content_field_zp_art_proizv} v ON v.nid = n.nid
//                LEFT JOIN {content_field_postav} f ON f.nid = n.nid
//                LEFT JOIN {content_field_zp_art_postav} x ON x.nid = n.nid
//                LEFT JOIN {content_field_zp_art_shop} e ON e.nid = n.nid"                
//                */
//                ."LEFT JOIN {content_field_zp_bar_world} w ON w.nid = n.nid
//                
//
//                INNER JOIN {term_node} t ON t.nid = n.nid
//                INNER JOIN {term_hierarchy} h ON h.tid = t.tid
//                INNER JOIN {term_node} p ON p.tid = h.parent
//                INNER JOIN {node} o ON o.nid = p.nid
//                WHERE n.type = 'product_set_1' AND s.period = '259200'
//                ORDER BY s.viewcount DESC
//                LIMIT 20";
//
//    // time periods:  43200, 259200, 604800
//    $results = db_query($query);
//
//    while($result = db_fetch_array($results))
//    {
//        if($image = zp_functions_get_product_teaser_picture_path($result['field_prodtype_pic_src_n_num_value'], $result['field_proizv_value'], $result['field_zp_art_proizv_value'], $result['field_postav_value'], $result['field_zp_art_postav_value'], $model, $result['field_zp_art_shop_value'], $result['field_zp_bar_world_value'], 'top-list'))
//        {
//            $out = '<div class="teaser">';
//                $out .= '<a href="' . url('node/' . $result['parentnid']) . '"><div class="image">' . $image . '</div></a>';
//                $out .= '<a href="' . url('node/' . $result['parentnid']) . '"><div class="title dept">' . $result['parenttitle'] . '</div></a>';
//                $out .='<div class="title prod">' . $result['title'] . '<div title="Цена без стоимости доставки" class="price">Цена: ' . uc_currency_format($result['sell_price']) . '</div></div>';
//            $out .= '</div>';
//            $top_list[] = $out;
//        }
//
//    } // end of  while($result = db_fetch_array($results))
//
//    if($top_list)
//    {
//        echo '<div class="top-list">';
//            echo '<div class="caption"></div>';
//            shuffle($top_list);
//            for($count = 0; $count <=2; $count++)
//                echo $top_list[$count];
//        echo '</div>';
//    }


echo  '<div class="zp_anons">' . $node->field_body_2[0]['value'] . '</div>';






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



<?php








if(!$user->uid)
	echo '<div class="become_client home"><a href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto"><strong>Стать клиентом</strong> службы доставки <strong>"За Покупками!"</strong> очень просто!</a></div><br>';




// uncomment to show the last blog posts and news
/*
$view = views_get_view('blog_zp_view');
if($view)
{
	echo '<div class="blog home">';
	echo '<div class="title">' . 'Блог <span class="title2">/последние записи' . '</span></div>';
	$limit = 3;
	echo views_build_view('embed', $view, array(), FALSE, $limit);
	
	echo '<div class="read_more">' . l(t('Read more'), MY_ZP_BLOG_NODE, array('class' => 'blog_link')) . '</div>';
	echo '</div>';
}


$view = views_get_view('news_view');
if($view)
{
	echo '<div class="news home">';
	echo '<div class="title">Новости <span class="title2">/последние</span></div>';
	$limit = 2;
	echo views_build_view('embed', $view, array(), FALSE, $limit);

	//print '<div class="read_more">' . l(t('Read more'), 'node/238', array('class' => 'blog_link')) . '</div>';
	echo '<div class="read_more">' . l(t('Read more'), MY_ZP_NEWS_NODE, array('class' => 'blog_link')) . '</div>';
	echo '</div>';
}
*/


// jcarousel
jcarousel_add(); 
drupal_add_js('sites/all/modules/_ZP_modules/_JS/zp_carousel.js'); 

// js menu with delay - very good!!!
//drupal_add_js('sites/all/modules/_Menu/superfish/js/hoverIntent.js'); 

