<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<style>

	.iCheck-checkbox .checked, .iCheck-checkbox .checked:hover {

	   background-color: #00a65a;

	}

	#preview

	{

		padding:5px 5px 5px 5px;

		

	}

</style>

<div class="panel">

	<div class="panel-heading">

		Coupon Edit

		
		<span class="page-buttons">

			<a href="<?=ADMIN_URL.'products/product-list'?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>

		</span>

	</div>

	<div class="panel-body">
		<form action="<?php $content=$content[0]; echo base_url(); ?>/admincp/coupon_post" method="post">
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label>Coupon Title</label>
						<input type="hidden" name="id" value="<?php echo @$content['id']; ?>" class="form-control" />
						<input type="text" value="<?=@$content['title']?>" name="title" id="" class="form-control">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label>Coupon Code</label>
						<input type="text" value="<?=@$content['coupon_code']?>" name="coupon_code" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Coupon Type</label>
						<select class="form-control" name="type">
							<option value="">-Enter Coupon Type-</option>
							<option <?php (@$content['type']=="P") ? "selected" :  "" ?> value="P">Percentage</option>
							<option <?php (@$content['type']=="F") ? "selected" :  "" ?> value="F">Fixed</option>
						</select>
					</div>
				</div>

				
				<div class="col-md-4">
					<div class="form-group">
						<label>Coupon Value</label>
						<input type="text" value="<?=@$content['coupon_value']?>" name="coupon_value" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Minimum Cart Amount</label>
						<input type="text" value="<?=@$content['min_cart_amount']?>" name="min_cart_amount" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Maximum Discount on this coupon</label>
						<input type="text" value="<?=@$content['max_discount_amount']?>" name="max_discount_amount" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Promotion Start Date</label>
						<input type="text" value="<?=@$content['start_date']?>" name="start_date" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Promotion End Date</label>
						<input type="text" value="<?=@$content['end_date']?>" name="end_date" id="" class="form-control">
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group">
						<label>Coupon Description</label>
						<textarea class="form-control" name="description"><?=@$content['description']?></textarea>
					</div>
				</div>


				<div class="col-md-4">
					<div class="form-group">
						<label>One Time Use ( Is this coupon for one time use)</label><br>
						<input type="radio" value="1" name="one_time_use" <?php if(@$content['one_time_use']==1){ echo "checked"; } ?> /> YES
						<input type="radio" value="0" name="one_time_use" <?php if(@$content['one_time_use']==0){ echo "checked"; } ?> /> NO
					</div>
				</div>
				

				<div class="col-md-12">
					<div class="form-group">
						<input type="submit" class="btn btn-info" />
					</div>
				</div>
				
			</form>

					
	</div>
<script>



   

</script>