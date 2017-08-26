<?php
/**
 * Genesis Design Palette Pro - Admin Module
 *
 * Contains functions for WP admin items
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
 * Admin Class.
 *
 * Contains all generic admin functionality.
 */
class GP_Pro_Admin
{

	/**
	 * Handle our checks then call our hooks.
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

		// Call the functions.
		add_action( 'load-genesis_page_genesis-palette-pro',    array( $this, 'remove_child_prompt'     )           );
		add_action( 'admin_enqueue_scripts',                    array( $this, 'admin_styles'            )           );
		add_action( 'admin_enqueue_scripts',                    array( $this, 'admin_scripts'           )           );
		add_action( 'admin_init',                               array( $this, 'register_settings'       )           );
		add_action( 'admin_init',                               array( $this, 'protection_files'        )           );
		add_action( 'admin_menu',                               array( $this, 'admin_menu'              ),  99      );
		add_filter( 'plugin_action_links',                      array( $this, 'quick_link'              ),  10, 2   );
		add_filter( 'admin_body_class',                         array( $this, 'admin_body_class'        )           );
		add_filter( 'admin_footer_text',                        array( $this, 'admin_footer'            )           );
		add_filter( 'upload_mimes',                             array( $this, 'favicon_mime_type'       )           );
		add_filter( 'wp_auth_check_load',                       array( $this, 'remove_user_auth'        ),  10, 2   );
		add_filter( 'gppro_admin_title',                        array( $this, 'admin_title'             )           );
		add_filter( 'gppro_section_inline_general_body',        array( $this, 'filter_favicon_setting'  ),  99, 2   );
		add_filter( 'gppro_section_inline_build_settings',      array( $this, 'show_addons_link'        ),  99, 2   );
	}

	/**
	 * Remove the warning prompt about using a child theme.
	 *
	 * @return void
	 */
	public function remove_child_prompt() {

		// Check for our current DPP screen and if on our page, remove the prompt.
		if ( false !== $page = GP_Pro_Utilities::check_current_dpp_screen() ) {
			remove_action( 'admin_notices', 'genesis_use_child_theme_notice' );
		}
	}

	/**
	 * Swap admin title tag if using a child theme.
	 *
	 * @param  string $title  The existing title being shown.
	 *
	 * @return string $title  The modified title being shown.
	 */
	public function admin_title( $title ) {

		// Do my child theme check. if fail, return the title.
		if ( false === $data = GP_Pro_Helper::is_child_theme() ) {
			return $title;
		}

		// Return the name if we have it, or the fallback.
		return ! empty( $data['name'] ) ? sprintf( __( 'Genesis Design Palette Pro - %s', 'gppro' ), esc_attr( $data['name'] ) ) : $title;
	}

	/**
	 * Add our "settings" links to the plugins page.
	 *
	 * @param  array  $links  The existing array of links.
	 * @param  string $file   The file we are actually loading from.
	 *
	 * @return array  $links  The updated array of links.
	 */
	public function quick_link( $links, $file ) {

		// Check to make sure we are on the correct plugin.
		if ( ! empty( $file ) && $file == GPP_BASE ) {

			// Get the link based on license status.
			$quick  = self::quick_link_url();

			// Add the link to the array.
			array_push( $links, $quick );
		}

		// Return the links.
		return $links;
	}

	/**
	 * Return the general link or the license directed if missing.
	 *
	 * @return mixed  The markup and URL to display.
	 */
	public static function quick_link_url() {

		// Make our base URL.
		$link   = GP_Pro_Helper::get_settings_url();

		// Set our base URL, which is just the settings link.
		$base   = '<a href="' . esc_url( $link ) . '">' . __( 'Settings', 'gppro' ) . '</a>';

		// Do our local check first to avoid wasting any time.
		if ( false !== $local = GP_Pro_Utilities::check_local_dev() ) {
			return $base;
		}

		// Run the active license check.
		$check  = Genesis_Palette_Pro::license_data( 'status' );

		// Return the link based on license status.
		switch ( $check ) {

			// Local dev, which we won't require a license.
			case 'local':
				return $base;
				break;

			// Active license.
			case 'valid':
				return $base;
				break;

			// Expired license, return renewal link.
			case 'expired':
				return '<a style="font-weight:700;color:#ee7600;" target="_blank" href="' . GP_Pro_Licensing::get_renewal_link() . '">' . __( 'Renew License Key', 'gppro' ) . '</a>';
				break;

			// Default for anything else.
			default:
				return '<a style="font-weight:700;color:#ee7600;" href="' . GP_Pro_Helper::get_settings_url( 'support_section' ) . '">' . __( 'Enter License Key', 'gppro' ) . '</a>';
				break;
		}
	}

