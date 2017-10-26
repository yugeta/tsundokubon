<?php

class MYNT_CONFIG{
  public function setDataUpload(){
		$data = $_POST["page"];
		$data["update"] = date("YmdHis");
    $json = json_encode($data , JSON_PRETTY_PRINT);
    $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);
    $json = str_replace("\\/","/",$json);
    file_put_contents("data/config/default.json" , $json);
    MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]);
  }

  public function getConfigData($key){
    $path = "data/config/default.json";
    if(!is_file($path)){return;}

    $json = json_decode(file_get_contents($path) , true);

    if(isset($json[$key])){
      return $json[$key];
    }
  }
}
