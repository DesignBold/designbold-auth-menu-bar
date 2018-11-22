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

define( 'DBMN_FILE', __FILE__ ); // this file
define( 'DBMN_URL', plugin_dir_url(__FILE__) ); // Url to file
define( 'DBMN_BASENAME', plugin_basename( DBMN_FILE ) ); // plugin name as known by WP
define( 'DBMN_DIR', dirname( DBMN_FILE ) ); // our directory
define( 'DBMN', ucwords( str_replace( '-', ' ', dirname( DBMN_BASENAME ) ) ) );

define( 'DBMN_ADMIN_INC', DBMN_URL . '/admin' );
define( 'DBMN_ASSETS_INC', DBMN_URL . '/assets' );
define( 'DBMN_TEMP_INC', DBMN_URL . '/templates' );

/**
 * The code that runs during plugin activation (but not during updates).
 */
function dbmenu_activate() {
	if ( version_compare( phpversion(), '5.4', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'Designbold menu requires PHP version 5.4 or higher. Plugin was deactivated.' );
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-disqus-deactivator.php
 */
function dbmenu_deactivate() {
	// Do something
}

register_activation_hook( __FILE__, 'dbmenu_activate' );
register_deactivation_hook( __FILE__, 'dbmenu_deactivate' );

// Add html
add_action('wp_footer', 'dbmenu_add_header_and_footer');
function dbmenu_add_header_and_footer (){
	$config = json_decode(file_get_contents(DBMN_URL . "/config.json"));
	include('templates/header.php');
	include('templates/footer.php');
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
	$dir = DBMN_ASSETS_INC . '/';
	wp_enqueue_style('dbmenu_google-font', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese');
	wp_enqueue_style('dbmenu_font-awesome','https://pro.fontawesome.com/releases/v5.5.0/css/all.css');

	wp_enqueue_style( 'dbmenu_bootstrap.min.css', $dir . 'css/bootstrap.min.css', false, '1.1', 'all' );
	wp_enqueue_style( 'dbmenu_style', $dir . 'css/main.css', false, '1.1', 'all' );
	wp_enqueue_style( 'dbmenu_custome', $dir . 'css/custome.css', false, '1.4', 'all' );


	// 3rd party
	//plugin source
	wp_enqueue_script( 'dbmenu_underscore.js', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js');
	wp_enqueue_script( 'dbmenu_jquery.min.js', $dir . 'js/jquery.min.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_popper.js', $dir . 'js/popper.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_bootstrap.min.js', $dir . 'js/bootstrap.min.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_main.js', $dir . 'js/main.js', array ( 'jquery' ), 1.1, true);
	wp_enqueue_script( 'dbmenu_designbold_sdk.js', $dir . 'js/designbold_sdk.js', array ( 'jquery' ), 1.1, true);

	wp_localize_script( 'dbmenu_designbold_sdk.js', 'dbtopbarconfig', array(
		'baseUrl' => get_option('siteurl') != '' ? get_option('siteurl') : "",
		'pluginUrl' => DBMN_URL . '/designbold.php',
		'options' => array (
			'app_key' => get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "",
			'app_secret'  => get_option('dbmenu_option_app_secret') != '' ? get_option('dbmenu_option_app_secret') : ""
		)
	) );
}

// Registers a navigation menu location for a theme.
register_nav_menu( 'designbold-menu', __( 'Designbold menu', 'designbold-menu' ) );

// Add item to admin menu
add_action('admin_menu', 'dbmenu_add_admin_menu');
function dbmenu_add_admin_menu() {
	//create new top-level menu
	$icon = DBMN_ASSETS_INC . '/images/16.png';
	add_menu_page( 'Designit menu option', 'Designit menu', 'manage_options', 'designit-menu', 'dbmenu_plugin_setting_page', $icon);
}

// Generate html page option
function dbmenu_plugin_setting_page() {
	?>
	<div class="wrap">
		<h1>DesignIt Menu Option</h1>
		<p>App key and app secret must be required to plugin work. If one of 2 fields be empty then plugin will not work.</p>
		<?php settings_errors(); ?>
		<!-- Phải chạy vào options.php đây là mặc định của wordpress :( -->
		<form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
			<?php settings_fields( 'dbmenu_options' ); ?>
			<input type="hidden" name="action" value="db-save-option">
			<table class="form-table">
				<tr class="form-field form-required">
					<th scope="row">App key <span class="description">(required)</span></th>
					<td><input type="text" name="dbmenu_option_app_key" value="<?php echo esc_attr( get_option('dbmenu_option_app_key') ); ?>" placeholder="app key" /></td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row">App secret <span class="description">(required)</span></th>
					<td><input type="text" name="dbmenu_option_app_secret" value="<?php echo esc_attr( get_option('dbmenu_option_app_secret') ); ?>" placeholder="app secret"/></td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row">App secret <span class="description">(required)</span></th>
					<td>
						<select name="dbmenu_option_menu_name">
							<?php 
							$menus = get_terms('nav_menu');
							$active = get_option('dbmenu_option_menu_name');
							foreach($menus as $menu){
								if($menu->slug == $active){
									echo '<option selected value="'.$menu->slug.'">'. $menu->name . "</option>";
								}else{
									echo '<option value="'.$menu->slug.'">'. $menu->name . "</option>";
								}
							} 
							?>
						</select>
					</td>
				</tr>
			</table>

			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>

		</form>
	</div>
<?php } 

// Call update option function
add_action('wp_ajax_nopriv_db-save-option', 'db_save_option');
add_action('wp_ajax_db-save-option', 'db_save_option');
function db_save_option() {
	if(isset($_POST['submit'])){
		$app_key = 
		isset($_POST['dbmenu_option_app_key']) ? sanitize_text_field($_POST['dbmenu_option_app_key']) : "";

		$app_secret = 
		isset($_POST['dbmenu_option_app_secret']) ? sanitize_text_field($_POST['dbmenu_option_app_secret']) : "";

		$menu_name = 
		isset($_POST['dbmenu_option_menu_name']) ? sanitize_text_field($_POST['dbmenu_option_menu_name']) : "";

		add_action( 'alter_item', 'dbmenu_update_option', 10, 3 );
		function dbmenu_update_option($app_key, $app_secret, $menu_name) {
			update_option( 'dbmenu_option_app_key', $app_key, 'yes');
			update_option( 'dbmenu_option_app_secret', $app_secret, 'yes');
			update_option( 'dbmenu_option_menu_name', $menu_name, 'yes');
		}

		do_action('alter_item', $app_key, $app_secret, $menu_name);
		// redirect when complete
		wp_safe_redirect('admin.php?page=designit-menu');
	}
	exit(0);
}

// Handle login through designbold.php
add_action('wp_ajax_nopriv_db-process-login', 'db_process_login');
add_action('wp_ajax_db-process-login', 'db_process_login');
function db_process_login() {
	if(file_exists(DBMN_DIR . '/designbold.php')) {
		include('designbold.php');
		$action = isset($_GET['action']) ? $_GET['action'] : 'callback';
		if ($action == 'connect'){
			connect();
		}
		else{
			callback();
		}
		exit(0);
	}
}