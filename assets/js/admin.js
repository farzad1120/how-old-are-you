/**
 * Admin settings page interactions.
 *
 * - Initialises wp-color-picker on every .hoay-color input.
 * - Wires the WP media library frame for the logo picker.
 */
( function ( $ ) {
	'use strict';

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.hoay-color' ).wpColorPicker();
		}

		var frame;
		var $idInput = $( '#hoay-logo-id' );
		var $preview = $( '.hoay-media-preview' );

		$( '.hoay-media-pick' ).on( 'click', function ( e ) {
			e.preventDefault();
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( {
				title: ( window.HOAY_ADMIN && window.HOAY_ADMIN.mediaTitle ) || 'Choose image',
				library: { type: 'image' },
				button: { text: ( window.HOAY_ADMIN && window.HOAY_ADMIN.mediaButton ) || 'Use this image' },
				multiple: false
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				$idInput.val( attachment.id );
				$preview.html( '<img src="' + attachment.url + '" alt="" />' );
			} );

			frame.open();
		} );

		$( '.hoay-media-clear' ).on( 'click', function ( e ) {
			e.preventDefault();
			$idInput.val( '0' );
			$preview.html(
				'<span class="hoay-media-empty">' +
					( $preview.attr( 'data-empty' ) || '' ) +
				'</span>'
			);
		} );
	} );
} )( jQuery );
