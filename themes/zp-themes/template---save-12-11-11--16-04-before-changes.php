<?php
// $Id: template.php,v 1.1.2.9 2008/03/09 18:39:08 derjochenmeyer Exp $




// paths for my cart and order nodes

//MY_CART_NODE, MY_CART_CHECKOUT_NODE, MY_CART_REVIEW_NODE, MY_CART_COMPLETE_NODE
//MY_ORDER_HISTORY_NODE, MY_ORDER_REVIEW_NODE


require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');


















/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function phptemplate_body_class($sidebar_left, $sidebar_right) {
  if ($sidebar_left != '' && $sidebar_right != '') {
    $class = 'sidebars';
  }
  else {
    if ($sidebar_left != '') {
      $class = 'sidebar-left';
    }
    if ($sidebar_right != '') {
      $class = 'sidebar-right';
    }
  }

  if (isset($class)) {
    print ' class="'. $class .'"';
  }
}


function phptemplate_adminwidget($scripts) {

  if (empty($scripts)) { 
    print '
    <script type="text/javascript" src="'.base_path().'misc/jquery.js"></script>';
  }
    
  print '
    <script type="text/javascript">
    function toggle_style(color) {
      $("#header-image").css("background-color", color);
      $("#header-image").css("background-image", "none");
      $("h1").css("color", color);
      $("h2").css("color", color);
      $("h3").css("color", color);
      $("#headline a").css("color", color);
    }
    </script>
  
    <div id="farben">
      <span>try another color: </span>
      <a href="#" style="background-color:#FF9900;" onclick="toggle_style(\'#FF9900\');"></a>
      <a href="#" style="background-color:#003366;" onclick="toggle_style(\'#003366\');"></a>
      <a href="#" style="background-color:#990000;" onclick="toggle_style(\'#990000\');"></a>
      <a href="#" style="background-color:#CCCCCC;" onclick="toggle_style(\'#CCCCCC\');"></a>
      <a href="#" style="background-color:#006699;" onclick="toggle_style(\'#006699\');"></a>
      <a href="#" style="background-color:#000000;" onclick="toggle_style(\'#000000\');"></a>
    </div>

    <div id="font">
      <span style="margin-left:20px;">try another fontsize: </span>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'60%\');">60%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'70%\');">70%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'80%\');">80%</a>
      <a href="#" onclick="$(\'body\').css(\'font-size\',\'90%\');">90%</a>
    </div>
  ';
  
  if (arg(0) == 'admin' && arg(1) == 'build' && arg(2) == 'themes') { 
    print '<img src="http://www.kletterfotos.de/autor.php">';
  }

}


/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' &rsaquo;', $breadcrumb) .'</div>';
  }
}


/**
* Allow themable wrapping of all comments.
*/
function phptemplate_comment_wrapper($content, $type = null) {
    return '<div id="comments">'. $content . '</div>';
}


/**
 * Override or insert PHPTemplate variables into the templates.
 */
function _phptemplate_variables($hook, $vars) {
	
  global $user;

  
  

  if ($hook == 'page') {
      
    if ($secondary = menu_secondary_local_tasks()) {
      $output = '<span class="clear"></span>';
      $output .= "<ul class=\"tabs secondary\">\n". $secondary ."</ul>\n";
      $vars['tabs2'] = $output;
    }

     // необходимо для работы модуля названия страниц page_title (без этих строк модуль не работает)
    if (module_exists('page_title')) {
      $vars['head_title'] = page_title_page_get_title();
    }  
    
    //$vars['head_title'] = 'dfsdfsdf';
    
    return $vars;
    
  }

  
  
if ($hook == 'node') 
{
 if ($vars['page']) 
        {
            
            switch($vars['node']->type)
            {
                case 'product_set_1':
                case 'product_set_2':
                case 'c_department':
                case 'c_shop':
                case 'zp_issue':
                    // image_scr tag for search and social were set in templates
                    break;
                    
                default:
                    // set meta tag for search and social for other types
                    drupal_set_html_head('<link rel="image_src" href="/sites/all/themes/zp-themes/zp-two/img4/zaPokupkami.com.jpg" />'); 
                    break;
            }
             
            //drupal_set_html_head('<meta name="title" content="' . str_replace(array('"', 'amp;'), array("'", '&'), $vars['node']->title) . '" />');

            
     
            drupal_set_html_head('<meta name="author" content="Служба доставки ' . "'За покупками'" . '" />');


            // This is LIFO (Last In First Out) so put them in reverse order, i.e
            // most important last.
            $vars['template_files'] = array('node-page', 'node-'. $vars['node']->type .'-page', 'node-'. $vars['node']->nid .'-page', 'node-'. $vars['node']->type . '-' . $vars['node']->nid);

            // не сработало
            //$vars['template_files'] = array('node-page', 'node-'. $vars['node']->type .'-page', 'node-'. $vars['node']->nid .'-page', 'node-'. $vars['node']->type . '-' . $vars['node']->nid, 'node-'. $vars['node']->type . '-' . $vars['node']->nid . '-edit');

            //drupal_set_message("node page", 'error');
      }
      else 
      {
        //$vars['template_files'] = array('node-'. $vars['node']->nid);
        
        $vars['template_files'] = array('node-'. $vars['node']->type, 'node-'. $vars['node']->nid, 'node-'. $vars['node']->type . '-' . $vars['node']->nid);
        
        // не сработало
        //$vars['template_files'] = array('node-'. $vars['node']->type, 'node-'. $vars['node']->nid, 'node-'. $vars['node']->type . '-' . $vars['node']->nid, 'node-'. $vars['node']->type . '-' . $vars['node']->nid . '-edit');
        

        //drupal_set_message("node node", 'error');
      }



/*

   print '<pre>';
   print htmlspecialchars(print_r(get_defined_vars(), TRUE), ENT_QUOTES);
   print '</pre>';

   $node_title = $vars['node']->title;  
   $node_nid = $vars['node']->nid;  
   $node_type = $vars['node']->type;  
   drupal_set_message("Rendering node: node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');

*/


// formiruem peremennye dlia verhney shpki s menu
//if($vars['node']->type == 'zp_header' AND $vars['node']->title == 'Zp header 01')
//if($vars['node']->type == 'zp_footer' OR ($vars['node']->type == 'zp_header' AND $vars['node']->title == 'Zp header 01'))
if($vars['node']->type == 'zp_footer')
 {
	
 	//global $user;

	//if(arg(0) == 'node' AND arg(1) == 227) // если первая страница
	if(arg(0) == 'node' AND arg(1) == MY_HOME_PAGE_NODE_NUM) // если первая страница
	{
		$vars['welcome'] = '<div id="welcome"><div class="caption">' . 'Ваша Персональная Служба Доставки' . '</div>' . 'продуктов и других товаров или услуг 
из известных Вам магазинов и прочих заведений  к Вам домой или в офис.' . '</div>';
	
		/*
		$vars['explain'] = '<div id="explain"><div class="caption">' . 'Как работает сервис?' . '</div>' . '1.Станьте нашим клиентом. 2.Авторизуйтесь на сайте. 3.Перейдите в каталог одного из доступных Вам для покупок магазинов. 4.В выбранном магазине добавьте необходимые товары 
в корзину. 4.Сделайте заказ и оплатите доставленный Вам товар.' . '</div>';
		*/
		
		$vars['explain'] = '<div id="explain"><div class="caption">' . ' <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa">Делать покупки с нами удобнее!</a>' . '</div>' . 'У нас Вы можете <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#my-vybiraem-luchshee">регулярно заказывать</a> товары <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny">из магазинов в вашем районе</a>, быстро <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#servisy-saita">находить полезную информацию</a> по товарам, услугам, производителям и магазинам из наших каталогов, повторно <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#istoriya-zakazov">использовать свои списки заказов</a>... и  <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa">многое другое!</a>' . '</div>';
	
	}
 	
 	// menu s stranami i gorodami cities_menu, тут инициализируем меню для second_menu, а позже вычислим меню для отдельного меню городов
 	
   if(!($vars['cities_menu_second'] = $_SESSION['cities_menu_second'])) //OR $_SESSION['masquarade'])
   { 
   	if($items = nice_tax_menu_build_items(1, 0, 2, 0, 0, -1))
     {

        $vars['cities_menu_second'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
        $_SESSION['cities_menu_second'] = $vars['cities_menu_second'];
     }   
   }         
       
       
       
       
       
       
       
       
//--------------------------------------------------------------------


 	// glavnoe menu first_menu 
 	if(!($vars['first_menu'] = $_SESSION['first_menu']))
 	{
     if($items = nice_tax_menu_build_items(4, 0, 0, 0))
     {
   
       // так как для первой страницы мы не можем просто назначить пункт меню, изменим данные для этого пункта в меню
       // для этого определим тид пункта "На главную", так как по этому тиду (как ключу в массиве) и можно обратиться в массив с пунктами меню

       $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'На главную'))] = array(
             'data' => l('На главную', 'http://www.zapokupkami.com/', array('title' => 'Перейти на главную страницу сайта')),
             'children' => array(),
             );
  

       // зададим ссылку для пункта меню Возврат / обмен, так как просто присвоить её нельзя, ибо это ссылка на главу в другой странице
       $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Инфо'))]['children'][db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Возврат / обмен'))] = array(
             'data' => l('Возврат / обмен', 'http://' . $_SERVER['HTTP_HOST'] . '/help/pravila-i-osobennosti-raboty-nashego-servisa#vozvrat-i-obmen', array('title' => 'Правила возврата и обмена доставленных товаров')),
             'children' => array(),
             );
        
       // зададим ссылку для пункта меню Возврат / обмен, так как просто присвоить её нельзя, ибо это ссылка на главу в другой странице
       $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Инфо'))]['children'][db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Конфиденциальность'))] = array(
             'data' => l('Конфиденциальность', 'http://' . $_SERVER['HTTP_HOST'] . '/help/pravila-i-osobennosti-raboty-nashego-servisa#konfidencialnost', array('title' => 'Вопросы конфиденциальности и безопасности')),
             'children' => array(),
             );

	   // зададим ссылку для пункта меню Возврат / обмен, так как просто присвоить её нельзя, ибо это ссылка на главу в другой странице
       $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Помощь'))] = array(
             'data' => l('Помощь', 'http://' . $_SERVER['HTTP_HOST'] . '/help/pravila-i-osobennosti-raboty-sluzhby-dostavki-za-pokupkami', array('title' => 'Правила и особенности работы нашего сервиса')),
             'children' => array(),
             );             
       
             /* 
           // зададим ссылку для пункта меню Жалобы и предложения
           $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 2 AND name = '%s'", 'Жалобы и предложения'))]['data'] = l('Жалобы и предложения', 'node/'.db_result(db_query("SELECT nid from {node} WHERE title = '%s'", 'u'. $user->uid . '-pc')));

           */
           
           /*
            // терм для меню Любимые магазины
                $tid_favorite_shops = db_result(db_query("SELECT tid from {term_data} WHERE vid = 2 AND name = '%s'", 'Любимые магазины'));
                // нода, прицепленная к этому терму
                $nid_favorite_shops = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $tid_favorite_shops));
                $items[$tid_favorite_shops] = array(
                  'data' => l('Любимые магазины', 'node/'.$nid_favorite_shops, array('title' => 'Магазины, доступные Вам для покупок через наш сервис')),
                  'children' => $items_user_shops,
                 );
                 */
                 
           
           
       // ВРЕМЕННО уберём пункт меню "Блог"     
       unset($items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 4 AND name = '%s'", 'Блог'))]);  
             
             
             
       $vars['first_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'first_menu'), 'down');
       $_SESSION['first_menu'] = $vars['first_menu'];
     } 
 	}
       
       
       
       
       
       
       
       
       
       
       
       
       
       
