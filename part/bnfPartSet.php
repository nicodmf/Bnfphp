<?php

namespace BNF;

/**
 * A succession of bnfPart(s)
 * 
 * A part set correspond to a set of sub part
 * positionning in a define order :
 * 
 * BNF representation : ( subpart1 subpart2 ... subpartn ) 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 *
 */

class bnfPartSet extends bnfPart{
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */
	public $type = bnf::PART_SET;
		/**
	 * The content of the rule
	 * @var array bnfPart $parts
	 */	
	public $parts = array();
	
	/**
	 * Simple link to render this object
	 * with the function render the parts includes
	 * @return string
	 */
	function render(){
		return bnfSubParts::render($this->parts, bnf::PART_SET);
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
