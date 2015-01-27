<?php

/**
 * Main plugin class
 * @package cconover
 * @subpackage featured-image-caption
 */

namespace cconover;

class FeaturedImageCaption {
    // Plugin constants
    const ID			= 'cc-featured-image-caption';	// Plugin ID
    const NAME			= 'Featured Image Caption';		// Plugin name
    const VERSION		= '0.4.0';						// Plugin version
    const WPVER			= '2.7';						// Minimum version of WordPress required for this plugin
    const PREFIX		= 'cc_featured_image_caption_';	// Plugin database prefix
    const METAPREFIX	= '_cc_featured_image_caption';	// Post meta database prefix

    // Class properties
    protected $options; // Plugin options and settings

    /**
    * Class constructor
    */
    function __construct() {
        // Get plugin options from database
        $this->options = get_option( self::PREFIX . 'options' );
        
        // Admin
        if ( is_admin() ) {
            // Include the file containing the main Admin class and create an admin object
            require_once( plugin_dir_path( __FILE__ ) . 'admin/featured-image-caption-admin.php' );
            $this->admin = new Admin;
        }
    }

    /**
    * Retrieve the caption data
    *
    * @param 	integer	$id	The ID of the post or page for which we need the featured image caption
    *
    * @return 	mixed
    */
    function get_caption( $id ) {
        // Get the caption data from the post meta
        $caption = get_post_meta( $id, self::METAPREFIX, true );

        // If caption data is not present, return false
        if ( empty( $caption ) ) {
            return false;
        }

        // Legacy support: if caption is a string, convert it to an array
        if ( is_string( $caption ) ) {
            $string = $caption;
            $caption = array(
                'caption_text'	=> $string
            );
        }

        return $caption;
    }

}

?>
