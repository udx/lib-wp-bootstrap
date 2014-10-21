<?php
/**
 * Bootstrap
 *
 * @namespace UsabilityDynamics
 *
 * This file can be used to bootstrap any of the UD plugins.
 */
namespace UsabilityDynamics\WP {

  if( !class_exists( 'UsabilityDynamics\WP\Bootstrap_Plugin' ) ) {

    /**
     * Bootstrap the plugin in WordPress.
     *
     * @class Bootstrap
     * @author: peshkov@UD
     */
    class Bootstrap_Plugin extends Bootstrap {
    
      public static $version = '1.0.0';
      
      /**
       * Constructor
       * Attention: MUST NOT BE CALLED DIRECTLY! USE get_instance() INSTEAD!
       *
       * @author peshkov@UD
       */
      protected function __construct( $args ) {
        parent::__construct( $args );
        
        //** Determine if plugin/theme requires or recommends another plugin(s) */
        $this->plugins_dependencies();
        //** Maybe define license client */
        $this->define_license_client();
        
        //** Load text domain */
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 1 );
        //** Add additional conditions on 'plugins_loaded' action before we start plugin initialization. */
        add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 10 );
        //** Initialize plugin here. All plugin actions must be added on this step */
        add_action( 'plugins_loaded', array( $this, 'init' ), 100 );
        
        $this->boot();
      }

	    /**
	     * Returns absolute DIR or URL path
	     *
	     * @since 1.0.2
	     *
	     * @param $short_path
	     * @param string $type
	     *
	     * @return bool|string
	     */
      public function path( $short_path, $type = 'url' ) {
        switch( $type ) {
          case 'url':
            return $this->root_url . ltrim( $short_path, '/\\' );
            break;
          case 'dir':
            return $this->root_path . ltrim( $short_path, '/\\' );
            break;
        }
        return false;
      }
      
      /**
       * Initialize application.
       * Redeclare the method in child class!
       *
       * @author peshkov@UD
       */
      public function init() {}
      
      /**
       * Called in the end of constructor.
       * Redeclare the method in child class!
       *
       * @author peshkov@UD
       */
      public function boot() {}
      
      /**
       * Load Text Domain
       *
       * @author peshkov@UD
       */
      public function load_textdomain() {
        load_plugin_textdomain( $this->domain, false, basename( $this->plugin_file, '.php' ) . '/static/languages/' );
      }
      
      /**
       * Go through additional conditions on 'plugins_loaded' action before we start plugin initialization
       *
       * @author peshkov@UD
       */
      public function plugins_loaded() {
        //** Determine if we have TGMA Plugin Activation initialized. */
        $is_tgma = $this->is_tgma;
        if( $is_tgma ) {
          $tgma = TGM_Plugin_Activation::get_instance();
          //** Maybe get TGMPA notices. */
          $notices = $tgma->notices( get_class( $this ) );
          if( !empty( $notices[ 'messages' ] ) && is_array( $notices[ 'messages' ] ) ) {
            $error_links = false;
            $message_links = false;
            foreach( $notices[ 'messages' ] as $m ) {
              if( $m[ 'type' ] == 'error' ) $error_links = true;
              elseif( $m[ 'type' ] == 'message' ) $message_links = true;
              $this->errors->add( $m[ 'value' ], $m[ 'type' ] );
            }
            //** Maybe add footer action links to errors and|or notices block. */
            if( !empty( $notices[ 'links' ] ) && is_array( $notices[ 'links' ] ) ) {
              foreach( $notices[ 'links' ] as $type => $links ) {
                foreach( $links as $link ) {
                  $this->errors->add_action_link( $link, $type );
                }
              }
            }
          }
        }
        //** Maybe define license manager */
        $this->define_license_manager();
      }
      
