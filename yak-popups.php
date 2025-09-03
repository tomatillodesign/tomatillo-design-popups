<?php
/**
 * Plugin Name:       Tomatillo Design ~ Popups
 * Description:       Lightweight popup system for Yak theme sites. Supports background image, title, text, and Gravity Forms embed. Simple JS state management (localStorage).
 * Version:           1.1
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
 * Enqueue frontend assets.
 */
add_action( 'wp_enqueue_scripts', function() {
	// CSS
	wp_enqueue_style(
		'yak-popups',
		YAK_POPUPS_URL . 'assets/yak-popups.css',
		[],
		'1.0.0'
	);

	// JS
	wp_enqueue_script(
		'yak-popups',
		YAK_POPUPS_URL . 'assets/yak-popups.js',
		[],
		'1.0.0',
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
    if ( ! class_exists( 'GFForms' ) ) {
        return;
    }

    // Get the form shortcode from ACF (stored in Yak Popups settings)
    $form_shortcode = get_field( 'yak_popup_form_shortcode', 'option' );

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

