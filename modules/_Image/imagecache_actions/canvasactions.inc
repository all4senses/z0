<?php
// $ID: $
/**
 * Helper functions for the text2canvas action for imagecache
 * 
 */

/**
 * Implementation of imagecache_hook_form()
 *
 * Settings for preparing a canvas.
 *
 * @param $action array of settings for this action
 * @return a form definition
 */
function canvasactions_definecanvas_form($action) {
  $form = array(
    'RGB' => imagecache_rgb_form($action['RGB']),
    'help' => array(
      '#type' => 'markup',
      '#value' => '<p>'. t('Enter no color value for transparent. This will have the effect of adding clear margins around the image.') .'</p>',
    ),
    'under' => array(
      '#type' => 'checkbox',
      '#title' => t('Resize canvas under image (possibly cropping)'),
      '#default_value' => $action['under'],
      '#description' => t('If not set, this will create a solid flat layer, probably totally obscuring the source image'),
    ),
  );
  $form['info'] = array('#value' => t('Enter values in ONLY ONE of the below options. Either exact or relative. Most values are optional - you can adjust only one dimension as needed. If no useful values are set, the current base image size will be used.'));
  $form['exact'] = array(
      '#type' => 'fieldset',
      '#collapsible' => true,
      '#title' => 'Exact size',
      'help' => array(
        '#type' => 'markup',
        '#value' => '<p>'. t('Set the canvas to a precise size, possibly cropping the image. Use to start with a known size.'). '</p>',
      ),
      
      'width' => array(
        '#type' => 'textfield',
        '#title' => t('Width'),
        '#default_value' => $action['exact']['width'],
        '#description' => t('Enter a value in pixels or percent'),
        '#size' => 5,
      ),
      'height' => array(
        '#type' => 'textfield',
        '#title' => t('Height'),
        '#default_value' => $action['exact']['height'],
        '#description' => t('Enter a value in pixels or percent'),
        '#size' => 5,
      ),
    );
  $form['exact'] = array_merge($form['exact'], canvasactions_pos_form($action['exact']));
  if(! $action['exact']['width'] && !$action['exact']['height']) {
    $form['exact']['#collapsed'] = true;
  }

  $form['relative'] = array(
      '#type' => 'fieldset',
      '#collapsible' => true,
      '#title' => t('Relative size'),
      'help' => array(
        '#type' => 'markup',
        '#value' => '<p>'. t('Set the canvas to a relative size, based on the current image dimensions. Use to add simple borders or expand by a fixed amount. Negative values may crop the image.') .'</p>',
      ),
      'leftdiff' => array(
        '#type' => 'textfield',
        '#title' => t('left difference'),
        '#default_value' => $action['relative']['leftdiff'],
        '#size' => 6,
        '#description' => t('Enter an offset in pixels.'),
      ),
      'rightdiff' => array(
        '#type' => 'textfield',
        '#title' => t('right difference'),
        '#default_value' => $action['relative']['rightdiff'],
        '#size' => 6,
        '#description' => t('Enter an offset in pixels.'),
      ),
      'topdiff' => array(
        '#type' => 'textfield',
        '#title' => t('top difference'),
        '#default_value' => $action['relative']['topdiff'] ,
        '#size' => 6,
        '#description' => t('Enter an offset in pixels.'),
      ),
      'bottomdiff' => array(
        '#type' => 'textfield',
        '#title' => t('bottom difference'),
        '#default_value' => $action['relative']['bottomdiff'],
        '#size' => 6,
        '#description' => t('Enter an offset in pixels.'),
      ),
    );
  if(! $action['relative']['leftdiff'] && !$action['relative']['rightdiff'] && !$action['relative']['topdiff'] && !$action['relative']['bottomdiff']) {
    $form['relative']['#collapsed'] = true;
  }

  $form['#submit'][] = 'canvasactions_definecanvas_form_submit';
  return $form;
}

/**
 * Implementation of theme_hook() for imagecache_ui.module
 */
