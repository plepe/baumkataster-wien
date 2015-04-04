<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?
$f = fopen("http://data.wien.gv.at/daten/geo?service=WFS&request=GetFeature&version=1.1.0&typeName=ogdwien:BAUMOGD&srsName=EPSG:4326&outputFormat=csv&maxFeatures=50", "r");
$headers = fgetcsv($f);

while($r = fgetcsv($f)) {
  $r = array_map("utf8_encode", $r);
  $data = array_combine($headers, $r);
  print_r($data);
}
