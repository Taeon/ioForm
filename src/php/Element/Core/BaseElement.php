<?php

namespace ioForm\Element\Core;

class BaseElement extends \ioForm\Core\Element{
	public function __construct( $element_definition ){
		if($element_definition){
			$this->tag = $element_definition->tag;
		}
		parent::__construct( $element_definition );
	}
}
	
