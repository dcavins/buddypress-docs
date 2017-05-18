<?php

/**
 * Provide some toold for moving docs into and out of "pending" status.
 *
 * @since 2.0.0
 */
class BP_Docs_Moderation {

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
		// Our pending bp_docs need to have a post name to interact with them.
		add_action( 'save_post_bp_doc', array( $this, 'maybe_force_apply_post_name'), 10, 3 );
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
