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
?>

<?php
$cat     = isset( $_GET['cat'] ) ? sanitize_key( $_GET['cat'] ) : '';
$post_id = isset( $_GET['post_id'] ) ? sanitize_key( $_GET['post_id'] ) : '';

/* Check if category is published in this post. */
if ( ! cofl_is_cat_published( $post_id, $cat ) ) {
	return;
}

$cat_keys   = cofl_get_keys_by_cat( $cat );
$return_url = add_query_arg( 'status', '1#cofl-thanks', get_permalink( $post_id ) );

?>

<?php if ( $cat_keys && $cat && $post_id ) : ?>
	<form action="<?php echo esc_url( $return_url ); ?>" method="POST">
		<?php wp_nonce_field( 'cofl_submit', 'security', false ); ?>
		<input type="hidden" name="cofl_frontend_submit">
		<input type="hidden" name="post_id" value="<?php echo absint( $post_id ); ?>">
		<input type="hidden" name="cat[cat_key]" value="<?php echo esc_attr( $cat ); ?>">
		<?php $id = get_the_ID(); ?>
		<?php foreach( $cat_keys as $key => $name ) : ?>
			<?php if ( 'name' === $key ) continue; ?>
			<?php if ( ! $value = get_post_meta( $post_id, "cofl_{$key}", true ) ) continue; ?>
			<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></label>
			<input id="<?php echo esc_attr( $key ); ?>" type="number" name="cat[cofl_<?php echo esc_attr( $key ); ?>]" min="0" step="0.01">
			<br>
		<?php endforeach; ?>
		<input type="submit" value="<?php esc_html_e( 'Submit', 'cofl' ); ?>">
	</form>
<?php endif; ?>
