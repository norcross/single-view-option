<?php
/**
 * Single View Option - Front Module
 *
 * Contains functions only intended to run on the front-end
 *
 * @package Single View Option
 */

/**
 * Start our engines.
 */
class RKV_SVO_Front
{

	/**
	 * Load our metabox items on the admin
	 *
	 * @return void
	 */
	public function init() {

		// Bail on admin.
		if ( is_admin() ) {
			return;
		}

		// Load our actions.
		add_action( 'init',                         array( $this, 'rem_base_canonical'      )           );
		add_action( 'genesis_meta',                 array( $this, 'rem_genesis_canonical'   )           );
		add_action( 'wp_head',                      array( $this, 'add_canonical'           ),  5       );
		add_action( 'template_redirect',            array( $this, 'redirect'                ),  1       );
		add_filter( 'query_vars',                   array( $this, 'query_vars'              )           );
		add_filter( 'the_content',                  array( $this, 'show_view_link'          ),  5       );
		add_filter( 'the_content',                  array( $this, 'view_all_content'        ),  10      );
		add_filter( 'wp_link_pages_args',           array( $this, 'link_args'               )           );
	}

	/**
	 * Remove the default canonical if needed.
	 *
	 * @return void
	 */
	public function rem_base_canonical() {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return;
		}

		// Call the $post global.
		global $post;

		// Bail if we don't have a post ID or content to check.
		if ( empty( $post->ID ) ) {
			return;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( $post->ID ) ) {
			return;
		}

		// If we have not activated it, bail without changing anything.
		if ( false === $check = RKV_SVO_Helper::check_post_active( $post->ID ) ) {
			return;
		}

		// Remove the standard WP canonical.
		remove_action( 'wp_head', 'rel_canonical' );

		// Do our action to allow other canoncical things.
		do_action( 'svo_canonical_remove' );
	}

	/**
	 * Check for Genesis framework and do their canonical thing.
	 */
	public function rem_genesis_canonical() {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return;
		}

		// Call the $post global.
		global $post;

		// Bail if we don't have a post ID or content to check.
		if ( empty( $post->ID ) ) {
			return;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( $post->ID ) ) {
			return;
		}

		// If we have not activated it, bail without changing anything.
		if ( false === $check = RKV_SVO_Helper::check_post_active( $post->ID ) ) {
			return;
		}

		// And remove it.
		remove_action( 'wp_head', 'genesis_canonical', 5 );
	}

	/**
	 * Add back in our 'all' canonical link.
	 *
	 * @return void
	 */
	public function add_canonical() {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return;
		}

		// Call the $post global.
		global $post;

		// Bail if we don't have a post ID or content to check.
		if ( empty( $post->ID ) ) {
			return;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( $post->ID ) ) {
			return;
		}

		// If we have not activated it, return the original canonical.
		if ( false === $check = RKV_SVO_Helper::check_post_active( $post->ID ) ) {
			return;
		}

		// Get my link.
		$link   = RKV_SVO_Helper::all_permalink( $post->ID );

		// And echo out the new canonical.
		echo '<link rel="canonical" href="' . esc_url( $link ) . '" />' . "\n";
	}

	/**
	 * Redirect the 'all' endpoint if not active
	 *
	 * @return void
	 */
	public function redirect() {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return;
		}

		// Call the $wp_query global.
		global $wp_query;

		// If we don't have the query var, just bail.
		if ( ! isset( $wp_query->query['all'] ) ) {
			return;
		}

		// Bail if we don't have a post ID to check.
		if ( empty( $wp_query->post->ID ) ) {
			return;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( $wp_query->post->ID ) ) {
			return;
		}

		// If we have not activated it, redirect to the original URL.
		if ( false === $check = RKV_SVO_Helper::check_post_active( $wp_query->post->ID ) ) {

			// Get our original permalink.
			$link   = get_permalink( $wp_query->post->ID );

			// And process the URL redirect.
			wp_redirect( esc_url_raw( $link ), 301 );
			exit();
		}
	}

	/**
	 * Adds our new /all/ endpoint into the query args.
	 *
	 * @param  array $vars  The existing vars in the array.
	 *
	 * @return array  All the available vars
	 */
	public function query_vars( $vars ) {

		// Check if the 'all' param exists in the array. If not, add it.
		if ( ! in_array( 'all', $vars ) ) {
			$vars[] = 'all';
		}

		// Return our full array.
		return $vars;
	}

	/**
	 * Add a "view as single page" link above the content.
	 *
	 * @param  mixed $content  The original content being passed.
	 *
	 * @return mixed $content  The updated content, without breaks.
	 */
	public function show_view_link( $content ) {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return $content;
		}

		// Check for the enabled filter.
		if ( false === $show = RKV_SVO_Helper::check_view_all() ) {
			return $content;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( get_the_ID() ) ) {
			return $content;
		}

		// Call the $wp_query global.
		global $wp_query;

		// If we are already on the "all" page, or if we have not activated it, return the original content.
		if ( isset( $wp_query->query['all'] ) || false === $check = RKV_SVO_Helper::check_post_active( get_the_ID() ) ) {
			return $content;
		}

		// Call my global $post object.
		$link   = RKV_SVO_Helper::all_permalink( $wp_query->post->ID );

		// Build the text portion
		$text	= '<p class="svo-view-all-link"><a href="' . esc_url( $link ) . '">' . esc_html__( 'View as single page', 'single-view-option' ) . '</a></p>';

		return apply_filters( 'svo_view_all_link_text', $text ) . $content;
	}

	/**
	 * Run content filter to display all content, without page breaks.
	 *
	 * @param  mixed $content  The original content being passed.
	 *
	 * @return mixed $content  The updated content, without breaks.
	 */
	public function view_all_content( $content ) {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return $content;
		}

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( get_the_ID() ) ) {
			return $content;
		}

		// Call the $wp_query global.
		global $wp_query;

		// If we have not activated it, return the original content.
		if ( ! isset( $wp_query->query['all'] ) || false === $check = RKV_SVO_Helper::check_post_active( get_the_ID() ) ) {
			return $content;
		}

		// Return the full content setup.
		return wpautop( wptexturize( $wp_query->post->post_content ) );
	}

	/**
	 * Remove the paginated output if requested.
	 *
	 * @param  array $args  The original pagination args.
	 *
	 * @return array $args  The updated pagination args.
	 */
	public function link_args( $args ) {

		// Bail if we aren't on a supported type.
		if ( false === $types = RKV_SVO_Helper::check_post_types() ) {
			return $args;
		}

		// Call the $wp_query global.
		global $wp_query;

		// Check the content for the `nextpage` tag.
		if ( false === $tag = RKV_SVO_Helper::check_post_content( $wp_query->post->ID ) ) {
			return $args;
		}

		// If we have not activated it, return the original content.
		if ( ! isset( $wp_query->query['all'] ) || false === $check = RKV_SVO_Helper::check_post_active( $wp_query->post->ID ) ) {
			return $args;
		}

		// Set the 'echo' arg to false.
		$args['echo'] = 0;

		// Return our array of args.
		return $args;
	}

	// End the class.
}


// Call our class.
$RKV_SVO_Front = new RKV_SVO_Front();
$RKV_SVO_Front->init();



