<?php


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
		$order_column = array('feedback_id','name','email','feedback');

		$output = array();

		$main_query = "
		SELECT * FROM feedback_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR email LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY feedback_id DESC ';
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
			$sub_array[] = $row["feedback_id"];
			$sub_array[] = $row["name"];
			$sub_array[] = $row["email"];
			$sub_array[] = $row["feedback"];

			$sub_array[] = '
			<div align="center">
			<button type="button" name="reply_button" class="btn btn-info btn-circle btn-sm reply_button" data-id="'.$row["feedback_id"].'"><i class="fas fa-reply"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["feedback_id"].'"><i class="fas fa-times"></i></button>
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

	if($_POST["action"] == 'reply'){

		$error = '';

		$success = '';
		
		$object->query = "
		SELECT * FROM feedback_table 
		WHERE feedback_id = '".$_POST['hidden_id']."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['email'] = $row['email'];	
			$data['feedback'] = $row['feedback'];
		}		
		
		
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

			$mail->AddAddress($data['email']);
			$mail->Subject($data['feedback']);
			
			$message_body = '
			<p>Thank you for reaching out and providing us with valuable feedback.</p>

			<p>Sincerely,</p>
			<p>Salvation Jewellers</p>
			';
			$mail->Body = $message_body;

				if($mail->Send())
				{
					$success = '<div class="alert alert-success">Message has been sent</div>';
				}
				else
				{
					$error = '<div class="alert alert-danger">' . $mail->ErrorInfo . '</div>';
				}

			$output = array(
				'error'		=>	$error,
				'success'	=>	$success
			);
			echo json_encode($output);		

	}	
	
	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM feedback_table 
		WHERE feedback_id = '".$_POST["feedback_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['feedback_id'] = $row['feedback_id'];
			$data['name'] = $row['name'];
			$data['email'] = $row['email'];
			$data['feedback'] = $row['feedback'];

		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM feedback_table 
		WHERE feedback_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Feedback Record Deleted</div>';
	}
}

?>