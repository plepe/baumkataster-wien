<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?
$f = fopen("http://data.wien.gv.at/daten/geo?service=WFS&request=GetFeature&version=1.1.0&typeName=ogdwien:BAUMOGD&srsName=EPSG:4326&outputFormat=csv", "r");
$headers = fgetcsv($f);
$r = array_map("utf8_encode", $headers);

mkdir("data");
unlink("data/baum.db");

$db = new PDO("sqlite:data/baum.db");

$db->beginTransaction();

$db->query("create table data (". implode(", ", array_map(function($col) {
  global $db;

  return $db->quote($col) . " text";
}, $headers)) . ")");

while($r = fgetcsv($f)) {
  $r = array_map("utf8_encode", $r);

  $db->query("insert into data values (". implode(", ", array_map(function($v) {
    global $db;

    return $db->quote($v);
  }, $r)) . ")");
}

$db->commit();
