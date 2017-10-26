<?php

class MYNT_PAGE_EDIT{

	/** Lib **/
	public static $dir = "data/page/";

	public static function getFileLists($type, $ext="html"){
		$path = self::getType2Dir($type);
		if(!is_dir($path)){return;}

		$lists = array();
		$files = scandir($path);
		for($i=0,$c=count($files); $i<$c; $i++){
			if($files[$i]==="." || $files[$i]===".."){continue;}
			if($ext && !preg_match("/(.+?)\.".$ext."/",$files[$i],$match)){continue;}
			$lists[] = $match[1];
		}
		return $lists;
	}


	/** HTML proc **/

	// get-value
	public static function getPageInfoString($fileName="", $key=""){
		if($key === "" || $fileName === ""){return;}
		$path = self::$dir;
		if(!is_file($path.$fileName.".info")){return;}
		$json = json_decode(file_get_contents($path."/".$fileName.".info"),true);
		if(!isset($json[$key])){return;}
		return $json[$key];
	}

	public static function getFileListsOptions($type, $file, $ext="html"){
		$fileNames = self::getFileLists($type, $ext);
		$options = array();
		for($i=0,$c=count($fileNames); $i<$c; $i++){
			// preg_match("/(.+?)\.(.+?)/",$files[$i] , $match);
			$selected = ($file === $fileNames[$i])?"selected":"";
			$viewTitle = self::getPageInfoString($fileNames[$i],"title");
			if(!$viewTitle){$viewTitle = $fileNames[$i].".html";}
			$options[] = "<option value='".$fileNames[$i]."' ".$selected.">".$viewTitle."</option>".PHP_EOL;
		}
		return join("",$options);
	}

	public static function getPageCategoryLists($key=""){
		if(isset($GLOBALS["config"]["pageCategoryLists"][$key])){
			return $GLOBALS["config"]["pageCategoryLists"][$key];
		}
		else if($key === "group"){

		}
		else{
			return array();
		}
	}

	public static function getSetatusListsOptions($file){

		// 登録データの取得
		$val = self::getPageInfoString($file, "status");

		// configデータの取得
		$lists = $GLOBALS["config"]["page_status"];

		// optionタグの作成
		$options = array();
		for($i=0,$c=count($lists); $i<$c; $i++){
			$selected = "";
			if($val !== "" && $val === $lists[$i]["key"]){$selected = " selected";}
			$options[] = "<option value='".$lists[$i]["key"]."'".$selected.">".$lists[$i]["value"]."</option>";
		}
		return join(PHP_EOL,$options);
	}

	public static function getGroupListsOptions($file){

		// 登録データの取得
		$val = self::getPageInfoString($file, "group");

		// configデータの取得
		$lists = $GLOBALS["config"]["group"];

		// optionタグの作成
		$options = array();
		for($i=0,$c=count($lists); $i<$c; $i++){
			$selected = "";
			if($val !== "" && $val === $lists[$i]["id"]){$selected = " selected";}
			$options[] = "<option value='".$lists[$i]["id"]."'".$selected.">".$lists[$i]["name"]."</option>";
		}
		return join(PHP_EOL,$options);
	}

	public static function getTemplateFile(){
		if(!isset($_REQUEST["filePath"]) || !is_file($_REQUEST["filePath"])){return;}
		$temp = file_get_contents($_REQUEST["filePath"]);

		// $MYNT_SOURCE = new MYNT_SOURCE;
		echo MYNT::conv($temp);
		exit();
	}



	/** Proc **/

	// [page-edit] load-source-file-data
	public static function getSource($fileName){
		$path = self::$dir;
		$filePath = $path.$fileName.".html";
		$data = "";
		if(is_file($filePath)){
			$data = file_get_contents($filePath);
			$data = str_replace("<","&lt;",$data);
			$data = str_replace(">","&gt;",$data);
		}
		return $data;
	}

	//
	public static function getType2Dir($type){
		return self::$dir;
	}

