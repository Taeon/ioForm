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
	
	protected $auto_tabindex = false;
	protected $tabindex_start = 1;
	
	protected $auto_field_id = false;
	protected $auto_field_id_prefix = true;
	
	protected $auto_field_class = true;
	
	protected $values = array();

	public function Render(){
		if( $this->buttons ){
			$buttons = array();
			foreach( $this->buttons as $button ){
				$definition = new \ioForm\Core\Definition();
				$definition->type = 'button';
				$definition->button_type = $button[ 'type' ];
				if( isset( $button[ 'value' ] ) ){
					$definition->value = $button[ 'value' ];
				}
				if( isset( $button[ 'class' ] ) ){
					$definition->class = $button[ 'class' ];
				}
				$this->elements[] = $definition;
			}
			$this->buttons = array();
		}
		$index = $this->tabindex_start;
		foreach( $this->fields as $field ){
			// Set tabindex
			// We do this just before render, because the form's structure might've changed
			if( $this->auto_tabindex ){
				if( $field->type != 'radio' ){
					$field->tabindex = $index;
					$index++;
				} else {
					foreach( $field->options as $option_index => $option ){
						$field->options[ $option_index ][ 'tabindex' ] = $index;
						$index++;
					}
				}
			}
			// Automatically set field ID from its name
			if( $this->auto_field_id ){
				if( !( isset( $field->id ) ) ){
					// Auto-prepend with form ID, if set
					$field->id = (($this->id)?$this->id . '-':'' ) . $field->name;
				}
			}
			// Automatically set container class with field's type
			if( $this->auto_field_class ){
				if( !isset( $field->classes ) ){
					$field->classes = array();
				}
				$field->classes[] = (object)array( 'element' => 'container', 'class' => strtolower( $field->type ) );
			}
		}
		

		// Create a form element
		$form = \ioForm\ioForm::CreateElement( $this );
		$form->Populate( $this->values );
		return $form->Render();
	}

	public function SetValues( $values ){
		foreach( $values as $name => $value ){
			$this->values[ $name ] = $value;
		}
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
		return $this;

	}
}