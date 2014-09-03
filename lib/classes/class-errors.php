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
       * Action Links in Footer
       *
       * @used admin_notices
       * @public
       * @property $messages
       * @type array
       */
      private $action_links = array();
      
      /**
       *
       */
      public function __construct( $args ) {
        parent::__construct( $args );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'admin_head', array( $this, 'dismiss' ) );
      }
      
      /**
       * Add new message for admin notices
       *
       * @author peshkov@UD
       */
      public function add( $message, $type = 'error' ) {
        switch( $type ) {
          case 'error':
            $this->errors[] = $message;
            break;
          case 'message':
            $this->messages[] = $message;
            break;
        }
      }
      
      /**
       * Add footer link to specific ( errors|notices ) block
       *
       * @author peshkov@UD
       */
      public function add_action_link( $link, $type = 'error' ) {
        switch( $type ) {
          case 'error':
            $this->action_links[ 'errors' ][] = $link;
            break;
          case 'message':
            $this->action_links[ 'messages' ][] = $link;
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
       * Add dismissable admin notices.
       *
       * Appends a link to the admin nag messages. If clicked, the admin notice disappears and no longer is visible to users.
       *
       * @since 2.1.0
       */
      public function dismiss() {
        if ( isset( $_GET[ 'udan-dismiss-' . sanitize_key( $this->name ) ] ) ) {
          update_user_meta( get_current_user_id(), ( 'dismissed_notice_' . sanitize_key( $this->name ) ), time() );
        }
      }
      
      /**
       * Renders admin notes in case there are errors or notices on bootstrap init
       *
       * @author peshkov@UD
       */
      public function admin_notices() {
        global $wp_version;
        
        if( !empty( $this->errors ) || !empty( $this->messages ) ) {
          echo "<style>.ud-admin-notice a { text-decoration: underline !important; }</style>";
        }
        
        //** Errors Block */
        if( !empty( $this->errors ) && is_array( $this->errors ) ) {
          $errors = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $this->errors ) . '</li></ul>';
          $message = sprintf( __( '<p><b>%s</b> is not active due to following errors:</p> %s', $this->domain ), $this->name, $errors );
          if( !empty( $this->action_links[ 'errors' ] ) && is_array( $this->action_links[ 'errors' ] ) ) {
            $message .= '<p>' . implode( ' | ', $this->action_links[ 'errors' ] ) . '</p>';
          }
          echo '<div class="ud-admin-notice error fade" style="padding:11px;">' . $message . '</div>';
        }
        
        //** Determine if message has been dismissed ( for week! ) */
        $dismiss_timer = get_user_meta( get_current_user_id(), ( 'dismissed_notice_' . sanitize_key( $this->name ) ), false );
        if ( !$dismiss_timer || ( time() - (int)$dismiss_timer ) >= 604800 ) {
          //** Notices Block */
          if( !empty( $this->messages ) && is_array( $this->messages ) ) {
            $messages = '<ul style="list-style:disc inside;"><li>' . implode( '</li><li>', $this->messages ) . '</li></ul>';
            if( !empty( $errors ) ) {
              $message = sprintf( __( '<p><b>%s</b> has the following additional notices:</p> %s', $this->domain ), $this->name, $messages );
            } else {
              $message = sprintf( __( '<p><b>%s</b> is active, but has the following notices:</p> %s', $this->domain ), $this->name, $messages );
            }
            $this->action_links[ 'messages' ][] = '<a class="dismiss-notice" href="' . add_query_arg( 'udan-dismiss-' . sanitize_key( $this->name ), 'dismiss_admin_notices' ) . '" target="_parent">' . __( 'Dismiss this notice', $this->domain ) . '</a>';
            $message .= '<p>' . implode( ' | ', $this->action_links[ 'messages' ] ) . '</p>';
            echo '<div class="ud-admin-notice updated fade" style="padding:11px;">' . $message . '</div>';
          }
        }
        
      }
      
    }
  
  }
  
}