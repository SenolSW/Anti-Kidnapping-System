<?php

include('../../Config.php');

$object = new Config;

if(!$object->is_login())
{
    header("location:".$object->base_url."User");
}

if($_SESSION['type'] != 'Customer')
{
    header("location:".$object->base_url."");
}

$object->query = "
    SELECT * FROM customer_table
    WHERE customer_id = '".$_SESSION["admin_id"]."'
    ";

$result = $object->get_result();
$data = array();

foreach($result as $row)
{
	$data['customer_id'] 				= $row['customer_id'];
	$data['customer_email_address'] 	= $row['customer_email_address'];
	$data['customer_password'] 			= $row['customer_password'];
	$data['customer_first_name'] 		= decryptthis($row['customer_first_name'], $passphrase);
	$data['customer_last_name'] 		= decryptthis($row['customer_last_name'], $passphrase);
	$data['customer_phone_no'] 			= decryptthis($row['customer_phone_no'], $passphrase);
	$data['customer_address'] 			= decryptthis($row['customer_address'], $passphrase);
	$data['customer_date_of_birth'] 	= decryptthis($row['customer_date_of_birth'], $passphrase);
}	


include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-10"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="customer_profile" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                        <span id="form_message"></span>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Email Address </label>
                                                    <input type="text" name="customer_email_address" id="customer_email_address" readonly class="form-control" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="customer_password" id="customer_password" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Full Name </label>
                                                    <input type="text" name="customer_name" id="customer_name" readonly class="form-control" />
                                                </div>
												<div class="col-md-6">
                                                    <label>Date of Birth </label>
                                                    <input type="date" name="customer_date_of_birth" id="customer_date_of_birth" readonly class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Contact No. <span class="text-danger">*</span></label>
                                                    <input type="tel" name="customer_phone_no" id="customer_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
												<div class="col-md-6">
                                                    <label>Address <span class="text-danger">*</span></label>
                                                    <input type="text" name="customer_address" id="customer_address" class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    $('#hidden_id').val("<?php echo $data['customer_id']; ?>");
    $('#customer_email_address').val("<?php echo $data['customer_email_address']; ?>");
    $('#customer_password').val("<?php echo $data['customer_password']; ?>");
    $('#customer_name').val("<?php echo $data['customer_first_name'] . ' ' . $data['customer_last_name']; ?>");
    $('#customer_phone_no').val("<?php echo $data['customer_phone_no']; ?>");
    $('#customer_address').val("<?php echo $data['customer_address']; ?>");
    $('#customer_date_of_birth').val("<?php echo $data['customer_date_of_birth']; ?>");
    


	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		
			$.ajax({
				url:"profile_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                    $('#customer_password').val(data.customer_password);
                    $('#customer_phone_no').val(data.customer_phone_no);
                    $('#customer_address').text(data.customer_address);
						
                    $('#message').html(data.success);

					setTimeout(function(){

				        $('#message').html('');

				    }, 5000);
				}
			})
	});

});
</script>