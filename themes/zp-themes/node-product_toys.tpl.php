
  
<div class="test">product_toys</div>

<?php 

//if teaser ---------------------------------------------------------------------------------

?>

<?php if ($teaser == 1): ?>
 <div class="product_teaser toys">

    <a href="<?php print $node_url ?>">
      <div id="title"><?php print check_plain($title) ?></div>
    </a>
    
    <?php print $node->weight ?>
    <?php print $node->weight_units ?>
     
    <a href="<?php print $node_url ?>">
	  <div class="image">
	    <?php if($node->field_p_toys_img[0]['title'])
	      {?>
   		   <h3 class="label-image"><?php //print $node->field_p_toys_img[0]['title'] ?></h3> 
   		  <?php }; ?>

   		  <?php print theme('imagecache', 'product_img_teaser', $node->field_p_toys_img[0]['filepath'], $node->field_p_toys_img[0]['alt'], $node->field_p_toys_img[0]['title']); //, $attributes); ?> 
   	   </div> <?php /* end of image */ ?>
    
   	</a>
   	
    
    <div id="descr"><?php print check_plain($node->field_p_toys_descr[0]['value']) ?></div>
    

    
    
    <?php 
    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 

       //$show_price = 1;
       if($show_price < 0)
         print $c_form1 . 'no_sell">' . 'Доставка из этого магазина для Вас не доступна ' . l(t('<?>'), 'user/'.$user->uid, array('title' => t('Почему?'))) .'</div></div></form></div>';    
       else 
       { 
		 // убираем метку
       	 $c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           
        
       } 

       //print $node->content['add_to_cart']['#value'] 
       
    ?>
  

 
 </div>
<?php endif; // end of teaser ?>











<?php 

//if body (page) ---------------------------------------------------------------------------------

?>


<?php if ($page == 1): ?>
 <div class="product_body toys">
   <div id="txt">
     
     <div id="title"><?php print check_plain($title) ?></div>
     
     <?php print $node->weight ?>
     <?php print $node->weight_units ?>
    
     <div id="descr"><?php print check_plain($node->field_p_toys_descr[0]['value']) ?></div>
     
   </div>
   
   
   <?php 
    
       $c_form1 = explode('edit-qty-wrapper', $node->content['add_to_cart']['#value']);
       $c_form2 = $c_form1[1];
       $c_form1 = $c_form1[0]; 

       //$show_price = 1;
       if($show_price < 0)
         print $c_form1 . 'no_sell">' . 'Доставка из этого магазина для Вас не доступна ' . l(t('<?>'), 'user/'.$user->uid, array('title' => t('Почему?'))) .'</div></div></form></div>';    
       else 
       { 
		 // убираем метку
       	 $c_form2 = ereg_replace('<label for="edit-qty">Quantity: </label>', '', $c_form2);
         
       	 // добавляем единицу отгрузки (шт, кг и т.д.) для того, чтобы понимать, чем измеряется одна единица товара. Если это кг, то тогда возможно дробное значение (0.3кг колбасы), которые пользователь может указать
       	 $c_form2 = explode('</div>', $c_form2, 2);
       	 $c_form2 = $c_form2[0] .'</div><div class="sell_measure">' . $node->field_sell_measure[0]['view'] . '</div>' . $c_form2[1];
       	 
       	 print $c_form1 . 'sell_price">' . uc_currency_format($node->sell_price) . '</div><div id="dost_prise">' . $show_price . '</div><div id="edit-qty-wrapper' . $c_form2;           
        
       } 

       //print $node->content['add_to_cart']['#value'] 
       
   ?>   
   
   
   <div class="images">

      <?php for($i=0; $node->field_p_toys_img[$i]['view']; $i++) { ?>  
	   <div class="image <?php print $i ?>">
	    <?php if($node->field_p_toys_img[$i]['title']) 
	      {?>
   		   <h3 class="label-image"><?php //print $node->field_p_toys_img[$i]['title'] ?></h3> 
   		  <?php }; ?>

		  <?php //print $node->field_p_toys_img[$i]['view'] ?>

  		  <a href="<?php print base_path() . $node->field_p_toys_img[$i]['filepath']?>" <?php print 'title="Увеличить и посмотреть другие фото..."'; ?> rel="lightbox[roadtrip]['<?php if($node->field_p_toys_img[$i]['alt']) print $node->field_p_toys_img[$i]['alt'] ?>']" <?php if($i>0) print 'class="lightbox_hide_image"'?> rel2="body-images">
		   <?php print theme('imagecache', 'product_img_body', $node->field_p_toys_img[$i]['filepath'], $node->field_p_toys_img[$i]['alt'], $node->field_p_toys_img[$i]['title']); //, $attributes); ?> 
		  </a>

		  </div> <?php /* end of image $i */ ?>
	  <?php }; ?>
	  
    </div> <?php /* end of images */ ?>
 
    
  
   
 </div>
<?php endif; // end of body ?>  