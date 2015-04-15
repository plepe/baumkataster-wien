<?php
function modify_headers(&$headers) {
  $headers[] = array("LAT", "real");
  $headers[] = array("LON", "real");
}

function modify_data(&$data) {
  preg_match("/POINT \(([0-9\.]+) ([0-9\.]+)\)/", $data[2], $m);
  $data[] = $m[2];
  $data[] = $m[1];
}

$form_search_def = array(
  'BAUMNUMMER' => array(
    'type' => 'text',
    'name' => 'Baumnummer',
    'sql_function' => function($v) {
      global $db;

      if(preg_match("/^([0-9]+)([0-9]{1})$/", $v, $m)) {
	return "BAUMNUMMER in (". $db->quote($m[1]." ".$m[2]). ", ". $db->quote("{$m[1]}{$m[2]}  ").")";
      }

      if(preg_match("/^([0-9]+) ?([0-9A-Za-z]+)?$/", $v, $m)) {
	if(!$m[2])
	  $m[2] = " ";
	$m[2] = strtoupper($m[2]);

	return "BAUMNUMMER=". $db->quote("{$m[1]} {$m[2]}");
      }

      return "BAUMNUMMER=". $db->quote($v);
    },
  ),
  'location' => array(
    'type' => 'geolocation',
    'name' => "In der Umgebung von",
    'desc' => "Nur Bäume im Umkreis von max. 1km",
    'options' => array(
      'enableHighAccuracy' => true,
    ),
    'sql_function' => function($v) {
      global $db;

      // 0.0090 resp 0.0135 is approx. 1.5km at the center of Vienna, Austria
      $bbox = array(
	(float)$v['latitude'] - 0.0090,
	(float)$v['longitude'] - 0.0135,
	(float)$v['latitude'] + 0.0090,
	(float)$v['longitude'] + 0.0135
      );

      return array(
        'add_columns' => "distance(lat, lon, ". $db->quote($v['latitude']) .", ". $db->quote($v['longitude']) .") as distance",
	'order' => "distance asc",
	'where' => "distance <= 1000 and lat >= {$bbox[0]} and lon >= {$bbox[1]} and lat <= {$bbox[2]} and lon <= {$bbox[3]}",
      );
    },
  ),
);

$table_def = array(
  'BAUMNUMMER' => array(
    'name' => "Baum&shy;num&shy;mer",
  ),
  'GEBIET' => array(
    'name' => "Ge&shy;biet",
  ),
  'STRASSE' => array(
    'name' => "Straße / Park",
  ),
  'ART' => array(
    'name' => "Art",
  ),
  'PFLANZJAHR' => array(
    'name' => "Pflanz&shy;jahr",
    'format' => "{% if PFLANZJAHR %} {{ PFLANZJAHR }} {% endif %}",
  ),
  'STAMMUMFANG' => array(
    'name' => "Stamm&shy;um&shy;fang",
    'format' => "{{ STAMMUMFANG }} cm",
  ),
  'KRONENDURCHMESSER' => array(
    'name' => "Kro&shy;nen&shy;durch&shy;mes&shy;ser",
    'format' => "{{ KRONENDURCHMESSER }} m",
  ),
  'BAUMHOEHE' => array(
    'name' => "Baum&shy;höhe",
    'format' => "{{ BAUMHOEHE }} m",
  ),
  'geo' => array(
    'name' => "Koor&shy;dina&shy;ten",
    'format' => "<a target='_blank' href='http://www.openstreetmap.org/?mlat={{ LAT }}&amp;mlon={{ LON }}&zoom=18'>{{ LAT|number_format(5) }} {{ LON|number_format(5) }}</a>",
  ),
  'distance' => array(
    'name' => "Ent&shy;fern&shy;ung",
    'format' => "{% if distance %}{{ distance|number_format(0) }} m{% endif %}",
    'sort' => array("type" => "num", "dir" => "asc"),
  ),
);
