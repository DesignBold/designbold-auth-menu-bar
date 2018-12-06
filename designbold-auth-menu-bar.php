<?php
/**
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

define( 'DBMN_ADMIN_INC', DBMN_URL . 'admin' );
define( 'DBMN_ASSETS_INC', DBMN_URL . 'assets' );
define( 'DBMN_TEMP_INC', DBMN_URL . 'templates' );

define( 'DESIGNBOLD_USER_METADATA', 'dbmenu_info_user' );

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
	$config = json_decode(file_get_contents(DBMN_URL . "config.json"));
	include('templates/header.php');
	include('templates/footer.html');
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
	wp_enqueue_style( 'dbmenu_bootstrap.min.css', $dir . 'css/bootstrap.min.css');
	wp_enqueue_style( 'dbmenu_style', $dir . 'css/main.css');
	wp_enqueue_style( 'dbmenu_custome', $dir . 'css/custome.css', false, time());


	// 3rd party
	//plugin source
	wp_enqueue_script( 'dbmenu_underscore.js', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.9.1/underscore-min.js');
	wp_enqueue_script( 'dbmenu_jquery.min.js', $dir . 'js/jquery.min.js');
	wp_enqueue_script( 'dbmenu_popper.js', $dir . 'js/popper.js');
	wp_enqueue_script( 'dbmenu_bootstrap.min.js', $dir . 'js/bootstrap.min.js');
	wp_enqueue_script( 'dbmenu_main.js', $dir . 'js/main.js');
	wp_enqueue_script( 'dbmenu_designbold_sdk.js', $dir . 'js/designbold_sdk.js', array(), time());
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
					<th scope="row">Menu location <span class="description">(required)</span></th>
					<td>
						<select name="dbmenu_option_menu_name">
							<?php 
							$menus = get_nav_menu_locations();
							$active = get_option('dbmenu_option_menu_name');
							foreach($menus as $menu => $val){
								if($menu == $active){
									echo '<option selected value="'.$menu.'">'. $menu . "</option>";
								}else{
									echo '<option value="'.$menu.'">'. $menu . "</option>";
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

		$menu_name = 
		isset($_POST['dbmenu_option_menu_name']) ? sanitize_text_field($_POST['dbmenu_option_menu_name']) : "";

		add_action( 'alter_item', 'dbmenu_update_option', 10, 2 );
		function dbmenu_update_option($app_key, $menu_name) {
			update_option( 'dbmenu_option_app_key', $app_key, 'yes');
			update_option( 'dbmenu_option_menu_name', $menu_name, 'yes');
		}

		do_action('alter_item', $app_key, $menu_name);
		// redirect when complete
		wp_safe_redirect('admin.php?page=designit-menu');
	}
	exit(0);
}

// Insert /Update a user into the database. But not sending a password change email.
function dbmenu_insert_user( $userdata ){
	return wp_insert_user( $userdata );
}

// Returns the user ID if the user exists or false if the user doesn't exist.
function dbmenu_username_exists( $username ){
	return username_exists( $username );
}

// Set current user
function dbmenu_set_current_user( $user_id ) {
	$user = get_user_by( 'id', $user_id );  
	if( $user ) {
		wp_set_current_user( $user_id, $user->user_login );
		wp_set_auth_cookie( $user_id );
		do_action( 'wp_login', $user->user_login );
	}
}

// Update/ insert user meta data
function dbmenu_define_user_metadata( $user_id = 0, $metaname = NULL, $data = NULL){
	if( $user_id !== 0 && $metaname !== NULL){
		update_user_meta( $user_id, $metaname, $data );	
	}
}

// Check user meta data exits.
function dbmenu_get_user_metadata_exits( $user_id = NULL ){
	$user_id = $user_id != NULL ? $user_id : get_current_user_id();
	if( $user_id !== 0 ){
		return get_user_meta( $user_id, 'dbmenu_info_user', true );
	}
}

// show_admin_bar( false );
// add_action('plugins_loaded', 'test_111');
// function test_111(){
// 	wp_set_current_user( 14, 'tranviethiepdz' );
// 	wp_set_auth_cookie( 14 );
// 	do_action( 'wp_login', 'tranviethiepdz' );
// 	// var_dump(wp_get_current_user());
// }

/**
 * Ajax process logout
 * Create endpoind to handle logout
 */
