=== Spit It Out ===
Contributors: sashafiero
Tags: developers, development, query, error
Description: Provides different ways to display various developer-useful information about the theme page.
Tested up to: 4.2.2
Stable tag: 2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==
This provides logged in wordpress admin users a new submenu in Settings for "Spit It Out".

• Whether the Spit It Out overlay on every page is active

And which bits of information you would like displayed.
• Current template file name
• Current query
• $_SERVER
• $_REQUEST
• $_FILES
• $_SESSION
• The last error that occurred
• $wpdb->queries if it exists



There is a shortcode for use in WYSIWYG content
The option is to display if user is not a logged in admin; default to show only if admin.
[spititout] or [spititout adminonly="false"]


There is a function for use in templates.
If you call it with show_spitio(false)
it will show the stuff even if the viewer is not a logged in admin
show_spitio() or show_spitio(false)



In the future, I would like to provide options for the following items.

• Various style aspects of the box
• Whether to give back the result by just spitting it out in the box, or by json, or perhaps something else
• Specific bits of $_SERVER so you don't have to get a dump of the whole thing

Maybe offer the option to put a display toggling icon in the WP admin bar at the top of the site for logged in admins.


== Installation ==
1. Upload the `spit-it-out` folder to the `/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Spit It Out to choose your displayed information and turn on the overlay, if desired.


== Frequently Asked Questions ==
= Can just anyone see the information displayed? =
The overlay can ONLY be seen by logged in admin users.  The shortcode and template tag offer options for displaying to non-admin viewers, but default to admin visibility only.



== Screenshots ==
1. Options for displaying overlay and what information to show
2. The overlay box
3. The overlay box slid closed


== Changelog ==
= 2.1 =
* Changed overlay to slide out from the left side, defaulting to closed, with an icon for toggling open/closed
* Updated the screenshots & added #3
* Added link to Settings in the Plugins list page

= 2.0.1 =
* Fixed some variable names and global delcarations

= 2.0 =
* Added shortcode for use in WYSIWYG, and function for use in templates

= 1.0 =
* Basic functionality: 
* display overlay box
* options for show or not
* which things to put in the box.


== Upgrade Notice ==
= 2.0.1 =
Fixed some variable names and global delcarations. Important!