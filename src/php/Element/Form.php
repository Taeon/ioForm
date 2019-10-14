<?php

namespace ioForm;

/**
 * Create a form definition
 */
class Form extends \ioForm\Core\Definition{

	protected $fields = array();
	public $type = 'Form';
	public $method = 'get';

	/**
	 * Element container templates
	 * Use whatever markup you want here
	 * But be sure to always have an <elements/> tag in there somewhere
	 */
	protected $templates = array(
		'default' => '<label></label><elements/>',
		'radio-button' => '<elements/><label></label>',
		'buttons' => '<elements/>'
	);

	protected $buttons_container = 'buttons';
	protected $buttons = array(
		array( 'type' => 'submit', 'value' => 'Submit' )
	);

	protected $auto_tabindex = false;
	protected $tabindex_start = 1;

	protected $auto_field_id = false;
	protected $auto_field_id_prefix = true;

	// Use button elements instead of inputs for buttons (e.g. submit)
	protected $use_buttons = false;

	protected $auto_field_class = true;

	protected $values = array();

	public function ElementAdded( $definition ){
		parent::ElementAdded( $definition );
		// It's a field, so store it in the fields lookup
		if( $definition->IsField() ){
			$this->fields[ $definition->name ] = $definition;
		} else {
			// We need to recurse into child elements
			foreach( $definition->elements as $child ){
				$this->ElementAdded( $child );
			}
		}
	}
	public function ElementRemoved( $definition ){
		parent::ElementRemoved( $definition );
		// It's a field, so store it in the fields lookup
		if( $definition->IsField() ){
			if( isset( $this->fields[ $definition->name ] ) ){
				unset( $this->fields[ $definition->name ] );
			}
		} else {
			// We need to recurse into child elements
			foreach( $definition->elements as $child ){
				$this->ElementRemoved( $child );
			}
		}
	}

	/**
	 * List all fields
	 *
	 * @return		\ioForm\Core\Definition
	 */
	public function GetFields(){
		return $this->fields;
	}

	/**
	 * Find a field
	 *
	 * @param		string		$field_name
	 *
	 * @return		\ioForm\Core\Definition
	 */
	public function GetField( $field_name ){
		if( isset( $this->fields[ $field_name ] ) ){
			return $this->fields[ $field_name ];
		}
		return null;
	}

	/**
	 * Return a field by its name
	 *
	 * @param		string		$name
	 *
	 * @return		\ioform\Element\Field
	 */
	public function HasField( $field_name ){
		if( isset( $this->fields[ $field_name ] ) ){
			return true;
		}
		return false;
	}

