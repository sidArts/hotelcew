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
<?php

		$content=@$content[0];

?>
<div class="panel">

	<div class="panel-heading">

		General Settings 
		<p style="color: #00a65a;font-size:12px;"><?php echo $this->session->flashdata("success"); ?></p>
		
		<span class="page-buttons">
		
			<a href="<?=ADMIN_URL.'products/product-list'?>" class="header-button"><i class="fa fa-angle-double-left"></i> Back</a>

		</span>

	</div>



	<div class="panel with-nav-tabs panel-default">
					<div class="panel-heading">
							<ul class="nav nav-tabs" style="background-color: blanchedalmond;font-size:10px;">
								<li class="active"><a href="#tab1default" data-toggle="tab">Content Settings</a></li>
								<li><a href="#tab2default" data-toggle="tab">Map Settings</a></li>
								<li><a href="#tab3default" data-toggle="tab">Paypal Settings</a></li>
								<li><a href="#tab4default" data-toggle="tab">Social Media Settings</a></li>
								<li><a href="#tab5default" data-toggle="tab">Shipping Method</a></li>
								<li><a href="#tab6default" data-toggle="tab">Chat Script</a></li>
								<li><a href="#tab7default" data-toggle="tab">Advertise Script</a></li>
								<li><a href="javascript:void(0);" data-toggle="modal" data-target="#myModal">Change Password</a></li>
							</ul>
					</div>
					<div class="panel-body">
					<form method="post" action="<?php echo base_url(); ?>admincp/general_settings_post">
						<div class="tab-content">
							<div class="tab-pane fade in active" id="tab1default">
							<div class="panel-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label>Address</label>
												<input type="text" placeholder="Enter Address" value="<?=@$content['address']?>" name="address" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Email</label>
												<input type="text" placeholder="Enter Admin Email" value="<?=@$content['email']?>" name="email" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Contact</label>
												<input type="text" placeholder="Enter Contact" value="<?=@$content['contact']?>" name="contact" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Secondary Contact</label>
												<input type="text" placeholder="Enter Secondary Contact" value="<?=@$content['s_contact']?>" name="s_contact" id="" class="form-control">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tab2default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Google Map</label>
												<input type="text" placeholder="Enter Google Map src" value="<?=@$content['google_map']?>" name="google_map" id="" class="form-control">
											</div>
											<div class="form-group">
												<label>Google Map Api Key</label>
												<input type="text" placeholder="Enter Google Map Api Key" value="<?=@$content['gmap_api_key']?>" name="gmap_api_key" id="" class="form-control">
											</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tab3default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Paypal API Marchent Key</label>
												<input type="text" placeholder="Paypal API Marchent Key" value="<?=@$content['paypal_key']?>" name="paypal_key" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Paypal Secrect</label>
												<input type="text" placeholder="Paypal Secrect" value="<?=@$content['paypal_secret']?>" name="paypal_secret" id="" class="form-control">
											</div>
									</div>
								</div>
							</div>
							<div class="tab-pane fade" id="tab4default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Facebook</label>
												<input type="text" placeholder="Enter facebook" value="<?=@$content['facebook']?>" name="facebook" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Twitter</label>
												<input type="text" placeholder="Enter Twitter" value="<?=@$content['twitter']?>" name="twitter" id="" class="form-control">
											</div>

											<div class="form-group">
												<label>Linkedin</label>
												<input type="text" placeholder="Enter Twitter" value="<?=@$content['linkedin']?>" name="linkedin" id="" class="form-control">
											</div>
									</div>
								</div>
							</div>

							
							<div class="tab-pane fade" id="tab5default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Shipping Charges (Amount will be in dollar EX- 0 / 9 / 10 )</label>
												<input type="text" placeholder="Enter Shipping Rate" value="<?=@$content['shipping_fee']?>" name="shipping_fee" id="" class="form-control">
											</div>
									</div>
								</div>
							</div>

							<div class="tab-pane fade" id="tab6default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Chat Script</label>
												<textarea rows="10" class="form-control" name="chat_script"><?=@$content['chat_script']?></textarea>
											</div>

											<div class="form-group">
												<label>do you want to appear chat box on the site</label><br>
												<input type="radio" name="showChat" value="Y" <?php (@$content['showChat']=="Y") ? "checked" : "" ?>/> YES
												<input type="radio" name="showChat" value="N" <?php (@$content['showChat']=="N") ? "checked" : "" ?>/> NO
											</div>
									</div>
								</div>
							</div>
							

							<div class="tab-pane fade" id="tab7default">
								<div class="panel-body">
									<div class="row">
											<div class="form-group">
												<label>Advertise Script</label>
												<textarea rows="10" class="form-control" name="advertise_script"><?=@$content['advertise_script']?></textarea>
											</div>

											<div class="form-group">
												<label>do you want to appear advertise box on the site</label><br>
												<input type="radio" name="showAdvertise" value="Y" <?php (@$content['showAdvertise']=="Y") ? "checked" : "" ?>/> YES
												<input type="radio" name="showAdvertise" value="N" <?php (@$content['showAdvertise']=="N") ? "checked" : "" ?>/> NO
											</div>
									</div>
								</div>
							</div>
							


							
						</div>
						<div class="form-group">
												<input type="submit" value="Submit" class="btn btn-info">
											</div>
						</form>
					</div>
				</div>


				<div class="container">
					<!-- Modal -->
						<form method="post" action="<?php echo base_url(); ?>admincp/change_password">
							<div class="modal fade" id="myModal" role="dialog">
								<div class="modal-dialog" style="border-radius: 0px !important;">
									<!-- Modal content-->
									<div class="modal-content">
										<div class="modal-header" style="background-color: #39435c;color:aliceblue;">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title">Change Password</h4>
										</div>
										<div class="modal-body">
											<div class="form-group">
												<label>New Password</label>
												<input type="text" id="npass" name="npass" class="form-control"/>
											</div>
											<div class="form-group">
												<label>Confirm Password</label>
												<input type="text" id="cpass" name="cpass" class="form-control"/>
											</div>
										</div>
										<div class="modal-footer" style="background-color: #39435c;">
											<input type="submit" value="Change Password" class="btn btn-info"/>
											<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>