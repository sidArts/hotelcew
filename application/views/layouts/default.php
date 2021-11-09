<!doctype html>
<html lang="en">

<head>
    <!-- load CSS and bootstrap and jquery -->
    <?php include("includes/front/head.php"); ?>

    <title><?=$site_title?></title>
</head>

<body>
    <!-- Your Header Goes here -->
    <div class="wrapper">
        <!-- Load header menu and common parts-->
        <?php include("includes/front/header.php"); ?>

        <!-- Your Page Templete here -->
        <main class="inner-page">            
            <section class="inrBnr">
                <div class="bnrImg">
                    <img src="<?=FRONT_ASSETS?>images/inrBnr.jpg" alt="">
                </div>
                <div class="breadCm">
                    <?php 
		                if(!empty($breadcrumb)):?>
                            <div class="container">
                                <ul>
                                    <li><a href="<?=BASE_URL?>">Home</a></li>
                                    <?php
                                        foreach($breadcrumb AS $eachBread):
                                            if(isset($eachBread['page']) && $eachBread['page'] != ""):
                                                if(isset($eachBread['url']) && $eachBread['url'] != ""): ?>
                                                    <li>
                                                        <a href="<?=$eachBread['url']?>">
                                                            <?=ucwords($eachBread['page'])?>
                                                        </a>
                                                    </li><?php
                                                else:
                                                    echo "<li class='current'>".ucwords($eachBread['page'])."</li>";
                                                endif;
                                            endif;
                                        endforeach;
                                    ?>
                                </ul>
                            </div> <?php 
                        endif;
                    ?>
                </div>
            </section>
            <section class="inner-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2><?=@$page_title?></h2>
                        </div>

                        <div class="col-sm-12">
                            <?=$content?>
                        </div>
                    </div>
                </div>
            </section>  
        </main>      

        <!-- Footer -->
        <?php include("includes/front/footer.php") ?>
    </div>
    <!-- Custom Hand Written Js -->
    <script src="<?=FRONT_ASSETS?>js/main.js"></script>
</body>

</html>