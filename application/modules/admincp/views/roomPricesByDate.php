<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php //print_r($stores); exit;?>
<div class="panel">
	<div class="panel-heading">
		Room Prices by Day of Week
		<span class="page-buttons">
			<a href="<?=ADMIN_URL.'stores/create'?>" class="header-button"><i class="fa fa-plus-circle"></i> Add New</a>
		</span> 
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="custom-datatable table table-striped opt-table" id="store_table">
				<thead>
					<tr>
						<th width="25px" data-orderable="false">
							<input type="checkbox" id="checkAll"/><Label>All</label>

						    <label for="selectAll"><button type="button" class="btn btn-danger deleteselected">Delete</button></label>
						</th>

						<th>S.L</th>
						<th>Date</th>
						<th>Name</th>
						<th>Capacity</th>
						<th>Size</th>
						<th>Rate</th>
						<th>GST</th>						
						<th>New Rate</th>
						<th>Total Rooms</th>
						<th>Action</th>						
					</tr>
					
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	
</div>


<script>
function deleteWeeklyRate(id) {
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

function deleteMultiple(id) {
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


$(document).ready(function() {
    $('#store_table').DataTable({     
    	ajax:{
    		url:"<?=ADMIN_URL.'user/roomPricesByDateAPI'?>",
    		type : 'GET'
    	},
		"columns": [
            {"data": 0},
            {"data": 1},
            {"data": 2},
            {"data": 3},
            {"data": 4},
            {"data": 5},
            {"data": 6},
            {"data": 7},
            {"data": 8},
            {"data": 9},
            {"data": 10},
            // {"data": 11},
            // {"data": 12}
        ]
    });

    $("#checkAll").click(function(){
	    $('.check').not(this).prop('checked', this.checked);
	});

	$(".deleteselected").click(function() {
	   	$('.check:checked').each(function(index,item){
			let ids = $(item).val();
			deletemultiple(ids);		     
	  	});
	});
});

</script>