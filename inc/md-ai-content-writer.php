<?php
/**
 * MD AI Content Writer
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Helper;

/**
 * MD AI Content Writer class
 *
 * @since x.x.x
 */
class Md_Ai_Content_Writer {

	use Get_Instance;

	/**
	 * The post type slug.
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * The post per page limit.
	 *
	 * @var string
	 */
	protected $post_per_page = 10;

	/**
	 * Get setting option data.
	 *
	 * @since x.x.x
	 *
	 * @param string $option Option name.
	 * @param string $section Option section.
	 * @param string $default Default value.
	 */
	public function get_option( $option, $section, $default = '' ) {
		$options = get_option( $section );
		$helper  = Helper::get_instance();

		if ( isset( $options[ $option ] ) ) {
			return '' === $options[ $option ] ? $default : $options[ $option ];
		}

		if ( empty( $default ) && isset( $helper->get_option( $section )[ $option ] ) ) {
			return $helper->get_option( $section )[ $option ];
		}

		return $default;
	}

	/**
	 * Get error message
	 *
	 * @since x.x.x
	 */
	public function get_error_msg( $type = '' ) {
		$message = [
			'invalid_request_error' => __( 'Invalid API Key! Check your API key.', 'md-ai-content-writer' ),
			'insufficient_quota'    => __( 'Insufficient Quota! Check billing info in your Open AI account.', 'md-ai-content-writer' ),
			'server_error'          => __( 'Server Error! Try again later.', 'md-ai-content-writer' ),
			'api_key_empty'         => __( 'API Key Empty! Set your api key first.', 'md-ai-content-writer' ),
		];

		return isset( $message[$type] ) ? $message[$type] : __( 'Something went wrong! Try again later.', 'md-ai-content-writer' );
	}

	/**
	 * Check script is globally enable
	 *
	 * @since x.x.x
	 */
	public function is_global_enabled() {
		return true;
	}

	/**
	 * Get taxonomy list
	 *
	 * @since x.x.x
	 * 
	 * @param string $type Return type data.
	 *
	 * @return string
	 */
	public function get_ip_info( $type = 'country' ) {
		$client  = @$_SERVER["HTTP_CF_CONNECTING_IP"];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$a       = @$_SERVER['HTTP_X_FORWARDED'];
		$b       = @$_SERVER['HTTP_FORWARDED_FOR'];
		$c       = @$_SERVER['HTTP_FORWARDED'];
		$d       = @$_SERVER['HTTP_CLIENT_IP'];
		$remote  = @$_SERVER['REMOTE_ADDR'];
		
		$userip_data = "";
				  
		if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {		
			$ip = $client;
		} elseif( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
			$ip = $forward;
		} elseif( filter_var( $a, FILTER_VALIDATE_IP ) ) {
			$ip = $a;
		} elseif( filter_var( $b, FILTER_VALIDATE_IP ) ) {
			$ip = $b;
		} elseif( filter_var( $c, FILTER_VALIDATE_IP ) ) {
			$ip = $c;
		} elseif ( filter_var( $remote, FILTER_VALIDATE_IP ) ) {
			$ip = $remote;
		} else {
			$ip = '';
		}
	
		if ( empty( $ip ) ) {
			return $userip_data;
		}
		
		$ip_data = @json_decode( wp_remote_retrieve_body( wp_remote_get( "http://ip-api.com/json/" . $ip ) ) );
		
		if ( $type == 'countryCode' ) {
			$userip_data = $ip_data->countryCode;
		} elseif ( $type == 'country' ) {
			$userip_data = $ip_data->country;
		} elseif ( $type == 'region' ) {
			$userip_data = $ip_data->region;
		} elseif ( $type == 'city' ) {
			$userip_data = $ip_data->city;
		} elseif ( $type == 'ip' ) {
			$userip_data = $ip_data->query;
		}
		
		return $userip_data;
	}

	/**
	 * Get taxonomy list
	 *
	 * @since x.x.x
	 * 
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array
	 */
	public function get_taxonomy_list( $taxonomy ) {
		$args = array(
			'taxonomy'     => $taxonomy,
			'orderby'      => 'name',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0
		);
		$categories   = get_categories( $args );
		$get_category = [];

		foreach( $categories as $cat ) {
			$get_category[ $cat->term_id ] = $cat->name;
		} 

		return $get_category;
	}
}
