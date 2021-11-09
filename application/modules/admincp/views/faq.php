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

		FAQ Management

		
		<span class="page-buttons">

			<a href="javascript:void(0);"  data-toggle="modal" data-target="#myModal" class="header-button"><i class="fa fa-plus"></i> Add FAQ</a>
			<a href="<?=ADMIN_URL.'products/product-list'?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>

		</span>

	</div>

	<div class="panel-body">
		<p style="color:#00a65a"><?php echo $this->session->flashdata("success"); ?></p>
	<table class="table table-bordered" id="example">
			<thead>
			<tr style="background-color: #ccc;">
				<td>Question</td>
				<td>Answer</td>
				<td>Active</td>
			</tr>
			</thead>
			<tbody>
			<?php
				if(count($faq)>0)
				{
					foreach($faq as $faqSingle)
					{
						?>
							<tr>
								<td><?php echo $faqSingle['question']; ?></td>
								<td><?php echo $faqSingle['ans']; ?></td>
								
								<td>
									<?php
										if($faqSingle['status']=="A")
										{
											?>
												<a href="javascript:void(0);" onclick="changeStatus('rr_faq','I','<?php echo $faqSingle['id']; ?>')" class="badge bg-red">Inactive</a>
											<?php
										}
										else
										{
											?>
												<a  href="javascript:void(0);" onclick="changeStatus('rr_faq','A','<?php echo $faqSingle['id']; ?>')" class="badge bg-green">Active</a>
											<?php
										}
									?>
									<a href="javascript:void(0);"  onclick="changeStatus('rr_faq','D','<?php echo $faqSingle['id']; ?>')" class="badge bg-red">Delete</a>
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

				<div class="container">
					<!-- Modal -->
						<form method="post" action="<?php echo base_url(); ?>admincp/faq_post">
							<div class="modal fade" id="myModal" role="dialog">
								<div class="modal-dialog" style="border-radius: 0px !important;">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header" style="background-color: #39435c;color:aliceblue;">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Add FAQ</h4>
										</div>
										<div class="modal-body">
											<div class="form-group">
												<label>Faq Question</label>
												<input type="text" id="npass" name="question" required class="form-control"/>
											</div>
											<div class="form-group">
												<label>Answer</label>
												<textarea rows="10" class="form-control" required name="ans"></textarea>
											</div>
										</div>
										<div class="modal-footer" style="background-color: #39435c;">
											<input type="submit" value="Add FAQ" class="btn btn-info"/>
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
