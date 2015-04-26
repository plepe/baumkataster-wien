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

var filters = {};
function load_filters() {
  for(var k in form_search.def) {
    if(form_search.def[k].filter_function) {
      eval("_filter = " + form_search.def[k].filter_function);
      filters[k] = _filter;
    }
  }
}

function apply_filters(data, filter) {
  for(var k in filters) {
    if(!filters[k](data[k], filter[k]))
      return false;
  }

  return true;
}

function update_table() {
  if(!data.data)
    return;

  update_distances();
  var search_param = form_search.get_data();

  var filtered_data = [];
  for(var i = 0; i < data.data.length; i++) {
    if(apply_filters(data.data[i], search_param))
      filtered_data.push(data.data[i]);
  }

  var t = new table(table_def, filtered_data, {
    template_engine: "twig"
  });

  twig_render_into(document.getElementById("search_status"), "result.html", {
    'count': filtered_data.length,
    'max_list': max_list
  });

  var table_content = "";
  if(filtered_data.length == 1)
    table_content = t.show("html-transposed");
  else if(filtered_data.length > 0)
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
    var ajax_param = {
      latitude: search_param.location.latitude,
      longitude: search_param.location.longitude
    };

    ajax("data.php", ajax_param, update_data.bind(this, search_param));
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
  load_filters();
  document.getElementById("form_search").onsubmit = function() {
    update_location();
    return false;
  }

  call_hooks("init");

  orig_search_param = form_search.get_data();
}
