<?php
/**
 * The file that defines helper functions
 *
 * @link       http://www.themesbros.com
 * @since      1.0.0
 *
 * @package    Cofl
 * @subpackage Cofl/includes
 *
 * @since      1.0.0
 * @package    Cofl
 * @subpackage Cofl/includes
 * @author     Sinisa Nikolic <sin2384@gmail.com>
 */

/**
 * @since 	1.0.0
 */
function cofl_get_all_keys() {

	if ( ! $options = get_option( 'cofl_data' ) ) {
		return;
	}

	$keys = [];

	foreach ( $options as $category => $data ) {
		foreach ( $data as $key => $data ) {
			$keys[] = $key;
		}
	}

	return $keys;
}

/**
 * @since 	1.0.0
 */
function cofl_get_title_keys() {
	if ( ! $options = get_option( 'cofl_data' ) ) {
		return;
	}

	return array_keys( $options );
}

/**
 * @since 	1.0.0
 */
function cofl_get_cat_title_by_key( $key ) {
	if ( ! $options = get_option( 'cofl_data' ) ) {
		return;
	}
	return isset( $options[ $key ] ) ? $options[ $key ]['name'] : '';
}

/**
 * @since 	1.0.0
 */
function cofl_get_item_title_by_key( $cat_key, $item_key ) {
	if ( ! $options = get_option( 'cofl_data' ) ) {
		return;
	}
	return isset( $options[ $cat_key ] ) ? $options[ $cat_key ][ $item_key ] : '';
}

/**
 * @since 	1.0.0
 */
function cofl_get_item_price( $post_id, $item, $type = 'original' ) {

	if ( ! $price = get_post_meta( $post_id, $item ) ) {
		return;
	}

	if ( 'original' == $type ) {
		return is_array( $price ) ? $price[0] : $price;
	}

	if ( 'avg' == $type ) {
		return is_array( $price ) ? number_format( array_sum( $price ) / count( $price ), 2 ) : $price;
	}
}

/**
 * @since 	1.0.0
 */
function cofl_get_keys_by_cat( $cat ) {
	if ( ! $options = get_option( 'cofl_data' ) ) {
		return;
	}
	return isset( $options[ $cat ] ) ? $options[ $cat ] : '';
}

/**
 * @since 	1.0.0
 */
function cofl_get_submit_link( $submit_page, $category ) {

	if ( ! $submit_page || ! $category ) {
		return;
	}

	$query_args = array(
		'page_id' => $submit_page,
		'post_id' => get_the_ID(),
		'cat'	  => $category
	);

	$url = add_query_arg( $query_args, get_permalink( $submit_page ) );

	return sprintf(
		'<a href="%s">%s</a>',
		esc_url( add_query_arg( $query_args, get_permalink( get_the_ID() ) ) ),
		esc_html__( '[EDIT]', 'cofl' )
	);
}

/**
 * Checks whether the category is published in some post.
 *
 * @since  1.0.0
 *
 * @param  int 		$post_id
 * @param  string 	$cat
 * @return bool
 */
function cofl_is_cat_published( $post_id, $cat ) {
	return get_post_meta( $post_id, "cofl_{$cat}_display", true );
}

/**
 * Removes "cofl_" from the custom field key name.
 * "cofl_" is added to avoid naming conflict with other custom fields.
 *
 * @param  string $item
 * @return string
 */
function cofl_get_key_basename( $item ) {
	return str_replace( 'cofl_', '', $item );
}

function cofl_float( $num ) {
	return filter_var( $num, FILTER_VALIDATE_FLOAT );
}