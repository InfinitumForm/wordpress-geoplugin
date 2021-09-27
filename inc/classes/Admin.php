<?php
/**
 * Settings page
 *
 * @link          http://infinitumform.com/
 * @since         8.0.0
 * @package       cf-geoplugin
 * @author        Ivijan-Stefan Stipic
 * @version       3.0.0
 *
 */
 // If someone try to called this file directly via URL, abort.
if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
if ( ! defined( 'ABSPATH' ) ) { exit; }

if(!class_exists('CFGP_Admin')) :
class CFGP_Admin extends CFGP_Global {
	function __construct(){
		$this->add_action( 'admin_bar_menu', 'admin_bar_menu', 90, 1 );
		$this->add_action( 'admin_enqueue_scripts', 'register_scripts' );
		$this->add_action( 'admin_enqueue_scripts', 'register_scripts_ctp' );
		$this->add_action( 'admin_enqueue_scripts', 'register_style' );
		$this->add_action( 'admin_init', 'admin_init' );
		
		$this->add_action('manage_edit-cf-geoplugin-country_columns', 'rename__cf_geoplugin_country__column');
		$this->add_action('manage_edit-cf-geoplugin-region_columns', 'rename__cf_geoplugin_region__column');
		$this->add_action('manage_edit-cf-geoplugin-city_columns', 'rename__cf_geoplugin_city__column');
		$this->add_action('manage_edit-cf-geoplugin-postcode_columns', 'rename__cf_geoplugin_postcode__column');
		
		$this->add_action('wp_ajax_cfgp_load_regions', 'ajax__cfgp_load_regions');
		$this->add_action('wp_ajax_cfgp_load_cities', 'ajax__cfgp_load_cities');
	}
	
	public function ajax__cfgp_load_regions () {
		
		$country_code = CFGP_U::request('country_code');
		$options = array();
		
		if(is_array($country_code))
		{
			foreach($country_code as $cc){
				$regons = CFGP_Library::get_regions($cc);	
				foreach( $regons as $key => $fetch ){
					$options[]= array(
						'key' => strtolower(sanitize_title($fetch['region'])),
						'value' => $fetch['region']
					);
				}
			}
		}
		else
		{
			$regons = CFGP_Library::get_regions($country_code);	
			foreach( $regons as $key => $fetch ){
				$options[]= array(
					'key' => strtolower(sanitize_title($fetch['region'])),
					'value' => $fetch['region']
				);
			}
		}
		
		wp_send_json($options);
	}
	
	public function ajax__cfgp_load_cities () {
		$country_code = CFGP_U::request('country_code');
		$options = array();
		
		if(is_array($country_code))
		{
			foreach($country_code as $cc){
				$cities = CFGP_Library::get_cities($cc);
				foreach( $cities as $fetch ){
					$options[]= array(
						'key' => strtolower(sanitize_title($fetch)),
						'value' => $fetch
					);
				}
			}
		}
		else
		{
			$cities = CFGP_Library::get_cities($country_code);
			foreach( $cities as $fetch ){
				$options[]= array(
					'key' => strtolower(sanitize_title($fetch)),
					'value' => $fetch
				);
			}
		}
		
		wp_send_json($options);
	}
	
	// Rename county table
	public function rename__cf_geoplugin_country__column ($theme_columns){
		$theme_columns['name'] = __('Country code', CFGP_NAME);
		$theme_columns['description'] = __('Country full name', CFGP_NAME);
		return $theme_columns;
	}
	
	// Rename region table
	public function rename__cf_geoplugin_region__column ($theme_columns){
		$theme_columns['name'] = __('Region code', CFGP_NAME);
		$theme_columns['description'] = __('Region full name', CFGP_NAME);
		return $theme_columns;
	}
	
	// Rename city table
	public function rename__cf_geoplugin_city__column ($theme_columns){
		$theme_columns['name'] = __('City name', CFGP_NAME);
		unset($theme_columns['description']);
		return $theme_columns;
	}
	
	// Rename postcode table
	public function rename__cf_geoplugin_postcode__column ($theme_columns){
		$theme_columns['name'] = __('Postcode', CFGP_NAME);
		unset($theme_columns['description']);
		return $theme_columns;
	}
	
	// Initialize plugin settings
	public function admin_init(){
		$this->plugin_custom_menu_class();
		$this->add_privacy_policy();
	}
	
