<?php

include('../../Config.php');

$object = new Config;

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Feedback Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Feedback List</h6>
                            	</div>
                            	<div class="col" align="right">
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="feedback_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Feedback ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Feedback</th>
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


<div id="feedbackModal" class="modal fade">
    <div class="modal-dialog">
		<form method="post" id="feedback_form">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">Feedback Reply</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" >
			  <span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>To</label>
                                <input type="text" name="email" id="email" class="form-control" readonly />
                            </div>
							<div class="col-md-6">
                                <label>Subject</label>
                                <input type="tel" name="feedback" id="feedback" class="form-control" readonly  />
                            </div>
		          		</div>
		          	</div>                                   
                    <div class="form-group">
                         <div class="row">	
                        <div class="col-md-6">
                                <label>Reply<span class="text-danger">*</span></label>
								<textarea name="reply_msg" id="reply_msg" class="form-control" required data-parsley-trigger="keyup"></textarea>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" value="reply" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="reply" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        	</div>
        </div>
		</form>
    </div>
</div>


<script>
$(document).ready(function(){
	
	var dataTable = $('#feedback_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"Feedbacks_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[2, 3, 4],
				"orderable":false,
			},
		],
	});

    $(document).on('click', '.reply_button', function(){
        var feedback_id = $(this).data('id');

        $.ajax({

            url:"Feedback_action.php",

            method:"POST",

            data:{feedback_id:feedback_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {

                $('#feedbackModal').modal('show');

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"Feedbacks_action.php",

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