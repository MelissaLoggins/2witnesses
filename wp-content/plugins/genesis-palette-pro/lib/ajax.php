<?php
/**
 * Genesis Design Palette Pro - Ajax Module
 *
 * Contains functionality for various Ajax calls
 *
 * @package Design Palette Pro
 */

/*
	Copyright 2014 Reaktiv Studios

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License (GPL v2) only.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Ajax Class.
 *
 * Contains all Ajax calls made
 */
class GP_Pro_Ajax {

	/**
	 * Handle our ajax call loading.
	 *
	 * @return void
	 */
	public function init() {

		// Bail on non admin.
		if ( ! is_admin() ) {
			return;
		}

		// First make sure we have our main class. not sure how we wouldn't but then again...
		if ( ! class_exists( 'Genesis_Palette_Pro' ) ) {
			return;
		}

		// Load the Ajax calls.
		add_action( 'wp_ajax_save_styles',                  array( $this, 'save_styles'             )           );
		add_action( 'wp_ajax_clear_styles',                 array( $this, 'clear_styles'            )           );
		add_action( 'wp_ajax_set_preview',                  array( $this, 'set_preview'             )           );
		add_action( 'wp_ajax_set_user_logged',              array( $this, 'set_user_logged'         )           );
		add_action( 'wp_ajax_ignore_warning',               array( $this, 'ignore_warning'          )           );
		add_action( 'wp_ajax_ignore_webfont',               array( $this, 'ignore_webfont'          )           );
		add_action( 'wp_ajax_core_activate',                array( $this, 'core_activate'           )           );
		add_action( 'wp_ajax_core_deactivate',              array( $this, 'core_deactivate'         )           );
		add_action( 'wp_ajax_gppro_support_request',        array( $this, 'gppro_support_request'   )           );
	}

