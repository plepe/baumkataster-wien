var orig_search_param;
var data;

function update_data(search_param, result) {
  orig_search_param = search_param;
  data = result.responseJSON;
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
