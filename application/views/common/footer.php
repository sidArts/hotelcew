

                <div class="footer-main">

                    Copyright &copy Rajib Gandhi Board of Higher Education, 2016

                </div>

            </aside><!-- /.right-side -->



        </div><!-- ./wrapper -->





        <!-- jQuery 2.0.2 -->

        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>

        <script type="text/javascript" src="<?=ADMIN_JS?>jquery.min.js"></script>



        <!-- jQuery UI 1.10.3 -->

        <script type="text/javascript" src="<?=ADMIN_JS?>jquery-ui-1.10.3.min.js"></script>

        <!-- Bootstrap -->

        <script type="text/javascript" src="<?=ADMIN_JS?>bootstrap.min.js"></script>

        <!-- daterangepicker -->

        <script type="text/javascript" src="<?=ADMIN_JS?>plugins/daterangepicker/daterangepicker.js"></script>



        <script type="text/javascript" src="<?=ADMIN_JS?>plugins/chart.js"></script>

        

        <!-- CkEditor -->

		<script type="text/javascript" src="<?=ADMIN_JS?>/ckeditor/ckeditor.js"></script>

		<link   type="text/css"        href="<?=ADMIN_JS?>/ckeditor/sample.css" rel="stylesheet" media="screen" />



        <!-- datepicker

        <script src="<?=ADMIN_JS?>plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>-->

        <!-- Bootstrap WYSIHTML5

        <script src="<?=ADMIN_JS?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>-->

        <!-- iCheck -->

        <script type="text/javascript" src="<?=ADMIN_JS?>plugins/iCheck/icheck.min.js"></script>

        <!-- calendar -->

        <script type="text/javascript" src="<?=ADMIN_JS?>plugins/fullcalendar/fullcalendar.js"></script>



        <!-- Director App -->

        <script type="text/javascript" src="<?=ADMIN_JS?>Director/app.js"></script>



        <!-- Director dashboard demo (This is only for demo purposes) -->

        <script type="text/javascript" src="<?=ADMIN_JS?>Director/dashboard.js"></script>



        <!-- Director for demo purposes -->

        <script type="text/javascript">

            $('input').on('ifChecked', function(event) {

                $(this).parents('li').addClass("task-done");

                console.log('ok');

            });

            $('input').on('ifUnchecked', function(event) {

                $(this).parents('li').removeClass("task-done");

                console.log('not');

            });



        </script>

        <script>

            $('#noti-box').slimScroll({

                height: '400px',

                size: '5px',

                BorderRadius: '5px'

            });



            $('input[type="checkbox"].flat-grey, input[type="radio"].flat-grey').iCheck({

                checkboxClass: 'icheckbox_flat-grey',

                radioClass: 'iradio_flat-grey'

            });

    </script>

</body>

</html>