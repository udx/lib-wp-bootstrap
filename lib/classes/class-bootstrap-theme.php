<?php
/**
 * Bootstrap
 *
 * @namespace UsabilityDynamics
 *
 * This file can be used to bootstrap any of the UD plugins.
 */
namespace UsabilityDynamics\WP {

  if( !class_exists( 'UsabilityDynamics\WP\Bootstrap_Theme' ) ) {

    /**
     * Bootstrap the plugin in WordPress.
     *
     * @class Bootstrap
     * @author: peshkov@UD
     */
    class Bootstrap_Theme extends Bootstrap {
    
      public static $version = '1.0.0';
      
      /**
       * Constructor
       * Attention: MUST NOT BE CALLED DIRECTLY! USE get_instance() INSTEAD!
       *
       * @author peshkov@UD
       */
      protected function __construct( $args ) {
        parent::__construct( $args );
        load_plugin_textdomain( $this->domain, false, $this->root_path . 'static/languages/' );
        $this->init();
      }

      /**
       * Initialize application.
       * Redeclare the method in child class!
       *
       * @author peshkov@UD
       */
      public function init() {}
      
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
          $t = wp_get_theme();
          $args = array_merge( (array)$args, array(
            'name' => $t->get( 'Name' ),
            'version' => $t->get( 'Version' ),
            'template' => $t->get( 'Template' ),
            'domain' => $t->get( 'TextDomain' ),
            'is_child' => is_child_theme(),
            'root_path' => '',
          ) );
          $class::$instance = new $class( $args );
        }
        return $class::$instance;
      }
      
    }
  
  }
  
}