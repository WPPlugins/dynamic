<?php
/*
Plugin Name: Dynamic
Plugin URI: http://dynamicplugin.com/dynamic/
Description: Dynamic plugin to create your own plugins
Version: 1.0
Author: Genie Soft
License: http://dynamicplugin.com/terms-and-conditions-and-end-user-license-agreement/
*/
include_once dirname(__FILE__) .'/Logic/DynamicHelper.php';

//vars
define('DYNAMIC_VERSION', '1.0');
define( 'DYNAMIC_DB_VERSION', "Version1" );
//Development mode:
define( 'DYNAMIC_DEVELOPMENT_MODE', true );

//install
register_activation_hook(__FILE__,'Dynamics_install');
function Dynamics_install($networkwide) {

    $DynamicHelper = new DynamicHelper();
    $DynamicHelper->Install($networkwide);

}

//on new blog
add_action( 'wpmu_new_blog', 'Dynamic_new_blog', 10, 6);

function Dynamic_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;

    if (is_plugin_active_for_network('dynamic/dynamic.php')) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
        $DynamicHelper = new DynamicHelper();
        $DynamicHelper->ActivateThisBlog();
        switch_to_blog($old_blog);
    }
}

//uninstall
register_deactivation_hook( __FILE__, 'Dynamics_remove' );
function Dynamics_remove() {

    $DynamicHelper = new DynamicHelper();
    $DynamicHelper->Remove();

}

//load scripts and styles
add_action('wp_enqueue_scripts', 'Dynamic_scripts_method');
add_action('admin_enqueue_scripts', 'Dynamic_scripts_method');

function Dynamic_scripts_method() {

    $dir =  plugin_dir_url(__FILE__);
    $DynamicHelper = new DynamicHelper();
    $DynamicHelper->RegisterScripts($dir);

}

//get scripts calls
add_action('wp_ajax_nopriv_DynamicRequest', 'prefix_ajax_DynamicRequest');
add_action('wp_ajax_DynamicRequest', 'prefix_ajax_DynamicRequest');

function prefix_ajax_DynamicRequest()
{
    $DynamicHelper = new DynamicHelper();
    $DynamicHelper->ManageRequest($_REQUEST);
}

//load menus
if ( is_admin() ){
    add_action('admin_menu', 'Dynamic_admin_menu');
    function Dynamic_admin_menu()
    {
        $dir =  plugin_dir_url(__FILE__);
        $DynamicHelper = new DynamicHelper();
        $DynamicHelper->AddAdminMenues();

        //TODO: add reading from database
    }
}


//content filter
add_filter( 'the_content', 'Dynamic_content_filter', 20 );

function Dynamic_content_filter( $content ) {
    $DynamicHelper = new DynamicHelper();
    return $DynamicHelper->ContentFilter($content);

}

//load widgets



//translate short tags
function Dynamic_short_func( $atts ) {
    $DynamicHelper = new DynamicHelper();
    return $DynamicHelper->GetShortcode($atts);
}

add_shortcode( 'dynamic', 'Dynamic_short_func' );


