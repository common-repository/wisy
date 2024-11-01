<?php

if ( ! class_exists('Wisy_Block_Shortcode') )
{

class Wisy_Block_Shortcode extends Wisy_Block_Base
{
	public function get_name()
	{
		return 'shortcode';
	}

	public function get_title()
	{
		return esc_html__( 'Shortcode', 'wisy' );
	}

	public function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
			],
		);
	}

	public function settings()
	{
		return array(
			'shortcode' => [
				'title' => esc_html__( 'Shortcode', 'wisy' ),
				'type' => 'textarea',
				'section' => 'general'
			]
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts($atts);
		$html = '';

		if ( function_exists('do_shortcode') )
		{
			$html .= do_shortcode( $atts['shortcode'] );
		}

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Shortcode');

} // end if
