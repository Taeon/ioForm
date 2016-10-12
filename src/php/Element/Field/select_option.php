<?php

namespace ioForm\Element\Field;

class select_option extends \ioForm\Core\Element{
	
	protected $tag = 'option';
	protected $attributes = array(
		'value' => '',
		'selected' => false
	);	
}