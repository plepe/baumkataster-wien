<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: text/html; charset=utf-8"); ?>
<?php
$max_list = 30;
html_export_var(array("table_def" => $table_def, "max_list" => $max_list));
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

  $data = get_data($search, $form_search_def);

  $search_status = twig_render("result.html", array(
    'count' => sizeof($data),
    'max_list' => $max_list,
  ));

  $table_content = "";
  if(sizeof($data)) {
    $table = new table($table_def, $data, array(
      'template_engine' => 'twig',
    ));
    $table_content = $table->show("html", array(
      "limit" => $max_list,
    ));
  }
}
else {
  $content = "Bitte Baumnummer angeben oder auf Erkennung Deiner Position warten.";
}

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
  </body>
</html>
