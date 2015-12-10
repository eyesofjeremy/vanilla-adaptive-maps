<?php
class VAMSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Vanilla Adaptive Maps Settings', 
            'manage_options', 
            'vamap-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'vamap_style' );
        ?>
        <div class="wrap">
            <h2>Vanilla Adaptive Maps Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'vamap_style_option_group' );   
                do_settings_sections( 'vamap-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'vamap_style_option_group', // Option group
            'vamap_style', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'vamap_style_settings', // ID
            'Style Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'vamap-setting-admin' // Page
        );  

        add_settings_field(
            'map_api_key', // ID
            '<a href="https://developers.google.com/maps/documentation/javascript/">Google API Key</a>', // Title 
            array( $this, 'map_api_callback' ), // Callback
            'vamap-setting-admin', // Page
            'vamap_style_settings' // Section           
        );      

        add_settings_field(
            'map_style', // ID
            'Map Style', // Title 
            array( $this, 'map_style_callback' ), // Callback
            'vamap-setting-admin', // Page
            'vamap_style_settings' // Section           
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['map_api_key'] ) ) {
          $new_input['map_api_key'] = sanitize_text_field( $input['map_api_key'] );
        }

        if( isset( $input['map_style'] ) ) {
          $new_input['map_style'] = sanitize_text_field( $input['map_style'] );
        }

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '
<p>If you would like to style your map, you will need call the Google map via JavaScript 
rather than embedding it.</p>
<p>This requires an <a href="https://developers.google.com/maps/documentation/javascript/">API key</a>, and a valid JSON string of map options.</p>
<p><a href="https://snazzymaps.com">Snazzy Maps</a> offers a nice interface for figgerin’ out your map style.</p>
';
    }

    /** 
     * Get the Google Maps API key.
     * TODO: validate Google Maps API key before proceeding further.
     */
    public function map_api_callback()
    {
        printf(
            '<input id="map_api_key" type="text" name="vamap_style[map_api_key]" value="%s">',
            isset( $this->options['map_api_key'] ) ? esc_attr( $this->options['map_api_key'] ) : ''
        );
    }

    /** 
     * Print style options for Vanilla Maps. Should be JSON.
     */
    public function map_style_callback()
    {
      $map_style = $this->options['map_style'];
      
      if( isset( $map_style ) && ! $this->is_json( $map_style) ) {
        print("<div class=\"error\"> <p>Looks like the Map Style is not valid JSON.</p></div>");
      }
      
      printf(
          '<textarea id="map_style" name="vamap_style[map_style]">%s</textarea>',
          isset( $map_style ) ? esc_attr( $map_style ) : ''
      );
    }
    
    /**
     * Ultra basic check for valid JSON string.
     * http://stackoverflow.com/a/6041773
     */
    function is_json($string) {
      json_decode($string);
      return (json_last_error() == JSON_ERROR_NONE);
    }
}

if( is_admin() ) {
  $my_settings_page = new VAMSettingsPage();
}