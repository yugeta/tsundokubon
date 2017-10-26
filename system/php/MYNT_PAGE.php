<?php

class MYNT_PAGE{

	public static $default_dir  = "data/page/";
	public static $system_dir   = "system/page/";
	public static $default_top  = "system/page/";
	public static $notlogin     = "system/page/login";
	// public $default_404  = "design/".$GLOBALS["config"]["design"]["target"]."/html/404";

	public static function default_404(){
		// return "design/".$GLOBALS["config"]["design"]["target"]."/html/404";
		return "data/page/default/404";
	}

	/**
	* return @ %path%/***.html
	*/
	public static function getBase(){
		$base = $GLOBALS["config"]["page"]["base"];
		$type = $GLOBALS["config"]["pageCategoryLists"]["type"];

		for($i=0,$c=count($type); $i<$c; $i++){
			$key = $type[$i]["key"];
			$dir = $type[$i]["dir"];
			$baseFile = $type[$i]["baseFile"];
			if(isset($_REQUEST[$key]) && is_file($dir.$_REQUEST[$key].".html")){
				$base = $baseFile;
				break;
			}
		}
		return $base;
	}

	/**
	* return @ %path%/***.html | %path%/***.info
	*/
	public static function getPageID(){
		$path = "";
		$type = $GLOBALS["config"]["pageCategoryLists"]["type"];

		for($i=0,$c=count($type); $i<$c; $i++){
			$key = $type[$i]["key"];
			$dir = $type[$i]["dir"];
			$baseFile = $type[$i]["baseFile"];
			if(isset($_REQUEST[$key]) && is_file($dir.$_REQUEST[$key].".html")){
				$path = $dir.$_REQUEST[$key];
				break;
			}
		}
		return $path;
	}

	// クエリを判別してページを表示（ない場合はエラーページ）
	public static function getSource($type = ""){

		// ログイン後に読み込みページが変わる場合の設定
		// $file = ($loginedFile !== "" && isset($_SESSION["login_id"]) && $_SESSION["login_id"])?$loginedFile:$notLoginFile;
		//

		// mode
		$mode = "";
		$file = "";



		// path-get
		$path = "";
		if(!isset($_REQUEST["m"])&& !$_REQUEST["m"] && !isset($_REQUEST["p"]) && !$_REQUEST["p"]){
			$path = "data/page/default/top.html";
		}
		else if(!isset($_REQUEST["m"])&& !$_REQUEST["m"] && isset($_REQUEST["p"]) && $_REQUEST["p"]){
			$path = "data/page/blog/".$_REQUEST["p"].".html";
		}
		else if(isset($_REQUEST["m"])&& $_REQUEST["m"] && isset($_REQUEST["p"]) && $_REQUEST["p"]){
			$path = "data/".$_REQUEST["m"]."/".$_REQUEST["p"].".html";
		}

		// 認証
		// if($type === "login" && !isset($_SESSION["login_id"])){
		// 	$path = $this->system_dir."login.html";
		// }
		//
		// // クエリにページ指定があるか確認
		// else if(isset($_REQUEST["p"]) && $_REQUEST["p"]){
		// 	if(is_file($this->default_dir.$_REQUEST["p"].".html")){
		// 		// $path = $this->default_dir.$_REQUEST["p"].".html";
		// 		$path = "design/".$GLOBALS["config"]["design"]["target"]."/html/blog.html";
		// 	}
		// }
		// else if(isset($_REQUEST["blog"]) && $_REQUEST["blog"]){
		// 	if(is_file("data/page/blog/".$_REQUEST["blog"].".html")){
		// 		// $path = $this->default_dir.$_REQUEST["p"].".html";
		// 		$path = "design/".$GLOBALS["config"]["design"]["target"]."/html/blog.html";
		// 	}
		// }


		// systemページ
		else if(isset($_REQUEST["system"]) && $_REQUEST["system"]){
			// $file = $_REQUEST["system"];
			if(is_file(self::$system_dir.$_REQUEST["system"].".html")){
				$path = self::$system_dir.$_REQUEST["system"].".html";
			}
		}

		// ページ指定が無ければデフォルトページを設定
		else{
			$top = (isset($GLOBALS["config"]["page"]["top"]))?$GLOBALS["config"]["page"]["top"]:"top";
			// $path = "design/".$GLOBALS["config"]["design"]["target"]."/html/".$top.".html";
			$path = "data/page/default/".$top.".html";
		}

		if($path === "" || !is_file($path)){
			$path = self::default_404().".html";
		}

		$source = file_get_contents($path);

		// $MYNT_SOURCE = new MYNT_SOURCE;
		return MYNT::conv($source);
	}