// ------------------------------------------------------------------------------------------------       
       
       
    
    // make user_menu variable
    

    //$masquarade = $_SESSION['masquarade']; // флаг, использовался ли модуль маскарад в сессии
    //$cur_user = $user->uid;
    //drupal_set_message("cur user id = $cur_user, masquarade = $masquarade", 'error');

    if($user->uid) // if user is logged in
      {
       if(!$_SESSION['masquarade'])
              {
                //drupal_set_message("0", 'error');
              	//if(db_result(db_query("SELECT uid_as FROM {masquerade} WHERE sid = '%s' AND uid_from = %d ", session_id(), $user->uid)))
                if(db_result(db_query("SELECT uid_from FROM {masquerade} WHERE uid_as = %d ", $user->uid)))
                 { 
              	  $_SESSION['masquarade'] = 1;
              	  //drupal_set_message("1", 'error');
                 }
              }  
      	
      	if(!($vars['user_menu'] = $_SESSION['user_menu']) OR $_SESSION['masquarade']) // если меню пользователя уже сформировано в этой сессии, загрузить его, а не формировать заново
        {	
      	//drupal_set_message("-1", 'error'); 
      	//if(!$masquarade) $masquarade = 0;
      	//drupal_set_message("masquarade = $masquarade", 'error');
        if($items = nice_tax_menu_build_items(2, 0, 0, 0))
          {
           
           // затем сформируем для пунктов Личные данные и Жалобы и предложения подпукты и ссылку соответственно
           // для текущего пользователя
           // затем найдём номера tid для этих пунктов и по этим номерам исправим ссылки в сФормированном меню $items
           $items_personal = array();
          
           
           
           // страница с настройками доступа пользователя - почта, пароль и т.д.
           
           $items_personal['access'] = array(
         		//'data' => l('Настройки доступа', 'node/73'),
         		'data' => l('Настройки доступа', MY_USER_SETTINGS_NODE),
         		
                //'children' => nice_tax_menu_add_kids($term->tid, $vid, $mydepth),
                'children' => array(),
                );
                
                
                
           // страница с открытывми собственными данными пользователя, которые он может сам заполнять (интересы, возраст и пр.)
           /* пока не даём клиентам заводить такие страницы
           $items_personal['open_info'] = array(
         		'data' => l('Открытые данные', 'node/'.db_result(db_query("SELECT nid from {node} WHERE title = '%s'", 'u'. $user->uid . '-info'))),
                //'children' => nice_tax_menu_add_kids($term->tid, $vid, $mydepth),
                'children' => array(),
         		);
         	*/
         		
           
           $items_personal['prefer'] = array(
         		'data' => l('Мои предпочтения', 'node/'.db_result(db_query("SELECT nid from {node} WHERE title = '%s'", 'u'. $user->uid . '-pp'))),
                //'children' => nice_tax_menu_add_kids($term->tid, $vid, $mydepth),
                'children' => array(),
         		);
           
         		
           // сформируем меню Любимые магазины (если выполняются эти строки, значит они выполняются первый и единственный раз за сессию)
           // а значит и меню с любимыми магазинами будет формироваться первый и единственный за сессию раз
    
           // данные находятся на ноде со скрытыми данными пользователя, на которых указаны любимые магазины
             
           if($results = db_query("SELECT r_id, r_text, description from {node_field_multireference_data} WHERE nid = %d", db_result(db_query("SELECT nid from {node} WHERE title = '%s'", 'u'. $user->uid . '-hi')))) 
            {
                //$result = db_query("SELECT pcid, name FROM {uc_product_classes}");
                while($result = db_fetch_object($results))
                {
                  //$classes[$c->pcid] = $c->name;
                  
                     
                  $shop_tid = db_result(db_query("SELECT tid from {term_data} WHERE vid = 1 AND name = '%s'", $result->r_text));
                  
                  
                  // теперь эта переменная вычисляется отдельно в функции zp_functions_get_user_shops_data(), ещё до формирования пользовательского меню
	              // так как польз. меню было перекинуто в футер и рассчитывается в самом конце формирования страницы, а данные нужны раньше
	              /*
                  $user_shops_data[$shop_tid] = array(
                    't_name' => $result->r_text,
                    //'tid' => $shop_tid, // tid выносим в ключ массива ($user_shops_data[$shop_tid])
                    'nid' => $result->r_id,
                    'price_factor' => $result->description,
                    );   
                  */
                    
                    
                  $count = 0;
                  $linage_tids = array($shop_tid); 
                  while(($shop_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $shop_tid))) != 0)
                   {
                     $linage_tids[] = $shop_tid;
                     $count++;
                   } 

                  $current_name = $result->r_text;
                  
                  if($count >= 4) // отдел
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    $shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    $rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    $city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $city_name . '->' . $rajon_name . '->' . $shop_name . '->' . $current_name . ' (только этот отдел)';
                  }
                 /*
                  if($count == 4) // отдел
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    $shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    $rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    $city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $city_name . '->' . $rajon_name . '->' . $shop_name . '->' . $current_name;
                  }
                  */
                  if($count == 3)  // магазин
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    //$shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    $rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    $city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $city_name . '->' . $rajon_name . '->' . $current_name;
                  }
                  
                  if($count == 2) // район
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    //$shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    //$rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    $city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $city_name . '->' . $current_name . ' (все магазины района)';
                  }
                  
                  if($count == 1) // город
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    //$shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    //$rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    //$city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $current_name . ' (все магазины города)';
                  }
                  
                  
                  if($count == 0) // страна
                  {
                    //$shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин, магазин стоит 4-м по списку (в этом массива - с конца)
                    //$shop_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-3]));
                    //$rajon_tid = $linage_tids[$count-3]; // Третьим элементом в этом варианте становится район
                    //$rajon_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-2]));
                    //$city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом с конца (после страны)
                    //$city_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_tids[$count-1]));
                    
                    $hint = $current_name . ' (все магазины страны)';
                  }
                  
                  
                  
                  
                  //drupal_set_message("current_name = $current_name, shop_name = $shop_name, rajon_name = $rajon_name, city_name = $city_name", 'error');
                  //drupal_set_message("hint = $hint", 'error');
 
                    
                  $items_user_shops[$result->r_id . '-s'] = array(
                     'data' => l($result->r_text, 'node/'. $result->r_id, array('title' => $hint)), 
                     'children' => array());  
                    
                    
                    
                }
                
                // теперь эта переменная вычисляется отдельно в функции zp_functions_get_user_shops_data(), ещё до формирования пользовательского меню
                // так как польз. меню было перекинуто в футер и рассчитывается в самом конце формирования страницы, а данные нужны раньше
                //$_SESSION['user_shops_data'] = $user_shops_data;

 /*               
   foreach($user_shops_data as $key => $value)
    drupal_set_message("key = $key, value = $value", 'error');
 */
 
 //$q_get = $_GET['q'];
 //drupal_set_message("q_get = $q_get ", 'error');
                
                // терм для меню Любымые магазины
                $tid_favorite_shops = db_result(db_query("SELECT tid from {term_data} WHERE vid = 2 AND name = '%s'", 'Любимые магазины'));
                // нода, прицепленная к этому терму
                $nid_favorite_shops = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $tid_favorite_shops));
                $items[$tid_favorite_shops] = array(
                  //'data' => l('Любимые магазины', 'node/'.$nid_favorite_shops, array('title' => 'Магазины, доступные Вам для покупок через наш сервис')),
                  'data' => '<div class="user-menu-button" title="Магазины, доступные Вам для покупок через наш сервис">Любимые магазины</div>',
                  'children' => $items_user_shops,
                 );
            }
           
      
           
         			
           // прицепим сформированные пункты к основному меню
           $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 2 AND name = '%s'", 'Личные данные'))]['children'] = $items_personal;           
           // зададим ссылку для пункта меню Жалобы и предложения
           $items[db_result(db_query("SELECT tid from {term_data} WHERE vid = 2 AND name = '%s'", 'Жалобы и предложения'))]['data'] = l('Жалобы и предложения', 'node/'.db_result(db_query("SELECT nid from {node} WHERE title = '%s'", 'u'. $user->uid . '-pc')));

           
                           
           // если это админ, добавим в меню пункт для администрации сайта
           if($user->uid == 1)
    		{
   				 
    			$items['admin'] = array(
                  'data' => l(t('Admin'), 'admin/', array('title' => t('Site administration'), 'class' => 'site_admin')),
                  'children' => array(),
                 );
    	    }
           
           
           
           // поместим все пункты в один родительский пункт
            $items_root['root'] = array(
                  //'data' => l('Ваше личное меню', '', array('title' => 'Личное меню пользователя', 'id' => 'user-menu-button')),
                  'data' => '<div class="user-menu-button capt" title="Личное меню пользователя">Ваше личное меню</div>',
                  'children' => $items,
                 );
           
           
           
           $vars['user_menu'] = theme('nice_tax_menu', $items_root, array('class' => 'ddmenu', 'id' => 'user_menu'), 'down', 'left');
           // добавим приветствие и логаут для пользователя
           
           $output = '<div id="user-bar-logged">';

		   //$output .= t('<p class="user-info">Здравстуйте, !user, добро пожаловать на сайт zapokupkami.com</p>', array('!user' => theme('username', $user))); 
           $output .= t('<p class="user-info">Здравствуйте, !user, <br>добро пожаловать на наш сайт!</p>', array('!user' => $user->name)); 
		   /*
           $output .= theme('item_list', 
                            array(l(t('Sign out'), 'logout', array('class' => 'sign_out'), drupal_get_destination()),),
            				NULL,
            				'ul', 
            				array('class' => 'logged_user_buttons'));
           */
           
		   // добавим палочки между блоками текста для красоты :)
           $output .= '<div id="delim1">|</div> <div id="delim2">|</div>';
		   
           
           
           
           
           
           // оригинальная версия, отправляющая пользователя при выходе на страницу, на которой он когда-то залогинился
           // это не есть правильно
		   //$output .= l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашего экаунта'), drupal_get_destination());
		   //$output .= l(t(' '), 'node/75', array('id' => 'help_button', 'title' => 'Помощь'), drupal_get_destination());		   
		   
		   //$output .= '</div>';
		   
		   
		   //$lout .= l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашего экаунта'), drupal_get_destination());
		   if($_SERVER['REQUEST_URI'] != '/')
		   	$lout .= l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашей учётной записи'), 'destination=' . $_SERVER['REQUEST_URI']);
		   else 
		   	$lout .= l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашей учётной записи'), 'destination=' . '');
		   
		   
		   //$hlp .= l(t(' '), 'node/75', array('id' => 'help_button', 'title' => 'Помощь')); //, drupal_get_destination());		   
		   //$lout_help_closediv = $lout . $hlp . '</div>';
		   $lout_help_closediv = $lout . '</div>';

           
		   $vars['user_menu'] .= $output;
		   
           // добавим к меню остальные пункты блока пользователя
           //$vars['user_menu'] .= $output;
          
            // сохраним сформированное меню пользователя в переменной сессии для дяльнейшего повторного использования
           $_SESSION['user_menu'] = $vars['user_menu']; 
           
           
           // добавим хелп и выход, формирующийся заново для каждой страницы 
           $vars['user_menu'] .= $lout_help_closediv; 

           
           // сохраним также боковое меню в раскрытом виде для использования позже
           $_SESSION['user_menu_side'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'user_menu_side'), 'right');
           
           //$um = $vars['user_menu'];
           //drupal_set_message("um = $um", 'error');
           
          

          }
        } // end of if(!$_SESSION['user_menu'])
       else 
        {
       	 // если меню уже было сформировано, просто выводим его
         //$vars['user_menu'] = $_SESSION['user_menu'];
   	     //drupal_set_message("user menu recreate", 'error'); 
   	     

   	     
   	     
   	     
   	     //$lout = l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашего экаунта'), drupal_get_destination());
   	     if($_SERVER['REQUEST_URI'] != '/')
   	     	$lout = l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашей учётной записи'), 'destination=' . $_SERVER['REQUEST_URI']);
   	     else
   	     	$lout = l(t('Выйти'), 'logout', array('id' => 'logout_button', 'title' => 'Выйти из Вашей учётной записи'), 'destination=' . '');
   	     	
		   //$hlp = l(t(' '), 'node/75', array('id' => 'help_button', 'title' => 'Помощь')); //, drupal_get_destination());		   
		   //$lout_help_closediv = $lout . $hlp . '</div>';
		   $lout_help_closediv = $lout . '</div>';

           
           // добавим к меню остальные пункты блока пользователя
           //$vars['user_menu'] .= $output;
           
           $vars['user_menu'] .= $lout_help_closediv; 

   	     
   	     
        }  
      }
     else // if not, show login form
      {
        $output = '<div id="user-bar-notlogged">';
        $output .= t('<p class="login-invite">Для пользования сервисом "zapokupkami.com" введите свои данные...</p>');
        $output .= '<div class="input">' . drupal_get_form('custom_user_login_blocks') . '</div>'; 
        //$output .= '<div class="input">' . '' . '</div>'; 
        $output .= '</div>';
        
     	$vars['user_menu'] = $output; // заменить на показ формы с логином
      }     
            
       
       
    //$current_city = 1;
    //$current_city_tid = variable_get('current_city', NULL);
    //$current_shop_tid = 5; //variable_get('current_shop_tid', NULL);
    //drupal_set_message("Heading: current_city = $current_shop_tid ", 'error');
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
//--------------------------------------------------------------------------------------------



    
    // сформируем second_menu
    // оно является контекстно зависимым от содержания страницы

    if(arg(0) =='node' AND is_numeric(arg(1)))
    {    
      	//$node = node_load(arg(1));
      	
      	$node = new stdClass();
   		$node->nid = arg(1); 
   		$node->type = db_result(db_query("SELECT type FROM {node} WHERE nid = '%s' ", $node->nid));
   
      	
    
    //$node_type = $node->type;
    //$node_title = $node->title;
    //$node_nid = $node->nid;    
    //drupal_set_message("Argument node: node_type = $node_type, node_nid = $node_nid, node_title = $node_title", 'error');
    
    // если в продукте, в отделе или в магазине, то выяснить, в каком магазине и установить его текущим в переменную current_shop
    // а заодно и установить second_menu для этого магазина
    // затем проверить, установлен ли параметр current_city и равен ли он текущему городу текущего установленного магазина
    // если не установлен или не равен, установить новое значение, равное городу только что установленного магазина
    
    
    
    
     if(strpos($node->type, 'product') !== FALSE OR $node->type == 'c_department')
      {
        // если находимся в продукте, найдём по товару текущий магазин и установим меню товаров текущего магазина
        // также установим текущий город и магазин
      	
      	//$node_tid = db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", $node->nid)); 
        $node_tids = taxonomy_node_get_terms_by_vocabulary($node->nid, 1); 
      	foreach($node_tids as $node_tid)
          $node_tid = $node_tid->tid;
      	
      	$linage_tids = array();
        //$linage_tids[] = $node_tid;
        $save_node_tid = $node_tid;
        
        $count = 0;
        while(($node_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $node_tid))) != 0)
         {
           $linage_tids[] = $node_tid;
           $count++;
         } 

        //$argument_shop_tid = $linage_tids[$count-3]; // вариант город-магазин
        $argument_shop_tid = $linage_tids[$count-4]; // вариант город-район-магазин. Третьим элементом в этом варианте становится район
        $argument_rajon_tid = $linage_tids[$count-3];
        $argument_city_tid = $linage_tids[$count-2]; // при любом варианте город будет вторым элементом после страны
        
		// зададим переменную текущего магазина        
        $_SESSION['current_shop'] = $argument_shop_tid;
        $_SESSION['current_rajon'] = $argument_rajon_tid;
    	$_SESSION['current_city'] = $argument_city_tid;
    	$_SESSION['current_city_name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $argument_city_tid));
    	
        //$argument_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $argument_shop_tid)); 

		// зададим переменные для логотипа, адреса и прочих данных магазина
		// будем сохранять в сессии данные (вернее, данные по файлам логотипов, по адресу магазина и т.д.) для всех просмотренных магазинов, в название данных в сессии будем вставлять тид магазина, 
		// таким образом, не придётся каждый раз заново рассчитывать данные по магазину, если они уже были однажды определёны

		$cur_shop_info = zp_functions_shop_info($argument_shop_tid, $user->uid);
		$_SESSION['current_shop_name'] = $cur_shop_info['shop_name'];
		$_SESSION['current_shop_type_sp2'] = $cur_shop_info['shop_type_spell_2'];
		
        //variable_set('current_city', $argument_city_tid);
        
    	 
    	 
    	if($node->type == 'c_department')
    	  //$_SESSION['current_otdel'] = $linage_tids[0]; // если находимся в отделе, значит устанавливаем терм текущей ноды для переменной отдела
    	    $_SESSION['current_otdel'] = $save_node_tid; // поправка... так как в данной подпрограмме мы почему-то не сохранили терм текущей ноды в линедже, то берём оригинальную переменную, а не ищем её в массиве
    	else 
    	   //$_SESSION['current_otdel'] = $linage_tids[1]; // если находимся в продукт, значит устанавливаем терм родителя текущей ноды для переменной отдела
    	   $_SESSION['current_otdel'] = $linage_tids[0]; // поправка... так как в данной подпрограмме мы почему-то не сохранили терм текущей ноды в линедже, то нужно брать нулевой элемент
    	 
    	   
    	
    	   	//$x = $_SESSION['current_otdel'];   
    	   	//$y0 = $save_node_tid;
    	   	//$y1 = $linage_tids[0];
    	   	//drupal_set_message("x = $x, y0 = $y0,  y1 = $y1", 'error');
    	   
        if($argument_shop = $_SESSION['n' . $cur_shop_info['shop_nid'] . '_shop'])
        	$vars['second_menu'] = $argument_shop['smenu'];
        else 
        {
    		/*
        	if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $argument_shop_tid))
    		{
        		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'second_menu'), 'down');       	 
        		$argument_shop['smenu'] = $vars['second_menu'];
    		}
    		*/
    		
        	// храним меню магазина в общей переменной для всех пользователей,  
        	// чтобы заново каждый раз не генерировать меню из тысяч наименований
        	// если меню нет в общей переменной, создаём его и помещаем в эту переменную
        	//if(!($vars['second_menu'] = variable_get('n' . $cur_shop_info['shop_nid'] . '_shop_smenu', null)))
        	
        	
        	
        	
        	// РАСКОММЕНТИРОВАТЬ ПОСЛЕ ТЕСТИРОВАНИЯ!
        	if(!($vars['second_menu'] = variable_get('shop_smenu_nid' . $cur_shop_info['shop_nid'], null)))
        	//if(1)
    		{
        		if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $argument_shop_tid))
    			{
        			//$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
        			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu catalog', 'id' => 'second_menu'), 'down'); // без superfish
        			$argument_shop['smenu'] = $vars['second_menu'];
        			
        			// сохраняе меню в общую переменную
        			//variable_set('n' . $cur_shop_info['shop_nid'] . '_shop_smenu', $vars['second_menu']);
        			variable_set('shop_smenu_nid' . $cur_shop_info['shop_nid'], $vars['second_menu']);
    			}
    		}
    		else 
    			$argument_shop['smenu'] = $vars['second_menu'];
    		
    		
    		$argument_shop['stid'] = $argument_shop_tid;
    		$argument_shop['rtid'] = $argument_rajon_tid;
    		$argument_shop['ci_tid'] = $argument_city_tid;
    		$_SESSION['n' . $cur_shop_info['shop_nid'] . '_shop'] = $argument_shop;
        }
       	
     } // end of  if(!(strpos($node->type, 'product') === FALSE) OR $node->type == 'c_department')
    else
     if($node->type == 'c_shop')
      {
      	// установим меню товаров текущего магазина
      	// также установим текущий город и магазин
      	
      	// сбрасываем переменную отдела
    	$_SESSION['current_otdel'] = NULL;
    	

    	if($argument_shop = $_SESSION['n' . $node->nid . '_shop'])
       	 {
       	 	$_SESSION['current_shop'] = $argument_shop['stid'];
       	 	$cur_shop_info = zp_functions_shop_info($argument_shop['stid'], $user->uid);
       	 	$_SESSION['current_shop_name'] = $cur_shop_info['shop_name'];
       	 	$_SESSION['current_shop_type_sp2'] = $cur_shop_info['shop_type_spell_2'];
       	 	
    	 	$_SESSION['current_city'] = $argument_shop['ci_tid'];
    	 	$_SESSION['current_city_name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $argument_shop['ci_tid']));
    	 	$_SESSION['current_rajon'] = $argument_shop['rtid'];
    	 	
    	 	$vars['second_menu'] = $argument_shop['smenu'];
       	 }
       	else 
       	{
      		// если просто запрашивать тид с базы, то по магазину хватается не тот тид, а ошибочный тид из другого каталога, поэтому меню не формируется, 
      		// db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", $node->nid));
      		// поэтому воспользуемся функцией выбора тида из конкретного каталога, тогда всё работает
      		$argument_shop_tids = taxonomy_node_get_terms_by_vocabulary($node->nid, 1); 
      		foreach($argument_shop_tids as $argument_shop_tid)
          		$argument_shop_tid = $argument_shop_tid->tid;
      	
      	
      		//if(!($current_shop_tid = variable_get('current_shop', NULL)) OR $argument_shop_tid != $current_shop_tid)
        	//variable_set('current_shop', $argument_shop_tid);
        	$_SESSION['current_shop'] = $argument_shop_tid;
        	$argument_shop['stid'] = $argument_shop_tid; // вспомогательная переменная для сохранения в сессии
        
			// зададим переменные для логотипа, адреса и прочих данных магазина
			// будем сохранять в сессии данные (вернее, данные по файлам логотипов, по адресу маназина и т.д.) для всех просмотренных магазинов, в название данных в сессии будем вставлять тид магазина, 
			// таким образом, не придётся каждый раз заново рассчитывать данные по магазину, если они уже были однажды определёны
			
			$cur_shop_info = zp_functions_shop_info($argument_shop_tid, $user->uid);			
        	$_SESSION['current_shop_name'] = $cur_shop_info['shop_name'];
        	$_SESSION['current_shop_type_sp2'] = $cur_shop_info['shop_type_spell_2'];
        
        	// вариант город-магазин
        	//$argument_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $argument_shop_tid)); 
        
        	// вариант город-район-магазин
        	$argument_rajon_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $argument_shop_tid)); 
    		$argument_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $argument_rajon_tid)); 
        
        	//variable_set('current_city', $argument_city_tid);
    		$_SESSION['current_city'] = $argument_city_tid; 
    		$argument_shop['ci_tid'] = $argument_city_tid; // вспомогательная переменная для сохранения в сессии
    		
    		$_SESSION['current_rajon'] = $argument_rajon_tid;
    		$argument_shop['rtid'] = $argument_rajon_tid; // вспомогательная переменная для сохранения в сессии
    	
    		
    		/*
    		if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $argument_shop_tid))
    		{
          		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'second_menu'), 'down');        	
          		$argument_shop['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    		}
    		*/
    		// храним меню магазина в общей переменной для всех пользователей,  
        	// чтобы заново каждый раз не генерировать меню из тысяч наименований
        	// если меню нет в общей переменной, создаём его и помещаем в эту переменную
        	//if(!($vars['second_menu'] = variable_get('n' . $cur_shop_info['shop_nid'] . '_shop_smenu', null)))
        	if(!($vars['second_menu'] = variable_get('shop_smenu_nid' . $cur_shop_info['shop_nid'], null)))
    		{
        		if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $argument_shop_tid))
    			{
        			///////////// 22222 ///////////$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');       	 
                                $vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu catalog', 'id' => 'second_menu'), 'down'); // без superfish
        			$argument_shop['smenu'] = $vars['second_menu'];
        			
        			// сохраняе меню в общую переменную
        			//variable_set('n' . $cur_shop_info['shop_nid'] . '_shop_smenu', $vars['second_menu']);
        			variable_set('shop_smenu_nid' . $cur_shop_info['shop_nid'], $vars['second_menu']);
    			}
    		}
    		else 
    			$argument_shop['smenu'] = $vars['second_menu'];
    		
    		
    		$_SESSION['n' . $node->nid . '_shop'] = $argument_shop;
       	}
      } // end of if($node->type == 'c_shop')
    else
      if($node->type == 'c_rajon')
      {
      	 // если находимся в районе, установим меню c магазинами по группам товаров в городе, с ограничением на район
         // также установим текущий город. 
         // и СБРОСИМ магазин
         // район запоминать не будем, так как не понятно будет (при находжении не в каталоге), из какого района представлены магазины по группам
       	 
       	 //$argument_rajon_tid = db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", $node->nid)); 
       	 
    	 $_SESSION['current_shop'] = NULL; 
    	 $_SESSION['current_otdel'] = NULL;
    	 
       	 
       	 if($argument_rajon = $_SESSION['n' . $node->nid . '_rajon'])
       	 {
       	 	$_SESSION['current_city'] = $argument_rajon['ci_tid'];
       	 	$_SESSION['current_city_name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $argument_rajon['ci_tid']));
    	 	$_SESSION['current_rajon'] = $argument_rajon['rtid'];
    	 	$vars['second_menu'] = $argument_rajon['smenu'];
       	 }
       	 else 
       	 {
       	 	$argument_rajon_tids = taxonomy_node_get_terms_by_vocabulary($node->nid, 1); 
         	foreach($argument_rajon_tids as $argument_rajon_tid)
           		$argument_rajon_tid = $argument_rajon_tid->tid;
       	 
       	 	$argument_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $argument_rajon_tid));
       	 	//if(!($current_city_tid = variable_get('current_city', NULL)) OR $argument_city_tid != $current_city_tid)
         	//variable_set('current_city', $argument_city_tid);
         	
    	 	$_SESSION['current_city'] = $argument_city_tid; 
    	 	$argument_rajon['ci_tid'] = $argument_city_tid; // вспомогательная переменная для сохранения в сессии
    	 
    	 	$_SESSION['current_rajon'] = $argument_rajon_tid;
    	 	$argument_rajon['rtid'] = $argument_rajon_tid; // вспомогательная переменная для сохранения в сессии
    	   
         	$argument_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $argument_city_tid));
    	 	$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $argument_city_name));
    	
    	 	if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0, $argument_rajon_tid))
    	 	{
    	 		foreach($items as $type_tid => $type)
					if(empty($type['children']))
    					unset($items[$type_tid]); // убираем пункт меню типа магазина, если в этом типе нет магазинов	
          		
    	 		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
          		$argument_rajon['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	 	}
    	 	
    	 	// сохраним в сессиионной переменной данные по этому району (second меню, тиды этого района и его города), ориентир - номер ноды района (чтобы быстрее вычислять, взяв номер прямо с командрой строки)
    	 	$_SESSION['n' . $node->nid . '_rajon'] = $argument_rajon;
          		
       	 } // end of else
       } // end of if($node->type == 'c_rajon')
    else
      if($node->type == 'c_city')
       {
         // если находимся в городе, установим меню c магазинами по группам товаров в городе
         // также установим текущий город
         // и СБРОСИМ магазин

    	 $_SESSION['current_rajon'] = NULL;
    	 $_SESSION['current_shop'] = NULL; 
    	 $_SESSION['current_otdel'] = NULL;
    	 
    	      
         if($argument_city = $_SESSION['n' . $node->nid . '_city'])
       	 {
       	 	$_SESSION['current_city'] = $argument_city['ci_tid'];
    	 	$vars['second_menu'] = $argument_city['smenu'];
       	 }
       	 else 
       	 {
       	 	//$argument_city_tid = db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", $node->nid));
       	 	$argument_city_tids = taxonomy_node_get_terms_by_vocabulary($node->nid, 1);
         	foreach($argument_city_tids as $argument_city_tid)
           		$argument_city_tid = $argument_city_tid->tid;
       	 
       	 	//if(!($current_city_tid = variable_get('current_city', NULL)) OR $argument_city_tid != $current_city_tid)
         	//variable_set('current_city', $argument_city_tid);
         
    	 	$_SESSION['current_city'] = $argument_city_tid;
    	 	$argument_city['ci_tid'] = $argument_city_tid; // вспомогательная переменная для сохранения в сессии

    	  
         	$argument_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $argument_city_tid));
    	 	$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $argument_city_name));
    	
    	 	if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0))
    	 	{
				// собираем все магазины в районы (немного изменяя структуру сформированного меню)
              	$items = zp_functions_shops_by_rajons($items);
    	 		
    	 		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
          		$argument_city['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	 	}
    	 	
    	 	// сохраним в сессиионной переменной данные по этому городу (second меню, тиды этого района и его города), ориентир - номер ноды района (чтобы быстрее вычислять, взяв номер прямо с командрой строки)
    	 	$_SESSION['n' . $node->nid . '_city'] = $argument_city;
       	 }
       }
      else  
        if($node->type == 'c_country')
        {
           
    	   	$_SESSION['current_shop'] = NULL; 
    	   	$_SESSION['current_city'] = NULL; 
    	   	$_SESSION['current_rajon'] = NULL;
    	   	$_SESSION['current_otdel'] = NULL;
        	
			$vars['second_menu'] = $vars['cities_menu_second'];

			
    	   	if($argument_country = $_SESSION['n' . $node->nid . '_country'])
       	 	{
       	 		$_SESSION['current_country'] = $argument_country['co_tid']; // похоже, что для страны не сохраняются другие переменные
       	 	}
       	 	else 
       	 	{
        		$argument_country_tids = taxonomy_node_get_terms_by_vocabulary($node->nid, 1);
           		foreach($argument_country_tids as $argument_country_tid)
            		$argument_country_tid = $argument_country_tid->tid;
        	
            		
           		$_SESSION['current_country'] = $argument_country_tid; 
           	
           		$argument_country['co_tid'] = $argument_country_tid;
            	$_SESSION['n' . $node->nid . '_country'] = $argument_country; //$argument_country_tid;
       	 	}
        	
        }
      else //else with other node types
       {
       	
    	// если в любом другом типе
    	// выяснить, задан ли параметр current_shop
    	// если да, то установаить в second_menu меню этого магазина
    	// если нет...
    	// выяснить, задан ли параметр current_city
    	// если да, то установаить в second_menu магазины по группам товаров для этого города
    	// если нет, установить в second_menu меню стран с городами    	
    	
       	//if($current_shop_tid = variable_get('current_shop', NULL)) // if $current_shop_tid is set 
       	if($cur_shop_tid = $_SESSION['current_shop'])
         {
            $cur_shop_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_shop_tid));
         	
            if($cur_shop = $_SESSION['n' . $cur_shop_nid . '_shop'])
            	$vars['second_menu'] = $cur_shop['smenu'];
            else 
            {
         		/*
            	//if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $current_shop_tid))
           		if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $_SESSION['current_shop']))
           		{
            		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'second_menu'), 'down');       	
					$cur_shop['smenu'] = $vars['second_menu'];
           		}
				*/
         		
         		// храним меню магазина в общей переменной для всех пользователей,  
        		// чтобы заново каждый раз не генерировать меню из тысяч наименований
        		// если меню нет в общей переменной, создаём его и помещаем в эту переменную
        		//if(!($vars['second_menu'] = variable_get('n' . $cur_shop_nid . '_shop_smenu', null)))
        		if(!($vars['second_menu'] = variable_get('shop_smenu_nid' . $cur_shop_nid, null)))
    			{
        			if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $cur_shop_tid))
    				{
        				////////// 2222 ///////////$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');       	 
                                        $vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu catalog', 'id' => 'second_menu'), 'down'); // без superfish
        				$cur_shop['smenu'] = $vars['second_menu'];
        			
        				// сохраняе меню в общую переменную
        				//variable_set('n' . $cur_shop_nid . '_shop_smenu', $vars['second_menu']);
        				variable_set('shop_smenu_nid' . $cur_shop_nid, $vars['second_menu']);
    				}
    			}
    			else 
    				$cur_shop['smenu'] = $vars['second_menu'];

           		$cur_shop['stid'] = $cur_shop_tid;
           		$_SESSION['n' . $cur_shop_nid . '_shop'] = $cur_shop;
            }
	     }
	    else 
	     if($cur_rajon_tid = $_SESSION['current_rajon'])
	     {

	     	$cur_rajon_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_rajon_tid));
	     	
	     	if($cur_rajon = $_SESSION['n' . $cur_rajon_nid . '_rajon'])
            	$vars['second_menu'] = $cur_rajon['smenu'];
            else 
            {
	       	 	$cur_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $cur_rajon_tid));

    		 	$_SESSION['current_city'] = $cur_city_tid; 
    		 	$cur_rajon['ci_tid'] = $cur_city_tid; // вспомогательная переменная для сохранения в сессии
    	 
    	 		$cur_rajon['rtid'] = $cur_rajon_tid; // вспомогательная переменная для сохранения в сессии
    	   
         		$cur_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $cur_city_tid));
    	 		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $cur_city_name));
    	
    	 		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0, $cur_rajon_tid))
    	 		{
    	 			foreach($items as $type_tid => $type)
						if(empty($type['children']))
    						unset($items[$type_tid]); // убираем пункт меню типа магазина, если в этом типе нет магазинов	
          		
    	 			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
          			$cur_rajon['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	 		}
    	 	
    	 		// сохраним в сессиионной переменной данные по этому району (second меню, тиды этого района и его города), ориентир - номер ноды района (чтобы быстрее вычислять, взяв номер прямо с командрой строки)
    	 		$_SESSION['n' . $node->nid . '_rajon'] = $cur_rajon;
            }
            
	     }
	   	else 
	     if($cur_city_tid = $_SESSION['current_city'])
	     {
	     	
	     	$cur_city_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_city_tid));
	     	
	     	if($cur_city = $_SESSION['n' . $cur_city_nid . '_city'])
            	$vars['second_menu'] = $cur_city['smenu'];
            else 
            {
	     	
	     		//$current_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $current_city_tid));
    	   		$current_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $cur_city_tid));
	       		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $current_city_name));
    	
    	   		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0))
    	   		{
    	   			
    	   			// собираем все магазины в районы (немного изменяя структуру сформированного меню)
              		$items = zp_functions_shops_by_rajons($items);
    	 			
            		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
            		$cur_city['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	   		}
    	   		
    	   		$cur_city['ci_tid'] = $cur_city_tid;
    	   		$_SESSION['n' . $cur_city_nid . '_city'] = $cur_city; // сохраняем в сессионной переменной
            }
            
	     }
	   else if($cur_country_tid = $_SESSION['current_country'])
	    {
	     	// если текущий город и магазин не установлены, в second_menu устанавливаем меню городов (и стран), 

	     	
	     	// вернее просто копируем в него cities_menu, сформированного ранее в этой же функции
	     	//$vars['second_menu'] = $vars['cities_menu_second'];
	     	
	     	// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
	     	//if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, -1))
	     	if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, $cur_country_tid))
	     	{
	     		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
	     		$cur_country['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
	     	}

	     	$cur_country['co_tid'] = $cur_country_tid;
	     	$_SESSION['n' . $cur_country_nid . '_city'] = $cur_country; // сохраняем в сессионной переменной


	    } 
       
       } // end of block else with undefined node types
      
    } 
    else // если находимся не в ноде
    {
    	
    	// копируем полностью содержимое предыдущего блока block else with undefined node types
    
    
    		
    	// если находимся не в ноде
    	// выяснить, задан ли параметр current_shop
    	// если да, то установить в second_menu меню товаров текущего магазина
    
    	// иначе выяснить, задан ли параметр current_city
    	// если да, то установить в second_menu магазины по группам товаров для этого города
    	// если нет, установить в second_menu меню стран с городами    	
    	
       	if($cur_shop_tid = $_SESSION['current_shop'])
        {
        	
        	$cur_shop_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_shop_tid));
         	
            if($cur_shop = $_SESSION['n' . $cur_shop_nid . '_shop'])
            	$vars['second_menu'] = $cur_shop['smenu'];
            else 
            {
         		/*
            	//if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $current_shop_tid))
           		if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $_SESSION['current_shop']))
           		{
            		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'second_menu'), 'down');       	
					$cur_shop['smenu'] = $vars['second_menu'];
           		}
           		*/
         		
           		// храним меню магазина в общей переменной для всех пользователей,  
        		// чтобы заново каждый раз не генерировать меню из тысяч наименований
        		// если меню нет в общей переменной, создаём его и помещаем в эту переменную
        		//if(!($vars['second_menu'] = variable_get('n' . $cur_shop_nid . '_shop_smenu', null)))
        		if(!($vars['second_menu'] = variable_get('shop_smenu_nid' . $cur_shop_nid, null)))
    			{
        			if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $cur_shop_tid))
    				{
        				/////// 2222 //////////$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');       	 
                                        $vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu catalog', 'id' => 'second_menu'), 'down'); // без superfish
        				$cur_shop['smenu'] = $vars['second_menu'];
        			
        				// сохраняе меню в общую переменную
        				//variable_set('n' . $cur_shop_nid . '_shop_smenu', $vars['second_menu']);
        				variable_set('shop_smenu_nid' . $cur_shop_nid, $vars['second_menu']);
    				}
    			}
    			else 
    				$cur_shop['smenu'] = $vars['second_menu'];
				

           		$cur_shop['stid'] = $cur_shop_tid;
           		$_SESSION['n' . $cur_shop_nid . '_shop'] = $cur_shop;
            }
        	

	    }
	    else 
	     if($cur_rajon_tid = $_SESSION['current_rajon'])
	     {

	     	$cur_rajon_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_rajon_tid));
	     	
	     	if($cur_rajon = $_SESSION['n' . $cur_rajon_nid . '_rajon'])
            	$vars['second_menu'] = $cur_rajon['smenu'];
            else 
            {
	       	 	$cur_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $cur_rajon_tid));

    		 	$_SESSION['current_city'] = $cur_city_tid; 
    		 	$cur_rajon['ci_tid'] = $cur_city_tid; // вспомогательная переменная для сохранения в сессии
    	 
    	 		$cur_rajon['rtid'] = $cur_rajon_tid; // вспомогательная переменная для сохранения в сессии
    	   
         		$cur_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $cur_city_tid));
    	 		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $cur_city_name));
    	
    	 		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0, $cur_rajon_tid))
    	 		{
    	 			foreach($items as $type_tid => $type)
						if(empty($type['children']))
    						unset($items[$type_tid]); // убираем пункт меню типа магазина, если в этом типе нет магазинов	
          		
    	 			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
          			$cur_rajon['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	 		}
    	 	
    	 		// сохраним в сессиионной переменной данные по этому району (second меню, тиды этого района и его города), ориентир - номер ноды района (чтобы быстрее вычислять, взяв номер прямо с командрой строки)
    	 		$_SESSION['n' . $node->nid . '_rajon'] = $cur_rajon;
            }
            
	     }
	   	else 
	    if($cur_city_tid = $_SESSION['current_city'])
	    {
	    	
	    	$cur_city_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_city_tid));
	     	
	     	if($cur_city = $_SESSION['n' . $cur_city_nid . '_city'])
            	$vars['second_menu'] = $cur_city['smenu'];
            else 
            {
            	
	     		//$current_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $current_city_tid));
    	   		$current_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $_SESSION['current_city']));
	       		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $current_city_name));
    	
    	   		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0))
    	   		{
            		
    	   			// собираем все магазины в районы (немного изменяя структуру сформированного меню)
              		$items = zp_functions_shops_by_rajons($items);
    	   			
    	   			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down'); 
            		$cur_city['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
    	   		}
    	   		
    	   		$cur_city['ci_tid'] = $cur_city_tid;
    	   		$_SESSION['n' . $cur_city_nid . '_city'] = $cur_city; // сохраняем в сессионной переменной
            }
	    	
	    }
	   else if($cur_country_tid = $_SESSION['current_country'])
	    {
	     	// если текущий город и магазин не установлены, в second_menu устанавливаем меню городов (и стран), 

	     	
	     	// вернее просто копируем в него cities_menu, сформированного ранее в этой же функции
	     	//$vars['second_menu'] = $vars['cities_menu_second'];
	     	
	     	// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
	     	//if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, -1))
	     	if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, $cur_country_tid))
	     	{
	     		$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
	     		$cur_country['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
	     	}

	     	$cur_country['co_tid'] = $cur_country_tid;
	     	$_SESSION['n' . $cur_country_nid . '_city'] = $cur_country; // сохраняем в сессионной переменной


	    } 
	    else 
	    {
	    	// если местоположение не установлено, прелагаем его выбрать
	    	$vars['second_menu'] = 'Выберите магазин для покупок';
	    }
    	    	
    	
    } // end of если находимся не в ноде      
            

       
       
       

       
       
       
       
       
       
       
       
       

 	// menu s stranami i gorodami cities_menu 
 	
   if(!($vars['cities_menu'] = $_SESSION['cities_menu']) OR $_SESSION['masquarade'])
   { 
    //$vid, $myflag = 0, $mydepth = 0, $upper_items = 0, $node = 0, $set_parent_tid = -1, $via_views = 1, $only_with_parent_tid_in_v1 = 0 
   	if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, -1))
     {
       
    	// поместим все пункты в один родительский пункт

    	
    	$items_root['root'] = array(
                  'data' => t('Выбрать город, район, магазин'),
                  'children' => $items,
                 );
    	$vars['cities_menu'] = theme('nice_tax_menu', $items_root, array('class' => 'sf-menu ddmenu', 'id' => 'cities_menu'), 'down', 'right');
    	$_SESSION['cities_menu'] = $vars['cities_menu'];
    	
        //$vars['cities_menu_second'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu', 'id' => 'second_menu'), 'down');
        //$_SESSION['cities_menu_second'] = $vars['cities_menu_second'];
     }   
   }         
       
   
   
   
   
   
   
   
   
   
   
   
   
   
        
        // покажем в заголовке меню выбора города-страны текущий город (страну)
        // заодно установим текущий город или страну, если они ещё не установлены для текущего пользователя
    	
	   	if($current_city_tid = $_SESSION['current_city']) // если установлен текущий город, то указываем его основным в меню стран и городов
    	 {
    		// так как всё равно не показывается название текущего места, то закомментируем эти строки
    	 	$current_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d", $current_city_tid)); 
    		$vars['current_place'] = $current_city_name;
    	 }
         else if($current_country_tid = $_SESSION['current_country']) // если установлена текущая страна, то указываем её основной в меню стран и городов
          {  
            // так как всё равно не показывается название текущего места, то закомментируем эти строки
          	$current_country_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d", $current_country_tid)); 
            $vars['current_place'] = $current_country_name;
          }
         else  // иначе, если не указано текущее местоположение (например, при первой загрузке сайта в новой сессии), то ставим текущим местоположение, указанное основным по умолчанию или у основным у текущего пользователя
         {     // если местоположение не указано ни общее по умолчанию, ни для юзера, ничего не ставим, показываем пригашение выбрать местопложение
       	
         	
         	
         	// Получаем данные по городу (местоположению) по умолчанию (для авторизованного или не авторизованного юзера)         	
      		$zp_default_set = zp_functions_get_zp_default_set();
         	 	
       	 	$vars['current_place'] = $zp_default_set['main_place_default']['name'];
		 	
       	 	if($zp_default_set['main_place_default']['type'] == 'c_city')
       	 	{
       	 		$_SESSION['current_city'] = $zp_default_set['main_place_default']['tid'];

       	 		//$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $zp_default_set['main_city_default']['name']));
       	 		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $zp_default_set['main_place_default']['name']));

       	 		// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
       	 		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0))
       	 		{
       	 			// собираем все магазины в районы (немного изменяя структуру сформированного меню)
       	 			$items = zp_functions_shops_by_rajons($items);

       	 			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
       	 			$cur_city['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
       	 		}

       	 		

       	 		$cur_city['ci_tid'] = $zp_default_set['main_place_default']['tid'];
       	 		$_SESSION['n' . $zp_default_set['main_place_default']['nid'] . '_city'] = $cur_city; // сохраняем в сессионной переменной

       	 	}
       	 	else if($zp_default_set['main_place_default']['type'] == 'c_country')
       	 	{
       	 		$_SESSION['current_country'] = $zp_default_set['main_place_default']['tid'];
       	 		
       	 		// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
       	 		//if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, -1))
       	 		if($items = nice_tax_menu_build_items(1, 0, 4, 0, 0, $zp_default_set['main_place_default']['tid']))
       	 		{
    	 			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
       	 			$cur_country['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
       	 		}

       	 		$cur_country['co_tid'] = $zp_default_set['main_place_default']['tid'];
       	 		$_SESSION['n' . $zp_default_set['main_place_default']['nid'] . '_country'] = $cur_country; // сохраняем в сессионной переменной

       	 	}
       	 	else if($zp_default_set['main_place_default']['type'] == 'c_rajon')
       	 	
       	 	//($cur_rajon_tid = $_SESSION['current_rajon'])
       	 	{

       	 		//$cur_rajon_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $cur_rajon_tid));
       	 	
       	 		$_SESSION['current_rajon'] = $zp_default_set['main_place_default']['tid'];
       	 		
       	 		$cur_city_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $zp_default_set['main_place_default']['tid']));
       	 		$cur_city_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d and vid = 1", $cur_city_tid));
       	 		$_SESSION['current_city'] = $cur_city_tid;
       	 		
       	 		$cur_rajon['ci_tid'] = $cur_city_tid; // вспомогательная переменная для сохранения в сессии
       	 		$cur_rajon['rtid'] = $zp_default_set['main_place_default']['tid']; // вспомогательная переменная для сохранения в сессии
       	 		
       	 		$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $cur_city_name));
       	 		//$goods_groups_city_tid = db_result(db_query("SELECT tid from {term_data} WHERE name = '%s' and vid = 3", $zp_default_set['main_place_default']['name']));

       	 		if($items = nice_tax_menu_build_items(3, 0, 0, 0, $node, $goods_groups_city_tid, 0, $zp_default_set['main_place_default']['tid']))
       	 		{
       	 			foreach($items as $type_tid => $type)
       	 				if(empty($type['children']))
       	 					unset($items[$type_tid]); // убираем пункт меню типа магазина, если в этом типе нет магазинов
       	 				
     	 			$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');
       	 			$cur_rajon['smenu'] = $vars['second_menu']; // вспомогательная переменная для сохранения в сессии
       	 		}

       	 		// сохраним в сессиионной переменной данные по этому району (second меню, тиды этого района и его города), ориентир - номер ноды района (чтобы быстрее вычислять, взяв номер прямо с командрой строки)
       	 		$_SESSION['n' . $zp_default_set['main_place_default']['nid'] . '_rajon'] = $cur_rajon;
       	 	
       	 	}
       	 	else if($zp_default_set['main_place_default']['type'] == 'c_shop')
       	 	{
       	 		
            	$_SESSION['current_shop'] = $zp_default_set['main_place_default']['tid'];
            	// тут может понадобиться и установка всех данных по родителям этого магазина (район, город, страна) и её параметров
            	// как это делается в других блоках (выше) при инициализации магазина
            	// но пока вроде работает и без этого
            	
            	
       	 		//if($cur_shop = $_SESSION['n' . $zp_default_set['main_place_default']['nid'] . '_shop'])
            		//$vars['second_menu'] = $cur_shop['smenu'];
            	//else 
            
  				// пробуем получить уже сформированное меню из базы данных или формируем его, если нет в базе
        		if(!($vars['second_menu'] = variable_get('shop_smenu_nid' . $zp_default_set['main_place_default']['nid'], null)))
    			{
        			if($items = nice_tax_menu_build_items(1, 0, 0, 0, 0, $zp_default_set['main_place_default']['tid']))
    				{
        				//////////// 22222222 ////////////$vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'sf-menu ddmenu', 'id' => 'second_menu'), 'down');       	 
                                        $vars['second_menu'] = theme('nice_tax_menu', $items, array('class' => 'ddmenu catalog', 'id' => 'second_menu'), 'down'); // без superfish
        				$cur_shop['smenu'] = $vars['second_menu'];
        			
        				// сохраняе меню в общую переменную
        				variable_set('shop_smenu_nid' . $zp_default_set['main_place_default']['nid'], $vars['second_menu']);
    				}
    			}
    			else 
    				$cur_shop['smenu'] = $vars['second_menu'];

           		$cur_shop['stid'] = $zp_default_set['main_place_default']['tid'];
           		$_SESSION['n' . $zp_default_set['main_place_default']['nid'] . '_shop'] = $cur_shop;
       	 		
       	 	}
       	 	else 
       	 	{
       	 		// если местоположение не установлено, прелагаем его выбрать
	    		$vars['second_menu'] = '<div class="choose_place">< < < Пожалуйста, выберите место для покупок</div>';
       	 	}

         	
         	
         } // end of else  // иначе, если не указано текущее местоположение...

         
         
         
         
         
         
