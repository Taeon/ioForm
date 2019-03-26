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

	// Refer to a container template by its index in the templates array
	public $container_template = false;
	// Pass a string directly to be used as a container template. Overrides $container_template
	public $container = false;
	// Whether or not to add 'for' attribute in labels
	public $show_label_for = false;

	protected $element_classes = array();
	protected $element_content = array();

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
					case 'data':{
						foreach( $value as $key => $data_value ){
							$this->SetAttribute( 'data-' . $key, $data_value );
						}
						break;
					}
					case 'classes':{
						if( is_array( $element_definition->classes ) ){

							// It's a list of objects with class details
							foreach( $element_definition->classes as $class ){
								// By default it's added to the element itself, unless the 'element' property dictates otherwise
								if( isset( $class->element ) && $class->element ){
									switch( $class->element ){
										// The field itself. This isn't required -- just miss out the 'element' property
										case 'element':{
											$this->AddClass( $class->class );
											break;
										}
										// Anything else
										default:{
											if( !isset( $this->element_classes[ $class->element ] ) ){
												$this->element_classes[ $class->element ] = array();
											}
											$this->element_classes[ $class->element ][] = $class->class;
											break;
										}
									}
								} else {
									if( is_string( $class ) ){
										$this->AddClass( $class );
									} else {
										throw new \Exception( 'Additional field classes not defined correctly.' );
										exit;
									}
								}
							}
						} else {
							// It's a string
							$this->AddClass( $element_definition->classes );
						}

						break;
					}
					case 'content':{
						if( is_array( $element_definition->content ) ){
							// It's a list of objects with content details
							foreach( $element_definition->content as $content ){
								// By default it's added to the element utself, unless the 'element' property dictates otherwse
								if( isset( $content->element ) && $content->element ){
									switch( $content->element ){
										// The field itself. This isn't required -- just miss out the 'element' property
										case 'element':{
											$this->content = $content->content;
											break;
										}
										// Anything else
										default:{
											if( !isset( $this->element_content[ $content->element ] ) ){
												$this->element_content[ $content->element ] = array();
											}
											$this->element_content[ $content->element ][] = $content->content;
											break;
										}
									}
								} else {
									$this->content = $content->content;
								}
							}
						} else {
							// It's a string
							$this->content = $element_definition->content;
						}

						break;
					}
					case 'attributes':{
						foreach( $value as $key => $value ){
							$this->attributes->$key = $value;
						}
						break;
					}
					case 'type':{
						// Do nothing
						break;
					}
					default:{
						if( property_exists( $this, $name ) ){
							$this->$name = $value;
						} elseif( array_key_exists( $name, $this->attributes ) ){
							$this->attributes->$name = $value;
						} else {

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

		$container_template = false;
		// Container?
		if( $this->container ){
			$container_template = $this->container;
		} elseif( $this->container_template ){
			$container_template = $element_definition->GetTemplate( $this->container_template );
		}
		$this->container_element = false;
		if( $container_template ){
			// Wrap in container
			$container_definition = new \ioForm\Core\Definition();
			$container_definition->type = 'Core:Container';
			$container_definition->template = $container_template;
			// Check if it's been cached already (to save re-parsing the template string)
			$hash = sha1( $container_template );
			if( !$this->container_element = \ioForm\Cache::Retrieve( $hash ) ){
				// Nope, need to create a new one
				$this->container_element = \ioForm\ioForm::CreateElement( $container_definition );
				\ioForm\Cache::Store( $hash, $this->container_element );
			}
			if( property_exists( $element_definition, 'label' ) ){
				// Set label
				$this->container_element->SetLabel( $element_definition->label, $this );
			} else {
				// Don't show a label
				$this->container_element->SetLabel( null, $this );
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

		// Does it have a container?
		if( $this->container_element ){
			$temp = \ioForm\ioForm::CreateElement(
				( new \ioForm\Core\Definition )->FromArray(
					array(
						'type'=>'Core:BaseElement'
					)
				)
			);
			$this->container_element->AddElement(
				$temp
			);
		}

		if( $this instanceof \ioForm\Element\Field ){
			if( isset( $this->help ) ){
				if( $this->container_element && $help = $this->container_element->GetByAlias( 'help' ) ){
					$help->content = $this->help;
				} else {
					$help = \ioForm\ioForm::CreateElement(
						( new \ioForm\Core\Definition )->FromArray(
							array(
								'type'=>'layout:div',
								'content' => $this->help
							)
						)
					);
					$this->container_element->AddElement( $help, 'help' );
				}
				$help_id = $this->attributes->id . '-help';
				$help->SetAttribute( 'id', $help_id );
				$this->SetAttribute( 'aria-describedby', $help_id );
			} else {
				if( $this->container_element && $help = $this->container_element->GetByAlias( 'help' ) ){
					$help->enabled = false;
				}
			}

			if( $this->container_element ){
				// Apply classes for elements within container
				foreach( $this->element_classes as $element => $classes ){
					foreach( $classes as $class ){
						$this->container_element->GetByAlias( $element )->AddClass( $class );
					}
				}
				foreach( $this->element_content as $element => $contents ){
					foreach( $contents as $content ){
						$this->container_element->GetByAlias( $element )->content .= $content;
					}
				}
			}
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
					$attributes_strings[] = $attribute . ((is_bool($value))?'':'="' . htmlentities( $value ) . '"');
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

		if( $this->container_element ){
			$temp->content = $output;
			return $this->container_element->Render();
		}

		return $output;
	}
}
