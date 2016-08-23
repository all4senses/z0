<?php
// $Id: pathauto_node.inc,v 1.29.4.25 2008/02/03 12:31:32 greggles Exp $

/*
 * Implementation of hook_pathauto()
 */
function node_pathauto($op) {
  switch ($op) {
    case 'settings':
      $settings = array();
      $settings['module'] = 'node';
      $settings['token_type'] = 'node';
      $settings['groupheader'] = t('Node path settings');
      $settings['patterndescr'] = t('Default path pattern (applies to all node types with blank patterns below)');
      $settings['patterndefault'] = t('content/[title-raw]');
      $settings['bulkname'] = t('Bulk generate aliases for nodes that are not aliased');
      $settings['bulkdescr'] = t('Generate aliases for all existing nodes which do not already have aliases.');

      $patterns = token_get_list('node');
      foreach ($patterns as $type => $pattern_set) {
        if ($type != 'global') {
          foreach ($pattern_set as $pattern => $description) {
            $settings['placeholders']['['. $pattern .']'] = $description;
          }
        }
      }
      $settings['supportsfeeds'] = 'feed';
      $nodetypes = node_get_types();
      foreach ($nodetypes as $ntype => $nodetype) {
        $fieldlabel = t('Pattern for all @node_type paths', array('@node_type' => $nodetype->name));
        $settings['patternitems'][$ntype] = $fieldlabel;
      }
      return (object) $settings;
    default:
      break;
  }
}

/**
 * Generate aliases for all nodes without aliases. 
 */