	// Add privacy policy content
	function add_privacy_policy() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}
	 
		$content = sprintf(
			__( 'This site uses the WordPress Geo Plugin (formerly: CF Geo Plugin) to display public visitor information based on IP addresses that can then be collected or used for various purposes depending on the settings of the plugin.
			
			CF Geo Plugin is a GeoMarketing tool that allows you to have full geo control of your WordPress. CF Geo Plugin gives you ability to attach content, geographic information, geo tags, Google Maps to posts, pages, widgets and custom templates by using simple options, shortcodes, PHP code or JavaScript. It also lets you specify a default geographic location for your entire WordPress blog, do SEO redirection, spam protection, WooCommerce control and many more. CF Geo Plugin help you to increase conversion, do better SEO, capture leads on your blog or landing pages.
			
			This website uses API services, technology and goods from the WordPress Geo Plugin and that part belongs to the <a href="%1$s" target="_blank">WordPress Geo Plugin Privacy Policy</a>.',
			CFGP_NAME ),
			CFGP_STORE . '/privacy-policy/'
		);
	 
		wp_add_privacy_policy_content(
			'WordPress Geo Plugin',
			wp_kses_post( wpautop( $content, false ) )
		);
	}
	
	// Fix collapsing admin menu
	public function plugin_custom_menu_class()
	{
		global $menu;

		$show = false;
		if( isset( $_GET['post_type'] ) ) $show = $this->limit_scripts( $_GET['post_type'] ); // This will also check for taxonomies

		if( is_array( $menu ) && $show )
		{
			foreach( $menu as $key => $value )
			{
				if( $value[0] == 'Geo Plugin' )
				{
					$menu[$key][4] = 'wp-has-submenu wp-has-current-submenu wp-menu-open menu-top toplevel_page_cf-geoplugin menu-top-first wp-menu-open';
				}
			}
		}
	}
	
	// Add admin top bar menu pages
	public function admin_bar_menu($wp_admin_bar) {
		if ( !(current_user_can( 'update_plugins' ) && current_user_can( 'delete_plugins' ) && current_user_can( 'install_plugins' )) ) {
			return $wp_admin_bar;
		}
		
		$wp_admin_bar->add_node(array(
			'id' => CFGP_NAME . '-admin-bar-link',
			'title' => __('Geo Plugin', CFGP_NAME), 
			'href' => esc_url(admin_url('admin.php?page=cf-geoplugin')), 
			'meta' => array(
				'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-link',
				'title' => __('Geo Plugin', CFGP_NAME),
			)
		));
		
		$wp_admin_bar->add_menu(array(
			'parent' => CFGP_NAME . '-admin-bar-link',
			'id' => CFGP_NAME . '-admin-bar-shortcodes-link',
			'title' => __('Shortcodes', CFGP_NAME), 
			'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME)), 
			'meta' => array(
				'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-shortcodes-link',
				'title' => __('Shortcodes', CFGP_NAME),
			)
		));
		if(CFGP_Options::get('enable_gmap', false))
		{
			$wp_admin_bar->add_menu(array(
				'parent' => CFGP_NAME . '-admin-bar-link',
				'id' => CFGP_NAME . '-admin-bar-google-map-link',
				'title' => __('Google Map', CFGP_NAME), 
				'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-google-map')), 
				'meta' => array(
					'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-google-map-link',
					'title' => __('Google Map', CFGP_NAME),
				)
			));
		}
		if(CFGP_Options::get('enable_defender', 1))
		{
			$wp_admin_bar->add_menu(array(
				'parent' => CFGP_NAME . '-admin-bar-link',
				'id' => CFGP_NAME . '-admin-bar-defender-link',
				'title' => __('Site Protection', CFGP_NAME), 
				'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-defender')), 
				'meta' => array(
					'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-defender-link',
					'title' => __('Site Protection', CFGP_NAME),
				)
			));
		}
		if(CFGP_Options::get('enable_banner', false)) {
			$wp_admin_bar->add_menu(array(
				'parent' => CFGP_NAME . '-admin-bar-link',
				'id' => CFGP_NAME . '-admin-bar-banner-link',
				'title' => __('Geo Banner', CFGP_NAME), 
				'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-banner')), 
				'meta' => array(
					'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-banner-link',
					'title' => __('Geo Banner', CFGP_NAME),
				)
			));
		}
		if(CFGP_Options::get('enable_seo_redirection', 1))
		{
			$wp_admin_bar->add_menu(array(
				'parent' => CFGP_NAME . '-admin-bar-link',
				'id' => CFGP_NAME . '-admin-bar-seo-redirection-link',
				'title' => __('SEO Redirection', CFGP_NAME), 
				'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-seo-redirection')), 
				'meta' => array(
					'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-seo-redirection-link',
					'title' => __('SEO Redirection', CFGP_NAME),
				)
			));
		}
		
		$wp_admin_bar->add_menu(array(
			'parent' => CFGP_NAME . '-admin-bar-link',
			'id' => CFGP_NAME . '-admin-bar-settings-link',
			'title' => __('Settings', CFGP_NAME), 
			'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-settings')), 
			'meta' => array(
				'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-settings-link',
				'title' => __('Settings', CFGP_NAME),
			)
		));
		
		$wp_admin_bar->add_menu(array(
			'parent' => CFGP_NAME . '-admin-bar-link',
			'id' => CFGP_NAME . '-admin-bar-debug-link',
			'title' => __('Debug Mode', CFGP_NAME), 
			'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-debug')), 
			'meta' => array(
				'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-debug-link',
				'title' => __('Debug Mode', CFGP_NAME),
			)
		));
		
		$wp_admin_bar->add_menu(array(
			'parent' => CFGP_NAME . '-admin-bar-link',
			'id' => CFGP_NAME . '-admin-bar-activate-link',
			'title' => __('License', CFGP_NAME), 
			'href' => esc_url(admin_url('admin.php?page=' . CFGP_NAME . '-activate')), 
			'meta' => array(
				'class' => CFGP_NAME . ' ' . CFGP_NAME . '-admin-bar-activate-link',
				'title' => __('License', CFGP_NAME),
			)
		));
	}
	
	public function register_style($page){
		if(!$this->limit_scripts($page)) return;
		
		wp_enqueue_style( CFGP_NAME . '-fontawesome', CFGP_ASSETS . '/css/font-awesome.min.css', array(), (string)CFGP_VERSION );
		wp_enqueue_style( CFGP_NAME . '-admin', CFGP_ASSETS . '/css/style-admin.css', array(CFGP_NAME . '-fontawesome'), (string)CFGP_VERSION );
	}
	
	// Register CPT and taxonomies scripts
	public function register_scripts_ctp( $page )
	{
		$post = '';
		$url = '';
		
		if( isset( $_GET['taxonomy'] ) ) $post = $_GET['taxonomy'];
		elseif( isset( $_GET['post'] ) )
		{
			$post = get_post( absint( $_GET['post'] ) );
			$post = isset( $post->post_type ) ? $post->post_type : '';
		}
		elseif( isset( $_GET['post_type'] ) ) $post = $_GET['post_type'];

		if( !$this->limit_scripts( $post ) ) return false;

		if( $post === '' . CFGP_NAME . '-banner' ) $url = sprintf( 'edit.php?post_type=%s', $post );
		else $url = sprintf( 'edit-tags.php?taxonomy=%s&post_type=%s-banner', $post, CFGP_NAME );
		
		wp_enqueue_style( CFGP_NAME . '-cpt', CFGP_ASSETS . '/css/style-cpt.css', 1, (string)CFGP_VERSION, false );
		wp_enqueue_script( CFGP_NAME . '-cpt', CFGP_ASSETS . '/js/script-cpt.js', array('jquery'), (string)CFGP_VERSION, true );
		wp_localize_script(CFGP_NAME . '-cpt', 'CFGP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'label' => array(
				'unload' => esc_attr__('Data will lost , Do you wish to continue?',CFGP_NAME),
				'chosen' => array(
					'not_found' 		=> esc_attr__('Nothing found!',CFGP_NAME),
					'choose' 			=> esc_attr__('Choose...',CFGP_NAME),
					'choose_first' 		=> esc_attr__('Choose countries first!',CFGP_NAME),
					'choose_countries' 	=> esc_attr__('Choose countries...',CFGP_NAME),
					'choose_regions' 	=> esc_attr__('Choose regions...',CFGP_NAME),
					'choose_cities' 	=> esc_attr__('Choose cities...',CFGP_NAME),
					'choose_postcodes' 	=> esc_attr__('Choose postcodes...',CFGP_NAME),
				),
				'loading' => esc_attr__('Loading...',CFGP_NAME),
				'not_found' => esc_attr__('Not Found!',CFGP_NAME),
				'placeholder' => esc_attr__('Search',CFGP_NAME),
				'taxonomy' => array(
					'country' => array(
						'name' => esc_attr__('Country code',CFGP_NAME),
						'name_info' => esc_attr__('Country codes are short (2 letters) alphabetic or numeric geographical codes developed to represent countries and dependent areas, for use in data processing and communications.',CFGP_NAME),
						'description' => esc_attr__('Country full name',CFGP_NAME),
						'description_info' => esc_attr__('The name of the country must be written in English without spelling errors.',CFGP_NAME),
					),
					'region' => array(
						'name' => esc_attr__('Region code',CFGP_NAME),
						'name_info' => esc_attr__('Region codes are short (2 letters) alphabetic or numeric geographical codes developed to represent countries and dependent areas, for use in data processing and communications.',CFGP_NAME),
						'description' => esc_attr__('Region full name',CFGP_NAME),
						'description_info' => esc_attr__('The name of the region must be written in English without spelling errors.',CFGP_NAME),
					),
					'city' => array(
						'name' => esc_attr__('City name',CFGP_NAME),
						'name_info' => esc_attr__('The city name must be written in the original city name.',CFGP_NAME),
					),
					'postcode' => array(
						'name' => esc_attr__('Postcode',CFGP_NAME),
						'name_info' => esc_attr__('The postcode name must be written in the original international format.',CFGP_NAME),
					)
				)
			),
			'current_url'	=> $url
		));
		// Load geodata
		if(strpos($url, 'post-new.php') !== false || strpos($url, 'post=') !== false){
			wp_localize_script(CFGP_NAME . '-cpt', 'CFGP_GEODATA', CFGP_Library::all_geodata());
		}
	}
	
	public function register_scripts($page){
		if(!$this->limit_scripts($page)) return;
		
		wp_enqueue_style( CFGP_NAME . '-choosen', CFGP_ASSETS . '/js/chosen_v1.8.7/chosen.min.css', 1,  '1.8.7' );
		wp_enqueue_script( CFGP_NAME . '-choosen', CFGP_ASSETS . '/js/chosen_v1.8.7/chosen.jquery.min.js', array('jquery'), '1.8.7', true );
		
		wp_enqueue_script( CFGP_NAME . '-admin', CFGP_ASSETS . '/js/script-admin.js', array('jquery', CFGP_NAME . '-choosen'), (string)CFGP_VERSION, true );
		wp_localize_script(CFGP_NAME . '-admin', 'CFGP', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'adminurl' => self_admin_url('/'),
			'label' => array(
				'upload_csv' => esc_attr__('Select or Upload CSV file',CFGP_NAME),
				'unload' => esc_attr__('Data will lost , Do you wish to continue?',CFGP_NAME),
				'loading' => esc_attr__('Loading...',CFGP_NAME),
				'not_found' => esc_attr__('Not Found!',CFGP_NAME),
				'alert' => array(
					'close' => esc_attr__('Close',CFGP_NAME)
				),
				'rss' => array(
					'no_news' => esc_attr__('There are no news at the moment.',CFGP_NAME),
					'error' => esc_attr__("ERROR! Can't load news feed.",CFGP_NAME)
				),
				'chosen' => array(
					'not_found' => esc_attr__('Nothing found!',CFGP_NAME)
				),
				'settings' => array(
					'saved' => esc_attr__('Option saved successfuly!',CFGP_NAME),
					'fail' => esc_attr__('There was some unexpected system error. Changes not saved!',CFGP_NAME),
					'false' => esc_attr__('Changes not saved for unexpected reasons. Try again!',CFGP_NAME),
					'error' => esc_attr__('Option you provide not match to global variables. Permission denied!',CFGP_NAME)
				),
				'csv' => array(
					'saved' => esc_attr__('Successfuly saved %d records.',CFGP_NAME),
					'fail' => esc_attr__('Failed to add %d rows.',CFGP_NAME),
					'upload' =>	esc_attr__('Upload CSV file.',CFGP_NAME),
					'filetype' => esc_attr__('The file must be comma separated CSV type',CFGP_NAME),
					'exit' => esc_attr__('Are you sure, you want to exit?\nChanges wont be saved!',CFGP_NAME),
					'delete' =>	esc_attr__('Are you sure, you want to delete this redirection?',CFGP_NAME),
					'missing_url' => esc_attr__('URL Missing. Please insert URL from your CSV file or choose file from the library.',CFGP_NAME),
				),
				'rest' => array(
					'delete' => esc_attr__("Are you sure, you want to delete this access token?",CFGP_NAME),
					'error' => esc_attr__("Can't delete access token because unexpected reasons.",CFGP_NAME),
				),
				'footer_menu' => array(
					'documentation' =>	esc_attr__('Documentation',CFGP_NAME),
					'contact' => esc_attr__('Contact',CFGP_NAME),
					'blog' => esc_attr__('Blog',CFGP_NAME),
					'faq' => esc_attr__('FAQ',CFGP_NAME),
					'thank_you' => esc_attr__('Thank you for using',CFGP_NAME)
				)
			)
		));
		
		// Load geodata
		if(CFGP_U::request_string('page') == 'cf-geoplugin-defender'){
			wp_localize_script(CFGP_NAME . '-admin', 'CFGP_GEODATA', CFGP_Library::all_geodata());
		}
	}
	
	/*
	 * Limit scripts
	 */
	public function limit_scripts($page){
		if(strpos($page, CFGP_NAME) !== false) return true;
		return false;
	}
	
	/* 
	 * Instance
	 * @verson    1.0.0
	 */
	public static function instance() {
		$class = self::class;
		$instance = CFGP_Cache::get($class);
		if ( !$instance ) {
			$instance = CFGP_Cache::set($class, new self());
		}
		return $instance;
	}
}
endif;