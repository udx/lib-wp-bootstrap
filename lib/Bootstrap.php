<?php
/**
 * Bootstrap
 *
 * @namespace UsabilityDynamics
 *
 * This file can be used to bootstrap any of the UD plugins, it essentially requires that you have
 * a core file which will be called after 'plugins_loaded'. In addition, if the core class has
 * 'activate' and 'deactivate' functions, then those will be called automatically by this class.
 */
namespace UsabilityDynamics\WP {

  if( !class_exists( 'UsabilityDynamics\WP\Bootstrap' ) ) {

    /**
     * Bootstrap the plugin in WordPress.
     *
     * @class Bootstrap
     * @author: potanin@UD
     */
    class Bootstrap {

      /**
       * Core version.
       *
       * @static
       * @property $version
       * @type {Object}
       */
      public $version = false;

      /**
       * Textdomain String
       *
       * @public
       * @property domain
       * @var string
       */
      public $domain = false;

      /**
       * Singleton Instance Reference.
       *
       * @private
       * @static
       * @property $instance
       * @type \UsabilityDynamics\WPP\Bootstrap object
       */
      private static $instance = null;

      /**
       * Settings
       *
       * @private
       * @static
       * @property $settings
       * @type \UsabilityDynamics\Settings object
       */
      private $settings = null;

      /**
       * Constructor
       */
      private function __construct( $args ) {
        $this->init( $args );
      }
      
      /**
       * Initialize application.
       * Redeclare the method in child class!
       *
       */
      public function init() {}

      /**
       * Determine if instance already exists and Return Instance
       *
       */
      public static function get_instance( $args = array() ) {
        if( null === self::$instance ) {
          self::$instance = new self( $args );
        }
        return self::$instance;
      }
      
      /**
       * @param string $key
       * @param mixed $value
       *
       * @return \UsabilityDynamics\Settings
       */
      public function set( $key = null, $value = null ) {
        if( !is_object( $this->settings ) || !is_callable( array( $this->settings, 'set' ) ) ) {
          return $value;
        }
        return $this->settings->set( $key, $value );
      }

      /**
       * @param string $key
       * @param mixed $default
       *
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        if( !is_object( $this->settings ) || !is_callable( array( $this->settings, 'set' ) ) ) {
          return $value;
        }
        return $this->settings->get( $key, $default );
      }
      
    }
  
  }
  
}