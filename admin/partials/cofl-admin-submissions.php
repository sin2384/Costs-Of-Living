<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.themesbros.com
 * @since      1.0.0
 *
 * @package    Cofl
 * @subpackage Cofl/admin/partials
 */

$user_submissions = get_option( 'cofl_user_submissions' );
?>
<div class="wrap">
	<h2><?php esc_html_e( 'User Submissions', 'cofl' ); ?></h2>
	<?php if ( ! empty( $user_submissions ) ) : ?>
		<?php foreach( $user_submissions as $index => $data ) : ?>
		<hr>
		<form action="admin-post.php" method="POST">
			<?php wp_nonce_field( 'cofl_approve' ); ?>
			<input type="hidden" name="action" value="cofl_approve_submission">
			<input type="hidden" name="index" value="<?php echo absint( $index ); ?>">
			<h3><?php printf( esc_html__( 'New Data For "%s"', 'cofl' ), get_the_title( $data['post_id'] ) ); ?></h3>
			<input type="hidden" name="post_id" value="<?php echo absint( $data['post_id'] ); ?>">
			<?php unset( $data['post_id'] ); ?>
			<table class="widefat">
				<?php foreach( $data as $cat_slug => $cat_data ) : ?>
					<?php $name = $cat_data['name']; unset( $cat_data['name'] ); ?>
					<thead>
						<th><strong><?php echo esc_html( $name ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Original Price', 'cofl' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'Average Price', 'cofl' ); ?></strong></th>
						<th><strong><?php esc_html_e( 'User Submitted', 'cofl' ); ?></strong></th>
					</thead>
					<?php foreach ( $cat_data as $cat_key => $cat_key_data ) : ?>
					<tr>
						<td><?php echo esc_html( $cat_key_data['name'] ); ?></td>
						<td><?php echo cofl_float( $cat_key_data['price_original'] ); ?></td>
						<td><?php echo cofl_float( $cat_key_data['price_avg'] ); ?></td>
						<td><?php echo cofl_float( $cat_key_data['price_submitted'] ); ?></td>
					</tr>
					<input type="hidden" name="<?php echo esc_attr( $cat_key ); ?>" value="<?php echo cofl_float( $cat_key_data['price_submitted'] ); ?>">
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>
			<br>
			<input class="button-primary" type="submit" name="approve" value="<?php esc_html_e( 'Approve', 'cofl' ); ?>">
			<input class="button-primary" type="submit" name="delete" value="<?php esc_html_e( 'Delete', 'cofl' ); ?>">
		</form>
		<br>
		<?php endforeach; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No submissions at the moment.', 'cofl' ); ?></p>
	<?php endif; ?>
</div>
