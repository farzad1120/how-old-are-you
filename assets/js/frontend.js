/**
 * How Old Are You — verification overlay client.
 *
 * Submits the form via fetch to admin-ajax.php with a nonce. On success
 * the server has already set the verification cookie, so we redirect to
 * the original destination. On failure we show the rejection step.
 */
( function () {
	'use strict';

	var form = document.getElementById( 'hoay-form' );
	if ( ! form ) {
		return;
	}

	var ajax    = form.getAttribute( 'data-ajax' );
	var nonce   = form.getAttribute( 'data-nonce' );
	var mode    = form.getAttribute( 'data-mode' );
	var minAge  = parseInt( form.getAttribute( 'data-min-age' ), 10 ) || 18;
	var error   = form.querySelector( '.hoay-error' );
	var submit  = form.querySelector( 'button[type="submit"]' );
	var ask     = document.querySelector( '.hoay-step--ask' );
	var reject  = document.querySelector( '.hoay-step--reject' );
	var dobInput = document.getElementById( 'hoay-dob' );

	function showError( msg ) {
		if ( ! error ) return;
		error.textContent = msg || '';
		error.hidden = ! msg;
	}

	function showRejection() {
		if ( ask )    ask.hidden = true;
		if ( reject ) reject.hidden = false;
	}

	function setLoading( on, btn ) {
		var target = btn || submit;
		if ( ! target ) return;
		if ( on ) {
			target.classList.add( 'hoay-button--loading' );
			target.setAttribute( 'aria-busy', 'true' );
		} else {
			target.classList.remove( 'hoay-button--loading' );
			target.removeAttribute( 'aria-busy' );
		}
	}

	function postVerify( body, btn ) {
		var data = new FormData();
		data.append( 'action', 'hoay_verify' );
		data.append( '_ajax_nonce', nonce );
		data.append( 'mode', mode );
		Object.keys( body ).forEach( function ( k ) { data.append( k, body[ k ] ); } );

		setLoading( true, btn );
		showError( '' );

		return fetch( ajax, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		} )
			.then( function ( r ) { return r.json().catch( function () { return { success: false }; } ); } )
			.then( function ( res ) {
				setLoading( false, btn );
				if ( res && res.success ) {
					window.location.reload();
					return;
				}
				if ( res && res.data && res.data.reason === 'underage' ) {
					showRejection();
					return;
				}
				showError(
					( res && res.data && res.data.message ) ||
					'Verification failed. Please try again.'
				);
			} )
			.catch( function () {
				setLoading( false, btn );
				showError( 'Network error. Please try again.' );
			} );
	}

	function readDobFromSelects() {
		var d = document.getElementById( 'hoay-dob-day' );
		var m = document.getElementById( 'hoay-dob-month' );
		var y = document.getElementById( 'hoay-dob-year' );
		if ( ! d || ! m || ! y ) return '';
		if ( ! d.value || ! m.value || ! y.value ) return '';
		var pad = function ( n ) { n = String( n ); return n.length < 2 ? '0' + n : n; };
		return y.value + '-' + pad( m.value ) + '-' + pad( d.value );
	}

	form.addEventListener( 'submit', function ( e ) {
		e.preventDefault();

		if ( mode === 'dob' ) {
			var dob = '';
			if ( document.querySelector( '.hoay-dob-selects' ) ) {
				dob = readDobFromSelects();
			} else if ( dobInput ) {
				dob = dobInput.value;
			}
			if ( ! dob ) {
				showError( 'Please enter your date of birth.' );
				return;
			}
			postVerify( { dob: dob }, submit );
			return;
		}

		// Confirm mode: which button was clicked?
		var btn = ( document.activeElement && document.activeElement.dataset.confirm )
			? document.activeElement
			: form.querySelector( '[data-confirm]' );
		var choice = btn ? btn.getAttribute( 'data-confirm' ) : 'no';

		if ( choice === 'no' ) {
			// Local short-circuit; server still validates.
			postVerify( { confirm: 'no' }, btn );
			return;
		}
		postVerify( { confirm: 'yes' }, btn );
	} );

	// Track which button was activated so submit handler knows.
	form.querySelectorAll( '[data-confirm]' ).forEach( function ( b ) {
		b.addEventListener( 'click', function () { /* keep activeElement on this button */ } );
	} );
} )();
