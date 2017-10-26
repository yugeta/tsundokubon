<?php

class MYNT_MENU{

  public static function getSource($path){
    if(!is_file($path)){return;}
    $tmpSource = file_get_contents($path);
    return $tmpSource;
  }

  public static function getMenuLists($type, $tmpPath, $url=""){
    $tmpSource = self::getSource($tmpPath);
    $currentUrl = MYNT_URL::getUri();
    $currentUrlBasename = MYNT_URL::getBasename($currentUrl);
    $menus = $GLOBALS["config"]["menu"][$type]["lists"];

    $html = "";
    for($i=0,$c=count($menus); $i<$c; $i++){
      $tmp = "";
      // file
      if(isset($menus[$i]["file"]) && $menus[$i]["file"]){
        $tmp = MYNT::conv(file_get_contents($menus[$i]["file"]));
      }
      //link
      else if(isset($menus[$i]["link"]) && $menus[$i]["link"]){
        $linkUrlBasename = MYNT_URL::getBasename($menus[$i]["link"]);
        $tmp = $tmpSource;
        $tmp = str_replace("<%link%>",   $menus[$i]["link"],   $tmp);
        $tmp = str_replace("<%text%>",   $menus[$i]["text"],   $tmp);
        $tmp = str_replace("<%className_li%>",   $GLOBALS["config"]["menu"]["nav"]["className_li"],   $tmp);
        $tmp = str_replace("<%className_a%>",    $GLOBALS["config"]["menu"]["nav"]["className_a"],   $tmp);

        // active-check
        $active = "";
        // full-path
        if(preg_match("/^[http|https]:\/\//",$menus[$i]["link"])){
          if($currentUrl === $menus[$i]["link"]){
            $active = $GLOBALS["config"]["menu"]["nav"]["activeString"];
          }
        }
        // query
        else{
          if($currentUrlBasename === $linkUrlBasename){
            $active = $GLOBALS["config"]["menu"]["nav"]["activeString"];
          }
        }
        $tmp = str_replace("<%active_li%>", $active, $tmp);
        $tmp = str_replace("<%active_a%>",  $active, $tmp);
      }
      // add-source
      $html .= $tmp.PHP_EOL;
    }
    return MYNT::conv($html);
  }

  public static function nav($url = ""){
    if(!isset($GLOBALS["config"]["menu"]["nav"])){return;}
    $path = MYNT::getDesignPath()."custom/html/nav_ul.html";
    $path = (is_file($path))?$path:"lib/html/nav_ul.html";
    $source = self::getSource($path);
    $source = str_replace("<%className_ul%>", $GLOBALS["config"]["menu"]["nav"]["className_ul"], $source);
    return MYNT::conv(str_replace("<%lists%>", self::nav_li($url), $source));
  }

  public static function footer($url = ""){
    if(!isset($GLOBALS["config"]["menu"]["footer"])){return;}
    $path = MYNT::getDesignPath()."custom/html/footer_ul.html";
    $path = (is_file($path))?$path:"lib/html/footer_ul.html";
    $source = self::getSource($path);
    $source = str_replace("<%className_ul%>", $GLOBALS["config"]["menu"]["footer"]["className_ul"], $source);
    return MYNT::conv(str_replace("<%lists%>", self::footer_li($url), $source));
  }

  public static function nav_li($url = ""){
    if(!isset($GLOBALS["config"]["menu"]["nav"])){return;}
    $path = MYNT::getDesignPath()."custom/html/nav_li.html";
    $path = (is_file($path))?$path:"lib/html/nav_li.html";
    $html = self::getMenuLists("nav", $path, $url);
    return MYNT::conv($html);
  }

  public static function footer_li($url = ""){
    if(!isset($GLOBALS["config"]["menu"]["footer"])){return;}
    $path = MYNT::getDesignPath()."custom/html/footer_li.html";
    $path = (is_file($path))?$path:"lib/html/footer_li.html";
    $html = self::getMenuLists("footer", $path, $url);

    return MYNT::conv($html);
  }


}
