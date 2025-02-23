<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://dnsempresas.com/
 * @since      1.0.0
 *
 * @package    Profiling_Tool_For_Wp
 * @subpackage Profiling_Tool_For_Wp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Profiling_Tool_For_Wp
 * @subpackage Profiling_Tool_For_Wp/includes
 * @author     Dns Empresas <Administracion@dnsempresas.com>
 */
class Profiling_Tool_For_Wp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Profiling_Tool_For_Wp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PROFILING_TOOL_FOR_WP_VERSION' ) ) {
			$this->version = PROFILING_TOOL_FOR_WP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'profiling-tool-for-wp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

		if ( get_option( 'PROFILING_TOOL_FOR_WP_LANGUAGE' ) ) {
		    // code...
		} else {

		    add_option( 'PROFILING_TOOL_FOR_WP_LANGUAGE' );

		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Profiling_Tool_For_Wp_Loader. Orchestrates the hooks of the plugin.
	 * - Profiling_Tool_For_Wp_i18n. Defines internationalization functionality.
	 * - Profiling_Tool_For_Wp_Admin. Defines all hooks for the admin area.
	 * - Profiling_Tool_For_Wp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profiling-tool-for-wp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-profiling-tool-for-wp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-profiling-tool-for-wp-admin.php';

		$this->loader = new Profiling_Tool_For_Wp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Profiling_Tool_For_Wp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Profiling_Tool_For_Wp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Profiling_Tool_For_Wp_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_menu' );

		// Add Settings link to the plugin
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );

		$this->loader->add_filter( 'plugin_action_links_profiling-tool-for-wp/profiling-tool-for-wp.php', $plugin_admin, 'ptfwp_settings_link', 10, 2);
		
		//Add the ajax calls
		$this->loader->add_action('wp_ajax_save_plugin_options', $plugin_admin, 'save_plugin_options');
		$this->loader->add_action('wp_ajax_nopriv_save_plugin_options', $plugin_admin, 'save_plugin_options');
		
		$this->loader->add_action('wp_ajax_save_page_profile', $plugin_admin, 'save_page_profile');
		$this->loader->add_action('wp_ajax_nopriv_save_page_profile', $plugin_admin, 'save_page_profile');
		
		$this->loader->add_action('wp_ajax_save_profile', $plugin_admin, 'save_profile');
		$this->loader->add_action('wp_ajax_nopriv_save_profile', $plugin_admin, 'save_profile');
					
		$this->loader->add_action('wp_ajax_load_table_data', $plugin_admin, 'load_table_data');
		$this->loader->add_action('wp_ajax_nopriv_load_table_data', $plugin_admin, 'load_table_data');

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Profiling_Tool_For_Wp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
