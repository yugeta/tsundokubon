<?php

class MYNT_GROUP{

	// グループコンフィグデータを取得
	public static function getData($path = "data/config/group.json"){
		if(!is_file($path)){return;}
		return json_decode(file_get_contents($path) , true);
	}

	// グループデータ部分を配列で取得
	public static function getLists(){
		$group = self::getData();
		if(!isset($group["data"]) || !count($group["data"])){return;}
		return $group["data"];
	}

	// 名前の一覧を取得
	public static function getArray_Names(){
		$group = self::getLists();
		if(!count($group)){return;}
		$names = array();
		for($i=0,$c=count($group); $i<$c; $i++){
			$names[] = $group["name"];
		}
		return $names;
	}

	// key = value(name)で 連想配列を取得
	public static function getAssociateArray(){
		$group = self::getLists();
		if(!count($group)){return;}
		$data = array();
		for($i=0,$c=count($group); $i<$c; $i++){
			$data[$group["key"]] = $group["name"];
		}
		return $data;
	}

	public static function getNamesHtml_option($value="", $arrtibute="", $class="", $style=""){
		$group = self::getLists();
		if(!count($group)){return;}
		$html = "";
		for($i=0,$c=count($group); $i<$c; $i++){
			$selected = ($value == $group[$i]["id"])?"selected":"";
			$html .= "<option value='".$group[$i]["id"]."' ".$arrtibute." class='".$class."' style='".$style."' ".$selected.">";
			$html .= $group[$i]["name"];
			$html .= "</option>";
			$html .= PHP_EOL;
		}
		return $html;
	}
	public static function getNamesHtml_links($arrtibute="",$class="",$style=""){
		$group = self::getLists();
		if(!count($group)){return;}

		// $MYNT_URL = new MYNT_URL;
		$defaultURL = MYNT_URL::getUrl() ."?default=search&group=";

		$html = "";
		for($i=0,$c=count($group); $i<$c; $i++){
			$url = $defaultURL.$group[$i]["id"];
			$html .= "<p>";
			$html .= "<a href='".$url."' ".$arrtibute." class='".$class."' style='".$style."'>";
			$html .= $group[$i]["name"];
			$html .= "</a>";
			$html .= "</p>";
			$html .= PHP_EOL;
		}
		return $html;
	}
	public static function getNamesHtml_li($arrtibute="",$class="",$style=""){
		$group = self::getLists();
		if(!count($group)){return;}
		$html = "";
		for($i=0,$c=count($group); $i<$c; $i++){
			$html .= "<li class='".$class."' ".$arrtibute." style='".$style."'>";
			$html .= $group[$i]["name"];
			$html .= "</li>";
			$html .= PHP_EOL;
		}
		return $html;
	}

	/** Config-setting **/

	public static function setUpdate(){

		// get sved-data
		$groupDatas = self::getData();

		// set update-data
		$data = array();
		foreach($_POST["group_data"] as $key => $val){
			// echo $key ."=". $val."<br>";
			$line = array("id"=>(string)$key , "name"=>$val);
			array_push($data , $line);
		}
		$groupDatas["data"] = $data;
// 		print_r($groupDatas);
// exit();
		$json = json_encode($groupDatas , JSON_PRETTY_PRINT);
		$json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
		$json = str_replace("\\/","/",$json);
		file_put_contents("data/config/group.json" , $json);
		MYNT_URL::setUrl(MYNT_URL::getUrl()."?system=".$_REQUEST["system"]);
	}

	public static function addGroupList(){
		$groupDatas = self::getData();

		$currentTime = date("YmdHis");

		$newData = array(
			"id"   => $currentTime,
			"name" => "group-".$currentTime
		);

		if(!isset($groupDatas["data"])){
			$groupDatas["data"] = array();
		}

		array_push($groupDatas["data"] , $newData);

		// print_r($groupDatas);
		$json = json_encode($groupDatas , JSON_PRETTY_PRINT);
		$json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
    $json = str_replace("\\/","/",$json);

		file_put_contents("data/config/group.json" , $json);

		MYNT_URL::setUrl(MYNT_URL::getUrl()."?system=".$_REQUEST["system"]);
	}

	public static function viewGroupTableRows(){
		//$groupDatas = $GLOBALS["config"]["group"]["data"];
		$groupDatas = self::getLists();

		$html = "";
		for($i=0,$c=count($groupDatas); $i<$c; $i++){
			$html .= "<tr class='data' data-id='".$groupDatas[$i]["id"]."'>";
			$html .= "<th class='id'>".($i+1)."</th>";
			$html .= "<td class='name'><input class='form-control group-name' type='text' name='group_data[".$groupDatas[$i]["id"]."]' value='".$groupDatas[$i]["name"]."'></td>";
			$html .= "<td class='count'>".MYNT_BLOG::getGroupNameCount($groupDatas[$i]["id"])."</td>";
			$html .= "<td class='remove'>削除</td>";
			$html .= "</tr>".PHP_EOL;
		}
		return $html;
	}


}
