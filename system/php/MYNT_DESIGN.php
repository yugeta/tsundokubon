<?php

class MYNT_DESIGN{
  public function setDataUpload(){
    // print_r($_POST["design"]);exit();

		$data = $_POST["design"];
		$data["update"] = date("YmdHis");
    $json = json_encode($data , JSON_PRETTY_PRINT);
    $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
    $json = str_replace("\\/","/",$json);
    // echo $json;exit();

    file_put_contents("data/config/design.json" , $json);
    MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]);
  }

  // public function getConfigData($file , $key){
  //   $path = "data/config/".$file.".json";
  //   if(!is_file($path)){return;}
  //
  //   $json = json_decode(file_get_contents($path) , true);
  //
  //   if(isset($json[$key])){
  //     return $json[$key];
  //   }
  // }

  public function viewDesignLists(){
		$dir = "design/";
    $dirs = scandir($dir);
    $html = "";
    for($i=0,$c=count($dirs); $i<$c; $i++){
      if($dirs[$i] == "." || $dirs[$i] == ".." || !is_dir($dir.$dirs[$i])){continue;}
      $default = self::getDesignExplain($dirs[$i]);
      // if(!isset($default["flg"]) || $default["flg"] === "false" || $default["flg"] === "1"){continue;}
			if(isset($default["flg"]) && $default["flg"] === "1"){continue;}
			if(!is_file($dir.$dirs[$i]."/template.html")){continue;}
      $name    = (isset($default["name"]))?$default["name"]:$dirs[$i];
      // $explane = (isset($default["explane"]))?" : <span class='plugin-explane'>".$default["explane"]."</span>":"";
      $selected = ($GLOBALS["config"]["design"]["target"] === $dirs[$i])?"selected":"";
      $html .= "<option value='".$dirs[$i]."' ".$selected."> ".$name."</option>".PHP_EOL;
    }
    return $html;
  }

  public function getDesignExplain($design="",$key=""){
    if($design===""){return;}
    if(!is_dir("design/".$design)){return;}
    if(!is_file("design/".$design."/config/default.json")){return;}
    $json = json_decode(file_get_contents("design/".$design."/config/default.json"),true);
    if($key!=="" && isset($json[$key])){
      return $json[$key];
    }
    else{
      return $json;
    }
  }
}
