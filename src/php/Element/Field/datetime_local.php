<?php

namespace ioForm\Element\Field;

class datetime_local extends text{

	protected $attributes = array(
		'type' => 'datetime-local'
	);

	public function SetValue( $value ){
		switch( gettype( $value ) ){
			case 'object':{
				$value = $value->format( 'Y-m-d H:i:s' );
				break;
			}
		}
		$this->attributes->value = $value;
	}
}
