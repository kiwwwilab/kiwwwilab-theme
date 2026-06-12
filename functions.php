<?php
// Prevent to access the file from outside of WordPress
if(!defined('ABSPATH')) {
	exit;
}

// Theme Update Checker
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$puc_file = __DIR__ . '/lib/theme-update-checker/theme-update-checker.php';

if ( file_exists( $puc_file ) ) {
    
    require_once $puc_file;
    
    $myUpdateChecker = PucFactory::buildUpdateChecker(
        'https://github.com/kiwwwilab/kiwwwilab-theme/',
        __FILE__,
        'kiwwwilab-theme'
    );

    $myUpdateChecker->setBranch('main');

} else {
    error_log('Error de Theme Update Checker: No es troba el fitxer a ' . $puc_file);
}

add_action( 'wp_enqueue_scripts', 'kiwwwilab_enqueue', 99 );

function kiwwwilab_enqueue() {

	wp_enqueue_style( 'kiwwwilab-styles', get_stylesheet_uri() );
    wp_enqueue_script( 'kiwwwilab-js', get_parent_theme_file_uri( 'assets/js/scripts.js' ), array('jquery'), wp_get_theme()->get( 'Version' ), true );
}

add_action( 'enqueue_block_editor_assets', 'kiwwwilab_block_editor_scripts' );

function kiwwwilab_block_editor_scripts() {
	wp_enqueue_style( 'kiwwwilab-styles', get_stylesheet_uri() );
}

add_filter( 'should_load_remote_block_patterns', '__return_false' );

add_action('init', function() {
	remove_theme_support('core-block-patterns');
});

add_filter( 'body_class', function( $classes ) {

    $extra_classes = array();

    if(has_post_thumbnail()) {
        $extra_classes[] = 'has-post-thumbnail';
    }

	return array_merge( $classes, $extra_classes );
} );


function kiwwwilab_enable_gutenberg_editor_for_post_types( $current_status, $post_type ) {
    $enabled_post_types = array( 'post', 'page', 'product' );

    if ( in_array( $post_type, $enabled_post_types ) ) {
        return true; // Enable Gutenberg editor
    }

    return $current_status;
}
add_filter( 'use_block_editor_for_post_type', 'kiwwwilab_enable_gutenberg_editor_for_post_types', 10, 2 );

add_filter( 'woocommerce_taxonomy_args_product_cat', 'enable_taxonomy_rest' );
add_filter( 'woocommerce_taxonomy_args_product_tag', 'enable_taxonomy_rest' );
function enable_taxonomy_rest( $args ) {
    $args['show_in_rest'] = true;
    return $args;
}
