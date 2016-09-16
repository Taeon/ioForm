<?php

namespace ioForm\Element;

class Form extends \ioForm\Core\Element{
	
	protected $tag = 'form';
	protected $attributes = array(
		'id' => null,
		'method' => 'get',
		'action' => null
	);
	protected $fields = array();
	protected $auto_tabindex = true;
	protected $tabindex_start = 1;
	
	public function __construct( \ioForm\Core\FormDefinition $element_definition ){

		$this->alias_lookup = new \stdClass();
		$this->action = $element_definition->action;

		parent::__construct( $element_definition );
		$this->FindFields( $this, $this->tabindex_start );
		
		// Wrap the whole form in a fieldset. This allows form to pass w3C validation for XHTML 1.0 (Strict)
		//$definition = new \ioform\Core\Definition();
		//$definition->type = 'Layout:Fieldset';
		//
		//$fieldset = $this->CreateElement( $definition );
		//$fieldset->elements = $this->elements;
		//$this->elements = array( $fieldset );
	}
	public function Populate( $values ){
		foreach( $values as $field_name => $value ){
			if( $field = $this->GetField( $field_name ) ){
				$field->SetValue( $value );
			}
		}
	}
	public function SetValidator( $validator_definition ){
		foreach( $validator_definition->GetValidators() as $field_name => $validators ){
			$field = $this->GetField( $field_name );
			if( !$field ){
				continue;
			}
			foreach( $validators as $validator ){
				switch( $validator[ 'type' ] ){
					case 'Number':{
						if( isset( $validator[ 'values' ][ 'min' ] ) ){
							$field->SetAttribute( 'min', $validator[ 'values' ][ 'min' ] );
						}
						if( isset( $validator[ 'values' ][ 'max' ] ) ){
							$field->SetAttribute( 'max', $validator[ 'values' ][ 'max' ] );
						}
						if( isset( $validator[ 'values' ][ 'step' ] ) ){
							$field->SetAttribute( 'step', $validator[ 'values' ][ 'step' ] );
						}
						break;
					}
					case 'Length':{
						if( isset( $validator[ 'values' ][ 'min' ] ) ){
							$field->SetAttribute( 'data-nvalidate-minlength', $validator[ 'values' ][ 'min' ] );
						}
						if( isset( $validator[ 'values' ][ 'max' ] ) ){
							$field->SetAttribute( 'maxlength', $validator[ 'values' ][ 'max' ] );
						}
						break;
					}
					case 'Required':{
						if( !(isset( $validator[ 'enabled' ] ) && $validator[ 'enabled' ] == false ) ){
							$field->SetAttribute( 'required', true );
						}
						break;
					}
				}
			}
		}
	}
	public function GetField( $name ){
		return $this->fields[ $name ];
	}
	public function FindFields( $parent_element, &$index = 1 ){
		foreach( $parent_element->elements as $element ){
			if( $element instanceof \ioForm\Element\Field && $element->HasAttribute( 'name' ) ){
				$this->fields[ $element->GetAttribute( 'name' ) ] = $element;
				if( $this->auto_tabindex ){
					// Radio buttons are a special case
					if( $element instanceof \ioForm\Element\Field\Radio ){
						foreach( $element->GetOptions() as $option ){
							$option->SetAttribute( 'tabindex', $index );
							$index++;
						}
					} else {
						$element->SetAttribute( 'tabindex', $index );
						$index++;
					}
				}
			} else {
				$this->FindFields( $element, $index );
			}
		}
	}
}
	
