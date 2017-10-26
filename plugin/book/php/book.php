<?php

class BOOK{

  public static function viewBook($dir, $bookFile, $pageNumber=0){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}
    if(!$pageNumber){$pageNumber=0;}

    $config = MYNT::loadConfig(BOOK_COMMON::$config);
    $path = $config["default"]["expand"].BOOK_COMMON::getExpandPath($dir.$bookFile);
    if(!is_dir($path)){return;}
    if(!preg_match("/\/$/",$path)){$path .= "/";}

    $files = scanDir($path);
    $datas = array();
    $pageMaxCount = 0;

    for($i=0,$c=count($files); $i<$c; $i++){
      if(!preg_match("/^\./",$files[$i])){
        array_push($datas,$files[$i]);
        $pageMaxCount++;
      }
    }

    if($pageNumber===""){$pageNumber=0;}
    else if($pageNumber > $pageMaxCount-1){$pageNumber = $pageMaxCount-1;}
    if(count($datas) < $pageNumber){return "Bad page-number !!";}
    // return $path.$datas[$pageNumber];
    if(!is_file($path.$datas[$pageNumber])){return "Not found page-number !! [ ".$path.$datas[$pageNumber]." ]";}
    // return $path.$datas[$pageNumber];
    // return is_file($path.$datas[$pageNumber]);
    return '<img class="view-page" src="'.$path.$datas[$pageNumber].'" data-num="'.$pageNumber.'" data-num-max="'.$pageMaxCount.'" data-dir="'.$dir.'" data-file="'.$bookFile.'">';
  }

  public static function viewBook_log($dir, $bookFile, $pageNumber=0){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}
    $logPath = "data/book/log/";
    if(!is_dir($logPath)){mkdir($logPath,0777,true);}
    $logFile = BOOK_COMMON::getExpandPath($dir.$bookFile).".log";
    // $logFile = "bookView.log";

    if(!$pageNumber){$pageNumber=0;}

    $recordArr = array(date("YmdHis") , self::changeCsvDelim($dir) , self::changeCsvDelim($bookFile) , $pageNumber);
    $record = join(",",$recordArr).PHP_EOL;
    file_put_contents($logPath.$logFile,$record,FILE_APPEND);
  }
  public static function changeCsvDelim($str){
    return str_replace(",","&#44;",$str);
  }

  // 本を最後に読んだページを取得
  public static function getPagenumberLastBookView($dir, $bookFile){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}
    $logPath = "data/book/log/";
    if(!is_dir($logPath)){return;}
    $logFile = BOOK_COMMON::getExpandPath($dir.$bookFile).".log";
    // $logFile = "bookView.log";
    if(!is_file($logPath.$logFile)){return;}

    // $cmd = "grep '". self::changeCsvDelim($dir) .",". self::changeCsvDelim($bookFile). "' ".$logPath.$logFile;
    // return $cmd;
    // exec($cmd,$res);
    $logDatas = explode(PHP_EOL,file_get_contents($logPath.$logFile));
    // return count($logDatas);

    $last_line = $logDatas[count($logDatas)-2];
    // return $last_line;
    $sp = explode(",",$last_line);
    return $sp[3];
  }

  // 本の総ページ数を取得
  public static function getBookPageNumber($dir, $bookFile, $pageNumber=0){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}
    if($pageNumber===""){$pageNumber=0;}
    $config = MYNT::loadConfig(BOOK_COMMON::$config);
    $path = $config["default"]["expand"].BOOK_COMMON::getExpandPath($dir.$bookFile)."/";
    $files = scanDir($path);
    $datas = array();
    for($i=0,$c=count($files); $i<$c; $i++){
      if(!preg_match("/^\./",$files[$i])){
        array_push($datas,$files[$i]);
      }
    }
    return ($pageNumber+1) ."/". count($datas)." (".$datas[$pageNumber].")";
  }

  public static function getExpand2book($expandDir){
    $config = MYNT::loadConfig(BOOK_COMMON::$config);
    $basePath = str_replace(BOOK_COMMON::$pathSplitString,"/",$expandDir);
    // $basePath = str_replace(MYNT::$pathPipeString,MYNT::$pathSplitString,$expandDir);
    // return $config["shelf"]["dir"].$basePath;
    if(!is_file($config["shelf"]["dir"].$basePath)){
      $basePath = "";
    }

    return $basePath;
  }

}
