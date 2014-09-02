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
       * Plugin ( Theme ) Name.
       *
       * @public
       * @property $name
       * @type string
       */
      public $name = false;
    
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
       * Schemas
       *
       * @public
       * @property schema
       * @var array
       */
      public $schemas = array();

      /**
       * Errors
       *
       * @public
       * @static
       * @property $errors
       * @type array
       */
      public $errors = array();
      
      /**
       * Singleton Instance Reference.
       *
       * @private
       * @static
       * @property $instance
       * @type \UsabilityDynamics\WPP\Bootstrap object
       */
      protected static $instance = null;

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
       *
       * @author peshkov@UD
       */
      private function __construct( $args ) {
        //** Define schemas here since we can set correct paths directly in property */
        $this->define_schemas();
        //** Determine if Composer autoloader is included and modules classes are up to date */
        $this->check_autoload_dependencies();
        //** Application initialization. */
        $this->init( $args );
        //** The last step. Print errors if they exist */
        if( !empty( $this->errors ) ) {
          add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        }
      }
      
      /**
       * Initialize application.
       * Redeclare the method in child class!
       *
       * @author peshkov@UD
       */
      public function init() {}

      /**
       * Defines property schemas ( $this->schemas )
       * The list of pathes to schemas files must be set if needed.
       * Redeclare the method in child class!
       * 
       */
      public function define_schemas() {}
      
      /**
       * Determine if instance already exists and Return Instance
       *
       * @author peshkov@UD
       */
      public static function get_instance( $args = array() ) {
        $class = get_called_class();
        if( null === $class::$instance ) {
          $class::$instance = new $class( $args );
        }
        return $class::$instance;
      }
      
      /**
       * @param string $key
       * @param mixed $value
       *
       * @author peshkov@UD
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
       * @author peshkov@UD
       * @return \UsabilityDynamics\type
       */
      public function get( $key = null, $default = null ) {
        if( !is_object( $this->settings ) || !is_callable( array( $this->settings, 'set' ) ) ) {
          return $value;
        }
        return $this->settings->get( $key, $default );
      }
      
      /**
       * Returns specific schema from file.
       *
       * @param string $file Path to file
       * @param array $l10n Locale data
       * @author peshkov@UD
       * @return array
       */
      public static function get_schema( $file = '', $l10n = array() ) {
        if( !empty( $file ) && file_exists( $file ) ) {
          return (array)\UsabilityDynamics\Utility::l10n_localize( json_decode( file_get_contents( $file ), true ), (array)$l10n );
        }
        return array();
      }
      
      /**
       * Renders admin notes in case there are errors on bootstrap init
       *
       * @author peshkov@UD
       */
      public function admin_notices() {
        if( !empty( $this->errors ) && is_array( $this->errors ) ) {
          $errors = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $this->errors ) . '</li></ul>';
          $message = sprintf( __( '<p><b>%s</b> is not active due to following errors:</p> %s' ), $this->name, $errors );
          echo '<div class="error fade" style="padding:11px;">' . $message . '</div>';
        }
      }
      
      /**
       * Maybe determines if Composer autoloader is included and modules classes are up to date
       *
       * @author peshkov@UD
       * @return boolean
       */
      private function check_autoload_dependencies() {
        //** Determine if schema is set */
        if( empty( $this->schemas[ 'dependencies' ] ) ) {
          return null;
        }
        $dependencies = $this->get_schema( $this->schemas[ 'dependencies' ] );
        if( !empty( $dependencies ) && is_array( $dependencies ) ) {
          foreach( $dependencies as $module => $classes ) {
            if( !empty( $classes ) && is_array( $classes ) ) {
              foreach( $classes as $class => $v ) {
                if( !class_exists( $class ) ) {
                  $this->errors[] = sprintf( __( 'Module <b>%s</b> is not installed or the version is old, class <b>%s</b> does not exist.' ), $module, $class );
                }
                if ( '*' != trim( $v ) && ( !property_exists( $class, 'version' ) || $class::$version < $v ) ) {
                  $this->errors[] = sprintf( __( 'Module <b>%s</b> should be updated to the latest version, class <b>%s</b> must have version <b>%s</b> or higher.' ), $module, $class, $v );
                }
              }
            }
          }
        }
      }
      
    }
  
  }
  
}