	public function Render(){
		$index = $this->tabindex_start;
		$form_id = $this->id;
		if( !$this->id ){
			$form_id = date('U') . rand( 0,10000 );
		}
		foreach( $this->FindFields( $this ) as $field ){
			// Set tabindex
			// We do this just before render, because the form's structure might've changed
			if( $this->auto_tabindex ){
				if( $field->type != 'radio' ){
					$field->tabindex = $index;
					$index++;
				} else {
					foreach( $field->options as $option_index => $option ){
						$option = (object)$option;
						$field->options[ $option_index ]->tabindex = $index;
						$index++;
					}
				}
			}
			// Automatically set field ID from its name
			if( $this->auto_field_id ){
				if( !( isset( $field->id ) ) ){
					// Auto-prepend with form ID, if set
					$field->id = $form_id . '-' . $field->name;
				}
			}
			// Automatically set container class with field's type
			if( $this->auto_field_class ){
				if( !isset( $field->classes ) ){
					$field->classes = array();
				} else {
					// Just in case
					if( is_string( $field->classes ) ){
						$field->classes = array( $field->classes );
					}
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
			$this->SetValue( $name, $value );
		}
	}
	public function SetValue( $name, $value ){
		$this->values[ $name ] = $value;
	}

	/**
	 * Get a list of all form values
	 *
	 * @param 		boolean		$raw		Return raw values rather than converted
	 *
	 * @return		array
	 */
	public function GetValues( $raw = false ){
		// Get values that have been applied by code
		$values = $this->values;

		// Get submitted values (if any)
		$raw_values = array();
		if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) == strtolower( $this->method ) ){
			switch( strtolower( $this->method ) ){
				case 'get':{
					$raw_values = $_GET;
					break;
				}
				case 'post':{
					$raw_values = $_POST;
					break;
				}
				default:{
					throw new \Exception( "GetValue not supported for method '" . $this->method . "'" );
				}
			}
		}

		// Get a list of all fields
		foreach( $this->fields as $field ){
			if( !$field->enabled ){
				continue;
			}
			// Is there a submitted value?
			if( isset( $raw_values[ $field->name ] ) ){
				// Yes there's a submitted value
				$values[ $field->name ] = $raw_values[ $field->name ];
			} elseif( !isset( $values[ $field->name ] ) ){
				// No value submitted and nothing set by code, so...
				// is there a default value?
				if( $field->default !== null ){
					$values[ $field->name ] = $field->default;
				} else {
					// No default value, so just return an empty string
					$values[ $field->name ] = '';
				}
			}
			// Convert values or return raw?
			if( !$raw ){
				// Conert values
				switch( $field->type ){
					case 'date':{
						if( $values[ $field->name ] !== '' ){
							// Convert to DateTime object
							$values[ $field->name ] = new \DateTime( $values[ $field->name ] );
						}
						break;
					}
					case 'number':{
						if( $values[ $field->name ] !== '' ){
							// Convert to number
							$values[ $field->name ] = $values[ $field->name ] + 0;
						}
						break;
					}
					case 'checkbox':{
						$values[ $field->name ] = ($values[ $field->name ]==='false')?false:(boolean)$values[ $field->name ];
						break;
					}
				}
			}
		}

		return $values;
	}

	/**
	 * Convert an element definition array in an element definition object
	 *
	 * @param		array		$element		A definition in the form of an array
	 *
	 * @return		ioform\Core\DefinitionF
	 */
	public function FromArray( $element ){
		$this->ArrayToDefinition( $element, $this );
		if( $this->buttons ){
			$buttons = array();
			if( $this->buttons_container ){
				$container = new \ioForm\Core\Definition();
				$container->type = 'layout';
				$container->parent = $this;
				$container->container_template = $this->buttons_container;
				$this->elements[] = $container;
				$this->ElementAdded( $container );
			} else {
				$container = $this;
			}

			foreach( $this->buttons as $button ){
				$definition = new \ioForm\Core\Definition();
				$definition->type = 'button';
				$definition->is_button = $this->use_buttons;
				$definition->button_type = $button[ 'type' ];
				if( isset( $button[ 'value' ] ) ){
					$definition->value = $button[ 'value' ];
				}
				if( isset( $button[ 'class' ] ) ){
					$definition->class = $button[ 'class' ];
				}
				if( isset( $button[ 'data' ] ) ){
					$definition->data = $button[ 'data' ];
				}
				if( isset( $button[ 'alias' ] ) ){
					$definition->alias = $button[ 'alias' ];
				}
				if( isset( $button[ 'id' ] ) ){
					$definition->id = $button[ 'id' ];
				}

				$definition->parent = $container;
				$container->elements[] = $definition;
				$this->ElementAdded( $definition );
			}
			$this->buttons = array();
		}

		return $this;
	}

	public function FindFields( $definition, $field_container_template_default = array() ){

		$fields = array();
		foreach( $definition->field_container_template_default as $type => $template ){
			$field_container_template_default[ $type ] = $template;
		}

		foreach( $definition->elements as $element ){
			if( $element->IsField() ){
				$fields[] = $element;
				// Set default container template for field type
				if( !isset( $element->container_template ) && isset( $field_container_template_default[ $element->type ] ) ){
					$element->container_template = $field_container_template_default[ $element->type ];
				}
			}
			if( count( $element->elements ) > 0 ){
				$fields = array_merge( $fields, $this->FindFields( $element, $field_container_template_default ) );
			}
		}
		return $fields;
	}
}
