<?php

if ( ! class_exists('Wisy_Block_Countdown_Timer') )
{

class Wisy_Block_Countdown_Timer extends Wisy_Block_Base
{
	function __construct()
	{}

	public function get_name()
	{
		return 'countdown-timer';
	}

	public function get_title()
	{
		return esc_html__( 'Countdown Timer', 'wisy' );
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
			'date' => [
				'title' => esc_html__( 'End Date', 'wisy' ),
				'type' => 'text',
				'placeholder' => date("m/d/Y h:i:s A", strtotime("+1 month") ),
				'default' => date("m/d/Y h:i:s A", strtotime("+1 month") ),
				'section' => 'general'
			],
			'size' => [
				'title' => esc_html__( 'Numbers Size', 'wisy' ),
				'type' => 'fontSize',
				'section' => 'style',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR .countdown span' => 'font-size:VALUEpx;'
				]
			],
			'txt_size' => [
				'title' => esc_html__( 'Text Size', 'wisy' ),
				'type' => 'fontSize',
				'section' => 'style',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR p' => 'font-size:VALUEpx;'
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
					'SELECTOR .countdown span' => 'background-color:VALUE;'
				]
			],
			'color' => [
				'title' => esc_html__( 'Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'default' => '#333',
				'args' => [
					'hover' => true,
				],
				'css' => [
					'SELECTOR .countdown span' => 'color:VALUE;'
				]
			],
			'txt_color' => [
				'title' => esc_html__( 'Text Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'colors',
				'args' => [
					'hover' => true,
				],
				'css' => [
					'SELECTOR p' => 'color:VALUE;'
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
					'SELECTOR .countdown span' => 'border-radius:VALUE;'
				],
			],
			'padding' => [
				'title' => esc_html__( 'Numbers Padding', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR .countdown span' => 'padding:VALUE;'
				]
			],
			'margin' => [
				'title' => esc_html__( 'Unit Margin', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'advanced',
				'args' => [
					'responsive' => true
				],
				'default' => '',
				'css' => [
					'SELECTOR .countdown>div' => 'margin:VALUE;'
				]
			],
			'b_width' => [
				'title' => esc_html__( 'Border Width', 'wisy' ),
				'type' => 'boxSides',
				'section' => 'style',
				'group' => 'border',
				'default' => '0',
				'css' => [
					'SELECTOR .countdown span' => 'border-width:VALUE;'
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
					'SELECTOR .countdown span' => 'border-color:VALUE;'
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
					'SELECTOR .countdown span' => 'border-style:VALUE;'
				]
			]
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts( $atts );

		$html = '<div class="countdown" data-date="' . $atts['date'] . '">';
			$html .= '<div class="days"><span></span><p>' . esc_html__( 'Days', 'wisy' ) . '</p></div>';
			$html .= '<div class="hours"><span></span><p>' . esc_html__( 'Hours', 'wisy' ) . '</p></div>';
			$html .= '<div class="minutes"><span></span><p>' . esc_html__( 'Minutes', 'wisy' ) . '</p></div>';
			$html .= '<div class="seconds"><span></span><p>' . esc_html__( 'Seconds', 'wisy' ) . '</p></div>';
		$html .= '</div>';

		return $html;
	}

	public function enqueue_scripts( $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$scripts = [];

		$scripts[] = [
			'handle' => 'wisy-countdown-timer',
			'url' => WISY_URL . 'blocks/countdown-timer/countdown.js',
			'type' => 'js',
			'deps' => [
				'wisy-default-blocks'
			]
		];

		return $scripts;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Countdown_Timer');

} // end if
