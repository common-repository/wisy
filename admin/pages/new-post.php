<?php

if ( isset($_POST['create']) )
{
	$post_id = wp_insert_post( [
		'post_title' => sanitize_text_field( $_POST['post_title'] ),
		'post_type' => sanitize_text_field( $_GET['type'] ),
		'post_status' => ( isset( $_POST['post_status'] ) && $_POST['post_status'] == 'publish' ) ? 'publish' : 'draft'
	] );

	if ( $post_id > 0 )
	{
		$post_edit_link = wisy_add_var_to_url(
			[ 'action' => 'wisy-editor' ],
			get_edit_post_link($post_id)
		);

		wp_redirect($post_edit_link);
	}
}

?>
<!DOCTYPE html>
<html id="wisy-builder-editor" <?php language_attributes(); ?>>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title><?php echo esc_html__( 'Wisy Builder', 'wisy' ) . ' - ' . esc_html__( 'New', 'wisy' ); ?></title>
	<script>
		var wisyBuilder = {
			ajaxurl: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
			post: {
				id: 0,
			}
		};
	</script>

<?php

$hook_suffix = 'wisy-editor';

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

<body class="wisy-new-post-page">

	<div class="wisy-new-post-container">
		<img src="<?php echo WISY_URL . 'assets/images/wisy-logo.png'; ?>" class="wisy-logo"/>
		<div class="wisy-new-post-box">
			<header><?php esc_html_e( 'Add New', 'wisy' ); ?></header>
			<div class="content">
				<form action="" method="post">
					<input type="text" name="post_title" placeholder="<?php esc_attr_e( 'Enter title', 'wisy' ); ?>" autofocus>
					<select name="post_status">
						<option value="draft"><?php esc_html_e( 'Draft', 'wisy' ); ?></option>
						<option value="publish"><?php esc_html_e( 'Publish', 'wisy' ); ?></option>
					</select>
					<button type="submit" name="create"><?php esc_html_e( 'Create', 'wisy' ); ?></button>
				</form>
			</div>
		</div>

		<ul class="footer-menu">
			<li>
				<a href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Back to Dashboard', 'wisy' ); ?></a>
			</li>
		</ul>
	</div>

</body>

</html>