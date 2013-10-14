<?php
/**
 * WP_Boostrap.
 *
 * @namespace UsabilityDynamics
 */
namespace UsabilityDynamics {

  /**
   * Bootstrap the plugin in WordPress.
   *
   * @class Bootstrap
   * @author: potanin@UD
   */
  class WP_Bootstrap {

    /**
     * Singleton Instance Reference.
     *
     * @public
     * @static
     * @property $instance
     * @type {Object}
     */
    public static $instance = false;

    /**
     * Core object reference for the plugin
     *
     * @public
     * @static
     * @property $core
     * @type {Object}
     */
    public $core = false;

    /**
     * Core constructor.
     *
     * @for WP_Provision_Core
     * @author potanin@UD
     * @since 0.1.0
     */
    public function __construct() {

      if( self::$instance ) {
        return self::$instance;
      }

      /** Load vendor dependencies */
      include_once( dirname( __DIR__ ) . '/vendor/autoload.php' );

      /** Register activation hook. */
      register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

      /** Register activation hook. */
      register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

      /** Add the action */
      add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ));

      /** Setup the instance */
      self::$instance =& $this;

    }

    /**
     * Initiate the plugin.
     *
     * @private
     * @for Bootstrap
     * @method plugins_loaded
     */
    public function plugins_loaded() {
      /** Create the core object */
      $this->core = new Core();
    }

    /**
     * Activation actions.
     *
     * @public
     * @author potanin@UD
     * @for Bootstrap
     * @method activation
     */
    public static function activation() {}

    /**
     * Deactivation actions.
     *
     * @private
     * @author potanin@UD
     * @for Bootstrap
     * @method deactivation
     */
    public static function deactivation() {}

    /**
     * Get the WP-Provision Singleton
     *
     * Concept based on the CodeIgniter get_instance() concept.
     *
     * @example
     *
     *      var settings = WP_Provision::get_instance()->Settings;
     *      var api = WP_Provision::$instance()->API;
     *
     * @static
     * @return object
     *
     * @author potanin@UD
     * @method get_instance
     * @for WP_Provision
     */
    public static function &get_instance() {
      return self::$instance;
    }
  }
}