	//
	public static function setDirSlash($dir){
		if(!preg_match("/.+?\/$/",$dir)){
			$dir .= "/";
		}
		return $dir;
	}

	// page-data-save
	public static function setSystemPage(){
		if($_REQUEST["mode"] === "remove" && isset($_REQUEST["file"]) && $_REQUEST["file"]){
			self::setPageRemove($_REQUEST["type"] , $_REQUEST["file"]);
			header("Location: ". MYNT_URL::getUrl()."?system=pageLists&type=".$_REQUEST["type"]."&status=".$_REQUEST["status"]);
			exit();
		}

		$current_time = time();

		// file-name
		if(!isset($_REQUEST["file"]) || !$_REQUEST["file"]){
			$_REQUEST["file"] = $current_time;
		}
		if(!isset($_REQUEST["regist"]) || !$_REQUEST["regist"]){
			$_REQUEST["regist"] = $current_time;
		}

		// set-Path
		$previous_path = self::getType2Dir($type);
		$default_path  = self::getType2Dir($_REQUEST["type"]);
		$backupDir     = "data/backup/";

		// backup-dir
		if(!is_dir($backupDir)){
			mkdir($backupDir.$previous_path , 0777 , true);
		}
		// save-dir
		if(!is_dir($default_path)){
			mkdir($default_path , 0777 , true);
		}

		// backup
		if(is_file($previous_path.$_REQUEST["file"].".html")){
			rename($previous_path.$_REQUEST["file"].".html" , $backupDir.$previous_path.$_REQUEST["file"].".html.".$current_time);
		}
		if(is_file($previous_path.$_REQUEST["file"].".info")){
			rename($previous_path.$_REQUEST["file"].".info" , $backupDir.$previous_path.$_REQUEST["file"].".info.".$current_time);
		}

		// source-save
		file_put_contents($default_path.$_REQUEST["file"].".html" , $_REQUEST["source"]);

		// // info-save
		// $info = array(
		// 	"id"         => $_REQUEST["file"],
		// 	"title"      => $_REQUEST["title"],
		// 	"discription"=> $_REQUEST["source"],
		// 	"source"     => $_REQUEST["source"],
		// 	"eyecatch"   => $_REQUEST["eyecatch"],
		// 	"type"       => $_REQUEST["type"],
		// 	"status"     => $_REQUEST["status"],
		// 	"schedule"   => $_REQUEST["schedule"],
		// 	"tag"        => $_REQUEST["tag"],
		// 	"group"      => $_REQUEST["group"],
		// 	"category"   => $_REQUEST["category"],
		// 	"regist"     => $_REQUEST["regist"],
		// 	"update"     => $current_time
		// );
		//
		// $json = json_encode($info, JSON_PRETTY_PRINT);
		// $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
		// file_put_contents($default_path.$_REQUEST["file"].".info" , $json);


		//redirect
		// $url = new MYNT_URL;
		header("Location: ". MYNT_URL::getUrl()."?p=".$_REQUEST["p"]."&file=".$_REQUEST["file"]);

	}


	public static function setPageRemove($type , $file){
		$current_time = date("YmdHis");

		$default_path  = self::getType2Dir($type);
		//die($default_path ." | ". $file);
		$htmlPath = $default_path.$file.".html";
		$infoPath = $default_path.$file.".info";

		// backup-dir
		if(!is_dir("data/backup/".$default_path)){
			mkdir("data/backup/".$default_path , 0777 , true);
		}

		// html
		if(isset($htmlPath)){
			rename($htmlPath , "data/backup/".$htmlPath.".".$current_time);
		}

		// info
		if(isset($infoPath)){
			rename($infoPath , "data/backup/".$infoPath.".".$current_time);
		}
	}

	/**
	* statusが「trash」の時のみ「removeボタンが表示される」
	*/
	public static function setRemoveButton(){
		$status = self::getPageInfoString($_REQUEST["file"], "status");
		if($status==="trash"){
			return "inline-block";
		}
		else{
			return "none";
		}
	}

}
