<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
<?php Header("content-type: text/html; charset=utf-8"); ?>
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

$form_search = new form("data", $form_search_def);

$content = "";
if($form_search->is_complete()) {
  $search = $form_search->get_data();

  $res = $db->query("select * from data where BAUMNUMMER regexp '^\s*' || ". $db->quote($search['BAUMNUMMER']) . " || '\s*$'");
  while($elem = $res->fetch()) {
    $data[] = $elem;
  }

  if(sizeof($data)) {
    $content .= sprintf("%d Bäume gefunden:", sizeof($data));
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

print $content;
?>
<p>(cc) <a href='mailto:skunk@xover.mud.at'>Stephan Bösch-Plepelits</a>, <a href='https://github.com/plepe/baumkataster-wien'>Source Code (Github)</a>
  </body>
</html>
