<?php
/**
 * Logs List.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Logs_List_Table;
use MdAiContentWriter\Inc\Logs;

/**
 * Logs List Class
 *
 * @since x.x.x
 */
class Logs_List extends Logs {

	use Get_Instance;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'list_page_menu' ], 99 );
	}

	/**
	 * Add menu
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function list_page_menu() {
		global $md_ai_logs_list_page;

		$md_ai_logs_list_page = add_submenu_page(
			'md_ai_content_writer_settings',
			__( 'Logs List', 'md-ai-content-writer' ),
			__( 'Logs', 'md-ai-content-writer' ),
			'manage_options',
			'multidots_logs_list',
			[ $this, 'list_page_render' ],
		);

		add_action( "load-$md_ai_logs_list_page", [ $this, 'screen_options' ] );
	}

	/**
	 * Screen options
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function screen_options() {
		global $md_ai_logs_list_page;
        global $table;
 
		$screen = get_current_screen();
	
		// Get out of here if we are not on our settings page
		if ( ! is_object( $screen ) || $screen->id !== $md_ai_logs_list_page )
			return;
	
		$args = array(
			'label'   => __( 'Logs per page', 'md-ai-content-writer' ),
			'default' => 2,
			'option'  => 'logs_per_page'
		);
		add_screen_option( 'per_page', $args );
		
		$table = Logs_List_Table::get_instance();
	}

    /**
	 * Render list page
	 *
	 * @since x.x.x
	 *
	 * @return html
	 */
	public function list_page_render() {
		if (
			isset( $_GET['page'] ) &&
			'multidots_logs_list' === $_GET['page'] &&
			isset( $_GET['action'] ) &&
			'edit' === $_GET['action']
		) {
			$ai_logs_id  = ( ! empty( $_GET['ai_log_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ai_log_id'] ) ) : 0 );
			$get_ai_logs = $this->get_log_by_id( absint( $ai_logs_id ) );
			
			$data = [
				'title'       => __( 'Update Log', 'md-ai-content-writer' ),
				'button_text' => __( 'Update', 'md-ai-content-writer' ),
				'ai_logs'    => $get_ai_logs,
			];
	
			//multidots_locations_store_get_template_part( 'render-ai_logs-form', '', $data );
		} else {
			if (
				isset( $_GET['page'] ) &&
				'multidots_logs_list' === $_GET['page'] &&
				isset( $_GET['action'] ) &&
				'delete' === $_GET['action']
			) {
				$ai_logs_id  = ( ! empty( $_GET['ai_log_id'] ) ? sanitize_text_field( wp_unslash( $_GET['ai_log_id'] ) ) : 0 );
				$get_ai_logs = $this->delete_log( absint( $ai_logs_id ) );
			}

			$table = Logs_List_Table::get_instance();

			echo '<div class="wrap"><h2>' . __( 'Log List Table', 'md-ai-content-writer' ) . '</h2>';
			echo '<form method="post">';
			// Prepare table
			$table->prepare_items();
			// Search form
			$table->search_box( 'search', 'search_id' );
			// Display table
			$table->display();
			echo '</div></form>';
		}
	}
}