<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Welcome Section -->
<div class="home_cat">
	<div class="container">
		<h2>Our popular categories</h2>
		<div class="row">
		<?php   
			if(count($categories)>0)
			{
				$amx=0;
				foreach(array_reverse($categories) as $category)
				{
					if($category['image']!=NULL || $category['image']!="")
					{
						?>
							<div class="col-md-4">
								<a href="<?= base_url()."public/uploads/".$category['image']; ?>"><img class="pulse" src="<?= base_url()."public/uploads/".$category['image']; ?>" alt="" /></a>
							</div>
						<?php
						$amx++;
						if($amx==2)
						{
							break;
						}
					}
					
				}
			}
		?>
			<div class="col-md-4">
				<a href="javascript:void(0)"><img class="pulse" src="<?=FRONT_ASSETS?>images/sale-1.png" alt="" /></a>
			</div>
			<!-- <div class="col-md-4">
				<a href="javascript:void(0)"><img class="pulse" src="<images/sale-2.png" alt="" /></a>
			</div>
			<div class="col-md-4">
				<a href="javascript:void(0)"><img class="pulse" src="images/sale-3.png" alt="" /></a>
			</div> -->
		</div>
	</div>
</div>

<div class="propRecomandArea">
	<div class="container">
		<div class="row">
			<h2>Our popular Products</h2>
			<div class="col-sm-9">
			<div class="propProd">
					<div class="owl-carousel owl-theme propProdSlid">
					<?php
							if(count($products)>0)
							{
								
								foreach($products as $product)
								{
									?>
										<div class="item">
											<div class="productRap">
												<div class="proimg">
													<a href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>">
														<img src="<?= base_url()."public/uploads/".$product['pimg']; ?>" alt="">
													</a>
													<a class="wish" href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
												</div>
												<div class="proDtls">
													<h3 class="proNm"><a href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>"><?php echo $product['title']; ?></a></h3>
													<div class="price-Rap">
														<span class="label">Starting at:</span>
														<span class="price">$<?php echo $product['price']; ?></span>
													</div>
													<div class="rating">
														<img src="<?=FRONT_ASSETS?>images/rating.png"> <span>20</span>
													</div>
													<div class="proColAction">
														<button class="addToCart btn"  onclick="addtocart('<?php echo $product['product_id']; ?>','<?php echo $product['sku']; ?>','<?php echo $product['qty']; ?>');" value="">ADD TO CART</button>
														<a class="compaire" href="#">COMPARE</a>
													</div>
												</div>
											</div>
										</div>
									<?php
								}
							}
						?>
					</div>
				</div>
				<div class="rePropProd">
					<h2>Recommended Products</h2>
					<div class="owl-carousel owl-theme propProdSlid">
						
					<?php
							if(count($recomended_product)>0)
							{
								foreach($recomended_product as $product)
								{
									?>
										<div class="item">
											<div class="productRap">
												<div class="proimg">
													<a href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>">
														<img src="<?= base_url()."public/uploads/".$product['pimg']; ?>" alt="">
													</a>
													<a class="wish" href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
												</div>
												<div class="proDtls">
													<h3 class="proNm"><a href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>"><?php echo $product['title']; ?></a></h3>
													<div class="price-Rap">
														<span class="label">Starting at:</span>
														<span class="price">$<?php echo $product['price']; ?></span>
													</div>
													<div class="rating">
														<img src="<?=FRONT_ASSETS?>images/rating.png"> <span>20</span>
													</div>
													<div class="proColAction">
														<button class="addToCart btn"  onclick="addtocart('<?php echo $product['product_id']; ?>','<?php echo $product['sku']; ?>','<?php echo $product['qty']; ?>');" value="">ADD TO CART</button>
														<a class="compaire" href="#">COMPARE</a>
													</div>
												</div>
											</div>
										</div>
									<?php
								}
							}
						?>		
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="sidAd">
					<a href="#"><img src="<?=FRONT_ASSETS?>images/sidAd.png" alt=""></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="recentlyViewRap">
	<div class="container">
		<header class="recentlyView">
			<h2>Recently Viewed Products</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. </p>
		</header>
		<div class="propProd">
					<div class="owl-carousel owl-theme propProdSlid">
					<?php
							if(count($products)>0)
							{
								foreach($products as $product)
								{
									?>
										<div class="item">
											<div class="productRap">
												<div class="proimg">
													<a href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>">
														<img src="<?= base_url()."public/uploads/".$product['pimg']; ?>" alt="">
													</a>
													<a class="wish" href="<?= base_url()."product-details/".$product['slug']."/".base64_encode($product['product_id']); ?>"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
												</div>
												<div class="proDtls">
													<h3 class="proNm"><a href=""><?php echo $product['title']; ?></a></h3>
													<div class="price-Rap">
														<span class="label">Starting at:</span>
														<span class="price">$<?php echo $product['price']; ?></span>
													</div>
													<div class="rating">
														<img src="<?=FRONT_ASSETS?>images/rating.png"> <span>20</span>
													</div>
													<div class="proColAction">
														<button class="addToCart btn"  onclick="addtocart('<?php echo $product['product_id']; ?>','<?php echo $product['sku']; ?>','<?php echo $product['qty']; ?>');" name="" value="">ADD TO CART</button>
														<a class="compaire" href="#">COMPARE</a>
													</div>
												</div>
											</div>
										</div>
									<?php
								}
							}
						?>
					</div>
				</div>
				<div class="rePropProd">
					<h2>Recommended Products</h2>
					<div class="owl-carousel owl-theme propProdSlid">
						
					<?php
							if(count($products)>0)
							{
								foreach($products as $product)
								{
									?>
										<div class="item">
											<div class="productRap">
												<div class="proimg">
													<a href="#">
														<img src="<?= base_url()."public/uploads/".$product['pimg']; ?>" alt="">
													</a>
													<a class="wish" href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
												</div>
												<div class="proDtls">
													<h3 class="proNm"><a href="#"><?php echo $product['title']; ?></a></h3>
													<div class="price-Rap">
														<span class="label">Starting at:</span>
														<span class="price">$<?php echo $product['price']; ?></span>
													</div>
													<div class="rating">
														<img src="<?=FRONT_ASSETS?>images/rating.png"> <span>20</span>
													</div>
													<div class="proColAction">
														<button class="addToCart btn" onclick="addtocart('<?php echo $product['product_id']; ?>','<?php echo $product['sku']; ?>','<?php echo $product['qty']; ?>');" value="">ADD TO CART</button>
														<a class="compaire" href="#">COMPARE</a>
													</div>
												</div>
											</div>
										</div>
									<?php
								}
							}
						?>		
					</div>
				</div>
	</div>
