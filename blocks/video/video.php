<?php

if ( ! class_exists('Wisy_Block_Video') )
{

class Wisy_Block_Video extends Wisy_Block_Base
{
	public function get_name()
	{
		return 'video';
	}

	public function get_title()
	{
		return esc_html__( 'Video', 'wisy' );
	}

	public function sections()
	{
		return array(
			'general' => [
				'title' => esc_html__( 'General', 'wisy' ),
				'groups' => [
					'yt_vid' => esc_html__( 'YouTube Video', 'wisy' ),
					'vid_from_url' => esc_html__( 'Video From URL', 'wisy' )
				]
			],
			'style' => [
				'title' => esc_html__( 'Style', 'wisy' )
			]
		);
	}

	public function settings()
	{
		return array(
			'vid_src' => [
				'title' => esc_html__( 'Video Source', 'wisy' ),
				'type' => 'select',
				'section' => 'general',
				'values' => [
					'url' => esc_html__( 'Local/External', 'wisy' ),
					'yt' => esc_html__( 'YouTube', 'wisy' )
				]
			],
			'vid' => [
				'title' => esc_html__( 'Video', 'wisy' ),
				'type' => 'video',
				'media_type' => "['video']",
				'button_txt' => esc_html__( 'Select Video', 'wisy' ),
				'section' => 'general',
				'group' => 'vid_from_url'
			],
			'poster' => [
				'title' => esc_html__( 'Poster', 'wisy' ),
				'type' => 'image',
				'media_type' => "['image']",
				'button_txt' => esc_html__( 'Select Image', 'wisy' ),
				'section' => 'general',
				'group' => 'vid_from_url'
			],
			'vid_yt' => [
				'title' => esc_html__( 'YouTube Video', 'wisy' ),
				'type' => 'text',
				'placeholder' => esc_html__( 'Video URL', 'wisy' ),
				'section' => 'general',
				'group' => 'yt_vid'
			],
			'yt_privacy' => [
				'title' => esc_html__( 'Privacy-enhanced mode', 'wisy' ),
				'type' => 'switch',
				'value' => 'enabled',
				'section' => 'general',
				'group' => 'yt_vid',
				'text' => [
					'enabled' => esc_html__( 'Enabled', 'wisy' ),
					'disabled' => esc_html__( 'Disabled', 'wisy' ),
				]
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
					'SELECTOR>.video-cont .plyresc_html__control--overlaid, SELECTOR>.video-cont .plyresc_html__control:hover, SELECTOR>.video-cont .plyresc_html__control.plyresc_html__tab-focus, SELECTOR>.video-cont .plyresc_html__control[aria-expanded="true"]' => 'background-color:VALUE;',
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
		$atts = $this->get_atts($atts);
		$html = '';

		// Extract video ID from YouTube video URL
		preg_match(
			'/(?:[?&]v=|\/embed\/|\/1\/|\/v\/|https:\/\/(?:www\.)?youtu\.be\/)([^&\n?#]+)/',
			$atts['vid_yt'],
			$yt_vid_id
		);

		$yt = ( $atts['yt_privacy'] != 'enabled' ) ? 'youtube' : 'youtube-nocookie';

		$player_classes = 'wisy-media-player';
		if ( $atts['player'] == 'plyr' )
		{
			$player_classes .= ' plyr-player';
		}

		if ( $atts['vid_src'] == 'yt' && isset( $yt_vid_id[1] ) )
		{
			$html .= '<div class="' . $player_classes . '" data-plyr-provider="youtube" data-plyr-embed-id="' . $yt_vid_id[1] . '">';
				$html .= '<iframe src="//www.' . $yt . '.com/embed/' . $yt_vid_id[1] . '" frameborder="0" allow="fullscreen; accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			$html .= '</div>';
		}
		else if ( $atts['vid_src'] == 'url' && filter_var( $atts['vid'], FILTER_VALIDATE_URL ) )
		{
			$vid_mime_type = get_headers( $atts['vid'], 1 )['Content-Type'];
			$html .= '<video poster="' . $atts['poster'] . '" class="' . $player_classes . '" playsinline controls>';
				$html .= '<source src="' . $atts['vid'] . '" type="' . $vid_mime_type . '" />';
			$html .= '</video>';
		}

		return $html;
	}

	public function enqueue_scripts( $atts = [] )
	{
		$atts = $this->get_atts( $atts );
		$scripts = [];

		if ( $atts['player'] == 'plyr' )
		{
			$scripts[] = [
				'handle' => 'plyr',
				'url' => WISY_URL . 'assets/lib/plyr/plyr.min.js',
				'type' => 'js',
				'ver' => '3.5.6'
			];

			$scripts[] = [
				'handle' => 'plyr',
				'inline' => "document.addEventListener('DOMContentLoaded', function(event) { new Plyr('.wisy-media-player.plyr-player'); });",
				'type' => 'js'
			];

			$scripts[] = [
				'handle' => 'plyr',
				'url' => WISY_URL . 'assets/lib/plyr/plyr.min.css',
				'type' => 'css',
				'ver' => '3.5.6'
			];
		}

		return $scripts;
	}
} // end class

// Register this block
wisy_register_block('Wisy_Block_Video');

} // end if
