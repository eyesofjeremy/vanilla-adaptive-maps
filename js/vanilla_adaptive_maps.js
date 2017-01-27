function buildMap(amap) {

  // Check if the window is wider than our setting for static maps.
  // If it is, we'll be serving up a dynamic map.
  if ( document.body.clientWidth > amap.bp ) {

    // If we have style settings saved, then we will build the map
    // natively using Google's JavaScript library so we can style it.
    if ( amap.hasOwnProperty('style') && window.hasOwnProperty('google') ) {

      // Check to see we haven't built a map already
      if ( document.getElementById(amap.id + 'js') == null ) {
        buildJS(amap);
      }
    } else {
      if ( document.getElementById(amap.id + 'embed') == null ) {
        buildEmbed(amap);
      }
    }
  } else {
    if ( document.getElementById(amap.id + 'static') == null ) {
      buildStatic(amap);
    }
  }
}

/* geocoding from google
https://developers.google.com/maps/documentation/javascript/geocoding
 var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var mapOptions = {
      zoom: 8,
      center: latlng
    }
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
  }

 function codeAddress() {
    var address = document.getElementById("address").value;
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
*/

function buildJS(amap) {
  var el = document.createElement('div'),
    map = document.getElementById('map' + amap.id),
    geocoder = new google.maps.Geocoder(),
    gmap;

    el.classList.add('amap-container');
    el.setAttribute('id', amap.id + 'js');

    geocoder.geocode( { 'address': amap.addr }, function( results, status ) {

      if (status == google.maps.GeocoderStatus.OK) {
      
        var mapOptions = {
          zoom : amap.zoom,
          center : results[0].geometry.location,
          styles : amap.style,
          backgroundColor: "none"
        };
        
        console.log ( amap.style );

        gmap = new google.maps.Map(el, mapOptions);
        var marker = new google.maps.Marker({
            map: gmap,
            position: results[0].geometry.location,
            icon: amap.icon
        });
        
        console.log ( gmap );

      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }

  });

  map.insertBefore(el, map.firstChild);

}

function buildEmbed(amap) {
  var el = document.createElement('div'),
    map = document.getElementById('map' + amap.id);

  el.classList.add('amap-container');
  el.setAttribute('id', amap.id + 'embed');
  el.innerHTML = amap.embedMap;

  map.insertBefore(el, map.firstChild);
}

function buildStatic(amap) {
  var linkid = 'maplink' + amap.id,
    mapLink = document.getElementById(linkid).getAttribute('href'),
    img = '<img class="static-img" id="' + amap.id + 'static" src="' + amap.staticMap + '">',
    el = document.createElement('a'),
    map = document.getElementById('map' + amap.id);
    
  el.setAttribute('href', mapLink);
  el.innerHTML = img;
  
  map.insertBefore(el, map.firstChild);
}
