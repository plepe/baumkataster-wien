<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: application/javascript; charset=utf-8"); ?>
<?php
$db = new PDO("sqlite:data/baum.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
include("inc/db.php");

$form_search = new form(null, $form_search_def, array(
    'orig_data' => false,
  ));

if($form_search->is_complete()) {
  $search = $form_search->save_data();
  $form_search->set_orig_data($search);

  $data = get_data($search, $form_search_def);

  print json_readable_encode($data);
}
