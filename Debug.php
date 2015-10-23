<?php
include "bootstrap.php";

if($_POST['token'] != "vvjAhKeo8nlEynnvXp5llUJM")
{
    print_r("wrong token, bro");
  	return;
}

$message = "*DEBUG:*\n:";

$currencies = [];

$DB = GetData();
foreach($DB as $user)
{
    foreach($user["currencies"] as $currencyName => $currencyAmount)
    {
        $currencies[$currencyName] += $currencyAmount;
    }
}

$message .= "currencies:\n";
foreach ($currencies as $currencyName => $currencyAmount)
{
    $message .= "\t".$currencyName . " x " . $currencyAmount."\n";
}


$response = $Slack->call('chat.postMessage', [
    'channel' => '@'.$_POST['user_name'],
    'text' => $message ,
    'icon_emoji' => $botEmoji,
    'username' => $botName
        ]);

