<?php

namespace ioForm\Element\Field;

class select extends \ioForm\Element\Field{
	
	protected $tag = 'select';
	protected $options = array();
	
	public function __construct( $element_definition ){
		parent::__construct( $element_definition );
		
		if( $element_definition->default !== null ){
			$this->SetValue( $element_definition->default );
		}
	}
	public function SetValue( $value ){
		foreach( $this->options as $index => $element ){
			if( is_array( $value ) ){
				if( in_array( $element[ 'value' ], $value ) ){
					$this->options[ $index ][ 'selected' ] = true;
				} else {
					$this->options[ $index ][ 'selected' ] = false;
				}					
			} else {
				if( $element[ 'value' ] == $value ){
					$this->options[ $index ][ 'selected' ] = true;
				} else {
					$this->options[ $index ][ 'selected' ] = false;
				}					
			}		
		}
	}
	/**
	 * Rather than create an element object for every option, it's much faster to just spit out HTML directly
	 */
	public function Render(){
		$options_html = '';
		foreach( $this->options as $option ){
			$options_html .= '<option value="' . htmlentities( $option[ 'value' ] ) . '"' . ((isset($option['selected'])&&$option['selected'])?' selected':'') . '>' . htmlentities( $option[ 'text' ] ) . '</option>';
		}
		$this->content = $options_html;

		return parent::Render();
	}
}