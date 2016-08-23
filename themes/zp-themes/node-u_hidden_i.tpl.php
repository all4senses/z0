<!-- <div class="test">node-u_hidden_i.tpl</div> -->

<?php 


drupal_add_js('misc/collapse.js');

echo $node->body;

//print '<fieldset class="collapsible collapsed"><legend>Расположение на карте</legend>';
echo '<fieldset class="collapsible"><legend>Расположение на карте</legend>';
	if($map) print theme('gmap', array('#settings' => $map));
echo '</fieldset>';

// выведем любимые магазины клиента и расстояния от них до клиента

	//zp_functions_show($node);
	
	//$places = zp_functions_distances_to_client_loveshops($node->uid); // можно так
	$places = zp_functions_distances_to_client_loveshops(null, $node->nid); // а можно и так: передаём уже известный нид ноды со скрытыми данными юзера (текущая нода), чтобы в функции ноду заново не вычислять
	echo '<strong>Расстояния до любимых магазинов клиента:</strong><br><br>';
	foreach ($places as $place)
		echo '- <strong>' . $place['name'] . '</strong> (' . $place['address'] . ')'. ($place['distance'] ? ': ' . $place['distance']['distance'] . 'км, азимут ' . $place['distance']['bearing'] . 'грд' : '') . '<br>';



?>




