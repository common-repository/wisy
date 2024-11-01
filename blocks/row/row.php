<?php

if ( ! class_exists('Wisy_Block_Row') )
{

class Wisy_Block_Row extends Wisy_Block_Base
{
	public function get_type()
	{
		return 'row';
	}

	public function get_name()
	{
		return 'row';
	}

	public function get_title()
	{
		return esc_html__( 'Row', 'wisy' );
	}

	public function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
			],
			'style' => [
				'title' => esc_html__( 'Style', 'wisy' ),
				'groups' => [
					'background' => esc_html__( 'Background', 'wisy' ),
					'top_shape' => esc_html__( 'Top Shape', 'wisy' ),
					'bottom_shape' => esc_html__( 'Bottom Shape', 'wisy' )
				]
			],
			'advanced' => [
				'title' => esc_html__( 'Advanced', 'wisy' )
			]
		);
	}

	public function settings()
	{
		return array(
			'columns_xspace' => [
				'title' => esc_html__( 'Columns Horizontal Space', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'default' => '',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'body.wisy-builder-preview-mode #wisy-builder-frontend-editor-area SELECTOR>.wrap>.wisy-block-children>.wisy-column, SELECTOR>.wrap>.wisy-block-children>.wisy-column' => 'padding-right:calc(VALUE/2);padding-left:calc(VALUE/2);',
					//
					'body.wisy-builder-preview-mode #wisy-builder-frontend-editor-area SELECTOR>.wrap>.wisy-block-children, SELECTOR>.wrap>.wisy-block-children' => 'margin-right:calc(-VALUE/2);margin-left:calc(-VALUE/2);'
				],
			],
			'columns_yspace' => [
				'title' => esc_html__( 'Columns Vertical Space', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'default' => '',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'body.wisy-builder-preview-mode #wisy-builder-frontend-editor-area SELECTOR>.wrap>.wisy-block-children>.wisy-column, SELECTOR>.wrap>.wisy-block-children>.wisy-column' => 'padding-bottom:calc(VALUE/2);padding-top:calc(VALUE/2);',

					'body.wisy-builder-preview-mode #wisy-builder-frontend-editor-area SELECTOR>.wrap>.wisy-block-children, SELECTOR>.wrap>.wisy-block-children' => 'margin-bottom:calc(-VALUE/2);margin-top:calc(-VALUE/2);'
				],
			],
			'content_width' => [
				'title' => esc_html__( 'Content Width', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'default' => '',
				'css' => [
					'SELECTOR>.block-cont>.wrap' => 'width:VALUE;'
				]
			],
			'bg' => [
				'title' => esc_html__( 'Background Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'group' => 'background',
				'default' => '',
				'css' => [
					'SELECTOR>.block-cont' => 'background:VALUE;'
				]
			],
			'bg_img' => [
				'title' => esc_html__( 'Background Image', 'wisy' ),
				'type' => 'image',
				'media_type' => "['image']",
				'button_txt' => esc_html__( 'Select Image', 'wisy' ),
				'section' => 'style',
				'group' => 'background',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR::after' => 'background-image:url(VALUE);'
				]
			],
			'border_radius' => [
				'title' => esc_html__( 'Border Radius', 'wisy' ),
				'type' => 'borderRadius',
				'section' => 'style',
				'css' => [
					'SELECTOR::after, SELECTOR>.block-cont' => 'border-radius:VALUE;'
				]
			],
			'wide_mode' => [
				'title' => esc_html__( 'Wide Mode', 'wisy' ),
				'type' => 'switch',
				'value' => 'enabled',
				'text' => [
					'enabled' => esc_html__( 'Enabled', 'wisy' ),
					'disabled' => esc_html__( 'Disabled', 'wisy' ),
				],
				'section' => 'advanced'
			],
		);
	}

	function block( $atts = [] )
	{
		$atts = $this->get_atts($atts);

		$html = ''.
		'<div class="wrap">'.
			'<div class="before-blocks-container" data-blocks-types="column"></div>'.
			'<div class="wisy-block-children">{block_children}</div>'.
			'<div class="after-blocks-container" data-blocks-types="column"></div>'.
		'</div>';

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Row');

} // end if
