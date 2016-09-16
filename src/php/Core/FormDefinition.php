<?php

namespace ioForm\Core;

/**
 * Create a form definition as an array property of an object
 */
class FormDefinition extends Definition{
	
	protected $elements = array();
	protected $buttons = array();
	protected $alias_lookup;
	
	public function __construct(){
		$this->alias_lookup = new \stdClass();
		$elements = $this->elements;
		$this->elements = array();
		foreach( $this->ArrayToDefinitionObjects( $elements ) as $element ){
			$this->AddElement( $element );
		}

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
	
	/**
	 * Iterate through an array of element definition arrays and convert them to definition objects
	 *
	 * @param		array		$elements
	 *
	 * @return		array	
	 */
	protected function ArrayToDefinitionObjects( $elements ){
		foreach( $elements as $index => $element ){
			$elements[ $index ] = $this->ArrayToDefinitionObject( $element );
		}
		return $elements;
	}
	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	protected function ArrayToDefinitionObject( $element ){
		$element_obj = new Definition();
		$element_obj->form = $this;
		
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
					foreach( $this->ArrayToDefinitionObjects( $element[ 'elements' ] ) as $child ){
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

		return $element_obj;
	}
}