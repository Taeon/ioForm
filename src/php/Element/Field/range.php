<?php

namespace ioForm\Element\Field;

class range extends text{

	protected $min;
	protected $max;
	protected $step;

	protected $attributes = array(
		'type' => 'range'
	);

	public function GetAttributes(){
		$attributes = parent::GetAttributes();
		$attributes->{ 'min' } = $this->min;
		$attributes->{ 'max' } = $this->max;
		$attributes->{ 'step' } = $this->step;
		return $attributes;
	}

}
