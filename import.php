<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?
$f = fopen($ogd_source, "r");
$headers = fgetcsv($f);
$r = array_map("utf8_encode", $headers);

mkdir("data");
unlink("data/baum.db");

$db = new PDO("sqlite:data/baum.db");

$db->beginTransaction();

modify_headers($headers);

$db->query("create table data (". implode(", ", array_map(function($col) {
  global $db;

  if(is_array($col)) {
    return $db->quote($col[0]) ." ". $col[1];
  }
  else
    return $db->quote($col) . " text";
}, $headers)) . ")");

while($r = fgetcsv($f)) {
  if($ogd_source_encoding == "ISO-8859-1")
    $r = array_map("utf8_encode", $r);

  modify_data($r);

  $db->query("insert into data values (". implode(", ", array_map(function($v) {
    global $db;

    return $db->quote($v);
  }, $r)) . ")");
}

// Indexes
$db->query("create index data_lat on data(LAT)");
$db->query("create index data_lon on data(LON)");

$db->commit();
