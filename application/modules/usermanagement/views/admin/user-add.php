<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!-- 

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



<div class="panel">

	<div class="panel-heading">

		<?=(@$user_details) ? 'Update' : 'Add New'?> User Information

		<span class="page-buttons">

			<a href="<?=ADMIN_URL.'user/all'?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>

			

		</span>

	</div>

	<div class="panel-body">

		<form method="POST" action="<?= ADMIN_URL . 'usermanagement/submit' ?>"  id="user_form" enctype="multipart/form-data">

			<input type="hidden" id="user_id" name="user_id" value="<?=@$user_details['id'] ?>"/>

			<div class="row">

				<div class="col-md-9">

					<div class="form-group">

                		<label>Name<small style="color:red">*</small></label>

						<input type="text" placeholder="Name" value="<?=@$user_details['name'] ?>" name="name" id="name" class="form-control" required>

						<small id="namemsg" style="color:red"></small>

					</div>

					





                	<div class="row">

                		<div class="col-md-6">	                			

		                	<div class="form-group">

		                		<label>Email<small style="color:red">*</small></label>

								<input type="email" placeholder="email" value="<?=@$user_details['email'] ?>" name="email" id="email" class="form-control" onblur='checkemail(this.value)' required>

								<small id="emailmsg" style="color:red"></small>

		                	</div>

                		</div>

                		<?php

                		if(!isset($user_details) && empty($user_details))

                		{

                			?>

                			<div class="col-md-6">	                			

		                	<div class="form-group">

		                		<label>Password<small style="color:red">*</small></label>

								<input type="text" placeholder="Password" value="" name="password" id="password" class="form-control" required>

								<small id="passwordmsg" style="color:red"></small>

		                	</div>

                		</div>

                			<?php

                		}



                		?>

                		

                		<div class="col-md-6">	                			

			                	<div class="form-group">

			                		<label>Status<small style="color:red">*</small></label>

			                	  <select class="form-control" name="status" id="status" required>

									  <option value="">Select</option>

			                	  	<option <?php echo (@$user_details['status']=="A")?"selected":""; ?> value="A">Active</option>

			                	  	<option <?php echo (@$user_details['status']=="I")?"selected":""; ?> value="I">Inactive</option>

			                	  </select>

			                	</div>

	                		</div>



                		<div class="col-md-6">

                			<div class="form-group">

		                		<label>Phone<small style="color:red">*</small></label>

								<input type="text" placeholder="Phone" value="<?=@$user_details['phone'] ?>" name="phone" id="phone" class="form-control iconpicker" required/>

								<small id="phonemsg" style="color:red"></small>

		                	</div>

                		</div>

					</div>

					<div class="row">

                		



                		<div class="col-md-6">

                			<div class="form-group">

		                		<label>User Image <small style="color:red">*</small></label>

		                		<div class="form-control switch-parent">

		                			<!-- <span class="switch-label">Store Image</span> -->

		                			

		                			<div class="switch-block">

			                			

										  	 <input type="file" name="image" id="image">

										  

										

									</div>



								</div>

								

							</div>

							

						</div>

				

						<div class="col-md-6">

							<div class="form-group">

								<label>&nbsp;&nbsp;</label>



								<?php if (empty($user_details['image'])) { ?>

									<img src="https://ctt.trains.com/sitefiles/images/no-preview-available.png" class="previewHolder" style="height:100%,width:350%" />

								<?php } else { ?>



									<img src="<?php echo base_url(); ?>/public/uploads/users/<?php echo $user_details['image']; ?>" class="previewHolder" style="height:100%,width:350%" />

								<?php } ?>



							</div>

						</div>

                	</div>



                	

				</div>



				<div class="col-md-3">

					<div class="form-group">

						<label>&nbsp;</label>

						<button type="submit" class="btn btn-primary btn-block submit-btn"><i class="fa fa-save"></i>&ensp;Save <i class="fa fa-refresh fa-spin spin-loader" style="display: none;"></i></button>

					</div>



					<!-- <div class="form-group">

						<div class="iCheck-checkbox">

							<input class="checkbox" type="checkbox" name="status" value="A" <?=(@$content['status'] == 'A') ? 'checked' : ''?>>

							<label>Active</label>

						</div>

					</div> -->

				</div>

			</div>



		</form>

	</div>



</div>

<script>

$(document).ready(function(){

        $("#user_form").validate({

            rules : {

                email : {

                    required : true,

                    email : true

				}, 

				

                password : {

                    required : true,

                    minlength : 6

                }

			}, 

			phone:{

                    required : true,

					phone : true,

					minlength : 10

				},

			

            messages : {

				email : "Enter a valid email address", 

				phone:"phone no field is required",

                password : {

                    required : "Password should not be blank", 

                    minlength : "Password should be minimum 6 character"

                }

            },

            submitHandler: function(form) {

				$(".submit-btn").attr("disabled", true);

                $(".spin-loader").show();

				$.ajax({

					url: form.action,

					type: form.method,

				    data:new FormData(form),

				    contentType: false,

					cache: false,

					processData: false,

                  success: function(response) {

					  let obj  = JSON.parse(response);

					  

					  if(obj.stat =='success')

					  {

						toastr.success(obj.msg);

					    $(location).attr('href', "<?= ADMIN_URL . 'user/all' ?>")

					  }

					  else if(obj.stat =='error'){

						toastr.error(obj.msg);

					  }

                }            

            });

				

		

            }

        });

    })



</script>

<script>

	
function checkemail(email) {

		//alert(email);

		$.ajax({

				url: "<?= ADMIN_URL . 'usermanagement/checkexsitingmail' ?>",

				type: 'POST',

				data: {

					email: email

				},

				datatype: 'json'

			})

			.done(function(data) {

				if (data == 1) {

					$("#email").addClass("error");

					$("#email").focus();

					toastr.error("email id is already exsists");

				} else {

					

					// toastr.success("email id is not exsists");

					$("#email").removeClass("error");

				}



				console.log(data);

			})

			.fail(function(jqXHR, textStatus, errorThrown) {

				console.log('error');

			});

	}

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





// 	function save() {



// let formdata = document.getElementById("user_form");

// $.ajax({

// 	url: "<?= ADMIN_URL . 'usermanagement/submit' ?>",

// 	type: "POST",

// 	data: new FormData(formdata),

// 	contentType: false,

// 	cache: false,

// 	processData: false,

// 	beforeSend:function(){

// 		$(".submit-btn").prop("disabled",true);

// 		$(".spin-loader").css({"display":"block"});



// 	},

// 	success: function(data) {

// 		console.log(data);

// 		let converjson = JSON.parse(data);

		

// 	      if(converjson.stat==false){

//             $("#namemsg").text(converjson.error.name);

// 			$("#emailmsg").text(converjson.error.email);

// 			$("#passwordmsg").text(converjson.error.password);

// 			$("#phonemsg").text(converjson.error.phone);

// 		  }

// 		  else{

// 			$("#namemsg").hide();

// 			$("#emailmsg").hide();

// 			$("#passwordmsg").hide();

// 			$("#phonemsg").hide();

// 			toastr.success(converjson.msg);

// 			$(location).attr('href', "<?= ADMIN_URL . 'usermanagement/list' ?>")

// 		  }





// 	},

// 	complete:function(){

// 		$(".submit-btn").prop("disabled",false);

// 		$(".spin-loader").css({"display":"none"});

// 	},

// 	error: function(e) {

// 		//error

// 	}

// });



// }





</script>