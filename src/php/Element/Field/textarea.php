<?php

namespace ioForm\Element\Field;

class textarea extends \ioForm\Element\Field{
	protected $tag = 'textarea';
	
	protected $attributes = array(
		// Rows and cols attributes are required for the form to pass w3C validation for XHTML 1.0 (Strict)
		'rows' => 2,
		'cols' => 20
	);
	
	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		if( $element_definition->default ){
			$this->SetValue( $element_definition->default );
		}
	}
	
	public function SetValue( $value ){
		$this->content = htmlentities( $value );
	}
}