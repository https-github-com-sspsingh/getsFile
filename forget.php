<?PHP
    include 'includes.php';
    $login->checkLogin();
    $login->Form = 'login';
    $login->Signin();
    $login->Login_Redi(); 

    $getID = $login->RunIndexPageThoughts();
    
    $TH_Array  = $login->select('thoughts',array("*"), " WHERE ID = ".$getID." ");
    
    $fileID_0  = str_replace("’", "/", ($TH_Array[0]['title']));
    $fileID_1  = str_replace("’", ",", ($fileID_0));
    $fileID_2  = str_replace("–", ' " <b>', ($fileID_1));
    $fileID_3 = preg_replace('/&nbsp;/i', ' ', $fileID_1);	
    $return_titles = str_replace('&nbsp;', ' ', $fileID_3);        
    $return_title = str_replace('/', "'", $return_titles);
    
    if(isset($_REQUEST['Submit']))
    {
        //echo '<pre>';   echo print_r($_POST);
        
        extract($_POST);
        
        if($username <> '')
        {
            extract($_POST);
            
            $rowCount = 0;
            $rowCount = ($username <> '' ? $login->select('users',array("*")," WHERE username = '".$username."' ") : '');
            
            if($rowCount[0]['ID'] > 0 && $rowCount[0]['email'] <> '')
            {
                $passwordID = substr($rowCount[0]['first_name'],0).''.rand(1020887,7845004);
                
                $update = array();
                $update['pstexts'] = $passwordID;
                $update['password'] = md5($update['pstexts']);
                $ons['ID'] = $rowCount[0]['ID'];
                /*echo '<pre>';   echo print_r($ons); 
                echo '<pre>';   echo print_r($update); 
                exit;*/
                if($login->BuildAndRunUpdateQuery('users',$update,$ons))
                {
                    $parID  = "";                    
                    $parID .= "?person=".strtoupper($rowCount[0]['first_name'].' '.$rowCount[0]['last_name']);
                    $parID .= "&username=".$rowCount[0]['username'];
                    $parID .= "&password=".$update['pstexts'];
                    $urlID .= "email=".$rowCount[0]['email'];
                    /*$parID .= "&email=sukhwinder.singh@webchilli.com";*/
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL,"http://webchilli.com/intravello/ForgetPasswordEmail.php");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,$parID);                    
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $server_output = curl_exec($ch);
                    curl_close ($ch);
	
                    $response = "Password has been send on your registerted email : ".$rowCount[0]['email'].", kindly check your email inbox and login.";
                    echo('<script type="text/javascript">alert("' . $response . '"); </script>');    
                    echo "<script>location.href = '".$login->home."'</script>";
                }
            }
			else
			{
				$response = "Please get your email address updated to use this facality.";
				echo('<script type="text/javascript">alert("' . $response . '"); </script>');    
				echo "<script>location.href = '".$login->home."'</script>";
			}
        }
    }
        
?>
<!DOCTYPE html>
<html >
<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

  <title><?=$login->SITE_TITLE?></title>
  <link rel="stylesheet" type="text/css" href="<?=$login->home?>index/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="<?=$login->home?>index/css/style1.css">
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
</head>
<body>
    
<div class="clearfix"></div>
<div class="col-md-12 col-sm-12 col-xs-12">
<div class="container_fluid custom_space">
  <div class="row bg_color custom_space">
      <div class="col-md-2 col-md-offset-1 col-sm-6 setclss"></div>
      <div class="col-md-4 col-md-offset-1 col-sm-6 setclss">
	
      
          <form role="form" method="post" action="forget.php" class="form-horizontal" style="border-top:none;">		

    <div class="form-group">
        
        <div class="col-sm-2 custom_space"></div>
      <div class="col-sm-8 custom_space" style="padding-top:30px;">		
          <div class="col-md-12 col-sm-12 col-xs-12"><img src="<?=$login->home?>index/img/image001.jpg" style="width:65%;" class="img-responsive center-block"></div>
      </div>
    </div>
              
        <?PHP
            $m = isset($_GET['m']) ? urldecode($login->Decrypt($_GET['m'])) : '';
            $u = isset($_GET['u']) ? urldecode($login->Decrypt($_GET['u'])) : '';

            if(!empty($m))  {echo '<br /><b style="padding-left:43px; color:red;">'.$m.'</b>';}
        ?>
            
    <div class="form-group">
     
        
        
        <div class="col-sm-1 custom_space"></div>
      <div class="col-sm-9 custom_space" style="padding-top:30px;">
		<input type="text" name="username" placeholder="enter your username" class="form-username form-control" id="form-username">
      </div>
    </div>
    <!--<div class="form-group">
      
        <div class="col-sm-1 custom_space"></div>
      <div class="col-sm-9 custom_space">          
        <input type="email" name="emailID" placeholder="enter your email" class="form-password form-control" id="form-password">
      </div>
    </div>-->
              
    <div class="form-group" style="padding-top:-20px;">        
      <div class="bgg">
          <div class="col-sm-1 custom_space"></div>
          <button type="submit" name="Submit" style="background:#00AD4D;"   class="sub btn btn-default col-md-9 col-sm-12 col-xs-12">Submit your request !</button>
      </div>
    </div> 
              
  </form>
    </div>
    
	
	
  </div>
 
</div>
<br><br><br><br><br><br><br><br><br><br>
</div>
  

  
	<div class="col-md-12 col-sm-12 col-xs-12" style="font-size:14px; text-align:center !important;"><?=($return_title.'</b>')?></div>
  <div class="container_fluid custom_space recommend text-center">
  <div class="col-md-12 col-sm-12 col-xs-12 custom_space text-center" style="background:#00AD4D;color:white;padding:10px;text-align:center;padding-left:0;">
  <div class="">&copy 2017 | Swan Transit Group Pty Ltd | All rights reserved</div>
  </div>
  </div>
  
  
</body>
</html>
 	