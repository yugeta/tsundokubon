<?php

class BOOK_BASE64{
  public static function getBase64($dir,$file,$num){
    $dir  = (preg_match("/\/$/",$dir))? $dir : $dir."/";
    // $file = (isset($_REQUEST["file"]) && $_REQUEST["file"])? $_REQUEST["file"] : "";
    $num  = (!$num)? 0 : $num;

    $expandPath = BOOK_COMMON::getConfig();
    $bookDir    = BOOK_COMMON::getExpandPath($dir.$file);
    $bookPath   = $expandPath["default"]["expand"].$bookDir;
    $files = scandir($bookPath);

    // echo $bookPath."<br>".PHP_EOL;
    // var_dump($files);

    // echo $expandPath["default"]["expand"]."<br>".PHP_EOL;
    // echo BOOK_COMMON::getExpandPath($_REQUEST["dir"])."<br>".PHP_EOL;
    $no = 0;
    $path = "";
    for($i=0,$c=count($files); $i<$c; $i++){
      if(preg_match("/^\./",$files[$i])){continue;}
      if(!preg_match("/\.jpg$/",$files[$i])){continue;}
      if($no == $num){
        $path = $files[$i];
        break;
      }
      $no++;
    }

    if(!is_file($bookPath."/".$path) || is_dir($bookPath."/".$path)){
      exit();
    }

    // log
    BOOK::viewBook_log($dir,$file,$num);

    // echo $bookPath."/".$file;
    return base64_encode(file_get_contents($bookPath."/".$path));
  }
}
