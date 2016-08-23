<!-- <div class="test">it's a cart complete</div> -->


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
 
 
 
print '<div class="cart_review">';
print '<div class="title">Ваш заказ <br>отправлен на обработку!</div>';
print $zp_cart_cart;
print '</div>';
 
?>
