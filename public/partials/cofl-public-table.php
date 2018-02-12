<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://www.themesbros.com
 * @since      1.0.0
 *
 * @package    Cofl
 * @subpackage Cofl/public/partials
 */


/* Show only on single pages. */
if ( ! is_single() ) {
	return;
}

/* Get plugin options. */
if ( ! $options = get_option( 'cofl_data' ) ) {
	return;
}

$submit_page = get_option( 'cofl_submit_page' );
?>

<?php if ( isset( $_GET['status'] ) ) : ?>
	<h2 id="cofl-thanks"><?php esc_html_e( 'Thanks for your submission! We\'ll review it.', 'cofl' ); ?></h2>
<?php endif; ?>

<?php if ( ! empty( $options ) ) : ?>
	<table class="responsive-table">
	<?php foreach ( $options as $category => $items ) : ?>
		<?php $cat_name = $items['name']; ?>
		<?php unset( $items['name'] ); ?>
		<?php $category_display = get_post_meta( get_the_ID(), "cofl_{$category}_display", true ); ?>
		<?php if ( $category_display ) : ?>
			<thead>
				<tr>
					<th><?php echo esc_html( $cat_name ); ?></th>
					<th><?php printf( esc_html__( 'Price %s', 'cofl' ), cofl_get_submit_link( $submit_page, $category ) ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $items as $item => $name ) : ?>
				<?php $price = cofl_get_item_price( get_the_ID(), "cofl_{$item}", 'avg' ); ?>
				<?php if ( $price > 0 ) : ?>
					<tr>
						<td data-label="<?php esc_html_e( 'Item', 'cofl' ); ?>"><?php echo esc_html( $name ); ?></td>
						<td data-label="<?php esc_html_e( 'Price', 'cofl' ); ?>"><?php echo esc_html( $price ); ?> USD</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
			</tbody>
		<?php endif; ?>
	<?php endforeach; ?>
	</table>
<?php endif; ?>