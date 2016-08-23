<div class="test">blog zp via tpl</div>

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





echo '<div class="blog">';
echo '<div class="title">' . $title . '</div>';
echo '<div class="body">' . $node->body . '</div>'; 
	
$view = views_get_view('blog_zp_view');
if($view)
{
	

	$limit = 0;
	echo views_build_view('embed', $view, array(), FALSE, $limit);
	
}
echo '</div>';
?>