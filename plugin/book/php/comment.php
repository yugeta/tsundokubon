<?php

class BOOK_COMMENT{
  public static function setData($dir,$file,$num,$comment){
    if(!preg_match("/\/$/",$dir)){$dir.="/";}

    // config
    $config = BOOK_COMMON::getConfig();

    // save-dir
    if(!is_dir($config["default"]["comment"])){
      mkdir($config["default"]["comment"],0777,true);
    }

    // save-file
    $saveFile = BOOK_COMMON::getExpandPath($dir.$file).".json";

    // data-make
    $data = array();
    $data["date"] = date("YmdHis");
    $data["dir"] = $dir;
    $data["file"] = $file;
    $data["num"] = $num;
    $data["comment"] = $comment;

    // json-conv
    $json = json_encode($data);
    // $json = json_encode($data,JSON_PRETTY_PRINT);
    $json = preg_replace_callback('/\\\\u([0-9a-zA-Z]{4})/', function ($matches) {return mb_convert_encoding(pack('H*',$matches[1]),'UTF-8','UTF-16');},$json);

    // save
    file_put_contents($config["default"]["comment"].$saveFile , $json.PHP_EOL , FILE_APPEND);

    return "saved";
  }

  public static function getData($dir,$file,$num){
    if(!preg_match("/\/$/",$dir)){$dir.="/";}

    // config
    $config = BOOK_COMMON::getConfig();

    // save-dir
    if(!is_dir($config["default"]["comment"])){return;}

    // save-file
    $saveFile = BOOK_COMMON::getExpandPath($dir.$file).".json";

    $path = $config["default"]["comment"].$saveFile;

    $datas = explode(PHP_EOL , file_get_contents($path));

    for($i=count($datas)-1; $i>=0; $i--){
      $json = json_decode($datas[$i],true);
      if($json["dir"] === $dir
      && $json["file"] === $file
      && $json["num"] === $num){
        return $json["comment"];
      }
    }
  }
}
