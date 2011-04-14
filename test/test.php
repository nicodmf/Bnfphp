<?php
require_once "autoload.php";

$bnf = new BNF\bnf();
$bnf->parseRules(file_get_contents("json.bnf"));
echo $bnf->render("object")."\n ";
echo $bnf->render("array")."\n ";
echo $bnf->render("value")."\n ";
echo $bnf->render("number")."\n ";
echo $bnf->render("string")."\n ";
echo $bnf->render("not empty string")."\n ";

$rules = new BNF\bnf();
$rules->parseRules(file_get_contents("rules.bnf"));
echo  $rules->render("phrase", " ")."\n";