	// infoデータからタイトルを取得
	public static function getTitle($file = ""){
		$info = self::getPageInfo($file);

		if(!isset($info["title"])){
			return "";
		}

		// $MYNT_SOURCE = new MYNT_SOURCE;
		return MYNT::conv($info["title"]);
	}

	public static function getPageInfo($file = ""){

		$path="";

		if(isset($_REQUEST["p"]) && $_REQUEST["p"]){
			$path = self::$default_dir;
			if(is_file(self::$default_dir.$_REQUEST["p"].".info")){
				$path = self::$default_dir.$_REQUEST["p"].".info";
			}
			else{
				$path .= self::default_404().".info";
			}
		}
		if(isset($_REQUEST["system"]) && $_REQUEST["system"]){
			$path = self::$default_dir;
			if(is_file(self::$default_dir.$_REQUEST["system"].".info")){
				$path = self::$default_dir.$_REQUEST["system"].".info";
			}
			else{
				$path .= self::default_404().".info";
			}
		}
		else{
			$top = (isset($GLOBALS["config"]["page"]["top"]))? $GLOBALS["config"]["page"]["top"] : "top";
			$path = self::$default_top.$top.".info";
		}

		// $path = $this->default_dir;
		// if(is_file($path.$file.".info")){
		// 	$path .= $file.".info";
		// }
		// else{
		// 	$path .= $this->default_404().".info";
		// }
		$json = array();
		if(is_file($path)){
			$source = file_get_contents($path);
			$json   = json_decode($source , true);
		}

		return $json;
	}
	//
	// function getPageDat($root = "html-info/top"){
	// 	$source = "";
	//
	// 	$err404 = "data/page/html-info/404.dat";
	//
	// 	if(isset($_REQUEST["s"]) && $_REQUEST["s"] !== ""){
	// 		$path = "data/page/s/".$_REQUEST["s"].".html";
	// 		if(is_file($path)){
	// 			$source = file_get_contents($path);
	// 		}
	// 		else{
	// 			$source = file_get_contents($err404);
	// 		}
	// 	}
	// 	else if(isset($_REQUEST["html"]) && $_REQUEST["html"] !== ""){
	// 		$path = "data/page/html/".$_REQUEST["html"].".html";
	// 		if(is_file($path)){
	// 			// $source = $this->getJsonData("",file_get_contents("data/page/html/".$_REQUEST["html"].".html"));
	// 			$source = file_get_contents($path);
	// 		}
	// 		else{
	// 			$source = file_get_contents($err404);
	// 		}
	// 	}
	// 	else{
	// 		$path = "data/page/".$root.".html";
	// 		if(is_file($path)){
	// 			$source = file_get_contents($path);
	// 		}
	// 		else{
	// 			$source = file_get_contents($err404);
	// 		}
	// 	}
	//
	// 	return $source;
	// }

	// function getJsonData($title,$source){
	// 	$jsonArr = array("title"=>$title , "source"=>$source);
	// 	$jsonStr = json_encode($jsonArr);
	// 	$jsonStr = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$jsonStr);
	// 	return $jsonStr;
	// }



	// function getPageTitle($pageID){
	// 	if(is_file("data/page/title/".$pageID.".dat")){
	// 		return file_get_contents("data/page/title/".$pageID.".dat");
	// 	}
	// 	else{
	//
	// 	}
	// }

	//
	// function changePageSource($source){
	// 	$sources = explode("\n",$source);
	// 	$new_source = "";
	// 	for($i=0; $i<count($sources); $i++){
	// 		$new_source .= $sources[$i];
	// 	}
	// 	return $new_source;
	// }
	// function changePageNewline($source){
	// 	$sources = explode("\n",$source);
	// 	$new_source = "";
	// 	for($i=0; $i<count($sources); $i++){
	// 		$new_source .= $sources[$i].PHP_EOL;
	// 	}
	// 	return $new_source;
	// }
	// function changePageLine($source){
	// 	$sources = explode("\n",$source);
	// 	$new_source = "";
	// 	for($i=0; $i<count($sources); $i++){
	// 		$new_source .= "<p>".$sources[$i]."</p>".PHP_EOL;
	// 	}
	// 	return $new_source;
	// }

	//
	public static function getFile($filePath){
		if(!is_file($filePath)){return;}
		$data = file_get_contents($filePath);
		$data = str_replace("<","&lt;",$data);
		$data = str_replace(">","&gt;",$data);
		return $data;
	}

