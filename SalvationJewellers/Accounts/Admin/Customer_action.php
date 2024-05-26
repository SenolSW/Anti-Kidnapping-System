<?php

//doctor_action.php

include('../../Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('customer_id', 'customer_first_name', 'customer_last_name', 'customer_email_address', 'customer_phone_no', 'email_verify');

		$output = array();

		$main_query = "
		SELECT * FROM customer_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE customer_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR email_verify LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY customer_id, customer_email_address, email_verify DESC ';
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
			$sub_array[] = $row["customer_id"];
			$sub_array[] = decryptthis($row["customer_first_name"], $passphrase) . ' ' . decryptthis($row["customer_last_name"], $passphrase);
			$sub_array[] = $row["customer_email_address"];
			$sub_array[] = decryptthis($row["customer_phone_no"], $passphrase);
			$status = '';
			if($row["email_verify"] == 'Yes')
			{
				$status = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$status = '<span class="badge badge-danger">Yes</span>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["customer_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["customer_id"].'"><i class="fas fa-times"></i></button>
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
		SELECT * FROM customer_table 
		WHERE customer_id = '".$_POST["customer_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['customer_id'] = $row['customer_id'];
			$data['customer_email_address'] = $row['customer_email_address'];
			$data['customer_first_name'] = decryptthis($row['customer_first_name'], $passphrase);
			$data['customer_last_name'] = decryptthis($row['customer_last_name'], $passphrase);
			$data['customer_date_of_birth'] = decryptthis($row['customer_date_of_birth'], $passphrase);
			$data['customer_phone_no'] = decryptthis($row['customer_phone_no'], $passphrase);
			$data['customer_address'] = decryptthis($row['customer_address'], $passphrase);
			if($row['email_verify'] == 'Yes')
			{
				$data['email_verify'] = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$data['email_verify'] = '<span class="badge badge-danger">No</span>';
			}
		}

		echo json_encode($data);
	}



	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM customer_table 
		WHERE customer_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Customer Record Deleted</div>';
	}
}

?>