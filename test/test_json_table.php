<?php
require_once "autoload.php";
if(!isset($_SESSION['bnf']) or $_GET['reset']){
	$bnf = new BNF\bnf();
	$bnf->parseRules(file_get_contents("json.bnf"));
	$_SESSION['bnf'] = serialize($bnf);
}else{
	$bnf = unserialize($_SESSION['bnf']);
}


$graph = $bnf->table("object", 2);
$graph.= $bnf->table("array", 1);
$graph.= $bnf->table("value", 1);

$graph.= $bnf->table("number", 1);
$graph.= $bnf->table("string", 1);
$graph.= $bnf->table("not empty string", 1);
$graph.= $bnf->table("char content string", 1);

?><!DOCTYPE html><html>
<head>
<meta charset="UTF-8">
<?php echo $bnf->getCssLink("..")?>
<title>Repr&eacute;sentation JSON par chargement de règle bnf</title>
</head>
<body><pre>
<?php foreach(array("object", "array", "value", "number", "string", "not empty string") as $rule)
	echo "a(n) $rule : ".$bnf->render($rule)."\n"?>
</pre>
<div class="graph">
<?php echo $graph?>
</div>
</body>

</html>
