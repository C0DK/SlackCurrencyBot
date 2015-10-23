<?php
include 'GrubbesFramework.php';

define(BANK_FILE_NAME, "bank.json");
define(TRANSACTION_FILE_NAME, "transactionHistory.json");
define(STATS_FILE_NAME, "stats.json");

function DoesUserExist()
{
    $userList = GetUsers();
    $noUser = true;
    foreach ($userList as $userVal) {
        if ($userVal["name"] == $targetUser)
            $noUser = false;
    }
    
    return $noUser;
}

function GetData() {
    $DB = file_get_contents(BANK_FILE_NAME);
    return json_decode($DB, true);
}

function AddCoinSumStats($messagesSum) {
    $stats = ReadSource(STATS_FILE_NAME);

    $DB = GetData();
    $currencies = [];
    foreach ($DB as $user) {
        foreach ($user["currencies"] as $currencyName => $currencyAmount) {
            $currencies[$currencyName] += $currencyAmount;
        }
    }
    $stats[] = ["ts" => date("Y-m-d G:i"), "messages" => $messagesSum, "currencies" => $currencies];
    SetSource(STATS_FILE_NAME, $stats);
}

function ReadSource($fileName) {
    $file_JSON = file_get_contents($fileName);
    return json_decode($file_JSON, true);
}

function SetSource($fileName, $data) {
    $json = json_encode($data, true);
    file_put_contents($fileName, $json);
}

function WriteToTransactionHistory($userName, $currency, $amount) {

    $history = ReadSource(TRANSACTION_FILE_NAME);

    $history[] = ["ts" => date("Y-m-d G:i")
        , "user" => $userName, "currency" => $currency, "amount" => $amount];

    SetSource(TRANSACTION_FILE_NAME, $history);
}

function GetUsers() {
    global $Slack;
    $userList = $Slack->call('users.list');
    $usersToReturn = [];

    foreach ($userList["members"] as $user) {
        if (!IsBailedOut($user["name"]))
            $usersToReturn[] = $user;
    }
    return $usersToReturn;
}

function IsBailedOut($userName) {
    $json_DB = ReadSource(BANK_FILE_NAME);

    if (!$json_DB[$userName])
        return false;
    else
        return $json_DB[$userName]["capitalismOff"];
}

function GenerateCoin($userlist, $currency, $chance) {
    if (rand(0, 100) <= $chance) {
        global $Slack;
        foreach ($userlist as $userID) {

            $UserInfo = $Slack->call('users.info', [
                        'user' => $userID,
                    ])["user"];

            GiveCurrency($UserInfo['name'], $currency, 1);
        }
    }
}

function GiveTo($fromName, $toName, $currency, $amount) {

    GiveCurrency($fromName, $currency, -$amount);
    GiveCurrency($toName, $currency, $amount);

    $DB = ReadSource(BANK_FILE_NAME);

    $DB[$fromName]["TimesGiven"] ++;
    $DB[$toName]["TimesRecieved"] ++;

    SetSource(BANK_FILE_NAME, $DB);
}

function CheckForSpam($userName) {
    $transactions = ReadSource(TRANSACTION_FILE_NAME);
    $amountOfTranactions = 0;
    foreach($transactions as $trans)
    {
        if($trans["user"] == $userName && time() - strtotime($trans["ts"]) < 120)
            $amountOfTranactions++;
    }
    return $amountOfTranactions > 2;
}

function GiveCurrency($userName, $currency, $amount) {
    $DB = ReadSource(BANK_FILE_NAME);

    $DB[$userName]["currencies"][$currency]+=$amount;

    SetSource(BANK_FILE_NAME, $DB);

    WriteToTransactionHistory($userName, $currency, $amount);
}

function ReadCurrency($userName, $currency) {
    $DB = ReadSource(BANK_FILE_NAME);

    return $DB[$userName]["currencies"][$currency];
}

?>