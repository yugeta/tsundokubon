<?php

class MYNT_LOGIN{

	/** Logout **/

	public static function setLogout(){
		self::checkLogout();
	}

	public static function checkLogout(){

		//セッション情報を削除
		foreach($_SESSION as $key=>$val){
			unset($_SESSION[$key]);
		}

		//リダイレクト
		header("Location: ".MYNT_URL::getURL()."?p=login");

		exit();
	}



	// Login
	public static function viewLogin(){
		$_REQUEST["contentsPath"] = "plugin/login/html/login.html";
	}

	// Authorize
	public static function checkAuth(){
		if(isset($_SESSION["login_id"]) && $_SESSION["login_id"]!==""){
			return true;
		}
		else{
			return false;
		}
	}


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




	/** account **/

	public static function checkAccountAdd(){

	}
	public static function setAccountAdd(){
		if(!isset($_REQUEST["account_id"]) || !isset($_REQUEST["account_pw"])){return;}

		$account_id = $_REQUEST["account_id"];
		$account_pw = $_REQUEST["account_pw"];

		//die($account_id."/".$account_pw);

		// check accountid->mail-address
		$mail_exp = "|^[0-9a-z_./?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$|";
		if(!preg_match($mail_exp , $account_id) || !$account_id){
			die("Not mail-address !");
		}
		if(!$account_pw){
			die("Not regist password !");
		}
		//重複IDチェック
		if(self::checkRefgistedAccountID($account_id)){
			die("Registed account ! : ".$account_id);
		}

		self::setAccountAdd_proc($account_id , $account_pw);
	}

	public static function checkRefgistedAccountID($account_id=""){
		if($account_id===""){return;}
		$dir  = "data/config/";
		if(!is_dir($dir)){return false;}
		$fl   = "users.json";
		$jsons = explode("\n",file_get_contents($dir.$fl));
		for($i=0,$c=count($jsons); $i<$c; $i++){
			$json = json_decode($jsons[$i] , true);
			if($json["id"] === $account_id){return true;}
		}
		return false;
	}

	public static function setAccountAdd_proc($account_id , $account_pw){
		$dir  = "data/config/";
		if(!is_dir($dir)){mkdir($dir,0777,true);}
		$fl   = "users.json";
		$data = array();
		$data["flg"] = "0";
		$data["id"]  = $account_id;
		$data["md5"] = md5($account_pw);
		$data["update"] = date("YmdHis");
		$json = json_encode($data);
		file_put_contents($dir.$fl , $json.PHP_EOL , FILE_APPEND);
		header("Location: ".MYNT_URL::getURL()."?plugin=login&html=account_add_finish");
	}

	// public static function sendMailProc(){
	// 	//仮登録(provisional ragistration)
	// 	$dir = "data/provosopnal/";
	// 	if(!is_dir($dir)){
	// 		mkdir($dir,0777,true);
	// 	}
	// 	$d    = date("YmdHis");
	// 	$md5  = md5($d);
	// 	$data = $d.",".$account_id.",".md5($account_pw).",".md5($d);
	// 	file_put_contents($dir.date("Ym").".dat" , $data.PHP_EOL, FILE_APPEND);
	//
	// 	// mail-sent
	// 	$sub    = "Confirmation of registration";
	// 	$msg    = MYNT_URL::getUrl()."?".$md5;
	// 	$header = "From:test@hoge.com"."\r\n";
	//
	// 	mb_send_mail($account_id , $sub , $msg , $header);
	//
	// 	// send
	// 	header("Location: ".MYNT_URL::getURL()."?plugin=login&html=mailSend");
	// }




	// b=system(base)は、ログイン済みでないといけない。（但しloginは対象外）
	public static function checkSystemBase(){
		// if(!isset($_REQUEST["b"]) || $_REQUEST["b"] !== "system"){return;}
		// if($_REQUEST["b"] === "system" && isset($_REQUEST["p"]) && $_REQUEST["p"] === "login"){return;}
		if(isset($_SESSION["login_id"])){return;}

		// セッションがない場合は、トップページへ
		$MYNT_URL = new MYNT_URL;
		$MYNT_URL->setUrl($MYNT_URL->getUrl());
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



	// //セッションデータ保持
	// public static function setSessionData($id=""){
	//
	// 	if(!$id){return;}
	//
	// 	$account = new ACCOUNT();
	// 	$_SESSION['pass_data'] = $account->getPassData($id);
	// 	$_SESSION['user_data'] = $account->getUserData($id);
	//
	// }
	// public static function delSessionData(){
	// 	unset($_SESSION['no']);
	// 	unset($_SESSION['id']);
	// 	unset($_SESSION['name']);
	// 	unset($_SESSION['mail']);
	// 	unset($_SESSION['service']);
	// 	unset($_SESSION['auth']);
	// 	unset($_SESSION['img']);
	// }

	public static function viewPageLogin(){
		$page = new MYNT_PAGE;
		$source = "";
		if(!isset($_SESSION["login_id"]) || !$_SESSION["login_id"]){
			$source = $page->getPageSource("data/page/source/login.dat");
		}
		else if(isset($_REQUEST["p"]) && $_REQUEST["p"]){
			$source = $page->getPageSource("data/page/source/".$_REQUEST["p"].".dat");
		}
		else{
			$top = (isset($GLOBALS["config"]["page"]["top"]))?$GLOBALS["config"]["page"]["top"]:"top";
			$source = $page->getPageSource("data/page/source/".$top.".dat");
		}
		return $page->changePageNewLine($source);
	}

}
