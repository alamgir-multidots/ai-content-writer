<?php
/**
 * Helper.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;

/**
 * Helper
 *
 * @since x.x.x
 */
class Helper {

	use Get_Instance;

	/**
	 * Keep default values of all settings.
	 *
	 * @var array
	 * @since x.x.x
	 */
	public function get_defaults() {
		return [
			MD_AI_CONTENT_WRITER_SETTINGS    => [
				'api_key'                 => '',
				'temperature'             => 0.8,
				'max_tokens'              => 150,
				'top_prediction'          => 0.5,
				'frequency_penalty'       => 0,
				'presence_penalty'        => 0.6,
				'content_structure'       => 'article',
				'content_lenght'          => 'long',
				'writing_style'           => 'normal',
				'writing_tone'            => 'informative',
				'add_excerpt'             => 'yes',
				'add_conclusion'          => 'yes',
				'generate_featured_image' => 'yes',
				'featured_image_size'     => '512x512px',
				'excerpt_words'           => 200,
			],
			MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE => [
				'primary_bg_color'   => '#F66338',
				'primary_font_color' => '#fff',
			],
		];
	}

	/**
	 * Get option value from database and retruns value merged with default values
	 *
	 * @param string $option option name to get value from.
	 * @return array
	 * @since x.x.x
	 */
	public function get_option( $option ) {
		$db_values = get_option( $option, [] );
		return wp_parse_args( $db_values, $this->get_defaults()[ $option ] );
	}
}