// зададим строку подсказки в шапке сайта, в зависимости от текущего местонахождения (магазин, район, город и т.д.)         
// а также логотип магазина для шапки сайта, если находимся в магазине, отделе или продукте
         
          // сначала возьмём местонахождение в магазине
          
          // если задана переменная с инфо о текущем магазине, значит она только что была рассчитана и можно выводить в шаблоне (в шапке страницы) данные по этому магазину (адрес, лого в шапке) 
          // значит зададим переменные для вывода инфы о текущем магазине (адрес, лого и т.д.) в верхней шапке сайта с меню
    
          if($cur_shop_info)  // если эта переменная задана, значит используем её значения
          {
          	$vars['current_page_info'] = '<div class="prenote">Текущий или последний выбранный Вами для покупок магазин/заведение:</div>'.'<div class="caption">' . l($cur_shop_info['shop_name'], 'node/' . $cur_shop_info['shop_nid'], array('title' => 'Перейти на первую страницу каталога этого заведения')) . ',</div><div class="type">' . $cur_shop_info['shop_type_spell_1'] . '</div><div class="address">' . $cur_shop_info['shop_address'] . '</div><div class="postnote">Воспользуйтесь меню (ниже) для выбора в нём отделов и товаров/услуг или для смены магазина/заведения</div>';
          	$vars['shop_id'] = $cur_shop_info['shop_id']; // на основе именно внутреннего артикула ZP магазина будем формировать название картинок и логотипов магазина
          }
          else if($cur_shop_tid = $_SESSION['current_shop']) // если задана сессионная переменная текущего магазина
          {
           	$cur_shop_info = zp_functions_shop_info($cur_shop_tid, $user->uid);

         	$vars['current_page_info'] = '<div class="prenote">Текущий или последний выбранный Вами для покупок магазин/заведение:</div>'.'<div class="caption">' . l($cur_shop_info['shop_name'], 'node/' . $cur_shop_info['shop_nid'], array('title' => 'Перейти на первую страницу каталога этого заведения')) . ',</div><div class="type">' . $cur_shop_info['shop_type_spell_1'] . '</div><div class="address">' . $cur_shop_info['shop_address'] . '</div><div class="postnote">Воспользуйтесь меню (ниже) для выбора в нём отделов и товаров/услуг или для смены магазина/заведения</div>';
 			$vars['shop_id'] = $cur_shop_info['shop_id']; // на основе именно внутреннего артикула ZP магазина будем формировать название картинок и логотипов магазина
          }
          else if($argument_rajon_tid OR ($argument_rajon_tid = $_SESSION['current_rajon'])) // если задана эта переменная, значит мы в районе
          {
           	  if(!($cur_rajon_info = $_SESSION[$argument_rajon_tid . '_rajon_info'])) // если при этом в сессии ещё не сохранены данные для такого магазина
              {
                // вычислим данные для этого района, внесём их в сессионную переменную и в переменную для файла шаблона              	 	
                  
        		// выясним ноду района по его тиду
      	   		$cur_rajon_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $argument_rajon_tid));
      	   		
      	   		$cur_rajon_info['name'] = db_result(db_query("SELECT title from {node} WHERE nid = %d", $cur_rajon_nid));
      	   		
      	   		//определим адрес района
      	   		$cur_rajon_info['address'] = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid = %d", $cur_rajon_nid));
      	   		
            	// также сохраним эти данные в сессионную переменную
          		$_SESSION[$argument_rajon_tid . '_rajon_info'] = $cur_rajon_info;
          	  }
          	  
          	  // данные (в переменных шаблона страницы) по району для шапки страницы с меню
          	  $vars['current_page_info'] = '<div class="prenote">Текущий или последний выбранный Вами для покупок район:</div>'.'<div class="caption">' . $cur_rajon_info['name'] . '</div><div class="address">' . $cur_rajon_info['address'] . '</div><div class="postnote">Используйте меню (ниже) для выбора в этом районе магазина/заведения или для смены города/района</div>';
          }
          else if($argument_city_tid OR ($argument_city_tid = $current_city_tid) OR ($argument_city_tid = $_SESSION['current_city']))
          {

           	  if(!($cur_city_info = $_SESSION[$argument_city_tid . '_city_info'])) // если при этом в сессии ещё не сохранены данные для такого города
              {
                // вычислим данные для этого города, внесём их в сессионную переменную и в переменную для файла шаблона              	 	
                  
        		// выясним ноду города по его тиду
      	   		$cur_city_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $argument_city_tid));
      	   		
      	   		$cur_city_info['name'] = db_result(db_query("SELECT title from {node} WHERE nid = %d", $cur_city_nid));
      	   		
      	   		//определим адрес города
      	   		$cur_city_info['address'] = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid = %d", $cur_city_nid));
      	   		
            	// также сохраним эти данные в сессионную переменную
          		$_SESSION[$argument_city_tid . '_city_info'] = $cur_city_info;
          		
          	  }

          	  $vars['current_page_info'] = '<div class="prenote">Текущий или последний выбранный Вами для покупок населённый пункт:</div>'.'<div class="caption">' . $cur_city_info['name'] . '</div><div class="address">' . $cur_city_info['address'] . '</div><div class="postnote">Используйте меню (ниже) для выбора в этом населённом пункте магазина/заведения или для смены города</div>';

           }
           else if($argument_country_tid OR ($argument_country_tid = $current_country_tid) OR ($argument_country_tid = $_SESSION['current_country']))
           {
  
           	if(!($cur_country_info = $_SESSION[$argument_country_tid . '_country_info']))
           	   {
           	   	
           	   	// вычислим данные для этой страны, внесём их в сессионную переменную и в переменную для файла шаблона              	 	
                  
        		// выясним ноду страны по её тиду
      	   		$cur_country_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $argument_country_tid));// 28; //
      	   		
      	   		$cur_country_info['name'] = db_result(db_query("SELECT title from {node} WHERE nid = %d", $cur_country_nid));
      	   		
      	   		//определим адрес 
      	   		$cur_country_info['address'] = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid = %d", $cur_country_nid));
      	   		
      	   		
            	// также сохраним эти данные в сессионную переменную
          		$_SESSION[$argument_country_tid . '_country_info'] = $cur_country_info;

           	   }
           	
           	   $vars['current_page_info'] = '<div class="prenote">Текущая или последняя выбранная Вами для покупок страна:</div>'.'<div class="caption">' . $cur_country_info['name'] . '</div><div class="address">' . $cur_country_info['address'] . '</div><div class="postnote">Используйте меню (ниже) для выбора в этой стрране города/района/заведения или для смены города/района</div>';
           }

           
           
    
   
 } // end of if($vars['node']->type == 'zp_header' AND $vars['node']->title == 'Zp header 01')
   // конец формирования переменных для шапки сайта
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 //---------------------------------------------------------------------------------------------------
 
 
 
 
 
// make variable breadcramb for catalog
if($vars['node']->type == 'c_breadcramb' AND $vars['node']->title == 'c_breadcramb_01')
 {
 	//echo '1 = ' . arg(0) . ', 2 = ' . arg(1) . '<br>';

    // если мы на первой странице или в форме контактов 
 	//if(arg(0) == 'node' AND (arg(1) == 227 OR arg(1) == 224 OR arg(1) == 228)) 
 		//$vars['c_breadcramb'] = main_page_breadcrumb();
	//else 
		//$vars['c_breadcramb'] = catalog_breadcrumb();    

 	if(arg(0) == 'node')
 	{
 	 $ntype = (db_result(db_query("SELECT type FROM {node} WHERE nid = '%s' ", arg(1))));
 	 if($ntype == 'home_page' 
 	 	OR $ntype == 'system_messages'
 	 	OR $ntype == 'zp_default_set' // настройки, цены, проценты магазина по умолчанию
 	 	OR $ntype == 'mc_descr' // описание производителя, поставщика
 	 	OR $ntype == 'shop_descr' // описание и контакты магазинов
 	 	OR $ntype == 'zp_issue' // статьи
 	 	OR $ntype == 'zp_issue_spravka' // справочные данные, статьи
 	 	OR $ntype == 'zp_help' // помощь 	 	
 	 	
 	 	//OR $ntype == 'webform' 
 	 	//OR ($ntype == 'zp_user' AND arg(1) == 225) // если мы в своей переопределённой ноде создания-редактирования-удаления комментариев
 	 	OR ($ntype == 'zp_user')// AND arg(1) == MY_ZP_COMMENTS_NODE_NUM) // если мы в своей переопределённой ноде создания-редактирования-удаления комментариев
 	 	
 	 	//OR $ntype == 'zp_cart'
 	 	OR strpos($ntype, 'u_') !== FALSE
 	 	) 
     	$vars['c_breadcramb'] = main_page_breadcrumb();
     else 
     	$vars['c_breadcramb'] = catalog_breadcrumb();
 	}
     else
     	$vars['c_breadcramb'] = catalog_breadcrumb();     	
 }


 
 
 
 
 
 //----------------------------------------------------------------------------------------
 
 
 // make side menu block variable 
