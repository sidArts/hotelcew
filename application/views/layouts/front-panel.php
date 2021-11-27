<!doctype html>

<html lang="en">



<head>

    <!-- load CSS and bootstrap and jquery -->
    
    
    <?php include("includes/front/head.php"); ?>



    <title><?=$site_title?>1</title>

</head>



<body>

    <!-- Your Header Goes here -->

    <div class="wrapper">

        <!-- Load header menu and common parts-->

        <?php include("includes/front/header.php"); ?>



        <!-- Your Page Templete here -->

        <main>            

            <div class="home_banner">

                <!-- Your Banner Goes Here -->

                <div class="container">

                    <div id="owl-banner" class="owl-carousel owl-theme">

                        <div class="item"><img src="<?=FRONT_ASSETS?>images/banner-1.jpg" alt="Banner"></div>

                        <div class="item"><img src="<?=FRONT_ASSETS?>images/banner-2.jpg" alt="Banner"></div>

                    </div>

                </div>

            </div>

            

            <?=$content?>  

        </main>      



        <!-- Footer -->

        <?php include("includes/front/footer.php") ?>

    </div>



    <!-- jQuery first, then Bootstrap JS -->

    

    <!-- Custom JS Include Here -->

    <script src="<?=FRONT_ASSETS?>js/owl.carousel.min.js"></script>

    <!-- Custom Hand Written Js -->

    <script src="<?=FRONT_ASSETS?>js/main.js"></script>
    
</body>



</html>