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
		<?= $details['name']?>
	</div>
	<div class="panel-body">
		<form method="POST" action="<?= ADMIN_URL . 'user/insert_page/' ?>" id="store" enctype="multipart/form-data">
			<input type="hidden" name="slug" value="<?= $details['slug']?>">
			<div class="row">
				<div class="col-md-8">
					<div class="form-group">
						<label><?= $details['name']?><small style="color:red">*</small></label>
						<textarea class="" name="editor1"><?= $details['content']?></textarea>
					</div>
				</div>
				<!-- <div class="col-md-8">
					<div class="form-group">
						<label>Admin email<small style="color:red">*</small></label>
						<input type="text" name="admin_email" class="form-control" id="site_title" required  value="<?= $site_settings[0]['admin_email']?>">
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<label>Address<small style="color:red">*</small></label>
						<input type="text" name="address" class="form-control" id="site_title" required value="<?= $site_settings[0]['address']?>">
					</div>
				</div> -->
					
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
				// site_title: {
				// 	required: true,
				// },
				// admin_email: {
				// 	required: true,
				// },
				// address: {
				// 	required: true,
				// },
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
				CKupdate();
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
							location.reload();
						} else if (obj.stat == 'error') {
							toastr.error(obj.msg);
						}
					}
				});


			}
		});
	})
</script>
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
 <script>
        CKEDITOR.replace( 'editor1' );
</script>
<script type="text/javascript">
	function CKupdate(){
    for ( instance in CKEDITOR.instances )
        CKEDITOR.instances[instance].updateElement();
}

</script>