if($vars['node']->type == 'zp_side_block' AND $vars['node']->title == 'Zp side menu 01')
 {
 
    if(!(arg(0) =='node' AND is_numeric(arg(1))))
      return 0; 
      
    $node = new stdClass();
   	$node->nid = arg(1); 
   	$node->type = db_result(db_query("SELECT type FROM {node} WHERE nid = '%s' ", $node->nid));
	
    if($node->type == 'zp_cart') // esli nahodimsia v korzine, pokazati menu korziny zi user_menu
       {
         if($user->uid) // if user is logged in  
         	// лучше покажем полное меню пользователя, сформированное ранее для верхней шапки и сохранённое в сессии
         	$vars['zp_side_menu_01'] = '<div class="smenu_header toggle-anchor user"></div><div class="toggle-content"><div class="upper_items user">Личное меню</div>' . $_SESSION['user_menu_side'] . '</div><div class="sidemenu_bottom"></div>';
       }
     else  
      if(strpos($vars['node']->type, 'u_') !== FALSE OR $node->type == 'zp_user')// OR $node->type == 'webform') 
        {
         if($user->uid) // if user is logged in
         	// считаем, что если загрузилась одна из страниц этого типа, значит уже есть и меню
         	$vars['zp_side_menu_01'] = '<div class="smenu_header toggle-anchor user"></div><div class="toggle-content"><div class="upper_items user">Личное меню</div>' . $_SESSION['user_menu_side'] . '</div><div class="sidemenu_bottom"></div>';
        }
     
    else  
      if($node->type == 'mc_descr' OR $node->type == 'shop_descr' OR $node->type == 'zp_issue' OR $node->type == 'zp_issue_spravka') 
        {
        	
        	// для любого материала этих типов покажем общее для них меню 
        	// для этого возьмём за ноду по умолчанию, например, "Товары и услуги. Все статьи и обзоры"
        	// и будем сохранять и загружать боковое меню этой ноды для любого материала указанных выше типов
        	
     		$work_node = new stdClass();
       		$work_node->nid = db_result(db_query("SELECT nid FROM {node} WHERE title = '%s'", "Товары и услуги. Все статьи и описания"));
       		$work_node->type = 'zp_issue';
        	
        	if($side_menu = $_SESSION['n' . $work_node->nid . '_sidemenu'])
         		$vars['zp_side_menu_01'] = $side_menu;
         	else 
         	{
       			/*
         		//один из рабочих вариантов - передавать на формирование меню не ноду, а тид родителя по меню
  				$work_vid = 4; // это словарь главного меню, к которому прицеплена эта рабочая нода
         		$work_tids = taxonomy_node_get_terms_by_vocabulary($work_nid, $work_vid); 
  				foreach($work_tids as $work_tid)
    				$work_tid = $work_tid->tid;
    			$parent_work_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $work_tid));
				//if($items = nice_tax_menu_build_items(4, 1, 2, 1, 0, $parent_work_tid))
				*/
				
				// определяем меню с указанием конкретного пункта родительского меню
				$parent_menu_tid = db_result(db_query("SELECT tid from {term_data} WHERE name  = '%s'", "Полезно!"));
				//if($items = nice_tax_menu_build_items(4, 1, 2, 1, 0, $parent_menu_tid))
				if($items = nice_tax_menu_build_items(4, 1, 2, 1, 0, $parent_menu_tid, 1, 0))//, array('rel' => 'nofollow')))
      			
       			// вариант с определением меню по ноде
       			//if($items = nice_tax_menu_build_items(4, 1, 2, 1, $work_node))
         		// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
       			//if($items = nice_tax_menu_build_items(4, 1, 2, 1, $node))
         		{
            		//$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, array('class' => 'ddmenu')) . '</div><div class="sidemenu_bottom"></div>';
            		$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, /* array('class' => 'ddmenu')*/ null, 0, 0, 0, 0, 'exp-menu', 'exp-side-menu') . '</div><div class="sidemenu_bottom"></div>';
            		//$_SESSION['n' . $node->nid . '_sidemenu'] = $vars['zp_side_menu_01'];	
            		$_SESSION['n' . $work_node->nid . '_sidemenu'] = $vars['zp_side_menu_01'];	
            		
         		}
         	}
           
       }
     
     else 
      //if(strpos($node->type, 'c_') !== FALSE OR strpos($node->type, 'product_') !== FALSE) // esli v kataloge
      if(strpos($node->type, 'c_') === 0 OR strpos($node->type, 'product_') !== FALSE) // esli v kataloge
       {
         
       		if($side_menu = $_SESSION['n' . $node->nid . '_sidemenu'])
         		$vars['zp_side_menu_01'] = $side_menu;
         	else 
         	{
       			// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
         		//if($items = nice_tax_menu_build_items(1, 'department', 2, 1, $node))
         		if($items = nice_tax_menu_build_items(1, 'department', 2, 1, $node, -1, 1, 0))//, array('rel' => 'nofollow')))
         		
         		{
            		//$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, array('class' => 'ddmenu')) . '</div><div class="sidemenu_bottom"></div>';
            		$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, /* array('class' => 'ddmenu')*/ null, 0, 0, 0, 0, 'exp-menu', 'exp-side-menu') . '</div><div class="sidemenu_bottom"></div>';
            		$_SESSION['n' . $node->nid . '_sidemenu'] = $vars['zp_side_menu_01'];	
         		}
         	}
       }
       else // esli nahodimsia v lubom drugom meste, pokazivaem подменю first menu (no nado otdelno nastroit' i drugie tipy)
       {
       		if($side_menu = $_SESSION['n' . $node->nid . '_sidemenu'])
         		$vars['zp_side_menu_01'] = $side_menu;
         	else 
         	{
       			// (myflag, level of start items, depth, show upper_items link, argument node, parent tid to start from)
         		//if($items = nice_tax_menu_build_items(4, 1, 2, 1, $node, -1, 1, 0, array('rel' => 'nofollow')))
         		//if($items = nice_tax_menu_build_items(4, 1, 2, 1, $node))
         		if($items = nice_tax_menu_build_items(4, 1, 2, 1, $node, -1, 1, 0))//, array('rel' => 'nofollow')))
         		
         		{
            		//$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, array('class' => 'ddmenu')) . '</div><div class="sidemenu_bottom"></div>';
            		$vars['zp_side_menu_01'] = '<div class="smenu_header catalog toggle-anchor"></div><div class="toggle-content">' . theme('nice_tax_menu', $items, /* array('class' => 'ddmenu')*/ null, 0, 0, 0, 0, 'exp-menu', 'exp-side-menu') . '</div><div class="sidemenu_bottom"></div>';
            		$_SESSION['n' . $node->nid . '_sidemenu'] = $vars['zp_side_menu_01'];	
         		}
         	}
       	
       }

 }


 
 
 
 
 
 
 
 
 
       

 
 
 
 
 
 
 // ------------------------------------------------------------------------------------
 // действия, выполняемые в зависимости от выводящейся в данный момент ноды
 

 
 /*
  if($vars['node']->type == 'c_country' 
    OR $vars['node']->type == 'c_city' 
    OR $vars['node']->type == 'c_rajon' 
    OR $vars['node']->type == 'c_shop' 
    OR $vars['node']->type == 'c_department' 
 	OR $vars['node']->type == 'zp_brand' 
 	OR $vars['node']->type == 'webform' 
 	OR $vars['node']->type == 'blog_zp_post' 
 	OR $vars['node']->type == 'blog_zp'
 	OR $vars['node']->type == 'news_post' 
 	OR $vars['node']->type == 'news'
 	OR strpos($vars['node']->type, 'product') !== FALSE
 	)
 {
*/
 	// восстанавливаем переменную $title, которая почему-то теряется при выводе через панели
 	
	//if(!$vars['teaser'])
  		//$vars['title'] = db_result(db_query("SELECT title from {node} WHERE nid  = %d", $vars['node']->nid));
  		
  	
  		
  		
  	// если для элемента задано копирование логотипов из другого элемента 
  	// (логотипы есть только у магазинов и отделов)
  	// в данном случае копируются логотипы (вернее, один логотип) для содержимого ноды (не логотип магазина для меню)
  	if(($vars['node']->type == 'c_shop' OR $vars['node']->type == 'c_department')
  		AND $vars['field_source_of_logo'][0]['view'] 
  	  )
      {

    	//echo 'vars[field_source_of_logo][0][view] = ' . $vars['field_source_of_logo'][0]['view'] . '<br>';
    	
    	if($source_logo_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", $vars['field_source_of_logo'][0]['view'])))
		{
			// если нид определился, значит происходит копирование картинок из элемента типа места в элемента типа места

			// загружаем все картинки из элемента-источника
			$source_logo_fid = db_fetch_array(db_query("SELECT field_place_logo_fid, field_place_logo_title, field_place_logo_alt FROM {content_field_place_logo} WHERE nid = %d", $source_logo_nid));
			
			$vars['node']->field_place_logo[0]['title'] = $source_logo_fid['field_place_logo_title'];
			$vars['node']->field_place_logo[0]['alt'] = $source_logo_fid['field_place_logo_alt'];

			$source_logo_path = db_result(db_query("SELECT filepath FROM {files} WHERE fid = %d", $source_logo_fid['field_place_logo_fid']));
			
			$vars['node']->field_place_logo[0]['filepath'] = $source_logo_path;	

		}
		
      }
   
      
      
      
      	
  			
  		
  			
  		
	// если для элемента задано копирование картинок из другого элемента
	// если этот элемент выводится через тизер, определяем только первую картинку
	
	
	// если не пустое поле с источником картинок, 
	// значит нужно копировать картинки из другого элемента	
	if($vars['field_source_of_pics'][0]['view'])
	{
		if(strpos($vars['node']->type, 'product') !== FALSE)
		{
			//echo '-1 <br>';
			// определяем нид источника
			if($source_nid = db_result(db_query("SELECT nid FROM {uc_products} WHERE model = '%s'", $vars['field_source_of_pics'][0]['view'])))
			{
				//echo '-1 ' . $source_nid . ' <br>';
				
				// если нид определился, значит происходит копирование картинок из товара для товара
				$source_type = 'product';
				// загружаем все картинки из элемента-источника
				$source_fids = db_query("SELECT delta, field_product_img_fid, field_product_img_title, field_product_img_alt FROM {content_field_product_img} WHERE nid = %d", $source_nid);			
				//echo '-2 ' . $source_fids . ' <br>';
			}
			else  
			{
				// иначе похоже, что происходит копирование картинок для товара из другого типа элемента (магазин, отдел или какай-то другой тип места)
				// значит определяем нид из картинки из других таблиц, которые относятся к типам места
				$source_type = 'place';
				// определяем нид источника
				$source_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", $vars['field_source_of_pics'][0]['view']));
				// загружаем все картинки из элемента-источника
				$source_fids = db_query("SELECT delta, field_place_img_fid, field_place_img_title, field_place_img_alt FROM {content_field_place_img} WHERE nid = %d", $source_nid);
			}
		} // end of if(strpos($vars['node']->type, 'product') !== FALSE)
		else // если другой тип (не продукт)
		{
			// определяем нид источника
			if($source_nid = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", $vars['field_source_of_pics'][0]['view'])))
			{
				// если нид определился, значит происходит копирование картинок из элемента типа места в элемента типа места
				$source_type = 'place';
				// загружаем все картинки из элемента-источника
				$source_fids = db_query("SELECT delta, field_place_img_fid, field_place_img_title, field_place_img_alt FROM {content_field_place_img} WHERE nid = %d", $source_nid);
			}
			else 
			{
				// иначе похоже, что происходит копирование картинок для элемента типа места из элемента типа товар
				// значит определяем нид из картинки из других таблиц, которые относятся к типу товар (product...)
				$source_type = 'product';
				// определяем нид источника
				$source_nid = db_result(db_query("SELECT nid FROM {uc_products} WHERE model = '%s'", $vars['field_source_of_pics'][0]['view']));
				// загружаем все картинки из элемента-источника
				$source_fids = db_query("SELECT delta, field_product_img_fid, field_product_img_title, field_product_img_alt FROM {content_field_product_img} WHERE nid = %d", $source_nid);			
				
			}
	
		} // end of else // если другой тип (не продукт)
		
		//echo '---teaser ' . $vars['field_source_of_pics'][0]['view'] . '<br>';

		while($source_fid = db_fetch_array($source_fids))
		{

			//echo '-3 ' . $source_fid['field_product_img_fid'] . ' <br>';
	
			
			if($source_type == 'product')
			{
				// для тизера загружаем только первую (delta = 0) картинку из элемента-источника
				if($vars['teaser'] AND $source_fid['delta'] != 0)
					continue;
					
				$source_pic = db_result(db_query("SELECT filepath FROM {files} WHERE fid = %d", $source_fid['field_product_img_fid']));
				$vars['node']->field_product_img[$source_fid['delta']]['title'] = $source_fid['field_product_img_title'];
				$vars['node']->field_product_img[$source_fid['delta']]['alt'] = $source_fid['field_product_img_alt'];
				
				
				//echo '-4 ' . $source_type . ' <br>';
			
			}
			else
			{
				// для тизера загружаем только первую (delta = 0) картинку из элемента-источника
				if($vars['teaser'] AND $source_fid['delta'] != 0)
					continue;
					
				$source_pic = db_result(db_query("SELECT filepath FROM {files} WHERE fid = %d", $source_fid['field_place_img_fid']));
				$vars['node']->field_product_img[$source_fid['delta']]['title'] = $source_fid['field_place_img_title'];
				$vars['node']->field_product_img[$source_fid['delta']]['alt'] = $source_fid['field_place_img_alt'];
			}
			$vars['node']->field_product_img[$source_fid['delta']]['filepath'] = $source_pic;	
				
		} // end of while($source_fid = db_fetch_array($source_fids))

	} // end of if($vars['field_source_of_pics'][0]['view']) // если не пустое поле с источником картинок, 
 

 //} // end of
/* if($vars['node']->type == 'c_country' 
    OR $vars['node']->type == 'c_city' 
    OR $vars['node']->type == 'c_rajon' 
    OR $vars['node']->type == 'c_shop' 
    OR $vars['node']->type == 'c_department' 
 	OR $vars['node']->type == 'zp_brand' 
 	OR strpos($vars['node']->type, 'product') !== FALSE
 	)
*/
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 // выводим карту
 
 
 
 
 // сначала карту расположения магазина
 
 if($vars['node']->type == 'c_shop')
 {
 	
 	
 	//$test_xxx = zp_functions_shop_min_sum($cur_shop_nid, $user->uid);//$argument_shop_nid);
    //echo 'test_xxx = ' . $test_xxx . '<br>';
    //drupal_set_message('test_xxx = ' . $test_xxx, 'error');
    //$cur_shop_info['shop_min_sum'] = $test_xxx;
            
    //$cur_shop_info['shop_min_sum'] = zp_functions_shop_min_sum($c_nid, $user->uid);//$argument_shop_nid);

 	if($_SESSION['current_shop'])
 	{
 		if(!$_SESSION[$_SESSION['current_shop'] . '_shop_info']['shop_min_sum'])
 		{
 			$data = zp_functions_shop_min_sum($vars['node']->nid, $user->uid);
 			$_SESSION[$_SESSION['current_shop'] . '_shop_info']['shop_min_sum'] = $data['final_min_sum'];
 			
 		}
 		
 	}
 
 	
 	
 	
 	
	//$c_min_sum = uc_currency_format($_SESSION['c_shop_tids']['c_min_sum']);
	//$c_min_sum = $c_min_sum['c_min_sum'];
	//echo 'c_min_sum = ' . $c_min_sum;
 	
	
 	 // a simple GMap array
 if($vars['node']->locations[0]['latitude']) // если определены координаты, т.е. задано положение на карте
 {
    $vars['map'] = array(
    'id' => "city_map",         // id attribute for the map
    'width' => "100%",        // map width in pixels or %
    'height' => "300px",      // map height in pixels
    'latitude' => $vars['node']->locations[0]['latitude'], //41.9023,    // map center latitude
    'longitude' => $vars['node']->locations[0]['longitude'], //-87.5391,  // map center longitude
    'zoom' => 15,              // zoom level
    'maptype' => "Map",       // baselayer type
    'controltype' => "Small",  // size of map controls
    
    'behavior' => array(
      'locpick' => FALSE,
      'nodrag' => FALSE,
      'nokeyboard' => TRUE,
      //'overview' => TRUE,
      'scale' => TRUE,
      'collapsehack' => TRUE,
      //'autozoom' => TRUE,
      
    ),
    
    'markers' => array(
      array(
        'text' => 'First Marker',
        'latitude' => $vars['node']->locations[0]['latitude'], //41.9023,   
   		'longitude' => $vars['node']->locations[0]['longitude'], //-87.5391,
        'markername' => "lblue",
        //'offset' => 600,
        'autoclick' => TRUE,
        'tabs' => array(
    		'Адрес' => 'Магазин: ' . /*$_SESSION['c_shop_tids']['name']*/ $vars['title'] . '<br>Адрес: ' . $vars['node']->field_place_address[0]['value'],
    		'Мин.заказ' => $_SESSION[$_SESSION['current_shop'] . '_shop_info']['shop_min_sum'] ? 'Минимальная сумма заказа доставки <br>для этого магазина: <br>' . uc_currency_format($_SESSION[$_SESSION['current_shop'] . '_shop_info']['shop_min_sum']) : 'Доставка из этого магазина <br>для Вас пока не доступна. <br>Для справок обратитесь <br>в раздел "Помощь"',
  			),
      ),
      
      /*
      array(
        'text' => 'Second Marker',
        'latitude' => 50.056257082994534,
        'longitude' => 36.19600296020508,
        'markername' => "blue",
      ),
      */ 
      
     ), 
       
     'shapes' => array(

      	array(
        'type' => "circle",
        //'style' => array("000000", 3, 25, "ffff00", 45),
        'style' => array("0091ff", 1.5, 30, "ffff00", 0),
        //'color' => '#00dd00',
        //'fillopacity' => 0.2,
        'width' => 1,
        'radius' => 1,
        'center' => array($vars['node']->locations[0]['latitude'], $vars['node']->locations[0]['longitude']),
      	),
	  ),
 
  );
  
  
  
  // статическая картинка карты
   
    $static_map = 'http://maps.google.com/staticmap?center=';
    $static_map .= $vars['node']->locations[0]['latitude'].','.$vars['node']->locations[0]['longitude'];
    $static_map .= '&zoom=15&size=334x300&maptype=mobile';
    $static_map .= '&markers='.$vars['node']->locations[0]['latitude'].','.$vars['node']->locations[0]['longitude'].',blues';
    $static_map .= '&key='. variable_get('googlemap_api_key', ''); //keys_api_get_key('gmap', $_SERVER['HTTP_HOST']);
    //print '<a href="/map/'.$node->nid.'"><img src="'.$static_map.'" border="0" /></a><br /><br />';
    $vars['static_map_url'] = $static_map;
    $vars['static_map_img'] = '<img src="'.$static_map.'" border="0" />';
  
  
  
 } // end of if($vars['node']->locations[0]['latitude']) // если определены координаты, т.е. задано положение на карте
  
} // end of if($vars['node']->type == 'c_shop')
 
 
 
 
 
 
 
 
 
 
 
 // теперь выводим карту с расположением юзера
 
 if($vars['node']->type == 'u_hidden_i')
 {
	//$c_min_sum = uc_currency_format($_SESSION['c_shop_tids']['c_min_sum']);
	//$c_min_sum = $c_min_sum['c_min_sum'];
	//echo 'c_min_sum = ' . $c_min_sum;
 	//print '<PRE>';
	//print_r($_SESSION['c_shop_tids']);
	//print '</PRE>'; 
 	//$_SESSION['c_shop_tids']['c_min_sum']
 	
 	 // a simple GMap array
 if($vars['node']->locations[0]['latitude'])
 {
    $vars['map'] = array(
    'id' => "city_map",         // id attribute for the map
    'width' => "600px",        // map width in pixels or %
    'height' => "600px",      // map height in pixels
    'latitude' => $vars['node']->locations[0]['latitude'], //41.9023,    // map center latitude
    'longitude' => $vars['node']->locations[0]['longitude'], //-87.5391,  // map center longitude
    'zoom' => 15,              // zoom level
    'maptype' => "Map",       // baselayer type
    'controltype' => "Small",  // size of map controls
    
    'behavior' => array(
      'locpick' => FALSE,
      'nodrag' => FALSE,
      'nokeyboard' => TRUE,
      'overview' => TRUE,
      'scale' => TRUE,
    ),
    
    'markers' => array(
      array(
        'text' => 'First Marker',
        'latitude' => $vars['node']->locations[0]['latitude'], //41.9023,   
   		'longitude' => $vars['node']->locations[0]['longitude'], //-87.5391,
        'markername' => "blue",
        //'offset' => 600,
        'autoclick' => TRUE,
        'tabs' => array(
    		'Адрес' => $vars['node']->locations[0]['name'] . '<br>' 
            . $vars['node']->locations[0]['country_name'] . ', '
            . $vars['node']->locations[0]['postal_code'] . ', '
            . $vars['node']->locations[0]['province'] . ', '
            . $vars['node']->locations[0]['city'] . ', '
    		. $vars['node']->locations[0]['street'],
                    
    		//'Мин.заказ' => 'Минимальная сумма заказа доставки <br>для этого магазина: <br>' . uc_currency_format($_SESSION['c_shop_tids']['c_min_sum']),
  			), // end of 'tabs' => array(
  			
      	), // end of  array(
      
      /*
      array(
        'text' => 'Second Marker',
        'latitude' => 50.056257082994534,
        'longitude' => 36.19600296020508,
        'markername' => "blue",
      	),
      
      	*/
      
       ), // end of 'markers' => array(
       
       
     /*
     'shapes' => array(

      	array(
        'type' => "circle",
        'style' => array("000000", 3, 25, "ffff00", 45),
        'radius' => 0.07622248729082767,
        'center' => array($vars['node']->locations[0]['latitude'], $vars['node']->locations[0]['longitude']),
      	),
	  ),
	  
	  */
	  
 
  ); // end of  $vars['map'] = array(
  
  foreach ($vars['node']->field_multiref_list as $key => $place)
  {
  	
  	//$place_type = db_result(db_query("SELECT type from {node} WHERE nid = %d", $place['r_id'])); 
  	//echo 'place = ' . $place['r_text'] . ', r_id = ' . $place['r_id'] . ', type = ' . $place_type . '<br>';
  	
  	
  	
  	// найдём всю историю элемента и покажем её как название
  	$tids = taxonomy_node_get_terms_by_vocabulary($place['r_id'], 1); 

  	foreach($tids as $tid)
    	$tid = $tid->tid;
  	
  	$linage = array();
    for($count = 0; $tid != 0; $count++)
  	{
  		$tid_name = db_result(db_query("SELECT name from {term_data} WHERE tid = %d", $tid)); 
  		$linage[$count]['tid'] = $tid;
  		$linage[$count]['tid_name'] = $tid_name;
  			
  		if(!$linage['hint'])
  			$linage['hint'] = $tid_name;
  		else 
  			$linage['hint'] = $tid_name . '->' . $linage['hint'];
  				
  		$tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid = %d", $tid));
  	}
	
  	$linage['count'] = $count;
  			
  	//echo 'city = ' . $linage[$count - 4]['tid_name'] . '<br>';
  	//$address = 
  	//if($place_type == 'c_shop')
  	//{
  	
  	if($count - 4 >= 0)
  	{
  		if($city_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $linage[$count - 4]['tid'])))
  			$places[$city_nid][] = $linage; 
  	}
  	else 
  	{
  		//echo 'linage = ' . $linage . '<br>';

		
		$all_children[] = $linage[0]['tid'];
		for($i = 0; $i < 4 - $count; $i++)
		{
			$temp_children = array();
			foreach($all_children as $child)
			{
				
				$child_tids = db_query("SELECT tid from {term_hierarchy} WHERE parent = %d", $child);
				while($child_tid = db_fetch_array($child_tids))
				{
			  		$t['tid'] = $child_tid['tid'];
			  		$t['name'] = db_result(db_query("SELECT name from {term_data} WHERE tid = %d", $child_tid['tid']));
			  		
					$temp_children[] = $t;
			  		
				}	

			}
			$all_children = $temp_children;
		}
		
		foreach($all_children as $child)
		{
			$city_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $child['tid']));
			if(!$places[$city_nid])
			{
				$linage = array();
				$linage[0]['tid'] = $child['tid'];
				$linage[0]['tid_name'] = $child['name'];

				$linage['count'] = 4;
				//$linage['hint'] = 'Some hint';
				
				$places[$city_nid][] = $linage; 
			}
		}

  		
  	}
  	

  	
  	//echo 'nid = ' . $city_nid . '<br>';
  		
  	/*
  	if($place_type == 'c_shop')
  		$city_nid = $place['r_id'];
  	else
  	{
  		
  		
  	}
  	*/
  	
  	
  } // foreach ($vars['node']->field_multiref_list as $key => $place)
  

  	
  foreach($places as $city_nid => $linage)
  	if($place_lid = db_result(db_query("SELECT lid from {location_instance} WHERE nid = %d", $city_nid))) // location ID
  	{
  		$place_loc = db_fetch_array(db_query("SELECT latitude, longitude from {location} WHERE lid = %d", $place_lid));
  		
  		
		/* test
		$StartLat = 50.066506;
		$StartLong = 36.210444;
		$EndLat = 50.062484;
		$EndLong = 36.211538;
		*/

		
		$distance_data = zp_functions_mapdistance($vars['node']->locations[0]['latitude'], $vars['node']->locations[0]['longitude'], $place_loc['latitude'], $place_loc['longitude']);
		
		$distance_data['distance']  = round($distance_data['distance'] , 2) . ' км';
    	$distance_data['bearing']  = round($distance_data['bearing'] , 2) . ' градусов';
		
    	if($max_distance < $distance_data['distance'])
    		$max_distance = $distance_data['distance'];
    	
		$place_loc['markername'] = 'route';
  		
  		$address = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid = %d", $city_nid));
	
  		//$place_loc['text'] = $place['r_text'];
  		foreach($linage as $city)
  		{
  			//echo 'place_loc[text] = ' . $place_loc['text'] . '<br>'; 
  			if(!$place_loc['text'])
  			{
 				if($city['hint'])
  					$place_loc['text'] = 'Торговое заведение: ' . $city[$city['count'] - 4]['tid_name'] . '<br> Адрес: <br>' . $address . '<br>Разрешённый доступ: <br> - ' . $city['hint'] . '<br>';
  				else 
  					$place_loc['text'] = 'Торговое заведение: ' . $city[$city['count'] - 4]['tid_name'] . '<br> Адрес: <br>' . $address. '<br>';
  			}
  			else 
  			{
  				if($city['hint'])
  					$place_loc['text'] = $place_loc['text'] . '- ' . $city['hint'] . '<br>';
  				else 
  					$place_loc['text'] = $place_loc['text'] . '<br>';
  			}
  		}
  		
  		$place_loc['text'] = $place_loc['text'] . '<br>Расстояние до клиента: ' . $distance_data['distance'] . '<br>Азимут: ' . $distance_data['bearing'];
  		$vars['map']['markers'][] = $place_loc;
 		
  	}
  	
  	
  	//$max_distance = 2;
  	$vars['map']['zoom'] = (16 - $max_distance/2.9);
	//$vars['map']['zoom'] = 13;
	//echo 'max_distance = ' . $max_distance . '<br>';
	//echo 'zoom = ' . $vars['map']['zoom'];
  	//}
  	
 } // end of if($vars['node']->locations[0]['latitude'])
  
  
  
} // end of  if($vars['node']->type == 'u_hidden_i') // теперь выводим карту с расположением юзера
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
  //  копирование описание элементов из других элементов, если в поле с описанием указан артикул источника в соответствующем формате
 
  // если в поле элемента с описанием магазина или отдела задано копирование из другого элемента
  // копируем описание из указанного элемента в поле описания текущего элемента
  if(
  	  ($vars['node']->type == 'c_shop' OR $vars['node']->type == 'c_department')
  		AND
  	  (strpos($vars['node']->field_description[0]['value'], 'copy *z') !== FALSE)
    )
    {
 		//print '<PRE>';
		//print_r($vars);
		//print '</PRE>';
 	
 		//$node->field_description[0]['value'] // так выглядит переменная уже в шаблоне
 	
 		//echo 'descr = ' .  $vars['node']->field_description[0]['value'];
 		
		$source = explode('copy *', $vars['node']->field_description[0]['value']);
		$source = $source[1];
 		
		// обнуляем поле, которое будет выводится пользователю
		// на тот случай, если вдруг описание из заданного источника не будет получено
		// чтобы вдруг пользователю не была показана вместо описания строка типа copy *z342343523
		$vars['node']->field_description[0]['value'] = ''; 
		$vars['node']->field_description[0]['view'] = '';
 		
		if($source = db_result(db_query("SELECT nid FROM {content_field_zp_art_place} WHERE field_zp_art_place_value = '%s'", $source)))
		{
			// если найден нид источника описания
			$vars['node']->field_description[0]['value'] = db_result(db_query("SELECT field_description_value FROM {content_field_description} WHERE nid = %d", $source));
			$vars['node']->field_description[0]['view'] = $vars['node']->field_description[0]['value'];
		}

  	 } // end of if($vars['node']->type == 'c_shop' OR $vars['node']->type == 'c_department' AND strpos($vars['node']->field_description[0]['value'], 'copy *z') !== FALSE)
 
     

    
  // если в поле продукта с описанием задано копирование из другого элемента
  // копируем описание из указанного элемента в поле описания текущего элемента
  if(
 	  (strpos($vars['node']->type, 'product') !== FALSE)
 		AND 
 	  (strpos($vars['node']->field_product_descr[0]['value'], 'copy *z') !== FALSE)
 	)
    {
	
 		//$node->field_product_descr[0]['view'] // так выглядит переменная уже в шаблоне
 	
 		//echo 'descr prod = ' .  $vars['node']->field_product_descr[0]['value'];
 		
		$source = explode('copy *', $vars['node']->field_product_descr[0]['value']);
		$source = $source[1];
 		
		//echo 'source = ' . $source . '<br>';
		
		// обнуляем поле, которое будет выводится пользователю
		// на тот случай, если вдруг описание из заданного источника не будет получено
		// чтобы вдруг пользователю не была показана вместо описания строка типа copy *z342343523
		$vars['node']->field_product_descr[0]['value'] = ''; 
		$vars['node']->field_product_descr[0]['view'] = ''; 
 		
		
		if($source = db_result(db_query("SELECT nid FROM {uc_products} WHERE model = '%s'", $source)))
		{
			// если найден нид источника описания
			$vars['node']->field_product_descr[0]['value'] = db_result(db_query("SELECT field_product_descr_value FROM {content_field_product_descr} WHERE nid = %d", $source));
			$vars['node']->field_product_descr[0]['view'] = $vars['node']->field_product_descr[0]['value']; 
		}

  	 } // end of if($vars['node']->type == 'c_shop' OR $vars['node']->type == 'c_department' AND strpos($vars['node']->field_description[0]['value'], 'copy *z') !== FALSE)
 
 

 	
 	
 	
 	
 	
 
 
 
 
 
 
 
 
 

 

 
 
 
 
 if($vars['node']->type == 'zp_user')
 {
 	//if($vars['node']->nid == 73) // Нода настроек пользователя (пароль, почта, время и т.д.)
  	if($vars['node']->nid == MY_USER_SETTINGS_NODE_NUM) // Нода настроек пользователя (пароль, почта, время и т.д.)
 	{ 
  		//$vars['u_settings'] = zp_user_settings_page(); //u_settings_page(); 
 		
  		if($_GET['t'] == 'cp') //if change password (after reset)
  			$vars['u_settings'] = zp_user_settings_page('change_pass_after_reset'); //u_settings_page(); // изменение пароля после его сброса (текущий пароль не запрашивается)
  		else
  			$vars['u_settings'] = zp_user_settings_page('usual_settings'); //u_settings_page(); // обычное изменение данных пользователя (а не восстановление пароля)
	}
	
	
	
	
	if($vars['node']->nid == MY_PASSREMIND_NODE_NUM) // Нода настроек пользователя (пароль, почта, время и т.д.)
 	{ 
  		//echo 'arg(2) = ' . arg(2);
  		
 		if(arg(2) == 'reset')
 		{
 									//zp_user_settings_passremind_reset($uid, $timestamp, $hashed_pass, $action = NULL)
 			$vars['pass_remind'] = zp_user_settings_passremind_reset(arg(3), arg(4), arg(5), arg(6));
 			
 		}
 		else 
 		if($_GET['passreminded'])
  		{
	  		$vars['pass_remind'] = '<br><br><br>Дальнейшие инструкции по восстановлению забытого Вами пароля отосланы на Ваш емейл. <br><br>' . l('Вернуться', $_GET['xdestination']) . ' на последнюю посещённую Вами страницу.';
  		}
  		else
 			$vars['pass_remind'] = '<br><br><br>Для восстановления забытого пароля, пожалуйста, введите свой логин (ник) или e-mail, указанные Вами при регистрации на нашем сайте.<br><br>' . drupal_get_form('zp_user_settings_passremind');
	}
	
	
	
	
	//if($vars['node']->nid == 225) // Нода комментариев... Если не переопределять, комментарии выпадают из панелей
	if($vars['node']->nid == MY_ZP_COMMENTS_NODE_NUM) // Нода комментариев... Если не переопределять, комментарии выпадают из панелей
  	{ 
  		
  		//comment/reply/$node->nid
  		
  		//comment_reply($nid, $pid);
  		//echo 'arg(2) = ' . arg(2);
  		
  		if(arg(2) == 'reply')
  		{

  			$u_comments = comment_reply(arg(3), arg(4));
  			//$u_comments = ereg_replace('node/225/reply', 'comment/reply', $u_comments);  
  			$u_comments = ereg_replace(MY_ZP_COMMENTS_NODE . '/reply', 'comment/reply', $u_comments);  
  			
  			$vars['title'] = 'Ваш ответ на комментарий...';
  		
  		}	
  				
  		if(arg(2) == 'edit_comment')
  		{
			//echo '6666666arg(2) = ' . arg(2);
  			$u_comments = comment_edit(arg(3));
  			//$u_comments = ereg_replace('node/225/edit_comment', 'comment/edit', $u_comments);  
  			$u_comments = ereg_replace(MY_ZP_COMMENTS_NODE . '/edit_comment', 'comment/edit', $u_comments);  
  			
  			$vars['title'] = 'Редактирование комментария...';
  			$vars['u_body'] = '<br><br>На этой странце Вы можете отредактировать комментарий, введённый Вами ранее.
<br>Пожалуйста, внесите изменения и нажмите на кнопку "Отправить комментарий".';

  		}
  		
  		if(arg(2) == 'delete_comment')
  		{
			//echo '6666666arg(2) = ' . arg(2);
  			$u_comments = comment_delete(arg(3));
  			//$u_comments = ereg_replace('node/225/delete_comment', 'comment/delete', $u_comments);
  			$u_comments = ereg_replace(MY_ZP_COMMENTS_NODE . '/delete_comment', 'comment/delete', $u_comments);
  			
  			$vars['title'] = 'Удаление комментария...';

  		}
  		
  		//$u_comments = ereg_replace('node/225', 'comment', $u_comments);  
		
  		$vars['u_comments'] = $u_comments;
	}
 

 }
  
  
  
  
  
