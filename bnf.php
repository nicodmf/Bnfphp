<?php

/**
 * Parse and display BNF rules
 *
 * @author nicolas
 * @since 5 avr. 2011
 */
namespace BNF;

/**
 * The central class
 * 
 * The BNF class parse, construct and represente BNF rules.
 * The BNF (Backus Normal Form or Backus–Naur Form) is a notation
 * technique for context-free grammars.
 * 
 * Represention
 * 
 * @author Nicolas de Marqué Fromentin
 * @since 2011
 * @version 1.0
 *
 */
class bnf{
	
	const PART_WORD                = "__word__";
	const PART_WORD_QUOTED         = "__word_quoted__";
	const PART_WORD_SIMPLE_QUOTED  = "__word_simple_quoted__";
	const PART_REPETITION          = "__repetition__";
	const PART_REPETITION_FORCED   = "__repetition_forced__";
	const PART_REPETITION_SUFFIXED = "__repetition_suffixed__";
	const PART_RULE                = "__rule__";
	const RULE                     = "__is_rule__";
	const PART_OPTION              = "__option__";
	const PART_SET                 = "__set__";
	/**
	 * The parser
	 * @var bnfParser $parser
	 */
	public $parser;
	/**
	 * The identificate string of the bnf instance.
	 * @var string $id
	 */
	public $id;
	/**
	 * The rules of the instances in memory
	 * @var array $rules
	 */
	protected $rules=array();
	/**
	 * The max level for display sub rules.
	 * Saved in static form to simplificate
	 * use in other class
	 * @var integer $maxLevel; 
	 */
	public static $maxLevel;
	/**
	 * The separator use beetwen string in render process.
	 * The static form is to simplificate use in other class.
	 * @var string $separator
	 */
	public static $separator="";
	
	/**
	 * Construct the bnf object
	 * An optionnel string can be add to be parsed
	 * as rules
	 * @param string $id
	 * @param string $rules
	 * @return bnf
	 */
	public function __construct($id="bnf", $rules="")
	{
		$this->id = $id;
		//$this->parser = new bnfParser($this);
		if($rules!=="")
			$this->parseRules($rules);
		
	}
	/**
	 * Parse the $rules
	 * @param string $bnfRules
	 */
	public function parseRules($bnfRules)
	{
		$this->rules = bnfParser::parseRules($this->id, $bnfRules);
	}
	/**
	 * Add a rule in this bnf system
	 * @param bnfRule $rule
	 */
	public function addRule(bnfRule $rule){
		$this->rules[] = $rule;		
	}
	/**
	 * With saved rules generate a well formed string
	 * of the rule givedn in parameter 
	 * @param string $rule : the rule name
	 * @param string $separator : the separator use beetween part
	 * @return string 
	 */
	public function render($rule, $separator="")
	{
		bnf::$separator = $separator;
		return bnfRule::$rules[$this->id][$rule]->render();
	}
	/**
	 * An alias from table
	 * 
	 * @param string $rule
	 * @param integer $level
	 * @return html : an html table html4.0 transitional compliant
	 * 
	 */
	public function graph($rule, $level)
	{
		return $this->table($rule, $level);
	}
	/**
	 * Create an html table representing the rule given by 
	 * the first parameter, and limit the representation by 
	 * the levels of the  
	 *
	 * @param string $rule
	 * @param integer $level
	 * @return html : an html table html4.0 transitional compliant
	 * 
	 */
	public function table($rule, $level)
	{
		bnf::$maxLevel = $level;
		return bnfRule::$rules[$this->id][$rule]->table(0);
	}
	/**
	 * Return css file positioning by the given
	 * relative path to the lib directory
	 * @param string $htmlRelativeDir
	 */
	public function getCss($htmlRelativeDir=".")
	{
		return  $htmlRelativeDir."/assets/table.style.css";
	}
	/**
	 * Return css link tag positioning by the given
	 * relative path to the lib directory
	 * @param string $htmlRelativeDir
	 */
	public function getCssLink($htmlRelativeDir="."){
		return  "<link type=\"text/css\" media=\"all\" rel=\"stylesheet\" href=\"".$this->getCss($htmlRelativeDir)."\">";
	}
	/**
	 * Return all rules
	 * @return array of rules
	 */
	public function getRules(){
		return $this->rules;		
	}

}
