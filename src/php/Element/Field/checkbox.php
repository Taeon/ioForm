<?php

namespace ioForm\Element\Field;

class checkbox extends \ioForm\Element\Field{
	protected $tag = 'input';
	protected $is_singleton = true;
	protected $attributes = array(
		'type' => 'checkbox'
	);

	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		if( isset( $element_definition->value ) ){
			$this->setAttribute( 'value', (string)$element_definition->value );
		}
		if( $element_definition->default ){
			$this->SetValue( true );
		}
	}
	public function SetValue( $value ){
		$this->attributes->checked = ($value == true);
	}
}
