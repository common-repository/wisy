<?php

// Frontend scripts & styles
add_action( 'wp_enqueue_scripts', 'wisy_enqueue_scripts', 10 );
function wisy_enqueue_scripts()
{
	global $post, $wisy_builder;

	$upload_dir = wp_get_upload_dir();
	if ( is_object( $post ) )
	{
		$post_css_file_path = $upload_dir['basedir'] . '/wisy-builder-files/css/post-' . $post->ID . '.css';
		$post_css_file = $upload_dir['baseurl'] . '/wisy-builder-files/css/post-' . $post->ID . '.css';
		$post_css_file = str_replace( 'http://', '//', $post_css_file );

		if ( file_exists( $post_css_file_path ) )
		{
			if ( ! wisy_load_for_editor() )
			{
				wp_enqueue_style( 'wisy-post-' . $post->ID . '-style', $post_css_file, [], get_the_modified_date( 'U', $post->ID ) );
			}

			$blocks_scripts = wisy_get_blocks_scripts( wisy_get_post_blocks( $post->ID ) );

			foreach ( $blocks_scripts as $key => $data )
			{
				$handle = isset( $data['handle'] ) ? $data['handle'] : '';
				$type = isset( $data['type'] ) ? $data['type'] : '';
				$ver = isset( $data['ver'] ) ? $data['ver'] : WISY_VERSION;
				$url = isset( $data['url'] ) ? $data['url'] : '';
				$inline = isset( $data['inline'] ) ? $data['inline'] : '';
				$deps = ( isset( $data['deps'] ) && is_array( $data['deps'] ) ) ? $data['deps'] : [];

				if ( $type == 'css' )
				{
					if ( isset( $data['inline'] ) )
					{
						wp_add_inline_style( $handle, $inline );
						continue;
					}
					wp_enqueue_style( $handle, $url, $deps, $ver );
				}
				elseif ( $type == 'js' )
				{
					if ( isset( $data['inline'] ) )
					{
						wp_add_inline_script( $handle, $inline );
						continue;
					}
					wp_enqueue_script( $handle, $url, $deps, $ver );
				}
			}

			wp_enqueue_style( 'wisy-default-blocks', WISY_URL . 'assets/css/default-blocks.css', [], WISY_VERSION );

			wp_enqueue_script( 'wisy-default-blocks', WISY_URL . 'assets/js/default-blocks.js', ['jquery'], WISY_VERSION );

			$post_blocks = wisy_get_post_blocks( $post->ID );
			$post_fonts = $wisy_builder->style_manager->get_blocks_fonts( $post_blocks );
			$fonts_url = '//fonts.googleapis.com/css?family=';
			$count = 0;

			foreach ( $post_fonts as $font_name => $font_weights )
			{
				if ( !empty($font_name) )
				{
					$fonts_url .= ($count > 0) ? '|' : '';
					$fonts_url .= $font_name . ":*";
					$count++;
				}
			}

			wp_enqueue_style( 'wisy_google_fonts', str_replace( ['%3A', '%2A'], [':', '*'], $fonts_url ) );
		}
	}

	if ( wisy_load_for_editor() )
	{
		wp_enqueue_style( 'wisy-editor', WISY_URL . 'assets/css/editor.css', [], WISY_VERSION );
		wp_enqueue_style( 'feather', WISY_URL . 'assets/fonts/feather/feather.min.css' );
	}

}

// Admin scripts & styles
add_action( 'admin_enqueue_scripts', 'wisy_admin_enqueue_scripts', 10, 1 );
function wisy_admin_enqueue_scripts( $page )
{
	global $post, $wisy_builder;

	if ( $page !== 'wisy-editor' )
	{
		return;
	}

	if ( wisy_load_for_editor() || wisy_is_editor() || wisy_is_editor('new') )
	{
		wp_enqueue_style( 'wisy-editor', WISY_URL . 'assets/css/editor.css', [], WISY_VERSION );
		wp_enqueue_style( 'wisy-default-blocks', WISY_URL . 'assets/css/default-blocks.css', [], WISY_VERSION );
		wp_enqueue_style( 'feather', WISY_URL . 'assets/fonts/feather/feather.min.css', [] );
		wp_enqueue_style( 'font-awesome', WISY_URL . 'assets/fonts/font-awesome/font-awesome.min.css', [] );
		wp_enqueue_style( 'st-color-picker', WISY_URL . 'assets/lib/st-color-picker/st-color-picker.min.css', [], '1.0.0' );
		wp_enqueue_style( 'st-select', WISY_URL . 'assets/lib/st-select/st-select.min.css', [], '1.1.0' );

		wp_enqueue_media();

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-resizable' );
	}

	if ( wisy_is_editor() )
	{
		wp_enqueue_script( 'wisy-block-settings', WISY_URL . 'assets/js/class-wisy-block-settings.js', [], WISY_VERSION );
		wp_enqueue_script( 'wisy-blocks', WISY_URL . 'assets/js/class-wisy-blocks.js', ['wisy-block-settings'], WISY_VERSION );
		wp_enqueue_script( 'st-color-picker', WISY_URL . 'assets/lib/st-color-picker/st-color-picker.min.js', ['jquery', 'jquery-ui-draggable', 'jquery-ui-sortable', 'jquery-ui-resizable'], '1.0.0' );
		wp_enqueue_script( 'st-select', WISY_URL . 'assets/lib/st-select/st-select.min.js', '1.1.0' );
		wp_enqueue_script( 'wisy-editor', WISY_URL . 'assets/js/editor.js', ['wisy-blocks', 'st-color-picker', 'st-select'], WISY_VERSION );

		wp_localize_script( 'wisy-editor', 'wisyURL', WISY_URL );
		wp_localize_script( 'wisy-editor', 'wisyBuilderBlocks', wisy_get_blocks_data() );
		wp_localize_script( 'wisy-editor', 'wisyBuilderGoogleFonts', json_decode( file_get_contents( WISY_URL . 'assets/json/google-fonts.json' ) ) );
		wp_localize_script( 'wisy-editor', 'wisyIcons', json_decode( file_get_contents( WISY_URL . 'assets/json/icons.json' ) ) );
		wp_localize_script( 'wisy-editor', 'wisyL10n', array(
			'default'	=> __( 'Default', 'wisy' ),
			'top'		=> _x( 'Top', 'block-settings', 'wisy' ),
			'right'		=> _x( 'Right', 'block-settings', 'wisy' ),
			'bottom'	=> _x( 'Bottom', 'block-settings', 'wisy' ),
			'left'		=> _x( 'Left', 'block-settings', 'wisy' ),
		) );
	}
}

