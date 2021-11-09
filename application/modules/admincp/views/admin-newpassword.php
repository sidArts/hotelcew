<div class="login-panel">
    <form enctype="multipart/Form-data" id="loginform" method="post" accept-charset="utf-8">
        <div class="form-group">
            <input type="text" class="form-control" name="email_address" placeholder="Enter Email" maxlength="128" required="required" />
        </div>

        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Enter Password" maxlength="128" required="required" />
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block login-submit-btn">
                <i class="fa fa-spinner fa-spin spin-loader" style="display: none;"></i> Login
            </button>
        </div>
    </form>
</div>

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

                $.post("<?=BASE_URL.'admincp/admin-login'?>", $(form).serialize(), function(data){
                    var obj = JSON.parse(data);

                    $(".return_message").html(obj.msg);
                    $(".return_message").addClass("status-"+obj.status);

                    if(obj.status == 1){
                        $(".return_message").removeClass("status-0")
                        $(".return_message").html(obj.msg);
                        $(".admin_login_lock").removeClass("fa-lock");
                        $(".admin_login_lock").addClass("fa-unlock");
                        setTimeout(function(){
                            location.replace("<?= base_url()?>");
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