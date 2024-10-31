<?php

/**
 * Fired during plugin activation
 *
 * @link       https://dnsempresas.com/
 * @since      1.0.0
 *
 * @package    Profiling_Tool_For_Wp
 * @subpackage Profiling_Tool_For_Wp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Profiling_Tool_For_Wp
 * @subpackage Profiling_Tool_For_Wp/includes
 * @author     Dns Empresas <Administracion@dnsempresas.com>
 */
class Profiling_Tool_For_Wp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::create_history_tb();
		self::create_options_tb();
		self::fill_options_table();
		
	}


	/**
	 * Create the history table of the plugin
	 *
	 * @since    1.0.0
	 */
	public static function create_history_tb() {

		global $wpdb;
		$table_name = $wpdb->prefix . "ptfwp_history";
		$plugin_name_db_version = get_option( 'ptfwp_db_version', '1.0' );

		$prepared_sql = $wpdb->prepare(
		    "SHOW TABLES LIKE %s", 
		    $table_name
		);

		if( $wpdb->get_var( $prepared_sql ) != $table_name || version_compare( $version, '1.0' ) < 0 ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql[] = "CREATE TABLE " . $table_name . " (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				nombre varchar(150) NOT NULL DEFAULT '',
				fecha datetime NOT NULL,
				tipo varchar(150) DEFAULT '',
				items varchar(100) NOT NULL DEFAULT '',
				tiempo varchar(150) NOT NULL DEFAULT '',
				memory varchar(100) NOT NULL DEFAULT '',
				queries varchar(100) NOT NULL DEFAULT '',
				PRIMARY KEY (ID)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			add_option( 'ptfwp_db_version', $plugin_name_db_version );

		}

	}
	
	/**
	 * Create the options table of the plugin
	 *
	 * @since    1.0.0
	 */
	public static function create_options_tb() {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "ptfwp_options";
		$plugin_name_db_version = get_option( 'ptfwp_db_version', '1.0' );

		$prepared_sql = $wpdb->prepare(
		    "SHOW TABLES LIKE %s", 
		    $table_name
		);
		
		if( $wpdb->get_var( $prepared_sql ) != $table_name || version_compare( $version, '1.0' ) < 0 ) {

			$charset_collate = $wpdb->get_charset_collate();

			$sql[] = "CREATE TABLE " . $table_name . " (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                LANGUAGE varchar(150)  DEFAULT '',
                TABLE_SORT varchar(55) DEFAULT '',
                PRIMARY KEY (ID)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

			add_option( 'ptfwp_db_version', $plugin_name_db_version );

		}
		
		
	}
	
	public static function fill_options_table(){
		
		global $wpdb;

		$table = $wpdb->prefix . 'ptfwp_options';
		$data = array(
			'LANGUAGE'     => 'ES',
			'TABLE_SORT'     => 'asc',
		);
		
		$format = array( '%s','%s' );

		$wpdb->insert( $table, $data, $format );
	
	}

}
