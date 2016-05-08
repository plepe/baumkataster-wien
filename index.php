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
<?php call_hooks("html_head"); ?>
<?php
print "Available Datasets:";

print "<ul>\n";
foreach($datasets as $id) {
  print "<li><a href='dataset.php?dataset={$id}'>{$id}</a></li>\n";
}
print "</ul>\n";
?>
<?php call_hooks("html_bottom"); ?>
  </body>
</html>
