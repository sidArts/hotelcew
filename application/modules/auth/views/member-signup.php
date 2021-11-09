<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<section class="logRes signUpAreaWrap">
    <div class="container">
        <h2>Create An Account</h2>
        <div class="signUpArea">
            <div class="row">
                <div class="col-sm-12">
                    <div class="logInpnl">
                        <span class="return_message"></span>
                        <form id="signupFrm" action="" method="post">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                                        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="First Name">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-user-o" aria-hidden="true"></i></span>
                                        <input type="text" name="last_name" class="form-control" id="tel" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Email">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-mobile" aria-hidden="true"></i></span>
                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="Mobile No.">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-key" aria-hidden="true"></i></span>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="frmCol">
                                        <span class="icon"><i class="fa fa-key" aria-hidden="true"></i></span>
                                        <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>
                            <div class="frmCol">
                                <button type="submit" class="btn btn-default">Continue</button>
                            </div>
                            <div class="frmCol">
                                <p>Alredy have an account? <a href="<?=BASE_URL.'auth/login'?>">LOGIN</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function(){
        $("#signupFrm").validate({
            rules : {
                first_name : {
                    required : true
                }, 
                last_name : {
                    required : true
                }, 
                email : {
                    required : true,
                    email : true, 
                    remote:"<?=BASE_URL.'auth/check_if_exist'?>"
                }, 
                phone : {
                    required : true,
                    number : true, 
                    remote:"<?=BASE_URL.'auth/check_if_exist'?>"
                }, 
                password : {
                    required : true,
                    minlength : 6
                }, 
                confirm_password : {
                    required : true,
                    minlength : 6,
                    equalTo:"#password"
                }
            }, 
            messages : {
                email_address : "Enter a valid email address", 
                password : {
                    required : "Password should not be blank", 
                    minlength : "Password should be minimum 6 character"
                },
                email:{
                	remote : "Email already exist"
                },
                phone:{
                	remote : "Mobile no. already exist"
                }
            },
            submitHandler: function(form) {
                $(this).attr("disabled", true);
                $(".spin-loader").show();

                $.post("<?=BASE_URL.'auth/member_save'?>", $(form).serialize(), function(data){
                    var obj = JSON.parse(data);

                    $(".return_message").html(obj.msg);
                    $(".return_message").addClass("status-"+obj.status);

                    if(obj.status == 1){
                        $(".return_message").removeClass("status-0")
                        $(".return_message").html(obj.msg);
                        setTimeout(function(){
                            location.replace("<?=BASE_URL.'member/login'?>");
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