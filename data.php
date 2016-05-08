<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: application/json; charset=utf-8"); ?>
<?php
if(array_key_exists('dataset', $_REQUEST)) {
  if(in_array($_REQUEST['dataset'], $datasets)) {
    $id = $_REQUEST['dataset'];
    include "datasets/{$id}.php";
  }
  else {
    print "Invalid dataset!";
    exit(1);
  }
}

$db = new PDO("sqlite:data/baum.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
include("inc/db.php");

$form_search = new form(null, $form_search_def, array(
    'orig_data' => false,
  ));

if($_REQUEST['latitude'] && $_REQUEST['longitude']) {
  $form_search->set_data(array(
    "location" => array(
      "latitude" => $_REQUEST['latitude'],
      "longitude" => $_REQUEST['longitude'],
    ),
  ));
}

if(true) {
  $search = $form_search->save_data();
  $form_search->set_orig_data($search);

  list($count, $data) = get_data($search, $form_search_def);

  $result = array(
    'info' => data_info(),
    'count' => $count,
    'data' => $data,
  );

  print json_readable_encode($result);
}
