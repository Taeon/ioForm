<?php

namespace ioForm\Element\Field;

class date extends text{

	protected $attributes = array(
		'type' => 'date',
	);

	public function SetValue( $value ){
		switch( gettype( $value ) ){
			case 'object':{
				$value = $value->format( 'Y-m-d' );
				break;
			}
		}
		$this->attributes->value = $value;
	}
}
