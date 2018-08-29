<?php

namespace ioForm\Element\Layout;

class button extends \ioForm\Core\Element{

	protected $tag = 'button';
	protected $button_type = 'button';
    public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		$this->attributes->type = $this->button_type;
    }
}

