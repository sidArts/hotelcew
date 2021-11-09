<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>



<style>



    #dash_red



    {



        background: rgb(255,92,92);



        background: linear-gradient(90deg, rgba(255,92,92,1) 0%, rgba(255,92,92,1) 35%, rgba(255,184,0,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



        box-shadow:0 1px 4px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 0, 0, 0.1) inset;



    }







    #dash_blue



    {



      background: rgb(17,44,45);



background: linear-gradient(90deg, rgba(17,44,45,1) 0%, rgba(0,100,152,1) 35%, rgba(0,105,238,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_pink



    {



      background: rgb(183,0,238);



background: linear-gradient(90deg, rgba(183,0,238,1) 0%, rgba(183,0,238,1) 35%, rgba(106,37,255,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_orange



    {



      background: rgb(156,42,0);



background: linear-gradient(90deg, rgba(156,42,0,1) 0%, rgba(255,69,0,1) 35%, rgba(255,158,0,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_green



    {



      background: rgb(10,75,0);



background: linear-gradient(90deg, rgba(10,75,0,1) 0%, rgba(75,68,0,1) 35%, rgba(181,191,0,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_green2



    {



      background: rgb(3,62,0);



background: linear-gradient(90deg, rgba(3,62,0,1) 0%, rgba(0,97,23,1) 35%, rgba(0,142,34,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_lightgreen



    {



      background: rgb(24,195,0);



background: linear-gradient(90deg, rgba(24,195,0,1) 0%, rgba(125,195,0,1) 35%, rgba(187,195,0,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_yellow



    {



      background: rgb(154,99,12);



background: linear-gradient(90deg, rgba(154,99,12,1) 0%, rgba(251,181,13,1) 35%, rgba(191,213,9,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    #dash_black



    {



      background: rgb(62,70,79);



background: linear-gradient(90deg, rgba(62,70,79,1) 0%, rgba(13,31,50,1) 35%, rgba(0,0,0,1) 100%);



        padding: 10px;



        border-radius: 5px;



        margin-left: 10px;



        margin-top: 10px;



    }







    .text-xs



    {



        padding: 10px 10px 10px 20px;



        color: white;



        font-size: 15px;



        font-weight: 700;



    }







    .text-sm



    {



        padding: 0px 0px 0px 20px;



        color: white;



        font-size: 20px;



        font-weight: 700;



    }



</style>







<div class="col-xl-3 col-md-2 mb-4" id="dash_red">



              <div class="card border-left-primary shadow h-100 py-2">



                <div class="card-body">



                  <div class="row no-gutters align-items-center">



                    <div class="col mr-2">



                      <div class="text-xs">Today's Booking</div>



                      <div class="text-sm"><?= $today_booking;?></div>



                    </div>



                  </div>



                </div>



              </div>



            </div>









<!-- 

            <div class="col-xl-3 col-md-2 mb-4" id="dash_blue">



              <div class="card border-left-primary shadow h-100 py-2">



                <div class="card-body">



                  <div class="row no-gutters align-items-center">



                    <div class="col mr-2">



                      <div class="text-xs">Total User</div>



                      <div class="text-sm"><?php echo $user; ?></div>



                    </div>



                  </div>



                </div>



              </div>



            </div> -->







            <div class="col-xl-3 col-md-2 mb-4" id="dash_pink">



              <div class="card border-left-primary shadow h-100 py-2">



                <div class="card-body">



                  <div class="row no-gutters align-items-center">



                    <div class="col mr-2">



                      <div class="text-xs">Total Bookings</div>



                      <div class="text-sm"><?= $total_booking;?></div>



                    </div>



                  </div>



                </div>



              </div>



            </div>











            <!-- <div class="col-xl-3 col-md-2 mb-4" id="dash_green2">



              <div class="card border-left-primary shadow h-100 py-2">



                <div class="card-body">



                  <div class="row no-gutters align-items-center">



                    <div class="col mr-2">



                      <div class="text-xs">Today's Earnings</div>



                      <div class="text-sm"><?= $today_earning?></div>



                    </div>



                  </div>



                </div>



              </div>



            </div> -->









