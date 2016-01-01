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
class RKV_SVO_Admin
{

	/**
	 * Load our metabox items on the admin
	 *
	 * @return void
	 */
	public function init() {

		// Bail on non admin.
		if ( ! is_admin() ) {
			return;
		}

		// Load our actions.
		add_action( 'post_submitbox_misc_actions',  array( $this, 'load_checkbox'       )           );
		add_action( 'save_post',                    array( $this, 'save_checkbox'       )           );
	}

	/**
	 * Load the checkbox on the side publish area to enable / disable single view option.
	 *
	 * @return void
	 */
	public function load_checkbox() {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return;
		}

		// Only fire if user has the option.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Check for the auto-enable.
		if ( false !== $auto = RKV_SVO_Helper::check_auto_enable() ) {

			// Output the box.
			echo '<div id="single-view" class="misc-pub-section misc-pub-single-view">';
				echo '<p class="description">' . esc_html__( 'The single view option is auto enabled.', 'single-view-option' ) . '</p>';
			echo '</div>';

			// And return.
			return;
		}

		// Fetch my global post object.
		global $post;

		// Bail without a post object or type, or not in our allowed types.
		if ( ! is_object( $post ) ||  empty( $post->post_type ) || ! in_array( $post->post_type, RKV_SVO_Helper::get_post_types() ) ) {
			return;
		}

		// Fetch the meta value itself.
		$single = get_post_meta( $post->ID, '_svo_active', true );

		// Use nonce for verification.
		wp_nonce_field( 'svo_meta_nonce', 'svo_meta_nonce' );

		// Echo our the actual side checkbox.
		echo '<div id="single-view" class="misc-pub-section misc-pub-single-view">';
			echo '<label for="svo-active">';
				echo '<input type="checkbox" name="svo-active" id="svo-active" value="1" ' . checked( $single, 1, false ) . '>';
			echo ' ' . esc_html__( 'Enable the single view option.', 'single-view-option' ) . '</label>';
		echo '</div>';
	}

	/**
	 * Update the meta value for the single view checkbox.
	 *
	 * @param  integer $post_id  The post ID being passed on save.
	 *
	 * @return void
	 */
	function save_checkbox( $post_id ) {

		// Do our nonce comparison.
		if ( empty( $_POST['svo_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['svo_meta_nonce'] ), 'svo_meta_nonce' ) ) {
			return;
		}

		// Run various checks to make sure we aren't doing anything weird.
		if ( false === RKV_SVO_Helper::meta_save_check( $post_id ) ) {
			return;
		}

		// Check against the post types we've allowed.
		if ( ! in_array( get_post_type( $post_id ), RKV_SVO_Helper::get_post_types() ) ) {
			return;
		}

		// Check for the true value being posted.
		if ( ! empty( $_POST['svo-active'] ) ) {
			update_post_meta( $post_id, '_svo_active', sanitize_key( $_POST['svo-active'] ) );
		} else {
			delete_post_meta( $post_id, '_svo_active' );
		}
	}

	// End class.
}


// Call our class.
$RKV_SVO_Admin = new RKV_SVO_Admin();
$RKV_SVO_Admin->init();



