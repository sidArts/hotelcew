<link rel="stylesheet" id="woocommerce-general-css" href="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=3.8.1" media="all">

<link rel="stylesheet" id="woocommerce-layout-css" href="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=3.8.1" media="all">

<link rel="stylesheet" href="<?= base_url()?>public/slider/css/swiper.min.css" />
<link rel="stylesheet" href="<?= base_url()?>public/slider/css/easyzoom.css" />
<link rel="stylesheet" href="<?= base_url()?>public/slider/css/main.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css"/>
<style type="text/css">
	.swiper-container
	{
		height: auto !important;
		margin-bottom: 20px;
	}
	.feature-item
	{
	    background: #bcbebe;
	    padding: 10px;
	    border-radius: 4px;
	    border: 1px solid #978f8f;
	}
	.feature-item ul li
	{
		
	}
</style>
<div class="bd_main">

	<div class="inner_banner show-breadcrumb-only">
		<!-- <img class="baner_img_page" src="<?= base_url()?>public/front/assets/images/banner.jpg" alt="Room details"> -->
		<div class="inner_banner_text">

			<div class="container">

				<div class="banner_area_text_box">

					<div class="row">

						<div class="col-md-6"><h1 class="page-title"><?= $roomdetails['name'] ?></h1></div>

						<div class="col-md-6">

							<div class="breadcrumb">

								<ul id="breadcrumbs" class="breadcrumbs">
									<li class="item-home">
										<a class="bread-link bread-home" href="<?= base_url();?>" title="Home">Home
										</a>
									</li>
									<li class="item-home">
										<a class="bread-link bread-home" href="<?= base_url().'rates';?>" title="Home">Rates
										</a>
									</li>
									<li class="separator separator-home"> / </li>
									<li class="item-current item-16">
										<strong class="bread-current bread-16"> 
											<?= $roomdetails['name'] ?>
										</strong>
									</li>
								</ul>
							</div>

						</div>

					</div>



				</div>

			</div>

		</div>
	</div>

	<section class="ptb inner-page woocommerce">
		<div class="container">
			<div class="ratesdetails_sec">
				<div class="woocommerce-notices-wrapper"></div>
				<div id="product-81" class="product type-product post-81 status-publish first instock product_cat-uncategorized has-post-thumbnail shipping-taxable purchasable product-type-simple">
					<div class="images">
						<div class="gallery-parent">
							<!-- SwiperJs and EasyZoom plugins start -->
							<div class="swiper-container gallery-top">
								<div class="swiper-wrapper">
									<?php
									foreach ($room_images as $key => $value) {
										?>
										<div class="swiper-slide easyzoom easyzoom--overlay">
											<a href="<?= base_url()?>public/uploads/room/<?= $value['image']?>">
												<img src="<?= base_url()?>public/uploads/room/<?= $value['image']?>" alt="" />
											</a>
										</div>
										<?php
									}
									?>
								</div>
								<!-- Add Arrows -->
								<div class="swiper-button-next swiper-button-white"></div>
								<div class="swiper-button-prev swiper-button-white"></div>
							</div>
							<div class="swiper-container gallery-thumbs">
								<div class="swiper-wrapper">
									<?php
									foreach ($room_images as $key => $value) {
										?>
										<div class="swiper-slide">
											<img src="<?= base_url()?>public/uploads/room/<?= $value['image']?>" alt="" />
										</div>
										<?php
									}
									?>
								</div>
							</div>
							<!-- SwiperJs and EasyZoom plugins end -->
						</div>
					</div>
					<div class="summary entry-summary">

						<h1 class="product_title entry-title"><?= $roomdetails['name']?></h1>

						<div class="woocommerce-product-rating"></div>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">

									<label for="pwd">Check In:<span>*</span></label>

									<input type="text" class="form-control datepicker-checkin"required="" name="booking_start_date" autocomplete="off">

								</div>	
							</div>
							<div class="col-lg-6">
								<div class="form-group">

									<label for="pwd">Check Out:<span>*</span></label>

									<input type="text" class="form-control datepicker-checkout"required="" name="booking_start_date" autocomplete="off">

								</div>	
							</div>							
						</div>
						<div class="padding-top-bottom-10" id="stay-summary"></div>
						<p class="price">
							<span class="woocommerce-Price-amount amount">
								<span class="woocommerce-Price-currencySymbol">₹</span>
								<span id="per-night-price"></span>
								
							</span>
						</p>

						<div class="woocommerce-product-details__short-description"><p>Per Night</p></div>

						<div><p>Capacity: <?= $roomdetails['person'] ?> persons per room.</p></div>

						<!-- <div><p>Available rooms: <?= $available_room ?></p></div> -->

						<div class="product_meta">
							<?= $roomdetails['description'];?>
							<button type="button" class="btn btn-primary" id="book-now">Book Now</button>
						</div>
					</div>

					<section class="related products" style="clear: both;">
						<h2>Related rooms</h2>
						<ul class="products columns-4">

							<?php foreach ($rooms as $key => $value) {
								if($roomdetails['id'] != $value['id']) { ?>

							<li class="product type-product post-79 status-publish  instock product_cat-uncategorized has-post-thumbnail shipping-taxable purchasable product-type-simple">

								<a href="<?= base_url('rooms/'.$value["slug"])?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><img width="250" height="250" src="<?= base_url()?>public/uploads/room/<?= $value['image']?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" srcset="<?= base_url()?>public/uploads/room/<?= $value['image']?>" sizes="(max-width: 250px) 85vw, 250px"><h2 class="woocommerce-loop-product__title"><?= $value['name']?></h2><!-- <div class="star-rating" role="img" aria-label="Rated 3.00 out of 5"><span style="width:60%">Rated <strong class="rating">3.00</strong> out of 5</span></div> -->

									<span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?= number_format($value['back_rate'],2)?></span></span>

								</a>

							</li>
							<?php }} ?>
						</ul>
					</section>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- CREATE NEW BOOKING Modal -->

