<?php

namespace ioForm\Element\Core;

class BaseElement extends \ioForm\Core\Element{
	public function __construct( $element_definition ){
		if($element_definition && property_exists( $element_definition, 'tag' )){
			$this->tag = $element_definition->tag;
		}
		parent::__construct( $element_definition );
	}
}
	
