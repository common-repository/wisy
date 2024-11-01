<?php

if ( ! class_exists('Wisy_Block_Column') )
{

class Wisy_Block_Column extends Wisy_Block_Base
{
	function __construct()
	{}

	function get_type()
	{
		return 'column';
	}

	function get_name()
	{
		return 'column';
	}

	function get_title()
	{
		return esc_html__( 'Column', 'wisy' );
	}

	function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' )
			],
			'advanced' => [
				'title' => esc_html__( 'Advanced', 'wisy' )
			]
		);
	}

	function settings()
	{
		return array(
			'width' => [
				'title' => esc_html__( 'Width', 'wisy' ),
				'type' => 'text',
				'section' => 'general',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR' => 'width:VALUE;flex-basis:VALUE;'
				]
			]
		);
	}

	//<div class="block-cont column-cont"><div class="before-blocks-container" data-blocks-types="row,widget"></div><div class="wisy-block-children">{block_children}</div><div class="after-blocks-container" data-blocks-types="row,widget"></div></div>

	function block( $atts = [] )
	{
		$html = '<div class="before-blocks-container" data-blocks-types="row,widget"></div>';
			$html .= '<div class="wisy-block-children">{block_children}</div>';
		$html .= '<div class="after-blocks-container" data-blocks-types="row,widget"></div>';

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Column');

} // end if
