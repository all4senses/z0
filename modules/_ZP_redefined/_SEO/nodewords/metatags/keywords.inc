<?php
// $Id: keywords.inc,v 1.1.2.3 2008/08/12 10:18:04 robrechtj Exp $

/**
 * @file
 * Support file for KEYWORDS meta tag.
 */

function nodewords_keywords_prepare($type, $ids, $value, $settings) {
  
// my changes -----------------------------	
	
  	
	//if($type == 'node' && function_exists('taxonomy_node_get_terms') && count($ids) == 1) 
	if($type == 'node')
	{
		$node_type = db_result(db_query("SELECT type from {node} WHERE nid  = %d", arg(1)));
		
		if($node_type == 'c_department' OR $node_type == 'mc_descr' OR $node_type == 'c_shop')
  		{
  			//content_field_keywords
  			$value = db_result(db_query("SELECT field_keywords_value from {content_field_keywords} WHERE nid = %d", arg(1)));
  			
  			// добавим после специально заданых ключевых слов для отдела
  			// всю ветку термов каталога
          	$vid2 = 1; // словарь с каталогом
          	$category2 = db_fetch_object(db_query_range("SELECT t.tid, t.name FROM {term_data} t INNER JOIN {term_node} r ON r.tid = t.tid WHERE t.vid = %d AND r.nid = %d ORDER BY weight", $vid2, arg(1), 0, 1));
          	//$category2->vid = $vid2;
          	$all_linage = taxonomy_get_parents_all($category2->tid);
  			
          	
          	$len = count($all_linage);
          	for($i = 1; $i < $len-1; $i++)
          	{
          		
          		// в ключевых словах показываем всех родителей, кроме страны ($len-1), района ($len-3) и магазина ($len-4)
          		if($i == $len-3 OR $i == $len-4)
          			continue;
          		$linage = $linage . ',' . $all_linage[$i]->name;
          	}
          	
          	$value = $value . ',' . $linage;
          	
  			//global $user;
  			//if($user->uid == 1)
  			{	
  				//zp_functions_show($category2);
          		//zp_functions_show($all_linage);
          		
          		//echo '<br>linage = ' . $linage . '<br>';
          		//echo 'value = ' . $value . '<br>';
  			}
  		}
  		
  		
		else if(strpos($node_type, 'product') !== FALSE)
  		{
  			//content_field_keywords
  			$value = db_result(db_query("SELECT field_keywords_value from {content_field_keywords} WHERE nid = %d", arg(1)));
  			// добавляем перед ключевыми словами товара ключевые слова родительского отдела, если они есть
  			$parent_otdel = zp_functions_get_parent_otdel(arg(1));
  			if($parent_keywords = db_result(db_query("SELECT field_keywords_value from {content_field_keywords} WHERE nid = %d", $parent_otdel['nid'])))
  				$value = $parent_keywords . ',' . $value;
  				
  			// добавим после специально заданых ключевых слов для продукта (или его отдела)
  			// всю ветку термов каталога
          	$vid2 = 1; // словарь с каталогом
          	$category2 = db_fetch_object(db_query_range("SELECT t.tid, t.name FROM {term_data} t INNER JOIN {term_node} r ON r.tid = t.tid WHERE t.vid = %d AND r.nid = %d ORDER BY weight", $vid2, arg(1), 0, 1));
          	//$category2->vid = $vid2;
          	$all_linage = taxonomy_get_parents_all($category2->tid);
  			
          	
          	$len = count($all_linage);
          	for($i = 2; $i < $len-1; $i++)
          	{
          		// начинаем с 2, т.к. в description мы уже указали подгруппу, родительский отдел и магазин... осталось
          		// в ключевых словах показываем всё остальное, кроме страны ($len-1), района ($len-3) и магазина ($len-4)
          		if($i == $len-3 OR $i == $len-4)
          			continue;
          		$linage = $linage . ',' . $all_linage[$i]->name;
          	}
          	
          	$value = $value . ',' . $linage;
          	
  		}


  		else 
		// my changes ------------- end of my changes
		if(function_exists('taxonomy_node_get_terms') && count($ids) == 1) 
		{
     		// my changes
     		// для статей не показываем все ключевые слова ноды
     		// специально ДЛЯ ТОГО, чтобы в ключевые слова не попадали сокращённые названия каталогов, к которым привязываются статьи
		 if($node_type != 'zp_issue' AND $node_type != 'zp_issue_spravka')
			if (node_access('view', node_load($ids[0]))) 
      		{
       			foreach (taxonomy_node_get_terms($ids[0]) as $term) 
       			{
        			if (in_array($term->vid, $settings['keywords_vids'])) 
        			{
          				if ($settings['keywords_include_parents']) 
          				{
            				foreach (taxonomy_get_parents_all($term->tid) as $parent) 
              					$value .= ','. $parent->name;
          				}
          				else 
            				$value .= ','. $term->name;
	         		}
				}
			} 
		}
	} // end of if($type == 'node')
  
// my changes -----------------------------

  if($_GET['page'] AND $_GET['tf'])
  	$value = $_GET['page'] . ', ' . $_GET['tf'] . ', ' . $value;
  else if($_GET['page'])
  	$value = $_GET['page'] . ', ' . $value;
  else if($_GET['tf'])
  	$value = $_GET['tf'] . ', ' . $value;

  if(strpos($node_type, 'product') !== FALSE OR $node_type == 'c_department' OR $node_type == 'mc_descr')
	$value .= ',интернет-магазин, электронный магазин, заказ товаров, заказать продукты, домой, срочная доставка, продукты через интернет, продукты по интернету, товары через интернет, еда, поесть, пожрать'; 
  else if($node_type == 'zp_issue' OR $node_type == 'zp_issue_spravka')
	$value .= ',статьи о товарах, статьи о продуктах, обзор, описания, описание, отзыв, комментарии, критика, жалобы, разъяснения'; 
  else
  	$value .= ','. $settings['global']['keywords'];
 
  $value = _nodewords_keywords_uniq($value);
  
  // если этого не сделать, то ключевые слова обрываются на первом же пробеле в одной из ключевых фраз, 
  // а так мы пробел заменяем дефисом... вроде так нормально должно быть
  $value = str_replace(' ', '-', $value);
  
  return $value;
}

function nodewords_keywords_form($type, $value, $settings) {
  return array(
    '#type' => 'textfield',
    '#title' => t('Keywords'),
    '#default_value' => $value,
    '#size' => 60,
    '#maxlength' => $settings['max_size'],
    '#description' => t('Enter a comma separated list of keywords for this page. Avoid duplication of words as this will lower your search engine ranking.'),
  );
}

/*
 * Uniq a string which is a seperated list to items, preserving
 * the order in which they appeared.
 */
function _nodewords_keywords_uniq($text, $max_items = 0, $seperator = ',') {
  if (empty($text)) {
    return $text;
  }

  $items = array_map('trim', explode($seperator, $text));
  $uniq_values = array();
  $uniq_lowers = array();

  foreach ($items as $item) {
    $lower = strtolower($item);
    if (!in_array($lower, $uniq_lowers) && $lower != '') {
      $uniq_values[] = $item;
      $uniq_lowers[] = $lower;
    }
  }

  if ($max_items > 0) {
    $uniq_values = array_slice($uniq_values, 0, $max_items);
  }

  return implode($seperator, $uniq_values);
}
