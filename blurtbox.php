<?php
/*
Plugin Name: Blurtbox
Description: For logged in admin users, displays a box at the top left with various information about the theme page
Version:     0.1
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



// Right now, this just blurts out the current template file name.
// I'd love to have an admin for the plugin to checkbox what to blurt
// like template file name
// page title
// ????
// if/when you get these as options, will need to do an "activate" thingie
// and a "delete" thingie to set defaults, and to remove settings from database, respectively




/////////////////////////////////////////////////////////////////////
// load in js
function blurtbox_scripts_important() {
    wp_register_script( 'blurtbox-js', plugins_url( '/js/blurtbox.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script( 'blurtbox-js' );
	}
add_action( 'wp_enqueue_scripts', 'blurtbox_scripts_important', 10 );

// load in styles
function blurtbox_styles() {
    // Register the style like this for a plugin:
    wp_register_style( 'blurtbox-css', plugins_url( '/css/blurtbox.css', __FILE__ ) );
    wp_enqueue_style( 'blurtbox-css' );
	}
add_action( 'wp_enqueue_scripts', 'blurtbox_styles' );



/////////////////////////////////////////////////////////////////////
// blurtbox function to echo out whatever is in a variable (nicely)
function bb_blurt($printme, $echoplz = true) {
	$bb_thing = '<pre>';
	$bb_thing .= htmlentities(var_export($printme));
	$bb_thing .= '</pre>';
	if ($echoplz) { echo $bb_thing; }
	else { return $bb_thing; }
	}


/////////////////////////////////////////////////////////////////////
// Get current page template name
add_filter( 'template_include', 'blurtbox_var_template_include', 1000 );
function blurtbox_var_template_include( $t ){
	$GLOBALS['blurtbox_current_theme_template'] = basename($t);
	return $t;
}

// this function was originally just added to a custom template, so I gave it the option of either returning or echoing the results
function blurtbox_get_current_template( $echo = false ) {
	if( !isset( $GLOBALS['blurtbox_current_theme_template'] ) )
		return false;
	if( $echo )
		echo $GLOBALS['blurtbox_current_theme_template'];
	else
		return $GLOBALS['blurtbox_current_theme_template'];
}




/////////////////////////////////////////////////////////////////////
// display the blurtbox
add_action('wp_footer', 'blurtbox_wp_foot');
function blurtbox_wp_foot(){
	if(is_super_admin() && get_option('blurtbox_active') == '1'){
    ?>
    <div class="blurtbox" style="width: 400px; height: 100px;">
	    <p>Current Template File: <?php blurtbox_get_current_template(true); ?></p>
	</div>
    <?php }
}



/////////////////////////////////////////////////////////////////////
// WP Admin page to control options
add_action( 'admin_menu', 'admin_blurtbox' );
function admin_blurtbox() {
	add_options_page( 'Blurtbox Options', 'Blurtbox', 'manage_options', 'blurtbox', 'blurtbox_options' );
	}


function blurtbox_options() {
	// variables for the field and option names
    $hidden_field_name = 'blurtbox_submit_hidden';


    // more options to include later
    /*
		$_SERVER (all of it, or just a few bits)

		whether to give back the result as just echoing it out in a box, or json, or...?

		ability to echo `$wpdb->queries` if it exists

		error_get_last() - returns an array


	*/

	$blurtbox_option_list = array(
		array(
			'description' => 'Blurtbox Active',
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
			'description' => 'Last Error that Occurred',
			'db_name' => 'error',
			'init' => '0'
			)
		);


	$option_values = array();

	foreach($blurtbox_option_list as $option) {
		$option_values[$option['db_name']] = $option['init'];
		}



	// start things up if they haven't already been
	add_option('blurtbox', $option_values);



	// Read in existing option value from database
    $blurt_options = get_option( 'blurtbox' );





// NEXT STEP : read in submitted info and update them in the database
// settings saved msg

// form for changing options













// below this is how it was working before I knew I ought to cram all options into an array
// which would be serialized into one option entry in the database
/*

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_value['bb_active'] = $_POST[ $opt_input_name['bb_active'] ];

        // Save the posted value in the database
        update_option( $opt_db_names['bb_active'], $opt_value['bb_active'] );

        // Put a "settings saved" message on the screen

?>
	<div class="updated"><p><strong><?php _e('Settings saved.', 'blurtbox' ); ?></strong></p></div>
<?php }  */ ?>



	<div class="wrap">
		<h2>Blurtbox Options</h2>
		<?php /*

		<form name="blurtbox_options" method="post" action="">
			<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">


			<p><?php _e("Blurtbox Active:", 'blurtbox' ); ?>
			 <input name="<?=$opt_input_name['bb_active']?>" type="checkbox" value="1" <?php if ($opt_value['bb_active'] == '1') { echo ' checked="checked"'; }?>>
			</p><hr />

			<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>

		</form>
		*/
		echo '<pre>';
		echo htmlentities(var_export($blurt_options));
		echo '</pre>';


		//bb_blurt($blurt_options);
		?>

	</div>
<?php
	}
























