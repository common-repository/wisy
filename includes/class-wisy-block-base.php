<?php

/*
* Wisy widgets base
* @since 0.1.0
*/

if ( ! defined('ABSPATH') ) { exit; } // Exit if access directly

if ( ! class_exists('Wisy_Block_Base') )
{

class Wisy_Block_Base
{

	private $inside_editor = false;

	/*
	* Class constructor
	*/
	public function __construct() {}

	/*
	* Block frontend
	*/
	public function block_frontend( $atts = [], $inside_editor = false )
	{
		$atts = $this->get_atts( $atts );
		$this->inside_editor = $inside_editor;

		$block_id = wisy_uniqid( 15, 'wisy_' );

		if ( isset( $atts['_id'] ) && ! empty( $atts['_id'] ) )
		{
			$block_id = 'wisy_' . $atts['_id'];
		}

		$get_styles = $this->get_styles( $block_id, $atts );
		$styles = $get_styles['style'];

		$styles .= '@media screen and (max-width: 768px) {';
			$styles .= $get_styles['style_md'];
		$styles .= '}';

		$styles .= '@media screen and (max-width: 360px) {';
			$styles .= $get_styles['style_sm'];
		$styles .= '}';

		$html = '';

		// if it's not inside the editor
		if ( ! $this->inside_editor && ! wisy_load_for_editor() )
		{
			$html .= "<div id='{$block_id}' class='" . $this->get_css_classes( $atts ) . "'>";
		}
			// if it's inside the editor
			if ( $this->inside_editor || wisy_load_for_editor() )
			{
				$html .= '<style>' . $styles . '</style>';
			}

			$html .= '<div class="block-cont ' . $this->get_name() . '-cont">';
				$html .= $this->block( $atts );
			$html .= '</div>';

		// if it's not inside the editor
		if ( ! $this->inside_editor && ! wisy_load_for_editor() )
		{
			$html .= '</div>';
		}

		return $html;
	}

	public function get_styles( $block_id, $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$styles_arr = ['default' => [], '_md' => [], '_sm' => []];
		$styles =  ['style' => '', 'style_md' => '', 'style_sm' => ''];
		$custom_css = '';

		foreach ( $this->get_settings() as $name => $data )
		{
			$data = array_merge(
				[
					'title' => '',
					'type' => '',
					'args' => [
						'hover' => false,
						'responsive' => false
					],
					'css' => []
				],
				$data
			);

			if ( $name == '_css' )
			{
				$custom_css .= str_replace( 'SELECTOR', '#' . $block_id, $atts[ $name ] );
				continue;
			}

			if ( $data['type'] == 'image' && $data['args']['responsive'] && wisy_get_image_id( $atts[ $name ] ) )
			{
				$medium_img = wp_get_attachment_image_src( wisy_get_image_id( $atts[ $name ] ), 'large' );
				if ( empty( $atts['_md'][ $name ] ) && $medium_img )
				{
					$atts['_md'][ $name ] = $medium_img[0];
				}

				$small_img = wp_get_attachment_image_src( wisy_get_image_id( $atts[ $name ] ), 'medium_large' );
				if ( empty( $atts['_sm'][ $name ] ) && $small_img )
				{
					$atts['_sm'][ $name ] = $small_img[0];
				}
			}

			foreach ( $data['css'] as $key => $value )
			{
				if ( is_callable( $value ) )
				{
					$value = $value( $atts[ $name ] );
				}

				if ( ! empty( $atts[ $name ] ) )
				{
					$styles_arr['default'][ $key ][] = str_replace( 'VALUE', $atts[ $name ], $value );
				}

				if ( isset( $atts['_hov'][ $name ] ) && ! empty( $atts['_hov'][ $name ] ) )
				{
					$styles_arr['default'][$key . ':hover'][] = str_replace('VALUE', $atts['_hov'][ $name ], $value);
				}

				if ( isset( $data['args']['responsive'] ) && $data['args']['responsive'] )
				{
					if ( ! empty( $atts['_md'][$name] ) )
					{
						$styles_arr['_md'][$key][] = str_replace('VALUE', $atts['_md'][$name], $value);
					}
					if ( ! empty( $atts['_sm'][$name] ) )
					{
						$styles_arr['_sm'][$key][] = str_replace('VALUE', $atts['_sm'][$name], $value);
					}
				}
			}
		}

		foreach ( $styles_arr['default'] as $selector => $css )
		{
			$styles['style'] .= str_replace( 'SELECTOR', '#'.$block_id, $selector ) . '{' . implode( '', $css ) . '}';
		}
		$styles['style'] .= str_replace( '<br/>', '', $custom_css );

		foreach ( $styles_arr['_md'] as $selector => $css )
		{
			$styles['style_md'] .= str_replace( 'SELECTOR', '#' . $block_id, $selector ) . '{' . implode( '', $css ) . '}';
		}

		foreach ( $styles_arr['_sm'] as $selector => $css )
		{
			$styles['style_sm'] .= str_replace( 'SELECTOR', '#' . $block_id, $selector ) . '{' . implode( '', $css ) . '}';
		}

		return $styles;
	}

	public function fonts_names( $atts = [], $return_type = '' )
	{
		$atts = $this->get_atts( $atts );
		$fonts = [];

		foreach ( $this->get_settings() as $name => $data )
		{

			if ( $data['type'] == 'fontFamily' && isset( $atts[$name] ) )
			{
				$font_name = str_replace( ' ', '+', $atts[ $name ] );
				$fonts[ $font_name ] = isset( $fonts[ $font_name ] ) ? $fonts[ $font_name ] : [];
			}

			if ( $data['type'] == 'fontWeight' && isset( $atts[ $name ] ) && isset( $data['parent'] ) && isset( $atts[ $data['parent'] ] ) )
			{
				$font_name = str_replace( ' ', '+', $atts[ $data['parent'] ] );
				$fonts[ $font_name ] = isset( $fonts[ $font_name ] ) ? $fonts[ $font_name ] : [];
				$fonts[ $font_name ][] = $atts[ $name ];
			}

		}

		if ( $return_type == 'array' )
		{
			return $fonts;
		}
		else
		{
			$fonts_url = [];
			foreach ( $fonts as $font_name => $font_weight )
			{
				if ( ! empty( $font_name ) )
				{
					$fonts_url[] = $font_name . ':' . implode( ',', $font_weights );
				}
			}
			return implode( '|', $fonts_url );
		}
	}

	public function get_type()
	{
		return 'widget';
	}

	/*
	* Get widget name (slug)
	* @since 0.1.0
	* @access public
	*/
	public function get_name()
	{
		return '';
	}

	/*
	* Get widget title
	* @since 0.1.0
	* @access public
	*/
	public function get_title()
	{
		return '';
	}

	/*
	* Widget sections
	* @since 0.1.0
	* @access public
	*/
	public function get_sections()
	{
		$sections = apply_filters( 'wisy_builder_block_sections', $this->sections(), $this->get_name(), $this->get_type() );
		return $sections;
	}

	public function sections()
	{
		return array();
	}

	/*
	* Widget settings
	* @since 0.1.0
	* @access public
	*/
	public function get_settings()
	{
		$settings = apply_filters( 'wisy_builder_block_settings', $this->settings(), $this->get_name(), $this->get_type() );
		return $settings;
	}

	public function settings()
	{
		return array();
	}

	public function group_blocks()
	{
		return array();
	}

	public function get_atts( $atts = [] )
	{
		$atts = json_decode( json_encode( $atts ), true );
		$def_atts = array( '_id' => wisy_uniqid(), '_md' => [], '_sm' => [], '_hov' => [] );

		foreach ( $this->get_settings() as $name => $setting )
		{
			$def_atts[ $name ] = isset( $setting['default'] ) ? $setting['default'] : '';

			if ( isset( $setting['args']['responsive'] ) && $setting['args']['responsive'] )
			{
				$def_atts['_md'][ $name ] = '';
				$def_atts['_sm'][ $name ] = '';
			}
		}

		$final_atts = is_array( $atts ) ? wisy_array_merge($def_atts, $atts) : $def_atts;
		array_walk( $final_atts, 'wisy_decode_characters' );

		return $final_atts;
	}

	/*
	* Block frontend
	* @since 0.1.0
	* @access
	*/
	public function block( $atts = [] )
	{
		return '';
	}

	public function get_css_classes( $atts )
	{
		$atts = $this->get_atts( $atts );

		$classes = apply_filters( 'wisy_block_frontend_classes', [], $this, $atts );
		$classes = is_array( $classes ) ? $classes : [];
		$classes = array_merge(
			$classes,
			[
				'wisy-block',
				'wisy-' . $this->get_type(),
				$this->get_type() . '-' . $this->get_name()
			]
		);
		$classes = is_array( $this->css_classes( $atts ) ) ? array_merge( $this->css_classes($atts), $classes ) : $classes;

		return implode( ' ', $classes );
	}

	public function css_classes( $atts = [] )
	{
		return [];
	}

	/*
	* Block icon
	* @since 0.1.0
	* @access public
	*/
	public function get_icon_url()
	{
		return WISY_URL . 'blocks/' . $this->get_name() . '/icon.png';
	}

	/*
	* Enqueue scripts & styles for block
	* @since 0.2.0
	* @access public
	*/
	public function enqueue_scripts( $atts = [] )
	{
		return [];
	}
} // end class

} // end if