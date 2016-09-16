<?php

namespace ioForm\Element\Field;

/**
 * Number field
 */
class Number extends Text{
	
	protected $attributes = array(
		'type' => 'number',
		'max' => null,
		'min' => null,
		'step' => null
	);	

	
}