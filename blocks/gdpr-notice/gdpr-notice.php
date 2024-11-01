<?php

if ( ! class_exists('Wisy_Block_GDPR_Notice') )
{

class Wisy_Block_GDPR_Notice extends Wisy_Block_Base
{
	function __construct()
	{}

	function get_name()
	{
		return 'gdpr-notice';
	}

	function get_title()
	{
		return esc_html__( 'GDPR Notice', 'wisy' );
	}

	function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
			],
			'style' => [
				'title' => esc_html__( 'Style', 'wisy' ),
			]
		);
	}

	function settings()
	{
		return array(
			'txt' => [
				'title' => esc_html__( 'Notice Text', 'wisy' ),
				'type' => 'textarea',
				'section' => 'general',
				'default' => sprintf(
					/* Translators: 1: Learn More Link */
					__( 'We\'re using cookies in this website. <a href="%s">Learn More</a>', 'wisy' ),
					'#'
				)
			],
			'btn_txt' => [
				'title' => esc_html__( 'Button Text', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'default' => esc_html__( 'Accept', 'wisy' )
			],
			'pos' => [
				'title' => esc_html__( 'Position', 'wisy' ),
				'type' => 'select',
				'section' => 'general',
				'values' => [
					'' => esc_html__( 'Bottom', 'wisy' ),
					'top' => esc_html__( 'Top', 'wisy' )
				]
			],
			// Style section
			'box_bg' => [
				'title' => esc_html__( 'Background', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'default' => '#fff',
				'css' => [
					'SELECTOR' => 'background:VALUE;'
				]
			],
			'box_color' => [
				'title' => esc_html__( 'Text Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'default' => '#333',
				'css' => [
					'SELECTOR .notice-txt, SELECTOR .close-btn' => 'color:VALUE;'
				]
			],
			'btn_bg' => [
				'title' => esc_html__( 'Button Background', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'default' => '#571af9',
				'css' => [
					'SELECTOR .accept-btn' => 'background:VALUE;'
				]
			],
			'btn_color' => [
				'title' => esc_html__( 'Button Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'default' => '#fff',
				'css' => [
					'SELECTOR .accept-btn' => 'color:VALUE;'
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
					'SELECTOR *' => 'font-size:VALUEpx;'
				]
			],
			'f_family' => [
				'title' => esc_html__( 'Font Family', 'wisy' ),
				'type' => 'fontFamily',
				'section' => 'style',
				'group' => 'typography',
				'default' => '',
				'css' => [
					'SELECTOR *' => 'font-family:VALUE;'
				]
			],
		);
	}

	function block( $atts = [] )
	{
		$atts = $this->get_atts( $atts );

		$html = '<p class="notice-txt">' . $atts['txt'] . '</p>';
		$html .= '<button type="button" class="btn accept-btn">' . $atts['btn_txt'] . '</button>';
		$html .= '<button type="button" class="btn close-btn">x</button>';

		return $html;
	}

	function css_classes( $atts = [] )
	{
		$atts = $this->get_atts($atts);
		$classes = [];

		if ( $atts['pos'] == 'top' )
		{
			$classes[] = 'position-top';
		}

		if ( isset($_COOKIE['wisy_gdpr_accepted']) && $_COOKIE['wisy_gdpr_accepted'] == '1' && ! wisy_load_for_editor() && ! wisy_is_editor() )
		{
			$classes[] = 'hidden';
		}

		return $classes;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_GDPR_Notice');

} // end if
