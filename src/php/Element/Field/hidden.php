<?php

namespace ioForm\Element\Field;

class hidden extends text{
	
	public $container_template = false;
	
	protected $attributes = array(
		'type' => 'hidden'
	);	
	
}