	public static function getFileLists($ext="html"){
		// if(!$type){return;}

		$path = "data/page/";
		if(!is_dir($path)){return;}

		$lists = array();
		$files = scandir($path);
		for($i=0,$c=count($files); $i<$c; $i++){
			if($files[$i]==="." || $files[$i]===".."){continue;}
			if($ext && !preg_match("/(.+?)\.".$ext."/",$files[$i],$match)){continue;}
			// $lists[] = $files[$i];
			$lists[] = $match[1];
		}
		// print_r($lists);
		return $lists;
	}
	public static function getFileListsOptions($ext="html"){
		// if(!$type){return;}

		$fileNames = self::getFileLists($ext);

		$options = array();
		for($i=0,$c=count($fileNames); $i<$c; $i++){
			// preg_match("/(.+?)\.(.+?)/",$files[$i] , $match);
			$selected = (isset($_REQUEST["file"]) && $_REQUEST["file"] === $fileNames[$i])?"selected":"";
			$viewTitle = self::getPageInfoString($fileNames[$i],"title");
			if(!$viewTitle){$viewTitle = "* ".$fileNames[$i];}
			$options[] = "<option value='".$fileNames[$i]."' ".$selected.">".$viewTitle."</option>".PHP_EOL;
		}
		// print_r($options);
		return join("",$options);
	}

	// page-data-save
	public static function setSystemPage(){
		// die("saveing");
		// die($_REQUEST["source"]);
		// die($_REQUEST["file"]." | ".$_REQUEST["type"]);

		$current_time = time();

		// file-name
		if(!isset($_REQUEST["file"]) || !$_REQUEST["file"]){
			$_REQUEST["file"] = $current_time;

		}
		if(!isset($_REQUEST["regist"]) || !$_REQUEST["regist"]){
			$_REQUEST["regist"] = $current_time;
		}

		// file-path
		$path_html = "data/page/".$_REQUEST["file"].".html";
		$path_info = "data/page/".$_REQUEST["file"].".info";
		$backupDir = "data/backup/page/".$_REQUEST["type"]."/";

		// backup-folder
		if(!is_dir($backupDir)){
			mkdir($backupDir , 0777 , true);
		}

		// backup
		if(is_file($path_html)){
			rename($path_html , $backupDir.$_REQUEST["file"].".html.".date(Ymdhis));
		}
		if(is_file($path_info)){
			rename($path_info , $backupDir.$_REQUEST["file"].".info.".date(Ymdhis));
		}

		// source-save
		file_put_contents($path_html , $_REQUEST["source"]);

		// info-save
		$info = array(
			"id"        => $_REQUEST["file"],
			"title"     => $_REQUEST["title"],
			"discription"=> $_REQUEST["source"],
			"eyecatch"  => $_REQUEST["eyecatch"],
			"type"      => $_REQUEST["type"],
			"status"    => $_REQUEST["status"],
			"schedule"  => $_REQUEST["schedule"],
			"tag"       => $_REQUEST["tag"],
			"group"     => $_REQUEST["group"],
			"category"  => $_REQUEST["category"],
			"regist"    => $_REQUEST["regist"],
			"update"    => $current_time
		);
		$json = json_encode($info);
		$json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
		file_put_contents($path_info , $json);


		//redirect
		// $url = new MYNT_URL;
		header("Location: ". MYNT_URL::getUrl()."?system=".$_REQUEST["system"]."&file=".$_REQUEST["file"]);

	}

	public static function getPageInfoString($fileName = "" , $key = ""){
		if($key === "" || $fileName === ""){return;}
		if(!is_file(self::$default_dir.$fileName.".info")){return;}

		$json = json_decode(file_get_contents(self::$default_dir.$fileName.".info"),true);

		if(!isset($json[$key])){return;}

		return $json[$key];
	}

