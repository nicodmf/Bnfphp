<?php
require_once "autoload.php";
if(!isset($_SESSION['bnf']) or $_GET['reset']){
	$bnf = new BNF\bnf();
	$syntax = BNF\bnfPrepareSpecialSyntax::ecma("ECMA_262_simple.bnf");
	$bnf->parseRules($syntax);
	$_SESSION['bnf'] = serialize($bnf);
	$_SESSION['syntax'] = $syntax;
}else{
	$bnf = unserialize($_SESSION['bnf']);
	$syntax = $_SESSION['syntax'];
}

$file = fopen("ECMA_262_simple.bnf", "r");
$titles = array();
$sees = array();
$rule="";
while($line = fgets($file)){
	if($line[0]=="#"){
		if($line[1]=="#"){
			if(isset($sees[count($sees)-1]))$sees[count($sees)-1]['strrule']=htmlspecialchars($rule);
			$rule="";
			$titles[count($titles)-1]['see']++;
			$sees[] = array(
				'value'=>"See section ".trim(substr($line,6)),
				"title"=>$titles[count($titles)-1]['title'],
				"subtitle"=>isset($titles[count($titles)-1]['subtitle'])?$titles[count($titles)-1]['subtitle']:""
			);
		}else{
			if((isset($line[4]) and $line[4]!=".") or count($titles)==0){
				$titles[] = array();
				$titles[count($titles)-1]['see']=0;
			}
			if(isset($titles[count($titles)-1]['title']))
				$titles[count($titles)-1]['subtitle'] = trim(substr($line,3));
			else
				$titles[count($titles)-1]['title'] = trim(substr($line,3));
		}
	}elseif($rule!="\n"){
		$rule.=$line;
	}	
}
//print_r($titles);

//print_r($bnf);
$rule = isset($_POST['rule']) ?$_POST['rule']:"";
$level= isset($_POST['level'])?$_POST['level']:1;

$graph = $rule!=="" ? $bnf->table($rule, $level) : "";

$graphs = "";

$select = "\n<select name=\"rule\">";
$dr=null;
$i=0; $t=""; $s=$cinfos="";$f=true;$rf=true;
foreach($bnf->getRules() as $r)
{
	//print_r($r->id);
	$firstfieldset = $rf===true?"": "</fieldset>";
	$selected = $rule == $r->id ? "  selected=\"selected\"" : "";
	$fo=$f==true?"":"</optgroup>\n\t\t\t\t";$f=false;
	$optiongroup = $sees[$i]['title']==$t ? "" : "$fo<optgroup label=\"".$sees[$i]['title']."\">";
	$dr = $rule == $r->id?$r:$dr;
	$select .= "$optiongroup<option $selected>".$r->id."</option>";
	$ft = $sees[$i]['title']==$t ? " class=\"notfirst\"" : " class=\"first\"";
	$fieldset = $sees[$i]['title']==$t ? "" : " $firstfieldset<fieldset class=\"box\"><legend>".$sees[$i]['title']."</legend>";
	//echo $fieldset."\n";
	$fst= $sees[$i]['subtitle']==$s ? " class=\"notfirst\"" : " class=\"first\"";
	$rf = $sees[$i]['value'];
	$t = $sees[$i]['title'];
	$s = $sees[$i]['subtitle'];
	$st= $sees[$i]['strrule'];
	$rst = $r->strRule;
	$title = "<h1$ft>$t</h1>"; 
	$subtitle = "<h2$fst>$s</h2>"; 
	$strrule = "<pre>$st</pre>";
	$rstrrule = "<pre>$rst</pre>";
	$ref = "<div class=\"ref\">$rf</div>";
	$infos = "<div class=\"infos\">$title$subtitle$ref$strrule$rstrrule</div>";
	$cinfos = $rule == $r->id?$infos:$cinfos;
	$graphs .= "\t\t\t$fieldset<div class=\"tabled_rule ".$r->id."\">$infos\n\t\t\t\t".$bnf->table($r->id, 1)."\n\t\t\t</div>\n";
	$i++;
}
$graphs .= "</fieldset>\n";
$select .= "</optgroup></select>\n";
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<?php echo $bnf->getCssLink("..")?>
	<title>Repr&eacute;sentation JSON par chargement de règle bnf</title>
	<script type="text/javascript" src="../assets/mootools-yui-compressed.js"></script>
	<script type="text/javascript">
	function open(evt)
	{
		e = $(evt.target);
		name = e.get('title');
		element = $$("div.tabled_rules>div.tabled_rule>table."+name+">tbody>tr>td>table")[0];
		div = e.parentNode.parentNode;
		links = e.parentNode;
		links.removeClass("close");
		links.addClass("open");
		div.getElement("span").destroy();
		clone = element.clone();
		clone.inject(div, 'after');			
		addLinks(clone);
	}
	function close(evt){
		e = $(evt.target);
		name = e.get('title');
		td = e.parentNode.parentNode.parentNode;
		div = e.parentNode.parentNode;
		links = e.parentNode;
		links.removeClass("open");
		links.addClass("close");
		span=new Element("span").set("text", name);
		td.getElement("table").destroy();
		span.inject(div);			
		
	}
	function addLinks(base)
	{
		//console.log(base);
		base.getElements(".final.rule").each(function(e){
			name = e.getElement("span").get("text");
			div = new Element("div").addClass("links close");
			aouvr = new Element("a").addClass('open').set("text", "+").set("title", name);
			aferm = new Element("a").addClass('close').set("text", "-").set("title", name);
			div.inject(e);
			aouvr.inject(div);
			aferm.inject(div);
			aouvr.addEvent('click', open);
			aferm.addEvent('click', close);				
		})			
	}
	function changeRule(e)
	{
		$('rule').getElements("*").each(function(e){
			e.destroy();			
		});
		name = $(e).value;
		//console.log(name)
		element = $$("div.tabled_rules>div.tabled_rule>table."+name)[0];
		clone = element.clone();
		clone.inject($('rule'));
		addLinks(clone);				
	}
	window.addEvent('domready',	function (){addLinks($('rule'));} );
	</script>
	<style>
		.rule_in_study,
		.tabled_rules{border:1px solid black; border-radius:10px; margin-bottom:20px; padding-bottom:20px;}
		fieldset{border-radius:10px;}
		div.links{position:absolute; right:50%; margin-right:-5px;margin-top: -6px; width:10px;height:10px;display:block; font-weight:bold;border-radius:5px; background:black; text-align:center;}
		div.links.open{right:0px; margin-top: -6px;}
		div.links a {color:white;}
		a.open,a.close{cursor:pointer;}
		div.links.close a.close{display:none;}
		div.links.open a.open{display:none;}
		div.final{position:relative;}
		h1.notfirst,h2.notfirst{display:none;}
		div#infos h1.notfirst,
		div#infos h2.notfirst{display:block;}
	</style>
</head>
<body>
	<form method="post">
		<fieldset>
			<legend>Choisissez votre règle ainsi que le niveau de détail</legend>
			<label>Rule<?php echo $select?></label>
			<label>Level<input name="level" value="<?php echo $level?>"/></label>
			<input type="submit" />
		</fieldset>
	</form>
	<div id="infos"><?php echo $cinfos;?></div>
	<div id="rule" class="rule_in_study">
		<?php echo $graph?>
	</div>
	<div class="tabled_rules">
<?php echo $graphs?>
	</div>
</body>

</html>
