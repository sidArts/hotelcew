<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="panel">
	<div class="panel-heading">
		Users Informations List
		<span class="page-buttons">
			<a href="<?=ADMIN_URL.'user/create'?>" class="header-button"><i class="fa fa-plus-circle"></i> Add New</a>
		</span>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<div class="table-action-btns">
				<div class="dropdown">
				  	<!-- <button class="btn btn-primary btn-flat dropdown-toggle" type="button" data-toggle="dropdown">Action
					  	<span class="caret"></span>
					 </button> -->
				  	<!-- <ul class="dropdown-menu">
				    	<li><a href="#"><i class="fa fa-trash-o"></i>Delete</a></li>
				    	<li><a href="#"><i class="fa fa-check-circle"></i>Set Active</a></li>
				    	<li><a href="#"><i class="fa fa-ban"></i>Set Inactive</a></li>
				  	</ul> -->
				</div>
			</div>
			<table class="custom-datatable table table-striped opt-table" id="myusers">
				<thead>
					<tr>
					<th width="25px" data-orderable="false">
							<input type="checkbox" id="checkAll"/><Label>All</label>
						    <label for="selectAll"><button type="button" class="btn btn-danger deleteselected">Delete</button></label>
						</th>
						<th>SL</th>
						<th>Name</th>
						<th>Role</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Image</th>
						<th>Status</th>
						<th>Action</th>
					
						<th data-orderable="false"></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	
</div>
<script>
	function deleteusers(id){

var flag = confirm("Are you sure want to delete this user");

 if(flag == true){

	  $.ajax({
		url:"<?=ADMIN_URL.'usermanagement/list/delete/'?>",
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
	  url:"<?=ADMIN_URL.'usermanagement/list/delete/'?>",
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
  var flag = confirm("Are you sure want to delete this user");

     if(flag == true){
   $('.check:checked').each(function(index,item){
		  let ids = $(item).val();
		  deletemultiple(ids);
		
		     
  });
}
});
$(document).ready(function() {
    $('#myusers').DataTable({
     
    	ajax:{
    		url:"<?=ADMIN_URL.'usermanagement/list/data'?>",
    		type : 'GET',

    	},
		"columns": [
            { "data": 0},
            { "data": 1},
            { "data": 2},
            { "data": 3},
            { "data": 4},
            { "data": 5},
            { "data": 6},
            { "data": 7},
            { "data": 8},
			// { "data": 9},
			
        ]
    	
    });
});
</script>
