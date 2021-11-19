<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- 

<style>

	.iCheck-checkbox .checked, .iCheck-checkbox .checked:hover {

	   background-color: #00a65a;

	}

</style> -->

<style>

	.error {

		border: solid 1px red;

	}

</style>

<?php  //echo"<pre>";print_r($time_slots[0]['id']); exit; ?>

<div class="panel">

	<div class="panel-heading">

		<?= (@$ride_details) ? 'Update' : 'Add New' ?> Ride Information

		<span class="page-buttons">

			<a href="<?= ADMIN_URL . 'bookings/all' ?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>



		</span>

	</div>

	<div class="panel-body ">

		<form method="POST" action="<?= ADMIN_URL . 'user/ride/save' ?>" id="myform" enctype="multipart/form-data">

			<input type="hidden" name="id" value="<?= (!empty($ride_details['id'])) ? $ride_details['id'] : ''; ?>" />

			<div class="row">

				<div class="col-md-9">

					<div class="form-group">

						<label>Store Name:<small style="color:red">*</small></label>

						<select name="store_name" class="form-control" required>

							<option value="">select</option>

							<?php foreach ($stores as $store) { ?>

								<option <?php echo @($ride_details['store_id'] == $store['id']) ? "selected" : ''; ?> value="<?= $store['id']; ?>"><?= $store['store_name']; ?></option>

							<?php } ?>

						</select>

						<small id="store_name" style="color:red"></small>







					</div>





					<div class="row">

						<div class="col-md-12">

							<div class="form-group">

								<label>Description</label>

								<textarea class="form-control" name="description" required><?= (!empty($ride_details['ride_desc'])) ? $ride_details['ride_desc'] : '' ?></textarea>



							</div>

						</div>

						<div class="col-md-6">

							<div class="form-group">

								<label>Ride Name<small style="color:red">*</small></label>

								<input type="text" placeholder="Ride Name" name="ridename" id="ridename" class="form-control" value="<?= (!empty($ride_details['ride_name'])) ? $ride_details['ride_name'] : '' ?>" required />

								<small id="ride_name" style="color:red"></small>

							</div>

						</div>



						<div class="col-md-6">

							<div class="form-group">

								<label>Ride Image</label>

								<div class="form-control switch-parent">

									<!-- <span class="switch-label">Store Image</span> -->



									<div class="switch-block">



										<input type="file" name="image" id="image" required>





									</div>



								</div>

							</div>

						</div>

					</div>

					<div class="row">

						<div class="col-md-6">

							<!-- <div class="form-group">

								<label>Ride Minimum Time:<small style="color:red">*</small></label>

								<input type="number" placeholder="Ride Minimum Time" value="<?= (!empty($ride_details['ride_min_time'])) ? $ride_details['ride_min_time'] : '' ?>" name="time" id="time" class="form-control iconpicker" required>

								<small id="ridemin" style="color:red"></small>

							</div> -->

						</div>





						<div class="col-md-6">

							<div class="form-group">

								<label>&nbsp;&nbsp;</label>



								<?php if (@empty($ride_details['ride_img'])) { ?>

									<img src="https://ctt.trains.com/sitefiles/images/no-preview-available.png" class="previewHolder" style="height:100%,width:350%" />

								<?php } else { ?>



									<img src="<?php echo base_url(); ?>/public/uploads/ride/<?php echo $ride_details['ride_img']; ?>" class="previewHolder" style="height:100%,width:350%" />

								<?php } ?>



							</div>

						</div>







					</div>



					<!-- <div class="row"> -->



					<!-- <div class="col-md-6">

							<div class="form-group">

								<label>Ride base Price<small style="color:red">*</small></label>

								<input type="number" placeholder="Extra Cost Permin" value="<?= (!empty($ride_details['ride_base_price'])) ? $ride_details['ride_base_price'] : '' ?>" name="ride_base" id="ride_base" class="form-control iconpicker" required>

							</div>

							<small id="rbase" style="color:red"></small>

						</div> -->







					<!-- </div> -->

					<div class="row ">

						<div class="col-md-6">

							<div class="form-group">

								<label>Status</label>

								<select class="form-control" name="status" id="status" required>

									<option value="">Select</option>

									<option <?php echo (@$ride_details['status'] == 'A') ? "selected" : ''; ?> value="A">Active</option>

									<option <?php echo (@$ride_details['status'] == 'I') ? "selected" : ''; ?> value="I">Inactive</option>

								</select>

							</div>

						</div>

						<div class="col-md-6">

							<div class="form-group">

								<label>Extra Cost Permin</label>

								<input type="number" placeholder="Extra Cost Permin" value="<?= (!empty($ride_details['extra_per_min_cost'])) ? $ride_details['extra_per_min_cost'] : '' ?>" name="extra_const" id="extra_const" class="form-control iconpicker" required>

							</div>

						</div>

					</div>



					<div class="row ">

						<div class="col-md-12">

							<div class="form-group">

								<label><u>Add More</u></label>

							

							</div>

						</div>

						

					</div>

                <?php if(empty($ride_details)){ ?>

					<!--- TIME SLOT  -->

					<div class="add">

					<div class="row">

						<div class="col-md-3">

							<div class="form-group">

								<label>Ride Base Time<small style="color:red">*</small></label>

								<input type="number" placeholder="Ride Base" name="ridebase[]" id="ridebase" class="form-control" value=""  required />

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Ride Base Charges <small style="color:red">*</small></label>

								<input type="number" placeholder="Ride Base Charges" value="" name="ride_base_charges[]" id="ride_base_charges" class="form-control iconpicker" required>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Status<small style="color:red">*</small></label>

								<select class="form-control" name='timestatus[]' id="timestatus" required>

									<option value=''>Select</option>

									<option value='A'>Active</option>

									<option value='I'>Inactive</option>

								</select>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Action</label>

								<button type="button" class="btn btn-success btn-block addmore"><i class="fa fa-plus"></i></button>

							</div>

						</div>

					</div>

					</div>

				<?php  } else {?>  

					               

					<div class="add">

					<input type="hidden" id ="multile" name="multiple" />

					<div class="row">

						<div class="col-md-3">

							<div class="form-group">

								<label>Ride Base Time<small style="color:red">*</small></label>

								<input type="number" placeholder="Ride Base" name="ridebase[]" id="ridebase" class="form-control" value="<?=(!empty($time_slots))?$time_slots[0]['ride_base_time']:''?>" onblur="updateslot('ride_base_time',this.value,'<?php echo $time_slots[0]['id'] ?>')" required/>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Ride Base Charges<small style="color:red">*</small></label>

								<input type="number" placeholder="Ride Base Charges" value="<?=(!empty($time_slots))?$time_slots[0]['ride_base_charge']:''?>" name="ride_base_charges[]" id="ride_base_charges" class="form-control iconpicker" onblur="updateslot('ride_base_charge',this.value,'<?php echo $time_slots[0]['id'] ?>')" required>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Status<small style="color:red">*</small></label>

								<select class="form-control" name='timestatus[]' onchange="updateslot('timestatus',this.value,'<?php echo $time_slots[0]['id'] ?>')"   id="timestatus" required>

									<option value=''>Select</option>

									<option value='A' <?=($time_slots[0]['status'] =='A')?'selected':'';?>>Active</option>

									<option value='I' <?=($time_slots[0]['status'] =='I')?'selected':'';?>>Inactive</option>

								</select>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<label>Action</label>

								<button type="button" class="btn btn-success btn-block addmore"><i class="fa fa-plus"></i></button>

							</div>

						</div>

					</div>

					



					<?php if(!empty($time_slots)){ ?>  

						<?php foreach($time_slots as $key=>$time){ ?>							<?php if($key > 0){?>

								<?php if($time['status']!="D"){ ?>



					<div class="row more_<?php echo $time['id']; ?>">

						<div class="col-md-3">

							<div class="form-group">

							

								<input type="number" placeholder="Ride Base" name="ridebase[]" id="ridebase" class="form-control" value="<?= $time['ride_base_time']; ?>"  onblur="updateslot('ride_base_time',this.value,'<?php echo $time['id']; ?>')" required/>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								

								<input type="number" placeholder="Ride Base Charges" name="ride_base_charges[]" id="ride_base_charges" class="form-control iconpicker" value='<?= $time['ride_base_charge']; ?>' onblur="updateslot('ride_base_charge',this.value,'<?php echo $time['id'] ?>')" required>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

							

								<select class="form-control" name='timestatus[]' id="timestatus" onchange="updateslot('timestatus',this.value,'<?php echo $time['id'] ?>')" required>

									<option value=''>Select</option>

									<option <?= $time['status'] == 'A'?'selected':''; ?> value='A'>Active</option>

									<option <?= $time['status'] == 'I'?'selected':''; ?> value='I'>Inactive</option>

								</select>

							</div>

						</div>

						<div class="col-md-3">

							<div class="form-group">

								<button type="button" class="btn btn-danger btn-block" onclick="deleteslot('<?php echo $time['id'];  ?>')"><i class="fa fa-minus"></i></button>

							</div>

						</div>

					</div>

							<?php } }?>

						<?php  } ?>

						

					<?php } ?>



					</div>

				

				<?php } ?>

				</div>





				<div class="col-md-3">

					<div class="form-group">

						<label>&nbsp;</label>

						<button type="submit" class="btn btn-primary btn-block submit-btn"><i class="fa fa-save"></i>&ensp;Save <i class="fa fa-refresh fa-spin spin-loader" style="display: none;"></i></button>

					</div>



					<!-- <div class="form-group">

						<div class="iCheck-checkbox">

							<input class="checkbox" type="checkbox" name="status" value="A" <?= (@$content['status'] == 'A') ? 'checked' : '' ?>>

							<label>Active</label>

						</div>

					</div> -->

				</div>

			</div>



		</form>

	</div>



