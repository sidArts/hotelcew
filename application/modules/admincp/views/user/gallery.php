<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php //print_r($stores); exit;?>
<div class="panel">
	<div class="panel-heading">
		Gallery List
		<span class="page-buttons">
			<a href="<?=ADMIN_URL.'gallery/create'?>" class="header-button"><i class="fa fa-plus-circle"></i> Add New</a>
		</span>
	</div>
	<div class="panel-body">
		<div class="row">
			<?php
			foreach ($gallery as $key => $value) {
				?>
				<div class="col-md-3">
					<div class="gallery-item">
						<img src="<?=base_url().'public/uploads/gallery/'.$value['image'] ?>">
						<button type='button' class='btn btn-danger' id='del-<?= $value["id"]?>' onclick='deleteusers("<?=$value['id']?>")'><i class='fa fa-trash'></i></button>
					</div>
					
				</div>
				<?php
			}

			?>
		</div>
		<div class="table-responsive">
			<!-- <div class="table-action-btns">
				<div class="dropdown">
				  	<button class="btn btn-primary btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action
					  	<span class="caret"></span>
					 </button>
				  	<ul class="dropdown-menu">
				    	<li><a href="#"><i class="fa fa-trash-o"></i>Delete</a></li>
				    	<li><a href="#"><i class="fa fa-check-circle"></i>Set Active</a></li>
				    	<li><a href="#"><i class="fa fa-ban"></i>Set Inactive</a></li>
				  	</ul>
				</div>
			</div> -->
			<!-- <table class="custom-datatable table table-striped opt-table" id="store_table">
				<thead>
					<tr>
						<th width="25px" data-orderable="false">
							<input type="checkbox" id="checkAll"/><Label>All</label>

						    <label for="selectAll"><button type="button" class="btn btn-danger deleteselected">Delete</button></label>
						</th>

						<th>S.L</th>
						<th>Image</th>
						<th>Action</th>
						
					</tr>
					
				</thead>
				<tbody>
					
				</tbody>
			</table> -->
		</div>
	</div>
	
</div>


<script>
function deleteusers(id){

	var flag = confirm("Are you sure want to delete this store");

     if(flag == true){
         

          $.ajax({
            url:"<?=ADMIN_URL.'user/delete_gallery/'?>",
            type: 'post',
            data:{id:id},
            datatype: 'json'
        })
        .done(function (data) { 
         
            location.reload(true)
         })
        .fail(function (jqXHR, textStatus, errorThrown) { 
         console.log('error');
         });

     }
	
}

function deletemultiple(id){
	

          $.ajax({
            url:"<?=ADMIN_URL.'user/delete_user/'?>",
            type: 'post',
            data:{id:id},
            datatype: 'json'
        })
        .done(function (data) { 
         
            location.reload(true)
         })
        .fail(function (jqXHR, textStatus, errorThrown) { 
         console.log('error');
         });

     
	
}

$("#checkAll").click(function(){
    $('.check').not(this).prop('checked', this.checked);
    
   
   
});
$(".deleteselected").click(function(){
 // var flag = confirm("Are you sure want to delete this store");

    // if(flag == true){
   $('.check:checked').each(function(index,item){
		  let ids = $(item).val();
		  deletemultiple(ids);
		     
  });
//}
});
$(document).ready(function() {
    $('#store_table').DataTable({
     
    	ajax:{
    		url:"<?=ADMIN_URL.'user/gallerylist/'?>",
    		type : 'GET',

    	},
    	 "columns": [
            { "data": 0},
            { "data": 1},
            { "data": 2 },
             { "data": 3},
            // { "data": 4},
            // { "data": 5},
            // { "data": 6},
            // { "data": 7},
            // { "data": 8},
            // { "data": 9}
        ]
    });
});

</script>