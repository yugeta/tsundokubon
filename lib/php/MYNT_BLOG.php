<?php

class MYNT_BLOG{

	public static $path_blog = "data/blog/";
	public static $path_picture = "data/picture/";
	public static $blogSource = "";
	public static $blogSourcePath = "data/page/blog_lists_box.html";

	// all article page lists for top-page
	public static function viewArticleLists_li(){

		$tmpSource = self::getBlogSource();

		$lists = self::getArticleLists();

		$html = "";
		for($i=0,$c=count($lists); $i<$c; $i++){
			$json = self::getPageInfoFromPath(self::$path_blog.$lists[$i]);
			$html .= self::setBlogSourceReplace($tmpSource, $json);
		}
		// $MYNT_VIEW = new MYNT_VIEW;

		return MYNT::conv($html);
	}

	public static function getBlogSource(){

		$tmpSource = "";

		if(self::$blogSource === ""){
			// $tmpPath = "system/html/top_article.html";
			$tmpPath = self::$blogSourcePath;
			$tmpSource = file_get_contents($tmpPath);
		}
		else{
			$tmpSource = self::$blogSource;
		}
		return $tmpSource;
	}

	public static function setBlogSourceReplace($tmpSource, $jsonData){
		preg_match_all("/<blog:(.+?)>/",$tmpSource,$match);
		// $tmpSource = preg_replace("<blog:title>",$json["title"],$tmpSource);
		for($i=0,$c=count($match[0]); $i<$c; $i++){
			$key = $match[1][$i];
			if(isset($jsonData[$key])){
				$repData = self::setBlogSourceReplace_parsonal($key , $jsonData[$key]);
				$tmpSource = str_replace("<blog:".$key.">", $repData, $tmpSource);
			}
		}
		return $tmpSource;
	}
	public static function setBlogSourceReplace_parsonal($key , $data){
		if($key === "eyecatch"){
			$info = self::getPicId2Info($data);
			if($data !== "" && is_file(self::$path_picture.$data.".".$info["extension"])){
				$data = "<img class='eyecatch' src='".self::$path_picture. $data.".".$info["extension"]."' />";
			}
			else{
				$data = "<img class='eyecatch' src='lib/img/no-image.png' />";
			}

		}
		else{

		}
		return $data;
	}

	public static function getPageEyecatch_img($page_id){

		// blog-info
		$default_blog_path = self::$path_blog;
		if(!is_file($default_blog_path.$page_id.".info")){return;}
		$pageInfo = json_decode(file_get_contents($default_blog_path.$page_id.".info") ,true);

		// eyecatch-info
		$default_pic_path = self::$path_picture;
		if(!isset($pageInfo["eyecatch"]) && !is_file($default_pic_path.$pageInfo["eyecatch"].".info")){return;}
		$picInfo = json_decode(file_get_contents($default_pic_path.$pageInfo["eyecatch"].".info") ,true);

		//eyecatch-image
		if(!is_file($default_pic_path.$pageInfo["eyecatch"].".".$picInfo["extension"])){return;}
		return "<img src='".$default_pic_path.$pageInfo["eyecatch"].".".$picInfo["extension"]."' id='eyecatch_".$pageInfo["eyecatch"]."' alt='".$pageInfo["alt"]."' />";

	}

	public static function getPageInfo($page_id){
		$path = self::$path_blog.$page_id;
		if(!is_file($path.".info")){return;}

		return json_decode(file_get_contents($path.".info"),true);
	}

	public static function getPageTitle($page_id){
		$info = self::getPageInfo($page_id);

		if(!isset($info["title"])){return "";}

		// $MYNT_SOURCE = new MYNT_SOURCE;
		return MYNT::conv($info["title"]);
	}
	public static function getPageSource($page_id){
		$path = self::$path_blog.$page_id.".html";
		if(!is_file($path)){return;}

		// $MYNT_SOURCE = new MYNT_SOURCE;
		return MYNT::conv(file_get_contents($path));
	}


	public static function getPageDiscription($page_id){

	}
	public static function getPageReleaseDate($page_id){

	}

	public static function getPicId2Info($pageID){
		$infoPath = self::$path_picture.$pageID.".info";
		if(!is_file($infoPath)){return;}

		return json_decode(file_get_contents($infoPath), true);
	}

	//
	public static function getArticleLists($status = "release"){
		if(!is_dir(self::$path_blog)){return array();}

		$lists = scandir(self::$path_blog);

		$datas = array();

		for($i=0,$c=count($lists); $i<$c; $i++){

			if($lists[$i]==="." || $lists[$i]===".."){continue;}

			if(preg_match("/^(.+?)\.info$/",$lists[$i],$m)){

				$json = self::getPageInfoFromPath(self::$path_blog.$lists[$i]);

				if($status !== "" && !isset($json["status"])){continue;}
				if($status !== "" && $status !== $json["status"]){continue;}

				$datas[] = $lists[$i];
			}
		}
		return $datas;
	}

	public static function getPageInfoFromPath($path){
		if(!is_file($path)){return;}

		$datas = array();
		if(is_file($path)){
			$datas = json_decode(file_get_contents($path),true);
		}
		return $datas;
	}

	public static function getGroupNameCount($group_id=""){
		if($group_id===""){return;}

		// search article
		$articles = self::getArticleLists();

		// group-name-check
		$count = 0;
		for($i=0,$c=count($articles); $i<$c; $i++){
			$page_id = preg_replace("/\.info$/","",$articles[$i]);
			$info = self::getPageInfo($page_id);
			$groups = explode(",",$info["group"]);
			for($j=0,$c2=count($groups); $j<$c2; $j++){
				if($groups[$j] === $group_id){
					$count++;
					break;
				}
			}
		}
		return $count;
	}
}