<div id="myModal" class="modal fade enquiryform" role="dialog" aria-hidden="true" style="display: none;">

	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">TWIN DELUX - ₹2000.00</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>

			<div class="modal-body contact_form1">

				<form id="enquiry-form" method="post" action="<?=base_url()?>frontend/createbooking" novalidate>

					<input type="hidden" name="room_id" value="" id="room_id">

					<div class="form-group">

						<label for="pwd">Full Name:<span>*</span></label>

						<input type="text" class="form-control" id="pwd" required="" name="name">

					</div>

					<div class="form-group">

						<label for="pwd">Email:<span>*</span></label>

						<input type="email" class="form-control" id="pwd" required="" name="email">

					</div>

					<div class="form-group">

						<label for="pwd">Phone:<span>*</span></label>

						<input type="text" class="form-control numbersOnly" id="pwd" required="" name="phone" maxlength="15" minlength="10">

					</div>

					<div class="form-group">

						<label for="pwd">Check In:<span>*</span></label>

						<input type="text" class="form-control datepicker_checkin"required="" name="booking_start_date" autocomplete="off">

					</div>

					<div class="form-group">

						<label for="pwd">Check Out:<span>*</span></label>

						<input type="text" class="form-control datepicker_checkout"required="" name="booking_end_date" autocomplete="off">

					</div>

					<div class="form-group" id="room-select-form-group">

						<label for="pwd">No of Rooms (<span id="room-count-by-date-range"></span> rooms available):<span>*</span></label>

						<input type="text" name="no_of_room" class="form-control">

					</div>

					<div class="form-group" id="no-room-available-waring">

						Sorry, no rooms are available on selected dates!

					</div>


					<button type="submit" id="booking-submit-btn" class="btn btn-warning" disabled="disabled">Submit</button>

					<img src="https://dev5.ivantechnology.in/fijihistory/wp-content/themes/myfiji/images/orange_circles.gif" id="loader-img" style="width: 84px;display: none">

					<p style="color: red;display: none" id="error-alert">Something error</p>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="loader-text">Loading...</div>
