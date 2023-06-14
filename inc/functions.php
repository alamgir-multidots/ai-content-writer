<?php
/**
 * Plugin functions.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */
if ( ! function_exists( 'md_ai_content_writer_get_template_part ' ) ) {

	/**
	 * Get template part implementation for wedocs.
	 *
	 * @since x.x.x
	 *
	 * @param string $slug Template slug.
	 * @param string $name Template name.
	 * @param array  $args Template passing data.
	 * @param bool   $return Flag for retun with ob_start.
	 *
	 * @return html Return html file.
	 */
	function md_ai_content_writer_get_template_part( $slug, $name = '', $args = [], $return = false ) {
		$defaults = [
			'pro' => false,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( $args && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}

		$template = '';

		// Look in yourtheme/md-ai-content-writer/slug-name.php and yourtheme/md-ai-content-writer/slug.php.
		$template_path = ! empty( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
		$template      = locate_template( [ 'md-ai-content-writer/' . $template_path ] );

		/**
		 * Change template directory path filter.
		 *
		 * @since x.x.x
		 */
		$template_path = apply_filters( 'md_ai_content_writer_set_template_path', MD_AI_CONTENT_WRITER_PLUGIN_PATH . '/templates', $template, $args );

		// Get default slug-name.php.
		if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
			$template = $template_path . "/{$slug}-{$name}.php";
		}

		if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
			$template = $template_path . "/{$slug}.php";
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$template = apply_filters( 'md_ai_content_writer_get_template_part', $template, $slug, $name );

		if ( $template ) {
			if ( $return ) {
				ob_start();
				require $template;
				return ob_get_clean();
			} else {
				require $template;
				return '';
			}
		}
	}
}
