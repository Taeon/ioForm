<?php

namespace ioForm;

class ioForm{
	/**
	 * Take an element definition and return an element object
	 * It's recursive, so if the element has any child elements it'll render those too
	 *
	 * @param		\ioForm\Core\Definition
	 *
	 * @return		\ioForm\Element
	 */
	public static function CreateElement( \ioForm\Core\Definition $element_definition ){
		$element = false;
		if( strpos( $element_definition->type, ':' ) !== false ){
			list( $element_class, $element_type ) = explode( ':', $element_definition->type );
			switch( $element_class ){
				case 'Layout':{
					if( in_array( $element_type, array( 'Fieldset' ) ) ){
						$element_class = '\\ioForm\\Element\\' . $element_class . '\\' . $element_type;
					} else {
						$element_definition->tag = strtolower( $element_type );
						$element = new \ioForm\Element\Core\BaseElement( $element_definition );
					}
					break;
				}
				default:{
					$element_class = '\\ioForm\\Element\\' . $element_class . '\\' . $element_type;
					break;
				}
			}
		} elseif( $element_definition->type == 'Form' ) {
			$element_class = '\\ioForm\\Element\\Form';
		} else {
			$element_class = '\\ioForm\\Element\\Field\\' . $element_definition->type;
		}
		
		if( !$element ){
			$element = new $element_class( $element_definition );
		}

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
				$container = self::CreateElement( $container_definition );
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

}