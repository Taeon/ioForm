<?php

namespace ioForm\Core;

/**
 * An object that represents an element definition
 */
class Definition{
	protected $parent;
	protected $alias;
	public $enabled = true;
	protected $elements = array();
	protected $templates = array();
	protected $alias_lookup;
	public $default;
	public $id;
	
	public function __construct(){
		$this->alias_lookup = new \stdClass();
	}
	
	public function SetTemplates( $templates ){
		$this->templates = $templates;
	}
	public function GetTemplates(){
		return $this->templates;
	}
	public function GetTemplate( $template_name ){
		if( !array_key_exists( $template_name, $this->templates ) ){
			if( $this->parent ){
				return $this->parent->GetTemplate( $template_name );
			} else {
				throw new \Exception( 'Template "' . $template_name .  '" not found' );
			}
		}

		return $this->templates[ $template_name ];
	}
	public function AddElement( \ioForm\Core\Definition $definition ){
		$definition->parent = $this;
		$this->elements[] = $definition;
		if( $definition->alias ){
			$this->alias_lookup->{ $definition->alias } = $definition;
		}
		// Cascade aliases up the chain
		foreach( $definition->alias_lookup as $alias => $child ){
			$this->alias_lookup->{ $alias } = $child;
		}
	}
	public function GetParent(){
		return $this->parent;
	}
	public function SetParent( $parent ){
		$this->parent = $parent;
	}
	public function GetElements(){
		return $this->elements;
	}

	
	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	public function FromArray( $element ){
		return $this->ArrayToDefinition( $element, new Definition() );
	}

	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	protected function ArrayToDefinition( $element, $element_obj ){
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
				case 'templates':{
					foreach( $value as $index => $template ){
						$element_obj->templates[ $index ] = $template;
					}
					break;
				}
				case 'elements':{
					// Convert child elements to definitions
					foreach( $element[ 'elements' ] as $child_definition ){
						$child = $this->ArrayToDefinition( $child_definition, new Definition() );
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
		if( strpos( $element_obj->type, ':' ) === false && isset( $element_obj->name ) && $element_obj->name ){
			$this->fields[ $element_obj->name ] = $element_obj;
		}
		
		return $element_obj;

	}	
	
	/**
	 * Get an element by its alias
	 *
	 * @param		\ioForm\Core\Element		$element
	 */
	public function GetElementByAlias( $alias ){
		if( !property_exists( $this->alias_lookup, $alias ) ){
			throw new \Exception( 'Element with alias ' . $alias . ' not found.' );
		}
		return $this->alias_lookup->$alias;
	}

	/**
	 * Find a field
	 *
	 * @param		string		$field_name
	 *
	 * @return		\ioForm\Core\Definition
	 */
	public function GetField( $field_name ){
		foreach( $this->elements as $element ){
			if( isset( $element->name ) && $element->name == $field_name ){
				return $element;
			} else {
				if( $field = $element->GetField( $field_name ) ){
					return $field;
				}
			}
		}
		return null;
	}

	
	/**
	 * Insert an element before another element
	 *
	 * @param		ioForm\Core\Element		$element
	 */
	public function Before( $element ){
		$elements = array();
		foreach( $this->parent->elements as $child_element ){
			if( $child_element == $this ){
				$element->SetParent( $this->parent );
				$elements[] = $element;
			}
			$elements[] = $child_element;
		}
		$this->parent->elements = $elements;
	}

	/**
	 * Insert an element after this element
	 *
	 * @param		ioForm\Core\Element		$element
	 */
	public function After( $element ){
		$elements = array();
		foreach( $this->parent->elements as $child_element ){
			$elements[] = $child_element;
			if( $child_element == $this ){
				$element->SetParent( $this->parent );
				$elements[] = $element;
			}
		}
		$this->parent->elements = $elements;
	}

	/**
	 * Replace this element with another element
	 *
	 * @param		ioForm\Core\Element		$element
	 */
	public function ReplaceWith( $element ){
		$elements = array();
		foreach( $this->parent->elements as $child_element ){
			if( $child_element == $this ){
				$element->SetParent( $this->parent );
				$elements[] = $element;
			} else {
				$elements[] = $child_element;
			}
		}
		$this->parent->elements = $elements;
	}

	/**
	 * Insert an element before all other child elements
	 *
	 * @param		ioForm\Core\Element		$element
	 */
	public function Prepend( $element ){
		$element->SetParent( $this->parent );
		$elements = array( $element );
		foreach( $this->parent->elements as $child_element ){
			$elements[] = $child_element;
		}
		$this->parent->elements = $elements;
	}
	/**
	 * Append an element to this element's child elements (an alias for AddElement())
	 *
	 * @param		ioForm\Core\Element		$element
	 */
	public function Append( $element ){
		$this->AddElement( $element );
	}
	/**
	 * Remove an element
	 *
	 * @param		string					$alias
	 */
	public function Remove(){
		$elements = array();
		foreach( $this->parent->elements as $child_element ){
			if( $child_element != $this ){
				$elements[] = $child_element;
			}
		}
		$this->parent->elements = $elements;
	}
	public function Enable(){
		$this->enabled = true;
	}
	public function Disable(){
		$this->enabled = false;
	}
}