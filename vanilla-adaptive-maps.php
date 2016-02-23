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

    // Add a link to the options page. Make your life easier.
    if( current_user_can( 'manage_options') ) {
      $output .= '<a href="' . get_admin_url( null, 'options-general.php?page=vamap-setting-admin' ) . '" class="post-edit-link">Map Settings</a>';
    }

    $output .= '
</div><!-- adaptive map -->
';
    
    wp_enqueue_script( 'vanilla-adaptive-maps' );

    // If we need the JavaScript API, let's go get 'er.
    // The check returns all options.
    if( vanilla_adaptive_maps::use_jsapi() ) {
      wp_enqueue_script( 'vanilla-adaptive-maps-google-maps' );
    }
    return $output;
  }
  
  /*
   * Check if we need the JavaScript API
   */
  function use_jsapi() {
  
    $options = get_option('vamap_style');
    
    return ( isset( $options['map_api_key'] ) ) ? $options : FALSE;
  }

  /*
   * Print Map Script
   * Output the individual script for your particular map shortcode
   */
  function print_map_script($map_address_encoded, $map_id) {
    
    $options = get_option('vamap_style');

    $map_style = '[]'; // empty array for JS map style
    $static_style = ''; // empty string for static map style
    if( isset( $options['map_style'] ) && ! empty( $options['map_style'] ) ) {
      $static_style = vanilla_adaptive_maps::url_args_from_style_json( $options['map_style'] );
      
      if( isset( $options['map_api_key']) ) {
        $map_style = $options['map_style'];
      }
    }
    
    if( isset( $options['map_icon'] ) ) {
      $map_icon = wp_get_attachment_url( $options['map_icon'] );
      $static_icon = "icon:$map_icon|";
    } else {
      $map_icon = '';
      $static_icon = '';
    }
    

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
      addr : '$map_address_encoded',
      zoom : 14,
      icon : '$map_icon',
      style : $map_style,
      staticMap : 'http://maps.google.com/maps/api/staticmap?center=' + address + '&markers=$static_icon' + address + '&size=' + staticSize + '&style=' + '$static_style' + '&sensor=true',
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
  
  // http://stackoverflow.com/a/28173229/156645
  function url_args_from_style_json($mapStyleJson) {
    $params = [];
    
    $json_object = json_decode($mapStyleJson, true);

    foreach ($json_object as $style) {
      $styleString = '';

      if (isset($style['stylers']) && count($style['stylers']) > 0) {
        $styleString .= (isset($style['featureType']) ? ('feature:' . $style['featureType']) : 'feature:all') . '|';
        $styleString .= (isset($style['elementType']) ? ('element:' . $style['elementType']) : 'element:all') . '|';

        foreach ($style['stylers'] as $styler) {
          $propertyname = array_keys($styler)[0];
          $propertyval = str_replace('#', '0x', $styler[$propertyname]);
          $styleString .= $propertyname . ':' . $propertyval . '|';
        }
      }

      $styleString = substr($styleString, 0, strlen($styleString) - 1);

      $params[] = 'style=' . $styleString;
    }

    return implode("&", $params);
  }

  // http://mikejolley.com/2013/12/02/sensible-script-enqueuing-shortcodes/
  function register_map_script() {
    wp_register_script(
      'vanilla-adaptive-maps', // script hook
      plugins_url( '/js/vanilla_adaptive_maps.js' , __FILE__ ), // script location
      array(), // dependencies
      '1.0.0', // script version
      true // in footer?
    );
    
    // If we need the JavaScript API, let's go get 'er.
    // The check returns all options.
    $use_api = vanilla_adaptive_maps::use_jsapi();
    
    if( $use_api ) {
      $api_key = $use_api['map_api_key'];

      wp_register_script(
        'vanilla-adaptive-maps-google-maps', // script hook
        'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&signed_in=true&key=' . $api_key, // script location
        array(), // dependencies
        '1.0.0', // script version
        true // in footer?
      );
    }
  }

} // end class

$adaptiveMap = new vanilla_adaptive_maps();
add_shortcode( 'vamap', array($adaptiveMap, 'map_shortcode') );
add_action( 'wp_enqueue_scripts', array($adaptiveMap, 'register_map_script') );

// Add options for the plugin
include('vamaps-options.php');