	/**
	 * Save our new CSS stylesheet via AJAX.
	 *
	 * @return void
	 */
	public function save_styles() {

		// Set up our return.
		$ret    = array();

		// Check for styles being passed through.
		if ( empty( $_POST['choices'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_DATA';
			$ret['message'] = __( 'No data was provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Make sure a nonce was passed.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_MISSING';
			$ret['message'] = __( 'A nonce was not provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Check to see if our nonce verification failed.
		if ( false === $check = check_ajax_referer( 'gppro_save_nonce', 'nonce', false ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAILED';
			$ret['message'] = __( 'The nonce did not validate.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Check if we need to mix it up and set my choices.
		$choices	= 'serialize' == GP_Pro_Helper::maybe_serialize_vars() ? GP_Pro_Utilities::string_to_multiarray( $_POST['choices'] ) : $_POST['choices'];

		// Pass the data to store our non-style options.
		$choices    = self::save_user_settings( $choices );

		// Fetch the items marked for always.
		$always     = ! empty( $_POST['always'] ) ? GP_Pro_Utilities::string_to_array( $_POST['always'] ) : array();

		// Send to our save function.
		self::save_style_settings( $choices, $always );

		// Check for the builder class.
		if ( ! class_exists( 'GP_Pro_Builder' ) ) {

			// Return JSON array with error code if the builder class is missing.
			$ret['success'] = false;
			$ret['errcode'] = 'NO_CLASS';
			$ret['message'] = __( 'The required file to write CSS is missing. Please reinstall or contact support.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Get the new CSS, and bail if our builder failed.
		if ( false === $build = GP_Pro_Builder::build_css() ) {

			// Return JSON array with error code if the CSS could not be generated.
			$ret['success'] = false;
			$ret['errcode'] = 'NO_CSS';
			$ret['message'] = __( 'The CSS could not be generated', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Attempt to generate the CSS, and bail if the file could be be generated.
		if ( false === $generate = Genesis_Palette_Pro::generate_file( $build ) ) {

			// Return JSON array with error code if the folder for the CSS was not writable.
			$ret['success'] = false;
			$ret['errcode'] = 'NOT_WRITEABLE';
			$ret['message'] = __( 'The uploads folder is not writable.', 'gppro' );
			echo json_encode( $ret );
			die();

		} else {

			// Set the file generation time.
			GP_Pro_Helper::set_css_buildtime();

			// Delete our transient for access checking.
			delete_transient( 'gppro_check_file_access' );

			// Return our JSON array as true with the message.
			$ret['success'] = true;
			$ret['message'] = __( 'Success! Your new style is saved.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// We've reached the end, and nothing worked....
		$ret['success'] = false;
		$ret['errcode'] = 'UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Clear our settings and delete the CSS file.
	 *
	 * @return void
	 */
	public function clear_styles() {

		// Start our return.
		$ret = array();

		// Make sure a nonce was passed.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_MISSING';
			$ret['message'] = __( 'A nonce was not provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Check to see if our nonce verification failed.
		if ( false === $check = check_ajax_referer( 'gppro_reset_nonce', 'nonce', false ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAILED';
			$ret['message'] = __( 'The nonce did not validate.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Fetch the current options and back them up.
		GP_Pro_Helper::set_settings_backup();

		// Delete my existing settings.
		GP_Pro_Helper::purge_settings();

		// Delete some transients.
		delete_transient( 'gppro_check_file_access' );

		// Fetch the filebase.
		$file   = Genesis_Palette_Pro::filebase();

		// This exists for a reason, but I cannot remember why.
		if ( file_exists( $file['dir'] ) ) {
			unlink( $file['dir'] );
		}

		// Action to allow deletion of other data by plugins, etc.
		do_action( 'gppro_after_clear' );

		// And return / redirect.
		$ret['success']  = true;
		$ret['redirect'] = menu_page_url( 'genesis-palette-pro', 0 );
		$ret['message']  = __( 'Your style settings have been deleted.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Set the preview URL when reloaded.
	 */
	public function set_preview() {

		// Start our return.
		$ret = array();

		// Make sure a nonce was passed.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_MISSING';
			$ret['message'] = __( 'A nonce was not provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Check to see if our nonce verification failed.
		if ( false === $check = check_ajax_referer( 'gppro_preview_nonce', 'nonce', false ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAILED';
			$ret['message'] = __( 'The nonce did not validate.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Bail without a URL.
		if ( empty( $_POST['preview'] ) ) {

			// Delete the preview URL.
			delete_option( 'gppro-user-preview-url' );

			// And handle the return.
			$ret['success'] = false;
			$ret['errcode'] = 'NO_PREVIEW_URL';
			$ret['message'] = __( 'No preview URL was provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Sanitize the URL.
		if ( false === $preview = GP_Pro_Helper::check_preview_url_scheme( $_POST['preview'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'BAD_PREVIEW_URL';
			$ret['message'] = __( 'The preview URL provided was invalid.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// We have a URL. go forth.
		if ( $preview ) {

			// Update the setting in the DB.
			update_option( 'gppro-user-preview-url', esc_url( $preview ) );

			// And return the JSON.
			$ret['success'] = true;
			$ret['errcode'] = null;
			$ret['preview'] = esc_url( $preview );
			$ret['message'] = __( 'The preview has been updated.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// We've reached the end, and nothing worked....
		$ret['success'] = false;
		$ret['errcode'] = 'UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Set the logged in mode true or false
	 * on user checkbox click.
	 */
	public function set_user_logged() {

		// Set up our return.
		$ret    = array();

		// Bail if no logged was passed.
		if ( ! isset( $_POST['logged'] ) ) {

			$ret['success'] = false;
			$ret['errcode'] = 'NO_DATA';
			$ret['message'] = __( 'No paramater was passed.', 'gppro' );
			echo json_encode( $ret );
			die();

		} else {

			// Check if true or false came.
			if ( ! empty( $_POST['logged'] ) ) {
				update_option( 'gppro-user-preview-type', true, 'no' );
			} else {
				delete_option( 'gppro-user-preview-type' );
			}

			// Handle the return.
			$ret['success'] = true;
			$ret['message'] = __( 'OK', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// We've reached the end, and nothing worked....
		$ret['success'] = false;
		$ret['errcode'] = 'UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Clear child theme warning.
	 *
	 * @return void
	 */
	public function ignore_warning() {

		// Set up our return.
		$ret    = array();

		// Bail if no child was passed.
		if ( empty( $_POST['child'] ) ) {

			$ret['success'] = false;
			$ret['errcode'] = 'NO_CHILD';
			$ret['message'] = __( 'No child theme name was given.', 'gppro' );
			echo json_encode( $ret );
			die();

		} else {

			// Set option name.
			$option = 'gppro-warning-' . sanitize_text_field( wp_unslash( $_POST['child'] ) );

			// Add the option to the DB.
			add_option( $option, true, null, 'no' );

			// Handle the return.
			$ret['success'] = true;
			$ret['message'] = __( 'OK', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// We've reached the end, and nothing worked....
		$ret['success'] = false;
		$ret['errcode'] = 'UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Clear webfont pagespeed warning.
	 *
	 * @return void
	 */
	public function ignore_webfont() {

		// Get the data passed.
		add_option( 'gppro-webfont-alert', 'ignore', null, 'no' );

		// Set up our return.
		$ret    = array();

		// Handle the return.
		$ret['success'] = true;
		$ret['message'] = __( 'OK', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Save our settings that aren't related to styles, such as
	 * favicon and header images.
	 *
	 * @param  array  $choices 	the full array of settings being passed.
	 *
	 * @return array  $choices 	the updated array of settings with the specific user settings removed
	 */
	public static function save_user_settings( $choices = array() ) {

		// Check for favicon.
		if ( ! empty( $choices['site-favicon-file'] ) ) {
			update_option( 'gppro-site-favicon-file', esc_url( $choices['site-favicon-file'] ), 'no' );
		} else {
			delete_option( 'gppro-site-favicon-file' );
		}

		// Check for user preview URL.
		if ( ! empty( $choices['user-preview-url'] ) ) {
			update_option( 'gppro-user-preview-url', GP_Pro_Helper::check_preview_url_scheme( $choices['user-preview-url'] ), 'no' );
		} else {
			delete_option( 'gppro-user-preview-url' );
		}

		// Check for user logged in choice.
		if ( ! empty( $choices['user-preview-type'] ) ) {
			update_option( 'gppro-user-preview-type', true, 'no' );
		} else {
			delete_option( 'gppro-user-preview-type' );
		}

		// Remove the three options from the array.
		unset( $choices['site-favicon-file'] );
		unset( $choices['user-preview-url'] );
		unset( $choices['user-preview-type'] );

		// Return the remaining array.
		return $choices;
	}

	/**
	 * Take the passed user choices and do various
	 * data validation and sanitiation before
	 * Saving and passing to the CSS build.
	 *
	 * @param  array  $choices [description]
	 * @param  array  $always  [description]
	 *
	 * @return [type]          [description]
	 */
	public static function save_style_settings( $choices = array(), $always = array() ) {

		// Handle our before action.
		do_action( 'gppro_before_save', $choices );

		// Fetch our current saved data and back it up.
		GP_Pro_Helper::set_settings_backup();

		// If we don't have our builder class, just save it and return.
		if ( ! class_exists( 'GP_Pro_Builder' ) ) {

			// Update the data.
			update_option( 'gppro-settings', $choices );

			// Handle our after action.
			do_action( 'gppro_after_save', $choices );

			// And finish.
			return;
		}

		// Set an empty array of our updated values.
		$updated	= array();

		// Loop our choices to get our field / value pair.
		foreach ( $choices as $field => $value ) {

			// Run our build check to not save defaults.
			$compare    = GP_Pro_Builder::compare_single_field( $value, $field );

			// If we passed, add it into our array OR is part of the ignore array.
			if ( ! empty( $compare ) || in_array( $field, $always ) ) {
				$updated[ $field ]  = $value;
			}
		}

		// Bail if no data is different.
		if ( empty( $updated ) ) {
			return;
		}

		// Update the data.
		update_option( 'gppro-settings', $updated );

		// Handle our after action.
		do_action( 'gppro_after_save', $updated );

		// And finish.
		return;
	}

	/**
	 * Call activation.
	 *
	 * @return void
	 */
	public function core_activate() {

		// First delete any transients, just in case.
		GP_Pro_Helper::purge_transients();

		// Make sure we have a license key.
		if ( empty( $_POST['license'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_LICENSE';
			$ret['message'] = __( 'No license key was submitted.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Run a quick security check.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAIL';
			$ret['message'] = __( 'The security key was not available.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Delete our current license data in case its left over.
		GP_Pro_Helper::purge_options( false );

		// Set variables posted via AJAX.
		$license    = $_POST['license'];
		$nonce      = $_POST['nonce'];

		// Data to send in our API request.
		$check  = GP_Pro_Licensing::api_license_key_check( $license, 'activate_license' );

		// No data came back.
		if ( empty( $check ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_RETURN';
			$ret['message'] = __( 'No data could be found for this license key.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Set my status variable.
		$status = ! empty( $check['status'] ) ? sanitize_key( $check['status'] ) : '';

		// No status. not sure why.
		if ( empty( $status ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_STATUS';
			$ret['message'] = __( 'This license key could not be verified.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Wrong status. not sure why.
		if ( ! in_array( $status, array( 'valid', 'invalid' ) ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'BAD_STATUS';
			$ret['message'] = __( 'This license key returned an unknown status code.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// If we have an error code.
		if ( is_array( $check ) && ! empty( $check['errcode'] ) && ! empty( $check['message'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = $check['errcode'];
			$ret['message'] = $check['message'];
			echo json_encode( $ret );
			die();
		}

		// Not valid. I SAID NOT VALID.
		if ( 'invalid' === $status ) {
			$ret['success'] = false;
			$ret['errcode'] = 'LICENSE_FAIL';
			$ret['message'] = __( 'This license key is not valid.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// License was good. LETS GO.
		if ( 'valid' === $status ) {

			// Run a check.
			if ( false !== GP_Pro_Licensing::api_license_verified( $license, $status ) ) {
				$ret['success']  = true;
				$ret['errcode']  = null;
				$ret['process']  = 'deactivate';
				$ret['action']   = 'core_deactivate';
				$ret['button']   = __( 'Deactivate License', 'gppro' );
				$ret['message']  = __( 'This license key has been activated.', 'gppro' );
				echo json_encode( $ret );
				die();
			}
		}

		// Somehow we got to the end...and we don't know how.
		$ret['success'] = false;
		$ret['errcode'] = 'ERROR_UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Call deactivation.
	 *
	 * @return void
	 */
	public function core_deactivate() {

		// First delete any transients, just in case.
		delete_transient( 'gppro_core_license_check' );
		delete_transient( 'gppro_core_license_verify' );

		// Get plugin items from DB, with a backup for the $_POST value.
		if ( false === $license = Genesis_Palette_Pro::license_data( 'license' ) ) {
			$license = ! empty( $_POST['license'] ) ? esc_attr( $_POST['license'] ) : '';
		}

		// Bail if no license.
		if ( empty( $license ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_LICENSE';
			$ret['message'] = __( 'No license key was stored.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Run a quick security check.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAIL';
			$ret['message'] = __( 'The security key was not available.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Data to send in our API request.
		$check  = GP_Pro_Licensing::api_license_key_check( $license, 'deactivate_license' );

		// No data came back.
		if ( empty( $check ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_RETURN';
			$ret['message'] = __( 'No data coudl be found for this license key.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Set my status variable.
		$status = ! empty( $check['status'] ) ? sanitize_key( $check['status'] ) : '';

		// No status. not sure why.
		if ( empty( $status ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_STATUS';
			$ret['message'] = __( 'This license key could not be verified.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// If we have an error code.
		if ( is_array( $check ) && ! empty( $check['errcode'] ) && ! empty( $check['message'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = $check['errcode'];
			$ret['message'] = $check['message'];
			echo json_encode( $ret );
			die();
		}

		// We didn't get the deactivated status.
		if ( 'deactivated' !== $status ) {
			$ret['success'] = false;
			$ret['errcode'] = 'LICENSE_FAIL';
			$ret['message'] = __( 'This license could not be deactivated.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Deactivation was good. LETS GO.
		if ( 'deactivated' === $status ) {

			// Delete the various data.
			delete_option( 'gppro_core_active' );
			delete_option( 'gppro_license_metadata' );
			delete_option( 'gppro_core_config_key' );

			$ret['success'] = true;
			$ret['errcode'] = null;
			$ret['process'] = 'activate';
			$ret['action']  = 'core_activate';
			$ret['button']  = __( 'Activate License', 'gppro' );
			$ret['message'] = __( 'This license key has been deactivated.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Somehow we got to the end...and we don't know how.
		$ret['success'] = false;
		$ret['errcode'] = 'ERROR_UNKNOWN';
		$ret['message'] = __( 'There was an unknown error.', 'gppro' );
		echo json_encode( $ret );
		die();
	}

	/**
	 * Process the help request.
	 *
	 * @return void
	 */
	public function gppro_support_request() {

		// Bail if the required fields aren't there.
		if ( empty( $_POST['name'] ) || empty( $_POST['email'] ) || empty( $_POST['text'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'MISSING_FIELDS';
			$ret['message'] = __( 'Required fields were not entered.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Make sure a nonce was passed.
		if ( empty( $_POST['nonce'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_MISSING';
			$ret['message'] = __( 'A nonce was not provided.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Check to see if our nonce verification failed.
		if ( false === $check = check_ajax_referer( 'gppro_support_nonce', 'nonce', false ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NONCE_FAILED';
			$ret['message'] = __( 'We\'re sorry, but the support request could not be sent. Please send an email to help@reaktivstudios.com.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Set and sanitize variables posted via AJAX.
		$name   = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$email  = sanitize_email( wp_unslash( $_POST['email'] ) );
		$text   = sanitize_text_field( wp_unslash( $_POST['text'] ) );

		// Put them in an array.
		$data   = array(
			'name'  => $name,
			'email' => $email,
			'text'  => $text,
		);

		// Fetch the details.
		if ( false === $details = GP_Pro_Support::get_help_details( $data ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'NO_DETAILS';
			$ret['message'] = __( 'We\'re sorry, but the support request could not be sent. Please send an email to help@reaktivstudios.com.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// First attempt the API request.
		if ( false !== $process = GP_Pro_Support::process_help_api( $details ) ) {
			$ret['success'] = true;
			$ret['message'] = __( 'Success! Your request has been sent. You\'ll be hearing from us shortly. If you do not get notification, please email us at help@reaktivstudios.com.', 'gppro' );
			$ret['method']  = 'API';
			echo json_encode( $ret );
			die();
		}

		// Now attempt the email fallback request.
		if ( false !== $fallback = GP_Pro_Support::process_help_email( $details ) ) {
			$ret['success'] = true;
			$ret['message'] = __( 'Success! Your request has been sent. You\'ll be hearing from us shortly. If you do not get notification, please email us at help@reaktivstudios.com.', 'gppro' );
			$ret['method']  = 'email';
			echo json_encode( $ret );
			die();
		}

		// Error out of both methods failed.
		if ( false === $process && false === $fallback ) {
			$ret['success'] = false;
			$ret['errcode'] = 'SEND_FAILED';
			$ret['message'] = __( 'We\'re sorry, but the support request could not be sent. Please send an email to help@reaktivstudios.com.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Send back the response.
		$ret['success'] = true;
		$ret['message'] = __( 'Success! Your request has been sent. You\'ll be hearing from us shortly. If you do not get notification, please email us at help@reaktivstudios.com.', 'gppro' );
		echo json_encode( $ret );
		die();
	}


	/**
	 * Do a check on the existing key to get the various information about it.
	 *
	 * @param  string $license  The license key being checked.
	 * @param  string $key      A specific array key to return, otherwise the entire thing.
	 *
	 * @return array  $data     The license status data.
	 */
	public static function fetch_license_data( $license = '', $key = '' ) {

		// If we had no license key passed, attempt to get it from the stored into.
		$license    = ! empty( $license ) ? $license : Genesis_Palette_Pro::license_data( 'license' );

		// Bail if no license key is available at all.
		if ( empty( $license ) ) {
			return false;
		}

		// Set our return.
		$ret    = array();

		// Data to send in our API request.
		$args   = array(
			'edd_action'    => 'check_license',
			'license'       => trim( $license ),
			'item_name'     => urlencode( GPP_ITEM_NAME ), // The name of our product in EDD.
			'url'           => home_url(),
		);

		// Call the custom API.
		$response   = wp_remote_post( GPP_STORE_URL, array( 'timeout' => GP_Pro_Utilities::get_timeout_val(), 'body' => $args ) );

		// Make sure the response came back okay.
		if ( is_wp_error( $response ) ) {

			// Fetch my error code.
			$code   = $response->get_error_code();
			$code   = ! empty( $code ) ? strtoupper( $code ) : 'API_REQUEST_FAIL';

			// Format the response.
			$ret['success'] = false;
			$ret['errmsg']  = $response->get_error_message();
			$ret['errcode'] = esc_attr( $code );
			$ret['message'] = __( 'The activation server is not available.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Decode the license data.
		$body   = wp_remote_retrieve_body( $response );

		// Make sure the response came back okay.
		if ( empty( $body ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'API_RETRIEVE_FAIL';
			$ret['message'] = __( 'The activation server did not return any information.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Get the license data items from the return.
		$items  = json_decode( GP_Pro_Utilities::remove_utf8_bom( $body ), true );

		// Ensure the return was successful
		if ( empty( $items['success'] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'API_RETURN_FAIL';
			$ret['message'] = __( 'The information request was not successful.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// Set an empty data array to build on.
		$data   = array();

		// Pull out my individual data pieces.
		$data['license'] = trim( $license );
		$data['status']  = ! empty( $items['license'] ) ? sanitize_text_field( $items['license'] ) : 'unknown';
		$data['expire']  = ! empty( $items['expires'] ) ? sanitize_text_field( $items['expires'] ) : '';
		$data['limit']   = ! empty( $items['license_limit'] ) ? absint( $items['license_limit'] ) : 0;
		$data['count']   = ! empty( $items['site_count'] ) ? absint( $items['site_count'] ) : 0;
		$data['remain']  = ! empty( $items['activations_left'] ) ? sanitize_text_field( $items['activations_left'] ) : 0;

		// Also get a human version of the amount of time left on the license.
		$data['until']   = ! empty( $data['expire'] ) ? human_time_diff( strtotime( $data['expire'] ) ) : '';

		// Update our option with the license data.
		update_option( 'gppro_license_data', $data );

		// If the requested key does not exist, return a failure.
		if ( ! empty( $key ) && ! isset( $data[ $key ] ) ) {
			$ret['success'] = false;
			$ret['errcode'] = 'INVALID_ARRAY_KEY';
			$ret['message'] = __( 'The requested data does not exist.', 'gppro' );
			echo json_encode( $ret );
			die();
		}

		// And return the data either with individual key or the whole thing.
		return isset( $data[ $key ] ) ? $data[ $key ] : $data;
	}

	// End class.
}

// Instantiate our class.
$GP_Pro_Ajax = new GP_Pro_Ajax();
$GP_Pro_Ajax->init();
