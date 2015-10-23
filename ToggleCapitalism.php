<?php

include "bootstrap.php";

$userName = $_POST['user_name'];

$json_DB = ReadSource(BANK_FILE_NAME);

$json_DB[$userName]["capitalismOff"] = !$json_DB[$userName]["capitalismOff"];

SetSource(BANK_FILE_NAME,$json_DB);

if(!$json_DB[$userName]["capitalismOff"])
    echo "Done - Capitalism is now ON!";
else
    echo "Done - Capitalism is now OFF!";
    
