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

		// Allow admins to preview when disabled if Test Mode is on.
		$enabled   = (bool) get_field( 'yak_popup_enable', 'option' );
		$test_mode = (bool) get_field( 'yak_popup_test_mode', 'option' );

		if ( ! $enabled ) {
			// If popup is disabled, only render when Test Mode is enabled AND user can manage options.
			if ( ! $test_mode || ! current_user_can( 'manage_options' ) ) {
				return;
			}
		}

		// Core fields.
		$title       = (string) get_field( 'yak_popup_title', 'option' );
		$content     = (string) get_field( 'yak_popup_content', 'option' );
		$bg_image    = (string) get_field( 'yak_popup_bg', 'option' );
		$format      = (string) get_field( 'yak_popup_format', 'option' );
		$side_image  = (string) get_field( 'yak_popup_side_image', 'option' );

		// Behavior.
		$trigger      = (string) get_field( 'yak_popup_trigger', 'option' );
		$delay        = (int)    get_field( 'yak_popup_delay', 'option' );
		$dismiss_days = (int)    get_field( 'yak_popup_dismiss_days', 'option' );
		$show_test    = (bool)   $test_mode; // already fetched above

		// Action selector.
		$action = (string) get_field( 'yak_popup_action', 'option' );
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
			$form_shortcode = (string) get_field( 'yak_popup_form_shortcode', 'option' );
			if ( $form_shortcode ) {
				$form_html = do_shortcode( $form_shortcode );
			}
		} elseif ( 'button' === $action ) {
			$btn_text   = (string) get_field( 'yak_popup_btn_text', 'option' );
			$btn_url    = (string) get_field( 'yak_popup_btn_url', 'option' );
			$btn_target = (string) get_field( 'yak_popup_btn_target', 'option' ); // 'same' | 'new'

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
