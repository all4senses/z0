<!-- <div class="test">how it works</div> -->

<div id="how_it_works">

<a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#kak-sdelat-zakaz"><div class="caption"><div class="caption2">Как</div>заказывать товары?</div></a>

<div class="text">

<?php

	global $user;
	
	// авторизованному пользователю уже не сообщать о необходимости стать нашим клиентом
	if($user->uid)
		echo '1.Добавьте необходимые товары в корзину из каталогов <a href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny">магазинов, доступных Вам для покупок. </a><br> 2.Сделайте заказ, подтвердите его и оплатите доставленные Вам товары при получении.';
	else
		echo '<a class="text2" href="http://www.zapokupkami.com/feedback/stat-nashim-klientom-prosto">1.Станьте нашим клиентом. </a>
  		2.Заходите на сайт под своим паролем. 3.Добавляйте товары в корзину из <a class="text2" href="http://www.zapokupkami.com/help/pravila-i-osobennosti-raboty-nashego-servisa#lubimye-magaziny">магазинов, доступных Вам для покупок. </a> 4.Делайте заказы и оплачивайте доставленные Вам товары.';
  
?>


</div>

<?php //print $node->field_side_images[0]['view'] ?>

</div>