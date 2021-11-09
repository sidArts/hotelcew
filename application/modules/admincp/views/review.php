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

		Review Management

		
		<span class="page-buttons">

			<a href="<?=ADMIN_URL.'products/product-list'?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>

		</span>

	</div>
	<div class="panel-body">
		<table class="table table-bordered" id="example">
			<thead>
			<tr style="background-color: #ccc;">
				<td>User Name</td>
				<td>User Email</td>
				<td>Review</td>
				<td>Star</td>
				<td>Date</td>
				<td>Product Name</td>
				<td>Active</td>
			</tr>
			</thead>
			<tbody>
			<?php
				if(count($review)>0)
				{
					foreach($review as $reviewSingle)
					{
						?>
							<tr>
								<td><?php echo $reviewSingle['name']; ?></td>
								<td><?php echo $reviewSingle['email']; ?></td>
								<td><?php echo $reviewSingle['review']; ?></td>
								<td><?php echo $reviewSingle['star']; ?></td>
								<td><?php echo $reviewSingle['review_date']; ?></td>
								<td><?php echo $reviewSingle['title']; ?></td>
								<td>
								<?php
										if($reviewSingle['status']=="A")
										{
											?>
												<a href="javascript:void(0);" onclick="changeStatus('rr_product_review','I','<?php echo $reviewSingle['id']; ?>')" class="badge bg-red">Inactive</a>
											<?php
										}
										else
										{
											?>
												<a  href="javascript:void(0);" onclick="changeStatus('rr_product_review','A','<?php echo $reviewSingle['id']; ?>')" class="badge bg-green">Active</a>
											<?php
										}
									?>
									<a></a>
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
				