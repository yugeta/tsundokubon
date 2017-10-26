<?php

class BOOK_COMMON{

  public static $config = "data/book/config/";

  public static function getConfig(){
    return MYNT::loadConfig(self::$config);
  }

  public static function getPankuzu($dir=""){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}
    $sp = explode("/",$dir);
    $html = "";

    // top
    $html .= '<span class="pankuzu"><a href="./"> *Top </a> / </span>'.PHP_EOL;

    // dir-in
    $link = "";
    for($i=0,$c=count($sp); $i<$c; $i++){
      if(!$sp[$i]){continue;}

      // un-link (last-list)
      if($i === (count($sp)-2)){
        $html .= '<span class="pankuzu"> '.$sp[$i].' / </span>'.PHP_EOL;
      }
      // link
      else{
        $link .= $sp[$i]."/";
        // $html .= $i."=".count($sp).";";
        $html .= '<span class="pankuzu"><a href="?dir='.$link.'"> '.$sp[$i].'</a> / </span>'.PHP_EOL;
      }
    }

    return $html;
  }

  public static function getBookLists($dir=""){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    $config = MYNT::loadConfig(self::$config);

    $files = scandir($config["shelf"]["dir"].$dir);
    $html="";

    $bookList_a = file_get_contents("data/page/bookList_a.html");

    // Hierarchy - up
    if(isset($_REQUEST["dir"]) && $_REQUEST["dir"]){
      // $sp = explode("/",$dir);
      // array_pop($sp);
      // array_pop($sp);
      $tmp = $bookList_a;
      $tmp = str_replace("<%icon%>", $GLOBALS["config"]["icon"][$GLOBALS["config"]["design"]["target"]]["top"], $tmp);
      // $tmp = str_replace("<%dir%>" , "?dir=".join("/",$sp) , $tmp);
      $tmp = str_replace("<%dir%>" , "?dir=".BOOK_DIR::getPreviousDir($dir) , $tmp);
      $tmp = str_replace("<%text%>", "..", $tmp);
      $tmp = str_replace("<%lastPage%>", "" , $tmp);
      $html .= $tmp;
    }

    // Books
    for($i=0,$c=count($files); $i<$c; $i++){
      if(preg_match("/^\.(.*?)/", $files[$i])){continue;}
      $text = $files[$i];
      $lastPage = "";
      if(is_dir($config["shelf"]["dir"].$dir.$files[$i])){
        $cache = (self::checkIncludePath($config["default"]["expand"], $dir.$files[$i]))?"red":"";
        $link = "?dir=".$dir.$files[$i];
        $icon = $GLOBALS["config"]["icon"][$GLOBALS["config"]["design"]["target"]]["folder"];
      }
      else{
        // isCache
        $cache = (self::checkExpandPath($config["default"]["expand"], $dir.$files[$i]))?"red":"";
        $lastPage = BOOK::getPagenumberLastBookView($dir,$files[$i]);
        $link = "?p=book&dir=".$dir."&file=".$files[$i]."&num=".$lastPage;
        $icon = $GLOBALS["config"]["icon"][$GLOBALS["config"]["design"]["target"]]["book"];
      }
      $tmp = $bookList_a;
      $tmp = str_replace("<%exist%>", $cache, $tmp);
      $tmp = str_replace("<%icon%>", $icon, $tmp);
      $tmp = str_replace("<%dir%>" , $link, $tmp);
      $tmp = str_replace("<%text%>", $text, $tmp);
      $tmp = str_replace("<%lastPage%>", $lastPage , $tmp);

      $html .= $tmp;
    }
    return $html;
  }

  public static function getBookLists_recent(){
    $config = MYNT::loadConfig(self::$config);

    $expands = scandir($config["default"]["expand"]);
    $bookList_a = file_get_contents("data/page/bookList_a.html");

    $html = "";
    for($i=0,$c=count($expands); $i<$c; $i++){
      if(preg_match("/^\./",$expands[$i])){continue;}
      $path = BOOK::getExpand2book($expands[$i]);
      if($path === ""){
        $rmpath = $expands[$i];
        $cmd = 'rm -rf "'.$config["default"]["expand"].$rmpath.'"';
        exec($cmd);
        continue;
      }
      $pathinfo = pathinfo($path);
      $text = $pathinfo["basename"];
      $lastPage = BOOK::getPagenumberLastBookView($pathinfo["dirname"],$pathinfo["basename"]);
      $cache = "red";
      $link = "?p=book&dir=". $pathinfo["dirname"] ."&file=". $pathinfo["basename"] ."&num=".$lastPage;
      $icon = $GLOBALS["config"]["icon"][$GLOBALS["config"]["design"]["target"]]["book"];

      // $html .= $expands[$i]."<br>".PHP_EOL;
      $tmp = $bookList_a;
      $tmp = str_replace("<%exist%>", $cache, $tmp);
      $tmp = str_replace("<%icon%>", $icon, $tmp);
      $tmp = str_replace("<%dir%>" , $link, $tmp);
      $tmp = str_replace("<%text%>", $text, $tmp);
      $tmp = str_replace("<%lastPage%>", $lastPage , $tmp);

      $html .= $tmp;
    }
    return $html;
  }


  public static function setBookFile($dir = "",$targetFile = ""){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    if($targetFile === ""){return "Empty !!";}
    // $config = MYNT::loadConfig(self::$config);
    $config = self::getConfig();

    if(!is_file($config["shelf"]["dir"]. $dir.$targetFile)){return;}

    // data-exists
    if(self::checkExpandPath($config["default"]["expand"],$dir.$targetFile)){return;}

    $fileInfo = pathinfo($targetFile);
    // 拡張子
    $extension = mb_strtolower($fileInfo["extension"]);

    switch ($extension){
      case "zip":
        $data = self::setBookArchiveFile(array_merge($config["shelf"],$config["default"]), $dir, $targetFile);
        break;
      case "rar":
        $data = self::setBookArchiveFile(array_merge($config["shelf"],$config["default"]), $dir, $targetFile);
        break;
      case "pdf":
        $data = self::setBookArchiveFile_pdf(array_merge($config["shelf"],$config["default"]), $dir, $targetFile);
        break;
      case "tgz":
        $data = self::setBookFile_tgz(array_merge($config["shelf"],$config["default"]), $dir, $targetFile);
        break;
    }
    return $data;
  }

  // zip , rar
  public static function setBookArchiveFile($config, $dir, $targetFile){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    // get lists
    $datas = self::getArchiveInnerFileLists($config["dir"].$dir.$targetFile);
    if(!count($datas)){return;}

    $tempPath = $config["temp"].$targetFile;
    if(!is_dir($tempPath)){mkdir($tempPath,0777,true);}

    $cmd = 'unar -D -o "'.$tempPath.'" "'.$config["dir"].$dir.$targetFile.'"';
    exec($cmd);

    // jpeg move
    $expandPath = $config["expand"]. self::getExpandPath($dir.$targetFile);
    if(!is_dir($expandPath)){mkdir($expandPath, 0777, true);}

    // jpeg numbering
    $num = 0;
    for($i=0,$c=count($datas); $i<$c; $i++){
      if(!is_file($tempPath."/".$datas[$i])){continue;}
      if(is_dir($tempPath."/".$datas[$i])){continue;}
      $numStr = sprintf("%04d",$num);
      $movedFileName = $expandPath."/".$numStr.".jpg";
      if(preg_match('/^(.+?)\.(jpg|jpeg)$/i', $datas[$i])){
        rename($tempPath."/".$datas[$i] , $movedFileName);
        $num++;
      }
      else if(preg_match('/^(.+?)\.(png|bmp|tif|tiff|gif|tga|pic|pict|pct|psd)$/i', $datas[$i])){
        $cmd = 'convert "'.$tempPath.'/'.$datas[$i].'" "'.$movedFileName.'"';
        exec($cmd);
        $num++;
      }
      else{

      }
    }
    if($num===0){

    }
    else{
      // remove
      $cmd = 'rm -rf "'.$tempPath.'"';
      exec($cmd);
    }
  }

  public static function setBookArchiveFile_pdf($config, $dir, $targetFile){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    $targetPath = $config["dir"].$dir.$targetFile;

    // mkdir
    // $tempPath = $config["temp"].$targetFile;
    // if(!is_dir($tempPath)){mkdir($tempPath,0777,true);}
    $expandPath = $config["expand"]. self::getExpandPath($dir.$targetFile);
    if(!is_dir($expandPath)){mkdir($expandPath, 0777, true);}

    // - [ImageMagick]pds -> jpeg (expand)
    // $cmd = 'convert -geometry 100% -density 100% "'.$targetPath.'" "'.$expandPath.'/pdf.jpg"';
    // - [pdftoppm]
    // $cmd = "pdftoppm -jpeg '".$targetPath."' '".$tempPath."/pdf'";
    $cmd = "pdftoppm -jpeg '".$targetPath."' '".$expandPath."/pdf'";
    exec($cmd);

  }

  public static function setBookFile_tgz($config, $dir, $targetFile){
    return "tgz";
  }

  // -----
  // Lists

  public static function getArchiveInnerFileLists($path){
    // $path = str_replace("[","\\[",$path);
    $cmd = 'lsar "'.$path.'"|sort';
    exec($cmd , $res);

    // echo $cmd."<br>".PHP_EOL;
    // print_r($res);exit();

    $newLists = array();
    for($i=1,$c=count($res); $i<$c; $i++){
      if(preg_match("/\/$/",$res[$i])){continue;}
      array_push($newLists , $res[$i]);
    }
    return $newLists;
  }


  // expand path
  public static $pathSplitString = "&slash;";
  // public static $pathPipeString  = "&#124;";
  public static function getExpandPath($dir){
    if(preg_match("/\/$/",$dir)){
      $sp = explode("/",$dir);
      array_pop($sp);
      $dir = join("/",$sp);
    }
    // $str = str_replace($pathSplitString, self::$pathPipeString, $dir);
    $str = str_replace("/", self::$pathSplitString, $dir);
    return $str;
  }

  // 対象のBookがExpandに存在するか
  public static function checkExpandPath($expandPath,$dir){
    $path = $expandPath.self::getExpandPath($dir);
    if(is_dir($path)){
      exec('find "'.$path.'" -name "*.jpg"' , $files);
      if(count($files)>2){
        return true;
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
  }

  // 対象のDirがExpandに存在するか
  public static function checkIncludePath($expandPath,$dir){
    $dirPath  = self::getExpandPath($dir);
    $dirPath = str_replace("|","\|",$dirPath);
    $dirPath = str_replace("-","\-",$dirPath);
    $dirPath = str_replace("[","\[",$dirPath);
    $dirPath = str_replace("]","\]",$dirPath);
    $dirPath = str_replace(".","\.",$dirPath);
    $dirPath = str_replace("(","\(",$dirPath);
    $dirPath = str_replace(")","\)",$dirPath);
    $dirPath = str_replace("*","\*",$dirPath);
    $dirPath = str_replace(":","\:",$dirPath);
    $dirPath = str_replace("?","\?",$dirPath);
    $dirPath = str_replace("^","\^",$dirPath);
    $dirPath = str_replace("$","\$",$dirPath);
    $dirPath = str_replace("+","\+",$dirPath);
    $dirPath = str_replace("{","\{",$dirPath);
    $dirPath = str_replace("}","\}",$dirPath);
    // echo $expandPath. $dirPath."<br>".PHP_EOL;
    $expandFiles = scandir($expandPath);
    for($i=0,$c=count($expandFiles); $i<$c; $i++){
      if(!$expandFiles[$i]){continue;}
      if(preg_match("/^\./",$expandFiles[$i])){continue;}
      // echo $dirPath." ~ ".$expandFiles[$i]."<br>".PHP_EOL;
      // if(preg_match("/^(".$dirPath.")(.*?)$/",$expandFiles[$i],$match)){
      if(preg_match("/^".$dirPath."/",$expandFiles[$i],$match)){
        // echo $dirPath ." = ". $expandFiles[$i]."<br>".PHP_EOL;
        // echo join(" | ",$match)."<br>".PHP_EOL;
        return true;
      }
    }
  }

  // max book cache count
  public static function delBookCache($dir="",$targetFile=""){
    if($dir !== "" && !preg_match('/\/$/',$dir)){$dir.="/";}

    $config = MYNT::loadConfig(self::$config);
    $expandBookCacheCount = scandir($config["default"]["expand"]);
    $dirs = array();
    $times = array();
    for($i=0,$c=count($expandBookCacheCount); $i<$c; $i++){
      if(preg_match("/^\./",$expandBookCacheCount[$i])){continue;}
      if(!$expandBookCacheCount[$i]){continue;}
      $path = $config["default"]["expand"].$expandBookCacheCount[$i];
      $tm = filemtime($path);
      $times[] = $tm;
      $dirs[$tm] = $path;
    }

    if(count($times)<=$config["default"]["maxBookCacheCount"]){return;}
    sort($times);
    for($i=0; $i<count($times) - $config["default"]["maxBookCacheCount"]; $i++){
      $path = $dirs[$times[$i]];
      if(!$path || preg_match("/^\//",$path)){continue;}
      if($path === $config["default"]["expand"].self::getExpandPath($dir.$targetFile)){continue;}
      $cmd = 'rm -rf "'.$path.'"';
      exec($cmd);
    }
  }

  // public static function setRemoveDir($path){
  //   $cmd = 'rm -rf "'.$path.'"';
  //   exec($cmd);
  // }

  public static function getArchiveFiles($path){
    putenv("LANG=ja_JP.UTF-8");
    $cmd = "lsar '" . $path . "' |sort";
    exec($cmd , $res);

    $datas = array();
    for($i=0,$c=count($res); $i<$c; $i++){
      if(preg_match('/^(.+?)\.(png|bmp|tif|tiff|gif|tga|pic|pict|pct|psd)$/i',$res[$i])){
        $datas[] = $res[$i];
      }
    }
    return $datas;
  }


}
