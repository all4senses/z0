<?php 
	global $user;
	if($user->uid != 1)
	     drupal_goto('http://www.zapokupkami.com');
	
	echo '<div class="title 1">' . $title . '</div>';
	echo '<div class="body">' . $body . '</div>';
?>