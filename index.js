var orig_search_param;
var data;

function update_data(search_param, _data) {
  orig_search_param = search_param;

  if((!_data) || (!_data.data)) {
    alert("Error loading data!");
    return;
  }

  data = _data;

  var content_div = document.getElementById("content");
  var t = new table(table_def, data.data, {
    template_engine: "twig"
  });
  content_div.innerHTML = t.show("html", { limit: max_list });
}

function update_location() {
  if(!data) {
    search_param = form_search.get_data();
    ajax("data.php", search_param, update_data.bind(this, search_param));
  }
}

window.onload = function() {
  form_search = form__;
  form_search.onchange = update_location;

  orig_search_param = form_search.get_data();
}
