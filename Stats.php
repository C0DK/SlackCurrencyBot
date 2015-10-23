<?php
include "bootstrap.php";

$username = $_POST['user_name'];
$caller = $_POST['user_name'];
$command = explode(' ',$_POST['text']);

if($command[0] != "")
{
	$username =  $command[0];
}

$DB = ReadSource(BANK_FILE_NAME);

$message = "*stats*:(".$username.")\n";

if(array_key_exists($username,$DB))	
{
	$userData = &$DB[$username];
	$message .= "currencies:\n";
	foreach($userData["currencies"] as $coinName => $coinAmount)
	{
		$HigherRanked = 0;
		//Get rank
		foreach($DB as $user)
		{
			if(array_key_exists($coinName,$user["currencies"]))
				if($user["currencies"][$coinName] > $coinAmount)
					$HigherRanked++;
		}
		
		//
		$message .= "\t *".$coinName."* x ".$coinAmount." - RANK #".$HigherRanked."\n";
		
	}
	//if($userData["TimesGiven"])
		$message .= "Times given=" . $userData["TimesGiven"] ."\n";
	//if($userData["TimesRecieved"])
		$message .= "Times recieved=" . $userData["TimesRecieved"] ."\n";
	if($userData["TimesRecieved"] && $userData["TimesGiven"])
		$message .= "Ratio(G/R)=" . ($userData["TimesGiven"]/$userData["TimesRecieved"]) ." (Approx)\n";
			
}
else
{
	print_r("user not found (No activity? Not a user?)");
	return;
}

echo $message;
