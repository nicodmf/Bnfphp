<?php
function __autoload($name){
	$baseDir = "../";
	$name = strrpos($name,"\\")!==false?substr($name,strrpos($name,"\\")+1):$name; 
	$iname = strtolower($name);
	if(file_exists($baseDir.$iname.".php")){
		require_once($baseDir.$iname.".php");
		return true;
	}elseif(file_exists($baseDir.$name.".php")){
		require_once($baseDir.$name.".php");
		return true;	
	}else{
		$res=glob($baseDir."*".$name.".php");
		if(isset($res[0])){
			require_once($res[0]);
			return true;
		}else{
			$res=glob($baseDir."*".$iname.".php");
			if(isset($res[0])){
				require_once($res[0]);
				return true;
			}else{
				$res=glob($baseDir."./*/".$name.".php");
				if(isset($res[0])){
					require_once($res[0]);
					return true;
				}else{
					$res=glob($baseDir."./*/".$iname.".php");
					if(isset($res[0])){
						require_once($res[0]);
						return true;
					}
				}
			}
		}
	}
}
