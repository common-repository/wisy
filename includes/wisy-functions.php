<?php

function wisy_is_editor( $new = null )
{
	if ( defined('WISY_IS_EDITOR') && WISY_IS_EDITOR )
	{
		return true;
	}

	if ( ! isset( $_GET['action'] ) || $_GET['action'] != 'wisy-editor' || ! is_admin() )
	{
		return false;
	}

	if ( $new == 'new' && $GLOBALS['pagenow'] == 'post-new.php' )
	{
		return true;
	}

	if ( $new == 'new' )
	{
		return false;
	}

	if ( $GLOBALS['pagenow'] == 'post.php' && isset( $_GET['post'] ) && is_numeric( $_GET['post'] ) )
	{
		return true;
	}

	return false;
}

function wisy_load_for_editor()
{
	return ! is_admin() && isset( $_GET['load_for'] ) && $_GET['load_for'] == 'wisy-builder-editor';
}

function wisy_uniqid( $length = 10, $prefix = '' )
{
	$key = '';
	$keys = array_merge( range(0, 9), range('a', 'z'), range('A', 'Z') );

	for ( $i = 0; $i < $length; $i++ )
	{
		$key .= $keys[ array_rand( $keys ) ];
	}

	return $prefix . $key;
}

function wisy_add_var_to_url( $variables = [], $url_string = null )
{
	$url_string = html_entity_decode( $url_string );
	$start_pos = strpos( $url_string, '?' );
	$url_vars_strings = isset( explode( '?', $url_string )[1] ) ? explode( '?', $url_string )[1] : '';

	preg_match_all( '/([^=&?]+)=([^=&?]+)/', $url_vars_strings, $old_vars );

	$old_vars = array_combine( $old_vars[1], $old_vars[2] );
	$url_string = explode( '?', $url_string )[0];

	$new_vars = array_merge( $old_vars, $variables );

	foreach ( $new_vars as $var_name => $var_value )
	{
		// add variable name and variable value
		if ( $var_value !== false )
		{
			$url_string .= ( ! strpos( $url_string, '?' ) ) ? '?' : '&';
			$url_string .= $var_name . '=' . $var_value;
		}
	}

	return $url_string;
}

function wisy_register_block( $block_class = '' )
{
	global $wisy_registered_blocks;

	if ( ! class_exists( $block_class ) )
	{
		return false;
	}

	$block = new $block_class();
	$wisy_registered_blocks = is_array( $wisy_registered_blocks ) ? $wisy_registered_blocks : array();

	// Check if block name exist
	if ( ! in_array( $block->get_name(), $wisy_registered_blocks ) )
	{
		$wisy_registered_blocks[ $block->get_name() ] = $block_class;
	}

	update_option( 'wisy_builder_registered_blocks', $wisy_registered_blocks );
}

// Get blocks data
function wisy_get_blocks_data()
{

	$wisy_blocks = get_option( 'wisy_builder_registered_blocks', array() );
	$wisy_blocks = is_array( $wisy_blocks ) ? $wisy_blocks : array();

	foreach ( $wisy_blocks as $block_name => $block_class )
	{
		if ( class_exists( $block_class ) )
		{
			$block = new $block_class();

			$blocks_data[ $block_name ] = [
				'title'		=> $block->get_title(),
				'type'		=> $block->get_type(),
				'icon'		=> $block->get_icon_url(),
				'sections' 	=> $block->get_sections(),
				'settings' 	=> $block->get_settings()
			];

			if ( $block->get_type() == 'group' )
			{
				$blocks_data[ $block_name ]['group_blocks'] = $block->group_blocks();
			}
		}
	}

	return $blocks_data;
}

function wisy_encode_characters( &$string, $key = null )
{
	if ( is_array( $string ) || is_object( $string ) )
	{
		array_walk( $string, __FUNCTION__ );
	}
	else
	{
		$characters = ['/', '[', ']', '"', "'", ',', '=', '}', '{', '<', '>', "\r\n", "\n"];
		$decoded_characters = ['%2F', '%5B', '%5D', '%5C%22', '%5C%27', '%2C', '%3D', '%7D', '%7B', '%3C', '%3E', '<br/>', '<br/>'];
		$string = str_replace($characters, $decoded_characters, $string);

		return $string;
	}
}

function wisy_decode_characters( &$string, $key = null )
{
	if ( is_string( $string ) )
	{
		$characters = ['/', '[', ']', '&quot;', '&apos;', ',', '=', '}', '{', '<', '>', '<br/>', '<br/>'];
		$decoded_characters = ['%2F', '%5B', '%5D', '%5C%22', '%5C%27', '%2C', '%3D', '%7D', '%7B', '%3C', '%3E', "\r\n", "\n"];
		$string = str_replace( $decoded_characters, $characters, $string );
	}
	elseif ( is_array( $string ) || is_object( $string ) )
	{
		array_walk( $string, __FUNCTION__ );
	}

	return $string;
}

function wisy_get_blocks()
{
	$get_blocks = get_option( 'wisy_builder_registered_blocks', [] );

	return $get_blocks;
}

function wisy_get_post_blocks( $post_id = 0 )
{
	if ( $post_id > 0 )
	{
		$post_blocks = get_post_meta( $post_id, '_wisy_builder_post_blocks', true );
		$post_blocks = is_array( json_decode( $post_blocks ) ) ? json_decode( $post_blocks ) : [];
		$post_blocks = json_decode( json_encode( $post_blocks ), true );

		return wisy_add_missed_blocks( $post_blocks );
	}

	return [];
}

