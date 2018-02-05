<?php
/**
 * The admin-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CF7_Booking
 * @subpackage CF7_Booking/admin
 */

/**
 * The admin-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-facing stylesheet and JavaScript.
 *
 * @package    CF7_Booking
 * @subpackage CF7_Booking/admin
 * @author     Your Name <email@example.com>
 */
class CF7_Booking_Admin {
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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		/**
		 * Summary.
		 * Description.
		 *
		 * @var $plugin_name Description.
		 */
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			// give warning if contact form 7 is not active.
			function cf7pp_my_admin_notice() {
				?>
				<div class="error">
					<p><?php _e( '<b>This is sad :(</b> Contact Form 7 is not installed and / or active! Booking form won\'t work', 'cf7pp' ); ?></p>
					</div>
					<?php
			}
				add_action( 'admin_notices', 'cf7pp_my_admin_notice' );
		} else {
			add_filter( 'wpcf7_init', array( $this, 'do_filter_sc' ) );
			add_action( 'wpcf7_mail_sent', array( $this, 'update_bookings' ) );
		}
	}
	/**
	 * Do shortcode thingi.
	 *
	 * @since    1.0.0
	 * @param object $cfdata for getting submission object.
	 */
	public function update_bookings( $cfdata ) {
		/**
		 * This gets contactform data before sending mail.
		 *
		 * @param object $cfdata for getting submission object.
		 */
		global $wpdb;
		$table_name = $wpdb->prefix . 'cf7_booking';
		if ( ! isset( $cfdata->posted_data ) && class_exists( 'WPCF7_Submission' ) ) {
			$submission = WPCF7_Submission::get_instance();
			if ( $submission ) {
				$formdata = $submission->get_posted_data();
			}
		} elseif ( isset( $cfdata->posted_data ) ) {
			$formdata = $cfdata->posted_data;
		} else {
			return $cfdata;
		}
		if ( $formdata['bookings'] ) {
			$booked = $formdata['bookings'];
			foreach ( $booked as $b ) {
				$wpdb->query( "update $table_name set status='1' where id=$b" );
			}
		}
		return $cfdata;
	}
	/**
	 * Do Register a tag for parent plugin.
	 *
	 * @since    1.0.0
	 */
	public function do_filter_sc() {
		/**
		 * Description.
		 */
		wpcf7_add_form_tag(
			array( 'ticket_book_cf7' ),
			array( $this, 'do_ticket_book_cf7' )
		);
	}
	/**
	 * Do shortcode.
	 *
	 * @since    1.0.0
	 * @param object $tag for so called shortcode.
	 */
	public function do_ticket_book_cf7( $tag ) {
		/**
		 * Lets check if form has booking data.
		 */
		$html = '';
		if ( empty( $tag->type ) ) {
			return '';
		}

		if ( 'ticket_book_cf7' === $tag->type ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'cf7_booking';
			$res        = wp_cache_get( 'cf7_list_result' );
			if ( false === $res ) {
				$sql = "select * from $table_name";
				$res = $wpdb->get_results( $sql, 'ARRAY_A' );
				wp_cache_set( 'cf7_list_result', $res );
			}
			$booked = wp_cache_get( 'cf7_booked_result' );
			if ( false === $booked ) {
				$sql    = "select id from $table_name where status='1'";
				$booked = $wpdb->get_results( $sql, 'ARRAY_N' );
				wp_cache_set( 'cf7_booked_result', $booked );
			}
			$booked_array = [];
			foreach ( $booked as $b ) {
				$booked_array[] = $b[0];
			}
			// This draws the check boxes.
			$html .= "<div class='booking_element'>";
			foreach ( $res as $r ) {
				$disabled = '';
				if ( in_array( $r['id'], $booked_array ) ) {
					$disabled = 'disabled';}
				$html .= '<label><input ' . $disabled . ' type="checkbox" name="bookings[]" value="' . esc_html( $r['id'] ) . '" /><span></span></label>';
			}
			$html .= '</div>';
		}
			return $html;
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		 * Summary.
		 * Description.
		 *
		 * @var $plugin_name Description.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-booking-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in CF7_Booking_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The CF7_Booking_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-booking-admin.js', array( 'jquery' ), $this->version, false );

	}



}
