<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style type="text/css">
	.no-pointer-events {
		pointer-events: none;
	}
</style>
<div class="panel">
	<div class="panel-heading">
		Booking Informations List
		<!-- <span class="page-buttons">
			<a href="<?=ADMIN_URL.'ride/create'?>" class="header-button"><i class="fa fa-plus-circle"></i> Add New</a>
		</span> -->
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
			<div class="table-responsive" style="overflow: scroll;">
				<table class="custom-datatable table table-striped opt-table" id="rides">
				<thead>
					<tr>
					<!-- <th width="25px" data-orderable="false">
							<input type="checkbox" id="checkAll"/><Label>All</label>
						    <label for="selectAll"><button type="button" class="btn btn-danger deleteselected">Delete</button></label>
						</th> -->
						<!-- <th>SL</th> -->
						<th>Booking no</th>
						<th>Name</th>
						<th>Mobile</th>
						<!-- <th>Email</th> -->
						<th>Start Date</th>
						<th>End Date</th>
						<th>Book date</th>
						<th>Rooms</th>
						<!-- <th>Person</th> -->
						<th>Amount</th>
						<th>status</th>
						<th>Action</th>
						<!-- <th>Image</th>
						<th>Minimum Time</th>
						<th>Ride Base Price</th>
						<th>Extra Cost</th>
						<th>Status</th>
						 -->
						<!-- $row->booking_no,
                $row->customer_name,
                $row->customer_mobile,
                $row->booking_no,
                $row->customer_email,
                $row->booking_start_date,
                $row->booking_end_date,
                $row->booking_date,
                $row->no_of_room,
                $row->no_of_person,
                $row->total_cost,
                $row->status, -->
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			</div>
		</div>
	</div>
	
</div>

<!-- Modal -->
<div id="booking_details_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Booking Details</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-booking-status-modal">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Booking Status</h5>
      </div>
      <div class="modal-body">
      	<form id="update-booking-status-form">
      		<input type="hidden" name="booking_id">
	        <div class="form-group">
	        	<label>Status</label>
	        	<select class="form-control" name="status_id">
	        		<option>asdfadf</option>
	        	</select>
	        </div>
	        <div class="form-group">
	        	<label>Comments</label>
	        	<textarea class="form-control" name="comments"></textarea>
	        </div>
	        
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submit-update-booking-status-form" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>

