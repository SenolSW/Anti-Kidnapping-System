<?php

session_start();
$server = "localhost";
$user = "root";
$password = "";
$database = "salvation_db";

$conn = mysqli_connect($server,$user,$password,$database);


$errorMessege="";
$Messege="";

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = 1;

   $select_cart = mysqli_query($conn, "SELECT * FROM cart_table WHERE name = '$product_name'");

   if(mysqli_num_rows($select_cart) > 0){
      $message[] = 'product already added to cart';
   }else{
      $insert_product = mysqli_query($conn, "INSERT INTO `cart`(name, price, image, quantity) VALUES('$product_name', '$product_price', '$product_image', '$product_quantity')");
      $message[] = 'product added to cart succesfully';
   }

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="stylesheet.css" type="text/css" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,700;1,600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="sb-admin-2.css">
<link rel="stylesheet" type="text/css" href="vendor/bootstrap-select/bootstrap-select.min.css"/>

<link rel="stylesheet" type="text/css" href="vendor/datepicker/bootstrap-datepicker.css"/>	
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<title>Salvation Jewellers</title>
</head>
<body>
<section id="sj-nav-bar">
<div class="header">
    <div class="navbar">
      <div class="logo">
		  <a href="Home.php"><img src="img/logo.png" width="150px"></a> 
	  </div>
      <nav id="nav_bar">
        <ul>
          <li><a href="Home.php">Home</a></li>
          <li><a href="Product.php">Products</a></li>
          <li><a href="About.php">About Us</a></li>
          <li><a href="Contact.php">Contact Us</a></li>
        </ul>
      </nav>
      <a href="Account.php"><button class="btn" id="sign-in/up-btn"> Sign in </button></a> 
     </div>

</div>
</section>
<?php

if(isset($message)){
   foreach($message as $message){
      echo '<div class="message"><span>'.$message.'</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i> </div>';
   };
};

?>


<h2 class="title">Popular Products</h2>
<div class="testimonial_products">
	<div class="small-container">

      <?php
      $select_products = mysqli_query($conn, "SELECT * FROM product_table");

         while($fetch_product = mysqli_fetch_assoc($select_products)){
      ?>
		
      <form action="" method="post">

		  <div class="row">
    		<div class="col-3">
            <img src="img/<?php echo $fetch_product['product_image']; ?>" alt="">
            <h3><?php echo $fetch_product['product_name']; ?></h3>
            <h3>$<?php echo $fetch_product['product_price']; ?>/-</h3>
			<input type="hidden" name="product_id" value="<?php echo $fetch_product['product_id']; ?>"/>
            <input type="hidden" name="product_name" value="<?php echo $fetch_product['product_name']; ?>"/>
            <input type="hidden" name="product_price" value="<?php echo $fetch_product['product_price']; ?>"/>
            <input type="hidden" name="product_image" value="<?php echo $fetch_product['product_image']; ?>"/>
			<button type="button" name="add_order" id="add_order" class="btn" >PURCHASE</button>
         </div>
		</div>
	
      </form>

      <?php
         };
      ?>
	   
	   </div>
	   </div>
<?php include("Footer.php"); ?>

<div id="productModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="product_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Product Purchase</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="customer_email_address" id="customer_email_address" class="form-control" required />
                            </div>
							<div class="col-md-6">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="customer_password" id="customer_password" class="form-control" required />
                            </div>
                        </div>
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<script>
$(document).ready(function(){
	
	$('#add_order').click(function(){

		$('#product_form')[0].reset();

		$('#modal_title').text('Product Purchase');

		$('#action').val('Add');

		$('#submit_button').val('PURCHASE');

		$('#productModal').modal('show');

		$('#form_message').html('');
		
	});		

	$('#product_form').on('submit', function(event){
		event.preventDefault();
	
			$.ajax({
				url:"Product_action.php",
				method:"POST",
				data: new FormData(this),
				dataType:'json',
                contentType: false,
                cache: false,
                processData:false,
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#productModal').modal('hide');
						$('#message').html(data.success);
						$('#product_form')[0].reset();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
	});
});
</script>