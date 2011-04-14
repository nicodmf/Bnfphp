<?php

namespace BNF;

/**
 * A repetition of bnfPart
 * 
 * A part repetition correspond to a repetition
 * of sub parts 
 * 
 * BNF representation : { subpart1 subpart2 ... subpartn } 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 *
 */

class bnfPartRepetition extends bnfPart{
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */	
	public $type = bnf::PART_REPETITION;
		/**
	 * The content of the rule
	 * @var array bnfPart $parts
	 */	
	public $parts = array();	
	/**
	 * Render this object and repeat the render
	 * with rand condition
	 * @return string
	 */	
	function render(){
		$render = "";
		while(mt_rand(0,1)===0)
			$render .= bnfSubParts::render($this->parts, bnf::PART_REPETITION);
		return $render;
	}
	/**
	 * Simple link to render the graph of this object
	 * with the function render the parts includes
	 * @param integer $level
	 * @return string
	 */
		function table($level){
		return bnfSubParts::table($this, $level);
	}

}

