<?php
session_start();
$server = "localhost";
$user = "root";
$password = "";
$database = "salvation_db";

$con = mysqli_connect($server,$user,$password,$database);

	 $Name= "";
	 $Email="";
	 $Feedback="";

$errorMessege="";
$Messege="";
if($con){


//---------------Customer feedback------------------------------
	if(isset($_POST['btnFeedback'])){
		
	 $name= $_POST['name'];
	 $email= $_POST['email'];	 
	 $feedback= $_POST['feedback'];
	 
	 
    	$sql="INSERT INTO feedback_table(name, email, feedback) VALUES ('".$name."','".$email."','".$feedback."')";
		
		if(mysqli_query($con,$sql)){
			header("Location:Contact.php?nodetails=Feedback sent!.");
		}else{
		   $Messege="Error :".mysqli_error($con);
		}
	}
}
else{
	$Messege= "Error ".mysqli_connect_error();
}


?>