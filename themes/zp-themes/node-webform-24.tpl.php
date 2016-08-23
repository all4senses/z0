<!-- <div class="test">it's a webform new client</div> -->


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


 
echo '<div class=wf_preface>';
	echo '<div class="title">' . $title . '</div>';   
        
        
        $link = zp_functions_continue_shopping_link();
	echo '<div class="links">' . l('Продолжить покупки', 'node/' . $link['nid'], array('class' => 'links')) . '</div>'; 
  	
	echo $node->content['body']['#value'];
echo '</div>';
	
echo $node->content['webform']['#value'];

//print $node->body 


?>