//if($vars['node']->type == 'zp_cart' AND $vars['node']->title == 'Zp cart cart')
if($vars['node']->type == 'zp_cart')
 {
 	 	
     // если показываем корзину (на любом этапе, кроме complete, так как там уже продукты не показываются), то показываем и название магазина
 	 //if($vars['node']->nid == 23 OR $vars['node']->nid == 24 OR $vars['node']->nid == 25)
 	 if($vars['node']->nid == MY_CART_NODE_NUM OR $vars['node']->nid == MY_CART_CHECKOUT_NODE_NUM OR $vars['node']->nid == MY_CART_REVIEW_NODE_NUM)
 	 
 	 {
 	 	
 	 	// считаем, что номер корзины в базе равен номеру юзера
        $c_shop_tids = zp_functions_get_cart_shop_data($user->uid, $user->uid);
        
        
        
		/* 	 	
        if($c_nid = db_result(db_query("SELECT nid FROM {uc_cart_products} WHERE cart_id = %d", $cid)))   // т.е. если корзина не пуста
        {   
         if(!($c_shop_tids = $_SESSION['c_shop_tids'])) // если не определена сессионная переменная с тидами магазина корзины, пытаемся её определить
      	 {  
      	 	
      	 	// если корзина при этом не пустая, то определяем сессионную переменную $_SESSION['c_shop_tids'] и название магазина в ней


            $c_tids = taxonomy_node_get_terms_by_vocabulary($c_nid, 1); 
      		foreach($c_tids as $c_tid)
          		$c_tid = $c_tid->tid;
	    	
            
            $term_name = db_result(db_query("SELECT name FROM {term_data} WHERE tid = %d", $c_tid));
            
 	   		// задаём массив тидов продукта и первым элементом делаем терм самого продукта
        	$linage_c_tids = array($c_tid);
        
        	$count = 1;
        	while(($c_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $c_tid))) != 0)
	         {
    	       $linage_c_tids[] = $c_tid;
        	   $count++;

         	 } 
			

			// определим данные по текущему магазину
			$c_shop_tids = zp_functions_shop_info($linage_c_tids[$count-4], $user->uid);

			// сохраняем данные в сессионной переменной  
			$_SESSION['c_shop_tids'] = $c_shop_tids;

    	
    	  } // end of if(!($c_nid = db_result(db_query("SELECT nid FROM {uc_cart_products} WHERE cart_id = %d", $cid))))   // т.е. если корзина пуста
        
        } // end of  if(!($c_shop_tids = $_SESSION['c_shop_tids'])) // если не определена сессионная переменная с тидами магазина корзины, пытаемся её определить
 	 	
        */
        
        
        
        
        
        
        
        if($c_shop_tids) // если сессионная переменная определена (а к этому моменту она должна быть определена) (т.е. в корзине есть продукты), значит создаём переменную для вывода в шаблоне с названием магазина
        {
          $vars['cart_shop_name'] = '<div class="crt_shop_name">' . l($c_shop_tids['shop_name'], 'node/'. $c_shop_tids['shop_nid']) . '</div><div class="crt_shop_address">' . $c_shop_tids['shop_address'] . '</div>';
          
          //zp_functions_show($c_shop_tids);
          
          // и добавим к этой переменной стоимость мин. покупки в этом магазине
          // хотя позже лучше сделать вывод этого значения в шаблоне через другую переменную шаблона
          $vars['cart_shop_name'] .= '<div class="crt_min_sum">Желательная минимальная сумма покупки (корзины) в этом заведении (без учёта стоимости доставки), которая определена для Вас: ' . uc_currency_format($c_shop_tids['shop_min_sum']) . '. При заказе на меньшую сумму стоимость доставки равна минимальной стоимости обычной (не срочной) доставки для этого заведения с учётом возможных дополнительных коэффициентов (срочная доставка, особые условия доставки и т.д.).' . '</div>'; 
		  
          // добавим ссылку на описание формирования стоимости доставки и минимальной суммы корзины
          $vars['cart_shop_name'] .= '<div class="info">Узнайте больше <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#stoimost-i-min-summa-korziny">о стоимости доставки и минимальной сумме корзины</a>...</div><br>';
        } 
 	 	
         
         
 	 }
 
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	
 	//if($vars['node']->nid == 23) // cart
 	if($vars['node']->nid == MY_CART_NODE_NUM) // cart
     {
       //drupal_set_message("----cart cart--------node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');
       $vars['zp_cart_cart'] = uc_cart_view();
     }

    //if($vars['node']->nid == 24) // checkout
    if($vars['node']->nid == MY_CART_CHECKOUT_NODE_NUM) // checkout
     {
       //drupal_set_message("----cart checkout--------node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');
       $vars['zp_cart_cart'] = uc_cart_checkout();
     }

    //if($vars['node']->nid == 25) // review
    if($vars['node']->nid == MY_CART_REVIEW_NODE_NUM) // review
     {
       //drupal_set_message("----cart checkout--------node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');
       $vars['zp_cart_cart'] = uc_cart_checkout_review();
     }

    //if($vars['node']->nid == 26) // complete
    if($vars['node']->nid == MY_CART_COMPLETE_NODE_NUM) // complete
     {
       //drupal_set_message("----cart checkout--------node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');
       $vars['zp_cart_cart'] = uc_cart_checkout_complete();
     }

     
     // добавляем "грубым образом" название магазина на странице
     // но потом надо будет правильно выводить в шаблоне название через эту переменную $vars['cart_shop_name'] 
     $vars['zp_cart_cart'] = $vars['cart_shop_name'] . $vars['zp_cart_cart'];
     
     
     
     
     
     
    // обработка заказов (просмотр, удаление, редактирование, копирование в корзину и т.д.)
    
    if($vars['node']->nid == MY_ORDER_HISTORY_NODE_NUM) // orders history list
     {
       //drupal_set_message("----cart checkout--------node_title = $node_title, node_id = $node_id, node_type = $node_type", 'error');
       $vars['zp_orders_history'] =  uc_order_history($user->uid);
      }

    if($vars['node']->nid == MY_ORDER_REVIEW_NODE_NUM) // Операции с заказами: просмотре, удаление, возврат в корзину и т.д. ..... order view/status update/copy-to-cart/return-to-cart (add or replace cart with this order's items)
     {

       //$vars['zp_order_view'] =  uc_order_view(arg(3), 'view'); // так добавляет таблицы для админских комментариев         

       //if(arg(4) == 'delete')
       // меняем способ вызова на через переменные командной строки
       if($_GET['a'] == 'delete' AND $user->uid == 1) 
       {
               
       	//$vars['zp_order_action'] = '<div class="del">Удаление заказа №' . arg(3) . '...</div>' . drupal_get_form('uc_order_delete_confirm_form', arg(3));
       	// меняем способ вызова на через переменные командной строки
       	$vars['zp_order_action'] = '<div class="del">Удаление заказа №' . $_GET['num'] . '...</div>' . drupal_get_form('uc_order_delete_confirm_form', $_GET['num']);
       }
       //else if (arg(4) == 'edit') 
       // меняем способ вызова на через переменные командной строки
       else if($_GET['a'] == 'edit' AND $user->uid == 1) 
       {
       	 //$vars['zp_order_action'] =  uc_order_edit(arg(3));
       	 // меняем способ вызова на через переменные командной строки
       	 $vars['zp_order_action'] =  uc_order_edit($_GET['num']);
       }
       else 
       {
       	if($user->uid != 1)
        	 //$vars['zp_order_action'] =  uc_order_view(arg(3), 'customer', arg(4)); // а так не добавляет
        	 // меняем способ вызова на через переменные командной строки
        	 $vars['zp_order_action'] =  uc_order_view($_GET['num'], 'customer', $_GET['a']); // а так не добавляет
       	else 
	         //$vars['zp_order_action'] =  uc_order_view(arg(3), 'view', arg(4));
	         // меняем способ вызова на через переменные командной строки
        	 $vars['zp_order_action'] =  uc_order_view($_GET['num'], 'view', $_GET['a']);
       }
        
      }
      
      
      
     // редактировать заказ может только администратор
     if($vars['node']->nid == MY_ORDER_EDIT_NODE_NUM ) // orders change
     {
     	if($user->uid != 1)
     		$vars['zp_order_change'] = 'У Вас недостаточно прав для редактирования заказов!';
    	else 
     	{
     		switch($_GET['a'])
     		{
     			

     				
     			case 'set-uptodate-prices-in-order':
     				
     				$vars['zp_order_change'] = zp_order_change_set_uptodate_prices_in_order($_GET['num']);
     				//return 'http://www.zapokupkami.com/' . drupal_get_path_alias(MY_ORDER_REVIEW_NODE) . '?num=' . $_GET['num'] . '&a=view';
     				
     				//$vars['zp_order_change'] =  zp_order_change_view($_GET['num']);
     				$vars['change_order_id'] = $_GET['num'];
     				
     				drupal_goto('http://www.zapokupkami.com/' . drupal_get_path_alias(MY_ORDER_REVIEW_NODE) . '?num=' . $_GET['num'] . '&a=view');
     				
     				break;
     			
     			case 'start-change-order': 
     				// первый переход на страницу, тогда запускаем проверку на изменившиеся цены в основной базе
     		
     				//$vars['zp_order_change'] =  zp_order_change_view(arg(3)); // формат url /node/248/change-order/456 где 68 - нода редактирования, 456 - номер заказа
     				// меняем способ вызова на через переменные командной строки
     				$vars['zp_order_change'] =  zp_order_change_view($_GET['num'], 'start');

    				//$vars['change_order_id'] = arg(3);
     				// меняем способ вызова на через переменные командной строки
     				$vars['change_order_id'] = $_GET['num'];

     			
     				break;	
     				
     			default:
     				
     			case 'change-order':
     		
     				//$vars['zp_order_change'] =  zp_order_change_view(arg(3)); // формат url /node/248/change-order/456 где 68 - нода редактирования, 456 - номер заказа
     				// меняем способ вызова на через переменные командной строки
     				$vars['zp_order_change'] =  zp_order_change_view($_GET['num']);

    				//$vars['change_order_id'] = arg(3);
     				// меняем способ вызова на через переменные командной строки
     				$vars['change_order_id'] = $_GET['num'];

     			
     				break;
     			
     				
     		} // end of switch
     		
     	} // end of else
     	
     	
     }
      
      

 }

 
 
 
 
 
 
 
 
 
 
 
 
 
 // ----------------------------------------------------------------------------------------
 
 
 
 
 // если выводится (рендерится) нода типа product_
  if(strpos($vars['node']->type, 'product') !== FALSE) // если любые ноды с типом, в названии которого содержится poroduct
  {
 	//echo 'u='.$user->uid.'<br>';
    $show_price = -1;

    $zp_default_set = zp_functions_get_zp_default_set();
    
	//echo '---------------------zp_default_set[show_costly] = ' . $zp_default_set['show_costly'] . '<br>';
    //echo '---------------------zp_default_set[costly_level] = ' . $zp_default_set['costly_level'] . '<br>';
  		    
    //zp_functions_show($zp_default_set);
    
    if($vars['node']->sell_price > $zp_default_set['costly_level'])
    	$vars['is_costly'] = 1;
    	
    $vars['show_costly'] = $zp_default_set['show_costly']; 
    $vars['u_costly'] = $zp_default_set['u_costly']; // флаг, показывающий, что юзер имеет персональные настройки по дорогому товару
 	if(//!$user->uid 
 		
 		//OR 
 		(
	 		$user->uid != 1
	 		AND
 			$vars['is_costly'] == 1	
	 		AND 
 			($zp_default_set['show_costly'] == 3 OR $zp_default_set['show_costly'] == 4) 
 		)
 		
 		
 	  )
 	{
 	 	// не показывать цену и возможность добавить в корзину незарегистристрированному пользователю
 	 	// или если товар дорогой и для него задан флаг не показывать цену
 		$vars['show_price'] = -2; 
 	}
 	else 
 	{
 	  
 	
 	  // если пользователь зарегистрирован, проверяем, можно ли ему показывать цену и разрешать ли добавлять данный конкретный товар в корзину	
 	  // это зависит от его привилегий
 	  
 	  
 	  if(!($vars['show_price'] = $_SESSION['price-u' . $user->uid . '-' . arg(1)]) OR $_SESSION['masquarade']) // если в переменной сессии ne сохранено значение флага цены для текущей строки ссылки (текущей ноды, предполагая, что это нода родительского отдела) в браузере, значит используем его, а не пересчитываем всё заново
 	  {
 	  	
 	  	  
   	  	  // найдём терм текущего продукта. Тут я его обозвал parent, но это может быть как перент (если мы находимся на странице 
 	  	  //со списком продуктов и главным на странице является отдел), так и сам продукт, если находимся на странице конкретного продукта
          $parent_tid = db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", arg(1)));
 	  	
 	  	  // найдём все родительские термы текущего продукта вплоть до страны, не забудем также и текущий терм
 	  	  $count = 0;
          $linage_tids = array($parent_tid); 
          while(($parent_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $parent_tid))) != 0)
           {
             $linage_tids[] = $parent_tid;
             $count++;
           } 
        
          //$user_shops_data = $_SESSION['user_shops_data']; // привилегии клиента, доступность для него отделов, магазинов, районов, городов, стран
          $user_shops_data = zp_functions_get_user_shops_data(); // привилегии клиента, доступность для него отделов, магазинов, районов, городов, стран
          
          $show_price = -3; // значит клиент зарегистрирован, но просто не имеет доступа к данному магазину/отделу
          
          if(is_array($user_shops_data))
              foreach($user_shops_data as $tid => $value)
              {
                    if(in_array($tid, $linage_tids)) // если тид из привилегий пользователя находится в последовательности термов продукта
                    {
                      $show_price = 1; // значит данный клиент имеет право видеть цену и добавлять данный продукт в корзину
                      break;
                    } 
              }
          
          $vars['show_price'] = $show_price; // определяем переменную для макета
          
          //if(!$_SESSION['masquarade'])
          	$_SESSION['price-u' . $user->uid . '-' . arg(1)] = $show_price; // сохраняем результаты в переменной сессии, чтобы потом заново не пересчитывать
                  
 	   } // end of else of if($_SESSION['price'.arg(1)])

 	  
 	} // end of else of if(!$user->uid)
 	 
 	if($vars['show_price'] > 0) // esli mozhno pokazivat cenu
 	{
 		// вычисляем коэффициент доставки
        // равный перемноженным коэффициентам страна*город*район*магазин*отдел(ы)*клиент-общий*клиент-по-всей-иерархии-от-отдела-до-страны
        // выясним всю последовательность до страны, затем тиды страны, города, района и магазина
 	   
 	  	$parent_otdel = zp_functions_get_parent_otdel($vars['node']->nid);
 	   
		// определение коэффициента доставки для конкретного юзера и конкретного отдела (с учётом коэф. доставки всех его родительских отделов)  	   
 	    // определить factor dostavki $d_factor_otdel dlia otdela
 	   	//$d_factor_otdel = zp_functions_d_factor_otdel($node_tid, $parent_otdel_nid, $user->uid);
 	   	$d_factor_otdel = zp_functions_d_factor_otdel($parent_otdel['tid'], $parent_otdel['nid'], $user->uid);
 	   
 	   	 
 	   	//$vars['show_price'] = '+доставка ' . uc_currency_format(round($vars['sell_price']*$d_factor_otdel, 2)) . '<div class="dost_descr">('. $d_factor_otdel*100 . '% стоимости товара)</div>';
 	   	$vars['show_price'] = uc_currency_format(round($vars['sell_price']*$d_factor_otdel, 2));
 	   	$vars['dost_descr'] = 'Стоимость доставки для Вас = '. $d_factor_otdel*100 . '% от стоимости товара';
 	
 		
 	} // end of if($vars['show_price'] > 0)
 	else 
 	{
 	  //$vars['show_price'] = 'dostavka dlia vas ne dostupna';	
 		
 	}

 	
 } // end of if($vars['node']->type == 'product_clothes' or $vars['node']->type == 'product_toys')
 
 
  return $vars;
}













// a nuzhny li blocki?


/*
if ($hook == 'block') {
    
//print '<pre>';
//print_r(get_defined_vars());
//print '</pre>';



    if( $vars['block']->module == 'block' AND $vars['block']->delta == 1)
      {
         //$vars['ntax'] = theme('nice_tax_menu', nice_tax_menu_build_items(1, 1), array('class' => 'ddmenu'));
         
         // ��������� ������� nice_tax_menu_build_items(�������, � ������ ������ ����� ���������� ����, ������������ �������, ���������� ������ ������������ �����)
         if($items = nice_tax_menu_build_items(1, 'department', 1, 1))
           $vars['block']->content = theme('nice_tax_menu', $items, array('class' => 'ddmenu'));

         return $vars;
      }

    if( $vars['block']->module == 'block' AND $vars['block']->delta == 2) // ���� ��� �����
      {
         //$vars['ntax'] = theme('nice_tax_menu', nice_tax_menu_build_items(1, 1), array('class' => 'ddmenu'));
         
         if($items = nice_tax_menu_build_items(1, 'shop', 0, 0))
            $vars['block']->content = theme('nice_tax_menu', $items, array('class' => 'ddmenu'));

         return $vars;
      }



    if( $vars['block']->module == 'block' AND $vars['block']->delta == 3) // ����������
      {
         
         $vars['block']->content = catalog_breadcrumb();

         return $vars;
      }


 }
 
 
*/





  return array();
}














function main_page_breadcrumb()
{
	//echo 'xxx';
	$data = zp_functions_continue_shopping_link();

if($node_tid = $data['tid']) // если был последний переход по каталогу
{
	$node_nid = $data['nid'];
	
	$tid_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = '%s'", $node_tid));
	
  	$linage_tids = array();
  
  	$linage_tids[] = l($tid_name, 'node/'.$node_nid);
  	//$linage_tids[] = t($tid_name); // ссылку на последний текущий элемент не показываем, так как у него нет своей родительской ноды... родительская нода есть только у следующего родителя-терма
 
  	$count = 0;
  	while(($node_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $node_tid))) != 0)
    {
      $node_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid  = '%s'", $node_tid));
      $tid_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = '%s'", $node_tid));

      if($node_nid)
      	//$linage_tids[] = l($tid_name, 'node/'.$node_nid);
      	$linage_tids[] = l($tid_name, 'node/'.$node_nid);//, array('rel' => 'nofollow'));
      else
      	$linage_tids[] = t($tid_name);
      $count++;
    } 

//array_splice($linage_tids, -2, 2); // ������� ����� �� ����� � ������


  //drupal_set_message("bread Count = $count, linage_tid = $linage_tid", 'error');
  //for($count = $count-2; $count >= 0; $count--)
  $c_breadcrumb = '';
  for(; $count >= 0; $count--)
   $c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count];


//drupal_set_message("c_breadcrumb = $c_breadcrumb", 'error');

//if($c_breadcrumb)

  $c_breadcrumb = '<div id="c_breadcrumb"><div id="title">История последнего перехода</div>' . $c_breadcrumb . '</div>';
  //$c_breadcrumb = '<div id="c_breadcrumb"><div id="title">Сейчас Вы здесь</div>' . $c_breadcrumb . '</div>';
  //$c_breadcrumb = '<div id="c_breadcrumb"><div id="title">Быстрый переход</div>' . $c_breadcrumb . '</div>';
}
else  
  //$c_breadcrumb = '<div id="c_empty_breadcrumb"></div>';  
  $c_breadcrumb = '<div id="c_breadcrumb"><div id="title">Главная страница</div>' . ' >> ' . '</div>';
  
  
return $c_breadcrumb;
	
	
}









function catalog_breadcrumb()
{


//$arg1 = arg(1);
//drupal_set_message("arg1 = $arg1", 'error');

  $linage_tids = array();
  
  $node = node_load(arg(1));
  
  // проверим, к какому меню относится элемент и установим соотвествующий словарь
if($node->type == 'blog_zp_post')
{
	
	$linage_tids[] = l($node->title, 'node/'.$node->nid);//, array('rel' => 'nofollow'));
	// найдём родителя (сам блог), предполагая, что такого типа нода на сайте только одна
	$blog_data = db_fetch_array(db_query("SELECT nid, title from {node} WHERE type = '%s'", 'blog_zp'));
	$linage_tids[] = l($blog_data['title'], 'node/'.$blog_data['nid']);//, array('rel' => 'nofollow'));
	$linage_tids[] = l(t('Главная'), 'http://zapokupkami.com');//, array('rel' => 'nofollow'));
	
	//foreach($linage_tids as $linage_tid)
   		//$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tid;
   	$c_breadcrumb = ' >> ' . $linage_tids[2] . ' >> ' . $linage_tids[1] . ' >> ' . $linage_tids[0];
}
else
if($node->type == 'news_post')
{
	
	$linage_tids[] = l($node->title, 'node/'.$node->nid);//, array('rel' => 'nofollow'));
	// найдём родителя (сам блог), предполагая, что такого типа нода на сайте только одна
	$blog_data = db_fetch_array(db_query("SELECT nid, title from {node} WHERE type = '%s'", 'news'));
	$linage_tids[] = l($blog_data['title'], 'node/'.$blog_data['nid']);//, array('rel' => 'nofollow'));
	$linage_tids[] = l(t('Главная'), 'http://zapokupkami.com');//, array('rel' => 'nofollow'));
	
	//foreach($linage_tids as $linage_tid)
   		//$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tid;
   	$c_breadcrumb = ' >> ' . $linage_tids[2] . ' >> ' . $linage_tids[1] . ' >> ' . $linage_tids[0];
}
else
if($node->type == 'zp_issue')
{
	$linage_tids[] = l($node->title, 'node/'.$node->nid);//, array('rel' => 'nofollow'));
  	$linage_tids[] = 'Статьи';
  	$linage_tids[] = 'Полезно';
  	$count = 2;
  	
  	while( $count >= 0)
  	  if($linage_tids[$count] AND $linage_tids[$count] != '')
   		$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count--];
}
else
if($node->nid == MY_CART_COMPLETE_NODE_NUM)
{
	$linage_tids[] = 'Отчёт об отправке заказа на рассмотрение';
  	$linage_tids[] = 'Ваша корзина';
  	$linage_tids[] = 'Личное меню';
  	$count = 2;
  	
  	while( $count >= 0)
  	  if($linage_tids[$count] AND $linage_tids[$count] != '')
   		$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count--];
}
else
if($node->nid == MY_ORDER_EDIT_NODE_NUM)
{
	$linage_tids[] = 'Редактирование заказа';
  	$linage_tids[] = 'Список заказов';
  	$linage_tids[] = 'Личное меню';
  	$count = 2;
  	
  	while( $count >= 0)
  	  if($linage_tids[$count] AND $linage_tids[$count] != '')
   		$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count--];
}
else
{
 
	//print '<PRE>';
	//print_r($node->taxonomy);
  	//print '<PRE>';
  	
  	foreach($node->taxonomy as $taxonomy)
  	{
  		if(($vid = $taxonomy->vid) == 1)
  		 break;
  		
  	}
	
  
  
  //$current_node_tid = taxonomy_node_get_terms_by_vocabulary($node->nid, 1);
  $current_node_tid = taxonomy_node_get_terms_by_vocabulary($node->nid, $vid);
  
  foreach($current_node_tid as $node_tid)
    {
     $tid_name = $node_tid->name;
     $node_tid = $node_tid->tid;
    }
  
  if($node->nid)
  	$linage_tids[] = l($tid_name, 'node/'.$node->nid);//, array('rel' => 'nofollow'));
  else
  	$linage_tids[] = t($tid_name); // ссылку на последний текущий элемент не показываем, так как у него нет своей родительской ноды... родительская нода есть только у следующего родителя-терма

  $first_tid = $node_tid;
  $first_tid_name = $tid_name;
  
  $count = 0;
  while(($node_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $node_tid))) != 0)
    {
 
      $node_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid  = '%s'", $node_tid));
      $tid_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = '%s'", $node_tid));
	
      if($node_nid)
  		$linage_tids[] = l($tid_name, 'node/'.$node_nid);//, array('rel' => 'nofollow'));
  	  else
  		$linage_tids[] = t($tid_name);
      
  		
  	  if(!$count)
  	  {
      	$first_parent_tid = $node_tid;
      	$first_parent_nid = $node_nid;
      	$first_parent_name = $tid_name;
  	  }
      	
  	  $count++;
    } 
    
    
    
    //array_splice($linage_tids, -2, 2); // ������� ����� �� ����� � ������


  //drupal_set_message("bread Count = $count, linage_tid = $linage_tid", 'error');
  //for($count = $count-2; $count >= 0; $count--)
  
  if($vid == 1)	
  {
  	for($count = $count-0; $count >= 0; $count--)
  	{
   		if($count > 0)
  			$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count];
  		else 
  		{
  			if(strpos($node->type, 'product') !== FALSE)
   			{
   				$c_breadcrumb = $c_breadcrumb . ' >> ' . '<a href="' . url('node/'.$first_parent_nid, 'tf='. $first_tid) . '">' . $first_tid_name . '</a>'; 
   				$c_breadcrumb = $c_breadcrumb . ' >> ' . t($node->title);
   				//$_SESSION['cur_parent_otdel_name'] = $first_parent_name; // сохраняем текущий родительский отдел товара для использования его позже в мета-описании (чтобы заново не находить)
   			}
   			else 
   				$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count];
  		}
   		
  	}
   		
   	if($_GET['tf'] > 0)
   	{
   		$tid_name = db_result(db_query("SELECT name from {term_data} WHERE tid  = '%s'", $_GET['tf']));
   		
   		//$c_breadcrumb = $c_breadcrumb . ' >> ' . l($tid_name, "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
   		//ссылка на текущую страницу тут как раз не нужна
   		$c_breadcrumb = $c_breadcrumb . ' >> ' . t($tid_name); 
   	}
   	
   		
  }
  else
  {
  	// добавим корневой элемент бредкрамба - название каталога или известное название корневого кабинета
  	switch($vid)
  	{
  		case 2:
  			$linage_tids[] = 'Личное меню';
  			break;
  		case 4:
  			$linage_tids[] =  l(t('Home'), 'http://www.zapokupkami.com');//, array('rel' => 'nofollow')); //t('Home');
  			break;
  	}
  	$count++;
  	
  	//print '<PRE>';
  	//print_r($linage_tids);
  	//print '</PRE>';
  	
  	if(!$linage_tids[1])
  	{
  		
  		//if($node->nid == 25) //  если это нода 'Проверка оформленного заказа'	
  		if($node->nid == MY_CART_REVIEW_NODE_NUM) //  если это нода 'Проверка оформленного заказа'	
  		{
  	
			$linage_tids[] = 'Проверка оформленного заказа';
  			$linage_tids[] = 'Ваша корзина';
  			$linage_tids[] = 'Личное меню';
  			$count = 2;
  		}
  		else if(!$linage_tids[1] AND $node->nid == MY_ORDER_REVIEW_NODE_NUM) //  если это нода 'Просмотр заказа из истории'
	  	{
			$linage_tids[] = 'Просмотр заказа из истории заказов';
  			$linage_tids[] = 'Ваша корзина';
  			$linage_tids[] = 'Личное меню';
  			$count = 2;
  		}
  	}
  	
  	while( $count >= 0)
  	  if($linage_tids[$count] AND $linage_tids[$count] != '')
   		$c_breadcrumb = $c_breadcrumb . ' >> ' . $linage_tids[$count--];
  }
  
}      
    
    
    



//drupal_set_message("c_breadcrumb = $c_breadcrumb", 'error');

if($c_breadcrumb)
  $c_breadcrumb = '<div id="c_breadcrumb"><div id="title">Сейчас Вы здесь</div>' . $c_breadcrumb . '</div>';
else  
  $c_breadcrumb = '<div id="c_empty_breadcrumb"></div>';  
  
  
return $c_breadcrumb;

}



/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  $output = '';

  if ($primary = menu_primary_local_tasks()) {
    $output .= "<ul class=\"tabs primary\">\n". $primary ."</ul>\n";
  }

  return $output;
}


/*
 * theme_table($header, $rows, $attributes = array(), $caption = NULL)
 * includes/theme.inc, line 757
 * we modify this to give each table a class and to wrap it into a div
 * thus we can add "overflow: auto" to show scrollbars
 */
