<?php
$depend = array("modulekit-form", "modulekit-table", "twig", "modulekit-form-geolocation", "json_readable_encode", "modulekit-ajax", "map");

$include = array(
  'php'=> array(
    'inc/get_data.php',
    'inc/baum.php',
  ),
  'js' => array(
    'index.js',
    'inc/haversine.js',
  ),
  'css' => array(
    'style.css',
  ),
);
