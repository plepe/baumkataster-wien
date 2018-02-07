<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php
$f = fopen($source['file'], "r");
$headers = fgetcsv($f);
if($source['encoding'] == "ISO-8859-1")
  $r = array_map("utf8_encode", $headers);

mkdir("data");
unlink("data/baum.db");

$db = new PDO("sqlite:data/baum.db");

$db->beginTransaction();

$db->query("create table data (". implode(", ", array_map(function($col, $def) {
  global $db;

  if($def === null)
    $def = array();
  if(!array_key_exists('type', $def))
    $def['type'] = 'text';

  return $db->quote($col) ." ". $def['type'];
}, array_keys($db_columns), $db_columns)) . ")");

while($r = fgetcsv($f)) {
  if($source['encoding'] == "ISO-8859-1")
    $r = array_map("utf8_encode", $r);

  $r = array_combine($headers, $r);

  $r = array_map(function($col, $def) use ($r) {
    if($def === null)
      $def = array();
    if(!array_key_exists('csv', $def))
      $def['csv'] = $col;

    if(array_key_exists('modify', $def))
      return call_user_func($def['modify'], $r);
    else
      return $r[$def['csv']];
  }, array_keys($db_columns), $db_columns);

  $db->query("insert into data values (". implode(", ", array_map(function($v) {
    global $db;

    return $db->quote($v);
  }, $r)) . ")");
}

// Indexes
$db->query("create index data_lat on data(LAT)");
$db->query("create index data_lon on data(LON)");

$db->commit();
