<?php

namespace ioForm\Element\Field;

class select_multiple extends select{
	
	protected $tag = 'select';
	
	protected $attributes = array(
		'multiple' => 'multiple'
	);
}