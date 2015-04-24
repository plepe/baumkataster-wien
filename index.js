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

  var t = new table(table_def, data.data, {
    template_engine: "twig"
  });

  var table_content = "";
  if(data.data.length > 0)
    table_content = t.show("html", { limit: max_list });

  document.getElementById("table").innerHTML = table_content;
}

function update_data(search_param, _data) {
  reload_active = false;
  document.body.className = "";

  orig_search_param = search_param;
  form_search.set_orig_data(search_param);

  if((!_data) || (!_data.data)) {
    alert("Error loading data!");
    return;
  }

  data = _data;
  update_table();

  call_hooks("update_data", data);

  twig_render_into(document.getElementById("search_status"), "result.html", {
    'count': data.data.length,
    'max_list': max_list
  });

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

  call_hooks("update_location", search_param);

  if(reload && (!reload_active)) {
    ajax("data.php", form_search.get_request_data(), update_data.bind(this, search_param));
    document.body.className = "loading";
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

  call_hooks("init");

  orig_search_param = form_search.get_data();
}
