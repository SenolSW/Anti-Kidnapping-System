<?php

//doctor.php

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
                    <h1 class="h3 mb-4 text-gray-800">Product Management</h1>

                    <!-- DataTales Example -->
                    <span id="error"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Product List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_product" id="add_product" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="product_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
											<th>Product ID</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Price</th>
											<th>Added On</th>
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

<div id="productModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="product_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Product</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label>Type <span class="text-danger">*</span></label>
                                <input type="text" name="product_type" id="product_type" class="form-control" required />
                            </div>                            
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Category </label>
                                <input type="text" name="product_category" id="product_category" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label>Price </label>
                                <input type="text" name="product_price" id="product_price" class="form-control" required />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image <span class="text-danger">*</span></label>
                        <br />
                        <input type="file" name="product_image" id="product_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_product_image" id="hidden_product_image" />
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
                <h4 class="modal-title" id="modal_title">View Product Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="product_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#product_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"Product_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[1,7],
				"orderable":false,
			},
		],
	});

	$('#add_product').click(function(){
		
		$('#product_form')[0].reset();

    	$('#modal_title').text('Add Product');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

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
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
	});
	

    $(document).on('click', '.view_button', function(){
        var product_id = $(this).data('id');

        $.ajax({

            url:"Product_action.php",

            method:"POST",

            data:{product_id:product_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

				html += '<tr><th width="40%" class="text-right">Product ID</th><td width="60%">'+data.product_id+'</td></tr>';
				
                html += '<tr><td colspan="2" class="text-center"><img src="'+data.product_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Name</th><td width="60%">'+data.product_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Type</th><td width="60%">'+data.product_type+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Category</th><td width="60%">'+data.product_category+'</td></tr>';
                html += '<tr><th width="40%" class="text-right">Price</th><td width="60%">'+data.product_price+'</td></tr>';
                html += '<tr><th width="40%" class="text-right">Added On</th><td width="60%">'+data.product_added_on+'</td></tr>';


                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#product_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"Product_action.php",

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

	$(document).on('click', '.edit_button', function(){

		var product_id = $(this).data('id');

		$('#form_message').html('');

		$.ajax({

	      	url:"Product_action.php",
	      	method:"POST",
	      	data:{product_id:product_id, action:'fetch_single'},
	      	dataType:'JSON',
	      	success:function(data)
	      	{

                $('#product_name').val(data.product_name);
                $('#product_type').val(data.product_type);
				$('#product_category').val(data.product_category);
				$('#product_price').val(data.product_price);
	            $('#uploaded_image').html('<img src="'+data.product_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_product_image').val(data.product_image);


	        	$('#modal_title').text('Edit Product Details');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#productModal').modal('show');

	        	$('#hidden_id').val(product_id);

	      	}

	    })

	});	

});
	

	
</script>