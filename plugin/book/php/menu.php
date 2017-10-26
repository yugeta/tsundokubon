<?php

class BOOK_MENU{
  public static function getTargetFiles($dir=""){
    $template_li = file_get_contents(MYNT::getDesignPath()."custom/html/sidemenu_li.html");
    $config = BOOK_COMMON::getConfig();
    $lists = scandir($config["shelf"]["dir"].$dir);
    $html = "";

    // up
    if($dir !== ""){
      $sp = explode("/",$dir);
      array_pop($sp);
      $dirUp = join("/",$sp);
      $tmp = $template_li;
      $tmp = str_replace("<%link%>", "?dir=".$dirUp, $tmp);
      $tmp = str_replace("<%text%>", "..", $tmp);
      $html .= $tmp;
    }

    for($i=0,$c=count($lists); $i<$c; $i++){
      if(preg_match("/^\./",$lists[$i])){continue;}
      $tmp = $template_li;
      if(is_dir($config["default"]["dir"].$dir.$lists[$i])){
        $tmp = str_replace("<%icon%>", "fa fa-fw fa-folder", $tmp);
        $tmp = str_replace("<%link%>", "?dir=".$dir.$lists[$i], $tmp);
        $tmp = str_replace("<%text%>", $lists[$i], $tmp);
      }
      else{
        $tmp = str_replace("<%icon%>", "fa fa-fw fa-book", $tmp);
        $tmp = str_replace("<%link%>", "?p=book&dir=".$dir."&file=".$lists[$i], $tmp);
        $tmp = str_replace("<%text%>", $lists[$i], $tmp);
      }
      $html .= $tmp;
    }
    return $html;
  }
}
