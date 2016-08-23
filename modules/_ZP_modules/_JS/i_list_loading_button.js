$(document).ready(function()
{
	$('#issues_button').click
	(
	function()
	{
		if($('#issues').html() == '')
		{
			$('#issues').html('<div class="wait">Пожалуйста, подождите. <br>Идёт загрузка данных...</div><div class="loader"></div>');
			$('#issues').load
			(
				window.location.pathname + '?source=ajax .ajaxed',
					function(response, status, xhr)
					{
						if($('#issues').children().html() == '' || status == 'error')
							$('#issues').html('Нет данных');
					}
				);
			}
	}
	);
});