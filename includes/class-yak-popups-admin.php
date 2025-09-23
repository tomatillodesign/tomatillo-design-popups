<?php
/**
 * Yak Popups ~ Admin Settings
 *
 * Registers ACF fields for the Yak Popups options page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Yak_Popups_Admin {

	public function __construct() {
		add_action( 'acf/init', [ $this, 'register_fields' ] );
		add_action( 'acf/input/admin_head', [ $this, 'add_nonce_field' ] );
	}

	/**
	 * Register ACF fields for Yak Popups settings.
	 */
	public function register_fields() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( [
			'key'      => 'group_yak_popups',
			'title'    => __( 'Yak Popups Settings', 'yak-popups' ),
			'fields'   => [

				// --- Enable / Test Mode ---
				[
					'key'           => 'field_yak_popup_enable',
					'label'         => __( 'Enable Popup', 'yak-popups' ),
					'name'          => 'yak_popup_enable',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
					'wrapper'       => [ 'width' => '50' ],
				],
				[
					'key'           => 'field_yak_popup_test_mode',
					'label'         => __( 'Test Mode', 'yak-popups' ),
					'name'          => 'yak_popup_test_mode',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
					'instructions'  => __( 'For admins only. Forces popup on every page load, ignoring dismissal.', 'yak-popups' ),
					'wrapper'       => [ 'width' => '50' ],
				],

				// --- Format & Content ---
				[
					'key'       => 'field_yak_popup_content_tab',
					'label'     => __( 'Format & Content', 'yak-popups' ),
					'type'      => 'tab',
					'placement' => 'top',
				],
				[
					'key'           => 'field_yak_popup_format',
					'label'         => __( 'Popup Layout', 'yak-popups' ),
					'name'          => 'yak_popup_format',
					'type'          => 'button_group',
					'choices'       => [
						'single'      => __( 'Single Panel', 'yak-popups' ),
						'image_left'  => __( 'Image Left / Body Right', 'yak-popups' ),
						'image_right' => __( 'Image Right / Body Left', 'yak-popups' ),
					],
					'default_value' => 'single',
					'layout'        => 'horizontal',
				],
				[
					'key'              => 'field_yak_popup_side_image',
					'label'            => __( 'Side Image', 'yak-popups' ),
					'name'             => 'yak_popup_side_image',
					'type'             => 'image',
					'return_format'    => 'url',
					'preview_size'     => 'medium',
					'instructions'     => __( 'Shown when using Image Left or Image Right format.', 'yak-popups' ),
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_format',
								'operator' => '!=',
								'value'    => 'single',
							],
						],
					],
				],
				[
					'key'         => 'field_yak_popup_title',
					'label'       => __( 'Popup Title', 'yak-popups' ),
					'name'        => 'yak_popup_title',
					'type'        => 'text',
					'placeholder' => __( 'Enter popup headline...', 'yak-popups' ),
				],
				[
					'key'          => 'field_yak_popup_content',
					'label'        => __( 'Popup Body', 'yak-popups' ),
					'name'         => 'yak_popup_content',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',   // Visual + Text tabs
					'toolbar'      => 'full',  // Full toolbar
					'media_upload' => 0,
				],

				// --- Action (directly below content, no separate tab) ---
				[
					'key'           => 'field_yak_popup_action',
					'label'         => __( 'Action', 'yak-popups' ),
					'name'          => 'yak_popup_action',
					'type'          => 'button_group',
					'choices'       => [
						'none'   => __( 'None', 'yak-popups' ),
						'button' => __( 'Button', 'yak-popups' ),
						'form'   => __( 'Form (Shortcode)', 'yak-popups' ),
					],
					'default_value' => 'none',
					'layout'        => 'horizontal',
				],
				// If Action = Form
				[
					'key'              => 'field_yak_popup_form_shortcode',
					'label'            => __( 'Form Shortcode', 'yak-popups' ),
					'name'             => 'yak_popup_form_shortcode',
					'type'             => 'text',
					'placeholder'      => '[gravityform id="4"]',
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_action',
								'operator' => '==',
								'value'    => 'form',
							],
						],
					],
				],
				// If Action = Button
				[
					'key'              => 'field_yak_popup_btn_text',
					'label'            => __( 'Button Text', 'yak-popups' ),
					'name'             => 'yak_popup_btn_text',
					'type'             => 'text',
					'placeholder'      => __( 'Learn more', 'yak-popups' ),
					'wrapper'          => [ 'width' => '33' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_action',
								'operator' => '==',
								'value'    => 'button',
							],
						],
					],
				],
				[
					'key'              => 'field_yak_popup_btn_url',
					'label'            => __( 'Button URL', 'yak-popups' ),
					'name'             => 'yak_popup_btn_url',
					'type'             => 'url',
					'placeholder'      => 'https://example.com/',
					'wrapper'          => [ 'width' => '44' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_action',
								'operator' => '==',
								'value'    => 'button',
							],
						],
					],
				],
				[
					'key'              => 'field_yak_popup_btn_target',
					'label'            => __( 'Button Window', 'yak-popups' ),
					'name'             => 'yak_popup_btn_target',
					'type'             => 'button_group',
					'choices'          => [
						'same' => __( 'Same Tab', 'yak-popups' ),
						'new'  => __( 'New Tab', 'yak-popups' ),
					],
					'default_value'    => 'same',
					'wrapper'          => [ 'width' => '23' ],
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_action',
								'operator' => '==',
								'value'    => 'button',
							],
						],
					],
				],

				// Optional image field still available, now below action controls.
				[
					'key'           => 'field_yak_popup_bg',
					'label'         => __( 'Background Image (Body)', 'yak-popups' ),
					'name'          => 'yak_popup_bg',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				],

				// --- Behavior ---
				[
					'key'       => 'field_yak_popup_behavior_tab',
					'label'     => __( 'Behavior', 'yak-popups' ),
					'type'      => 'tab',
					'placement' => 'top',
				],
				[
					'key'           => 'field_yak_popup_trigger',
					'label'         => __( 'Trigger Type', 'yak-popups' ),
					'name'          => 'yak_popup_trigger',
					'type'          => 'select',
					'choices'       => [
						'load'   => __( 'On Page Load', 'yak-popups' ),
						'delay'  => __( 'After Delay', 'yak-popups' ),
						'scroll' => __( 'After Scrolling', 'yak-popups' ),
					],
					'default_value' => 'delay',
					'ui'            => 1,
				],
				[
					'key'              => 'field_yak_popup_delay',
					'label'            => __( 'Delay (seconds)', 'yak-popups' ),
					'name'             => 'yak_popup_delay',
					'type'             => 'number',
					'default_value'    => 5,
					'min'              => 1,
					'max'              => 60,
					'step'             => 1,
					'append'           => 's',
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_trigger',
								'operator' => '==',
								'value'    => 'delay',
							],
						],
					],
				],
				[
					'key'              => 'field_yak_popup_scroll_percent',
					'label'            => __( 'Scroll Percentage', 'yak-popups' ),
					'name'             => 'yak_popup_scroll_percent',
					'type'             => 'number',
					'default_value'    => 50,
					'min'              => 10,
					'max'              => 90,
					'step'             => 5,
					'append'           => '%',
					'instructions'     => __( 'Show popup when user scrolls this percentage down the page.', 'yak-popups' ),
					'conditional_logic' => [
						[
							[
								'field'    => 'field_yak_popup_trigger',
								'operator' => '==',
								'value'    => 'scroll',
							],
						],
					],
				],
				[
					'key'           => 'field_yak_popup_dismiss_days',
					'label'         => __( 'Hide After Dismissal (days)', 'yak-popups' ),
					'name'          => 'yak_popup_dismiss_days',
					'type'          => 'number',
					'default_value' => 7,
					'min'           => 1,
					'max'           => 365,
					'step'          => 1,
					'append'        => 'days',
					'instructions'  => __( 'How many days to hide popup after user dismisses it.', 'yak-popups' ),
				],

			],
			'location' => [
				[
					[
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'yak-popups-settings',
					],
				],
			],
		] );
	}

	/**
	 * Add nonce field to ACF options page for security.
	 */
	public function add_nonce_field() {
		$screen = get_current_screen();
		if ( $screen && strpos( $screen->id, 'yak-popups-settings' ) !== false ) {
			wp_nonce_field( 'yak_popups_admin_nonce', 'yak_popups_nonce' );
		}
	}

	/**
	 * Verify nonce for admin actions.
	 */
	public static function verify_nonce() {
		if ( ! isset( $_POST['yak_popups_nonce'] ) || ! wp_verify_nonce( $_POST['yak_popups_nonce'], 'yak_popups_admin_nonce' ) ) {
			wp_die( __( 'Security check failed. Please try again.', 'yak-popups' ) );
		}
	}
}

new Yak_Popups_Admin();
