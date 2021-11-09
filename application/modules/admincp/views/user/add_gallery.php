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
		Add Gallery
		<span class="page-buttons">
			<a href="<?= ADMIN_URL . 'gallery/all' ?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>
			<!-- <a href="<?= ADMIN_URL . 'create/user' ?>" class="header-button"><i class="fa fa-plus-circle"></i> New</a> -->
		</span>
	</div>
	<div class="panel-body">
		<form method="POST" action="<?= ADMIN_URL . 'user/submitgallery/' ?>" id="store" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-9">
					<div class="form-group">
						<label>Image<small style="color:red">*</small></label>
						<input type="file" name="files[]" id="name" required multiple="">
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
	$(document).ready(function() {
		//let qrcode = $("#store_id").val();
		$("#store").validate({

			rules: {
				
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
							$(location).attr('href', "<?= ADMIN_URL . 'gallery/all' ?>")
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
