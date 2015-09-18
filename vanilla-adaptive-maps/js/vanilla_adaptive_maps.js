function buildMap(amap) {
  var sw = document.body.clientWidth;

  if (sw > amap.bp) {
    if ( document.getElementById(amap.id + 'embed') == null ) {
      buildEmbed(amap);
    }
  } else {
    if ( document.getElementById(amap.id + 'static') == null ) {
      buildStatic(amap);
    }
  }
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
