<?php

if ( ! class_exists('Wisy_Block_Button') )
{

class Wisy_Block_Button extends Wisy_Block_Base
{
	function __construct()
	{}

	public function get_name()
	{
		return 'button';
	}

	public function get_title()
	{
		return esc_html__( 'Button', 'wisy' );
	}

	public function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
			],
			'style' => [
				'title' => esc_html__( 'Style', 'wisy' ),
				'groups' => array(
					'colors' => esc_html__( 'Colors', 'wisy' ),
					'typography' => esc_html__( 'Typography', 'wisy' ),
					'border' => esc_html__( 'Border', 'wisy' ),
				)
			],
			'advanced' => [
				'title' => esc_html__( 'Advanced', 'wisy' ),
			]
		);
	}

	public function settings()
	{
		return array(
			'content' => [
				'title' => esc_html__( 'Button Text', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'default' => esc_html__( 'Click Button', 'wisy' )
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
			'icon' => [
				'title' => esc_html__( 'Icon', 'wisy' ),
				'type' => 'icon',
				'section' => 'general'
			],
			'icon_pos' => [
				'title' => esc_html__( 'Icon Position', 'wisy' ),
				'type' => 'select',
				'section' => 'general',
				'default' => 'left',
				'values' => [
					'left' => esc_html__( 'Left', 'wisy' ),
					'right' => esc_html__( 'Right', 'wisy' )
				]
			],
			// Colors Group
			'bg' => [
				'title' => esc_html__( 'Background Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'default' => '#106eef',
				'args' => [
					'hover' => true
				],
				'css' => [
					'SELECTOR a.button' => 'background-color:VALUE;'
				]
			],
			'color' => [
				'title' => esc_html__( 'Text Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'default' => 'white',
				'args' => [
					'hover' => true,
				],
				'css' => [
					'SELECTOR a.button' => 'color:VALUE;'
				]
			],
			// Typography Group
			'f_size' => [
				'title' => esc_html__( 'Font Size', 'wisy' ),
				'type' => 'fontSize',
				'section' => 'style',
				'group' => 'typography',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR a.button' => 'font-size:VALUEpx;'
				]
			],
			'f_family' => [
				'title' => esc_html__( 'Font Family', 'wisy' ),
				'type' => 'fontFamily',
				'section' => 'style',
				'group' => 'typography',
				'css' => [
					'SELECTOR a.button' => 'font-family:VALUE;'
				]
			],
			'f_weight' => [
				'title' => esc_html__( 'Font Weight', 'wisy' ),
				'type' => 'fontWeight',
				'parent' => 'f_family',
				'section' => 'style',
				'group' => 'typography',
				'css' => [
					'SELECTOR a.button' => 'font-weight:VALUE;'
				]
			],
			'b_radius' => [
				'title' => esc_html__( 'Border Radius', 'wisy' ),
				'type' => 'borderRadius',
				'section' => 'style',
				'default' => '',
				'args' => [
					'hover' => true
				],
				'css' => [
					'SELECTOR a.button' => 'border-radius:VALUE;'
				],
			],
			'btn_padding' => [
				'title' => esc_html__( 'Button Padding', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'default' => '10px 30px 10px 30px',
				'css' => [
					'SELECTOR a.button' => 'padding:VALUE;'
				]
			],
			'btn_margin' => [
				'title' => esc_html__( 'Button Margin', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'default' => '0',
				'css' => [
					'SELECTOR a.button' => 'margin:VALUE;'
				]
			],
			'b_width' => [
				'title' => esc_html__( 'Border Width', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'style',
				'group' => 'border',
				'default' => '0',
				'css' => [
					'SELECTOR a.button' => 'border-width:VALUE;'
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
					'SELECTOR a.button' => 'border-color:VALUE;'
				]
			],
			'b_style' => [
				'title' => esc_html__( 'Border Style', 'wisy' ),
				'type' => 'select',
				'section' => 'style',
				'group' => 'border',
				'default' => 'solid',
				'values' => [
					'solid' => esc_html__( 'Solid', 'wisy' ),
					'dashed' => esc_html__( 'Dashed', 'wisy' ),
					'dotted' => esc_html__( 'Dotted', 'wisy' )
				],
				'css' => [
					'SELECTOR a.button' => 'border-style:VALUE;'
				]
			]
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$href = esc_url( $atts['link']['url'] );
		$new_window = esc_textarea( $atts['link']['new_window'] );
		$icon_class = esc_attr( $atts['icon'] );
		$icon_pos = esc_attr( $atts['icon_pos'] );
		$html = '';

		$html .= "<a";
		$html .= ( ! empty( $href ) ) ? ' href="' . $href . '"' : '';
		$html .= ( $new_window == 'on' ) ? ' target="_blank"' : '';
		$html .= ' class="button pos-' . $icon_pos . '">';
		$html .= !empty( $icon_class ) ? '<i class="wisy-icon ' . $icon_class . '"></i>' : '';
		$html .= $atts['content'] . '</a>';

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Button');

} // end if
