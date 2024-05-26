<?php

include('../../Config.php');

$object = new Config;

if($_POST["action"] == 'customer_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$data = array(
		':customer_email_address'	=>	$_POST["customer_email_address"],
		':customer_id'				=>	$_POST['hidden_id']
	);

	$object->query = "
	SELECT * FROM customer_table 
	WHERE customer_email_address = :customer_email_address 
	AND customer_id != :customer_id
	";

	$object->execute($data);

	if($object->row_count() > 0)
	{
		$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
	}
	else
	{

		if($error == '')
		{
			$data = array(
			':customer_password'				=>	$_POST["customer_password"],
			':customer_phone_no'				=>	$object->clean_input($_POST["customer_phone_no"]),
			':customer_address'					=>	$object->clean_input($_POST["customer_address"])
			);

			$object->query = "
			UPDATE customer_table  
			SET customer_password = :customer_password, 
			customer_phone_no = :customer_phone_no, 
			customer_address = :customer_address
			WHERE customer_id = '".$_POST['hidden_id']."'
			";
			$object->execute($data);

			$success = '<div class="alert alert-success">Doctor Data Updated</div>';
		}			
	}

	$output = array(
		'error'						=>	$error,
		'success'					=>	$success,
		'customer_email_address'	=>	$_POST["customer_email_address"],
		'customer_password'			=>	$_POST["customer_password"],
		'customer_name'				=>	$_POST["customer_name"],
		'customer_phone_no'			=>	$_POST["customer_phone_no"],
		'customer_address'			=>	$_POST["customer_address"],
		'customer_date_of_birth'	=>	$_POST["customer_date_of_birth"]
	);

	echo json_encode($output);
}

if($_POST["action"] == 'admin_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$company_logo = $_POST['hidden_company_logo'];

	if($_FILES['company_logo']['name'] != '')
	{
		$allowed_file_format = array("jpg", "png");

	    $file_extension = pathinfo($_FILES["company_logo"]["name"], PATHINFO_EXTENSION);

	    if(!in_array($file_extension, $allowed_file_format))
		{
		    $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
		}
		else if (($_FILES["company_logo"]["size"] > 2000000))
		{
		   $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
	    }
		else
		{
		    $new_name = rand() . '.' . $file_extension;

			$destination = '../../img/' . $new_name;

			move_uploaded_file($_FILES['company_logo']['tmp_name'], $destination);

			$company_logo = $destination;
		}
	}

	if($error == '')
	{
		$data = array(
			':admin_email_address'			=>	$object->clean_input($_POST["admin_email_address"]),
			':admin_password'				=>	$_POST["admin_password"],
			':admin_name'					=>	$object->clean_input($_POST["admin_name"]),
			':company_name'					=>	$object->clean_input($_POST["company_name"]),
			':company_address'				=>	$object->clean_input($_POST["company_address"]),
			':company_contact_no'			=>	$object->clean_input($_POST["company_contact_no"]),
			':company_logo'					=>	$company_logo
		);

		$object->query = "
		UPDATE admin_table  
		SET admin_email_address = :admin_email_address, 
		admin_password = :admin_password, 
		admin_name = :admin_name, 
		company_name = :company_name, 
		company_address = :company_address, 
		company_contact_no = :company_contact_no, 
		company_logo = :company_logo 
		WHERE admin_id = '".$_SESSION["admin_id"]."'
		";
		$object->execute($data);

		$success = '<div class="alert alert-success">Admin Profile Updated</div>';

		$output = array(
			'error'					=>	$error,
			'success'				=>	$success,
			'admin_email_address'	=>	$_POST["admin_email_address"],
			'admin_password'		=>	$_POST["admin_password"],
			'admin_name'			=>	$_POST["admin_name"], 
			'company_name'			=>	$_POST["company_name"],
			'company_address'		=>	$_POST["company_address"],
			'company_contact_no'	=>	$_POST["company_contact_no"],
			'company_logo'			=>	$company_logo
		);

		echo json_encode($output);
	}
	else
	{
		$output = array(
			'error'					=>	$error,
			'success'				=>	$success
		);
		echo json_encode($output);
	}
}

?>