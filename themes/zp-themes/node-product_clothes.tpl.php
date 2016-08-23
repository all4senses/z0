<div class="test">product_clothes</div>

<?php 

//if teaser ---------------------------------------------------------------------------------

//print '<PRE>';
//print_r($node);
//print '</PRE>';

//print '<PRE>';
//print_r($_SESSION);
//print '</PRE>';
	
?>


<?php if ($teaser == 1): ?>
 <div class="product_teaser clothes ' <?php if ($show_price < 0) print ' no_dost' ?>">

    <a href="<?php print $node_url ?>" title="Перейти к подробному описанию товара <?php print $title . ', ' . $node->weight . ' ' . $node->weight_units ?>">
      <div class="title">
       <?php 
        //print check_plain($title);
        print $title;
        print ', <span class="ves">' . $node->weight . ' ' . $node->weight_units . '</span>';
       ?>
      </div>
      
      
	  <div class="image">
	   <table><td>
   		  <?php print theme('imagecache', 'product_img_teaser', $node->field_product_img[0]['filepath'], $node->field_product_img[0]['alt'], $node->field_product_img[0]['title']); //, $attributes); ?> 
   	   </table></td>
   	  </div> <?php /* end of image */ ?>
       
   	</a>
   	


   	
   	
    <?php 
    
    
    
       print '<div class="a_to_c_fixed"><table><td class="a_to_c_td">';
    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 

       //$show_price = 1;
       if($show_price < 0)
        {
         //print $c_form1 . 'no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('< ? >'), 'user/'.$user->uid, array('title' => t('Почему?'))) . '</div></div></form></div>';    
        print '<div class="no_dost_descr">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'user/'.$user->uid, array('title' => t('Почему?'))) . '</div>';
         
        } 
       else 
       { 
		 // убираем метку
       	 $c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
       	 $c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину в указанном количестве"', $c_form2);
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure"' . ' title="Стоимость без надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 print $c_form1 . 'sell_price"' . ' title="Стоимость без надбавки за дополнения/опции (если они доступны). Стоимость доставки рассчитывается также на основе стоимость товара без надбавки за возможные дополнения."' . ' >' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div><div class="edit-qty-wrapper' . $c_form2;           

       	 //print '<div class="sell_price">' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div>';
       	 //print $c_form1 . 'xxx"></div><div class="edit-qty-wrapper' . $c_form2;           
        
       } 

       print '</td></table></div>';
       
       //print $node->content['add_to_cart']['#value'] 
    
    
    ?>
  



 
 </div>
<?php endif; // end of teaser ?>











<?php 

//if body (page) ---------------------------------------------------------------------------------

?>


