<?php

//verify.php

include('Config.php');

$object = new Config;

if(isset($_GET["code"]))
{
	$object->query = "
	UPDATE customer_table 
	SET email_verify = 'Yes' 
	WHERE customer_verification_code = '".$_GET["code"]."'
	";

	$object->execute();

	$_SESSION['success_message'] = '<div class="alert alert-success">You Email has been verified, now you can login into the system</div>';

	header("location:Account.php?nodetails=Registration Successful!.");

}


?>