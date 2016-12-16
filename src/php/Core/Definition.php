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
	protected $field_container_template_default = array();
	public $validators = array();
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
	 * @param		array		$definition_array		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	public function FromArray( $definition_array ){
		return $this->ArrayToDefinition( $definition_array, new Definition() );
	}

	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	protected function ArrayToDefinition( $definition_array, $definition ){
		// Assign properties
		foreach( $definition_array as $property => $value ){
			switch( $property ){
				// Convert classes to objects
				case 'classes':
				case 'content':{
					if( is_array( $value ) ){
						foreach( $value as $index => $class ){
							$value[ $index ] = (object)$class;
						}
					}
					$definition->$property = $value;
					break;
				}
				case 'templates':{
					foreach( $value as $index => $template ){
						$definition->templates[ $index ] = $template;
					}
					break;
				}
				case 'elements':{
					// Convert child elements to definitions
					foreach( $value as $child_definition ){
						$child = $this->ArrayToDefinition( $child_definition, new Definition() );
						if( $definition->field_container_template_default ){
							$child->field_container_template_default = array_merge( $child->field_container_template_default, $definition->field_container_template_default );
						}
						$definition->AddElement( $child );
					}
					break;
				}
				case 'validators':{
					foreach( $value as $validator_definition ){
						$validator_type = '\\ioValidate\\Validator\\' . $validator_definition[ 'type' ];
						$validator = new $validator_type( (object)$validator_definition );
						$validator->value_type = $definition->type;
						$definition->validators[] = $validator;
					}
					break;
				}
				default:{
					$definition->$property = $value;
					break;
				}
			}
		}
		// Is it a field?
		if( strpos( $definition->type, ':' ) === false && isset( $definition->name ) && $definition->name ){
			$this->fields[ $definition->name ] = $definition;
			// Set default container template for field type
			if( !isset( $definition->container_template ) && isset( $this->field_container_template_default[ $definition->type ] ) ){
				$definition->container_template = $this->field_container_template_default[ $definition->type ];
			}
		}

		return $definition;

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
	 * Return a field by its name
	 *
	 * @param		string		$name
	 *
	 * @return		\ioform\Element\Field
	 */
	public function HasField( $field_name ){
		foreach( $this->elements as $element ){
			if( isset( $element->name ) && $element->name == $field_name ){
				return true;
			} else {
				if( $field = $element->GetField( $field_name ) ){
					return true;
				}
			}
		}
		return false;
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
	/**
	 * Add/amend an attribute value
	 *
	 * @param string $name  Name of attribute
	 * @param mixed $value Value of the attribute
	 */
	public function SetAttribute( $name, $value )
	{
		$this->attributes[ $name ] = $value;
	}

	/**
	 * Check whether an attribute has been set
	 *
	 * @param string $name  Name of attribute
	 *
	 * @return boolean
	 */
	public function HasAttribute( $name )
	{
		return isset( $this->attributes[ $name ] );
	}

	/**
	 * Add/amend a data attribute value
	 *
	 * @param string $name  Name of data attribute (no need to include data-)
	 * @param mixed $value Value of the attribute
	 */
	public function SetData( $name, $value )
	{
		$this->SetAttribute('data-' . $name, $value);
	}

	/**
	 * Check whether a data attribute has been set
	 *
	 * @param string $name  Name of data attribute (no need to include data-)
	 *
	 * @return boolean
	 */
	public function HasData( $name )
	{
		return isset( $this->data[ $name ] );
	}

}
