<?php

namespace ioForm\Core;

abstract class Element{
	
	protected $enabled = true;
	protected $tag;
	protected $is_singleton = false; // true => will never have child elements so no need for closing tag
	protected $classes = array();
	protected $elements = array();
	protected $attributes = array(
		'class' => null,
		'id' => null
	);
	public $content = '';
	public $container_template = false;
	public $container_class = false;
	public function GetTag(){
		return $this->tag . ':' . ((isset($this->type))?$this->type: '');
	}
	public function __construct( $element_definition = null ){

		$class = $this;
	    for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class);
		$properties = array();
		for( $i = count( $classes ) -1; $i >= 0; $i-- ){
			$reflection = new \ReflectionClass( $classes[ $i ]);
			$this->attributes = array_merge( $this->attributes, $reflection->getdefaultProperties()[ 'attributes' ] );
		}

		$this->attributes = (object)$this->attributes;
		if( $element_definition ){
			foreach( $element_definition as $name => $value ){
				switch( $name ){
					case 'type':
					case 'classes':{
						// Do nothing
						break;
					}
					default:{
						if( property_exists( $this, $name ) ){
							$this->$name = $value;
						} elseif( array_key_exists( $name, $this->attributes ) ){
							$this->attributes->$name = $value;
						}
					}
				}
			}

			// Iterate over child elements
			foreach( $element_definition->GetElements() as $element ){
				$element = \ioForm\ioForm::CreateElement( $element );
				$element->parent = $this;
				$this->AddElement( $element );
			}		
		}
	}

	/**
	 * Add a CSS class to this element
	 *
	 * @param		string		$class
	 */
	protected function AddClass( $class ){
		if( !in_array( $class, $this->classes ) ){
			$this->classes[] = $class;
		}
	}

	/**
	 * Set an attribute on this element
	 * Attributes are rendered in the element's HTML
	 *
	 * @param		string		$attribute
	 * @param		mixed		$value
	 */
	public function SetAttribute( $attribute, $value ){
		$this->attributes->$attribute = $value;
	}
	/**
	 * Get a list of all attributes for this element
	 *
	 * @return		object
	 */
	protected function GetAttributes(){
		return $this->attributes;
	}
	/**
	 * Get a specific attribute's value
	 *
	 * @param		string		$attribute
	 *
	 * @return		mixed
	 */
	protected function GetAttribute( $attribute ){
		return $this->attributes->$attribute;
	}
	/**
	 * Check whether an attribute has been set on this element
	 *
	 * @param		string		$attribute
	 *
	 * @return		boolean
	 */
	protected function HasAttribute( $attribute ){
		return property_exists( $this->attributes, $attribute ) && $this->attributes->$attribute !== null;
	}
	/**
	 * Get the content for this element
	 *
	 * @return		string
	 */
	protected function GetContent(){
		return $this->content;
	}

	/**
	 * Add a child element to this element
	 *
	 * @param		\ioForm\Core\Element		$element
	 */
	public function AddElement( \ioForm\Core\Element $element ){
		$this->elements[] = $element;
	}
	
	/**
	 * Process this element's properties and return it as a string for display
	 *
	 * @return		string
	 */
	public function Render(){
		
		// Don't render at all
		if( !$this->enabled ){
			return '';
		}
		
		$output = '';
		
		// Some elements only exist to render their child elements
		if( $this->tag ){
			$attributes = array();
			
			// Assemble attributes into key/value pairs
			foreach( $this->GetAttributes() as $name => $value ){
				if( $value !== null ){
					// Some attributes don't have a value, they're just true or false (so only attribute name is rendered)
					// e.g. disabled, required
					if( is_bool( $value ) ){ 
						// Only render if true
						if( $value ){
							$attributes[$name] = false;
						}
					} else {
						$attributes[$name] = $value;
					}
				}
			}
			// Convert classes to a single attribute			
			if( count( $this->classes ) > 0 ){
				if( isset( $attributes[ 'class' ] ) ){
					$this->AddClass( $attributes[ 'class' ] );
				}
				$attributes[ 'class' ] = implode( ' ', $this->classes );
			}
			// Render attributes
			$attributes_strings = array();
			if( count( $attributes ) > 0 ){
				foreach( $attributes as $attribute => $value ){
					$attributes_strings[] = $attribute . '="' . htmlentities( $value ) . '"';
				}
			}
			$output .= '<' . $this->tag . ((count($attributes_strings) > 0)?' ':'') . implode( ' ', $attributes_strings);
		}
		
		// No closing tag, e.g. <br/>, <input.../>
		if( $this->is_singleton ){
			if( $this->tag ){
				$output .= '/>';
			}
		} else {
			if( $this->tag ){
				$output .= '>';
			}
			
			// Render child elements
			if( count( $this->elements ) > 0 ){
				$content = '';
				foreach( $this->elements as $element ){
					$content .= $element->Render();
				}
				if( $content ){
					$output .= $content;
				}
			}
			// Render any content
			if( $content = $this->GetContent() ){
				$output .= $content;
			}
			// Closing tag?
			if( $this->tag ){
				$output .= '</' . $this->tag .  '>';
			}
		}
		
		return $output;
	}
}