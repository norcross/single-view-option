<?php
/**
 * Single View Option - Template Module
 *
 * Contains template tag functions
 *
 * @package Single View Option
 */

/**
 * Theme template tag to get link
 *
 * @param  integer $post_id  The post ID to check
 *
 * @return sting   $link  The permalink with our "all" included.
 */
function svo_link( $post_id = 0 ) {

	// If we have not activated it, return the original canonical.
	if ( false === $check = RKV_SVO_Helper::check_post_active( $post_id ) ) {
		return get_permalink( $post_id );
	}

	// Return the updated link.
	return RKV_SVO_Helper::all_permalink( $post_id );
}
