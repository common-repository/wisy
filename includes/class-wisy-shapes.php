<?php

if ( ! class_exists('Wisy_Shapes') ) {

class Wisy_Shapes
{
	public function __construct( $shape = '' )
	{
		$html = 'text';

		/*$shape_data = $this->$shape();
		if ( is_array($shape_data) ) {
			$html .= '<svg';
			if ( is_array($shape_data['attributes']) ) {
				foreach ( $shape_data['attributes'] as $name => $value ) {
					$html .= ' '.$name.'="'.$value.'"';
				}
			}
			$html .= '></svg>';
		}*/

		return $html;
	}

	/*
	*
	*/
	public function zigzag() {}

	/*
	*
	*/
	public function curve()
	{
		return [
			'attrributes' => [
				'viewBox' => '0 0 1280 100'
			],
			'contents' => [
				[
					'tag' => 'path',
					'attributes' => [
						'd' => ''
					]
				]
			]
		];
	}
} // end class

}
