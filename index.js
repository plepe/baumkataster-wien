var orig_search_param;
var reload_active = false;
var data;

function update_distances() {
  search_param = form_search.get_data();

  for(var i = 0; i < data.data.length; i++) {
    var el = data.data[i];

    el.distance = haversine({
      latitude: search_param.location.latitude,
      longitude: search_param.location.longitude
    }, {
      latitude: parseFloat(el.LAT),
      longitude: parseFloat(el.LON),
    }, {unit: 'meter'});
  }
}

function update_table() {
  if(!data.data)
    return;

  update_distances();

  var content_div = document.getElementById("content");
  var t = new table(table_def, data.data, {
    template_engine: "twig"
  });

  var content = "";
  if(data.data.length > max_list)
    content += sprintf("%d Bäume gefunden (%d gelistet):", data.data.length, max_list);
  else if(data.data.length == 0)
    content += "Kein Baum gefunden.";
  else
    content += sprintf("%d Bäume gefunden:", data.data.length);

  content += t.show("html", { limit: max_list });

  content_div.innerHTML = content;
}

function update_data(search_param, _data) {
  reload_active = false;
  orig_search_param = search_param;
  form_search.set_orig_data(search_param);

  if((!_data) || (!_data.data)) {
    alert("Error loading data!");
    return;
  }

  data = _data;
  update_table();

  twig_render_into(document.getElementById("footer"), "footer.html", data.info);
}

function update_location(reload) {
  var search_param = form_search.get_data();

  if(data) {
    var distance = haversine({
	latitude: search_param.location.latitude,
	longitude: search_param.location.longitude
      }, {
	latitude: orig_search_param.location.latitude,
	longitude: orig_search_param.location.longitude,
      }, {unit: 'meter'});

    if(distance > 100)
      reload = true;
  }
  else
    reload = true;

  if(reload && (!reload_active)) {
    ajax("data.php", search_param, update_data.bind(this, search_param));
    reload_active = true;
  }

  if(!data)
    return;

  update_table();
}

window.onload = function() {
  form_search = form__;
  form_search.onchange = update_location;
  document.getElementById("form_search").onsubmit = function() {
    update_location(true);
    return false;
  }

  orig_search_param = form_search.get_data();
}
