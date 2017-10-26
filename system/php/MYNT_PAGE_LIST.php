<?php

class MYNT_PAGE_LIST{

	public static $dir = "data/page/";

	/** Library **/

	// public static function getDefaultKey_type(){
	// 	return $GLOBALS["config"]["pageCategoryLists"]["type"][0]["key"];
	// }

	// get type-info [data/config/pageCategoryLists.json] keys[ key , value , dir , baseFile , SCRIPT_NAME ]
	public static function getType2Info($key = ""){
		$data = array();
		$types = $GLOBALS["config"]["pageCategoryLists"]["type"];
		for($i=0,$c=count($types); $i<$c; $i++){
			if($types[$i]["key"] === $key){
				$data = $types[$i];
			}
		}
		return $data;
	}

	public static function getFileName2ID($path){
		$sp0 = explode("/",$path);
		$sp1 = explode(".",$sp0[count($sp0)-1]);
		$sp2 = array_pop($sp1);
		return join(".",$sp1);
	}

	public static function getKey2Value($data , $key){
		$res = "";
		for($i=0; $i<count($data); $i++){
			if($data[$i]["key"] === $key){
				$res = $data[$i]["value"];
				break;
			}
		}
		return $res;
	}

	public static function getPageInfoFromPath($path){
		$datas = array();
		if(is_file($path)){
			$datas = json_decode(file_get_contents($path),true);
		}
		return $datas;
	}

	public static function getPageCount($status=""){
		// if($type===""){$type=self::getDefaultKey_type();}
		$lists = self::getPageLists($status);
		// echo "<pre>";
		// print_r($lists);
		// echo "</pre>";
		return count($lists);
	}

	/**
	* *blank : without remove
	* all : all
	*/
	public static function getPageLists($status=""){

		$dir = self::$dir;

		$datas = array();

		if(!is_dir($dir)){return $datas;}

		$lists = scandir($dir);

		for($i=0,$c=count($lists); $i<$c; $i++){
			if($lists[$i]==="." || $lists[$i]===".."){continue;}

			if(!preg_match("/^(.+?)\.html$/",$lists[$i],$match)){continue;}

			// page-info
			$pageInfo = self::getPageInfoFromPath($dir . $match[1] . ".info");

			// check
			if(!isset($pageInfo["status"])){
				if($status === "unregist"){
					$datas[] = $lists[$i];
					continue;
				}
				else if($status !== ""){
					continue;
				}
			}
			else{
				if($status !== "" && $status !== $pageInfo["status"]){
					continue;
				}
				else if($status === "" && $pageInfo["status"] === "trash"){
					continue;
				}
			}

			$datas[] = $lists[$i];
		}
		return $datas;
	}


	/** proc **/

	// // [page-list] type-list-tag(li) (blog/default/system/etc...)
	// public static function getPageTypeLists_li(){
	// 	// configデータの取得
	// 	$lists = $GLOBALS["config"]["page_status"];
	//
	// 	// optionタグの作成
	// 	$html = "";
	// 	for($i=0,$c=count($lists); $i<$c; $i++){
	// 		$page     = (isset($_REQUEST["p"]))?$_REQUEST["p"]:"";
	// 		$stat     = (isset($_REQUEST["status"]))?$_REQUEST["status"]:"";
	// 		$link_url = MYNT_URL::getUrl()."?p=".$page."&status=".$stat;
	// 		$active = ($lists[$i]["key"] === $stat)?$active = "active" : "";
	// 		$html .= "<li role='presentation' class='".$active."'>";
	// 		$html .= "<a href='".$link_url."'>".$lists[$i]["value"]."*"."</a>";
	// 		$html .= "</li>";
	// 		$html .= PHP_EOL;
	// 	}
	// 	return $html;
	// }

	// [page-list] status-tab-tag(li) (release , make...)
	public static function getPageCategoryLists_li($key="status"){

		if($key===""){return "";}

		// $pageDir = self::getPageDir();

		// configデータの取得
		$lists = $GLOBALS["config"]["page_status"];
		$status = (isset($_REQUEST["status"]))?$_REQUEST["status"]:"";

		// optionタグの作成
		$html = "";
		for($i=0,$c=count($lists); $i<$c; $i++){
			$query = array();
			$page = (isset($_REQUEST["p"]))? $_REQUEST["p"] : "";
			// $type   = (isset($_REQUEST["type"]))?   "type=".$_REQUEST["type"] : "";
			$key = $lists[$i]["key"];

			// $MYNT_URL = new MYNT_URL;
			$link_url = MYNT_URL::getUrl()."?p=".$page."&status=".$key;

			$active = ($lists[$i]["key"] === $status)? $active = "active" : "";

			$html .= "<li role='presentation' class='".$active."'>";
			$html .= "<a class='dropdown-toggle' role='button' aria-haspopup='true' aria-expanded='false' href='".$link_url."'>";
			$html .= $lists[$i]["value"];
			$html .= " (".self::getPageCount($lists[$i]["key"]).")</a>";
			$html .= "</li>";
			$html .= PHP_EOL;
		}
		return $html;
	}


	// Article-lists (table-tr)
	public static function viewPageLists_tr($status=""){

		$dir = self::$dir;

		$lists = self::getPageLists($status);

		$html = "";

		for($i=0,$c=count($lists); $i<$c; $i++){
			$fileName   = self::getFileName2ID($lists[$i]);
			$htmlFile   = $dir.$lists[$i];
			$infoFile   = $dir.$fileName.".info";
			// $infoFile   = str_replace(".html",".info" , $htmlFile);
			// if(is_file($infoFile)){
			// 	$info       = self::getPageInfoFromPath($infoFile);
			// 	$title      = (isset($info["title"]))?$info["title"] : "<b class='string-blue'>File:</b> ".$lists[$i];
			// 	$update     = (isset($info["update"]))?$info["update"]:filemtime($dir.$lists[$i]);
			//
			// 	$listStatus =self::getKey2Value($GLOBALS["config"]["page_status"] , $info["status"]);
			// }
			// else{
			// 	$title = $fileName;
			// 	$update = "";
			// 	$listStatus = "";
			// }
			$info       = self::getPageInfoFromPath($infoFile);
			$title      = (isset($info["title"]))?$info["title"] : "<b class='string-blue'>File:</b> ".$lists[$i];
			$update     = (isset($info["update"]))?$info["update"]:filemtime($dir.$lists[$i]);
			$release    = (isset($info["release"]))?$info["release"]:filemtime($dir.$lists[$i]);
			$listStatus = (isset($info["status"]))?self::getKey2Value($GLOBALS["config"]["page_status"] , $info["status"]):"--";

			$html .= "<tr class='titleList' onclick='location.href=\"?p=pageEdit&file=".$fileName."\"'>".PHP_EOL;
			$html .= "<th style='width:50px;'>".($i+1)."</th>".PHP_EOL;
			$html .= "<td>".$title."</td>".PHP_EOL;
			$html .= "<td>".$listStatus."</td>".PHP_EOL;
			$html .= "<td>".MYNT_DATE::format_ymdhis($update)."</td>".PHP_EOL;
			$html .= "<td>".MYNT_DATE::format_ymdhis($release)."</td>".PHP_EOL;
			$html .= "</tr>".PHP_EOL;
		}

		return $html;
	}


}
