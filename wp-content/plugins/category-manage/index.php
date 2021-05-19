<?php
/*
Plugin Name: Category Management System
Plugin URI: https://wordpress.org/
Description: Just another booking plugin. Simple but flexible.
Author: Dev Nitzee
Author URI: https://wordpress.org/
Text Domain: contact-form-7
Version: 1.1.1
*/

define( 'PLUGIN_URL', untrailingslashit( dirname((__FILE__))) );


if (is_admin()){

    function categoryManagement(){

        require "../wp-blog-header.php";

        add_menu_page("Category System","Category Management",'manage_options',"category_management","");
        add_submenu_page("category_management","All Categories","All Categories",'manage_options',"category_management","category_management");
        add_submenu_page("category_management","Add Category","Add Category",'manage_options',"category_management","category_management");

    }
    add_action('admin_menu','categoryManagement');
}

function category_management(){
	global $wpdb;
    include PLUGIN_URL."/inc/category-management.php";
}
