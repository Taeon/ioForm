<?php

namespace ioForm\Element\Field;

class text extends \ioForm\Element\Field{
	
	protected $tag = 'input';
	protected $attributes = array(
		'type'=>'text'
	);
	protected $is_singleton = true;
	
	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		if( $element_definition->default !== null ){
			$this->SetValue( $element_definition->default );
		}
	}
	public function SetValue( $value ){
		$this->attributes->value = $value;
	}
}