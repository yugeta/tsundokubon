<?php

class MYNT_MODULE_EDIT{

  public function setDataUpload(){

    if(!isset($_POST["target"])
    // || !isset($_REQUEST["path"])
    || !isset($_POST["source"])
    ){return;}

    // dir_rename
    if(is_dir($_REQUEST["path"]) && $_POST["target"] !== "" && $_REQUEST["path"] !== "" && $_POST["target"] !== $_REQUEST["path"]){
      $path   = preg_replace("/\/$/","",$_REQUEST["path"]);
      $target = preg_replace("/\/$/","",$_POST["target"]);
      rename($path , $target);
    }

    // file-rename
    else if(is_file($_REQUEST["path"]) && $_POST["target"] !== "" && $_REQUEST["path"] !== "" && $_POST["target"] !== $_REQUEST["path"]){
      rename($_REQUEST["path"] , $_POST["target"]);
      $_REQUEST["path"] = $_POST["target"];
    }

    // male-folder
    // die($_POST["target"]);
    $pathinfo = pathinfo($_POST["target"]);
    // die($pathinfo["dirname"]);
    if(!is_dir($pathinfo["dirname"])){
      mkdir($pathinfo["dirname"] , 0777 , true);
    }

    // make-new-folder
    if($_POST["target"]!=="" && preg_match("/\/$/",$_POST["target"]) && !$_POST["file_remove"]){
      if(!is_dir($_POST["target"])){
        mkdir($_REQUEST["target"] , 0777 , true);
      }
      MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]."&path=".$_POST["target"]);
    }

    // file-write
    else if($_POST["target"]!=="" && !$_POST["file_remove"]){
      file_put_contents($_POST["target"] , $_POST["source"]);
      // if(!is_file($_POST["target"])){
      //   die("not-file !!"." ".$_POST["target"]." | ".$_POST["source"]);
      // }
      MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]."&path=".$_REQUEST["path"]);
    }

    // file(dir)-remove
    else if($_POST["target"]!=="" && is_file($_POST["target"])){
      unlink($_POST["target"]);
      MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]);
    }
    else if($_POST["target"]!=="" && is_dir($_POST["target"])){
      $target = preg_replace("/\/$/","",$_POST["target"]);
      rmdir($target);
      $pathinfo = pathinfo($target);
      $path = ($pathinfo["dirname"] === ".")?"&path=".$pathinfo["dirname"]:"";
      MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"].$path);
    }

    // redirect
    else{
      MYNT_URL::setUrl(MYNT_URL::getUrl()."?p=".$_REQUEST["p"]."&path=".$_REQUEST["path"]."/");
    }

  }

  public function viewMenu($dir=""){
    $target = ($dir==="")?"./":$dir;
    $dirs = scanDir($target);
    $html = "";
    for($i=0,$c=count($dirs); $i<$c; $i++){
      $path = $dir.$dirs[$i];
      if($dirs[$i] === "." || $dirs[$i] === ".."){continue;}
      if($dirs[$i] === ".git" || $dirs[$i] === ".gitignore"){continue;}
      $type = (is_dir($dir.$dirs[$i]))?'dir':'file';
      $icon = (is_dir($dir.$dirs[$i]))?'<i class="glyphicon glyphicon-folder-close" data-type="'.$type.'" data-path="'.$path.'"></i>':'<i class="glyphicon glyphicon-file" data-type="'.$type.'" data-path="'.$path.'"></i>';
      $selected = (isset($_REQUEST["path"]) && $_REQUEST["path"] === $path)?"data-select='select'":"";
      $str = self::setSourceValue($path);
      $dir_open = (isset($_REQUEST["path"]) && $type === "dir" && preg_match("/^".$str."/", $_REQUEST["path"]))?"data-status='open'":"";

      $html.= "<div class='path' data-path='".$path."' data-type='".$type."' ".$selected." ".$dir_open.">"."<span class='path-icon' data-type='".$type."' data-path='".$path."'>".$icon."</span>" .$dirs[$i]."</div>".PHP_EOL;
      if($type === "dir"){
        $html .= "<div class='path-inner' data-path='".$path."' ".$dir_open.">";
        $html .= self::viewMenu($path."/");
        $html .= "</div>";
      }
    }
    return $html;
  }
  public function setSourceValue($source){
    $source = str_replace("/","\/",$source);
    $source = str_replace(".","\.",$source);
    $source = str_replace("[","\[",$source);
    $source = str_replace("]","\]",$source);
    $source = str_replace("-","\-",$source);
    $source = str_replace("^","\^",$source);
    $source = str_replace("$","\$",$source);
    return $source;
  }

  public function getSource($path=""){
    if($path===""){return;}
    if(!is_file($path)){return;}
    $source = file_get_contents($path);
    $source = str_replace("<","&lt;",$source);
    $source = str_replace(">","&gt;",$source);
    return $source;
  }

}
