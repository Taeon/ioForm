<?php

namespace ioForm\Element;

abstract class Field extends \ioForm\Core\Element{
	public $show_label_for = true;
	protected $attributes = array(
		'name' => null,
		'placeholder' => null,
		'id' => null,
		'disabled' => null,
		'tabindex' => null
	);
	protected $help = null;
	public $container_template = 'default';
}
	
