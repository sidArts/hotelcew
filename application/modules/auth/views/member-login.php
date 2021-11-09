<section class="logRes loginArea">

    <div class="container">

        <div class="row">

            <div class="col-sm-6" data-label="or">

                <h2>Already registered? </h2>

                <div class="logInpnl">

                    <span class="return_message"></span>

                    <form id="loginform" method="POST">

                        <div class="frmCol">

                            <span class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>

                            <input type="email" name="email_address" class="form-control" id="email_address" placeholder="Email address">

                        </div>

                        <div class="frmCol">

                            <span class="icon"><i class="fa fa-key" aria-hidden="true"></i></span>

                            <input type="password" name="password" class="form-control" id="password" placeholder="Password">

                        </div>

                        <div class="frmCol">

                            <a href="<?=BASE_URL.'auth/forgotten-password'?>">Forgotten Password?</a>

                        </div>

                        <div class="frmCol">

                            <button type="submit" class="btn btn-default btnFrm"><i class="fa fa-spinner fa-spin spin-loader" style="display: none;"> &nbsp;</i>Submit</button>

                        </div>

                    </form>

                </div>

            </div>

            <div class="col-sm-6">

                <h3>Also you can</h3>

                <ul>

                    <li class="fb">

                        <a target="_blank" href="#">

                            <span class="icon"><i class="fa fa-facebook" aria-hidden="true"></i></span> Continue With Facebook

                        </a>

                    </li>

                    <li class="ggl">

                        <a target="_blank" href="#">

                            <span class="icon"><i class="fa fa-google" aria-hidden="true"></i></span> Continue With Google

                        </a>

                    </li>

                    <li>Don't have an account <a href="<?=BASE_URL.'member/sign-up'?>">SIGN UP</a></li>

                </ul>

            </div>

        </div>

    </div>

</section>

<script>

    $(document).ready(function(){

        $("#loginform").validate({

            rules : {

                email_address : {

                    required : true,

                    email : true

                }, 

                password : {

                    required : true,

                    minlength : 6

                }

            }, 

            messages : {

                email_address : "Enter a valid email address", 

                password : {

                    required : "Password should not be blank", 

                    minlength : "Password should be minimum 6 character"

                }

            },

            submitHandler: function(form) {

                $(this).attr("disabled", true);

                $(".spin-loader").show();



                $.post("<?=BASE_URL.'auth/member_login'?>", $(form).serialize(), function(data){

                    var obj = JSON.parse(data);



                    $(".return_message").html(obj.msg);

                    $(".return_message").addClass("status-"+obj.status);



                    if(obj.status == 1){

                        $(".return_message").removeClass("status-0")

                        $(".return_message").html(obj.msg);

                        setTimeout(function(){

                            location.replace("<?=BASE_URL.'my-account'?>");

                        }, 2000);

                    }else{

                        $(".return_message").removeClass("status-1")

                        $(".login-submit-btn").attr("disabled", false);

                        $(".spin-loader").hide();

                    }

                });

            }

        });

    })

</script>