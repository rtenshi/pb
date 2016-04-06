<?php

if (class_exists('Singleton')):

    class Configuration extends Singleton{

        public $js      = array();
        public $css     = array();

        public function __construct()
        {
            add_action('init'               , array($this, 'remove_emojis'));
            add_action('after_setup_theme'  , array($this,'support'));
            if(WP_DEBUG && is_callable('shell_exec')) {
                add_action('after_setup_theme', array($this, 'generate_css'));
            };
            add_action('wp_loaded',array($this, 'load_footer'));
            add_action('admin_init',array($this, 'load_assets'));
        }

        public function support() {
            add_theme_support( 'post-thumbnails' );
            add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form' ) );
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'rsd_link');
        }
        public function remove_emojis() {
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

            // filter to remove TinyMCE emojis
            add_filter( 'tiny_mce_plugins', function($plugins) {
                if ( is_array( $plugins ) ) {
                    return array_diff( $plugins, array( 'wpemoji' ) );
                } else {
                    return array();
                }
            } );

            //remove_action('rest_api_init', 'wp_oembed_register_route');
            remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
            remove_action('wp_head', 'wp_oembed_add_discovery_links');
            remove_action('wp_head', 'wp_oembed_add_host_js');
            add_action('wp_footer', function() {
                wp_deregister_script( 'wp-embed' );
            } );

            show_admin_bar(false);
        }
        public static function add_dir($directory,$filet_type = "php")
        {
            $dir = dirname(__FILE__).$directory."/*.".$filet_type;
            foreach (glob($dir) as $filename)
            {
                include_once $filename;
            }
        }

        public static function generate_css() {
            global $template_dir;
            $path = realpath($_SERVER["DOCUMENT_ROOT"])."/static/less/style.less";
            $output = $template_dir.'/assets/css/template.css';
            $css = shell_exec('lessc -x '.$path.' '.$output);
        }
        /*public function add_css($file) {
            array_push($this->css, $file);
        }
        public function add_js($file , $attr = '') {
            array_push($this->js,array('file' =>$file, 'attr'=>$attr));
        }*/
        /*public function do_js() {
            foreach($this->js as $js) {
                echo '<script src="'.$js['file'].'" '.$js['attr'].'></script>';
            }
        }*/
        public function load_footer() {
            remove_action('wp_head', 'wp_enqueue_scripts', 1);
            remove_action('wp_head', 'wp_print_scripts');
            remove_action('wp_head', 'wp_print_head_scripts', 9);
            //add_action('wp_footer', 'wp_print_scripts', 5);
            //add_action('wp_footer', 'wp_print_head_scripts', 5);
            //add_action('wp_footer', 'wp_enqueue_scripts', 5);
        }
        public static function load_assets() {
            global $template_dir;
            $minified = new Minifier( array(
                'echo' => false,
                'gzip' => true,
                'closure' => true,
                'remove_comments' => true
            ) );
            //css
            $minified->merge( $template_dir.'/assets/prod/style.min.css', $template_dir.'/assets/css', 'css',array(),array($template_dir.'/style.css'));
            //js
            $minified->merge( $template_dir.'/assets/prod/j77.min.js', $template_dir.'/assets/js', 'js',array(),array());
        }

        public static function get_assets() {
            global $template_dir, $template_uri;
            $html = '';
            if(file_exists($template_dir.'/assets/prod/style.min.css')) {       //CSS
                echo '<link href="'.$template_uri.'/assets/prod/style.min.css.php" rel="stylesheet" />';
            }
            if(file_exists($template_dir.'/assets/prod/j77.min.js')) {       //CSS
                echo '<script src="'.$template_uri.'/assets/prod/j77.min.js.php" type="text/javascript"></script>';
            }
            return $html;
        }
    };

endif;

/*
 *
 *

if ( ! isset( $content_width ) )
	$content_width = 1180;

// Basic theme setup
if ( ! function_exists( 'j77_theme_setup' ) ) {

	function j77_theme_setup() {

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'comment-list', 'search-form', 'comment-form' ) );
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'wlwmanifest_link');
		remove_action('wp_head', 'rsd_link');
	}

} add_action( 'after_setup_theme', 'j77_theme_setup' );

// Load JS and CSS files
function j77_load_scripts() {

	wp_enqueue_script( 'jquery' );
	wp_enqueue_style( 'base-style', get_stylesheet_uri(), array(), '1.0' );

} add_action( 'wp_enqueue_scripts', 'lct_load_scripts' );

 */