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
		add_action( 'save_post', array( &$this, 'save_metabox' ) ); // Save the caption when the post is saved
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
				$screen, // Locations where the meta box should be shown
				'side' // Location where the meta box should be shown. This one is placed on the side.
			);
		}
	} // End metabox()

	// Featured image caption meta box callback
	function metabox_callback( $post ) {
		// Add a nonce field to verify data submissions came from our site
		wp_nonce_field( array( &$this, 'metabox' ), self::PREFIX . 'nonce' );
		
		// Retrieve the current caption as a string, if set
		$caption = get_post_meta( $post->ID, self::METAPREFIX, true );
		
		echo '<textarea style="width: 100%; max-width: 100%;" id="' . self::ID . '" name="' . self::ID . '">' . esc_attr( $caption ) . '</textarea>';
	} // End metabox_callback()
	
	// Save the meta box data
	function save_metabox( $post_id ) {
		/*
		Verify using the nonce that the data was submitted from our meta box on our site.
		If it wasn't, return the post ID and be on our way.
		*/
		// If no nonce was provided, return the post ID
		if ( ! isset( $_POST[self::PREFIX . 'nonce'] ) ) {
			return $post_id;
		}
		
		// Set a local variable for the nonce
		$nonce = $_POST[self::PREFIX . 'nonce'];
		
		// Verify that the nonce is valid
		if ( ! wp_verify_nonce( $nonce, array( &$this, 'metabox' ) ) ) {
			return $post_id;
		}
		
		// Make sure the user has valid permissions
		// If we're editing a page and the user isn't allowed to do that, return the post ID
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		}
		// If we're editing any other post type and the user isn't allowed to do that, return the post ID
		else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}
		
		// Now that we've validated nonce and permissions, let's save the caption data
		// Sanitize the caption
		$caption = sanitize_text_field( $_POST[self::ID] );
		
		// Update the caption meta field
		update_post_meta( $post_id, self::METAPREFIX, $caption );
	} // End save_metabox()
	
	// Retrieve the caption
	function get_caption( $id ) {
		// Get the caption data from the post meta as a string
		$caption = get_post_meta( $id, self::METAPREFIX, true );
		
		// If a caption value is present, return the caption
		if ( ! empty( $caption ) ) {
			return $caption;
		}
		else {
			return false;
		}
	}
} // End main plugin class

// Create plugin object in the global space
global $cc_featured_image_caption;
$cc_featured_image_caption = new \cconover\featured_image_caption\Caption;

/**
 * Theme function
 * Use this function to retrieve the caption for the featured image
 * This function must be used within The Loop
 * To display the results, you must use 'echo' with this function
 */
function cc_featured_image_caption() {
	// Retrieve the caption from post meta
	$caption = $cc_featured_image_caption->get_caption( $post->ID );
	
	// Return the result
	return $caption;
} // End cc_featured_image_caption()
?>