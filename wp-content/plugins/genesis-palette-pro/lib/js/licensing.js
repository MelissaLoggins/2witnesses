
/**
 * Reset the message status.
 */
function dppLicenseSubmitReset( licenseSubmit, licenseChange ) {

	// Clear any message text currently there.
	jQuery( 'p.gppro-license-field-message' ).text( '\u00A0' );

	// Set the button to disabled.
	if ( 'disable' === licenseChange ) {
		licenseSubmit.attr( 'disabled', 'disabled' );
		jQuery( '.gppro-license-spinner' ).addClass( 'gppro-license-spinner-display' );
	}

	// Set the button to disabled.
	if ( 'enable' === licenseChange ) {
		licenseSubmit.removeAttr( 'disabled' );
		jQuery( '.gppro-license-spinner' ).removeClass( 'gppro-license-spinner-display' );
	}

	// And finish up.
	return;
}

/**
 * Handle our message reporting for license fields.
 */
function dppLicenseFieldMessage( licenseField, message, licenseChange ) {

	// Set the text field as a variable.
	var licenseMessage  = jQuery( 'p.gppro-license-field-message' );

	// Set my license toggle icon as a variable.
	var licenseToggle   = jQuery( '.gppro-license-key-core-single .password-toggle' );

	// Clear any message text currently there.
	licenseMessage.text( '\u00A0' );

	// First remove existing valid an invalid classes.
	jQuery( licenseField ).removeClass( 'gppro-license-core-valid gppro-license-core-invalid' );

	// If we got the error flag, set that class.
	if ( 'error' === licenseChange ) {
		licenseField.addClass( 'gppro-license-core-invalid' );
	}

	// If we got the success flag, set that class.
	if ( 'success' === licenseChange ) {
		licenseField.addClass( 'gppro-license-core-valid' );
	}

	// If we got the "normal", remove any extra classes.
	if ( 'normal' === licenseChange ) {
		licenseField.removeClass( 'gppro-license-core-valid gppro-license-core-invalid' );
	}

	// Add the message itself (assuming we have one).
	if ( '' !== message ) {
		licenseMessage.text( message );
	}

	// If there's no license key, hide the little eyeball.
	if ( '' === licenseField.val() ) {
		licenseToggle.addClass( 'password-toggle-hide' );
	} else {
		licenseToggle.removeClass( 'password-toggle-hide' );
	}

	// And finish up.
	return;
}

/**
 * Update the button context after a license action.
 */
function dppLicenseButtonSwap( actionText, buttonText ) {

	// Update my hidden action field text.
	jQuery( 'input#gppro-license-action' ).val( actionText );

	// Update my button text.
	jQuery( 'button.gppro-license-core-button' ).html( buttonText );

	// Clear the license field if we did a deactivation.
	if ( 'core_activate' === actionText ) {
		jQuery( 'input#gppro-license-core' ).val( '' );
	}

	// And finish up.
	return;
}

