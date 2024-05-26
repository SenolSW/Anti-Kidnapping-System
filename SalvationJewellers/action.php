<?php

include('Config.php');

$object = new Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
					
require 'Accounts/PHPMailer-master/src/PHPMailer.php';
require 'Accounts/PHPMailer-master/src/SMTP.php';
require 'Accounts/PHPMailer-master/src/Exception.php';


if(isset($_POST["action"]))
{

	if($_POST['action'] == 'customer_register')
	{
		$error = '';

		$success = '';

		$data = array(
			':customer_email_address'	=>	$_POST["customer_email_address"]
		);

		$object->query = "
		SELECT * FROM customer_table 
		WHERE customer_email_address = :customer_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$customer_email_address			= $object->clean_input($_POST["customer_email_address"]);
			$customer_password				= $_POST["customer_password"];
			$customer_first_name			= $object->clean_input($_POST["customer_first_name"]);
			$enccustomer_first_name			= encryptthis($customer_first_name, $passphrase);
			$customer_last_name				= $object->clean_input($_POST["customer_last_name"]);
			$enccustomer_last_name			= encryptthis($customer_last_name, $passphrase);
			$customer_date_of_birth			= $object->clean_input($_POST["customer_date_of_birth"]);
			$enccustomer_date_of_birth		= encryptthis($customer_date_of_birth, $passphrase);
			$customer_phone_no				= $object->clean_input($_POST["customer_phone_no"]);
			$enccustomer_phone_no			= encryptthis($customer_phone_no, $passphrase);
			$customer_address				= $object->clean_input($_POST["customer_address"]);
			$enccustomer_address			= encryptthis($customer_address, $passphrase);
			
			$customer_verification_code = md5(uniqid());
			$data = array(
				':customer_email_address'		=>	$customer_email_address,
				':customer_password'			=>	$customer_password,
				':customer_first_name'			=>	$enccustomer_first_name,
				':customer_last_name'			=>	$enccustomer_last_name,
				':customer_date_of_birth'		=>	$enccustomer_date_of_birth,
				':customer_phone_no'			=>	$enccustomer_phone_no,
				':customer_address'				=>	$enccustomer_address,
				':customer_added_on'			=>	$object->now,
				':customer_verification_code'	=>	$customer_verification_code,
				':email_verify'					=>	'No'
			);

			$object->query = "
			INSERT INTO customer_table 
			(customer_email_address, customer_password, customer_first_name, customer_last_name, customer_date_of_birth, customer_phone_no, customer_address, customer_added_on, customer_verification_code, email_verify) 
			VALUES (:customer_email_address, :customer_password, :customer_first_name, :customer_last_name, :customer_date_of_birth, :customer_phone_no, :customer_address, :customer_added_on, :customer_verification_code, :email_verify)
			";

			$object->execute($data);

			$mail = new PHPMailer;
			$mail->IsSMTP();
			$mail->Host = "smtp.gmail.com"; 
			$mail->Port = '587';
			$mail->SMTPAuth = true;
			$mail->Username = 'SalvationJewellers';
			$mail->Password = 'uyizoajaamkpaglj';
			$mail->SMTPSecure = 'tsl';
			$mail->isHTML(true);  
			$mail->From = 'SalvationJewellers@gmail.com';
			$mail->FromName = 'Reviewist';
			$mail->AddAddress = 'rixeraw713@farebus.com';
			$mail->Subject = 'Verification code for Your Email Address';

			$message_body = '
			<p>To verify your email address, Please click on this <a href="'.$object->base_url.'verify.php?code='.$admin_verification_code.'"><b>link</b></a>.</p>
			<p>Sincerely,</p>
			<p>Hilton Hotel</p>
			';
			$mail->Body = $message_body;

			if($mail->Send())
			{
				$success = '<div class="alert alert-success">Please Verify your Email address</div>';
			}
			else
			{
				$error = '<div class="alert alert-danger">' . $mail->ErrorInfo . '</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);
		echo json_encode($output);
	}

	if($_POST['action'] == 'customer_login')
	{
		$error = '';

		$data = array(
			':customer_email_address'	=>	$_POST["customer_email_address"]
		);

		$object->query = "
		SELECT * FROM customer_table 
		WHERE customer_email_address = :customer_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{

			$result = $object->statement_result();

			foreach($result as $row)
			{
				if($row["email_verify"] == 'Yes')
				{
					if($row["customer_password"] == $_POST["customer_password"])
					{
						$_SESSION['customer_id'] = $row['customer_id'];
						$_SESSION['customer_name'] = $row['customer_first_name'] . ' ' . $row['customer_last_name'];
					}
					else
					{
						$error = '<div class="alert alert-danger">Wrong Password</div>';
					}
				}
				else
				{
					$error = '<div class="alert alert-danger">Please verify your email address</div>';
				}
			}
		}
		else
		{
			$error = '<div class="alert alert-danger">Wrong Email Address</div>';
		}

		$output = array(
			'error'		=>	$error
		);

		echo json_encode($output);

	}


	if($_POST['action'] == 'edit_profile')
	{
		$data = array(
			':customer_password'		=>	$_POST["customer_password"],
			':customer_first_name'		=>	$_POST["customer_first_name"],
			':customer_last_name'		=>	$_POST["customer_last_name"],
			':customer_date_of_birth'	=>	$_POST["customer_date_of_birth"],
			':customer_gender'			=>	$_POST["customer_gender"],
			':customer_phone_no'		=>	$_POST["customer_phone_no"],
			':customer_address'			=>	$_POST["customer_address"]
		);

		$object->query = "
		UPDATE customer_table  
		SET customer_password = :customer_password, 
		customer_first_name = :customer_first_name, 
		customer_last_name = :customer_last_name, 
		customer_date_of_birth = :customer_date_of_birth, 
		customer_gender = :customer_gender,
		customer_phone_no = :customer_phone_no,
		customer_address = :customer_address
		WHERE customer_id = '".$_SESSION['customer_id']."'
		";

		$object->execute($data);

		$_SESSION['success_message'] = '<div class="alert alert-success">Profile Data Updated</div>';

		echo 'done';
	}

}



?>