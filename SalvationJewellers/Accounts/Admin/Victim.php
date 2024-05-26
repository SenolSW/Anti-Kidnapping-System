<?php

//doctor.php

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
                    <h1 class="h3 mb-4 text-gray-800">Profile Management</h1>

                    <!-- DataTales Example -->
                    <span id="error"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Added Profile List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_victim_profile" id="add_victim_profile" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="profile_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
											<th>Profile ID</th>
											<th>Name</th>
                                            <th>Profile Image</th>
											<th>Customer ID</th>
											<th>Status</th>
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
                <h4 class="modal-title" id="modal_title">View Profile</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="victim_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
	
$(document).ready(function(){

	var dataTable = $('#profile_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"Victim_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
                "targets":[2, 5],
				"orderable":false
            }
				
		],
	});

    $(document).on('click', '.view_button', function(){

        var victim_id = $(this).data('id');

        $.ajax({

            url:"Victim_action.php",

            method:"POST",

            data:{victim_id:victim_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><th width="40%" class="text-right">Profile ID</th><td width="60%">'+data.victim_id+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Image</th><td colspan="2" class="text-center"><img src="'+data.victim_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Full Name</th><td width="60%">'+data.victim_first_name+' '+data.victim_last_name+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Date of Birth</th><td width="60%">'+data.victim_date_of_birth+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Gender</th><td width="60%">'+data.victim_gender+'</td></tr>';
				
                html += '<tr><th width="40%" class="text-right">Height</th><td width="60%">'+data.victim_height+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Customer ID</th><td width="60%">'+data.customer_id+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Profile Status</th><td width="60%">'+data.victim_status+'</td></tr>';
				
				html += '<tr><th width="40%" class="text-right">Added On</th><td width="60%">'+data.victim_added_on+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#victim_details').html(html);

            }

        })
    });
	
	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"Victim_action.php",

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