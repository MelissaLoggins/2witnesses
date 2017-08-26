<?php
/**
 * Genesis Design Palette Pro - Wellness Pro
 *
 * Genesis Palette Pro add-on for the Wellness Pro child theme.
 *
 * @package Design Palette Pro
 * @subpackage Wellness Pro
 * @version 1.0 (child theme version)
 */
/*  Copyright 2016 Reaktiv Studios

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
 * 2016-08-03: Initial development
 */

if ( ! class_exists( 'GP_Pro_Wellness_Pro' ) ) {

class GP_Pro_Wellness_Pro {

	/**
	 * Static property to hold our singleton instance
	 * @var GP_Pro_Wellness_Pro
	 */
	public static $instance = null;

	/**
	 * This is our constructor
	 *
	 * @return array
	 */
	private function __construct() {

		// GP Pro general
		add_filter( 'gppro_set_defaults',                       array( $this, 'set_defaults'                        ), 15    );

		// font stack modifications
		add_filter( 'gppro_webfont_stacks',                     array( $this, 'google_webfonts'                     )        );
		add_filter( 'gppro_font_stacks',                        array( $this, 'font_stacks'                         ), 20    );

		// Adding new widget and front page sections.
		add_filter( 'gppro_admin_block_add',                    array( $this, 'add_new_sections'                    ), 25    );
		add_filter( 'gppro_sections',                           array( $this, 'widgets_section'                     ), 10, 2 );
		add_filter( 'gppro_sections',                           array( $this, 'frontpage_section'                   ), 10, 2 );

		// GP Pro section item removals / additions
		add_filter( 'gppro_section_inline_general_body',        array( $this, 'general_body'                        ), 15, 2 );
		add_filter( 'gppro_section_inline_header_area',         array( $this, 'header_area'                         ), 15, 2 );
		add_filter( 'gppro_section_inline_navigation',          array( $this, 'navigation'                          ), 15, 2 );
		add_filter( 'gppro_section_inline_post_content',        array( $this, 'post_content'                        ), 15, 2 );
		add_filter( 'gppro_section_inline_content_extras',      array( $this, 'content_extras'                      ), 15, 2 );
		add_filter( 'gppro_section_inline_comments_area',       array( $this, 'comments_area'                       ), 15, 2 );
		add_filter( 'gppro_section_inline_main_sidebar',        array( $this, 'main_sidebar'                        ), 15, 2 );
		add_filter( 'gppro_section_inline_footer_widgets',      array( $this, 'footer_widgets'                      ), 15, 2 );
		add_filter( 'gppro_section_inline_footer_main',         array( $this, 'footer_main'                         ), 15, 2 );

		// Enable after entry widget sections
		add_filter( 'gppro_section_inline_content_extras',      array( 'GP_Pro_Sections', 'after_entry_widget_area' ), 15, 2 );
		add_filter( 'gppro_section_after_entry_widget_area',    array( $this, 'after_entry'                         ), 15, 2 );

		// add entry content defaults
		add_filter( 'gppro_set_defaults',                       array( $this, 'entry_content_defaults'              ), 40    );

		// add/remove settings
		add_filter( 'gppro_sections',                           array( $this, 'genesis_widgets_section'             ), 20, 2 );

		// Enable Genesis eNews sections
		add_filter( 'gppro_enews_set_defaults',                 array( $this, 'enews_defaults'                      ), 15    );

		// reset CSS builders
		add_filter( 'gppro_css_builder',                        array( $this, 'css_builder_filters'                 ), 50, 3 );
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

		// swap Open Sans if present
		if ( isset( $webfonts['open-sans'] ) ) {
			$webfonts['open-sans']['src'] = 'native';
		}

		// swap Sorts Arbutus Slab if present
		if ( isset( $webfonts['arbutus-slab'] ) ) {
			$webfonts['arbutus-slab']['src']  = 'native';
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

		// check Open Sans
		if ( ! isset( $stacks['sans']['open-sans'] ) ) {
			// add the array
			$stacks['sans']['open-sans'] = array(
				'label' => __( 'Open Sans', 'gppro' ),
				'css'   => '"Open Sans", Segoe, "Trebuchet MS", Verdana, sans-serif',
				'src'   => 'native',
				'size'  => '0',
			);
		}

		// check Arbutus Slab
		if ( ! isset( $stacks['serif']['arbutus-slab'] ) ) {
			// add the array
			$stacks['serif']['arbutus-slab'] = array(
				'label' => __( 'Arbutus Slab', 'gppro' ),
				'css'   => '"Arbutus Slab", Constantia, Georgia, serif',
				'src'   => 'native',
				'size'  => '0',
			);
		}

		// send it back
		return $stacks;
	}

	/**
	 * swap default values to match Wellness Pro
	 *
	 * @return string $defaults
	 */
	public function set_defaults( $defaults ) {
		$changes = array(
			// general body
			'body-color-back-thin'                          => '',  // Removed
            'body-color-back-main'                          => '#eeeeee',
			'body-color-text'                               => '#000000',
			'body-color-link'                               => '#e36344',
			'body-color-link-hov'                           => '#000000',
			'body-link-text-decoration'                     => 'underline',
			'body-link-text-decoration-hov'                 => 'none',

			'body-type-stack'                               => 'open-sans',
			'body-type-size'                                => '18',
            'body-type-size-mobile'                         => '16',
			'body-type-weight'                              => '400',
			'body-type-style'                               => 'normal',

			// site header
			'header-color-back'                             => '#ffffff',
            'header-color-opacity'                          => '100',
			'header-padding-top'                            => '20',
			'header-padding-bottom'                         => '20',
			'header-padding-left'                           => '0',
			'header-padding-right'                          => '0',

			// site title
			'site-title-text'                               => '#000000',
			'site-title-stack'                              => 'arbutus-slab',
			'site-title-size'                               => '42',
			'site-title-weight'                             => '400',
			'site-title-transform'                          => 'none',
			'site-title-align'                              => 'center',
			'site-title-style'                              => 'normal',
			'site-title-padding-top'                        => '6',
			'site-title-padding-bottom'                     => '6',
			'site-title-padding-left'                       => '0',
			'site-title-padding-right'                      => '0',

			// site description
			'site-desc-display'                             => 'block',
			'site-desc-text'                                => '#000000',
			'site-desc-stack'                               => 'open-sans',
			'site-desc-size'                                => '12',
			'site-desc-weight'                              => '700',
			'site-desc-transform'                           => 'uppercase',
			'site-desc-align'                               => 'center',
			'site-desc-style'                               => 'normal',

			// header navigation
			'header-nav-item-back'                          => '', // Removed
			'header-nav-item-back-hov'                      => '', // Removed
			'header-nav-item-link'                          => '#000000',
			'header-nav-item-link-hov'                      => '#e36344',
			'header-nav-active-link'                        => '#e36344',
			'header-nav-active-link-hov'                    => '#e36344',
			'header-nav-stack'                              => 'open-sans',
			'header-nav-size'                               => '14',
			'header-nav-weight'                             => 'bold',
			'header-nav-transform'                          => 'none',
			'header-nav-style'                              => 'normal',
			'header-nav-item-padding-top'                   => '33',
			'header-nav-item-padding-bottom'                => '33',
			'header-nav-item-padding-left'                  => '20',
			'header-nav-item-padding-right'                 => '20',

			// header widgets
			'header-widget-title-color'                     => '#000000',
			'header-widget-title-stack'                     => 'open-sans',
			'header-widget-title-size'                      => '14',
			/** For some reason this is displaying as 40 by default instead of 14--cf **/
			'header-widget-title-size-mobile'               => '12',
			'header-widget-title-weight'                    => '700',
			'header-widget-title-transform'                 => 'uppercase',
			'header-widget-title-align'                     => 'right',
			'header-widget-title-style'                     => 'normal',
			'header-widget-title-margin-bottom'             => '20',
			'header-widget-content-text'                    => '#000000',
			'header-widget-content-link'                    => '#e36344',
			'header-widget-content-link-hov'                => '#000000',
			'header-widget-content-stack'                   => 'open-sans',
			'header-widget-content-size'                    => '18',
			'header-widget-content-weight'                  => '400',
			'header-widget-content-align'                   => 'right',
			'header-widget-content-style'                   => 'normal',

			// below header menu
			'primary-nav-area-back'                         => '#ffffff',
			'primary-nav-border-top-color'                  => '#eeeeee',
			'primary-nav-border-top-style'                  => 'solid',
			'primary-nav-border-top-width'                  => '1',

			'primary-nav-top-stack'                         => 'open-sans',
			'primary-nav-top-size'                          => '14',
			'primary-nav-top-weight'                        => '700',
            'primary-nav-top-align'                         => 'left',
            'primary-nav-top-style'                         => 'normal',
            'primary-nav-top-transform'                     => 'none',

            'primary-nav-top-item-base-back'                => '', //Removed
			'primary-nav-top-item-base-back-hov'            => '', //Removed
			'primary-nav-top-item-base-link'                => '#000000',
			'primary-nav-top-item-base-link-hov'            => '#e36344',
            'primary-nav-highlight-color'                   => '#5da44f',
            'primary-nav-highlight-style'                   => 'solid',
            'primary-nav-highlight-width'                   => '3',

            'primary-nav-top-item-active-back'              => '#ffffff',
			'primary-nav-top-item-active-back-hov'          => '#ffffff',
			'primary-nav-top-item-active-link'              => '#e36344',
			'primary-nav-top-item-active-link-hov'          => '#e36344',

			'primary-nav-top-item-padding-top'              => '23',
			'primary-nav-top-item-padding-bottom'           => '23',
			'primary-nav-top-item-padding-left'             => '20',
			'primary-nav-top-item-padding-right'            => '20',

            'primary-responsive-menu-background-color'      => '#ffffff',
			'primary-responsive-icon-color'                 => '#000000',
            'primary-responsive-icon-size'                  => '20',
			'arrow-icon-color'                              => '#000000',
			'arrow-icon-color-hov'                          => '#000000',
			'arrow-icon-color-act'                          => '#e36344',

			'primary-nav-drop-stack'                        => 'open-sans',
			'primary-nav-drop-size'                         => '14',
			'primary-nav-drop-weight'                       => '700',
			'primary-nav-drop-transform'                    => 'none',
			'primary-nav-drop-align'                        => 'center',
			'primary-nav-drop-style'                        => 'normal',

			'primary-nav-drop-item-base-back'               => '#000000',
			'primary-nav-drop-item-base-back-hov'           => '#ffffff',
			'primary-nav-drop-item-base-link'               => '#ffffff',
			'primary-nav-drop-item-base-link-hov'           => '#5da44f',

			'primary-nav-drop-item-active-back'             => '#000000',
			'primary-nav-drop-item-active-back-hov'         => '#ffffff',
			'primary-nav-drop-item-active-link'             => '#ffffff',
			'primary-nav-drop-item-active-link-hov'         => '#5da44f',

			'primary-nav-drop-item-padding-top'             => '20',
			'primary-nav-drop-item-padding-bottom'          => '20',
			'primary-nav-drop-item-padding-left'            => '20',
			'primary-nav-drop-item-padding-right'           => '20',

            'primary-nav-drop-border-color'                 => '#efefef',
			'primary-nav-drop-border-style'                 => 'solid',
			'primary-nav-drop-border-width'                 => '1',

			// footer menu
			'secondary-nav-area-back'                       => '#ffffff',
			'secondary-nav-top-stack'                       => 'open-sans',
			'secondary-nav-top-size'                        => '16',
			'secondary-nav-top-weight'                      => '400',
			'secondary-nav-top-transform'                   => 'none',
			'secondary-nav-top-align'                       => 'center',
			'secondary-nav-top-style'                       => 'normal',
			'secondary-nav-text-decoration'                 => 'none',
			'secondary-nav-text-decoration-hov'             => 'underline',

            'secondary-nav-top-item-base-back'              => '', // Removed
			'secondary-nav-top-item-base-back-hov'          => '', // Removed
			'secondary-nav-top-item-base-link'              => '#000000',
			'secondary-nav-top-item-base-link-hov'          => '#5da44f',

			'secondary-nav-top-item-active-back'            => '', // Removed
			'secondary-nav-top-item-active-back-hov'        => '', // Removed
			'secondary-nav-top-item-active-link'            => '#e36344',
			'secondary-nav-top-item-active-link-hov'        => '#5da44f',

			'secondary-nav-top-item-padding-top'            => '', // Removed
			'secondary-nav-top-item-padding-bottom'         => '', // Removed
			'secondary-nav-top-item-padding-left'           => '', // Removed
			'secondary-nav-top-item-padding-right'          => '', // Removed

			'secondary-nav-drop-stack'                      => '', // Removed
			'secondary-nav-drop-size'                       => '', // Removed
			'secondary-nav-drop-weight'                     => '', // Removed
			'secondary-nav-drop-transform'                  => '', // Removed
			'secondary-nav-drop-align'                      => '', // Removed
			'secondary-nav-drop-style'                      => '', // Removed

            'secondary-nav-drop-item-base-back'             => '', // Removed
			'secondary-nav-drop-item-base-back-hov'         => '', // Removed
			'secondary-nav-drop-item-base-link'             => '', // Removed
			'secondary-nav-drop-item-base-link-hov'         => '', // Removed

            'secondary-nav-drop-item-active-back'           => '', // Removed
			'secondary-nav-drop-item-active-back-hov'       => '', // Removed
			'secondary-nav-drop-item-active-link'           => '', // Removed
			'secondary-nav-drop-item-active-link-hov'       => '', // Removed

            'secondary-nav-drop-item-padding-top'           => '', // Removed
			'secondary-nav-drop-item-padding-bottom'        => '', // Removed
			'secondary-nav-drop-item-padding-left'          => '', // Removed
			'secondary-nav-drop-item-padding-right'         => '', // Removed

            'secondary-nav-drop-border-color'               => '', // Removed
			'secondary-nav-drop-border-style'               => '', // Removed
			'secondary-nav-drop-border-width'               => '', // Removed

			// sticky message
			'sticky-message-back'                           => '#ffffff',
			'sticky-message-text'                           => '#000000',
			'sticky-message-link'                           => '#000000',
			'sticky-message-link-hov'                       => '#e36344',
			'sticky-message-lk-border-color'                => '#e36344',
			'sticky-message-lk-border-color-hov'            => '#e36344',
			'sticky-message-lk-border-style'                => 'solid',
			'sticky-message-lk-border-width'                => '1',

			'sticky-message-stack'                          => 'open-sans',
			'sticky-message-size'                           => '15',
			'sticky-message-weight'                         => '700',
			'sticky-message-style'                          => 'normal',

			'sticky-message-padding-top'                    => '15',
			'sticky-message-padding-bottom'                 => '15',
			'sticky-message-padding-left'                   => '20',
			'sticky-message-padding-right'                  => '20',

			// before footer widget
			'before-footer-back'                            => '#ffffff',

			'before-footer-padding-top'                     => '5',
			'before-footer-padding-bottom'                  => '0',
			'before-footer-padding-left'                    => '5',
			'before-footer-padding-right'                   => '5',

			'before-footer-title-text'                      => '#000',
			'before-footer-title-stack'                     => 'open-sans',
			'before-footer-title-size'                      => '14',
			'before-footer-title-weight'                    => '700',
			'before-footer-title-transform'                 => 'uppercase',
			'before-footer-title-align'                     => 'left',
			'before-footer-title-style'                     => 'normal',
			'before-footer-title-margin-bottom'             => '20',

			'before-footer-content-text'                    => '#000',
			'before-footer-content-link'                    => '#e36344',
			'before-footer-content-link-hov'                => '#e36344',
			'before-footer-content-text-decoration'         => 'underline',
			'before-footer-content-stack'                   => 'open-sans',
			'before-footer-content-size'                    => '18',
			'before-footer-content-weight'                  => '400',
			'before-footer-content-align'                   => 'left',
			'before-footer-content-style'                   => 'normal',

			// front page section one
			'front-page-1-title-stack'                      => 'arbutus-slab',
			'front-page-1-title-color'                      => '#e36344',
			'front-page-1-title-size'                       => '44',
            'front-page-1-title-size-mobile'                => '38',
			'front-page-1-title-weight'                     => '400',
			'front-page-1-title-transform'                  => 'none',
			'front-page-1-title-align'                      => 'center',
			'front-page-1-title-style'                      => 'normal',
			'front-page-1-title-margin-bottom'              => '20',

			'front-page-1-text-stack'                       => 'open-sans',
			'front-page-1-text-color'                       => '#000000',
			'front-page-1-text-size'                        => '18',
			'front-page-1-text-align'                       => 'center',
			'front-page-1-text-style'                       => 'normal',

            'front-page-1-btn-back'                         => '#000000',
            'front-page-1-btn-back-hov'                     => '#5da44f',
            'front-page-1-btn-stack'                        => 'open-sans',
            'front-page-1-btn-weight'                       => '700',
            'front-page-1-btn-transform'                    => 'uppercase',
            'front-page-1-btn-color'                        => '#ffffff',
            'front-page-1-btn-color-hover'                  => '#ffffff',
			'front-page-1-btn-size'                         => '14',

            // front page section two
			'front-page-2-title-stack'                      => 'arbutus-slab',
			'front-page-2-title-color'                      => '#000000',
            'front-page-2-title-link-color'                 => '#000000',
            'front-page-2-title-link-color-hov'             => '#e36344',
			'front-page-2-title-size'                       => '20',
            'front-page-2-title-size-mobile'                => '18',
			'front-page-2-title-weight'                     => '400',
			'front-page-2-title-transform'                  => 'none',
			'front-page-2-title-align'                      => 'center',
			'front-page-2-title-style'                      => 'none',
			'front-page-2-title-margin-bottom'              => '10',

            'front-page-2-meta-stack'                       => 'open-sans',
            'front-page-2-meta-color'                       => '#5a636c',
            'front-page-2-meta-size'                        => '14',
            'front-page-2-meta-weight'                      => '400',
            'front-page-2-meta-transform'                   => 'none',
            'front-page-2-meta-style'                       => 'italic',

            'front-page-2-meta-author-color'                => '#5a636c',
            'front-page-2-meta-author-color-hov'            => '#e36344',
            'front-page-2-author-stack'                     => 'open-sans',
			'front-page-2-title-size'                       => '14',
            'front-page-2-meta-author-weight'               => '700',
			'front-page-2-meta-author-transform'            => 'uppercase',
            'front-page-2-meta-author-style'                => 'none',

            // front page section three
			'front-page-3-title-stack'                      => 'arbutus-slab',
			'front-page-3-title-color'                      => '#e36344',
			'front-page-3-title-size'                       => '24',
            'front-page-3-title-size-mobile'                => '22',
			'front-page-3-title-weight'                     => '400',
			'front-page-3-title-transform'                  => 'none',
			'front-page-3-title-align'                      => 'center',
			'front-page-3-title-style'                      => 'normal',
			'front-page-3-title-margin-bottom'              => '10',

			'front-page-3-text-stack'                       => 'open-sans',
			'front-page-3-text-color'                       => '#000000',
			'front-page-3-text-size'                        => '18',
			'front-page-3-text-align'                       => 'center',
			'front-page-3-text-style'                       => 'normal',

            'front-page-3-btn-back'                         => '#000000',
            'front-page-3-btn-back-hov'                     => '#5da44f',
            'front-page-3-btn-stack'                        => 'open-sans',
            'front-page-3-btn-weight'                       => '700',
            'front-page-3-btn-transform'                    => 'uppercase',
            'front-page-3-btn-color'                        => '#ffffff',
            'front-page-3-btn-color-hover'                  => '#ffffff',
			'front-page-3-btn-size'                         => '14',

            // front page section four
			'front-page-4-featured-back'                    => '#5da44f',
            'front-page-4-featured-stack'                   => 'open-sans',
            'front-page-4-featured-color'                   => '#ffffff',
            'front-page-4-featured-weight'                  => '700',
            'front-page-4-featured-transform'               => 'uppercase',
            'front-page-4-featured-style'                   => 'normal',

            'front-page-4-title-stack'                      => 'arbutus-slab',
			'front-page-4-title-color'                      => '#000000',
            'front-page-4-title-link-color'                 => '#000000',
            'front-page-4-title-link-color-hov'             => '#e36344',
			'front-page-4-title-size'                       => '20',
            'front-page-4-title-size-mobile'                => '18',
			'front-page-4-title-weight'                     => '400',
			'front-page-4-title-transform'                  => 'none',
			'front-page-4-title-align'                      => 'center',
			'front-page-4-title-style'                      => 'none',
			'front-page-4-title-margin-bottom'              => '10',

            'front-page-4-meta-stack'                       => 'open-sans',
            'front-page-4-meta-color'                       => '#5a636c',
            'front-page-4-meta-size'                        => '14',
            'front-page-4-meta-weight'                      => '400',
            'front-page-4-meta-transform'                   => 'none',
            'front-page-4-meta-style'                       => 'italic',
            'front-page-4-meta-margin-bottom'               => '28',
            'front-page-4-meta-author-color'                => '#5a636c',
            'front-page-4-meta-author-color-hov'            => '#e36344',
            'front-page-4-meta-author-weight'               => '700',
			'front-page-4-meta-author-transform'            => 'uppercase',
            'front-page-4-meta-author-style'                => 'none',

            'front-page-4-btn-back'                         => '#000000',
            'front-page-4-btn-back-hov'                     => '#5da44f',
            'front-page-4-btn-stack'                        => 'open-sans',
            'front-page-4-btn-weight'                       => '700',
            'front-page-4-btn-transform'                    => 'uppercase',
            'front-page-4-btn-color'                        => '#ffffff',
            'front-page-4-btn-color-hover'                  => '#ffffff',
			'front-page-4-btn-size'                         => '14',

            // front page section five
			'front-page-5-title-stack'                      => 'arbutus-slab',
			'front-page-5-title-color'                      => '#e36344',
			'front-page-5-title-size'                       => '24',
            'front-page-5-title-size-mobile'                => '22',
			'front-page-5-title-weight'                     => '400',
			'front-page-5-title-transform'                  => 'none',
			'front-page-5-title-align'                      => 'center',
			'front-page-5-title-style'                      => 'normal',
			'front-page-5-title-margin-bottom'              => '10',

			'front-page-5-text-stack'                       => 'open-sans',
			'front-page-5-text-color'                       => '#000000',
			'front-page-5-text-size'                        => '18',
			'front-page-5-text-align'                       => 'center',
			'front-page-5-text-style'                       => 'normal',

            'front-page-5-btn-back'                         => '#000000',
            'front-page-5-btn-back-hov'                     => '#5da44f',
            'front-page-5-btn-stack'                        => 'open-sans',
            'front-page-5-btn-weight'                       => '700',
            'front-page-5-btn-transform'                    => 'uppercase',
            'front-page-5-btn-color'                        => '#ffffff',
            'front-page-5-btn-color-hover'                  => '#ffffff',
			'front-page-5-btn-size'                         => '14',

            // front page section six
            'front-page-6-title-stack'                      => 'open-sans',
			'front-page-6-title-color'                      => '#000000',
			'front-page-6-title-size'                       => '14',
			'front-page-6-title-weight'                     => '700',
			'front-page-6-title-transform'                  => 'uppercase',
			'front-page-6-title-align'                      => 'left',
			'front-page-6-title-style'                      => 'normal',
			'front-page-6-title-margin-bottom'              => '20',

			'front-page-6-text-stack'                       => 'open-sans',
			'front-page-6-text-color'                       => '#000000',
			'front-page-6-text-size'                        => '18',
			'front-page-6-text-align'                       => 'left',
			'front-page-6-text-style'                       => 'normal',

            'front-page-6-quote-stack'                      => 'arbutus-slab',
			'front-page-6-quote-color'                      => '#000000',
			'front-page-6-quote-size'                       => '18',
			'front-page-6-quote-weight'                     => '400',
			'front-page-6-quote-transform'                  => 'none',
			'front-page-6-quote-align'                      => 'left',
			'front-page-6-quote-style'                      => 'normal',
            'front-page-6-quote-margin-bottom'              => '28',

            'front-page-6-cite-stack'                        => 'arbutus-slab',
			'front-page-6-cite-color'                        => '#000000',
			'front-page-6-cite-size'                         => '18',
			'front-page-6-cite-weight'                       => '400',
			'front-page-6-cite-transform'                    => 'none',
			'front-page-6-cite-align'                        => 'left',
			'front-page-6-cite-style'                        => 'normal',
            'front-page-6-cite-margin-bottom'                => '28',

			// post content
			'site-inner-padding-top'                        => '40',

			'main-entry-back'                               => '#ffffff',
			'main-entry-border-radius'                      => '',  // Removed

			'main-entry-padding-top'                        => '70',
			'main-entry-padding-bottom'                     => '70',
			'main-entry-padding-left'                       => '80',
			'main-entry-padding-right'                      => '80',

			'main-entry-margin-top'                         => '0',
			'main-entry-margin-bottom'                      => '40',
			'main-entry-margin-left'                        => '0',
			'main-entry-margin-right'                       => '0',

			'post-title-text'                               => '#000000',
			'post-title-link'                               => '#000000',
			'post-title-link-hov'                           => '#e36344',
			'post-title-stack'                              => 'arbutus-slab',
			'post-title-size'                               => '36',
			'post-title-weight'                             => '400',
			'post-title-transform'                          => 'none',
			'post-title-align'                              => 'center',
			'post-title-style'                              => 'normal',
			'post-title-margin-bottom'                      => '10',

			// entry border
			'entry-header-border-color'                     => '#000000',
			'entry-header-border-style'                     => 'solid',
			'entry-header-border-width'                     => '1',
			'entry-header-border-length'                    => '80',

			// entry meta
			'post-header-meta-text-color'                   => '#5a636c',
			'post-header-meta-date-color'                   => '#5a636c',
			'post-header-meta-author-link'                  => '#5a636c',
			'post-header-meta-author-link-hov'              => '#000000',
			'post-header-meta-comment-link'                 => '#e36344',
			'post-header-meta-comment-link-hov'             => '#000000',

			'post-header-meta-stack'                        => 'open-sans',
			'post-header-meta-size'                         => '14',
			'post-header-meta-weight'                       => '400',
			'post-header-meta-transform'                    => 'none',
			'post-header-meta-align'                        => 'left',
			'post-header-meta-style'                        => 'italic',
			'post-meta-comment-lk-txt-dec'                  => 'underline',
			'post-meta-comment-lk-txt-dec-hov'              =>'none',

			// post text
			'post-entry-text'                               => '#000000',
			'post-entry-link'                               => '#000000',
			'post-entry-link-hov'                           => '#e36344',

			'post-entry-stack'                              => 'open-sans',
			'post-entry-size'                               => '14',
			'post-entry-weight'                             => '400',
			'post-entry-style'                              => 'normal',

			'post-entry-list-ol'                            => 'decimal',
			'post-entry-list-ul'                            => 'disc',

			// entry-footer
			'post-footer-category-text'                     => '#5a636c',
			'post-footer-category-link'                     => '#e36344',
			'post-footer-category-link-hov'                 => '#000000',
			'post-footer-tag-text'                          => '#5a636c',
			'post-footer-tag-link'                          => '#e36344',
			'post-footer-tag-link-hov'                      => '#000000',
			'post-footer-comment-lk-txt-dec'                => 'underline',
			'post-footer-comment-lk-txt-dec-hov'            => 'none',

			'post-footer-stack'                             => 'open-sans',
			'post-footer-size'                              => '14',
			'post-footer-weight'                            => '400',
			'post-footer-transform'                         => 'none',
			'post-footer-align'                             => 'left',
			'post-footer-style'                             => 'italic',

			'post-footer-divider-color'                     => '#eeeeee',
			'post-footer-divider-style'                     => 'solid',
			'post-footer-divider-width'                     => '1',

			'archive-title-text'                            => '#ffffff',

			'archive-description-padding-top'               => '80',
			'archive-description-padding-bottom'            => '80',
			'archive-description-padding-left'              => '80',
			'archive-description-padding-right'             => '80',
			'archive-description-margin-bottom'             => '60',

			// archive description
			'archive-title-text'                            => '#000',
			'archive-title-stack'                           => 'open-sans',
			'archive-title-size'                            => '14',
			'archive-title-weight'                          => '700',
			'archive-title-transform'                       => 'uppercase',
			'archive-title-align'                           => 'center',
			'archive-title-style'                           => 'normal',

			'archive-text-text'                             => '#000',
			'archive-text-stack'                            => 'open-sans',
			'archive-text-size'                             => '18',
			'archive-text-weight'                           => '400',
			'archive-text-transform'                        => 'none',
			'archive-text-align'                            => 'center',
			'archive-text-style'                            => 'normal',

			// read more link
			'extras-read-more-link'                         => '#e36344',
			'extras-read-more-link-hov'                     => '#000000',
			'extras-read-more-stack'                        => 'open-sans',
			'extras-read-more-size'                         => '18',
			'extras-read-more-weight'                       => '400',
			'extras-read-more-transform'                    => 'none',
			'extras-read-more-style'                        => 'normal',

			// breadcrumbs
			'extras-breadcrumb-back'                        => '#ffffff',
			'extras-breadcrumb-text'                        => '#000000',
			'extras-breadcrumb-link'                        => '#e36344',
			'extras-breadcrumb-link-hov'                    => '#000000',
			'extras-breadcrumb-stack'                       => 'open-sans',
			'extras-breadcrumb-size'                        => '16',
			'extras-breadcrumb-weight'                      => '400',
			'extras-breadcrumb-transform'                   => 'none',
			'extras-breadcrumb-style'                       => 'normal',

			// pagination typography (apply to both )
			'extras-pagination-stack'                       => 'open-sans',
			'extras-pagination-size'                        => '16',
			'extras-pagination-weight'                      => '700',
			'extras-pagination-transform'                   => 'none',
			'extras-pagination-style'                       => 'normal',

			// pagination text
			'extras-pagination-back'                        => '#ffffff',
			'extras-pagination-back-hov'                    => '#e36344',
			'extras-pagination-text-link'                   => '#000000',
			'extras-pagination-text-link-hov'               => '#ffffff',

			// pagination numeric
			'extras-pagination-numeric-back'                => '#ffffff',
			'extras-pagination-numeric-back-hov'            => '#e36344',
			'extras-pagination-numeric-active-back'         => '#e36344',
			'extras-pagination-numeric-active-back-hov'     => '#e36344',
			'extras-pagination-numeric-border-radius'       => '',// Removed

			'extras-pagination-numeric-padding-top'         => '8',
			'extras-pagination-numeric-padding-bottom'      => '8',
			'extras-pagination-numeric-padding-left'        => '12',
			'extras-pagination-numeric-padding-right'       => '12',

			'extras-pagination-numeric-link'                => '#000000',
			'extras-pagination-numeric-link-hov'            => '#ffffff',
			'extras-pagination-numeric-active-link'         => '#ffffff',
			'extras-pagination-numeric-active-link-hov'     => '#ffffff',

			// after entry widget area
			'after-entry-widget-area-back'                  => '#ffffff',
			'after-entry-widget-area-border-radius'         => '', // Removed

			'after-entry-widget-area-padding-top'           => '60',
			'after-entry-widget-area-padding-bottom'        => '60',
			'after-entry-widget-area-padding-left'          => '60',
			'after-entry-widget-area-padding-right'         => '60',

			'after-entry-widget-area-margin-top'            => '0',
			'after-entry-widget-area-margin-bottom'         => '40',
			'after-entry-widget-area-margin-left'           => '0',
			'after-entry-widget-area-margin-right'          => '0',

			'after-entry-widget-back'                       => '', // Removed
			'after-entry-widget-border-radius'              => '', // Removed

			'after-entry-widget-padding-top'                => '0',
			'after-entry-widget-padding-bottom'             => '0',
			'after-entry-widget-padding-left'               => '0',
			'after-entry-widget-padding-right'              => '0',

			'after-entry-widget-margin-top'                 => '0',
			'after-entry-widget-margin-bottom'              => '0',
			'after-entry-widget-margin-left'                => '0',
			'after-entry-widget-margin-right'               => '0',

			'after-entry-widget-title-text'                 => '#000000',
			'after-entry-widget-title-stack'                => 'open-sans',
			'after-entry-widget-title-size'                 => '14',
			'after-entry-widget-title-weight'               => '700',
			'after-entry-widget-title-transform'            => 'uppercase',
			'after-entry-widget-title-align'                => 'left',
			'after-entry-widget-title-style'                => 'normal',
			'after-entry-widget-title-margin-bottom'        => '20',

			'after-entry-widget-content-text'               => '#000000',
			'after-entry-widget-content-link'               => '#e36344',
			'after-entry-widget-content-link-hov'           => '#000000',
			'after-entry-widget-content-stack'              => 'open-sans',
			'after-entry-widget-content-size'               => '18',
			'after-entry-widget-content-weight'             => '400',
			'after-entry-widget-content-align'              => 'left',
			'after-entry-widget-content-style'              => 'normal',

            // author box
			'extras-author-box-back'                        => '#ffffff',

			'extras-author-box-padding-top'                 => '40',
			'extras-author-box-padding-bottom'              => '40',
			'extras-author-box-padding-left'                => '40',
			'extras-author-box-padding-right'               => '40',

			'extras-author-box-margin-top'                  => '0',
			'extras-author-box-margin-bottom'               => '40',
			'extras-author-box-margin-left'                 => '0',
			'extras-author-box-margin-right'                => '0',

			'extras-author-box-name-text'                   => '#000000',
			'extras-author-box-name-stack'                  => 'open-sans',
			'extras-author-box-name-size'                   => '16',
			'extras-author-box-name-weight'                 => '400',
			'extras-author-box-name-align'                  => 'left',
			'extras-author-box-name-transform'              => 'none',
			'extras-author-box-name-style'                  => 'normal',

			'extras-author-box-bio-text'                    => '#000000',
			'extras-author-box-bio-link'                    => '#e36344',
			'extras-author-box-bio-link-hov'                => '#000000',
			'extras-author-box-bio-stack'                   => 'open-sans',
			'extras-author-box-bio-size'                    => '16',
			'extras-author-box-bio-weight'                  => '400',
			'extras-author-box-bio-style'                   => 'normal',

			// comment list
			'comment-list-back'                             => '#ffffff',

			'comment-list-padding-top'                      => '40',
			'comment-list-padding-bottom'                   => '40',
			'comment-list-padding-left'                     => '40',
			'comment-list-padding-right'                    => '40',

			'comment-list-margin-top'                       => '0',
			'comment-list-margin-bottom'                    => '40',
			'comment-list-margin-left'                      => '0',
			'comment-list-margin-right'                     => '0',

			// comment list title
			'comment-list-title-text'                       => '#000000',
			'comment-list-title-stack'                      => 'open-sans',
			'comment-list-title-size'                       => '24',
			'comment-list-title-weight'                     => '400',
			'comment-list-title-transform'                  => 'none',
			'comment-list-title-align'                      => 'left',
			'comment-list-title-style'                      => 'normal',
			'comment-list-title-margin-bottom'              => '10',

			// single comments
			'single-comment-padding-top'                    => '32',
			'single-comment-padding-bottom'                 => '32',
			'single-comment-padding-left'                   => '32',
			'single-comment-padding-right'                  => '32',

			'single-comment-margin-top'                     => '24',
			'single-comment-margin-bottom'                  => '0',
			'single-comment-margin-left'                    => '0',
			'single-comment-margin-right'                   => '0',

			// color setup for standard and author comments
			'single-comment-standard-back'                  => '',  //Removed
			'single-comment-standard-border-color'          => '',  //Removed
			'single-comment-standard-border-style'          => '',  //Removed
			'single-comment-standard-border-width'          => '',  //Removed
			'single-comment-author-back'                    => '',  //Removed
			'single-comment-author-border-color'            => '',  //Removed
			'single-comment-author-border-style'            => '',  //Removed
			'single-comment-author-border-width'            => '',  //Removed

			// comment author
			'comment-element-name-text'                     => '#000000',
			'comment-element-name-link'                     => '#e36344',
			'comment-element-name-link-hov'                 => '#000000',
			'comment-element-name-text-decoration'          => 'underline',
			'comment-element-name-decoration-hov'           => 'none',

			'comment-element-name-stack'                    => 'open-sans',
			'comment-element-name-size'                     => '16',
			'comment-element-name-weight'                   => '400',
			'comment-element-name-style'                    => 'normal',

			// comment date
			'comment-element-date-link'                     => '#e36344',
			'comment-element-date-link-hov'                 => '#000000',
			'comment-element-date-text-decoration'          => 'underline',
			'comment-element-date-decoration-hov'           => 'none',

			'comment-element-date-stack'                    => 'open-sans',
			'comment-element-date-size'                     => '16',
			'comment-element-date-weight'                   => '400',
			'comment-element-date-style'                    => 'normal',

			// comment body
			'comment-element-body-text'                     => '#000000',
			'comment-element-body-link'                     => '#666666',
			'comment-element-body-link-hov'                 => '#e36344',
			'comment-element-body-text-decoration'          => 'underline',
			'comment-element-body-decoration-hov'           => 'none',

			'comment-element-body-stack'                    => 'open-sans',
			'comment-element-body-size'                     => '18',
			'comment-element-body-weight'                   => '400',
			'comment-element-body-style'                    => 'normal',

			// comment reply
			'comment-element-reply-link'                    => '#e36344',
			'comment-element-reply-link-hov'                => '#000000',
			'comment-element-reply-text-decoration'         => 'underline',
			'comment-element-reply-decoration-hov'          => 'none',

			'comment-element-reply-stack'                   => 'open-sans',
			'comment-element-reply-size'                    => '18',
			'comment-element-reply-weight'                  => '400',
			'comment-element-reply-align'                   => 'left',
			'comment-element-reply-style'                   => 'normal',

			// trackback list
			'trackback-list-back'                           => '#ffffff',
			'trackback-list-padding-top'                    => '60',
			'trackback-list-padding-bottom'                 => '32',
			'trackback-list-padding-left'                   => '60',
			'trackback-list-padding-right'                  => '60',

			'trackback-list-margin-top'                     => '0',
			'trackback-list-margin-bottom'                  => '40',
			'trackback-list-margin-left'                    => '0',
			'trackback-list-margin-right'                   => '0',

			// trackback list title
			'trackback-list-title-text'                     => '#000000',
			'trackback-list-title-stack'                    => 'arbutus-slab',
			'trackback-list-title-size'                     => '24',
			'trackback-list-title-weight'                   => '400',
			'trackback-list-title-transform'                => 'none',
			'trackback-list-title-align'                    => 'left',
			'trackback-list-title-style'                    => 'normal',
			'trackback-list-title-margin-bottom'            => '10',

			// trackback name
			'trackback-element-name-text'                   => '#000000',
			'trackback-element-name-link'                   => '#e36344',
			'trackback-element-name-link-hov'               => '#000000',
			'trackback-element-name-stack'                  => 'open-sans',
			'trackback-element-name-size'                   => '16',
			'trackback-element-name-weight'                 => '400',
			'trackback-element-name-style'                  => 'normal',

			// trackback date
			'trackback-element-date-link'                   => '#e36344',
			'trackback-element-date-link-hov'               => '#000000',
			'trackback-element-date-stack'                  => 'open-sans',
			'trackback-element-date-size'                   => '16',
			'trackback-element-date-weight'                 => '400',
			'trackback-element-date-style'                  => 'normal',

			// trackback body
			'trackback-element-body-text'                   => '#000000',
			'trackback-element-body-stack'                  => 'open-sans',
			'trackback-element-body-size'                   => '16',
			'trackback-element-body-weight'                 => '400',
			'trackback-element-body-style'                  => 'normal',

			// comment form
			'comment-reply-back'                            => '#ffffff',

			'comment-reply-padding-top'                     => '40',
			'comment-reply-padding-bottom'                  => '16',
			'comment-reply-padding-left'                    => '40',
			'comment-reply-padding-right'                   => '40',

			'comment-reply-margin-top'                      => '0',
			'comment-reply-margin-bottom'                   => '40',
			'comment-reply-margin-left'                     => '0',
			'comment-reply-margin-right'                    => '0',

			// comment form title
			'comment-reply-title-text'                      => '#000000',
			'comment-reply-title-stack'                     => 'open-sans',
			'comment-reply-title-size'                      => '24',
			'comment-reply-title-weight'                    => '400',
			'comment-reply-title-transform'                 => 'none',
			'comment-reply-title-align'                     => 'left',
			'comment-reply-title-style'                     => 'normal',
			'comment-reply-title-margin-bottom'             => '10',

			// comment form notes
			'comment-reply-notes-text'                      => '#000000',
			'comment-reply-notes-link'                      => '#e36344',
			'comment-reply-notes-link-hov'                  => '#000000',
			'comment-reply-notes-stack'                     => 'open-sans',
			'comment-reply-notes-size'                      => '18',
			'comment-reply-notes-weight'                    => '400',
			'comment-reply-notes-style'                     => 'normal',

			// comment allowed tags
			'comment-reply-atags-base-back'                 => '', //Removed
			'comment-reply-atags-base-text'                 => '', //Removed
			'comment-reply-atags-base-stack'                => '', //Removed
			'comment-reply-atags-base-size'                 => '', //Removed
			'comment-reply-atags-base-weight'               => '', //Removed
			'comment-reply-atags-base-style'                => '', //Removed

			// comment allowed tags code
			'comment-reply-atags-code-text'                 => '', //Removed
			'comment-reply-atags-code-stack'                => '', //Removed
			'comment-reply-atags-code-size'                 => '', //Removed
			'comment-reply-atags-code-weight'               => '', //Removed

			// comment fields labels
			'comment-reply-fields-label-text'               => '#000000',
			'comment-reply-fields-label-stack'              => 'open-sans',
			'comment-reply-fields-label-size'               => '18',
			'comment-reply-fields-label-weight'             => '400',
			'comment-reply-fields-label-transform'          => 'none',
			'comment-reply-fields-label-align'              => 'left',
			'comment-reply-fields-label-style'              => 'normal',

			// comment fields inputs
			'comment-reply-fields-input-field-width'        => '50',
			'comment-reply-fields-input-border-style'       => 'solid',
			'comment-reply-fields-input-border-width'       => '1',
			'comment-reply-fields-input-border-radius'      => ' ', //Removed
			'comment-reply-fields-input-padding'            => '16',
			'comment-reply-fields-input-margin-bottom'      => '0',

			'comment-reply-fields-input-base-back'          => '#ffffff',
			'comment-reply-fields-input-focus-back'         => '#ffffff',
			'comment-reply-fields-input-base-border-color'  => '#dddddd',
			'comment-reply-fields-input-focus-border-color' => '#999999',
			'comment-reply-fields-input-text'               => '#000000',

			'comment-reply-fields-input-stack'              => 'open-sans',
			'comment-reply-fields-input-size'               => '18',
			'comment-reply-fields-input-weight'             => '400',
			'comment-reply-fields-input-style'              => 'normal',

			// comment button
			'comment-submit-button-back'                    => '#000000',
			'comment-submit-button-back-hov'                => '#e36344',
			'comment-submit-button-text'                    => '#ffffff',
			'comment-submit-button-text-hov'                => '#ffffff',

			'comment-submit-button-stack'                   => 'open-sans',
			'comment-submit-button-size'                    => '16',
			'comment-submit-button-weight'                  => '400',
			'comment-submit-button-transform'               => 'uppercase',
			'comment-submit-button-style'                   => 'normal',
			'comment-submit-button-padding-top'             => '16',
			'comment-submit-button-padding-bottom'          => '16',
			'comment-submit-button-padding-left'            => '24',
			'comment-submit-button-padding-right'           => '24',
			'comment-submit-button-border-radius'           => ' ', //Removed

            // sidebar widgets
			'sidebar-widget-back'                           => '#ffffff',
			'sidebar-widget-border-radius'                  => ' ', //Removed

			'sidebar-widget-padding-top'                    => '40',
			'sidebar-widget-padding-bottom'                 => '40',
			'sidebar-widget-padding-left'                   => '40',
			'sidebar-widget-padding-right'                  => '40',

			'sidebar-widget-margin-top'                     => '0',
			'sidebar-widget-margin-bottom'                  => '40',
			'sidebar-widget-margin-left'                    => '0',
			'sidebar-widget-margin-right'                   => '0',

			// sidebar widget titles
			'sidebar-widget-title-text'                     => '#000000',
			'sidebar-widget-title-stack'                    => 'open-sans',
			'sidebar-widget-title-size'                     => '14',
			'sidebar-widget-title-weight'                   => '700',
			'sidebar-widget-title-transform'                => 'none',
			'sidebar-widget-title-align'                    => 'left',
			'sidebar-widget-title-style'                    => 'normal',
			'sidebar-widget-title-margin-bottom'            => '20',

			// sidebar widget content
			'sidebar-widget-content-text'                   => '#000000',
			'sidebar-widget-content-link'                   => '#e36344',
			'sidebar-widget-content-link-hov'               => '#000000',
			'sidebar-widget-content-stack'                  => 'open-sans',
			'sidebar-widget-content-size'                   => '16',
			'sidebar-widget-content-weight'                 => '400',
			'sidebar-widget-content-align'                  => 'left',
			'sidebar-widget-content-style'                  => 'normal',

			// footer widget row
			'footer-widget-row-back'                        => '#ffffff',

			'footer-widget-row-padding-top'                 => '0',
			'footer-widget-row-padding-bottom'              => '0',
			'footer-widget-row-padding-left'                => '0',
			'footer-widget-row-padding-right'               => '0',

			// footer widget singles
			'footer-widget-single-back'                     => '#ffffff',

			'footer-widget-single-margin-bottom'            => '0',
            'footer-widget-single-border-radius'            => ' ', //Removed

			'footer-widget-single-padding-top'              => '0',
			'footer-widget-single-padding-bottom'           => '0',
			'footer-widget-single-padding-left'             => '0',
			'footer-widget-single-padding-right'            => '0',

			// footer widget title
			'footer-widget-title-text'                      => '#000000',
			'footer-widget-title-stack'                     => 'open-sans',
			'footer-widget-title-size'                      => '18',
			'footer-widget-title-weight'                    => '700',
			'footer-widget-title-transform'                 => 'none',
			'footer-widget-title-align'                     => 'left',
			'footer-widget-title-style'                     => 'normal',
			'footer-widget-title-margin-bottom'             => '20',

			// footer widget content
			'footer-widget-content-text'                    => '#000000',
			'footer-widget-content-link'                    => '#000000',
			'footer-widget-content-link-hov'                => '#5da44f',
			'footer-widget-text-decoration'                 => 'none',
			'footer-widget-text-decoration-hov'             => 'underline',
			'footer-widget-content-stack'                   => 'open-sans',
			'footer-widget-content-size'                    => '18',
			'footer-widget-content-weight'                  => '400',
			'footer-widget-content-align'                   => 'left',
			'footer-widget-content-style'                   => 'normal',

			// bottom footer
			'footer-main-back'                              => '#ffffff',
			'footer-main-border-top-color'                  => '#eeeeee',
			'footer-main-border-top-style'                  => 'solid',
			'footer-main-border-top-width'                  => '1',

			'footer-main-padding-top'                       => '60',
			'footer-main-padding-bottom'                    => '60',
			'footer-main-padding-left'                      => '0',
			'footer-main-padding-right'                     => '0',

			'footer-main-content-text'                      => '#000000',
			'footer-main-content-link'                      => '#000000',
			'footer-main-content-link-hov'                  => '#5da44f',
			'footer-main-content-stack'                     => 'open-sans',
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

	/**
	 * add and filter options for entry content
	 *
	 * @return array|string $sections
	 */
	public function entry_content_defaults( $defaults ) {

		// entry content defaults
		$changes = array(

			'entry-content-h1-weight'                      =>'400',
			'entry-content-h2-weight'                      =>'400',
			'entry-content-h3-weight'                      =>'400',
			'entry-content-h4-weight'                      =>'400',
			'entry-content-h5-weight'                      =>'400',
			'entry-content-h6-weight'                      =>'400',
			'entry-content-h1-link-dec'                    => 'underline',
			'entry-content-h1-link-dec-hov'                => 'none',
			'entry-content-h2-link-dec'                    => 'underline',
			'entry-content-h2-link-dec-hov'                => 'none',
			'entry-content-h3-link-dec'                    => 'underline',
			'entry-content-h3-link-dec-hov'                => 'none',
			'entry-content-h4-link-dec'                    => 'underline',
			'entry-content-h4-link-dec-hov'                => 'none',
			'entry-content-h5-link-dec'                    => 'underline',
			'entry-content-h5-link-dec-hov'                => 'none',
			'entry-content-h6-link-dec'                    => 'underline',
			'entry-content-h6-link-dec-hov'                => 'none',
			'entry-content-a-dec'                          => 'underline',
			'entry-content-a-dec-hov'                      => 'underline',


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
			'enews-widget-back'                             => '', // Removed
			'enews-widget-title-color'                      => '#000000',
			'enews-widget-text-color'                       => '#000000',

			// General Typography
			'enews-title-gen-stack'                         => 'open-sans',
			'enews-title-gen-size'                          => '14',
			'enews-title-gen-weight'                        => '700',
			'enews-title-gen-transform'                     => 'uppercase',
			'enews-title-gen-text-margin-bottom'            => '20',

			'enews-widget-gen-stack'                        => 'open-sans',
			'enews-widget-gen-size'                         => '18',
			'enews-widget-gen-weight'                       => '400',
			'enews-widget-gen-transform'                    => 'none',
			'enews-widget-gen-text-margin-bottom'           => '28',

			// Field Inputs
			'enews-widget-field-input-back'                 => '#ffffff',
			'enews-widget-field-input-text-color'           => '#000000',
			'enews-widget-field-input-stack'                => 'open-sans',
			'enews-widget-field-input-size'                 => '16',
			'enews-widget-field-input-weight'               => '400',
			'enews-widget-field-input-transform'            => 'none',
			'enews-widget-field-input-border-color'         => '#dddddd',
			'enews-widget-field-input-border-type'          => 'solid',
			'enews-widget-field-input-border-width'         => '1',
			'enews-widget-field-input-border-radius'        => '0',
			'enews-widget-field-input-border-color-focus'   => '#999999',
			'enews-widget-field-input-border-type-focus'    => 'solid',
			'enews-widget-field-input-border-width-focus'   => '1',
			'enews-widget-field-input-pad-top'              => '16',
			'enews-widget-field-input-pad-bottom'           => '16',
			'enews-widget-field-input-pad-left'             => '16',
			'enews-widget-field-input-pad-right'            => '16',
			'enews-widget-field-input-margin-bottom'        => '20',
			'enews-widget-field-input-box-shadow'           => '', // Removed

			// Button Color
			'enews-widget-button-back'                      => '#000000',
			'enews-widget-button-back-hov'                  => '#5da44f',
			'enews-widget-button-text-color'                => '#ffffff',
			'enews-widget-button-text-color-hov'            => '#ffffff',

			// Button Typography
			'enews-widget-button-stack'                     => 'open-sans',
			'enews-widget-button-size'                      => '16',
			'enews-widget-button-weight'                    => '400',
			'enews-widget-button-transform'                 => 'uppercase',

			// Botton Padding
			'enews-widget-button-pad-top'                   => '16',
			'enews-widget-button-pad-bottom'                => '16',
			'enews-widget-button-pad-left'                  => '24',
			'enews-widget-button-pad-right'                 => '24',
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
	 * Add new block for sticky message layout and front page layout.
	 *
	 * @return string $blocks
	 */
	public function add_new_sections( $blocks ) {

		// Confirm that we don't already have a block for widgets.
		if ( ! isset( $blocks['widgets'] ) ) {

			$blocks['widgets'] = array(
				'tab'   => __( 'Widgets Area', 'gppro' ),
				'title' => __( 'Widgets Area', 'gppro' ),
				'intro' => __( 'These settings are for the Sticky and Before Footer widget areas.', 'gppro', 'gppro' ),
				'slug'  => 'widgets',
			);
		}

		// Confirm that we don't already have a block for the front page.
		if ( ! isset( $blocks['frontpage'] ) ) {

			$blocks['frontpage'] = array(
				'tab'   => __( 'Front Page', 'gppro' ),
				'title' => __( 'Front Page Sections', 'gppro' ),
				'intro' => __( 'The frontpage uses 5 custom widget sections.', 'gppro', 'gppro' ),
				'slug'  => 'frontpage',
			);
		}

		// Return the block setup.
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

		// chang target for body font size
		$sections['body-type-setup']['data']['body-type-size']['target'] = '> div';

		// add text decoration
		$sections['body-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'body-color-link-hov', $sections['body-color-setup']['data'],
			array(
				'body-link-text-decoration-setup' => array(
					'title'     => __( 'Link Style', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'body-link-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => 'a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'body-link-text-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( 'a:hover', 'a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

        // add general body mobile font size
		$sections['body-type-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'body-type-size', $sections['body-type-setup']['data'],
			array(
				'body-type-size-mobile'  => array(
					'label'    => __( 'Font Size', 'gppro' ),
                    'sub'      => __( 'Mobile', 'gppro' ),
					'tip'      => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
					'input'    => 'font-size',
					'scale'    => 'text',
					'target'   => '> div',
					'builder'  => 'GP_Pro_Builder::px_css',
					'selector' => 'font-size',
					'media_query' =>'@media only screen and (max-width: 860px)',
				),
			)
		);

		//** return the section array
		return $sections;
	}

	/**
	 * add and filter options in the header area
	 *
	 * @return array|string $sections
	 */
	public function header_area( $sections, $class ) {

		// remove item background (base and hover), sub, and tip
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'header-nav-color-setup', array( 'header-nav-item-back', 'header-nav-item-back-hov' ) );
		$sections   = GP_Pro_Helper::remove_data_from_items( $sections, 'header-nav-color-setup', 'header-nav-item-back-hov', array( 'sub', 'tip' ) );

		// remove item background (base and hover), sub, and tip
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'header-nav-color-setup', array( 'header-nav-item-back', 'header-nav-item-back-hov' ) );
		$sections   = GP_Pro_Helper::remove_data_from_items( $sections, 'header-nav-color-setup', 'header-nav-item-back-hov', array( 'sub', 'tip' ) );

        // add active colors
		$sections['header-nav-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'header-nav-item-link-hov', $sections['header-nav-color-setup']['data'],
			array(
				'header-nav-active-link'  => array(
					'label'       => __( 'Active Link Color', 'gppro' ),
					'sub'         => __( 'Base', 'gppro' ),
					'input'       => 'color',
					'target'      => '.genesis-nav-menu .current-menu-item > a',
					'builder'     => 'GP_Pro_Builder::hexcolor_css',
					'selector'    => 'color'
				),
				'header-nav-active-link-hov'  => array(
					'label'       => __( 'Active Link Color', 'gppro' ),
					'sub'         => __( 'Hover', 'gppro' ),
					'input'       => 'color',
					'target'      => '.genesis-nav-menu  .current-menu-item > a:hover',
					'builder'     => 'GP_Pro_Builder::hexcolor_css',
					'selector'    => 'color'
				),
			)
		);

		// add header widget mobile title size
		$sections['header-widget-title-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'header-widget-title-size', $sections['header-widget-title-setup']['data'],
			array(
				'header-widget-title-size-mobile'  => array(
					'label'       => __( 'Font Size', 'gppro' ),
					'sub'         => __( 'Mobile', 'gppro' ),
					'tip'         => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
					'input'       => 'font-size',
					'scale'       => 'title',
					'target'      => 'header-widget-area .widget .widget-title',
					'selector'    => 'font-size',
					'media_query' => '@media only screen and (max-width: 800px)',
					'builder'     => 'GP_Pro_Builder::px_css',
				),
			)
		);

		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the navigation area
	 *
	 * @return array|string $sections
	 */
	public function navigation( $sections, $class ) {

		// rename primary navigation
		$sections['section-break-primary-nav']['break']['title'] = __( 'Below Header Menu', 'gppro' );

        // rename secondary navigation
		$sections['section-break-secondary-nav']['break']['title'] = __( 'Footer Menu', 'gppro' );

		// change primary navigation text description
		$sections['section-break-primary-nav']['break']['text'] =__( 'These settings apply to the navigation menu that displays Below the Header.', 'gppro' );

		// remove primary menu item background
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'primary-nav-top-item-color-setup', array( 'primary-nav-top-item-base-back', 'primary-nav-top-item-base-back-hov' ) );

		// remove primary active menu item background
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'primary-nav-top-active-color-setup', array( 'primary-nav-top-item-active-back', 'primary-nav-top-item-active-back-hov' ) );

        // change secondary navigation text description
		$sections['section-break-secondary-nav']['break']['text'] =__( 'These settings apply to the navigation menu that displays in the Footer.', 'gppro' );

        // remove footer menu background color
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'secondary-nav-area-setup' ) );

        // remove footer menu item background
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'secondary-nav-top-item-setup', array( 'secondary-nav-top-item-base-back', 'secondary-nav-top-item-base-back-hov' ) );

        // remove footer menu active menu item background
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'secondary-nav-top-active-color-setup', array( 'secondary-nav-top-item-active-back', 'secondary-nav-top-item-active-back-hov' ) );

        // remove drop down settings from footer menu
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array(
				'secondary-nav-drop-type-setup',
				'secondary-nav-drop-item-color-setup',
				'secondary-nav-drop-active-color-setup',
				'secondary-nav-drop-padding-setup',
				'secondary-nav-drop-border-setup'
			)
		);

		// remove footer menu item padding
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'secondary-nav-top-padding-setup', array( 'secondary-nav-top-item-padding-top', 'secondary-nav-top-item-padding-bottom', 'secondary-nav-top-item-padding-left', 'secondary-nav-top-item-padding-right' ) );

        // remove footer menu padding
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'secondary-nav-top-padding-setup' ) );

		// remove primary nav top item background color base
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'primary-nav-top-item-color-setup', array( 'primary-nav-top-item-base-back' ) );

		// add border bottom to primary navigation
		$sections['primary-nav-area-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'primary-nav-area-back', $sections['primary-nav-area-setup']['data'],
			array(
				'primary-nav-border-top-setup' => array(
					'title'     => __( 'Top Border', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'primary-nav-border-top-color'  => array(
					'label'    => __( 'Top Color', 'gppro' ),
					'input'    => 'color',
					'target'   => '.nav-primary',
					'selector' => 'border-top-color',
					'builder'  => 'GP_Pro_Builder::hexcolor_css',
				),
				'primary-nav-border-top-style'  => array(
					'label'    => __( 'Top Style', 'gppro' ),
					'input'    => 'borders',
					'target'   => '.nav-primary',
					'selector' => 'border-top-style',
					'builder'  => 'GP_Pro_Builder::text_css',
					'tip'      => __( 'Setting the style to "none" will remove the border completely.', 'gppro' ),
				),
				'primary-nav-border-top-width'  => array(
					'label'    => __( 'Top Width', 'gppro' ),
					'input'    => 'spacing',
					'target'   => '.nav-primary',
					'selector' => 'border-top-width',
					'builder'  => 'GP_Pro_Builder::px_css',
					'min'      => '0',
					'max'      => '10',
					'step'     => '1',
				),
			)
		);

		// add responsive navigation
		$sections = GP_Pro_Helper::array_insert_before(
			'primary-nav-drop-type-setup', $sections,
			array(
				'primary-responsive-icon-area-setup'	=> array(
					'title' => __( 'Responsive Menu', 'gppro' ),
					'data'  => array(
						'primary-responsive-menu-background-color'	=> array(
							'label'    => __( 'Background Color', 'gppro' ),
							'input'    => 'color',
							'target'   => '.menu-toggle',
							'selector' => 'background-color',
							'media_query' => '@media only screen and (max-width: 1023px)',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
						'primary-responsive-icon-color'	=> array(
							'label'    => __( 'Icon Color', 'gppro' ),
							'input'    => 'color',
							'target'   => array( '.menu-toggle:before' ),
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
                        'primary-responsive-icon-size'	=> array(
							'label'    => __( 'Icon Size', 'gppro' ),
							'input'    => 'font-size',
                            'scale'    => 'title',
							'target'   => array( '.menu-toggle:before' ),
							'selector' => 'font-size',
                            'media_query' => '@media only screen and (max-width: 800px)',
                            'builder'  => 'GP_Pro_Builder::px_css',

						),
						'arrow-icon-color'	=> array(
							'label'    => __( 'Arrow Color', 'gppro' ),
							'sub'      => __( 'Inactive', 'gppro' ),
							'input'    => 'color',
							'target'   => array( '.sub-menu-toggle' ),
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
						'arrow-icon-color-hov'	=> array(
							'label'    => __( 'Arrow Color', 'gppro' ),
							'sub'      => __( 'Hover', 'gppro' ),
							'input'    => 'color',
							'target'   => array( '.sub-menu-toggle:hover' ),
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
						'arrow-icon-color-act'	=> array(
							'label'    => __( 'Arrow Color', 'gppro' ),
							'sub'      => __( 'Active', 'gppro' ),
							'input'    => 'color',
							'target'   => array( '.sub-menu-toggle.activated:before' ),
							'selector' => 'color',
							'builder'  => 'GP_Pro_Builder::hexcolor_css',
						),
					),
				),
			)
		);

		// add primary nav highlight color
		$sections['primary-nav-top-item-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'primary-nav-top-item-base-link-hov', $sections['primary-nav-top-item-color-setup']['data'],
			array(
				'primary-nav-highlight-color'  => array(
					'label'     => __( 'Highlight Color', 'gppro' ),
					'input'     => 'color',
					'target'    => '.genesis-nav-menu > .highlight a',
					'builder'   => 'GP_Pro_Builder::hexcolor_css',
					'selector'  => 'border-bottom-color'
				),
				'primary-nav-highlight-style'  => array(
					'label'     => __( 'Highlight Style', 'gppro' ),
					'input'     => 'color',
					'target'    => '.genesis-nav-menu > .highlight a',
					'builder'   => 'GP_Pro_Builder::hexcolor_css',
					'selector'  => 'border-bottom-style'
				),
                'primary-nav-highlight-width'  => array(
					'label'     => __( 'Highlight Width', 'gppro' ),
					'input'     => 'spacing',
					'target'    => '.genesis-nav-menu > .highlight a',
					'builder'   => 'GP_Pro_Builder::px_css',
					'selector'  => 'border-bottom-width',
                    'min'       => '0',
					'max'       => '10',
					'step'      => '1',
				),
			)
		);

		// add text decoration to footer menu
		$sections['secondary-nav-top-type-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'secondary-nav-top-transform', $sections['secondary-nav-top-type-setup']['data'],
			array(
				'secondary-nav-text-decoration-setup' => array(
					'title'     => __( 'Link Style', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'secondary-nav-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => '.nav-secondary .genesis-nav-menu > .menu-item > a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'secondary-nav-text-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.nav-secondary .genesis-nav-menu > .menu-item > a:hover', '.nav-secondary .genesis-nav-menu > .menu-item > a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
					'always_write' => true,
				),
			)
		);
        // return the section array
		return $sections;
	}

	/**
	 * add settings for global widgets message
	 *
	 * @return array|string $sections
	 */
	public function widgets_section( $sections, $class ) {
		$sections['widgets'] = array(

			// add sticky message settings
			'section-break-sticky-message' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Sticky Message', 'gppro' ),
					'text'	=> __( 'The Sticky Message widget area uses a text widget. This area displays content above the Header area of the site after scrolling down the page.', 'gppro' ),
				),
			),

            // add colors
			'sticky-message-color-setup' => array(
				'title'     => __( 'Colors', 'gppro' ),
				'data'      => array(
					'sticky-message-back'   => array(
						'label'     => __( 'Background Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.sticky-message',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
                    'sticky-message-text'   => array(
						'label'     => __( 'Text Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.sticky-message',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
					),
                    'sticky-message-link'   => array(
						'label'     => __( 'Link Color', 'gppro' ),
						'sub'       => __( 'Base' , 'gppro' ),
						'input'     => 'color',
						'target'    => '.sticky-message a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
					),
                    'sticky-message-link-hov'   => array(
						'label'     => __( 'Link Color', 'gppro' ),
						'sub'       => __( 'Hover' , 'gppro' ),
						'input'     => 'color',
						'target'    => '.sticky-message a:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write' => true,
					),
                    'sticky-message-link-border-setup' => array(
						'title'     => __( 'Link Border', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines',
					),
					'sticky-message-lk-border-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.sticky-message .widget a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'border-bottom-color',
					),
					'sticky-message-lk-border-color-hov'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => array( '.sticky-message .widget a:hover', '.sticky-message .widget a:focus' ),
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'border-bottom-color',
						'always_write' => true,
					),
					'sticky-message-lk-border-style'   => array(
						'label'     => __( 'Style', 'gppro' ),
						'input'     => 'borders',
						'target'    => '.sticky-message .widget a',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'border-bottom-style',
						'tip'       => __( 'Setting the type to "none" will remove the border completely.', 'gppro' ),
					),
					'sticky-message-lk-border-width'   => array(
						'label'     => __( 'Width', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.sticky-message .widget a',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'border-bottom-width',
						'min'       => '0',
						'max'       => '10',
						'step'      => '1',
					),
				),
			),

			// add typography
			'sticky-message-type-setup' => array(
				'title'     => __( 'Typography', 'gppro' ),
				'data'      => array(
					'sticky-message-stack'   => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.sticky-message',
						'selector' => 'font-family',
						'builder'  => 'GP_Pro_Builder::stack_css',
					),
					'sticky-message-size'    => array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.sticky-message',
						'selector' => 'font-size',
						'tip'      => __( 'This option may affect all subsequent font sizes.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'sticky-message-weight'  => array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.sticky-message',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'builder'  => 'GP_Pro_Builder::number_css',
					),
					'sticky-message-style'   => array(
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
						'target'   => '.sticky-message',
						'selector' => 'font-style',
						'builder'  => 'GP_Pro_Builder::text_css',
					),
				),
			),

			// add padding
			'sticky-message-padding-setup'  => array(
				'title' => __( 'Padding', 'gppro' ),
				'data'  => array(
					'sticky-message-padding-top'    => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.sticky-message',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '40',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'sticky-message-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.sticky-message',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '40',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'sticky-message-padding-left'   => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.sticky-message',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '40',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
					'sticky-message-padding-right'  => array(
						'label'    => __( 'Right', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.sticky-message',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '40',
						'step'     => '1',
						'builder'  => 'GP_Pro_Builder::px_css',
					),
				),
			),

			// add welcome widget settings
			'section-break-before-footer-area' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Before Footer Widget', 'gppro' ),
					'text'	=> __( 'The demo for Wellness Pro uses the WP Instagram Widget. Settings below are general setting for a text widget.', 'gppro' ),
				),
			),
			// add area background and border settings
			'before-footer-back-setup' => array(
				'title'     => '',
				'data'      => array(
					'before-footer-back'  => array(
						'label'     => __( 'Background Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.before-footer',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
				),
			),

			// add padding settings
			'before-footer-padding-setup' => array(
				'title' => __( 'Padding', 'gppro' ),
				'data'  => array(
					'before-footer-padding-top' => array(
						'label'    => __( 'Top', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.before-footer .wrap',
						'builder'  => 'GP_Pro_Builder::pct_css',
						'selector' => 'padding-top',
						'min'      => '0',
						'max'      => '50',
						'step'     => '1',
						'suffix'   => '%',
					),
					'before-footer-padding-bottom' => array(
						'label'    => __( 'Bottom', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.before-footer .wrap',
						'builder'  => 'GP_Pro_Builder::pct_css',
						'selector' => 'padding-bottom',
						'min'      => '0',
						'max'      => '50',
						'step'     => '1',
						'suffix'   => '%',
					),
					'before-footer-padding-left' => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.before-footer .wrap',
						'builder'  => 'GP_Pro_Builder::pct_css',
						'selector' => 'padding-left',
						'min'      => '0',
						'max'      => '50',
						'step'     => '1',
						'suffix'   => '%',
					),
					'before-footer-padding-right' => array(
						'label'    => __( 'Left', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.before-footer .wrap',
						'builder'  => 'GP_Pro_Builder::pct_css',
						'selector' => 'padding-right',
						'min'      => '0',
						'max'      => '50',
						'step'     => '1',
						'suffix'   => '%',
					),
				),
			),

			'section-break-before-footer-widget-title'	=> array(
				'break'	=> array(
					'type'	=> 'thin',
					'title'	=> __( 'Widget Title', 'gppro' ),
				),
			),

			// add widget title settings
			'before-footer-title-setup'	=> array(
				'title' => '',
				'data'  => array(
					'welcome-message-title-text'	=> array(
						'label'    => __( 'Text', 'gppro' ),
						'input'    => 'color',
						'target'   => '.before-footer .widget .widget-title',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
						'selector' => 'color',
					),
					'before-footer-title-stack'	=> array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::stack_css',
						'selector' => 'font-family',
					),
					'before-footer-title-size'	=> array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::px_css',
						'selector' => 'font-size',
					),
					'before-footer-title-weight'	=> array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::number_css',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
					),
					'before-footer-title-transform'	=> array(
						'label'    => __( 'Text Appearance', 'gppro' ),
						'input'    => 'text-transform',
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::text_css',
						'selector' => 'text-transform',
					),
					'before-footer-title-align'	=> array(
						'label'    => __( 'Text Alignment', 'gppro' ),
						'input'    => 'text-align',
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::text_css',
						'selector' => 'text-align',
						'always_write' => true,
					),
					'before-footer-title-style'	=> array(
						'label'    => __( 'Font Style', 'gppro' ),
						'input'    => 'radio',
						'options'  => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic',
							),
						),
						'target'   => '.before-footer .widget-title',
						'builder'  => 'GP_Pro_Builder::text_css',
						'selector' => 'font-style',
						'always_write' => true,
					),
					'before-footer-title-margin-bottom'	=> array(
						'label'    => __( 'Bottom Margin', 'gppro' ),
						'input'    => 'spacing',
						'target'   => '.welcome-message .widget .widget-title',
						'builder'  => 'GP_Pro_Builder::px_css',
						'selector' => 'margin-bottom',
						'min'      => '0',
						'max'      => '42',
						'step'     => '1',
					),
				),
			),

			'section-break-before-footer-widget-content'	=> array(
				'break'	=> array(
					'type'  => 'thin',
					'title' => __( 'Widget Content', 'gppro' ),
				),
			),

			// add widget content settings
			'before-footer-content-setup'	=> array(
				'title' => '',
				'data'  => array(
					'before-footer-content-text'	=> array(
						'label'    => __( 'Text', 'gppro' ),
						'input'    => 'color',
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
						'selector' => 'color',
					),
					'before-footer-content-link'	=> array(
						'label'    => __( 'Link', 'gppro' ),
						'sub'      => __( 'Base', 'gppro' ),
						'input'    => 'color',
						'target'   => '.before-footer .widget a',
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
						'selector' => 'color',
					),
					'before-footer-content-link-hov'	=> array(
						'label'    => __( 'Link', 'gppro' ),
						'sub'      => __( 'Hover', 'gppro' ),
						'input'    => 'color',
						'target'   => array( '.welcome-message .widget a:hover', '.welcome-message .widget a:focus' ),
						'builder'  => 'GP_Pro_Builder::hexcolor_css',
						'selector' => 'color',
						'always_write' => true,
					),
					'before-footer-content-text-decoration'    => array(
						'label'     => __( 'Link Style', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'text-decoration',
						'target'    => '.before-footer .widget a',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-decoration',
					),
					'before-footer-content-stack' => array(
						'label'    => __( 'Font Stack', 'gppro' ),
						'input'    => 'font-stack',
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::stack_css',
						'selector' => 'font-family',
					),
					'before-footer-content-size'	=> array(
						'label'    => __( 'Font Size', 'gppro' ),
						'input'    => 'font-size',
						'scale'    => 'text',
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::px_css',
						'selector' => 'font-size',
					),
					'before-footer-content-weight'	=> array(
						'label'    => __( 'Font Weight', 'gppro' ),
						'input'    => 'font-weight',
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::number_css',
						'selector' => 'font-weight',
						'tip'      => __( 'Certain fonts will not display every weight.', 'gppro' ),
					),
					'before-footer-content-align'	=> array(
						'label'    => __( 'Text Alignment', 'gppro' ),
						'input'    => 'text-align',
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::text_css',
						'selector' => 'text-align',
					),
					'before-footer-content-style'	=> array(
						'label'    => __( 'Font Style', 'gppro' ),
						'input'    => 'radio',
						'options'  => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic',
							),
						),
						'target'   => '.before-footer .widget',
						'builder'  => 'GP_Pro_Builder::text_css',
						'selector' => 'font-style',
					),
				),
			),
		);
		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the post content area
	 *
	 * @return array|string $sections
	 */
	public function post_content( $sections, $class ) {

		// add border bottom to post header
		$sections['post-title-type-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'post-title-margin-bottom', $sections['post-title-type-setup']['data'],
			array(
				'entry-header-border-setup' => array(
					'title'     => __( 'Post Title Border', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'entry-header-border-color'    => array(
					'label'    => __( 'Border Color', 'gppro' ),
					'input'    => 'color',
					'target'   => '.entry-header::after',
					'selector' => 'border-bottom-color',
					'builder'  => 'GP_Pro_Builder::hexcolor_css',
				),
				'entry-header-border-style'    => array(
					'label'    => __( 'Border Style', 'gppro' ),
					'input'    => 'borders',
					'target'   => '.entry-header::after',
					'selector' => 'border-bottom-style',
					'builder'  => 'GP_Pro_Builder::text_css',
					'tip'      => __( 'Setting the style to "none" will remove the border completely.', 'gppro' ),
				),
				'entry-header-border-width'    => array(
					'label'    => __( 'Border Width', 'gppro' ),
					'input'    => 'spacing',
					'target'   => '.entry-header::after',
					'selector' => 'border-bottom-width',
					'builder'  => 'GP_Pro_Builder::px_css',
					'min'      => '0',
					'max'      => '10',
					'step'     => '1',
				),
				'entry-header-border-length'    => array(
					'label'    => __( 'Border Length', 'gppro' ),
					'input'    => 'spacing',
					'target'   => '.entry-header::after',
					'selector' => 'width',
					'builder'  => 'GP_Pro_Builder::px_css',
					'min'      => '0',
					'max'      => '720',
					'step'     => '1',
				),
			)
		);

		// add post meta text decoration
		$sections['post-header-meta-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'post-header-meta-comment-link-hov', $sections['post-header-meta-color-setup']['data'],
			array(
				'post-meta-comment-lk-txt-dec-setup' => array(
					'title'     => __( 'Comment Link', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'post-meta-comment-lk-txt-dec'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => '.entry-header .entry-meta .entry-comments-link a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'post-meta-comment-lk-txt-dec-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.entry-header .entry-meta .entry-comments-link a:hover', '.entry-header .entry-meta .entry-comments-link a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// add post content text decoration
		if ( ! class_exists( 'GP_Pro_Entry_Content' ) ) {
		$sections['post-entry-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'post-entry-link-hov', $sections['post-entry-color-setup']['data'],
				array(
					'post-entry-lk-txt-dec-setup' => array(
						'title'     => __( 'Comment Link', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
					'post-entry-lk-txt-dec'    => array(
						'label'     => __( 'Link Style', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'text-decoration',
						'target'    => '.content > .entry .entry-content a',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-decoration',
					),
					'post-entry-lk-txt-dec-hov'    => array(
						'label'     => __( 'Link Style', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'text-decoration',
						'target'    => array( '.content > .entry .entry-content a:hover', '.content > .entry .entry-content a:focus' ),
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-decoration',
					),
				)
			);
		}

		// add post footer text decoration
		$sections['post-footer-color-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'post-footer-tag-link-hov', $sections['post-footer-color-setup']['data'],
			array(
				'post-footer-comment-lk-txt-dec-setup' => array(
					'title'     => __( 'Post Footer Link', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'post-footer-comment-lk-txt-dec'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.entry-footer .entry-categories a', '.entry-footer .entry-tags a' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'post-footer-comment-lk-txt-dec-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array(
						'.entry-footer .entry-categories a:hover',
						'.entry-footer .entry-categories a:focus',
						'.entry-footer .entry-tags a:hover',
						'.entry-footer .entry-tags a:focus',
						),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// add blog archive
		$sections = GP_Pro_Helper::array_insert_after(
			'post-footer-divider-setup', $sections,
			array(
				// add archive page setting
				'section-break-archive-page'   => array(
					'break' => array(
						'type'  => 'full',
						'title' => __( 'Archive Page', 'gppro' ),
						'text'  => __( 'These settings apply to the archive page title and description.', 'gppro' ),
					),
				),

				// add background color
				'archive-description-back'    => array(
					'title'     => __( '', 'gppro' ),
					'data'      => array(
						'archive-title-back'   => array(
							'label'     => __( 'Background', 'gppro' ),
							'input'     => 'color',
							'target'    => '.archive-description',
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'background-color'
						),
					),
				),

				// add padding and margin setting
				'archive-description-padding'    => array(
					'title'     => __( 'Padding', 'gppro' ),
					'data'      => array(
						'archive-description-padding-top'	=> array(
							'label'    => __( 'Top', 'gppro' ),
							'input'    => 'spacing',
							'target'   => '.archive-description',
							'builder'  => 'GP_Pro_Builder::px_css',
							'selector' => 'padding-top',
							'min'      => '0',
							'max'      => '80',
							'step'     => '1',
						),
						'archive-description-padding-bottom'	=> array(
							'label'    => __( 'Bottom', 'gppro' ),
							'input'    => 'spacing',
							'target'   => '.archive-description',
							'builder'  => 'GP_Pro_Builder::px_css',
							'selector' => 'padding-bottom',
							'min'      => '0',
							'max'      => '80',
							'step'     => '1',
						),
						'archive-description-padding-left'	=> array(
							'label'    => __( 'Left', 'gppro' ),
							'input'    => 'spacing',
							'target'   => '.archive-description',
							'builder'  => 'GP_Pro_Builder::px_css',
							'selector' => 'padding-left',
							'min'      => '0',
							'max'      => '32',
							'step'     => '1',
						),
						'archive-description-padding-right'	=> array(
							'label'    => __( 'Right', 'gppro' ),
							'input'    => 'spacing',
							'target'   => '.archive-description',
							'builder'  => 'GP_Pro_Builder::px_css',
							'selector' => 'padding-right',
							'min'      => '0',
							'max'      => '80',
							'step'     => '1',
						),
						'archive-description-margin-setup' => array(
							'title'     => __( 'Margin Bottom', 'gppro' ),
							'input'     => 'divider',
							'style'     => 'lines',
						),
						'archive-description-margin-bottom'  => array(
							'label'     => __( 'Margin Bottom', 'gppro' ),
							'input'     => 'spacing',
							'target'    => '.archive-description',
							'builder'   => 'GP_Pro_Builder::px_css',
							'selector'  => 'margin-bottom',
							'min'       => '0',
							'max'       => '70',
							'step'      => '1'
						),
					),
				),

				// add archive title typography settings
				'archive-title-type-setup'     => array(
					'title' => __( 'Title Typography', 'gppro' ),
					'data'  => array(
						'archive-title-text'   => array(
							'label'     => __( 'Text', 'gppro' ),
							'input'     => 'color',
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'color'
						),
						'archive-title-stack'  => array(
							'label'     => __( 'Font Stack', 'gppro' ),
							'input'     => 'font-stack',
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::stack_css',
							'selector'  => 'font-family'
						),
						'archive-title-size'   => array(
							'label'     => __( 'Font Size', 'gppro' ),
							'input'     => 'font-size',
							'scale'     => 'text',
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::px_css',
							'selector'  => 'font-size',
						),
						'archive-title-weight' => array(
							'label'     => __( 'Font Weight', 'gppro' ),
							'input'     => 'font-weight',
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::number_css',
							'selector'  => 'font-weight',
							'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' )
						),
						'archive-title-transform'	=> array(
							'label'    => __( 'Text Appearance', 'gppro' ),
							'input'    => 'text-transform',
							'target'   => array( '.archive-title', '.archive-description .entry-title' ),
							'selector' => 'text-transform',
							'builder'  => 'GP_Pro_Builder::text_css',
						),
						'archive-title-align'	=> array(
							'label'    => __( 'Text Alignment', 'gppro' ),
							'input'    => 'text-align',
							'target'   => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'  => 'GP_Pro_Builder::text_css',
							'selector' => 'text-align',
							'always_write' => true,
						),
						'archive-title-style'  => array(
							'label'     => __( 'Font Style', 'gppro' ),
							'input'     => 'radio',
							'options'   => array(
								array(
									'label' => __( 'Normal', 'gppro' ),
									'value' => 'normal',
								),
								array(
									'label' => __( 'Italic', 'gppro' ),
									'value' => 'italic'
								),
							),
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::text_css',
							'selector'  => 'font-style',
						),
					),
				),

				// add archive text typography settings
				'archive-text-type-setup'     => array(
					'title' => __( 'Text Typography', 'gppro' ),
					'data'  => array(
						'archive-text-text'   => array(
							'label'     => __( 'Text', 'gppro' ),
							'input'     => 'color',
							'target'    => '.archive-description p',
							'builder'   => 'GP_Pro_Builder::hexcolor_css',
							'selector'  => 'color'
						),
						'archive-text-stack'  => array(
							'label'     => __( 'Font Stack', 'gppro' ),
							'input'     => 'font-stack',
							'target'    => array( '.archive-title', '.archive-description .entry-title' ),
							'builder'   => 'GP_Pro_Builder::stack_css',
							'selector'  => 'font-family'
						),
						'archive-text-size'   => array(
							'label'     => __( 'Font Size', 'gppro' ),
							'input'     => 'font-size',
							'scale'     => 'text',
							'target'    => '.archive-description p',
							'builder'   => 'GP_Pro_Builder::px_css',
							'selector'  => 'font-size',
						),
						'archive-text-weight' => array(
							'label'     => __( 'Font Weight', 'gppro' ),
							'input'     => 'font-weight',
							'target'    => '.archive-description p',
							'builder'   => 'GP_Pro_Builder::number_css',
							'selector'  => 'font-weight',
							'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' )
						),
						'archive-text-transform'	=> array(
							'label'    => __( 'Text Appearance', 'gppro' ),
							'input'    => 'text-transform',
							'target'   => '.archive-description p',
							'selector' => 'text-transform',
							'builder'  => 'GP_Pro_Builder::text_css',
						),
						'archive-text-align'	=> array(
							'label'    => __( 'Text Alignment', 'gppro' ),
							'input'    => 'text-align',
							'target'   => '.archive-description p',
							'builder'  => 'GP_Pro_Builder::text_css',
							'selector' => 'text-align',
							'always_write' => true,
						),
						'archive-text-style'  => array(
							'label'     => __( 'Font Style', 'gppro' ),
							'input'     => 'radio',
							'options'   => array(
								array(
									'label' => __( 'Normal', 'gppro' ),
									'value' => 'normal',
								),
								array(
									'label' => __( 'Italic', 'gppro' ),
									'value' => 'italic'
								),
							),
							'target'    => '.archive-description p',
							'builder'   => 'GP_Pro_Builder::text_css',
							'selector'  => 'font-style',
						),
					),
				),
			)
		);
		// return the section array
		return $sections;
	}

	/**
	 * add settings for frontpage block
	 *
	 * @return array|string $sections
	 */
	public function frontpage_section( $sections, $class ) {

		$sections['frontpage'] = array(

            // front page 1
			'section-break-front-page-one' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section One', 'gppro' ),
					'text'	=> __( 'This area uses a text widget with an HTML button.', 'gppro' ),
				),
			),

			// add front page 1 title
			'front-page-1-title-setup' => array(
				'title'     => __( 'Widget Title', 'gppro' ),
				'data'      => array(
					'front-page-1-title-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-1-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-1 h3',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-1-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-1-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-1 .widget-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-1-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-1-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-1-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-1-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-1-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-1 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

			// add front page 1 text
			'front-page-1-text-setup' => array(
				'title'     => __( 'Widget Content', 'gppro' ),
				'data'      => array(
					'front-page-1-text-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-1 .widget',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
					'front-page-1-text-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .widget',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-1-text-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-1 .widget',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
					'front-page-1-text-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-1 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
					'front-page-1-text-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-1 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            // add front page 1 button
			'front-page-1-btn-setup' => array(
				'title'     => __( 'Button', 'gppro' ),
				'data'      => array(
					'single-comment-triangles-info'  => array(
						'input'     => 'description',
					 	'desc'      => __( 'The button top and bottom border color will not preview, but will match the base background color once settings are saved.', 'gppro' ),
					),
					'front-page-1-color-divider' => array(
						'title'     => __( 'Colors', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
                    'front-page-1-btn-back'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
					'front-page-1-btn-back-hov'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
						'always_write' => true,
					),
					'front-page-1-btn-color'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-1-btn-color-hover'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-1 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'=> true,
					),
					'front-page-1-type-divider' => array(
						'title'     => __( 'Typography', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
					'front-page-1-btn-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-1-btn-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-1-btn-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-1-btn-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-1 .button',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
				),
            ),

            // front page 2
			'section-break-front-page-two' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section Two', 'gppro' ),
					'text'	=> __( 'This area uses four Featured Post widgets.', 'gppro' ),
				),
			),

            // add front page 2 title
			'front-page-2-title-setup' => array(
				'title'     => __( 'Featured Title', 'gppro' ),
				'data'      => array(
					'front-page-2-title-link-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-2 .featured-content .entry-title a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-2-title-link-color-hov'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-2 .featured-content .entry-title a:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write' => true,
					),
					'front-page-2-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-2-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-2-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-2 .featured-content .entry-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-2-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-2-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-2-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-2-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-2-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-2 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

            // add front page 2 meta
            'front-page-2-meta-setup'  => array(
				'title'     => __( 'Featured Info', 'gppro' ),
				'data'      => array(
					 'front-page-2-meta-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-2-meta-stack'    => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                   'front-page-2-meta-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-2-meta-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-2-meta-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-2-meta-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-2 p.entry-meta',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
					'front-page-2-meta-author-color-setup' => array(
						'title'     => __( 'Author Link', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
                    'front-page-2-meta-author-color'   => array(
						'label'     => __( 'Link', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-2 .entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-2-meta-author-color-hov'   => array(
						'label'     => __( 'Link', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-2 .entry-header .entry-meta .entry-author-link:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-2-author-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-2 entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
					 'front-page-2-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-2 entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-2-meta-author-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-2 .entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-2-meta-author-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-2 .entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-2-meta-author-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-2 .entry-header .entry-meta .entry-author-link',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            //** front page 3
			'section-break-front-page-three' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section Three', 'gppro' ),
					'text'	=> __( 'This area uses a text widget with an HTML button.', 'gppro' ),
				),
			),

            // add front page 3 title
			'front-page-3-title-setup' => array(
				'title'     => __( 'Widget Title', 'gppro' ),
				'data'      => array(
					'front-page-3-title-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-1-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-3-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-3-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-3 .widget-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-3-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-3-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-3-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-3-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-3-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-3 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

            // add front page 3 text
			'front-page-3-text-setup' => array(
				'title'     => __( 'Widget Content', 'gppro' ),
				'data'      => array(
					'front-page-3-text-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .widget',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-3-text-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-3 .widget',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
					'front-page-3-text-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-3 .widget',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
					'front-page-3-text-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-3 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
					'front-page-3-text-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-3 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            // add front page 3 button
			'front-page-3-btn-setup' => array(
				'title'     => __( 'Button', 'gppro' ),
				'data'      => array(
					'front-page-3-button-info'  => array(
						'input'     => 'description',
					 	'desc'      => __( 'The button top and bottom border color will not preview, but will match the base background color once settings are saved.', 'gppro' ),
					),
					'front-page-3-color-divider' => array(
						'title'     => __( 'Colors', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
                    'front-page-3-btn-back'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
					'front-page-3-btn-back-hov'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
						'always_write' => true,
					),
					'front-page-3-btn-color'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-3-btn-color-hover'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-3 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'=> true,
					),
					'front-page-3-type-divider' => array(
						'title'     => __( 'Typography', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
					'front-page-3-btn-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-3-btn-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-3-btn-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-3-btn-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-3 .button',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
				),
            ),

            // front page 4
			'section-break-front-page-four' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section Four', 'gppro' ),
					'text'	=> __( 'This area uses four Author Pro - Featured Book widgets', 'gppro' ),
				),
			),

			// front page 4 featured
            'front-page-4-featured-setup'  => array(
				'title'     => __( 'Featured Banner', 'gppro' ),
				'data'      => array(
					 'front-page-4-featured-back'   => array(
						'label'     => __( 'Background Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color'
					),
					 'front-page-4-featured-color'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-4-featured-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-4-featured-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-4-featured-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-4-featured-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.featured-content .book-featured-text-banner',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
                ),
            ),

            // add front page 4 title
			'front-page-4-title-setup' => array(
				'title'     => __( 'Featured Title', 'gppro' ),
				'data'      => array(
					'front-page-4-title-link-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .entry-title a',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					 'front-page-4-title-link-color-hov'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .featured-content .entry-title a:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-4-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-4-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-4-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-4 .featured-content .entry-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-4-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-4-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-4-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-4-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-4-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-4 .featured-content .entry-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

            // add front page 4 meta
            'front-page-4-meta-setup'  => array(
				'title'     => __( 'Featured Info', 'gppro' ),
				'data'      => array(
					 'front-page-4-meta-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-4-meta-stack'    => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                   'front-page-4-meta-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-4-meta-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-4-meta-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-4-meta-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
					'front-page-4-meta-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-4 p.book-author',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
                    'front-page-4-meta-author-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .book-author .book-author-link',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-4-meta-author-color-hov'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .book-author .book-author-link',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-4-meta-author-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-4 .book-author .book-author-link',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-4-meta-author-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-4 .book-author .book-author-link',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-4-meta-author-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-4 .book-author .book-author-link',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            // front page 4 buttons
            'front-page-4-btn-setup' => array(
				'title'     => __( 'Button', 'gppro' ),
				'data'      => array(
					'single-comment-triangles-info'  => array(
						'input'     => 'description',
					 	'desc'      => __( 'The button top and bottom border color will not preview, but will match the base background color once settings are saved.', 'gppro' ),
					),
					'front-page-4-color-divider' => array(
						'title'     => __( 'Colors', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
                    'front-page-4-btn-back'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
					'front-page-4-btn-back-hov'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
						'always_write' => true,
					),
					'front-page-4-btn-color'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-4-btn-color-hover'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-4 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'=> true,
					),
					'front-page-4-type-divider' => array(
						'title'     => __( 'Typography', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
					'front-page-4-btn-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-4-btn-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-4-btn-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-4-btn-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-4 .button',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
				),
            ),

            // front page 5
			'section-break-front-page-five' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section Five', 'gppro' ),
					'text'	=> __( 'This area uses a text widget with an HTML button.', 'gppro' ),
				),
			),

            // add front page 5 title
			'front-page-5-title-setup' => array(
				'title'     => __( 'Widget Title', 'gppro' ),
				'data'      => array(
					 'front-page-5-title-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-5-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-5-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-5-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-5-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-5 h3',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-5-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-5-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-5-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-5 h3',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-5-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

            // add front page 5 text
			'front-page-5-text-setup' => array(
				'title'     => __( 'Widget Content', 'gppro' ),
				'data'      => array(
					'front-page-5-text-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-5-text-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
					'front-page-5-text-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
					'front-page-5-text-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
					'front-page-5-text-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            // add front page 5 button
			'front-page-5-btn-setup' => array(
				'title'     => __( 'Button', 'gppro' ),
				'data'      => array(
					'single-comment-triangles-info'  => array(
						'input'     => 'description',
					 	'desc'      => __( 'The button top and bottom border color will not preview, but will match the base background color once settings are saved.', 'gppro' ),
					),
					'front-page-5-color-divider' => array(
						'title'     => __( 'Colors', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
                    'front-page-5-btn-back'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
					),
					'front-page-5-btn-back-hov'  => array(
						'label'     => __( 'Background', 'gppro' ),
						'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'background-color',
						'always_write' => true,
					),
					'front-page-5-btn-color'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
                    'front-page-5-btn-color-hover'   => array(
						'label'     => __( 'Font Color', 'gppro' ),
                        'sub'       => __( 'Hover', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .button:hover',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'=> true,
					),
					'front-page-5-type-divider' => array(
						'title'     => __( 'Typography', 'gppro' ),
						'input'     => 'divider',
						'style'     => 'lines'
					),
					'front-page-5-btn-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-5-btn-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-5-btn-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-5-btn-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-5 .button',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
				),
            ),

            // front page 6
			'section-break-front-page-six' => array(
				'break' => array(
					'type'  => 'full',
					'title' => __( 'Front Page Section Six', 'gppro' ),
					'text'	=> __( 'This area uses a text widget.', 'gppro' ),
				),
			),

			// add front page 6 title
			'front-page-6-title-setup' => array(
				'title'     => __( 'Widget Title', 'gppro' ),
				'data'      => array(
					 'front-page-6-title-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-6-title-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-6-title-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-6-title-size-mobile'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
                        'sub'       => __( 'Mobile', 'gppro' ),
					    'tip'       => __( 'The live preview may not reflect the responsive CSS properly.', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget-title',
					    'media_query' => '@media only screen and (max-width: 800px)',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-6-title-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-6-title-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-6-title-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-6-title-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),

					'front-page-6-title-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-5 .widget-title',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '32',
						'step'      => '1'
					),
				),
			),

            // add front page 6 text
			'front-page-6-text-setup' => array(
				'title'     => __( 'Widget Content', 'gppro' ),
				'data'      => array(
					'front-page-6-text-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-6-text-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
					'front-page-6-text-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
					'front-page-6-text-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-5 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
					'front-page-6-text-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-6 .widget',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),

            // add front page 6 blockquote
			'front-page-6-quote-setup' => array(
				'title'     => __( 'Blockquote', 'gppro' ),
				'data'      => array(
					'front-page-6-quote-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color'
					),
					'front-page-6-quote-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-6-quote-size'   => array(
						'label'     => __( 'Font Size', 'gppro' ),
						'input'     => 'font-size',
						'scale'     => 'text',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'font-size'
					),
                    'front-page-6-quote-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-6-quote-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-6-quote-align'  => array(
						'label'     => __( 'Text Alignment', 'gppro' ),
						'input'     => 'text-align',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-align',
					),
                    'front-page-6-quote-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
                    'front-page-6-quote-margin-bottom'  => array(
						'label'     => __( 'Bottom Margin', 'gppro' ),
						'input'     => 'spacing',
						'target'    => '.front-page-6 blockquote',
						'builder'   => 'GP_Pro_Builder::px_css',
						'selector'  => 'margin-bottom',
						'min'       => '0',
						'max'       => '40',
						'step'      => '1',
					),
				),
			),

            // add front page 6 cite
			'front-page-6-cite-setup' => array(
				'title'     => __( 'Attribution', 'gppro' ),
				'data'      => array(
					'front-page-6-cite-color'   => array(
						'label'     => __( 'Color', 'gppro' ),
                        'sub'       => __( 'Base', 'gppro' ),
						'input'     => 'color',
						'target'    => '.front-page-6 cite',
						'builder'   => 'GP_Pro_Builder::hexcolor_css',
						'selector'  => 'color',
						'always_write'=> true,
					),
					'front-page-6-cite-stack'   => array(
						'label'     => __( 'Font Stack', 'gppro' ),
						'input'     => 'font-stack',
						'target'    => '.front-page-6 cite',
						'builder'   => 'GP_Pro_Builder::stack_css',
						'selector'  => 'font-family'
					),
                    'front-page-6-cite-weight'   => array(
						'label'     => __( 'Font Weight', 'gppro' ),
                        'tip'       => __( 'Certain fonts will not display every weight.', 'gppro' ),
						'input'     => 'font-weight',
						'target'    => '.front-page-6 cite',
						'builder'   => 'GP_Pro_Builder::number_css',
						'selector'  => 'font-weight',
					),
                    'front-page-6-cite-transform'  => array(
						'label'     => __( 'Text Appearance', 'gppro' ),
						'input'     => 'text-transform',
						'target'    => '.front-page-6 cite',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'text-transform',
					),
                    'front-page-6-cite-style'    => array(
						'label'     => __( 'Font Style', 'gppro' ),
						'input'     => 'radio',
						'options'   => array(
							array(
								'label' => __( 'Normal', 'gppro' ),
								'value' => 'normal',
							),
							array(
								'label' => __( 'Italic', 'gppro' ),
								'value' => 'italic'
							),
						),
						'target'    => '.front-page-6 cite',
						'builder'   => 'GP_Pro_Builder::text_css',
						'selector'  => 'font-style'
					),
				),
			),
		);
		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the post extras area
	 *
	 * @return array|string $sections
	 */
	public function content_extras( $sections, $class ) {

		// reset the specificity of the read more link
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link']['target']   = '.content > .post .entry-content a.more-link';
		$sections['extras-read-more-colors-setup']['data']['extras-read-more-link-hov']['target']   = array( '.content > .post .entry-content a.more-link:hover', '.content > .post .entry-content a.more-link:focus' );

		// remove pagination border radius
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'extras-pagination-numeric-backs', 'extras-pagination-numeric-border-radius' );

		// add breadcrumb background color
		$sections['extras-breadcrumb-setup']['data'] = GP_Pro_Helper::array_insert_before(
			'extras-breadcrumb-text', $sections['extras-breadcrumb-setup']['data'],
			array(
				'extras-breadcrumb-back'  => array(
				'label'     => __( 'Background color', 'gppro' ),
				'input'     => 'color',
				'target'    => '.breadcrumb',
				'builder'   => 'GP_Pro_Builder::hexcolor_css',
				'selector'  => 'background-color'
				),
			)
		);

		// add background for text pagination
		$sections['extras-pagination-text-setup']['data'] = GP_Pro_Helper::array_insert_before(
			'extras-pagination-text-link', $sections['extras-pagination-text-setup']['data'],
			array(
				'extras-pagination-back'  => array(
					'label'     => __( 'Background', 'gppro' ),
					'sub'         => __( 'Base', 'gppro' ),
					'input'     => 'color',
					'target'    => '.archive-pagination a',
					'builder'   => 'GP_Pro_Builder::hexcolor_css',
					'selector'  => 'background-color'
				),
				'extras-pagination-back-hov'  => array(
					'label'     => __( 'Background', 'gppro' ),
					'sub'         => __( 'Hover', 'gppro' ),
					'input'     => 'color',
					'target'    => array( '.archive-pagination a:hover', '.archive-pagination a:focus' ),
					'builder'   => 'GP_Pro_Builder::hexcolor_css',
					'selector'  => 'background-color'
				),
			)
		);

		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the after entry widget
	 *
	 * @return array|string $sections
	 */
	public function after_entry( $sections, $class ) {

		// remove after entry widget border radius
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'after-entry-widget-back-setup', 'after-entry-widget-area-border-radius' );

		// remove single widget settings
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, 'after-entry-single-widget-setup' );

		// return the section array
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
        // remove comment layout (standard and author)
		$sections   = GP_Pro_Helper::remove_settings_from_section( $sections, array( 'single-comment-standard-setup', 'single-comment-author-setup' ) );

        // remove input field border-radius
        $sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'comment-reply-fields-input-layout-setup', 'comment-reply-fields-input-border-radius' );

        // remove submit button border-radius
        $sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'comment-submit-button-spacing-setup', 'comment-submit-button-border-radius' );

        // Add text-decoration to single comment area
		$sections['comment-element-name-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'comment-element-name-link-hov', $sections['comment-element-name-setup']['data'],
			array(
				'comment-element-name-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => '.comment-author a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'comment-element-name-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.comment-author a:hover', '.comment-author a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// Add text-decoration to comment date
		$sections['comment-element-date-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'comment-element-date-link-hov', $sections['comment-element-date-setup']['data'],
			array(
				'comment-element-date-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.comment-meta', '.comment-meta a' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'comment-element-date-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.comment-meta a:hover', '.comment-meta a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// Add text-decoration to comment reply
		$sections['comment-element-reply-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'comment-element-reply-link-hov', $sections['comment-element-reply-setup']['data'],
			array(
				'comment-element-reply-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => 'a.comment-reply-link',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'comment-element-reply-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( 'a.comment-reply-link:hover', 'a.comment-reply-link:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// Add text-decoration to comment body
		$sections['comment-element-body-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'comment-element-body-link-hov', $sections['comment-element-body-setup']['data'],
			array(
				'comment-element-body-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => '.comment-content a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'comment-element-body-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.comment-content a:hover', '.comment-content a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// add comment reply text appearance
		$sections['comment-element-reply-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'comment-element-reply-align', $sections['comment-element-reply-setup']['data'],
			array(
				'comment-element-reply-transform'  => array(
					'label'     => __( 'Text Appearance', 'gppro' ),
					'input'     => 'text-transform',
					'target'    => 'a.comment-reply-link',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-transform',
				),
			)
		);
		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the main sidebar area
	 *
	 * @return array|string $sections
	 */
	public function main_sidebar( $sections, $class ) {

		// remove border-radius
        $sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'sidebar-widget-back-setup', 'sidebar-widget-border-radius' );

		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the footer widget section
	 *
	 * @return array|string $sections
	 */
	public function footer_widgets( $sections, $class ) {

		// remove border radius
		$sections   = GP_Pro_Helper::remove_items_from_settings( $sections, 'footer-widget-single-back-setup', 'footer-widget-single-border-radius' );

		// Add text-decoration to footer widget link
		$sections['footer-widget-content-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'footer-widget-content-link-hov', $sections['footer-widget-content-setup']['data'],
			array(
				'footer-widget-text-decoration'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => '.footer-widgets .widget a',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'footer-widget-text-decoration-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.footer-widgets .widget a:hover', '.footer-widgets .widget a:focus' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
			)
		);

		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the main footer section
	 *
	 * @return array|string $sections
	 */
	public function footer_main( $sections, $class ) {

		// add border bottom to footer main
		$sections['footer-main-back-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'footer-main-area-back', $sections['footer-main-back-setup']['data'],
			array(
				'footer-main-border-top-setup' => array(
					'title'     => __( 'Top Border', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines'
				),
				'footer-main-border-top-color'  => array(
					'label'    => __( 'Top Color', 'gppro' ),
					'input'    => 'color',
					'target'   => '.site-footer',
					'selector' => 'border-top-color',
					'builder'  => 'GP_Pro_Builder::hexcolor_css',
				),
				'footer-main-border-top-style'  => array(
					'label'    => __( 'Top Style', 'gppro' ),
					'input'    => 'borders',
					'target'   => '.site-footer',
					'selector' => 'border-top-style',
					'builder'  => 'GP_Pro_Builder::text_css',
					'tip'      => __( 'Setting the style to "none" will remove the border completely.', 'gppro' ),
				),
				'footer-main-border-top-width'  => array(
					'label'    => __( 'Top Width', 'gppro' ),
					'input'    => 'spacing',
					'target'   => '.site-footer',
					'selector' => 'border-top-width',
					'builder'  => 'GP_Pro_Builder::px_css',
					'min'      => '0',
					'max'      => '10',
					'step'     => '1',
				),
			)
		);

		// add text decoration
		$sections['footer-main-content-setup']['data'] = GP_Pro_Helper::array_insert_after(
			'footer-main-content-link-hov', $sections['footer-main-content-setup']['data'],
			array(
				'footer-main-content-lk-txt-dec'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Base', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.site-footer a', '.site-footer p a' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'footer-main-content-lk-txt-dec-hov'    => array(
					'label'     => __( 'Link Style', 'gppro' ),
					'sub'       => __( 'Hover', 'gppro' ),
					'input'     => 'text-decoration',
					'target'    => array( '.site-footer a', '.site-footer p a' ),
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-decoration',
				),
				'footer-main-content-typography-divider' => array(
					'title' => __( 'Typography', 'gppro' ),
					'input' => 'divider',
					'style' => 'lines',
				),
			)
		);

		// return the section array
		return $sections;
	}

	/**
	 * add and filter options in the Genesis Widgets - eNews
	 *
	 * @return array|string $sections
	 */
	public function genesis_widgets_section( $sections, $class ) {

		// bail without the enews add on
		if ( empty( $sections['genesis_widgets'] ) ) {
			return $sections;
		}

		// remove widget background color
		unset( $sections['genesis_widgets']['enews-widget-general']['data']['enews-widget-back'] );

		// remove field box shadow
		unset( $sections['genesis_widgets']['enews-widget-field-inputs']['data']['enews-widget-field-input-box-shadow'] );

		// add widget title settings
		$sections['genesis_widgets']['enews-widget-general']['data'] = GP_Pro_Helper::array_insert_before(
			'enews-widget-typography', $sections['genesis_widgets']['enews-widget-general']['data'],
			array(
				'enews-title-typography' => array(
					'title'     => __( 'Widget Title Typography', 'gppro' ),
					'input'     => 'divider',
					'style'     => 'lines',
				),
				'enews-title-gen-stack'    => array(
					'label'     => __( 'Font Stack', 'gpwen' ),
					'input'     => 'font-stack',
					'target'    => '.enews-widget .widget-title',
					'builder'   => 'GP_Pro_Builder::stack_css',
					'selector'  => 'font-family'
				),
				'enews-title-gen-size' => array(
					'label'     => __( 'Font Size', 'gpwen' ),
					'input'     => 'font-size',
					'scale'     => 'text',
					'target'    => '.enews-widget .widget-title',
					'builder'   => 'GP_Pro_Builder::px_css',
					'selector'  => 'font-size',
				),
				'enews-title-gen-weight'   => array(
					'label'     => __( 'Font Weight', 'gpwen' ),
					'input'     => 'font-weight',
					'target'    => '.enews-widget .widget-title',
					'builder'   => 'GP_Pro_Builder::number_css',
					'selector'  => 'font-weight',
					'tip'       => __( 'Certain fonts will not display every weight.', 'gpwen' )
				),
				'enews-title-gen-transform'    => array(
					'label'     => __( 'Text Appearance', 'gpwen' ),
					'input'     => 'text-transform',
					'target'    => '.enews-widget .widget-title',
					'builder'   => 'GP_Pro_Builder::text_css',
					'selector'  => 'text-transform'
				),
				'enews-title-gen-text-margin-bottom' => array(
					'label'     => __( 'Bottom Margin', 'gpwen' ),
					'input'     => 'spacing',
					'target'    => '.enews-widget .widget-title',
					'builder'   => 'GP_Pro_Builder::px_css',
					'selector'  => 'margin-bottom',
					'min'       => '0',
					'max'       => '48',
					'step'      => '1'
				),
			)
		);

		// return the section build
		return $sections;
	}

	/**
	 * checks the settings to see if dropdown background color
	 * has been changed and if so, adds the value to the CSS
	 * triangles so they match
	 *
	 * @param  [type] $setup [description]
	 * @param  [type] $data  [description]
	 * @param  [type] $class [description]
	 * @return [type]        [description]
	 */
	public function css_builder_filters( $setup, $data, $class ) {

		// check for change in primary navigation back
		if ( ! empty( $data['primary-nav-area-back'] ) ) {

			// the actual CSS entry
			$setup .= '@media only screen and (max-width: 1023px) { ';
			$setup  .= $class . ' .nav-primary .genesis-nav-menu .menu-item a, ' . $class . ' .sub-menu-toggle, ' . $class . ' .js nav button:hover { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'background-color', $data['primary-nav-area-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// check for change in primary navigation back
		if ( ! empty( $data['primary-nav-drop-item-base-back'] ) || ! empty( $data['primary-nav-drop-item-base-back'] )) {

			// the actual CSS entry
			$setup  .= $class . ' .nav-primary .genesis-nav-menu .sub-menu:before, ' . $class . ' .nav-primary .genesis-nav-menu .sub-menu:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['primary-nav-drop-item-base-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

        // check for change in front-page 1 button background
		if ( ! empty( $data['front-page-1-btn-back'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-1 .button:before, ' . $class . ' .front-page-1 .button:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-1-btn-back'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-1-btn-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// check for change in button background hover
		if ( ! empty( $data['front-page-1-btn-back-hov'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-1 .button:hover:before, ' . $class . ' .front-page-1 .button:hover:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-1-btn-back-hov'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-1-btn-back-hov'] ) . "\n";
			$setup  .= '}' . "\n";
		}

        // front page 3 button
        // check for change in front-page 3 button background
		if ( ! empty( $data['front-page-3-btn-back'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-3 .button:before, ' . $class . ' .front-page-3 .button:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-3-btn-back'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-3-btn-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// check for change in button background hover
		if ( ! empty( $data['front-page-3-btn-back-hov'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-3 .button:hover:before, ' . $class . ' .front-page-3 .button:hover:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-3-btn-back-hov'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-3-btn-back-hov'] ) . "\n";
			$setup  .= '}' . "\n";
		}

        // front page 4 button
        // check for change in front-page 4 button background
		if ( ! empty( $data['front-page-4-btn-back'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-4 .button:before, ' . $class . ' .front-page-4 .button:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-4-btn-back'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-4-btn-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// check for change in button background hover
		if ( ! empty( $data['front-page-4-btn-back-hov'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-4 .button:hover:before, ' . $class . ' .front-page-4 .button:hover:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-4-btn-back-hov'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-4-btn-back-hov'] ) . "\n";
			$setup  .= '}' . "\n";
		}

        // front page 5 button
        // check for change in front-page 5 button background
		if ( ! empty( $data['front-page-5-btn-back'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-5 .button:before, ' . $class . ' .front-page-5 .button:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-5-btn-back'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-5-btn-back'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// check for change in button background hover
		if ( ! empty( $data['front-page-5-btn-back-hov'] ) ) {

			// the actual CSS entry
			$setup  .= $class . ' .front-page-5 .button:hover:before, ' . $class . ' .front-page-5 .button:hover:after { ';
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-top-color', $data['front-page-5-btn-back-hov'] ) . "\n";
			$setup  .= GP_Pro_Builder::hexcolor_css( 'border-bottom-color', $data['front-page-5-btn-back-hov'] ) . "\n";
			$setup  .= '}' . "\n";
		}

		// return the setup array
		return $setup;
	}

} // end class GP_Pro_Wellness_Pro

} // if ! class_exists

// Instantiate our class
$GP_Pro_Wellness_Pro = GP_Pro_Wellness_Pro::getInstance();
