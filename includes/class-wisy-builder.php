<?php

/*
* Wisy Builder main class
* @since 0.1.0
*/

if ( ! defined('ABSPATH') ) { exit; } // Exit if access directly

if ( ! class_exists('Wisy_Builder') )
{

class Wisy_Builder
{
	/*
	* Wisy_Editor class
	*/
	public $editor;

	/*
	* Wisy_Frontend class
	*/
	public $frontend;

	/*
	* Wisy_Block_Base class
	*/
	public $block_base;

	/*
	* Wisy_Style_Manager class
	*/
	public $style_manager;

	/*
	* Class constructor
	*/
	public function __construct()
	{
		// Include classes files
		$this->include_classes();

		// Call the classes
		$this->editor = new Wisy_Editor();
		$this->frontend = new Wisy_Frontend();
		$this->block_base = new Wisy_Block_Base();
		$this->style_manager = new Wisy_Style_Manager();

		// Load blocks files
		$this->load_blocks();
	}

	/*
	* Include plugin's classes
	*/
	private function include_classes()
	{
		include_once ( WISY_PATH . 'includes/class-wisy-editor.php' );
		include_once ( WISY_PATH . 'includes/class-wisy-frontend.php' );
		include_once ( WISY_PATH . 'includes/class-wisy-block-base.php' );
		include_once ( WISY_PATH . 'includes/class-wisy-style-manager.php' );
	}

	/*
	* Load blocks files
	*/
	private function load_blocks()
	{
		$blocks_paths = glob( WISY_PATH . 'blocks/*', GLOB_ONLYDIR );
		foreach ($blocks_paths as $block_path )
		{
			// Include block file
			include_once ( $block_path . '/' . basename($block_path) . '.php' );
		}
	}
} // end class

$GLOBALS['wisy_builder'] = new Wisy_Builder();

} // end if