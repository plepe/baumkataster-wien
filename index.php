<?php include "conf.php"; /* load a local configuration */ ?>
<?php include "modulekit/loader.php"; /* loads all php-includes */ ?>
<?php call_hooks("init"); ?>
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
$form_def = array(
  'BAUMNUMMER' => array(
    'type' => 'text',
    'name' => 'Baumnummer',
  ),
);

$form_search = new form("data", $form_def);

$content = "";
if($form_search->is_complete()) {
  $db = new PDO("sqlite:data/baum.db");
  include("inc/db.php");

  $search = $form_search->get_data();

  $res = $db->query("select * from data where BAUMNUMMER regexp '^\s*' || ". $db->quote($search['BAUMNUMMER']) . " || '\s*$'");
  while($elem = $res->fetch()) {
    $content .= print_r($elem, 1);
  }
}

print "<form method='get'>\n";
print $form_search->show();
print "<input type='submit' value='Suche'>\n";
print "</form>\n";

print $content;
?>
  </body>
</html>
