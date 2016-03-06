<?php
/**
 * array(
 *   'type' => 'text|real|...',
 *   'csv'  => 'alternative csv column name',
 *   'modify' => function which parses data from array
 */
$db_columns = array(
  'OBJECTID' => array(
    'type' => 'text',
  ),
  'BAUMNUMMER' => null,
  'BEZIRK' => null,
  'OBJEKT_STRASSE' => null,
  'GEBIETSGRUPPE' => null,
  'GATTUNG_ART' => null,
  'PFLANZJAHR' => null,
  'STAMMUMFANG' => null,
  'BAUMHOEHE' => null,
  'KRONENDURCHMESSER' => null,
  'LAT' => array(
    'type' => 'real',
    'modify' => function($data) {
      preg_match("/POINT \(([0-9\.]+) ([0-9\.]+)\)/", $data['SHAPE'], $m);
      return $m[2];
    }
  ),
  'LON' => array(
    'type' => 'real',
    'modify' => function($data) {
      preg_match("/POINT \(([0-9\.]+) ([0-9\.]+)\)/", $data['SHAPE'], $m);
      return $m[1];
    }
  ),
);

$form_search_def = array(
  'OBJECTID' => array(
    'type' => 'hidden',
    'name' => 'OBJECTID',
    'filter_function' => <<<EOT
function(data, filter_value) {
  return (data == filter_value);
}
EOT
  ),
  'BAUMNUMMER' => array(
    'type' => 'text',
    'name' => 'Baumnummer',
    'sql_function' => function($v) {
      global $db;

      return "BAUMNUMMER=". $db->quote($v);
    },
    'filter_function' => <<<EOT
function(data, filter_value) {
  if(!filter_value)
    return true;

  if(data == filter_value)
    return true;

  return false;
}
EOT
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
    'link' => "?OBJECTID={{ OBJECTID }}",
  ),
  'GEBIETSGRUPPE' => array(
    'name' => "Ge&shy;biet",
    'show_priority' => 1,
  ),
  'OBJEKT_STRASSE' => array(
    'name' => "Straße / Park",
    'show_priority' => 6,
  ),
  'GATTUNG_ART' => array(
    'name' => "Art",
  ),
  'PFLANZJAHR' => array(
    'name' => "Pflanz&shy;jahr",
    'format' => "{% if PFLANZJAHR != 0 %} {{ PFLANZJAHR }} {% endif %}",
    'show_priority' => 3,
  ),
  'STAMMUMFANG' => array(
    'name' => "Stamm&shy;um&shy;fang",
    'format' => "{% if STAMMUMFANG != 0 %}{{ STAMMUMFANG }} cm{% endif %}",
    'show_priority' => 2,
  ),
  'KRONENDURCHMESSER' => array(
    'name' => "Kro&shy;nen&shy;durch&shy;mes&shy;ser",
    'format' => "{% if KRONENDURCHMESSER == 0 %}{% elseif KRONENDURCHMESSER == 1 %}0-3 m{% elseif KRONENDURCHMESSER == 8 %}> 21 m{% else %}{{ KRONENDURCHMESSER * 3 - 2 }}-{{ KRONENDURCHMESSER * 3 }} m{% endif %}",
    'show_priority' => 2,
  ),
  'BAUMHOEHE' => array(
    'name' => "Baum&shy;höhe",
    'format' => "{% if BAUMHOEHE == 0 %}{% elseif BAUMHOEHE == 1 %}0-5 m{% elseif BAUMHOEHE == 8 %}> 35 m{% else %}{{ BAUMHOEHE * 5 - 4 }}-{{ BAUMHOEHE * 5 }} m{% endif %}",
    'show_priority' => 2,
  ),
  'geo' => array(
    'name' => "Koor&shy;dina&shy;ten",
    'format' => "<a target='_blank' href='http://www.openstreetmap.org/?mlat={{ LAT }}&amp;mlon={{ LON }}&zoom=18'>{{ LAT|number_format(5) }} {{ LON|number_format(5) }}</a>",
    'show_priority' => 4,
  ),
  'distance' => array(
    'name' => "Ent&shy;fern&shy;ung",
    'format' => "{% if distance %}{{ distance|number_format(0) }} m{% endif %}",
    'sort' => array("type" => "num", "dir" => "asc"),
    'show_priority' => 7,
  ),
);
