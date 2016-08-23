 $(document).ready(function(){
                           mainmenu();
                });




	function mshow() {
	    var menu = $(this);
	    menu.find('ul:first').css({visibility: 'visible',display: 'none'}).show(400);
	   }
	   
	function mhide() { 
	    var menu = $(this);
	    menu.find('ul:first').css({visibility: 'hidden'});
	  }

               function mainmenu()
               {

                    $(' #second_menu ul ').css({display: 'none'}); // Opera Fix
                    
                    $(' #second_menu li').hoverIntent({
                      sensitivity: 1, // number = sensitivity threshold (must be 1 or higher)
                      interval: 20,   // number = milliseconds for onMouseOver polling interval
                      over: mshow,     // function = onMouseOver callback (required)
                      timeout: 700,   // number = milliseconds delay before onMouseOut
                      out: mhide       // function = onMouseOut callback (required)
                    });

                    
                }                    