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
<div class="panel">
	<div class="panel-heading">
		<?= (@$storeDetails) ? 'Update' : 'Add New' ?> Room Information
		<span class="page-buttons">
			<a href="<?= ADMIN_URL . 'stores/all' ?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>
			<!-- <a href="<?= ADMIN_URL . 'create/user' ?>" class="header-button"><i class="fa fa-plus-circle"></i> New</a> -->
		</span>
	</div>
	<div class="panel-body">
		<form method="POST" action="<?= ADMIN_URL . 'user/submitstoretypeuser/' ?>" id="store" enctype="multipart/form-data">
			<input type="hidden" id="store_id" name="store_id" value="<?php echo (!empty($storeDetails['id'])) ? $storeDetails['id'] : ''; ?>">
			<!-- <input type="hidden" id="qrcode" name="qrcode" value="<?php //echo mt_rand(000, 999) . date("m");  ?>"> -->
			<!-- <input type="hidden" id="qrcode" name="qrcode" value="<?php echo (!empty($storeDetails['qr_code']))?$storeDetails['qr_code']:''; ?>"/> -->

			<div class="row">
				<div class="col-md-9">
					<div class="form-group">
						<label>Name<small style="color:red">*</small></label>
						<input type="text" placeholder="Name" name="name" id="name" class="form-control" value="<?php echo (!empty($storeDetails['name'])) ? $storeDetails['name'] : ''; ?>" required>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Size<small style="color:red">*</small></label>
								<input type="text" placeholder="size" value="<?php echo (!empty($storeDetails['size'])) ? $storeDetails['size'] : ''; ?>" name="size" id="size" class="form-control" required>

							</div>
						</div>

						<div class="col-md-6">
							<?php if(!empty($storeDetails)){?>
							<div class="form-group">
								<label>No of rooms</label>
								<input type="text" placeholder="No of rooms" name="no_of_room" id="no_of_room" class="form-control" value="<?php echo $storeDetails['no_of_room'] ?>"/>
							</div>
							 <?php } else {?>
								<div class="form-group">
								<label>No of rooms<small style="color:red">*</small></label>
								<input type="text" placeholder="No of rooms" value="" name="no_of_room" id="no_of_room" class="form-control" required/>
							</div>
							 <?php } ?>
						</div>

						<div class="col-md-6">
							<?php if(!empty($storeDetails)){?>
							<div class="form-group">
								<label>Person</label>
								<input type="text" placeholder="Person" name="person" id="person" class="form-control" value="<?php echo $storeDetails['person'] ?>"/>
							</div>
							 <?php } else {?>
								<div class="form-group">
								<label>Person<small style="color:red">*</small></label>
								<input type="text" placeholder="Person" value="" name="person" id="person" class="form-control" required/>
							</div>
							 <?php } ?>
						</div>
						

						<div class="col-md-6">
							<div class="form-group">
								<label>Back Rate <small style="color:red">*</small></label>
								<input type="text" placeholder="Back Rate" value="<?php echo (!empty($storeDetails['back_rate'])) ? $storeDetails['back_rate'] : ''; ?>" name="back_rate" id="back_rate" class="form-control"  required />
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>GST<small style="color:red">*</small></label>
								<input type="text" name="gst" id="gst" style="font-family: Arial, font-size: 10pt" class="form-control" value="<?php echo (!empty($storeDetails['gst'])) ? $storeDetails['gst']: ''; ?>" required />
							</div>

						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Net Rate <small style="color:red">*</small></label>
								<input type="text" placeholder="Net rate" value="<?php echo (!empty($storeDetails['net_rate'])) ? $storeDetails['net_rate'] : ''; ?>" name="net_rate" id="net_rate" class="form-control"  required />
							</div>
						</div>
						<div class="col-md-6">
							

							<div class="form-group">
								<label>Status<small style="color:red">*</small></label>
								<select class="form-control" name="status" id="status" required>
									<option value="">Select</option>
									<option <?php echo @($storeDetails['status'] == 1) ? "selected" : ''; ?> value="1">Active</option>
									<option <?php echo @($storeDetails['status'] == 0) ? "selected" : ''; ?> value="0">Inactive</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							

							<div class="form-group">
								<label>Description</label>
								<textarea class="form-control" name="editor1"><?= (isset($storeDetails['description']) && ($storeDetails['description'] != '')) ? $storeDetails['description'] : ''?></textarea>
								
							</div>
						</div>
						<div class="col-md-6">
							
							<div class="form-group">
								<label>Image</label>
								<?php
								if(!empty($storeDetails))
								{
									?>
									<img src="<?= base_url().'public/uploads/room/'.$storeDetails['image'];?>">
									<br>
									<br>			
									<?php
								}

								?>
								
								<input type="file" name="image" id="image">
								
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
							<input class="checkbox" type="checkbox" name="status" value="A" <?= (@$content['status'] == 'A') ? 'checked' : '' ?>>
							<label>Active</label>
						</div>
					</div> -->
				</div>
			</div>

		</form>
	</div>

</div>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
 <script>
        CKEDITOR.replace( 'editor1' );
</script>
<script>
	$(document).ready(function() {
		//let qrcode = $("#store_id").val();
		$("#store").validate({

			rules: {
				name: {
					required: true,
					//email: true
				},

				size: {
					required: true,
				},
				person: {
					required: true,
				},
				back_rate: {
					required: true,
				},
				gst: {
					required: true,
				},
				net_rate: {
					required: true,
				}
			},
			messages: {
				name: "Name field is required",
				size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				
			},
			submitHandler: function(form) {
				CKupdate();
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
						console.log(response);
						let obj = JSON.parse(response);
						if (obj.stat == 'success') {
							toastr.success(obj.msg);
							$(location).attr('href', "<?= ADMIN_URL . 'stores/all' ?>")
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
	function CKupdate(){
    for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
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
</script>
