(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $( '.cofl-categories' ).sortable({
	 	update: function( event, ui ) {
	 		var data = {
	 			action: 'cofl_save_category_order',
	 			security: cofl.security,
	 			order: []
	 		};

	 		$('.cofl-cat', this).each(function(){
	 			data.order.push( $(this).attr('id') );
	 		});

	 		$.post(cofl.ajax_url, data);
	 	}
	 });

	 $('.cofl-cat__title').on('click', function(){
	 	$(this).parent().find('.cofl-cat__content').toggleClass('hide-content');
	 });

	 $( '.sortable' ).sortable({
	 	update: function( event, ui ) {
	 		var data = {
	 			action: 'cofl_update_item_order',
	 			security: cofl.security,
	 			cat_key: $(this).parents('.cofl-cat').attr('id'),
	 			order: []
	 		};

	 		$('li', this).each(function(){
	 			data.order.push( $(this).attr('id') );
	 		});

	 		$.post(cofl.ajax_url, data, function(response){
	 		});
	 	}
	 });

	 $('.cofl-cat__remove').on('click', function(){
	 	var data = {
	 			action: 'cofl_remove_category',
	 			security: cofl.security,
	 			cat_key: $(this).parents('.cofl-cat').attr('id')
	 		},
	 		catName = $.trim( $(this).prev().text() );


	 	if ( confirm('Do you really want to delete "'+ catName +'" category?') ) {
	 		$.post(cofl.ajax_url, data, function(response){
	 			if ( response.success ) {
	 				/* This is the ID. */
	 				$('#' + data.cat_key).slideUp(function(){
	 					$(this).remove();
	 				});
	 			}
	 		});
	 	}
	 });

	 $(document).on('submit', '.form-cofl-items', function(e){
	 	e.preventDefault();
	 	var id = $(this).parents('.cofl-cat').attr('id'),
	 	 	data = {
	 			action: 'cofl_add_cat_item',
	 			security: cofl.security,
	 			cat_key: id,
	 			item: $('#' + id).find('input[type="text"]').val()
	 		};
	 	$.post(cofl.ajax_url, data, function(response){
	 		if ( response.success ) {
	 			$('#' + data.cat_key + ' .sortable').append(response.data);
	 			$('#' + id).find('input[type="text"]').val('');
	 		}
	 	});
	 });

	 $(document).on('click', '.remove_link', function(e){
	 	e.preventDefault();
	 	var data = {
	 			action: 'cofl_remove_item',
	 			security: cofl.security,
	 			item: $(this).data('slug'),
	 			cat_key: $(this).parents('.cofl-cat').attr('id')
	 		},
	 		currentCat = $(this).parents('.cofl-cat');

	 	if ( confirm('Are you sure?') ) {
	 		$.post(cofl.ajax_url, data, function(response){
	 			console.log(response.data);
	 			if ( response.success ) {
	 				$('#' + data.item, currentCat).slideUp(function(){
	 					$(this).remove();
	 				});
	 			}
	 		});
	 	}
	 });

})( jQuery );
