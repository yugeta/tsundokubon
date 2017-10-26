<?php
/**
 * Mynt Studio
 * MakingDate : 2017.09.07
 * Auther @ Yugeta Koji (MYNT Inc.)
 * Summery : WebSiteFrameWork (WSFW)
 * Module : admin.php
 */

class MYNT{

	/** Config **/

	// Load-Config
	public static function loadConfig($dir = "data/config/"){
		$data  = array();
		$jsons = scandir($dir);
		for($j=0, $d=count($jsons); $j<$d; $j++){
			if(!preg_match("/(.+?)\.json$/", $jsons[$j], $match)){continue;}
			$config = json_decode(file_get_contents($dir.$jsons[$j]),true);
			if(isset($config["flg"]) && $config["flg"] === "1"){continue;}
			$data[$match[1]] = $config;
		}
		return $data;
	}


	/** Session **/

	// Start-Session
	public static function startSession(){
		if(isset($GLOBALS["config"]["default"]["session"])){
			session_name($GLOBALS["config"]["default"]["session"]);
			session_start();
		}
		else{
			die("not session-name.");
		}
	}

	public static function checkSession($callBackUrl="?p=login"){
		if(!isset($_SESSION["login_id"])){
			MYNT_URL::setUrl(MYNT_URL::getUrl().$callBackUrl);
		}
	}


	/** Plugins **/

	// Load-Plugins
	public static function loadPlugins($dir = "plugin/"){
		if(!isset($GLOBALS["config"]["plugin"]["target"])){return;}
		$lists = $GLOBALS["config"]["plugin"]["target"];
		for($i=0,$c=count($lists); $i<$c; $i++){
			$path = $dir . $lists[$i] ."/php/";
			if(!is_dir($path)){continue;}
			self::loadPHPs($path);
		}
	}
	// Load-PHP-Module
	public static function loadPHPs($dir=""){

   // Check
		if($dir==="" || !is_dir($dir)){return;}

   // Check-Directory-last-string
		if(!preg_match("/\/$/",$dir)){
			$dir .= "/";
		}

   // Load-Directory-inner-files
		$files = scandir($dir);
		for($i=0; $i<count($files); $i++){
			if($files[$i] == "." || $files[$i] == ".." || !preg_match("/\.php$/",$files[$i])){continue;}
			require_once $dir.$files[$i];
		}
	}


	public static function getDesignPath(){
		return "design/".$GLOBALS["config"]["design"]["target"]."/";
	}

	/** Method **/

	// Check Method
	public static function checkMethod(){
		//security


		// method [ class / function ] *POST only
		if(isset($_POST["method"]) && count(explode("/",$_POST["method"])) === 2){
			$sp = explode("/",$_POST["method"]);
			if(method_exists($sp[0],$sp[1])){
				call_user_func_array(array($sp[0] , $sp[1]),array());
			}
		}
	}

	/** Template **/

	// View-Error
	public static function viewError($msg){
		echo "<h1>".$msg."</h1>";
		exit();
	}

	// View-Base (query-check -> get-base)
	public static function viewTemplate($templatePath = ""){
		// path
		$templatePath = ($templatePath !== "")?$templatePath:self::getDesignPath()."template.html";

		// check
		$templatePath = (is_file($templatePath))?$templatePath:"lib/html/404.html";

		// load-template
		$source = file_get_contents($templatePath);

		// convert-strings
		echo self::conv($source);
	}


	// Convert-HTML-Source
	public static function conv($source = ""){
		// $MYNT_SOURCE = new MYNT_SOURCE;
		return MYNT_TAG::rep($source);
		// echo $source;
	}

	/**
	* Contents
	* 1. ? blog=** / default=** / system=** / etc=**
	* 2. ?b=**&p=** (data/page/base/page.html)
	*/
	public static function viewContents($page = ""){

		$htmlPath = "";

		// top
		if($page === ""){
			if(isset($GLOBALS["config"]["default"]["path-top"])){
				$htmlPath = $GLOBALS["config"]["default"]["path-top"];
			}
		}

		// page
		else if($page !== "" && is_file("data/page/".$page.".html")){
			$htmlPath = "data/page/".$page.".html";
		}

		//error
		if($htmlPath === "" || !is_file($htmlPath)){
			$htmlPath = "lib/html/404.html";
		}

		// load-data
		return self::conv(file_get_contents($htmlPath));
	}

	// // admin
	// public static function viewContents_admin(){
	//
  // // get-page-filename
	// 	$htmlPath = isset($_REQUEST["p"])?"admin/html/".$_REQUEST["p"].".html":"admin/html/top.html";
	//
	// 	//error
	// 	$htmlPath = (is_file($htmlPath))?$htmlPath:"plugin/system/html/404.html";
	//
	// 	// load-data
	// 	return self::conv(file_get_contents($htmlPath));
	// }

