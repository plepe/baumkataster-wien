<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: text/html; charset=utf-8"); ?>
<?php
html_export_var(array("table_def" => $table_def));
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
$max_list = 30;

$form_search = new form(null, $form_search_def, array(
    'orig_data' => false,
  ));

$content = "";
if($form_search->is_complete()) {
  $search = $form_search->save_data();
  $form_search->set_orig_data($search);

  $data = get_data($search, $form_search_def);

  if(sizeof($data)) {
    if(sizeof($data) > $max_list) {
      $content .= sprintf("%d Bäume gefunden (%d gelistet):", sizeof($data), $max_list);
      $data = array_slice($data, 0, $max_list);
    }
    else
      $content .= sprintf("%d Bäume gefunden:", $count);

    $table = new table($table_def, $data, array(
      'template_engine' => 'twig',
    ));
    $content .= $table->show();
  }
  else {
    $content = "Kein Baum gefunden.";
  }
}
else {
  $res = $db->query("select count(*) c from data");
  $elem = $res->fetch();
  $content = "<p>{$elem['c']} Bäume im Baumkataster. Stand: ". Date("d.m.Y", filemtime("data/baum.db"));
}

print "<form method='get'>\n";
print $form_search->show();
print "<input type='submit' value='Suche'>\n";
print "</form>\n";

print "<div id='content'>\n";
print $content;
print "</div>\n";
?>
<p>(cc) <a href='mailto:skunk@xover.mud.at'>Stephan Bösch-Plepelits</a>, <a href='https://github.com/plepe/baumkataster-wien'>Source Code (Github)</a>
  </body>
</html>
