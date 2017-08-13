=== Likes and replies from custom fields ===

Contributors: colinwalker
Tags: webmention, like, reply, response
Requires at least: 4.7
Tested up to: 4.8.1
Stable tag: 1.0.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

== Description ==

Add IndieWeb likes and replies to posts, with microformat2 markup, using a value added to custom fields.

Usage: Add the required page URL to the 'Liked' or 'Reply' field in the meta box to have it automatically added to the post body when saved. Microformat2 markup ensures the relevant webmention is sent on posting.

Also works when posts are made using the REST API (e.g. from Workflow)

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Likes and Replies settings

== Frequently Asked Questions ==

= Is this all I need  to send a like or reply? =

No. The plugin requires a way to send webmentions such as the Webmention plugin by Matthias Pfefferle - https://wordpress.org/plugins/webmention/ - the plugin adds the required mf2 markup so that the right webventions are sent.

= Can I customise how posts look? =

Yes. A settings page allows you to choose the text to add for likes and replies.

== Changelog ==

= 1.0.1 =

Ignore libxml errors to avoid problems parsing remote page contents

= 1.0.0 =

Initial release

== Upgrade Notice ==

Nothing here yet