//******************************************************************************
// Let's get started, shall we?
//******************************************************************************
jQuery( document ).ready( function($) {

	/**
	 * Quick helper to check for an existance of an element.
	 *
	 * @param  {Function} callback [description]
	 *
	 * @return {[type]}            [description]
	 */
	$.fn.divExists = function( callback ) {

		// Slice some args.
		var args = [].slice.call( arguments, 1 );

		// Check for length.
		if ( this.length ) {
			callback.call( this, args );
		}

		// Return it.
		return this;
	};

	/**
	 * Pull localized values and set other variables.
	 */
	var licenseBlock    = '';
	var licenseSubmit   = '';
	var licenseAction   = '';
	var licenseField    = '';
	var licenseValue    = '';
	var licenseChange   = '';
	var licenseNonce    = '';

	var fieldClass      = 'normal';
	var buttonText      = '';
	var actionText      = '';
	var messageText     = '';

	var buttonReset     = '';
	var actionReset     = '';
	var messageDefault  = '';

	var errorDefault    = licenseText.generalerror;

	/**
	 * Do the password magic.
	 *
	 * @param  {[type]}   [description]
	 *
	 * @return {[type]}   [description]
	 */
	$( 'td.gppro-license-key-core-single' ).divExists( function() {

		// If we don't have our password functionality, don't show the eyeball.
		if ( '' !== $( '#gppro-license-core' ).val() ) {
			$( '.gppro-license-key-core-single .password-toggle' ).removeClass( 'password-toggle-hide' );
		}

		// Hide the field on initial load.
		$( '#gppro-license-core' ).hidePassword( false );

		// now check for clicks
		$( 'td.gppro-license-key-core-single' ).on( 'click', 'span.password-toggle', function () {

			// if our password is not visible
			if ( ! $( this ).hasClass( 'password-visible' ) ) {
				$( this ).addClass( 'password-visible dashicons-visibility' ).removeClass( 'dashicons-hidden' );
				$( '#gppro-license-core' ).showPassword( false );
			} else {
				$( this ).removeClass( 'password-visible dashicons-visibility' ).addClass( 'dashicons-hidden' );
				$( '#gppro-license-core' ).hidePassword( false );
			}

		});
	});

	/**
	 * Process our license activation / deactivation request.
	 *
	 * @param  {[type]}       [description]
	 * @return {[type]}       [description]
	 */
	$( '.gppro-license-key-core-row' ).on( 'click', '.gppro-license-core-button', function ( event ) {

		// Stop the button action from doing it's thing.
		event.preventDefault();

		// Set the button as a variable for later.
		licenseSubmit   = $( this );

		// Set the button to disabled.
		dppLicenseSubmitReset( licenseSubmit, 'disable' );

		// Set our license block and license field as a variables to use.
		licenseBlock    = $( 'td.gppro-license-key-core-single' );
		licenseField    = $( licenseBlock ).find( '#gppro-license-core' );

		// Check for the nonce value.
		licenseNonce    = $( licenseBlock ).find( '#gppro-license-core-nonce' ).val();

		// Handle missing nonce key.
		if ( '' === licenseNonce ) {

			// Set the button back to enabled.
			dppLicenseSubmitReset( licenseSubmit, 'enable' );

			// Add our text message.
			dppLicenseFieldMessage( licenseField, errorDefault, 'error' );

			// And straight up bail.
			return false;
		}

		// Get variables from click.
		licenseAction   = $( licenseBlock ).find( '#gppro-license-action' ).val();
		licenseValue    = licenseField.val();

		// Check to make sure an action was passed.
		if ( '' === licenseAction ) {

			// Set the button back to enabled.
			dppLicenseSubmitReset( licenseSubmit, 'enable' );

			// And straight up bail.
			return false;
		}

		// Handle missing license key on activation.
		if ( '' === licenseValue ) {

			// Set the button back to enabled.
			dppLicenseSubmitReset( licenseSubmit, 'enable' );

			// Add our text message.
			dppLicenseFieldMessage( licenseField, licenseText.missingkey, 'error' );

			// And straight up bail.
			return false;
		}

		// Handle anything specific to doing activations.
		if ( 'core_activate' === licenseAction ) {

			// Set our default action, message, and button text for after the swap.
			actionReset     = 'core_deactivate';
			buttonReset     = licenseText.deactivate;
			messageDefault  = licenseText.deactivated;
			fieldClass      = 'success';
		}

		// Handle anything specific to doing deactivations.
		if ( 'core_deactivate' === licenseAction ) {

			// Set our default action, message, and button text for after the swap.
			actionReset     = 'core_activate';
			buttonReset     = licenseText.activate;
			messageDefault  = licenseText.deactivated;
			fieldClass      = 'normal';
		}

		// Build out my data array for the Ajax call.
		var data    = {
			action:     licenseAction,
			license:    licenseValue,
			nonce:      licenseNonce
		};

		// Process AJAX request.
		jQuery.post( ajaxurl, data, function( response ) {

			// Set the button back to enabled.
			dppLicenseSubmitReset( licenseSubmit, 'enable' );

			var obj;
			try {
				obj = jQuery.parseJSON( response );
			}
			catch(e) {
				return false;
			}

			// The process worked. Update various things.
			if ( obj.success === true ) {

				// Get our new action, message, and button text.
				actionText  = obj.action === undefined ? actionReset : obj.action;
				buttonText  = obj.button === undefined ? buttonReset : obj.button;
				messageText = obj.message === undefined ? messageDefault : obj.message;

				// Handle the swap.
				dppLicenseButtonSwap( actionText, buttonText );

				// Add our text message.
				dppLicenseFieldMessage( licenseField, messageText, fieldClass );

				// Handle the stupid Firefox message.
				window.onbeforeunload = null;

				// And finish up.
				return false;
			}

			// If it's false and we have an error code.
			if ( obj.success === false && obj.errcode !== '' ) {

				// Determine the appropriate message text.
				messageText = obj.message === '' ? errorDefault : obj.message;

				// Add our text message.
				dppLicenseFieldMessage( licenseField, messageText, 'error' );

				// And finish up.
				return false;
			}

			// Our fallback failure.
			if ( obj.success === false ) {

				// Add our text message.
				dppLicenseFieldMessage( licenseField, errorDefault, 'error' );

				// And finish up.
				return false;
			}

		});

	});

//******************************************************************************
// You're still here? It's over. gGo home.
//******************************************************************************
});