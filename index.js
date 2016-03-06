var orig_search_param;
var reload_active = false;
var data;

function update_distances() {
  search_param = form_search.get_data();

  for(var i = 0; i < data.data.length; i++) {
    var el = data.data[i];

    if(search_param.location) {
      el.distance = haversine({
	latitude: search_param.location.latitude,
	longitude: search_param.location.longitude
      }, {
	latitude: parseFloat(el.LAT),
	longitude: parseFloat(el.LON),
      }, {unit: 'meter'});
    }
    else {
      el.distance = '';
    }
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
    if(filter[k] == null)
      continue;

    if(!filters[k](data[k], filter[k]))
      return false;
  }

  return true;
}

function follow_link(a) {
  var x = update_url(a.href);

  if(x == true)
    return false;
}

function update_url(url) {
  var location_base = location.href.slice(0, location.href.length - location.search.length);
  var query_string = url.slice(location_base.length);

  if(!query_string.match(/^\?/))
    return false;

  var query_data = {};
  var parts = query_string.slice(1).split("&");
  for(var i = 0; i < parts.length; i++) {
    var p = parts[i].split("=");
    query_data[decodeURIComponent(p[0])] = decodeURIComponent(p[1]);
  }


  form_search.set_data(query_data);
  form_search.set_orig_data(form_search.get_data());

  history.pushState(form_search.get_data(), null, query_string);

  update_location();

  return true;
}

function catch_links(dom) {
  var as = dom.getElementsByTagName("a");
  var location_base = location.href.slice(0, location.href.length - location.search.length);

  for(var i = 0; i < as.length; i++) {
    var a = as[i];

    if(a.href.slice(0, location_base.length) == location_base) {
      a.onclick = follow_link.bind(this, a);
    }
  }
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

  if(filtered_data.length == 1)
    t.show("html-transposed", update_table_show.bind(this, t));
  else if(filtered_data.length > 0)
    t.show("html", { limit: max_list }, update_table_show.bind(this, t));
  else
    update_table_show(t, "");
}

function update_table_show(t, table_content) {
  document.getElementById("table").innerHTML = table_content;

  catch_links(document.getElementById("table"));

  t.resize();
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
  history.replaceState(search_param, null, location.href);

  var ajax_param = {};

  if(data) {
    if(search_param.location) {
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
  }
  else
    reload = true;

  if(!search_param.location) {
    if(search_param.BAUMNUMMER)
      ajax_param.BAUMNUMMER = search_param.BAUMNUMMER;

    reload = true;
  }

  if(reload && (!reload_active)) {
    if(search_param.location) {
      ajax_param.latitude = search_param.location.latitude;
      ajax_param.longitude = search_param.location.longitude;
    }

    ajax("data.php", ajax_param, update_data.bind(this, search_param));
    document.body.className = "loading";
    reload_active = true;
  }

  var url = [];

  for(var k in search_param) {
    if(k == 'location') {
      url.push('location[latitude]=' + encodeURIComponent(search_param.location.latitude));
      url.push('location[longitude]=' + encodeURIComponent(search_param.location.longitude));
    }
    else {
      if(search_param[k])
	url.push(encodeURIComponent(k) + '=' + encodeURIComponent(search_param[k]));
    }
  }

  url = '.?' + url.join('&');

  history.replaceState(form_search.get_data(), null, url);

  if(!data)
    return;

  update_table();
}

window.onload = function() {
  form_search = form__;
  form_search.onchange = update_location;
  load_filters();
  window.addEventListener('popstate', function(event) {
    form_search.set_data(event.state);
    form_search.set_orig_data(form_search.get_data());
    update_location();
  });
  document.getElementById("form_search").onsubmit = function() {
    update_location();
    return false;
  }

  history.replaceState(form_search.get_data(), null, location.href);

  // TODO: data may not have been loaded into JS space
  //catch_links(document.getElementById("table"));
  call_hooks("init");

  orig_search_param = form_search.get_data();
  update_location();
}
