<?php

if ( ! class_exists('Wisy_Block_Text') )
{

class Wisy_Block_Text extends Wisy_Block_Base
{
	public function get_name()
	{
		return 'text';
	}

	public function get_title()
	{
		return esc_html__( 'Text', 'wisy' );
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
					'typography' => esc_html__( 'Typography', 'wisy' )
				]
			]
		);
	}

	public function settings()
	{
		return array(
			'content' => [
				'title' => esc_html__( 'Text', 'wisy' ),
				'type' => 'textarea',
				'section' => 'general',
				'default' => 'Sample Text'
			],
			'tag' => [
				'title' => esc_html__( 'HTML tag', 'wisy' ),
				'type' => 'select',
				'section' => 'general',
				'default' => 'p',
				'values' => [
					'p' => 'P',
					'div' => 'DIV',
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				]
			],
			'color' => [
				'title' => esc_html__( 'Text Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'css' => [
					'SELECTOR>.text-cont>*' => 'color:VALUE;'
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
					'SELECTOR>.text-cont>*' => 'font-size:VALUEpx;'
				]
			],
			'f_family' => [
				'title' => esc_html__( 'Font Family', 'wisy' ),
				'type' => 'fontFamily',
				'section' => 'style',
				'group' => 'typography',
				'css' => [
					'SELECTOR>.text-cont>*' => 'font-family:VALUE;'
				]
			],
			'f_weight' => [
				'title' => esc_html__( 'Font Weight', 'wisy' ),
				'type' => 'fontWeight',
				'section' => 'style',
				'group' => 'typography',
				'css' => [
					'SELECTOR>.text-cont>*' => 'font-weight:VALUE;'
				]
			],
			'l_height' => [
				'title' => esc_html__( 'Line Height', 'wisy' ),
				'type' => 'lineHeight',
				'section' => 'style',
				'group' => 'typography',
				'args' => [
					'responsive' => true
				],
				'css' => [
					'SELECTOR>.text-cont>*' => 'line-height:VALUE;'
				]
			]
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts($atts);
		$allowed_tags = ['div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'];
		$atts['tag'] = in_array( $atts['tag'], $allowed_tags ) ? $atts['tag'] : 'p';

		$html = '<' . $atts['tag'] . ' dir="auto">';
			$html .= strip_tags( $atts['content'], '<br>' );
		$html .= '</' . $atts['tag'] . '>';

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Text');

} // end if
