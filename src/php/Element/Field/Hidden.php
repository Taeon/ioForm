<?php

namespace ioForm\Element\Field;

class Hidden extends Text{
	
	public $container_template = false;
	
	protected $attributes = array(
		'type' => 'hidden'
	);	
	
}