


<div class="bd_main">







	<div class="inner_banner show-breadcrumb-only">

				<img class="baner_img_page" src="<?= base_url()?>public/front/assets/images/banner.jpg" alt="Contact Us">

		

		<div class="inner_banner_text">

			<div class="container">

				<div class="banner_area_text_box">

					<div class="row">

						<div class="col-md-6"><h1 class="page-title">Contact Us</h1></div>

						<div class="col-md-6">

							<div class="breadcrumb">

					            <ul id="breadcrumbs" class="breadcrumbs"><li class="item-home"><a class="bread-link bread-home" href="<?= base_url()?>" title="Home">Home</a></li><li class="separator separator-home"> / </li><li class="item-current item-16"><strong class="bread-current bread-16"> Contact Us</strong></li></ul>					        </div>

						</div>

					</div>



		        </div>

			</div>

		</div>

	</div>







	<section class="inner_con ptb contact_sec">



		<div class="container">



			<div class="row">



				<div class="col-md-6">



					<div class="con_heading"><h2>Get In Touch</h2></div>



					<div id="post-16" class="post-16 page type-page status-publish hentry">
							<?= $content;?>

											</div>



					<div class="contact_form1">

						<div>

							

							<form action="#" method="post" id="contact-form">

							<div class="row">

							<div class="col-12 mb-4">

							<label>Name<span>*</span></label><span class="wpcf7-form-control-wrap text-242"><input type="text" name="name" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" aria-required="true" aria-invalid="false" required=""></span>

							</div>

							<div class="col-12 mb-4">

							<label>Email<span>*</span></label><span class="wpcf7-form-control-wrap email-488"><input type="email" name="email" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email" aria-required="true" aria-invalid="false" required=""></span>

							</div>

							<div class="col-12 mb-4">

							<label>Number<span>*</span></label><span class="wpcf7-form-control-wrap text-243"><input type="text" name="phone" value="" size="40" maxlength="14" minlength="10" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required numbersOnly" aria-required="true" aria-invalid="false" required=""></span>

							</div>

							<div class="col-12 mb-4">

							<label>Message</label><span class="wpcf7-form-control-wrap textarea-381"><textarea name="message" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea" aria-invalid="false"></textarea></span>

							</div>

							<div class="col-12">
							<button type="submit" class="wpcf7-form-control wpcf7-submit">Send</button>
							<span class="es_spinner_image" id="spinner-image" style="display: none;"><img src="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/email-subscribers/lite/public/images/spinner.gif"></span>

							</div>

							</div>

							<div class="es_subscription_message"></div></form></div>					</div>



				</div>



				<div class="col-md-6">

					<div class="con_heading"><h2>Contact Details</h2></div>

					<div class="condetails">

						<ul>

							<li><i class="fa fa-map-marker" aria-hidden="true"></i><?= $site_details['address'];?></li>

							<li><i class="fa fa-phone" aria-hidden="true"></i><a href="tel:<?= $site_details['admin_phone'];?>"><?= $site_details['admin_phone'];?></a></li>

							<li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:reiffoffice@aol.com"><?= $site_details['admin_email'];?></a></li>

						</ul>

					</div>

					<div class="con_heading"><h2>Contact Details</h2></div>

					<div class="consocial">

						<ul class="social d-flex">

							<li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>

							<li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>

							<li><a href="https://in.pinterest.com/" target="_blank"><i class="fa fa-pinterest-p" aria-hidden="true"></i></a></li>

						</ul>

					</div>

					<div class="conmap">
						<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14836.16542229402!2d87.5012206!3d21.6233121!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x8318fca29e08b36!2sHotel%20Aviani!5e0!3m2!1sen!2sin!4v1615632102075!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>

					</div>

				</div>



			</div>







		</div>



	</section>











</div>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#contact-form').submit(function(e)
		{
			e.preventDefault();
			$.ajax({
				type:'post',
				url:"<?= base_url()?>frontend/send_contact_form",
				data:$('#contact-form').serialize(),
				beforeSend:function()
				{
					$('#spinner-image').show();
				},
				success:function(response)
				{
					$('#spinner-image').hide();
					$('#contact-form input').val('');
					$('#contact-form textarea').val('');
					$('.es_subscription_message').html('<p>Message sent successfully.</p>');
					console.log(response);
				}
			})
		})
	})
</script>







