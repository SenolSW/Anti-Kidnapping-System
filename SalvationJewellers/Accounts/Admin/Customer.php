<?php

//patient.php

include('../../Config.php');

$object = new Config;

if(!$object->is_login())
{
    header("location:".$object->base_url."Admin");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Customer Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Customer List</h6>
                            	</div>
                            	<div class="col" align="right">
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="customer_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
											<th>Customer ID</th>
                                            <th>Full Name</th>
                                            <th>Email Address</th>
                                            <th>Contact No.</th>
                                            <th>Email Verification Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="patientModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="patient_form">
      		<div class="modal-content">
        		<div class="modal-header">
                    <h4 class="modal-title" id="modal_title"></h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Customer Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="customer_email_address" id="customer_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Customer Password <span class="text-danger">*</span></label>
                                <input type="password" name="customer_password" id="customer_password" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Customer First Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_first_name" id="customer_first_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Customer Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_last_name" id="customer_last_name" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Customer Date of Birth </label>
                                <input type="text" name="customer_date_of_birth" id="customer_date_of_birth" readonly class="form-control" />
                            </div>                          
                            <div class="col-md-6">
                                <label>Customer Phone No <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone_no" id="customer_phone_no" class="form-control" required data-parsley-trigger="keyup" />
                            </div>

                        </div>
                    </div>                    
                    <div class="form-group">
                        <div class="row">
                         <div class="col-md-6">
                                <label>Customer Address<span class="text-danger">*</span></label>
							<textarea name="customer_address" id="customer_address" class="form-control" required data-parsley-trigger="keyup"></textarea>
                            </div>
                        </div>
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Customer Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="customer_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
$(document).ready(function(){

	var dataTable = $('#customer_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"Customer_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[1, 3, 5],
				"orderable":false,
			},
		],
	});

    $(document).on('click', '.view_button', function(){

        var customer_id = $(this).data('id');

        $.ajax({

            url:"Customer_action.php",

            method:"POST",

            data:{customer_id:customer_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';
				
				html += '<tr><th width="40%" class="text-right">Customer ID</th><td width="60%">'+data.customer_id+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Email Address</th><td width="60%">'+data.customer_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Password</th><td width="60%">'+data.customer_password+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Patient Name</th><td width="60%">'+data.customer_first_name+' '+data.customer_last_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Contact No.</th><td width="60%">'+data.customer_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Address</th><td width="60%">'+data.customer_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Date of Birth</th><td width="60%">'+data.customer_date_of_birth+'</td></tr>';
				
                html += '<tr><th width="40%" class="text-right">Gender</th><td width="60%">'+data.customer_gender+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Email Verification Status</th><td width="60%">'+data.email_verify+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#customer_details').html(html);

            }

        })
    });
	

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"Customer_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

        		success:function(data)
        		{

          			$('#message').html(data);

          			dataTable.ajax.reload();

          			setTimeout(function(){

            			$('#message').html('');

          			}, 5000);

        		}

      		})

    	}

  	});



});
</script>