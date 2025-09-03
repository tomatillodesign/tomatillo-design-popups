<?php
/**
 * Yak Popups ~ Template
 *
 * Popup markup with layout support: single, image left, image right.
 * Updated for v1.1: Action selector (none | button | form) with conditional rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Ensure $settings exists
if ( empty( $settings ) || ! is_array( $settings ) ) {
	return;
}

// Core display
$format     = $settings['format']      ?? 'single';
$side_image = $settings['side_image']  ?? '';
$bg_image   = $settings['bg_image']    ?? '';
$title      = $settings['title']       ?? '';
$content    = $settings['content']     ?? '';

// Action
$action        = $settings['action']        ?? 'none'; // 'none' | 'button' | 'form'
$form_html     = $settings['form_html']     ?? '';
$button        = $settings['button']        ?? ['text' => '', 'url' => '', 'target' => '_self'];

// Build inline styles safely
$inner_style = $bg_image ? 'background-image:url(' . esc_url( $bg_image ) . ');' : '';

// ARIA: prefer labelledby if we have a title; otherwise give a generic label
$dialog_aria = $title
	? 'aria-labelledby="yak-popup-title"'
	: 'aria-label="' . esc_attr__( 'Popup', 'yak-popups' ) . '"';
?>

<div class="yak-popup yak-popup--<?php echo esc_attr( $format ); ?>" id="yak-popup" role="dialog" aria-modal="true" <?php echo $dialog_aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="yak-popup__overlay"></div>

	<div class="yak-popup__inner" style="<?php echo esc_attr( $inner_style ); ?>">

		<button type="button" class="yak-popup__close" aria-label="<?php esc_attr_e( 'Close popup', 'yak-popups' ); ?>">&times;</button>

		<?php if ( $format === 'single' ) : ?>

			<div class="yak-popup__content">
				<?php if ( $title ) : ?>
					<h2 class="yak-popup__title" id="yak-popup-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>

				<?php if ( $content ) : ?>
					<div class="yak-popup__body">
						<?php echo wp_kses_post( $content ); ?>
					</div>
				<?php endif; ?>

				<?php
				// Action rendering (single layout)
				if ( $action === 'form' && $form_html ) : ?>
					<div class="yak-popup__form">
						<?php echo $form_html; // already do_shortcode'd in frontend class ?>
					</div>
				<?php elseif ( $action === 'button' && ! empty( $button['text'] ) && ! empty( $button['url'] ) ) :
					$target = ( isset( $button['target'] ) && $button['target'] === '_blank' ) ? '_blank' : '_self';
					$rel    = ( $target === '_blank' ) ? ' rel="noopener"' : '';
					?>
					<div class="yak-popup__cta">
						<a class="yak-popup__btn button"
						   href="<?php echo esc_url( $button['url'] ); ?>"
						   target="<?php echo esc_attr( $target ); ?>"<?php echo $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $button['text'] ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

		<?php elseif ( $format === 'image_left' || $format === 'image_right' ) : ?>

			<div class="yak-popup__layout">
				<?php if ( $side_image ) : ?>
					<div class="yak-popup__side-image">
						<img src="<?php echo esc_url( $side_image ); ?>" alt="" />
					</div>
				<?php endif; ?>

				<div class="yak-popup__content <?php echo $side_image ? '' : 'yak-popup__content--full'; ?>">
					<?php if ( $title ) : ?>
						<h2 class="yak-popup__title" id="yak-popup-title"><?php echo esc_html( $title ); ?></h2>
					<?php endif; ?>

					<?php if ( $content ) : ?>
						<div class="yak-popup__body">
							<?php echo wp_kses_post( $content ); ?>
						</div>
					<?php endif; ?>

					<?php
					// Action rendering (image split layouts)
					if ( $action === 'form' && $form_html ) : ?>
						<div class="yak-popup__form">
							<?php echo $form_html; // already do_shortcode'd ?>
						</div>
					<?php elseif ( $action === 'button' && ! empty( $button['text'] ) && ! empty( $button['url'] ) ) :
						$target = ( isset( $button['target'] ) && $button['target'] === '_blank' ) ? '_blank' : '_self';
						$rel    = ( $target === '_blank' ) ? ' rel="noopener"' : '';
						?>
						<div class="yak-popup__cta">
							<a class="yak-popup__btn button"
							   href="<?php echo esc_url( $button['url'] ); ?>"
							   target="<?php echo esc_attr( $target ); ?>"<?php echo $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $button['text'] ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>

		<?php endif; ?>

	</div>
</div>
