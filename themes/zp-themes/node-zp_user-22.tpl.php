<div class="test">it's a user settings page - my variant</div>

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


echo '<div class="title">Ваши личные настройки сайта</div>';
//echo '<div class="explain">Пожалуйста, измените необходимые параметры и затем сохраните изменения.</div>';
echo $u_settings;

?>
