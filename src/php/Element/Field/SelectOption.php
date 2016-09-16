<?php

namespace ioForm\Element\Field;

class SelectOption extends \ioForm\Core\Element{
	
	protected $tag = 'option';
	protected $attributes = array(
		'value' => '',
		'selected' => false
	);	
}