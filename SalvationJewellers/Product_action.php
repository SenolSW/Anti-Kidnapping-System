<?php

include('Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	
	if($_POST["action"] == 'Add')
	{

		$error = '';

		$success = '';

		$data = array(
			
			':customer_email_address'	=>	$_POST["customer_email_address"],
			':customer_password'	=>	$_POST["customer_password"],
			':email_verify'	=>	'Yes'
		);

		$object->query = "
		SELECT * FROM customer_table 
		WHERE customer_email_address = :customer_email_address AND customer_password = :customer_password AND email_verify = :email_verify
		";

		$object->execute($data);

		if($object->row_count() < 1)
		{
			$error = '<div class="alert alert-danger">You cannot purchase a product without a account</div>';
		}
		else
		{

			if($error == '')
			{
				$victim_first_name			= $object->clean_input($_POST["victim_first_name"]);
				$encvictim_first_name		= encryptthis($victim_first_name, $passphrase);
				$victim_last_name			= $object->clean_input($_POST["victim_last_name"]);
				$encvictim_last_name		= encryptthis($victim_last_name, $passphrase);
				$victim_date_of_birth		= $object->clean_input($_POST["victim_date_of_birth"]);
				$encvictim_date_of_birth	= encryptthis($victim_date_of_birth, $passphrase);
				$victim_gender				= $object->clean_input($_POST["victim_gender"]);
				$encvictim_gender			= encryptthis($victim_gender, $passphrase);
				$victim_height				= $object->clean_input($_POST["victim_height"]);
				$encvictim_height			= encryptthis($victim_height, $passphrase);
				

				$data = array(
					':product_id'			=>	$encvictim_last_name,
					':customer_id'				=>	$victim_image,
					':victim_date_of_birth'		=>	$encvictim_date_of_birth,
					':victim_gender'			=>	$encvictim_gender,
					':victim_height'			=>	$encvictim_height,
					':order_added_on'			=>	$object->now
				);

				$object->query = "
				INSERT INTO order_table 
				(victim_first_name, victim_last_name, victim_image, victim_date_of_birth, victim_gender, victim_height, customer_id, victim_status, victim_added_on) 
				VALUES (:victim_first_name, :victim_last_name, :victim_image, :victim_date_of_birth, :victim_gender, :victim_height, :customer_id, :victim_status, :victim_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Product Purchased</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}
	
}
?>