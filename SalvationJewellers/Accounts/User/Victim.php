<?php

//doctor.php

include('../../Config.php');

$object = new Config;

if(!$object->is_login())
{
    header("location:".$object->base_url."Admin");
}

if($_SESSION['type'] != 'Customer')
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
											<th>Name</th>
                                            <th>Profile Image</th>
											<th>Date of Birth</th>
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

<div id="victimModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="victim_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Profile</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>First Name <span class="text-danger">*</span></label>
                                <input type="text" name="victim_first_name" id="victim_first_name" class="form-control" required />
                            </div>
							<div class="col-md-6">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="victim_last_name" id="victim_last_name" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="victim_date_of_birth" id="victim_date_of_birth" class="form-control" />
                            </div>
							<div class="col-md-6">
                                <label>Gender <span class="text-danger">*</span></label>
								<select name="victim_gender" id="victim_gender" class="form-control" >
									<option value="" disabled selected hidden>Select </option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
                            </div>
							<div class="col-md-6">
                                <label>Height <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Height in cm " data-format="height" name="victim_height" id="victim_height" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Profile Image <span class="text-danger">*</span></label>
                        <br />
                        <input type="file" name="victim_image" id="victim_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_victim_image" id="hidden_victim_image" />
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
	
	$('#victim_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

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
                "targets":[1, 2, 4],
				"orderable":false
            }
				
		],
	});
	
	$('#add_victim_profile').click(function(){

		$('#victim_form')[0].reset();

		$('#modal_title').text('Add Profile');

		$('#action').val('Add');

		$('#submit_button').val('Add');

		$('#victimModal').modal('show');

		$('#form_message').html('');

	});		

	$('#victim_form').on('submit', function(event){
		event.preventDefault();
	
			$.ajax({
				url:"Victim_action.php",
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
					$('#victim_form')[0].reset();
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#victimModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
	});

	$(document).on('click', '.edit_button', function(){

		var victim_id = $(this).data('id');

		$('#form_message').html('');

		$.ajax({

	      	url:"Victim_action.php",
	      	method:"POST",
	      	data:{victim_id:victim_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{

                $('#victim_first_name').val(data.victim_first_name);
				$('#victim_last_name').val(data.victim_last_name);
				$('#victim_date_of_birth').val(data.victim_date_of_birth);
				$('#victim_gender').val(data.victim_gender);
                $('#victim_height').val(data.victim_height);
	            $('#uploaded_image').html('<img src="'+data.victim_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_victim_image').val(data.victim_image);


	        	$('#modal_title').text('Edit Profile');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#victimModal').modal('show');

	        	$('#hidden_id').val(victim_id);

	      	}

	    })

	});	

	$(document).on('click', '.status_button', function(){
		var victim_id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'Inactive';
		if(status == 'Inactive')
		{
			next_status = 'Active';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{

      		$.ajax({

        		url:"Victim_action.php",
        		method:"POST",
        		data:{victim_id:victim_id, action:'change_status', status:status, next_status:next_status},

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
				
				html += '<tr><th width="40%" class="text-right">Profile Status</th><td width="60%">'+data.victim_status+'</td></tr>';

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