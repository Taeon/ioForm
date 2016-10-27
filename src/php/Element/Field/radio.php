<?php

namespace ioForm\Element\Field;

class radio extends \ioForm\Element\Field{
	
	protected $tag = false;
	protected $options = array();
	public $show_label_for = false;

	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		$this->options = array();
		foreach( $element_definition->options as $option ){
			$definition = new \ioform\Core\Definition();
			$definition->type = 'radio_button';
			$definition->name = $this->GetAttribute( 'name' );
			$definition->id = $this->GetAttribute( 'name' ) . '-' . $option[ 'value' ];
			$definition->value = $option[ 'value' ];
			$definition->label = $option[ 'text' ];
			if( isset( $option[ 'tabindex' ] ) && $option[ 'tabindex' ] !== null ){
				$definition->tabindex = $option[ 'tabindex' ];
			}
			$definition->SetTemplates( $element_definition->GetTemplates() );
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