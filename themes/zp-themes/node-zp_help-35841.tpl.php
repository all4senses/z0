


<?php 

	echo '<div class="help_page">';
		echo '<div class="title">' . $title . '</div>';

	// Social links
        //echo zp_functions_get_social_links(); 


		echo '<div class="body">' . $body . '</div>';

    ?>


<!-- Put the following javascript before the closing </head> tag. -->
<?php
/*
<script>
  (function() {
    var cx = '000707278114868443645:WMX-1193624781';
    var gcse = document.createElement('script'); gcse.type = 'text/javascript'; gcse.async = true;
    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
        '//www.google.com/cse/cse.js?cx=' + cx;
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(gcse, s);
  })();
</script>
<!-- Place this tag where you want both of the search box and the search results to render -->
<gcse:search></gcse:search>
*/
?>


<?php /*
<form action="search" id="cse-search-box">
  <div>
    <input type="hidden" name="cx" value="partner-pub-3789886794599428:1381772015" />
    <input type="hidden" name="cof" value="FORID:10" />
    <input type="hidden" name="ie" value="UTF-8" />
    <input type="text" name="query" size="55" />
    <input type="submit" name="sa" value="&#x041f;&#x043e;&#x0438;&#x0441;&#x043a;" />
  </div>
</form>

<script type="text/javascript" src="http://www.google.com.ua/coop/cse/brand?form=cse-search-box&amp;lang=ru"></script>


<div id="cse-search-results"></div>
<script type="text/javascript">
  var googleSearchIframeName = "cse-search-results";
  var googleSearchFormName = "cse-search-box";
  var googleSearchFrameWidth = 800;
  var googleSearchDomain = "www.google.com.ua";
  var googleSearchPath = "/cse";
</script>
<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>


<script type="text/javascript" src="http://www.google.com/cse/query_renderer.js"></script>
<div id="queries"></div>
<script src="http://www.google.com/cse/api/partner-pub-3789886794599428/cse/1381772015/queries/js?oe=UTF-8&amp;callback=(new+PopularQueryRenderer(document.getElementById(%22queries%22))).render"></script>

 */
 ?>


<div id="cse-search-form" style="width: 100%;">Загрузка</div>
<script src="http://www.google.com.ua/jsapi" type="text/javascript"></script>
<script type="text/javascript"> 
  google.load('search', '1', {language : 'ru'});
  google.setOnLoadCallback(function() {
    var customSearchOptions = {};  var customSearchControl = new google.search.CustomSearchControl(
      'partner-pub-3789886794599428:1381772015', customSearchOptions);
    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    var options = new google.search.DrawOptions();
    options.enableSearchboxOnly("http://www.zapokupkami.com/search", "query");
    customSearchControl.draw('cse-search-form', options);
  }, true);
  

</script>
<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" />


<div id="cse" style="width: 100%;">...</div>
<script src="http://www.google.com.ua/jsapi" type="text/javascript"></script>
<script type="text/javascript"> 
  google.load('search', '1', {language : 'ru'});
  google.setOnLoadCallback(function() {
    var customSearchOptions = {};  var customSearchControl = new google.search.CustomSearchControl(
      'partner-pub-3789886794599428:1381772015', customSearchOptions);
    customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
    customSearchControl.draw('cse');
    function parseParamsFromUrl() {
      var params = {};
      var parts = window.location.search.substr(1).split('\x26');
      for (var i = 0; i < parts.length; i++) {
        var keyValuePair = parts[i].split('=');
        var key = decodeURIComponent(keyValuePair[0]);
        params[key] = keyValuePair[1] ?
            decodeURIComponent(keyValuePair[1].replace(/\+/g, ' ')) :
            keyValuePair[1];
      }
      return params;
    }

    var urlParams = parseParamsFromUrl();
    var queryParamName = "query";
    if (urlParams[queryParamName]) {
      customSearchControl.execute(urlParams[queryParamName]);
      
      
      
      // a4s fix
      jQuery(".gsc-input").val(urlParams[queryParamName]);
    }
    
    // a4s fix
    jQuery(".gsc-input").addClass('gsc-input-focus');
    jQuery(".gsc-input").focus();
  
  }, true);
  
</script>
<link rel="stylesheet" href="http://www.google.com/cse/style/look/default.css" type="text/css" /> 
 


<?php

	// Social links
        //echo zp_functions_get_social_links(); 

	echo '</div>';

?>