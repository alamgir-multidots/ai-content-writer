<?php
/**
 * Scripts.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Md_Ai_Content_Writer;

/**
 * Scripts
 *
 * @since x.x.x
 */
class Scripts extends Md_Ai_Content_Writer {

	use Get_Instance;

	/**
	 * Plugin version.
	 *
	 * @var string $version Current plugin version.
	 */
	public $version;

	/**
	 * Folder suffix.
	 *
	 * @var string $folder_suffix Select script folder.
	 */
	public $folder_suffix;

	/**
	 * File suffix.
	 *
	 * @var string $file_suffix Select script file.
	 */
	public $file_suffix;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		$this->version       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : MD_AI_CONTENT_WRITER_VER;
		$this->folder_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : 'min-';
		$this->file_suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'dynamic_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
	}

	/**
	 * Dynamic styles
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function dynamic_styles() {
		if ( ! $this->is_global_enabled() ) {
			return;
		}

		$dynamic_css  = ':root {';
		$dynamic_css .= "
		--md-ai-content-writer-primary-background-color: {$this->get_option( 'primary_bg_color', MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE )};
		--md-ai-content-writer-primary-font-color: {$this->get_option( 'primary_font_color', MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE )};
		--md-ai-content-writer-notice-alignment: {$this->get_option( 'notice_alignment', MD_AI_CONTENT_WRITER_SETTINGS )};
		";

		$dynamic_css .= '}';

		wp_add_inline_style( 'md-ai-content-writer', $dynamic_css );
	}

	/**
	 * Admin enqueue scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function admin_styles() {
		global $post;

		$current_screen = get_current_screen();

		// Check the currect post.
		if ( $post ) {
			wp_register_style( 'md-ai-content-writer-admin-css', MD_AI_CONTENT_WRITER_URL . 'assets/' . $this->folder_suffix . 'css/admin-styles' . $this->file_suffix . '.css', [], $this->version );
			wp_enqueue_style( 'md-ai-content-writer-admin-css' );
		}
		
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			wp_register_script( 'md-ai-content-admin', MD_AI_CONTENT_WRITER_URL . 'assets/' . $this->folder_suffix . 'js/admin-scripts' . $this->file_suffix . '.js', [ 'jquery' ], $this->version, true );
			wp_enqueue_script( 'md-ai-content-admin' );

			wp_localize_script(
				'md-ai-content-admin',
				'md_ai_content_writer_admin_ajax_object',
				apply_filters(
					'md_ai_content_writer_localize_script_args',
					[
						'ajax_url'           => admin_url( 'admin-ajax.php' ),
						'ajax_nonce'         => wp_create_nonce( 'md_ai_content_writer_ajax_nonce' ),
						'general_error'      => __( 'Sometings wrong! try again later', 'md-ai-content-writer' ),
						'required_error'     => __( 'Required fields must be filled in', 'md-ai-content-writer' ),
						'generating_content' => __( 'Generating your content, please wait for a few seconds!', 'md-ai-content-writer' ),
					]
				)
			);
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_global_enabled() ) {
			return;
		}

		wp_register_style( 'md-ai-content-writer', MD_AI_CONTENT_WRITER_URL . 'assets/' . $this->folder_suffix . 'css/styles' . $this->file_suffix . '.css', [], $this->version );
		wp_enqueue_style( 'md-ai-content-writer' );
		
		wp_register_script( 'md-ai-content-writer', MD_AI_CONTENT_WRITER_URL . 'assets/' . $this->folder_suffix . 'js/scripts' . $this->file_suffix . '.js', [ 'jquery' ], $this->version, true );
		wp_enqueue_script( 'md-ai-content-writer' );

		$this->localize_script();
	}

	/**
	 * Localize scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function localize_script() {
		wp_localize_script(
			'md-ai-content-writer',
			'md_ai_content_writer_ajax_object',
			apply_filters(
				'md_ai_content_writer_localize_script_args',
				[
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'     => wp_create_nonce( 'md_ai_content_writer_ajax_nonce' ),
					'general_error'  => __( 'Sometings wrong! try again later', 'md-ai-content-writer' ),
					'required_error' => __( 'Required fields must be filled in', 'md-ai-content-writer' ),
				]
			)
		);
	}
}
