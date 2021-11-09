<?php

/**

 * The template for displaying the header

 *

 * Displays all of the head element and everything up until the "site-content" div.

 *

 * @package WordPress

 * @subpackage Twenty_Sixteen

 * @since Twenty Sixteen 1.0

 */



?><!DOCTYPE html>

<html class="no-js">

<head>

	<meta charset="">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="profile" href="http://gmpg.org/xfn/11">

    <link rel="shortcut icon" type="images/x-icon" href=""/>

    <title>Hotel Aviana</title>

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/font-awesome.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/owl.carousel.min.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/owl.theme.default.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/slick.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/bd_style.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/ekko-lightbox.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/fullcalendar.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url()?>public/front/assets/css/responsive.css">

    <script type="text/javascript" src="<?= base_url()?>public/front/assets/js/jquery-3.4.1.min.js"></script>

</head>



<body>



	<header class="bd_header">

		<div class="container">

			<nav class="navbar navbar-expand-lg navbar-light">

			  	<a href="<?= base_url()?>" class="bd_logo">

							<img src="<?= base_url()?>public/uploads/logo.png" alt="" title=""/>

						</a>

			  <div class="collapse navbar-collapse mob_nav" id="navbarSupportedContent">

			  	<ul id="menu-header-menu" class="ml-auto bd_menu navbar-nav">

			  	<li id="menu-item-22" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-22"><a href="<?= base_url();?>">Home</a></li>

			  	<!-- <li id="menu-item-23" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-23"><a href="<?= base_url('intimate-hotel-experience')?>">Intimate hotel experience</a></li> -->

				

				<li id="menu-item-86" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-86"><a href="<?= base_url('rates')?>">Rates</a></li>

				<li id="menu-item-21" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-21"><a href="<?= base_url('gallery')?>">Gallery</a></li>

				<li id="menu-item-20" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-20"><a href="<?= base_url('contact-us')?>">Contact Us</a></li>

				</ul>

			  </div>

			  <div class="head_rgt">

			  	<a href="<?= base_url('rates')?>" class="bd_btn">Book now</a>

			  </div>

			  <div class="head_rgt">
			  	<?php $site_details = $this->Custom->get_site_details(); ?>
			  	Call: <?= $site_details[0]['admin_phone']?><br>

			  	Email:<?= $site_details[0]['admin_email']?>




			  </div>

			  <button class="navbar-toggler bd_toggle" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

			    <span class="navbar-toggler-icon"></span>

			  </button>

			</nav>

		</div>

	</header>



	<div class="load_se" id="loader" style="display: none;">

	<div class="load" ><img src="">

		<div class="loader_line_mask">

			<div class="loding_line"></div>

		</div>

	</div>

	</div>



	

	