<?php

class MYNT_PICTURE{

	public static $default_page_dir = "data/blog/";
	public static $default_pic_dir = "data/picture/";

	// public static function getPageDir(){
	// 	$pageDir = "blog";
	// 	if(isset($_REQUEST["pageDir"]) && $_REQUEST["pageDir"] !== ""){
	// 		$pageDir = $_REQUEST["pageDir"];
	// 	}
	// 	return $pageDir;
	// }

	public static function getEyecatchFilePath($articleId = ""){
		if($articleId === ""){return;}

		$page_info_path = self::$default_page_dir.$articleId.".info";
		if(!is_file($page_info_path)){return;}

		$jsonPage = json_decode(file_get_contents(self::$default_page_dir.$articleId.".info") , true);
		if(!isset($jsonPage["eyecatch"]) || !$jsonPage["eyecatch"]){return;}

		$pic_info_path = self::$default_pic_dir.$jsonPage["eyecatch"].".info";
		if(!is_file($pic_info_path)){return;}

		$jsonPic = json_decode(file_get_contents($pic_info_path) , true);
		if(!isset($jsonPic["extension"]) || !$jsonPic["extension"]){return;}

		$pic_file_path = self::$default_pic_dir.$jsonPage["eyecatch"].".".$jsonPic["extension"];
		if(!is_file($pic_file_path)){return;}

		return $pic_file_path;
	}
}
