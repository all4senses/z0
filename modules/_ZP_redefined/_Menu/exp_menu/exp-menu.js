/*
$(document).ready(function() {
	initExpMenu('ul#my-menu ul')
});
*/



//$(document).ready(function() {
function initExpMenu(menu_id) {

	//$('ul#my-menu ul').each(function(i) { // Check each submenu:	
	$(menu_id).each(function(i) 
	{ 
		// Check each submenu:
		//alert(i);
		
		/*
		if ($.cookie('submenuMark-' + i)) {  // If index of submenu is marked in cookies:
			$(this).show().prev().removeClass('collapsed').addClass('expanded'); // Show it (add apropriate classes)
		}else {
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it
		}
		*/

		//if(i>0) // тогда первый пункт открывается при загрузке страницы
			$(this).hide().prev().removeClass('expanded').addClass('collapsed'); // Hide it

		//$(this).addClass('collapsible');
		$(this).prev().addClass('collapsed');
		
		$(this).prev().attr('title', 'Двойной клик для перехода к этому разделу!');

		
		$(this).prev().addClass('collapsible').dblclick(function() 
		{ 
			// Attach an event listener
			window.location.replace($(this).attr("href"));
			//return false; // Prohibit the browser to follow the link address
		});	
			
		
		
		//$(this).prev().addClass('collapsible').mouseover(function() { // Attach an event listener
		//$(this).prev().addClass('collapsible').click(function() { // Attach an event listener
		$(this).prev().addClass('collapsible').click(function() 
		{ // Attach an event listener
			var this_i = $('ul#my-menu ul').index($(this).next()); // The index of the submenu of the clicked link

			if ($(this).next().css('display') == 'none') {

				// When opening one submenu, we hide all same level submenus:
				$(this).parent('li').parent('ul').find('ul').each(function(j) {
					if (j != this_i) {
						$(this).slideUp(200, function () {
							$(this).prev().removeClass('expanded').addClass('collapsed');
							//cookieDel($('ul#my-menu ul').index($(this)));
						});
					}
				});
				// :end



				$(this).next().slideDown(200, function () { // Show submenu:
					$(this).prev().removeClass('collapsed').addClass('expanded');
					//cookieSet(this_i);
				});
			}else {
				$(this).next().slideUp(200, function () { // Hide submenu:
					$(this).prev().removeClass('expanded').addClass('collapsed');
					//cookieDel(this_i);

				//	$(this).find('ul').each(function() {
				//		$(this).hide(0, cookieDel($('ul#my-menu ul').index($(this)))).prev().removeClass('expanded').addClass('collapsed');
				//	});


				});
			}
		return false; // Prohibit the browser to follow the link address
		//e.preventDefault();  //это из примера, использовать не пробовал. e должно тогда передаваться как параметр текущей функции function() (в которую сейчас ничего не передаётся)
		});
	});
//});
}


/*
function cookieSet(index) {
	$.cookie('submenuMark-' + index, 'opened', {expires: null, path: '/'}); // Set mark to cookie (submenu is shown):
}
function cookieDel(index) {
	$.cookie('submenuMark-' + index, null, {expires: null, path: '/'}); // Delete mark from cookie (submenu is hidden):
}
*/