<?php

namespace BNF;

/**
 * A succession of optionnals bnfPart
 *  
 * A part option correspond to an 
 * option of sub parts :
 * 
 * BNF representation : [ bnfPart1 bnfPart2 ... bnfPartn ] 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 *
 */
class bnfPartOption extends bnfPart{
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */	
	public $type = bnf::PART_OPTION;
	/**
	 * The content of the rule
	 * @var array bnfPart $parts
	 */	
	public $parts = array();
	/**
	 * Simple link to render this object optionaly
	 * with the function render the parts includes
	 * @return string
	 */
	function render(){
		if(mt_rand(0,1)==0)
			return bnfSubParts::render($this->parts, bnf::PART_OPTION);
		return "";
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
