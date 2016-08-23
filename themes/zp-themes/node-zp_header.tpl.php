<?php 

// paths for my cart and order nodes

//MY_CART_NODE, MY_CART_CHECKOUT_NODE, MY_CART_REVIEW_NODE, MY_CART_COMPLETE_NODE
//MY_ORDER_HISTORY_NODE, MY_ORDER_REVIEW_NODE
require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');



if(arg(1) == MY_HOME_PAGE_NODE_NUM) // если домашняя страница, открываем тег с классом home
	echo '<div class="home">';



	echo '<div id = "head_back1"> </div>';
	echo '<div id = "head_back2"> </div>';



if(arg(1) == MY_HOME_PAGE_NODE_NUM)
	echo '<div id="main_pic"><img alt="За Покупками! - Доставка продуктов и других товаров на дом и в офис, Харьков" src="sites/all/themes/zp-themes/zp-two/img4/main_pic.jpg"/></div>';
	
	//print '<div id="current_place">' . $current_place . '</div>';
	//print '<div id="current_place">' . 'Выбрать город <br/>или район' . '</div>';
	//echo '<div id="current_place">' . 'Выбрать город, <br/>район, магазин' . '</div>';
	//echo $cities_menu;

	//echo '<div class="test">second menu</div>';
	//echo $second_menu;

/*
	if($welcome)
	{
		//echo $main_pic;
		echo '<div id="main_pic"></div>';

		echo $welcome;
	}


	if($current_page_info)
		print '<div id="current_page_info">' . $current_page_info . '</div>';
	else
	{
		if($explain)
			print $explain;
		else 
			print '<div id="welcome"><div class="caption">' . 'Ваша Персональная Служба Доставки' . '</div>' . 'продуктов и других товаров или услуг 
из известных Вам магазинов и прочих заведений  к Вам домой или в офис.' . '</div>';
	}
 */


if(arg(1) == MY_HOME_PAGE_NODE_NUM) // если домашняя страница, закрываем тег с классом home
	print '</div>';
/*
else // выводим логотип (вернее, заглавную картинку по магазину, которая может содержать и логотип, и иллюстрацию)
	{
		if(file_exists($_SERVER['DOCUMENT_ROOT'] . base_path() . 'files/shops/' . $shop_id . '/' . $shop_id . '-header-pic.jpg')) // если файл существует
			echo '<div id="shop_logo_upmenu">' . theme('imagecache', 'shop_header_pic', base_path() . 'files/shops/' . $shop_id . '/' . $shop_id . '-header-pic.jpg') . '</div>'; //, $attributes); 
		else
			echo '<div id="main_pic_default"></div>';
	}

*/

echo '<div id="bread">' . $c_breadcrumb . '</div>';

// add delay on mouseout
drupal_add_js('sites/all/modules/_Menu/smenu_hover/secondmenu_hover.js');

//echo '<div class="test">user menu</div>';
//print $user_menu;


//echo '<div class="test">first menu</div>';
//echo $first_menu;

////print $zp_header_menu_01;




/*

// добавим возможность переключения видимости блоков на страницу
zp_block_toggle_start(); 


// добавим предупреждение о том, что пользователь был выгружен из системы после определённого заданного в системе времени бездействия  
if($time = $_GET['alo'])
	{
  
  		//drupal_add_js(drupal_get_path('module', 'autologout') .'/autologout.js');
  		
   		//drupal_add_js('alert("Drupal Owns You")', 'inline');
  
   		drupal_add_js(	'$(document).ready(
   											function() 
   											{
   												alert("В целях безопасности после ' . $time . 'мин бездействия Вы были выгружены из своей учётной записи. Для продолжения полноценного использования сайта, пожалуйста, авторизуйтесь заново (верхний правый угол экрана).");
  											}
  									   );', 
  				'inline');
   
   
   
	}
	
	
*/	
	
	
	
/*	

//тест изменяемого элемента,
//но он в друпале не работает, так как друпал поддерживает пока только jquery 1.2 (5-й друпал) или 1.3 (6-й)
// и пока никакие трики не работают
	
drupal_add_css('files/js/jquery_resizable/jquery.ui.all.css');
drupal_add_css('files/js/jquery_resizable/demos.css');


//drupal_add_js('files/js/jquery_resizable/jquery-1.4.2.js');

drupal_add_js('files/js/jquery_resizable/jquery.ui.core.js');
drupal_add_js('files/js/jquery_resizable/jquery.ui.widget.js');
drupal_add_js('files/js/jquery_resizable/jquery.ui.mouse.js');
drupal_add_js('files/js/jquery_resizable/jquery.ui.resizable.js');

	
*/	
	
	
	
	
	
	
	
	
	

/*
// код для изменяемой высоты блока с корзиной покупателя

drupal_add_js('files/js/resizable_block/interface.js');

echo '<script type="text/javascript">';

echo "
$(document).ready(
	function()
	{
		$('#resizeMe').Resizable(
			{
				minWidth: 50,
				minHeight: 50,
				handlers: {
					s: '#resizeS'
				},
				onResize: function(size)
				{
					$('.resize-content', this).css('max-height', '3000px');
					$('.resize-content', this).css('height', size.height - 6 + 'px');
				}
			}
		);
	}
);


</script>

";

*/

?>



