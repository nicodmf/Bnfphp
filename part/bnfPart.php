<?php

namespace BNF;

/**
 * A part of bnfRule
 * 
 * A part is a unity who permit to define a rule
 * The primary interest of this class is to parsed
 * the string given
 * 
 * This class can be serialize
 * 
 * @author Nicolas de Marqué Fromentin
 * @since 2011
 *
 */
abstract class bnfPart{
	/**
	 * The factory function who create a part
	 * with a string give in parameter and return
	 * the exact object define in the string
	 * @param string $strPart
	 * @param string $ruleId
	 * @return bnfPart 
	 */	
	static function factory($type, $partId, $bnfId)
	{		
		switch($type){
			case bnf::PART_OPTION            : return new bnfPartOption();
			case bnf::PART_RULE              : return new bnfPartRule($partId, $bnfId);
			case bnf::PART_SET               : return new bnfPartSet(); 
			case bnf::PART_WORD_QUOTED       : return new bnfPartWordQuoted($partId);
			case bnf::PART_WORD_SIMPLE_QUOTED: return new bnfPartWordSimpleQuoted($partId); 
			case bnf::PART_REPETITION        : return new bnfPartRepetition();
			default                          : return new bnfPartWord($partId, $bnfId);
		}
	}
	/**
	 * Permit to clone object and parts include
	 */
	function __clone()
	{
		if(isset($this->parts))
			for($i=0; $i<count($this->parts); $i++)
				$this->parts[$i] = clone $this->parts[$i];
	}

}