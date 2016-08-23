<div class="error_page">

	<?php 
	
	//drupal_set_header('HTTP/1.1 404 Not Found');
	//drupal_set_header('Status: 404 Not Found');
	
  if(strpos($_GET['u'], '500.shtml') !== FALSE)
    drupal_goto('');	      
  
	drupal_set_header('HTTP/1.1 410 Gone');
	drupal_set_header('Status: 410 Gone');



	//echo '<div class="title">' . $title . '</div>';
	echo '<div class="title">Страница не найдена!</div>';
	
        //echo '<div class="body">' . $body . '</div>';

        echo '<div class="body"><br>К сожалению, запрошенная Вами страница "www.zapokupkami.com/' . $_GET['u'] . '" не найдена на сайте. Вероятно, она была переименована, перемещена или удалена.<br><br>';
         
        //global $user;
        //if($user->uid == 1)
        //{
        	if($_GET['n'])
        	{
        		
        		echo '<div class="no">Но...</div>';
        		echo '<br>Возможно, Вам подойдёт эта страница:<br>' . l($_GET['t'], 'node/' . $_GET['n']) . '<br>';
        		echo 'Если предложенный вариант Вас не устраивает, попробуйте ввести другой адрес или воспользуйтесь меню нашего сайта.';
        		
        	}
        	else 
        		echo 'Попробуйте ввести другой адрес или воспользуйтесь меню нашего сайта.';
        //}
        //else 
	    //   echo 'Попробуйте ввести другой адрес или воспользуйтесь меню нашего сайта.';
        
	echo '</div>';
	//echo '<div class="links">' . l(t('Вернуться на предыдущую страницу'), $_SERVER['HTTP_REFERER']) . '</div>';
	


	?>

</div>