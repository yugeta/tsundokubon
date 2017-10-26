<?php

class MYNT_PLUGIN{
  public function setDataUpload(){
    // print_r($_POST["plugin"]);exit();
		$data = $_POST["plugin"];
		$data["update"] = date("YmdHis");

    $json = json_encode($data , JSON_PRETTY_PRINT);
    $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
    $json = str_replace("\\/","/",$json);
    // echo $json;exit();

    file_put_contents("data/config/plugin.json" , $json);
    MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]);
  }

  public function getConfigData($file , $key){
    $path = "data/config/".$file.".json";
    if(!is_file($path)){return;}

    $json = json_decode(file_get_contents($path) , true);

    if(isset($json[$key])){
      return $json[$key];
    }
  }

  public function viewPluginLists(){
    $dirs = scandir("plugin");
    $html = "";
    for($i=0,$c=count($dirs); $i<$c; $i++){
      if($dirs[$i] == "." || $dirs[$i] == ".." || !is_dir("plugin/".$dirs[$i])){continue;}
      $default = self::getPluginExplain($dirs[$i]);
      $name    = (isset($default["name"]))?$default["name"]." [".$dirs[$i]."]":$dirs[$i];
      $explane = (isset($default["explane"]))?" : <span class='plugin-explane'>".$default["explane"]."</span>":"";
      $checked = (self::checkPluginConfigExists($dirs[$i]))?"checked":"";
      $html .= "<label class='plugin-lists'><input type='checkbox' name='plugin[target][]' value='".$dirs[$i]."' ".$checked."> ".$name.$explane."</label>".PHP_EOL;
    }
    return $html;
  }
  public function checkPluginConfigExists($pluginDirName){
    $plugins = $GLOBALS["config"]["plugin"]["target"];
    for($i=0,$c=count($plugins); $i<$c; $i++){
      if($plugins[$i] == $pluginDirName){return true;}
    }
    return false;
  }
  public function getPluginExplain($plugin="",$key=""){
    if($plugin===""){return;}
    if(!is_dir("plugin/".$plugin)){return;}
    if(!is_file("plugin/".$plugin."/config/default.json")){return;}
    $json = json_decode(file_get_contents("plugin/".$plugin."/config/default.json"),true);
    if($key!=="" && isset($json[$key])){
      return $json[$key];
    }
    else{
      return $json;
    }
  }
}
