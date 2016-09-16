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
	public function __construct( $element_definition ){
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
				$element = $this->CreateElement( $element );
				$element->parent = $this;
				$this->AddElement( $element );
			}		
		}
	}

	/**
	 * Take an element definition and return an element object
	 * It's recursive, so if the element has any child elements it'll render those too
	 *
	 * @param		\ioForm\Core\Definition
	 *
	 * @return		\ioForm\Element
	 */
	protected function CreateElement( \ioForm\Core\Definition $element_definition ){
		if( strpos( $element_definition->type, ':' ) !== false ){
			list( $element_class, $element_type ) = explode( ':', $element_definition->type );
		} else {
			$element_class = 'Field';
			$element_type = $element_definition->type;
		}
		
		$element_class = '\\ioForm\\Element\\' . (($element_class)?$element_class . '\\':'') . $element_type;
		$element = new $element_class( $element_definition );

		$container_classes = array();
		if( !empty( $element_definition->classes ) ){
			if( is_array( $element_definition->classes ) ){
				// It's a list of objects with class details
				foreach( $element_definition->classes as $class ){
					// By default it's added to the element utself, unless the 'element' property dictates otherwse
					if( isset( $class->element ) && $class->element ){
						switch( $class->element ){
							// The field itself. This isn't required -- just miss out the 'element' property
							case 'element':{
								$element->AddClass( $class->classes );
								break;
							}
							// Anything else
							default:{								
								$container_classes[] = $class;
								break;
							}
						}
					} else {
						$element->AddClass( $class->classes );
					}
				}
			} else {
				// It's a string
				$element->AddClass( $element_definition->classes );
			}
		}
		
		// Does it have a container?
		if( $element->container_template ){
			// Wrap in container
			$container_definition = new \ioForm\Core\Definition();
			$container_definition->SetTemplates( $element_definition->GetTemplates() );
			$container_definition->type = 'Core:Container';
			$container_definition->template_alias = $element->container_template;
			$container_definition->SetParent( $element_definition->GetParent() );
			$element_definition->SetParent( $container_definition );

			// Create new container element
			// Check if it's been cached already (to save re-parsing the template string)
			$hash = sha1( $container_definition->GetTemplate( $container_definition->template_alias ) );
			if( !$container = \ioForm\Cache::Retrieve( $hash ) ){
				// Nope, need to create a new one
				$container = $this->CreateElement( $container_definition );
				\ioForm\Cache::Store( $hash, $container );
			}

			$container->SetElements( array( $element ) );
			if( property_exists( $element_definition, 'label' ) ){
				// Set label
				$container->SetLabel( $element_definition->label, $element );
			} else {
				// Don't show a label
				$container->SetLabel( null, $element );
			}
			foreach( $container_classes as $class ){
				if( $class->element == $container->role ){
					$container->AddClass( $class->classes );
				} else {
					$container->AddClass( $class->classes, $class->element );
				}
			}
			
			$element = $container;
		}
		if( property_exists( $element_definition, 'content' ) ){
			$element->content = $element_definition->content;			
		}
		
		return $element;
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
			$output .= '<' . $this->tag . ((count($attributes_strings > 0))?' ':'') . implode( ' ', $attributes_strings);
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