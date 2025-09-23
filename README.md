# Tomatillo Design ~ Popups

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL%20v2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A lightweight, user-friendly popup system designed specifically for Yak theme sites. Features ACF-powered admin interface, Gravity Forms integration, and smart dismissal tracking.

## 🚀 Quick Start

1. **Install** the plugin and activate it
2. **Configure** your popup at Settings > Tomatillo Popups
3. **Enable** and customize your content
4. **Test** using Test Mode before going live

## ✨ Key Features

### 🎨 **Flexible Layouts**
- **Single Panel** - Clean, centered content
- **Image Left/Right** - Split layouts with side images
- **Mobile Responsive** - Automatically adapts to all screen sizes

### ⚡ **Smart Triggers**
- **Page Load** - Immediate display
- **Delayed** - Show after specified seconds
- **Scroll-Based** - Trigger at 50% scroll depth

### 🎯 **Multiple Actions**
- **None** - Pure content display
- **Button** - Custom CTA with URL
- **Form** - Gravity Forms integration with conditional logic

### 🛠 **Admin-Friendly**
- **ACF-Powered Interface** - Clean, intuitive settings
- **Test Mode** - Preview without affecting visitors
- **Smart Dismissal** - Configurable hide duration
- **Tracklight Integration** - Optional content management insights

## 📋 Requirements

- **WordPress** 6.0 or higher
- **PHP** 7.4 or higher
- **Advanced Custom Fields Pro** (required)
- **Yak Theme** (recommended)
- **Gravity Forms** (optional, for form functionality)

## 🔧 Installation

### Manual Installation
1. Download the plugin files
2. Upload to `/wp-content/plugins/tomatillo-design-popups/`
3. Activate through WordPress admin
4. Navigate to Settings > Tomatillo Popups

### Configuration
1. **Enable** your popup
2. **Choose** layout (Single, Image Left, Image Right)
3. **Add** title and content
4. **Configure** action (None, Button, or Form)
5. **Set** trigger behavior
6. **Test** with Test Mode enabled

## 🎛 Settings Overview

### Format & Content
- **Popup Layout** - Choose from three layout options
- **Side Image** - Upload image for split layouts
- **Title** - Popup headline
- **Body** - Rich text content with full WYSIWYG editor
- **Background Image** - Optional background for content area

### Action Configuration
- **Action Type** - None, Button, or Form
- **Button Settings** - Text, URL, and target window
- **Form Shortcode** - Gravity Forms integration

### Behavior Settings
- **Trigger Type** - Load, Delay, or Scroll
- **Delay** - Seconds before showing (delay trigger only)
- **Dismissal Duration** - Days to hide after dismissal

### Admin Features
- **Enable/Disable** - Toggle popup on/off
- **Test Mode** - Admin-only preview mode

## 🎨 Layout Options

### Single Panel
Perfect for simple announcements or calls-to-action. Clean, centered design that works on all devices.

### Image Left / Body Right
Split layout with image on the left and content on the right. Great for visual storytelling.

### Image Right / Body Left
Mirror of the left layout. Choose based on your content flow and visual hierarchy.

## 📱 Mobile Responsive

All layouts automatically adapt to mobile screens:
- Split layouts stack vertically
- Images resize appropriately
- Touch-friendly close buttons
- Optimized spacing and typography

## 🔗 Gravity Forms Integration

Seamless integration with Gravity Forms:
- **Conditional Logic** - Full support for form conditions
- **Auto-initialization** - Forms work immediately when popup shows
- **Proper Styling** - Forms inherit popup styling
- **Error Handling** - Graceful fallbacks if GF isn't available

## ♿ Accessibility Features

Built with accessibility in mind:
- **ARIA Labels** - Proper dialog labeling
- **Keyboard Navigation** - ESC key closes popup
- **Focus Management** - Proper focus handling
- **Screen Reader Support** - Semantic markup
- **High Contrast** - Works with system preferences

## 🧪 Test Mode

Perfect for content creators:
- **Admin-Only** - Only visible to administrators
- **Bypasses Dismissal** - Shows every time for testing
- **Real-Time Preview** - See changes immediately
- **Safe Testing** - No impact on regular visitors

## 📊 Tracklight Integration

Optional logging for content management:
- **Change Tracking** - Log all popup modifications
- **User Attribution** - Track who made changes
- **Event Types** - Categorized by content vs. administrative changes
- **Before/After Values** - Complete change history

## 🎯 Use Cases

### Lead Generation
- Newsletter signups
- Contact form collection
- Download offers
- Consultation requests

### Marketing Campaigns
- Product announcements
- Sale promotions
- Event notifications
- Seasonal campaigns

### User Experience
- Onboarding flows
- Feature announcements
- Important updates
- Help documentation

## 🔧 Customization

### CSS Custom Properties
The plugin uses CSS custom properties for easy theming:

```css
.yak-popup {
    --yak-primary-font: 'Your Font';
    --yak-color-white: #ffffff;
    --yak-color-black: #000000;
    --yak-radius: 8px;
    --yak-font-xl: 1.5rem;
    --yak-font-md: 1rem;
}
```

### WordPress Hooks
Extend functionality with WordPress hooks:

```php
// Filter popup settings before render
add_filter('yak_popups/settings', function($settings) {
    // Modify settings
    return $settings;
});

// Listen for popup events (with Tracklight)
add_action('yak_popups/log', function($payload) {
    // Handle popup events
});
```

## 🐛 Troubleshooting

### Common Issues

**Popup not showing:**
- Check if popup is enabled
- Verify ACF Pro is active
- Ensure JavaScript is loading
- Check browser console for errors

**Forms not working:**
- Confirm Gravity Forms is active
- Verify form shortcode is correct
- Check form ID in shortcode
- Ensure form is published

**Styling issues:**
- Check theme compatibility
- Verify CSS is loading
- Look for conflicting styles
- Test with default theme

### Debug Mode
Enable WordPress debug mode to see detailed logs:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📈 Performance

Optimized for speed and efficiency:
- **Minimal Database Queries** - Settings cached in options
- **Lazy Loading** - Assets only load when needed
- **Efficient JavaScript** - Event delegation and proper cleanup
- **CSS Optimization** - Minimal styles with CSS custom properties

## 🔒 Privacy & Security

- **No Data Collection** - Plugin doesn't collect personal data
- **Local Storage Only** - Dismissal preferences stored locally
- **Proper Sanitization** - All inputs properly sanitized
- **WordPress Standards** - Follows WordPress security best practices

## 🤝 Support

### Getting Help
- **Documentation** - Check this README first
- **WordPress Admin** - Settings page includes helpful instructions
- **Debug Mode** - Enable for detailed error information

### Custom Development
For custom features or integrations:
- **Tomatillo Design** - [www.tomatillodesign.com](https://www.tomatillodesign.com)
- **Professional Services** - Custom development available
- **Theme Integration** - Specialized Yak theme support

## 📝 Changelog

### Version 1.1
- ✨ Added action selector (none/button/form)
- 🔧 Enhanced Gravity Forms integration
- 📱 Improved mobile responsiveness
- 📊 Added Tracklight integration
- ♿ Enhanced accessibility features
- 🐛 Better error handling and debugging

### Version 1.0
- 🎉 Initial release
- 🎨 Multiple layout options
- ⚡ Smart trigger system
- 🛠 ACF admin interface

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 👨‍💻 Credits

**Developed by:** Chris Liu-Beers at Tomatillo Design  
**Built for:** Yak theme ecosystem  
**Inspired by:** Modern popup best practices and accessibility standards

---

*Transform your website with professional popups that engage visitors and drive conversions.*