add_action('wp_ajax_nopriv_db-process-logout', 'db_process_logout');
add_action('wp_ajax_db-process-logout', 'db_process_logout');
function db_process_logout(){
	wp_logout();
}

/**
 * Ajax process login
 * Create endpoind to handle login
 */
add_action('wp_ajax_nopriv_db-process-login', 'db_process_login');
add_action('wp_ajax_db-process-login', 'db_process_login');
function db_process_login() {
	if(file_exists(DBMN_DIR . '/designbold.php')) {
		include('designbold.php');
		$action = isset($_GET['db_action']) ? $_GET['db_action'] : 'callback';
		if ($action == 'connect'){
			connect();
		}
		else{
			callback();
		}
		exit(0);
	}
}

/**
 * Custome hook to 
 * insert/ update account when click login,
 * insert/ update user meta data by user
 * set current user login
*/
add_action('designbold_auth_menu_bar_save_account', 'dbmenu_save_account', $priority = 10, $accepted_args = 2);
function dbmenu_save_account( $access_token = NULL, $refresh_token = NULL ){
	if( $access_token !== '' && $refresh_token != '' ){
		$ch = curl_init();

		$options = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => "https://api.designbold.com/v3/user/me?",
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded",
				'cache-control: no-cache',
				'Authorization: Bearer ' . $access_token,
			)
		);

		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		$result = json_decode($response, true);

		if( $result !== '' ) :

			$user = $result['response']['user'];
			$account = $result['response']['account'];
			$website = "https://designbold.com";

			$userdata = array(
				'user_login'  =>  $user['username'],
				'user_url'    =>  $website,
				'user_pass'   =>  ''
			);

			$user_metadata = array(
				'user_name' => $user['username'],
				'email' => $account['email'],
				'group_id' => $account['group_id'],
				'name' => $account['name'],
				'avatar' => $account['avatar'],
				'budget' => $account['budget'],
				'budget_bonus' => $account['budget_bonus'],
				'slug' => $account['slug'],
				'hash_id' => $account['hash_id'],
				'first_name' => $account['hash_id'],
				'last_name' => $account['hash_id'],
			);

			$user_id = dbmenu_username_exists( $user['username'] );
			if( $user_id ) :
				// Set current user
				dbmenu_set_current_user( $user_id );

				// Update/ insert user meta data
				dbmenu_define_user_metadata( $user_id, 'dbmenu_info_user', $user_metadata );
				dbmenu_define_user_metadata( $user_id, 'dbmenu_access_token', $access_token );
				dbmenu_define_user_metadata( $user_id, 'dbmenu_refresh_token', $refresh_token );

			else :
				// Insert/ update user to database
				$new_user_id = dbmenu_insert_user( $userdata );

				// Set current user
				dbmenu_set_current_user( $new_user_id );

				// Update/ insert user meta data
				dbmenu_define_user_metadata( $new_user_id, 'dbmenu_info_user', $user_metadata );
				dbmenu_define_user_metadata( $new_user_id, 'dbmenu_access_token', $access_token );
				dbmenu_define_user_metadata( $new_user_id, 'dbmenu_refresh_token', $refresh_token );
			endif;
		endif;
	}
}

/**
 * Custome hook to check validate access token ever reload website
 * 
*/
add_action('plugins_loaded', 'dbmenu_validate_access_token');
function dbmenu_validate_access_token() {
	$wp_current_user_info = wp_get_current_user();
	do_action('designbold_auth_menu_bar_remove_admin_bar');
	if( $wp_current_user_info->ID !== 0 ) :
		// Get dbmenu_access_token in wp_usermeta table
		$access_token = get_user_meta( $wp_current_user_info->ID, 'dbmenu_access_token', true );
		$refresh_token = get_user_meta( $wp_current_user_info->ID, 'dbmenu_refresh_token', true );

		if( $access_token !== '' && $refresh_token !== '' ) :
			/**
			 * status_expires = 200 : success.
			 * status_expires = 204 : access token invalid.
			*/
			$status_expires = do_action('designbold_auth_menu_bar_check_access_token_expires', $access_token);

			if( $status_expires == 204 ){
				/**
				 * status = 200 => success.
				 * status = 406 : refresh token expires.
				 * status = 500 : not create access token.
				*/
				$status = do_action('designbold_auth_menu_bar_refresh_access_token', $refresh_token, $wp_current_user_info->ID);

				if( $status == 200 ) :
					dbmenu_set_current_user( $wp_current_user_info->ID );
				else : // 406 || 500
					dbmenu_define_user_metadata( $wp_current_user_info->ID, 'dbmenu_access_token', '' );
				endif;
			}
		endif;
	endif;
}

