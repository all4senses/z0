// $Id: panels_collapse.js,v 1.2 2009/02/01 09:45:27 joachim Exp $

if (Drupal.jsEnabled) {
  $(document).ready(function(){ 
    $('div.panel-display div.panel-pane-collapsible').each(function() {  
      $(this).find('.panel-pane > h2.title').click(function() {
          $(this).toggleClass('collapsed');
          $(this).next('.content').toggleClass('collapsed').slideToggle('slow');
        });
    });
  });
}