function phptemplate_table($header, $rows, $attributes = array(), $caption = NULL) {

  $output = '<div class="tablewrapper">';
  $output .= '<table'. drupal_attributes($attributes) ." class=\"tableclass\">\n";

  if (isset($caption)) {
    $output .= '<caption>'. $caption ."</caption>\n";
  }

  // Format the table header:
  if (count($header)) {
    $ts = tablesort_init($header);
    $output .= ' <thead><tr>';
    foreach ($header as $cell) {
      $cell = tablesort_header($cell, $header, $ts);
      $output .= _theme_table_cell($cell, TRUE);
    }
    $output .= " </tr></thead>\n";
  }

  // Format the table rows:
  $output .= "<tbody>\n";
  if (count($rows)) {
    $flip = array('even' => 'odd', 'odd' => 'even');
    $class = 'even';
    foreach ($rows as $number => $row) {
      $attributes = array();

      // Check if we're dealing with a simple or complex row
      if (isset($row['data'])) {
        foreach ($row as $key => $value) {
          if ($key == 'data') {
            $cells = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $cells = $row;
      }

      // Add odd/even class
      $class = $flip[$class];
      if (isset($attributes['class'])) {
        $attributes['class'] .= ' '. $class;
      }
      else {
        $attributes['class'] = $class;
      }

      // Build row
      $output .= ' <tr'. drupal_attributes($attributes) .'>';
      $i = 0;
      foreach ($cells as $cell) {
        $cell = tablesort_cell($cell, $header, $ts, $i++);
        $output .= _theme_table_cell($cell);
      }
      $output .= " </tr>\n";
    }
  }

  $output .= "</tbody></table>\n";
  $output .= "</div>\n";
  return $output;
} 






// переопределение функции из файла theme.inc (из папки includes)
// добавление поля для ввода комментариев к продуктам


/**
 * Return a themed list of items.
 *
 * @param $items
 *   An array of items to be displayed in the list. If an item is a string,
 *   then it is used as is. If an item is an array, then the "data" element of
 *   the array is used as the contents of the list item. If an item is an array
 *   with a "children" element, those children are displayed in a nested list.
 *   All other elements are treated as attributes of the list item element.
 * @param $title
 *   The title of the list.
 * @param $attributes
 *   The attributes applied to the list element.
 * @param $type
 *   The type of list to return (e.g. "ul", "ol")
 * @return
 *   A string containing the list output.
 */
function phptemplate_item_list($items = array(), $title = NULL, $type = 'ul', $attributes = NULL) {







// моё вмешательство 1
// ---------------------------------------------------------------------

// сохраняем атрибуты, переданные в функцию (так как дальше в функции этот оригинальный аргумент будет переобозначен)
// а он нам нужен будет в оригинальном виде

  $save_attributes = $attributes;
  //drupal_set_message('>>>>>attributes: attributes[source] = ' . $save_attributes['source']);










  $output = '<div class="item-list">';
  if (isset($title)) {
    $output .= '<h3>'. $title .'</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $list_count = 0;
    foreach ($items as $item) {
      $attributes = array();
      $children = array();
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          //drupal_set_message('items select  = ' . $key .  '=>' . $value, 'error');

         if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        $data .= phptemplate_item_list($children, NULL, $type, $attributes); // Render nested list
      }














// моё вмешательство 2
// -------------------------------------------------------------------------------------


   if($save_attributes['source'] == 'cart_pane_source')
    {
      // только если вызов пришёл из cart_pane_source,
      // даём пользователю изменять в корзине параметры атрибутов

      // если бы вызов пришёл из корзины-блока, то этого нельзя было бы разрешать пользователю (так как места в блоке нет)

      $str1 = explode(':', $data); // разобьём строку на две составляющие - до доветочия и после. До двоеточия идёт описание, после - выбор пользователя
      $user_action = explode('(', $str1[0]); // попробуем найти ключевые слова, по которым будем решать, как представлять данные
      $title = trim($str1[0]);
      $user_choice = trim($str1[1]);

      //drupal_set_message('all - '. $data);
      //drupal_set_message('title - '. $title);
      //drupal_set_message('user_choice - '. $user_choice);

      switch($user_action[1])
       {

        case 'укажите)':

             if($a_id = db_result(db_query("SELECT aid FROM {uc_attributes} WHERE name = '%s'", $title)))
              {
              	$output .= '<li'. drupal_attributes($attributes). '>' . $title .'<textarea cols="20" style="font-size:14px" name=cpid' . $save_attributes['cpid'] . '[' . $a_id . ']>' . $user_choice . '</textarea> </li>';

              }
             else
              {
              	$output .= '<li'. drupal_attributes($attributes). '>' . $title .'<textarea cols="20" style="font-size:14px" name=cpid' . $save_attributes['cpid'] . '[' . 'unknown_aid_' . $list_count++ . ']>' . $user_choice . '</textarea> </li>';

              }

             break;

        case 'выберите)':
           $output .= '<li'. drupal_attributes($attributes) . '>'. $title;
           if($a_id = db_result(db_query("SELECT aid FROM {uc_attributes} WHERE name = '%s'", $title)))
            {
              $output .= '<select name=cpid' . $save_attributes['cpid'] . '[' . $a_id . ']>';

              //drupal_set_message('a_id = '. $a_id);
            	if($a_options = db_query("SELECT oid, name, ordering, price FROM {uc_attribute_options} WHERE aid = '%s'", $a_id))
            	 {
            	 	//drupal_set_message('a_options = '. $a_options);
            	 	while($a_option = db_fetch_object($a_options))
            	 	 {
            	 	 	//drupal_set_message('a_option->oid = '. $a_option->oid);
            	 	 	$output .= '<option value="' . $a_option->oid . '"';
            	 	 	if($a_option->name == $user_choice)
            	 	 	  $output .= 'selected="selected"';
            	 	    $output .= '>' . $a_option->name;
            	 	    if(($option_price = db_result(db_query("SELECT price FROM {uc_product_options} WHERE nid = '%s' AND oid = '%s'", $save_attributes['nid'], $a_option->oid))) > 0)
            	 	     $output .= ' ( +' . uc_currency_format($option_price) . ' за шт. ) ';
            	 	    else
            	 	     {
            	 	      //if($a_option->price !='0.00')
            	 	       //$output .= ' ( +' . uc_currency_format($a_option->price) . ' за шт. ) ';

            	 	       //if()
            	 	        //if($a_option->name == "Нет")

            	 	       if($a_option->name != "Нет")
            	 	        $output .= ' ( цена не определена ) ';

            	 	     }
            	 	    $output .= '</option>';
            	 	 }

            	 }
                else
                 {
                 	//drupal_set_message('No options in db for a_id = ' . $a_id);
                 	$output .= '<option selected="selected">' . $user_choice . '</option>';
                 }
            }
           else
            {
               $output .= '<select name=cpid' . $save_attributes['cpid'] . '[' . 'unknown_aid_' . $list_count++ . ']>';

               //drupal_set_message('No attribute aid in db for name = ' . $data);
               $output .= '<option selected="selected">' . $user_choice . '</option>';
            }
           $output .= '</select > </li>';

              break;

        default: // оригинальные действия функции
             $output .= '<li' . drupal_attributes($attributes) . '>'. $data .'</li>';

       }

     }
    else
     {
       // если вызов пришёл из cart_pane_source
       // значит даём пользователю изменять в корзине параметры атрибутов

       // если вызов пришёл не из cart_pane_source, то нельзя разрешать пользователю изменять параметры атрибутов (например, в корзине-блоке или в checkout-pane или в order-review   (так как там места и необоходимости в редактировании)

       $output .= '<li' . drupal_attributes($attributes) . '>'. $data .'</li>';
     }
// конец моего вмешательства
// ---------------------------------------------------------------



      // оригинальный вызов, который был заменён моим вмешательством
      //$output .= '<li' . drupal_attributes($attributes) . '>'. $data .'</li>';
    }
    $output .= "</$type>";
  }
  $output .= '</div>';
  return $output;
}
















// block s korzinoy оригинальный с коррекцией-----------------------------

// но решено выводить этот блок через ноду с темизацией, так что для этого используется отдельная функция my_cart_block_node(), 
// которая возвращает массив переменных для вывода в шаблоне или ноль, если корзина пуста

// Коррекция для возможности использования поля для ввода комментариев к покупаемым продуктам на нодах продуктов
// и в корзине

// а также изменение ссылки на стандартную корзины на свою ноду со своим вызовом корзины


/**
 * Theme the shopping cart block content.
 */
function phptemplate_uc_cart_block_content() {

	
// формирование блока с корзиной	
	
	
  global $user;

  // Disabled until we figure out if this is actually screwing up caching. -RS
  //if (!$user->uid && variable_get('cache', 0) !== 0) {
  //  return t('<a href="!url">View</a> your shopping cart.', array('!url' => url('cart')));
  //}

  /*
  if (variable_get('uc_cart_show_help_text', FALSE)) {
    $output = '<span class="cart-help-text">'
            . variable_get('uc_cart_help_text', t('Click title to display cart contents.'))
             .'</span>';
  }
  */

  

  // обернём в такой див, чтобы этот блок реагировал на ajax-добавления продуктов
  $output .= '<div id="ajaxCartUpdate">';
  
  
  
  
  
  
  $output .= '<div id="block-cart-header" class="cart-block-toggle toggle-anchor" title="Кликните, чтобы показать или спрятать содержимое корзины"></div>';
  

  $items = uc_cart_get_contents();

  $item_count = 0;
  
  

  

  
  
 if (!empty($items)) 
  {
  	
  	
// укажем название магазина, из которого товары в корзине ------------------------- 	



// вместо проверки условия ниже и вычисления значения $c_shop_tids
// вызовем функцию, в которой всё это как раз и делается
	$c_shop_tids = zp_functions_get_cart_shop_data(null, $user->uid, $items[0]->nid);
	

    if(0)
    //if(!($c_shop_tids = $_SESSION['c_shop_tids'])) // если не определена сессионная переменная с тидами магазина корзины, пытаемся её определить
      	 {  

//--------------------------------------------

			/*
			$data = zp_functions_shop_min_sum($c_nid, $user->uid);
			
			$final_min_sum = $data['final_min_sum'];
			$linage_c_tids = $data['linage_c_tids'];
			$count = $data['count'];
			*/

			
    	    // сохраняем в сессионной переменной (сначала во временной переменной для использования в функции, чтобы постоянно в базу не лезть за ней) 
			// название магазина, минимальную сумму покупки (корзины) и все тиды продукта до магазина
			//$c_shop_tids = array();
			//$c_shop_tids['name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_c_tids[$count-4]));
			
			
			//найти тид продукта, продающегося в этом магазине
            //$c_tid = db_result(db_query("SELECT tid FROM {term_node} WHERE nid = %d", $c_nid));
            
            
            
            
            
            
            // найдём нид любого из продуктов корзины
      	 	//$c_nid = db_result(db_query("SELECT nid FROM {uc_cart_products} WHERE cart_id = %d", $cid))
      	 	// можно попробовать взять прямо из ордера первый товар
      	 	$c_nid = $items[0]->nid;
            
            $c_tids = taxonomy_node_get_terms_by_vocabulary($c_nid, 1); 
      		foreach($c_tids as $c_tid)
          		$c_tid = $c_tid->tid;
	    	
            $term_name = db_result(db_query("SELECT name FROM {term_data} WHERE tid = %d", $c_tid));
            
 	   		// задаём массив тидов продукта и первым элементом делаем терм самого продукта
        	$linage_c_tids = array($c_tid);
        
        	$count = 1;
        	while(($c_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $c_tid))) != 0)
	         {
    	       $linage_c_tids[] = $c_tid;
        	   $count++;
         	 } 
			

			// определим данные по текущему магазину
			$c_shop_tids = zp_functions_shop_info($linage_c_tids[$count-4], $user->uid);
			//$c_shop_tids['name'] = $c_shop_tids['shop_name']; // чтобы сохранить название ключа name, так как оно где-то уже используется
			
			//$c_shop_tids['shop_tid'] = $linage_c_tids[$count-4];
			
			/* // original
			if($_SESSION[$c_shop_tids['shop_tid'] . '_shop_info'])
				$c_shop_tids['shop_address'] = $_SESSION[$c_shop_tids['shop_tid'] . '_shop_info']['shop_address'];
			else
			{
				$shop_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid  = %d", $c_shop_tids['shop_tid']));
				$c_shop_tids['shop_nid'] = $shop_nid;
				$c_shop_tids['shop_address'] = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid  = %d", $shop_nid));
			}
			*/
		
			
		    //$c_shop_tids['c_min_sum'] = round($final_min_sum, 2); // минимальная сумма покупки (корзины) для этого магазина и клиента
		    
			// чтобы сохранить название ключа name, так как оно где-то уже используется
		    //$c_shop_tids['c_min_sum'] = $c_shop_tids['shop_min_sum']; //round($final_min_sum, 2); // минимальная сумма покупки (корзины) для этого магазина и клиента
		     
			// оставляем в переменной, которая затем будет сохранена в сессионной переменной для этого магазина
			// тиды, начиная с текущего магазина (отбрасываем район, город, страну)
			// вообще не знаю, нужны ли они там
			// а если нужны, нужно раскомментировать эти строчки в функции  zp_functions_shop_info($linage_c_tids[$count-4], $user->uid);
////////////////for($i = 0; $i <= $count - 4; $i++)
			  //$c_shop_tids[] = $linage_c_tids[$i];
		

			// сохраняем данные в сессионной переменной  
			$_SESSION['c_shop_tids'] = $c_shop_tids;
		  
		
// -------------------------------------------
      	 	
			// старый вариант, менее эффективный, с лишними командами
      	 	
            /*
      	 	// найдём нид любого из продуктов корзины
      	 	//$c_nid = db_result(db_query("SELECT nid FROM {uc_cart_products} WHERE cart_id = %d", $cid))
      	 	// можно попробовать взять прямо из ордера первый товар
      	 	$c_nid = $items[0]->nid;

			$data = zp_functions_shop_min_sum($c_nid, $user->uid);
			
			$final_min_sum = $data['final_min_sum'];
			$linage_c_tids = $data['linage_c_tids'];
			$count = $data['count'];

			// сохраняем в сессионной переменной (сначала во временной переменной для использования в функции, чтобы постоянно в базу не лезть за ней) название магазина и все тиды продукта до магазина
			$c_shop_tids = array();
			$c_shop_tids['name'] = db_result(db_query("SELECT name from {term_data} WHERE tid  = %d", $linage_c_tids[$count-4]));
			
			$c_shop_tids['shop_tid'] = $linage_c_tids[$count-4];
			if($_SESSION[$c_shop_tids['shop_tid'] . '_shop_info'])
				$c_shop_tids['shop_address'] = $_SESSION[$c_shop_tids['shop_tid'] . '_shop_info']['shop_address'];
			else
			{
				$shop_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid  = %d", $c_shop_tids['shop_tid']));
				$c_shop_tids['shop_nid'] = $shop_nid;
				$c_shop_tids['shop_address'] = db_result(db_query("SELECT field_place_address_value from {content_field_place_address} WHERE nid  = %d", $shop_nid));
			}
			

			$c_shop_tids['c_min_sum'] = round($final_min_sum, 2); // минимальная сумма покупки (корзины) для этого магазина и клиента    	    
			
			for($i = 0; $i <= $count - 4; $i++)
			  $c_shop_tids[] = $linage_c_tids[$i];

			  
			$_SESSION['c_shop_tids'] = $c_shop_tids;
		  	*/
        
        } // end of  if(!($c_shop_tids = $_SESSION['c_shop_tids'])) // если не определена сессионная переменная с тидами магазина корзины, пытаемся её определить
 	 	
        
        
        
        
        
        
        
        
        
        
        // теперь сессионная переменная определена 
        // добавляем название текущего магазина из этой переменной перед списком товаров в блоке корзины
        //if($c_shop_tids) 
           
   $output .= '<div id="shop_info">';
   $output .= '<div id="shop_name">' . l($c_shop_tids['shop_name'], 'node/' . $c_shop_tids['shop_nid'], array('title' => 'Перейти на первую страницу каталога этого заведения')) . '</div>'; 
   // добавим минимальную суммы покупки
   $output .= '<div id="min_sum_shop">Мин.сумма покупки: ' . uc_currency_format($c_shop_tids['shop_min_sum']) . '</div>'; 
   $output .= '</div>';
  	
	//if($user->uid == 1) 
		//zp_functions_show($c_shop_tids);
   
   //$output .= '<div id="resizeMe" class="ui-widget-content">'; // обрамляющий див для возможности изменения размера блока с корзиной
   
   
   
   //$output .= '<div id="resizeMe">'; // обрамляющий див для возможности изменения размера блока с корзиной
   $output .= '<div id="resizeMe" class="toggle-content">'; // обрамляющий див для возможности изменения размера блока с корзиной
   

   
   //$output .= '<div id="block-cart-contents" class="toggle-content resize-content">';
   $output .= '<div id="block-cart-contents" class="resize-content">';
  	
  	

  	
// список товаров в корзине -------------------------------------  	
  	
  	/*
    $output .= '<table class="cart-block-table">'
              .'<tbody class="cart-block-tbody">';
    */          
    $output .= '<div class="cart-block-table">';
              
              
              
    foreach ($items as $item) 
     {
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
// my changes 

// ------------------------------------------------------------------------------


    	
    	
 

// моё вмешательство

// тут я добавляю один новый аргумент 'cart_block_source' в функцию   module_invoke($item->module, 'cart_display', $item, 'cart_block_source');
// так я смогу в функции 'cart_display' (вернее, uc_product_cart_display) узнать, откуда поступил вызов
// и так определить, нужно ли давать возможность выбора/ ввода пользователем или нет

// тут я передаю 'cart_block_source'. Это значит, что нужно просто в строку перечислить все атрибуты и опции
	
    	// tut mi peredaiom cart_block_source' i poetomu eta funkcija ne vichisliaet stoimost dostavki
    	// tak mi sokrashaem vremia rascetov, 
    	// no tut v bloke cenu pokazivati uzhe nelzia -  tak kak bez dostavki cena uzhe ne vernaja
        $display_item = module_invoke($item->module, 'cart_display', $item, 'cart_block_source');
    	
        // оригинальная версия 	
        //$display_item = module_invoke($item->module, 'cart_display', $item);
      
     
        if (!empty($display_item)) 
         {
           
         	//ne budem v bloke korziny pokazyvat' summu, chtoby kazhyi raz ne pereschityvat' stoimost dostavki
         	
         	// original version
         	
         	/*
         	$output .= '<tr class="cart-block-item"><td class="cart-block-item-qty">'. $display_item['qty']['#default_value'] .'x</td>'
                  .'<td class="cart-block-item-title">'. $display_item['title']['#value'] .'</td>'
                  .'<td class="cart-block-item-price">'. uc_currency_format($display_item['#total']) .'</td></tr>';
              
            */         
            
         	// new version - bez ceny
         	/*
            $output .= '<tr class="cart-block-item"><td class="cart-block-item-qty">'. $display_item['qty']['#default_value'] .'x</td>'
                  .'<td class="cart-block-item-title">'. $display_item['title']['#value'] .'</td>'
                  .'</tr>';
            */
                  
         	//$output .= '<div class="cart-block-item"><div class="cart-block-item-qty">'. $display_item['qty']['#default_value'] .'x</div>'

         	//$output .= '<div class="cart-block-item"><div class="cart-block-item-qty">'. $display_item['qty']['#default_value'] . $item->data['sell_measure'] .'</div>'
         	$output .= '<div class="cart-block-item">' 
         					. '<div class="cart-block-item-qty">'
         					. $display_item['qty']['#default_value'] . $item->data['sell_measure']
         					. '</div>'
                  			. '<div class="cart-block-item-title">'. $display_item['title']['#value'] .'</div>';
                  			//. '<div class="cart-block-item-title">'. $display_item['qty']['#default_value'] . $item->data['sell_measure'] . ' ' . $display_item['title']['#value'] .'</div>';
                  //.'</div>';
                  
// my changes ----------------------

                
// -----------------------------------------------------------------------------                  
// вместо общего формирования опций, формируем их отдельно для блока и для ноды
// тут формируем содержимое блока с корзиной


//$element['options'] = '';

// это вариант общего формирования опций...
// пока его можем закомментировать и попробовать локальное формирование опций  
                        
//if ($display_item['options']['#value']) 
// $output .= '<tr><td colspan="3">'. $display_item['options']['#value'] .'</td></tr>';
         

  		  if (is_array($display_item['opt'])) 
    	   {
      	    foreach ($display_item['opt'] as $key => $option) 
       		 {
       		 	
       		 	// так как атрибуты я упаковал в филдсет, то в этом переборе массива мы пропускаем все элементы, 
       		 	//в названии которых содержится # 
       		 	// (так начинаются названия всех полей филдсета, тогда как нужные нам элементы с атрибутами называются просто своими именами)
       		 	if($key[0] == '#')
       		 	  continue;
     
		 		//$x = $display_item['qth']['0']['#default_value'];
		 		//drupal_set_message("def_val = $x", 'error');

		 		//foreach($option as $key => $value)
   				  //drupal_set_message(" block - key = $key, value = $value", 'error');
  
		 
        
		 		// формируем заголовок атрибута, заодно убирая из него пояснения в скобках (оставляем только то, что перед скобками)
         		$title = explode('(', $option['#title']);
         		$title = $title[0] . ': ';
                
         		// Не показываем строчку с атрибутом и его значением, если значение равно Нет или не задано
         		if($option['#default_value'] != 'Нет' AND $option['#default_value'] != '')
        		  //$output .= '<tr><td colspan="3">'. $title . $option['#default_value'] .'</td></tr>';
        		  $output .= '<div class="cart_opt">'. $title . $option['#default_value'] .'</div>';
      		 
       		 } // end of  foreach ($display_item['opt'] as $option)  
       		 
    	   } // end of  if (module_exists('uc_attribute'))   
    	   
    	   
    	   
    	 $output .= '</div>';  
    	   
    	   
         } // end of if (!empty($display_item))
         
     	 
         // vnosim v stoimost cenu dostavki
         // original version
         //$total += ($item->price) * $item->qty;
    	 
         
         
         //new version
         
         $total += $display_item['#total'];
         
         
         
         //if($item->data['sell_measure'] != 'шт')
         if(strpos($item->data['sell_measure'], 'шт') === FALSE)
           	$item_count += 1;
         else 
     	 	$item_count += $item->qty;
     	 
         
     	 
     	 //echo '<PRE>';
     	 //print_r($item->data['sell_measure']);
     	 //echo '</PRE>';
     	 
     	 //echo '<PRE>';
     	 //print_r($display_item);
     	 //echo '</PRE>';
     	 
    
     } // end of foreach ($items as $item) 

     //$output .= '</tbody></table>';
     $output .= '</div>';  // end of '<div class="cart-block-table">' ???
     
 

  $output .= '</div>'; // end of '<div id="block-cart-contents" class="toggle-content resize-content">' ???
  
  // добавим полоску, за которую нужно тащить, чтобы изменить размер блока
  $output .= '<div id="resizeS"><img src="http://www.zapokupkami.com/files/js/resizable_block/angle-nxs.gif" alt="slider" /></div>';

  // закрываем тег общего дива с блоком с изменяемым размером и картинокой для его изменения
  $output .= '</div>'; // end of '<div id="resizeMe">'; // обрамляющий див для возможности изменения размера блока с корзиной
  
  //$item_text = format_plural($item_count, '@count Item', '@count Items');
  
  if ($item_count > 0) {  
  
  
  
  
  
  
  
// my changes --------------------------------------------------------------------------------  
  
  
  
  
  // оригинальная версия
  //$view = '('. l(t('View cart'), 'cart', array('rel' => 'nofollow')) .')'; 
  
  //$view = '('. l(t('View cart'), 'node/23', array('rel' => 'nofollow')) .')';
  //$view = l(t(' '), 'node/23', array('rel' => 'nofollow', 'title' => 'Зайти в корзину'));
  $view = l(t('.'), MY_CART_NODE, array('title' => 'Зайти в корзину'));
  

  //$view = '<a rel="nofollow" href="' . '/content/zp-cart-cart' . '"><div id="view">View cart</div></a>';
  //$view = '<a rel="nofollow" href="' . '/content/zp-cart-cart' . '"><div id="view">View cart</div></a>';
  
  
  
  if (variable_get('uc_checkout_enabled', TRUE)) 
  {
	
  	
// my changes --------------------------------------------------------------------------------  


    // оригинальная версия
  	//$checkout = ' ('. l(t('Checkout'), 'cart/checkout', array('rel' => 'nofollow')) .')';
  	
  	
  	//$checkout = ' ('. l(t('Checkout'), 'node/24', array('rel' => 'nofollow')) .')';
  	//$checkout = l(t(' '), 'node/24', array('rel' => 'nofollow', 'title' => 'Оформить заказ'));
  	$checkout = l(t('.'), MY_CART_CHECKOUT_NODE, array('title' => 'Оформить заказ'));
  	
  	
  	//$checkout = '<a rel="nofollow" href="' . '/content/zp-cart-checkout' . '"><div id="checkout">Checkout</div></a>';
  	//$checkout = '<a rel="nofollow" href="' . '/content/zp-cart-checkout' . '"><div id="checkout"> </div></a>';
    
   }
  
  //ne budem v bloke korziny pokazyvat' summu, chtoby kazhyi raz ne pereschityvat' stoimost dostavki
         	
  // original version  
  /*
  $output .= '<table class="cart-block-summary-table"><tbody class="cart-block-summary-tbody">'
            .'<tr class="cart-block-summary-tr"><td class="cart-block-summary-items">'
            . $item_text .'</td><td class="cart-block-summary-total">'
            .'<strong>'. t('Total:') .'</strong> '. uc_currency_format($total) .'</td></tr>';
  */
       
  // new version bez ceny total
  
  /*
  $output .= '<table class="cart-block-summary-table"><tbody class="cart-block-summary-tbody">'
            .'<tr class="cart-block-summary-tr"><td class="cart-block-summary-items">'
            . $item_text .'</td></tr>';
  */          
  $output .= '<div class="cart-block-summary-table">';
  
  //$output .= '<div class="cart-block-summary-items">' . $item_text .'</div>';            
  $output .= '<div class="cart-block-summary-items">' . t('Всего товаров в корзине: ') .$item_count . t('шт') . '</div>';            
             

            

  //$output .= '<tr><td colspan="2" class="cart-block-summary-checkout">'. $view . $checkout .'</td></tr>';
  
  //$output .= '<div id="cart-block-summary-checkout">'. $view . $checkout .'</div>';
  
  
  
  
  //$output .= '<div id="cart-block-summary-checkout"><div id="view">'. $view . '</div><div id="checkout">' . $checkout .'</div></div>';
  //заменяем две кнопки (зайти, заказать) на одну (заказать) - хватит и одной
  $output .= '<div id="cart-block-summary-checkout"><div id="view2">'. $view . '</div></div>'; 
  
  
  //$output .= '<div id="cart-block-checkout">'. $view . $checkout .'</div>';

  //$output .= '</tbody></table>';
  $output .= '</div>';
  
 }
     } // end of if (!empty($items))
  else 
   {
     //$output .= '<p>'. t('There are no products in your shopping cart.') .'</p>';
    //$output .= '</div>';
     $output .= '<div class="cart-block-summary-table"> <div id="cart_empty">Пока пуста</div><div class="bottom"></div></div>';
     
   }
   
 $output .= '</div>';  
 return $output;
}












