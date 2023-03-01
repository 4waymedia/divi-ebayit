<?php

//namespace Plugin\WpepEbay;

class WpepEbay
{
	/**
	 * Action hook used by the AJAX class.
	 *
	 * @var string
	 */
	const ACTION = 'wpep_ebay';

	/**
	 * Action argument used by the nonce validating the AJAX request.
	 *
	 * @var string
	 */
	const NONCE = 'wpepplugin-ajax';
	
	const api_config = [
		'OPERATION-NAME' 	=> 'findItemsAdvanced',
		'SERVICE-VERSION' 	=> '1.4.0',
		'SECURITY-APPNAME' 	=> 'PaulGemi-CozCommo-PRD-490d912d4-f3382270',
		'REST-PAYLOAD'		=> 'true',
		'GLOBAL-ID'			=> 'EBAY-US', // default to US
		'networkId'			=> 9,
		'trackingId'		=> 5338947710
	];
	
	protected $_search_defaults = [
		'seller' 	=> '',	// sellername for the eBay store
		'keywords'	=> '', 	// keyword is the Module search term
		'search'	=> '', 	// filter are search terms to filter the keyword set
		'limit'		=> 20, 	// max results per page
		'pagenum' 	=> 1,
		'columns' 	=> 4,
		'imagesize' => 400,	// page number for pagination
		'template'=>'',
		'template_aspect'=>'4by3',
		'orientation' => 'portrait'
	];

	/**
	 * Register the AJAX handler class with all the appropriate WordPress hooks.
	 */
	public static function register()
	{
		$handler = new self();

		add_action('wp_ajax_' . self::ACTION, array($handler, 'wp_ajax_handle'));
		add_action('wp_ajax_nopriv_' . self::ACTION, array($handler, 'wp_ajax_handle'));
		add_action('wp_loaded', array($handler, 'register_script'));
	}

	
	/**
	 * Create Ebay endpoint
	 *
	 *
	 */
	protected function get_endpoint(){
		//findItemsByKeywords
    	//findItemsAdvanced
		$endpoint = 'https://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
		$version = '1.4.0';  // API version supported by your application
		$appid = 'PaulGemi-CozCommo-PRD-490d912d4-f3382270';  // Replace with your own AppID
		$globalid = get_option( 'wpep_default_ebay_site' )?: 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-US)
		
