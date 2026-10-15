<?php

/**
 * Plugin Name:       Paste As Plain Text By Default
 * Plugin URI:        https://victorduse.com
 * Description:       Enable paste as text by default with one click in editors like Classic Editor, Divi Builder, Elementor, Beaver Builder and WPBakery.
 * Version:           1.2
 * Requires at least: 4.7
 * Requires PHP:      7.4
 * Author:            Victor Duse
 * Author URI:        https://victorduse.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

// Paste as text in Classic Editor
add_filter('tiny_mce_before_init', function ($init) {
    $init['paste_as_text'] = true;
    return $init;
});

// Paste as text in WPUF
add_filter('wpuf_textarea_editor_args', function ($args) {
    $args['tinymce']['plugins']       = 'paste';
    $args['tinymce']['paste_as_text'] = true;
    return $args;
});

// Paste as text in Divi Visual Builder and Divi Theme Builder
add_action('wp_footer', 'dm_paste_as_text', 9999);
add_action('admin_head', 'dm_paste_as_text', 9999);

function dm_paste_as_text()
{
    if (!is_user_logged_in()) {
        return;
    }

    $et_fb = isset($_GET['et_fb']) ? sanitize_text_field(wp_unslash($_GET['et_fb'])) : '';
    $page  = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

    if ($et_fb !== '1' && $page !== 'et_theme_builder') {
        return;
    }
?>
    <script>
        jQuery(function($) {
            tinymce.on('AddEditor', function(e) {
                var enablePlainText = function() {
                    try {
                        if (e.editor.plugins.paste.clipboard.pasteFormat.get() !== 'text') {
                            e.editor.execCommand('mceTogglePlainTextPaste');
                            $('.mce-notification button').click();
                        }
                    } catch (exception) {
                        // Prevent JS error from execCommand when TinyMCE has no NotificationManager
                    }
                };

                [300, 1000, 2000].forEach(function(delay) {
                    setTimeout(enablePlainText, delay);
                });
            });
        });
    </script>
<?php
}

// Add donate link on the Plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $links[] = '<a href="https://buymeacoffee.com/victorduse/" target="_blank" rel="noopener noreferrer" style="color:#3db634;">Buy developer a coffee</a>';
    return $links;
});