      /**
       * Determine if instance already exists and Return Instance
       *
       * Attention: The method MUST be called from plugin core file at first to set correct path to plugin!
       *
       * @author peshkov@UD
       */
      public static function get_instance( $args = array() ) {
        $class = get_called_class();
        //** We must be sure that final class contains static property $instance to prevent issues. */
        if( !property_exists( $class, 'instance' ) ) {
          exit( "{$class} must have property \$instance" );
        }
        $prop = new \ReflectionProperty( $class, 'instance' );
        if( !$prop->isStatic() ) {
          exit( "Property \$instance must be <b>static</b> for {$class}" );
        }
        if( null === $class::$instance ) {    
          $dbt = debug_backtrace();
          if( !empty( $dbt[0]['file'] ) && file_exists( $dbt[0]['file'] ) ) {
            $pd = get_file_data( $dbt[0]['file'], array(
              'name' => 'Plugin Name',
              'version' => 'Version',
              'domain' => 'Text Domain',
            ), 'plugin' );
            $args = array_merge( (array)$pd, (array)$args, array(
              'root_path' => dirname( $dbt[0]['file'] ),
              'root_url' => plugin_dir_url( $dbt[0]['file'] ),
            ) );
            $class::$instance = new $class( $args );
            //** Register activation hook */
            register_activation_hook( $dbt[0]['file'], array( $class::$instance, 'activate' ) );
            //** Register activation hook */
            register_deactivation_hook( $dbt[0]['file'], array( $class::$instance, 'deactivate' ) );
          } else {
            $class::$instance = new $class( $args );
          }
        }
        return $class::$instance;
      }
      
      /**
       * Plugin Activation
       * Redeclare the method in child class!
       */
      public function activate() {}
      
      /**
       * Plugin Deactivation
       * Redeclare the method in child class!
       */
      public function deactivate() {}
      
      /**
       * Defines License Client if 'licenses' schema is set
       *
       * @author peshkov@UD
       */
      protected function define_license_client() {
        //** Break if we already have errors to prevent fatal ones. */
        if( $this->has_errors() ) {
          return false;
        }
        //** Be sure we have licenses scheme to continue */
        $schema = $this->get_schema( 'extra.schemas.licenses.client' );
        if( !$schema ) {
          return false;
        }
        //** Licenses Manager */
        if( !class_exists( '\UsabilityDynamics\UD_API\Bootstrap' ) ) {
          $this->errors->add( __( 'Class \UsabilityDynamics\UD_API\Bootstrap does not exist. Be sure all required plugins and (or) composer modules installed and activated.', $this->domain ) );
          return false;
        }
        $args = $this->args;
        $args = array_merge( $args, $schema, array( 
          'errors_callback' => array( $this->errors, 'add' ),
        ) );
        if( empty( $args[ 'screen' ] ) ) {
          $this->errors->add( __( 'Licenses client can not be activated due to invalid \'licenses\' schema.', $this->domain ) );
        }
        $this->client = new \UsabilityDynamics\UD_API\Bootstrap( $args );
      }
      
      /**
       * Defines License Manager if 'license' schema is set
       *
       * @author peshkov@UD
       */
      protected function define_license_manager() {
        //** Break if we already have errors to prevent fatal ones. */
        if( $this->has_errors() ) {
          return false;
        }
        //** Be sure we have license scheme to continue */
        $schema = $this->get_schema( 'extra.schemas.licenses.product' );
        if( !$schema ) {
          return false;
        }
        if( empty( $schema[ 'product_id' ] ) || empty( $schema[ 'referrer' ] ) ) {
          $this->errors->add( __( 'Product requires license, but product ID and (or) referrer is undefined. Please, be sure, that license schema has all required data.', $this->domain ) );
        }
        $schema = array_merge( (array)$schema, array( 
          'plugin_name' => $this->name,
          'plugin_file' => $this->plugin_file,
          'errors_callback' => array( $this->errors, 'add' )
        ) );
        //** Licenses Manager */
        if( !class_exists( '\UsabilityDynamics\UD_API\Manager' ) ) {
          $this->errors->add( __( 'Class \UsabilityDynamics\UD_API\Manager does not exist. Be sure all required plugins installed and activated.', $this->domain ) );
          return false;
        }
        $this->license_manager = new \UsabilityDynamics\UD_API\Manager( $schema );
        return true;
      }
      
      /**
       * Determine if plugin/theme requires or recommends another plugin(s)
       *
       * @author peshkov@UD
       */
      private function plugins_dependencies() {
        $plugins = $this->get_schema( 'extra.schemas.dependencies.plugins' );
        if( !empty( $plugins ) && is_array( $plugins ) ) {
          $tgma = TGM_Plugin_Activation::get_instance();
          foreach( $plugins as $plugin ) {
            $plugin[ '_referrer' ] = get_class( $this );
            $plugin[ '_referrer_name' ] = $this->name;
            $tgma->register( $plugin );
          }
          $this->is_tgma = true;
        }
      }
      
    }
  
  }
  
}