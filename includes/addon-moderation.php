<?php

/**
 * Provide some toold for moving docs into and out of "pending" status.
 *
 * @since 2.0.0
 */
class BP_Docs_Moderation {

	public $pending_status = 'bp_docs_pending';

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Hook the Folders functionality into Docs.
	 *
	 * @since 2.0.0
	 */
	public function setup_hooks() {
		// Register a custom status that's like the built-in "pending" status.
		add_action( 'bp_docs_init', array( $this, 'register_docs_pending_status' ), 9 );

		add_filter( 'display_post_states', array( $this, 'add_moderated_label' ), 10, 2 );

		// Our pending bp_docs need to have a post name to interact with them.
		// add_action( 'save_post_bp_doc', array( $this, 'maybe_force_apply_post_name'), 10, 3 );
	}

	/**
	 * Register custom status for pending BP Docs.
	 *
	 * @since 2.0.0
	 */
	public function register_docs_pending_status() {
		$args = array(
			'label'                     => _x( 'Awaiting Moderation', 'Status General Name', 'buddypress-docs' ),
			'label_count'               => _n_noop( 'Awaiting Moderation (%s)',  'Pending (%s)', 'buddypress-docs' ),
			'public'                    => current_user_can( 'bp_moderate' ),
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'exclude_from_search'       => true,
		);
		register_post_status( $this->pending_status, $args );
	}

	/**
	 * In the BP Docs list in wp-admin, add a label to posts that require moderation.
	 *
	 * @since 2.0.0
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 */
	function add_moderated_label( $post_states, $post ) {
		if ( $this->pending_status == $post->post_status ) {
			$post_states[] = __( 'Awaiting Moderation', 'buddypress-docs' );
		}
		return $post_states;
	}

	/**
	 * Our pending posts need to have a post_name, which WP tries to prevent.
	 *
	 * @since 2.0.0
	 *
	 * @param int     $post_ID Post ID.
	 * @param WP_Post $post    Post object.
	 * @param bool    $update  Whether this is an existing post being updated or not.
	 */
	public function maybe_force_apply_post_name( $post_id, $post, $update ) {
		global $wpdb;

		if ( 'pending' == $post->post_status && empty( $post->post_name ) ) {
			$doc_slug = wp_unique_post_slug(
				sanitize_title( $post->post_title, $post_id ),
				$post_id,
				'publish', // This is a lie, because pending posts don't get a slug.
				bp_docs_get_post_type_name(),
				$post->post_parent
			 );

			$wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_name = %s WHERE ID = %s AND post_type = %s",
				$doc_slug,
				$post_id,
				bp_docs_get_post_type_name()
			) );
		}
	}
}

/** Utility functions ********************************************************/
