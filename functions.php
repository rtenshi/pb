<?php

$template_dir = dirname(__FILE__);
$template_uri = get_template_directory_uri();

require_once($template_dir.'/_/backend.php');
//----------------------------------------------
new Configuration();


//Assets
function j77_load_scripts() {
    global $template_uri;
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'base-style', get_stylesheet_uri(), array(), '1.0' );
    wp_enqueue_style( 'template-style', $template_uri.'/assets/css/template.css', array(), '1.0' );

} add_action( 'wp_enqueue_scripts', 'j77_load_scripts' );


// TEST START

//TESTEND