Plugin Name: Blurtbox
Description: For logged in admin users, displays a dismissable box at the top left with various information that may be helpful for developers.
Version:     0.1



This provides logged in wordpress admin users a new submenu in Settings for "Blurtbox".

• Whether Blurtbox is active

And which bits of information you would like displayed in the blurtbox.
• Current template file name
• Current query
• $_SERVER
• $_REQUEST
• $_FILES
• $_SESSION
• The last error that occurred

In the future, I would like to provide options for the following items.

• Various style aspects of the box
• Whether to give back the result by just spitting it out in the box, or by json, or perhaps something else
• Specific bits of $_SERVER so you don't have to get a dump of the whole thing

I would also like to instead of hide()ing the box when clicking the X, make it minimized and accessible by a little icon off to the side or something like that.

Maybe offer the option to put a display toggling icon in the WP admin bar at the top of the site for logged in admins.