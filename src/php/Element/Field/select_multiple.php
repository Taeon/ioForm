<?php

namespace ioForm\Element\Field;

class SelectMultiple extends Select{
	
	protected $tag = 'select';
	
	protected $attributes = array(
		'multiple' => 'multiple'
	);
}