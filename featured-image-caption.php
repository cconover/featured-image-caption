<?php
/**
 * Plugin Name: Featured Image Caption
 * Plugin URI: https://christiaanconover.com/code/wp-featured-image-caption?ref=plugin-data
 * Description: Set a caption for the featured image of a post that can be displayed in your theme
 * Version: 0.3.2
 * Author: Christiaan Conover
 * Author URI: https://christiaanconover.com?ref=wp-featured-image-caption-plugin-author-uri
 * License: GPLv2
 * @package cconover
 * @subpackage featured-image-caption
 **/

/**
 * Main plugin class
 */
class cc_featured_image_caption {
	// Plugin constants
	const ID = 'cc-featured-image-caption'; // Plugin ID
	const NAME = 'Featured Image Caption'; // Plugin name
	const VERSION = '0.3.2'; // Plugin version
	const WPVER = '2.7'; // Minimum version of WordPress required for this plugin
	const PREFIX = 'cc_featured_image_caption_'; // Plugin database/method prefix
	const METAPREFIX = '_cc_featured_image_caption'; // Post meta database prefix

	// Class properties
	private $options; // Plugin options and settings

	/**
	 * Class constructor
	 */
	function __construct() {
		// Admin
		if ( is_admin() ) {
			// Initialize in admin
			$this->admin_initialize();

			// Hooks and filters
			add_action( 'add_meta_boxes', array( &$this, 'metabox') ); // Add meta box
			add_action( 'save_post', array( &$this, 'save_metabox' ) ); // Save the caption when the post is saved
			register_activation_hook( __FILE__, array( &$this, 'activate' ) ); // Plugin activation
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) ); // Plugin deactivation
		}
	} // __construct()

	/**
	 * Create the meta box
	 */
	function metabox() {
		// Specify the screens where the meta box should be available
		$screens = array( 'post', 'page' );

		// Iterate through the specified screens to add the meta box
		foreach ( $screens as $screen ) {
			add_meta_box(
				self::ID, // HTML ID for the meta box
				self::NAME, // Title of the meta box displayed to the us
				array( &$this, 'metabox_callback' ), // Callback function for the meta box to display it to the user
				$screen, // Locations where the meta box should be shown
				'side' // Location where the meta box should be shown. This one is placed on the side.
			);
		}
	} // metabox()

	/**
	 * Featured image caption meta box callback
	 */
	function metabox_callback( $post ) {
		// Add a nonce field to verify data submissions came from our site
		wp_nonce_field( array( &$this, 'metabox' ), self::PREFIX . 'nonce' );

		// Retrieve the current caption as a string, if set
		$caption = get_post_meta( $post->ID, self::METAPREFIX, true );

		// If the data is a string, convert it to an array (legacy data support)
		if ( is_string( $caption ) ) {
			$caption = array(
				'caption_text'	=> $caption
			);
		}

		echo 'Caption text <textarea style="width: 100%; max-width: 100%;" id="' . self::PREFIX . '_caption_text" name="' . self::PREFIX . '_caption_text">' . ( ! empty( $caption['caption_text'] ) ? esc_attr( $caption['caption_text'] ) : null ) . '</textarea>';
		echo '<br><br>';
		echo '<strong>Source Attribution</strong><br>';
		echo 'Text <input style="width: 100%;" id="' . self::PREFIX . '_source_text" name="' . self::PREFIX . '_source_text" value="' . ( ! empty( $caption['source_text'] ) ? $caption['source_text'] : null ) . '">';
		echo 'URL <input style="width: 100%;" id="' . self::PREFIX . '_source_url" name="' . self::PREFIX . '_source_url" value="' . ( ! empty( $caption['source_url'] ) ? $caption['source_url'] : null ) . '">';
	} // metabox_callback()

	/**
	 * Save the meta box data
	 */
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
		$caption = array(
			'caption_text'	=> wp_kses_post( $_POST[self::PREFIX . '_caption_text'] ),
			'source_text'	=> sanitize_text_field( $_POST[self::PREFIX . '_source_text'] ),
			'source_url'	=> esc_url_raw( $_POST[self::PREFIX . '_source_url'] )
		);

		// Update the caption meta field
		update_post_meta( $post_id, self::METAPREFIX, $caption );
	} // save_metabox()

	/**
	 * Retrieve the caption data
	 *
	 * @return 	mixed
	 */
	function get_caption( $id ) {
		// Get the caption data from the post meta
		$caption = get_post_meta( $id, self::METAPREFIX, true );

		// If caption data is present, return the caption
		if ( ! empty( $caption ) ) {
			return $caption;
		}
		else {
			return false;
		}
	} // get_caption()

	/* ===== Admin Initialization ===== */
	/**
	 * Initialize plugin admin
	 */
	function admin_initialize() {
		// Get plugin options from database
		$this->options = get_option( self::PREFIX . 'options' );

		// Run upgrade process
		$this->upgrade();
	} // admin_initialize()

	/**
	 * Plugin upgrade
	 */
	function upgrade() {
		// Check whether the database-stored plugin version number is less than the current plugin version number, or whether there is no plugin version saved in the database
		if ( ! empty( $this->options['dbversion'] ) && version_compare( $this->options['dbversion'], self::VERSION, '<' ) ) {
			// Set local variable for options (always the first step in the upgrade process)
			$options = $this->options;

			/* Update the plugin version saved in the database (always the last step of the upgrade process) */
			// Set the value of the plugin version
			$options['dbversion'] = self::VERSION;

			// Save to the database
			update_option( self::PREFIX . 'options', $options );
			/* End update plugin version */
		}
	} // upgrade()
	/*
	===== End Admin Initialization =====
	*/

	/*
	===== Plugin Activation and Deactivation =====
	*/
	/**
	 * Plugin activation
	 */
	public function activate() {
		// Check to make sure the version of WordPress being used is compatible with the plugin
		if ( version_compare( get_bloginfo( 'version' ), self::WPVER, '<' ) ) {
	 		wp_die( 'Your version of WordPress is too old to use this plugin. Please upgrade to the latest version of WordPress.' );
	 	}

	 	// Default plugin options
	 	$options = array(
	 		'dbversion'     => self::VERSION, // Current plugin version
	 	);

	 	// Add options to database
	 	add_option( self::PREFIX . 'options', $options );
	} // activate()

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		// Remove the plugin options from the database
		delete_option( self::PREFIX . 'options' );
	} // deactivate()

	/* ===== End Plugin Activation and Deactivation ===== */
} // End main plugin class