	/**
	 * Add a custom body class on the admin page for easier JS targeting.
	 *
	 * @param  string $classes  All the existing classes in a single string.
	 *
	 * @return string $classes  The updated string with ours.
	 */
	public function admin_body_class( $classes ) {

		// Check for our current DPP screen and if on our page, add our class.
		if ( false !== $check = GP_Pro_Utilities::check_current_dpp_screen() ) {
			$classes .= ' gppro-admin-page';
		}

		// Return classes.
		return $classes;
	}

	/**
	 * Allow .ico and .gif files to be uploaded in the native WP media manager.
	 *
	 * @param  array $mimes  The currently allowed MIME types.
	 *
	 * @return array $mimes  The updated array of allowed MIME types.
	 */
	public function favicon_mime_type( $mimes ) {

		// Check for gif support and add it if missing.
		if ( ! isset( $mimes['gif'] ) ) {
			$mimes['gif'] = 'image/gif';
		}

		// Check for ico support and add it if missing.
		if ( ! isset( $mimes['ico'] ) ) {
			$mimes['ico'] = 'image/x-icon';
		}

		// Send back array of MIME types.
		return $mimes;
	}

	/**
	 * Remove the constant user auth check on the main DPP screen to prevent issues with iframe loading.
	 *
	 * @param  bool      $show    Whether to load the authentication check.
	 * @param  WP_Screen $screen  The current screen object.
	 *
	 * @return bool      $show    Whether to load the authentication check.
	 */
	public function remove_user_auth( $show, $screen ) {
		return is_object( $screen ) && 'genesis_page_genesis-palette-pro' === $screen->base ? false : $show;
	}

	/**
	 * Load our admin side CSS.
	 *
	 * @return void
	 */
	public function admin_styles() {

		// Check for our current DPP screen.
		if ( false === $check = GP_Pro_Utilities::check_current_dpp_screen() ) {
			return;
		}

		// Load CSS that is not affected by script_debug.
		wp_enqueue_style( 'wp-color-picker' );

		// Set our prefix.
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.css' : '.min.css';

		// Load files in either minified or non-minified versions as requested.
		wp_enqueue_style( 'gppro-admin', plugins_url( 'css/admin' . $suffix, __FILE__ ), array(), GPP_VER, 'all' );

		// Allow other themes / plugins to piggyback on our call.
		do_action( 'gppro_load_admin_styles' );
	}

