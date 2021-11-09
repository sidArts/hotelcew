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
		Site Settings
	</div>
	<div class="panel-body">
		<form method="POST" action="<?= ADMIN_URL . 'user/submit_site_settings/' ?>" id="store" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<label>Site Title<small style="color:red">*</small></label>
						<input type="text" name="site_title" class="form-control" id="site_title" value="<?= $site_settings[0]['site_title']?>" required>
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<label>Admin email<small style="color:red">*</small></label>
						<input type="text" name="admin_email" class="form-control" id="site_title" required  value="<?= $site_settings[0]['admin_email']?>">
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<label>Admin Phone<small style="color:red">*</small></label>
						<input type="text" name="admin_phone" class="form-control" id="site_title" required  value="<?= $site_settings[0]['admin_phone']?>">
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<label>Address<small style="color:red">*</small></label>
						<input type="text" name="address" class="form-control" id="site_title" required value="<?= $site_settings[0]['address']?>">
					</div>
				</div>
					
				<div class="col-md-3">
					<div class="form-group">
						<label>&nbsp;</label>
						<button type="submit" class="btn btn-primary btn-block submit-btn"><i class="fa fa-save"></i>&ensp;Save <i class="fa fa-refresh fa-spin spin-loader" style="display: none;"></i></button>
					</div>
				</div>
			</div>

		</form>
	</div>

</div>
<script>
	$(document).ready(function() {
		//let qrcode = $("#store_id").val();
		$("#store").validate({

			rules: {
				site_title: {
					required: true,
				},
				admin_email: {
					required: true,
				},
				address: {
					required: true,
				},
			},
			messages: {
				//name: "Name field is required",
				//size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				// size: "Size field is required",
				
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
						console.log(response);
						let obj = JSON.parse(response);
						if (obj.stat == 'success') {
							toastr.success(obj.msg);
							$(location).attr('href', "<?= ADMIN_URL . 'site-settings' ?>")
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
