// $Id: nice_tax_menu.js,v 1.1.2.1 2009/03/01 07:20:55 jenlampton Exp $

// We need to do some browser sniffing to weed out IE 6 only
// because only IE6 needs this hover hack.
if (document.all && !window.opera && (navigator.appVersion.search("MSIE 6.0") != -1) && $.browser.msie) {
  function IEHoverPseudo() {
      $("ul.ddmenu li.menuparent").hover(function(){
          $(this).addClass("over").find("> ul").show().addShim();
        },function(){
          $(this).removeClass("over").find("> ul").removeShim().hide();
        }
      );
    }

    // This is the jquery method of adding a function
    // to the BODY onload event.  (See jquery.com)
    $(document).ready(function(){ IEHoverPseudo() });
}

$.fn.addShim = function() {
  return this.each(function(){
	  if(document.all && $("select").size() > 0) {
	    var ifShim = document.createElement('iframe');
	    ifShim.src = "javascript:false";
			ifShim.style.width=$(this).width()+1+"px";
      ifShim.style.height=$(this).find("> li").size()*23+20+"px";
			ifShim.style.filter="progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)";
		  ifShim.style.zIndex="0";
    $(this).prepend(ifShim);
      $(this).css("zIndex","99");
		}
	});
};

$.fn.removeShim = function() {
  return this.each(function(){
	  if (document.all) $("iframe", this).remove();
	});
};