<div class="loader"></div>
<div class="loader-overlay"></div>
<script src="<?= base_url()?>public/slider/js/swiper.min.js"></script>
<script src="<?= base_url()?>public/slider/js/easyzoom.js"></script>
<script src="<?= base_url()?>public/slider/js/main.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>

<script type="text/javascript">
	var roomId = '<?= $roomdetails['id']?>';
	var roomSlug = '<?= $roomdetails['slug']?>';
	var getRoomAvailabilityOnDateSelect = function () {
		// console.log("Selected date: " + dateText + "; input's current value: " + this.value);
        // $(this).val(dateText);
        var startDate = $(".datepicker_checkin").val().trim();
    	var endDate = $(".datepicker_checkout").val().trim();
    	console.log(startDate, endDate);
        if(startDate != '' && endDate != '') {
        	
        	var url = `/frontend/getRoomAvailabilityByDateRange/${roomId}/${startDate}/${endDate}`;
        	if(new Date(startDate).getTime() >= new Date(endDate).getTime()) {
        		$('.datepicker_checkout').val('');
        		$('#room-select-form-group').hide();
        		$('#no-room-available-waring').hide();
        		$('#booking-submit-btn').attr('disabled', 'disabled');
        	} else {
        		showHideLoader('show', 'Checking Rooms Availability...');
        		$.get(url, (data) => {
	        		$('#room-count-by-date-range').text(data['available_rooms']);        		
	        		if(data['available_rooms'] == 0) {
		        		$('#no-room-available-waring').show();
		        		$('#room-select-form-group').hide();
	        			$('#booking-submit-btn').attr('disabled', 'disabled');
		        	} else {
		        		$('#room-select-form-group').show();
		        		$('#no-room-available-waring').hide();
		        		$('#booking-submit-btn').removeAttr('disabled');
		        	}
		        	showHideLoader('hide');
	        	});
        	}
        }
	};

	var getDynamicRoomRatesInDateRange = function(startDate, endDate) {
		if(startDate != '' && endDate != '') {
			if(new Date(startDate).getTime() >= new Date(endDate).getTime()) {
	    		$('.datepicker-checkout').val('');
	    		// $('#room-select-form-group').hide();
	    		// $('#no-room-available-waring').hide();
	    		// $('#booking-submit-btn').attr('disabled', 'disabled');
	    	} else {
	    		showHideLoader('show');
	    		var url = `/frontend/getRoomRateByDate/${roomSlug}/${startDate}/${endDate}`;
				$.get(url, data => {
					var totalPrice = data.reduce((a, c) => { 
						a += +c.back_rate;
						return a;
					 }, 0);
					var perNightCost = totalPrice / data.length;
					console.log(perNightCost);
					var totalNights = data.length;
					var totalDays = data.length + 1;
					var staySummary = ((totalNights > 1) ? `${totalNights} Nights` : `${totalNights} Night`) + `, ${totalDays} Days`;
					$('#stay-summary').text(staySummary);
					$('#per-night-price').text(parseInt(perNightCost));
					showHideLoader('hide');
				}, 'json');
	    	}
		}
		
		
		
	};

	$(document).ready(function() {
		"use strict";

		$('#room-select-form-group').hide();
		$('#no-room-available-waring').hide();
		 
		$('#book-now').click(function() {

			$('#myModal').modal('show');

			$('.modal-title').text("<?= $roomdetails['name'].' - '.number_format($roomdetails['back_rate'],2)?>");

			$('#room_id').val("<?= $roomdetails['id']?>");

		})
		var maxStartDateTimestamp = new Date().setHours(0,0,0,0) + (60 * 24 * 60 * 60 * 1000);
		var maxStartDate = new Date(maxStartDateTimestamp);
		$(".datepicker_checkin").datepicker({
			dateFormat: 'yy-mm-dd',
    		minDate : 0,
    		maxDate: maxStartDate,
    		onSelect: function(dateText) {
    			var checkoutMinDate = new Date(dateText);
    			checkoutMinDate.setDate(checkoutMinDate.getDate() + 1);

    			var checkoutMaxDate = new Date(dateText);
    			checkoutMaxDate.setDate(checkoutMaxDate.getDate() + 30);

    			$('.datepicker_checkout').datepicker("option", "minDate", checkoutMinDate);
    			$('.datepicker_checkout').datepicker("option", "maxDate", checkoutMaxDate);
		        getRoomAvailabilityOnDateSelect(dateText);
		    }
    	});

		var maxEndDate = new Date(maxStartDateTimestamp + (30 * 24 * 60 * 60 * 1000));
    	$(".datepicker_checkout").datepicker({
			dateFormat: 'yy-mm-dd',
    		minDate : new Date(new Date().setHours(0,0,0,0) + (24 * 60 * 60 * 1000)),
    		maxDate: maxEndDate,
    		onSelect: function(dateText) {
		        getRoomAvailabilityOnDateSelect(dateText);
		    }
    	});


    	$(".datepicker-checkin").datepicker({
			dateFormat: 'yy-mm-dd',
    		minDate : 0,
    		maxDate: maxStartDate,
    		defaultDate: new Date(),
    		onSelect: function(dateText) {
    			var checkoutMinDate = new Date(dateText);
    			checkoutMinDate.setDate(checkoutMinDate.getDate() + 1);

    			var checkoutMaxDate = new Date(dateText);
    			checkoutMaxDate.setDate(checkoutMaxDate.getDate() + 30);

    			$('.datepicker-checkout').datepicker("option", "minDate", checkoutMinDate);
    			$('.datepicker-checkout').datepicker("option", "maxDate", checkoutMaxDate);
    			getDynamicRoomRatesInDateRange(dateText, $(".datepicker-checkout").val());
		    }
    	});

		var maxEndDate = new Date(maxStartDateTimestamp + (30 * 24 * 60 * 60 * 1000));
    	$(".datepicker-checkout").datepicker({
			dateFormat: 'yy-mm-dd',
    		minDate : new Date(new Date().setHours(0,0,0,0) + (24 * 60 * 60 * 1000)),
    		maxDate: maxEndDate,
    		onSelect: function(dateText) {
		        // getRoomAvailabilityOnDateSelect(dateText);
		        getDynamicRoomRatesInDateRange($(".datepicker-checkin").val(), dateText);
		    }
    	});

    	
    	$(".datepicker-checkin").datepicker('setDate', new Date()); 
    	$(".datepicker-checkout").datepicker('setDate', 1); 

		getDynamicRoomRatesInDateRange($(".datepicker-checkin").val(), 
			$(".datepicker-checkout").val());
		$('#enquiry-form').bootstrapValidator({
		    message: 'This value is not valid',
		    fields: {
		        name: {
		            validators: {
		                notEmpty: {
		                    message: 'Name is required and can\'t be empty'
		                },

		            }
		        },
		        email: {
		            validators: {
		                notEmpty: {
		                    message: 'Email is required and can\'t be empty'
		                },

		            }
		        },
		        phone: {
		            validators: {
		                notEmpty: {
		                    message: 'Phone no is required and can\'t be empty'
		                },

		            }
		        },
		        booking_start_date: {
		            validators: {
	                    date: {
	                        format: 'YYYY-MM-DD',
	                        message: 'The format is yy-mm-dd'
	                    },
	                    notEmpty: {
	                        message: 'Check In is required and can\'t be empty'
	                    }
	                }
		        },
		        booking_end_date: {
		            validators: {
		                notEmpty: {
		                    message: 'Check Out date is required and can\'t be empty'
		                },

		            }
		        },
		        no_of_room: {
		            validators: {
		                notEmpty: {
		                    message: 'No of rooms is required and can\'t be empty'
		                },
		                regexp: {
	                        regexp: /^[0-9]+$/,
	                        message: 'No of rooms can only consist of number'
	                    },
		            }
		        },
		        
		    }

		});

	});
</script>




















