<?php
/**
 * Plugin Name: Featured Image Caption
 * Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?ref=plugin-data
 * Description: Set a caption for the featured image of a post that can be displayed in your theme
 * Version: 0.4.1
 * Author: Christiaan Conover
 * Author URI: https://christiaanconover.com?ref=wp-featured-image-caption-plugin-author-uri
 * License: GPLv2
 * @package cconover
 * @subpackage featured-image-caption
 **/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You cannot access this resouce directly.' );
}

// Create plugin object
require_once( plugin_dir_path( __FILE__ ) . 'class-featured-image-caption.php' );
$cc_featured_image_caption = new \cconover\FeaturedImageCaption;


/**
 * Theme function
 * Use this function to retrieve the caption for the featured image.
 * This function must be used within The Loop.
 *
 * @param 	boolean $echo 			Whether to print the results true or return them false (default: true)
 * @param 	boolean $attribution	Whether to include attribution data, if available. (default: true)
 * @return 	mixed
 */
function cc_featured_image_caption( $echo = true, $attribution = true ) {
	// Access global featured image caption object and post object
	global $cc_featured_image_caption, $post;

	// Set local variable for plugin ID
	$pluginid = \cconover\FeaturedImageCaption::ID;

	// Retrieve the caption from post meta
	$captiondata = $cc_featured_image_caption->get_caption( $post->ID );

	// If no caption is set, return false
	if ( false == $captiondata ) {
		return false;
	}

	// If $echo is true, print the caption
	if ( $echo ) {
		// If caption text is set, place caption data inside an HTML <span> to allow for CSS formatting
		if ( ! empty( $captiondata['caption_text'] ) ) {
			$caption = '<span class="' . $pluginid . '">' . $captiondata['caption_text'] . '</span>';
		}
		else {
			$caption = null;
		}

		// If source attribution data is availble and desired, display it
		if ( ! empty( $captiondata['source_text'] ) && false != $attribution ) {
			// If source attribution has a URL, format the source as a link
			if ( ! empty( $captiondata['source_url'] ) ) {
				// If the link is set to open in a new window, add the target attribute to the a tag
				if ( ! empty( $captiondata['new_window'] ) ) {
					$caption .= ' <span class="' . $pluginid . '-source"><a href="' . $captiondata['source_url'] . '" target="_blank">' . $captiondata['source_text'] . '</a></span>';
				}
				else {
					$caption .= ' <span class="' . $pluginid . '-source"><a href="' . $captiondata['source_url'] . '">' . $captiondata['source_text'] . '</a></span>';
				}
			}
			// If no URL is set, just display the text
			else {
				$caption .= ' <span class="' . $pluginid . '-source">' . $captiondata['source_text'] . '</span>';
			}
		}

		echo $caption;
	}
	// If $echo is false, return the caption
	else {
		// If caption text is set, include it
		if ( ! empty( $captiondata['caption_text'] ) ) {
			$caption = $captiondata['caption_text'];
		}
		else {
			$caption = null;
		}

		// If source attribution data is set and desired, include it
		if ( ! empty( $captiondata['source_text'] ) && false != $attribution ) {
			// If a source URL is set, create a link
			if ( ! empty( $captiondata['source_url'] ) ) {
				$caption .= ' <a href="' . $captiondata['source_url'] . '">' . $captiondata['source_text'] . '</a>';
			}
			// If not, just include the text
			else {
				$caption .= ' ' . $captiondata['source_text'];
			}
		}

		return $caption;
	}
}

/**
 * Check whether a featured image caption is set. This function must be used within The Loop.
 *
 * @return 	boolean
 */
function cc_has_featured_image_caption() {
    // If the featured image caption function returns false, no caption data is set so return false
    if ( false == cc_featured_image_caption( false ) ) {
        return false;
    }

	return true;
}
?>
