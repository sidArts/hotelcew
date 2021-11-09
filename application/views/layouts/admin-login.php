<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?=@$site_title?></title>
    <link rel="shortcut icon" type="image/png" href="#">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Developed By M Abdur Rokib Promy">
    <meta name="keywords" content="Admin, Bootstrap 3, Template, Theme, Responsive">
    <!-- bootstrap 3.0.2 -->
    <link href="<?=ADMIN_CSS?>bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="<?=ADMIN_CSS?>font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Lato' />
    
    <!-- Theme style -->
    <link href="<?=ADMIN_CSS?>admin.css" rel="stylesheet" type="text/css" />
    <link href="<?=ADMIN_CSS?>styles.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script type="text/javascript" src="<?=ADMIN_JS?>bootstrap.min.js"></script>
    <script type="text/javascript" src="<?=ADMIN_PLUGINS?>jquery.validate.min.js"></script>

</head>

<body class="skin-black">

    <div class="wrapper row-offcanvas row-offcanvas-left">
        <aside class="right-side right_side">
            <!-- Main content -->
            <div class="row">
                <div class="login_box">
                    <section class="panel">
                        <div class="panel-body">
	                        <header class="panel-header">
	                            <center>  
	                            	<i class="fa fa-lock admin_login_lock"></i>                             	                              	
	                                <h2><?=SITE_NAME?></h2>
	                                <span class="return_message"></span>
	                            </center>
	                        </header>
                            <?=$content?>
                        </div>
                    </section>
                </div>
            </div>
            <div class="footer-main">
                Copyright &copy <?=SITE_NAME?>, <?=date("Y")?>
            </div>
        </aside>
    </div>
    </body>

</html>