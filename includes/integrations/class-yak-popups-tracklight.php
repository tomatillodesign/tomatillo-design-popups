<?php
/**
 * Yak Popups ↔ Tracklight (optional) integration.
 *
 * Logs with Tracklight v1 schema (_tl_* keys).
 * Events:
 *  - popup_activated / popup_deactivated
 *  - popup_title_changed, popup_body_changed
 *  - popup_layout_changed, popup_side_image_changed, popup_background_changed
 *  - popup_action_changed, popup_form_shortcode_changed, popup_button_changed
 *  - popup_trigger_changed, popup_delay_changed, popup_dismiss_days_changed, popup_test_mode_changed
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class Yak_Popups_Tracklight_Integration {

	const OBJ_TYPE  = 'option';
	const OBJ_ID    = 'yak_popups_settings';
	const OBJ_TITLE = 'Yak Popups';
	const SOURCE    = 'yak_popups';

	public function __construct() {

		// Enable/disable logs as discrete activation events.
		add_filter( 'acf/update_value/name=yak_popup_enable', [ $this, 'on_enable_change' ], 20, 3 );

		// Content / layout / media.
		add_filter( 'acf/update_value/name=yak_popup_title',       [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_content',     [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_format',      [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_side_image',  [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_bg',          [ $this, 'on_field_change' ], 20, 3 );

		// CTA (action, button, form).
		add_filter( 'acf/update_value/name=yak_popup_action',          [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_form_shortcode',  [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_btn_text',        [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_btn_url',         [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_btn_target',      [ $this, 'on_field_change' ], 20, 3 );

		// Behavior.
		add_filter( 'acf/update_value/name=yak_popup_trigger',        [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_delay',          [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_scroll_percent', [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_dismiss_days',   [ $this, 'on_field_change' ], 20, 3 );
		add_filter( 'acf/update_value/name=yak_popup_test_mode',      [ $this, 'on_field_change' ], 20, 3 );
	}

	/**
	 * Enable toggle → activated/deactivated.
	 */
	public function on_enable_change( $value, $post_id, $field ) {
		$before = (bool) get_field( 'yak_popup_enable', 'option' );
		$after  = (bool) $value;

		if ( $before === $after ) {
			return $value;
		}

		$this->send([
			'_tl_type'         => $after ? 'popup_activated' : 'popup_deactivated',
			'_tl_bucket'       => 'administrative',
			'_tl_note'         => $after ? 'Popup enabled' : 'Popup disabled',
			'_tl_before'       => [ 'yak_popup_enable' => $before ],
			'_tl_after'        => [ 'yak_popup_enable' => $after ],
		]);

		return $value;
	}

	/**
	 * Generic field logger mapped to specific event names.
	 */
	public function on_field_change( $value, $post_id, $field ) {
		$name   = isset( $field['name'] )  ? (string) $field['name']  : '';
		$label  = isset( $field['label'] ) ? (string) $field['label'] : $name;

		$before_raw = get_field( $name, 'option' );      // old (pre-save)
		$after_raw  = $value;                            // new (about to be saved)

		// Normalize for comparison.
		$before_cmp = $this->normalize_for_compare( $before_raw );
		$after_cmp  = $this->normalize_for_compare( $after_raw );

		if ( $before_cmp === $after_cmp ) {
			return $value; // nothing changed
		}

		// Determine event type & bucket by field.
		$event  = $this->event_type_for_field( $name );
		$bucket = $this->bucket_for_field( $name );

		// Make a human note.
		$note = $this->note_for_change( $name, $label, $before_raw, $after_raw );

		// Optionally coalesce button subfields into a single logical event type.
		if ( in_array( $name, [ 'yak_popup_btn_text', 'yak_popup_btn_url', 'yak_popup_btn_target' ], true ) ) {
			$event = 'popup_button_changed';
		}

		$this->send([
			'_tl_type'         => $event,
			'_tl_bucket'       => $bucket,
			'_tl_note'         => $note,
			'_tl_before'       => [ $name => $before_raw ],
			'_tl_after'        => [ $name => $after_raw ],
			'_tl_context'      => [ 'field' => $name ],
		]);

		return $value;
	}

	/**
	 * Build and dispatch a Tracklight payload (and local hook).
	 */
	private function send( array $payload ): void {
		$base = [
			'_tl_source'       => self::SOURCE,
			'_tl_actor'        => get_current_user_id() ?: 0,
			'_tl_object_type'  => self::OBJ_TYPE,
			'_tl_object_id'    => self::OBJ_ID,
			'_tl_object_title' => self::OBJ_TITLE,
			'_tl_when'         => current_time( 'mysql', true ),
		];

		$payload = array_merge( $base, $payload );

		// Local hook (always fires, even without Tracklight installed).
		do_action( 'yak_popups/log', $payload );

		// Tracklight intake (preferred helper) or action bridge.
		if ( function_exists( 'tracklight_log_event' ) ) {
			tracklight_log_event( $payload );
		} else {
			do_action( 'tracklight/log', $payload );
		}
	}

	/**
	 * Event name mapping by ACF field name.
	 */
	private function event_type_for_field( string $name ): string {
		return match ( $name ) {
			'yak_popup_title'         => 'popup_title_changed',
			'yak_popup_content'       => 'popup_body_changed',
			'yak_popup_format'        => 'popup_layout_changed',
			'yak_popup_side_image'    => 'popup_side_image_changed',
			'yak_popup_bg'            => 'popup_background_changed',
			'yak_popup_action'        => 'popup_action_changed',
			'yak_popup_form_shortcode'=> 'popup_form_shortcode_changed',
			'yak_popup_btn_text',
			'yak_popup_btn_url',
			'yak_popup_btn_target'    => 'popup_button_changed',
			'yak_popup_trigger'        => 'popup_trigger_changed',
			'yak_popup_delay'          => 'popup_delay_changed',
			'yak_popup_scroll_percent' => 'popup_scroll_percent_changed',
			'yak_popup_dismiss_days'   => 'popup_dismiss_days_changed',
			'yak_popup_test_mode'      => 'popup_test_mode_changed',
			default                   => 'popup_settings_changed',
		};
	}

	/**
	 * Bucket by field (editorial vs administrative).
	 */
	private function bucket_for_field( string $name ): string {
		$editorial = [
			'yak_popup_title',
			'yak_popup_content',
			'yak_popup_format',
			'yak_popup_side_image',
			'yak_popup_bg',
			'yak_popup_action',
			'yak_popup_form_shortcode',
			'yak_popup_btn_text',
			'yak_popup_btn_url',
			'yak_popup_btn_target',
		];

		return in_array( $name, $editorial, true ) ? 'editorial' : 'administrative';
	}

	/**
	 * Human-readable change notes.
	 */
	private function note_for_change( string $name, string $label, $before, $after ): string {

		// Special cases with nicer notes.
		if ( $name === 'yak_popup_content' ) {
			$bl = $this->strlen_html( (string) $before );
			$al = $this->strlen_html( (string) $after );
			return sprintf( 'Popup body updated (%d → %d chars, HTML stripped)', $bl, $al );
		}

		if ( $name === 'yak_popup_btn_target' ) {
			$map = [ 'same' => 'Same Tab', 'new' => 'New Tab' ];
			$from = $map[ (string) $before ] ?? (string) $before;
			$to   = $map[ (string) $after ]  ?? (string) $after;
			return sprintf( 'Button target changed (%s → %s)', $from, $to );
		}

		if ( $name === 'yak_popup_format' ) {
			$nicify = static function( $v ) {
				return match ( (string) $v ) {
					'single'      => 'Single Panel',
					'image_left'  => 'Image Left / Body Right',
					'image_right' => 'Image Right / Body Left',
					default       => (string) $v,
				};
			};
			return sprintf( 'Layout changed (%s → %s)', $nicify( $before ), $nicify( $after ) );
		}

		if ( $name === 'yak_popup_action' ) {
			$nicify = static function( $v ) {
				return match ( (string) $v ) {
					'none'   => 'None',
					'button' => 'Button',
					'form'   => 'Form (Shortcode)',
					default  => (string) $v,
				};
			};
			return sprintf( 'Action changed (%s → %s)', $nicify( $before ), $nicify( $after ) );
		}

		// Media fields (URLs)
		if ( in_array( $name, [ 'yak_popup_side_image', 'yak_popup_bg' ], true ) ) {
			$from = $before ? 'set' : 'empty';
			$to   = $after  ? 'set' : 'empty';
			$label = $name === 'yak_popup_bg' ? 'Background' : 'Side image';
			return sprintf( '%s %s (%s → %s)', $label, $from === 'set' ? 'updated' : 'changed', $from, $to );
		}

		// Default: short diff-ish note.
		$before_s = is_scalar( $before ) ? (string) $before : wp_json_encode( $before );
		$after_s  = is_scalar( $after )  ? (string) $after  : wp_json_encode( $after );
		$before_s = $this->trim_mid( $before_s );
		$after_s  = $this->trim_mid( $after_s );

		return sprintf( '%s updated (%s → %s)', $label, $before_s, $after_s );
	}

	private function normalize_for_compare( $val ): string {
		if ( is_array( $val ) || is_object( $val ) ) {
			return wp_json_encode( $val );
		}
		// Normalize booleans/"0"/"1", whitespace, etc.
		$str = (string) $val;
		$str = trim( $str );
		return $str;
	}

	private function strlen_html( string $html ): int {
		$text = wp_strip_all_tags( $html, true );
		return strlen( $text );
	}

	private function trim_mid( string $s, int $max = 48 ): string {
		$s = trim( $s );
		if ( strlen( $s ) <= $max ) {
			return $s;
		}
		$keep = (int) floor( ($max - 1) / 2 );
		return substr( $s, 0, $keep ) . '…' . substr( $s, -$keep );
	}
}

// Bootstrap
add_action( 'plugins_loaded', function () {
	if ( is_admin() ) {
		new Yak_Popups_Tracklight_Integration();
	}
} );
