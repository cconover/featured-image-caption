<?php
/**
 * Plugin Name: Featured Image Caption
 * Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?ref=plugin-data
 * Description: Set a caption for the featured image of a post that can be displayed in your theme
 * Version: 0.1.0-alpha1
 * Author: Christiaan Conover
 * Author URI: https://christiaanconover.com?ref=wp-featured-image-caption-plugin-author-uri
 * License: GPLv2
 * @package cconover
 * @subpackage featured-image-caption
 **/

// Plugin namespace
namespace cconover\featured_image_caption;

/**
 * Main plugin class
 */
class Caption {
	// Plugin constants
	const ID = 'cc-featured-image-caption'; // Plugin ID
	const NAME = 'Featured Image Caption'; // Plugin name
	const VERSION = '0.1.0-alpha1'; // Plugin version
	const WPVER = '3.6'; // Minimum version of WordPress required for this plugin
	const PREFIX = 'cc_featured_image_caption_'; // Plugin database/method prefix
	const METAPREFIX = '_cc_featured_image_caption'; // Post meta database prefix
	
	// Class constructor
	function __construct() {
		// Hooks and filters
		add_action( 'add_meta_boxes', array( &$this, 'metabox') ); // Add meta box
	} // End __construct()
	
	// Create the meta box
	function metabox() {
		// Specify the screens where the meta box should be available
		$screens = array( 'post', 'page' );
		
		// Iterate through the specified screens to add the meta box
		foreach ( $screens as $screen ) {
			add_meta_box(
				self::ID, // HTML ID for the meta box
				self::NAME, // Title of the meta box displayed to the us
				array( &$this, 'metabox_callback'), // Callback function for the meta box to display it to the user
				$screen // Locations where the meta box should be shown
			);
		}
	} // End metabox()

	// Featured image caption meta box callback
	function metabox_callback( $post ) {
		// Add a nonce field to verify data submissions came from our site
		wp_nonce_field( array( &$this, 'metabox' ), self::PREFIX . 'nonce' );
		
		// Retrieve the current caption as a string, if set
		$caption = get_post_meta( $post->ID, self::METAPREFIX, true );
		
		echo '<input type="text" id="' . self::ID . '" name="' . self::ID . '" value="' . esc_attr( $caption ) . '" size=40>';
	} // End metabox_callback()
} // End main plugin class

// Create plugin object in the global space
global $cc_featured_image_caption;
$cc_featured_image_caption = new \cconover\featured_image_caption\Caption;
?>