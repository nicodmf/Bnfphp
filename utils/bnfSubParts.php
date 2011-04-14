<?php

namespace BNF;

/**
 * Set of functions to represente rules
 * 
 * A set of functions used to render or represents
 * BNF rules 
 * 
 * @author Nicolas de Marqué Fromentin
 * @since 2011
 *
 */
class bnfSubParts{

	static $changeToSet = false;
	
	/**
	 * Render the parts of a rule
	 * @param array $parts
	 * @param string $name the rule's name 
	 * @return string
	 */

	static function render(&$parts, $name)
	{
		$response =  "";
		if(bnfSubParts::isOr($parts)){
			$response .= $parts[rand(0, count($parts)-1)]->render();
		}else{
			foreach($parts as $part){
				$response .= $part->render();
			}
		}
		return $response;
	}
	
	/* Functions use in graph */
	
	/**
	 * Test if a part is optional 
	 * @param array bnfPart $part
	 * @return boolean
	 */	
	static function isOr(&$parts)
	{
		foreach($parts as $part)
			if(isset($part->coordinator) and $part->coordinator=="or")
				return true;
		return false;
	}

	/**
	 * Compare Two bnfPart and define
	 * if they are identical
	 * @param bnfPart $part1
	 * @param bnfPart $part2
	 * @return boolean
	 */
	static function compare(bnfPart &$part1, bnfPart &$part2){
		if(isset($part1->id) && isset($part2->id) && $part1->id === $part2->id)
			return true;
		return false;
	}
	/**
	 * Define the css class dependant of
	 * the type of the part given
	 * @param bnfPart $part
	 * @return string
	 */
	static function defineClassPart(bnfPart &$part){
		switch($part->type){
			case bnf::PART_OPTION     : return "option";
			case bnf::RULE            : return $part->id." rule";
			case bnf::PART_SET        : return "set";
			case bnf::PART_REPETITION : return "repetition";
		}		
	}
	/**
	 * Define if a part has a row for title
	 * dependant of the $part type and the level
	 * @param bnfPart $part
	 * @param integer $level
	 * @return boolean
	 */
	function hasTitle(bnfPart &$part, &$level)
	{
		return
					 $part->type === bnf::PART_OPTION ||
					 $part->type === bnf::PART_REPETITION ||
					($part->type === bnf::PART_RULE && $level===0) ||
					 isset($part->specialRepetition);
	}
	/**
	 * Draw the table of a part given in parameter
	 * depending of the level
	 * @param bnfPart $origine
	 * @param integer $level
	 * @return string
	 */
	function table(bnfPart &$origine, &$level){

		//Specifics treatment if options are present in the first line		
		if($level===0 && bnfSubParts::isOr($origine->parts) && $origine->type === bnf::RULE && bnfSubParts::$changeToSet===false)
		{
			bnfSubParts::$changeToSet = true;
			$t = "";
			$clone = clone $origine;
			$set = new bnfPartSet($t, true);
			for($i=0; $i<count($clone->parts); $i++){
					$set->parts[$i] = $clone->parts[$i];
			}		
			unset($clone->parts);
			$clone->parts[0] = $set ;
			return bnfSubParts::tableAnd($clone, $level);
			
		}
		else
		{			
			bnfSubParts::$changeToSet = false;
			//echo $origine->type;
			if(bnfSubParts::isOr($origine->parts))
				return bnfSubParts::tableOr($origine, $level);
			return bnfSubParts::tableAnd($origine, $level);
			
		}
	}
	/**
	 * Draw the table of a part given in parameter
	 * depending of the level
	 * @param bnfPart $_origine
	 * @param integer $level
	 * @return string
	 */
	protected function tableOr(bnfPart &$_origine, &$level){

		$__origine = bnfSubParts::testAlwaysRepetition($_origine);
		$origine = bnfSubParts::testSuffixedRepetition($__origine);
		$classRepetitionForced = isset($origine->type_repetition) && $origine->type_repetition == bnf::PART_REPETITION_FORCED ? "forced" : "" ;
		$classRepetitionSuffixed = isset($origine->type_repetition) && $origine->type_repetition == bnf::PART_REPETITION_SUFFIXED ? "suffixed" : "" ;
		
		$test;

		$hasTitle = 	bnfSubParts::hasTitle($origine, $level);

		$nbParts = count($origine->parts);
		
		$classPart = bnfSubParts::defineClassPart($origine);
		$classAndOr = "or";
		$classLevel = $level===0 ? "level1" : "levelmore" ;
		$classWithTitle = $hasTitle ? "withtitle" :"notitle";
		$class = $classPart." ".$classAndOr." title ".$classLevel." ".$classWithTitle." ".$classRepetitionForced." ".$classRepetitionSuffixed;

		$id = isset($origine->id) ? $origine->id : "";

		$icon = "<div class=\"icon\"></div>";

		$placeCol = $level===0 && $origine->type===bnf::RULE ? "<td></td>" : "<td class=\"placeCols\" width=\"50%\"></td>";
		$simpleCol = "<td></td>";

		$width = $level===0 && $origine->type===bnf::RULE ? "" : "width=\"100%\"";
		
		$response  = "<table $width align=\"center\" class=\"$classPart\">";
		$response .= "<tr class=\"title $class\">";
		$response .= $placeCol;
		$response .= "<td class=\"title left $class\">$icon</td>";
		$response .= "<td class=\"title content $class\">".$id."</td>";
		$response .= "<td class=\"title right $class\">$icon</td>";
		$response .= $placeCol;
		$response .= "</tr>";
		
		for($i=0; $i<$nbParts; $i++){

			$first = $i === 0;
			$last  = $i === $nbParts -1;
			$part = $origine->parts[$i];

			$classFirst = $first ? "first" : "notfirst" ;
			$classLast  = $last  ? "last"  : "notlast"  ;
			
			$class = $classPart." ".$classLevel." ".$classLast." ".$classWithTitle." ".$classFirst." ".$classAndOr." ".$classRepetitionForced." ".$classRepetitionSuffixed;
			
			$response .= "<tr>";
			$response .= $first && !$hasTitle ? $placeCol : $simpleCol;
			$response .= "<td class=\"left link $class\">$icon</td>";
			$response .= "<td class=\"content $class\">".$part->table($level)."</td>";
			$response .= "<td class=\"right link $class\">$icon</td>";
			$response .= $first && !$hasTitle ? $placeCol : $simpleCol;
			$response .= "</tr>";
		}
		$response .= "</table>";
		return $response;
	}
	
