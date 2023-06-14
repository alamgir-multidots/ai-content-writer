<?php
/**
 * Plugin Name: MD AI Content Writer
 * Description: MD AI Content Writer is for WordPress Website
 * Plugin URI: https://www.multidots.come/
 * Author: Swhales
 * Author URI: https://multidots.come/
 * Version: 1.0.0
 * License: GPL v2
 * Text Domain: md-ai-content-writer
 *
 * @package md-ai-content-writer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set constants
 */
define( 'MD_AI_CONTENT_WRITER_FILE', __FILE__ );
define( 'MD_AI_CONTENT_WRITER_DIR_FILE', dirname(__FILE__) );
define( 'MD_AI_CONTENT_WRITER_BASE', plugin_basename( MD_AI_CONTENT_WRITER_FILE ) );
define( 'MD_AI_CONTENT_WRITER_DIR', plugin_dir_path( MD_AI_CONTENT_WRITER_FILE ) );
define( 'MD_AI_CONTENT_WRITER_URL', plugins_url( '/', MD_AI_CONTENT_WRITER_FILE ) );
define( 'MD_AI_CONTENT_WRITER_PLUGIN_PATH', untrailingslashit( MD_AI_CONTENT_WRITER_DIR ) );
define( 'MD_AI_CONTENT_WRITER_VER', '1.0.0' );
define( 'MD_AI_CONTENT_WRITER_SETTINGS', 'md_ai_content_writer_general' );
define( 'MD_AI_CONTENT_WRITER_SETTINGS_APPEARANCE', 'md_ai_content_writer_general_appearance' );

require_once 'inc/functions.php';
require_once 'plugin-loader.php';