		$call = "$endpoint?";
		 $call .= "OPERATION-NAME=findItemsAdvanced";
		 $call .= "&SERVICE-VERSION=$version";
		 $call .= "&SECURITY-APPNAME=$appid";
		 $call .= "&REST-PAYLOAD=true";
		 $call .= "&GLOBAL-ID=$globalid";
		 $call .= "&sortOrder=CurrentPriceHighest"; // CurrentPriceHighest | PricePlusShippingLowest | 
		 $call .= "&networkId=9";
		 $call .= "&trackingId=5338947710";
		 $call .= "&outputSelector(0)=PictureURLLarge";
		 $call .= "&outputSelector(1)=PictureURLSuperSize";
		 $call .= "&outputSelector(2)=GalleryInfo";
		 
		 
		return $call;
	}

	public function create_params_array(){
		$params_array = [
			'keywords' => '',
			'limit' => '',
			'seller' => '',
			'pagenum' 	=> 1, 
			'cache_key' => '',
			'imagesize' => 400,
			'template'=>'',
			'columns'=> '4',
			'template_aspect'=>'4by3',
			'orientation' => 'portrait'
		];
		
		// create cache_key from params
		$params['cache_key'] = $this->make_cache_key($params);
		
		return $params_array;
	}

	/*
	pagenum: pagenum,
	max: max,
	keyword: keyword,
	search: searchterm
		store: store
		*/
	public function get_ajax_params(){
		$params= [
			'keywords' => '',
			'search' => '',
			'columns' => '4',
			'limit' => '8',
			'seller' => '',
			'pagenum' 	=> 1,
			'imagesize'=>  'large',
			'template'=>'',
			'template_aspect'=>'4by3',
			'orientation' => 'portrait'
		];
		
		$params = $this->_search_defaults;
		
		// Override defaults with $POST data
		
		if( isset($_POST['keywords']) ){
			$params['keywords'] = sanitize_text_field($_POST['keywords']);
		}
		if( isset($_POST['limit']) ){
			$params['limit'] = sanitize_text_field($_POST['limit']);
		}
		if( isset($_POST['module_id']) ){ // modid is used to reference the module to update
			$params['module_id'] = sanitize_text_field($_POST['module_id']);
		}
		if( isset($_POST['pagenum']) ){
			$params['pagenum'] = sanitize_text_field($_POST['pagenum']);
		}
		if( isset($_POST['search']) ){
			$params['search'] = sanitize_text_field($_POST['search']);
		}
		if( isset($_POST['seller']) ){
			$params['seller'] = sanitize_text_field($_POST['seller']);
		}
		if( isset($_POST['template']) ){ // modid is used to reference the module to update
			$params['template'] = sanitize_text_field($_POST['template']);
		}
		if( isset($_POST['imagesize']) ){ // modid is used to reference the module to update
			$params['imagesize'] = sanitize_text_field($_POST['imagesize']);
		} else {
			$params['imagesize'] = 400;
		}
		if( isset($_POST['template_aspect']) ){ // modid is used to reference the module to update
			$params['template_aspect'] = sanitize_text_field($_POST['template_aspect']);
		} 
		if( isset($_POST['orientation']) ){ // modid is used to reference the module to update
			$params['orientation'] = sanitize_text_field($_POST['orientation']);
		} 
		
		// Create Cache Key
		$params['cache_key'] = $this->make_cache_key($params);
		
		return $params;
	}
	
	public function build_endpoint_string($params){
		// merge with defaults
		$searchSeller = false;
		
		if(isset($params['seller']) && $params['seller'] !== '' && $params['seller'] !== 'ebay'){
			$searchSeller = true;
		}
		//$data = array_replace_recursive($this->_search_defaults, $params);
		$string = '';
		
		// Create safe query
		$safequery = $this->build_keywords($params['keywords'], $params['search']);
	
		// Add Dynamic elements
		$string .= "&keywords=$safequery";

		$string .= "&itemFilter(0).name=HideDuplicateItems";
		$string .= "&itemFilter(0).value=true";
		
		if($searchSeller){
			$string .= "&itemFilter(1).name=Seller";
			$string .= "&itemFilter(1).value=". $params['seller'];
		}
		
		$string .= "&paginationInput.entriesPerPage=".$params['limit'];
		
		// Page number
		if(isset($params['pagenum'])){
			$string .= "&paginationInput.pageNumber=".$params['pagenum'];
		}

		return $string;
	}
	
	public function construct_api_call($params){
		// API request variables

		$apicall = $this->get_endpoint();
		$apicall .= $this->build_endpoint_string($params);

		return $apicall;
	}
	
	public function get_image_size($size){

		$options = [
			'thumbnail'=>50,
			'small' => 100,
			'medium' => 200,
			'large'=> 400
		];
	
		return $options[$size];
	}
	
	public function get_items($props){

		// Try to read cache
		if(!$resp = $this->read_cache($props['cache_key'])) {
			// create Cache
			$resp = $this->perform_api_call($props);	
		}

		return($resp);
	}
	
	public function perform_api_call($props){
		// Create URL for API call
		$apicall = $this->construct_api_call($props);
		// Attempt to make the API call and get JSON

		$resp = simplexml_load_file($apicall);

		//@TODO check the response for error

		$cache_data = [
			'pagination'=>(array)$resp->paginationOutput,
			'cache'=>[
				'key'=>(string)$props['cache_key'],
				'count'=>0
			],
			'search'=>[
				'limit'=>$props['limit'],
				'imagesize'=>$props['imagesize'],
				'keywords'=>$props['keywords'],
				'search'=> $props['search'],
				'seller'=>$props['seller'],
				'columns'=>$props['columns'],
				'template_aspect'=>$props['template_aspect'],
				'orientation'=>$props['orientation'],
				'template'=>$props['template'],
				'pagenum'=> (isset($props['pagenum']) ? $props['pagenum'] : 1)
			]
		];
		
		$counter = 0;
	
		$imagesize = $this->get_image_size($props['imagesize']);

		foreach($resp->searchResult->item as $item) {
			$size = 's-l' . $imagesize . '.jpg';
			if( !$image = (string)$item->galleryURL){
				if(!$pic = (string)$item->pictureURLLarge){
					if(!$pic = (string)$item->pictureURLSuperSize){
						// No image for item
						// print_r($item);
						//continue;
						$pic = plugin_dir_url( __DIR__ ).'media/no-photo.png';
					}
				}
			} else {
				$pic = str_replace('s-l140.jpg', $size, $image);
			}
			
			// replace image size
			
			$url = $item->viewItemURL;
			
			// Use pictureURLLarge from outputSelector param			
			//$url = $item->pictureURLLarge;
			$url .= '?mkevt=1&mkcid=1&mkrid=711-53200-19255-0&campid=5338947710&toolid=10001';
			$cache_data['cards'][] = [
				'itemId' 	=> (string)$item->itemId,
				'pic'		=> (string)$pic,
				'title'		=> (string)$item->title,
				'url'		=>(string)$url,
				'price'		=> (string)$item->sellingStatus->currentPrice
			];
			$counter++;
		}
		
		$cache_data['cache']['count'] = $counter;
		$cache_data['module_id'] = $this->unique_id('wpep');
		$data = $this->cache_results($props['cache_key'], $cache_data);
		
		return $data;
	}
	
	/*
	 * Combine the Keyword with any user input for search
	 */
	public function build_keywords($keyword, $search){
		// replace spaces with a comma
		$urlstring = $keyword;
		if(!empty($search) && $search != 'off'){ $urlstring .= ' ' . $search;}
		
		return urlencode($urlstring);
	}
	
	public function keyword_encode($keyword_string) {
		if(strpos($keyword_string, ':') === false) {
			// special char replacement
			$keyword_string = str_replace(array(', ', '( ', ' )', '()'), array(',', '(', ')', ''), $keyword_string);
			$keyword_string = urlencode($keyword_string);
		}
		
		return $keyword_string;		
	}
	
	
	public function make_cache_key($params){
		$query = implode($params, '_');
		//$params['keyword'];//$params['keyword'];  // You may want to supply your own query
		$key = str_replace(' ', '', $query);
		
		return $key;
	}
	
	public function read_cache($key) {
		if(get_option('wpep_default_cache') === 'off'){
			//print_r('delete cache');
			delete_transient( $key );
			return false;
		}
		
		if ( false === ( $data = get_transient( $key ) ) ) {
			// this code runs when there is no valid transient set
			return false;
		}
	
		return json_decode($data, true);
	}
	
	public function start_module_output($params){
		// start div element
		$output = '<div class="wpep-module-container" ';
		
		//write each module setting into a data- attribute
		foreach($params as $key => $value){
			if($key == 'columns'){
				$output .= ' data-slider="' . $value . '"';
			}
			if(!empty($value)){
				$output .= ' data-'.$key .'="'.$value.'"'; 
			}
			
		}

		// close element
		$output .= '>';
		
		return $output;
	}
	
	
	public function cache_results($key, $feed){
		$data = json_encode($feed);
		if(get_option('wpep_default_cache') === 'on'){
			// cache results for 12 hours
			set_transient( $key, $data, 12 * HOUR_IN_SECONDS );	
		}
		
		return json_decode($data, true);
	}
	
	/**
	 * Register our AJAX JavaScript.
	 */
	public function register_script()
	{
		wp_register_script('wp_ajax', plugins_url('path/to/ajax.js', __FILE__));
		wp_localize_script('wp_ajax', 'wp_ajax_data', $this->get_ajax_data());
		wp_enqueue_script('wp_ajax');
	}

	/**
	 * Get the AJAX data that WordPress needs to output.
	 *
	 * @return array
	 */
	private function get_ajax_data()
	{
		return array(
			'action' => self::ACTION,
			'nonce' => wp_create_nonce(AjaxHandler::NONCE)
		);
	}


	/**
	 * Sends a JSON response with the details of the given error.
	 *
	 * @param WP_Error $error
	 */
	private function send_error(WP_Error $error)
	{
		wp_send_json(array(
			'code' => $error->get_error_code(),
			'message' => $error->get_error_message()
		));
	}
	
	/* 
	 * Used to generate a unique id for the module element 
	 */
	protected function unique_id( $prefix = 'wpep' ) {
		static $id_counter = 0;
		return $prefix . '-id-' . (string) ++$id_counter;
	}
		
}