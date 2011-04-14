<?php

namespace BNF;

/**
 * A string with double quotes
 * 
 * BNF representation : "a string" 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 *
 */

class bnfPartWordQuoted extends bnfPart{
	/**
	 * The word
	 * @var string $id
	 */
	public $id;
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */
	public $type = bnf::PART_WORD_QUOTED;
	/**
	 * Construct the object
	 * @param $id
	 * @return bnfPartWordQuoted
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
		return "<div class=\"word quoted final\"><span>".htmlspecialchars($this->id)."</span></div>";	
	}

}