function theme_canvasactions_definecanvas($element) {
  $action = $element['#value'];
  if ($action['exact']['width'] || $action['exact']['width']) {
    $output = $action['exact']['width'] .'x'. $action['exact']['height'];
  }
  else {
    $output = ' left:'. $action['relative']['leftdiff'];
    $output .= ' right:'. $action['relative']['rightdiff'];
    $output .= ' top:'. $action['relative']['topdiff'];
    $output .= ' bottom:'. $action['relative']['bottomdiff'];
    
  }
  $output .= theme_canvasactions_rgb($action['RGB']);
  return $output ;
}

/**
 * Implementation of hook_image()
 *
 * Creates a solid background canvas
 *
 * Process the imagecache action on the passed image
 *
 * @param $image
 * array defining an image file, including  :
 *
 *   $image- >source as the filename,
 *
 *   $image->info array
 *
 *   $image->resource handle on the image object
 */
function canvasactions_definecanvas_image(& $image, $action = array()) {
 
  // May be given either exact or relative dimensions.
  if ($action['exact']['width'] || $action['exact']['width']) {
  // Allows only one dimension to be used if the other is unset.
    if (! $action['exact']['width']) $action['exact']['width'] = $image->info['width'];
    if (! $action['exact']['height']) $action['exact']['height'] = $image->info['height'];

    $targetsize['width'] = _imagecache_percent_filter($action['exact']['width'], $image->info['width']);
    $targetsize['height'] = _imagecache_percent_filter($action['exact']['height'], $image->info['height']);

    $targetsize['left'] = _imagecache_keyword_filter($action['exact']['xpos'], $targetsize['width'], $image->info['width']);
    $targetsize['top'] = _imagecache_keyword_filter($action['exact']['ypos'], $targetsize['height'], $image->info['height']);

  }
  else {
    // calculate relative size
    $targetsize['width'] = $image->info['width'] + $action['relative']['leftdiff'] +  $action['relative']['rightdiff'];
    $targetsize['height'] = $image->info['height'] + $action['relative']['topdiff'] +  $action['relative']['bottomdiff'];
    $targetsize['left'] = $action['relative']['leftdiff'];
    $targetsize['top'] = $action['relative']['topdiff'];
  }
  
  $newcanvas = imagecreatetruecolor($targetsize['width'], $targetsize['height']);
  $RGB = $action['RGB'];

  // convert from hex (as it is stored in the UI)
  if($RGB['HEX'] && $deduced = hex_to_rgb($RGB['HEX'])) {
    $RGB = array_merge($RGB, $deduced);
  }

  if ($RGB['red'] || $RGB['green'] || $RGB['blue']) { // one may be zero...
    $background = imagecolorallocate($newcanvas, $RGB['red'], $RGB['green'], $RGB['blue']);
  }
  else {
    // No color, attempt transparency, assume white
    $background = imagecolorallocatealpha($newcanvas, 255, 255, 255, 127);
    imagesavealpha($newcanvas, TRUE);
    imagealphablending($newcanvas, false);
    imagesavealpha($image->resource, TRUE);
  }
  imagefilledrectangle($newcanvas, 0, 0, $targetsize['width'], $targetsize['height'], $background);
  
  if ($action['under']) {
    require_once('watermark.inc');
    $watermark = new watermark();
    $image->resource = $watermark->create_watermark($newcanvas, $image->resource, $targetsize['left'], $targetsize['top'], 100);
    imagesavealpha($image->resource, TRUE);
  } 
  else {
    $image->resource = $newcanvas ;
  }
  
  $image->info['width'] = $targetsize['width'];
  $image->info['height'] = $targetsize['height'];
  return TRUE;
}

////////////////////////////////////////////////

/**
 * Place a given image under the current canvas
 *
 * Implementation of imagecache_hook_form()
 *
 * @param $action array of settings for this action
 * @return a form definition
 */
