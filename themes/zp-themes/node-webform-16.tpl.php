<!-- <div class="test">it's a webform feedback</div> -->


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
	echo $node->content['body']['#value'];
echo '</div>';
	
print $node->content['webform']['#value'];

//print $node->body 


?>