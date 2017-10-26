<?php

/**
* RepTag
* Replacement-Tag : mynt-format
* ex) <**:**>
*/

class MYNT_TAG{

	public static $tag1 = "<<";
	public static $tag2 = ">>";

	public static function viewFile($path){
		$html = "";
		if(is_file($path)){
			$html = self::rep(file_get_contents($path));
		}
		return $html;
	}

	public static function rep($source=""){
    if($source===""){return;}
    return self::pattern($source);
  }

	public static function pattern($source){

		$source = self::pattern1($source);
		$source = self::pattern2($source); //function , class , proc
		$source = self::pattern_method($source);
		$source = self::pattern3($source);
		// $source = self::pattern_variable($source);
		// $source = self::pattern_echo($source);
		$source = self::pattern_if($source);
		$source = self::pattern_for($source);
		return $source;
	}

	public static function pattern1($source){

		$keys    = array("post","get","request","globals","define","session","server");
		$ptn = self::$tag1.'('.join('|',$keys).')\:(.+?)'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(!count($match[1])){
			return $source;
		}

		for($i=0, $c=count($match[1]); $i<$c; $i++){
			if($match[0][$i]===""){continue;}
			$res = self::getValue($match[1][$i],$match[2][$i]);
			$source = str_replace($match[0][$i],$res,$source);
		}

		return $source;
	}
	public static function pattern2($source){

		$keys = array("class","function","proc");
		$ptn = self::$tag1.'('.join('|',$keys).')\:(.+?)\((.*?)\)'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(count($match[1])){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$res = self::getProcs($match[1][$i],$match[2][$i],$match[3][$i]);
				$source = str_replace($match[0][$i],$res,$source);
			}
		}
		return $source;
	}
	public static function pattern_method($source){
		$ptn = self::$tag1.'METHOD\:(.+?)\:\:(.+?)\((.*?)\)'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(count($match[1])){
			for($i=0, $c=count($match[1]); $i<$c; $i++){
				if($match[0][$i]===""){continue;}
				$res = self::getMethod($match[1][$i],$match[2][$i],$match[3][$i]);
				$source = str_replace($match[0][$i],$res,$source);
			}
		}
		return $source;
	}
	public static function pattern3($source){

		$keys = array("eval","file");
		$ptn = self::$tag1.'('.join('|',$keys).')\:\"(.+?)\"'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(!count($match[1])){
			return $source;
		}
		// print_r($match);

		for($i=0, $c=count($match[1]); $i<$c; $i++){
			if($match[0][$i]===""){continue;}
			$res = self::getCodes($match[1][$i],$match[2][$i]);
			$source = str_replace($match[0][$i],$res,$source);
		}

		return $source;
	}

	public static function pattern_if($source){

		$ptn = self::$tag1.'if\:(.+?)'.self::$tag2.'(.+?)'.self::$tag1.'\/if'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(!count($match[1])){
			return $source;
		}

		for($i=0, $c=count($match[1]); $i<$c; $i++){
			if($match[0][$i]===""){continue;}

			// else-check
			$val_else = "";
			$val_then = $match[2][$i];

			$ptn_else = '(.*)'.self::$tag1.'else'.self::$tag2.'(.*)';
			preg_match_all("/".$ptn_else."/is" , $val_then , $match_else);

			if(count($match_else[0])){
				$val_else = $match_else[2][0];
				$val_then = $match_else[1][0];
			}

			// if-else
			if(!preg_match("/".self::$tag1."elif\(.+?\)".self::$tag2."/",$val_then)){
				$ptn = $match[1][$i];
				$val_then = str_replace("'","\'",$val_then);
				$evalStr = "if(".$match[1][$i]."){return '".$val_then."';}";
				if($val_else !== ""){
					$val_else = str_replace("'","\'",$val_else);
					$evalStr .= "else{return '".$val_else."';}";
				}
				$res = eval($evalStr);
			}

			// if-elseif-else
			else{
				$ptn2 = self::$tag1.'elif\:(.+?)'.self::$tag2;
				$str = $val_then;
				$str = str_replace("\n","",$str);
				$str = str_replace("\r","",$str);
				preg_match_all("/".$ptn2."/is" , $str  , $elifs);

				$elif = "";
				for($j=0; $j<count($elifs[0]); $j++){
					$elif .= self::$tag1."elif\:\(.+?\)".self::$tag2."(.+?)";
				}
				$ptn3 = self::$tag1.'if\:\(.+?\)'.self::$tag2.'(.+?)'.$elif.self::$tag1.'else'.self::$tag2.'.+?'.self::$tag1.'\/if'.self::$tag2;
				preg_match_all("/".$ptn3."/is" , $match[0][$i]  , $elifs2);

				$evalStr = "if(".$match[1][$i]."){return '".$elifs2[1][0]."';}";
				for($j=2; $j<count($elifs2); $j++){
					$evalStr .= "elseif(".$elifs[1][$j-2]."){return '".$elifs2[$j][0]."';}";
				}
				$evalStr .= "else{return '".$val_else."';}";

				$res = eval($evalStr);
			}

			$source = str_replace($match[0][$i],$res,$source);
		}
		return $source;
	}

	public static function pattern_if_bak($source){

		//
		// $ptn = '<if\((.+?)\)>(.+?)<else>(.+?)<if\-end>';
		$ptn = self::$tag1.'if\:(.+?)'.self::$tag2.'(.+?)'.self::$tag1.'\/if'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(!count($match[1])){
			return $source;
		}

		for($i=0, $c=count($match[1]); $i<$c; $i++){
			if($match[0][$i]===""){continue;}

			// else-check
			$val_else = "";
			$val_then = $match[2][$i];

			$ptn_else = '(.*)'.self::$tag1.'else'.self::$tag2.'(.*)';
			preg_match_all("/".$ptn_else."/is" , $val_then , $match_else);

			if(count($match_else[0])){
				$val_else = $match_else[2][0];
				$val_then = $match_else[1][0];
			}

			// if-else
			if(!preg_match("/".self::$tag1."elif\(.+?\)".self::$tag2."/",$val_then)){
				$evalStr = "if(".$match[1][$i]."){return '".$val_then."';}";
				if($val_else !== ""){
					$evalStr .= "else{return '".$val_else."';}";
				}
				$res = eval($evalStr);
			}

			// if-elseif-else
			else{
				$ptn2 = self::$tag1.'elif\:(.+?)'.self::$tag2;
				$str = $val_then;
				$str = str_replace("\n","",$str);
				$str = str_replace("\r","",$str);
				preg_match_all("/".$ptn2."/is" , $str  , $elifs);

				$elif = "";
				for($j=0; $j<count($elifs[0]); $j++){
					$elif .= self::$tag1."elif\:\(.+?\)".self::$tag2."(.+?)";
				}
				$ptn3 = self::$tag1.'if\:\(.+?\)'.self::$tag2.'(.+?)'.$elif.self::$tag1.'else'.self::$tag2.'.+?'.self::$tag1.'\/if'.self::$tag2;
				preg_match_all("/".$ptn3."/is" , $match[0][$i]  , $elifs2);

				$evalStr = "if(".$match[1][$i]."){return '".$elifs2[1][0]."';}";
				for($j=2; $j<count($elifs2); $j++){
					$evalStr .= "elseif(".$elifs[1][$j-2]."){return '".$elifs2[$j][0]."';}";
				}
				$evalStr .= "else{return '".$val_else."';}";

				$res = eval($evalStr);
			}

			$source = str_replace($match[0][$i],$res,$source);
		}
		return $source;
	}

	public static function pattern_for($source){
		$ptn = self::$tag1.'for\((.*?)\.\.(.*?)\)'.self::$tag2.'(.+?)'.self::$tag1.'\/for'.self::$tag2;
		preg_match_all("/".$ptn."/is" , $source  , $match);

		if(!count($match[1])){
			return $source;
		}

		for($i=0, $c=count($match[1]); $i<$c; $i++){
			if($match[0][$i]===""){continue;}
			$str = $match[3][$i];
			$str = str_replace('"','\"',$str);
			$str = str_replace("\n",'',$str);
			$match[2][$i] = ($match[2][$i] === "")?$match[1][$i]:$match[2][$i];
			$evalStr = '$s=""; for($j='.$match[1][$i].'; $j<='.$match[2][$i].'; $j++){$s.= str_replace("##",$j,"'.$str.'");} return $s;';
			$res = eval($evalStr);
			$source = str_replace($match[0][$i],$res,$source);
		}
		return $source;
	}

	public static function getValue($key,$val){
		$res = "";
		$key = strtoupper($key);
		switch($key){
			case "POST":
				$res = self::getArrayValue($_POST,$val);
				break;
			case "GET":
				$res = self::getArrayValue($_GET,$val);
				break;
			case "REQUEST":
				$res = self::getArrayValue($_REQUEST,$val);
				break;
			case "GLOBALS":
				$res = self::getArrayValue($GLOBALS,$val);
				break;
			case "DEFINE":
				$res = constant($val);
				break;
			case "SESSION":
				$res = self::getArrayValue($_SESSION,$val);
				break;
			case "SERVER":
				$res = self::getArrayValue($_SERVER,$val);
				break;
			default:
				break;
		}
		return $res;
	}
	public static function getArrayValue($datas,$key=""){
		if($key===""){
			return "";
		}

		$keys = explode("/",$key);

		if(count($keys) === 1){
			if(isset($datas[$key])){
				return $datas[$key];
			}
			else{
				return "";
			}
		}

		$first_key = array_shift($keys);

		return self::getArrayValue($datas[$first_key] , join("/",$keys));
	}

	public static function getProcs($key,$proc,$val){
		$res = "";
		$key = strtoupper($key);
		switch($key){
			case "CLASS":
				$res = self::getProcs_class($proc,$val);
				break;
			case "FUNCTION":
				$res = self::getProcs_function($proc,$val);
				break;
			case "PROC":
				$res = self::getProcs_proc($proc,$val);
				break;
		}
		return $res;
	}

	public static function getCodes($key,$val){
		$res = "";
		$key = strtoupper($key);
		switch($key){
			case "EVAL":
				$res = self::getCodes_code($val);
				break;
			case "CODE":
				$res = self::getCodes_code($val);
				break;
			case "FILE":
				$res = self::getCodes_file($val);
				break;
		}
		return $res;
	}

  public static function getProcs_class($func,$val){
    $data = explode("/" , $func);

    if(count($data)!==2 || !class_exists($data[0])){return "";}

    $query = ($val=="")?array():explode(",",$val);

		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"','',$query[$i]);
			$query[$i] = str_replace("'","",$query[$i]);
		}

    if(!method_exists($data[0],$data[1])){return;}
		$cls = new $data[0];

		return call_user_func_array(array($cls , $data[1]) , $query);
  }
	public static function getProcs_proc($func,$val){
    $data = explode("/" , $func);

    if(count($data)!==2 || !class_exists($data[0])){return "";}

    $query = ($val=="")?array():explode(",",$val);

		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"','',$query[$i]);
			$query[$i] = str_replace("'","",$query[$i]);
		}

    if(!method_exists($data[0],$data[1])){return;}

		return call_user_func_array($data[0]."::".$data[1] , $query);
  }

	public static function getMethod($cls,$func,$vals=""){
		$res = "";
		if(class_exists($cls) && method_exists($cls, $func)){
			$querys = ($vals==="")? array() : explode(",",$vals);
			for($i=0,$c=count($querys); $i<$c; $i++){
				$querys[$i] = str_replace('"','',$querys[$i]);
				$querys[$i] = str_replace("'","",$querys[$i]);
			}
			$res = call_user_func_array($cls."::".$func , $querys);
		}
		return $res;
	}

	public static function getProcs_function($func,$val){
    if(!function_exists($func)){return "";}

    $query = ($val=="")?array():explode(",",$val);

		for($i=0,$c=count($query); $i<$c; $i++){
			$query[$i] = str_replace('"','',$query[$i]);
			$query[$i] = str_replace("'","",$query[$i]);
		}

		return call_user_func_array($func , $query);
  }

  public static function getData_FOR($val){
    preg_match("/^(.*?),(.*?),(.*?):(.*?)$/s" , $val , $match);
    //preg_match("/^([0-9]+),([0-9]+),([0-9]+):(.*?)$/s" , $val , $match);
    if(count($match)!==5){return $val;}

    $val1 = self::getPattern_Lite($match[1]);
    $val2 = self::getPattern_Lite($match[2]);
    $val3 = self::getPattern_Lite($match[3]);

    $value="";
    for($i=$val1; $i<=$val2; $i=$i+$val3){
      $str = $match[4];
      $str = str_replace("%num%" , $i , $str);
      $value.= $str;
    }
    $value = self::getPattern_Lite($value);
    return $value;
  }

	public static function getCodes_code($val){
    if(!$val){return;}
    return eval($val);
  }

  public static function getCodes_file($path){
    if(!is_file($path)){return;}
    $source = file_get_contents($path);
    $source = self::rep($source);
    return $source;
  }

  public static function getData_IF($val){
		$sp = explode(":",$val);
		if($sp[0]){
			return $sp[1];
		}
		else{
			return $sp[2];
		}
  }
}
