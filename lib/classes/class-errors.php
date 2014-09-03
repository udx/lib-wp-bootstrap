<?php
/**
 * Admin Notices Handler
 *
 * @namespace UsabilityDynamics
 *
 * This file can be used to bootstrap any of the UD plugins, it essentially requires that you have
 * a core file which will be called after 'plugins_loaded'. In addition, if the core class has
 * 'activate' and 'deactivate' functions, then those will be called automatically by this class.
 */
namespace UsabilityDynamics\WP {

  if( !class_exists( 'UsabilityDynamics\WP\Errors' ) ) {

    /**
     * 
     * @author: peshkov@UD
     */
    class Errors extends Scaffold {
    
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
       * Errors
       *
       * @used admin_notices
       * @public
       * @property $errors
       * @type array
       */
      private $errors = array();
      
      /**
       * Messages
       *
       * @used admin_notices
       * @public
       * @property $messages
       * @type array
       */
      private $messages = array();
      
      /**
       *
       */
      public function __construct( $args ) {
        parent::__construct( $args );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
      }
      
      /**
       * 
       *
       * @author peshkov@UD
       */
      public function set( $message, $type = 'error' ) {
        switch( $type ) {
          case 'error':
            $this->errors[] = $message;
            break;
          case 'messages':
            $this->messages[] = $message;
            break;
        }
      }
      
      /**
       * Determine if errors exist
       *
       * @author peshkov@UD
       */
      public function has_errors() {
        return !empty( $this->errors ) ? true : false;
      }

      /**
       * Renders admin notes in case there are errors on bootstrap init
       *
       * @author peshkov@UD
       */
      public function admin_notices() {
      
        //echo "<pre>"; print_r( $this->errors ); echo "</pre>";
        //echo "<pre>"; print_r( $this->messages ); echo "</pre>"; die();
      
        //*
        if( !empty( $this->errors ) && is_array( $this->errors ) ) {
          $errors = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $this->errors ) . '</li></ul>';
          $message = sprintf( __( '<p><b>%s</b> is not active due to following errors:</p> %s' ), $this->name, $errors );
          echo '<div class="error fade" style="padding:11px;">' . $message . '</div>';
        }
        if( !empty( $this->messages ) && is_array( $this->messages ) ) {
          $errors = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $this->messages ) . '</li></ul>';
          $message = sprintf( __( '<p><b>%s</b>.</p> %s' ), $this->name, $errors );
          echo '<div class="error fade" style="padding:11px;">' . $message . '</div>';
        }
        //*/
        
      }
      
    }
  
  }
  
}