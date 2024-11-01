<?php
$pid = (int) ( isset( $_GET['post'] ) ? sanitize_text_field( $_GET['post'] ) : 0 );
$post = get_post( $pid );

if ( isset( $post->ID ) && $post->ID > 0 )
{
	$post_id = $post->ID;
}
else
{
	$post_id = 0;
	wp_die( esc_html__( 'Sorry, this item doesn\'t exist.', 'wisy' ) );
}

if ( ! current_user_can( 'edit_post', $post->ID ) )
{
	wp_die( esc_html__( 'Sorry, you are not allowed to edit this item.', 'wisy' ) );
}

$post_blocks = json_encode( json_decode(json_encode( wisy_get_post_blocks($post_id) ), true) );

$post_permalink = wisy_add_var_to_url( ['load_for' => 'wisy-builder-editor'], get_post_permalink( $post->ID ) );
$post_permalink = '//' . explode( '://', $post_permalink )[1];

$hook_suffix = 'wisy-editor';
?>
<!DOCTYPE html>
<html id="wisy-builder-editor" <?php language_attributes(); ?>>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title><?php echo esc_html__( 'Wisy Builder', 'wisy' ) . ' - ' . $post->post_title; ?></title>
		<script>
			var wisyBuilder = {
				ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
				post: {
					id: <?php echo $post_id; ?>,
				}
			};
		</script>
<?php

/**
 * Enqueue scripts for all admin pages.
 *
 * @since 2.8.0
 *
 * @param string $hook_suffix The current admin page.
 */
do_action( 'admin_enqueue_scripts', $hook_suffix );

/**
 * Fires when styles are printed for a specific admin page based on $hook_suffix.
 *
 * @since 2.6.0
 */
do_action( "admin_print_styles-{$hook_suffix}" );

/**
 * Fires when styles are printed for all admin pages.
 *
 * @since 2.6.0
 */
do_action( 'admin_print_styles' );

/**
 * Fires when scripts are printed for a specific admin page based on $hook_suffix.
 *
 * @since 2.1.0
 */
do_action( "admin_print_scripts-{$hook_suffix}" );

/**
 * Fires when scripts are printed for all admin pages.
 *
 * @since 2.1.0
 */
do_action( 'admin_print_scripts' );

/**
 * Fires in head section for a specific admin page.
 *
 * The dynamic portion of the hook, `$hook_suffix`, refers to the hook suffix
 * for the admin page.
 *
 * @since 2.1.0
 */
do_action( "admin_head-{$hook_suffix}" );

/**
 * Fires in head section for all admin pages.
 *
 * @since 2.1.0
 */
do_action( 'admin_head' );

?>
	</head>
	<body id="wisy-builder-frontend-editor">
		<header id="wisy-builder-header">
			<div class="wisy-builder-logo">
				<img src="<?php echo WISY_URL.'assets/images/wisy-logo-white-40x40.png'; ?>" width="40px" height="40px"/>
				<a class="title"><?php _e('Wisy Builder', 'wisy'); ?></a>
			</div>
			<ul class="middle-menu">
				<li class="button" id="wisy-builder-undo" title="<?php _e('Undo', 'wisy'); ?>" disabled="disabled">
					<i class="feather icon-rotate-ccw"></i>
				</li>
				<li class="button" id="wisy-builder-redo" title="<?php _e('Redo', 'wisy'); ?>" disabled="disabled">
					<i class="feather icon-rotate-cw"></i>
				</li>
			</ul>
			<ul class="right-menu">
				<li class="button" id="wisy-builder-save" title="<?php _e('Save', 'wisy'); ?>">
					<i class="feather icon-save"></i>
					<span class="flash" title="<?php _e('Your changes aren\'t saved yet', 'wisy'); ?>"></span>
				</li>
				<li class="button" id="wisy-builder-preview" title="<?php _e('Preview', 'wisy'); ?>">
					<i class="feather icon-eye"></i>
				</li>
				<li class="button wisy-builder-responsive" title="<?php _e('Screen Size', 'wisy'); ?>">
					<i class="feather icon-monitor"></i>
					<ul class="devices-list hidden">
						<li data-size="lg" title="<?php _e('Desktop Screen', 'wisy'); ?>" class="active">
							<i class="feather icon-monitor"></i>
						</li>
						<li data-size="md" title="<?php _e('Tablet Screen', 'wisy'); ?>">
							<i class="feather icon-tablet"></i>
						</li>
						<li data-size="sm" title="<?php _e('Phone Screen', 'wisy'); ?>">
							<i class="feather icon-smartphone"></i>
						</li>
					</ul>
				</li>
			</ul>
		</header>

		<div id="wisy-builder-main">
			<div id="wisy-builder-editor-preview">
				<iframe name="wisy-builder-editor-preview" src="<?php echo $post_permalink; ?>" allowfullscreen></iframe>
			</div>

			<div class="wisy-builder-settings">
				<div class="wisy-editor-block-settings"></div>
				<div class="wisy-editor-page-settings"></div>
			</div>

			<textarea id="wisy-builder-post-content" class="hidden" name="wisy_post_data[content]"><?php echo esc_textarea( $post_blocks ); ?></textarea>
			<input type="hidden" name="wisy_post_data[post_id]" value="<?php echo esc_attr( $post_id ); ?>">
		</div>

		<div id="wisy-modal-new-block" class="wisy-modal">
			<div class="wisy-modal-inner" id="wisy-builder-new-widget-box">
				<header>
					<h3 class="modal-title"><?php esc_html_e('Add New', 'wisy'); ?></h3>
					<button class="close"><i class="feather icon-x"></i></button>
				</header>
				<div class="widgets-list">
					<ul>
						<li class="single-widget" data-name="row">
							<img src="<?php echo WISY_URL . 'blocks/row/icon.png'; ?>">
							<span class="widget-name"><?php _e('Row', 'wisy') ?></span>
						</li>
						<li class="single-widget" data-name="column">
							<img src="<?php echo WISY_URL . 'blocks/column/icon.png'; ?>">
							<span class="widget-name"><?php _e('Column', 'wisy') ?></span>
						</li>
						<span class="line"></span>
						<?php

						$wisy_blocks = wisy_get_blocks();
						$wisy_blocks = is_array($wisy_blocks) ? $wisy_blocks : array();

						foreach ($wisy_blocks as $block_name => $block_class)
						{
							if ( class_exists($block_class) && !in_array($block_name, ['row', 'column']) )
							{
								$block = new $block_class();
								echo '<li class="single-widget" data-name="' . esc_attr( $block->get_name() ) . '">'.
									'<img src="' .
									esc_url( $block->get_icon_url() ) .
									'"><span class="widget-name">'.
									esc_textarea( $block->get_title() ) .
								'</span></li>';
							}
						}

						?>
					</ul>
				</div>
			</div>
		</div>

		<div id="wisy-builder-fullpage-loader" class="wisy-modal">
			<div class="wisy-builder-loader white"></div>
		</div>

		<div id="wisy-modal-confirm-box" class="wisy-modal">
			<div class="wisy-modal-inner">
				<header>
					<h3 class="modal-title"><?php _e('Add New', 'wisy'); ?></h3>
					<button class="close"><i class="feather icon-x"></i></button>
				</header>
				<div class="modal-body">
					<p class="modal-msg"></p>
				</div>
				<footer>
					<button type="button" id="cancel-action" autofocus><?php _e('No', 'wisy'); ?></button>
					<button type="button" id="do-action"><?php _e('Yes', 'wisy'); ?></button>
				</footer>
			</div>
		</div>

		<?php wp_footer(); ?>
	</body>
</html>

<?php