function canvasactions_canvas2file_form($action) {
  $form = array(
    'xpos' => array(
      '#type' => 'textfield',
      '#title' => t('X offset'),
      '#default_value' => $action['xpos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>left</em>, <em>center</em>, or <em>right</em>.'),
    ),
    'ypos' => array(
      '#type' => 'textfield',
      '#title' => t('Y offset'),
      '#default_value' => $action['ypos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>top</em>, <em>center</em>, or <em>bottom</em>.'),
    ),
    'alpha' => array(
      '#type' => 'textfield',
      '#title' => t('opacity'),
      '#default_value' => isset($action['alpha']) ? $action['alpha'] : 100,
      '#size' => 6,
      '#description' => t('Opacity: 0-100.'),
    ),
    'path' => array(
      '#type' => 'textfield',
      '#title' => t('file name'),
      '#default_value' => $action['path'],
      '#description' => t('File may be in the "files/" folder, or relative to the Drupal siteroot.'),
    ),
  );
  return $form;
}

/**
 * Implementation of theme_hook() for imagecache_ui.module
 */
function theme_canvasactions_canvas2file($element) {
  $data = $element['#value'];
  return 'xpos:'. $data['xpos'] .', ypos:'. $data['ypos'] .' alpha:'. $data['alpha'] .'%' ;
}

/**
 * Place the source image on the current background
 *
 * Implementation of hook_image()
 *
 *
 * @param $image
 * @param $action
 */
function canvasactions_canvas2file_image(&$image, $action = array()) {
  // search for full (siteroot) paths, then file dir paths, then relative to the current theme
  if (file_exists($action['path'])) {
    $underlay = imageapi_image_open($action['path']);
  }
  else if (file_exists(file_create_path($action['path']))) {
    $underlay = imageapi_image_open(file_create_path($action['path']));
  }
  // This func modifies the underlay image by ref, placing the current canvas on it
  if (imageapi_image_overlay($underlay, $image, $action['xpos'], $action['ypos'], $action['alpha'])) {
    $image->resource = $underlay->resource;
    //$image = $underlay;
    return TRUE;
  }
}

////////////////////////////////////////////////


/**
 * Place a given image on top of the current canvas
 *
 * Implementation of imagecache_hook_form()
 *
 * @param $action array of settings for this action
 * @return a form definition
 */
function canvasactions_file2canvas_form($action) {
  $form = array(
    'help' => array(
      '#type' => 'markup',
      '#value' => t('Note that this action may require a lot of processing as transparency blends require that every pixel be re-calculated for each image. This can be a server-intensive process and generate a bit of load time.'),
    ),
    'xpos' => array(
      '#type' => 'textfield',
      '#title' => t('X offset'),
      '#default_value' => $action['xpos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>left</em>, <em>center</em>, or <em>right</em>.'),
    ),
    'ypos' => array(
      '#type' => 'textfield',
      '#title' => t('Y offset'),
      '#default_value' => $action['ypos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>top</em>, <em>center</em>, or <em>bottom</em>.'),
    ),
    'alpha' => array(
      '#type' => 'textfield',
      '#title' => t('opacity'),
      '#default_value' => $action['alpha'],
      '#size' => 6,
      '#description' => t('Opacity: 0-100.'),
    ),
    'path' => array(
      '#type' => 'textfield',
      '#title' => t('file name'),
      '#default_value' => $action['path'],
      '#description' => t('File may be in the "files/" folder, or relative to the Drupal siteroot.'),
    ),
  );
  return $form;
}

/**
 * Implementation of theme_hook() for imagecache_ui.module
 */
function theme_canvasactions_file2canvas($element) {
  $action = $element['#value'];
  return '<strong>'. basename($action['path']) . '</strong> x:'. $action['xpos'] .', y:'. $action['ypos'] .' alpha:'. $action['alpha'] .'%' ;
}

/**
 * Place the source image on the current background
 *
 * Implementation of hook_image()
 *
 *
 * @param $image
 * @param $action
 */
function canvasactions_file2canvas_image(&$image, $action = array()) {
  // search for full (siteroot) paths, then file dir paths, then relative to the current theme
  if (file_exists($action['path'])) {
    $overlay = imageapi_image_open($action['path']);
  }
  else if (file_exists(file_create_path($action['path']))) {
    $overlay = imageapi_image_open(file_create_path($action['path']));
  }
  return imageapi_image_overlay($image, $overlay, $action['xpos'], $action['ypos'], $action['alpha']);
}