</div>

<div class="propIndustry">
	<div class="container">
		<div class="row">
			<div class="col-sm-12">
				<div class="propIndusHdr">
					<h2>Click below to see which Badge Reels are popular by industry</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. </p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus1.jpg">
					</a>
					<a href="#">Hospitals & Nursing <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus2.jpg">
					</a>
					<a href="#">Employee ID <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus3.jpg">
					</a>
					<a href="#">Resellers <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus4.jpg">
					</a>
					<a href="#">Security <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus5.jpg">
					</a>
					<a href="#">Conventions / Trade shows <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
			<div class="col-lg-4 propColRap">
				<div class="propCol">
					<a class="propImg" href="#">
						<img src="<?=FRONT_ASSETS?>images/indus6.jpg">
					</a>
					<a href="#">Warehouse & Utility <img src="<?=FRONT_ASSETS?>images/aroImg.png" alt=""></a>
				</div>
			</div>
		</div>
	</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>

    function setOrdering(valcu)

    {

        window.location.href="<?= base_url(); ?>products?q=<?php  if(isset($_GET['q'])) { echo $_GET['q'];  } else {  ""; }  ?>&order="+valcu;

    }


    function addtocart(product_id,sku_id,qty)
    {

        var user_id="<?php if(isset($this->session->userdata['customer_sess'])){ echo $this->session->userdata['customer_sess']['id']; } else {"";} ?>";

        var product_id=product_id;

        var sku_id=sku_id;

        var status='A';

        var originalQty=qty;

        var qty=1;

        if(user_id=="")
        {

            Swal.fire({

                title: '<strong>Please Login to Continue</strong>',

                html: '<form id="loginform" action="<?php echo base_url(); ?>auth/member_login" method="POST"><div class="form-group"><label>Enter Your email address</label><input type="email" name="email_address" class="form-control valid" id="email_address" placeholder="Email address" aria-required="true" aria-invalid="false">\ </div>\ <div class="form-group">\ <label>Enter Your Password</label>\ <input type="password" name="password" class="form-control valid" id="password" placeholder="Password" aria-required="true" aria-invalid="false">\ </div>\ <div class="form-group">\ <input type="hidden" name="loginFrom" value="popup">\ <input type="submit" class="btn btn-info"><br>Do not have account ? <a href="<?php echo base_url(); ?>member/sign-up">Sign Up</a></div></form>',

                showCancelButton: false,

                showConfirmButton: false

            }); 

        }
        else
        {
            var IntStockOriginal=parseInt(originalQty);
            var refelectQty=parseInt(qty);



            if(refelectQty<=IntStockOriginal)
            {
                $.post("<?php echo base_url(); ?>addToCart/",
                {
                    user_id: user_id,
                    product_id: product_id,
                    sku_id: sku_id,
                    qty: qty,
                    status: status,
                },

                function(data, status){
                    if(status=="success")
                    {
                        Swal.fire({
                            title: "Product Added to cart successfully!",
                            icon: "success",
                            button: "OK",
                        });
                        $("#cart_incr_id").html(data);
                    }
                    else
                    {
                        swal.fire({
                            title: "Something Went Wrong Please Try again Later!",
                            icon: "danger",
                            button: "OK",
                        });
                    }
                });
            }
            else
            {
            Swal.fire({
                    title: "This Much stock is not available in the stock",
                    icon: "warning",
                    button: "OK",
                });
            }
        }


    }

</script>