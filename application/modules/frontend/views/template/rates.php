<link rel="stylesheet" id="woocommerce-general-css" href="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=3.8.1" media="all">

<link rel="stylesheet" id="woocommerce-layout-css" href="https://dev6.ivantechnology.in/mountdesertholding/wp-content/plugins/woocommerce/assets/css/woocommerce-layout.css?ver=3.8.1" media="all">

<div class="bd_main">
	<div class="inner_banner">

		<img class="baner_img_page" src="<?= base_url()?>public/front/assets/images/banner.jpg" alt="Room rates">

		

		<div class="inner_banner_text">

			<div class="container">

				<div class="banner_area_text_box">

					<div class="row">

						<div class="col-md-6"><h1 class="page-title">Rates</h1></div>

						<div class="col-md-6">

							<div class="breadcrumb">

					            <ul id="breadcrumbs" class="breadcrumbs"><li class="item-home"><a class="bread-link bread-home" href="<?= base_url()?>" title="Home">Home</a></li><li class="separator separator-home"> / </li><li class="item-current item-16"><strong class="bread-current bread-16"> Rates</strong></li></ul>					        </div>

						</div>

					</div>



		        </div>

			</div>

		</div>

	</div>

	<section class="ptb inner-pag woocommerce">

	<div class="container">

        <div class="rates_sec">
        	<h2 class="text-center">Rooms Rates</h2>



		<ul class="products columns-4">

			<?php

			foreach ($rooms as $key => $value) {

				?>

				<li class="product type-product post-83 status-publish  instock product_cat-uncategorized has-post-thumbnail virtual purchasable product-type-simple">

				<a href="<?= base_url()?>rooms/<?= $value['slug']?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link"><img width="250" height="250" src="<?= base_url()?>public/uploads/room/<?= $value['image']?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="" srcset="<?= base_url()?>public/uploads/room/<?= $value['image']?>" sizes="(max-width: 250px) 85vw, 250px"><h2 class="woocommerce-loop-product__title"><?= $value['name']?></h2><!-- <div class="star-rating" role="img" aria-label="Rated 3.00 out of 5"><span style="width:60%">Rated <strong class="rating">3.00</strong> out of 5</span></div> -->

				<span class="price"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">₹</span><?= number_format($value['back_rate'],2) ?></span></span>

			</a>

		</li>

				<?php

			}

			?>

			





		</ul>

</div>        </div>

    

</section>



</div>







