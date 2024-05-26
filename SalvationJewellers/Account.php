<?php

include('Config.php');

$object = new Config;

?>

<?php include("Header.php"); ?>

<div class="container">
  <div class="forms-container">
    <div class="signin-signup">
    <?php
			if(isset($_SESSION["success_message"]))
			{
				echo $_SESSION["success_message"];
				unset($_SESSION["success_message"]);
			}
			?>    
      <form method="post" id="customer_login_form" class="sign-in-form">
        <h2 class="title">Sign in</h2>
		<span id="message"></span>
        <div class="input-field"> <i class="fa fa-envelope"></i>
          <input type="email" name="email_address" id="email_address" class="form-control" placeholder="Email" required autofocus data-parsley-type="email" data-parsley-trigger="keyup" />
        </div>
        <div class="input-field"> <i class="fa fa-lock"></i>
          <input type="password" name="password" id="password" class="form-control" placeholder="Password" required  data-parsley-trigger="keyup" />
        </div>
        <input type="hidden" name="action" value="Login" />
        <input type="submit" name="customer_login_button" id="customer_login_button" class="btn btn-primary" value="Login" />
		  
        <p class="social-text">Or</p>
        <div class="social-media">
			<a href="#" class="social-icon"> <i class="fa fa-google"></i> </a> 
			<a href="#" class="social-icon"> <i class="fa fa-facebook-f "></i> </a> 
			<a href="#" class="social-icon"> <i class="fa fa-twitter"></i> </a> 
		</div>
      </form>
		
      <form method="post" id="customer_register_form" class="sign-up-form">
        <h2 class="title">Sign up</h2>
		<span id="message"></span>
        <div class="input-field"> <i class="fa fa-envelope"></i>
          <input type="email" name="customer_email_address" id="customer_email_address" class="form-control" placeholder="Email Address" required autofocus  />
        </div>
        <div class="input-field"> <i class="fa fa-lock"></i>
          <input type="password" name="customer_password" id="customer_password" class="form-control" placeholder="Password" required />
        </div>
        <div class="input-field"> <i class="fa fa-user"></i>
          <input type="text" name="customer_first_name" id="customer_first_name" class="form-control" placeholder="First Name" required  data-parsley-trigger="keyup" />
        </div>
        <div class="input-field"> <i class="fa fa-user"></i>
          <input type="text" name="customer_last_name" id="customer_last_name" class="form-control" placeholder="Last Name" required  data-parsley-trigger="keyup" />
        </div>
        <div class="input-field"> <i class="fa fa-birthday-cake"></i>
          <input type="date" name="customer_date_of_birth" id="customer_date_of_birth" class="form-control" placeholder="Date of Birth" required  data-parsley-trigger="keyup" />
        </div>
        <div class="input-field"> <i class="fa fa-phone"></i>
          <input type="text" name="customer_phone_no" id="customer_phone_no" class="form-control" placeholder="Contact No." required  data-parsley-trigger="keyup" />
        </div>
        <div class="textarea-field"> <i class="fa fa-address-card-o"></i>
          <textarea name="customer_address" id="customer_address" class="form-control" placeholder="Address" required data-parsley-trigger="keyup"></textarea>
        </div>
        <input type="hidden" name="action" value="customer_register" />
        <input type="submit" name="customer_register_button" id="customer_register_button" class="btn btn-primary" value="Register" />
      </form>
    </div>
  </div>
  <div class="panels-container">
    <div class="panel left-panel">
      <div class="content">
        <h3>New Here ?</h3>
        <p> Register now to get access to tools and resources to help you manage your plan and your health. </p>
        <button class="btn transparent" id="sign-up-btn"> Sign up </button>
      </div>
      <img src="img/SignIn.png" class="image" alt="" /> </div>
    <div class="panel right-panel">
      <div class="content">
        <h3>One Of Us ?</h3>
        <p> See a personalized view of your Medicare benefits. Sign In to your Healthcare account. </p>
        <button class="btn transparent" id="sign-in-btn"> Sign in </button>
      </div>
      <img src="img/SignUp.png" class="image" alt="" /> </div>
  </div>
</div>
<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script type="text/javascript" src="vendor/datepicker/bootstrap-datepicker.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="app.js"></script> 


<?php include("Footer.php"); ?>

<script>

$(document).ready(function(){

	$('#customer_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });



	$('#customer_register_form').on('submit', function(event){

		event.preventDefault();

			$.ajax({
				url:"action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function(){
					$('#customer_register_button').attr('disabled', 'disabled');
				},
				success:function(data)
				{
					$('#customer_register_button').attr('disabled', false);
					$('#customer_register_form')[0].reset();
					if(data.error !== '')
					{
						$('#message').html(data.error);
					}
					if(data.success != '')
					{
						$('#message').html(data.success);
					}
				}
			});


	});

});

</script>

<script>

$(document).ready(function(){

    $('#customer_login_form').on('submit', function(event){
        event.preventDefault();
  
            $.ajax({
                url:"login_action.php",
                method:"POST",
                data:$(this).serialize(),
                dataType:'json',
                beforeSend:function()
                {
                    $('#customer_login_button').attr('disabled', 'disabled');
                    $('#customer_login_button').val('wait...');
                },
                success:function(data)
                {
                    $('#customer_login_button').attr('disabled', false);
                    if(data.error != '')
                    {
                        $('#error').html(data.error);
                        $('#customer_login_button').val('Login');
                    }
                    else
                    {
                        window.location.href = data.url;
                    }
                }
            })
    });

});


</script>