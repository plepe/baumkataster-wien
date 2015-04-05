<?php
function modify_headers(&$headers) {
  $headers[] = "LAT";
  $headers[] = "LON";
}

function modify_data(&$data) {
  preg_match("/POINT \(([0-9\.]+) ([0-9\.]+)\)/", $data[2], $m);
  $data[] = $m[2];
  $data[] = $m[1];
}
