<?php
/**
 * Mynt Studio
 * MakingDate : 2017.09.07
 * Auther @ Yugeta Koji (MYNT Inc.)
 * Summery : WebSiteFrameWork (WSFW)
 * Module : admin.php
 */

require_once "lib/mynt.php";

date_default_timezone_set('Asia/Tokyo');

// Load-PHP-Plugins-module
// MYNT::loadPlugins();
MYNT::loadPHPs("lib/php/");
MYNT::loadPHPs("system/php/");

// Load-Config
$conf1 = MYNT::loadConfig("data/config/");
$conf2 = MYNT::loadConfig("system/config/");
$GLOBALS["config"] = array_merge($conf1 , $conf2);

// Session-Start
MYNT::startSession();

// Check-Query (system-process)
MYNT::checkMethod();

// Load-HTML-Default-source
MYNT::viewTemplate("system/html/template.html");
