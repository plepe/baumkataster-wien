<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: application/json; charset=utf-8"); ?>
<?php
if(array_key_exists('dataset', $_REQUEST)) {
  if(in_array($_REQUEST['dataset'], $datasets)) {
    $dataset = new Dataset($_REQUEST['dataset']);
    include "datasets/{$_REQUEST['dataset']}.php"; // deprecated
  }
  else {
    print "Invalid dataset!";
    exit(1);
  }
}

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
    'info' => $dataset->view(),
    'count' => $count,
    'data' => $data,
  );

  print json_readable_encode($result);
}
