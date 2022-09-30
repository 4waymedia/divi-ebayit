<?php

class WPEP_HelloWorld extends ET_Builder_Module {

	public $slug       = 'wpep_hello_world';
	public $vb_support = 'on';

	protected $module_credits = array(
		'module_uri' => '',
		'author'     => 'Paul Gemignani',
		'author_uri' => 'http://www.paulgemignani.info',
	);

	public function init() {
		$this->name = esc_html__( 'Hello World', 'wpep-wp-ebay-projector' );
	}

	public function get_fields() {
		return array(
			'content' => array(
				'label'           => esc_html__( 'Content', 'wpep-wp-ebay-projector' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear inside the module.', 'wpep-wp-ebay-projector' ),
				'toggle_slug'     => 'main_content',
			),
		);
	}

	public function render( $attrs, $content = null, $render_slug ) {
		return sprintf( '<h1>%1$s</h1>', $this->props['content'] );
	}
}

new WPEP_HelloWorld;
