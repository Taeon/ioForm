<?php

namespace ioForm\Element\Field;

class date extends text{
	
	protected $attributes = array(
		'type' => 'date',
	);
	protected $format = 'jS F Y';
	
	public function GetAttributes(){
		$attributes = parent::GetAttributes();
		$attributes->{ 'data-ioform-format' } = $this->format;
		return $attributes;
	}
}