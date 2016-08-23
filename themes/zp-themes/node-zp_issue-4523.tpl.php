<?php 

if ($teaser == 1)
    return; // ничего не выводим, если тизер




if($page == 1)
{    
        //zp_functions_show_DrupalStatusMessages();

        echo '<div class="issues">';
        echo '<div class="title">Товары и услуги <span class="title2">/ Cтатьи, описания</span></div>';

        $link = zp_functions_continue_shopping_link();
        echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьями Вы можете вернуться к покупкам...')) . '</div>'; 

        // Social links
        echo zp_functions_get_social_links();

        $qr = zp_functions_get_qr('issues-list', 'http://www.zapokupkami.com' . $_SERVER['REQUEST_URI']);
        echo '<div style="overflow:hidden; width: 150px; position: relative; top:-100px; left: 250px; margin-bottom: -100px;" class="qr"><img alt="QR-код для списка тематических статей" title="QR-код для списка тематических статей" src="/' . $qr . '" width="180px" style="position:relative; left:-14px; top:-10px"></div>';

        global $user;
        if($user->uid == 1)
        {
                echo '<a class="add_node" href="' . base_path() . 'node/add/' . $node->type . '" title="Добавить новую статью">';
                        echo '> Добавить новую статью';
                echo '</a>';
        }

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
        $tids_search = taxonomy_get_term_by_name('Товары / услуги');
        $typed_term_tid = NULL; // tid match, if any.
        foreach ($tids_search as $tid_search) 
                {
                if ($tid_search->vid == $vid)
                        $current_tid = $tid_search->tid;
                }


        drupal_add_js('misc/collapse.js'); // схлопывающиеся филдсеты	

        //$limit = 1;

        if($_GET['sort'] == 'date')
        {
                echo '<div class="sort_link">Сортировка:  <a class="active" href="' . url('node/' . arg(1)) . '?sort=date">по дате публикации</a>' . ' / ' . '<a href="' . url('node/' . arg(1)) . '?sort=type">по типу товаров</a></div>';

                cache_clear_all('*', 'cache_views', true);
                $view = views_get_view('zp_issues_all');

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
                echo '<div class="sort_link">Сортировка:  <a href="' . url('node/' . arg(1)) . '?sort=date">по дате публикации</a>' . ' / ' . '<a class="active" href="' . url('node/' . arg(1)) . '?sort=type">по типу товаров</a></div>';
                echo zp_functions_get_issues_content('zp_issues_all', $current_tid, NULL, $vid, NULL);
        }

        echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links', 'title' => 'После ознакомления со статьями Вы можете вернуться к покупкам...')) . '</div>'; 
        echo '</div>';



} // end of body

