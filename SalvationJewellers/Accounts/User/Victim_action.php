<?php

include('../../Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('victim_first_name','victim_last_name','victim_image', 'victim_date_of_birth','victim_status');

		$output = array();

		$main_query = "
		SELECT * FROM profile_table ";

		$search_query = 'WHERE customer_id = "'.$_SESSION["admin_id"].'" ';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND victim_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'AND victim_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY victim_status DESC ';
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
			$sub_array[] = decryptthis($row["victim_first_name"], $passphrase) . ' ' . decryptthis($row["victim_last_name"], $passphrase);
			$sub_array[] = '<img src="'.$row["victim_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = decryptthis($row["victim_date_of_birth"], $passphrase);
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
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["victim_id"].'"><i class="fas fa-edit"></i></button>
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

	if($_POST["action"] == 'Add')
	{

		$error = '';

		$success = '';

		$data = array(
			':customer_id'	=>	$_SESSION["admin_id"]
		);

		$object->query = "
		SELECT * FROM profile_table 
		WHERE customer_id = :customer_id
		";

		$object->execute($data);

		if($object->row_count() >= 3)
		{
			$error = '<div class="alert alert-danger">Cannot add more than THREE profiles</div>';
		}
		else
		{
			$victim_image = '';
			if($_FILES['victim_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["victim_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["victim_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../../img/' . $new_name;

					move_uploaded_file($_FILES['victim_image']['tmp_name'], $destination);

					$victim_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["victim_first_name"][0];
				$path = "../../img/". time() . ".png";
				$image = imagecreate(200, 200);
				$red = rand(0, 255);
				$green = rand(0, 255);
				$blue = rand(0, 255);
			    imagecolorallocate($image, 230, 230, 230);  
			    $textcolor = imagecolorallocate($image, $red, $green, $blue);
			    imagettftext($image, 100, 0, 55, 150, $textcolor, '../../font/arial.ttf', $character);
			    imagepng($image, $path);
			    imagedestroy($image);
			    $victim_image = $path;
			}

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
					':victim_first_name'		=>	$encvictim_first_name,
					':victim_last_name'			=>	$encvictim_last_name,
					':victim_image'				=>	$victim_image,
					':victim_date_of_birth'		=>	$encvictim_date_of_birth,
					':victim_gender'			=>	$encvictim_gender,
					':victim_height'			=>	$encvictim_height,
					':customer_id'				=>	$_SESSION["admin_id"],
					':victim_status'			=>	'Inactive',
					':victim_added_on'			=>	$object->now
				);

				$object->query = "
				INSERT INTO profile_table 
				(victim_first_name, victim_last_name, victim_image, victim_date_of_birth, victim_gender, victim_height, customer_id, victim_status, victim_added_on) 
				VALUES (:victim_first_name, :victim_last_name, :victim_image, :victim_date_of_birth, :victim_gender, :victim_height, :customer_id, :victim_status, :victim_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Profile Added</div>';
			}
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
		SELECT * FROM profile_table 
		WHERE victim_id = '".$_POST["victim_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['victim_id'] 				= $row['victim_id'];
			$data['victim_first_name'] 		= decryptthis($row['victim_first_name'], $passphrase);
			$data['victim_last_name'] 		= decryptthis($row['victim_last_name'], $passphrase);
			$data['victim_image'] 			= $row['victim_image'];
			$data['victim_date_of_birth'] 	= decryptthis($row['victim_date_of_birth'], $passphrase);
			$data['victim_gender'] 			= decryptthis($row['victim_gender'], $passphrase);
			$data['victim_height'] 			= decryptthis($row['victim_height'], $passphrase);
			$data['victim_status'] 			= $row['victim_status'];
		}

		echo json_encode($data);
	}
	

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':victim_id'			=>	$_POST['hidden_id']
		);
		
		$object->query = "
		SELECT * FROM profile_table 
		WHERE victim_id != :victim_id
		";

		$object->execute($data);		

			$victim_image = $_POST["hidden_victim_image"];

			if($_FILES['victim_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["victim_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["victim_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../../img/' . $new_name;

					move_uploaded_file($_FILES['victim_image']['tmp_name'], $destination);

					$victim_image = $destination;
			    }
			}

			if($error == '')
			{
				$victim_first_name			= $object->clean_input($_POST["victim_first_name"]);
				$encvictim_first_name		= encryptthis($victim_first_name, $passphrase);
				$victim_last_name			= $object->clean_input($_POST["victim_last_name"]);
				$encvictim_last_name		= encryptthis($victim_last_name, $passphrase);
				$victim_height				= $object->clean_input($_POST["victim_height"]);
				$encvictim_height			= encryptthis($victim_height, $passphrase);
				
				$data = array(
					':victim_first_name'		=>	$encvictim_first_name,
					':victim_last_name'			=>	$encvictim_last_name,
					':victim_image'				=>	$victim_image,
					':victim_height'			=>	$encvictim_height
				);

				$object->query = "
				UPDATE profile_table  
				SET victim_first_name = :victim_first_name, 
				victim_last_name = :victim_last_name,
				victim_image = :victim_image,
				victim_height = :victim_height
				WHERE victim_id = '".$_POST['hidden_id']."'
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Profile Updated</div>';
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
		DELETE FROM profile_table 
		WHERE victim_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Profile Deleted</div>';
	}

	if($_POST["action"] == 'change_status')
	{

		$data = array(
			':customer_id'			=>	$_SESSION["admin_id"],
			':status'				=>	'Active'
		);
		
		$object->query = "
		SELECT * FROM profile_table WHERE customer_id = :customer_id AND victim_status = :status
		";

		$object->execute($data);		

			if($object->row_count() == 1)
			{
				if($_POST['next_status']=='Active')
				{
					echo '<div class="alert alert-success">Cannot Have More Than ONE Active profile</div>';
					
				}else{
					$data = array(
						':victim_status'		=>	$_POST['next_status']
					);

					$object->query = "
					UPDATE profile_table 
					SET victim_status = :victim_status 
					WHERE victim_id = '".$_POST["victim_id"]."'
					";

					$object->execute($data);

					echo '<div class="alert alert-success">Profile Status have been changed to '.$_POST['next_status'].'</div>';
				}
			}else{
				$data = array(
					':victim_status'		=>	$_POST['next_status']
				);

				$object->query = "
				UPDATE profile_table 
				SET victim_status = :victim_status 
				WHERE victim_id = '".$_POST["victim_id"]."'
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Profile Status have been changed to '.$_POST['next_status'].'</div>';
			}			

	}	
	
}

?>