	protected function testAlwaysRepetition(bnfPart &$origine){
		
		$test = false;

		for($i=0; $i<count($origine->parts); $i++){

			if($origine->parts[$i]->type === bnf::PART_REPETITION)
			{				
				if($i!==0){
					if($origine->parts[$i]->parts[0]->id === $origine->parts[$i-1]->id)
					{
						if(count($origine->parts[$i]->parts)===1)
						{
							$new = clone $origine;
							$new->parts[$i]->type_repetition = bnf::PART_REPETITION_FORCED;
							$new->parts[$i-1] = $new->parts[$i];
							unset ( $new->parts[$i] );
							$j=0;
							foreach($new->parts as $part){
								$new->parts[$j] = $part;
								$j++;
							}
							return $new;
						}
					}
				}							
			}			
		}
		return $origine;	
	}
	protected function testSuffixedRepetition(bnfPart &$origine){
		if($origine->type !== bnf::PART_OPTION) return $origine;
		if(count($origine->parts)!==2) return $origine;
		if($origine->parts[1]->type !== bnf::PART_REPETITION) return $origine;
		if(count($origine->parts[1]->parts)!==2) return $origine;
		if($origine->parts[1]->parts[1]->id !== $origine->parts[0]->id) return $origine;
		$new = clone $origine->parts[1];
		$new->type_repetition = bnf::PART_REPETITION_SUFFIXED;
		$new->suffixe = $new->parts[0];
		$new->parts[0] = $new->parts[1];
		unset ( $new->parts[1] );
		return $new;
	}
	/**
	 * 
	 * Draw the table of a part given in parameter
	 * depending of the level
	 * @param bnfPart $_origine
	 * @param integer $level
	 * @return string
	 */	
	protected function tableAnd(bnfPart &$_origine, $level){
		
		$__origine = bnfSubParts::testAlwaysRepetition($_origine);
		$origine = bnfSubParts::testSuffixedRepetition($__origine);

		$nbParts = count($origine->parts);

		$hasTitle = 	bnfSubParts::hasTitle($origine, $level);
		
		$placeCol = $level===0 && ( $origine->type===bnf::RULE || $origine->type===bnf::PART_SET ) ? "<td></td>" : "<td class=\"placeCols\" width=\"50%\"></td>";
		$simpleCol = "<td></td>";

		$classPart = bnfSubParts::defineClassPart($origine);
		$classAndOr = "and";
		$classRepetitionForced = isset($origine->type_repetition) && $origine->type_repetition == bnf::PART_REPETITION_FORCED ? "forced" : "" ;
		$classRepetitionSuffixed = isset($origine->type_repetition) && $origine->type_repetition == bnf::PART_REPETITION_SUFFIXED ? "suffixed" : "" ;
		$classLevel = $level===0 ? "level1" : "levelmore" ;
		$classWithTitle = $hasTitle ? "withtitle" :"notitle";
		$class = $classPart." ".$classLevel." ".$classWithTitle." ".$classAndOr." ".$classRepetitionForced." ".$classRepetitionSuffixed;
		
		$id = isset($origine->id) ? $origine->id : "";

		$icon = "<div class=\"icon\"></div>";

		$colspan = $nbParts * 2 - 1 ;

		$width = $level===0 && $origine->type===bnf::RULE ? "" : "width=\"100%\"";
		
		$response = "<table $width align=\"center\" class=\"$classPart\">";
		$response .= "<tr class=\"title $class\">";
		$response .= $placeCol;
		$response .= "<td class=\"title left $class\">$icon</td>";
		$response .= "<td class=\"title content $class\" colspan=\"$colspan\">".$id."</td>";
		$response .= "<td class=\"title right $class\">$icon</td>";
		$response .= $placeCol;
		$response .= "</tr>";
		$response .= "<tr>";
		$response .= $simpleCol;
		$response .= "<td class=\"left link first last $class\">$icon</td>";
		
		for($i=0; $i<$nbParts; $i++){

			$first = $i === 0;
			$last  = $i === $nbParts -1;
			$part = $origine->parts[$i];

			$classFirst = $first ? "first" : "notfirst" ;
			$classLast  = $last  ? "last"  : "notlast" ;
			$class = $classPart." ".$classLevel." ".$classFirst." ".$classLast." ".$classWithTitle." ".$classAndOr." ".$classRepetitionForced." ".$classRepetitionSuffixed;

			$response .= "<td class=\"content\">".$part->table($level)."</td>";
			if(!$last)
			$response .= "<td class=\"middle link $class\">$icon</td>";
		}
		
		$response .= "<td class=\"right link first last $class\">$icon</td>";
		$response .= $simpleCol;
		$response .= "</tr>";
		
		if(isset($origine->suffixe)){
			$class = $classPart." ".$classLevel." last notfirst suffixe ".$classWithTitle." ".$classAndOr;

			$response .= "<tr>";
			$response .= $first && !$hasTitle ? $placeCol : $simpleCol;
			$response .= "<td class=\"left link first last $class\">$icon</td>";
		

			$response .= "<td class=\"content\">".$origine->suffixe->table($level)."</td>";
			
			$response .= "<td class=\"right link first last $class\">$icon</td>";
			$response .= $first && !$hasTitle ? $placeCol : $simpleCol;
			$response .= "</tr>";
		}

		$response .= "</table>";
		return $response;
		
	}

	
}
