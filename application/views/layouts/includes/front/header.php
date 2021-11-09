    <header>
        <div class="top_header 1">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4 col-lg-4 top_left">
                        <p>Get free shipping – Free 30 day money back guarantee</p>
                    </div>
                    <div class="col-md-6 col-lg-6 top_mid">
                        <ul>
                            <li><a href="javascript:void(0)">Track Your Order</a></li>
                            <li><a href="javascript:void(0)">Hotline: (012) 800 456 789</a></li>
                            <li><a href="javascript:void(0)">Quality Guarantee Of Products</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2 col-lg-2 top_right">
                        <ul>
                            <?php
                                if($this->session->has_userdata(CUSTOMER_SESS)):?>
                                    <li><a href="<?=BASE_URL.'auth/logout'?>">Logout</a></li>
                                    <li><a href="<?=BASE_URL.'my-account'?>" class="reg_btn">My Account</a></li><?php
                                else: ?>
                                    <li><a href="<?=BASE_URL.'auth/login'?>">Login</a></li>
                                    <li><a href="<?=BASE_URL.'auth/sign-up'?>" class="reg_btn">Register</a></li><?php 
                                endif;
                            ?>                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand" href="<?=BASE_URL?>"><img src="<?=FRONT_ASSETS?>images/logo.png" alt="" /></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarsExample02">
                    <ul class="navbar-nav ml-auto float-md-right">
                        <li class="nav-item">
                            <a class="nav-link" href="<?=BASE_URL?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=BASE_URL?>">About us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?=BASE_URL?>">Contact us</a>
                        </li>
                    </ul>
                    <div class="ml-auto float-md-right menu_right">
                        <div class="top_fev">
                            <a class="fav_link" href="javascript:void(0)"><img src="<?=FRONT_ASSETS?>images/fav.png" alt="" /> <span id="wishlist_incr_id"><?php echo $wishlist; ?></span></a>
                        </div>
                        <div class="top_fev">
                            <a class="fav_link" href="<?php echo base_url(); ?>cart"><img src="<?=FRONT_ASSETS?>images/cart.png" alt="" /> <span id="cart_incr_id"><?php echo $cartCount; ?></span></a>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="row">
                <div class="col-sm-4">
                    <div class="cat_brow">
                        <div class="navbar navbar-expand-sm">
                            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                                <ul class="navbar-nav">
                                    <li class="nav-item dropdown dmenu">
                                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbardrop" data-toggle="dropdown">
                                            BROWSE BY CATEGORY &nbsp; &nbsp; &nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i>
                                        </a>
                                        <div class="dropdown-menu sm-menu">
                                            <ul>
                                            <?php   
                                                if(count($categories)>0)
                                                {
                                                    foreach($categories as $category)
                                                    {
                                                        ?>
                                                            <li>
                                                                <?php /* ?><img src="<?= base_url()."public/uploads/".$category['image']; ?>" alt="Bedroom"> <?php */ ?>
                                                                <span><a href="<?php echo base_url(); ?>category/<?php echo $category['slug'] ?>/"><?php echo $category['title'] ?></a></span>
                                                            </li>
                                                        <?php
                                                    }
                                                }
                                            ?>      
                                                
                                                <?php /* ?><li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_seating.png" alt="Seating">
                                                    <span><a href="javascript:void(0)">Seating</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_Living-Room.png" alt="Living Room">
                                                    <span><a href="javascript:void(0)">Living Room</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_desking.png" alt="Seating">
                                                    <span><a href="javascript:void(0)">Desking</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_study-room.png" alt="Study Room">
                                                    <span><a href="javascript:void(0)">Study Room</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_modular.png" alt="Modular">
                                                    <span><a href="javascript:void(0)">Modular</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_dining-room.png" alt="Home Accessories">
                                                    <span><a href="javascript:void(0)">Home Accessories</a></span>
                                                </li>
                                                <li>
                                                    <img src="<?=FRONT_ASSETS?>images/dd_storage.png" alt="Dining Room">
                                                    <span><a href="javascript:void(0)">Dining Room</a></span>
                                                </li>

                                                <?php */ ?>
                                            </ul>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="search_box">
                        <form action="<?=BASE_URL.'products'?>">
                            <input type="text" placeholder="Search Product" name="q" value="<?=@$_GET['q']?>">
                            <button type="submit">Search <img src="<?=FRONT_ASSETS?>images/search.png" alt="" /></button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </header>
                    

                    

                