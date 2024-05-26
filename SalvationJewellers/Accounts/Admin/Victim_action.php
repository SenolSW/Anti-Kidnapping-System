<?php

include('../../Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('victim_id', 'victim_first_name', 'victim_last_name', 'victim_image', 'customer_id', 'victim_status');

		$output = array();

		$main_query = "
		SELECT * FROM profile_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE victim_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR victim_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR victim_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR victim_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY victim_id, victim_first_name, victim_last_name,customer_id, victim_status DESC ';
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
			$sub_array[] = $row["victim_id"];
			$sub_array[] = decryptthis($row["victim_first_name"], $passphrase) . ' ' . decryptthis($row["victim_last_name"], $passphrase);
			$sub_array[] = '<img src="'.$row["victim_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["customer_id"];
			$status = '';
			if($row["victim_status"] == 'Active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["victim_id"].'" data-status="'.$row["victim_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["victim_id"].'" data-status="'.$row["victim_status"].'">Inactive</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["victim_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["victim_id"].'"><i class="fas fa-times"></i></button>
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
		SELECT * FROM profile_table 
		WHERE victim_id = '".$_POST["victim_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['victim_id'] = $row['victim_id'];
			$data['victim_first_name'] = decryptthis($row['victim_first_name'], $passphrase);
			$data['victim_last_name'] = decryptthis($row['victim_last_name'], $passphrase);
			$data['victim_image'] = $row['victim_image'];
			$data['victim_date_of_birth'] = decryptthis($row['victim_date_of_birth'], $passphrase);
			$data['victim_gender'] = decryptthis($row['victim_gender'], $passphrase);
			$data['victim_height'] = decryptthis($row['victim_height'], $passphrase);
			$data['customer_id'] = $row['customer_id'];
			$data['victim_status'] = $row['victim_status'];
			$data['victim_added_on'] = $row['victim_added_on'];
		}

		echo json_encode($data);
	}
	

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM profile_table 
		WHERE victim_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Victim Profile Deleted</div>';
	}
}

?>