///////////////////////////////////////////////////////////////////
/**
 * Place the source image on top of the current canvas
 *
 * Implementation of imagecache_hook_form()
 *
 *
 *
 * @param $action array of settings for this action
 * @return a form definition
 */
function canvasactions_source2canvas_form($action) {
  $form = array(
    'xpos' => array(
      '#type' => 'textfield',
      '#title' => t('X offset'),
      '#default_value' => $action['xpos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>left</em>, <em>center</em>, or <em>right</em>.'),
    ),
    'ypos' => array(
      '#type' => 'textfield',
      '#title' => t('Y offset'),
      '#default_value' => $action['ypos'],
      '#size' => 6,
      '#description' => t('Enter an offset in pixels or use a keyword: <em>top</em>, <em>center</em>, or <em>bottom</em>.'),
    ),
    'alpha' => array(
      '#type' => 'textfield',
      '#title' => t('opacity'),
      '#default_value' => $action['alpha'] ? $action['alpha'] : 100,
      '#size' => 6,
      '#description' => t('Opacity: 0-100.'),
    ),
  );
  return $form;
}

/**
 * Implementation of theme_hook() for imagecache_ui.module
 */
function theme_canvasactions_source2canvas($element) {
  $data = $element['#value'];
  return 'xpos:'. $data['xpos'] .', ypos:'. $data['ypos'] .' alpha:'. $data['alpha'] .'%' ;
}

/**
 * Place the source image on the current background
 *
 * Implementation of hook_image()
 *
 *
 * @param $image
 * @param $action
 */
function canvasactions_source2canvas_image(&$image, $action = array()) {
  $overlay = imageapi_image_open($image->source); // this probably means opening the image twice. c'est la vie
  return imageapi_image_overlay($image, $overlay, $action['xpos'], $action['ypos'], $action['alpha']);
}

////////////////////////////////////////////////

/**
 * Set radius for corner rounding
 * 
 * Implementation of imagecache_hook_form()
 *
 * @param $action array of settings for this action
 * @return a form definition
 */
