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

$options     = get_option( 'cofl_data' );
$submit_page = get_option( 'cofl_submit_page' );
?>

<div class="wrap">
	<div class="cofl-cat">
		<h3 class="cofl-cat__title"><?php esc_html_e( 'Costs of Living Settings', 'cofl' ); ?></h3>
		<div class="cofl-cat__content">
			<form action="admin-post.php" method="POST">
				<br>
				<?php $pages = get_posts( ['post_type' => 'page'] ); ?>
				<?php if ( $pages ) : ?>
					<label for="cofl_submit_page"><?php esc_html_e( 'Please select the prices submit page. When user clicks "edit" it will be taken to this, selected page to edit items.', 'cofl' ); ?></label>
					<br>
					<select class="smallfat" name="cofl_submit_page" id="cofl_submit_page">
						<option value=""><?php esc_html_e( 'User submissions disabled.', 'cofl' ); ?></option>
						<?php foreach ( $pages as $page ) : ?>
							<option value="<?php echo absint( $page->ID ); ?>" <?php selected( $page->ID, $submit_page ); ?>><?php echo esc_html( $page->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php else : ?>
					<?php esc_html_e( 'Please create some pages', 'cofl' ); ?>
				<?php endif; ?>
				<input type="hidden" name="action" value="cofl_save_options">
				<?php wp_nonce_field( 'cofl' ); ?>
				<input type="submit" value="<?php esc_html_e( 'Save', 'cofl' ); ?>" class="button-primary">
			</form>

			<form action="admin-post.php" method="POST" class="cofl-create-category">
				<input type="hidden" name="action" value="cofl_save_options">
				<?php wp_nonce_field( 'cofl' ); ?>
				<br>
				<label for="add-new"><?php esc_html_e( 'Add New Category', 'cofl' ); ?></label>
				<br>
				<input required id="add-new" type="text" name="category">
				<input type="submit" value="<?php esc_html_e( 'Save', 'cofl' ); ?>" class="button-primary">
			</form>
		</div>
	</div>

	<div class="cofl-categories">
	<?php if ( ! empty( $options ) ) : ?>
		<?php foreach( $options as $cat_slug => $category ) : ?>
			<div id="<?php echo esc_attr( $cat_slug ); ?>" class="cofl-cat">
				<h3 class="cofl-cat__title">
					<span class="dashicons dashicons-move"></span> <?php echo esc_html( $category['name'] ); ?>
				</h3>
				<span class="cofl-cat__remove dashicons dashicons-trash"></span>
				<?php unset( $category['name'] ); ?>
				<div class="cofl-cat__content hide-content">
					<form action="admin-post.php" method="POST" class="form-cofl-items">
						<input type="hidden" name="action" value="cofl_save_options">
						<?php wp_nonce_field( 'cofl' ); ?>
						<ul class="sortable">
							<?php if ( ! empty( $category ) ) : ?>
								<?php foreach( $category as $key => $item ) : ?>
									<li id="<?php echo esc_attr( $key ); ?>" class="sortable__item">
										<span class="dashicons dashicons-move"></span>
										<?php echo esc_html( $item ); ?>
										<a class="remove_link" data-slug="<?php echo esc_attr( $key ); ?>" href="#"><small><?php esc_html_e( 'Delete', 'cofl' ); ?></small></a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						</ul>
						<input type="hidden" name="item_category" value="<?php echo esc_attr( $cat_slug ) ?>">
						<input type="hidden" name="add_items">
						<label for="<?php echo esc_attr( $cat_slug ); ?>_item_name"><?php esc_html_e( 'Item Name:', 'cofl' ); ?></label>
						<input class="widefat" id="<?php echo esc_attr( $cat_slug ); ?>_item_name" type="text" name="item_name">
						<br>
						<br>
						<input type="submit" value="<?php esc_html_e( 'Add New Item', 'cofl' ); ?>" class="button-secondary">
					</form>
				</div><!-- .cofl-cat__content -->
			</div><!-- .cofl-cat -->
		<?php endforeach; ?>
	<?php endif; ?>
	</div><!-- .cofl-categories -->
</div><!-- .wrap -->