
jQuery(document).ready(function() {
    
    //console.log('Last zp_place_tid = ' + jQuery.cookie('zp_place_tid') + ', last zp_place_type = ' + jQuery.cookie('zp_place_type') + ', last zp_shop_tid = ' + jQuery.cookie('zp_shop_tid'));
    
    // Set user menu
    (jQuery).ajax
            ({
                url: '/get_user', 
                data: {
                        op: 'user',
                        url: window.location.href
                        }, 
                    type: 'POST', 
                    dataType: 'json', 
                    success: function(data) 
                            { 
                                if(data.error)
                                    alert(data.error);
                                else
                                    {
                                        //console.log('The header is arrived!');

                                        // show arrived breadcrumbs

                                        //$('div#user-block').hide();
                                        if (data.status == 1)
                                          $('div#user-block').html(data.user_menu);
                                        //$('div#user-block').show();
                                    }
                                return false;
                            } 

            }); // end of (jQuery).ajax
    
    
    
    // Set header
    if(zp_type == 'place')
    {
        // hide default header and show current
        $('div#e_def').hide();
        $('div#c_breadcrumb_def').hide();
        $('#second_menu_def').hide();
                                        
        if(zp_nid != 1) // if not a home page
        {
            $('div#main_pic_default').hide();
            $('div#shop_logo_upmenu').show();
        }
        
        $('div#e_main').show();
        $('div#current_page_info').show();
        $('div#c_breadcrumb').show();
        $('#second_menu').show();  
       
        // save current place to cookies
        jQuery.cookie('zp_place_tid', zp_place_tid, { expires: 365, path: '/' });
        jQuery.cookie('zp_place_type', zp_place_type, { expires: 365, path: '/' });
        jQuery.cookie('zp_shop_tid', zp_shop_tid, { expires: 365, path: '/' });

        // show new cookies
        //console.log('New zp_place_tid = ' + jQuery.cookie('zp_place_tid') + ', new zp_place_type = ' + jQuery.cookie('zp_place_type') + ', new zp_shop_tid = ' + jQuery.cookie('zp_shop_tid'));
    }
    else
    {
        // if not place (prod, dept, shop, rajon, city, etc)
        // try to correct the header
        
        var saved_zp_place_tid = jQuery.cookie('zp_place_tid');
        var saved_zp_place_type = jQuery.cookie('zp_place_type');
        var saved_zp_shop_tid = jQuery.cookie('zp_shop_tid');
        
        if(!saved_zp_place_tid)
        {
            // if the site is entered for the first time, 

            // hide current header and show default, 
            // or do nothing if they are initially are hidden already
           
            // or try to get right breadcrumb
          
            /*
           (jQuery).ajax
                ({
                    url: '/get_header', 
                    data: {
                            zp_nid: zp_nid, 
                            zp_node_type: zp_node_type,
                            zp_type: zp_type,
                            zp_place_tid: saved_zp_place_tid,
                            zp_place_tid_of_node: zp_place_tid,
                            zp_place_type: saved_zp_place_type,
                            op: 'breadcrumb'
                            }, 
                        type: 'POST', 
                        dataType: 'json', 
                        success: function(data) 
                                { 
                                    if(data.error)
                                        alert(data.error);
                                    else
                                        {
                                            //console.log('The header is arrived!');
                                            
                                            // show arrived breadcrumbs
                                            
                                            $('div#c_breadcrumb_def').hide();
                                            $('div#c_breadcrumb').replaceWith(data.c_breadcrumb);
                                            $('div#c_breadcrumb').show();
                                        }
                                    return false;
                                } 

                }); // end of (jQuery).ajax
            */
        }
        
        else if(saved_zp_place_tid == zp_place_tid || saved_zp_shop_tid == zp_shop_tid)
        {
            // right shop! we can leave the almost all header we have already
            
            // hide default header and show current
            $('div#e_def').hide();
            $('#second_menu_def').hide();

            if(zp_nid != 1) // if not a home page
            {
                $('div#main_pic_default').hide();
                $('div#shop_logo_upmenu').show();
            }

            $('div#e_main').show();
            $('div#current_page_info').show();
            $('#second_menu').show();  
        
            // BUT... if otdel is different
            // we have to regenerate the breadcrumb
            if(saved_zp_place_tid != zp_place_tid)
            {
                (jQuery).ajax
                ({
                    url: '/get_header', 
                    data: {
                            zp_nid: zp_nid, 
                            zp_node_type: zp_node_type,
                            zp_type: zp_type,
                            zp_place_tid: saved_zp_place_tid,
                            zp_place_tid_of_node: zp_place_tid,
                            zp_place_type: saved_zp_place_type,
                            op: 'breadcrumbs'
                            }, 
                        type: 'POST', 
                        dataType: 'json', 
                        success: function(data) 
                                { 
                                    if(data.error)
                                        ;//alert(data.error);
                                    else
                                        {
                                            //console.log('The header is arrived! Just breadcrumb');
                                            //console.log('zp_nid = ' + zp_nid);
                                            
                                            // show arrived breadcrumbs
                                            
                                            $('div#c_breadcrumb_def').hide();
                                            $('div#c_breadcrumb').replaceWith(data.c_breadcrumb);
                                            $('div#c_breadcrumb').show();
                                        }
                                    return false;
                                } 

                }); // end of (jQuery).ajax
                
            } // end of if(saved_zp_place_tid != zp_place_tid) // if need to regenerate breadcrumbs
            
        } // end of else if(saved_zp_place_tid == zp_place_tid || saved_zp_shop_tid == zp_shop_tid)
        
        else
        {
            // need to regenerate all
            
            // hide current header and show default, 
            // or do nothing if they are initially are hidden already
        
            (jQuery).ajax
            ({
                url: '/get_header', 
                data: {
                        zp_nid: zp_nid, 
                        zp_node_type: zp_node_type,
                        zp_type: zp_type,
                        zp_place_tid: saved_zp_place_tid,
                        zp_place_tid_of_node: zp_place_tid,
                        zp_place_type: saved_zp_place_type,
                        op: 'all'
                        }, 
                    type: 'POST', 
                    dataType: 'json', 
                    success: function(data) 
                            { 
                                if(data.error)
                                    alert(data.error);
                                else
                                    {
                                        //console.log('The header is arrived!');
                                        //console.log('zp_nid = ' + zp_nid);
                                        
                                        // show arrived header
                                        
                                        $('div#e_def').hide();
                                        
                                        
                                        if(zp_nid != 1) // if not a home page
                                        {
                                            $('div#main_pic_default').hide();
                                            $('div#shop_logo_upmenu').replaceWith(data.header_pic);
                                            $('div#shop_logo_upmenu').show();
                                            
                                        }
                                        
                                        if($('div#current_page_info').html())
                                        {
                                            $('div#current_page_info').html(data.current_page_info);
                                            $('div#current_page_info').show();
                                        }
                                        else
                                            {
                                                $('div#e_main').replaceWith('<div id="current_page_info">' + data.current_page_info + '</div>');
                                                $('div#current_page_info').show();
                                            }
                                        
                                        $('div#c_breadcrumb_def').hide();
                                        $('div#c_breadcrumb').replaceWith(data.c_breadcrumb);
                                        $('div#c_breadcrumb').show();
                                        
                                        $('#second_menu_def').hide();
                                        $('#second_menu').replaceWith(data.second_menu);
                                        $('#second_menu').show();
                                        // rebind hover menu behavior
                                        menu_hover_rebind();
                                    }
                                return false;
                            } 

            }); // end of jQuery).ajax
            
        } // end of // need to regenerate
       
    } // end of else of if(zp_type == 'place')

    //console.log('current zp_type = ' + zp_type + ', current zp_nid = ' + zp_nid);
    
});



function mshow2() {
    var menu = $(this);
    menu.find('ul:first').css({visibility: 'visible',display: 'none'}).show(400);
}
	   
function mhide2() { 
    var menu = $(this);
    menu.find('ul:first').css({visibility: 'hidden'});
}

function menu_hover_rebind()
{

    $(' #second_menu ul ').css({display: 'none'}); // Opera Fix

    $(' #second_menu li').hoverIntent({
        sensitivity: 1, // number = sensitivity threshold (must be 1 or higher)
        interval: 20,   // number = milliseconds for onMouseOver polling interval
        over: mshow2,     // function = onMouseOver callback (required)
        timeout: 700,   // number = milliseconds delay before onMouseOut
        out: mhide2       // function = onMouseOut callback (required)
    });

} 