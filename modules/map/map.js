register_hook("init", function() {
  var map = L.map('map').setView([map_config.lat, map_config.lon], map_config.zoom);

  L.tileLayer(map_config.url, {
    attribution: map_config.attribution,
    maxZoom: map_config.maxZoom
  }).addTo(map);
});
