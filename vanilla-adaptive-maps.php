<?php

/*
Plugin Name: Vanilla Adaptive Maps
Plugin URI: https://github.com/eyesofjeremy/vanilla-adaptive-maps/
Description: Include an adaptive map based on a street address with a simple shortcode. No JavaScript library required.
Version: 1.0.1
Author: Jeremy Carlson
Author http://jeremycarlson.com/
License: GPL-2.0+
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

class vanilla_adaptive_maps {

  function set_breakpoint( $units = false ) {
    return '550' . $units;
  }

  /*
   * Adaptive Map
   * Generate the HTML from the shortcode [vamap addr="Your Street Address"]
   */
  function map_shortcode( $atts ) {

    // Encode address for mapping. This may need to get beefier.
    $map_address_encoded = str_replace(' ', '+', $atts['addr']);

    // Set up unique ID for the map, so you can have more than one on a page.
    $mid = uniqid();
    $map_id = "map$mid";
    $link_id = "maplink$mid";
    
    $output = '
<div class="adaptive-map" id="' . $map_id . '">
<a href="https://maps.apple.com/maps?q=' . $map_address_encoded . '" class="map-link" id="' . $link_id . '">' . __('View Map', 'wordpress') . '</a>
';

    // Script is distinct to this ID, and must be output inline.
    $output .= vanilla_adaptive_maps::print_map_script($map_address_encoded, $mid);
    
    // For now, we are inlining CSS as well. I realize this is not "the WordPress Way"
    // but this reduces the requests, and the CSS may eventually be something the user
    // can tweak. In particular, it would be nice to be able to adjust the breakpoints.
    $output .= vanilla_adaptive_maps::print_map_styles();

    $output .= '
</div><!-- adaptive map -->
';
    
    wp_enqueue_script( 'vanilla-adaptive-maps' );
    return $output;
  }


  /*
   * Print Map Script
   * Output the individual script for your particular map shortcode
   */
  function print_map_script($map_address_encoded, $map_id) {
  
    // get breakpoint for mobile vs desktop
    $breakpoint = vanilla_adaptive_maps::set_breakpoint();

    $script_output = "

<script>
  document.addEventListener( 'DOMContentLoaded', buildMap$map_id );
  window.addEventListener( 'resize', buildMap$map_id, false );

  function buildMap$map_id() {
  var address = '$map_address_encoded',
    staticSize = '640x320',
    amap$map_id = {
      id  : '$map_id',
      bp : $breakpoint,
      staticMap : 'http://maps.google.com/maps/api/staticmap?center=' + address + '&markers=' + address + '&size=' + staticSize + '&sensor=true',
      embedMap : '<iframe width=\"980\" height=\"650\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"https://maps.google.com/maps?q=' + address + '&output=embed\"></iframe>',
    };

    buildMap(amap$map_id);
  }
</script>

";

    return $script_output;
  }

  /*
   * Print Map Styles
   * Output the CSS for the map
   */
  function print_map_styles() {
    // get breakpoint for mobile vs desktop
    $breakpoint = vanilla_adaptive_maps::set_breakpoint('px');

    $css = "

<style type='text/css'>
.static-img {
  display: block;
}

.adaptive-map iframe {
  max-width: 100%;
}

/* From http://codepen.io/chriscoyier/full/kycDp */
.amap-container {
  width: 100%;
  margin: 0 auto;
  height: 0;
  padding-top: 38%;
  position: relative;
  display: none;
  /* Hide for small screens */
}
.amap-container iframe {
  width: 100%;
  height: 100%;
  /* had to specify height/width */
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
}

/* Medium Screens */
@media all and (min-width: $breakpoint) {
  .amap-container {
    display: block;
  }

  .static-img {
    display: none;
  }
}
</style>

";

    return $css;
  }

  // http://mikejolley.com/2013/12/02/sensible-script-enqueuing-shortcodes/
  function register_map_script() {
    wp_register_script( 'vanilla-adaptive-maps', plugins_url( '/js/vanilla_adaptive_maps.js' , __FILE__ ), array(), '1.0.0', true );
  }

} // end class

$adaptiveMap = new vanilla_adaptive_maps();
add_shortcode( 'vamap', array($adaptiveMap, 'map_shortcode') );
add_action( 'wp_enqueue_scripts', array($adaptiveMap, 'register_map_script') );
