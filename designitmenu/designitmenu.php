<?php
/*
Plugin Name: Designit Menu
Plugin URI: https://www.designbold.com/collection/create-new
Description: Desingbold designit build plugin allow create a menu
Version: 1.0.0
Author: Designit
Author URI: https://www.designbold.com/
License: GPLv2 or later
*/

/*
{Designit Menu} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Designit Menu} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Designit Menu}. If not, see {Plugin URI}.
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Add html
add_action('wp_footer', 'dbmenu_add_header_and_footer');
function dbmenu_add_header_and_footer (){
	include('header_footer.php');
}

// Add class for the body element
add_filter('body_class', 'multisite_body_classes');
function multisite_body_classes($classes) {
	$classes[] = 'designitmenu';
	return $classes;
}

// Add list css, js for plugin
add_action( 'wp_enqueue_scripts', 'dbmenu_namespace_scripts_styles' );
function dbmenu_namespace_scripts_styles() {
	$dir = plugin_dir_url(__FILE__);
	wp_enqueue_style( 'dbmenu_google_font', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese');
	wp_enqueue_style( 'dbmenu_font-awesome.min.css', $dir . 'css/font-awesome.min.css');
	wp_enqueue_style( 'dbmenu_bootstrap.min.css', $dir . 'css/bootstrap.min.css', false, '1.1', 'all' );
	wp_enqueue_style( 'dbmenu_style', $dir . 'css/main.css', false, '1.1', 'all' );
	wp_enqueue_style( 'dbmenu_custome', $dir . 'css/custome.css', false, '1.1', 'all' );

	wp_enqueue_script( 'dbmenu_popper.js', $dir . 'js/popper.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_bootstrap.min.js', $dir . 'js/bootstrap.min.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_main.js', $dir . 'js/main.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_designbold_sdk.js', $dir . '/designbold_sdk.js', array ( 'jquery' ), 1.1, true);
}

// Add item to admin menu
add_action('admin_menu', 'dbmenu_add_admin_menu');
function dbmenu_add_admin_menu() {
	//create new top-level menu
	$icon = plugins_url('designitmenu/images/16.png');
	add_menu_page( 'Designit menu option', 'Designit menu', 'manage_options', 'designit-menu', 'dbmenu_plugin_setting_page', $icon);
}

// Generate html page option
function dbmenu_plugin_setting_page() {
	?>
	<div class="wrap">
		<h1>DesignIt Menu Option</h1>

		<?php settings_errors(); ?>
		<!-- Phải chạy vào options.php đây là mặc định của wordpress :( -->
		<form method="post" action="">
			<?php settings_fields( 'dbmenu_options' ); ?>
			<table class="form-table">
				<tr class="form-field form-required">
					<th scope="row">App key <span class="description">(required)</span></th>
					<td><input type="text" name="dbmenu_option_app_key" value="<?php echo esc_attr( get_option('dbmenu_option_app_key') ); ?>" placeholder="app key" /></td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row">App secret <span class="description">(required)</span></th>
					<td><input type="text" name="dbmenu_option_app_secret" value="<?php echo esc_attr( get_option('dbmenu_option_app_secret') ); ?>" placeholder="app secret"/></td>
				</tr>
			</table>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>

		</form>
	</div>
<?php } 

// Call update option function

if(isset($_POST['submit'])){
	$app_key = 
	isset($_POST['dbmenu_option_app_key']) ? sanitize_text_field($_POST['dbmenu_option_app_key']) : "";

	$app_secret = 
	isset($_POST['dbmenu_option_app_secret']) ? sanitize_text_field($_POST['dbmenu_option_app_secret']) : "";

	add_action( 'alter_item', 'dbmenu_update_option', 10, 2 );
	function dbmenu_update_option($app_key, $app_secret) {
		update_option( 'dbmenu_option_app_key', $app_key, 'yes');
		update_option( 'dbmenu_option_app_secret', $app_secret, 'yes');
	}

	do_action('alter_item', $app_key, $app_secret);
}

