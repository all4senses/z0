<!-- <div id="test">footer my 1</div> -->

<a id="home_link" href="http://www.zapokupkami.com">	
        <div class="text1">Удобная Служба Доставки</div>
        <div id="test">За Покупками!</div>
        <div class="text2">в Ваши любимые магазины!</div>
</a>


<?php

if(arg(1) == MY_HOME_PAGE_NODE_NUM) // если домашняя страница, открываем тег с классом home
	echo '<div class="home">';
	

        echo $cities_menu;

	if($welcome)
            echo $welcome;
	
	if($current_page_info)
            print '<div id="current_page_info">' . $current_page_info . '</div>';
	else
            echo $explain;

        echo $explain_default;
	
        // выводим логотип (вернее, заглавную картинку по магазину, которая может содержать и логотип, и иллюстрацию)
        if(isset($header_pic))
            echo $header_pic;

	echo '<!-- <div class="test">user menu</div> -->',
	'<div id="user-block">',  $user_menu, '</div>',
	
	'<!-- <div class="test">first menu</div> -->',
	$first_menu;

	
?>

<div id = "footer_back1"> 
    <div class="footer">
            <?php

            echo '<div class="copy"><a href="' , $node->field_links[0]['url'] , '">' , $node->field_links[0]['title'] , '</a></div>',

            $node->content['body']['#value'],

            '<div id="block1">',
                    '<a href="' . $node->field_links[1]['url'] . '">' . $node->field_links[1]['title'] . '</a><br>',

            '</div>',

            '<div id="block2">',
                    '<a href="' , $node->field_links[2]['url'] , '">' , $node->field_links[2]['title'] , '</a><br>',
                    '<a href="' , $node->field_links[3]['url'] , '">' , $node->field_links[3]['title'] , '</a><br>',
                    '<a href="' , $node->field_links[4]['url'] , '">' , $node->field_links[4]['title'] , '</a><br>',
                    //'<a href="' , $node->field_links[5]['url'] , '">' , $node->field_links[5]['title'] , '</a><br>',
                '</div>';

            ?>
            <!-- Google translate -->
            <div id="google_translate_element"></div><script type="text/javascript">
            function googleTranslateElementInit() {
              new google.translate.TranslateElement({pageLanguage: 'ru', includedLanguages: 'ar,de,en,es,fr,ru,uk,zh-CN', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, multilanguagePage: true, gaTrack: true, gaId: 'UA-3285209-14'}, 'google_translate_element');
            }
            </script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
            
    </div>
</div>


<?php

echo '<!-- <div class="test">second menu</div> -->';
echo $second_menu;

if(arg(1) == MY_HOME_PAGE_NODE_NUM)
        echo '</div>';	
		

// добавим возможность переключения видимости блоков на страницу
zp_block_toggle_start(); 

// добавим предупреждение о том, что пользователь был выгружен из системы после определённого заданного в системе времени бездействия  
if($time = $_GET['alo'])
	zp_functions_show_logoutAlert($time);

// superfish для меню
drupal_add_css('sites/all/modules/_Menu/superfish/css/superfish.css');
// js menu with delay - very good!!!
drupal_add_js('sites/all/modules/_Menu/superfish/js/hoverIntent.js'); 
drupal_add_js('sites/all/modules/_Menu/superfish/js/superfish.js');

drupal_add_js('sites/all/modules/_ZP_modules/zp_functions/zp_functions.js');
    
// блок AddThis
//drupal_add_js('sites/all/modules/_SEO/addthis/addthis-config.js');
drupal_set_html_head('<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=all4senses"></script>');

// add Google +1 button script 
// later on a page should be placed bitton tags itself
drupal_set_html_head('<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>');

// Social links
echo zp_functions_get_social_links(array('class_head' => ' head'));
