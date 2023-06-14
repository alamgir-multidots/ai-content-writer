( function ( $ ) {
	const MdAiContentWriterAdminScripts = {
		init() {
            $( 'body' ).on(
				'click',
				'.md-content-generator-btn',
				function ( e ) {
					e.preventDefault();

					MdAiContentWriterAdminScripts.generateAiContent( e );
				}
			);

			$( 'body' ).on(
				'keyup',
				'.md-ai-prompt-box',
				function ( e ) {
					e.preventDefault();

					if ( $(this).val() === '' ) {
						$(".md-content-generator-btn").attr( 'aria-disabled', 'true' );
					} else {
						$(".md-content-generator-btn").attr( 'aria-disabled', 'false' );
					}
				}
			);
			
			$( 'body' ).on(
				'click',
				'.md-ai-popup-modal-dismiss',
				function ( e ) {
					e.preventDefault();

					$(".md-ai-content-writer-popup-block").removeClass('md-ai-content-writer-popup-block-open');
				}
			);

			$( 'body' ).on(
				'click',
				'#md-content-writer-btn',
				function ( e ) {
					e.preventDefault();

					$(".md-ai-api-error").html('');
					$(".md-ai-prompt-box").html('');
					$(".md-ai-content-writer-popup-block").addClass('md-ai-content-writer-popup-block-open');
				}
			);
		},

		/**
		 * Show general error
		 *
		 * @since x.x.x
		 */
		showGeneralError: () => {
			$( '#md-ai-content-writer-locations-render-search-result' ).html(
				md_ai_content_writer_admin_ajax_object.general_error
			);
			MdAiContentWriterAdminScripts.afterAjaxAction();
			setTimeout(function() { 
				$('.winemaker_render_loading_spinner').html('');
			}, 500);
		},

		/**
		 * Before ajax actions
		 *
		 * @since x.x.x
		 */
		beforeAjaxAction: () => {
			$( 'body' ).css( 'cursor', 'progress' );
			$( '.md-content-generator-btn' ).addClass('is-busy');
			$(".md-ai-api-error").html('');

			setTimeout(function(){
				if ( $(".md-ai-api-error").html() == '' ) {
					let generatingContent = $(".md-ai-api-generating-content");
					MdAiContentWriterTypeWriter(generatingContent, md_ai_content_writer_admin_ajax_object.generating_content, 50);
				}
			}, 5000);
		},

		/**
		 * After ajax actions
		 *
		 * @since x.x.x
		 */
		afterAjaxAction: () => {
			$( 'body' ).css( 'cursor', '' );
			$( '.md-content-generator-btn' ).removeClass('is-busy');
			$(".md-ai-content-writer-popup-block").removeClass('md-ai-content-writer-popup-block-open');
			$(".md-ai-api-error").html('');
			$(".md-ai-api-generating-content").html('');
		},

		/**
		 * Error after ajax actions
		 *
		 * @since x.x.x
		 */
		errorAfterAjaxAction: () => {
			$( 'body' ).css( 'cursor', '' );
			$( '.md-content-generator-btn' ).removeClass('is-busy');
		},

		/**
		 * Refresh recipes data
		 *
		 * @since x.x.x
		 */
		generateAiContent: () => {
			const post_id = wp.data.select("core/editor").getCurrentPostId();

			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_ai_content_writer_admin_ajax_object.ajax_url,
				data: {
					action: 'md_ai_content_writer_for_builder',
					_nonce: md_ai_content_writer_admin_ajax_object.ajax_nonce,
					prompt: $('.md-ai-prompt-box').val(),
					skip_content: $('.md-ai-skip-content-generate').is(':checked'),
					image: $('.md-ai-generate-image').is(':checked'),
					conclusion: $('.md-ai-add-conclusion').is(':checked'),
					excerpt: $('.md-ai-add-excerpt').is(':checked'),
					post_id
				},
				beforeSend: () => {
					MdAiContentWriterAdminScripts.beforeAjaxAction();
				},
				success( response ) {

					if ( response.success == 0 ) {
						$(".md-ai-api-generating-content").html('');
						$(".md-ai-api-error").html(response.content);
						MdAiContentWriterAdminScripts.errorAfterAjaxAction();
						return;
					}

					if (tinymce.activeEditor) {
						$('#title').siblings('label').addClass('screen-reader-text')
						$('#title').val( response.title );
			
						setTimeout(function(){
							tinymce.activeEditor.execCommand('mceInsertContent', false, response.content);
						}, 1000);
			
						if ($('.excerpt').length&&$('#excerpt').length){
							$('#excerpt').text($('.excerpt p').text());
						}
					} else {
						if ( wp.data.dispatch('core/block-editor') == undefined ) {
							return
						}
						
						if ( response.title ) {
							wp.data.dispatch('core/editor').editPost({title: response.title});
						}
						
						var name = 'core/paragraph';
							
						var insertedBlock = wp.blocks.createBlock( name, {
							content: response.content,
						} );
						
						wp.data.dispatch('core/block-editor').insertBlocks( insertedBlock );

						if ( response.conc_title !== '' ) {
							var insertedBlock = wp.blocks.createBlock( 'core/heading', {
								content: response.conc_title,
							} );
							
							wp.data.dispatch('core/block-editor').insertBlocks( insertedBlock );

							if ( response.conc_content !== '' ) {
								var insertedBlock = wp.blocks.createBlock( 'core/paragraph', {
									content: response.conc_content,
								} );
								
								wp.data.dispatch('core/block-editor').insertBlocks( insertedBlock );
							}
						}

						if ( response.excerpt_data !== '' ) {
							$("textarea#inspector-textarea-control-0").val(response.excerpt_data);
						}
					}

					setTimeout(function(){
						$(".editor-post-publish-button").trigger('click');
					}, 200);
					
					MdAiContentWriterAdminScripts.afterAjaxAction();
				},
				error() {
					MdAiContentWriterAdminScripts.showGeneralError();
				},
			} );
		},
	};

	$( function () {
		MdAiContentWriterAdminScripts.init();
	} );

	function MdAiContentWriterTypeWriter(l,s,i) {
		var track = "";
		var len = s.length;
		var n = 0;
		l.text("");
		var si = setInterval(function(){
			var res = track + s.charAt(n);
			l.text(res);
			track = res;
			if(n===len-1){
				clearInterval(si);
			}
			n = n + 1;
		},i);
	}

} )( jQuery );

( function( window, wp ){
    // prepare our custom link's html.
    var link_html = '<a id="md-content-writer-btn" class="md-content-writer-toolbar-btn md-content-writer-loading" href="#" >AI Content Writer <span class="md-content-writer-aiwa-spinner md-content-writer-hide-spin"></span></a>';

    // check if gutenberg's editor root element is present.
    var editorEl = document.getElementById( 'editor' );
    
    if ( ! editorEl ) { // do nothing if there's no gutenberg root element on page.
        return;
    }

    wp.data.subscribe( function () {
        setTimeout( function () {
            if ( document.getElementsByClassName("md-content-writer-toolbar-btn")[0]==undefined ) {
                var toolbalEl = editorEl.querySelector( '.edit-post-header-toolbar' );
                
                if ( toolbalEl instanceof HTMLElement ) {
                    toolbalEl.insertAdjacentHTML( 'beforeend', link_html );
                }
            }
        }, 1 )
    } );

} )( window, wp )