<div id="booking_cancel_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reason for Cancellation</h4>
      </div>
      <div class="modal-body">
        <ul class="list-group">
        	<li class="list-group-item">
        		<input type="checkbox" name="invalid-documents-checkbox" id="invalid-documents-checkbox"> Invalid or Irrelevant Documents
        	</li>
        	<li class="list-group-item">
        		<input type="checkbox" name="bad-behaviour-checkbox" id="bad-behaviour-checkbox"> Bad Behaviour
        	</li>
        	<li class="list-group-item">
        		<input type="checkbox" name="bad-behaviour-checkbox" id="others-checkbox"> Other Comments
        		<textarea class="form-control" id="booking-cancel-other-comments"></textarea>
        	</li>
        </ul>
        <input type="hidden" id="booking-id-to-cancel" name="booking-id" value="">
      </div>
      <div class="modal-footer">
      	<input type="button" class="btn btn-primary" value="Submit" id="cancellation-reason-btn">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script>
	function deleteusers(id){

var flag = confirm("Are you sure want to delete this store");

 if(flag == true){
	 

	  $.ajax({
		url:"<?=ADMIN_URL.'users/ride/remove/'?>",
		type: 'post',
		data:{id:id},
		datatype: 'json'
	})
	.done(function (data) { 
	console.log(data);
		location.reload(true)
	 })
	.fail(function (jqXHR, textStatus, errorThrown) { 
	 console.log('error');
	 });

 }

}
	function deletemultiple(id){
	

	$.ajax({
	  url:"<?=ADMIN_URL.'users/ride/remove/'?>",
	  type: 'post',
	  data:{id:id},
	  datatype: 'json'
  })
  .done(function (data) {
  console.log(data); 
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
  //var flag = confirm("Are you sure want to delete this store");

    // if(flag == true){
   $('.check:checked').each(function(index,item){
		  let ids = $(item).val();
		  deletemultiple(ids);
		
		     
  });
//}
});

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}
$(document).ready(function() {
	$('#submit-update-booking-status-form').click(() => {
		var updateBookingformData = getFormData($('#update-booking-status-form'));
		var url = '/admincp/BookingAPI/setStatus';
		showHideLoader('show');
		$.ajax({
            type: 'POST',
            url: url,
            data: JSON.stringify(updateBookingformData),
            contentType: "application/json",
            dataType: 'json',
            success: (data) => {
				console.log(data);
				bookingsDatatable.ajax.reload();
				$('#edit-booking-status-modal').modal('hide');
				showHideLoader('hide');
			},
			error: err => { 
				console.log(err);
				showHideLoader('hide');
			}
        });
	});

	$('body').on('click', '.edit-booking-status', function() {
		var bookingId = $(this).attr('booking-id');
		var $select = $('#update-booking-status-form > div.form-group > select[name="status_id"]');
		$('#update-booking-status-form > input[type="hidden"]').val(bookingId);
		$select.html('');
		showHideLoader('show');
		$.get(`/admincp/BookingAPI/getChildStatusList/${bookingId}`, (data) => {
			data.forEach(d => {
				$select.append(
					$('<option>', {'text': d.name, 'value': d.status_id})
				);
			})
			
			$('#edit-booking-status-modal').modal('show');
			showHideLoader('hide');
		});
		
	})
	$('#cancellation-reason-btn').click(() => {
		let desc = '';
		if($('#invalid-documents-checkbox').prop('checked')) {
			desc += 'Invalid or Irrelevant Documents. ';
		}
		if($('#bad-behaviour-checkbox').prop('checked')) {
			desc += 'Bad Behaviour. ';
		}
		if($('#others-checkbox').prop('checked')) {
			if($('#booking-cancel-other-comments').val().trim() === '')
				return;
			desc += $('#booking-cancel-other-comments').val().trim();
		}
		if(desc != '') {
			$.ajax({
				url:"<?=ADMIN_URL.'users/ride/cancelBooking'?>",
				type: 'post',
				data:{id: $('#booking-id-to-cancel').val(), description: desc},
				success:function(response)
				{
					location.reload();
				}
			});	
		}		
	});

	$.fn.dataTable.ext.buttons.refresh = {
	    text: 'Refresh'
	  , action: function ( e, dt, node, config ) {
	      dt.clear().draw();
	      dt.ajax.reload();
	    }
	};
    var bookingsDatatable = $('#rides').DataTable({
    	"aaSorting": [],
     	dom: 'Bfrtip',
        buttons: [
            //'csv', 'excel', 'pdf'
            //'csv',
            {
                extend: 'excelHtml5',
                title:'HA-<?=date("Y-m-d")?>',
                exportOptions: {
                    columns: [0,1, 2, 3,4,5,6,7,8 ]
                }
            },
            {
                extend: 'pdfHtml5',
                title:'HA-<?=date("Y-m-d")?>',
                exportOptions: {
                    columns: [0,1, 2, 3,4,5,6,7,8 ]
                }
            },
            {
			    text: "<i class='fa fa-refresh'></i>",
			    action: function (e, dt, node, config) {
			        dt.ajax.reload(null, false);
			    }
			}
            //'colvis'
        ],
    	ajax:{
    		url:"<?=ADMIN_URL.'user/ride/list/'?>",
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
			{ "data": 9}
        ]
    });
});
</script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.colVis.min.js"></script>







<script type="text/javascript">
	function checkout(id) {
		$.ajax({
		  url:"<?=ADMIN_URL.'users/ride/checkout/'?>",
		  type: 'post',
		  data:{id:id},
		  success:function(response)
		  {
		  	location.reload();
		  }
	  })
	}
</script>
<script type="text/javascript">
	function booking_details(id) {
		$.ajax({
		  url:"<?=ADMIN_URL.'users/ride/booking_details/'?>",
		  type: 'post',
		  data:{id:id},
		  success:function(response)
		  {
		  	console.log(response);
		  	$('#booking_details_modal .modal-body').html(response);
		  	$('#booking_details_modal').modal('show');
		 // 	location.reload();
		  }
	  })
	}
	var cancelBooking = (id) => {
		$('#booking-id-to-cancel').val(id);
		$('#booking_cancel_modal').modal('show');
	};
</script>
