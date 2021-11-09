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
		Ride global rate (per minute)

	</div>
	<div class="panel-body ">
		<div class="row">
			<div class="col-md-6">
				<form action="" method="post">
					<?php
					if(isset($global_rate[0]['rate']) && (!empty($global_rate[0]['rate'])))
					{
						?>
						<input type="hidden" name="_data_id" class="form-control" value="<?= $global_rate[0]['id']?>">
						<?php
					}
					?>
					<input type="text" name="global_rate" class="form-control" value="<?=isset($global_rate[0]['rate']) ? $global_rate[0]['rate'] : '' ?>"><br>
					<button class="btn">Submit</button>
				</form>
			</div>
		</div>
	</div>

</div>
