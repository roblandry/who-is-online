=== Plugin Name ===
Contributors: Peter McDonald
Donate link: http://collectionmanagers.com/donate.php
Tags: who is online, display online users, sidebar, admin widget
Requires at least: 2.5.0
Tested up to: 2.9.1
Stable Tag: 0.1.5
Tag: trunk

Shows how many users are browsing your blog including how many pageviews they have made and how long they have been on the blog.

== Description ==

Plugin that allows you to easily see who is currently viewing your blog.

For administrators the plugin shows you how many people, guesys and search engines are currently viewing your blog. The plugin shows how long the person has been viewing your blog as well as what page they are currently viewing.

For users the plugin has a side box that displays how many users and guests are currently viewing your blog. If the template you are using is set to show the authors name on posts this will now also show if the author is online.

== Installation ==

1. Upload the who-is-online folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate too appearance/widget within the wordpress admin panel and activate the who is online sidebar 

== Changelog ==

= 0.1.5 =
* Fixed gettext support with thanks to Micht.
* Added French, Finish and Swedish languages.

= 0.1.4 =
* Added gettext support.

= 0.1.3 =
* Added the authors names now show (online) after them on the blog if that person is online. Good for multi author blogs.
* Fixed an issue where the footer displayed within the online users list.
* Fixed an issue where the plugin did not work properly in subfolders.

= 0.1.2 =
* Fixed a bug where an IP translated into a signed long.

= 0.1.1 =
* Changed structure of the file.
* Removed sql functions and changed to using php.

= 0.1.0 =
* Initial plugin released.

== Upgrade Notice ==

= 0.1.5 =
This version resolves the internationalization issues. We have also added a couple of languages.

= 0.1.4 =
This version gives the ability of translating the plugin.

= 0.1.3 =
This version resolved a display issue in the online users page. Also an issue with running the plugin in subdirectories was resolved.

== Frequently Asked Questions ==

= Can the plugin be translated into other languages? =
The plugin now uses gettext so can be easily translated into other languages. If you translate the plugin into another language please let me know so that I can also provide this language.

= Is it possible to show online users in the admin widget? =
A future release will show the most active users however this is not implemented at this time.

= Can the visitors see who are online? =
At the moment users can see how many members and how many guests are online. If your template shows post authors they will also see within posts if that author is online.

= My theme does not handle widgets can my visitors still see how many users are online? =
At present we only support the widget method but I will be adding support for adding simply code in a future release.

= I have a problem running the script, what can I do? =
You can post the issue on the wordpress forum however the best method of gaining support is by visiting the support page in the plugin home page.

= I have a feature request, how can I request it? =
We are always looking for ideas. You can post the feature request on the wordpress forum however the best method is by visiting the support page in the plugin home page.