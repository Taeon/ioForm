<?php

namespace ioForm\Element\Field;

class radio extends \ioForm\Element\Field{

	protected $tag = false;
	protected $options = array();
	public $show_label_for = false;

	public function __construct( $element_definition ){
		// Separate out element classes
		$classes = array();
		$element_classes = array();
		if( isset( $element_definition->classes ) ){
			foreach( $element_definition->classes as $class ){
				if( strpos( $class->element, 'element-' ) === 0 ){
					$element_classes[] = $class;
				} else {
					$classes[] = $class;
				}
			}
		}
		$element_definition->classes = $classes;
		parent::__construct( $element_definition );
		$this->options = array();
		// This prevents conflicts with other radio buttons with the same name in other forms
		$id = date('U') . rand( 0,10000 ) . '-' . $this->GetAttribute( 'name' ) . '-';
		foreach( $element_definition->options as $option ){
			$definition = new \ioform\Core\Definition();
			$definition->type = 'radio_button';
			$definition->name = $this->GetAttribute( 'name' );
			$definition->id = $id . $option[ 'value' ];
			$definition->value = $option[ 'value' ];
			$definition->label = $option[ 'text' ];
			if( isset( $option[ 'disabled' ] ) && $option[ 'disabled' ] ){
				$definition->disabled = true;
			}
			if( isset( $element_definition->class ) ){
				$definition->class = $element_definition->class;
			}
			$definition->classes = $element_classes;
			if( isset( $option[ 'tabindex' ] ) && $option[ 'tabindex' ] !== null ){
				$definition->tabindex = $option[ 'tabindex' ];
			}
			$definition->SetTemplates( $element_definition->GetTemplates() );
			// Use a custom template for the buttons
			if( isset( $element_definition->element_container_template ) ){
				$definition->container_template = $element_definition->element_container_template;
			}
			if( isset( $option[ 'data' ] ) ){
				$definition->data = $option[ 'data' ];
			}
			$definition->SetParent( $element_definition );
			$option = \ioForm\ioForm::CreateElement( $definition );
			$this->options[] = $option;
			$this->AddElement( $option );
		}
		$this->SetAttribute( 'id', null );

		// For setting value later
		$this->FindOptions( $this );

		// Set default value
		if( $element_definition->default !== null ){
			$this->SetValue( $element_definition->default );
		}
	}

	public function GetOptions(){
		return $this->options;
	}

	/**
	 * Recursively look through this field's element structure for option objects
	 */
	public function FindOptions( $element ){
		foreach( $element->elements as $child ){
			if( $child instanceof \ioForm\Element\Field\RadioButton ){
				$this->options[] = $child;
			} else {
				$this->FindOptions( $child );
			}
		}
	}
	/**
	 * Set field value
	 */
	public function SetValue( $value ){
		foreach( $this->options as $option ){
			if( (string)$option->GetAttribute( 'value' ) == (string)$value ){
				$option->SetAttribute( 'checked', true );
			} else {
				$option->SetAttribute( 'checked', false );
			}
		}
	}
}
