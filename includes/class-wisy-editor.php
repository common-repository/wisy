<?php

/*
* Wisy editor class
* @since 0.1.0
*/

if ( ! defined('ABSPATH') ) { exit; } // Exit if access directly

if ( ! class_exists('Wisy_Editor') )
{

class Wisy_Editor
{

	/*
	* Class constructor
	*/
	public function __construct()
	{
		add_action( 'init', [$this, 'remove_admin_bar'], 9999 );
	}

	public function remove_admin_bar()
	{
		if ( ! wisy_load_for_editor() && ! wisy_is_editor() && ! wisy_is_editor('new') )
		{
			return;
		}

		add_filter( 'show_admin_bar', '__return_false' );

		wp_deregister_script( 'admin-bar' );
		wp_deregister_style( 'admin-bar' );
		remove_action( 'admin_init', '_wp_admin_bar_init' );
		remove_action( 'in_admin_header', 'wp_admin_bar_render' );
	}

	/**/
	public function editor_blocks( $post_id = 0 )
	{
		global $wisy_builder;
		$post_blocks = wisy_get_post_blocks( $post_id );

		$content = "<div id='wisy-builder-frontend-editor-area'>".
			'<div class="wisy-editor-rows">'.
				$this->get_editor_blocks( $post_blocks ).
			'</div>'.
		"<div class='wisy-builder-new-area'><button class='wisy-builder-new-row'>New Row</button></div></div>";

		return $content;
	}

	private function get_editor_blocks( $blocks = [] )
	{
		$html = '';
		$templates = $this->blocks_editor_templates();

		$blocks = json_decode( json_encode( $blocks ), true );
		if ( ! is_array( $blocks ) )
		{
			return $html;
		}

		$get_blocks = wisy_get_blocks();

		foreach ( $blocks as $key => $block )
		{

			if ( ! isset( $get_blocks[ $block['name'] ] ) || ! is_string( $get_blocks[ $block['name'] ]) || ! class_exists( $get_blocks[ $block['name'] ] ) )
			{
				continue;
			}

			if ( ! isset( $block['type'] ) )
			{
				$block['type'] = 'widget';
			}

			$block['atts']['_id'] = ( ! empty( $block['atts']['_id'] ) ) ? $block['atts']['_id'] : wisy_uniqid(15);

			$block_class = new $get_blocks[ $block['name'] ]();
			$editor_block_id = 'wisy_editor_'.$block['type'].'_'.$block['atts']['_id'];
			$block_id = 'wisy_' . $block['atts']['_id'];

			$block_children = ( isset( $block['children'] ) && is_array( $block['children'] ) ) ? $this->get_editor_blocks( $block['children'] ) : '';

			if ( $block['type'] == 'column' )
			{
				$editor_block_id = $block_id;
			}

			$html .= str_replace(
				[
					'{editor_block_id}',
					'{block_name}',
					'{block_type}',
					'{block_frontend}',
					'{block_classes}',
					'{block_children}',
					'{block_id}',
					'{block_atts}'
				],

				[
					$editor_block_id,
					$block['name'],
					$block['type'],
					$block_class->block_frontend( $block['atts'] ),
					$block_class->get_css_classes( $block['atts'] ),
					$block_children,
					$block_id,
					htmlentities( json_encode( $block['atts'] ) )
				],

				$templates[ 'editor_' . $block['type'] ]
			);

		}

		return $html;
	}

	public function blocks_editor_templates()
	{
		$templates = [];

		// Editor widget HTML template
		$templates['editor_widget'] = '<div id="{block_id}" class="wisy-editor-block wisy-editor-widget {block_classes}" data-name="{block_name}" data-type="{block_type}">' .
			'<div class="controls">'.
				'<span class="move ui-sortable-handle" title="' . esc_attr__( 'Move Widget', 'wisy' ) . '"><i class="feather icon-move"></i></span>' .
				'<span class="duplicate" title="' . esc_attr__( 'Duplicate Widget', 'wisy' ) . '"><i class="feather icon-copy"></i></span>' .
				'<span class="delete" title="' . esc_attr__( 'Delete Widget', 'wisy' ) . '"><i class="feather icon-trash"></i></span>' .
			'</div>'.

			'{block_frontend}'.

			'<div>'.
				'<textarea class="atts hidden">{block_atts}</textarea>'.
			'</div>'.
		'</div>';

		// Editor column HTML template
		$templates['editor_column'] = '<div id="{block_id}" class="wisy-editor-block wisy-editor-column {block_classes}" data-name="column" data-type="{block_type}">'.
			'<div class="controls">'.
				'<span class="move ui-sortable-handle" title="' . esc_attr__( 'Move Column', 'wisy' ) . '"><i class="feather icon-move"></i></span>'.
				'<span class="duplicate" title="' . esc_attr__( 'Duplicate Column', 'wisy' ) . '"><i class="feather icon-copy"></i></span>'.
				'<span class="delete" title="' . esc_attr__( 'Delete Column', 'wisy' ) . '"><i class="feather icon-trash"></i></span>'.
			'</div>'.

			'{block_frontend}'.

			'<div class="wisy-add-new-btn">'.
				'<button type="button"><i class="feather icon-plus"></i></button>'.
			'</div>'.
			'<div><textarea class="atts hidden">{block_atts}</textarea></div>'.
		'</div>';

		// Editor row HTML template
		$templates['editor_row'] = '<div id="{block_id}" class="wisy-editor-block wisy-editor-row {block_classes}" data-name="row" data-type="{block_type}">' .
			'<div class="controls">' .
				'<span class="move ui-sortable-handle" title="' . esc_attr__( 'Move Row', 'wisy' ) . '"><i class="feather icon-move"></i></span>'.
				'<span class="duplicate" title="' . esc_attr__( 'Duplicate Row', 'wisy' ) . '"><i class="feather icon-copy"></i></span>'.
				'<span class="delete" title="' . esc_attr__( 'Delete Row', 'wisy' ) . '"><i class="feather icon-trash"></i></span>'.
			'</div>'.

			'{block_frontend}'.

			'<div><textarea class="atts hidden">{block_atts}</textarea></div>'.
		'</div>';

		$templates['row_frontend'] = '<div class="wrap">'.
			'<div class="before-blocks-container" data-blocks-types="column"></div>'.
			'<div class="wisy-block-children">{block_children}</div>'.
			'<div class="after-blocks-container" data-blocks-types="column"></div>'.
		'</div>';

		return $templates;
	}
} // end class

} // end if