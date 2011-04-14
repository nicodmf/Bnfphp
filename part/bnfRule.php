<?php

namespace BNF;

/**
 * A bnf rule
 * 
 * Class bnfRule
 * 
 * 	A rule composited by parts
 * 	This class is used as start of
 * 	render, parse or graph.
 * 
 * This class can be serialize
 * 
 * BNF representation : \<rule\> 
 * 
 * 
 * \msc
 *       ~ (!) bnfRule
 *       ~ ==> bnfRule __construct()
 *  bnfRule (!) bnf
 *  bnfRule ==> bnf render()
 *  bnfRule <== bnf 0
 *       ~ <== bnfRule 0
 * \endmsc
 * @author Nicolas de Marqué Fromentin
 * @since 2011
 * 
 */
class bnfRule extends bnfPart{
	/**
	 * The id of this rule 
	 * @var string $id
	 */
	public $id;
	/**
	 * The bnf id
	 * @var string $bnfId
	 */
	private $bnfId;
	/**
	 * The bnf
	 * @var bnf $bnfId
	 */
	private $bnf;
	/**
	 * The rule in string form
	 * @var string $strRule
	 */
	public $strRule;
	/**
	 * The content of the rule
	 * @var array bnfPart $parts
	 */
	public $parts=array();
	/**
	 * The type in centralized string	 
	 * @var string $type
	 */
	public $type = bnf::RULE;
	/**
	 * All rules in memory, grouped by bnf_id then by rule
	 * 
	 * @code
	 * $rule = array(
	 * 	'bnf1' => array('rule1'=>bnfRule(), ... 'rulen'=>bnfRule()),
	 * 	...,
	 * 	'bnfn' => array('rule1'=>bnfRule(), ... 'rulen'=>bnfRule())
	 * )
	 * @endcode 
	 * @var bnfRule array $rules
	 */
	static $rules;
	
	/**
	 * The constructor initialize the rule
	 * add the rule in the static and begin
	 * to parsed
	 * @param[in] string $bnfId
	 * @param[in] string $id
	 * @param[in] string $strRule
	 * @return bnfRule
	 */
	function __construct($bnfId, $id, $strRule="")
	{
		//print_r($bnf);
		$this->id = $id;
		//$this->bnf = &$bnf;
		$this->bnfId = $bnfId;
		$this->strRule = $strRule;
		bnfRule::$rules[$this->bnfId][$this->id] = $this;
		//$this->parser = $bnf->parser;
		$this->parseParts();
	}
	/**
	 * Parse the parts
	 * @return void
	 */
	function parseParts()
	{
		$toParseStr = $this->strRule;
		while($toParseStr!==""){
			$this->parts[] = bnfParser::parseSubRule($toParseStr, $this->bnfId);
		}		
	}
	/**
	 * Integrate the rules in the static var saving all rules 
	 */
	function __wakeup()
	{
		self::$rules[$this->bnfId][$this->id] = $this;
	}
	/**
	 * Render the rule
	 * @return string
	 */
	function render()
	{
		$response = bnfSubParts::render($this->parts, $this->id);
		return $response;
	}
	/**
	 * Draw the graph
	 * @param integer $level
	 */
	function table($level){
		bnfSubParts::$changeToSet = false;
		return bnfSubParts::table($this, $level);
	}
}
