<?php

class MYNT_URL{

	//port + domain [http://hoge.com:8800/]
	//現在のポートの取得（80 , 443 , その他）
	public static function getSite(){
		//通常のhttp処理
		if($_SERVER['SERVER_PORT']==80){
			$site = 'http://'.$_SERVER['HTTP_HOST'];
		}
		//httpsページ処理
		else if($_SERVER['SERVER_PORT']==443){
			$site = 'https://'.$_SERVER['HTTP_HOST'];
		}
		//その他ペート処理
		else{
			$site = 'http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'];
		}

		return $site;
	}

	//現在ページのサービスroot階層のパスを返す
	public static function getDir(){

		$uri = self::getSite();
		$req = explode('?',$_SERVER['REQUEST_URI']);

		return $uri.dirname($req[0]." ")."/";
	}

	//現在のクエリ無しパスを返す
	public static function getUrl(){
		$uri = self::getSite();
		$req = explode('?',$_SERVER['REQUEST_URI']);
		$uri.= $req[0];
		return $uri;
	}

	//フルパスを返す
	public static function getUri(){
		$uri = self::getSite();
		if($_SERVER['REQUEST_URI']){
			$uri.= $_SERVER['REQUEST_URI'];
		}
		else{
			$uri = self::getUrl.(($_SERVER['QUERY_STRING'])?"?".$_SERVER['QUERY_STRING']:"");
		}
		return $uri;
	}

	//基本ドメインを返す
	public static function getDomain(){
		return $_SERVER['HTTP_HOST'];
	}

	//リダイレクト処理
	public static function setUrl($url){
		if(!$url){return;}
		header("Location: ".$url);
	}

	//
	public static function getDesignRoot(){
		return self::getDir()."design/".$GLOBALS["config"]["design"]["target"]."/";
	}
	public static function getLibraryRoot(){
		return self::getDir()."library/";
	}
	public static function getPluginRoot(){
		return self::getDir()."plugin/";
	}
	public static function getDataRoot(){
		return self::getDir()."data/";
	}
	public static function getSystemRoot(){
		return self::getDir()."system/";
	}

	public static function getLocalDir(){
		$req = explode('?',$_SERVER['REQUEST_URI']);
		return dirname($req[0]." ")."/";
	}
	public static function getLocalFilename(){
		return $_SERVER['SCRIPT_NAME'];
	}

	// public static function getPathInfo($url){
	// 	$info = pathInfo($url);echo json_encode($info).PHP_EOL;
	// 	// $querys = explode("?",$url);
	// 	// $info["query"] = (count($querys)>=2)?$querys[1]:"";
	// 	$info["basename"] = ($info["basename"] === ".")?"":$info["basename"];
	// 	return $info;
	// }

	// 最終階層の文字列を取得
	public static function getBasename($url){
		$sp = explode("/",$url);
		return $sp[count($sp)-1];
	}

}
