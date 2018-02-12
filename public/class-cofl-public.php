<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.themesbros.com
 * @since      1.0.0
 *
 * @package    Cofl
 * @subpackage Cofl/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cofl
 * @subpackage Cofl/public
 * @author     Sinisa Nikolic <sin2384@gmail.com>
 */
class Cofl_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cofl_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cofl_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cofl-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cofl_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cofl_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cofl-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Sets form for user on submit page.
	 *
	 * @since 	1.0.0
	 */
	public function set_user_form( $content ) {

		if ( ! is_page() ) {
			return $content;
		}

		$form = '';
		$id   = get_option( 'cofl_submit_page' );

	    if ( $id && is_page( $id ) ) {
	    	ob_start();
	        include plugin_dir_path( __FILE__ ) . 'partials/cofl-public-form.php';
	        $form = ob_get_clean();
	    }
	    return $content . $form;
	}

	/**
	 * Register shortcodes.
	 *
	 * @since 	1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'cofl', array( $this, 'shortcode_render_table' ) );
	}

	/**
	 * Shortcode for displaying prices in table.
	 *
	 * @since 	1.0.0
	 */
	public function shortcode_render_table( $content ) {
		ob_start();
		include plugin_dir_path( __FILE__ ) . 'partials/cofl-public-table.php';
		$data = ob_get_clean();
		return $content . $data;
	}

	/**
	 * Submit prices from front end.
	 *
	 * @since 	1.0.0
	 */
	function frontend_submit_prices() {

		/* Check if necessary data has been sent. */
		if ( ! isset( $_POST['cofl_frontend_submit'] ) || ! isset( $_POST['cat'] ) || ! isset( $_POST['post_id'] ) ) {
			return;
		}

		/* Check nonce. */
		if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'cofl_submit' ) ) {
			return;
		}

		/* Check if user changed hidden input of the post_id. */
		if ( $_GET['p'] != $_POST['post_id'] ) {
			return;
		}

		$cat_key = $_POST['cat']['cat_key'];
		$options = get_option( 'cofl_data' );

		/* Check if the category user sent actually exists. */
		if ( ! isset( $options[ $cat_key ] ) ) {
			return;
		}

		/* Check if category user sent is published. */
		if ( ! cofl_is_cat_published( $_POST['post_id'], $cat_key ) ) {
			return;
		}

		$cat_data = ['name' => cofl_get_cat_title_by_key( $_POST['cat']['cat_key'] )];
		unset( $_POST['cat']['cat_key'] );

		foreach ( $_POST['cat'] as $item => $value ) {

			/* Check if items exist in this category. */
			if ( ! isset( $options[ $cat_key ][ cofl_get_key_basename( $item ) ] ) ) {
				continue;
			}

			$cat_data[ $item ] = [
				'name'            => sanitize_text_field( cofl_get_item_title_by_key( $cat_key, cofl_get_key_basename( $item ) ) ),
				'price_original'  => cofl_float( cofl_get_item_price( $_POST['post_id'], $item, 'original') ),
				'price_avg'       => cofl_float( cofl_get_item_price( $_POST['post_id'], $item, 'avg') ),
				'price_submitted' => cofl_float( $value ),
			];

			/* If there's no value submitted for the item, unset it. No need to render in the admin. */
			if ( $value <= 0 ) {
				unset( $cat_data[ $item ] );
			}
		}

		/*
		 * If there's only 1 item (or no items) in this array - it's ['name' => 'Cat name'].
		 * In this case, there are no prices to send.
		 */
		if ( count( $cat_data ) <= 1 ) {
			return;
		}

	 	$formatted_data = array(
			'post_id' => $_POST['post_id'],
			$cat_key  => $cat_data
	 	);

		$submitted_data   = get_option( 'cofl_user_submissions' );
		$submitted_data[] = $formatted_data;
	 	update_option( 'cofl_user_submissions', $submitted_data );

		$admin_email  = get_option( 'admin_email' );

		$email = wp_mail(
			$admin_email,
			sprintf(
				esc_html__( 'New prices submission for %s', 'cofl' ),
				get_the_title( $_POST['post_id'] )
			),
			sprintf(
				esc_html__( "Howdy!\nNew prices submitted for %s. Please review it.\n%s", 'cofl' ),
				get_the_title( $_POST['post_id'] ),
				add_query_arg( 'page', 'cofl-submission', admin_url( 'admin.php' ) )
			)
		);

	}

}
