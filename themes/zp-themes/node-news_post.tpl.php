<!-- <div class="test">news POST via tpl </div> -->



<?php 



// paths for my cart and order nodes

//MY_CART_NODE, MY_CART_CHECKOUT_NODE, MY_CART_REVIEW_NODE, MY_CART_COMPLETE_NODE
//MY_ORDER_HISTORY_NODE, MY_ORDER_REVIEW_NODE


require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');

//drupal_set_message("test cart path, MY_CART_NODE = " . MY_CART_NODE, 'error');








if ($teaser == 1): 

?>



	<?php 
	//if(arg(1) == 238)
	if(arg(1) == MY_ZP_NEWS_NODE_NU)
		print '<div class="news post teaser body">';
	else
		print '<div class="news post teaser">';
		
	
	print '<div class="title">' . l($node->title, $node->path, array('class' => 'blog_title')) . '</div>'; 
	print '<div class="body">' . $node->teaser . '</div>'; 
	print '<div class="author date">' . format_date($node->created, $format="%m/%d/%Y") . '</div>';
	
	//if(arg(1) == 227)
	if(arg(1) == MY_HOME_PAGE_NODE_NUM)
		print '<div class="links">' . l('Читать далее, оставить комментарий', $node->path, array('class' => 'blog_title')) . '</div>';
	else
		print '<div class="links">' . l('Читать далее, оставить комментарий', $node->path, array('class' => 'blog_title')) . ' (комментариев: ' . $node->comment_count . ')</div>';
	//print '<div class="links">' . l('Комментариев: ' . $node->comment_count, $node->path, array('class' => 'blog_title')) . '</div>';

	?>
	
</div>

<?php endif; // end of teaser ?>










<?php if ($page == 1): ?>






<?php 

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

?>






<div class="news post page">

	<?php 

	print '<div class="title">' . $title . '</div>';
	
	print '<div class="body">' . $body . '</div>';
	print '<div class="author date">' . format_date($node->created, $format="%m/%d/%Y") . '</div>';

	//print '<div class="links">' . l(t('Return to news'), 'node/238', array('class' => 'blog_link')) . '</div>';
	print '<div class="links">' . l(t('Return to news'), MY_ZP_NEWS_NODE, array('class' => 'blog_link')) . '</div>';


	?>

</div>
	
<?php endif; // end of body ?>
