=== WordPress Page Transitions ===
Contributors: wordpresstuning
Tags: page transitions, animatehtml, effects, animations
Requires at least: 3.1
Tested up to: 3.4.1
Stable tag: 1.4.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The plugin adds amazing Page Transitions to your WordPress blog.

== Description ==

The plugin adds amazing Page Transitions to your WordPress blog.
<br><br>
Before you install the plugin please note the following:<br>
*    The plugin requires flash to be installed on visitors computer. Without flash the website will work as regular one.<br>
*    On mobile devices the website will work as regular one.<br>
*    Page transitions do not work with ajax.<br>
*    The website should look as much as possible same in most common browsers.<br>
*    WordPress admin will have to install Adobe AIR and Page Transitions Generator software on their computer.<br>
*    Each time you add / edit posts - Page Transition AIR app should be launched.<br>
*    The plugin is at the testing stage. That's why it is distributed for free at the moment.<br>
<br><br>
Check out <a href="http://www.pagetransitions.com">http://www.PageTransitions.com</a> for documentation and examples

== Installation ==

1. Download and extract zip archive
2. Upload `wpt` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to 'Page Transitions' -> 'Transition files' and click 'Reset All Page Transitions' button. Follow the notice message
Please note: `wp-content` and theme directories should be writable

== Screenshots ==

1. 'Scanner' Page Transition
2. 'Genie' Page Transition
3. 'Bad Signal' Page Transition
4. 'Butterflies' Page Transition 
5. 'Old TV' Page Transition 
6. 'Flip' Page Transition
7. 'Dispersion' Page Transition
8. 'Transformers' Page Transition 

== Changelog ==

= 1.4.3 =
* added remove page transition files function 

= 1.4.2 =
* fixed IE bug with the removing event listeners

= 1.4.1 =
* added support for author pages
* fixed endless pre-loader image spinning IE bug on Notifications page

= 1.4 =
* added support for the tags
* merged category options into one section
* styling for the pre-loader and page
* display normal home page for search crawlers
* fixed problem when wordpress is located not in the root
* better filenames for the page transition files
* fixed endless reloading bug for the urls when it has double (or more) slashes
* added support for archives urls without permalinks
* small code refactoring

= 1.3 =
* added support for the archives
* config.xml is deprecated
* force coping the theme files on plugin activation / updates

= 1.2.1 =
* fixed bug when home page is a static page

= 1.2 =
* added page transitions for categories and other paginated pages
* fixed resize bug when page transition failed to play
* fixed home page pagination problem

= 1.1 =
* the plugin has been moved to www.pagetransitions.com
* warning problem fix