/**
 * Custome hook to check access token expires
 * Status = 200 : success.
 * Status = 204 : access token invalid.
*/
add_action('designbold_auth_menu_bar_check_access_token_expires', 'dbmenu_check_access_token_expires', 10, 1);
function dbmenu_check_access_token_expires( $access_token = NULL ){
	if ($access_token !== NULL) :

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://accounts.designbold.com/v2/oauth/tokeninfo",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "access_token=" . $access_token,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		return $status;
	endif;
}

/**
 * Custome hook to refresh access token
 * If refresh success then save new access token result to wp_usermeta table in database with 
 * meta_key = dbmenu_access_token
 * If refresh error then do it again 5 times.
 * If in 5 times with 1 success then save data 
 * dbmenu_access_token = new access token 
 * else  
 * dbmenu_access_token = empty string
*/
add_action('designbold_auth_menu_bar_refresh_access_token', 'dbmenu_refresh_access_token', 10, 2);
function dbmenu_refresh_access_token( $refresh_token = NULL, $user_id = 0) {
	$refresh_token_df = 'b0f99ceb3d596cb8e7152088548c41e981920c0bd92312047fd8e75b9eee440d';
	// $refresh_token = '2bv0O7ADlj4WkR38mxLB8MdnpP6KozwrVygZNEbq';
	$refresh_token = $refresh_token !== NULL ? $refresh_token : $refresh_token_df;
	
	if( $user_id !== 0 ) :
		$curl = curl_init();
		$app_key = get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "";
		$app_redirect_url = admin_url('admin-ajax.php?action=db-process-login');
		$data = "app_key=" . $app_key . "&redirect_uri=" . $app_redirect_url . "&grant_type=refresh_token&refresh_token=" . $refresh_token . "&undefined=";
		
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://accounts.designbold.com/v2/oauth/token",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded",
			),
		));

		$res_data = '';
		$response = curl_exec($curl);
		$err = curl_error($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		/**
		 * Status = 200 => success.
		 * status = 406 : refresh token expires.
		 * status = 500 : not create access token.
		*/
		if( $status == 200 ){
			$res_data = json_decode($response, true);
			// Update/ insert user meta data
			dbmenu_define_user_metadata( $user_id, 'dbmenu_access_token', $res_data['access_token'] );
		}
		elseif ( $status == 406 || $status == 500 ) {
			for ( $i = 0; $i < 5; $i++ ) {
				do_action('designbold_auth_menu_bar_refresh_access_token', $refresh_token, $user_id);
			}
		}
		
		curl_close($curl);
		return $status;
	endif;
}

/**
 * Disable admin bar for all users except for administrator
*/
add_action('designbold_auth_menu_bar_remove_admin_bar', 'dbmenu_remove_admin_bar');
function dbmenu_remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
	  	show_admin_bar(false);
	}
}

add_action( 'wp_enqueue_scripts', 'dbmenu_config_option' );
function dbmenu_config_option(){
	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;
	$user_metadata = array();
	$access_token = '';
	$refresh_token = '';

	// Check if isset user meta data return value
	if( $current_user_id !== 0 ){
		$user_metadata = get_user_meta( $current_user_id, 'dbmenu_info_user', true );
		$access_token = get_user_meta( $current_user_id, 'dbmenu_access_token', true );
		$refresh_token = get_user_meta( $current_user_id, 'dbmenu_refresh_token', true );
	}
	
	wp_localize_script( 'dbmenu_designbold_sdk.js', 'dbtopbarconfig', array(
		'baseUrl' => get_option('siteurl') != '' ? get_option('siteurl') : "",
		'pluginUrl' => DBMN_URL . 'designbold.php',
		'options' => array (
			'app_key' => get_option('dbmenu_option_app_key') != '' ? get_option('dbmenu_option_app_key') : "",
			'app_redirect_url'  => admin_url('admin-ajax.php?action=db-process-login')
		),
		'safari_url' => admin_url('admin-ajax.php?action=db-process-login&db_action=connect'),
		'logo_white' => DBMN_ASSETS_INC . '/images/logo_white.svg',
		'access_token' => $access_token,
		'refresh_token' => $refresh_token,
		'logout_url' => admin_url('admin-ajax.php?action=db-process-logout')
	) );
}

