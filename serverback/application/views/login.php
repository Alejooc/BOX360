<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=base_url('/assets/plugins/images/favicon.png')?>">
    <title><?=get_nombre_portal()?> - Login</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?=base_url('/assets/bootstrap/dist/css/bootstrap.min.css')?>" rel="stylesheet">
    <link href="<?=base_url('/assets/plugins/bower_components/bootstrap-extension/css/bootstrap-extension.css')?>" rel="stylesheet">
    <!-- animation CSS -->
    <link href="<?=base_url('/assets/css/animate.css')?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?=base_url('/assets/css/style.css')?>" rel="stylesheet">
    <!-- color CSS -->
    <link href="<?=base_url('/assets/css/colors/blue.css')?>" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
    <script src="http://www.w3schools.com/lib/w3data.js"></script>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box">
				<?= my_validation_errors(validation_errors()); ?>
				<?if(!empty($error)){ echo my_validation_errors($error);}?>
                <form class="form-horizontal form-material" id="loginform" method="post" action="<?=site_url("login/access")?>">
                    <h3 class="box-title m-b-20">Sign In</h3>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input name="email" class="form-control" type="text" required="" placeholder="Username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input name="pass" class="form-control" type="password" required="" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary pull-left p-t-0">
                                <input id="checkbox-signup" type="checkbox">
                                <label for="checkbox-signup"> Remember me </label>
                            </div>
                            <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a> </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                            <div class="social">
                                <a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip" title="Login with Facebook"> <i aria-hidden="true" class="fa fa-facebook"></i> </a>
                                <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="Login with Google"> <i aria-hidden="true" class="fa fa-google-plus"></i> </a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Don't have an account? <a href="register.html" class="text-primary m-l-5"><b>Sign Up</b></a></p>
                        </div>
                    </div>
                </form>
                <form class="form-horizontal" id="recoverform" action="index.html">
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <h3>Recover Password</h3>
                            <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- jQuery -->
    <script src="<?=base_url('/assets/plugins/bower_components/jquery/dist/jquery.min.js')?>"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?=base_url('/assets/bootstrap/dist/js/tether.min.js')?>"></script>
    <script src="<?=base_url('/assets/bootstrap/dist/js/bootstrap.min.js')?>"></script>
    <script src="<?=base_url('/assets/plugins/bower_components/bootstrap-extension/js/bootstrap-extension.min.js')?>"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="<?=base_url('/assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js')?>"></script>
    <!--slimscroll JavaScript -->
    <script src="<?=base_url('/assets/js/jquery.slimscroll.js')?>"></script>
    <!--Wave Effects -->
    <script src="<?=base_url('/assets/js/waves.js')?>"></script>
    <!-- Custom Theme JavaScript -->
    <script src="<?=base_url('/assets/js/custom.min.js')?>"></script>
    <!--Style Switcher -->
    <script src="<?=base_url('/assets/plugins/bower_components/styleswitcher/jQuery.style.switcher.js')?>"></script>
</body>

</html>
