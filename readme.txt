=== Single View Option ===
Contributors: norcross, reaktivstudios
Tags: single page, nexttag
Donate link: http://andrewnorcross.com/donate
Requires at least: 4.2
Tested up to: 4.4
Stable tag: 0.0.2
License: MIT
License URI: https://opensource.org/licenses/MIT

Add a "view as single page" option for paginated content.

== Description ==

Add a "view as single page" option for paginated content. When activated on a piece of content, paginated content will be converted into a single page.

== Installation ==

1. Upload `single-view-option` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the `Plugins` menu in WordPress.
1. Check the box labeled "Enable the single view option." in the Publish area to activate on a single piece of content.

== Frequently Asked Questions ==

= Where is the settings page? =

There isn't one. Enabling the plugin will add the checkbox to the Publish box.

= Why can I only see this on posts and pages? =

By default, the plugin only applies to posts and pages. The available post types can be modified via the `svo_post_types` filter by returning an array of the post types you want to enable it on. Example:

`
function rkv_modify_svo_types( $types ) {
	return array( 'post', 'page', 'MY-CUSTOM-POST-TYPE' );
}

add_filter( 'svo_post_types', 'rkv_modify_svo_types' );
`

= Can this be turned on automatically? =

Yes, via the `svo_enable_auto` filter. Example:

`
add_filter( 'svo_enable_auto', '__return_true' );
`

== Screenshots ==

1. The standard checkbox on content.

== Changelog ==

= 0.0.2: 2016-01-01 =

* Added `view-all-active` body class when "view all" is enabled.
* Removed meta box on non-supported post types.

= 0.0.1: 2015-12-27 =

* Initial release.

== Upgrade Notice ==

= 0.0.1: 2015-12-27 =

* Initial release.