<?php
/*
  Plugin Name: Rich Text Editor Field for Contact Form 7
  Plugin URI: http://www.wpexpertdeveloper.com/contact-form7-rich-text-editor/
  Description: Use WordPress Rich Text Editor as a form field
  Version: 1.1
  Author: Rakhitha Nimesh
  Author URI: http://www.innovativephp.com
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/* Intializing the plugin on plugins_loaded action */
add_action( 'plugins_loaded', 'cf7rt_plugin_init' );

function cf7rt_plugin_init(){
    if(!class_exists('WPCF7_ContactForm')){
        add_action( 'admin_notices', 'cf7rt_plugin_admin_notice' );
    }
    
}

function cf7rt_plugin_admin_notice() {
   $message = __('<strong>Rich Text Editor Field for Contact Form 7</strong> requires <strong>Contact Form 7</strong> plugin to function properly','cf7rt');
   echo '<div class="error"><p>'.$message.'</p></div>';
}

/* Main Class for Contact Form7 Rich Text Editor Field */
if( !class_exists( 'Contact_Form7_Rich_Text' ) ) {
    
    class Contact_Form7_Rich_Text{
    
        private static $instance;

        /* Create instances of plugin classes and initializing the features  */
        public static function instance() {
            
            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Contact_Form7_Rich_Text ) ) {
                self::$instance = new Contact_Form7_Rich_Text();
                self::$instance->setup_constants();

                //add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
                self::$instance->includes();

                add_action('wp_enqueue_scripts',array(self::$instance,'load_scripts'),9);
                 
                self::$instance->template_loader    = new CF7RT_Template_Loader();
                self::$instance->private_content    = new CF7RT_Rich_Text_Editor();
            }
            return self::$instance;
        }

        /* Setup constants for the plugin */
        private function setup_constants() {
            
            // Plugin version
            if ( ! defined( 'CF7RT_VERSION' ) ) {
                define( 'CF7RT_VERSION', '1.1' );
            }

            // Plugin Folder Path
            if ( ! defined( 'CF7RT_PLUGIN_DIR' ) ) {
                define( 'CF7RT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
            }

            // Plugin Folder URL
            if ( ! defined( 'CF7RT_PLUGIN_URL' ) ) {
                define( 'CF7RT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            }

            
        }

        /* Load scripts and styles for frontend */
        public function load_scripts(){
          
            wp_register_style('cf7rt-front-style', CF7RT_PLUGIN_URL . 'css/cf7rt-front.css');
            wp_enqueue_style('cf7rt-front-style');
         
        }
        
        /* Include class files */
        private function includes() {

            require_once CF7RT_PLUGIN_DIR . 'classes/class-cf7rt-template-loader.php';
            require_once CF7RT_PLUGIN_DIR . 'classes/class-cf7rt-rich-text-editor.php';
            
            if ( is_admin() ) {}
        }
    
    }
}

/* Intialize Contact_Form7_Rich_Text  instance */
function Contact_Form7_Rich_Text() {
    global $cf7rt;    
	$cf7rt = Contact_Form7_Rich_Text::instance();
}

Contact_Form7_Rich_Text();


















