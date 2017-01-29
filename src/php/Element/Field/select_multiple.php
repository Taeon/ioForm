<?php

namespace ioForm\Element\Field;

class select_multiple extends select{

	protected $tag = 'select';

	protected $attributes = array(
		'multiple' => 'multiple'
	);

	public function Render(){
		$this->attributes->{ 'data-ioform-field-name' } .= $this->attributes->name;
		$this->attributes->name .= '[]';
		return parent::Render();
	}
}
