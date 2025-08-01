<?php
/**
 * Yak Popups ~ Frontend
 *
 * Outputs popup HTML into footer and passes settings to JS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Yak_Popups_Frontend {

	public function __construct() {
		add_action( 'wp_footer', [ $this, 'render_popup' ], 20 );
	}

	/**
	 * Render the popup markup if enabled.
	 */
	public function render_popup() {
		// Bail if disabled
		if ( ! function_exists( 'get_field' ) || ! get_field( 'yak_popup_enable', 'option' ) ) {
			return;
		}

		// Collect settings
		$settings = [
            'title'        => get_field( 'yak_popup_title', 'option' ),
            'content'      => get_field( 'yak_popup_content', 'option' ),
            'bg_image'     => get_field( 'yak_popup_bg', 'option' ),
            'form'         => get_field( 'yak_popup_form_shortcode', 'option' ),
            'trigger'      => get_field( 'yak_popup_trigger', 'option' ),
            'delay'        => get_field( 'yak_popup_delay', 'option' ),
            'dismiss_days' => get_field( 'yak_popup_dismiss_days', 'option' ),
            'show_test'    => get_field( 'yak_popup_test_mode', 'option' ),
            'format'       => get_field( 'yak_popup_format', 'option' ),
            'side_image'   => get_field( 'yak_popup_side_image', 'option' ),
        ];

		// Load template
		$template = YAK_POPUPS_DIR . 'templates/popup.php';
		if ( file_exists( $template ) ) {
			include $template;
		}

		// Inline JS config for frontend
		printf(
			'<script type="application/json" id="yak-popups-config">%s</script>',
			wp_json_encode( $settings )
		);
	}
}

new Yak_Popups_Frontend();
