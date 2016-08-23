
jQuery(document).ready(function() {
    
    
    // Ride the carousel...
    jQuery('#mycarousel').jcarousel({
        auto: 5,
        wrap: 'last', //'both'
        //wrap: 'circular',
        //vertical: 'true',
        scroll: 1,
        initCallback: mycarousel_initCallback
    });
    
    
    
});





function mycarousel_initCallback(carousel)
{
	// jQuery('.jcarousel-control a').bind('click', function() {
	jQuery('.jcarousel-control a').bind('mouseover', function() { 
        carousel.scroll(jQuery.jcarousel.intval(jQuery(this).text()));
        return false;
    });
	
	
    // Disable autoscrolling if the user clicks the prev or next button.
    /*
    carousel.buttonNext.bind('click', function() {
        carousel.startAuto(0);
    });

    carousel.buttonPrev.bind('click', function() {
        carousel.startAuto(0);
    });
    
    */

    // Pause autoscrolling if the user moves with the cursor over the clip.
    carousel.clip.hover(function() {
        carousel.stopAuto();
    }, function() {
        carousel.startAuto();
    });
    
    
};

