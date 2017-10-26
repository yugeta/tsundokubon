<?php

class MYNT_PLUGIN_pageAxcel{
	public static function set(){
		if(!isset($_REQUEST["b"]) || !$_REQUEST["b"]){
			// $v = file_get_contents("plugin/axcel/config/version");
			// $json = json_decode(file_get_contents("plugin/pageAxcel/config/default.json"),true);
			$version = $GLOBALS["plugin"]["pageAxcel"]["version"];
			return "<script type='text/javascript' src='plugin/pageAxcel/js/axcel.js?".$version."'></script>";
		}
		else{
			return "";
		}
	}
}