	public static function getPageCategoryLists($key=""){
		if($key===""){return array();}
		$path = "data/config/pageCategoryLists.json";
		if(!is_file($path)){return array();}
		$json = json_decode(file_get_contents($path),true);
		if(!isset($json[$key])){return array();}
		return $json[$key];
	}
	public static function getPageCategoryListsOptions($key=""){
		if($key===""){return "";}

		// configデータの取得
		$lists = self::getPageCategoryLists($key);

		// 登録データの取得
		$val = "";
		if(isset($_REQUEST["file"])){
			$val = self::getPageInfoString($_REQUEST["file"],$key);
		}

		// optionタグの作成
		$options = array();
		for($i=0,$c=count($lists); $i<$c; $i++){
			$selected = "";
			if($val !== "" && $val === $lists[$i]["key"]){$selected = " selected";}
			$options[] = "<option value='".$lists[$i]["key"]."'".$selected.">".$lists[$i]["value"]."</option>";
		}
		return join(PHP_EOL,$options);
	}
	public static function getPageCategoryLists_li($key=""){
		if($key===""){return "";}

		// configデータの取得
		$lists = self::getPageCategoryLists($key);

		// optionタグの作成
		$html = "";
		for($i=0,$c=count($lists); $i<$c; $i++){

			// $MYNT_URL = new MYNT_URL;
			$link_url = MYNT_URL::getUrl() ."?system=".$_REQUEST["system"] ."&status=".$lists[$i]["key"];

			$active = "";
			if($lists[$i]["key"] === $_REQUEST["status"]){$active = "active";}

			$html .= "<li role='presentation' class='".$active."'>";
			$html .= "<a class='dropdown-toggle' role='button' aria-haspopup='true' aria-expanded='false' href='".$link_url."'>".$lists[$i]["value"]." (".$this->getPageCount($lists[$i]["key"]).")</a>";
			$html .= "</li>";
			$html .= PHP_EOL;
		}
		return $html;
	}


	public static function getFileSource(){
		if(isset($_REQUEST["filePath"]) && is_file($_REQUEST["filePath"])){
			echo file_get_contents($_REQUEST["filePath"]);
		}
		exit();
	}

	public static function getTemplateFile(){
		if(!isset($_REQUEST["filePath"]) || !is_file($_REQUEST["filePath"])){return;}
		$temp = file_get_contents($_REQUEST["filePath"]);

		// $mode = "";
		// if(isset($_REQUEST["mode"]) && $_REQUEST["mode"]){
		// 	$mode = $_REQUEST["mode"];
		// }

		// $MYNT_SOURCE = new MYNT_SOURCE;
		echo MYNT::conv($temp);
		exit();
	}



	public static function viewPageLists($status = ""){
		$lists = self::getPageLists($status);
		$html = "";
		for($i = 0,$c = count($lists); $i < $c; $i++){
			$info = self::getPageInfoFromPath(self::$default_dir.$lists[$i]);
			$html .= "<tr class='titleList' onclick='location.href=\"?system=pageEdit&file=".self::getFileName2ID($lists[$i])."\"'>".PHP_EOL;
			$html .= "<th style='width:50px;'>".($i+1)."</th>".PHP_EOL;
			$html .= "<td>".$info["title"]."</td>".PHP_EOL;
			$html .= "<td>".self::getKey2Value($GLOBALS["config"]["pageCategoryLists"]["status"] , $info["status"])."</td>".PHP_EOL;
			$html .= "<td>".$info["update"]."</td>".PHP_EOL;
			$html .= "<td>".$info["release"]."</td>".PHP_EOL;
			$html .= "</tr>".PHP_EOL;
		}

		return $html;
	}

	public static function getPageLists($status = ""){
		if(!is_dir(self::$default_dir)){return array();}

		$lists = scandir(self::$default_dir);

		$datas = array();

		for($i=0,$c=count($lists); $i<$c; $i++){
			if($lists[$i]==="." || $lists[$i]===".."){continue;}
			if(preg_match("/^(.+?)\.info$/",$lists[$i],$m)){
				$json = self::getPageInfoFromPath(self::$default_dir.$lists[$i]);
				if($status !== "" && !isset($json["status"])){continue;}
				if($status !== "" && $status !== $json["status"]){continue;}
				$datas[] = $lists[$i];
			}
		}
		return $datas;
	}

	public static function getPageInfoFromPath($path){
		$datas = array();
		if(is_file($path)){
			$datas = json_decode(file_get_contents($path),true);
		}
		return $datas;
	}

	// public function viewStatusLists(){
	// 	$html = "";
	// 	for($i=0; $i<count($GLOBALS["config"]["pageCategpryLists"]["status"]); $i++){
	// 		$html .= $GLOBALS["config"]["pageCategpryLists"]["status"][$i]."".PHP_EOL;
	// 	}
	// }

	public static function getPageCount($status = ""){
		$lists = self::getPageLists($status);
		return count($lists);
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
	public static function getValue2Key($data , $value){
		$res = "";
		for($i=0; $i<count($data); $i++){
			if($data[$i]["value"] === $value){
				$res = $data[$i]["key"];
				break;
			}
		}
		return $res;
	}
	public static function getFileName2ID($path){
		$sp0 = explode("/",$path);
		$sp1 = explode(".",$sp0[count($sp0)-1]);
		$sp2 = array_pop($sp1);
		return join(".",$sp1);
	}



}
