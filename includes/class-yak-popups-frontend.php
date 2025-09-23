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
	 * Render the popup markup if enabled, or if Test Mode is on for admins.
	 */
	public function render_popup() {
		// ACF required.
		if ( ! function_exists( 'get_field' ) ) {
			return;
		}

		// Get all popup fields in a single call for better performance
		$fields = get_fields( 'option' );
		if ( ! $fields ) {
			return;
		}

		// Allow admins to preview when disabled if Test Mode is on.
		$enabled   = (bool) ( $fields['yak_popup_enable'] ?? false );
		$test_mode = (bool) ( $fields['yak_popup_test_mode'] ?? false );

		if ( ! $enabled ) {
			// If popup is disabled, only render when Test Mode is enabled AND user can manage options.
			if ( ! $test_mode || ! current_user_can( 'manage_options' ) ) {
				return;
			}
		}

		// Core fields.
		$title       = (string) ( $fields['yak_popup_title'] ?? '' );
		$content     = (string) ( $fields['yak_popup_content'] ?? '' );
		$bg_image    = (string) ( $fields['yak_popup_bg'] ?? '' );
		$format      = (string) ( $fields['yak_popup_format'] ?? 'single' );
		$side_image  = (string) ( $fields['yak_popup_side_image'] ?? '' );

		// Behavior.
		$trigger      = (string) ( $fields['yak_popup_trigger'] ?? 'delay' );
		$delay        = (int)    ( $fields['yak_popup_delay'] ?? 5 );
		$dismiss_days = (int)    ( $fields['yak_popup_dismiss_days'] ?? 7 );
		$show_test    = (bool)   $test_mode; // already fetched above

		// Action selector.
		$action = (string) ( $fields['yak_popup_action'] ?? 'none' );
		if ( $action !== 'button' && $action !== 'form' ) {
			$action = 'none';
		}

		// Action payloads.
		$form_shortcode = '';
		$form_html      = '';
		$button         = [
			'text'   => '',
			'url'    => '',
			'target' => '_self',
		];

		if ( 'form' === $action ) {
			$form_shortcode = (string) ( $fields['yak_popup_form_shortcode'] ?? '' );
			if ( $form_shortcode ) {
				$form_html = do_shortcode( $form_shortcode );
			}
		} elseif ( 'button' === $action ) {
			$btn_text   = (string) ( $fields['yak_popup_btn_text'] ?? '' );
			$btn_url    = (string) ( $fields['yak_popup_btn_url'] ?? '' );
			$btn_target = (string) ( $fields['yak_popup_btn_target'] ?? 'same' ); // 'same' | 'new'

			$button['text']   = $btn_text;
			$button['url']    = $btn_url ? esc_url_raw( $btn_url ) : '';
			$button['target'] = ( 'new' === $btn_target ) ? '_blank' : '_self';
		}

		// Build settings array for template + JS.
		$settings = [
			// Display
			'title'       => $title,
			'content'     => $content,              // HTML; template should escape carefully where needed.
			'bg_image'    => $bg_image ? esc_url_raw( $bg_image ) : '',
			'format'      => $format ?: 'single',
			'side_image'  => $side_image ? esc_url_raw( $side_image ) : '',

			// Action
			'action'         => $action,            // 'none' | 'button' | 'form'
			'form_shortcode' => $form_shortcode,
			'form_html'      => $form_html,         // pre-rendered for convenience
			'button'         => $button,            // ['text','url','target']

			// Behavior
			'trigger'      => $trigger ?: 'delay',
			'delay'        => max( 0, $delay ),
			'dismiss_days' => max( 0, $dismiss_days ),
			'show_test'    => $show_test ? 1 : 0,
		];

		/**
		 * Filter the popup settings before render.
		 *
		 * @param array $settings
		 */
		$settings = apply_filters( 'yak_popups/settings', $settings );

		// Load template.
		$template = YAK_POPUPS_DIR . 'templates/popup.php';
		if ( file_exists( $template ) ) {
			// Template expects $settings in scope.
			/** @var array $settings */
			include $template;
		}

		// Inline JS config for frontend (for dismissal / triggers etc).
		printf(
			'<script type="application/json" id="yak-popups-config">%s</script>',
			wp_json_encode( $settings )
		);
	}
}

new Yak_Popups_Frontend();
