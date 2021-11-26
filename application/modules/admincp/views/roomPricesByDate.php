<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php //print_r($stores); exit;?>
<div class="panel">
	<div class="panel-heading">
		Room Prices by Date
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
						<label>Date:<span>*</span></label>
						<input type="text" class="form-control" id="date" required="" name="date">
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
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
<script>
var datatable;
function deleteOne(id) {
 	bootbox.confirm('Do you really want to delete this entry?', result => {
		if(!result)
			return;
		$.ajax({
	        url:"<?=ADMIN_URL.'user/deleteRoomPricesByDateAPI/'?>" + id,
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

var upsertRoomPricesByDate = (data) => {
	let url = "<?=ADMIN_URL.'user/upsert_room_prices_by_date'?>";
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
				$('#room-id-filter').append($('<option>', { 'value': d.name, 'text': d.name }));
				$('#room-id-select').append($('<option>', { 'value': d.id, 'text': d.name }));
			});
		},
		error: err => {
			console.log(err);
		}
	})
};

$(document).ready(function() {
	getAllRoomTypes();
	$('body').on('click', '.delete-price', function() {
		deleteOne($(this).attr('data-id'));
	});

	$('body').on('click', '.edit-price', function() {
		var room_id = $(this).attr('data-room-type');
		var day_of_week = $(this).attr('data-date');
		var rate = $(this).attr('data-rate');
		$('#room-id-select').val(room_id);
		$('#date').val(day_of_week);
		$('#rate').val(rate);
		$('#open-create-new-room-price-modal').modal('show');
	});
	$('#room-id-filter').change(function() {
		datatable.search( $(this).val() ).draw();
	});
	$("input#date").datepicker({
		dateFormat: 'yy-mm-dd',
		minDate : 0,
		defaultDate: new Date(),
		onSelect: function(dateText) {}
	});
	$("#date").datepicker('setDate', new Date());
	$('#submit-room-prices').click(() => {
		var data = $('#create-new-room-price-form').serializeArray()
		.reduce((p, c) => { p[c.name] = c.value; return p; }, {});
		data['rate'] = parseFloat(data['rate']);
		console.log(data);
		upsertRoomPricesByDate(data);
	});

	$('#open-create-new-room-price')

    datatable = $('#store_table').DataTable({
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