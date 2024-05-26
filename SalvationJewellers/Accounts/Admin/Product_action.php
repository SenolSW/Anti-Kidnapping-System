<?php

include('../../Config.php');

$object = new Config;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('product_id', 'product_added_on');

		$output = array();

		$main_query = "SELECT * FROM product_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE product_id LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_type LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_category LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_added_on LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY product_name DESC ';
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
			$sub_array[] = $row["product_id"];
			$sub_array[] = '<img src="'.$row["product_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["product_name"];
			$sub_array[] = $row["product_type"];
			$sub_array[] = $row["product_category"];
			$sub_array[] = $row["product_price"];
			$sub_array[] = $row["product_added_on"];

			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["product_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["product_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["product_id"].'"><i class="fas fa-times"></i></button>
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



			$product_image = '';
			if($_FILES['product_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["product_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../../img/' . $new_name;

					move_uploaded_file($_FILES['product_image']['tmp_name'], $destination);

					$product_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["product_name"][0];
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
			    $product_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':product_name'				=>	$object->clean_input($_POST["product_name"]),
					':product_type'				=>	$object->clean_input($_POST["product_type"]),
					':product_category'			=>	$object->clean_input($_POST["product_category"]),
					':product_price'			=>	$object->clean_input($_POST["product_price"]),
					':product_image'			=>	$product_image,
					':product_added_on'			=>	$object->now
				);

				$object->query = "
				INSERT INTO product_table 
				(product_name, product_type,  product_category, product_price, product_image, product_added_on) 
				VALUES (:product_name, :product_type, :product_category, :product_price, :product_image, :product_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Product Added</div>';
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
		SELECT * FROM product_table 
		WHERE product_id = '".$_POST["product_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['product_id'] = $row['product_id'];
			$data['product_image'] = $row['product_image'];
			$data['product_name'] = $row['product_name'];
			$data['product_type'] = $row['product_type'];
			$data['product_category'] = $row['product_category'];
			$data['product_price'] = $row['product_price'];
			$data['product_added_on'] = $row['product_added_on'];
		}

		echo json_encode($data);
	}


	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM product_table 
		WHERE product_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Product Record Deleted</div>';
	}
}

if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':product_id'			=>	$_POST['hidden_id']
		);

		$object->query = "
		SELECT * FROM product_table 
		WHERE product_id != :product_id
		";

		$object->execute($data);

			$product_image = $_POST["hidden_product_image"];

			if($_FILES['product_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["product_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../../img/' . $new_name;

					move_uploaded_file($_FILES['product_image']['tmp_name'], $destination);

					$product_image = $destination;
			    }
			}

			if($error == '')
			{
				$data = array(
					':product_name'					=>	$object->clean_input($_POST["product_name"]),
					':product_type'					=>	$object->clean_input($_POST["product_type"]),
					':product_category'				=>	$object->clean_input($_POST["product_category"]),
					':product_price'				=>	$object->clean_input($_POST["product_price"]),
					':product_image'				=>	$product_image,
					':product_added_on'				=>	$object->now
				);

				$object->query = "
				UPDATE product_table  
				SET	product_name = :product_name, 
				product_type = :product_type, 
				product_category = :product_category,  
				product_price = :product_price,
				product_image = :product_image,
				product_added_on = :product_added_on
				WHERE product_id = '".$_POST['hidden_id']."'
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Product Data Updated</div>';
			}			

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

?>