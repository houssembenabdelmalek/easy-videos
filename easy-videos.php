<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.linkedin.com/in/houssem-ba/
 * @since             1.0.0
 * @package           Easy_Videos
 *
 * @wordpress-plugin
 * Plugin Name:       Easy Videos
 * Plugin URI:        https://localhost.com
 * Description:       Import videos from the Linus Tech Tips youtube channel
 * Version:           1.0.0
 * Author:            Houssem Eddine
 * Author URI:        https://www.linkedin.com/in/houssem-ba/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy-videos
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EASY_VIDEOS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy-videos-activator.php
 */
function activate_easy_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-videos-activator.php';
	Easy_Videos_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-easy-videos-deactivator.php
 */
function deactivate_easy_videos() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-videos-deactivator.php';
	Easy_Videos_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_easy_videos' );
register_deactivation_hook( __FILE__, 'deactivate_easy_videos' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-easy-videos.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function easy_videos_on_activation(){
	// create the custom table
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'easy_videos';
	$charset_collate = $wpdb->get_charset_collate();
	
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		video_id varchar(80) NOT NULL default '',
		video_title varchar(500) NOT NULL default '',
		video_description varchar(500) NOT NULL default '',
		video_thumbnail varchar(500) NOT NULL default '',
		video_pub_time  datetime DEFAULT '0000-00-00 00:00:00' NOT NULL) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'easy_videos_on_activation' );

function shortcode_easy_videos(){
	/**
	 * This function is provided for demonstration purposes only.
	 *
	 * An instance of this class should be passed to the run() function
	 * defined in Easy_Videos_Loader as all of the hooks are defined
	 * in that particular class.
	 *
	 * The Easy_Videos_Loader will then create the relationship
	 * between the defined hooks and the functions defined in this
	 * class.
	 */
	
	global $wpdb;
	$videoIdver = $yt->id->videoId ;
	$table_name = $wpdb->prefix . 'easy_videos';
	$arr_list = $wpdb->get_results("SELECT * FROM $table_name");
								  
	$html = '';
				if (!empty($arr_list)) {
					$html .=  '<span class="popuptext" id="myPopup">';
					$html .= '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'. $arr_list[0]->video_id .'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
					$html .=  '</span>';
					$html .=  '<div class="video-list row">';
					foreach ($arr_list as $video_import) {
						
						$html .= '<div class="col-md-6">';
						$html .= '<img class="thumbnail" src="'.$video_import->video_thumbnail.'" width="100%" data-video="'.$video_import->video_id.'"  />';
						$html .= '<span class="meta-video">';
						$html .= '<b>Title: </b>'.$video_import->video_title.'<br><hr>' ;
						$html .= '<b>Description: </b>'.$video_import->video_description.'<br><hr>' ;
						$html .= '<b>Publish time: </b>'.date_i18n('l d/m/Y \a\t g:i', strtotime($video_import->video_pub_time)).'<br><hr>' ;
						$html .= '<div class="popup" data-video="'.$video_import->video_id.'" >Preview </div>';
						$html .= '<hr>';
						$html .= '</span>';
						$html .= '</div>';
					}
					$html .= '</div>';
					
				}
				
	return $html ;
}
add_shortcode('easyvideos', 'shortcode_easy_videos');

function run_easy_videos() {

	$plugin = new Easy_Videos();
	$plugin->run();

}
run_easy_videos();

