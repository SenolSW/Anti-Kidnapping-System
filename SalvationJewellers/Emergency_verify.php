<?php

include('Config.php');

$object = new Config;

if(isset($_GET["code"]))
{
	$object->query = "
	UPDATE emergency_table 
	SET emergency_email_verify = 'Yes' 
	WHERE emergency_verification_code = '".$_GET["code"]."'
	";

	$object->execute();
	
	header("location:Accounts/User/Emergency.php?nodetails=Added Successfully!.");

}


?>