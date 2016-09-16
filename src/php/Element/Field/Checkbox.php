<?php

namespace ioForm\Element\Field;

class Checkbox extends \ioForm\Element\Field{
	protected $tag = 'input';
	protected $is_singleton = true;
	protected $attributes = array(
		'type' => 'checkbox'
	);	
	
	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		if( $element_definition->default ){
			$this->SetValue( true );
		}
	}
	public function SetValue( $value ){
		$this->attributes->checked = ($value == true);
	}
}