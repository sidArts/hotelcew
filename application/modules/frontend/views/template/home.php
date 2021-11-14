<div class="banner_area">
	<div class="owl-carousel owl-theme home-carousel owl-loaded owl-drag">
		<div class="owl-stage-outer">
			<div class="owl-stage" style="transform: translate3d(-2698px, 0px, 0px); transition: all 0.25s ease 0s; width: 8094px;">
				<div class="owl-item cloned" style="width: 1349px;">
				 	<div class="item">
						<div class="banner_img">
							<img src="<?= base_url();?>public/front/assets/images/slider/banner-1200x542.jpg" class="img-fluid" alt="">
						</div>
					</div>
				</div>
				<div class="owl-item cloned" style="width: 1349px;">
					<div class="item">
						<div class="banner_img">
							<img src="<?= base_url();?>public/front/assets/images/slider/banner1-1200x542.jpg" class="img-fluid" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="owl-nav disabled">
			<button type="button" role="presentation" class="owl-prev"><span aria-label="Previous">‹</span></button>
			<button type="button" role="presentation" class="owl-next"><span aria-label="Next">›</span></button>
		</div>
		<div class="owl-dots">
			<button role="button" class="owl-dot active"><span></span></button>
			<button role="button" class="owl-dot"><span></span></button>
		</div>
	</div>
	<div class="banner_text">
		<div class="container">
			<h2>Stay with us in comfort</h2>
			<p>Located in an urban setting close to Acadia<br>
			National Park, Bar Harbor and the beautiful Maine Coast.</p>
			<a href="<?= base_url()?>rates" class="bd_btn">Book Now</a>
		</div>
	</div>
</div>
<div class="ptb mt">
	<div class="container">
		<div class="row">
			
			<div class="col-lg-6 order-lg-1">
				<div class="about_text">
					<div class="heading">
						<h2>About Us</h2>
					</div>
					<div class="about_con">
						<?= $about; ?>
					</div>
					
				</div>
			</div>
			<div class="col-lg-6">
				<div class="about_img">
					<div class="abimg1"><img src="<?= base_url()?>public/front/assets/images/about_img.jpg" alt=""></div>
					<div class="abimg2"><img src="<?= base_url()?>public/front/assets/images/hotel.jpeg" alt=""></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="ptb">
	<div class="container">
		<div class="heading rrates">
			<h2>Room Rates</h2>
			<!-- <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p> -->
		</div>
		<div class="room_carousel">
			<div class="owl-carousel owl-theme room-carousel owl-loaded owl-drag">
				<div class="owl-stage-outer">
					<div class="owl-stage" style="transform: translate3d(0px, 0px, 0px); transition: all 0s ease 0s; width: 1520px;">
						<?php
						foreach ($rooms as $key => $value) {
						?>
						<div class="owl-item active" style="width: 350px; margin-right: 30px;"><div class="item">
							<div class="carousel_room_box">
								<div class="room_img">
									<a href="<?= base_url()?>rooms/<?= $value['slug']?>" id="id-79" title="<?= $value['name']?>">
										<img src="<?= base_url()?>public/uploads/room/<?= $value['image']?>" alt="Room">
									</a>
								</div>
								<div class="room-cont-footer">
									<div class="room_contop">
										<div class="title"><?= $value['name']?></div>
										<div class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?= number_format($value['back_rate'],2) ?></span></div>
									</div>
									<div class="room_conbtm">
										<div class="bd_rating">
											<div class="pro-review">
												
												<ul class="woocommerce">
													<li>
														<!-- <div class="star-rating" title="Rated 3.00 out of 5">
																		<span style="width:60%; color:#d6aa2ade">
																					<strong itemprop="ratingValue" class="rating">3.00</strong>
																				out of 5
																		</span>
														</div> -->
													</li>
												</ul>
											</div>
										</div>
										<div class="description">Per Night</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
					}
					?>
					
				</div>
			</div>
			<div class="owl-nav"><button type="button" role="presentation" class="owl-prev disabled"><span aria-label="Previous">‹</span></button><button type="button" role="presentation" class="owl-next"><span aria-label="Next">›</span></button></div><div class="owl-dots disabled"></div></div>
		</div>
	</div>
