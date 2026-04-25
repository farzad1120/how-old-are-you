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

		var frames = {};

		function targetForButton( $button ) {
			var key = $button.data( 'target' ) || 'logo';
			var $row = $button.closest( 'tr, td' );
			var $id;
			var $preview;
			if ( key === 'bg-image' ) {
				$id      = $( '#hoay-bg-image-id' );
				$preview = $row.find( '.hoay-media-preview' ).first();
			} else {
				$id      = $( '#hoay-logo-id' );
				$preview = $row.find( '.hoay-media-preview' ).first();
			}
			return { key: key, $id: $id, $preview: $preview };
		}

		$( '.hoay-media-pick' ).on( 'click', function ( e ) {
			e.preventDefault();
			var t = targetForButton( $( this ) );
			var frame = frames[ t.key ];
			if ( ! frame ) {
				frame = wp.media( {
					title: ( window.HOAY_ADMIN && window.HOAY_ADMIN.mediaTitle ) || 'Choose image',
					library: { type: 'image' },
					button: { text: ( window.HOAY_ADMIN && window.HOAY_ADMIN.mediaButton ) || 'Use this image' },
					multiple: false
				} );

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					t.$id.val( attachment.id );
					t.$preview.html( '<img src="' + attachment.url + '" alt="" />' );
				} );

				frames[ t.key ] = frame;
			}
			frame.open();
		} );

		$( '.hoay-media-clear' ).on( 'click', function ( e ) {
			e.preventDefault();
			var t = targetForButton( $( this ) );
			t.$id.val( '0' );
			t.$preview.html(
				'<span class="hoay-media-empty">' +
					( t.$preview.attr( 'data-empty' ) || '' ) +
				'</span>'
			);
		} );
	} );
} )( jQuery );