function node_pathauto_bulkupdate() {

  // From all node types, only attempt to update those with patterns
  $pattern_types = array();
  $type_where = '';
  foreach (node_get_types() as $type => $info) 
  {
        $pattern = '';
        $pattern = variable_get('pathauto_node_'. $type .'_pattern', '');

        // If it's not set, check the default
        if (!trim($pattern)) {
          $pattern = variable_get('pathauto_node_pattern', '');
        }



         // original
        
        /*
        if (trim($pattern)) {
          $pattern_types[] = $type;
          if (!trim($type_where)) {
            $type_where = " AND (type = '%s' ";
          }
          else {
            $type_where .= " OR type = '%s'";
          }
        }
        */
        
        // my changes test 1
        if (trim($pattern)) 
        {
              $pattern_types[] = $type;
              
              // make a query more elegant with IN instead of OR
              if (!trim($type_where))
                $type_where = " AND type IN ('$type' ";
              else
                $type_where .= ", '$type'";
        }
        
        
        
        
  }
  $type_where .= ')';

  
  
  
  $count = 0;  
  $placeholders = array();
  
  
  // my changes 
  //global $user;
  $nodes_to_update = variable_get('pathauto_max_bulk_update', 50);
  //$nodes_amount_all = db_result(db_query("SELECT COUNT(nid) FROM {node} WHERE 1"));
  $nodes_amount_all = db_result(db_query("SELECT nid FROM {node} WHERE 1 ORDER BY nid DESC LIMIT 0,1"));
  
//  if($user->uid == 1)
//      {
//          echo '$nodes_amount_all = ' . $nodes_amount_all . '<br>';
//          //die;
//      }
      

  // step
  $delta = 300;
  
  // from end to begin
  
  $i_start = $nodes_amount_all - $delta;
  $i_end = $nodes_amount_all;
  
  // comment it if want to just one iteration from $nodes_amount_all back to $nodes_amount_all - $delta
  /////while($i_end > 0 AND $nodes_to_update > 0)
  {
      // select from $i_start to $i_end
      // make a query more elegant with IN instead of OR (see above)
      
      $result = db_query("SELECT nid, type, title, uid, created, src, dst, vid 
                                                    FROM {node} 
                                                    LEFT JOIN {url_alias} ON CONCAT('node/', nid) = src 
                                                    WHERE nid > " . $i_start . " AND nid <= " . $i_end
                                                . " AND src IS NULL ". $type_where 
                                                . " LIMIT 0," . $nodes_to_update);
      
      
//      if($user->uid == 1)
//      {
//          echo '$i_start = ' . $i_start . '<br>';
//          echo '$i_end = ' . $i_end . '<br>';
//          echo '$nodes_to_update = ' . $nodes_to_update . '<br>';
//      }
      
      while ($node_ref = db_fetch_object($result)) 
      {
            $node = node_load($node_ref->nid, NULL, TRUE);
            $node->src = $node_ref->src;
            $node->dst = $node_ref->dst;
            if (module_exists('taxonomy')) {
                // Must populate the terms for the node here for the category
                // placeholders to work
                $node->taxonomy = array_keys(taxonomy_node_get_terms($node->nid));
            }
            $placeholders = pathauto_get_placeholders('node', $node);
            $src = "node/$node->nid";

            // my changes test
            
//            if($user->uid == 1)
//            {
//                echo '$src = ' . $src . '<br>';
//                //echo '$placeholders = <pre>' . print_r($placeholders), true . '</pre><br>';
//                //die;
//            }
            


            //if (1) {
            if ($alias = pathauto_create_alias('node', 'bulkupdate', $placeholders, $src, $node->nid, $node->type)) {
              $count++;
            }
            
            // my changes
            $nodes_to_update--;

      }


      // my changes
      $i_end = $i_start - 1;
      //$i_end = ($i_start + $delta < $nodes_amount_all) ? $i_start + $delta : $nodes_amount_all;
      $i_start = ($i_end - $delta <= 0) ? 0 : $i_end - $delta;

      
//      if($user->uid == 1)
//      {
//          echo '--$i_start = ' . $i_start . '<br>';
//          echo '--$i_end = ' . $i_end . '<br>';
//          echo '--$nodes_to_update = ' . $nodes_to_update . '<br>';
//      }
      
  }
  
  // from begin to end
  /*
  $i_start = 0;
  $i_end = ($i_start + $delta < $nodes_amount_all) ? $i_start + $delta : $nodes_amount_all;
  
  while($i_start < $nodes_amount_all AND $nodes_to_update > 0)
  {
      // select from $i_start to $i_end
      // make a query more elegant with IN instead of OR (see above)
      $result = db_query("SELECT nid, type, title, uid, created, src, dst, vid 
                                                    FROM {node} 
                                                    LEFT JOIN {url_alias} ON CONCAT('node/', nid) = src 
                                                    WHERE nid > " . $i_start . " AND nid <= " . $i_end
                                                . " AND src IS NULL ". $type_where 
                                                . " LIMIT 0," . $nodes_to_update);
      
//      if($user->uid == 1)
//      {
//          echo '$i_start = ' . $i_start . '<br>';
//          echo '$i_end = ' . $i_end . '<br>';
//          echo '$nodes_to_update = ' . $nodes_to_update . '<br>';
//      }
      
      while ($node_ref = db_fetch_object($result)) 
      {
            $node = node_load($node_ref->nid, NULL, TRUE);
            $node->src = $node_ref->src;
            $node->dst = $node_ref->dst;
            if (module_exists('taxonomy')) {
                // Must populate the terms for the node here for the category
                // placeholders to work
                $node->taxonomy = array_keys(taxonomy_node_get_terms($node->nid));
            }
            $placeholders = pathauto_get_placeholders('node', $node);
            $src = "node/$node->nid";

            // my changes test
            
//            if($user->uid == 1)
//            {
//                echo '$src = ' . $src . '<br>';
//                //echo '$placeholders = <pre>' . print_r($placeholders), true . '</pre><br>';
//                //die;
//            }
            


            //if (1) {
            if ($alias = pathauto_create_alias('node', 'bulkupdate', $placeholders, $src, $node->nid, $node->type)) {
              $count++;
            }
            
            // my changes
            $nodes_to_update--;

      }


      // my changes
      $i_start = $i_end + 1;
      $i_end = ($i_start + $delta < $nodes_amount_all) ? $i_start + $delta : $nodes_amount_all;

      
//      if($user->uid == 1)
//      {
//          echo '--$i_start = ' . $i_start . '<br>';
//          echo '--$i_end = ' . $i_end . '<br>';
//          echo '--$nodes_to_update = ' . $nodes_to_update . '<br>';
//      }
      
  }
  */
  
  // my changes test
  /*
  $query = "SELECT nid, type, title, uid, created, src, dst, vid FROM {node} LEFT JOIN {url_alias} ON CONCAT('node/', nid) = src WHERE nid > 29000 AND src IS NULL ". $type_where . ' LIMIT 0,3';
  $result = db_query($query);
  
  global $user;
  if($user->uid == 1)
  {
      
      echo '$nodes_amount_all = ' . $nodes_amount_all . '<br>';
      echo '$query = ' . $query . '<br>';
      echo '$pattern_types = <pre>' . print_r($pattern_types, true) . '</pre><br>';
      echo 'result = <pre>' . print_r($result, true) . '</pre><br>';
      die('xxx');
  }
  */
  // end of my changes
  
  
  
  
  // original
  /*
  $query = "SELECT nid, type, title, uid, created, src, dst, vid FROM {node} LEFT JOIN {url_alias} ON CONCAT('node/', nid) = src WHERE src IS NULL ". $type_where;
  $result = db_query_range($query, $pattern_types, 0, variable_get('pathauto_max_bulk_update', 50));
 
  // original 
  // see above corrections

  $count = 0;
  $placeholders = array();
  while ($node_ref = db_fetch_object($result)) 
  {
    $node = node_load($node_ref->nid, NULL, TRUE);
    $node->src = $node_ref->src;
    $node->dst = $node_ref->dst;
    if (module_exists('taxonomy')) {
        // Must populate the terms for the node here for the category
        // placeholders to work
        $node->taxonomy = array_keys(taxonomy_node_get_terms($node->nid));
    }
    $placeholders = pathauto_get_placeholders('node', $node);
    $src = "node/$node->nid";

    if ($alias = pathauto_create_alias('node', 'bulkupdate', $placeholders, $src, $node->nid, $node->type)) {
      $count++;
    }
    
  }
  */
  

  drupal_set_message(format_plural($count,
    "Bulk generation of nodes completed, one alias generated.",
    "Bulk generation of nodes completed, @count aliases generated."));
}
