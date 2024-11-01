<?php

if ( ! class_exists('Wisy_Block_Image') )
{

class Wisy_Block_Image extends Wisy_Block_Base
{
	public function get_name()
	{
		return 'image';
	}

	public function get_title()
	{
		return esc_html__( 'Image', 'wisy' );
	}

	public function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
			],
			'style' => [
				'title' => esc_html__( 'Style', 'wisy' )
			]
		);
	}

	public function settings()
	{
		return array(
			'img' => [
				'title' => esc_html__( 'Image', 'wisy' ),
				'type' => 'image',
				'media_type' => "['image']",
				'button_txt' => esc_html__( 'Select Image', 'wisy' ),
				'section' => 'general'
			],
			'title' => [
				'title' => esc_html__( 'Image Title', 'wisy' ),
				'type' => 'text',
				'section' => 'general'
			],
			'link' => [
				'title' => esc_html__( 'Link', 'wisy' ),
				'type' => 'link',
				'section' => 'general',
				'default' => [
					'url' => '',
					'new_window' => '',
					'nofollow' => ''
				]
			],
			'width' => [
				'title' => esc_html__( 'Image Width', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'css' => [
					'SELECTOR img' => 'width:VALUE;'
				]
			],
			// Style section
			'border_radius' => [
				'title' => esc_html__( 'Border Radius', 'wisy' ),
				'type' => 'borderRadius',
				'section' => 'style',
				'css' => [
					'SELECTOR img' => 'border-radius:VALUE;'
				]
			],
			'b_width' => [
				'title' => esc_html__( 'Border Width', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'style',
				'group' => 'border',
				'default' => '0',
				'css' => [
					'SELECTOR img' => 'border-width:VALUE;'
				]
			],
			'b_color' => [
				'title' => esc_html__( 'Border Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'border',
				'args' => [
					'hover' => true
				],
				'css' => [
					'SELECTOR img' => 'border-color:VALUE;'
				]
			],
			'b_style' => [
				'title' => esc_html__( 'Border Style', 'wisy' ),
				'type' => 'select',
				'section' => 'style',
				'group' => 'border',
				'default' => 'solid',
				'values' => [
					'solid' => esc_html__( 'Solid', 'wisy' )
				],
				'css' => [
					'SELECTOR img' => 'border-style:VALUE;'
				]
			]
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts($atts);
		$html = '';

		if ( ! empty( $atts['link']['url'] ) )
		{
			$html .= '<a href="'.$atts['link']['url'].'"';
			$html .= ($atts['link']['new_window'] == 'on') ? ' target="_blank"' : '';
			$html .= ($atts['link']['nofollow'] == 'on') ? ' rel="nofollow"' : '';
			$html .= '>';
		}

		if ( filter_var( $atts['img'], FILTER_VALIDATE_URL ) )
		{
			$html .= '<img src="' . $atts['img'] . '" title="' . $atts['title'] . '" alt="' . $atts['title'] . '"/>';
		}

		$html .= ( ! empty( $atts['link']['url'] ) ) ? '</a>' : '';

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Image');

} // end if
