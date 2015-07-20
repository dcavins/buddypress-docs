<?php

/**
 * BuddyPress Docs Directory
 *
 * @package BuddyPress_Docs
 * @since 1.2
 */

?>

<?php get_header( 'buddypress' ); ?>

	<?php do_action( 'bp_before_directory_docs_page' ); ?>

	<div id="content">
		<div class="padder">

		<?php if ( ! did_action( 'template_notices' ) ) : ?>
			<?php do_action( 'template_notices' ) ?>
		<?php endif ?>

		<?php do_action( 'bp_before_directory_docs' ); ?>

		<h3><?php _e( 'Docs Directory', 'bp-docs' ); ?></h3>

		<?php
		if ( apply_filters( 'bp_docs_use_legacy_directory_template', false ) ) {
			$template = 'legacy/docs-loop.php';
		} else {
			$template = 'docs-loop.php';
		}
		include( bp_docs_locate_template( $template ) );
		?>

		<?php do_action( 'bp_after_directory_docs' ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php do_action( 'bp_after_directory_docs_page' ); ?>

<?php bp_docs_get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>

