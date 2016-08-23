<div class="error_page">

	<?php 
	
	drupal_set_header('HTTP/1.1 403 Forbidden');
	drupal_set_header('Status: 403 Forbidden');
	
	//echo '<div class="title">' . $title . '</div>';
	echo '<div class="title">Доступ запрещён!</div>';

	//echo '<div class="body">' . $body . '</div>';

        //echo '<div class="body"><br>К сожалению, у Вас недостаточно прав для доступа к странице "www.zapokupkami.com/' . $_GET['u'] . '".<br><br> Попробуйте ввести другой адрес или воспользуйтесь меню.</div>';
        echo '<div class="body"><br>К сожалению, у Вас недостаточно прав для доступа к странице "www.zapokupkami.com' . url($_GET['u']) . '".<br><br> Попробуйте ввести другой адрес или воспользуйтесь меню.</div>';



	//echo '<div class="links">' . l(t('Вернуться на предыдущую страницу'), $_SERVER['HTTP_REFERER']) . '</div>';
	


	?>

</div>