</div>

<script>

	function updateslot(field_name,value,id){

		

		$.ajax({

            url: "<?= ADMIN_URL . 'users/ride/slot/update' ?>",

            type: 'post',

            data:{id:id,field_name:field_name,value:value},

            datatype: 'json'

        })

        .done(function (data) { 

			

		    let obj = JSON.parse(data);

			toastr.success(obj.msg);

		 // location.reload();

           

         })

        .fail(function (jqXHR, textStatus, errorThrown) { 

         console.log('error');

         });

	  

	}

</script>

<script>

	function deleteslot(id){

		 $.ajax({

            url: "<?= ADMIN_URL . 'users/ride/slot/remove' ?>",

            type: 'post',

            data:{id:id},

            datatype: 'json'

        })

        .done(function (data) { 

			let obj = JSON.parse(data);

			let id = obj.id;

		

			$(".more_"+id).remove();

			toastr.success(obj.msg);

    

           

         })

        .fail(function (jqXHR, textStatus, errorThrown) { 

         console.log('error');

         });

	}

</script>

<script>

	var records ='<?php echo (!empty($time_slots))?count($time_slots):4; ?>';

	var count = 1;

$(".addmore").click(function(){

	$("#multile").val(count);

	if(count <  5){

        $("<div class='row more_"+count+"'><div class='col-md-3'><div class='form-group'><input type='number' placeholder='Ride Name' name='ridebase[]' id='ridebase_"+count+"' class='form-control' value='' required/></div></div><div class='col-md-3'><div class='form-group'><input type='number' placeholder='Ride Base Charges' value='' name='ride_base_charges[]' id='ride_base_charges_"+count+"' class='form-control iconpicker' required></div></div><div class='col-md-3'><div class='form-group'><select class='form-control' name='timestatus[]' id='timestatus_"+count+"' required><option value=''>Select</option><option value='A'>Active</option><option value='I'>Inactive</option></select></div></div><div class='col-md-3'><div class='form-group'><button type='button' class='btn btn-danger btn-block' onclick='remove("+count+")'><i class='fa fa-minus'></i></button></div></div></div>").appendTo(".add");

	}

	else{

		toastr.error("No More time slot will be add");

	}



  count++;

});

