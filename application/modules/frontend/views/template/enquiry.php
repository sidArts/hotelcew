<?php
/**
 Template name: Enquiry Template
*/
get_header();?>

<?php 
	$hours = get_option('business_hours');
	$mail = get_option('store_email_id');
	$call = get_option('store_mobile_no');
	$address = get_option('store_address');

	$facebook = get_option('facebook_link');
	$twitter = get_option('twitter_link');
	$pinterest = get_option('pinterest_link');

	$map = get_option( 'contact_map');
?>
<div class="bd_main">

	<?php get_template_part( 'template-parts/innerbanner', 'page' );?>

	<section class="inner_con ptb contact_sec">
		<div class="container">
			<div class="form">
				<?php echo do_shortcode('[contact-form-7 id="122" title="Enquiry Form"]');?>
			</div>
		</div>
	</section>


</div>

<?php get_footer(); ?>