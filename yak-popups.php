<?php
/**
 * Plugin Name:       Tomatillo Design ~ Popups
 * Description:       Lightweight popup system for Yak theme sites. Supports background image, title, text, and Gravity Forms embed. Simple JS state management (localStorage).
 * Version:           1.2.1
 * Author:            Chris Liu-Beers @ Tomatillo Design
 * Author URI:        https://www.tomatillodesign.com
 * Text Domain:       yak-popups
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define plugin paths.
 */
define( 'YAK_POPUPS_DIR', plugin_dir_path( __FILE__ ) );
define( 'YAK_POPUPS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Includes.
 */
require_once YAK_POPUPS_DIR . 'includes/class-yak-popups-admin.php';
require_once YAK_POPUPS_DIR . 'includes/class-yak-popups-frontend.php';
require_once YAK_POPUPS_DIR . 'includes/yak-popups-helpers.php';
require_once YAK_POPUPS_DIR . 'includes/integrations/class-yak-popups-tracklight.php';


/**
 * Check if popup assets should be loaded.
 */
function yak_popups_should_load_assets() {
	// Always load in admin for preview
	if ( is_admin() ) {
		return true;
	}

	// Check if popup is enabled
	if ( function_exists( 'get_fields' ) ) {
		$fields = get_fields( 'option' );
		if ( $fields ) {
			$enabled = (bool) ( $fields['yak_popup_enable'] ?? false );
			$test_mode = (bool) ( $fields['yak_popup_test_mode'] ?? false );
			
			// Load if enabled OR if test mode is on for admins
			if ( $enabled || ( $test_mode && current_user_can( 'manage_options' ) ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Enqueue frontend assets conditionally.
 */
add_action( 'wp_enqueue_scripts', function() {
	if ( ! yak_popups_should_load_assets() ) {
		return;
	}

	// CSS
	wp_enqueue_style(
		'yak-popups',
		YAK_POPUPS_URL . 'assets/yak-popups.css',
		[],
		'1.2'
	);

	// JS
	wp_enqueue_script(
		'yak-popups',
		YAK_POPUPS_URL . 'assets/yak-popups.js',
		[],
		'1.2',
		true
	);
} );

/**
 * Register ACF Options Page (separate from Yak Theme).
 */
add_action( 'acf/init', function() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page( [
			'page_title'  => __( 'Tomatillo Popups', 'yak-popups' ),
			'menu_title'  => __( 'Tomatillo Popups', 'yak-popups' ),
			'menu_slug'   => 'yak-popups-settings',
			'capability'  => 'manage_options',
			'redirect'    => false,
			'parent_slug' => 'options-general.php', // under Settings
		] );
	}
} );


add_filter( 'body_class', function( $classes ) {
    if ( current_user_can( 'administrator' ) ) {
        $classes[] = 'role-administrator';
    }
    return $classes;
});


/**
 * Enqueue Gravity Forms scripts for the popup form.
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( ! yak_popups_should_load_assets() ) {
        return;
    }

    if ( ! class_exists( 'GFForms' ) ) {
        return;
    }

    // Get the form shortcode from ACF (stored in Yak Popups settings)
    $fields = get_fields( 'option' );
    $form_shortcode = $fields['yak_popup_form_shortcode'] ?? '';

    if ( $form_shortcode && preg_match( '/id=["\']?(\d+)["\']?/', $form_shortcode, $matches ) ) {
        $form_id = absint( $matches[1] );

        if ( $form_id ) {
            // Load core GF scripts
            GFForms::enqueue_scripts();

            // Load scripts for this specific form (includes conditional logic)
            gravity_form_enqueue_scripts( $form_id, true );

            // Optional: log for debugging
            if ( WP_DEBUG ) {
                error_log( "[Yak Popups] Enqueued GF scripts for form ID {$form_id}" );
            }
        }
    }
}, 20 );

