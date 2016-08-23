<!-- <div class="test">public info via template</div> -->


<?php 

print '<div class="title">Ваши открытые данные</div>';
print '<div class="explain">На этой странице Вы можете указывать любую информацию о себе (на отдельных полях, добавляя новые поля при необходимости), которая будет доступна для просмотра остальным пользователям сайта.</div>';
print '<div class="public_ei">' . $node->body . '</div>';

?>

