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
	protected $validators = array();
	public $container_template = 'default';
	
	public function Render(){
		foreach( $this->validators as $validator ){
			foreach( $validator->GetFormAttributes() as $attribute => $value ){
				$this->attributes->$attribute = $value;
			}
		}
		return parent::Render();
	}
}
	
