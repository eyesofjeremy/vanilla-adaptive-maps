function buildMap(amap) {
  var sw = document.body.clientWidth;

  if (sw > amap.bp) {
    if ( document.getElementById(amap.id + 'embed') == null ) {
      if ( amap.style !== '' ) {
        buildJS(amap);
      } else {
        buildEmbed(amap);
      }
    }
  } else {
    if ( document.getElementById(amap.id + 'static') == null ) {
      buildStatic(amap);
    }
  }
}

/* buildJS()
 * Create a map using Google's JavaScript API
 */
function buildJS(amap) {
  var el = document.createElement('div'),
    map = document.getElementById('map' + amap.id),
    geocoder = new google.maps.Geocoder(),
    gmap;

    el.classList.add('amap-container');
    el.setAttribute('id', amap.id + 'embed');

    // Geocode the address. If we have a match, then go ahead and make us a map.
    // https://developers.google.com/maps/documentation/javascript/geocoding
    geocoder.geocode( { 'address': amap.addr }, function( results, status ) {

      if (status == google.maps.GeocoderStatus.OK) {
      
        var mapOptions = {
          zoom : amap.zoom,
          center : results[0].geometry.location,
          styles : amap.style,
        };
        
        gmap = new google.maps.Map(el, mapOptions);
        var marker = new google.maps.Marker({
            map: gmap,
            position: results[0].geometry.location
        });
        
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }

  });

  map.insertBefore(el, map.firstChild);

}

/* buildEmbed()
 * The quick and easy way to build in a map in an iframe.
 */
function buildEmbed(amap) {
  var el = document.createElement('div'),
    map = document.getElementById('map' + amap.id);

  el.classList.add('amap-container');
  el.setAttribute('id', amap.id + 'embed');
  el.innerHTML = amap.embedMap;

  map.insertBefore(el, map.firstChild);
}

/* buildStatic()
 * Make a static map for smaller displays.
 */
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
