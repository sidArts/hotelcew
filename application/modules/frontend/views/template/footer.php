	<footer class="footer_sec">

		<div class="container">

			<div class="row">

				<div class="col-md-3 col-sm-6">

					<div class="ftr_box">

						<strong>Quick Link</strong>

						<ul id="menu-quick-links" class="ftr_link">

							<li id="menu-item-22" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-22"><a href="<?= base_url();?>">Home</a></li>

						  	<!-- <li id="menu-item-23" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-23"><a href="<?= base_url('intimate-hotel-experience')?>">Intimate hotel experience</a></li> -->

							

							<li id="menu-item-86" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-86"><a href="<?= base_url('rates')?>">Rates</a></li>

							<li id="menu-item-21" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-21"><a href="<?= base_url('gallery')?>">Gallery</a></li>

							<li id="menu-item-20" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-20"><a href="<?= base_url('contact-us')?>">Contact Us</a></li>

						</ul>					

					</div>

				</div>

				<div class="col-md-3 col-sm-6">

					<div class="ftr_box">

						<strong>Rooms</strong>

						<ul id="menu-packages-menu" class="ftr_link">

							<?php

							$rooms = $this->Custom->get_rooms();

							//var_dump($rooms);

							foreach ($rooms as $key => $value) {

								?>

								<li class=""><a href="<?= base_url('rooms/').$value['slug']?>"><?= $value['name']?></a></li>

								<?php

							}

							?>

						</ul>					

					</div>

				</div>

				<div class="col-md-3 col-sm-6">

					<div class="ftr_box">

						<strong>Business Hours</strong>

						<div class="ftr_link">

							<p>Mon - Fri</p>

<p>9:00 am – 5:00 pm</p>

<p>Sat &amp; Sun Day</p>

<p>Closed</p>						</div>

					</div>

				</div>

				<div class="col-md-3 col-sm-6">

					<div class="ftr_box">
						<?php $site_details = $this->Custom->get_site_details(); ?>
						<strong>Hotel Aviana</strong>

						<div class="ftr_ad"><img src="https://dev6.ivantechnology.in/mountdesertholding/wp-content/themes/mountdesertholding/assets/images/location.png" alt=" "><?= $site_details[0]['address']?></div>

						<div class="ftr_ad"><img src="https://dev6.ivantechnology.in/mountdesertholding/wp-content/themes/mountdesertholding/assets/images/call.png" alt=""><a href="tel:<?= $site_details[0]['admin_phone']?>"><?= $site_details[0]['admin_phone']?></a></div>

						<div class="ftr_ad"><img src="https://dev6.ivantechnology.in/mountdesertholding/wp-content/themes/mountdesertholding/assets/images/msg.png" alt=""><a href="mailto:<?= $site_details[0]['admin_email']?>"><?= $site_details[0]['admin_email']?></a></div>

					</div>

				</div>

			</div>

			

		</div>



		<div class="footer_lower">

			<div class="container">

				<div class="row">

					<div class="col-lg-8 col-md-8 col-12 align-self-center">

						© 2020 Hotel Aviana, All right reserved.

					</div>

					<div class="col-lg-4 col-md-4 col-12">

						<ul class="social d-flex justify-content-end">

							<li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>

							<li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>

							<li><a href="https://in.pinterest.com/" target="_blank"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>

						</ul>

					</div>

				</div>

			</div>

		</div>

	</footer>			

	</body>

</html>



  <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  -->



<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/popper.min.js"></script>

<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/owl.carousel.js"></script>

<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/bootstrap.min.js"></script>

<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/ekko-lightbox.min.js"></script>

<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/slick.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">	

<script type="text/javascript" src="<?= base_url()?>public/front/assets/js/custom.js"></script>