</div>
<div class="ptb">
	<div class="container">
		<div class="heading">
			<h2>Your Comfort Services</h2>
			<!-- <p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p> -->
		</div>
		
		<div class="mb-5">
			<div class="row service-section">
				<div class="col-md-3 home_service">
						<div class="gallery_box">
							<a href="<?= base_url()?>public/front/assets/images/service/restaurent.jpg" data-toggle="lightbox" data-gallery="example-gallery">
								<img src="<?= base_url()?>public/front/assets/images/service/restaurent.jpg" class="img-fluid">
								<span class="bd_zoom"><i class="fa fa-arrows-alt" aria-hidden="true"></i></span>
							</a>
						</div>
						<div class="service-text">
							<p>Restaurant</p>
						</div>
				</div>
				<div class="col-md-3 home_service">
						<div class="gallery_box">
							<a href="<?= base_url()?>public/front/assets/images/service/sea.jpg" data-toggle="lightbox" data-gallery="example-gallery">
								<img src="<?= base_url()?>public/front/assets/images/service/sea.jpg" class="img-fluid">
								<span class="bd_zoom"><i class="fa fa-arrows-alt" aria-hidden="true"></i></span>
							</a>
						</div>
						<div class="service-text">
							<p>Sea</p>
						</div>
				</div>
				<div class="col-md-3 home_service">
						<div class="gallery_box">
							<a href="<?= base_url()?>public/front/assets/images/service/swimming.jpg" data-toggle="lightbox" data-gallery="example-gallery">
								<img src="<?= base_url()?>public/front/assets/images/service/swimming.jpg" class="img-fluid">
								<span class="bd_zoom"><i class="fa fa-arrows-alt" aria-hidden="true"></i></span>
							</a>
						</div>
						<div class="service-text">
							<p>Swimming Pool</p>
						</div>
				</div>
				<div class="col-md-3 home_service">
						<div class="gallery_box">
							<a href="https://hotelaviana.com/public/uploads/gallery/1614713972.jpeg" data-toggle="lightbox" data-gallery="example-gallery">
								<img src="https://hotelaviana.com/public/uploads/gallery/1614713972.jpeg" class="img-fluid">
								<span class="bd_zoom"><i class="fa fa-arrows-alt" aria-hidden="true"></i></span>
							</a>
						</div>
						<div class="service-text">
							<p>Room Service</p>
						</div>
				</div>
				
				
			</div>
			
		</div>
	
	
<div class="home_service d-flex">
	<div class="home_service_img"><img src="<?= base_url()?>public/uploads/gallery/16146298171.jpeg" alt=""></div>
	<div class="home_service_text">
		<h4>REASONS TO CHOOSE HOTEL AVIANI</h4>
		<div class="home_service_cont"><ul>
			<li>
			<div class="reason-item">
				<h5>Multicuisine Restaurant</h5>
				<p>Now enjoy the delicious seafood, chinese dish, continental in beach themed restaurant</p>
			</div>
			</li>
			<li>
			<div class="reason-item">
				<h5>Park For All</h5>
				<p>The playground is suitable for for all. We recommend parental supervision as the playground is not monitored by the staff at Hotel Aviana Digha.</p>
			</div>
			</li>
			<li>
			<div class="reason-item">
				<h5>Conference Room</h5>
				<p>Perfect place to host business meeting, seminars in digha. Stay at the best corporate hotel in digha</p>
			</div>
			</li>
			<li>
			<div class="reason-item">
				<h5>Swimming Pool</h5>
				<p>Beach themed swimming pool is the perfect place to spend after visitng Sea Beach in Digha.</p>
			</div>
			</li>
		</ul>
	</div>
</div>
</div>
</div>
</div>
<div class="ptb guaranted_bg parallax">
<div class="container">
<div class="d-flex">
<div class="guaranted_text align-self-end">
	<h3>100% Satisfaction Guaranteed</h3>
	<p>Whether this is your first visit, or you have been a guest many times, we want your experience to be excellent. Our staff is always available to help with any questions or concerns you may have.</p>
	<p>Please keep the questions simple and don’t count on the answers being correct.</p>
</div>
<div class="guaranted_img">
	<img src="https://hotelcoraldigha.com/newdev/wp-content/uploads/2018/12/13221523_10208214247506061_7975917934776621680_n.jpg">
</div>
</div>
</div>
</div>
<div class="ptb">
<div class="container">
<div class="subscribe_sec">
<div class="heading text-center">
	<h2>Stay Connected</h2>
	<p>Be the first to hear about local events and new amenities.</p>
</div>
<div class="subscribe_form">
	
	<div class="emaillist">
		<form action="#" method="post" class="es_subscription_form es_shortcode_form" id="es_subscription_form_1612089134" data-source="ig-es">
			<div class="es-field-wrap"><label><input class="es_required_field es_txt_email" type="email" name="email" value="" placeholder="Enter your email address" required=""></label></div>				
			
			<input type="submit" name="submit" class="es_subscription_form_submit es_submit_button es_textbox_button" id="es_subscription_form_submit_1612089134" value="Subscribe">
			
			<span class="es_spinner_image" id="spinner-image"  style="display: none;"><img src="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/email-subscribers/lite/public/images/spinner.gif"></span>
		</form>
		<span class="es_subscription_message" id="es_subscription_message_1612089134"></span>
	</div>
	
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#es_subscription_form_1612089134').submit(function(e)
		{
			e.preventDefault();
			$.ajax({
				type:'post',
				url:"<?= base_url()?>frontend/insert_subscriber",
				data:$('#es_subscription_form_1612089134').serialize(),
				beforeSend:function()
				{
					$('#spinner-image').show();
				},
				success:function(response)
				{
					$('#spinner-image').hide();
					$('.es_txt_email').val('');
					$('.es_subscription_message').html('<p>Thank you for your subscription.</p>');
					console.log(response);
				}
			})
		})
	})
</script>	