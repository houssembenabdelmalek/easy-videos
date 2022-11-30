<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/houssem-ba/
 * @since      1.0.0
 *
 * @package    Easy_Videos
 * @subpackage Easy_Videos/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_Videos
 * @subpackage Easy_Videos/admin
 * @author     Houssem Eddine <houssem.ba@outlook.com>
 */
class Easy_Videos_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', [$this, 'easy_videos_plugin_page']);
		//add_action( 'init', [$this, 'easy_videos_page_init'], 0);
		add_action('wp_ajax_import_video', [$this, 'import_video']);
		add_action( 'wp_ajax_nopriv_import_video', [$this, 'import_video']);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/easy-videos-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bootstrap-grid.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/easy-videos-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Gets the request parameter.
	 *
	 * @param      string  $key      The query parameter
	 * @param      string  $default  The default value to return if not found
	 *
	 * @return     string  The request parameter.
	 */
	public function get_request_parameter( $key, $default = '' ) {
		// If not request set
		if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
			return $default;
		}
	
		// Set so process it
		return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
	}
	

	public function easy_videos_plugin_page() {
		add_menu_page(
			'Easy Videos', // page_title
			'Easy Videos', // menu_title
			'manage_options', // capability
			'easy-videos', // menu_slug
			array( $this, 'easy_videos_admin_page' ), // function
			'dashicons-video-alt3', // icon_url
			75 // position
		);
	}
	
	public function import_video() {
		
		
		// Récupération des données 
		$videoId = sanitize_text_field( $_POST['dataId'] );
		$dataTitle = sanitize_text_field( $_POST['dataTitle'] );
		$dataDesc = sanitize_text_field( $_POST['dataDesc'] );
		$dataDate = sanitize_text_field( $_POST['dataDate'] );
		$dataThumb = sanitize_text_field( $_POST['dataThumb'] );
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'easy_videos';

		$video_verif = $wpdb->get_row("SELECT * FROM $table_name WHERE video_id='$videoId' ORDER BY id DESC LIMIT 0,1");
		if ($video_verif){ 

			$wpdb->delete($table_name, array("video_id" => $videoId), array("%s") );
			wp_send_json_success( 'remouve success' ) ;
		}
		else {
			$wpdb->insert($table_name, array("video_id" => $videoId, "video_title" => $dataTitle, "video_description" => $dataDesc, "video_thumbnail" => $dataThumb, "video_pub_time" => $dataDate), array("%s", "%s", "%s", "%s", "%s") );
			wp_send_json_success( 'success' ) ;
		}
		
	}

	public function getYTList($api_url = '') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		$arr_result = json_decode($response);
		if (isset($arr_result->items)) {
			return $arr_result;
		} elseif (isset($arr_result->error)) {
			var_dump($arr_result); //this line gives you error info if you are not getting a video list.
		}
	}
	
		
	public function easy_videos_admin_page() {
		$this->badge_fl_options = get_option( 'badge_fl_option_name' ); ?>

		<div class="container">
			<h2>Easy Videos</h2>
			<p></p>
			<?php settings_errors(); ?>
			<?php

				$arr_list = array();
				$GOOGLE_API_KEY=  'YOUR_GOOGLE_API_KEY';
				$channel = 'ID_YouTube_Channel';
				$max_result = 8;
				
				if(!empty(self::get_request_parameter('pageToken'))){
					$url = "https://www.googleapis.com/youtube/v3/search?channelId=".$channel."&order=date&part=snippet&type=video&maxResults=". $max_result ."&pageToken=". self::get_request_parameter('pageToken') ."&key=". $GOOGLE_API_KEY;
				}
				else {
					$url = "https://www.googleapis.com/youtube/v3/search?channelId=".$channel."&order=date&part=snippet&type=video&maxResults=". $max_result ."&key=". $GOOGLE_API_KEY;
				}
				$arr_list = self::getYTList($url);
				if (!empty($arr_list)) {
					echo   '<span class="popuptext" id="myPopup"></span>';
					echo '<div class="video-list row">';
					foreach ($arr_list->items as $yt) {
						echo '<div class="col-md-3">';
						echo '<img class="thumbnail" src="'.$yt->snippet->thumbnails->medium->url.'" data-video="'.$yt->id->videoId.'" width="95%" />';
						echo  '<span class="meta-video">';
						echo   '<b>Title: </b>'.$yt->snippet->title.'<br><hr>' ;
						echo   '<b>Description: </b>'.$yt->snippet->description.'<br><hr>' ;
						echo   '<b>Publish time: </b>'.date_i18n('l d/m/Y \a\t g:i', strtotime($yt->snippet->publishedAt)).'<br><hr>' ;
						?>
							<div class="popup" data-video="<?php echo $yt->id->videoId ; ?>" >Preview </div> | 
							<div class="importvideo"
							 	data-title="<?php echo $yt->snippet->title ; ?>"
							 	data-id="<?php echo $yt->id->videoId ; ?>" 
							 	data-desc="<?php echo $yt->snippet->description ; ?>" 
							 	data-date="<?php echo $yt->snippet->publishedAt ; ?>" 
							 	data-thumb="<?php echo $yt->snippet->thumbnails->medium->url ; ?>" 
								data-nonce="<?php echo wp_create_nonce('import_video'); ?>" 
								data-action="import_video"
    							data-ajaxurl="<?php echo admin_url( 'admin-ajax.php' ); ?>" 
								id="import-video" >
							  <?php
							  	
								  global $wpdb;
								  $videoIdver = $yt->id->videoId ;
								  $table_name = $wpdb->prefix . 'easy_videos';
						  
								  $video_verif = $wpdb->get_row("SELECT * FROM $table_name WHERE video_id='$videoIdver' ORDER BY id DESC LIMIT 0,1");
								  if ($video_verif){ 
									  echo "Remove" ;
								  }
								  else {
									echo "Import" ;
								  }
							  ?> 
							</div>
							<input type="hidden" name="action" value="import_video">
						<?php
						
						echo   '<hr>';
						echo '</span>';
						 
						echo '</div>';
					}
					echo '</div>';
					
					if (isset($arr_list->prevPageToken)) {
						echo ' <a href="'.esc_url($_SERVER['REQUEST_URI']).'&pageToken='.$arr_list->prevPageToken.'" id="loadmore"><< Prev</a> | ';
					}
					
					if (isset($arr_list->nextPageToken)) {
						echo ' <a href="'.esc_url($_SERVER['REQUEST_URI']).'&pageToken='.$arr_list->nextPageToken.'" id="loadmore">Next >></a> ';
					}
					$total_video = $arr_list->pageInfo->totalResults;
					echo ' | '.$total_video.' videos' ;
				}
			?>
			
		</div>
		
	<?php }


}
