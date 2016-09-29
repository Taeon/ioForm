<?php

namespace ioForm;

/**
 * Create a form definition
 */
class Form extends \ioForm\Core\Definition{
	
	protected $fields = array();
	public $type = 'Form';
	
	/**
	 * Element container templates
	 * Use whatever markup you want here
	 * But be sure to always have an <elements/> tag in there somewhere
	 */
	protected $templates = array(
		'default' => '<label></label><elements/>',
		'radio-button' => '<elements/><label></label>'
	);	
	protected $buttons = array(
		array( 'type' => 'submit', 'value' => 'Submit' )
	);	
	
	public function Render(){
		$form = \ioForm\ioForm::CreateElement( $this );
		return $form->Render();
	}
	
	/**
	 * Add a validator to 
	 */
	public function SetValidator( $validator_definition ){
		foreach( $validator_definition->GetValidators() as $field_name => $validators ){
			$field = $this->GetField( $field_name );
			if( !$field ){
				continue;
			}
			foreach( $validators as $validator ){
				switch( $validator[ 'type' ] ){
					case 'Number':{
						if( isset( $validator[ 'values' ][ 'min' ] ) ){
							$field->SetAttribute( 'min', $validator[ 'values' ][ 'min' ] );
						}
						if( isset( $validator[ 'values' ][ 'max' ] ) ){
							$field->SetAttribute( 'max', $validator[ 'values' ][ 'max' ] );
						}
						if( isset( $validator[ 'values' ][ 'step' ] ) ){
							$field->SetAttribute( 'step', $validator[ 'values' ][ 'step' ] );
						}
						break;
					}
					case 'Length':{
						if( isset( $validator[ 'values' ][ 'min' ] ) ){
							$field->SetAttribute( 'data-nvalidate-minlength', $validator[ 'values' ][ 'min' ] );
						}
						if( isset( $validator[ 'values' ][ 'max' ] ) ){
							$field->SetAttribute( 'maxlength', $validator[ 'values' ][ 'max' ] );
						}
						break;
					}
					case 'Required':{
						if( !(isset( $validator[ 'enabled' ] ) && $validator[ 'enabled' ] == false ) ){
							$field->SetAttribute( 'required', true );
						}
						break;
					}
				}
			}
		}
	}
	
	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\Definition
	 */
	public function FromArray( $element ){
		
		$this->ArrayToDefinition( $element, $this );

		if( !( isset( $element[ 'type' ] ) && $element[ 'type' ] != 'Form' ) ){
			if( $this->buttons ){
				$buttons = array();
				foreach( $this->buttons as $button ){
					$definition = new \ioForm\Core\Definition();
					$definition->type = 'Button';
					$definition->button_type = $button[ 'type' ];
					if( isset( $button[ 'value' ] ) ){
						$definition->value = $button[ 'value' ];
					}
					$this->elements[] = $definition;
				}
			}
		}
		
		return $this;

	}
}