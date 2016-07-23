<?php
/*
Plugin Name: Spit It Out
Description: Provides different ways to display various developer-useful information about the theme page
Version:	 2.2
Author: Christy.pw
Author URI: http://christy.pw
Plugin URI: http://christy.pw/web-mobile/wordpress-plugins/spit-it-out/
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/////////////////////////////////////////////////////////////////////
//region updater service
require 'plugin-updates/plugin-update-checker.php';
$ExampleUpdateChecker = PucFactory::buildUpdateChecker(
    'http://wpplugs.stellarstudios.net/spit-it-out/info.json',
    __FILE__
);
//endregion




// the options we want to offer
// they will be stored in the database as an array under 'spititout' in the options table
// the descriptions are for the settings page
// the stored array will actually resemble:
/*
array (
	'active' -> '1',
	'templatefile' -> '0'
	)

*/
global $spittio_save_as; // the `option_name` field in the `wp_options` table
$spittio_save_as = 'spititout';

global $spitio_option_list;
$spitio_option_list = array(
	array(
		'description' => 'Spit It Out Overlay Active',
		'db_name' => 'active',
		'init' => '0'
		),
	array(
		'description' => 'Current Template File Name',
		'db_name' => 'templatefile',
		'init' => '0'
		),
	array(
		'description' => 'Current Query',
		'db_name' => 'currentquery',
		'init' => '1'
		),
	array(
		'description' => '$wp_rewrite->rules',
		'db_name' => 'rewriterules',
		'init' => '1'
		),
	array(
		'description' => '$_SERVER',
		'db_name' => 'server',
		'init' => '0'
		),
	array(
		'description' => '$_REQUEST',
		'db_name' => 'request',
		'init' => '0'
		),
	array(
		'description' => '$_FILES',
		'db_name' => 'files',
		'init' => '0'
		),
	array(
		'description' => '$_SESSION',
		'db_name' => 'session',
		'init' => '0'
		),
	array(
		'description' => 'Last Error that Occurred', // error_get_last() - returns an array
		'db_name' => 'error',
		'init' => '0'
		)
	);



/////////////////////////////////////////////////////////////////////
//region load in js & styles
function spitio_scripts_important() {
	wp_register_script( 'spitio-js', plugins_url( '/js/scripts.js', __FILE__ ), array( 'jquery' ) );
	//wp_enqueue_script('jquery-ui-core');
	//wp_enqueue_script('jquery-ui-draggable');
	//wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script( 'spitio-js' );
	}
add_action( 'wp_enqueue_scripts', 'spitio_scripts_important', 10 );

function spitio_styles() {
	wp_register_style( 'spitio-css', plugins_url( '/css/styles.css', __FILE__ ) );
	wp_enqueue_style( 'spitio-css' );
	}
add_action( 'wp_enqueue_scripts', 'spitio_styles' );
//endregion


/////////////////////////////////////////////////////////////////////
//region activation tasks
function spitio_activate() {
	global $spitio_option_list, $spittio_save_as;

	$init_options = array();

	foreach($spitio_option_list as $option) {
		$init_options[$option['db_name']] = $option['init'];
		}

	add_option($spittio_save_as, $init_options);
	}
register_activation_hook( __FILE__, 'spitio_activate' );
//endregion




/////////////////////////////////////////////////////////////////////
//region add a "Settings" link in the Spit It Out entry in the Plugin list
add_filter('plugin_action_links', 'spitio_plugin_action_links', 10, 2);

function spitio_plugin_action_links($links, $file) {
	static $this_plugin;

	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}

	if ($file == $this_plugin) {
		$settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=spit-it-out">Settings</a>';
		array_unshift($links, $settings_link);
	}

	return $links;
}
//endregion




/////////////////////////////////////////////////////////////////////
// all the things we may want to display that require more than just echo $thing or spitio_prettify($thing)


//////////////////////////////////
//region Get current page template name
add_filter( 'template_include', 'spitio_var_template_include', 1000 );
function spitio_var_template_include( $t ){
	$GLOBALS['spitio_current_theme_template'] = basename($t);
	return $t;
	}
function spitio_get_current_template() {
	if( !isset( $GLOBALS['spitio_current_theme_template'] ) )
		return false;
	return $GLOBALS['spitio_current_theme_template'];
	}
//endregion



/////////////////////////////////////////////////////////////////////
//region spitio_prettify - pretty up things like arrays and objects so it's readable
function spitio_prettify($thingie) {
	$pretty_thing = '<pre>';
	$pretty_thing .= htmlentities(var_export($thingie, true));
	$pretty_thing .= '</pre>';
	return $pretty_thing;
	}
//endregion



