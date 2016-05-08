<?php
$depend = array("modulekit-form", "modulekit-table", "twig", "modulekit-form-geolocation", "json_readable_encode", "modulekit-ajax");

$include = array(
  'php'=> array(
    'inc/get_data.php',
    'inc/db.php',
    'inc/Dataset.php',
  ),
  'js' => array(
    'dataset.js',
    'inc/haversine.js',
  ),
  'css' => array(
    'style.css',
  ),
);