function phptemplate_uc_cart_view_form($form) {
	
	
	
// эта переопределение оригинальной функций theme_uc_cart_view_form из модуля uc_cart
	
  drupal_add_css(drupal_get_path('module', 'uc_cart') .'/uc_cart.css');

  $output = '<div id="cart-form-products">'
          . tapir_get_table('uc_cart_view_table', $form) .'</div>';

  if (($page = variable_get('uc_continue_shopping_url', '')) != '<none>') {
    if (variable_get('uc_continue_shopping_type', 'link') == 'link') {
      

    	
   	
    	
    	
    	
// my changes ------------------------------------------------------


// выносим определение линка для продолжения покупок в отдельную функцию zp_functions_continue_shopping_link() и отдельный модуль zp_functions


// определим ссылку для линка "Continue shopping" (Продолжить покупки)


   	  
    	

    	$continue_shopping_link = zp_functions_continue_shopping_link();
    	if($continue_shopping_link['nid'])
    		$continue_shopping_link = l(variable_get('uc_continue_shopping_text', t('Continue shopping')), 'node/' . $continue_shopping_link['nid']); 
    	else 
    		$continue_shopping_link = l(variable_get('uc_continue_shopping_text', t('На предыдущую страницу >>')), $_SERVER['HTTP_REFERER']); 
    	
    	// original
    	//$continue_shopping_link =  l(variable_get('uc_continue_shopping_text', t('Continue shopping')), $page); 
    	
    	
    	
    	
    	$output .= '<div id="cart-form-buttons"><div id="continue-shopping-link">' . $continue_shopping_link .'</div>' . drupal_render($form) .'</div>';
    }
    else {
      $button = drupal_render($form['continue_shopping']);
      $output .= '<div id="cart-form-buttons"><div id="update-checkout-buttons">'
               . drupal_render($form) .'</div><div id="continue-shopping-button">'
               . $button .'</div></div>';
    }
  }
  else {
    $output .= '<div id="cart-form-buttons">'. drupal_render($form) .'</div>';
  }

  return $output;
}












function phptemplate_cart_review_table($show_subtotal = TRUE) {


	// это функция вывода таблицы с товарами на странице чекаута (НЕ ревью), оригинал определён в файле uc_cart_checkout_pane.inc



	$items = uc_cart_get_contents();
	$subtotal = 0;
	$total_qty = 0;


	// добавим подсчёт общей стоимости доставки
	$dostavka_total = 0;
	
	// максимальный коэф доставки из продуктов в корзине, нужен для определения минимальной стоимости доставки
    $max_d_factor_otdel = 0; 


	// my changes ----------------------------------------------------------


	/* оригинальная версия
	$output = '<table class="cart-review"><thead>'
	.'<tr class="first last odd"><td class="first odd qty">'. t('Qty')
	.'</td><td class="even products">'. t('Products')
	.'</td><td class="last odd price">'. t('Price')
	.'</td></tr></thead><tbody>';
	*/

	// добавим колонку с ценой за единицу, так как до этого была только колонка Total
	$output = '<table class="cart-review"><thead>'
	.'<tr class="first last odd"><td class="even products">'. t('Products')
	.'</td><td class="last odd price">'. t('Price')
	.'</td><td class="first odd qty">'. t('Qty')
	.'</td><td class="last odd price">'. t('Сумма')
	.'</td></tr></thead><tbody>';

	$row = 1;

	$order_opt_price_total = 0;

	for ($i = 0; $i < count($items); $i++) 
	{
		$item = $items[$i];

		$rows = array();


		// покажем выбранные опции

		$item_opt_price_total = 0;

		foreach($item->options as $option)
		{


			// my changes ----------------------------------------


			// тут я корректирую так, чтобы атрибуты не выводились, если значение опции равно "Нет" или пусто "",
			// то есть, например, если комментариев "Нет" или выбран вариант с упаковкой "Нет"
			// тогда просто не показываем этот атрибут

			// также убираем из названия атрибута пояснения, то есть, то, что в скобках


			$attr_name = explode('(', $option['attribute']);
			$attr_name = rtrim($attr_name[0]);



			if($option['name'] != 'Нет' AND $option['name'] != '') // my change
			{

				// сохраним общую стоимость опций, чтобы показать её в соседней колонке, вместе со стоимостью доставки
				// !!!кстати, стоимость доставки должна начисляться на стоимость товара со всеми опциями!!!!!!!

				/*
				// ---
				$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $attr));
				$oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s'", $option['#default_value']));
				$price = db_result(db_query("SELECT price from {uc_product_options} WHERE nid = %d AND oid = %d ", $item->nid, $oid));
				// ---
				*/

				/*
				// ---
				$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $option['attribute']));
				$oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s'", $option['name']));
				$o_price = db_result(db_query("SELECT price from {uc_product_options} WHERE nid = %d AND oid = %d ", $item->nid, $oid));
				// ---


				//$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name  = '%s'", $option['attribute']));
				//$o_price = db_result(db_query("SELECT price from {uc_attribute_options} WHERE name  = '%s' AND aid = %d", $option['name'], $aid));

				$item_opt_price_total += $o_price;


				if($o_price) // если у опции есть цена, указываем её в скобках
				$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option['name'] . ' (+' . uc_currency_format($o_price). ')'  )); // my change
				//$rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
				else  // иначе ничего про цену не пишем
				$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option['name'] )); // my change

				*/

				// ---
				//$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name = '%s'", $option['attribute']));
				//$oid = db_result(db_query("SELECT oid FROM {uc_attribute_options} WHERE name = '%s'", $option['name']));
				//$o_price = db_result(db_query("SELECT price from {uc_product_options} WHERE nid = %d AND oid = %d ", $item->nid, $oid));
				// ---


				//$aid = db_result(db_query("SELECT aid from {uc_attributes} WHERE name  = '%s'", $option['attribute']));
				//$o_price = db_result(db_query("SELECT price from {uc_attribute_options} WHERE name  = '%s' AND aid = %d", $option['name'], $aid));

				$item_opt_price_total += $option['price'];


				if($option['price']) // если у опции есть цена, указываем её в скобках
				$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option['name'] . ' (+' . uc_currency_format($option['price']). ')'  )); // my change
				//$rows[] = t('@attribute: @option', array('@attribute' => $option['attribute'], '@option' => $option['name']));
				else  // иначе ничего про цену не пишем
				$rows[] = t('@attribute: @option', array('@attribute' => $attr_name, '@option' => $option['name'] )); // my change


			}
			//$rows[] = $option['attribute'] .': '. $option['name'];
		}
		$desc = check_plain($item->title) . theme('item_list', $rows, NULL, 'ul', array('class' => 'product-options'));

		$order_opt_price_total += ($item->qty) ? $item_opt_price_total * $item->qty : $item_opt_price_total;


		// -------------------------------------------------------------------------------------------
		// my changes -----------------



		// учтём в общей стоимости стоимость доставки

		//--------------------------------------------------------------------------

		// вычисляем коэффициент доставки
		// этот блок полностью скопирован из функции uc_product_cart_display($item, $source = NULL) в модуле uc_product.module
		// на тот случай, если пользователь заходит в чекаут, минуя корзину


		// равный перемноженным коэффициентам страна*город*район*магазин*отдел(ы)*клиент-общий*клиент-по-всей-иерархии-от-отдела-до-страны


		// выясним всю последовательность до страны, затем тиды страны, города, района и магазина

		//$node_tid = db_result(db_query("SELECT tid from {term_node} WHERE nid = %d", $node->nid));
		$node_tids = taxonomy_node_get_terms_by_vocabulary($item->nid, 1);

		// определим терм ноды продукта
		foreach($node_tids as $node_tid)
			$node_tid = $node_tid->tid;

		// определим родителя терма данной ноды. Это будет как раз терм отдела, в котором продаётся этот продукт
		$node_tid = db_result(db_query("SELECT parent from {term_hierarchy} WHERE tid  = '%s'", $node_tid));
		$parent_otdel_nid = db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $node_tid));


		// ----------------------------------------------------------------------------------------

		// вот теперь выясним, определён ли для этого отдела коэффициент доставки в сессионной переменной
		// он сохраняется по номеру nid отдела


		$d_factor_otdel = zp_functions_d_factor_otdel($node_tid, $parent_otdel_nid, $user->uid);
		
		
		// найдём максимальный коэффициент доставки среди всех товаров
        // и на основе него вычислим коэффициент для минимальной стоимости доставки
        // которую определим как произведение минимальной стоимости доставки по умолчанию на этот коэффициент
        if($max_d_factor_otdel < $d_factor_otdel)
           	$max_d_factor_otdel = $d_factor_otdel;

		//--------------------------------------------------------------------


		// $d_factor_otdel
		// теперь в переменной $d_factor_otdel сохранёно значение коэффициента доставки для данного отдела и юзера
		// стоимость доставки будет вычисляться по формуле (цена товара)*$d_factor_otdel
		// таким образом, общая стоимость, включая доставку, будет равна (цена товара) + (цена товара)*$d_factor_otdel


		// оригинальная версия
		//$total = ($item->qty) ? $item->qty * $item->price : $item->price;

		// цена почему-то уже включает (так программа изначально работает в данном месте) стоимость опций
		$total = ($item->qty) ? ($item->price + ($item->price * $d_factor_otdel)) * $item->qty : ($item->price + ($item->price * $d_factor_otdel));


		$subtotal += $total;


		// my changes ------------------------------

		// добавим подсчёт стоимости доставки
		$dostavka_total += ($item->price * $d_factor_otdel) * $item->qty; //($item->qty) ? ($item->price * $d_factor_otdel) * $item->qty : ($item->price * $d_factor_otdel);


		//$total_qty += $item->qty;


		if(strpos($item->data['sell_measure'], 'шт') === FALSE)
			$total_qty += 1;
		else
			$total_qty += $item->qty;


		// конец моего вмешательства



		$qty = ($item->qty) ? $item->qty : '';
		$tr_class = ($i % 2 == 0) ? 'even' : 'odd';
		if ($show_subtotal && $i == count($items)) {
			$tr_class .= ' last';
		}

		/*
		$output .= '<tr class="'. $tr_class .'"><td class="qty">'
		. t('!qtyx', array('!qty' => $qty)) .'</td><td class="products">'
		. $desc .'</td><td class="price">'. uc_currency_format($total)
		.'</td></tr>';
		*/


		// добавим колонку с ценой за единицу, так как до этого была только колонка Total

		// если не накопилось стоимости за опции, не показываем и пометку о стоимости выбранных опций
		if($item_opt_price_total > 0)
			$price_descr = '<br> (в т.ч. цена: ' . uc_currency_format($item->price - $item_opt_price_total) . ',<br>' . 'выбр. дополн.: ' . uc_currency_format($item_opt_price_total) . ',<br>доставка: ' . uc_currency_format(($item->price * $d_factor_otdel)) . ' - ' . $d_factor_otdel*100 . '%)';
		else
			$price_descr = '<br> (в т.ч. цена: ' . uc_currency_format($item->price - $item_opt_price_total) . ',<br>доставка: ' . uc_currency_format(($item->price * $d_factor_otdel)) . ' - ' . $d_factor_otdel*100 . '%)';


		$output .= '<tr class="'. $tr_class .'"><td class="products">'
		. $desc .'</td><td class="price">'
		. uc_currency_format(($item->price + ($item->price * $d_factor_otdel))) . $price_descr .'</td><td class="qty">'
		. t('- !qty ' . $item->data['sell_measure'] . ' -', array('!qty' => $qty)) .'</td><td class="price">'. uc_currency_format($total)
		.'</td></tr>';

	}
	
	
	
	
	// подбиваем итоговые суммы в подвале таблицы-------------------------------
	


	
	if ($show_subtotal) 
	{

		//$subtotal -= $gift_wrap_subtotal;

		$tr_class = ($tr_class == 'even') ? 'odd' : 'even';



		// my changes -----------------------------------------------
		
		
		// если общая стоимость заказа меньше минимальной суммы заказа для этого магазина и клиента
    	// с учётом максимального коэффициента доставки товаров этой корзины
    	// то заменяем стоимость доставки на минимальную стоимость доставки, умноженную на максимальный коэфициент доставки
      
    	// найдём минимальную стоимость доставки по умолчанию
    	$zp_default_set = zp_functions_get_zp_default_set();
      
    	$max_d_factor_otdel =  $max_d_factor_otdel * 10;
       
    	//echo 'min_dost_price_default = ' . $zp_default_set['min_dost_price_default'] . '<br>';
    	//echo 'max_d_factor_otdel = ' . $max_d_factor_otdel . '<br>';
      
      	$c_shop_tids = zp_functions_get_cart_shop_data(); // получим (среди прочих) минимальную стоимость доставки
      
      	// изменяем общую итоговую сумму, с учётом замены обычной стоимости доставки на минимальную для этого заведения и клиента
      	if($subtotal < $c_shop_tids['shop_min_sum'])
      	{
      		$subtotal = $subtotal - $dostavka_total; // уберём сначала старую стоимость доставки из общей суммы

      		// изменим стоимость доставки на новую (минимальную стоимость доставки)
      		$dostavka_total = $zp_default_set['min_dost_price_default']*$max_d_factor_otdel;
      	
      		// посчитаем новую общую сумму заказа с обычной доставкой (но теперь уже минимальной для данного заведения и юзера)
      		$subtotal = $subtotal + $dostavka_total;
      		
      		$flag_min_dost_price = 1;
      
      	}      
      
      	
      	
      

		if($order_opt_price_total > 0)
		{
			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			//.'colspan="4"><span id="subtotal-title">'. t('Subtotal (with chosen options), for ' . $total_qty . ' items:')
			.'colspan="4"><span id="subtotal-title">'. t('Сумма, всего (с учётом уточнений и обычной доставки) за ' . $total_qty . ' шт:')
			.'</span> '. uc_currency_format($subtotal) .'</td></tr>';
		}
		else
		{
			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			//.'colspan="4"><span id="subtotal-title">'. t('Subtotal (with chosen options), for ' . $total_qty . ' items:')
			.'colspan="4"><span id="subtotal-title">'. t('Сумма, всего (с учётом обычной доставки) за ' . $total_qty . ' шт:')
			.'</span> '. uc_currency_format($subtotal) .'</td></tr>';
		}



		if($order_opt_price_total > 0)
		{

			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			.'colspan="4"><span id="subtotal-title">'.'В т.ч. общая стоимость  выбранных товаров с выбр. дополнениями, без доставки: '
			.'</span> '. uc_currency_format($subtotal - $dostavka_total) .'</td></tr>';

		}
		else
			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			.'colspan="4"><span id="subtotal-title">'.'В т.ч. общая стоимость выбранных товаров без доставки: '
			.'</span> '. uc_currency_format($subtotal - $dostavka_total) .'</td></tr>';


		// ещё добавим общую сумму за доставку
		// не будем указывать, сколько процентов стоит общая доставка, так как проценты для разных отделов одного магазина могут различаться
		// таким образом общий процент в общем случае неизвестен
		
		if($flag_min_dost_price)
		{
			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			.'colspan="4"><span id="subtotal-title">'. t('В т.ч. стоимость обычной доставки <br>(которая была увеличена до минимальной стоимости доставки по умолчанию, так как пока что общая сумма заказа меньше минимальной суммы заказа  для Вас в этом заведении):')
			//.'colspan="4"><span id="subtotal-title">'. t('В том числе стоимость доставки (' . $d_factor_otdel*100 . '%):')
			.'</span> '. uc_currency_format($dostavka_total) .'</td></tr>';
		}
		else 
		{
			$output .= '<tr class="'. $tr_class .' last"><td class="subtotal" '
			.'colspan="4"><span id="subtotal-title">'. t('В т.ч. стоимость обычной доставки:')
			//.'colspan="4"><span id="subtotal-title">'. t('В том числе стоимость доставки (' . $d_factor_otdel*100 . '%):')
			.'</span> '. uc_currency_format($dostavka_total) .'</td></tr>';
		
		}





		// конец моего вмешательства







	}
	$output .= '</tbody></table>';

	return $output;
}



// функция из модуля uc_cart.module

// добавляем сброс сессионной переменной текущего магазина корзины

// Returns the text displayed for an empty shopping cart.
function phptemplate_uc_empty_cart() {

// my changes-------------

  // обнуляем текущий магазин (если корзина пуста), чтобы можно было добавлять товары из другого магазина
  unset($_SESSION['c_shop_tids']);
  
  
      	
// my changes ------------------------------------------------------

// определим ссылку для линка "Continue shopping" (Продолжить покупки)

// - если прошлая страница была продуктом, отделом, магазином и т.д., значит показываем ссылку на это

// - если прошлая страница какого-то другого типа, значит показываем:

// отдел, если переменная текущего отдела установлена в сессиях
// если отдел не задан, показываем магазин, если переменная текущего магазина установлена в сессиях
// если магазин не задан, показываем текущий город
// или если город не задан... страну, но для страны я пока переменную не задал

    	//$page = uc_referer_uri(); // показывает предыдущую ссылку, но уже испорченную by pathauto
    	
    	
    	
    	/*
    	$current_tid = NULL;
    	
    	//$otd = $_SESSION['current_otdel'];
    	//$sho = $_SESSION['current_shop'];
    	//$cit = $_SESSION['current_city'];
    	
    	//drupal_set_message("0 - otd = $otd, sho = $sho, cit = $cit, page = $page", 'error');
    	
    	if($current_tid = $_SESSION['current_otdel'])
    	{
    	  
    		$page = "node/" . db_result(db_query("SELECT nid from {term_node} WHERE tid  = %d", $current_tid));
    		//drupal_set_message("1 - otd = $otd, sho = $sho, cit = $cit, page = $page", 'error');
         }
    	else 
    	 if($current_tid = $_SESSION['current_shop'])
    	 {
			
    	 	$page = "node/" . db_result(db_query("SELECT nid from {term_node} WHERE tid  = %d", $current_tid));
    	 	//drupal_set_message("2 - otd = $otd, sho = $sho, cit = $cit, page = $page", 'error');
    	 }
    	else 
    	 if($current_tid = $_SESSION['current_rajon'])
    	 {
    	  
    	 	$page = "node/" . db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $current_tid));
    	  //drupal_set_message("3 - otd = $otd, sho = $sho, cit = $cit, page = $page", 'error');
    	 }
    	else 
    	 if($current_tid = $_SESSION['current_city'])
    	 {
    	  
    	 	$page = "node/" . db_result(db_query("SELECT nid from {term_node} WHERE tid = %d", $current_tid));
    	  //drupal_set_message("3 - otd = $otd, sho = $sho, cit = $cit, page = $page", 'error');
    	 }
    	

    	*/
    	
    	
    	  
    	$continue_shopping_link = zp_functions_continue_shopping_link();
    	//$continue_shopping_link = l(variable_get('uc_continue_shopping_text', t('Continue shopping')), 'node/' . $continue_shopping_link['nid']); 
    	if($continue_shopping_link['nid'])
    		$continue_shopping_link = l(variable_get('uc_continue_shopping_text', t('Continue shopping')), 'node/' . $continue_shopping_link['nid']); 
    	else 
    		$continue_shopping_link = l(variable_get('uc_continue_shopping_text', t('На предыдущую страницу >>')), $_SERVER['HTTP_REFERER']); 
    	  
    	  
    	  
		$continue_shopping = '<div id="continue-shopping-link">' . $continue_shopping_link .'</div>';    	  
    	  
    	// original
    	//$continue_shopping = '<div id="cart-form-buttons"><div id="continue-shopping-link">' . l(variable_get('uc_continue_shopping_text', t('Continue shopping')), $page) .'</div>' . drupal_render($form) .'</div>';
    	
    
  	
  return '<div class="empty_cart_text">'. t('There are no products in your shopping cart.') .'</div>' . $continue_shopping;
  
  
  
  
}






// правим шаблон с панелями - убираем заголовки по умолчанию и т.д.


/**
 * Render a panel pane like a block.
 *
 * A panel pane can have the following fields:
 *
 *  - $pane->type -- the content type inside this pane
 *  - $pane->subtype -- The subtype, if applicable. If a view it will be the
 *    view name; if a node it will be the nid, etc.
 *  - $content->title -- The title of the content
 *  - $content->content -- The actual content
 *  - $content->links -- Any links associated with the content
 *  - $content->more -- An optional 'more' link (destination only)
 *  - $content->admin_links -- Administrative links associated with the content
 *  - $content->feeds -- Any feed icons or associated with the content
 *  - $content->subject -- A legacy setting for block compatibility
 *  - $content->module -- A legacy setting for block compatibility
 *  - $content->delta -- A legacy setting for block compatibility
 */
function phptemplate_panels_pane($content, $pane, $display) {
  if (!empty($content->content)) {
    $idstr = $classstr = '';
    if (!empty($content->css_id)) {
      $idstr = ' id="' . $content->css_id . '"';
    }
    if (!empty($content->css_class)) {
      $classstr = ' ' . $content->css_class;
    }

    // original
    //$output = "<div class=\"panel-pane$classstr\"$idstr>\n";
//my changes    
    $output = "<div class=\"panel-pane$classstr\"$idstr>\n";
    
    if (user_access('view pane admin links') && !empty($content->admin_links)) {
      $output .= "<div class=\"admin-links panel-hide\">" . theme('links', $content->admin_links) . "</div>\n";
    }
    

    
    //print '<PRE>';
    //print_r($content);
    //print '</PRE>';    
    
    
    
    
    
    
    
// my changes ---------------------------------     
    
    //if (!empty($content->title) AND strpos($content->title, 'Shopping cart') !== FALSE) {
    //if (!empty($content->title) AND strpos($content->type, 'product') !== FALSE) {
    //if (!empty($content->title)) {
    //  $output .= "<h2 class=\"title\">$content->title</h2>\n";
   // }
     
  
   // original
    //$output .= "<h2 class=\"title\">$content->title</h2>\n";
    
    
    if (!empty($content->feeds)) {
      $output .= "<div class=\"feed\">" . implode(' ', $content->feeds) . "</div>\n";
    }

    
    
    
    
    
// my changes --------------------------------- 
    
    //$output .= "<div class=\"content\">$content->content</div>\n";
    $output .= $content->content;

    
    
    if (!empty($content->links)) {
      $output .= "<div class=\"links\">" . theme('links', $content->links) . "</div>\n";
    }


    if (!empty($content->more)) {
      if (empty($content->more['title'])) {
        $content->more['title'] = t('more');
      }
      $output .= "<div class=\"more-link\">" . l($content->more['title'], $content->more['href']) . "</div>\n";
    }

    $output .= "</div>\n";
    return $output;
  }
}




//function phptemplate_nice_tax_menu($items = array(), $attributes = array('class' => 'ddmenu'), $type = NULL, $type2 = NULL){
//function phptemplate_nice_tax_menu($items = array(), $attributes = array('class' => 'ddmenu'), $type = NULL, $add_class = null, $type2 = NULL, $level = 0){
function phptemplate_nice_tax_menu($items = array(), $attributes = array('class' => 'ddmenu'), $type = NULL, $add_class = null, $type2 = NULL, $level = 0, $upper_class = null, $upper_id = null, $current_length = null, $redirect_step = 0, $parent_title = null){
  
  if(!$type)
  {
   if ($type = variable_get('nice_tax_menu_type', 'right'))
    {
     $attributes['class'] .= ' ddmenu-' . $type . ' len=' . $current_length;
    }
  }
  else 
   $attributes['class'] .= ' ddmenu-' . $type . ' len=' . $current_length;
  
  
    
  if($upper_class AND $level == 0)
   	$attributes['class'] .= ' ' . $upper_class;
  
  if($upper_id AND $level == 0)
   	$attributes['id'] .= $upper_id;
   
  if($add_class)
   	$attributes['class'] .= ' ' . $add_class;

  //echo 'add_class = ' . $add_class . '<br>';
   	
  // покажем уровень вложения меню
  //$attributes['class'] .= ' ' . $level++ . ' ' . $type2 . ' rstep-' . $redirect_step;
  $current_level = $level++;
  $attributes['class'] .= ' l-' . $current_level . ' ' . $type2 . ' rs-' . $redirect_step;
 
  //$attributes['class'] .= ' type=' . $type . ' type2=' . $type2
   
  $output = '';
  if (!empty($items)) {


  	
// my changes 
// добавляем ссылку на родительский пункт меню (уровень выше)
// вернее, тут мы этот пункт удаляем из массива, чтобы он не мешал формировать стандартное меню
// а затем просто его добавим
  	
 if($items['upper_items'])
  {
   $upper_items = $items['upper_items'];
   array_splice($items, -1, 1);
  }

  if($level > 1)  // if =1 we count width of first level item (later here), not width of the ul block
  {
    if($type == 'left')
        //$current_length = $first_line_length + $current_length - 100; // 100 - width of a menu ul block
        $current_length = $first_line_length + $current_length - 20; // 100 - width of a menu ul block
    else
        //$current_length = $first_line_length + $current_length + 100;
        $current_length = $first_line_length + $current_length + 20;
  }

    $output .= '<ul' . drupal_attributes($attributes) . '>';
    
    if($level == 2)
        $output .= '<div class="u">' . $parent_title . '</div>';
    
    foreach ($items as $item)
    {

      $attributes = array();
      $children = array();
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
            $title = $value; //////////////////////
          }
          elseif ($key == 'children') {
            $children = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      
      $type_for_li = $type;
      if (count($children) > 0)
      {
      	
        //$data .= theme('nice_tax_menu', $children, $attributes, $type2, $type2); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню
      	// we use revers direction of menu open only in 'down' type
        if($type == 'down' AND $level == 1)
        {
           if(/*$i>1* AND */ $first_line_length >60)
               // swap direction
               $data .= theme('nice_tax_menu', $children, $attributes, 'left', $add_class, $type2, $level, null, null, $first_line_length, $redirect_step + 1, strip_tags($title)); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
           else
               $data .= theme('nice_tax_menu', $children, $attributes, $type2, $add_class, $type2, $level, null, null, $first_line_length, $redirect_step, strip_tags($title));

           $first_line_length += mb_strlen(strip_tags($title));
        }
        else
        {
            if($type == 'right' AND $current_length > 95) // 400 - just some test number now... for the biggest border from the right
            {

                //$data .= theme('nice_tax_menu', $children, $attributes, $type2, $add_class, $type2, $level); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
                //$data .= theme('nice_tax_menu', $children, $attributes, $type, $add_class, $type2, $level, null, null, $current_length); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
                // change the direction to left
                //$current_length_new = $current_length - 200;
                
                //$current_length_new = $current_length - 40;
                $current_length_new = $current_length;
                $type_for_li = 'left';
                $data .= theme('nice_tax_menu', $children, $attributes, 'left', $add_class, $type2, $level, null, null, $current_length_new, $redirect_step + 1); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
            }
            //elseif($type == 'left' AND $current_length < -300)
            elseif($type == 'left' AND $current_length < -30)
            {
                // change the direction to right
                //$current_length_new = $current_length + 200;
                
                //$current_length_new = $current_length + 40;
                $current_length_new = $current_length;
                $type_for_li = 'right';
                $data .= theme('nice_tax_menu', $children, $attributes, 'right', $add_class, $type2, $level, null, null, $current_length_new, $redirect_step + 1); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
            }
            else
                // no change direction
                $data .= theme('nice_tax_menu', $children, $attributes, $type, $add_class, $type2, $level, null, null, $current_length, $redirect_step); // Render nested list // тут я добавил ещё и type2, чтобы указать тип второго уровня меню, $level для уровня вложенности и add_class для указания дополнительного класса для возможности применения его к определённому уровню
        }        
        $attributes['class'] .= ' menuparent ' . 'l-' . $current_level . ' rs-' . $redirect_step . ' ' . $type_for_li;
      }
      else { 
      	$attributes['class'] .= ' endleaf ' . 'l-' . $current_level . ' rs-' . $redirect_step . ' ' . $type_for_li;
      }

      //if($level == 1) $attributes['class'] .= ' x' . $i;
      $output .= '<li' . drupal_attributes($attributes) . '>'. $data .'</li>';
      $i++;
    }
    $output .= "</ul>";
  }


// my changes 
// добавляем ссылку на родительский пункт меню (уровень выше), если он предусмотрен
  
if($upper_items)
 $output = '<li class = "upper_items">'. $upper_items['data'] .'</li>' . $output;

 //print '<PRE>';
 //print_r($upper_items);
 //print '</PRE>';
 
  return $output;
}














