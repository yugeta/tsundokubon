<?php

class BOOK_DIR{
  public static function getPreviousDir($dir="",$file=""){
    if(!$dir){return "";}
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    if($file !== ""){
      return $dir;
    }
    else{
      $sp = explode("/",$dir);
      if(count($sp) < 2){return "";}
      array_pop($sp);
      array_pop($sp);
      return join("/",$sp);
    }

  }
}
