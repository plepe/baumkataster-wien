<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: text/html; charset=utf-8"); ?>
<?php
if(array_key_exists('dataset', $_REQUEST)) {
  if(in_array($_REQUEST['dataset'], $datasets)) {
    $dataset = $_REQUEST['dataset'];
    include "datasets/{$dataset}.php";
  }
  else {
    print "Invalid dataset!";
    exit(1);
  }
}

$max_list = 30;
html_export_var(array("dataset" => $dataset, "table_def" => $table_def, "max_list" => $max_list));
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Baumkataster Wien</title>
    <?php print modulekit_to_javascript(); /* pass modulekit configuration to JavaScript */ ?>
    <?php print modulekit_include_js(); /* prints all js-includes */ ?>
    <?php print modulekit_include_css(); /* prints all css-includes */ ?>
    <?php print_add_html_headers(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
<?php call_hooks("html_head"); ?>
<?php
$db = new PDO("sqlite:data/baum.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
include("inc/db.php");

$form_search = new form(null, $form_search_def, array(
    'orig_data' => false,
  ));

$content = "";
if($form_search->is_complete()) {
  $search = $form_search->save_data();
  $form_search->set_orig_data($search);

  if(!array_key_exists('location', $search) || !array_key_exists('latitude', $search['location']))
    unset($search['location']);

  list($count, $data) = get_data($search, $form_search_def, $max_list);

  $search_status = twig_render("result.html", array(
    'count' => $count,
    'max_list' => $max_list,
  ));

  $table_content = "";
  if(sizeof($data)) {
    $table = new table($table_def, $data, array(
      'template_engine' => 'twig',
    ));
    $table_content = $table->show((sizeof($data) == 1 ? "html-transposed" : "html"), array(
      "limit" => $max_list,
    ));
  }
}
else {
  $content = "Bitte Baumnummer angeben oder auf Erkennung Deiner Position warten.";
}

print "<div id='content-container'>\n";
print "<form id='form_search' method='get'>\n";
print $form_search->show();
print "<input type='submit' value='Suche'>";
print "<span id='load_status'>". twig_render("load_status.html", array()) ."</span>\n";
print "</form><hr>\n";

print "<div id='content'>\n";
print "<div id='search_status'>{$search_status}</div>\n";
print "<div id='table'>{$table_content}</div>\n";
print "</div>\n";
?>
<div id='footer'>
<?php
print twig_render("footer.html", data_info());
?>
</div>
</div>
<?php call_hooks("html_bottom"); ?>
  </body>
</html>