	/**
	 * Load our admin side JS.
	 *
	 * @return void
	 */
	public function admin_scripts() {

		// Check for our current DPP screen.
		if ( false === $check = GP_Pro_Utilities::check_current_dpp_screen() ) {
			return;
		}

		// Get our custom colorpicker file based on WP version.
		$picker = version_compare( get_bloginfo( 'version' ), '4.0', '>=' ) ? 'dpp-picker.4.0' : 'dpp-picker.3.9';

		// Set our prefix.
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		// Unregister the default colorpicker.
		wp_deregister_script( 'wp-color-picker' );

		// Register our patched one.
		wp_register_script( 'wp-color-picker', plugins_url( 'js/ext/' . $picker . $suffix, __FILE__ ), array( 'iris' ), false, true );

		// Load media pieces for uploaders.
		wp_enqueue_media();

		// Load our fullscreen JS library.
		wp_enqueue_script( 'screenfull', plugins_url( 'js/ext/screenfull' . $suffix, __FILE__ ), array( 'jquery' ), '2.0.0', true );

		// Load and localize the color picker.
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'picker-alpha', plugins_url( 'js/ext/picker-alpha' . $suffix, __FILE__ ), array( 'wp-color-picker' ), '1.0.0', 'all' );
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear'         => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick'          => __( 'Select Color' ),
			'current'       => __( 'Current Color' ),
		));

		// Only load the preview JS if we have a preview.
		if ( false === apply_filters( 'gppro_disable_preview_pane', false ) ) {
			wp_enqueue_script( 'gppro-preview', plugins_url( 'js/preview'.$suffix, __FILE__ ), array( 'jquery' ), GPP_VER, true );
		}

		// Load and localize our admin file.
		wp_enqueue_script( 'gppro-admin', plugins_url( 'js/admin'.$suffix, __FILE__ ), array( 'wp-color-picker', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-tooltip', 'jquery' ), GPP_VER, true );
		wp_localize_script( 'gppro-admin', 'adminData', array(
			'colorchoice'       => apply_filters( 'gppro_picker_defaults', true ),
			'gfontlink'         => '//fonts.googleapis.com/css?family=',
			'clearconfirm'      => __( 'Are you sure you want to delete all the settings?', 'gppro' ),
			'errormessage'      => __( 'There was an error parsing your data.', 'gppro' ),
			'supporterror'      => __( 'Please see the areas in red.', 'gppro' ),
			'uploadtitle'       => __( 'Upload Your Header Image', 'gppro' ),
			'favicontitle'      => __( 'Upload Your Favicon file', 'gppro' ),
			'uploadbutton'      => __( 'Attach', 'gppro' ),
			'tooltip_my'        => apply_filters( 'gppro_tooltip_pos_my', 'left+15 center' ),
			'tooltip_at'        => apply_filters( 'gppro_tooltip_pos_at', 'right center' ),
			'base_font_size'    => GP_Pro_Helper::base_font_size(),
			'use_rems'          => GP_Pro_Helper::rems_enabled(),
			'basepreview'       => is_ssl() ? home_url( '/', 'https' ) : home_url( '/', 'http' ),
			'perhapsSerial'     => GP_Pro_Helper::maybe_serialize_vars(),
		));

		// Turn off heartbeat.
		wp_deregister_script( 'heartbeat' );

		// Allow other themes / plugins to piggyback on our call.
		do_action( 'gppro_load_admin_scripts' );
	}

	/**
	 * Add attribution link to settings page.
	 *
	 * @param  string $text  The existing footer text.
	 *
	 * @return string $text  The modified footer text.
	 */
	public function admin_footer( $text ) {

		// Check for our current DPP screen.
		if ( false === $check = GP_Pro_Utilities::check_current_dpp_screen() ) {
			return $text;
		}

		// Set our footer link with GA campaign tracker.
		$link	= 'http://reaktivstudios.com/?utm_source=plugin&utm_medium=link&utm_campaign=dpp';

		// Build footer markup.
		$text	= sprintf( __( '<span id="footer-thankyou">This plugin brought to you by the fine folks at <a href="%1$s" title="%2$s" target="_blank">Reaktiv Studios</a></span>', 'gppro' ), esc_url( $link ), esc_html( 'Reaktiv Studios', 'gppro' ) );

		// Return the text, fitered.
		return apply_filters( 'gppro_admin_footer_text', $text );
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting( 'gppro-settings', 'gppro-settings' );
	}

	/**
	 * Call settings page.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'genesis', __( 'Genesis Design Palette Pro', 'gppro' ), __( 'Design Palette Pro', 'gppro' ), apply_filters( 'gppro_caps', 'manage_options' ), 'genesis-palette-pro', array( $this, 'admin_page' ) );
	}

	/**
	 * Load our settings page.
	 *
	 * @return mixed  The settings page.
	 */
	public function admin_page() {

		// Bail without our setup class.
		if ( ! class_exists( 'GP_Pro_Setup' ) ) {
			return;
		}

		// Fetch our blocks abd bail without them.
		if ( false === $blocks = GP_Pro_Setup::blocks() ) {
			return;
		}

		// Set our admin title with filter for child theme add ons.
		$title  = apply_filters( 'gppro_admin_title', __( 'Genesis Design Palette Pro', 'gppro' ) );

		// Start the wrapper itself.
		echo '<div class="wrap gppro-wrap">';

			echo '<h1 class="gppro-admin-title">' . esc_html( $title ) . '</h1>';

			echo '<div class="gppro-settings-wrapper">';

				echo '<div class="gppro-block gppro-actions gppro-actions-top">';
					echo GP_Pro_Setup::buttons();
				echo '</div>';

				echo '<div class="gppro-block gppro-options">';

					echo '<div class="gppro-tabs gppro-column">';
						echo GP_Pro_Setup::tabs( $blocks );
					echo '</div>';

					echo '<div class="gppro-sections gppro-column">';
						echo GP_Pro_Sections::sections( $blocks );
					echo '</div>';

				echo '</div>';

				echo '<div class="gppro-block gppro-actions gppro-actions-bottom">';
					echo GP_Pro_Setup::buttons();
				echo '</div>';

			echo '</div>';

			echo GP_Pro_Setup::preview_block();

		echo '</div>';
	}

	/**
	 * Creates blank index.php and .htaccess files.
	 *
	 * This function runs approximately once per week in order to ensure all folders
	 * have their necessary protection files.
	 *
	 * @since 1.0.0.0
	 * @return void
	 */
	public static function protection_files() {

		// If we aren't using apache, just set it true for a month and bail.
		if ( ! stristr( sanitize_key( $_SERVER['SERVER_SOFTWARE'] ), 'apache' ) ) {
			set_transient( 'gppro_check_protection_files', true, ( WEEK_IN_SECONDS * 4 ) );
			return;
		}

		// Do the transient check.
		if ( false === get_transient( 'gppro_check_protection_files' ) ) {

			// Grab our folder setup.
			$folder = Genesis_Palette_Pro::filebase( 'root' );

			// Bail without the folder.
			if ( ! isset( $folder ) ) {
				return false;
			}

			// Get the htaccess file.
			$file   = self::get_htaccess();

			// Kill the trailing slash.
			if ( substr( $folder, -1 ) == '/' ) {
				$folder = substr( $folder, 0, -1 );
			}

			// Top level blank index.php file.
			if ( ! file_exists( $folder . '/index.php' ) ) {
				@file_put_contents( $folder . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
			}

			// Top level .htaccess file.
			if ( ! file_exists( $folder . '/.htaccess' ) && ! empty( $file ) ) {
				@file_put_contents( $folder . '/.htaccess', $file );
			}

			// Check for the files once per day.
			set_transient( 'gppro_check_protection_files', true, WEEK_IN_SECONDS );

		} // Finish transient check.
	}

	/**
	 * Get the setup for the htaccess file.
	 *
	 * @return string $file  The data for the htaccess file.
	 */
	public static function get_htaccess() {

		// Empty start.
		$file   = '';

		// The file build.
		$file  .= 'Options -Indexes' . "\n";
		$file  .= 'RewriteEngine on' . "\n";
		$file  .= 'RewriteCond %{REQUEST_URI} !\.(css)$ [NC]' . "\n";
		$file  .= 'RewriteRule .* - [F,L]' . "\n";

		// Filter it and return.
		return apply_filters( 'gppro_htaccess_content', $file );
	}

	/**
	 * Check for the customizer favicon and remove the field if present.
	 *
	 * @param  array  $items  The array of items inside the setting area.
	 * @param  string $class  The body class applied.
	 *
	 * @return array  $items  The updated array of items inside the setting area.
	 */
	public function filter_favicon_setting( $items, $class ) {

		// Check for the setting first. If no icon is present, just return what we have.
		if ( false === $check = Genesis_Palette_Pro::plugin_option_check( 'site_icon' ) ) {
			return $items;
		}

		// Add description field for add on link.
		$items['site-favicon-setup']  = array(
			'title' => __( 'Site Favicon', 'gppro' ),
			'data'  => array(
				'favicon-customizer-setup'   => array(
					'desc'  => __( 'You currently have a favicon set within the customizer.', 'gppro' ),
					'input' => 'description',
					'class' => 'solid-description',
				),
			),
		);

		// And return the resulting setup.
		return $items;
	}

	/**
	 * Add a link at the bottom for the plugin addons.
	 *
	 * @param  array  $items  The array of items inside the setting area.
	 * @param  string $class  The body class applied.
	 *
	 * @return array  $items  The updated array of items inside the setting area.
	 */
	public function show_addons_link( $items, $class ) {

		// If we are a multisite, bail because it won't work.
		if ( is_multisite() ) {
			return $items;
		}

		// Build URL for add ons.
		$link   = add_query_arg( array( 'tab' => 'favorites', 'user' => 'reaktivstudios' ), admin_url( 'plugin-install.php' ) );

		// Add section header for add on link.
		$items['section-break-addon-link']  = array(
			'break' => array(
				'type'  => 'full',
				'title' => __( 'Add Ons', 'gppro' ),
				'text'  => __( 'Want to level up your Design Palette?', 'gppro' ),
			),
		);

		// Add description field for add on link.
		$items['addon-link-setup']  = array(
			'title' => '',
			'data'  => array(
				'addon-link-desc'   => array(
					'desc'  => __( '<a href="' . esc_url( $link ) . '" target="_blank">Click here to view all available add-ons</a>', 'gppro' ),
					'input' => 'description',
					'class' => 'solid-description center-description',
				),
			),
		);

		// And return the resulting setup.
		return $items;
	}

	// End class.
}

// Instantiate our class.
$GP_Pro_Admin = new GP_Pro_Admin();
$GP_Pro_Admin->init();
