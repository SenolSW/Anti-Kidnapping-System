<?php

//patient.php

include('../../Config.php');

$object = new Config;

if(!$object->is_login())
{
    header("location:".$object->base_url."Accounts/Admin/");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Emergency Contact Management</h1>

                    <!-- DataTales Example -->
                    <span id="error"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Emergency Contact List</h6>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="emergency_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
											<th>Emergency ID</th>
                                            <th>Name</th>
                                            <th>Email Address</th>
                                            <th>Contact No.</th>
											<th>Customer ID</th>
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

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Emergency Contact Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="emergency_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
	
	
$(document).ready(function(){

	var dataTable = $('#emergency_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"Emergency_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[2, 3, 5, 6],
				"orderable":false,
			},
		],
	});

    $(document).on('click', '.view_button', function(){

        var emergency_id = $(this).data('id');

        $.ajax({

            url:"Emergency_action.php",

            method:"POST",

            data:{emergency_id:emergency_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><th width="40%" class="text-right">Emergency ID</th><td width="60%">'+data.emergency_id+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Full Name</th><td width="60%">'+data.emergency_first_name+' '+data.emergency_last_name+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Email Address</th><td width="60%">'+data.emergency_email_address+'</td></tr>';
				
                html += '<tr><th width="40%" class="text-right">Contact No.</th><td width="60%">'+data.emergency_contact+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Address</th><td width="60%">'+data.emergency_address+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Customer ID</th><td width="60%">'+data.customer_id+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Email Verification Status</th><td width="60%">'+data.emergency_email_verify+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#emergency_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"Emergency_action.php",

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