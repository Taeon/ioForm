<?php

namespace ioForm\Element\Layout;

class fieldset extends \ioForm\Core\Element{
	
	protected $tag = 'fieldset';
	protected $label = null;
	protected $legend = null;
	
	public function Render(){
		if( $this->legend === null && $this->label !== null ){
			$this->legend = $this->label;
		}
		if( $this->legend !== null ){
			$legend = new \ioForm\Element\Core\BaseElement();
			$legend->tag = 'legend';
			$legend->content = $this->legend;
			array_unshift( $this->elements, $legend );
		}
		return parent::Render();
	}
}
	
