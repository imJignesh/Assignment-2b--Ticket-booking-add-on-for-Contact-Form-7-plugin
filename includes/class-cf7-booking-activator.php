<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    CF7_Booking
 * @subpackage CF7_Booking/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CF7_Booking
 * @subpackage CF7_Booking/includes
 * @author     Your Name <email@example.com>
 */
class CF7_Booking_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'cf7_booking';

		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  status varchar(55) DEFAULT '0' NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

	}


	/**
	 * Demo data. (use period)
	 *
	 * Install Demo data.
	 *
	 * @since    1.0.0
	 */
	public static function demo() {
		
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'cf7_booking';
		$res             = wp_cache_get( 'my_result' );
		if ( false === $res ) {
			$res = $wpdb->get_row( 'select COUNT(*) as total from ' . $table_name, 'ARRAY_A' );
			wp_cache_set( 'my_result', $res );
		}
		if ( '0' === $res['total'] ) {
			$insert = array();
			for ( $i = 0; $i < 100; $i++ ) {
				$insert = array(
					'id'     => '',
					'time'   => current_time( 'mysql' ),
					'status' => '0',
				);
				$wpdb->insert( $table_name, $insert );
			}
		}
	}

}
