jQuery(document).ready(function() {

        $('#where_to_buy_button').click
        (
                function() 
                {
                        if($('#shops_otdels').html() == '')
                        {
                                $('#shops_otdels').html('<div class="wait">Пожалуйста, подождите. <br>Идёт загрузка данных...</div><div class="loader"></div>');
                                $('#shops_otdels').load
                                (
                                        window.location.pathname + '?source=ajax_where_to_buy .ajaxed', 
                                        function(response, status, xhr) 
                                        {
                                                if($('#shops_otdels').children().html() == '' || status == 'error')
                                                        $('#shops_otdels').html('Нет данных');
                                        }
                                );


                                /*
                                $('#otdel_parent').bind('mouseover', function() 
                                {
                                        // Live handler called.
                                        ////$(this).addClass('xxx');
                                        //$(this).prev().prev().toggleClass('collapsed');
                                        //$(this).after('<p>Another paragraph!</p>');

                                        // doesn't work bind???!!!!
                                        // doesn't work
                                        ////alert('xxx');
                                });	
                                */


                        }
                }
        );

    
});
