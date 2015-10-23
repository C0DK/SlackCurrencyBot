<?php
ini_set('display_errors',1); 
 error_reporting(E_ERROR | E_WARNING);
 
 

include "src/slack.php";
$Slack = new Slack('xoxb-12487033041-CHRGBGy1QaJq99bwparz7JFS');
include "src/functions.php";

$botName = "CurBot";
$botEmoji = ":cop:";

?>