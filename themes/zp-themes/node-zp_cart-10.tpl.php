<!-- <div class="test">it's a orders history list</div> -->


<?php 

print '<div class="order_history">';
print '<div class="title">История Ваших заказов</div>';

print '<div class="explain">В приведенном ниже списке содержатся заказы, которые были выполнены или находятся в обработке. 
<br><br>Используя соответствующие иконки рядом с номером заказа, Вы можете просмотреть любой выбранный заказ, а также скопировать или вернуть заказ в корзину (если он ещё не был принят в обработку).</div>';

print $zp_orders_history;
print '</div>';




?>