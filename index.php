<?php
/**
 * Mynt Studio
 * Auther @ Yugeta Koji (MYNT Inc.)
 * WebSiteFrameWork (WSFW)
 * ver 1.0 (2017.08.01)
 * ver 2.0 (2017.09.01)
 */
putenv("LANG=ja_JP.UTF-8");
ini_set('default_charset', 'UTF-8');
require_once "lib/mynt.php";

date_default_timezone_set('Asia/Tokyo');

// PHP-module
MYNT::loadPHPs("lib/php/");

// Load-Config
$GLOBALS["config"] = MYNT::loadConfig("data/config/");

// Session-Start
MYNT::startSession();

// Load-PHP-Plugins-module
MYNT::loadPlugins();

// // Check-Query (system-process)
MYNT::checkMethod();

// Load-HTML-Default-source
MYNT::viewTemplate("design/".$GLOBALS["config"]["design"]["target"]."/template.html");
