<?php
$db = new PDO("sqlite:data/baum.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$db->sqliteCreateFunction('regexp',
    function ($pattern, $data, $delimiter = '~', $modifiers = 'isuS')
    {
        if (isset($pattern, $data) === true)
        {
            return (preg_match(sprintf('%1$s%2$s%1$s%3$s', $delimiter, $pattern, $modifiers), $data) > 0);
        }

        return null;
    }
);

// from http://stackoverflow.com/a/16032915 (slightly modified)
// parameters:
//   lat1, lon1
//   lat2, lon2
$db->sqliteCreateFunction('distance',
  function() {
    // needs 4 parameters
    if(count(func_get_args()) != 4)
      return null;

    // convert to radians
    $geo = array_map('deg2rad', func_get_args());

    // apply the spherical law of cosines to our latitudes and longitudes, and set the result appropriately
    // 6378.1 is the approximate radius of the earth in kilometres
    return acos(sin($geo[0]) * sin($geo[2]) + cos($geo[0]) * cos($geo[2]) * cos($geo[1] - $geo[3])) * 6378140;
  },
  4);

function data_info() {
  global $db;

  $res = $db->query("select count(*) c from data");
  $elem = $res->fetch();

  $date = Date("c", filemtime("data/baum.db"));

  return array(
    'count' => $elem['c'],
    'date' => $date,
  );
}
