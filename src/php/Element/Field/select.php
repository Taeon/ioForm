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
		if( is_array( $value ) ){
			foreach( $value as $index => $item ){
				$value[ $index ] = (string)$item;
			}
		} else {
			$value = (string)$value;
		}
		foreach( $this->options as $index => $element ){
			if( is_array( $value ) ){
				if( in_array( (string)$element[ 'value' ], $value ) ){
					$this->options[ $index ][ 'selected' ] = true;
				} else {
					$this->options[ $index ][ 'selected' ] = false;
				}
			} else {
				if( $value !== null && (string)$element[ 'value' ] === $value ){
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
			$data = '';
			if( isset( $option[ 'data' ] ) ){
				$data = array();
				foreach( $option[ 'data' ] as $key => $value ){
					$data[] = ' data-' . $key . '="' . htmlentities( $value ) . '"';
				}
				$data = implode( ' ', $data );
			}
			$options_html .= '<option value="' . htmlentities( $option[ 'value' ] ) . '"' . ((isset($option['selected'])&&$option['selected'])?' selected':'') . '' . $data . '>' . htmlentities( $option[ 'text' ] ) . '</option>';
		}
		$this->content = $options_html;

		return parent::Render();
	}
}
