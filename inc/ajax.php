<?php
/**
 * Ajax.
 *
 * @package md-ai-content-writer
 * @since x.x.x
 */

namespace MdAiContentWriter\Inc;

use MdAiContentWriter\Inc\Traits\Get_Instance;
use MdAiContentWriter\Inc\Md_Ai_Content_Writer;
use MdAiContentWriter\Inc\Logs;

/**
 * Ajax
 *
 * @since x.x.x
 */
class Ajax extends Md_Ai_Content_Writer {

	use Get_Instance;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
        add_action( 'wp_ajax_md_ai_content_writer_for_builder', [ $this, 'generate_ai_content' ] );
		add_action( 'admin_footer', [ $this, 'render_popup' ] );
	}

	/**
	 * Render popup
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function render_popup() {
		$current_screen = get_current_screen();

		if ( method_exists( $current_screen, 'is_block_editor' ) && ! $current_screen->is_block_editor() ) {
			return;
		}
		
		$generate_featured = $this->get_option( 'generate_featured_image', MD_AI_CONTENT_WRITER_SETTINGS );
		$add_conclusion    = $this->get_option( 'add_conclusion', MD_AI_CONTENT_WRITER_SETTINGS );
		$add_excerpt       = $this->get_option( 'add_excerpt', MD_AI_CONTENT_WRITER_SETTINGS );
		?>	
			<div id="md-ai-content-writer-modal" class="md-ai-content-writer-popup-block">
				<div class="md-ai-content-writer-popup-inner">
					<p><a class="md-ai-popup-modal-dismiss" href="#"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></a></p>
					<h3 class="md-ai-popup-modal-title"><?php esc_html_e( 'MD AI Content Writer', 'md-ai-content-writer' ) ?></h3>
					<span class="md-ai-api-error"></span>
					<p class="md-ai-popup-modal-subtile"><?php esc_html_e( 'AI/GPT-3', 'md-ai-content-writer' ) ?></p>
					<label class="md-ai-prompt-checkbox-label md-ai-prompt-checkbox-skip-content-label"><input type="checkbox" name="skip_content_generate" value="1" class="md-ai-skip-content-generate"><?php esc_html_e( 'Skip content generate', 'md-ai-content-writer' ) ?></label>
					<input id="md-ai-prompt-input" autocomplete="off" type="text" name="md-ai-prompt" class="md-ai-prompt-box clearfix" placeholder="<?php esc_attr_e( 'Send a message here....', 'md-ai-content-writer' ); ?>">
					<label class="md-ai-prompt-checkbox-label"><input type="checkbox" name="generate_image" value="1" class="md-ai-generate-image" <?php echo checked( $generate_featured, 1 ); ?>><?php esc_html_e( 'Generate featured image', 'md-ai-content-writer' ) ?></label>
					<label class="md-ai-prompt-checkbox-label"><input type="checkbox" name="add_conclusion" value="1" class="md-ai-add-conclusion" <?php echo checked( $add_conclusion, 1 ); ?>><?php esc_html_e( 'Add conclusion', 'md-ai-content-writer' ) ?></label>
					<label class="md-ai-prompt-checkbox-label"><input type="checkbox" name="add_excerpt" value="1" class="md-ai-add-excerpt" <?php echo checked( $add_excerpt, 1 ); ?>><?php esc_html_e( 'Add excerpt', 'md-ai-content-writer' ) ?></label>
					<button class="md-content-generator-btn components-button is-primary" aria-disabled="true"><?php esc_html_e( 'Generate Content', 'md-ai-content-writer' ) ?></button>

					<span class="md-ai-api-generating-content"></span>
				</div>
			</div>
		<?php
	}

	/**
	 * Generate AI Content
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function generate_ai_content() {
		if (
			! isset( $_POST['_nonce'] ) ||
			! wp_verify_nonce( $_POST['_nonce'], 'md_ai_content_writer_ajax_nonce' )
		) {
			return;
		}
		
		$api_key = $this->get_option( 'api_key', MD_AI_CONTENT_WRITER_SETTINGS );
		$prompt  = ( ! empty( $_POST['prompt'] ) ? sanitize_text_field( wp_unslash( $_POST['prompt'] ) ) : '' );
		
		if ( empty( $api_key ) ) {
			$error  = $this->get_error_msg( 'api_key_empty' );
			$return = [ 'success' => 0, 'content' => $error, 'title' => '' ];

			// Adding logs
			$this->add_logs( $prompt, '', '', '', 'Failed: ' . $error );
			wp_send_json( $return );
			wp_die();
		}

		// Get post data
		$skip_content = ( ! empty( $_POST['skip_content'] ) ? sanitize_text_field( wp_unslash( $_POST['skip_content'] ) ) : '' );
		$image        = ( ! empty( $_POST['image'] ) ? sanitize_text_field( wp_unslash( $_POST['image'] ) ) : 0 );
		$conclusion   = ( ! empty( $_POST['conclusion'] ) ? sanitize_text_field( wp_unslash( $_POST['conclusion'] ) ) : 0 );
		$excerpt      = ( ! empty( $_POST['excerpt'] ) ? sanitize_text_field( wp_unslash( $_POST['excerpt'] ) ) : 0 );
		$post_id      = ( ! empty( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : 0 );
		$image_style  = ' realistic, high_resolution, artstation_three, trending_in_artstation';
		$content      = '';
		$conc_title   = '';
		$conc_content = '';
		
		// Creating Open AI Object
		$open_ai  = new Open_Ai( $api_key );

		if ( $skip_content !== 'true' ) {
			$content_structure = $this->get_option( 'content_structure', MD_AI_CONTENT_WRITER_SETTINGS, 'article' );
			$content_lenght    = $this->get_option( 'content_lenght', MD_AI_CONTENT_WRITER_SETTINGS, 'long' );
			$writing_style     = $this->get_option( 'writing_style', MD_AI_CONTENT_WRITER_SETTINGS, 'normal' );
			$writing_tone      = $this->get_option( 'writing_tone', MD_AI_CONTENT_WRITER_SETTINGS, 'informative' );

			$response = $open_ai->completion( [
				'model'             => 'text-davinci-003',
				'prompt'            => 'Details of ' . $writing_tone . ' ' . $writing_style . ' ' . $content_lenght . ' ' . $content_structure . ' about on ' . $prompt,
				'temperature'       => absint( $this->get_option( 'temperature', MD_AI_CONTENT_WRITER_SETTINGS, 0.9 ) ),
				'max_tokens'        => absint( $this->get_option( 'max_tokens', MD_AI_CONTENT_WRITER_SETTINGS, 150 ) ),
				'frequency_penalty' => absint( $this->get_option( 'frequency_penalty', MD_AI_CONTENT_WRITER_SETTINGS, 0 ) ),
				'presence_penalty'  => absint( $this->get_option( 'presence_penalty', MD_AI_CONTENT_WRITER_SETTINGS, 0.6 ) ),
			] );

			//$response = '{ "id": "cmpl-7QV8ysnvbKBXUaEeLymINMPtqFFmj", "object": "text_completion", "created": 1686550600, "model": "text-davinci-003", "choices": [ { "text": "\n\nMultidots is a WordPress agency based in Ahmedabad, India. It was founded in 2009 by two passionate entrepreneurs, Sonal and Akshat. The company specializes in providing premium services for the WordPress platform including theme and plugin development, custom PHP and WordPress solutions, website designing, analytics, and hosting services. They also offer enterprise-level solutions for custom WordPress projects, such as website consultation, web design, content management systems, SEO, social media marketing, custom plug-ins, eCommerce and web development. Their primary focus is to provide high-quality, creative and cost-effective solutions for WordPress users worldwide.", "index": 0, "logprobs": null, "finish_reason": "stop" } ], "usage": { "prompt_tokens": 5, "completion_tokens": 127, "total_tokens": 132 } }';
			$response = json_decode( $response, true );

			if ( isset( $response['error'] ) ) {
				$error  = $this->get_error_msg( $response['error']['type'] );
				$return = [ 'success' => 0, 'content' => $error, 'title' => '' ];

				// Adding logs
				$this->add_logs( $prompt, '', '', '', 'Failed: ' . $error );
				wp_send_json( $return );
				wp_die();
			}

			$content = isset( $response['choices'][0]['text'] ) ? trim( $response['choices'][0]['text'] ) : '';
		}

		// Generating image
		$image_added = 0;
		if ( ! empty( $post_id ) && $image === 'true' ) {
			$img_response = $open_ai->image([
				"prompt"          => $prompt . $image_style,
				"n"               => 1,
				"size"            => $this->get_option( 'featured_image_size', MD_AI_CONTENT_WRITER_SETTINGS, '512x512px' ),
				"response_format" => "url",
			]);
			
			$img_content  = json_decode( $img_response, true );

			if ( isset( $img_content['error'] ) ) {
				$error  = $this->get_error_msg( $img_content['error']['type'] );
				$return = [ 'success' => 0, 'content' => $error, 'title' => '' ];
				
				// Adding logs
				$this->add_logs( $prompt, '', '', '', 'Image Generating Failed: ' . $error );
				
				wp_send_json( $return );
				wp_die();
			}

			if ( ! isset( $img_content['error'] ) ) {
				$img_url      = $img_content['data'][0]['url'];
				
				// Set post thumbnail
				$this->set_thumbnail( $post_id, $img_url );
			}
			$image_added = 1;
		}

		// Set excerpt to current post
		$excerpt_added = 0;
		$excerpt_data  = '';
		if ( ! empty( $post_id ) && ! empty( $content ) && $excerpt === 'true' ) {
			//Excerpt words
			$excerpt_words = $this->get_option( 'excerpt_words', MD_AI_CONTENT_WRITER_SETTINGS, 200 );
			$excerpt_data  = substr( $content, 0, $excerpt_words);

			global $wpdb;
        	$wpdb->update( $wpdb->prefix . 'posts', [ 'post_excerpt' => $excerpt_data ], [ 'ID' => $post_id ] );
			$excerpt_added = 1;
		} else if ( ! empty( $post_id ) && empty( $content ) && $excerpt === 'true' ) {
			$excerpt_response = $open_ai->completion( [
				'model'             => 'text-davinci-003',
				'prompt'            => 'Introductions about on ' . $prompt,
				'temperature'       => 0.4,
				'max_tokens'        => 150,
				'frequency_penalty' => 0.3,
				'presence_penalty'  => 0.5,
			] );
			
			$excerpt_response = json_decode( $excerpt_response, true );	
			$excerpt_content  = isset( $excerpt_response['choices'][0]['text'] ) ? trim( $excerpt_response['choices'][0]['text'] ) : '';		
			$excerpt_words    = $this->get_option( 'excerpt_words', MD_AI_CONTENT_WRITER_SETTINGS, 200 );
			$excerpt_data     = substr( $excerpt_content, 0, $excerpt_words);

			global $wpdb;
        	$wpdb->update( $wpdb->prefix . 'posts', [ 'post_excerpt' => $excerpt_data ], [ 'ID' => $post_id ] );
			$excerpt_added = 1;
		}

		// Set conclusion to current post
		if ( $conclusion === 'true' ) {
			$response = $open_ai->completion( [
				'model'             => 'text-davinci-003',
				'prompt'            => 'Conclusion of ' . $prompt,
				'temperature'       => 0.4,
				'max_tokens'        => 150,
				'frequency_penalty' => 0.3,
				'presence_penalty'  => 0.5,
			] );
			
			$response = json_decode( $response, true );	
			
			if ( isset( $response['choices'][0]['text'] ) ) {
				$conc_title   = __( 'Conclusion', 'md-ai-content-writer' );
				$conc_content = trim( $response['choices'][0]['text'] );
			}
		}

		// Adding logs
		$this->add_logs( $prompt, $image_added, $conclusion, $excerpt_added, 'Successfully' );

		$return  = [ 'success' => 1, 'conc_title' => $conc_title, 'excerpt_data' => $excerpt_data, 'conc_content' => $conc_content, 'content' => $content, 'title' => $prompt ];
		wp_send_json( $return );
		wp_die();
	}

	/**
	 * Add logs
	 *
	 * @since x.x.x
	 * 
	 * @return void
	 */
    public function add_logs( $prompt, $image_added, $conclusion, $excerpt_added, $api_run ) {
		$current_date_time = current_datetime()->format('Y-m-d H:i:s');
		
		$logs_data = [
			'prompt'      => $prompt,
			'gen_image'   => ( $image_added ) ? __( 'Added', 'md-ai-content-writer' ) : __( 'No', 'md-ai-content-writer' ),
			'add_conc'    => ( $conclusion === 'true' ) ? __( 'Added', 'md-ai-content-writer' ) : __( 'No', 'md-ai-content-writer' ),
			'add_excerpt' => ( $excerpt_added ) ? __( 'Added', 'md-ai-content-writer' ) : __( 'No', 'md-ai-content-writer' ),
			'entry_date'  => $current_date_time,
			'api_run'     => $api_run,
		];

		// Adding logs
		$logs = Logs::get_instance();
		$logs->manage_logs( $logs_data );
	}

	/**
	 * Set thumbnail
	 *
	 * @since x.x.x
	 * 
	 * @param Int    $post_id  Post Id.
	 * @param String $image_url Image url.
	 * 
	 * @return void
	 */
    public function set_thumbnail( $post_id, $ai_image_url ) {
		if ( empty( $ai_image_url ) ) {
			return;
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$image_url = 'http://example.com/image.jpg';

		$tmp = download_url( $ai_image_url );

		$file_array = array(
			'name'     => basename( $image_url ),
			'tmp_name' => $tmp
		);

		$attach_id = media_handle_sideload( $file_array, 0 );

		if ( is_wp_error( $attach_id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $attach_id;
		}

		// Create the thumbnails
		$attach_data = wp_generate_attachment_metadata( $attach_id,  get_attached_file( $attach_id ) );

		wp_update_attachment_metadata( $attach_id,  $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
	}
}
