<?php
/**
 * Plugin Loader.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter;

use MdAiContentWriter\Admin_Core\Admin_Menu;
use MdAiContentWriter\Inc\Scripts;
use MdAiContentWriter\Inc\Ajax;
use MdAiContentWriter\Inc\Logs_List;

/**
 * Plugin_Loader
 *
 * @since x.x.x
 */
class Plugin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since x.x.x
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since x.x.x
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);

		$file = MD_AI_CONTENT_WRITER_DIR . $filename . '.php';

		// if the file redable, include it.
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', [ $this, 'load_classes' ] );
		add_filter( 'plugin_action_links_' . MD_AI_CONTENT_WRITER_BASE, [ $this, 'action_links' ] );
		register_activation_hook( MD_AI_CONTENT_WRITER_FILE, [ $this, 'activate' ] );
	}

	/**
	 * Create roles on plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		$this->create_table();
	}

	/**
	 * Create table.
	 *
	 * @return void
	 */
	public function create_table() {
		global $wpdb;
		
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$ai_logs         = $wpdb->prefix . 'md_ai_logs';

		$sql = "CREATE TABLE IF NOT EXISTS `$ai_logs` (
			id bigint NOT NULL AUTO_INCREMENT,
			prompt tinytext NOT NULL,
			gen_image VARCHAR(50) NOT NULL,
			add_conc VARCHAR(50) NOT NULL,
			add_excerpt VARCHAR(50) NOT NULL,
			entry_date DATETIME NOT NULL,
			api_run VARCHAR(150) NOT NULL,
			PRIMARY KEY id (id)
		) $charset_collate;";
		dbDelta($sql);
	}

	/**
	 * Load Plugin Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/md-ai-content-writer/ folder
	 *      2. Local dorectory /wp-content/plugins/md-ai-content-writer/languages/ folder
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function load_textdomain() {
		// Default languages directory.
		$lang_dir = MD_AI_CONTENT_WRITER_DIR . 'languages/';

		/**
		 * Filters the languages directory path to use for plugin.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'wpb_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Language Locale for plugin
		 *
		 * @var $get_locale The locale to use.
		 * Uses get_user_locale()` in WordPress 4.7 or greater,
		 * otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'md-ai-content-writer' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'md-ai-content-writer', $locale );

		// Setup paths to current locale file.
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
		$mofile_local  = $lang_dir . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/md-ai-content-writer/ folder.
			load_textdomain( 'md-ai-content-writer', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/md-ai-content-writer/languages/ folder.
			load_textdomain( 'md-ai-content-writer', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'md-ai-content-writer', false, $lang_dir );
		}
	}

	/**
	 * Loads plugin classes as per requirement.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_classes() {
		if ( is_admin() ) {
			Admin_Menu::get_instance();
			Logs_List::get_instance();
		}

		Scripts::get_instance();
		Ajax::get_instance();
	}

	/**
	 * Adds links in Plugins page
	 *
	 * @param array $links existing links.
	 * @return array
	 * @since x.x.x
	 */
	public function action_links( $links ) {
		$plugin_links = apply_filters(
			'md_ai_content_writer_action_links',
			[
				'md_ai_content_writer_settings' => '<a href="' . admin_url( 'options-general.php?page=md_ai_content_writer_settings' ) . '">' . __( 'Settings', 'md-ai-content-writer' ) . '</a>',
			]
		);

		return array_merge( $plugin_links, $links );
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Plugin_Loader::get_instance();