// Create plugin object
$cc_featured_image_caption = new cc_featured_image_caption;

/**
 * Theme function
 * Use this function to retrieve the caption for the featured image. This function must be used within The Loop.
 *
 * @param 	boolean $echo 	Whether to print the results [true] or return them [false] (default: true)
 * @param 	boolean $source Whether to include source data, if available. (default: true)
 * @return 	mixed
 */
function cc_featured_image_caption( $echo = true, $source = true ) {
	// Access global featured image caption object and post object
	global $cc_featured_image_caption, $post;

	// Retrieve the caption from post meta
	$captiondata = $cc_featured_image_caption->get_caption( $post->ID );

	// If a caption is set, assemble it
	if ( ! false == $captiondata ) {
		// If the data is a string, convert it to an array (legacy data support)
		if ( is_string( $captiondata ) ) {
			$captiondata = array(
				'caption_text'	=> $captiondata
			);
		}

		// If $echo is true, print the caption
		if ( $echo ) {
			// If caption text is set, place caption data inside an HTML <span> to allow for CSS formatting
			if ( ! empty( $captiondata['caption_text'] ) ) {
				$caption = '<span class="cc-featured-image-caption">' . $captiondata['caption_text'] . '</span>';
			}
			else {
				$caption = null;
			}

			// If source attribution data is availble and desired, display it
			if ( ! empty( $captiondata['source_text'] ) && false != $source ) {
				// If source attribution has a URL, format the source as a link
				if ( ! empty( $captiondata['source_url'] ) ) {
					$caption .= ' <span class="cc-featured-image-caption-source"><a href="' . $captiondata['source_url'] . '">' . $captiondata['source_text'] . '</a></span>';
				}
				// If no URL is set, just display the text
				else {
					$caption .= ' <span class="cc-featured-image-caption-source">' . $captiondata['source_text'] . '</span>';
				}
			}

		    echo $caption;
		}
		// If false, return the caption
		else {
			// If caption text is set, include it
			if ( ! empty( $captiondata['caption_text'] ) ) {
				$caption = $captiondata['caption_text'];
			}
			else {
				$caption = null;
			}

			// If source attribution data is set and desired, include it
			if ( ! empty( $captiondata['source_text'] ) && false != $source ) {
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
	// If no caption is set, return false
	else {
		return false;
	}
} // cc_featured_image_caption()

/**
 * Check whether a featured image caption is set. This function must be used within The Loop.
 *
 * @return 	boolean
 */
function cc_has_featured_image_caption() {
    // If the featured image caption function does not return false, a featured image caption is set
    if ( ! false == cc_featured_image_caption( false ) ) {
        return true;
    }
    // If it does return false, a featured image caption is not set
    else {
        return false;
    }
} // cc_has_featured_image_caption()
?>
