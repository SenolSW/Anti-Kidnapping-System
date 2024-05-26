<?php


include('../../Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('emergency_id', 'emergency_first_name', 'emergency_last_name','emergency_email_address', 'emergency_contact', 'emergency_address', 'customer_id', 'emergency_email_verify');

		$output = array();

		$main_query = "SELECT * FROM emergency_table  ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE emergency_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR emergency_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR emergency_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR emergency_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR emergency_contact LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_id LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY emergency_id, emergency_first_name, emergency_last_name, customer_id DESC ';
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
			$sub_array[] = $row["customer_id"];
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
			$data['customer_id'] = $row['customer_id'];			
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