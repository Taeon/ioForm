<?php

namespace ioForm\Element\Field;

class radio_button extends \ioForm\Element\Field{
	
	protected $tag = 'input';
	public $container_template = 'radio-button';
	protected $is_singleton = true;
	protected $attributes = array(
		'type' => 'radio',
		'value' => '',
		'checked' => null,
		'tabindex' => null
	);	
}