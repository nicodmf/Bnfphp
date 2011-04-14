<?php

namespace BNF;

/**
 * A string with simple quotes
 * 
 * BNF representation : 'a string' 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 */

class bnfPartWordSimpleQuoted extends bnfPart{

	/**
	 * The type in centralized string	 
	 * @var string $type
	 */
	public $type = bnf::PART_WORD_SIMPLE_QUOTED;
	/**
	 * The word
	 * @var string $id
	 */
	public $id;
	/**
	 * Construct the object
	 * @param $id
	 * @return bnfPartWordSimpleQuoted
	 */
	function __construct($id){
		$this->id = $id;
	}
	/**
	 * Simple link to render this object
	 * with the function render the parts includes
	 * @return string
	 */
	function render(){
		return $this->id.bnf::$separator;
	}
	/**
	 * Simple link to render the graph of this object
	 * with the function render the parts includes
	 * @param integer $level
	 * @return string
	 */
	function table($level){
		return "<div class=\"word simplequoted final\"><span>".htmlspecialchars($this->id)."</span></div>";	
	}

}