<?php if ($page == 1): ?>
 <div class="product_body clothes">
   <div class="txt">
     
     <div class="title"><?php print $title ?></div>
    
     <div class="ves"><div class="label"><?php print 'Вес/объём: </div>' . $node->weight . ' ' . $node->weight_units ?></div>
     
     <div class="proizv" title="Перейти на страницу с описанием производителя/бренда"><div class="label">
       <?php 
         if($node->field_zp_brand_link[0]['view'])   
           print 'Производитель/бренд: </div>' . $node->field_zp_brand_link[0]['view'] . ' <<';
         else 
           print 'Производитель/бренд: </div>' . $node->field_zp_brand_nolink[0]['view'];      
       ?></div>
           
     <?php 
     	if($node->field_product_descr[0]['view'])
     		print '<div class="descr"><div class="label">Описание: </div>' . $node->field_product_descr[0]['view'] . '</div>';
     ?>
   
     
     
     
     <?php 
     
     global $user;
     if ($user->uid == 1): ?>
     
     <div class="art_and_bar"><div class="label"><?php print 'Внутренний артикул zp SKU: </div>' . $node->model ?></div>
     <?php
     if($node->field_zp_art_postav[0]['view']) 
     	print '<div class="art_and_bar"><div class="label"> Артикул поставщика: </div>' . $node->field_zp_art_postav[0]['view'] . '</div>';
     if($node->field_zp_art_proizv[0]['view'])
     	print '<div class="art_and_bar"><div class="label">Артикул производителя: </div>' . $node->field_zp_art_proizv[0]['view'] . '</div>';
     if($node->field_zp_art_shop[0]['view'])
     	print '<div class="art_and_bar"><div class="label">Артикул магазина: </div>' . $node->field_zp_art_shop[0]['view'] . '</div>';
     if($node->field_zp_bar_postav[0]['view'])
     	print '<div class="art_and_bar"><div class="label">Штрих-код поставщика: </div>' . $node->field_zp_bar_postav[0]['view'] . '</div>';
     if($node->field_zp_bar_proizv[0]['view'])
     	print '<div class="art_and_bar"><div class="label">Штрих-код производителя: </div>' . $node->field_zp_bar_proizv[0]['view'] . '</div>';
     if($node->field_zp_bar_shop[0]['view'])
     	print '<div class="art_and_bar"><div class="label">Штрих-код магазина: </div>' . $node->field_zp_bar_shop[0]['view'] . '</div>';
     	
     if($node->field_source_of_pics[0]['view'])
     	print '<br><div class="art_and_bar"><div class="label">Zp-артикул элемента-источника картинок: </div>' . $node->field_source_of_pics[0]['view'] . '</div><br>';
     
     ?>
     
     
     <?php endif; // end of teaser ?>
   
   
   
   
     <?php 
    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 

       //$show_price = 1;
       if($show_price < 0)
         print $c_form1 . 'no_sell">' . 'Доставка этого товара для Вас не доступна ' . l(t('(?)'), 'user/'.$user->uid, array('title' => t('Почему?'))) .'</div></div></form></div>';    
       else 
       { 
		 // убираем метку
       	 $c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
       	 //$c_form2 = ereg_replace('class="form-submit node-add-to-cart"', 'class="form-submit node-add-to-cart" title="Добавить этот товар в корзину"', $c_form2);
       	 
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 //print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           
       	 
       	 print $c_form1 . 'sell_price"  title="' . $dost_descr .'"><div class="label">Стоимость: </div>' . uc_currency_format($node->sell_price) . ' + доставка ' . $show_price . '</div><div class="atc" title="Добавить товар в корзину в указанном количестве"><div class="edit-qty-wrapper' . $c_form2 . '</div>';

       	 //print '<div class="sell_price">' . uc_currency_format($node->sell_price) . '</div><div class="dost_prise"' . ' title="' . $dost_descr .'"> +доставка ' . $show_price . '</div>';
       	 //print $c_form1 . 'xxx"></div><div class="edit-qty-wrapper' . $c_form2;           
       	 
        
       } 

       //print $node->content['add_to_cart']['#value'] 
       
     ?>

   </div>
   
   
    
   <div class="images">

      <?php for($i=0; $node->field_product_img[$i]['filepath']; $i++) { ?>  
	   <div class="image<?php 
	   if($i==0) 
	     print '_big';
	   else  
	     print '_small ' . $i;
	   ?>">
	    <?php if($node->field_product_img[$i]['filepath']) 
	      {?>
   		   <h3 class="label-image"><?php //print $node->field_product_img[$i]['title'] ?></h3> 
   		  <?php }; ?>

		  <?php //print $node->field_product_img[$i]['view'] ?>

  		  <a href="<?php print base_path() . $node->field_product_img[$i]['filepath']?>" <?php print 'title="Увеличить и посмотреть другие фото..."'; ?> rel="lightbox[roadtrip]['<?php if($node->field_product_img[$i]['alt']) print $node->field_product_img[$i]['alt'] ?>']" <?php if($i>0) print '_class="lightbox_hide_image"'?> rel2="body-images">
		   <?php 
 			 if($i == 0) // первая картинка большая, остальные маленькие
		       print theme('imagecache', 'product_img_body', $node->field_product_img[$i]['filepath'], $node->field_product_img[$i]['alt'], $node->field_product_img[$i]['title']); //, $attributes); 
		     else  
		       print theme('imagecache', 'product_img_body2', $node->field_product_img[$i]['filepath'], $node->field_product_img[$i]['alt'], $node->field_product_img[$i]['title']); //, $attributes); 
		   ?> 
		  </a>

		  </div> <?php /* end of image $i */ ?>
	  <?php }; ?>
	  
    </div> <?php /* end of images */ ?>    
    
    
    
 </div>
<?php endif; // end of body ?>  