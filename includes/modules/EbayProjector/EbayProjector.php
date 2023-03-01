<?php


class WPEP_EbayProjector extends ET_Builder_Module {

	public $slug       = 'wpep_ebay_projector';
	public $vb_support = 'on';
	public $cache_group ='wpep_cache_';

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'Paul Gemignani',
		'author_uri' => 'http://www.paulgemignani.info',
	);

	public function init() {
		$this->name = esc_html__( 'Ebay Projector', 'wpep-wp-ebay-projector' );
		$this->id = '';
		
		// init Class
		$this->Ebay = new WpepEbay();
		$this->Template = new WpepTemplate();
	}
	
	public function get_fields() {
		// Defaults
		$store_id = get_option('wpep_default_store_id');
		
		return array(
			'heading'     => array(
				'label'           => esc_html__( 'Heading', 'wpep-extension' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired heading here.', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_section'
			),
			'seller'     => array(
				'label'           => esc_html__( 'Store', 'wpep-extension' ),
				'type'            => 'text',
				'default'		=> $store_id,
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Ebay Store ID. You can use "ebay" to search all of Ebay', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_section'
			),
			'keywords'     => array(
				'label'           => esc_html__( 'Keyword', 'wpep-extension' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Keyword search terms', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_section'
			),
			'show_heading' => array(
				'label'           => esc_html__( 'Disply Heading', 'wpep-extension' ),
				'type'            => 'yes_no_button',
				'options' => ['off'=>'no','on'=>'yes'],
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Heading Text', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'template'     => array(
				'label'           => esc_html__( 'Display Template', 'wpep-extension' ),
				'type'            => 'select',
				'default' 		  => 'slideshow',
				'options' => ['slideshow'=> 'Slideshow', 'table'=>'Table', 'flip'=>'Flip Card','poster'=>'Poster', 'image' => 'Image Only'],
				'description'     => esc_html__( 'Select display type', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'paginate' => array(
				'label'           => esc_html__( 'Pagination', 'wpep-extension' ),
				'type'            => 'yes_no_button',
				'options' => ['off'=>'no','on'=>'yes'],
				'description'     => esc_html__( 'Show Pagination Links', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'search' => array(
				'label'           => esc_html__( 'Disply Search', 'wpep-extension' ),
				'type'            => 'yes_no_button',
				'options' => ['off'=>'no','on'=>'yes'],
				'description'     => esc_html__( 'Show Search Bar', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'template_aspect'     => array(
				'label'           => esc_html__( 'Image Aspect Ratio', 'wpep-extension' ),
				'type'            => 'select',
				'default' 		  => '2by3',
				'options' => ['1by1'=> '1 x 1','2by3'=> '2 x 3','3by4'=> '3 x 4','3by5'=> '3 x 5','4by5'=> '4 x 5','16by9'=> '16 x 9',],
				'description'     => esc_html__( 'Select display aspect ratio for your images', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'orientation'     => array(
				'label'           => esc_html__( 'Display Orientation', 'wpep-extension' ),
				'type'            => 'select',
				'default' 		  => 'portrait',
				'options' => ['portrait'=> 'Portrait', 'landscape'=>'Landscape'],
				'description'     => esc_html__( 'Select display type', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),	
			'columns'     => array(
				'label'           => esc_html__( 'Columns', 'wpep-extension' ),
				'type'            => 'select',
				'default' 		  => '4',
				'options' => ['1','2','3','4','5','6','7','8'],
				'description'     => esc_html__( 'Columns for Full Width', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'limit'     => array(
				'label'           => esc_html__( 'Product Limit', 'wpep-extension' ),
				'type'            => 'range',
				'default' 			=> 8,
				'option_category' => 'basic_option',
				'range_settins' 	=> ['min'=>1,'max'=>100,'step'=>1],
				'description'     => esc_html__( 'Limit of items per page', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			),
			'imagesize'     => array(
				'label'           => esc_html__( 'Image Size', 'wpep-extension' ),
				'type'            => 'select',
				'default' 		  => 'medium',
				'options' => ['thumbnail'=>'Thumbnail','small'=>'Small','medium'=>'Medium','large'=>'Large'],
				'description'     => esc_html__( 'Ebay limits size, so we can use CSS to set the image size to: 50px, 150px, 250px, 300px', 'wpep-extension' ),
				'toggle_slug'     => 'wpep_projector_settings'
			)
		);
	}
	

	public function get_settings_modal_toggles() {
	  return array(
		'advanced' => array(
		  'toggles' => array(
			'wpep_section' => array(
			  'priority' => 2,
			  'tabbed_subtoggles' => true,
			  'title' => 'Ebay Settings',
			),
			'wpep_projector_settings' => array(
			  'priority' => 3,
			  'tabbed_subtoggles' => true,
			  'title' => 'Projector Settings',
			 ),
		  ),
		),
	  );
	}
		
	
	public function get_mod_fields(){
		$list = $this->get_fields();
		$arr = [];
		foreach($list as $key=>$data){
			$arr[$key] = $this->props[$key];
		}
		return $arr;
	}

	public function get_props($is_ajax = false){		
		
		$params= [
			'keywords' 		=> $this->props['keywords'],
			'search' 		=> '',
			'limit' 		=> $this->props['limit'],
			'seller' 		=> $this->props['seller'],
			'pagenum' 		=> '1',
			'columns' 		=> $this->props['columns'],
			'imagesize'		=>  $this->props['imagesize'],
			'template'		=> $this->props['template'],
			'template_aspect'=> $this->props['template_aspect'],
			'orientation'	=> $this->props['orientation']
		];


		//	params = Array ( [keyword] => MTG [limit] => 60 [store] => commanduncomm ));	
		$params['cache_key'] = $this->Ebay->make_cache_key($params);
		
		return $params;
	}
	
	public function render( $unprocessed_props, $content, $render_slug ) {
		// Get items for keyword
		$props = $this->get_props();

		//get items by API or from Cache
		$ebay_data = $this->Ebay->get_items($props);
		$ebay_data['module'] = $this->get_mod_fields();

		//@TOTO create TemplateClass
		$template = $this->Template->get_item_template($this->props['template'], $ebay_data);

		// create div wrapper with all data attributes
		$output = $this->Ebay->start_module_output($ebay_data['module']);
		
		// output Heading of Module		
		if($this->props['show_heading'] === 'on'){
			$output .= sprintf('<h1 class="wpep-heading">%s</h1>',$this->props['heading']);
		}

		if($this->props['template'] !='slideshow' && $this->props['search'] === 'on'){
			// create nonce
			// uses url vars to set the value or placeholder
			$placeholder = isset($_GET['wpepq'])? 'value="'.$_GET['wpepq'].'"': 'placeholder="Search for Names, dates, stack, collection, team.."';
			
			$output .= '<form action="#" class="wpep-search-form " id=' . $ebay_data['module_id'] . '>
			  <input id="search-'.$ebay_data['module_id'].'" name="wpepq" class="wpep-search-input" ' . $placeholder .'/>
			  <button class="wpep-search-button" type="submit">Search</button>
			</form>';
		}
		
		if($this->props['template'] != 'slideshow' && $this->props['paginate'] === 'on'){
			$pagination = $this->Template->paginationHeader();
			$pagination .= $this->Template->template_pagination($ebay_data['pagination']);
			$pagination .= '</div>';
			$output .= $pagination;
		}

		$output .= $template['header'];
		$output .= $template['body'];
		$output .= $template['footer'];
		$output .= '</div>';
		
		return $output;
	}

}

new WPEP_EbayProjector;
