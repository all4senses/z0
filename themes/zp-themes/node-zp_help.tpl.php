<?php 

	echo '<div class="help_page">';
		echo '<div class="title">' . $title . '</div>';

	// Social links
  //echo zp_functions_get_social_links(); 
    
    ?>
    
    <div class="share">

          <?php $url = 'http://' . $_SERVER['SERVER_NAME'] . url('node/' . $node->nid); ?>

          <div class="main">
              <?php echo zp_functions_getSocialiteButtons($url, $title_corrected); ?> 
          </div> <!-- main share buttons -->

          <div class="others">
            <!-- ADDTHIS BUTTON BEGIN -->
            <script type="text/javascript">
            var addthis_config = {
                //pubid: "all4senses"
            }
            var addthis_share =
            {
              // ... members go here
              url: "<?php echo $url?>"
            }
            </script>

            <div class="addthis_toolbox addthis_default_style" addthis:url="<?php echo $url?>">
              <a href="http://addthis.com/bookmark.php?v=250&amp;pub=all4senses"></a>
              <a class="addthis_button_compact">Другие сервисы</a>
            </div>
            <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pub=all4senses"></script>
            <!-- ADDTHIS BUTTON END -->

          </div>

        <div class="bottom_clear"></div>
        
    </div> <!-- all share buttons -->
    
    
    <?php
    


		echo '<div class="body">' . $body . '</div>';


	// Social links
        //echo zp_functions_get_social_links(); 
?>
  
      
	</div>

