<?php

namespace BNF;

/**
 * A word 
 * 
 * BNF representation : word 
 * 
 * @author Nicolas de Marqué
 * @since 2011
 *
 */
class bnfPartWord extends bnfPart{
	/**
	 * The word
	 * @var string $id
	 */
	public $id;
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */	
	public $type = bnf::PART_WORD;
	/**
	 * Construct the object
	 * @param $id
	 * @param $bnfId
	 * @return bnfPartWord
	 */
	function __construct($id, $bnfId){
		$this->id = $id;
		$this->bnfId = $bnfId;
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
		$class="word";
		if(isset(bnfRule::$rules[$this->bnfId][$this->id])){
			$part = new bnfPartRule($this->id, $this->bnfId);
			return $part->table($level);				
		}
		return "<div class=\"word final\"><span>".htmlspecialchars($this->id)."</span></div>";
	}	

}
