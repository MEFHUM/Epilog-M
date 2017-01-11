<?php

/*
 * Plugin Name: Epilog Default Content
 * Description: Sets default content in editor, such as name, source etc.
 * Version: 1.0
 * Author: Samet Altun
 * Author URI: http://github.com/sametaltun
 */


// Register Functions
function epilog_default_content_settings() {
	
	add_settings_section('epilog_default_content_section', 'Epilog Varsayılan İçerik', 'epilog_default_content_set', 'epilog_default_content');
	
	add_settings_field('epilog_default_title_id', 'Epilog Varsayılan Başlık', 'epilog_default_title_input', 'epilog_default_content', 'epilog_default_content_section');

	register_setting('epilog_default_content_section', 'epilog_default_title_id');

	add_settings_field('epilog_default_content_id', 'Epilog Varsayılan İçerik', 'epilog_default_content_input', 'epilog_default_content',  'epilog_default_content_section');

	register_setting('epilog_default_content_section', 'epilog_default_content_id');


}
add_action('admin_init', 'epilog_default_content_settings');



//register input title field
function epilog_default_title_input(){
	echo '<input type="text" class="widefat" name="epilog_default_title_id" value="'.get_option('epilog_default_title_id').'"/>';


}


//register input Content field
function epilog_default_content_input(){
	echo '<textarea class="large-text code" cols="50" rows="10" name="epilog_default_content_id">'.get_option('epilog_default_content_id').'</textarea>';

}

//Section Register Funtion
function epilog_default_content_set(){
	echo '<p>DC by Samet Altun</p>';

}


// Epilog Default Content Menu Register
function epilog_default_content_menu_register() {
	add_options_page('Epilog Varsayılan İçerik Ayarları', 'Epilog Varsayılan İçerik', 'manage_options', 'epilog_default_content', 'epilog_default_content_menu');

}
add_action('admin_menu', 'epilog_default_content_menu_register');



//Epilog Default Content Menu content
function epilog_default_content_menu() {
?>
	<form action="options.php" method="POST">
		<?php do_settings_sections('epilog_default_content');?>
		<?php settings_fields('epilog_default_content_section');?>
		<?php submit_button();?>
	</form>

<?php }

// Set Default Content
function epilog_default_title( $title ) {

	$title = get_option('epilog_default_title_id');
	return $title;

}
add_filter( 'default_title', 'epilog_default_title' );

function epilog_editor_content( $content ) {

	$content = get_option('epilog_default_content_id');
	return $content;

}
add_filter( 'default_content', 'epilog_editor_content' );





?>