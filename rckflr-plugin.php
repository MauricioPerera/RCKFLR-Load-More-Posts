<?php
/**
 * Plugin Name: RCKFLR Load More Posts
 * Plugin URI: https://rckflr.party/
 * Description: A simple plugin to load more posts using AJAX
 * Version: 1.0
 * Author: Mauricio Perera
 * Author URI: https://www.linkedin.com/in/mauricioperera/
 * Donate link: https://www.buymeacoffee.com/rckflr
 */
require_once 'vendor/autoload.php';
// Prevent direct file access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Include the class file
require_once plugin_dir_path( __FILE__ ) . 'includes/RCKFLR_LoadMorePosts.php';

// Instantiate the class
$rckflr_load_more_posts = new RCKFLR_LoadMorePosts();

// Register the Gutenberg block
add_action('carbon_fields_register_fields', function() use ($rckflr_load_more_posts) {
    $rckflr_load_more_posts->register_block();
});

// Initialize Carbon Fields
add_action('after_setup_theme', 'crb_load');
function crb_load() {
    require_once('vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
}
