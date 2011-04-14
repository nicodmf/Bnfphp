<?php
namespace BNF;
/**
 * Usefull to prepare non pure bnf syntax
 *
 * @author nicolas
 * @version 1
 * @since 13 avr. 2011
 */
class bnfPrepareSpecialSyntax{
	/**
	 * Take a filename, load the file and return its content
	 * @param string $file
	 * @return string
	 */
	function loadFile($file){
		return file_get_contents($file);
	}
	/**
	 * Remove carriage return of a string
	 * @param string $str
	 * @return string
	 */
	function stripLines($str){
		$r = "";
		foreach(preg_split("/(\r\n)|(\r)|(\n)/", $str) as $s)
			$r.=trim($s)." ";
		return trim($r);		
	}
	/**
	 * Usefull for ecma, replace the on of sequences
	 * @param array $args, only $args[1] is used
	 * @return string
	 */
	static function replaceOneOf($args){
		return "::= ".preg_replace("/ /"," | ",self::stripLines($args[1]))."\n\n#";
	}
	/**
	 * Usefull for ecma, transform lines in options of sets 
	 * @param array $args, only $args[1] is used
	 * @return string
	 */
	static function setMultiLinesOptions($args){
		$r="";
		$splits = preg_split("/(\r\n)|(\r)|(\n)/", $args[1]);
		//print_r($splits);
		foreach($splits as $s)
			if($s!="")
			$r.=preg_replace("/([ ]{8})(.*)/", "$1( $2 )\t|\n", $s);
			else $r=substr($r,0,strlen($r)-3);
		return "::=\n".$r."\n\n#";
		
	}
	/**
	 * Usefull for ecma, transform a file in bnf string 
	 * @param string $file the filename
	 * @return string
	 */
	static function ecma($file){
		$str = self::loadFile($file);
		//protect quotes
		$str = preg_replace('/ "( |\n)/', " '\"'$1", $str);
		$str = preg_replace("/ '( |\n)/", ' "\'"$1', $str);
		$str = preg_replace('/“|”/', "\"", $str);
		//Change special part
		$str = preg_replace('/\[([^ \n][^\]]*)\]/', "'$1'", $str);
		//Change special part
		//$str = preg_replace('/\[lookahead[^\]]*\]/', "'$1'\"", $str);
		//Protect bnf specials chars
		$str = preg_replace('/((\(|\)|\[|\]|<|>|\{|\}|\|)[^ \n]*)/', "\"$1\"", $str);
		//Replace one of by alternatives
		$str = preg_replace_callback('/::= one of\n([^#]*?)\n#/s', "self::replaceOneOf", $str);
		//Replace lines by set and multilines by alternatives
		$str = preg_replace_callback('/::=\n([^#]*?)\n#/s', "self::setMultiLinesOptions", $str);
		//Replace opt
		$str = preg_replace('/ ([^ ]*?)opt/s', " [ $1 ]", $str);
		//Replace rules
		$str = preg_replace('/\n([^ ]*) ::=/', "\n<$1> ::=", $str);
		//Replace but not
		$str = preg_replace('/(but not[^\)]*)/', "'$1'", $str);
		return $str; 
	}
}