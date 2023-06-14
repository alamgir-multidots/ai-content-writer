( function ( $ ) {
	const BevoshelvesWcDokanAddonFrontendScripts = {
		init() {
			//
		},

		/**
		 * Show general error
		 *
		 * @since x.x.x
		 */
		showGeneralError: () => {
			$( '#md-ai-content-writer-locations-render-search-result' ).html(
				md_ai_content_writer_ajax_object.general_error
			);
			BevoshelvesWcDokanAddonFrontendScripts.afterAjaxAction();
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
			$( '#md-ai-content-writer-locations-render-search-result' ).html( '' );
			$( '.md-ai-content-writer-locations-render-search-location' ).append(
				'<div class="md-ai-content-writer-search-location-form-loading"></div>'
			);

			$( '.md-ai-content-writer-submitted-error-mgs' ).html('');
			$( '.md-ai-content-writer-submitted-data-mgs' ).html('');
			$( '.md-ai-content-writer-field' ).removeClass('md-required-field');

			$( '#md-ai-content-writer-locations-render-search-result' ).append(
				'<div class="md-ai-content-writer-search-location-listing-loading"><div class="md-ai-content-writer-search-location-form-spinner"></div></div>'
			);
		},

		/**
		 * After ajax actions
		 *
		 * @since x.x.x
		 */
		afterAjaxAction: () => {
			$( 'body' ).css( 'cursor', '' );
			$( '.md-ai-content-writer-search-location-form-loading' ).remove();
			$( '.md-ai-content-writer-search-location-form-spinner' ).remove();
		},

		/**
		 * Refresh recipes data
		 *
		 * @since x.x.x
		 */
		autoRefreshWinemakersList: () => {
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_ai_content_writer_ajax_object.ajax_url,
				data: {
					action: 'md_ai_content_writer_refresh_winemakers_listing',
					_nonce: md_ai_content_writer_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					BevoshelvesWcDokanAddonFrontendScripts.beforeAjaxAction();
				},
				success( response ) {
					$( '#winemaker-render-search-result' ).html(
						response.content
					);
					BevoshelvesWcDokanAddonFrontendScripts.afterAjaxAction();
					setTimeout(function() { 
						$('.winemaker_render_loading_spinner').html('');
					}, 500);
				},
				error() {
					BevoshelvesWcDokanAddonFrontendScripts.showGeneralError();
				},
			} );
		},

		/**
		 * Refresh Winemakers data
		 *
		 * @since x.x.x
		 *
		 * @param {Object}  e    Object current prevent default event.
		 * @param {Integer} page Integer current prevent default event.
		 */
		refresWinemakersList: ( e, page = 1 ) => {
			e.preventDefault();

			if ( '' == $( '.md-ai-content-writer_search_name' ).val() ) {
				$('.wine_mr__search .cross__icon').hide();
			} else {
				$('.wine_mr__search .cross__icon').show();
			}
			
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_ai_content_writer_ajax_object.ajax_url,
				data: {
					action: 'md_ai_content_writer_refresh_winemakers_listing',
					name: $( '.md-ai-content-writer_search_name' ).val(),
					region: $('.winemakers_listing_refresh_action_input').val(),
					page,
					_nonce: md_ai_content_writer_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					$( '.winemaker_render_loading_spinner' ).append(
						'<div class="md-ai-content-writer-search-listing-loading"><div class="md-ai-content-writer-search-form-spinner"></div></div>'
					);
					BevoshelvesWcDokanAddonFrontendScripts.beforeAjaxAction();
				},
				
				success( response ) {
					$( '#winemaker-render-search-result' ).html(
						response.content
					);

					BevoshelvesWcDokanAddonFrontendScripts.afterAjaxAction();
					setTimeout(function() { 
						$('.winemaker_render_loading_spinner').html('');
					}, 500);
				},
				error() {
					BevoshelvesWcDokanAddonFrontendScripts.showGeneralError();
				},
			} );
		},

		/**
		 * Refresh recipes data
		 *
		 * @since x.x.x
		 */
		autoRefreshRecipesList: () => {
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_ai_content_writer_ajax_object.ajax_url,
				data: {
					action: 'md_ai_content_writer_refresh_recipes_listing',
					_nonce: md_ai_content_writer_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					$( '#recipes-render-search-result' ).append(
						'<div class="md-ai-content-writer-search-listing-loading"><div class="md-ai-content-writer-search-form-spinner"></div></div>'
					);
					BevoshelvesWcDokanAddonFrontendScripts.beforeAjaxAction();
				},
				success( response ) {
					$( '#recipes-render-search-result' ).html(
						response.content
					);
					BevoshelvesWcDokanAddonFrontendScripts.afterAjaxAction();
				},
				error() {
					BevoshelvesWcDokanAddonFrontendScripts.showGeneralError();
				},
			} );
		},

		/**
		 * Refresh Recipes data
		 *
		 * @since x.x.x
		 *
		 * @param {Object}  e    Object current prevent default event.
		 * @param {Integer} page Integer current prevent default event.
		 */
		refreshRecipesList: ( e, page = 1 ) => {
			e.preventDefault();

			if ( '' == $( '.md-ai-content-writer_search_name' ).val() ) {
				$('.wine_mr__search .cross__icon').hide();
			} else {
				$('.wine_mr__search .cross__icon').show();
			}
			
			$.ajax( {
				type: 'post',
				dataType: 'json',
				url: md_ai_content_writer_ajax_object.ajax_url,
				data: {
					action: 'md_ai_content_writer_refresh_recipes_listing',
					name: $( '.md-ai-content-writer_search_name' ).val(),
					type: $( '.recipes_listing_refresh_action_input' ).val(),
					page,
					_nonce: md_ai_content_writer_ajax_object.ajax_nonce,
				},
				beforeSend: () => {
					$( '.winemaker_render_loading_spinner' ).append(
						'<div class="md-ai-content-writer-search-listing-loading"><div class="md-ai-content-writer-search-form-spinner"></div></div>'
					);
					BevoshelvesWcDokanAddonFrontendScripts.beforeAjaxAction();
				},
				
				success( response ) {
					$( '#recipes-render-search-result' ).html(
						response.content
					);

					BevoshelvesWcDokanAddonFrontendScripts.afterAjaxAction();
					setTimeout(function() { 
						$('.winemaker_render_loading_spinner').html('');
					}, 500);
				},
				error() {
					BevoshelvesWcDokanAddonFrontendScripts.showGeneralError();
				},
			} );
		},
	};

	$( function () {
		BevoshelvesWcDokanAddonFrontendScripts.init();
	} );
} )( jQuery );
