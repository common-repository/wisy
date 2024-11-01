<?php

/* FRONTEND FILTERS */

// Filter the post content
add_filter( 'the_content', 'wisy_the_content', 999999 );
function wisy_the_content( $content )
{
	global $wisy_builder;

	// Check if the page is loaded for the editor
	if ( wisy_load_for_editor() )
	{
		$content = $wisy_builder->editor->editor_blocks( get_the_ID() );
	}
	elseif ( get_post_meta( get_the_ID(), '_wisy_builder_post_editor_enabled', true ) == '1' )
	{
		$post_blocks = wisy_get_post_blocks( get_the_ID() );
		$content = $wisy_builder->frontend->blocks( $post_blocks );
	}

	return $content;
}

add_filter( 'wisy_block_frontend_classes', 'wisy_block_frontend_classes', 10, 3 );
function wisy_block_frontend_classes( $classes, $block, $atts )
{
	if ( $block->get_type() == 'row' && $atts['wide_mode'] == 'enabled' )
	{
		$classes[] = 'wisy-wide-mode';
	}

	return $classes;
}

/* BACKEND FILTERS */

add_filter( 'wisy_builder_block_sections', 'wisy_builder_block_sections', 10, 3 );
function wisy_builder_block_sections( $block_sections, $block_name, $block_type )
{
	if ( ! isset( $block_sections['general'] ) )
	{
		$block_sections['general'] = [
			'title' => esc_html__( 'General', 'wisy' ),
		];
	}

	if ( ! isset( $block_sections['style'] ) )
	{
		$block_sections['style'] = [
			'title' => esc_html__( 'Style', 'wisy' ),
		];
	}

	if ( ! isset( $block_sections['advanced'] ) )
	{
		$block_sections['advanced'] = [
			'title' => esc_html__( 'Advanced', 'wisy' ),
		];
	}

	if ( ! isset( $block_sections['advanced']['groups']['block_box'] ) )
	{
		$block_sections['advanced']['groups']['block_box'] = esc_html__( 'Block Box', 'wisy' );
	}

	if ( ! isset( $block_sections['style']['groups']['block_box'] ) )
	{
		$block_sections['style']['groups']['block_box'] = esc_html__( 'Block Box', 'wisy' );
	}

	return $block_sections;
}

add_filter( 'wisy_builder_block_settings', 'wisy_builder_block_settings', 10, 3 );
function wisy_builder_block_settings( $block_settings, $block_name, $block_type )
{

	$block_settings['_align'] = [
		'title' => esc_html__( 'Align', 'wisy' ),
		'type' => 'select',
		'section' => 'general',
		'default' => '',
		'values' => [
			'' => esc_html__( 'Center', 'wisy' ),
			'left' => esc_html__( 'Left', 'wisy' ),
			'right' => esc_html__( 'Right', 'wisy' )
		],
		'css' => [
			'SELECTOR>.block-cont' => 'justify-content:VALUE;'
		]
	];

	$block_settings['_b_width'] = [
		'title' => esc_html__( 'Border Width', 'wisy' ),
		'type' => 'boxSides',
		'section' => 'style',
		'group' => 'block_box',
		'default' => '',
		'css' => [
			'SELECTOR>.block-cont' => 'border-width:VALUE;'
		]
	];
	$block_settings['_b_color'] = [
		'title' => esc_html__( 'Border Color', 'wisy' ),
		'type' => 'color',
		'section' => 'style',
		'group' => 'block_box',
		'default' => '',
		'args' => [
			'hover' => true
		],
		'css' => [
			'SELECTOR>.block-cont' => 'border-color:VALUE;'
		]
	];
	$block_settings['_b_style'] = [
		'title' => esc_html__( 'Border Style', 'wisy' ),
		'type' => 'select',
		'section' => 'style',
		'group' => 'block_box',
		'default' => '',
		'values' => [
			'' => esc_html__( '-Select-', 'wisy' ),
			'solid' => esc_html__( 'Solid', 'wisy' )
		],
		'css' => [
			'SELECTOR>.block-cont' => 'border-style:VALUE;'
		]
	];

	// Advanced section

	$block_settings['_padding'] = [
		'title' => esc_html__( 'Padding', 'wisy' ),
		'type' => 'boxSides',
		'section' => 'advanced',
		'group' => 'block_box',
		'default' => '0',
		'args' => [
			'responsive' => true
		],
		'alerts' => [
			'note' => sprintf(
				/* Translators: 1: CSS units */
				esc_html_x( 'Enter value with valid CSS unit %1$s.', 'settings-alerts', 'wisy' ),
				'(px, em, %)'
			),
		],
		'css' => [
			'SELECTOR>.block-cont' => 'padding:VALUE;'
		]
	];

	$block_settings['_margin'] = [
		'title' => esc_html__( 'Margin', 'wisy' ),
		'type' => 'boxSides',
		'section' => 'advanced',
		'group' => 'block_box',
		'default' => '0',
		'args' => [
			'responsive' => true
		],
		'alerts' => [
			'note' => sprintf(
				/* Translators: 1: CSS units */
				esc_html_x( 'Enter value with valid CSS unit %1$s.', 'settings-alerts', 'wisy' ),
				'(px, em, %)'
			),
		],
		'css' => [
			'SELECTOR' => 'margin:VALUE;'
		]
	];

	$block_settings['_zindex'] = [
		'title' => esc_html__( 'Z-Index', 'wisy' ),
		'type' => 'text',
		'section' => 'advanced',
		'group' => 'block_box',
		'default' => '',
		'css' => [
			'SELECTOR' => 'z-index:VALUE;'
		]
	];

	$block_settings['_visibility'] = [
		'title' => esc_html__( 'Visibility', 'wisy' ),
		'type' => 'select',
		'section' => 'advanced',
		'values' => [
			'' => esc_html__( 'Visible', 'wisy' ),
			'none' => esc_html__( 'Hidden', 'wisy' )
		],
		'default' => '',
		'css' => [
			'SELECTOR' => 'display:VALUE;'
		]
	];

	$block_settings['_css'] = [
		'title' => esc_html__( 'Custom CSS', 'wisy' ),
		'type' => 'textarea',
		'section' => 'advanced',
		'default' => '',
		'alerts' => [
			'note' => sprintf(
				/* Translators: 1:SELECTOR */
				_x( '"%1$s" will be changed to block ID.', 'settings-alerts', 'wisy' ),
				'SELECTOR'
			),
		]
	];

	$block_settings['_id'] = [
		'title' => esc_html__( 'Block ID', 'wisy' ),
		'type' => 'hidden',
		'section' => 'advanced',
		'default' => '',
	];

	return $block_settings;
}

// Filter the post row actions
add_filter( 'post_row_actions', 'wisy_modify_list_row_actions', 10, 2 );
add_filter( 'page_row_actions', 'wisy_modify_list_row_actions', 10, 2 );
function wisy_modify_list_row_actions( $actions, $post )
{
	// Check for your post type.
	if ( $post->post_type )
	{

		// Build your links URL.
		$url = admin_url( 'post.php?post=' . $post->ID );

		// Add parameters to URL
		$edit_link = add_query_arg( array( 'action' => 'wisy-editor' ), $url );

		$actions = array_merge( $actions, array(
			'edit-with-wisy' => sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $edit_link ),
				esc_html__( 'Edit With Wisy', 'wisy' )
			)
		) );

	}

	return $actions;
}