/////////////////////////////////////////////////////////////////////
//region separate function, so it can be used in the plugin's normal overlay box,
// or in a shortcode or template tag
function show_spitio_content($spitiooptions){
	ob_start();
	echo '<h3>Developer Information</h3>'.PHP_EOL;
	if ($spitiooptions['templatefile'] === '1') {
		echo '<hr /><p><b>Current Template File</b>: '.spitio_get_current_template().'</p>'.PHP_EOL;
		}

	if ($spitiooptions['currentquery'] === '1') {
		echo '<hr /><p><b>Current Query</b>:</p>'.spitio_prettify(get_queried_object()).PHP_EOL;
		}

	if ($spitiooptions['rewriterules'] === '1') {
		global $wp_rewrite;
		echo '<hr /><p><b>WP Rewrite Rules</b>:</p>'.spitio_prettify($wp_rewrite->rules).PHP_EOL;
		}

	if ($spitiooptions['server'] === '1') {
		echo '<hr /><p><b>$_SERVER</b>:</p>'.spitio_prettify($_SERVER).PHP_EOL;
		}

	if ($spitiooptions['request'] === '1') {
		echo '<hr /><p><b>$_REQUEST</b>:</p>'.spitio_prettify($_REQUEST).PHP_EOL;
		}

	if ($spitiooptions['files'] === '1') {
		echo '<hr /><p><b>$_FILES</b>:</p>'.spitio_prettify($_FILES).PHP_EOL;
		}

	if ($spitiooptions['session'] === '1') {
		echo '<hr /><p><b>$_SESSION</b>:</p>'.spitio_prettify($_SESSION).PHP_EOL;
		}

	if ($spitiooptions['error'] === '1') {
		echo '<hr /><p><b>Last Error that Occurred - error_get_last()</b>:</p>'.spitio_prettify(error_get_last()).PHP_EOL;
		}
	return ob_get_clean();
	}
//endregion





/////////////////////////////////////////////////////////////////////
//region display the box! (on top of every page of the site if user is admin)
add_action('wp_footer', 'spitio_wp_foot');
function spitio_wp_foot(){
	global $spittio_save_as;
	$spitiooptions = get_option($spittio_save_as);

	if(is_super_admin() && ($spitiooptions['active'] === '1')){

		echo '<div id="spitio_box" class="closed"><div id="spitio_content">'.PHP_EOL;
		echo show_spitio_content($spitiooptions).PHP_EOL;
		//echo '<div class="closespitio">&#8855;</div>'.PHP_EOL;
		echo '</div>'.PHP_EOL;
		echo '<img id="spitio_badge" src="'.plugins_url( '/spitio-badge.png', __FILE__ ).'" alt="Spit It Out" />'.PHP_EOL;
		echo '</div>';
		}
	}
//endregion





/////////////////////////////////////////////////////////////////////
//region function for use in templates. if you call it with show_spitio(false)
// it will show the stuff even if the viewer is not a logged in admin
function show_spitio($adminonly = true) {
	global $spittio_save_as;
	$spitiooptions = get_option($spittio_save_as);
	if(is_super_admin() || $adminonly === false){
		echo show_spitio_content($spitiooptions);
		}
	}
//endregion




/////////////////////////////////////////////////////////////////////
//region shortcode for use in WYSIWYG content for displaying Spit It Out stuff
// option to display if user is not a logged in admin; default to show only if admin.
// [spit-it-out adminonly="true"] or "false"
function spit_it_out($atts, $content = null) {
	global $spittio_save_as;
	$options = shortcode_atts( array(
		'adminonly' => 'true'
		), $atts);

	$spitiooptions = get_option($spittio_save_as);

	if(is_super_admin() || // if the viewer is an admin... OR
	($options['adminonly'] === 'false')) { // if "admin-only" is false
		return show_spitio_content($spitiooptions);
		}
	return false;
	}
add_shortcode('spititout', 'spit_it_out');
//endregion





/////////////////////////////////////////////////////////////////////
//region WP Admin page to control options
add_action( 'admin_menu', 'admin_spitio' );
function admin_spitio() {
	add_options_page( 'Spit It Out Options', 'Spit It Out', 'manage_options', 'spit-it-out', 'spitio_options' );
	}


function spitio_options() {
	// variables for the field and option names
	global $spitio_option_list, $spittio_save_as;


	$hidden_field_name = 'spitio_submit_hidden';

	// Read in existing option value from database
	$spitio_options = get_option($spittio_save_as);


	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// Read their posted value
		foreach($spitio_option_list as $option) {
			if(isset($_POST[$option['db_name']])) {
				$spitio_options[$option['db_name']] = $_POST[$option['db_name']];
				}
			else {
				$spitio_options[$option['db_name']] = "0";
				}
			}

		// Save the posted value in the database
		update_option($spittio_save_as, $spitio_options);

		// Put a "settings saved" message on the screen
		echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
		}

		//region settings page output
	?>
	<div class="wrap">
		<h2><img id="spitio_badge" src="<?=plugins_url( '/spitio-badge.png', __FILE__ )?>" alt="Spit It Out" /> Spit It Out Options</h2>
		<form name="spitio_options" method="post" action="">
			<input type="hidden" name="<?=$hidden_field_name?>" value="Y">

			<?php
			foreach($spitio_option_list as $option) {
				?>
				<p>
				<input name="<?=$option['db_name']?>" type="checkbox" value="1" <?php if ($spitio_options[$option['db_name']] === '1') { echo ' checked="checked"'; } ?> />
				&nbsp; <?=$option['description']?>
				</p>
				<?php
				}
			?>

			<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>

			<p>There is a shortcode for use in WYSIWYG content.<br />
				The option is to display if user is not a logged in admin; default to show only if admin.<br />
				[spititout] or [spititout adminonly="false"] </p>
			<p>There is a function for use in templates.<br />
				If you call it with <pre>echo show_spitio(false);</pre>, it will show the stuff even if the viewer is not a logged in admin.<br />
				<pre>echo show_spitio()</pre> or <pre>echo show_spitio(false)</pre></p>
			<p>You can also <pre>echo spitio_prettify($thingie);</pre> to pretty up things like arrays and objects so they're human readable.</p>
		</form>
	</div>
<?php
    //endregion
	}
//endregion




















