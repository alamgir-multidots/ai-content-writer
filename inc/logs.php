<?php
/**
 * Logs.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Md_Ai_Content_Writer;

/**
 * Logs Class
 *
 * @since x.x.x
 */
class Logs extends Md_Ai_Content_Writer {

	use Get_Instance;

	/**
	 * Refresh locations listing
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function manage_logs( $logs_data, $id = '' ) {
		global $wpdb;
        
		if ( ! empty( $id ) ) {
			global $wpdb;
        	$wpdb->update( $wpdb->prefix . 'md_ai_logs', $logs_data, [ 'id' => $id ] );
		} else {
			$wpdb->insert( $wpdb->prefix . 'md_ai_logs', $logs_data);
		}

		// Clear feedback cache
		wp_cache_delete( 'md_ai_logs_cache_data' );

        return true;
	}

	/**
	 * Get data by id.
	 *
	 * @since x.x.x
	 */
    public function get_log_by_id( $id ) {
		global $wpdb;
        return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}md_ai_logs WHERE id = $id", ARRAY_A );
    }

	/**
	 * Delete data.
	 *
	 * @since x.x.x
	 */
    public function delete_log( $id ) {
		global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->prefix}md_ai_logs WHERE id = $id;" );
    }
}