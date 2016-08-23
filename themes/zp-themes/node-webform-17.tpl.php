<!-- <div class="test">it's a webform new shop</div> -->


<?php


// paths for my cart and order nodes

//MY_CART_NODE, MY_CART_CHECKOUT_NODE, MY_CART_REVIEW_NODE, MY_CART_COMPLETE_NODE
//MY_ORDER_HISTORY_NODE, MY_ORDER_REVIEW_NODE


require_once('sites/all/modules/_ZP_modules/zp_node_paths_aliases/zp_node_paths_aliases.inc');

//drupal_set_message("test cart path, MY_CART_NODE = " . MY_CART_NODE, 'error');








//if(arg(1) == '228') // если форма на отдельной странице, а не в блоке (например, после ошибки при заполнении формы)
if(arg(1) == MY_ZP_ADDSHOP_NODE_NUM) // если форма на отдельной странице, а не в блоке (например, после ошибки при заполнении формы)
{


	
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
	

	
	
	print '<div class="new_shop alone">';    
	print '<div class="title">' . $title . '</div>';    
}
else 
	print '<div class="new_shop">';
     
	print '<div class="block_head toggle-anchor"></div>';
	print '<div class="block_body toggle-content">'; 
	
	
	print '<div class="caption">' . $node->content['body']['#value'] . '</div>';
	print $node->content['webform']['#value'];
	
?>
	
</div>
	<?php 
	print '<div class="bottom"></div>';		
	
	//print $node->body ?>
</div>