function wisy_get_block_from_post( $block_name = '', $post_id = 0 )
{
	return wisy_get_block_from_array( $block_name, wisy_get_post_blocks( $post_id ) );
}

function wisy_get_block_from_array( $block_name = '', $blocks = [] )
{
	$match = [];

	if ( empty( $block_name ) || ! is_string( $block_name ) )
	{
		return false;
	}

	if ( ! is_array( $blocks ) )
	{
		return $match;
	}

	foreach ( $blocks as $key => $data )
	{
		if ( $block_name == $data['name'] )
		{
			$match[] = $data['atts'];
		}

		if ( isset( $data['children'] ) )
		{
			$match = array_merge( $match, wisy_get_block_from_array( $block_name, $data['children'] ) );
		}
	}

	return $match;
}

function wisy_get_blocks_scripts( $blocks = [] )
{
	$scripts = [];
	$blocks = is_array( $blocks ) ? $blocks : [];
	$get_blocks = wisy_get_blocks();

	foreach ( $blocks as $key => $data )
	{
		if ( isset( $data['name'] ) && isset( $get_blocks[ $data['name'] ] ) && class_exists( $get_blocks[ $data['name'] ] ) )
		{
			$data = array_merge(
				[
					'name' => '',
					'type' => '',
					'atts' => [],
					'children' => []
				],
				$data
			);
			$block = new $get_blocks[ $data['name'] ]();
			$scripts = array_merge( $scripts, $block->enqueue_scripts( $data['atts'] ) );

			if ( isset( $data['children'] ) && is_array( $data['children'] ) )
			{
				$scripts = array_merge( $scripts, wisy_get_blocks_scripts( $data['children'] ) );
			}
		}
	}

	return $scripts;
}

function wisy_is_block_renders( $block_name = '' )
{
	if ( is_empty( $block_name ) || ! is_string( $block_name ) || ! is_int( $post_id ) )
	{
		return false;
	}

	if ( $post_id > 0 )
	{
		//
	}

	return [];
}


function wisy_add_missed_blocks( $blocks, $block_type_to_add = 'row' )
{
	if ( $block_type_to_add == 'row' )
	{
		foreach ( $blocks as $key => $block )
		{
			if ( isset( $block['type'] ) && $block['type'] != 'row' )
			{
				$blocks[$key] = [];
				$blocks[$key]['type'] = 'row';
				$blocks[$key]['name'] = 'row';
				$blocks[$key]['atts'] = [];
				$blocks[$key]['children'] = [ wisy_add_missed_blocks( $block, 'column' ) ];
			}
		}
	}
	elseif ( $block_type_to_add == 'column' )
	{
		foreach ( $blocks as $key => $block )
		{
			if ( isset( $block['type'] ) && $block['type'] != 'column' )
			{
				$blocks[$key] = [];
				$blocks[$key]['type'] = 'column';
				$blocks[$key]['name'] = 'column';
				$blocks[$key]['atts'] = [];
				$blocks[$key]['children'] = [ $block ];
			}
		}
	}

	return $blocks;
}

function wisy_sanitize_textarea_field( $array = [], $block_class = '' ) : array
{
	$array = is_array( $array ) ? $array : [];

	foreach ( $array as $key => $value )
	{
		if ( is_string( $value ) )
		{
			if ( is_object( $block_class ) && isset( $block_class->get_settings()[ $key ] ) && $block_class->get_settings()[ $key ]['type'] == 'textarea' )
			{
				$array[ $key ] = strip_tags( $value, '<br>' );
			}
			else
			{
				$array[ $key ] = esc_textarea( $value );
			}
		}
		elseif ( is_array( $value ) )
		{
			$array[ $key ] = wisy_sanitize_textarea_field( $value );
		}
	}

	return $array;
}

function wisy_array_merge( $defaults, $array ) : array
{
	$merged_array = [];

	if ( ! is_array( $defaults ) || ! is_array( $array ) )
	{
		return $merged_array;
	}

	foreach ( $defaults as $key => $value )
	{
		if ( is_array( $value ) && isset( $array[ $key ] ) && is_array( $array[ $key ] ) )
		{
			$merged_array[ $key ] = array_merge( $value, $array[ $key ] );
		}
		else
		{
			$merged_array[ $key ] = ( isset( $array[ $key ] ) ) ? $array[ $key ] : $value;
		}
	}

	return $merged_array;
}

function wisy_br_html( &$string, $key = null )
{
	if ( is_string( $string ) )
	{
		$characters = ['\r\n', '\n'];
		$html_characters = ['<br>', '<br>'];
		$string = str_replace( $characters, $html_characters, $string );
	}
	elseif ( is_array( $string ) || is_object( $string ) )
	{
		array_walk( $string, __FUNCTION__ );
	}

	return $string;
}

function wisy_object_merge( $object1, $object2 )
{
	$final_object = new stdClass;

	if ( ! is_array( $object1 ) && ! is_object( $object1 ) )
	{
		$object1 = new stdClass;
	}

	if ( ! is_array( $object2 ) && ! is_object( $object2 ) )
	{
		$object2 = new stdClass;
	}

	foreach ( $object1 as $key => $value )
	{
		$final_object->{$key} = $value;
	}

	foreach ( $object2 as $key => $value )
	{
		$final_object->{$key} = $value;
	}

	return $final_object;
}

// Get the attachment's ID from the file URL
function wisy_get_image_id( $image_url )
{
	global $wpdb;

	$attachment = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE post_type='attachment' AND guid='%s';",
			$image_url
		)
	);

	return ( isset( $attachment[0] ) && $attachment[0] > 0 ) ? $attachment[0] : false;
}