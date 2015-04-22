<?
register_hook("init", function() {
  global $map_config;

  html_export_var(array("map_config" => $map_config));
  add_html_header('<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />');
  add_html_header('<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>');
});

register_hook("html_bottom", function() {
  print "<div id='map'></div>";
});
