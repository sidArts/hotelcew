<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php //print_r($stores); exit;?>
<div class="panel">
	<div class="panel-heading">
		Room Prices by Day of Week
		<span class="page-buttons">
			<button data-toggle="modal" data-target="#open-create-new-room-price-modal" class="header-button" id="open-create-new-room-price">
				<i class="fa fa-plus-circle"></i> Add New
			</button>
		</span> 
		<span class="page-buttons-2">
			<label>Room Filter</label>
			<select name="room_id" id="room-id-filter"></select>
		</span> 
		
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="custom-datatable table table-striped opt-table" id="room-prices-by-day-of-week-table">
				<thead>
					<tr>
						<!-- <th width="25px" data-orderable="false">
							<input type="checkbox" id="checkAll"/><Label>All</label>

						    <label for="selectAll"><button type="button" class="btn btn-danger deleteselected">Delete</button></label>
						</th> -->

						<th>S.L</th>
						<th>Day of Week</th>
						<th>Room Type</th>
						<th>Old Rate</th>											
						<th>New Rate</th>
						<th>Capacity</th>
						<th>Size</th>						
						<th>GST</th>
						<th>Total Rooms</th>
						<th>Action</th>						
					</tr>
					
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
	
</div>

<!-- CREATE NEW Room Price by date Modal -->

<div id="open-create-new-room-price-modal" class="modal fade enquiryform">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Set Room Price</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body contact_form1">
				<form id="create-new-room-price-form" novalidate>
					<input type="hidden" name="room_id" value="" id="room_id">
					<div class="form-group">
						<label>Select Room:<span>*</span></label>
						<select class="form-control" name="room_id" id="room-id-select"></select>
					</div>
					<div class="form-group">
						<label>Day of Week:<span>*</span></label>
						<select class="form-control" name="day_of_week" id="day-of-week-select"></select>
					</div>
					<div class="form-group">
						<label for="phone">Rate:<span>*</span></label>
						<input type="text" class="form-control numbersOnly" id="rate" name="rate" autocomplete="off">
					</div>
					<button type="button" id="submit-room-prices" class="btn btn-warning">
						Submit
					</button>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<script>
var datatable;
function deleteOne(id) {
	bootbox.confirm('Do you really want to delete this entry?', result => {
		if(!result)
			return;
		$.ajax({
	        url:"<?=ADMIN_URL.'user/deleteRoomPricesByDayOfWeekAPI/'?>" + id,
	        type: 'get'
	    })
	    .done(function (data) {         
	        datatable.ajax.reload();
	 	})
	    .fail(function (jqXHR, textStatus, errorThrown) { 
	 		console.log('error');
	 	});	
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

var getAllRoomTypes = () => {
	$.ajax({
		url: "<?=ADMIN_URL.'user/roomListAPIJSON'?>",
		dataType: "json",
		method: 'GET',
		success: (data) => {

			$('#room-id-select').html('');
			$('#room-id-filter').html('');
			$('#room-id-filter').append($('<option>', { 'value': '', 'text': 'All' }));
			data.forEach(d => {
				$('#room-id-select').append($('<option>', { 'value': d.id, 'text': d.name }));
				$('#room-id-filter').append($('<option>', { 'value': d.name, 'text': d.name }));
			});
		},
		error: err => {
			console.log(err);
		}
	});
};

var getWeekDaysJSON = () => {
	let url = "<?=ADMIN_URL.'user/getWeekDaysJSON'?>";
	$.ajax({
		url: url,
		dataType: "json",
		method: 'GET',
		success: (data) => {
			$('#day-of-week-select').html('');
			data.forEach(d => {
				$('#day-of-week-select').append($('<option>', { 'value': d.id, 'text': d.day }));
			});
		},
		error: err => {
			console.log(err);
		}
	});
};


var upsertRoomPricesByDayOfWeek = (data) => {
	let url = "<?=ADMIN_URL.'user/upsert_room_prices_by_day_of_week'?>";
	showHideLoader('show');
	$.ajax({
		url: url,
		method: 'POST',
		data: JSON.stringify(data),
		contentType: 'application/json',
		dataType: 'json',
		success: () => {
			$('#open-create-new-room-price-modal').modal('hide');
			showHideLoader('hide');
			bootbox.alert('Successfull!')
			datatable.ajax.reload();
		},
		error: () => {
			$('#open-create-new-room-price-modal').modal('hide');
			showHideLoader('hide');
			bootbox.alert('Failed! Please try again..');
		}
	})
};



$(document).ready(function() {
	getAllRoomTypes();
	getWeekDaysJSON();

	$('body').on('click', '.delete-price', function() {
		deleteOne($(this).attr('data-id'))
	});

	$('body').on('click', '.edit-price', function() {
		var room_id = $(this).attr('data-room-type');
		var day_of_week = $(this).attr('data-week-day');
		var rate = $(this).attr('data-rate');
		$('#room-id-select').val(room_id);
		$('#day-of-week-select').val(day_of_week);
		$('#rate').val(rate);
		$('#open-create-new-room-price-modal').modal('show');
	});

	$('#room-id-filter').change(function() {
		datatable.search( $(this).val() ).draw();
	});
	$('#submit-room-prices').click(() => {
		var data = $('#create-new-room-price-form').serializeArray()
		.reduce((p, c) => { p[c.name] = c.value; return p; }, {});
		data['rate'] = parseFloat(data['rate']);
		console.log(data);
		upsertRoomPricesByDayOfWeek(data);
	});
    datatable = $('#room-prices-by-day-of-week-table').DataTable({
    	"aaSorting": [],
     	dom: 'Bfrtip',
    	buttons: [
    		{
			    text: "<i class='fa fa-refresh'></i>",
			    action: function (e, dt, node, config) {
			        dt.ajax.reload(null, false);
			    }
			}
        ],  
    	ajax:{
    		url:"<?=ADMIN_URL.'user/roomPricesByDayOfWeekAPI'?>",
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
            // {"data": 10},
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