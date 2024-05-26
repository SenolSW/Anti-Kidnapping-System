<?php

//login_action.php

include('Config.php');

$object = new Config;

if(isset($_POST["email_address"]))
{
	sleep(2);
	$error = '';
	$url = '';
	$data = array(
		':email_address'	=>	$_POST["email_address"],
		
	);

	$object->query = "
		SELECT * FROM admin_table 
		WHERE admin_email_address = :email_address
	";

	$object->execute($data);

	$total_row = $object->row_count();

	if($total_row == 0)
	{
		$object->query = "
			SELECT * FROM customer_table 
			WHERE customer_email_address = :email_address
		";
		$object->execute($data);

		if($object->row_count() == 0)
		{
			$error = '<div class="alert alert-danger">Wrong Email Address</div>';
		}
		else
		{
			$result = $object->statement_result();

			foreach($result as $row)
			{

					if($_POST["password"] == $row["customer_password"])
					{
						$_SESSION['admin_id'] = $row['customer_id'];
						$_SESSION['type'] = 'Customer';
						$url = $object->base_url . 'Accounts/User/Victim.php';
					}
					else
					{
						$error = '<div class="alert alert-danger">Wrong Password</div>';
					}
			}
		}
	}
	else
	{
		//$result = $statement->fetchAll();

		$result = $object->statement_result();

		foreach($result as $row)
		{
			if($_POST["password"] == $row["admin_password"])
			{
				$_SESSION['admin_id'] = $row['admin_id'];
				$_SESSION['type'] = 'Admin';
				$url = $object->base_url . 'Accounts/Admin/Dashboard.php';
			}
			else
			{
				$error = '<div class="alert alert-danger">Wrong Password</div>';
			}
		}
	}

	$output = array(
		'error'		=>	$error,
		'url'		=>	$url
	);

	echo json_encode($output);
}

?>