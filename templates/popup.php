<?php
/**
 * Yak Popups ~ Template
 *
 * Popup markup with layout support: single, image left, image right.
 */

if ( ! defined( 'ABSPATH' ) ) {
     exit;
}

// Ensure $settings exists
if ( empty( $settings ) || ! is_array( $settings ) ) {
     return;
}

$format     = $settings['format'] ?? 'single';
$side_image = $settings['side_image'] ?? '';
$bg_image   = $settings['bg_image'] ?? '';
$title      = $settings['title'] ?? '';
$content    = $settings['content'] ?? '';
$form       = $settings['form'] ?? '';
?>

<div class="yak-popup yak-popup--<?php echo esc_attr( $format ); ?>" id="yak-popup" role="dialog" aria-modal="true" aria-labelledby="yak-popup-title" hidden>
     <div class="yak-popup__overlay"></div>

     <div class="yak-popup__inner" style="<?php echo $bg_image ? 'background-image:url(' . esc_url( $bg_image ) . ');' : ''; ?>">

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

                    <?php if ( $form ) : ?>
                         <div class="yak-popup__form">
                              <?php echo do_shortcode( $form ); ?>
                         </div>
                    <?php endif; ?>
               </div>

          <?php elseif ( ( $format === 'image_left' || $format === 'image_right' ) ) : ?>

               <div class="yak-popup__layout">
                    <?php if ( $side_image ) : ?>
                         <div class="yak-popup__side-image">
                              <img src="<?php echo esc_url( $side_image ); ?>" alt="">
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

                         <?php if ( $form ) : ?>
                              <div class="yak-popup__form">
                                   <?php echo do_shortcode( $form ); ?>
                              </div>
                         <?php endif; ?>
                    </div>
               </div>

          <?php endif; ?>

     </div>
</div>
