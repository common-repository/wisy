<?php

/*
* Wisy editor class
* @since 0.1.0
*/

if ( ! defined('ABSPATH') ) { exit; } // Exit if access directly

if ( ! class_exists('Wisy_Frontend') )
{

class Wisy_Frontend
{
	public function blocks( $blocks = [] )
	{
		$html = '';

		if ( ! is_array( $blocks ) )
		{
			return $html;
		}

		$blocks_list = wisy_get_blocks();

		foreach ( $blocks as $key => $block )
		{

			if ( ! isset( $blocks_list[ $block['name'] ] ) || ! is_string( $blocks_list[ $block['name'] ] ) || ! class_exists( $blocks_list[ $block['name'] ] ) )
			{
				continue;
			}

			$block_class = new $blocks_list[ $block['name'] ]();
			$block_atts = isset( $block['atts'] ) ? $block_class->get_atts( $block['atts'] ) : $block_class->get_atts( [] );
			$block_children = '';

			if ( isset( $block['children'] ) && is_array( $block['children'] ) )
			{
				$block_children = $this->blocks( $block['children'] );
			}

			$html .= str_replace(
				'{block_children}',
				$block_children,
				$block_class->block_frontend( $block_atts )
			);

		}

		return $html;
	}
} // end class

} // end if