add_action( 'admin_action_wisy-editor', 'wisy_admin_edit_post_template' );
function wisy_admin_edit_post_template()
{
	if ( wisy_is_editor() )
	{
		require_once ( WISY_PATH . 'admin/pages/editor.php' );
		exit;
	} elseif ( wisy_is_editor('new') )
	{
		require_once ( WISY_PATH . 'admin/pages/new-post.php' );
		exit;
	}
}

add_action( 'wp_footer', 'wisy_widgets_template', 10 );
function wisy_widgets_template()
{

	if ( wisy_is_editor() )
	{

		global $wisy_builder;
		$templates = $wisy_builder->editor->blocks_editor_templates();

		foreach ( $templates as $name => $template )
		{
			echo "<script id='wisy_builder_{$name}_template' type='html'>{$template}</script>\n";
		}

	}
}

// Load Language
add_action( 'init', 'wisy_language_load' );
function wisy_language_load()
{
	$langs_dir = WISY_PATH . 'languages/';
	load_plugin_textdomain( 'wisy', false, $langs_dir );
}

// Save post action
add_action( 'save_post', 'wisy_save_post', 10, 3 );
function wisy_save_post( $post_id, $post, $update )
{
	if ( get_post_meta( $post_id, '_wisy_builder_post_editor_enabled', true ) == '1' )
	{
		update_post_meta( $post_id, '_wisy_builder_post_editor_enabled', '0' );
	}
}

/* AJAX Actions */
add_action( 'wp_ajax_wisy_save_post', 'wisy_ajax_save_post' );
function wisy_ajax_save_post()
{
	global $wisy_builder;

	$post_id = (int) sanitize_text_field( $_REQUEST['post_id'] );
	$post_blocks = (array) wisy_sanitize_textarea_field( $_REQUEST['wisy_post_blocks'] ); // sanitize multiple values

	$response = [ 'status' => 'fail' ];

	$post_blocks = $wisy_builder->style_manager->add_blocks_ids( $post_blocks );

	if ( $post_id > 0 )
	{
		$post_id = wp_update_post([
			'ID' => $post_id,
		]);

		// Update post content metadata
		update_post_meta( $post_id, '_wisy_builder_post_blocks', json_encode( $post_blocks, JSON_UNESCAPED_UNICODE ) );
		update_post_meta( $post_id, '_wisy_builder_post_editor_enabled', '1' );

		$response['status'] = 'success';
		$response['post_id'] = $post_id;

		$wisy_builder->style_manager->create_post_styles( $post_id, $post_blocks );
	}

	echo json_encode( $response );

	// Stop execution
	wp_die();
}

add_action( 'wp_ajax_wisy_get_block_frontend', 'wisy_ajax_get_block_frontend' );
function wisy_ajax_get_block_frontend()
{
	$blocks = wisy_get_blocks();
	$block_name = (string) sanitize_text_field( $_REQUEST['block_name'] );

	$response = [
		'html' => '',
		'block_classes' => ''
	];

	if ( ! class_exists( $blocks[ $block_name ] ) )
	{
		wp_die();
	}

	define( 'WISY_IS_EDITOR', true );

	$block_class = new $blocks[ $block_name ]();

	$block_atts = (array) wisy_sanitize_textarea_field( $_REQUEST['block_atts'], $block_class ); // sanitize multiple values

	$response['html'] = $block_class->block_frontend( $block_atts, true );
	$response['block_classes'] = $block_class->get_css_classes( $block_atts );
	$response['scripts'] = $block_class->enqueue_scripts( $block_atts );

	echo json_encode( $response );

	// Stop execution
	wp_die();
}