	public static function viewContents_system($dir = ""){

		$dir = ($dir !== "" && is_dir($dir))?$dir:"data/page/";

		$htmlPath = "";

		// un-login
		if(!isset($_SESSION["login_id"])){
			if(isset($_REQUEST["p"]) && $_REQUEST["p"] === "login_error"){
				$htmlPath = $dir."login_error.html";
			}
			else{
				$htmlPath = $dir."login.html";
			}

		}

		// top
		else if(!isset($_REQUEST["p"])){
			if(isset($GLOBALS["config"]["system"]["setting"]["top"])){
				$htmlPath = $dir.$GLOBALS["config"]["system"]["setting"]["top"].".html";
			}
			else{
				$htmlPath = $dir."top.html";
			}
		}

		// page
		else if(isset($_REQUEST["p"]) && is_file($dir.$_REQUEST["p"].".html")){
			$htmlPath = $dir.$_REQUEST["p"].".html";
		}

		//error
		else{
			$htmlPath = "lib/html/404.html";
		}

		// load-data
		return self::conv(file_get_contents($htmlPath));
	}

	/** login **/

	//Login-check
	public static function checkLogin(){

		// success
		if(self::setLogin($_POST["login_id"] , $_POST["login_pw"]) === true){
			$_SESSION["login_id"] = $_POST["login_id"];
			// header("Location: ".MYNT_URL::getURL()."?".$_SERVER['QUERY_STRING']);
			header("Location: ".MYNT_URL::getURL());
		}

		//fault
		else{
			header("Location: ".MYNT_URL::getURL()."?p=login_error");
		}

	}

	public static function setLogin($id="",$pw=""){

		if($id==="" || $pw===""){return;}

		//-----
		//DB検索
		//-----

		$data = false;

		//mysql
		if($GLOBALS['config']['define']['database_type']=='mysql'){
			$data = self::checkLogin_mysql($id,$pw);
			// if(!$data){return;}
		}
		//mongodb
		else if($GLOBALS['config']['define']['database_type']=='mongodb'){
			$data = self::checkLogin_mongodb($id,$pw);
			// if(!$data){return;}
		}
		//couched
		else if($GLOBALS['config']['define']['database_type']=='couchdb'){
			$data = self::checkLogin_couchdb($$id,$pw);
			// if(!$data){return;}
		}
		//file (data/)
		else{
			$data = self::checkLogin_file($id,$pw);
			// $data = self::checkLogin_file($id,$pw);
			// if(!$data){return;}
		}

		if($data === true){
			$_SESSION["login_id"] = $id;
		}

		return $data;
	}

	/*----------
	 ログイン DataBase Check
	----------*/

	public static function checkLogin_mysql($id,$pw){
		return false;
	}
	public static function checkLogin_mongodb($id,$pw){
		return false;
	}
	public static function checkLogin_couchdb($id,$pw){
		return false;
	}
	public static function checkLogin_file($id="",$pw=""){

		if($id==="" || $pw===""){
			return false;
		}

		// $regist = new SYSTEM_REGIST();
		$passwdFile = "data/config/users.json";

		if(!file_exists($passwdFile)){return;}

		//ユーザーデータ読み込み
		$data_users = explode("\n",file_get_contents($passwdFile));

		//unset($pw_data,$buf);

		//データ内のライン処理
		// [ 0:unique-id 1:delete-flg 2:service 3:user-id 4:password]
		$loginFlg = false;
		for($i=count($data_users)-1;$i>=0;$i--){
			$data_users[$i] = str_replace("\r","",$data_users[$i]);
			if(!$data_users[$i]){continue;}

			//ラインの文字列を分解
			$json = json_decode($data_users[$i],true);

			//アカウント判別
			if($json["id"]===$id){

				if(isset($json["flg"]) && $json["flg"]==="1"){
					break;
				}

				//論理削除フラグフラグ->on
				if($json["md5"] === md5($pw)){
					$loginFlg = true;
					break;
				}

				//パスワード保持->通常ログイン
				break;
			}
		}
		return $loginFlg;
	}


	public static function setActiveMenu($key = ""){
		if(isset($_REQUEST["p"]) && $_REQUEST["p"] == $key){
			return "active";
		}
		else if(!isset($_REQUEST["p"]) && $key === ""){
			return "active";
		}
	}

	public static function currentTime(){
		return date("YmdHis");
	}