function remove(id){



 $(".more_"+id+"").remove();



 

}



	

</script>

<script>

	$(document).ready(function() {

		$("#myform").validate({

			rules: {

				email: {

					required: true,

					email: true

				},



				password: {

					required: true,

					minlength: 6

				}

			},

			phone: {

				required: true,

				phone: true,

				minlength: 10

			},



			messages: {

				email: "Enter a valid email address",

				phone: "phone no field is required",

				password: {

					required: "Password should not be blank",

					minlength: "Password should be minimum 6 character"

				}

			},

			submitHandler: function(form) {

				$(".submit-btn").attr("disabled", true);

				$(".spin-loader").show();

				$.ajax({

					url: form.action,

					type: form.method,

					data: new FormData(form),

					contentType: false,

					cache: false,

					processData: false,

					success: function(response) {

						let obj = JSON.parse(response);

                        console.log(obj);

						if (obj.stat == 'success') {

							toastr.success(obj.msg);

						  $(location).attr('href', "<?= ADMIN_URL . 'bookings/all' ?>")

						} else if (obj.stat == 'error') {

							toastr.error(obj.msg);

						}

					}

				});





			}

		});

	})

</script>

<script type="text/javascript">

	function readURL(input) {

		if (input.files && input.files[0]) {

			var reader = new FileReader();



			reader.onload = function(e) {

				$('.previewHolder').attr('src', e.target.result);

			}

			reader.readAsDataURL(input.files[0]);

		}

	}

	$("#image").change(function() {

		readURL(this);

	});

</script>