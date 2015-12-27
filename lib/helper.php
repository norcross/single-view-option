<?php
/**
 * Single View Option - Admin Module
 *
 * Contains functions only intended to run on the admin side.
 *
 * @package Single View Option
 */

/**
 * Start our engines.
 */
class RKV_SVO_Helper
{

	/**
	 * Confirm the user has permission to save meta.
	 *
	 * @param  integer $post_id  The post ID being viewed.
	 * @param  string  $cap      The capability being compared against.
	 *
	 * @return bool              Whether or not the user has the desired capability.
	 */
	public static function meta_save_check( $post_id = 0, $cap = 'edit_post' ) {

		// Bail out if running an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		// Bail out if running an ajax request.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}

		// Bail out if running a cron.
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return false;
		}

		// Bail out if user does not have permissions.
		if ( ! empty( $post_id ) && ! current_user_can( $cap, $post_id ) ) {
			return false;
		}

		// Return true (which means all checks passed).
		return true;
	}

	/**
	 * Return the allowed post types to use on. Defaults to "post" and "page".
	 *
	 * @return array  The post types.
	 */
	public static function get_post_types() {
		return apply_filters( 'svo_post_types', array( 'post', 'page' ) );
	}

	/**
	 * Return whether or not to show the 'view all' link.
	 *
	 * @return array  The post types.
	 */
	public static function check_view_all() {
		return apply_filters( 'svo_enable_view_all_link', false );
	}

	/**
	 * Return a boolean value on whether or not we are on a OK post type.
	 *
	 * @return bool  Whether or not to auto enable.
	 */
	public static function check_post_types() {

		// Get our types.
		$types  = self::get_post_types();

		// Return the bool.
		return ! empty( $types ) && is_singular( $types ) ? true : false;
	}

	/**
	 * Return a boolean value on whether or not this is enabled by default.
	 *
	 * @return bool  Whether or not to auto enable.
	 */
	public static function check_auto_enable() {
		return apply_filters( 'svo_enable_auto', false );
	}

	/**
	 * Checks if the 'view all' option is enabled on the specific post item
	 *
	 * @param  integer $post_id  The post ID to check.
	 *
	 * @return bool              Whether or not it is active
	 */
	public static function check_post_active( $post_id = 0 ) {

		// Check for the auto-enable.
		if ( false !== $auto = self::check_auto_enable() ) {
			return true;
		}

		// Pull the value from the DB.
		$single = get_post_meta( $post_id, '_svo_active', true );

		// Return the resulting bool.
		return ! empty( $single ) ? true : false;
	}

	/**
	 * Checks the post content for the <!--nextpage--> tag
	 *
	 * @param  integer $post_id  The post ID to check.
	 *
	 * @return bool              Whether or not it is active
	 */
	public static function check_post_content( $post_id = 0 ) {

		// Pull the value from the DB.
		$text   = get_post_field( 'post_content', $post_id, 'raw' );

		// Check the content for the `nextpage` tag.
		return strpos( $text, '<!--nextpage-->' ) === false ? false : true;
	}

	/**
	 * Creates the permalink with the 'all' in in.
	 *
	 * @param  integer $post_id  The post ID to use in constructing the permalink.
	 *
	 * @return string            The original or updated permalink.
	 */
	public static function all_permalink( $post_id = 0 ) {

		// Check against the post types we've allowed.
		if ( ! in_array( get_post_type( $post_id ), self::get_post_types() ) ) {
			return get_permalink( $post_id );
		}

		// Fetch my base permalink.
		$link   = get_permalink( $post_id );

		// Build out and return the link and make sure it's cleaned up.
		return rtrim( $link, '/' ) . '/all/';
	}

	// End class.
}

// Call our class.
new RKV_SVO_Helper();



