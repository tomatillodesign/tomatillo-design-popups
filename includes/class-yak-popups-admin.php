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
                         'key'      => 'field_yak_popup_enable',
                         'label'    => __( 'Enable Popup', 'yak-popups' ),
                         'name'     => 'yak_popup_enable',
                         'type'     => 'true_false',
                         'ui'       => 1,
                         'default_value' => 0,
                         'wrapper'  => [ 'width' => '50' ],
                    ],
                    [
                         'key'      => 'field_yak_popup_test_mode',
                         'label'    => __( 'Test Mode', 'yak-popups' ),
                         'name'     => 'yak_popup_test_mode',
                         'type'     => 'true_false',
                         'ui'       => 1,
                         'default_value' => 0,
                         'instructions'  => __( 'For admins only. Forces popup on every page load, ignoring dismissal.', 'yak-popups' ),
                         'wrapper'  => [ 'width' => '50' ],
                    ],

                    // --- Format & Content Combined ---
                    [
                         'key'   => 'field_yak_popup_content_tab',
                         'label' => __( 'Format & Content', 'yak-popups' ),
                         'type'  => 'tab',
                         'placement' => 'top',
                    ],
                    [
                         'key'     => 'field_yak_popup_format',
                         'label'   => __( 'Popup Layout', 'yak-popups' ),
                         'name'    => 'yak_popup_format',
                         'type'    => 'button_group',
                         'choices' => [
                              'single'      => __( 'Single Panel', 'yak-popups' ),
                              'image_left'  => __( 'Image Left / Body Right', 'yak-popups' ),
                              'image_right' => __( 'Image Right / Body Left', 'yak-popups' ),
                         ],
                         'default_value' => 'single',
                         'layout'        => 'horizontal',
                    ],
                    [
                         'key'           => 'field_yak_popup_side_image',
                         'label'         => __( 'Side Image', 'yak-popups' ),
                         'name'          => 'yak_popup_side_image',
                         'type'          => 'image',
                         'return_format' => 'url',
                         'preview_size'  => 'medium',
                         'instructions'  => __( 'Shown when using Image Left or Image Right format.', 'yak-popups' ),
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
                         'key'        => 'field_yak_popup_title',
                         'label'      => __( 'Popup Title', 'yak-popups' ),
                         'name'       => 'yak_popup_title',
                         'type'       => 'text',
                         'placeholder'=> __( 'Enter popup headline...', 'yak-popups' ),
                    ],
                    [
                         'key'        => 'field_yak_popup_content',
                         'label'      => __( 'Popup Body', 'yak-popups' ),
                         'name'       => 'yak_popup_content',
                         'type'       => 'wysiwyg',
                         'tabs'       => 'visual',
                         'toolbar'    => 'basic',
                         'media_upload' => 0,
                    ],
                    [
                         'key'           => 'field_yak_popup_bg',
                         'label'         => __( 'Background Image (Body)', 'yak-popups' ),
                         'name'          => 'yak_popup_bg',
                         'type'          => 'image',
                         'return_format' => 'url',
                         'preview_size'  => 'medium',
                    ],
                    [
                         'key'         => 'field_yak_popup_form_shortcode',
                         'label'       => __( 'Form Shortcode', 'yak-popups' ),
                         'name'        => 'yak_popup_form_shortcode',
                         'type'        => 'text',
                         'placeholder' => '[gravityform id="4"]',
                    ],

                    // --- Behavior Section ---
                    [
                         'key'   => 'field_yak_popup_behavior_tab',
                         'label' => __( 'Behavior', 'yak-popups' ),
                         'type'  => 'tab',
                         'placement' => 'top',
                    ],
                    [
                         'key'     => 'field_yak_popup_trigger',
                         'label'   => __( 'Trigger Type', 'yak-popups' ),
                         'name'    => 'yak_popup_trigger',
                         'type'    => 'select',
                         'choices' => [
                              'load'   => __( 'On Page Load', 'yak-popups' ),
                              'delay'  => __( 'After Delay', 'yak-popups' ),
                              'scroll' => __( 'After Scrolling %', 'yak-popups' ),
                         ],
                         'default_value' => 'delay',
                         'ui'            => 1,
                    ],
                    [
                         'key'           => 'field_yak_popup_delay',
                         'label'         => __( 'Delay (seconds)', 'yak-popups' ),
                         'name'          => 'yak_popup_delay',
                         'type'          => 'number',
                         'default_value' => 5,
                         'min'           => 1,
                         'append'        => 's',
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
                         'key'           => 'field_yak_popup_dismiss_days',
                         'label'         => __( 'Hide After Dismissal (days)', 'yak-popups' ),
                         'name'          => 'yak_popup_dismiss_days',
                         'type'          => 'number',
                         'default_value' => 7,
                         'min'           => 1,
                         'append'        => 'days',
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
}

new Yak_Popups_Admin();
