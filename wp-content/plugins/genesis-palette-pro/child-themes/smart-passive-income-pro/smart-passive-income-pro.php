<?php
/**
 * Genesis Design Palette Pro - Smart Passive Income Pro
 *
 * Genesis Palette Pro add-on for the Smart Passive Income Pro child theme.
 *
 * @package Design Palette Pro
 * @subpackage Smart Passive Income Pro
 * @version 1.0.0 (child theme version)
 */

/*  Copyright 2017 Reaktiv Studios

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
 * CHANGELOG:
 * 2015-11-15: Initial development
 */

if ( ! class_exists( 'GP_Pro_Smart_Passive_Income_Pro' ) ) {

class GP_Pro_Smart_Passive_Income_Pro {

	/**
	 * Static property to hold our singleton instance
	 * @var GP_Pro_Smart_Passive_Income_Pro
	 */
	public static $instance = null;

	/**
	 * This is our constructor
	 *
	 * @return array
	 */
	private function __construct() {
		// GP Pro general
		add_filter( 'gppro_set_defaults',                       array( $this, 'set_defaults'             ), 15    );

		// font stack modifications
		add_filter( 'gppro_webfont_stacks',                     array( $this, 'google_webfonts'          )        );
		add_filter( 'gppro_font_stacks',                        array( $this, 'font_stacks'              ), 20    );
        add_filter( 'gppro_default_css_font_weights',           array( $this, 'font_weights'             ),  20   );

        // reset CSS builders
        add_filter( 'gppro_css_builder',                        array( $this, 'css_builder_filters'      ), 50, 3 );

		// GP Pro new section additions
		add_filter( 'gppro_admin_block_add',                    array( $this, 'homepage'                 ), 25    );
		add_filter( 'gppro_sections',                           array( $this, 'homepage_section'         ), 10, 2 );

        add_filter( 'gppro_admin_block_add',                    array( $this, 'footer_banner'            ), 65    );
		add_filter( 'gppro_sections',                           array( $this, 'footer_banner_section'    ), 10, 2 );

		// GP Pro section item removals / additions
		add_filter( 'gppro_section_inline_general_body',        array( $this, 'general_body'             ), 15, 2 );
		add_filter( 'gppro_section_inline_header_area',         array( $this, 'header_area'              ), 15, 2 );
		add_filter( 'gppro_section_inline_navigation',          array( $this, 'navigation'               ), 15, 2 );
		add_filter( 'gppro_section_inline_post_content',        array( $this, 'post_content'             ), 15, 2 );
		add_filter( 'gppro_section_inline_content_extras',      array( $this, 'content_extras'           ), 15, 2 );
		add_filter( 'gppro_section_inline_comments_area',       array( $this, 'comments_area'            ), 15, 2 );
		add_filter( 'gppro_section_inline_main_sidebar',        array( $this, 'main_sidebar'             ), 15, 2 );
        add_filter( 'gppro_section_inline_entry_content',       array( $this, 'entry_content'            ), 15, 2 );
        add_filter( 'gppro_sections',                           array( $this, 'genesis_widgets_section'  ), 20, 2 );


		// Enable after entry widget sections
		add_filter( 'gppro_section_inline_content_extras',      array( 'GP_Pro_Sections', 'after_entry_widget_area' ), 15, 2 );
		add_filter( 'gppro_section_after_entry_widget_area',    array( $this, 'after_entry'                         ), 15, 2 );

        // add entry content defaults
		add_filter( 'gppro_set_defaults',                       array( $this, 'entry_content_defaults'              ), 40     );

		// Enable Genesis eNews sections
		add_filter( 'gppro_enews_set_defaults',                 array( $this, 'enews_defaults'                      ), 15    );
      	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * returns it.
	 *
	 * @return void
	 */
	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * swap Google webfont source to native
	 *
	 * @return string $webfonts
	 */
	public function google_webfonts( $webfonts ) {

		// bail if plugin class isn't present
		if ( ! class_exists( 'GP_Pro_Google_Webfonts' ) ) {
			return;
		}

		// swap Roboto if present
		if ( isset( $webfonts['roboto'] ) ) {
			$webfonts['roboto']['src'] = 'native';
		}

		return $webfonts;
	}

	/**
	 * add the custom font stacks
	 *
	 * @param  [type] $stacks [description]
	 * @return [type]         [description]
	 */
	public function font_stacks( $stacks ) {

		// check Roboto
		if ( ! isset( $stacks['sans']['roboto'] ) ) {
			// add the array
			$stacks['sans']['roboto'] = array(
				'label' => __( 'Roboto', 'gppro' ),
				'css'   => '"Roboto", sans-serif',
				'src'   => 'native',
				'size'  => '0',
			);
		}

		// send it back
		return $stacks;
	}

    /**
	 * add the extra bold weight (900) used in this theme
	 *
	 * @param  array	$weights 	the standard array of weights
	 * @return array	$weights 	the updated array of weights
	 */
	public function font_weights( $weights ) {

		// add the 600 weight if not present
		if ( empty( $weights['600'] ) ) {
			$weights['600'] = __( '600 (Semibold)', 'gppro' );
		}

        // add the 900 weight if not present
		if ( empty( $weights['900'] ) ) {
			$weights['900'] = __( '900 (Extrabold)', 'gppro' );
		}

		// return font weights
		return $weights;
	}

	/**
	 * swap default values to match Smart Passive Income Pro
	 *
	 * @return string $defaults
	 */
	public function set_defaults( $defaults ) {
		$changes = array(
			// general body
			'body-color-back-thin'                          => '',  // Removed
			'body-color-back-main'                          => '#ffffff',
			'body-color-text'                               => '#333333',
			'body-color-link'                               => '#009946',
			'body-color-link-hov'                           => '#333333',
            'body-type-stack'                               => 'roboto',
			'body-type-size'                                => '18',
			'body-type-weight'                              => '400',
			'body-type-style'                               => 'normal',

			// site header
			'header-color-back'                             => '#2d2c2a',
            'header-padding-top'                            => '60',
			'header-padding-bottom'                         => '60',
			'header-padding-left'                           => '0',
			'header-padding-right'                          => '0',

			// site title
			'site-title-text'                               => '#ffffff',
			'site-title-stack'                              => 'roboto',
			'site-title-size'                               => '40',
			'site-title-weight'                             => '900',
			'site-title-transform'                          => 'none',
			'site-title-align'                              => 'left',
			'site-title-style'                              => 'normal',
            'site-title-padding-top'                        => '16',
			'site-title-padding-bottom'                     => '16',
			'site-title-padding-left'                       => '0',
			'site-title-padding-right'                      => '0',

			// site description
			'site-desc-display'                             => '',  // Removed
			'site-desc-text'                                => '',  // Removed
			'site-desc-stack'                               => '',  // Removed
			'site-desc-size'                                => '',  // Removed
			'site-desc-weight'                              => '',  // Removed
			'site-desc-transform'                           => '',  // Removed
			'site-desc-align'                               => '',  // Removed
			'site-desc-style'                               => '',  // Removed

			// header navigation
			'header-nav-item-back'                          => '',  // Removed
			'header-nav-item-back-hov'                      => '',  // Removed
			'header-nav-item-link'                          => '',  // Removed
			'header-nav-item-link-hov'                      => '',  // Removed
			'header-nav-stack'                              => '',  // Removed
			'header-nav-size'                               => '',  // Removed
			'header-nav-weight'                             => '',  // Removed
			'header-nav-transform'                          => '',  // Removed
			'header-nav-style'                              => '',  // Removed
			'header-nav-item-padding-top'                   => '',  // Removed
			'header-nav-item-padding-bottom'                => '',  // Removed
			'header-nav-item-padding-left'                  => '',  // Removed
			'header-nav-item-padding-right'                 => '',  // Removed

			// header widgets
			'header-widget-title-color'                     => '#ffffff',
			'header-widget-title-stack'                     => 'roboto',
			'header-widget-title-size'                      => '18',
			'header-widget-title-weight'                    => '900',
			'header-widget-title-transform'                 => 'uppercase',
			'header-widget-title-align'                     => 'right',
			'header-widget-title-style'                     => 'normal',
			'header-widget-title-margin-bottom'             => '20',
			'header-widget-content-text'                    => '#ffffff',
			'header-widget-content-link'                    => '#b4151b',
			'header-widget-content-link-hov'                => '#ffffff',
			'header-widget-content-stack'                   => 'roboto',
			'header-widget-content-size'                    => '18',
			'header-widget-content-weight'                  => '400',
			'header-widget-content-align'                   => 'right',
			'header-widget-content-style'                   => 'normal',
			'header-widget-button-link'                     => '#ffffff',
			'header-widget-button-link-hov'                 => '#ffffff',
			'header-widget-button-back'                     => '#0e763c',
			'header-widget-button-back-hov'                 => '#009946',
            'header-widget-button-stack'                    => 'roboto',
			'header-widget-button-size'                     => '14',
			'header-widget-button-weight'                   => '700',
            'header-widget-button-style'                    => 'normal',
            'header-widget-button-transform'                => 'uppercase',
            'header-widget-button-border-radius'            => '4',
            'header-widget-button-padding-top'              => '12',
            'header-widget-button-padding-bottom'           => '12',
            'header-widget-button-padding-left'             => '20',
            'header-widget-button-padding-right'            => '20',

            // primary navigation
            'primary-nav-area-back'                         => '#b4151b',
            'primary-nav-top-stack'                         => 'roboto',
            'primary-nav-top-size'                          => '16',
            'primary-nav-top-desc-size'                     => '12',
            'primary-nav-top-weight'                        => '700',
            'primary-nav-top-transform'                     => 'none',
            'primary-nav-top-align'                         => 'left',
            'primary-nav-top-style'                         => 'normal',
            'primary-nav-top-item-base-back'                => '',  // Removed
            'primary-nav-top-item-base-back-hov'            => '#b4151b',
            'primary-nav-top-item-base-link'                => '#fffefe',
            'primary-nav-top-item-base-link-hov'            => '#ffffff',
            'primary-nav-top-item-active-back'              => '#b4151b',
            'primary-nav-top-item-active-back-hov'          => '#b4151b',
            'primary-nav-top-item-active-link'              => '#ffffff',
            'primary-nav-top-item-active-link-hov'          => '#ffffff',
            'primary-nav-top-item-padding-top'              => '32',
            'primary-nav-top-item-padding-bottom'           => '32',
            'primary-nav-top-item-padding-left'             => '32',
            'primary-nav-top-item-padding-right'            => '32',
            'primary-nav-drop-stack'                        => 'roboto',
            'primary-nav-drop-size'                         => '14',
            'primary-nav-drop-weight'                       => '700',
            'primary-nav-drop-transform'                    => 'none',
            'primary-nav-drop-align'                        => 'left',
            'primary-nav-drop-style'                        => 'normal',
            'primary-nav-drop-item-base-back'               => '#e5554e',
            'primary-nav-drop-item-base-back-hov'           => '#930000',
            'primary-nav-drop-item-base-link'               => '#fffefe',
            'primary-nav-drop-item-base-link-hov'           => '#ffffff',
            'primary-nav-drop-item-active-back'             => '#930000',
            'primary-nav-drop-item-active-back-hov'         => '#930000',
            'primary-nav-drop-item-active-link'             => '#ffffff',
            'primary-nav-drop-item-active-link-hov'         => '#ffffff',
            'primary-nav-drop-item-padding-top'             => '20',
            'primary-nav-drop-item-padding-bottom'          => '20',
            'primary-nav-drop-item-padding-left'            => '30',
            'primary-nav-drop-item-padding-right'           => '30',
            'primary-nav-drop-border-color'                 => '#930000',
            'primary-nav-drop-border-style'                 => 'solid',
            'primary-nav-drop-border-width'                 => '1',

            // secondary navigation
            'secondary-nav-area-back'                       => '#b4151b',
            'secondary-nav-top-stack'                       => 'roboto',
            'secondary-nav-top-size'                        => '16',
            'secondary-nav-top-weight'                      => '600',
            'secondary-nav-top-transform'                   => 'none',
            'secondary-nav-top-align'                       => 'center',
            'secondary-nav-top-style'                       => 'normal',
            'secondary-nav-top-item-base-back'              => '',  // Removed
            'secondary-nav-top-item-base-back-hov'          => '',  // Removed
            'secondary-nav-top-item-base-link'              => '#ffffff',
            'secondary-nav-top-item-base-link-hov'          => '#ffffff',
            'secondary-nav-top-item-active-back'            => '',  // Removed
            'secondary-nav-top-item-active-back-hov'        => '',  // Removed
            'secondary-nav-top-item-active-link'            => '#ffffff',
            'secondary-nav-top-item-active-link-hov'        => '#ffffff',
            'secondary-nav-top-item-padding-top'            => '32',
            'secondary-nav-top-item-padding-bottom'         => '32',
            'secondary-nav-top-item-padding-left'           => '32',
            'secondary-nav-top-item-padding-right'          => '32',
            'secondary-nav-drop-stack'                      => '',  // Removed
            'secondary-nav-drop-size'                       => '',  // Removed
            'secondary-nav-drop-weight'                     => '',  // Removed
            'secondary-nav-drop-transform'                  => '',  // Removed
            'secondary-nav-drop-align'                      => '',  // Removed
            'secondary-nav-drop-style'                      => '',  // Removed
            'secondary-nav-drop-item-base-back'             => '',  // Removed
            'secondary-nav-drop-item-base-back-hov'         => '',  // Removed
            'secondary-nav-drop-item-base-link'             => '',  // Removed
            'secondary-nav-drop-item-base-link-hov'         => '',  // Removed
            'secondary-nav-drop-item-active-back'           => '',  // Removed
            'secondary-nav-drop-item-active-back-hov'       => '',  // Removed
            'secondary-nav-drop-item-active-link'           => '',  // Removed
            'secondary-nav-drop-item-active-link-hov'       => '',  // Removed
            'secondary-nav-drop-item-padding-top'           => '',  // Removed
            'secondary-nav-drop-item-padding-bottom'        => '',  // Removed
            'secondary-nav-drop-item-padding-left'          => '',  // Removed
            'secondary-nav-drop-item-padding-right'         => '',  // Removed
            'secondary-nav-drop-border-color'               => '',  // Removed
            'secondary-nav-drop-border-style'               => '',  // Removed
            'secondary-nav-drop-border-width'               => '',  // Removed

            // front page 1
            'home-1-widget-back'                            => '#b4151b',
            'home-1-widget-section-padding-top'             => '4%',
            'home-1-widget-section-padding-bottom'          => '4%',
            'home-1-widget-section-padding-left'            => '0',
            'home-1-widget-section-padding-right'           => '0',
            'home-1-widget-title-color'                     => '#ffffff',
            'home-1-widget-title-stack'                     => 'roboto',
            'home-1-widget-title-size'                      => '80',
            'home-1-widget-title-weight'                    => '900',
            'home-1-widget-title-transform'                 => 'none',
            'home-1-widget-title-style'                     => 'normal',
            'home-1-widget-title-margin-bottom'             => '30',
            'home-1-menu-color'                             => 'rgba( 255, 255, 255, .8 )',
            'home-1-menu-color-link'                        => 'rgba( 255, 255, 255, .8 )',
            'home-1-menu-color-hover'                       => 'rgba( 255, 255, 255, .8 )',
            'home-1-menu-back-link'                         => '',  //transparent
            'home-1-menu-back-hover'                        => 'rgba( 0, 0, 0, .2 );',
            'home-1-menu-stack'                             => 'roboto',
            'home-1-menu-size'                              => '18',
            'home-1-menu-weight'                            => '900',
            'home-1-menu-transform'                         => 'uppercase',

            // front page 2
            'home-2-widget-back'                            => '#333333',
            'home-2-section-padding-top'                    => '4%',
            'home-2-section-padding-bottom'                 => '4%',
            'home-2-section-padding-left'                   => '0',
            'home-2-section-padding-right'                  => '0',
            'home-2-widget-padding-top'                     => '60',
            'home-2-widget-padding-bottom'                  => '60',
            'home-2-widget-padding-left'                    => '0',
            'home-2-widget-padding-right'                   => '0',
            'home-2-widget-title-color'                     => '#ffffff',
            'home-2-widget-title-stack'                     => 'roboto',
            'home-2-widget-title-size'                      => '18',
            'home-2-widget-title-weight'                    => '900',
            'home-2-widget-title-transform'                 => 'uppercase',
            'home-2-widget-title-style'                     => 'normal',
            'home-2-widget-title-margin-bottom'             => '20',
            'home-2-widget-content-color'                   => '#ffffff',
            'home-2-widget-content-link'                    => '#ffffff',
            'home-2-widget-content-hover'                   => '#ffffff',
            'home-2-widget-content-stack'                   => 'roboto',
            'home-2-widget-content-size'                    => '32',
            'home-2-widget-content-weight'                  => '400',
            'home-2-widget-content-transform'               => 'none',
            'home-2-button-color'                           => '#ffffff',
            'home-2-button-color-hover'                     => '#ffffff',
            'home-2-button-back'                            => '#0e763c',
            'home-2-button-back-hover'                      => '#009946',
            'home-2-button-stack'                           => 'roboto',
            'home-2-button-size'                            => '24',
            'home-2-button-weight'                          => '700',
            'home-2-button-transform'                       => 'uppercase',
            'home-2-button-padding-top'                     => '20',
            'home-2-button-padding-bottom'                  => '20',
            'home-2-button-padding-left'                    => '25',
            'home-2-button-padding-right'                   => '25',
            'home-2-button-border-radius'                   => '4',

            // front page 3
            'home-3-top-widget-back'                        => '#3677aa',
            'home-3-section-padding-top'                    => '4%',
            'home-3-section-padding-bottom'                 => '4%',
            'home-3-section-padding-left'                   => '0',
            'home-3-section-padding-right'                  => '0',
            'home-3-section-divider-color'                  => 'rgba( 255, 255, 255, .16 )',
            'home-3-section-divider-style'                  => 'normal',
            'home-3-section-divider-width'                  => '2px',
            'home-3-widget-title-color'                     => '#ffffff',
            'home-3-widget-title-stack'                     => 'roboto',
            'home-3-widget-title-size'                      => '18',
            'home-3-widget-title-weight'                    => '900',
            'home-3-widget-title-transform'                 => 'uppercase',
            'home-3-widget-title-style'                     => 'normal',
            'home-3-widget-title-margin-bottom'             => '20',
            'home-3-large-heading'                           => '#ffffff',
            'home-3-large-heading-stack'                     => 'roboto',
            'home-3-large-heading-size'                      => '30',
            'home-3-large-heading-weight'                    => '900',
            'home-3-large-heading-transform'                 => 'none',
            'home-3-large-heading-style'                     => 'normal',
            'home-3-large-heading-margin-bottom'             => '10',
            'home-3-sub-heading'                             => '#ffffff',
            'home-3-sub-heading-stack'                       => 'roboto',
            'home-3-sub-heading-size'                        => '20',
            'home-3-sub-heading-weight'                      => '900',
            'home-3-sub-heading-transform'                   => 'none',
            'home-3-sub-heading-style'                       => 'normal',
            'home-3-sub-heading-margin-bottom'               => '10',
            'home-3-widget-content-color'                   => '#ffffff',
            'home-3-widget-content-link'                    => '#ffffff',
            'home-3-widget-content-link-hov'                => '#ffffff',
            'home-3-widget-content-stack'                   => 'roboto',
            'home-3-widget-content-size'                    => '18',
            'home-3-button-link'                            => '#333333',
            'home-3-button-link-hov'                        => '#333333',
            'home-3-button-back'                            => '#ffffff',
            'home-3-button-back-hov'                        => '#ebebeb',
            'home-3-button-stack'                           => 'roboto',
            'home-3-button-size'                            => '14',
            'home-3-button-weight'                          => '700',
            'home-3-button-style'                           => 'normal',
            'home-3-button-transform'                       => 'uppercase',
            'home-3-button-border-radius'                   => '4',
            'home-3-button-padding-top'                     => '20',
            'home-3-button-padding-bottom'                  => '20',
            'home-3-button-padding-left'                    => '25',
            'home-3-button-padding-right'                   => '25',

            // front page 4
            'home-4-top-widget-back'                        => '#ffffff',
            'home-4-section-padding-top'                    => '4%',
            'home-4-section-padding-bottom'                 => '4%',
            'home-4-section-padding-left'                   => '0',
            'home-4-section-padding-right'                  => '0',
            'home-4-widget-title-color'                     => '#333333',
            'home-4-widget-title-stack'                     => 'roboto',
            'home-4-widget-title-size'                      => '18',
            'home-4-widget-title-weight'                    => '900',
            'home-4-widget-title-transform'                 => 'uppercase',
            'home-4-widget-title-style'                     => 'normal',
            'home-4-widget-title-margin-bottom'             => '20',
            'home-4-post-title'                             => '#333333',
            'home-4-post-title-hov'                         => '#009946',
            'home-4-post-title-stack'                       => 'roboto',
            'home-4-post-title-size'                        => '32',
            'home-4-post-title-weight'                      => '900',
            'home-4-post-title-transform'                   => 'none',
            'home-4-post-title-style'                       => 'normal',
            'home-4-post-title-margin-bottom'               => '20',
            'home-4-meta-text-color'                        => '#6d6d6d',
            'home-4-meta-author-link'                       => '#6d6d6d',
            'home-4-meta-author-link-hov'                   => '#6d6d6d',
            'home-4-meta-comment-link'                      => '#6d6d6d',
            'home-4-meta-comment-link-hov'                  => '#6d6d6d',
            'home-4-meta-stack'                             => 'roboto',
            'home-4-meta-size'                              => '12',
            'home-4-meta-weight'                            => '400',
            'home-4-meta-transform'                         => 'uppercase',
            'home-4-meta-style'                             => 'normal',
            'home-4-meta-top-border-color'                  => 'rgba( 0, 0, 0, .16 )',
            'home-4-meta-top-border-style'                  => 'solid',
            'home-4-meta-top-border-width'                  => '1',
            'home-4-meta-bottom-border-color'               => 'rgba( 0, 0, 0, .16 )',
            'home-4-meta-bottom-border-style'               => 'solid',
            'home-4-meta-bottom-border-width'               => '1',
            'home-4-post-content-color'                     => '#333333',
            'home-4-post-content-link'                      => '#009946',
            'home-4-post-content-link-hov'                  => '#333333',
            'home-4-post-content-stack'                     => 'roboto',
            'home-4-post-content-size'                      => '18',
            'home-4-button-link'                            => '#ffffff',
            'home-4-button-link-hov'                        => '#ffffff',
            'home-4-button-back'                            => '#686868',
            'home-4-button-back-hov'                        => '#333333',
            'home-4-button-stack'                           => 'roboto',
            'home-4-button-size'                            => '14',
            'home-4-button-weight'                          => '700',
            'home-4-button-style'                           => 'normal',
            'home-4-button-transform'                       => 'uppercase',
            'home-4-button-border-radius'                   => '4',
            'home-4-button-padding-top'                     => '20',
            'home-4-button-padding-bottom'                  => '20',
            'home-4-button-padding-left'                    => '25',
            'home-4-button-padding-right'                   => '25',

            // post area wrapper
            'site-inner-padding-top'                        => '60',

            // main entry area
            'main-entry-back'                               => '#ffffff',
            'main-entry-border-radius'                      => '0',
            'main-entry-padding-top'                        => '0',
            'main-entry-padding-bottom'                     => '0',
            'main-entry-padding-left'                       => '0',
            'main-entry-padding-right'                      => '0',
            'main-entry-margin-top'                         => '0',
            'main-entry-margin-bottom'                      => '40',
            'main-entry-margin-left'                        => '0',
            'main-entry-margin-right'                       => '0',

            // post title area
            'post-title-text'                               => '#333333',
            'post-title-link'                               => '#333333',
            'post-title-link-hov'                           => '#009946',
            'post-title-stack'                              => 'roboto',
            'post-title-size'                               => '40',
            'post-title-weight'                             => '900',
            'post-title-transform'                          => 'none',
            'post-title-align'                              => 'left',
            'post-title-style'                              => 'normal',
            'post-title-margin-bottom'                      => '30',

            // entry meta
            'post-header-meta-text-color'                   => '#6d6d6d',
            'post-header-meta-date-color'                   => '#6d6d6d',
            'post-header-meta-author-link'                  => '#6d6d6d',
            'post-header-meta-author-link-hov'              => '#333333',
            'post-header-meta-comment-link'                 => '#6d6d6d',
            'post-header-meta-comment-link-hov'             => '#333333',
            'post-header-meta-stack'                        => 'roboto',
            'post-header-meta-size'                         => '12',
            'post-header-meta-weight'                       => '400',
            'post-header-meta-transform'                    => 'uppercase',
            'post-header-meta-style'                        => 'normal',
            'post-header-meta-top-border-color'             => 'rgba( 0, 0, 0, .16 )',
            'post-header-meta-top-border-style'             => 'solid',
            'post-header-meta-top-border-width'             => '1',
            'post-header-meta-bottom-border-color'          => 'rgba( 0, 0, 0, .16 )',
            'post-header-meta-bottom-border-style'          => 'solid',
            'post-header-meta-bottom-border-width'          => '1',
            'post-header-meta-comment-link'                 => '#ffffff',
            'post-header-meta-comment-link-hov'             => '#ffffff',
            'post-header-content-count-back'                => '#009946',

            // post text
            'post-entry-text'                               => '#333333',
            'post-entry-link'                               => '#009946',
            'post-entry-link-hov'                           => '#333333',
            'post-entry-stack'                              => 'roboto',
            'post-entry-size'                               => '12',
            'post-entry-weight'                             => '400',
            'post-entry-style'                              => 'normal',
            'post-entry-list-ol'                            => 'decimal',
            'post-entry-list-ul'                            => 'disc',

            // entry-footer
            'post-footer-category-text'                     => '', // removed
            'post-footer-category-link'                     => '', // removed
            'post-footer-category-link-hov'                 => '', // removed
            'post-footer-tag-text'                          => '', // removed
            'post-footer-tag-link'                          => '', // removed
            'post-footer-tag-link-hov'                      => '', // removed
            'post-footer-stack'                             => '', // removed
            'post-footer-size'                              => '', // removed
            'post-footer-weight'                            => '', // removed
            'post-footer-transform'                         => '', // removed,
            'post-footer-align'                             => '', // removed
            'post-footer-style'                             => '', // removed
            'post-footer-divider-color'                     => '', // removed
            'post-footer-divider-style'                     => '', // removed
            'post-footer-divider-width'                     => '', // removed

            // read more link
            'extras-read-more-link'                         => '#009946',
            'extras-read-more-link-hov'                     => '#333333',
            'extras-read-more-stack'                        => 'roboto',
            'extras-read-more-size'                         => '18',
            'extras-read-more-weight'                       => '400',
            'extras-read-more-transform'                    => 'none',
            'extras-read-more-style'                        => 'normal',

            // breadcrumbs
            'extras-breadcrumb-text'                        => '#333333',
            'extras-breadcrumb-link'                        => '#009946',
            'extras-breadcrumb-link-hov'                    => '#333333',
            'extras-breadcrumb-stack'                       => 'roboto',
            'extras-breadcrumb-size'                        => '16',
            'extras-breadcrumb-weight'                      => '400',
            'extras-breadcrumb-transform'                   => 'none',
            'extras-breadcrumb-style'                       => 'normal',

            // pagination typography (apply to both )
            'extras-pagination-stack'                       => 'roboto',
            'extras-pagination-size'                        => '16',
            'extras-pagination-weight'                      => '600',
            'extras-pagination-transform'                   => 'none',
            'extras-pagination-style'                       => 'normal',

            // pagination text
            'extras-pagination-text-link'                   => '#333333',
            'extras-pagination-text-link-hov'               => '#ffffff',

            // pagination numeric
            'extras-pagination-numeric-back'                => '#ffffff',
            'extras-pagination-numeric-back-hov'            => '#009946',
            'extras-pagination-numeric-active-back'         => '#009946',
            'extras-pagination-numeric-active-back-hov'     => '#009946',
            'extras-pagination-numeric-border-radius'       => '0',
            'extras-pagination-numeric-padding-top'         => '8',
            'extras-pagination-numeric-padding-bottom'      => '8',
            'extras-pagination-numeric-padding-left'        => '12',
            'extras-pagination-numeric-padding-right'       => '12',
            'extras-pagination-numeric-link'                => '#333333',
            'extras-pagination-numeric-link-hov'            => '#ffffff',
            'extras-pagination-numeric-active-link'         => '#ffffff',
            'extras-pagination-numeric-active-link-hov'     => '#ffffff',

            // after entry widget area
            'after-entry-widget-area-back'                  => '#ffffff',
            'after-entry-widget-border-radius'              => '0',
            'after-entry-widget-area-box-shadow'            => '0 0 3em 0 rgba( 0, 0, 0, .2 )',
            'after-entry-widget-box-shadow'                 => '0 0 3em 0 rgba( 0, 0, 0, .2 )',
            'after-entry-widget-area-padding-top'           => '0',
            'after-entry-widget-area-padding-bottom'        => '0',
            'after-entry-widget-area-padding-left'          => '0',
            'after-entry-widget-area-padding-right'         => '0',
            'after-entry-widget-area-margin-top'            => '0',
            'after-entry-widget-area-margin-bottom'         => '0',
            'after-entry-widget-area-margin-left'           => '0',
            'after-entry-widget-area-margin-right'          => '0',
            'after-entry-widget-back'                       => '#ffffff',
            'after-entry-widget-border-color'               => '#ccccc',
            'after-entry-widget-border-style'               => 'solid',
            'after-entry-widget-border-width'               => '1',
            'after-entry-widget-border-radius'              => '0',
            'after-entry-widget-padding-top'                => '30',
            'after-entry-widget-padding-bottom'             => '30',
            'after-entry-widget-padding-left'               => '30',
            'after-entry-widget-padding-right'              => '30',
            'after-entry-widget-margin-top'                 => '40',
            'after-entry-widget-margin-bottom'              => '70',
            'after-entry-widget-margin-left'                => '0',
            'after-entry-widget-margin-right'               => '0',
            'after-entry-widget-title-background'           => '#b4151b',
            'after-entry-widget-title-text'                 => '#ffffff',
            'after-entry-widget-title-stack'                => 'roboto',
            'after-entry-widget-title-size'                 => '22',
            'after-entry-widget-title-weight'               => '900',
            'after-entry-widget-title-transform'            => 'uppercase',
            'after-entry-widget-title-align'                => 'center',
            'after-entry-widget-title-style'                => 'normal',
            'after-entry-widget-title-margin-top'           => '-30',
            'after-entry-widget-title-margin-bottom'        => '0',
            'after-entry-widget-title-margin-left'          => '-30',
            'after-entry-widget-title-margin-right'         => '-30',
            'after-entry-widget-title-padding-top'          => '15',
            'after-entry-widget-title-padding-bottom'       => '15',
            'after-entry-widget-title-padding-left'         => '15',
            'after-entry-widget-title-padding-right'        => '15',
            'after-entry-widget-content-text'               => '#333333',
            'after-entry-widget-content-link'               => '#009946',
            'after-entry-widget-content-link-hov'           => '#333333',
            'after-entry-widget-content-stack'              => 'roboto',
            'after-entry-widget-content-size'               => '18',
            'after-entry-widget-content-weight'             => '400',
            'after-entry-widget-content-align'              => 'left',
            'after-entry-widget-content-style'              => 'normal',

            // author box
            'extras-author-box-back'                        => '#ffffff',
            'extras-author-box-border-color'                => '#cccccc',
            'extras-author-box-border-style'                => 'solid',
            'extras-author-box-border-width'                => '1',
            'extras-author-box-box-shadow'                  => '0 0 3em 0 rgba( 0, 0, 0, .2 )',
            'extras-author-box-padding-top'                 => '30',
            'extras-author-box-padding-bottom'              => '30',
            'extras-author-box-padding-left'                => '30',
            'extras-author-box-padding-right'               => '30',
            'extras-author-box-margin-top'                  => '70',
            'extras-author-box-margin-bottom'               => '0',
            'extras-author-box-margin-left'                 => '0',
            'extras-author-box-margin-right'                => '0',
            'extras-author-box-name-text'                   => '#333333',
            'extras-author-box-name-stack'                  => 'roboto',
            'extras-author-box-name-size'                   => '20',
            'extras-author-box-name-weight'                 => '900',
            'extras-author-box-name-align'                  => 'left',
            'extras-author-box-name-transform'              => 'none',
            'extras-author-box-name-style'                  => 'normal',
            'extras-author-box-bio-text'                    => '#333333',
            'extras-author-box-bio-link'                    => '#009946',
            'extras-author-box-bio-link-hov'                => '#333333',
            'extras-author-box-bio-stack'                   => 'roboto',
            'extras-author-box-bio-size'                    => '18',
            'extras-author-box-bio-weight'                  => '400',
            'extras-author-box-bio-style'                   => 'normal',

            // comment list
            'comment-list-back'                             => '#ffffff',
            'comment-list-padding-top'                      => '0',
            'comment-list-padding-bottom'                   => '0',
            'comment-list-padding-left'                     => '0',
            'comment-list-padding-right'                    => '0',
            'comment-list-margin-top'                       => '0',
            'comment-list-margin-bottom'                    => '0',
            'comment-list-margin-left'                      => '0',
            'comment-list-margin-right'                     => '0',

            // comment list title
            'comment-list-title-text'                       => '#333333',
            'comment-list-title-stack'                      => 'roboto',
            'comment-list-title-size'                       => '24',
            'comment-list-title-weight'                     => '900',
            'comment-list-title-transform'                  => 'none',
            'comment-list-title-align'                      => 'left',
            'comment-list-title-style'                      => 'normal',
            'comment-list-title-margin-bottom'              => '10',

            // single comments
            'single-comment-padding-top'                    => '40',
            'single-comment-padding-bottom'                 => '0',
            'single-comment-padding-left'                   => '40',
            'single-comment-padding-right'                  => '40',
            'single-comment-margin-top'                     => '0',
            'single-comment-margin-bottom'                  => '0',
            'single-comment-margin-left'                    => '0',
            'single-comment-margin-right'                   => '0',

            // color setup for standard and author comments
            'single-comment-standard-back'                  => '', // removed
            'single-comment-standard-border-color'          => '', // removed
            'single-comment-standard-border-style'          => '', // removed
            'single-comment-standard-border-width'          => '', // removed
            'single-comment-author-back'                    => '', // removed
            'single-comment-author-border-color'            => '', // removed
            'single-comment-author-border-style'            => '', // removed
            'single-comment-author-border-width'            => '', // removed

            // comment name
            'comment-element-name-text'                     => '#333333',
            'comment-element-name-link'                     => '#009946',
            'comment-element-name-link-hov'                 => '#333333',
            'comment-element-name-stack'                    => 'roboto',
            'comment-element-name-size'                     => '16',
            'comment-element-name-weight'                   => '400',
            'comment-element-name-style'                    => 'normal',

            // comment date
            'comment-element-date-link'                     => '#009946',
            'comment-element-date-link-hov'                 => '#333333',
            'comment-element-date-stack'                    => 'roboto',
            'comment-element-date-size'                     => '16',
            'comment-element-date-weight'                   => '400',
            'comment-element-date-style'                    => 'normal',

            // comment body
            'comment-element-body-text'                     => '#333333',
            'comment-element-body-link'                     => '#009946',
            'comment-element-body-link-hov'                 => '#333333',
            'comment-element-body-stack'                    => 'roboto',
            'comment-element-body-size'                     => '16',
            'comment-element-body-weight'                   => '400',
            'comment-element-body-style'                    => 'normal',

            // comment reply
            'comment-element-reply-link'                    => '#009946',
            'comment-element-reply-link-hov'                => '#333333',
            'comment-element-reply-stack'                   => 'roboto',
            'comment-element-reply-size'                    => '16',
            'comment-element-reply-weight'                  => '400',
            'comment-element-reply-align'                   => 'left',
            'comment-element-reply-style'                   => 'normal',

            // trackback list
            'trackback-list-back'                           => '#ffffff',
            'trackback-list-padding-top'                    => '0',
            'trackback-list-padding-bottom'                 => '0',
            'trackback-list-padding-left'                   => '0',
            'trackback-list-padding-right'                  => '0',

            'trackback-list-margin-top'                     => '0',
            'trackback-list-margin-bottom'                  => '14',
            'trackback-list-margin-left'                    => '0',
            'trackback-list-margin-right'                   => '0',

            // trackback list title
            'trackback-list-title-text'                     => '#333333',
            'trackback-list-title-stack'                    => 'roboto',
            'trackback-list-title-size'                     => '24',
            'trackback-list-title-weight'                   => '900',
            'trackback-list-title-transform'                => 'none',
            'trackback-list-title-align'                    => 'left',
            'trackback-list-title-style'                    => 'normal',
            'trackback-list-title-margin-bottom'            => '10',

            // trackback name
            'trackback-element-name-text'                   => '#333333',
            'trackback-element-name-link'                   => '#009946',
            'trackback-element-name-link-hov'               => '#333333',
            'trackback-element-name-stack'                  => 'roboto',
            'trackback-element-name-size'                   => '16',
            'trackback-element-name-weight'                 => '400',
            'trackback-element-name-style'                  => 'normal',

            // trackback date
            'trackback-element-date-link'                   => '#009946',
            'trackback-element-date-link-hov'               => '#333333',
            'trackback-element-date-stack'                  => 'roboto',
            'trackback-element-date-size'                   => '16',
            'trackback-element-date-weight'                 => '400',
            'trackback-element-date-style'                  => 'normal',

            // trackback body
            'trackback-element-body-text'                   => '#333333',
            'trackback-element-body-stack'                  => 'roboto',
            'trackback-element-body-size'                   => '16',
            'trackback-element-body-weight'                 => '400',
            'trackback-element-body-style'                  => 'normal',

            // comment form
            'comment-reply-back'                            => '#ffffff',
            'comment-reply-padding-top'                     => '0',
            'comment-reply-padding-bottom'                  => '0',
            'comment-reply-padding-left'                    => '0',
            'comment-reply-padding-right'                   => '0',

            'comment-reply-margin-top'                      => '60',
            'comment-reply-margin-bottom'                   => '40',
            'comment-reply-margin-left'                     => '0',
            'comment-reply-margin-right'                    => '0',

            // comment form title
            'comment-reply-title-text'                      => '#333333',
            'comment-reply-title-stack'                     => 'roboto',
            'comment-reply-title-size'                      => '24',
            'comment-reply-title-weight'                    => '900',
            'comment-reply-title-transform'                 => 'none',
            'comment-reply-title-align'                     => 'left',
            'comment-reply-title-style'                     => 'normal',
            'comment-reply-title-margin-bottom'             => '10',

            // comment form notes
            'comment-reply-notes-text'                      => '', //removed
            'comment-reply-notes-link'                      => '', //removed
            'comment-reply-notes-link-hov'                  => '', //removed
            'comment-reply-notes-stack'                     => '', //removed
            'comment-reply-notes-size'                      => '', //removed
            'comment-reply-notes-weight'                    => '', //removed
            'comment-reply-notes-style'                     => '', //removed

            // comment allowed tags
            'comment-reply-atags-base-back'                 => '', //removed
            'comment-reply-atags-base-text'                 => '', //removed
            'comment-reply-atags-base-stack'                => '', //removed
            'comment-reply-atags-base-size'                 => '', //removed
            'comment-reply-atags-base-weight'               => '', //removed
            'comment-reply-atags-base-style'                => '', //removed

            // comment allowed tags code
            'comment-reply-atags-code-text'                 => '', //removed
            'comment-reply-atags-code-stack'                => '', //removed
            'comment-reply-atags-code-size'                 => '', //removed
            'comment-reply-atags-code-weight'               => '', //removed

            // comment fields labels
            'comment-reply-fields-label-text'               => '#333333',
            'comment-reply-fields-label-stack'              => 'roboto',
            'comment-reply-fields-label-size'               => '18',
            'comment-reply-fields-label-weight'             => '400',
            'comment-reply-fields-label-transform'          => 'none',
            'comment-reply-fields-label-align'              => 'left',
            'comment-reply-fields-label-style'              => 'normal',

            // comment fields inputs
            'comment-reply-fields-input-field-width'        => '50',
            'comment-reply-fields-input-border-style'       => 'solid',
            'comment-reply-fields-input-border-width'       => '2',
            'comment-reply-fields-input-border-radius'      => '4',
            'comment-reply-fields-input-padding'            => '16',
            'comment-reply-fields-input-margin-bottom'      => '0',
            'comment-reply-fields-input-base-back'          => '#ffffff',
            'comment-reply-fields-input-focus-back'         => '#ffffff',
            'comment-reply-fields-input-base-border-color'  => '#e2e2e2',
            'comment-reply-fields-input-focus-border-color' => '#222222',
            'comment-reply-fields-input-text'               => '#222222',
            'comment-reply-fields-input-stack'              => 'roboto',
            'comment-reply-fields-input-size'               => '16',
            'comment-reply-fields-input-weight'             => '700',
            'comment-reply-fields-input-style'              => 'normal',

            // comment button
            'comment-submit-button-back'                    => '#0e763c',
            'comment-submit-button-back-hov'                => '#009946',
            'comment-submit-button-text'                    => '#ffffff',
            'comment-submit-button-text-hov'                => '#ffffff',
            'comment-submit-button-stack'                   => 'roboto',
            'comment-submit-button-size'                    => '14',
            'comment-submit-button-weight'                  => '700',
            'comment-submit-button-transform'               => 'uppercase',
            'comment-submit-button-style'                   => 'normal',

            'comment-submit-button-padding-top'             => '20',
            'comment-submit-button-padding-bottom'          => '20',
            'comment-submit-button-padding-left'            => '25',
            'comment-submit-button-padding-right'           => '25',
            'comment-submit-button-border-radius'           => '4',

            // sidebar widgets
            'sidebar-widget-back'                           => '#ffffff',
            'sidebar-widget-border-radius'                  => '0',
            'sidebar-widget-padding-top'                    => '0',
            'sidebar-widget-padding-bottom'                 => '0',
            'sidebar-widget-padding-left'                   => '0',
            'sidebar-widget-padding-right'                  => '0',
            'sidebar-widget-margin-top'                     => '0',
            'sidebar-widget-margin-bottom'                  => '40',
            'sidebar-widget-margin-left'                    => '0',
            'sidebar-widget-margin-right'                   => '0',

            // sidebar widget titles
            'sidebar-widget-title-text'                     => '#787878',
            'sidebar-widget-title-stack'                    => 'roboto',
            'sidebar-widget-title-size'                     => '18',
            'sidebar-widget-title-weight'                   => '900',
            'sidebar-widget-title-transform'                => 'uppercase',
            'sidebar-widget-title-align'                    => 'left',
            'sidebar-widget-title-style'                    => 'normal',
            'sidebar-widget-title-margin-bottom'            => '20',

            // sidebar widget content
            'sidebar-widget-content-text'                   => '#333333',
            'sidebar-widget-content-link'                   => '#009946',
            'sidebar-widget-content-link-hov'               => '#333333',
            'sidebar-widget-content-stack'                  => 'roboto',
            'sidebar-widget-content-size'                   => '16',
            'sidebar-widget-content-weight'                 => '400',
            'sidebar-widget-content-align'                  => 'left',
            'sidebar-widget-content-style'                  => 'normal',

            // sidebar recent entries widget
            'sidebar-recent-posts-title-back'               => '#787878',
            'sidebar-recent-posts-title-text'               => '#ffffff',
            'sidebar-recent-posts-title-stack'              => 'roboto',
            'sidebar-recent-posts-title-size'               => '18',
            'sidebar-recent-posts-title-weight'             => '400',
            'sidebar-recent-posts-title-transform'          => 'none',
            'sidebar-recent-posts-title-align'              => 'left',
            'sidebar-recent-posts-title-style'              => 'normal',
            'sidebar-recent-posts-title-padding-top'        => '15',
            'sidebar-recent-posts-title-padding-bottom'     => '15',
            'sidebar-recent-posts-title-padding-left'       => '35',
            'sidebar-recent-posts-title-padding-right'      => '35',
            'sidebar-recent-posts-list-items-back'          => '#f7f6f4',
            'sidebar-recent-posts-list-items-back-hov'      => '#eceae5',
            'sidebar-recent-posts-list-items-text'          => '#5c5c5c',
            'sidebar-recent-posts-list-items-text-hov'      => '#333333',
            'sidebar-recent-posts-list-items-stack'         => 'roboto',
            'sidebar-recent-posts-list-items-size'          => '16',
            'sidebar-recent-posts-list-items-weight'        => '400',
            'sidebar-recent-posts-list-items-transform'     => 'none',
            'sidebar-recent-posts-list-items-align'         => 'left',
            'sidebar-recent-posts-list-items-style'         => 'normal',
            'sidebar-recent-posts-list-items-padding-top'    => '20',
            'sidebar-recent-posts-list-items-padding-bottom' => '20',
            'sidebar-recent-posts-list-items-padding-left'   => '30',
            'sidebar-recent-posts-list-items-padding-right'  => '30',

            // footer banner
            'footer-banner-back'                             => '#b4151b',
            'footer-banner-section-padding-top'              => '4',
            'footer-banner-section-padding-bottom'           => '4',
            'footer-banner-section-padding-left'             => '0',
            'footer-banner-section-padding-right'            => '0',
            'footer-banner-large-heading'                    => '#ffffff',
            'footer-banner-large-heading-stack'              => 'roboto',
            'footer-banner-large-heading-size'               => '30',
            'footer-banner-large-heading-weight'             => '300',
            'footer-banner-large-heading-transform'          => 'none',
            'footer-banner-large-heading-style'              => 'normal',
            'footer-banner-large-heading-margin-bottom'      => '10',
            'footer-banner-widget-content-color'             => '#ffffff',
            'footer-banner-widget-content-link'              => '#ffffff',
            'footer-banner-widget-content-link-hover'        => '#ffffff',
            'footer-banner-widget-content-stack'             => 'roboto',
            'footer-banner-widget-content-size'              => '18',
            'footer-banner-widget-content-weight'            => '400',
            'footer-banner-widget-content-align'             => 'center',
            'footer-banner-button-color'                     => '#333333',
            'footer-banner-button-hover'                     => '#333333',
            'footer-banner-button-back'                      => '#ffffff',
            'footer-banner-button-back-hover'                => '#ebebeb',
            'footer-banner-button-stack'                     => 'roboto',
            'footer-banner-button-size'                      => '14',
            'footer-banner-button-weight'                    => '700',
            'footer-banner-button-transform'                 => 'uppercase',
            'footer-banner-button-padding-top'               => '20',
            'footer-banner-button-padding-bottom'            => '20',
            'footer-banner-button-padding-left'              => '25',
            'footer-banner-button-padding-right'             => '25',
            'footer-banner-button-border-radius'             => '4',

            // footer widget row
            'footer-widget-row-back'                        => '#2d2c2a',
            'footer-widget-row-padding-top'                 => '4',
            'footer-widget-row-padding-bottom'              => '4',
            'footer-widget-row-padding-left'                => '0',
            'footer-widget-row-padding-right'               => '0',

            // footer widget singles
            'footer-widget-single-back'                     => '',
            'footer-widget-single-margin-bottom'            => '40',
            'footer-widget-single-padding-top'              => '0',
            'footer-widget-single-padding-bottom'           => '0',
            'footer-widget-single-padding-left'             => '0',
            'footer-widget-single-padding-right'            => '0',
            'footer-widget-single-border-radius'            => '0',

            // footer widget title
            'footer-widget-title-text'                      => '#ffffff',
            'footer-widget-title-stack'                     => 'roboto',
            'footer-widget-title-size'                      => '18',
            'footer-widget-title-weight'                    => '900',
            'footer-widget-title-transform'                 => 'uppercase',
            'footer-widget-title-align'                     => 'left',
            'footer-widget-title-style'                     => 'normal',
            'footer-widget-title-margin-bottom'             => '10',

            // footer widget content
            'footer-widget-content-text'                    => '#ffffff',
            'footer-widget-content-link'                    => '#ffffff',
            'footer-widget-content-link-hov'                => '#ffffff',
            'footer-widget-content-stack'                   => 'roboto',
            'footer-widget-content-size'                    => '18',
            'footer-widget-content-weight'                  => '400',
            'footer-widget-content-align'                   => 'left',
            'footer-widget-content-style'                   => 'normal',

            // bottom footer
            'footer-main-back'                              => '#2d2c2a',
            'footer-main-padding-top'                       => '4',
            'footer-main-padding-bottom'                    => '4',
            'footer-main-padding-left'                      => '0',
            'footer-main-padding-right'                     => '0',
            'footer-main-content-text'                      => '#ffffff',
            'footer-main-content-link'                      => 'rgba( 255, 255, 255, .6 );',
            'footer-main-content-link-hov'                  => '#ffffff',
            'footer-main-content-stack'                     => 'roboto',
            'footer-main-content-size'                      => '16',
            'footer-main-content-weight'                    => '400',
            'footer-main-content-transform'                 => 'none',
            'footer-main-content-align'                     => 'center',
            'footer-main-content-style'                     => 'normal',
        );

		// put into key value pair
		foreach ( $changes as $key => $value ) {
			$defaults[ $key ] = $value;
		}

		// return the array
		return $defaults;
	}

    public function entry_content_defaults( $defaults ) {

        $changes = array(
            'entry-content-bquotes-border-color'            => '#930000',
            'entry-content-bquotes-border-style'            => 'solid',
            'entry-content-bquotes-border-width'            => '4',
		);

		// put into key value pairs
		foreach ( $changes as $key => $value ) {
			$defaults[ $key ] = $value;
		}

		// return the array of default values
		return $defaults;
    }

	/**
	 * add and filter options in the genesis widgets - enews
	 *
	 * @return array|string $sections
	 */
	public function enews_defaults( $defaults ) {

		$changes = array(
			// General
			'enews-widget-box-shadow'                       => '0 0 60px 0 rgba( 0, 0, 0, .2 )',
            'enews-widget-back'                             => '#ffffff',
			'enews-widget-title-color'                      => '#ffffff',
			'enews-widget-text-color'                       => '#333333',
            'enews-widget-title-back'                       => '#b4151b',

			// General Typography
			'enews-widget-gen-stack'                        => 'roboto',
			'enews-widget-gen-size'                         => '16',
			'enews-widget-gen-weight'                       => '400',
			'enews-widget-gen-transform'                    => 'none',
			'enews-widget-gen-text-margin-bottom'           => '28',

			// Field Inputs
			'enews-widget-field-input-back'                 => '#ffffff',
			'enews-widget-field-input-text-color'           => '#222222',
			'enews-widget-field-input-stack'                => 'roboto',
			'enews-widget-field-input-size'                 => '16',
			'enews-widget-field-input-weight'               => '700',
			'enews-widget-field-input-transform'            => 'uppercase',
			'enews-widget-field-input-border-color'         => '#e2e2e2',
			'enews-widget-field-input-border-type'          => 'solid',
			'enews-widget-field-input-border-width'         => '2',
			'enews-widget-field-input-border-radius'        => '4',
			'enews-widget-field-input-border-color-focus'   => '#222222',
			'enews-widget-field-input-border-type-focus'    => 'solid',
			'enews-widget-field-input-border-width-focus'   => '2',
			'enews-widget-field-input-pad-top'              => '16',
			'enews-widget-field-input-pad-bottom'           => '16',
			'enews-widget-field-input-pad-left'             => '16',
			'enews-widget-field-input-pad-right'            => '16',
			'enews-widget-field-input-margin-bottom'        => '16',
			'enews-widget-field-input-box-shadow'           => '', // removed

			// Button Color
			'enews-widget-button-back'                      => '#0e763c',
			'enews-widget-button-back-hov'                  => '#009946',
			'enews-widget-button-text-color'                => '#ffffff',
			'enews-widget-button-text-color-hov'            => '#ffffff',

			// Button Typography
			'enews-widget-button-stack'                     => 'roboto',
			'enews-widget-button-size'                      => '16',
			'enews-widget-button-weight'                    => '700',
			'enews-widget-button-transform'                 => 'uppercase',

			// Button Padding
			'enews-widget-button-pad-top'                   => '20',
			'enews-widget-button-pad-bottom'                => '20',
			'enews-widget-button-pad-left'                  => '25',
			'enews-widget-button-pad-right'                 => '25',
			'enews-widget-button-margin-bottom'             => '0',
		);

		// put into key value pairs
		foreach ( $changes as $key => $value ) {
			$defaults[ $key ] = $value;
		}

		// return the array of default values
		return $defaults;
	}

	/**
	 * add new block for front page layout
	 *
	 * @return string $blocks
	 */
	public function homepage( $blocks ) {

		if ( ! isset( $blocks['homepage'] ) ) {

			$blocks['homepage'] = array(
				'tab'   => __( 'Homepage', 'gppro' ),
				'title' => __( 'Homepage', 'gppro' ),
				'intro' => __( 'The homepage uses 5 custom widget areas.', 'gppro', 'gppro' ),
				'slug'  => 'homepage',
			);
		}

		// return the block setup
		return $blocks;
	}

	/**
	 * add new block for footer banner
	 *
	 * @return string $blocks
	 */
	public function footer_banner( $blocks ) {

		if ( ! isset( $blocks['footer_banner'] ) ) {

			$blocks['footer_banner'] = array(
				'tab'   => __( 'Footer Banner', 'gppro' ),
				'title' => __( 'Footer Banner', 'gppro' ),
				'intro' => __( 'The homepage uses 5 custom widget areas.', 'gppro' ),
				'slug'  => 'footer_banner',
			);
		}

		// return the block setup
		return $blocks;
	}

    /**
	 * add and filter options in the general body area
	 *
	 * @return array|string $sections
	 */
	public function general_body( $sections, $class ) {
		// remove mobile background color option
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'body-color-setup', array( 'body-color-back-thin' ) );

		// remove sub and tip from body background color
		$sections   = GP_Pro_Helper::remove_data_from_items( $sections, 'body-color-setup', 'body-color-back-main', array( 'sub', 'tip' ) );

        // Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the header area
	 *
	 * @return array|string $sections
	 */
	public function header_area( $sections, $class ) {
        // remove site description
        $sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'section-break-site-desc', 'site-desc-display-setup', 'site-desc-type-setup' ) );

        // remove header nav
        $sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'section-break-header-nav', 'header-nav-color-setup', 'header-nav-type-setup', 'header-nav-item-padding-setup',  ) );

        // add widget buttons
        $sections = GP_Pro_Helper::array_insert_after(
			'header-widget-content-setup', $sections,
			array(
				'header-widget-button-setup'     => array(
					'title' => __( 'Widget Buttons', 'gppro' ),
					'data'  => array(
						'header-widget-button-link' => array(
							'label'    => __( 'Text Color', 'gppro' ),
                            'sub'      => __( 'Base', 'gppro' ),
							'input'    => 'color',
							'target'   => '.header-widget-area .widget a.button',
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                        'header-widget-button-link-hov' => array(
							'label'    => __( 'Text Color', 'gppro' ),
                            'sub'      => __( 'Hover', 'gppro' ),
							'input'    => 'color',
							'target'   => '.header-widget-area .widget a.button:hover',
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                        'header-widget-button-back' => array(
							'label'    => __( 'Background Color', 'gppro' ),
                            'sub'      => __( 'Base', 'gppro' ),
							'input'    => 'color',
							'target'   => '.header-widget-area .widget a.button',
							'selector' => 'background-color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                        'header-widget-button-back-hov' => array(
							'label'    => __( 'Background Color', 'gppro' ),
                            'sub'      => __( 'Hover', 'gppro' ),
							'input'    => 'color',
							'target'   => '.header-widget-area .widget a.button:hover',
							'selector' => 'background-color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                        'header-widget-button-stack' => array(
							'label'    => __( 'Font Stack', 'gppro' ),
						    'input'    => 'font-stack',
						    'target'   => '.header-widget-area .widget a.button',
					    	'selector' => 'font-family',
					    	'builder'  => 'GP_Pro_Builder::stack_css',
						),
                        'header-widget-button-size' => array(
							'label'    => __( 'Font Size', 'gppro' ),
                            'input'    => 'font-size',
                            'scale'    => 'text',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'font-size',
                            'builder'  => 'GP_Pro_Builder::px_css',
						),
                        'header-widget-button-weight' => array(
							'label'    => __( 'Font Weight', 'gppro' ),
                            'input'    => 'font-weight',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'font-weight',
                            'builder'  => 'GP_Pro_Builder::number_css',
                            'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' )
						),
                        'header-widget-button-style' => array(
							'label'   => __( 'Font Style', 'gppro' ),
                            'input'   => 'radio',
                            'options' => array(
                                array(
                                    'label' => __( 'Normal', 'gppro' ),
                                    'value' => 'normal',
                                ),
                                array(
                                    'label' => __( 'Italic', 'gppro' ),
                                    'value' => 'italic'
                                ),
                            ),
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'font-style',
                            'builder'  => 'GP_Pro_Builder::text_css',
						),
                        'header-widget-button-transform' => array(
                            'label'    => __( 'Text Appearance', 'gppro' ),
                            'input'    => 'text-transform',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'text-transform',
                            'builder'  => 'GP_Pro_Builder::text_css',
					    ),
                        'header-widget-button-border-radius'  => array(
                            'label'     => __( 'Border Radius', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.site-container a.button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'border-radius',
                            'min'       => '0',
                            'max'       => '40',
                        ),
                            'header-widget-button-padding-top'  => array(
                            'label'    => __( 'Padding - Top', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'padding-top',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '20',
                            'step'     => '1',
					    ),
                        'header-widget-button-padding-bottom'   => array(
                            'label'    => __( 'Padding - Bottom', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'padding-bottom',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '20',
                            'step'     => '1',
					    ),
                        'header-widget-button-padding-left' => array(
                            'label'    => __( 'Padding - Left', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'padding-left',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '30',
                            'step'     => '1',
					    ),
                        'header-widget-button-padding-right'    => array(
                            'label'    => __( 'Padding - Right', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.header-widget-area .widget a.button',
                            'selector' => 'padding-right',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '30',
                            'step'     => '1',
					    ),
					),
				),
			)
		);

		// Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the navigation area
	 *
	 * @return array|string $sections
	 */
	public function navigation( $sections, $class ) {
		// remove primary nav item background color
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'primary-nav-top-item-color-setup', array( 'primary-nav-top-item-base-back' ) );

        //remove primary nav padding settings
        $sections   = GP_Pro_Helper::remove_settings_from_section( $sections, 'primary-nav-top-padding-setup' );

        // remove secondary nav standard item colors - top level background colors
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'secondary-nav-top-item-setup', array( 'secondary-nav-top-item-base-back', 'secondary-nav-top-item-base-back-hov' ) );

        // remove secondary nav active item colors - top level background colors
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'secondary-nav-top-active-color-setup', array( 'secondary-nav-top-item-active-back', 'secondary-nav-top-item-active-back-hov' ) );

        // remove drop down settings from secondary navigation
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array(
			'secondary-nav-drop-type-setup',
			'secondary-nav-drop-item-color-setup',
			'secondary-nav-drop-active-color-setup',
			'secondary-nav-drop-padding-setup',
			'secondary-nav-drop-border-setup'
            )
        );

        // rename the primary navigation
		$sections['section-break-primary-nav']['break']['title'] = __( 'After Header Navigation', 'gppro' );

        // rename the secondary navigation
		$sections['section-break-secondary-nav']['break']['title'] = __( 'Footer Navigation', 'gppro' );

        // change primary navigation text description
		$sections['section-break-primary-nav']['break']['text'] =__( 'These settings apply to the navigation menu that displays in the "After Header" menu.', 'gppro' );

        // change secondary navigation text description
		$sections['section-break-secondary-nav']['break']['text'] =__( 'These settings apply to the navigation menu that displays in the "Footer Menu".', 'gppro' );

        // filter typography - top level to display title sub
        $sections['primary-nav-top-type-setup']['data']['primary-nav-top-size']['sub'] = 'Title';

         // filter typography - top level to fix text alignment
        $sections['primary-nav-top-type-setup']['data']['primary-nav-top-align']['target'] = '.nav-primary .genesis-nav-menu a';

        // filter standard item colors - top level item background (hover)
        $sections['primary-nav-top-item-color-setup']['data']['primary-nav-top-item-base-back-hov']['target'] = array ('.nav-primary .genesis-nav-menu > .menu-item > a:hover', '.nav-primary .genesis-nav-menu .sub-menu a:focus', '.nav-primary .genesis-nav-menu > li:hover:before', '.nav-primary .genesis-nav-menu > li.current-menu-item:before');

        $sections['primary-nav-top-item-color-setup']['data']['primary-nav-top-item-base-back-hov']['always_write'] = true;

        // rename footer menu section dividers
        //$sections['secondary-nav-top-type-setup']['data']['title'] = 'Typography';

        // add typography - top level description options
        $sections['primary-nav-top-type-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'primary-nav-top-size', $sections['primary-nav-top-type-setup']['data'],
			array(
				'primary-nav-top-desc-size' => array(
                    'label'    => __( 'Font Size', 'gppro' ),
                    'sub'      => __( 'Description', 'gppro' ),
                    'input'    => 'font-size',
                    'scale'    => 'text',
                    'target'   => '.nav-primary .genesis-nav-menu a span[itemprop="description"]',
                    'selector' => 'font-size',
                    'builder'  => 'GP_Pro_Builder::px_css',
                ),
			)
		);

        // Return the section array.
		return $sections;
	}

	/**
	 * add settings for homepage block
	 *
	 * @return array|string $sections
	 */
	public function homepage_section( $sections, $class ) {
        $sections['homepage'] = array(
            // home page 1
			'section-break-home-page-one' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page 1', 'gppro' ),
					'text'	=> __( 'These controls only work when this widget area is set up exactly like <a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/front-page-setup/front-page-1-widget-area/" target="_blank">the demo site</a>.', 'gppro' ),
				),
			),
            'home-1-widget-back-setup'     => array(
				'title' => __( '', 'gppro' ),
				'data'  => array(
					'home-1-widget-back' => array(
						'label'    => __( 'Background', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
				),
			),
            'home-1-widget-section-padding-setup'     => array(
				'title' => __( 'Section Padding', 'gppro' ),
				'data'  => array(
					'home-1-widget-section-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-1',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-1-widget-section-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-1',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-1-widget-section-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-1',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-1-widget-section-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-1',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
                ),
            ),
            'home-1-widget-title-setup'     => array(
				'title' => __( 'Widget Title', 'gppro' ),
				'data'  => array(
					'home-1-widget-title-color' => array(
						'label'    => __( 'Widget Title', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
					'home-1-widget-title-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'home-1-widget-title-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-1-widget-title-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
					'home-1-widget-title-transform' => array(
						'label'    => __( 'Text Appearance', 'gppro' ),
						'input'    => 'text-transform',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'text-transform',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
					'home-1-widget-title-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
					'home-1-widget-title-margin-bottom' => array(
						'label'    => __( 'Bottom Margin', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-1 .widget .widget-title',
						'selector' => 'margin-bottom',
						'min'      => '0',
						'max'      => '42',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
				),
            ),
            'home-1-menu-setup'     => array(
				'title' => __( 'Menu', 'gppro' ),
				'data'  => array(
					'home-1-menu-color' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1 .widget_nav_menu .menu li:after',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        'rgb'      => true,
					),
                    'home-1-menu-color-link' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Link', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1.color li a',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        'rgb'      => true,
					),
                    'home-1-menu-color-hover' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1.color li a:hover',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        'rgb'      => true,
					),
                    'home-1-menu-back-link' => array(
						'label'    => __( 'Background', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1.color li a',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        'rgb'      => true,
					),
                    'home-1-menu-back-hover' => array(
						'label'    => __( 'Background', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-1.color li a:hover',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        'rgb'      => true,
					),
					'home-1-menu-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.front-page-1.color li',
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'home-1-menu-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.front-page-1.color li',
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-1-menu-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.front-page-1 li',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
					'home-1-menu-transform' => array(
						'label'    => __( 'Text Appearance', 'gppro' ),
						'input'    => 'text-transform',
						'target'   => '.front-page-1.color li a',
						'selector' => 'text-transform',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
				),
            ),

            // home page 2
			'section-break-home-page-two' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page 2', 'gppro' ),
					'text'	=> __( 'These controls only work when this widget area is set up exactly like <a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/front-page-setup/front-page-2-widget-area/" target="_blank">the demo site</a>.', 'gppro' ),
				),
			),
            'home-2-widget-back-setup'     => array(
				'title' => __( '', 'gppro' ),
				'data'  => array(
					'home-2-widget-back' => array(
						'label'    => __( 'Background', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
				),
			),
            'home-2-section-padding-setup'     => array(
				'title' => __( 'Section Padding', 'gppro' ),
				'data'  => array(
					'home-2-section-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-2-section-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-2-section-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-2-section-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
                ),
            ),
            'home-2-widget-padding-setup'     => array(
				'title' => __( 'Widget Padding', 'gppro' ),
				'data'  => array(
					'home-2-widget-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2.widget-area .widget',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '100',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-widget-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2.widget-area .widget',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '100',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-widget-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2.widget-area .widget',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '100',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-widget-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2.widget-area .widget',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '100',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
                ),
            ),
            'home-2-widget-title-setup'     => array(
				'title' => __( 'Widget Title', 'gppro' ),
				'data'  => array(
					'home-2-widget-title-color' => array(
						'label'    => __( 'Widget Title', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
					'home-2-widget-title-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'home-2-widget-title-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-widget-title-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
					'home-2-widget-title-transform' => array(
						'label'    => __( 'Text Appearance', 'gppro' ),
						'input'    => 'text-transform',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'text-transform',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
					'home-2-widget-title-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
					'home-2-widget-title-margin-bottom' => array(
						'label'    => __( 'Bottom Margin', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-2 .widget .widget-title',
						'selector' => 'margin-bottom',
						'min'      => '0',
						'max'      => '42',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
				),
            ),

            'home-2-widget-content-setup'     => array(
				'title' => __( 'Widget Content', 'gppro' ),
				'data'  => array(
					'home-2-widget-content-color' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.front-page-2',
                            '.front-page-2.color',
                            '.front-page-2 p',
                            '.front-page-2.color p',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'home-2-widget-content-link' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Link', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.front-page-2 a',
                            '.front-page-2.color a',
                            '.front-page-2 p a',
                            '.front-page-2.color p a',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'home-2-widget-content-link-hover' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.front-page-2 a:hover',
                            '.front-page-2.color a:hover',
                            '.front-page-2 p a:hover',
                            '.front-page-2.color p a:hover',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
					'home-2-widget-content-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => array (
                            '.front-page-2',
                            '.front-page-2.color',
                            '.front-page-2 p',
                            '.front-page-2.color p',
                        ),
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'home-2-widget-content-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => array (
                            '.front-page-2',
                            '.front-page-2.color',
                            '.front-page-2 p',
                            '.front-page-2.color p',
                        ),
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-widget-content-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => array (
                            '.front-page-2',
                            '.front-page-2.color',
                            '.front-page-2 p',
                            '.front-page-2.color p',
                        ),
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
				),
            ),

            'home-2-button-setup'     => array(
				'title' => __( 'Buttons', 'gppro' ),
				'data'  => array(
					'home-2-button-color' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2 .button',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'home-2-button-hover' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2 .button:hover',
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'home-2-button-back' => array(
						'label'    => __( 'Background', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2 .button',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'home-2-button-back-hover' => array(
						'label'    => __( 'Background', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-2 .button:hover',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
					'home-2-button-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.front-page-2.widget-area .widget .button',
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'home-2-button-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.front-page-2.widget-area .widget .button',
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'home-2-button-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.front-page-2.widget-area .widget .button',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
					'home-2-button-transform' => array(
						'label'    => __( 'Text Appearance', 'gppro' ),
						'input'    => 'text-transform',
						'target'   => '.front-page-2.widget-area .widget .button',
						'selector' => 'text-transform',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-2-button-padding-top' => array(
                        'label'     => __( 'Padding - Top', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-2.widget-area .widget .button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'padding-top',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-2-button-padding-bottom'  => array(
                        'label'     => __( 'Padding - Bottom', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-2.widget-area .widget .button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'padding-bottom',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-2-button-padding-left'    => array(
                        'label'     => __( 'Padding - Left', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-2.widget-area .widget .button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'padding-left',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-2-button-padding-right'   => array(
                        'label'     => __( 'Padding - Right', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-2.widget-area .widget .button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'padding-right',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-2-button-border-radius'  => array(
                        'label'     => __( 'Border Radius', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-2 .button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'border-radius',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
				),
            ),

            // front page 3
            'section-break-home-page-three' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page 3', 'gppro' ),
					'text'	=> __( 'These controls apply to both the <strong><a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/front-page-setup/front-page-3-top-widget-area/" target="_blank">Front Page 3 - Top</a></strong> and <strong><a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/front-page-setup/front-page-3-bottom-widget-area/" target="_blank">Front Page 3 - Bottom</a></strong> widget sections.', 'gppro' ),
				),
			),

            'home-3-widget-back-setup'     => array(
				'title' => __( '', 'gppro' ),
				'data'  => array(
					'home-3-top-widget-back' => array(
						'label'    => __( 'Background', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-3',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
				),
			),

            'home-3-section-divider-setup'     => array(
				'title' => __( 'Section Divider', 'gppro' ),
				'data'  => array(
					'home-3-section-divider-color' => array(
						'label'     => __( 'Border Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3-a + .front-page-3-b > .wrap',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'border-top-color',
                        'rgb'      => true,
					),
					'home-3-section-divider-style' => array(
						'label'     => __( 'Border Style', 'gppro' ),
						'input'     => 'borders',
						'target'    => '.front-page-3-a + .front-page-3-b > .wrap',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'border-top-style',
						'tip'       => __( 'Setting the type to "none" will remove the border completely.', 'gppro' )
					),
					'home-3-section-divider-width' => array(
						'label'     => __( 'Border Width', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-3-a + .front-page-3-b > .wrap',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'border-top-width',
						'min'       => '0',
						'max'       => '10',
						'step'      => '1'
					),
				),
			),

            'home-3-section-padding-setup'     => array(
				'title' => __( 'Section Padding', 'gppro' ),
				'data'  => array(
					'home-3-section-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-3-a',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-3-section-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-3-b',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-3-section-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-3-a',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-3-section-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-3',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
                ),
            ),

            'home-3-widget-title-setup'     => array(
                'title' => __( 'Widget Title', 'gppro' ),
                'data'  => array(
                    'home-3-widget-title-color' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-widget-title-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-3-widget-title-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-3-widget-title-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'home-3-widget-title-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-3-widget-title-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => '.front-page-3 .widget-title',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-3-widget-title-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-3 .widget-title',
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-3-large-heading-setup'     => array(
                'title' => __( 'Large Headings', 'gppro' ),
                'data'  => array(
                    'home-3-large-heading' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-large-heading-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-3-large-heading-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-3-large-heading-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'home-3-large-heading-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-3-large-heading-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-3-large-heading-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => array (
                            '.front-page-3 h2',
                            '.front-page-3 .jumbo',
                            '.front-page-3.color .jumbo',
                        ),
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-3-sub-heading-setup'     => array(
                'title' => __( 'Sub Headings', 'gppro' ),
                'data'  => array(
                    'home-3-sub-heading' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-3 h4',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-sub-heading-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-3 h4',

                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-3-sub-heading-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-3 h4',

                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-3-sub-heading-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-3 h4',
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'home-3-sub-heading-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.front-page-3 h4',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-3-sub-heading-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
                        'target'   => '.front-page-3 h4',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-3-sub-heading-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-3 h4',
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-3-widget-content-setup'     => array(
                'title' => __( 'Content', 'gppro' ),
                'data'  => array(
                    'home-3-widget-content-color' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-3',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-widget-content-link' => array(
                        'label'    => __( 'Link', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro'),
                        'input'    => 'color',
                        'target'   => '.front-page-3 a',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-widget-content-link-hov' => array(
                        'label'    => __( 'Link', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro'),
                        'input'    => 'color',
                        'target'   => array (
                            '.front-page-3 a:hover',
                            '.front-page-3 a:focus',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-widget-content-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-3',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-3-widget-content-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-3',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-3-widget-content-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-3',
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                ),
            ),

            'home-3-button-setup'     => array(
                'title' => __( 'Buttons', 'gppro' ),
                'data'  => array(
                    'home-3-button-link' => array(
                        'label'    => __( 'Text Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-button-link-hov' => array(
                        'label'    => __( 'Text Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                        '.site-container .front-page-3.color a.button:hover',
                        '.site-container .front-page-3.color a.button:focus',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-button-back' => array(
                        'label'    => __( 'Background Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'background-color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-button-back-hov' => array(
                        'label'    => __( 'Background Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                            '.site-container .front-page-3.color a.button:hover',
                            '.site-container .front-page-3.color a.button:focus',
                            ),
                        'selector' => 'background-color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-3-button-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-3-button-size' => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-3-button-weight' => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'font-weight',
                        'builder'  => 'GP_Pro_Builder::number_css',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' )
                    ),
                    'home-3-button-style' => array(
                        'label'   => __( 'Font Style', 'gppro' ),
                        'input'   => 'radio',
                        'options' => array(
                            array(
                                'label' => __( 'Normal', 'gppro' ),
                                'value' => 'normal',
                            ),
                            array(
                                'label' => __( 'Italic', 'gppro' ),
                                'value' => 'italic'
                            ),
                        ),
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'font-style',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-3-button-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-3-button-border-radius'  => array(
                        'label'     => __( 'Border Radius', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.site-container .front-page-3.color a.button',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'border-radius',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-3-button-padding-top'  => array(
                        'label'    => __( 'Padding - Top', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'padding-top',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),

                    'home-3-button-padding-bottom'   => array(
                        'label'    => __( 'Padding - Bottom', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'padding-bottom',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                    'home-3-button-padding-left' => array(
                        'label'    => __( 'Padding - Left', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'padding-left',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                    'home-3-button-padding-right'    => array(
                        'label'    => __( 'Padding - Right', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.site-container .front-page-3.color a.button',
                        'selector' => 'padding-right',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                ),
            ),

            // front page 4
            'section-break-home-page-four' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page 4', 'gppro' ),
					'text'	=> __( 'These controls only work when this widget area is set up exactly like <a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/front-page-setup/front-page-4-widget-area/" target="_blank">the demo site</a>.', 'gppro' ),
				),
			),

            'home-4-widget-back-setup'     => array(
				'title' => __( '', 'gppro' ),
				'data'  => array(
					'home-4-top-widget-back' => array(
						'label'    => __( 'Background', 'gppro' ),
						'input'    => 'color',
						'target'   => '.front-page-4',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
				),
			),

            'home-4-section-padding-setup'     => array(
				'title' => __( 'Section Padding', 'gppro' ),
				'data'  => array(
					'home-4-section-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-4-section-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-4-section-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'home-4-section-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
                ),
            ),

            'home-4-widget-title-setup'     => array(
                'title' => __( 'Widget Title', 'gppro' ),
                'data'  => array(
                    'home-4-widget-title-color' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-widget-title-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-4-widget-title-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-4-widget-title-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'home-4-widget-title-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-4-widget-title-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => '.front-page-4 .widget-title',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-4-widget-title-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .widget-title',
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-4-post-title-setup'     => array(
                'title' => __( 'Post Titles', 'gppro' ),
                'data'  => array(
                    'home-4-post-title-link' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Link', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .featured-content .entry-title a',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-post-title-link-hov' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                            '.front-page-4 .featured-content .entry-title a:hover',
                            '.front-page-4 .featured-content .entry-title a:focus',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-post-title-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-4 .featured-content .entry-title',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-4-post-title-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-4 .featured-content .entry-title a ',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-4-post-title-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-4 .featured-content .entry-title',
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'home-4-post-title-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.front-page-4 .featured-content .entry-title',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-4-post-title-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => '.front-page-4 .featured-content .entry-title',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'home-4-post-title-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .featured-content .entry-title',
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-4-post-meta-setup'  => array(
				'title'     => __( 'Post Meta', 'gppro' ),
				'data'      => array(
					'home-4-meta-text-color'   => array(
						'label'     => __( 'Text', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .entry-meta',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'home-4-meta-author-link'  => array(
						'label'     => __( 'Author Link', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .entry-meta .entry-author a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'home-4-meta-author-link-hov'  => array(
						'label'     => __( 'Author Link', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => array(
                            '.front-page-4 .featured-content .entry-meta .entry-author a:hover',
                            '.front-page-4 .featured-content .entry-meta .entry-author a:focus'
                        ),
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'  => true
					),
					'home-4-meta-comment-link' => array(
						'label'     => __( 'Comments', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .entry-meta .entry-comments-link a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'home-4-meta-comment-link-hov' => array(
						'label'     => __( 'Comments', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => array(
                            '.front-page-4 .featured-content .entry-meta .entry-comments-link a:hover',
                            '.front-page-4 .featured-content .entry-meta .entry-comments-link a:focus' ),
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'  => true
					),
                    'home-4-meta-top-border-color' => array(
						'label'     => __( 'Border Color', 'gppro' ),
                        'sub'      => __( 'Top', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content p.entry-meta',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'border-top-color',
                        'rgb'      => true,
					),
                    'home-4-meta-top-border-style'   => array(
						'label'    => __( 'Border Style', 'gppro' ),
                        'sub'      => __( 'Top', 'gppro' ),
						'input'    => 'borders',
						'target'   => '.front-page-4 .featured-content p.entry-meta',
						'selector' => 'border-top-style',
						'builder'  => 'GP_Pro_Builder::text_css',
						'tip'      => __( 'Setting the style to "none" will remove the border completely.', 'gppro' ),
					),
                    'home-4-meta-top-border-width'    => array(
						'label'    => __( 'Border Width', 'gppro' ),
                        'sub'      => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4 .featured-content p.entry-meta',
						'selector' => 'border-top-width',
						'builder'  => 'GP_Pro_Builder::px_css',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
					),
                    'home-4-meta-bottom-border-color' => array(
						'label'     => __( 'Border Color', 'gppro' ),
                        'sub'      => __( 'Bottom', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content p.entry-meta',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'border-bottom-color',
                        'rgb'      => true,
					),
                    'home-4-meta-bottom-border-style'   => array(
						'label'    => __( 'Border Style', 'gppro' ),
                        'sub'      => __( 'Bottom', 'gppro' ),
						'input'    => 'borders',
						'target'   => '.front-page-4 .featured-content p.entry-meta',
						'selector' => 'border-bottom-style',
						'builder'  => 'GP_Pro_Builder::text_css',
						'tip'      => __( 'Setting the style to "none" will remove the border completely.', 'gppro' ),
					),
                    'home-4-meta-bottom-border-width'    => array(
						'label'    => __( 'Border Width', 'gppro' ),
                        'sub'      => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.front-page-4 .featured-content p.entry-meta',
						'selector' => 'border-bottom-width',
						'builder'  => 'GP_Pro_Builder::px_css',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
					),
				),
			),

            'home-4-post-content-setup'     => array(
                'title' => __( 'Content', 'gppro' ),
                'data'  => array(
                    'home-4-post-content-color' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .featured-content',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-post-content-link' => array(
                        'label'    => __( 'Link', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro'),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .featured-content a',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-post-content-link-hov' => array(
                        'label'    => __( 'Link', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro'),
                        'input'    => 'color',
                        'target'   => array (
                            '.front-page-4 .featured-content a:hover',
                            '.front-page-4 .featured-content a:focus',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-post-content-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-4 .featured-content',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-4-post-content-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-4 .featured-content',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'home-4-button-setup'     => array(
                'title' => __( 'Buttons', 'gppro' ),
                'data'  => array(
                    'home-4-button-link' => array(
                        'label'    => __( 'Text Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-button-link-hov' => array(
                        'label'    => __( 'Text Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                        '.front-page-4 .featured-content .more-link:hover',
                        '.front-page-4 .featured-content .more-link:focus',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-button-back' => array(
                        'label'    => __( 'Background Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
                        'input'    => 'color',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'background-color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-button-back-hov' => array(
                        'label'    => __( 'Background Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                            '.front-page-4 .featured-content .more-link:hover',
                            '.front-page-4 .featured-content .more-link:focus',
                            ),
                        'selector' => 'background-color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'home-4-button-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'home-4-button-size' => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'home-4-button-weight' => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'font-weight',
                        'builder'  => 'GP_Pro_Builder::number_css',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' )
                    ),
                    'home-4-button-style' => array(
                        'label'   => __( 'Font Style', 'gppro' ),
                        'input'   => 'radio',
                        'options' => array(
                            array(
                                'label' => __( 'Normal', 'gppro' ),
                                'value' => 'normal',
                            ),
                            array(
                                'label' => __( 'Italic', 'gppro' ),
                                'value' => 'italic'
                            ),
                        ),
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'font-style',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-4-button-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'home-4-button-border-radius'  => array(
                        'label'     => __( 'Border Radius', 'gppro' ),
                        'input'     => 'spacing',
                        'target'    => '.front-page-4 .featured-content .more-link',
                        'builder'   => 'GP_Pro_Builder::px_css',
                        'selector'  => 'border-radius',
                        'min'       => '0',
                        'max'       => '60',
                        'step'      => '1'
                    ),
                    'home-4-button-padding-top'  => array(
                        'label'    => __( 'Padding - Top', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'padding-top',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),

                    'home-4-button-padding-bottom'   => array(
                        'label'    => __( 'Padding - Bottom', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'padding-bottom',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                    'home-4-button-padding-left' => array(
                        'label'    => __( 'Padding - Left', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'padding-left',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                    'home-4-button-padding-right'    => array(
                        'label'    => __( 'Padding - Right', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => '.front-page-4 .featured-content .more-link',
                        'selector' => 'padding-right',
                        'builder'  => 'GP_Pro_Builder::px_css',
                        'min'      => '0',
                        'max'      => '40',
                        'step'     => '1',
                    ),
                ),
            ),
        );
		// Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the post content area
	 *
	 * @return array|string $sections
	 */
	public function post_content( $sections, $class ) {
        // remove post footer
        $sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array(
            'section-break-post-footer-text',
            'post-footer-color-setup',
            'post-footer-type-setup',
            'post-footer-divider-setup',
            )
        );

        // add the body class override for padding
		$sections['site-inner-setup']['data']['site-inner-padding-top']['body_override'] = array(
			'preview' => 'body.gppro-preview:not(.front-page)',
			'front'   => 'body.gppro-custom:not(.front-page)',
		);

        // add the body class override for padding
		$sections['site-inner-setup']['data']['site-inner-padding-top']['body_override'] = array(
			'preview' => 'body.gppro-preview:not(.front-page)',
			'front'   => 'body.gppro-custom:not(.front-page)',
		);

		// add comment count background
        $sections['post-header-meta-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
            'post-header-meta-comment-link-hov', $sections['post-header-meta-color-setup']['data'],
            array(
                'post-header-content-count-back'  => array(
                    'label'     => __( 'Comment Bubble', 'gppro' ),
                    'input'     => 'color',
                    'target'    => '.entry-header .entry-meta .entry-comments-link',
                    'body_override'    => array(
                            'preview' => 'body.gppro-preview:not(.front-page)',
                            'front'   => 'body.gppro-preview:not(.front-page)',
                        ),
                    'builder'   => 'GP_Pro_Builder::hexcolor_css',
                    'selector'  => 'background-color',
                ),
            )
        );

        // filter

        // Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the after entry widget
	 *
	 * @return array|string $sections
	 */
	public function after_entry( $sections, $class ) {
        // add box shadow toggle
        $sections['after-entry-single-widget-setup']['data'] = GP_Pro_Helper::array_insert_before(
            'after-entry-single-widget-border-radius', $sections['after-entry-single-widget-setup']['data'],
			array(
				'after-entry-widget-box-shadow'	=> array(
                    'label'    => __( 'Box Shadow', 'gpwen' ),
                    'input'    => 'radio',
                    'options'  => array(
                        array(
                            'label' => __( 'Keep', 'gpwen' ),
                            'value' => '0 0 3em 0 rgba( 0, 0, 0, .2 )',
                        ),
                        array(
                            'label' => __( 'Remove', 'gpwen' ),
                            'value' => 'none'
                        ),
                    ),
                    'target'   => '.after-entry .widget',
                    'builder'  => 'GP_Pro_Builder::text_css',
                    'selector' => 'box-shadow',
                ),
            )
        );

        // add widget title background
        $sections['after-entry-widget-title-setup']['data'] = GP_Pro_Helper::array_insert_before(
            'after-entry-widget-title-text', $sections['after-entry-widget-title-setup']['data'],
			array(
				'after-entry-widget-title-background'  => array(
					'label'     => __( 'Background', 'gppro' ),
                    'input'     => 'color',
                    'target'    => '.after-entry .widget-title',
                    'builder'   => 'GP_Pro_Builder::hexcolor_css',
                    'selector'  => 'background-color'
				),
            )
        );

        // add widget margins
        $sections['after-entry-widget-title-setup']['data'] = GP_Pro_Helper::array_insert_after(
            'after-entry-widget-title-margin-bottom', $sections['after-entry-widget-title-setup']['data'],
			array(

                'after-entry-widget-title-margin-top'  => array(
					'label'     => __( 'Top Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.after-entry .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-top',
						'min'       => '-30',
						'max'       => '60',
						'step'      => '1'
				),

                'after-entry-widget-title-margin-left'  => array(
					'label'     => __( 'Left Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.after-entry .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-left',
						'min'       => '-30',
						'max'       => '60',
						'step'      => '1'
				),

                'after-entry-widget-title-margin-right'  => array(
					'label'     => __( 'Right Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.after-entry .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-right',
						'min'       => '-30',
						'max'       => '60',
						'step'      => '1'
				),
            )
        );

        // add border styles
        $sections['after-entry-single-widget-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'after-entry-widget-back', $sections['after-entry-single-widget-setup']['data'],
			array(
				'after-entry-widget-border-color'  => array(
					'label'    => __( 'Border Color', 'gppro' ),
                    'input'    => 'color',
                    'target'   => '.after-entry .widget',
                    'selector' => 'border-color',
                    'builder'  => 'GP_Pro_Builder::hexcolor_css',
				),

                'after-entry-widget-border-style'  => array(
					'label'    => __( 'Border Style', 'gppro' ),
                    'input'    => 'borders',
                    'target'   => '.after-entry .widget',
                    'selector' => 'border-style',
                    'builder'  => 'GP_Pro_Builder::text_css',
				),

                'after-entry-widget-border-width'  => array(
					'label'    => __( 'Border Width', 'gppro' ),
                    'input'    => 'spacing',
                    'target'   => '.after-entry .widget',
                    'selector' => 'border-width',
                    'builder'  => 'GP_Pro_Builder::px_css',
                    'min'      => '0',
                    'max'      => '10',
                    'step'     => '1',
				),
			)
		);

		// Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the content extras area
	 *
	 * @return array|string $sections
	 */
	public function content_extras( $sections, $class ) {

		// reset the specificity of the read more link
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link']['target']   = '.content > .post .entry-content a.more-link';
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link-hov']['target']   = array( '.content > .post .entry-content a.more-link:hover', '.content > .post .entry-content a.more-link:focus' );

        // increase author box top and bottom margins max
        $sections['extras-author-box-margin-setup']['data']['extras-author-box-margin-top']['max'] = '120';
        $sections['extras-author-box-margin-setup']['data']['extras-author-box-margin-bottom']['max'] = '120';

        // add author box border
        $sections['extras-author-box-back-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'extras-author-box-back', $sections['extras-author-box-back-setup']['data'],
			array(
				'extras-author-box-border-color' => array(
                    'label'     => __( 'Border Color', 'gppro' ),
                    'input'     => 'color',
                    'target'    => '.author-box',
                    'builder'   => 'GP_Pro_Builder::comment_borders',
                    'selector'  => 'border-color'
                ),

                'extras-author-box-border-style' => array(
                    'label'     => __( 'Border Style', 'gppro' ),
                    'input'     => 'borders',
                    'target'    => '.author-box',
                    'builder'   => 'GP_Pro_Builder::comment_borders',
                    'selector'  => 'border-style',
                    'tip'       => __( 'Setting the type to "none" will remove the border completely.', 'gppro' )
                ),

                'extras-author-box-border-width' => array(
                    'label'     => __( 'Border Width', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.author-box',
						'builder'   => 'GP_Pro_Builder::comment_borders',
						'selector'  => 'border-width',
						'min'       => '0',
						'max'       => '10',
						'step'      => '1',
                ),

                // add shadow toggle
                'extras-author-box-box-shadow'	=> array(
                    'label'    => __( 'Box Shadow', 'gpwen' ),
                    'input'    => 'radio',
                    'options'  => array(
                        array(
                            'label' => __( 'Keep', 'gpwen' ),
                            'value' => '0 0 3em 0 rgba( 0, 0, 0, .2 )',
                        ),
                        array(
                            'label' => __( 'Remove', 'gpwen' ),
                            'value' => 'none'
                        ),
                    ),
                    'target'   => '.author-box',
                    'builder'  => 'GP_Pro_Builder::text_css',
                    'selector' => 'box-shadow',
                ),
			)
		);

		// reset the specificity of the read more link
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link']['target']   = '.content > .post .entry-content a.more-link';
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link-hov']['target']   = array( '.content > .post .entry-content a.more-link:hover', '.content > .post .entry-content a.more-link:focus' );

		// Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the comments area
	 *
	 * @return array|string $sections
	 */
	public function comments_area( $sections, $class ) {
		// remove comment allowed tags
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array(
			'section-break-comment-reply-atags-setup',
			'comment-reply-atags-area-setup',
			'comment-reply-atags-base-setup',
			'comment-reply-atags-code-setup',
		) );

        // remove comment notes
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, 'comment-reply-notes-setup' );

        // remove comment layout standard
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'single-comment-standard-setup', 'single-comment-author-setup' ) );

        // Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the main sidebar area
	 *
	 * @return array|string $sections
	 */
	public function main_sidebar( $sections, $class ) {
        // add recent posts widget settings
        $sections = GP_Pro_Helper::array_insert_after(
			'sidebar-widget-divider', $sections,
			array(
                'section-break-sidebar-recent-posts' => array(
					'title'     => '',
					'data'      => array(
						'sidebar-recent-posts-divider' => array(
							'title'     => __( 'Recent Posts Widget', 'gppro' ),
							'text'      => __( '', 'gppro' ),
							'input'     => 'divider',
							'style'     => 'block-full'
						),
					)
				),

				'sidebar-recent-posts-title-setup' => array(
					'title'     => __( 'Widget Title', 'gppro' ),
					'data'      => array(
						'sidebar-recent-posts-title-back'  => array(
							'label'     => __( 'Background', 'gppro' ),
							'input'     => 'color',
							'target'    => '.sidebar .widget_recent_entries .widget-title',
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'background-color'
						),
    						'sidebar-recent-posts-title-text' => array(
                            'label'    => __( 'Color', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                            ),
                            'sidebar-recent-posts-title-stack' => array(
                            'label'    => __( 'Font Stack', 'gppro' ),
                            'input'    => 'font-stack',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'font-family',
                            'builder'  => 'GP_Pro_Builder::stack_css',
                        ),
                            'sidebar-recent-posts-title-size'  => array(
                            'label'    => __( 'Font Size', 'gppro' ),
                            'input'    => 'font-size',
                            'scale'    => 'text',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'font-size',
                            'builder'  => 'GP_Pro_Builder::px_css',
                        ),
                            'sidebar-recent-posts-title-weight'    => array(
                            'label'    => __( 'Font Weight', 'gppro' ),
                            'input'    => 'font-weight',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'font-weight',
                            'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                            'builder'  => 'GP_Pro_Builder::number_css',
                        ),
                            'sidebar-recent-posts-title-transform' => array(
                            'label'    => __( 'Text Appearance', 'gppro' ),
                            'input'    => 'text-transform',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'text-transform',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-title-align' => array(
                            'label'    => __( 'Text Alignment', 'gppro' ),
                            'input'    => 'text-align',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'text-align',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-title-style' => array(
                            'label'   => __( 'Font Style', 'gppro' ),
                            'input'   => 'radio',
                            'options' => array(
                                array(
                                    'label' => __( 'Normal', 'gppro' ),
                                    'value' => 'normal',
                                ),
                                array(
                                    'label' => __( 'Italic', 'gppro' ),
                                    'value' => 'italic'
                                ),
                            ),
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'font-style',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-title-padding-top'  => array(
                            'label'    => __( 'Top Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'padding-top',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-title-padding-bottom'   => array(
                            'label'    => __( 'Bottom Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'padding-bottom',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-title-padding-left' => array(
                            'label'    => __( 'Left Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'padding-left',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-title-padding-right'    => array(
                            'label'    => __( 'Right Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries .widget-title',
                            'selector' => 'padding-right',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
					),
				),

                'sidebar-recent-posts-list-items-setup' => array(
					'title'     => __( 'List Items', 'gppro' ),
					'data'      => array(
						'sidebar-recent-posts-list-items-back'  => array(
							'label'     => __( 'Background', 'gppro' ),
                            'sub'       => __( 'Base', 'gppro' ),
							'input'     => 'color',
							'target'    => '.sidebar .widget_recent_entries li a',
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'background-color'
						),

                        'sidebar-recent-posts-list-items-back-hov'  => array(
							'label'     => __( 'Background', 'gppro' ),
                            'sub'       => __( 'Hover', 'gppro' ),
							'input'     => 'color',
							'target'    => array (
                                '.sidebar .widget_recent_entries li a:hover',
                                '.sidebar .widget_recent_entries li a:focus'
                            ),
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'background-color'
						),
    						'sidebar-recent-posts-list-items-color' => array(
                            'label'    => __( 'Color', 'gppro' ),
                            'sub'       => __( 'Base', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'sidebar-recent-posts-list-items-color-hov' => array(
                            'label'    => __( 'Color', 'gppro' ),
                            'sub'       => __( 'Hover', 'gppro' ),
                            'input'    => 'color',
                            'target'    => array (
                                '.sidebar .widget_recent_entries li a:hover',
                                '.sidebar .widget_recent_entries li a:focus'
                            ),
                            'selector' => 'color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'sidebar-recent-posts-list-items-stack' => array(
                            'label'    => __( 'Font Stack', 'gppro' ),
                            'input'    => 'font-stack',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'font-family',
                            'builder'  => 'GP_Pro_Builder::stack_css',
                        ),
                            'sidebar-recent-posts-list-items-size'  => array(
                            'label'    => __( 'Font Size', 'gppro' ),
                            'input'    => 'font-size',
                            'scale'    => 'text',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'font-size',
                            'builder'  => 'GP_Pro_Builder::px_css',
                        ),
                            'sidebar-recent-posts-list-items-weight'    => array(
                            'label'    => __( 'Font Weight', 'gppro' ),
                            'input'    => 'font-weight',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'font-weight',
                            'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                            'builder'  => 'GP_Pro_Builder::number_css',
                        ),
                            'sidebar-recent-posts-list-items-transform' => array(
                            'label'    => __( 'Text Appearance', 'gppro' ),
                            'input'    => 'text-transform',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'text-transform',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-list-items-align' => array(
                            'label'    => __( 'Text Alignment', 'gppro' ),
                            'input'    => 'text-align',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'text-align',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-list-items-style' => array(
                            'label'   => __( 'Font Style', 'gppro' ),
                            'input'   => 'radio',
                            'options' => array(
                                array(
                                    'label' => __( 'Normal', 'gppro' ),
                                    'value' => 'normal',
                                ),
                                array(
                                    'label' => __( 'Italic', 'gppro' ),
                                    'value' => 'italic'
                                ),
                            ),
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'font-style',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'sidebar-recent-posts-list-items-padding-top'  => array(
                            'label'    => __( 'Top Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'padding-top',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-list-items-padding-bottom'   => array(
                            'label'    => __( 'Bottom Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'padding-bottom',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-list-items-padding-left' => array(
                            'label'    => __( 'Left Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'padding-left',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
                        'sidebar-recent-posts-list-items-padding-right'    => array(
                            'label'    => __( 'Right Padding', 'gppro' ),
                            'input'    => 'spacing',
                            'target'   => '.sidebar .widget_recent_entries li a',
                            'selector' => 'padding-right',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '40',
                            'step'     => '1',
                        ),
					),
				),
			)
		);

		// Return the section array.
		return $sections;
	}

	/**
	 * add settings for footer banner block
	 *
	 * @return array|string $sections
	 */
	public function footer_banner_section( $sections, $class ) {
        $sections['footer_banner'] = array(
            // footer banner
			'section-break-footer-banner' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Footer Banner', 'gppro' ),
					'text'	=> __( 'These controls only work when this widget area is set up exactly like <a href="http://my.studiopress.com/setup/smart-passive-income-pro-theme/widget-areas/footer-banner-widget-area/" target="_blank">the demo site</a>.', 'gppro' ),
				),
			),

            'footer-banner-back-setup'     => array(
				'title' => __( '', 'gppro' ),
				'data'  => array(
					'footer-banner-back' => array(
						'label'    => __( 'Background', 'gppro' ),
						'input'    => 'color',
						'target'   => '.footer-banner',
						'selector' => 'background-color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
				),
			),

            'footer-banner-padding-setup'     => array(
				'title' => __( 'Section Padding', 'gppro' ),
				'data'  => array(
					'footer-banner-section-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.footer-banner.flexible-widgets',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'footer-banner-section-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.footer-banner.flexible-widgets',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'footer-banner-section-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.footer-banner.flexible-widgets',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
					'footer-banner-section-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => array (
                            '.footer-banner.flexible-widgets'                        ),
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '10',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::pct_css',
                        'suffix'    => '%'
					),
                ),
            ),

                'footer-banner-large-heading-setup'     => array(
                'title' => __( 'Large Headings', 'gppro' ),
                'data'  => array(
                    'footer-banner-large-heading' => array(
                        'label'    => __( 'Color', 'gppro' ),
                        'input'    => 'color',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'color',
                        'builder'  => 'GP_Pro_Builder::hexcolor_css',
                    ),
                    'footer-banner-large-heading-stack' => array(
                        'label'    => __( 'Font Stack', 'gppro' ),
                        'input'    => 'font-stack',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'font-family',
                        'builder'  => 'GP_Pro_Builder::stack_css',
                    ),
                    'footer-banner-large-heading-size'  => array(
                        'label'    => __( 'Font Size', 'gppro' ),
                        'input'    => 'font-size',
                        'scale'    => 'text',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'font-size',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                    'footer-banner-large-heading-weight'    => array(
                        'label'    => __( 'Font Weight', 'gppro' ),
                        'input'    => 'font-weight',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'font-weight',
                        'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                        'builder'  => 'GP_Pro_Builder::number_css',
                    ),
                    'footer-banner-large-heading-transform' => array(
                        'label'    => __( 'Text Appearance', 'gppro' ),
                        'input'    => 'text-transform',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'text-transform',
                        'builder'  => 'GP_Pro_Builder::text_css',
                    ),
                    'footer-banner-large-heading-style' => array(
						'label'   => __( 'Font Style', 'gppro' ),
						'input'   => 'radio',
						'options' => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner .jumbo',
                        ),
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
                    'footer-banner-large-heading-margin-bottom' => array(
                        'label'    => __( 'Bottom Margin', 'gppro' ),
                        'input'    => 'spacing',
                        'target'   => array (
                            '.footer-banner .jumbo',
                            '.footer-banner.color .jumbo',
                        ),
                        'selector' => 'margin-bottom',
                        'min'      => '0',
                        'max'      => '42',
                        'step'     => '1',
                        'builder'  => 'GP_Pro_Builder::px_css',
                    ),
                ),
            ),

            'footer-banner-widget-content-setup'     => array(
				'title' => __( 'Widget Content', 'gppro' ),
				'data'  => array(
					'footer-banner-widget-content-color' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.footer-banner',
                            '.footer-banner.color',
                            '.footer-banner p',
                            '.footer-banner.color p',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'footer-banner-widget-content-link' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Link', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.footer-banner a',
                            '.footer-banner.color a',
                            '.footer-banner p a',
                            '.footer-banner.color p a',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
                    'footer-banner-widget-content-link-hover' => array(
						'label'    => __( 'Color', 'gppro' ),
                        'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => array (
                            '.footer-banner a:hover',
                            '.footer-banner.color a:hover',
                            '.footer-banner p a:hover',
                            '.footer-banner.color p a:hover',
                        ),
						'selector' => 'color',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
					),
					'footer-banner-widget-content-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => array (
                            '.footer-banner',
                            '.footer-banner.color',
                            '.footer-banner p',
                            '.footer-banner.color p',
                        ),
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'footer-banner-widget-content-size'  => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => array (
                            '.footer-banner',
                            '.footer-banner.color',
                            '.footer-banner p',
                            '.footer-banner.color p',
                        ),
						'selector' => 'font-size',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'footer-banner-widget-content-weight'    => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => array (
                            '.footer-banner',
                            '.footer-banner.color',
                            '.footer-banner p',
                            '.footer-banner.color p',
                        ),
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
				),
            ),

                'footer-banner-button-setup'     => array(
                    'title' => __( 'Buttons', 'gppro' ),
                    'data'  => array(
                        'footer-banner-button-color' => array(
                            'label'    => __( 'Color', 'gppro' ),
                            'sub'      => __( 'Base', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.site-container .color a.button',
                            'selector' => 'color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'footer-banner-button-hover' => array(
                            'label'    => __( 'Color', 'gppro' ),
                            'sub'      => __( 'Hover', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.site-container .color a.button:hover',
                            'selector' => 'color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'footer-banner-button-back' => array(
                            'label'    => __( 'Background', 'gppro' ),
                            'sub'      => __( 'Base', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.site-container .color a.button',
                            'selector' => 'background-color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'footer-banner-button-back-hover' => array(
                            'label'    => __( 'Background', 'gppro' ),
                            'sub'      => __( 'Hover', 'gppro' ),
                            'input'    => 'color',
                            'target'   => '.site-container .color a.button:hover',
                            'selector' => 'background-color',
                            'builder'  => 'GP_Pro_Builder::hexcolor_css',
                        ),
                            'footer-banner-button-stack' => array(
                            'label'    => __( 'Font Stack', 'gppro' ),
                            'input'    => 'font-stack',
                            'target'   => '.footer-banner.widget-area .widget .button',
                            'selector' => 'font-family',
                            'builder'  => 'GP_Pro_Builder::stack_css',
                        ),
                            'footer-banner-button-size'  => array(
                            'label'    => __( 'Font Size', 'gppro' ),
                            'input'    => 'font-size',
                            'scale'    => 'text',
                            'target'   => '.footer-banner.widget-area .widget .button',
                            'selector' => 'font-size',
                            'builder'  => 'GP_Pro_Builder::px_css',
                        ),
                            'footer-banner-button-weight'    => array(
                            'label'    => __( 'Font Weight', 'gppro' ),
                            'input'    => 'font-weight',
                            'target'   => '.footer-banner.widget-area .widget .button',
                            'selector' => 'font-weight',
                            'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
                            'builder'  => 'GP_Pro_Builder::number_css',
                        ),
                            'footer-banner-button-transform' => array(
                            'label'    => __( 'Text Appearance', 'gppro' ),
                            'input'    => 'text-transform',
                            'target'   => '.footer-banner.widget-area .widget .button',
                            'selector' => 'text-transform',
                            'builder'  => 'GP_Pro_Builder::text_css',
                        ),
                            'footer-banner-button-padding-top' => array(
                            'label'     => __( 'Padding - Top', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.footer-banner.widget-area .widget .button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'padding-top',
                            'min'       => '0',
                            'max'       => '60',
                            'step'      => '1'
                        ),
                            'footer-banner-button-padding-bottom'  => array(
                            'label'     => __( 'Padding - Bottom', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.footer-banner.widget-area .widget .button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'padding-bottom',
                            'min'       => '0',
                            'max'       => '60',
                            'step'      => '1'
                        ),
                            'footer-banner-button-padding-left'    => array(
                            'label'     => __( 'Padding - Left', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.footer-banner.widget-area .widget .button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'padding-left',
                            'min'       => '0',
                            'max'       => '60',
                            'step'      => '1'
                        ),
                            'footer-banner-button-padding-right'   => array(
                            'label'     => __( 'Padding - Right', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.footer-banner.widget-area .widget .button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'padding-right',
                            'min'       => '0',
                            'max'       => '60',
                            'step'      => '1'
                        ),
                            'footer-banner-button-border-radius'  => array(
                            'label'     => __( 'Border Radius', 'gppro' ),
                            'input'     => 'spacing',
                            'target'    => '.footer-banner .button',
                            'builder'   => 'GP_Pro_Builder::px_css',
                            'selector'  => 'border-radius',
                            'min'       => '0',
                            'max'       => '60',
                            'step'      => '1'
                        ),
                    ),
                ),
            );
		// Return the section array.
		return $sections;
	}

	/**
	 * add and filter options in the main footer section
	 *
	 * @return array|string $sections
	 */
	public function entry_content( $sections, $class ) {

		// shouldn't be called without the active class, but still
		if ( ! class_exists( 'GP_Pro_Entry_Content' ) ) {
			return $sections;
		}

		// add entry content blockqquote left rule
		$sections = GP_Pro_Helper::array_insert_before(
			'entry-content-bquotes-area-padding-setup', $sections,
			array(
				'entry-content-bquotes-border'     => array(
					'title' => __( 'Left Border', 'gppro' ),
					'data'  => array(
						'entry-content-bquotes-border-color' => array(
							'label'    => __( 'Color', 'gppro' ),
							'input'    => 'color',
							'target'   => 'blockquote',
							'selector' => 'border-left-color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                            'entry-content-bquotes-border-style'  => array(
                            'label'    => __( 'Style', 'gppro' ),
                            'input'    => 'borders',
							'target'   => 'blockquote',
                            'selector' => 'border-left-style',
                            'builder'  => 'GP_Pro_Builder::text_css',
                            'tip'       => __( 'Setting the type to "none" will remove the border completely.', 'gppro' )
                        ),
                            'entry-content-bquotes-border-width'  => array(
                            'label'    => __( 'Width', 'gppro' ),
                            'input'    => 'spacing',
							'target'   => 'blockquote',
                            'selector' => 'border-left-width',
                            'builder'  => 'GP_Pro_Builder::px_css',
                            'min'      => '0',
                            'max'      => '10',
                            'step'     => '1',
                        ),
					),
				),
			)
		);
		// Return the section array.
		return $sections;
	}

    /**
	 * add and filter options in the DPP eNews Extended add-on
	 *
	 * @return array|string $sections
	 */
	public function genesis_widgets_section( $sections, $class ) {
        // remove box shadow
		unset( $sections['genesis_widgets']['enews-widget-field-inputs']['data']['enews-widget-field-input-box-shadow']  );

		// add always write
		$sections['genesis_widgets']['enews-widget-general']['data']['enews-widget-back']['always_write'] = true;

		// add title background color
		$sections['genesis_widgets']['enews-widget-general']['data'] = GP_Pro_Helper::array_insert_after(
			'enews-widget-title-color', $sections['genesis_widgets']['enews-widget-general']['data'],
			array(
				'enews-widget-title-back' => array(
					'label'    => __( 'Title Background', 'gppro' ),
					'input'    => 'color',
					'target'   => '.widget-area .widget.enews-widget .enews .widget-title',
					'selector' => 'background-color',
					'builder'  => 'GP_Pro_Builder::hexcolor_css',
					'rgb'      => true,
					'always_write' => true,
				),
			)
		);

        // add box shadow toggle
        $sections['genesis_widgets']['enews-widget-general']['data'] = GP_Pro_Helper::array_insert_before(
			'enews-widget-back', $sections['genesis_widgets']['enews-widget-general']['data'],
			array(
				'enews-widget-box-shadow'	=> array(
                    'label'    => __( 'Box Shadow', 'gpwen' ),
                    'input'    => 'radio',
                    'options'  => array(
                        array(
                            'label' => __( 'Keep', 'gpwen' ),
                            'value' => '0 0 60px 0 rgba( 0, 0, 0, .2 )',
                        ),
                        array(
                            'label' => __( 'Remove', 'gpwen' ),
                            'value' => 'none'
                        ),
                    ),
                    'target'   => array ('.sidebar .enews-widget', '.widget.enews-widget'),
                    'builder'  => 'GP_Pro_Builder::text_css',
                    'selector' => 'box-shadow',
                ),
            )
        );

		// Return the section array.
		return $sections;
	}

    /**
	 * checks the settings to see if content count background
	 * has been changed and if so, adds the value to the CSS
	 * triangle so they match
	 *
	 * @param  [type] $setup [description]
	 * @param  [type] $data  [description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function css_builder_filters( $setup, $data, $class ) {
        // check for change in content count background
		if ( ! empty( $data['post-header-content-count-back'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .entry-header .entry-meta .entry-comments-link:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-left-color', $data['post-header-content-count-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// return the setup array
		return $setup;
	}

} // end class GP_Pro_Smart_Passive_Income_Pro

} // if ! class_exists

// Instantiate our class
$GP_Pro_Smart_Passive_Income_Pro = GP_Pro_Smart_Passive_Income_Pro::getInstance();
