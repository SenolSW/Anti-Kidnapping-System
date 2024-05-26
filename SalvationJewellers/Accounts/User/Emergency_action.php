<?php

//doctor_action.php

include('../../Config.php');

$object = new Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
					
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('emergency_id', 'emergency_first_name', 'emergency_last_name','emergency_email_address', 'emergency_contact', 'emergency_address', 'emergency_email_verify');

		$output = array();

		$main_query = "SELECT * FROM emergency_table ";

		$search_query = 'WHERE customer_id = "'.$_SESSION["admin_id"].'" ';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND emergency_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND emergency_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND emergency_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND emergency_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND emergency_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND emergency_contact LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY emergency_id, emergency_first_name, emergency_last_name DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = $row["emergency_id"];
			$sub_array[] = decryptthis($row["emergency_first_name"],$passphrase) . ' ' . decryptthis($row["emergency_last_name"],$passphrase);
			$sub_array[] = decryptthis($row["emergency_email_address"],$passphrase);
			$sub_array[] = decryptthis($row["emergency_contact"],$passphrase);
			$status = '';
			if($row["emergency_email_verify"] == 'Yes')
			{
				$status = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$status = '<span class="badge badge-danger">No</span>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["emergency_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["emergency_id"].'"><i class="fas fa-times"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM emergency_table 
		WHERE emergency_id = '".$_POST["emergency_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['emergency_id'] = $row['emergency_id'];	
			$data['emergency_first_name'] = decryptthis($row['emergency_first_name'], $passphrase);
			$data['emergency_last_name'] = decryptthis($row['emergency_last_name'], $passphrase);
			$data['emergency_email_address'] = decryptthis($row['emergency_email_address'], $passphrase);
			$data['emergency_contact'] = decryptthis($row['emergency_contact'], $passphrase);
			$data['emergency_address'] = decryptthis($row['emergency_address'], $passphrase);		
			if($row['emergency_email_verify'] == 'Yes')
			{
				$data['emergency_email_verify'] = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$data['emergency_email_verify'] = '<span class="badge badge-danger">No</span>';
			}
		}

		echo json_encode($data);
	}

	if($_POST['action'] == 'Add')
	{
		$error = '';

		$success = '';

		$data = array(
			':emergency_email_address'	=>	$_POST["emergency_email_address"]
		);

		$object->query = "
		SELECT * FROM emergency_table 
		WHERE emergency_email_address = :emergency_email_address
		";
		
		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$emergency_verification_code = md5(uniqid());
			
			$emergency_email_address		= $object->clean_input($_POST["emergency_email_address"]);
			$encemergency_email_address		= encryptthis($emergency_email_address, $passphrase);
			$emergency_first_name			= $object->clean_input($_POST["emergency_first_name"]);
			$encemergency_first_name		= encryptthis($emergency_first_name, $passphrase);
			$emergency_last_name			= $object->clean_input($_POST["emergency_last_name"]);
			$encemergency_last_name			= encryptthis($emergency_last_name, $passphrase);
			$emergency_contact				= $object->clean_input($_POST["emergency_contact"]);
			$encemergency_contact			= encryptthis($emergency_contact, $passphrase);
			$emergency_address				= $object->clean_input($_POST["emergency_address"]);
			$encemergency_address			= encryptthis($emergency_address, $passphrase);
			
			$data = array(
				':emergency_email_address'		=>	$encemergency_email_address,
				':emergency_first_name'			=>	$encemergency_first_name,
				':emergency_last_name'			=>	$encemergency_last_name,
				':emergency_contact'			=>	$encemergency_contact,
				':emergency_address'			=>	$encemergency_address,
				':customer_id'					=>	$_SESSION["admin_id"],
				':emergency_added_on'			=>	$object->now,
				':emergency_verification_code'	=>	$emergency_verification_code,
				':emergency_email_verify'		=>	'No'
			);

			$object->query = "
			INSERT INTO emergency_table 
			(emergency_email_address, emergency_first_name, emergency_last_name, emergency_contact, emergency_address, customer_id, emergency_added_on, emergency_verification_code, emergency_email_verify) 
			VALUES (:emergency_email_address, :emergency_first_name, :emergency_last_name, :emergency_contact, :emergency_address, :customer_id, :emergency_added_on, :emergency_verification_code, :emergency_email_verify)
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
			$mail->FromName = 'Salvation Jewellers';
			$mail->AddAddress($_POST["emergency_email_address"]);
			$mail->Subject = 'Verification code for Your Email Address';

			$message_body = '
			<p>To verify your email address, Please click on this <a href="'.$object->base_url.'Emergency_verify.php?code='.$emergency_verification_code.'"><b>link</b></a>.</p>
			<p>Sincerely,</p>
			<p>Salvation Jewellers</p>
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
	
	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM emergency_table 
		WHERE emergency_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Emergency Contact Deleted</div>';
	}	
	
}

?>