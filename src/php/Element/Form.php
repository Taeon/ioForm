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
	
	public function __construct( \ioForm\Core\Definition $element_definition ){
		parent::__construct( $element_definition );
		$this->FindFields( $this, $this->tabindex_start );
	}
	
	/**
	 * Set the value of a set of fields in a form
	 *
	 * @param		mixed		$values		An iterable list of key/value pairs
	 */
	public function Populate( $values ){
		foreach( $values as $field_name => $value ){
			if( $field = $this->GetField( $field_name ) ){
				$field->SetValue( $value );
			}
		}
	}
	
	/**
	 * Return a field by its name
	 *
	 * @param		string		$name
	 *
	 * @return		\ioform\Element\Field
	 */
	public function GetField( $name ){
		if( array_key_exists( $name, $this->fields ) ){
			return $this->fields[ $name ];			
		} else {
			throw new \Exception( "Field '$name' not found" );
		}
	}

	/**
	 * Iterate recursively through the structure to find all field elements
	 *
	 * @param		\ioform\Core\Element		$parent_element
	 * @param		int							$index
	 */
	protected function FindFields( \ioform\Core\Element $parent_element, &$index = 1 ){
		foreach( $parent_element->elements as $element ){
			if( $element instanceof \ioForm\Element\Field && $element->HasAttribute( 'name' ) ){
				// With file elements, you need to set the enctype
				if( $element instanceof \ioForm\Element\Field\File ){
					$this->SetAttribute( 'enctype', 'multipart/form-data' );
					$this->SetAttribute( 'method', 'post' );
				}

				// Fields lookup
				$this->fields[ $element->GetAttribute( 'name' ) ] = $element;

				// Add autoindex attribute
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
	
