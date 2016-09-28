<?php

namespace ioForm\Core;

/**
 * Create a form definition as an array property of an object
 */
class FormDefinition extends Definition{
	
	public $action = null;
	protected $buttons = array();
	protected $alias_lookup;
	protected $fields = array();
	
	public $type = 'Form';
	
	protected $templates = array(
		'default' => '<div><label></label><!--elements--></div>',
		'radio-button' => '<div data-nform-role="radiobutton"><!--elements--><label></label></div>'
	);	
	
	public function __construct(){
		$this->alias_lookup = new \stdClass();
		// Parse the definition array
		$this->DefinitionFromArray( $this->definition, $this );

		if( $this->buttons ){
			$buttons = array();
			foreach( $this->buttons as $button ){
				$definition = new \ioForm\Core\Definition();
				$definition->type = 'Button';
				$definition->button_type = $button[ 'type' ];
				if( isset( $button[ 'value' ] ) ){
					$definition->value = $button[ 'value' ];
				}
				$this->elements[] = $definition;
			}
		}
	}

	public function Render(){
		$form = \ioForm\ioForm::CreateElement( $this );
		return $form->Render();
	}
	
	/**
	 * Add a validator to 
	 */
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
	
	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	protected function DefinitionFromArray( $element, $element_obj = null ){
		
		if( $element_obj === null ){
			$element_obj = new Definition();
		}
		
		// Assign properties
		foreach( $element as $property => $value ){
			switch( $property ){
				// Convert classes to objects
				case 'classes':{
					foreach( $value as $index => $class ){
						$value[ $index ] = (object)$class;
					}
					$element_obj->$property = $value;
					break;
				}
				case 'alias':{
					$this->alias_lookup->{ $value } = $element_obj;
					break;
				}
				case 'elements':{
					// Convert child elements to definitions
					foreach( $element[ 'elements' ] as $child_definition ){
						$child = $this->DefinitionFromArray( $child_definition );
						if( $child->alias ){
							$this->alias_lookup->{ $child->alias } = $child;
						}
						$element_obj->AddElement( $child );
					}
					break;
				}
				default:{
					$element_obj->$property = $value;
					break;
				}
			}
		}
		// Is it a field?
		if( strpos( $element_obj->type, ':' ) === false && $element_obj->name ){
			$this->fields[ $element_obj->name ] = $element_obj;
		}

		return $element_obj;

	}
}