// функция формирования блока логина
/*
function custom_user_login() {
 global $user;                                                               
  $output = '';

  if (!$user->uid) {                                                          

$output = '<div id="user-bar-notlogged">';

$output .= t('<p class="user-info">Для пользования сервисом "zapokupkami.com" введите свои данные:</p>');
    $output .= drupal_get_form('custom_user_login_blocks'); 
//$output .= drupal_get_form('user_login_block'); 
  //все что нужно ещё
 }
 else {                                                                       

$output = '<div id="user-bar-logged">';

//$output .= t('<p class="user-info">Здравстуйте, !user, добро пожаловать на сайт zapokupkami.com</p>', array('!user' => theme('username', $user))); 
$output .= t('<p class="user-info">Здравствуйте, !user, добро пожаловать на сайт zapokupkami.com</p>', array('!user' => $user->name)); 

if($user->uid == 1)
    {
    $output .= theme(
           'item_list', 
            array(
                  //'<div class="delimiter">|</div>',
                  l(
                     t('Admin'), 
                     'admin/', 
                     array('title' => t('Site administration'), 
                     'class' => 'site_admin')
                  ),
                  //'<div class="delimiter">|</div>',
                  l(
                     t('Sign out'), 
                     'logout', 
                     array('class' => 'sign_out'), 
                     drupal_get_destination()
                   ),
                  //'<div class="delimiter">|</div>'
                 ),
            NULL,
            'ul', 
            array('class' => 'logged_user_buttons'));



    } else 
        {

         $output .= theme(
           'item_list', 
            array(
                  //'<div class="delimiter">|</div>',
                  l(
                     t('Your account'), 
                     'user/'.$user->uid, 
                     array('title' => t('Edit your account'), 
                     'class' => 'edit_your_account')
                  ),
                  //'<div class="delimiter">|</div>',
                  l(
                     t('Sign out'), 
                     'logout', 
                     array('class' => 'sign_out'), 
                     drupal_get_destination()
                   ),
                  //'<div class="delimiter">|</div>'
                 ),
            NULL,
            'ul', 
            array('class' => 'logged_user_buttons'));
         }

  }
    
  $output .= '</div>';
      
  return $output;
}
*/

// функция, необходимая для формирования блока логина


function custom_user_login_blocks() {
	
	
 $dest_url = drupal_get_destination();

  // убираем ез строки с урл назначения параметр alo (autologout), который мог появиться при автологауте пользователя по истечении заданного врвмени
  // если его не убрать, система будет без конца уведомлять пользователя, что он выгружен из системы, даже если он опять залогинится
  
  
  //if(stripos($dest_url, 'alo=') !== FALSE)
  if(stripos($dest_url, 'alo%3D') !== FALSE)
  {
  		//echo $destination_url;
  		//$dest = explode('?', $dest_url); // считаем, что если в строке есть передача переменной alo=... то там есть и символ ?, разделяющий путь и параметры
  		$dest = explode('%3F', $dest_url); // считаем, что если в строке есть передача переменной alo=... то там есть и символ ?, разделяющий путь и параметры
		
  		
  		$params = $dest[1]; // параметры, передающиеся ноде через url (через ?)
  		$dest = $dest[0]; // нода назначения
  	
  		//if(stripos($params, '&') !== FALSE) // если параметров несколько, оставляем все, кроме alo
  		if(stripos($params, '%26') !== FALSE) // если параметров несколько, оставляем все, кроме alo
  		{
  			//$params = explode('&', $params);
  			$params = explode('%26', $params);
  			
  			foreach($params as $param)
  			{
	  			//$value = explode('=', $param);
	  			$value = explode('%3D', $param);
	  			
  				if($value[0] != 'alo')
  				{
  					if($start == 1)
  						//$new_params .= '&' . $param;
  						$new_params .= '%26' . $param;
	  				else
  					{
  						$new_params .= $param;
  						$start = 1;
  					}
	  			}
  			}
  			
  			//$dest_url = $dest . '?' . $new_params;
  			$dest_url = $dest . '%3F' . $new_params;
  			
  		
  		}
  		else 
  		{
  			// иначе считаем, что передавался только параметр времени автологаута
  		// так что мы его просто убираем и оставляем в строке назначения только саму строку (ноду назначения) без параметров
  			$dest_url = $dest; 
  		}
  		
  	}
  	

    
	
  $form = array(
    //'#action' => url($_GET['q'], drupal_get_destination()),
    '#action' => url($_GET['q'], $dest_url),
    '#id' => 'user-login-form',
    '#base' => 'user_login',
  );
  $form['name'] = array('#type' => 'textfield',
    '#title' => t('Логин'),
    '#maxlength' => USERNAME_MAX_LENGTH,
    '#size' => 15,
    '#required' => TRUE,
  );
  $form['pass'] = array('#type' => 'password',
    '#title' => t('Пароль'),
    '#maxlength' => 60,
    '#size' => 15,
    '#required' => TRUE,
  );
  $form['submit'] = array('#type' => 'submit',
    '#value' => t('Ок!'),
  );
  /*
  $items = array();
  if (variable_get('user_register', 1)) {
    //$items[] = l(t('Create new account'), 'user/register', array('title' => t('Create a new user account.')));
  }
  //$items[] = l(t('Request new password'), 'user/password', array('title' => t('Request new password via e-mail.')));
  $form['links'] = array('#value' => theme('item_list', $items));
  */

  
  // если находимся уже на странице восстановления пароля, то путь возвращения destination к ссылке не добавляем, чтобы не было многократного его дублирования бессмысленного
  if(arg(0) == 'node' AND arg(1) == MY_PASSREMIND_NODE_NUM)
  	$form['links'] = array('#value' => t('<div class="rpass"><a rel="nofollow" href="!login">Забыли свой пароль?</a></div>', array('!login' => url((MY_PASSREMIND_NODE)))));
  else
	//$form['links'] = array('#value' => t('<div class="rpass"><a rel="nofollow" href="!login">Забыли свой пароль?</a></div>', array('!login' => url((MY_PASSREMIND_NODE), 'x' . drupal_get_destination()))));
	$form['links'] = array('#value' => t('<div class="rpass"><a rel="nofollow" href="!login">Забыли свой пароль?</a></div>', array('!login' => url((MY_PASSREMIND_NODE), 'xdestination=' . $_SERVER['REQUEST_URI']))));
	//'destination=' . $_SERVER['REQUEST_URI']


  return $form;
}




// корректируем форму продукта, добавляем новые меры веса и убираем лишние поля, типа лишних типов цен

function phptemplate_uc_product_form_prices($prices) {
  return '<table></td><td>'. drupal_render($prices['sell_price'])
    ."</td></tr></table>\n";
}

function xxx_______phptemplate_uc_product_form_weight($form) {
  
/*	
  $units = array(
    'kg' => t('Kilograms'),
    'gr' => t('Grams'),
    'l' => t('Liters'),    
  );
*/
  $units = array(
    'lb' => t('Pounds'),
    'kg' => t('Kilograms'),
    'oz' => t('Ounces'),
    'g' => t('Grams'),
    'кг' => t('Килограммы'),
    'гр' => t('Граммы'),
    'л' => t('Литры'),    
  );
    
  
  
  //$form['weight_units']['#options'] = $units;
  
/*
  $form['weight_units'] = array('#type' => 'select',
    '#title' => t('Unit of measurement'),
    '#default_value' => $node->weight_units ? $node->weight_units : variable_get('uc_weight_unit', 'кг'),
    '#options' => $units,
  );
  
*/  
  
/*  
  $form['base']['weight']['weight_units'] = array('#type' => 'select',
    '#title' => t('Unit of measurement'),
    '#default_value' => $node->weight_units ? $node->weight_units : variable_get('uc_weight_unit', 'lb'),
    '#options' => $units,
  );
*/
	
	return '<table><tr><td>' . drupal_render($form['weight']) .'</td><td>'
       . drupal_render($form['weight_units']) .'</td></tr></table>';
}











function phptemplate_webform_mail_message($form_values, $node, $sid, $cid) {

  // вкрапление моего кода
  // начало ------------------------------------------------


  // если раскомментировать следующую строку, можно использовать шаблон webform-mail.tpl.php 
  //return _phptemplate_callback('webform-mail', array('form_values' => $form_values, 'node' => $node, 'sid' => $sid, 'cid' => $cid));

 
  // иначе отсылаем фрагмент (так как всё сообщение не влезет) сообщения на телефон в виде sms
  // а затем полностью повторяем оригинальную функцию theme_webform_mail_message, но с небольшими изменениями
 
 // это мы делаем в отдельной функции (см. вызов события ниже) 
 //$header = theme('webform_mail_headers', $form_values, $node, $sid, $cid);
 //drupal_mail('webform-submission-sms', 'all4senses@gmail.com', 'A4S-Message',  $form_values['submitted_tree']['your_message'], 'info@all4senses.com', $header);

 

 
  workflow_ng_invoke_event('zp_webform_message_sent', array('form_values' => $form_values, 'page_title' => $node->title, 'uid' => $node->uid, 'user_name' => $node->name)); 
 

 
  // конец вкрапления моего кода ------------------------------------------------


  
  
  
  
  // содержимое оригинальной функции theme_webform_mail_message с небольшими изменениями

 global $user;

  $message = '';
  $message .=  t('Submitted on') .' '. format_date(time(), 'small') ."\n";
  $ip_address = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

  if ($user->uid) {
    $message .= t('Submitted by user') .": $user->name [$ip_address]\n";
  }
  else {
    $message .= t('Submitted by anonymous user') .": [$ip_address]\n";
  }

  $message .= "\n";

  // Немного изменяем текст
  //$message .= t('Submitted values are:') ."\n\n";
  //$message .= t('Submitted to all4senses.com message is:') ."\n\n";


  $message .= theme('webform_mail_fields', 0, $form_values['submitted_tree'], $node);

  $message .= "\n\n";

  // чтобы убрать возможность посмотреть The results of this submission... комментируем две следующие строки
  $message .= t('The results of this submission may be viewed at:') ."\n";
  $message .= url('node/'. $node->nid. '/submission/'. $sid, NULL, NULL, TRUE);

  return $message;

}




function phptemplate_webform_mail_headers($form_values, $node, $sid, $cid) {
  $headers = array(
    //'X-Mailer' => 'Drupal Webform (PHP/'. phpversion() .')',
    'X-Mailer' => 'Webform (PHP/'. phpversion() .')',
  );
  
  $headers['Sender'] = $headers['Return-Path'] = $headers['Errors-To'] = $form_values['details']['email_from_address'];
  
  return $headers;
}





// Организация комментариев через свою ноду... Если не переопределять, комментарии выпадают из панелей,
// свои вызовы функции с комментариями св. выще на типе ноды zp_user

function phptemplate_comment_view($comment, $links = array(), $visible = 1) {
  static $first_new = TRUE;

  $output = '';
  $comment->new = node_mark($comment->nid, $comment->timestamp);
  if ($first_new && $comment->new != MARK_READ) {
    // Assign the anchor only for the first new comment. This avoids duplicate
    // id attributes on a page.
    $first_new = FALSE;
    $output .= "<a id=\"new\"></a>\n";
  }

  $output .= "<a id=\"comment-$comment->cid\"></a>\n";

  // Switch to folded/unfolded view of the comment
  if ($visible) {
    $comment->comment = check_markup($comment->comment, $comment->format, FALSE);

    // Comment API hook
    comment_invoke_comment($comment, 'view');
    

    
    //$links = '';
    
    //$links['comment_reply']['href'] = ereg_replace('comment/reply', 'node/225/reply', $links['comment_reply']['href']);
    $links['comment_reply']['href'] = ereg_replace('comment/reply', MY_ZP_COMMENTS_NODE . '/reply', $links['comment_reply']['href']);
    
    //$links['comment_edit']['href'] = ereg_replace('comment/edit', 'node/225/edit_comment', $links['comment_edit']['href']);
    $links['comment_edit']['href'] = ereg_replace('comment/edit', MY_ZP_COMMENTS_NODE . '/edit_comment', $links['comment_edit']['href']);
    
    //$links['comment_delete']['href'] = ereg_replace('comment/delete', 'node/225/delete_comment', $links['comment_delete']['href']);
    $links['comment_delete']['href'] = ereg_replace('comment/delete', MY_ZP_COMMENTS_NODE . '/delete_comment', $links['comment_delete']['href']);
    
//print '<pre>';
//print print_r($links);
//print '</pre>';
    
    $output .= theme('comment', $comment, $links);
  }
  else {
    $output .= theme('comment_folded', $comment);
  }

  return $output;
}





// эта функция уже была переопределена в системном модуле phptemplate.engine (/public_html/themes/engines/phptemplate/)
// теперь мы её ещё раз переопределяем уже на свой манер, но за основу берём прошлое переопределение, а не оригинал из модуля с комментариями


// уберём ссылку с заголовка комментария, чтобы по щелчку на заголовке комментария не переходить на оригинальный друпаловский просмотр комментария


function phptemplate_comment($comment, $links = 0) {
  return _phptemplate_callback('comment', array(
    'author'    => theme('username', $comment),
    'comment'   => $comment,
    'content'   => $comment->comment,
    'date'      => format_date($comment->timestamp),
    'links'     => isset($links) ? theme('links', $links) : '',
    'new'       => $comment->new ? t('new') : '',
    'picture'   => theme_get_setting('toggle_comment_user_picture') ? theme('user_picture', $comment) : '',
    'submitted' => t('Submitted by !a on @b.',
                      array('!a' => theme('username', $comment),
                            '@b' => format_date($comment->timestamp))),
                            
                            
    //'title'     => l($comment->subject, $_GET['q'], NULL, NULL, "comment-$comment->cid")
    'title'     => $comment->subject
    
    
  ));
}












/**
 * Display a message to a user if they are not allowed to fill out a form.
 *
 * @param $node
 *   The webform node object.
 * @param $teaser
 *   If this webform is being displayed as the teaser view of the node.
 * @param $page
 *   If this webform node is being viewed as the main content of the page.
 * @param $submission_count
 *   The number of submissions this user has already submitted. Not calculated
 *   for anonymous users.
 * @param $limit_exceeded
 *   Boolean value if the submission limit for this user has been exceeded.
 * @param $allowed_roles
 *   A list of user roles that are allowed to submit this webform.
 */
function phptemplate_webform_view_messages($node, $teaser, $page, $submission_count, $limit_exceeded, $allowed_roles) {
  global $user;

  $type = 'notice';

  // If not allowed to submit the form, give an explaination.
  if (array_search(TRUE, $allowed_roles) === FALSE && $user->uid != 1) {
    if (empty($allowed_roles)) {
      // No roles are allowed to submit the form.
      $message = t('Submissions for this form are closed.');
    }
    elseif (isset($allowed_roles[2])) {
      // The "authenticated user" role is allowed to submit and the user is currently logged-out.
      $message = t('You must <a href="!login">login</a> or <a href="!register">register</a> to view this form.', array('!login' => url(('user/login'), drupal_get_destination()), '!register' => url(('user/register'), drupal_get_destination())));
    }
    else {
      // The user must be some other role to submit.
      $message = t('You do not have permission to view this form.');
    }
  }

  // If the user has exceeded the limit of submissions, explain the limit.
  if ($limit_exceeded) {
    if ($node->webform['submit_interval'] == -1 && $node->webform['submit_limit'] > 1) {
      $message = t('You have submitted this form the maximum number of times (@count).', array('@count' => $node->webform['submit_limit']));
    }
    elseif ($node->webform['submit_interval'] == -1 && $node->webform['submit_limit'] == 1) {
      $message = t('You have already submitted this form.');
    }
    else {
      $message = t('You may not submit another entry at this time.');
    }
    $type = 'error';
  }

  
  /*
  // If the user has submitted before, give them a link to their submissions.
  if ($submission_count > 0) {
    if (empty($message)) {
      $message = t('You have already submitted this form.') .' '. t('<a href="!url">View your previous submissions</a>.', array('!url' => url('node/'. $node->nid .'/submissions')));
    }
    else {
      $message .= ' '. t('<a href="!url">View your previous submissions</a>.', array('!url' => url('node/'. $node->nid .'/submissions')));
    }
  }
  */
  

  if ($page && isset($message)) {
    drupal_set_message($message, $type);
  }
}











// my changes  убираем жёсткое задание размеров панелей в css
// теперь можно вручную настраивать через файл style.css


function phptemplate_panels_flexible($id, $content, $settings) {
  if (empty($settings)) {
    $settings = panels_flexible_default_panels();
  }

  // Special check for updating.
  if (empty($settings['width_type'])) {
    $settings['width_type'] = '%';
    $settings['percent_width'] = 100;
  }

  if ($id) {
    $idstr = " id='$id'";
    $idcss = "#$id";
  }
  else {
    $idcss = "div.panel-flexible";
  }

  $css = '';
  $output = '';

  for ($row = 1; $row <= intval($settings['rows']); $row++) {
  	
  	
    // original
  	//$output .= "<div class=\"panel-row panel-row-$row clear-block\">\n";
  	
// my changes  	
  	//$output .= "<div class=\"prow prow-$row\">\n";
    //$output .= "<div class=\"panel-row panel-row-$row \">\n";
    //$output .= "<div class=\"prow prow-$row \">\n";
    $output .= "<div class=\"p-row r$row \">\n";
  	
    
    if($row == 2)
    	$col_seq = array(2,1,3,4,5,6,7,8);
    else 
    	$col_seq = array(1,2,3,4,5,6,7,8);
    	
    for ($count = 0, $col = $col_seq[$count]; $col <= intval($settings["row_$row"]["columns"]); $count++, $col = $col_seq[$count]) 
  	//for ($col = 1; $col <= intval($settings["row_$row"]["columns"]); $col++) 
  	{
      // We do a width reduction formula to help IE out a little bit. If width is 100%, we take 1%
      // off the total; by dividing by the # of columns, that gets us the reduction overall.
      $reduce = 0;
      if ($settings['width_type'] == '%' && $settings['percent_width'] == 100) {
        $reduce = 1 / $settings["row_$row"]["columns"];
      }
      if ($col == 1) {
        if (intval($settings["row_$row"]["columns"]) == 1) {
          //original
          //$class = 'panel-col-only';
//my changes          
          $class = 'only';
        }
        else {
          //original
          //$class = 'panel-col-first';
// my changes          
          $class = 'first';
        }
      }
      elseif ($col == intval($settings["row_$row"]["columns"])) {
        //original
      	//$class = 'panel-col-last';
// my changes      	
        $class = 'last';
      }
      else {
      	// original
        //$class = 'panel-col-inside';
//my changes        
        $class = 'inside';
      }
      
      // original
      //$output .= "<div class=\"panel-col panel-col-$col $class\">\n";

      
// my changes      
      //$output .= "<div class=\"p-col $col $class\">\n";
      $output .= "<div class=\"p-col c$col $class\">\n";
      
      	// original
      	//$output .= "<div class=\"inside\">" . $content["row_${row}_$col"] . "</div>\n";

// my changes --------------------
      	
      	$output .= $content["row_${row}_$col"];
      
      
      
      $output .= "</div>\n"; // panel-col-$col
      
      //original
      //$css .= "$idcss div.panel-row-$row div.panel-col-$col { width: " . ((intval($settings["row_$row"]["width_$col"])) - $reduce) . $settings["width_type"] ."; }\n";
    }
    $output .= "</div>\n"; // panel-row-$row
  }

  // Add our potential sidebars
  if (!empty($settings['sidebars']['left']) || !empty($settings['sidebars']['right'])) {
    // provide a wrapper if we have a sidebar
    $output = "<div class=\"panel-sidebar-middle panel-sidebar\">\n$output</div>\n";
    
    //original
	/*
    if ($settings['sidebars']['width_type'] == '%') {
      $css .= "$idcss div.panel-flexible-sidebars div.panel-sidebar-middle { width: " . (intval($settings['percent_width']) - intval($settings['sidebars']['left_width']) - intval($settings['sidebars']['right_width'])) . "; }\n";
    }
	*/    
    
  }

  if (!empty($settings['sidebars']['left'])) {
    $size = intval($settings['sidebars']['left_width']) . $settings['sidebars']['width_type'];
    
    // original
    //$output = "<div class=\"panel-sidebar panel-sidebar-left panel-col panel-col-first\"><div class=\"inside\">\n" . $content["sidebar_left"] . "</div>\n</div>\n" . $output;
    

// my changes -------------    
    
    $output = "<div class=\"panel-sidebar panel-sidebar-left panel-col panel-col-first\">" . $content["sidebar_left"] . "\n</div>\n" . $output;

    
    //original
    /*
    $css .= "$idcss div.panel-flexible-sidebars div.panel-sidebar-left { width: $size; margin-left: -$size; }\n";
    $css .= "$idcss div.panel-flexible-sidebars { padding-left: $size; }\n";
    // IE hack
    $css .= "* html $idcss div.panel-flexible-sidebars div.panel-sidebar-left { left: $size; }\n";
    */
  }

  if (!empty($settings['sidebars']['right'])) {
    $size = intval($settings['sidebars']['right_width']) . $settings['sidebars']['width_type'];
    
    // original
    //$output .= "<div class=\"panel-sidebar panel-sidebar-right panel-col panel-col-last\"><div class=\"inside\">\n" . $content["sidebar_right"] . "</div>\n</div>\n";

    
// my changes -------------    
    $output .= "<div class=\"panel-sidebar panel-sidebar-right panel-col panel-col-last\">" . $content["sidebar_right"] . "\n</div>\n";
    
    
    //original
    /*
    $css .= "$idcss div.panel-flexible-sidebars div.panel-sidebar-right { width: $size; margin-right: -$size; }\n";
    $css .= "$idcss div.panel-flexible-sidebars { padding-right: $size; }\n";
    */
  }

  // Wrap the whole thing up nice and snug
  $sidebar_class = (!empty($settings['sidebars']['left']) || !empty($settings['sidebars']['right'])) ? ' class="panel-flexible-sidebars"' : '';

  
  
  
  
  // original
  //$output = "<div class=\"panel-flexible clear-block\" $idstr>\n<div". $sidebar_class .">\n" . $output . "</div>\n</div>\n";

  
// my changes --------------------------------------  
  
  if($sidebar_class)
  	$output = "<div class=\"panel-flexible clear-block\" $idstr>\n<div". $sidebar_class .">\n" . $output . "</div>\n</div>\n";
  else
  	$output = "<div class=\"p-flex\" $idstr>\n" . $output . "</div>\n";
  
  
  
  
  
  
// my changes  убираем жёсткое задание размеров панелей в css

  
  //original
  //drupal_set_html_head("<style type=\"text/css\" media=\"all\">\n$css</style>\n");
  return $output;
}








function phptemplate_panels_default_style_render_panel($display, $panel_id, $panes, $settings) {
  $output = '';

  $print_separator = FALSE;
  foreach ($panes as $pane_id => $content) {
    // Add the separator if we've already displayed a pane.
    
    //original
    /*
    if ($print_separator) {
      $output .= '<div class="panel-separator"></div>';
    }
    */
    $output .= $text = panels_render_pane($content, $display->content[$pane_id], $display);

    // If we displayed a pane, this will become true; if not, it will become
    // false.
    $print_separator = (bool) $text;
  }

  return $output;
}




/**
 * Themes the message body.
 *
 * @param $body
 *   The message body to theme.
 * @param $mailkey
 *   An identifier for the message.
 * @return
 *   The themed HTML message body.
 */
function phptemplate_mimemail_message($body, $mailkey = NULL) {
	
// убираем добавление всех стилей сайта
	
  $output = '<html><head>';
  $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

  // attempt to include a mail-specific version of the css.
  // if you want smaller mail messages, add a mail.css file to your theme
  $styles = path_to_theme() .'/mail.css';

  $output .= '<style type="text/css"><!--';
  if (!file_exists($styles)) {
    // embed a version of all style definitions
    
    
// my changes
    
    // original
    //$styles = preg_replace('|<style.*"'. base_path() .'([^"]*)".*|', '\1', drupal_get_css());
  }
  foreach (explode("\n", $styles) as $style) {
    if (file_exists($style)) $output .= file_get_contents($style);
  }
  $output .= '--></style></head><body id="mimemail-body"><div id="center"><div id="main">'. $body .'</div></div></body></html>';
  // compress output
  return preg_replace('/\s+|\n|\r|^\s|\s$/', ' ', $output);
}
