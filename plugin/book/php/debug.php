<?php

class BOOK_DEBUG{
  public static function viewArchive($dir="",$file=""){
    //return $dir." ~ ".$file;
    $config = MYNT::loadConfig(BOOK_COMMON::$config);
    $path = $config["shelf"]["dir"].$dir.$file;
    // return $path;
    if(is_file($path)){
      $cmd = "lsar -l '".$path."'|sort";
      exec($cmd,$res);
      return join(PHP_EOL,$res);
    }
  }
}