	public static function viewMenu_nav($url = ""){
		if(!isset($GLOBALS["config"]["menu"]["nav"])){return;}

		$tmpFile1 = "lib/html/nav_ul.html";
		if(!is_file($tmpFile1)){return;}
		$tmpSource1 = file_get_contents($tmpFile1);
		$tmpSource1 = str_replace("<%className_ul%>",   $GLOBALS["config"]["menu"]["nav"]["className_ul"],   $tmpSource1);

		$tmpFile2 = "lib/html/nav_li.html";
		if(!is_file($tmpFile2)){return;}
		$tmpSource2 = file_get_contents($tmpFile2);

		$menus  = $GLOBALS["config"]["menu"]["nav"]["lists"];
		// $design = $GLOBALS["config"]["design"]["target"];
		$currentUrl = MYNT_URL::getUri();
		$currentUrlBasename = MYNT_URL::getBasename($currentUrl);

		$html = "";
		for($i=0,$c=count($menus); $i<$c; $i++){
			$tmp = "";
			// file
			if(isset($menus[$i]["file"]) && $menus[$i]["file"]){
				$tmp = self::conv(file_get_contents($menus[$i]["file"]));
			}
			//link
			else if(isset($menus[$i]["link"]) && $menus[$i]["link"]){
				$linkUrlBasename = MYNT_URL::getBasename($menus[$i]["link"]);
				$tmp = $tmpSource2;
				$tmp = str_replace("<%link%>",   $menus[$i]["link"],   $tmp);
				$tmp = str_replace("<%text%>",   $menus[$i]["text"],   $tmp);
				$tmp = str_replace("<%className_li%>",   $GLOBALS["config"]["menu"]["nav"]["className_li"],   $tmp);
				$tmp = str_replace("<%className_a%>",    $GLOBALS["config"]["menu"]["nav"]["className_a"],   $tmp);

				// active-check
				$active = "";
				// full-path
				if(preg_match("/^[http|https]:\/\//",$menus[$i]["link"])){
					if($currentUrl === $menus[$i]["link"]){
						$active = $GLOBALS["config"]["menu"]["nav"]["activeString"];
					}
				}
				// query
				else{
					if($currentUrlBasename === $linkUrlBasename){
						$active = $GLOBALS["config"]["menu"]["nav"]["activeString"];
					}
				}
				$tmp = str_replace("<%active_li%>", $active, $tmp);
				$tmp = str_replace("<%active_a%>",  $active, $tmp);
			}
			// add-source
			$html .= $tmp.PHP_EOL;
		}
		return MYNT::conv(str_replace("<%lists%>", $html, $tmpSource1));
	}

	public static function viewMenu_footer($url = ""){
		if(!isset($GLOBALS["config"]["menu"]["footer"])){return;}

		$tmpFile1 = "lib/html/footer_ul.html";
		if(!is_file($tmpFile1)){return;}
		$tmpSource1 = file_get_contents($tmpFile1);
		$tmpSource1 = str_replace("<%className_ul%>",   $GLOBALS["config"]["menu"]["footer"]["className_ul"],   $tmpSource1);

		$tmpFile2 = "lib/html/footer_li.html";
		if(!is_file($tmpFile2)){return;}
		$tmpSource2 = file_get_contents($tmpFile2);

		$menus  = $GLOBALS["config"]["menu"]["footer"]["lists"];
		// $design = $GLOBALS["config"]["design"]["target"];
		$currentUrl = MYNT_URL::getUri();
		$currentUrlBasename = MYNT_URL::getBasename($currentUrl);

		$html = "";
		for($i=0,$c=count($menus); $i<$c; $i++){
			$tmp = "";
			// file
			if(isset($menus[$i]["file"]) && $menus[$i]["file"]){
				$tmp = self::conv(file_get_contents($menus[$i]["file"]));
			}
			//link
			else if(isset($menus[$i]["link"]) && $menus[$i]["link"]){
				$linkUrlBasename = MYNT_URL::getBasename($menus[$i]["link"]);
				$tmp = $tmpSource2;
				$tmp = str_replace("<%link%>",   $menus[$i]["link"],   $tmp);
				$tmp = str_replace("<%text%>",   $menus[$i]["text"],   $tmp);
				$tmp = str_replace("<%className_li%>",   $GLOBALS["config"]["menu"]["footer"]["className_li"],   $tmp);
				$tmp = str_replace("<%className_a%>",    $GLOBALS["config"]["menu"]["footer"]["className_a"],   $tmp);

				// active-check
				$active = "";
				// full-path
				if(preg_match("/^[http|https]:\/\//",$menus[$i]["link"])){
					if($currentUrl === $menus[$i]["link"]){
						$active = $GLOBALS["config"]["menu"]["footer"]["activeString"];
					}
				}
				// query
				else{
					if($currentUrlBasename === $linkUrlBasename){
						$active = $GLOBALS["config"]["menu"]["footer"]["activeString"];
					}
				}
				$tmp = str_replace("<%active_li%>", $active, $tmp);
				$tmp = str_replace("<%active_a%>",  $active, $tmp);
			}
			// add-source
			$html .= $tmp.PHP_EOL;
		}
		return MYNT::conv(str_replace("<%lists%>", $html, $tmpSource1));
	}


}
