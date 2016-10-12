<?php

namespace ioForm\Element\Field;

/**
 * Number field
 */
class number extends text{
	
	protected $attributes = array(
		'type' => 'number',
		'max' => null,
		'min' => null,
		// Step value dictates value of stepper buttons
		// ...but also affects validation
		// null means default, which is 1
		// 1 means decimal places will not validate
		// Use e.g. 0.1 for 1 decimal place, 0.01 for 2 etc.
		// 'any' allows any number of decimal places, stepper will increment/decrement by 1
		'step' => null
	);	
}