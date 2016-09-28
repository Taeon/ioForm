<?php

namespace ioForm\Element\Core;

class Container extends \ioForm\Core\Element{
	
	protected $lookup = array();
	public $role = 'row';
	
	public $template_alias = '';

	protected $temp = array();
	
	public function __construct( $field_definition = null ){
		parent::__construct( $field_definition );

		$this->ParseTemplateString( $field_definition->GetTemplate( $field_definition->template_alias ) );

		$this->lookup = (object)$this->lookup;
	}
	public function AddClass( $class, $element = false ){
		if( $element ){
			$this->lookup->$element->AddClass( $class );
		} else {
			parent::AddClass( $class );
		}
	}
	
	/**
	 * Pass in the element(s) for this container
	 *
	 * @param		\ioForm\Element		$field
	 */
	public function SetElements( $elements ){
		$this->lookup->{'elements-container'}->elements = $elements;
	}
	
	/**
	 * Add a child element to this element
	 *
	 * @param		\ioForm\Core\Element		$element
	 */
	public function AddElement( \ioForm\Core\Element $element ){
		if( isset( $this->lookup->{'elements-container'} ) ){
			$this->lookup->{'elements-container'}->AddElement( $element );
		} else {
			// Might not have parsed the structure yet, so store these for later
			$this->temp[] = $element;
		}
	}
	
	public function SetLabel( $label, $field ){
		
		// No label
		if( $label === null ){
			$this->lookup->label->enabled = false;
			return;
		}
		if( !isset( $this->lookup->label ) ){
			return;
		}
		
		$this->lookup->label->content = htmlentities( $label );

		// Link label to field with 'for' attribute
		$id = null;
		if( $field->HasAttribute( 'id' ) ){
			$id = $field->GetAttribute( 'id' );
		} else {
			$id = $field->GetAttribute( 'name' );
		}
		if( $id !== null && $field->show_label_for ){
			$this->lookup->label->SetAttribute( 'for', $id );
			$field->SetAttribute( 'id', $id );
		}
	}
	
	/**
	 * Populate this container from an HTML string
	 */
	public function ParseTemplateString( $template_string ){
		// Load the HTML string into a DOM document
		$dom = new \DOMDocument;
		$dom->loadHTML( $template_string );
		$xpath = new \DOMXPath($dom);
		// The HTML is passed into the body of the DOM document
		$body = $nodes = $xpath->query('/html/body/*');
		// We can't have multiple 'root' nodes in the HTML
		if( $body->length > 1 ){
			throw new \Exception( 'Field container template may not contain more than one root element' );
		}

		// We store our various 'elements 'important' elements in the lookup (anything marked with a class beginning with ioform-)
		$this->lookup = (object)$this->lookup;

		// Parse the root node
		$node = $this->ParseTemplateNode( $body->item( 0 ) );
		$this->tag = $node->tag;
		foreach( $node->attributes as $attribute => $value ){
			// data-ioform-* attributes are for internal use only
			if( strpos( $attribute, 'data-ioform-' ) !== 0 ){
				$this->SetAttribute( $attribute, $value );
			}
		}

		// No element container set
		if( !isset( $this->lookup->{'elements-container'} ) ){
			throw new \Exception( 'No element container found in template (you need a comment like this <!--elements--> somewhere in your template"): ' . $template_string );
		}

		foreach( $node->elements as $element ){
			if( $element !== null ){
				$this->elements[] = $element;
			}
		}
	}
	/**
	 * Convert an XML node into an element object
	 */
	protected function ParseTemplateNode( $node ){

		$definition = new \ioform\Core\Definition();
		$definition->type = 'Core:BaseElement';
		if($node->nodeName != '#text'){
			$definition->tag = $node->nodeName;
		} else{
			$definition->tag = null;
			$definition->content = $node->nodeValue;
			// Emptry text node (or whitespace) 
			if( trim( $definition->content ) == '' ){
				return null;
			}
		}

		$node_obj = \ioForm\ioForm::CreateElement( $definition );
		if( $node->nodeName == 'label' ){
			$this->lookup->label = $node_obj;
		}
		// Look for comments
		if( $node->nodeName == '#comment' ){
			switch( strtolower( trim( $node->nodeValue ) ) ){
				case 'elements':{
					$this->lookup->{'elements-container'} = new \ioForm\Element\Core\BaseElement( false );
					// Add any elements that were added before this was defined
					foreach( $this->temp as $element ){
						$this->AddElement( $element );
					}
					$this->temp = null;
					return $this->lookup->{'elements-container'};
					break;
				}
			}
		}

		if($node->attributes){
			foreach( $node->attributes as $attribute ){
				switch( $attribute->name ){
					case 'data-ioform-role':{
						$this->lookup->{ $attribute->value } = $node_obj;
						break;
					}
					default:{
						break;
					}
				}
				$node_obj->SetAttribute( $attribute->name, $attribute->value );
			}
		}
		if($node->childNodes){
			foreach ($node->childNodes as $child_node) {
				if( $child_node_obj = $this->ParseTemplateNode( $child_node ) ){
					$node_obj->AddElement( $child_node_obj );
				}
			}
		}

		return $node_obj;
	}
	function __clone() {
        foreach ($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }
}
	
