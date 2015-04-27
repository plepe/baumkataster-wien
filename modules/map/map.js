var map;
var map_features;
var loc_feature;

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
  for(var i = 0; i < data.data.length; i++) {
    var el = data.data[i];

    features.push(new L.circleMarker([ el.LAT, el.LON ], {
      radius: 3,
      stroke: true,
      color: '#00ff00',
      weight: 1,
      fillOpacity: 1,
      fill: true,
      fillColor: '#007f00'
    }));
  }

  if(map_features)
    map.removeLayer(map_features);

  map_features = new L.featureGroup(features).addTo(map);
});
