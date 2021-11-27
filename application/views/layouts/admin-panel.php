<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<?php

	if(!$this->session->has_userdata(ADMIN_SESS)) {

		redirect(ADMIN_URL.'login');

	}else{

		$adminCred = $this->session->userdata(ADMIN_SESS);

	}

?>

<!DOCTYPE html>

<html>

	<head>

	    <meta charset="UTF-8">

	    <title>Hotel Aviana</title>

		<link rel="shortcut icon" type="image/png" href="#Image-Path-here" >

		

	    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	    <meta name="description" content="Developed By M Abdur Rokib Promy">

	    <meta name="keywords"	 content="Admin, Bootstrap 3, Template, Theme, Responsive">

	    <!-- LOAD HEADER FILES -->

	    <?php $this->load->view("layouts/includes/admin/header"); ?>

	</head>



	<body class="skin-black">

		<div class="loading-parent" style="display: none;"><div class="loading"></div></div>

		<header class="header">

		    <a href="<?= ADMIN_URL ?>" class="logo"> <?=SITE_NAME?> </a>

		    <!-- Header Navbar: style can be found in header.less -->

		    <nav class="navbar navbar-static-top" role="navigation">

		        <!-- Sidebar toggle button-->

		        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">

		            <span class="sr-only">Toggle navigation</span>

		            <span class="icon-bar"></span>

		            <span class="icon-bar"></span>

		            <span class="icon-bar"></span>

		        </a>

		        <div class="navbar-right">

		            <ul class="nav navbar-nav">



		                <!-- User Account: style can be found in dropdown.less -->

		                <li class="dropdown user user-menu">

		                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">

		                        <i class="fa fa-user"></i>

		                        <span> <?= @$adminCred['data']['name'] ?> <i class="caret"></i></span>

		                    </a>

		                    <ul class="dropdown-menu dropdown-custom dropdown-menu-right">

		                        <li> <a href="<?= BASE_URL."admincp/admin-logout" ?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a> </li>

		                        <li class="divider"></li>

		                        <li> <a href="<?= BASE_URL."admincp/site-settings" ?>"><i class="fa fa-cogs fa-fw"></i> Settings</a> </li>

		                    </ul>

		                </li>

		            </ul>

		        </div>

		    </nav>

		</header>		

		<div class="wrapper row-offcanvas row-offcanvas-left">

			<?php $this->load->view("layouts/includes/admin/left_menu"); ?>

			<section class="content">				

				<aside class="right-side">

					<header class="header-section">

						<div class="breadcrumbs">

							<ul class="breadcrumb">						

							  	<li><a href="<?=ADMIN_URL?>"><i class="fa fa-home"></i> Home</a></li>

							  	<?php 

			            			if(!empty($breadcrumb)):

			            				foreach($breadcrumb AS $eachBread): 

					            			if(isset($eachBread['page']) && $eachBread['page'] != ""):?>

							  					<li><?php 

								                	if(isset($eachBread['url']) && $eachBread['url'] != ""): ?>

								                    	<a href="<?=$eachBread['url']?>"><?=ucwords($eachBread['page'])?></a><?php 

								                   	else:

								                   		echo ucwords($eachBread['page']);

								                   	endif;?>



								                </li><?php 

								            endif;

						                endforeach;?>

						            </ul><?php 

						        endif;

						    ?>

							</ul>

						</div>

					</header>



					<div class="admin-body">

						<?=$content?>						

					</div>					

					

				</aside>

			</section>

			<div class="footer-main">

                Copyright &copy <?=SITE_NAME?>, <?=date("Y")?>

            </div>

		</div>

		<?=get_flash_message()?>

		

		<!-- R-Crop Modal-->

		<div class="modal fade" id="myModalRCrop" role="dialog">

		    <div class="modal-dialog">

		        <div class="modal-content">

		            <div class="modal-body">

		                <div class="row">

		                    <main class="page">

							    <div class="box-2">

							        <div class="result">

							        	<img src="<?=timthumb(DEFAULT_IMAGE, 300)?>">

							        </div>

							    </div>

								<!-- input file -->

								<div class="box" style="display: none">

									<div class="options hide">

										<label> Width</label>

										<input type="number" class="img-w" value="600" min="100" max="1200" />

									</div>

								</div>



							    <div class="col-md-12" style="margin-top: 15px; text-align: center;">

		                            <button class="btn save hide upload-result btn-success"><i class="fa fa-crop"></i> Crop</button>

		                            <button class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i> cancel</button>

		                        </div>

							</main>

		                </div>

		            </div>

		        </div>



		    </div>

		</div>

		<div class="loader-text">Loading...</div>
		<div class="loader"></div>
		<div class="loader-overlay"></div>

		<!-- R-Crop Script-->

		<script type="text/javascript">

		    var add_photo_src = "<?=timthumb(DEFAULT_IMAGE, 300)?>";


		    var showHideLoader = function (mode = 'show', loaderText = 'Loading...') {
		    	if(mode === 'show') {
		    		$('.loader-text').text(loaderText);
		    		$('.loader').show();
			    	$('.loader-text').show();
			    	$('.loader-overlay').show();
		    	} else {
		    		$('.loader').hide();
			    	$('.loader-text').hide();
			    	$('.loader-overlay').hide();
		    	}    	
		    };
		    $(document).ready(function(){

				let result 		= document.querySelector('.result'),

				    img_result 	= document.querySelector('.img-result'),

				    img_w 		= $(".crop-config").data("width"),

				    img_h 		= $(".crop-config").data("height"),

				    ratio 		= $(".crop-config").data("ratio"),

				    options 	= document.querySelector('.options'),

				    save 		= document.querySelector('.save'),

				    dwn 		= document.querySelector('.download'),

				    upload 		= document.querySelector('#file-input'),

				    cropper 	= '';



				// on change show image with crop options
				if(upload != null)
					upload.addEventListener('change', (e) => {

					    if (e.target.files.length) {

					        // start file reader

					        const reader = new FileReader();

					        reader.onload = (e) => {

					            if (e.target.result) {

			                		$("#myModalRCrop").modal({

			                			backdrop: 'static',

			    						keyboard: false

			                		});

			                		$("#overlay").remove();



					                // create new image

					                let img = document.createElement('img');

					                img.id = 'image';

					                img.src = e.target.result

					                // clean result before

					                result.innerHTML = '';

					                // append new image

					                result.appendChild(img);

					                // show save btn and options

					                save.classList.remove('hide');

					                options.classList.remove('hide');

					                cropper = new Cropper(img, {

					                	aspectRatio 		: ratio,

										dragCrop			: true,

										zoomable 			: true,

										viewMode 			: 2,

										minCropBoxWidth		: 200,

										minCropBoxHeight	: 200,

										background			: false,

										minContainerHeight	: 400,

										minContainerWidth	: 580,

										minCanvasHeight		: 400,

										minCanvasWidth		: 580,

					                });

					            }

					        };

					        reader.readAsDataURL(e.target.files[0]);

					    }

					});



				// save on click

				save.addEventListener('click', (e) => {

				    e.preventDefault();

				    // get result to data uri

				    let imgSrc = cropper.getCroppedCanvas({

				        width: img_w,

				        height: img_h,

				    }).toDataURL();



		            $("#place-add-image").attr('src', imgSrc);

		            $("#image_code").val(imgSrc);

		            $("#myModalRCrop").modal('toggle');

		            $(".btn-corpper-delete").show();

				});



		        $(".btn-corpper-delete").click(function(){

		            if(confirm('Are you sure want to delete?')){

		            	var unique 	= $(this).data("unique");

		            	var imgId 	= $(this).data("imageid");

		            	var response= 1;

			            

			            if(imgId != ""){

			            	$.post("<?=ADMIN_URL.'ajax/delete_image'?>/"+imgId, function(data){

			            		if(data == 1){			            			

						            $(".btn-corpper-delete").hide();

						            $("#place-add-image").attr('src', add_photo_src);

						            $("#image_code").val();	

						            $("#delete-"+unique).val(1);  

			            		}

			            	});

			            }else{

				            $(".btn-corpper-delete").hide();

				            $("#place-add-image").attr('src', add_photo_src);

				            $("#image_code").val();	

				            $("#delete-"+unique).val(1);  

					    }            

		            }

		        });

		    });

		</script>

	</body>

</html>