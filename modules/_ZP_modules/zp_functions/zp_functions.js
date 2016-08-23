
jQuery(document).ready(function() {
    
        // js for superfish menu
        // initialise plugins
        jQuery(function(){
                jQuery('ul.sf-menu').superfish(
                        {
                            delay:       1000,                            // one second delay on mouseout 
                            //animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
                            //speed:       'fast',                          // faster animation speed 
                            speed:       50,                          // faster animation speed 
                            //autoArrows:  false,                           // disable generation of arrow mark-up 
                            //disableHI:     true, 
                            //pathLevels:    3,  
                            dropShadows: false 
                        }

                );
        });


    
});


