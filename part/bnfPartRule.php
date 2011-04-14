<?php

namespace BNF;

/**
 * A bnfRule link present inside another rule
 * 
 * A part rule correspond to a rule in another rule
 * For table representation this rule haven't obligation
 * to is defined.
 * 
 * BNF representation : \<rule\> 
 * 
 * @author nicolas
 * @since 2011
 *
 */
class bnfPartRule extends bnfPart{
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */	
	var $type = bnf::PART_RULE;
	/**
	 * The bnf id
	 * @var string $bnfId
	 */
		var $bnfId;
	/**
	 * Construct the object
	 * @param $id
	 * @param $bnfId
	 * @return bnfPartRule
	 */
	function __construct($id, $bnfId){
		$this->id = $id;
		$this->bnfId = $bnfId;
	}
	/**
	 * Render the rule
	 * @return string
	 */	
	function render(){
		if(isset(bnfRule::$rules[$this->bnfId][$this->id]))
			return bnfRule::$rules[$this->bnfId][$this->id]->render();
		return $this->id.bnf::$separator;
	}
	/**
	 * Draw the graph
	 * @param integer $level
	 */
	function table($level){
		if(bnfSubParts::$changeToSet !== true)	$level++;
		if(bnf::$maxLevel<=$level or !isset( bnfRule::$rules[$this->bnfId][$this->id]))
			return "<div class=\"rule final ".$this->id."\"><span>".$this->id."</span></div>";		
		return bnfRule::$rules[$this->bnfId][$this->id]->table($level);
	}
}
