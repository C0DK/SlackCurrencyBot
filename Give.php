<?php

include "bootstrap.php";

if($_POST['token'] != "ozawlyr8HZ2yKErpSHBU58s5")
{
    print_r("wrong token, bro");
}

$username = $_POST['user_name'];
$command = explode(' ', $_POST['text']);


if (count($command) != 3) {
    print_r("WRONG SYNTAX!");
    return;
}

if(CheckForSpam($username))
{
    print_r("Dont spam!");
    return;
}

$targetUser = $command[0];
$amount = preg_replace("/[^0-9,.]/", "", $command[1]);
$currency = $command[2];

if ($amount != $command[1]) {
    print_r("Amount has to be a positive number");
    return;
}
if ($targetUser == $username) {
    print_r("You cant send yourself coins. Get a friend instead!");
    return;
}

if(ReadCurrency($username, $currency) < $amount)
{
   print_r("You are too poor to do that!");
   return;
}

if (!DoesUserExist($targetUser)) {
    echo "User doesn't exist";
    return;
}

GiveTo($username, $targetUser, $currency, $amount);

$response = $Slack->call('chat.postMessage', [
    'channel' => '#auctionhouse',
    'text' => $username . ' gave ' . $amount . "x *" . $currency . "* to " . $targetUser ,
    'icon_emoji' => $botEmoji,
    'username' => $botName
        ]);

if ($response['ok'])
    print_r("success!");
else
    print_r("error!?");
?>