jQuery(document).ready(function() {

        $('.fix_tr').click
        (
            function() 
            {
                //  /sites/all/modules/_ZP_modules/_JS/zp_functions_fixTranslate.js
                $('.enter_tr').show();
                $('.fix_tr').hide();
                
                return false;
                
            }
        );
            
        $('.cancel_tr').click
        (
            function() 
            {
                $('.enter_tr').hide();
                $('.fix_tr').show();
                $('#tr').val('');
                
                return false;
                
            }
        );
            
            
            
        
        
        

        $('.send_tr').click(
            function(){
                
                
                if($("#tr").val() == '') 
                {
                    alert('Please write in your reason...'); 
                    return false;
                } 
                
                $('.enter_tr').hide(); 
                $('.fix_tr').show();
                
                //alert('Sending...');
                //return false;
                
                (jQuery).ajax
                    ({
                        url: '/fix_tr', 
                        data: {
                                node_nid: $('#tr').attr('class'), 
                                string: $('#tr').val(),
                                op: 'fix'
                               }, 
                         type: 'POST', 
                         dataType: 'json', 
                         success: function(data) 
                                    { 
                                        if(data.error)
                                            alert(data.error);
                                        else
                                            {
                                                alert('Благодарим за бдительность! :) Мы рассмотрим Ваше предложение и внесём необходимые изменения в ближайшее время!');
                                                $('#tr').val('');
                                            }
                                        return false;
                                    } 
                     }); 
                
                return false;            
            }

        );
            
            
            
            
       
       
        $('.agree_tr').click(
            function(){
                
       
                $('.suggested_tr').hide(); 
                
                //alert('Sending...');
                //return false;
                
                (jQuery).ajax
                    ({
                        url: '/fix_tr', 
                        data: {
                                node_nid: $('#tr').attr('class'), 
                                op: 'agree'
                               }, 
                         type: 'POST', 
                         dataType: 'json', 
                         success: function(data) 
                                    { 
                                        if(data.error)
                                            alert(data.error);
                                        else
                                            {
                                                alert('Предложенный перевод подтверждён!');
                                            }
                                        return false;
                                    } 
                     }); 
                
                return false;            
            }

        );
            
            
            
            

    
});

