<?php
/**
 * Logs List Table.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Logs List Table Class
 *
 * @since x.x.x
 */
class Logs_List_Table extends \WP_List_Table {

	use Get_Instance;

	// Define $table_data property
    private $table_data;

	/**
	 * Get columns
	 *
	 * @since x.x.x
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'prompt'      => __( 'Prompt Title', 'md-ai-content-writer' ),
			'gen_image'   => __( 'Generated Image', 'md-ai-content-writer' ),
			'add_conc'    => __( 'Add Conclusion', 'md-ai-content-writer' ),
			'add_excerpt' => __( 'Add Excerpt', 'md-ai-content-writer' ),
			'entry_date'  => __( 'Date', 'md-ai-content-writer' ),
			'api_run'     => __( 'API Run', 'md-ai-content-writer' )
		);
		return $columns;
	}

	/**
	 * Prepare items
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function prepare_items() {
		// Data
		if ( isset( $_POST['s'] ) ) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }
		
		$columns  = $this->get_columns();
        $hidden   = ( is_array( get_user_meta( get_current_user_id(), 'managetoplevel_page_multidots_logs_listcolumnshidden', true)) ) ? get_user_meta( get_current_user_id(), 'managetoplevel_page_multidots_logs_listcolumnshidden', true ) : array();
        $sortable = $this->get_sortable_columns();
		$primary  = 'prompt';
        $this->_column_headers = array( $columns, $hidden, $sortable, $primary );

		//usort( $this->table_data, array( &$this, 'usort_reorder' ) );

		/* Pagination */
        $per_page         = $this->get_items_per_page( 'logs_per_page', 10 );
        $current_page 	  = $this->get_pagenum();
        $total_items      = count($this->table_data);
        $this->table_data = array_slice($this->table_data, (($current_page - 1) * $per_page), $per_page);

        $this->set_pagination_args(array(
			'total_items' => $total_items, // total number of items
			'per_page'    => absint( $per_page ), // items to show on a page
			'total_pages' => ceil( $total_items / $per_page ) // use ceil to round up
        ));
        
        $this->items = $this->table_data;
	}

	/**
	 * Get table data
	 *
	 * @since x.x.x
	 * 
	 * @param String $search Search key.
	 *
	 * @return array
	 */
	public function get_table_data( $search = '' ) {
		global $wpdb;

        $table = $wpdb->prefix . 'md_ai_logs';

        if ( ! empty( $search ) ) {
            return $wpdb->get_results(
                "SELECT * from {$table} WHERE prompt Like '%{$search}%' OR entry_date Like '%{$search}%' ORDER BY id DESC",
                ARRAY_A
            );
        } else {
			$result = wp_cache_get( 'md_ai_logs_cache_data' );
			
			if ( false === $result ) {
				$result = $wpdb->get_results(
					"SELECT * from {$table} ORDER BY id DESC",
					ARRAY_A
				);

				// Cache set
				wp_cache_set( 'md_ai_logs_cache_data', $result );
			} 
            
			return $result;
        }
	}

	/**
	 * Set column default
	 *
	 * @since x.x.x
	 * 
	 * @param Array  $item Iteam data.
	 * @param String $column_name Column name.
	 *
	 * @return void
	 */
	public function column_default( $item, $column_name )	{
		switch ($column_name) {
			case 'prompt':
			case 'gen_image':
			case 'add_conc':
			case 'add_excerpt':
			case 'entry_date':
			case 'run_succ':
			default:
				return $item[$column_name];
	  	}
	}

	/**
	 * Manage name column
	 *
	 * @since x.x.x
	 * 
	 * @param Array $item Item data.
	 *
	 * @return html
	 */
	public function column_prompt( $item ) {
		$confirm_msg = __( 'Are you sure you want to delete this item?', 'md-ai-content-writer' );
		$actions = array(
			'delete' => sprintf('<a href="admin.php?page=multidots_logs_list&action=%s&ai_log_id=%s" onclick="return confirm(\''. $confirm_msg .'\');">' . __( 'Delete', 'md-ai-content-writer' ) . '</a>', 'delete', $item['id'] ),
		);

		return sprintf( '%1$s %2$s', $item['prompt'], $this->row_actions( $actions ) );
	}

	/**
	 * Manage name column
	 *
	 * @since x.x.x
	 * 
	 * @param Array $item Item data.
	 *
	 * @return html
	 */
	public function column_api_run( $item ) {
		if ( 'Successfully' === $item['api_run'] ) {
			return '<span style="color:green; font-size: 12px;">' . __( 'Successfully', 'md-ai-content-writer' ) . '</span>';
		}
		return '<span style="color:red; font-size: 12px;">' . $item['api_run'] . '</span>';
	}

	/**
	 * Manage name column
	 *
	 * @since x.x.x
	 * 
	 * @param Array $item Item data.
	 *
	 * @return html
	 */
	public function column_entry_date( $item ) {
		return date_i18n( 'F j, Y g:i a', strtotime( $item['entry_date'] ) );
	}

	/**
	 * Get sortable columns
	 *
	 * @since x.x.x
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
            'prompt_name' => array( 'prompt_name', false ),
            'entry_date'  => array( 'entry_date', false ),
      	);
      	return $sortable_columns;
	}

	/**
	 * Usort reorder
	 *
	 * @since x.x.x
	 *
	 * @return Int
	 */
    public function usort_reorder( $a, $b ) {
        // If no sort, default to user_login
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';

        // If no order, default to desc
        $order = ( ! empty( $_GET['order'] ) ) ? $_GET['order'] : 'desc';

        // Determine sort order
        $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

        // Send final sort direction to usort
        return ( $order === 'asc') ? $result : -$result;
    }
}