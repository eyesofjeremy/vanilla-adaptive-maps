=== Vanilla Adaptive Maps ===
Contributors: jcdesign
Tags: responsive, map, google maps, shortcode
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 1.0.1
Donate link: http://jeremycarlson.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Map any address with a shortcode. Mobile users get a static map; desktop users will see a google map.

== Description ==

A way to include an Adaptive Map, based on [Brad Frost’s Adaptive Maps pattern](http://bradfrostweb.com/blog/post/adaptive-maps/), without requiring an external JavaScript library.

The basic premise is that we should be using mobile devices’ far better-suited mapping applications rather than attempting to frame maps in our own websites. Users w/ larger screens will get a full map in an iframe.

No styling has been provided, but the link and map are wrapped in `div.adaptive-map`.

== Installation ==

1. Upload the `vanilla-adaptive-maps` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

To use, write a shortcode like this: `[vamap addr="1203 Pearl St, Boulder, CO 80302"]`

To include a responsive map in a theme template, write the following PHP:

`<?php echo do_shortcode('[vamap addr="1203 Pearl St, Boulder, CO 80302"]'); ?>`

== Frequently Asked Questions ==

= What is the breakpoint between mobile and desktop? =

Right now, the breakpoint is set at 550px, which was the default in Brad’s model, and seems reasonable to me.

= Can I change the breakpoint for switching from mobile to desktop? =

Yes, although you’ll have to modify the plugin. Open `vanilla-adaptive-maps.php` and look for `set_breakpoint`. You can change the number there.

Right now we are only supporting a pixel-based breakpoint. I want to change that, though.

== Screenshots ==

1. Entering shortcode into a post
2. The map is created above the link
3. On narrower screens, a static map image will be created instead

== Changelog ==

= 1.0.1 =
* Files reorganized. No code changes.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.0 =
First release. Fly, little plugin, fly! Help with the mappin'!

== Credits ==

All props to [Brad Frost](http://bradfrostweb.com/blog/post/adaptive-maps/) who presented the idea quite a while ago.