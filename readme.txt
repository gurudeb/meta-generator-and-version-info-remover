=== Meta Generator and Version Info Remover ===
Contributors: gurudeb
Plugin URI: http://pankajgurudeb.blogspot.com/2013/04/meta-generator-and-version-info-remover.html
Author URI: http://pankajgurudeb.blogspot.com
Donate link: http://pankajgurudeb.blogspot.com/2013/04/meta-generator-and-version-info-remover.html
Tags: remove, version, generator, security, meta, appended version, css ver, js ver, meta generator
Requires at least: 3.0
Tested up to: 3.6-beta3
Stable tag: 2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin will remove the version info appended to enqueued style and script urls along with Meta Generator in the head section and in RSS feeds.

== Description ==

This plugin will remove the version information that gets appended to enqueued style and script urls. It will also remove the Meta Generator in the head and in RSS feeds. Adds a bit of obfuscation to hide the WordPress version number and generator tag that many sniffers detect automatically from view source. 

You can enable/disable each removal options from admin dashboard:
<ol><li>Remove Meta Generator Tag</li>
<li>Remove Version from Stylesheet</li>
<li>Remove Version from Script</li></ol>

Dashboard > Settings > Meta Generator and Version Info Remover

== Installation ==

1. Unzip the zipped file and upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Manage individual options from Dashboard > Settings > Meta Generator and Version Info Remover

== Frequently Asked Questions ==

N/A.

== Screenshots ==

N/A.

== Changelog ==

= 2.1 =
* Updated: Generator Remover Filter.

= 2.0 =
* Added: Options to manage the settings from Administrator Dashboard.

= 1.0 =
* Initial Commit.

== Upgrade Notice ==

N/A.
