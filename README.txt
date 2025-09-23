=== Tomatillo Design ~ Popups ===
Contributors: chrisliubeers
Tags: popup, modal, gravity forms, acf, lightweight, yak theme
Requires at least: 6.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight, user-friendly popup system designed specifically for Yak theme sites. Features ACF-powered admin interface, Gravity Forms integration, and smart dismissal tracking.

== Description ==

**Tomatillo Design ~ Popups** is a streamlined WordPress plugin that brings professional popup functionality to your Yak theme-powered website. Built with simplicity and performance in mind, this plugin offers everything you need to create engaging popups without the bloat.

= Key Features =

* **ACF-Powered Admin Interface** - Clean, intuitive settings page under WordPress Settings
* **Multiple Layout Options** - Single panel, image left/right split layouts
* **Smart Trigger System** - Page load, delayed, or scroll-based activation
* **Gravity Forms Integration** - Seamlessly embed forms with conditional logic support
* **Flexible Actions** - None, button, or form shortcode options
* **Test Mode** - Preview popups without affecting visitors (admin-only)
* **Smart Dismissal** - Respects user preferences with configurable hide duration
* **Mobile Responsive** - Optimized layouts for all screen sizes
* **Accessibility Ready** - ARIA labels, keyboard navigation, and semantic markup
* **Tracklight Integration** - Optional logging for content management insights

= Perfect For =

* Lead generation campaigns
* Newsletter signups
* Important announcements
* Product promotions
* Event notifications
* User onboarding flows

= Requirements =

* Advanced Custom Fields (ACF) Pro
* Yak Theme (recommended)
* Gravity Forms (optional, for form functionality)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/tomatillo-design-popups/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Navigate to Settings > Tomatillo Popups to configure your popup
4. Enable your popup and customize the content, layout, and behavior
5. Use Test Mode to preview changes before going live

== Frequently Asked Questions ==

= Do I need ACF Pro? =

Yes, Advanced Custom Fields Pro is required. The plugin uses ACF's options page functionality to create the admin interface.

= Can I use this without the Yak theme? =

While designed for Yak theme sites, the plugin will work with any WordPress theme. Some styling may need adjustment for optimal appearance.

= Does it work with other form plugins? =

The plugin is specifically designed for Gravity Forms integration. Other form plugins would require custom development.

= How does the dismissal tracking work? =

The plugin uses localStorage to remember when users dismiss the popup. You can configure how long to hide the popup after dismissal (default: 7 days).

= Can I preview changes before going live? =

Yes! Enable Test Mode in the settings to preview your popup on every page load without affecting regular visitors.

= Is the plugin mobile-friendly? =

Absolutely. The popup layouts automatically adapt to mobile screens, stacking content vertically for optimal viewing.

== Screenshots ==

1. Admin settings interface with ACF fields
2. Single panel popup layout
3. Image left/right split layouts
4. Mobile responsive design
5. Test mode functionality

== Changelog ==

= 1.1 =
* Added action selector (none/button/form)
* Enhanced Gravity Forms integration with conditional logic support
* Improved mobile responsiveness
* Added Tracklight integration for content management insights
* Enhanced accessibility features
* Better error handling and debugging

= 1.0 =
* Initial release
* Basic popup functionality
* ACF admin interface
* Multiple layout options
* Smart trigger system

== Upgrade Notice ==

= 1.1 =
Major update with enhanced form integration and improved user experience. Test your popups after upgrading.

== Support ==

For support, feature requests, or custom development, visit [Tomatillo Design](https://www.tomatillodesign.com) or contact us directly.

== Technical Details ==

The plugin is built with modern WordPress standards:
* Uses ACF Pro for admin interface
* Implements proper WordPress hooks and filters
* Follows WordPress coding standards
* Includes comprehensive error handling
* Optimized for performance with minimal database queries
* Uses CSS custom properties for theme integration
* Implements proper sanitization and escaping

== Privacy Policy ==

This plugin does not collect or store any personal data. Dismissal preferences are stored locally in the user's browser using localStorage. No data is sent to external servers.

== Credits ==

Developed by Chris Liu-Beers at Tomatillo Design
Built for the Yak theme ecosystem
Inspired by modern popup best practices and accessibility standards
