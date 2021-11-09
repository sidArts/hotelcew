<div class="bd_main">

	<div class="inner_banner">

				<img class="baner_img_page" src="<?= base_url()?>public/front/assets/images/banner.jpg" alt="Gallery">

		

		<div class="inner_banner_text">

			<div class="container">

				<div class="banner_area_text_box">

					<div class="row">

						<div class="col-md-6"><h1 class="page-title">Gallery</h1></div>

						<div class="col-md-6">

							<div class="breadcrumb">

					            <ul id="breadcrumbs" class="breadcrumbs"><li class="item-home"><a class="bread-link bread-home" href="<?= base_url();?>" title="Home">Home</a></li><li class="separator separator-home"> / </li><li class="item-current item-16"><strong class="bread-current bread-16"> Gallery</strong></li></ul>					        </div>

						</div>

					</div>



		        </div>

			</div>

		</div>

	</div>







	<section class="inner_con ptb">



		<div class="container">



			<div class="gallery_sec">



				<div class="row">
					<?php
					foreach ($gallery as $key => $value) {
						?>
						<div class="col-md-4 col-sm-6 mb-4">



							<div class="gallery_box">



								<a href="<?= base_url()?>public/uploads/gallery/<?= $value['image']?>" data-toggle="lightbox" data-gallery="example-gallery">



									<img src="<?= base_url()?>public/uploads/gallery/<?= $value['image']?>" class="img-fluid">



									<span class="bd_zoom"><i class="fa fa-arrows-alt" aria-hidden="true"></i></span>



								</a>



								<!-- <div class="gatitle">Gallery 1</div> -->



							</div>



						</div>
						<?php
					}

					?>

				</div>



			</div>



			







				







		</div>



	</section>











</div>















