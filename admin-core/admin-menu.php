<?php
/**
 * Admin menu.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Admin_Core;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Helper;


/**
 * Admin menu
 *
 * @since x.x.x
 */
class Admin_Menu {

	use Get_Instance;

	/**
	 * Tailwind assets base url
	 *
	 * @var string
	 * @since x.x.x
	 */
	private $tailwind_assets = MD_AI_CONTENT_WRITER_URL . 'admin-core/assets/build/';

	/**
	 * Instance of Helper class
	 *
	 * @var Helper
	 * @since x.x.x
	 */
	private $helper;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		$this->helper = new Helper();
		add_action( 'admin_menu', [ $this, 'settings_page' ], 99 );
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_page_scripts' ] );
		add_action( 'wp_ajax_md_ai_content_writer_update_settings', [ $this, 'md_ai_content_writer_update_settings' ] );
	}

	/**
	 * Adds admin menu for settings page
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function settings_page() {
		add_menu_page(
			__( 'Settings - MD AI Content Writer', 'md-ai-content-writer' ),
			__( 'AI Content Writer', 'md-ai-content-writer' ),
			'manage_options',
			'md_ai_content_writer_settings',
			[ $this, 'render' ],
			'',
			10
		);
	}

	/**
	 * Renders main div to implement tailwind UI
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function render() {
		?>
		<div class="md-ai-content-writer-settings" id="md-ai-content-writer-settings"></div>
		<?php
	}

	/**
	 * Enqueue settings page script and style
	 *
	 * @param string $hook Current page hook name.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function settings_page_scripts( $hook ) {
		if ( 'toplevel_page_md_ai_content_writer_settings' !== $hook ) {
			return;
		}

		$version           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : MD_AI_CONTENT_WRITER_VER;
		$script_asset_path = MD_AI_CONTENT_WRITER_DIR . 'admin-core/assets/build/settings.asset.php';
		$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: array(
				'dependencies' => [],
				'version'      => $version,
			);

		$script_dep = array_merge( $script_info['dependencies'], [ 'updates' ] );

		wp_register_script( 'md_ai_content_writer_settings', $this->tailwind_assets . 'settings.js', $script_dep, $version, true );
		wp_enqueue_script( 'md_ai_content_writer_settings' );
		wp_localize_script(
			'md_ai_content_writer_settings',
			'md_ai_content_writer_settings',
			[
				'ajax_url'                                     => admin_url( 'admin-ajax.php' ),
				'update_nonce'                                 => wp_create_nonce( 'md_ai_content_writer_update_settings' ),
				MD_AI_CONTENT_WRITER_SETTINGS            => $this->helper->get_option( MD_AI_CONTENT_WRITER_SETTINGS ),
				MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE => $this->helper->get_option( MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE ),
			]
		);

		wp_register_style( 'md_ai_content_writer_settings', $this->tailwind_assets . 'settings.css', [], $version );
		wp_enqueue_style( 'md_ai_content_writer_settings' );
	}

	/**
	 * Ajax handler for submit action on settings page.
	 * Updates settings data in database.
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function md_ai_content_writer_update_settings() {
		check_ajax_referer( 'md_ai_content_writer_update_settings', 'security' );
		$keys = [];

		if ( ! empty( $_POST[ MD_AI_CONTENT_WRITER_SETTINGS ] ) ) {
			$keys[] = MD_AI_CONTENT_WRITER_SETTINGS;
		}

		if ( ! empty( $_POST[ MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE ] ) ) {
			$keys[] = MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE;
		}

		if ( empty( $keys ) ) {
			wp_send_json_error( [ 'message' => __( 'No valid setting keys found.', 'md-ai-content-writer' ) ] );
		}

		$succeded = 0;
		foreach ( $keys as $key ) {
			if ( $this->update_settings( $key, $_POST[ $key ] ) ) {
				$succeded++;
			}
		}

		if ( count( $keys ) === $succeded ) {
			wp_send_json_success( [ 'message' => __( 'Settings saved successfully.', 'md-ai-content-writer' ) ] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to save settings.', 'md-ai-content-writer' ) ] );
	}

	/**
	 * Update dettings data in database
	 *
	 * @param string $key options key.
	 * @param string $data user input to be saved in database.
	 * @return boolean
	 * @since x.x.x
	 */
	public function update_settings( $key, $data ) {
		$data 		  = ! empty( $data) ? json_decode( stripslashes( $data ), true ) : array(); // phpcs:ignore
		$data         = $this->sanitize_data( $data );
		$default_data = $this->helper->get_option( $key );
		$data         = wp_parse_args( $data, $default_data );

		return update_option( $key, $data );
	}

	/**
	 * Sanitize data as per data type
	 *
	 * @param array $data raw input received from user.
	 * @return array
	 * @since x.x.x
	 */
	public function sanitize_data( $data ) {
		$temp     = [];
		$booleans = [];
		$numbers  = [];

		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $booleans, true ) ) {
				$temp[ $key ] = rest_sanitize_boolean( $value );
			} elseif ( in_array( $key, $numbers, true ) ) {
				$temp[ $key ] = (int) sanitize_text_field( $value );
			} else {
				$temp[ $key ] = sanitize_text_field( $value );
			}
		}

		return $temp;
	}
}
