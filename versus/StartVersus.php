<?php

if($_POST['token'] != "SH55uf14mIDSQV6fWicRXV1A")
{
    print_r("wrong token, bro");
}

$username = $_POST['user_name'];
$targetUser = $command[0];

if (!DoesUserExist($targetUser)) {
    echo "User doesn't exist";
    return;
}