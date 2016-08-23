<!-- <div class="test">news via tpl</div> -->

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



<div class="news">

<?php 

	print '<div class="title">' . $title . '</div>';
	print '<div class="body">' . $node->body . '</div>'; 

	$view = views_get_view('news_view');
	$limit = 0;
	print views_build_view('embed', $view, array(), FALSE, $limit);

?>

</div>