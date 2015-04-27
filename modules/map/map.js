var map;
var map_features;
var features_list = {};
var shown_feature;
var loc_feature;

var feature_style = {
  radius: 3,
  stroke: true,
  color: '#00ff00',
  weight: 1,
  fillOpacity: 1,
  fill: true,
  fillColor: '#007f00'
};
var shown_feature_style = {
  radius: 4,
  stroke: true,
  color: '#000000',
  weight: 2,
  fillOpacity: 1,
  fill: true,
  fillColor: '#007f00'
};

register_hook("init", function() {
  map = L.map('map').setView([map_config.lat, map_config.lon], map_config.zoom);

  L.tileLayer(map_config.url, {
    attribution: map_config.attribution,
    maxZoom: map_config.maxZoom
  }).addTo(map);
});

register_hook("update_location", function(data) {
  if(data.location) {
    var pos = [ data.location.latitude, data.location.longitude ];

    map.setView(pos);

    if(!loc_feature) {
      var icon = L.icon({
	iconUrl: 'img/geolocation.svg',
	iconSize: [ 19, 19 ],
	iconAnchor: [ 10, 10 ]
      });

      loc_feature = new L.marker(pos, {
	icon: icon,
	clickable: false
      });
      loc_feature.addTo(map);
    }
    else
      loc_feature.setLatLng(pos);
  }
});

register_hook("update_data", function(data) {
  var features = [];
  var new_features_list = {};

  for(var i = 0; i < data.data.length; i++) {
    var el = data.data[i];

    var feature = new L.circleMarker([ el.LAT, el.LON ], feature_style);

    feature.on('click', function(ob, e) {
      update_url("?OBJECTID=" + ob.OBJECTID);
    }.bind(this, el));

    features.push(feature);
    new_features_list[el.OBJECTID] = feature;
  }

  if(map_features) {
    map.removeLayer(map_features);
    features_list = null;
  }

  map_features = new L.featureGroup(features);
  map_features.addTo(map);
  features_list = new_features_list;
  shown_feature = null;
});

register_hook("show_single", function(el) {
  if(!(el.OBJECTID in features_list))
    return;

  if(shown_feature) {
    shown_feature.setStyle(feature_style);
  }

  shown_feature = features_list[el.OBJECTID];
  shown_feature.setStyle(shown_feature_style);
  shown_feature.bringToFront();
});

register_hook("show_empty", function(el) {
  if(shown_feature) {
    shown_feature.setStyle(feature_style);
    shown_feature = null;
  }
});

register_hook("show_multiple", function(el) {
  if(shown_feature) {
    shown_feature.setStyle(feature_style);
    shown_feature = null;
  }
});
