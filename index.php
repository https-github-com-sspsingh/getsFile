<!DOCTYPE html>
<html lang="en">

<?PHP
    include 'includes.php';
    $login->checkLogin();
    $login->Form = 'login';
    $login->Signin();
    $login->Login_Redi(); 

    /*$getID = $login->RunIndexPageThoughts();    
    $TH_Array  = $login->select('thoughts',array("*"), " WHERE ID = ".$getID." ");
    $Thoughts = str_replace('&nbsp;', ' ', (preg_replace('/&nbsp;/i', ' ', str_replace("’", ",", (str_replace("’", "/", ($TH_Array[0]['title'])))))));
    $return_title = str_replace('/', "'", $Thoughts);*/
?>
<html lang="en">
<head>
<title><?=$login->SITE_TITLE?></title>
<!-- Meta tag Keywords -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Meta tag Keywords -->
<!-- css files -->
<link rel="stylesheet" href="<?=$login->home?>assets/index/font-awesome.css"> <!-- Font-Awesome-Icons-CSS -->
<link rel="stylesheet" href="<?=$login->home?>assets/index/style.css" type="text/css" media="all" /> <!-- Style-CSS --> 
<!-- //css files -->
<!-- web-fonts -->
<link href="http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700" rel="stylesheet">
<link href="http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700" rel="stylesheet">
<!-- //web-fonts -->
</head>
<body>
    <!--header-->
    <div class="header-w3l"><h1 style="font-size:29px !important;font-weight: bold;">&nbsp;</h1></div>
    <!--//header-->
    <!--main-->
    <div class="main-w3layouts-agileinfo">
       <!--form-stars-here-->
            <div class="wthree-form">
                <!--<h2>Fill out the form below to login</h2>-->
                <form action="<?=$login->home?>" method="post">

        <?PHP
            $m = isset($_GET['m']) ? urldecode($login->Decrypt($_GET['m'])) : '';
            $u = isset($_GET['u']) ? urldecode($login->Decrypt($_GET['u'])) : '';

            if(!empty($m))  {echo '<br /><b style="font-size: 15px; color:red;">'.$m.'</b><br /><br /><br />';}
        ?>
                    
					<div class="form-sub-w3" style="width: 210px;">
						<!--<img src="assets/index/images/image001.jpg" align="middle" style="margin-top: -24px;height: 75px;margin-left: 65px;" />-->
						<img src="img/ts.png" align="middle" style="margin-top: -24px;height: 75px;" />
					</div>
						
                        <div class="form-sub-w3" style="width: 180px;">
                            <input type="text" name="username" value="" style="height:10px;" placeholder="Username " required="" />
                            <!--<div class="icon-w3"><i class="fa fa-user" aria-hidden="true"></i></div>-->
                        </div>

                        <div class="form-sub-w3" style="width: 180px;">
                            <input type="password" name="password" style="height:10px;" placeholder="Password" required="" />
                            <!--<div class="icon-w3"><i class="fa fa-user" aria-hidden="true"></i></div>-->
                        </div>

                        <!--<label class="anim"><span>&nbsp;</span><a href="forget.php">Forgot Password</a></label> -->
						
                        <div class="clear"></div>
                        <div class="submit-agileits" style="margin-top:5px; text-align:left; width:234px;"><input type="submit" name="login" value="Login" style="width:234px;"></div>
                </form>
            </div>
                    <!--//form-ends-here-->
    </div> 
</body>
</html>