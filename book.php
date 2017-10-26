<?php

putenv("LANG=ja_JP.UTF-8");
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('Asia/Tokyo');
header("Content-type: text/html; charset=utf-8");

require_once "lib/mynt.php";

// PHP-module
MYNT::loadPHPs("lib/php/");

// Load-PHP-Plugins-module
MYNT::loadPHPs("plugin/book/php/");

if($_REQUEST["mode"] === "comment-save"){
  // echo $_REQUEST["dir"] .",". $_REQUEST["file"] .",". $_REQUEST["num"] .",". $_REQUEST["comment"];
  echo BOOK_COMMENT::setData($_REQUEST["dir"],$_REQUEST["file"],$_REQUEST["num"],$_REQUEST["comment"]);
}
else if($_REQUEST["mode"] === "comment-load"){
  echo BOOK_COMMENT::getData($_REQUEST["dir"],$_REQUEST["file"],$_REQUEST["num"]);
}
else if($_REQUEST["mode"] === "get-base64"){
  echo BOOK_base64::getBase64($_REQUEST["dir"],$_REQUEST["file"],$_REQUEST["num"]);
}

exit();