function canvasactions_roundedcorners_form($action) {
  if (imageapi_default_toolkit() != 'imageapi_gd') {
    drupal_set_message('Rounded corners are not currently supported by using imagemagick. This effect requires GD image toolkit only.', 'warning');
  }

  drupal_add_js(drupal_get_path('module', 'imagecache_canvasactions') .'/imagecache_actions.jquery.js');
  $defaults = array(
    'radius' => '16',
    'antialias' => TRUE,
    'independent_corners_set' => array(
      'independent_corners' => FALSE,
      'radii' => array(
        'tl' => 0,
        'tr' => 0,
        'bl' => 0,
        'br' => 0,
      ),
    ),
  );
  $action = array_merge($defaults, (array)$action);

  $form['radius'] = array(
      '#type' => 'textfield',
      '#title' => t('radius'),
      '#default_value' => $action['radius'],
      '#size' => 2,
  );

  $form['independent_corners_set'] = array(
      '#type' => 'fieldset',
      '#title' => t('Individual Corner Values'),
      '#collapsible' => TRUE,
      '#collapsed' => (! $action['independent_corners_set']['independent_corners']),
  );
  $form['independent_corners_set']['independent_corners'] = array(
      '#type' => 'checkbox',
      '#title' => t('Set Corners Independently'),
      '#default_value' => $action['independent_corners_set']['independent_corners'],
  );
  $corners = array(
    'tl' => t("Top Left Radius"),
    'tr' => t("Top Right Radius"),
    'bl' => t("Bottom Left Radius"),
    'br' => t("Bottom Right Radius"),
  );
  // Loop over the four corners and create field elements for them.
  $form['independent_corners_set']['radii'] = array('#type' => 'item');
  foreach ($corners as $attribute => $label) {
    $form['independent_corners_set']['radii'][$attribute] = array(
        '#type' => 'textfield',
        '#title' => $label,
        '#default_value' => 0+$action['independent_corners_set']['radii'][$attribute],
        '#size' => 2,
    );
  }

  $form['antialias'] = array(
      '#type' => 'checkbox',
      '#title' => t('antialias'),
      '#return_value' => TRUE,
      '#default_value' => $action['antialias'],
      '#description' => t('Attempt antialias smoothing when drawing the corners'),
  );

  $form['notes'] = array(
      '#type' => 'markup',
      '#value' => t('
        Note: the rounded corners effect uses true alpha transparency masking. 
        This means that this effect <b>will fail to be saved</b> on jpegs 
        <em>unless</em> you either <ul>
        <li>convert the image to PNG (using the coloractions filter for that),</li>
        <li>underlay a solid color (using coloractions-alpha-flatten) or</li> 
        <li>underlay a background image (canvasactions-underlay)</li> 
        </ul>
        as a later part of this imagecache pipeline.
        <br/>
      '),
  );

  return $form;
}

/**
 * Create a rounded corner mask and alpha-merge it with the image.
 * 
 * Implementation of hook_image()
 * 
 * Note, this is not image toolkit-agnostic yet! It just assumes GD.
 * We can abstract it out once we have something else to abstract to.
 * In the meantime just don't.
 * 
 * 'independant' rounded corners logic contributed by canaryMason 2009-03
 *
 * @param $image
 * @param $action
 */
function canvasactions_roundedcorners_image(&$image, $action = array()) {
  if ($image->toolkit != 'imageapi_gd') {
    drupal_set_message("Unable to create rounded corners with {$image->toolkit}");
    // Pretend we did anyway, just return the untrimmed version.
    return TRUE;
  }
  
  $width = $image->info['width'];
  $height = $image->info['height'];
  $radius = $action['radius'];
  $independent_corners = $action['independent_corners_set']['independent_corners'];
  $radii = $action['independent_corners_set']['radii'];
  $diameter = array();

  if ($action['antialias']) {
    $width = $width * 3;
    $height = $height * 3;
    $radius = $radius * 3;
    foreach ($radii as $corner => $corner_radius) {
      $radii[$corner] = $radii[$corner] * 3;
    }
  }

  foreach ($radii as $corner => $corner_radius) {
    if (!$independent_corners){
      // Use the unique maths for independant corners, 
      // even if they are all the same.
      $radii[$corner] = $radius;
    }
    $diameter[$corner] = $radii[$corner] * 2;
  }

  // Create a mask with rounded corners
  $mask = imagecreatetruecolor($width, $height);

  // Is using the toolkit really worth the bother?
  // Just doing it in an attempt to be consistent
  $mask_image = (object)array(
    'res' => &$mask, 
    'info' => array('width' => $width, 'height' => $height, 'extension' => 'png'), 
    'toolkit' => $image->toolkit,
    'resource' => &$mask,
  );

  // Start with a blank slate
  $background = imagecolorallocatealpha($mask, 255, 255, 255, 127);
  imagesavealpha($mask, TRUE);
  imagealphablending($mask, false);
  imagefilledrectangle($mask, 0, 0, $width, $height, $background);
  // Place solid lumps on it
  $foreground = imagecolorallocatealpha($mask, 0, 0, 0, 0);

  // Place blobs in the corners
  ImageFilledEllipse($mask, $radii['tl'], $radii['tl'], $diameter['tl'], $diameter['tl'], $foreground);
  ImageFilledEllipse($mask, $width-$radii['tr'], $radii['tr'], $diameter['tr'], $diameter['tr'], $foreground);
  ImageFilledEllipse($mask, $radii['bl'], $height-$radii['bl'], $diameter['bl'], $diameter['bl'], $foreground);
  ImageFilledEllipse($mask, $width-$radii['br'], $height-$radii['br'], $diameter['br'], $diameter['br'], $foreground);
  
  // Block out the middle
  ImageFilledRectangle($mask, $radii['tl'], 0, $width*0.5, $height*0.5, $foreground);
  ImageFilledRectangle($mask, 0, $radii['tl'], $width*0.5, $height*0.5, $foreground);
  ImageFilledRectangle($mask, $width*0.5, 0, $width-$radii['tr'], $height*0.5, $foreground);
  ImageFilledRectangle($mask, $width*0.5, $radii['tr'], $width, $height*0.5, $foreground);
  ImageFilledRectangle($mask, 0, $height*0.5, $width*0.5, $height-$radii['bl'], $foreground);
  ImageFilledRectangle($mask, $radii['bl'], $height*0.5, $width*0.5, $height, $foreground);
  ImageFilledRectangle($mask, $width*0.5, $height*0.5, $width, $height-$radii['br'], $foreground);
  ImageFilledRectangle($mask, $width*0.5, $height*0.5, $width-$radii['br'], $height, $foreground);

  if ($action['antialias']) {
    // Use toolkit so scale down again. 
    imageapi_image_scale($mask_image, $width/3, $height/3);
    $mask = $mask_image->resource;
  }

  // Now we have a mask. Merge to get a result
  canvasactions_mask($image, $mask_image);
  
  return TRUE;
}

/**
 * Given a mask image resource object in the $action, use the alpha values from
 * it to set transparency on the source image.
 * 
 * Note that the returned image has transparency set BUT if it's a jpeg it may
 * not remember that channel.
 * Need to switch formats or flatten before saving, or the transparency will be
 * lost.
 */
function canvasactions_mask(&$image, $mask) {
  // I do not believe there is a func for this, so I'll do it pixel-by-pixel
  // Slow, I know.

  $info = $image->info;
  if (!$info) { return FALSE; }
  $img = &$image->resource;
  $msk = &$mask->resource;
  imagesavealpha($image->resource, TRUE);
  imagealphablending($image->resource, FALSE);

  // Support indexed color (gif) if I have to
  $transparent_ix = imagecolortransparent($image->resource);
    
  $width = imagesx($img);  // Use the actual, not claimed image size.
  $height = imagesy($img);
  for ($i = 0; $i < $height; $i++) { //this loop traverses each row in the image
    for ($j = 0; $j < $width; $j++) { //this loop traverses each pixel of each row

      // Get the color & alpha info of the current pixel
      $color_ix = imagecolorat($img, $j, $i); // an index
      $rgba_array = imagecolorsforindex($img, $color_ix);
      
      // support indexed trans
      if ($color_ix == $transparent_ix) { $rgba_array['alpha'] = 127; }
      
      // Get the alpha of the corresponding mask pixel
      $mask_color_ix = imagecolorat($msk, $j, $i); // an index
      $msk_rgba_array = imagecolorsforindex($msk, $mask_color_ix);

      // Calculate the total alpha value of this pixel
      $rgba_array['alpha'] = max($msk_rgba_array['alpha'], $rgba_array['alpha']);

      //paint the pixel
      if($image->info['mime_type'] == 'image/gif') {
        // indexed color - re-use existing pallette
        $color_to_paint = imagecolorclosestalpha($image->resource, $rgba_array['red'], $rgba_array['green'], $rgba_array['blue'], $rgba_array['alpha']);
      }
      else {
        $color_to_paint = imagecolorallocatealpha($image->resource, $rgba_array['red'], $rgba_array['green'], $rgba_array['blue'], $rgba_array['alpha']);
      }
      imagesetpixel($image->resource, $j, $i, $color_to_paint);  
    }
  }
  return TRUE;
}

/**
 * Implementation of theme_hook() for imagecache_ui.module
 */
function theme_canvasactions_roundedcorners($element) {
  $data = $element['#value'];
  if($data['independent_corners_set']['independent_corners']){
    $dimens = join('px | ', $data['independent_corners_set']['radii']).'px';
  } else { 
    $dimens = "Radius: {$data['radius']}px "; 
  }
  return $dimens . ($data['antialias'] ? "antialiased" : "") ;
}
