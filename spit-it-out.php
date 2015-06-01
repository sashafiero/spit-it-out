<?php
/*
Plugin Name: Spit It Out
Description: For logged in admin users, displays a box at the top left with various information about the theme page
Version:	 1.0
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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
global $save_as_option_name; // the `option_name` field in the `wp_options` table
$save_as_option_name = 'spititout';

global $spitio_option_list;
$spitio_option_list = array(
	array(
		'description' => 'Spit It Out Active',
		'db_name' => 'active',
		'init' => '1'
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
// load in js
function spitio_scripts_important() {
	wp_register_script( 'spitio-js', plugins_url( '/js/scripts.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'spitio-js' );
	}
add_action( 'wp_enqueue_scripts', 'spitio_scripts_important', 10 );

// load in styles
function spitio_styles() {
	// Register the style like this for a plugin:
	wp_register_style( 'spitio-css', plugins_url( '/css/styles.css', __FILE__ ) );
	wp_enqueue_style( 'spitio-css' );
	}
add_action( 'wp_enqueue_scripts', 'spitio_styles' );



/////////////////////////////////////////////////////////////////////
// activation tasks
function spitio_activate() {
	global $spitio_option_list;

	$init_options = array();

	foreach($spitio_option_list as $option) {
		$init_options[$option['db_name']] = $option['init'];
		}

	add_option($save_as_option_name, $init_options);
	}
register_activation_hook( __FILE__, 'spitio_activate' );



/////////////////////////////////////////////////////////////////////
// all the things we may want to display that require more than just echo $thing or spitio_prettify($thing)


//////////////////////////////////
// Get current page template name
add_filter( 'template_include', 'spitio_var_template_include', 1000 );
function spitio_var_template_include( $t ){
	$GLOBALS['spitio_current_theme_template'] = basename($t);
	return $t;
}

// this function was originally just added to a custom template, so I gave it the option of either returning or echoing the results
function spitio_get_current_template( $echo = false ) {
	if( !isset( $GLOBALS['spitio_current_theme_template'] ) )
		return false;
	if( $echo )
		echo $GLOBALS['spitio_current_theme_template'];
	else
		return $GLOBALS['spitio_current_theme_template'];
}



/////////////////////////////////////////////////////////////////////
// pretty up things like arrays and objects so it's readable
function spitio_prettify($thingie) {
	$pretty_thing = '<pre>';
	$pretty_thing .= htmlentities(var_export($thingie, true));
	$pretty_thing .= '</pre>';
	return $pretty_thing;
	}



/////////////////////////////////////////////////////////////////////
// display the box!
add_action('wp_footer', 'spitio_wp_foot');
function spitio_wp_foot(){
	$spitiooptions = get_option('spititout');

	if(is_super_admin() && ($blurtoptions['active'] == '1')){

		echo '<div class="spitio_box" style="width: 1000px; height: 700px;">'.PHP_EOL.'<h3>Developer Information</h3>'.PHP_EOL;

		if ($spitiooptions['templatefile'] === '1') {
			echo '<hr /><p><b>Current Template File</b>: '.blurtbox_get_current_template().'</p>'.PHP_EOL;
			}

		if ($spitiooptions['currentquery'] === '1') {
			echo '<hr /><b>Current Query</b>:<br />'.bb_pretty(get_queried_object()).'<br />'.PHP_EOL;
			}

		if ($spitiooptions['server'] === '1') {
			echo '<hr /><b>$_SERVER</b>:<br />'.bb_pretty($_SERVER).'<br />'.PHP_EOL;
			}

		if ($spitio_options['request'] === '1') {
			echo '<r /><b>$_REQUEST</b>:<br />'.bb_pretty($_REQUEST).'<br />'.PHP_EOL;
			}

		if ($spitiooptions['files'] === '1') {
			echo '<hr /><b>$_FILES</b>:<br />'.bb_pretty($_FILES).'<br />'.PHP_EOL;
			}

		if ($spitio_options['session'] === '1') {
			echo '<r /><b>$_SESSION</b>:<br />'.bb_pretty($_SESSION).'<br />'.PHP_EOL;
			}

		if ($spitiooptions['error'] === '1') {
			echo '<hr /><b>Last Error that Occurred - error_get_last()</b>:<br />'.bb_pretty(error_get_last()).'<br />'.PHP_EOL;
			}


		echo '</div>';
		}
	}



/////////////////////////////////////////////////////////////////////
// WP Admin page to control options
add_action( 'admin_menu', 'admin_spitio' );
function admin_spitio() {
	add_options_page( 'Spit It Out Options', 'Spit It Out', 'manage_options', 'spit-it-out', 'spitio_options' );
	}


function spitio_options() {
	// variables for the field and option names
	global $spitio_option_list, $save_as_option_name;


	$hidden_field_name = 'spitio_submit_hidden';


	// Read in existing option value from database
	$spitio_options = get_option($save_as_option_name);


	// See if the user has posted us some information
	// If they did, this hidden field will be set to 'Y'
	if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
		// Read their posted value
		foreach($spitio_option_list as $option) {
			$spitio_options[$option['db_name']] = $_POST[$option['db_name']];
			}

		// Save the posted value in the database
		update_option($save_as_option_name, $spitio_options);

		// Put a "settings saved" message on the screen
		echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';

		}
	?>


	<div class="wrap">
		<h2>Spit It Out Options</h2>
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

		</form>
	</div>
<?php
	}























