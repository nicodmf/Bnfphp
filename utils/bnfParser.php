<?php

namespace BNF;

/**
 * Set of functions to parse rules
 * 
 * @author Nicolas de Marqué Fromentin
 * @since 2011
 *
 */
class bnfParser{
	
	/**
	 * The bnf system
	 * @var bnf $bnf
	 */
	public $bnf;
	
	/**
	 * Construct the parser
	 * @param bnf $bnf
	 * @return bnfParser
	 */
	public function __construct(bnf $bnf) 
	{
		$this->bnf = $bnf;
	}
	/**
	 * Recognize the type of the rule
	 * @param string $strPart
	 * @return string Identifiing describe the type
	 */
	public function parseType(&$strPart){
		$strPart = trim($strPart);		
		switch($strPart[0]){
			case self::getTypeClosure(bnf::PART_OPTION):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_OPTION;
			case self::getTypeClosure(bnf::PART_RULE):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_RULE;
			case self::getTypeClosure(bnf::PART_SET):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_SET;
			case self::getTypeClosure(bnf::PART_WORD_QUOTED):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_WORD_QUOTED;
			case self::getTypeClosure(bnf::PART_WORD_SIMPLE_QUOTED):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_WORD_SIMPLE_QUOTED;
			case self::getTypeClosure(bnf::PART_REPETITION):
				$strPart = trim(substr($strPart,1));
				return bnf::PART_REPETITION;
			default :	return bnf::PART_WORD;
		}
	}
	/**
	 * The factory function who create a part
	 * with a string give in parameter and return
	 * the exact object define in the string
	 * @param string $strPart
	 * @param string $ruleId
	 * @return bnfPart 
	 */
	static function parseSubRule(&$strPart, $bnfId)
	{
		$type = self::parseType($strPart);
		$part = bnfPart::factory($type, "", $bnfId);
		self::parseRule($part, $strPart, $bnfId);
		return $part;		
	}
	/**
	 * Parse the $rules
	 * @param string $bnfRules
	 * @return array $rules
	 */
	public function parseRules($bnf_id, $bnfRules)
	{
		$bnfRules = self::removeComments ($bnfRules);
		$rules = array();
		
		$splitStrRules = preg_split("/\n</", $bnfRules);
		foreach($splitStrRules as $rule){
			$cutRule = preg_split("/::=/", $rule);
			$id =  trim(preg_replace(array('/\</', '/\>/'), '', $cutRule[0]));
			$_rule = trim($cutRule[1]);
			$rules[] = new bnfRule($bnf_id, $id, $_rule);
		}
		return $rules;
	}
		
	/**
	 * Parse a string in sub parts
	 * @param string $part
	 * @param string $strPart
	 * @param string $ruleId the rule name
	 * @param string $bnfId the name of bnf declared
	 */
	function parseRule(&$part, &$strPart, $bnfId){
		$strPart = trim($strPart);
		$notParsedStrPart = $strPart;
		$type = $part->type;
		
		$end = self::findEnd(
			$notParsedStrPart, 
			self::getTypeClosure($part->type),
			self::getTypeClosure($part->type, false)
		);
		
		if($type == bnf::PART_OPTION or $type == bnf::PART_SET or $type == bnf::PART_REPETITION)
		{
			$part->parts = array();
			$toParseStr = substr($notParsedStrPart, 0, $end);
			while($toParseStr != "")
			{
				$part->parts[] = self::parseSubRule($toParseStr, $bnfId);
			}
		}else{
			$part->id = substr($notParsedStrPart, 0, $end);
		}		
		$notParsedStrPart = trim(substr($notParsedStrPart, $end+1));
		
		if(isset($notParsedStrPart[0]) && $notParsedStrPart[0]==="|")
		{
			$part->coordinator = "or";
			$notParsedStrPart = trim(substr($notParsedStrPart, 1));			
		}		
		$strPart = trim($notParsedStrPart);
				
	}
	/**
	 * Find the position of the end of the
	 * subpart with the closure define in
	 * the getClosures function
	 * @param string $str
	 * @param char $open
	 * @param char $end
	 */
	protected function findEnd($str, $open, $end){
		$opens = 1;
		$ends = 0;
		for($i=0; $i<strlen($str); $i++){
			if($str[$i]===$end )$ends++;
			if($opens==$ends)
				break;
			if($str[$i]===$open)$opens++;
		}
		return $i;
	}
	/**
	 * Return coordinators
	 * @return array
	 */
	protected function getCordinators(){
		return array("", "|");
	}
	/**
	 * Return closures
	 * @return array
	 */
	private static function getClosures(){
		return array(
			bnf::PART_WORD  => array("" , " "),
			bnf::PART_WORD_QUOTED  => array("\"" , "\""),
			bnf::PART_WORD_SIMPLE_QUOTED  => array("'" , "'"),
			bnf::PART_REPETITION  => array("{" , "}"),
			bnf::PART_RULE  => array("<", ">"),
			bnf::PART_OPTION=> array("[", "]"),
			bnf::PART_SET=> array("(", ")"),
		);
	}
	/**
	 * Get the closure dependant of the type given
	 * @param string $type
	 * @param boolean $first
	 * @return string
	 */
	private static function getTypeClosure($type, $first=true){
		$types=self::getClosures();
		if($first)
			return $types[$type][0];
		return $types[$type][1];		
	}
		/**
	 * Remove comments from given strings
	 * The comments are defined as line begin by #
	 * @param string $str
	 */	
	protected function removeComments($str){
		$res = array();
		foreach(preg_split("/\n/", $str) as $line){
			if(isset($line[0]) && $line[0]!=="#")
				$res[] = $line;
		}
		return implode("\n",$res);
	}

}
