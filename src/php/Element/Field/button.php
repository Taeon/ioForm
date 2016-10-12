<?php

namespace ioForm\Element\Field;

class button extends \ioForm\Element\Field{
	
	protected $tag = 'input';
	protected $button_type = 'submit';
	protected $is_singleton = true;
	public $container_template = false;
	
	protected $attributes = array(
		'type' => '',
		'value' => null
	);	
	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		$this->attributes->type = $this->button_type;
	}
}