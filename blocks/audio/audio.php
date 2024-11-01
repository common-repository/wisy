<?php

if ( ! class_exists('Wisy_Block_Audio') )
{

class Wisy_Block_Audio extends Wisy_Block_Base
{
	function __construct()
	{}

	public function get_name()
	{
		return 'audio';
	}

	public function get_title()
	{
		return esc_html__( 'Audio', 'wisy' );
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
			'audio' => [
				'title' => esc_html__( 'Audio', 'wisy' ),
				'type' => 'video',
				'media_type' => "['audio']",
				'button_txt' => esc_html__( 'Select Audio', 'wisy' ),
				'section' => 'general'
			],
			'player' => [
				'title' => esc_html__( 'Video Player', 'wisy' ),
				'type' => 'select',
				'section' => 'general',
				'values' => [
					'' => esc_html__( 'Default', 'wisy' ),
					'plyr' => esc_html__( 'Plyr (plyr.io)', 'wisy' )
				]
			],
			// Style section
			'color' => [
				'title' => esc_html__( 'Player Color', 'wisy' ),
				'type' => 'color',
				'section' => 'style',
				'css' => [
					'SELECTOR>.video-cont .plyr__control--overlaid, SELECTOR>.video-cont .plyr__control:hover, SELECTOR>.video-cont .plyr__control.plyr__tab-focus, SELECTOR>.video-cont .plyr__control[aria-expanded="true"]' => 'background-color:VALUE;',
					'SELECTOR>.video-cont .plyr--full-ui input[type="range"]' => 'color:VALUE;',
				]
			],
			'border_radius' => [
				'title' => esc_html__( 'Border Radius', 'wisy' ),
				'type' => 'borderRadius',
				'section' => 'style',
				'css' => [
					'SELECTOR>.video-cont' => 'border-radius:VALUE;'
				]
			],
		);
	}

	public function block( $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$html = '';

		$player_classes = 'wisy-media-player';
		if ( $atts['player'] == 'plyr' )
		{
			$player_classes .= ' plyr-player';
		}

		if ( filter_var( $atts['audio'], FILTER_VALIDATE_URL ) )
		{
			$mime_type = get_headers( $atts['audio'], 1 )['Content-Type'];
			$html .= '<audio class="' . $player_classes . '" crossorigin playsinline controls>';
				$html .= '<source src="' . $atts['audio'] . '" type="' . $mime_type . '" />';
			$html .= '</audio>';
		}

		return $html;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Audio');

} // end if
