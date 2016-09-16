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
		$definition->SetTemplates( array_merge( $this->templates, $definition->GetTemplates() ) );
		$definition->parent = $this;
		$this->elements[] = $definition;
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