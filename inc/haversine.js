// from: https://github.com/niix/haversine/blob/master/haversine.js
var haversine = (function() {

  // convert to radians
  var toRad = function(num) {
    return num * Math.PI / 180
  }

  return function haversine(start, end, options) {
    var earth_radius = {
      'km': 6371,
      'meter': 6371000,
      'mile': 3960
    };
    options   = options || {}

    var R = earth_radius['km'];
    if(options.unit in earth_radius)
      R = earth_radius[options.unit];

    var dLat = toRad(end.latitude - start.latitude)
    var dLon = toRad(end.longitude - start.longitude)
    var lat1 = toRad(start.latitude)
    var lat2 = toRad(end.latitude)

    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2)
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))

    if (options.threshold) {
      return options.threshold > (R * c)
    } else {
      return R * c
    }
  }

})()
