<?php
function get_data($search, $form_search_def, $limit = 1024) {
  global $db;

  $where = array();
  $add_columns = array();
  $order = array();

  foreach($search as $k=>$v) {
    if($v !== null) {
      if(array_key_exists('sql_function', $form_search_def[$k])) {
	if($w = $form_search_def[$k]['sql_function']($v))
	  if(is_array($w)) {
	    if(array_key_exists('where', $w))
	      $where[] = $w['where'];
	    if(array_key_exists('order', $w))
	      $order[] = $w['order'];
	    if(array_key_exists('add_columns', $w))
	      $add_columns[] = $w['add_columns'];
	  }
	  else
	    $where[] = $w;
      }
      else {
	if(!strpos('"', $k))
	  $where[] = '"'. $k . '"=' .  $db->quote($v);
      }
    }
  }

  if(sizeof($where))
    $where = "where ". implode(" and ", $where);
  else
    $where = "";

  if(sizeof($add_columns))
    $add_columns = ", ". implode(", ", $add_columns);
  else
    $add_columns = "";

  if(sizeof($order))
    $order = "order by ". implode(", ", $order);
  else
    $order = "";

  $query = "select count(*) c from (select *{$add_columns} from data {$where}) t";
  $res = $db->query($query);
  $elem = $res->fetch();
  $count = $elem['c'];
  $res->closeCursor();

  $query = "select *{$add_columns} from data {$where} {$order}";
  if($limit !== null)
    $query .= " limit {$limit}";
  //print "<pre wrap>". htmlspecialchars($query) ."</pre>\n";
  $res = $db->query($query);
  $data = array();
  while($elem = $res->fetch()) {
    $data[] = $elem;
  }
  $res->closeCursor();

  return array($count, $data);
}
