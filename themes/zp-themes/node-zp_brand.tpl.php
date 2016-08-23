<!-- <div class="test_">zp_brand</div> -->

<?php 

//if teaser ---------------------------------------------------------------------------------

?>

<?php if ($teaser == 1): ?>



<?php endif; // end of teaser ?>











<?php 

//if body (page) ---------------------------------------------------------------------------------

?>


<?php if ($page == 1): ?>
 <div class="zp_brand_body">
     <div class="title"><?php print check_plain($title) ?></div>
     <div class="descr"><div class="label"><?php print 'Описание: </div>' . $node->field_p_clothes_descr[0]['view'] ?></div>
     


   
   
    
   <div class="images">

      <?php for($i=0; $node->field_p_clothes_img[$i]['view']; $i++) { ?>  
	   <div class="image<?php 
	   if($i==0) 
	     print '_big';
	   else  
	     print '_small ' . $i;
	   ?>">
	    <?php if($node->field_p_clothes_img[$i]['title']) 
	      {?>
   		   <h3 class="label-image"><?php //print $node->field_p_clothes_img[$i]['title'] ?></h3> 
   		  <?php }; ?>

		  <?php //print $node->field_p_clothes_img[$i]['view'] ?>

  		  <a href="<?php print base_path() . $node->field_p_clothes_img[$i]['filepath']?>" <?php print 'title="Увеличить и посмотреть другие фото..."'; ?> rel="lightbox[roadtrip]['<?php if($node->field_p_clothes_img[$i]['alt']) print $node->field_p_clothes_img[$i]['alt'] ?>']" <?php if($i>0) print '_class="lightbox_hide_image"'?> rel2="body-images">
		   <?php 
 			 if($i == 0)  
		       print theme('imagecache', 'product_img_body', $node->field_p_clothes_img[$i]['filepath'], $node->field_p_clothes_img[$i]['alt'], $node->field_p_clothes_img[$i]['title']); //, $attributes); 
		     else  
		       print theme('imagecache', 'product_img_body2', $node->field_p_clothes_img[$i]['filepath'], $node->field_p_clothes_img[$i]['alt'], $node->field_p_clothes_img[$i]['title']); //, $attributes); 
		   ?> 
		  </a>

		  </div> <?php /* end of image $i */ ?>
	  <?php }; ?>
	  
    </div> <?php /* end of images */ ?>    
    
    
    
 </div>
<?php endif; // end of body ?>  