<?php

if ( ! class_exists('Wisy_Block_Icon') )
{

class Wisy_Block_Icon extends Wisy_Block_Base
{
	function __construct()
	{}

	public function get_name()
	{
		return 'icon';
	}

	public function get_title()
	{
		return esc_html__( 'Icon', 'wisy' );
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
			'i_class' => [
				'title' => esc_html__( 'Icon', 'wisy' ),
				'type' => 'icon',
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
			'size' => [
				'title' => esc_html__( 'Icon Size', 'wisy' ),
				'type' => 'fontSize',
				'section' => 'style',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR a' => 'font-size:VALUEpx;'
				]
			],
			// Colors Group
			'bg' => [
				'title' => esc_html__( 'Background Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'default' => '',
				'args' => [
					'hover' => true
				],
				'css' => [
					'SELECTOR a' => 'background-color:VALUE;'
				]
			],
			'color' => [
				'title' => esc_html__( 'Icon Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'default' => '#333',
				'args' => [
					'hover' => true,
				],
				'css' => [
					'SELECTOR a' => 'color:VALUE;'
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
					'SELECTOR a' => 'border-radius:VALUE;'
				],
			],
			'padding' => [
				'title' => esc_html__( 'Icon Padding', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'default' => '10px',
				'css' => [
					'SELECTOR a' => 'padding:VALUE;'
				]
			],
			'margin' => [
				'title' => esc_html__( 'Icon Margin', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'default' => '',
				'css' => [
					'SELECTOR a' => 'margin:VALUE;'
				]
			],
			'b_width' => [
				'title' => esc_html__( 'Border Width', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'style',
				'group' => 'border',
				'default' => '0',
				'css' => [
					'SELECTOR a' => 'border-width:VALUE;'
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
					'SELECTOR a' => 'border-color:VALUE;'
				]
			],
			'b_style' => [
				'title' => esc_html__( 'Border Style', 'wisy' ),
				'type' => 'select',
				'section' => 'style',
				'group' => 'border',
				'default' => 'none',
				'values' => [
					'none' => esc_html__( 'None', 'wisy' ),
					'solid' => esc_html__( 'Solid', 'wisy' ),
					'dashed' => esc_html__( 'Dashed', 'wisy' ),
					'dotted' => esc_html__( 'Dotted', 'wisy' )
				],
				'css' => [
					'SELECTOR a' => 'border-style:VALUE;'
				]
			]
		);
	}

	public function block( $atts = [] )
	{
		$href = esc_url( $atts['link']['url'] );
		$new_window = esc_textarea( $atts['link']['new_window'] );
		$icon_class = esc_attr( $atts['i_class'] );

		$html = "<a";
		$html .= ( ! empty( $href ) ) ? ' href="' . $href . '"' : '';
		$html .= ( $new_window == 'on' ) ? ' target="_blank"' : '';
		$html .= ' class="button ' . $icon_class . '"></a>';

		return $html;
	}

	public function enqueue_scripts( $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$scripts = [];

		if ( isset( $atts['i_class'] ) )
		{
			if ( strpos( $atts['i_class'], 'feather' ) === 0 )
			{
				$scripts[] = [
					'handle' => 'feather',
					'url' => WISY_URL . 'assets/fonts/feather/feather.min.css',
					'type' => 'css'
				];
			}
			elseif ( strpos( $atts['i_class'], 'fa ' ) === 0 )
			{
				$scripts[] = [
					'handle' => 'font-awesome',
					'url' => WISY_URL . 'assets/fonts/font-awesome/font-awesome.min.css',
					'type' => 'css'
				];
			}
		}

		return $scripts;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Icon');

} // end if
