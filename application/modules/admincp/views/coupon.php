<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" />

<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
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

		Coupon Management

		
		<span class="page-buttons">

				<a href="<?php echo base_url(); ?>admincp/coupon_edit/all" class="header-button"><i class="fa fa-plus-circle"></i> Add New</a>

		</span>

	</div>
	<div class="panel-body">
		<table class="table table-bordered" id="example">
			<thead>
			<tr style="background-color: #ccc;">
				<td>Title</td>
				<td>Coupon Code</td>
				<td>Description</td>
				<td>Coupon Type</td>
				<td>Coupon Value</td>
				<td>Min Cart Amount</td>
				<td>Max Amount to be discounted</td>
				<td>Start Date</td>
				<td>End Date</td>
				<td>Active</td>
			</tr>
			</thead>
			<tbody>
			<?php
				if(count($coupons)>0)
				{
					foreach($coupons as $couponsSingle)
					{
						?>
							<tr>
								<td><?php echo $couponsSingle['title']; ?></td>
								<td><span class="badge bg-green" style="border-radius:0%;font-size:20px;"><?php echo $couponsSingle['coupon_code']; ?></span></td>
								<td><?php echo $couponsSingle['description']; ?></td>
								<td><?php echo ($couponsSingle['type']=="P") ? "PERCENTAGE" : "FIXED"; ?></td>
								<td><?php echo ($couponsSingle['type']=="P") ? $couponsSingle['coupon_value']."%" : $couponsSingle['coupon_value'].CURRENCY; ?></td>
								<td><?php echo $couponsSingle['min_cart_amount'].CURRENCY; ?></td>
								<td><?php echo $couponsSingle['max_discount_amount'].CURRENCY; ?></td>
								<td><?php echo $couponsSingle['start_date']; ?></td>
								<td><?php echo $couponsSingle['end_date']; ?></td>
								<td>
									<?php
										if($couponsSingle['status']=="A")
										{
											?>
												<a href="javascript:void(0);" onclick="changeStatus('rr_coupons','I','<?php echo $couponsSingle['id']; ?>')" class="badge bg-red">Inactive</a>
											<?php
										}
										else
										{
											?>
												<a  href="javascript:void(0);" onclick="changeStatus('rr_coupons','A','<?php echo $couponsSingle['id']; ?>')" class="badge bg-green">Active</a>
											<?php
										}
									?>
									<a class="badge bg-blue" href="<?php echo base_url(); ?>admincp/coupon_edit/<?php echo $couponsSingle['id']; ?>" target="_blank">Edit</a>
								</td>
							</tr>
						<?php
					}
					
				}
				else
				{
					?>
						<tr align="center">
							<td colspan="5">No Recoed Found</td>
						</tr>
					<?php
				}
			?>
			</tbody>
		</table>		
	</div>

	<script>
		$(document).ready(function() {
			$('#example').DataTable( {
				dom: 'Bfrtip',
				buttons: [
					'csv', 'excel', 'pdf', 'print'
				]
			} );
		} );


		function changeStatus(TABLE,STATUS,ID)
		{
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					location.reload();
				}
			};
			xhttp.open("GET", "<?php echo base_url(); ?>admincp/statusChange?table="+TABLE+"&status="+STATUS+"&id="+ID, true);
			xhttp.send();
		}

	</script>
				