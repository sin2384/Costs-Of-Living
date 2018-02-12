<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.themesbros.com
 * @since      1.0.0
 *
 * @package    Cofl
 * @subpackage Cofl/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cofl
 * @subpackage Cofl/admin
 * @author     Sinisa Nikolic <sin2384@gmail.com>
 */
class Cofl_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cofl-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {
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

		if ( 'toplevel_page_cofl' != $hook ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/cofl-admin.js',
			array( 'jquery', 'jquery-ui-sortable' ),
			$this->version,
			true
		);

		wp_localize_script( $this->plugin_name, 'cofl', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'nonce' )
		]);

	}

	/**
	 * Registers plugin admin pages.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_page() {

		/* Add cofl menu page. */
		add_menu_page(
			esc_html__( 'Costs of Living', 'cofl' ),
			esc_html__( 'Costs of Living', 'cofl' ),
			'manage_options',
			'cofl',
			array( $this, 'render_admin' )
		);

		/* Add cofl submenu page. */
		add_submenu_page(
			'cofl',
			esc_html__( 'Submissions', 'cofl' ),
			esc_html__( 'Submissions', 'cofl' ),
			'manage_options',
			'cofl-submission',
			array( $this, 'render_submissions' )
		);
	}

	/**
	 * Displays plugin admin page.
	 *
	 * @since 	1.0.0
	 */
	public function render_admin() {
		include plugin_dir_path( __FILE__ ) . 'partials/cofl-admin-display.php';
	}

	/**
	 * Displays users submissions.
	 *
	 * @since 	1.0.0
	 */
	public function render_submissions() {
		include plugin_dir_path( __FILE__ ) . 'partials/cofl-admin-submissions.php';
	}

	/**
	 * Saves options.
	 *
	 * @since 	1.0.0
	 */
	public function save_options() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_admin_referer( 'cofl' );

		$options = get_option( 'cofl_data' );

		if ( isset( $_POST['category'] ) && ! empty( $_POST['category'] ) ) {
			$slug = sanitize_key( $_POST['category'] );
			$options[ $slug ]['name'] = sanitize_text_field( $_POST['category'] );
			update_option( 'cofl_data', $options );
		}

		if ( isset( $_POST['cofl_submit_page'] ) ) {
			update_option( 'cofl_submit_page', absint( $_POST['cofl_submit_page'] ) );
		}

		wp_redirect( add_query_arg( 'page', 'cofl', admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Removes price category.
	 *
	 * @since 	1.0.0
	 */
	public function remove_category() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_ajax_referer( 'nonce', 'security' );

		$cat_key = isset( $_POST['cat_key'] ) ? $_POST['cat_key'] : '';
		$options = get_option( 'cofl_data' );

		if ( isset( $options[ $cat_key ] ) ) {
			if ( $posts = get_posts() ) {
				$all_keys = cofl_get_all_keys();
				foreach ( $posts as $post ) {
					/* Delete display option (e.g. Display market?). */
					delete_post_meta( $post->ID, "cofl_{$cat_key}_display" );
					foreach ( $options[ $cat_key ] as $key => $value ) {
						delete_post_meta( $post->ID, "cofl_{$key}" );
					}
				}
			}

			/* Update options. */
			unset( $options[ $cat_key ] );
			update_option( 'cofl_data', $options );

			/*Update submissions. */
			if ( $user_submissions = get_option( 'cofl_user_submissions' ) ) {
				foreach( $user_submissions as $key => $data ) {
					if ( isset( $data[ $cat_key ] ) ) {
						unset( $user_submissions[ $key ] );
					}
				}
				update_option( 'cofl_user_submissions', $user_submissions );
			}

			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

		wp_die();
	}

	/**
	 * Saves category order.
	 *
	 * @since 	1.0.0
	 */
	public function save_category_order() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_ajax_referer( 'nonce', 'security' );

		$order          = isset( $_POST['order'] ) ? $_POST['order'] : '';
		$sorted_options = [];

		if ( $options = get_option( 'cofl_data' ) ) {
			foreach( $order as $order ) {
				if ( isset( $options[ $order ] ) ) {
					$sorted_options[ $order ] = $options[ $order ];
				}
			}
		}
		$options = $sorted_options;

		update_option( 'cofl_data', $options );

		wp_send_json_success();


		wp_die();
	}

	/**
	 * Adds item to the category.
	 *
	 * @since 	1.0.0
	 */
	public function add_cat_item() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_ajax_referer( 'nonce', 'security' );

		$cat_key = isset( $_POST['cat_key'] ) ? $_POST['cat_key'] : '';
		$item    = isset( $_POST['item'] ) ? $_POST['item'] : '';
		$options = get_option( 'cofl_data' );

		if ( ! isset( $options[ $cat_key ] ) || ! $item ) {
			wp_send_json_error();
			wp_die();
		}

		$current_cat = $options[ $cat_key ];
		unset( $current['name'] );

		ksort( $current_cat );

		$last_key    = end( array_keys( $current_cat ) );
		$item_number = 0;

		if ( preg_match( '/[\d]+$/', $last_key, $matches ) ) {
			$item_number = $matches[0] + 1; // Will create cat_key_55 for example.
		}

		$item_key = sprintf( '%s_%d', sanitize_key( $cat_key ), $item_number );

		$current_cat[ $item_key ] = sanitize_text_field( $item );
		$options[ $cat_key ][ $item_key ] = $item;

		update_option( 'cofl_data', $options );

		$data = sprintf(
			'<li id="%1$s"><span class="dashicons dashicons-move"></span> %2$s <a class="remove_link" data-slug="%1$s" href="#"><small>%3$s</small></a></li>',
			$item_key,
			esc_html( $item ),
			esc_html__( 'Delete', 'cofl' )
		);

		wp_send_json_success( $data );
		wp_die();
	}

	/**
	 * Removes item from the category.
	 *
	 * @since 	1.0.0
	 */
	public function remove_cat_item() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_ajax_referer( 'nonce', 'security' );

		$cat_key = isset( $_POST['cat_key'] ) ? $_POST['cat_key'] : '';
		$item    = isset( $_POST['item'] ) ? $_POST['item'] : '';
		$options = get_option( 'cofl_data' );

		if ( ! isset( $options[ $cat_key ] ) || ! $cat_key || ! $item ) {
			wp_send_json_error();
			wp_die();
		}

		unset( $options[ $cat_key ][ $item ] );
		update_option( 'cofl_data', $options );

		if ( $posts = get_posts() ) {
			foreach ( $posts as $post ) {
				delete_post_meta( $post->ID, "cofl_{$item}" );
			}
		}

		/*Update submissions. */
		if ( $user_submissions = get_option( 'cofl_user_submissions' ) ) {
			foreach( $user_submissions as $key => $data ) {
				if ( isset( $data[ $cat_key ][ "cofl_{$item}" ] ) ) {
					unset( $user_submissions[ $key ][ $cat_key ][ "cofl_{$item}" ] );
				}
			}
			update_option( 'cofl_user_submissions', $user_submissions );
		}

		wp_send_json_success( [$cat_key, $item]);
		wp_die();
	}

	/**
	 * Saves item order in the category.
	 *
	 * @since 	1.0.0
	 */
	public function update_cat_item_order() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_ajax_referer( 'nonce', 'security' );

		$cat_key   = isset( $_POST['cat_key'] ) ? $_POST['cat_key'] : '';
		$new_order = isset( $_POST['order'] ) ? $_POST['order'] : '';
		$options   = get_option( 'cofl_data' );

		if ( ! isset( $options[ $cat_key ] ) || ! $new_order ) {
			wp_send_json_error();
			wp_die();
		}

		$sorted_array = [];

		foreach( $new_order as $item_slug ) {
			$sorted_array[ $cat_key ][ $item_slug ] = $options[ $cat_key ][ $item_slug ];
		}

		$sorted_array[ $cat_key ]['name'] = $options[ $cat_key ]['name'];
		$sorted_array = wp_parse_args( $sorted_array, $options );

		update_option( 'cofl_data', $sorted_array );

		wp_send_json_success();
		wp_die();
	}

	/**
	 * Register meta box.
	 *
	 * @since  1.0.0
	 */
	public function add_meta_boxes() {

		$options = get_option( 'cofl_data' );

		if ( ! $options ) {
			return;
		}

		add_meta_box(
			"cofl_meta_box",
			esc_html__( 'Costs Of Living', 'cofl' ),
			[ $this, 'render_meta_box' ],
			'post',
			'advanced',
			'high',
			$options
		);
	}

	/**
	 * Register meta box.
	 *
	 * @since  1.0.0
	 */
	function render_meta_box( $options, $callback_args ) {

		if ( ! isset( $callback_args['args'] ) || empty( $callback_args['args'] ) ) {
			return;
		}

		wp_nonce_field( 'cofl_action_nonce', 'cofl_nonce' );

		unset( $callback_args['args']['name'] );
		$id = get_the_ID();
		?>
		<?php foreach ( $callback_args['args'] as $key => $data ) : ?>
			<br>
			<table class="widefat">
				<?php $name = $data['name']; unset( $data['name'] ); ?>
				<thead>
					<th>
						<label for="<?php echo esc_attr( "cofl_{$key}_display" ); ?>">
							<?php printf( esc_html__( 'Display %s?', 'cofl' ), esc_html( strtolower( $name ) ) ); ?>
						</label>
						<?php $display = get_post_meta( $id, "cofl_{$key}_display", true ); ?>
						<input id="<?php echo esc_attr( "cofl_{$key}_display" ); ?>" type="checkbox" name="<?php echo esc_attr( "cofl_{$key}_display" ); ?>" <?php checked( $display, 1 ); ?>>
					</th>
					<th><?php esc_html_e( 'Original Price', 'cofl' ); ?></th>
					<th><?php esc_html_e( 'Average Price', 'cofl' ); ?></th>
				</thead>
				<?php foreach ( $data as $key => $item ) : ?>
				<tr>
					<td>
						<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $item ); ?></label>
					</td>
					<td>
						<?php $val = get_post_meta( $id, "cofl_{$key}", true ); ?>
						<input style="max-width: 70px;" id="<?php echo esc_attr( $key ); ?>" type="number" name="cofl_<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( is_array( $val ) ? $val[0] : $val ); ?>" step="0.01" min="0"> USD
					</td>
					<td>
						<?php $price = cofl_get_item_price( $id, "cofl_{$key}", 'avg' ); ?>
						<?php echo ! empty( $price ) ? esc_html( $price ) : 'N/A'; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</table>
		<?php endforeach; ?>
		<?php
	}

	/**
	 * Saves meta box.
	 *
	 * @since 	1.0.0
	 */
	function save_meta_fields( $post_id ) {

		if ( ! isset( $_POST['cofl_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['cofl_nonce'], 'cofl_action_nonce' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'manage_options', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['post_type'] ) && 'post' !== $_POST['post_type'] ) {
			return;
		}

		if ( $keys = cofl_get_all_keys() ) {
			foreach( $keys as $key ) {
				if ( isset( $_POST[ "cofl_{$key}" ] ) ) {
					update_post_meta( $post_id, "cofl_{$key}", cofl_float( $_POST[ "cofl_{$key}" ] ) );
				}
			}
		}

		if ( $title_keys = cofl_get_title_keys() ) {
			foreach ( $title_keys as $title ) {
				if ( isset( $_POST["cofl_{$title}_display"] ) ) {
					update_post_meta( $post_id, "cofl_{$title}_display", (bool)$_POST["cofl_{$title}_display"] );
				} else {
					update_post_meta( $post_id, "cofl_{$title}_display", '' );
				}
			}
		}

	}

	/**
	 * Action necessary for saving submissions approvals.
	 *
	 * @since 	1.0.0
	 */
	public function submenu_save() {
		add_action( 'admin_post_cofl_approve_submission', array( $this, 'approve_submission' ) );
	}

	/**
	 * Approve submision.
	 *
	 * @since 	1.0.0
	 */
	function approve_submission() {

		if ( ! current_user_can( 'manage_options' ) )	{
			wp_die( 'Not allowed!' );
		}

		check_admin_referer( 'cofl_approve' );

		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';

		if ( ! $post_id ) {
			wp_die( 'Post ID missing.' );
		}

		/* Array index, used to unset array once approved. */
		$index = isset( $_POST['index'] ) ? $_POST['index'] : '';

		if ( '' === $index ) {
			wp_die();
		}

		$action = isset( $_POST['approve'] ) ? 'approve' : 'delete';

		unset(
			$_POST['post_id'],
			$_POST['index'],
			$_POST[ $action ]
		);

		$submissions = get_option( 'cofl_user_submissions' );

		if ( 'approve' === $action ) {
			foreach( $_POST as $key => $price ) {
				add_post_meta( $post_id, $key, $price );
			}
		}

		unset( $submissions[ $index ] );
		update_option( 'cofl_user_submissions', $submissions );

		wp_redirect( add_query_arg( 'page', 'cofl-submission', admin_url( 'admin.php' ) ) );
		exit;
	}

}
