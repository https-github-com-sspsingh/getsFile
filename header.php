<?PHP
    $changeSession = isset($_GET['c']) ? $_GET['c'] : '';
    if(!empty($changeSession))
    {
        $SC_Array = $login->select('company',array("*"), " WHERE ID = ".$changeSession." ");		
        $_SESSION[$login->website]['compID'] = $SC_Array[0]['ID'];
		$_SESSION[$login->website]['dashID'] = $SC_Array[0]['dash_notID'];
        $_SESSION[$login->website]['compNM'] = $SC_Array[0]['title'].' - '.$SC_Array[0]['pscode'];
		$_SESSION[$login->website]['compCD'] = $SC_Array[0]['code'];
		$_SESSION[$login->website]['dashID'] = $SC_Array[0]['dsnotID'];
		$_SESSION[$login->website]['cdysID'] = $SC_Array[0]['dcdaysID'];
		
		$Qry = $login->DB->prepare("SELECT Group_Concat(ID) as scompanyID FROM company_dtls WHERE companyID = :cID ");
		$Qry->bindParam(':cID',$changeSession);
		$Qry->execute();
		$login->rowsC = $Qry->fetch(PDO::FETCH_ASSOC);
		$_SESSION[$login->website]['scompID'] = $login->rowsC['scompanyID'];
		
		///echo print_r($_SESSION[$login->website]);
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?PHP echo  $login->SITE_TITLE;  ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="<?=$login->home?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="<?=$login->home?>css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="<?=$login->home?>css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="<?=$login->home?>css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/style.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/slider.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!--<link href="<?=$login->home?>css/all.css" rel="stylesheet" type="text/css" />-->
        <!-- Ion Slider -->
        <link href="<?=$login->home?>css/ion.rangeSlider.css" rel="stylesheet" type="text/css" />
		
        <!-- Ion Slider -->
        <link href="<?=$login->home?>css/files.css" rel="stylesheet" type="text/css" />

        <!-- TimePicker -->
        <link rel="stylesheet" href="<?=$login->home?>js/clockface.css">

        <!-- DataTables -->
        <link rel="stylesheet" href="<?=$login->home?>css/dataTables.bootstrap.css">
        
        <!-- ion slider Nice -->
        <link href="<?=$login->home?>css/ion.rangeSlider.skinNice.css" rel="stylesheet" type="text/css" />
        <!--<link href="<?=$login->home?>css/iCheck_all.css" rel="stylesheet" type="text/css" />-->
        <link href="<?=$login->home?>css/spacing.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/lining_view.css" rel="stylesheet" type="text/css" />
        <link href="<?=$login->home?>css/validators.css" rel="stylesheet" type="text/css" />
        
        <!-- Select2 -->
        <link rel="stylesheet" href="<?=$login->home?>font-awesome/css/font-awesome.min.css"/>
        <link rel="stylesheet" href="<?=$login->home?>js/notify/demo.css"/>
        <link rel="stylesheet" href="<?=$login->home?>js/notify/Lobibox.min.css"/>
        
        <!-- Select2 -->
        <link rel="stylesheet" href="<?=$login->home?>css/select2.min.css">  
        <script type="text/javascript" src="<?=$login->home?>js/jquery-1.6.3.min.js"></script>   
            
<?PHP
	$pageURL = basename($_SERVER['PHP_SELF']);
	
	if(substr($pageURL, 0, 7) == 'profile' || $pageURL == 'dashboard.php' || $pageURL == 'datasender.php' || $pageURL == 'missings.php' || substr($pageURL, 0, 3) == 'rpt')
	{
?>
	<script type="text/javascript"> $(window).load(function()   {$(".loader").fadeOut("slow");})</script>
	
<style>
	.loader {
		position: fixed;<?=($pageURL == 'dashboard.php' ? 'left: 15%;top: 0px;width: 90%;' 
							  :(substr($pageURL, 0, 7) == 'profile' || $pageURL == 'missings.php' || $pageURL == 'datasender.php' ? 'left: 0;top: 40px;width: 100%;' : 'top: 200px;width: 100%;'))?>height: 100%;z-index: 9999;
		background: url('<?=$login->home?><?=($pageURL == 'dashboard.php'  || $pageURL == 'missings.php' ? 'img/6.gif' :($pageURL == 'datasender.php' ? 'img/default.gif' :(substr($pageURL, 0, 7) == 'profile' ? 'img/loading.svg' : 'img/103.gif')))?>') 50% 50% no-repeat rgb(249,249,249);
	}
</style>
<?PHP
	}
	
    if($pageURL == 'profile_4.php' || $pageURL == 'drvsigon.php')
    {
?>
        <script type="text/javascript" src="<?=$login->home?>js/dragdrop/redips-drag-min.js"></script>
        <script type="text/javascript" src="<?=$login->home?>js/dragdrop/script.js"></script>
<?PHP
    }
?>
<style>
	.wrk_loads{position: fixed;left:0px;top: 0px;width: 100%;height: 100%;z-index: 9999;	background: url('img/spinners.gif') 50% 50% no-repeat rgb(249,249,249);}
</style>

</head>
<body class="skin-blue" id="Remove_Dash_Search">    
	<input type="hidden" id="currentID" value="<?=($_SESSION[$login->website]['userID'])?>" />    
	<input type="hidden" id="currentUT" value="<?=($_SESSION[$login->website]['userTY'])?>" />    
    <input type="hidden" id="passedID"  value="<?=($pageURL == 'dashboard.php' ? '786' : '0')?>" />
    
 <div id="wrk_loads" class="loader"></div>
 
   <header class="header">
   <div class="logo" style="text-align: left !important;">
	<a href="<?=$login->home?>dashboard.php">
	<img src="<?=$login->home?>img/logo.png" style="max-width: 190px; text-align: left !important;max-height: 53px;" />
	</a>
   </div>
	
	
	<nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
	</a>
	<div class="navbar-right">
    <ul class="nav navbar-nav"> 
<?PHP
    if($pageURL == 'dashboard.php')
    {
        $gyrID = (isset($_GET['yrID'])   ?   $login->Decrypt($_GET['yrID'])  :   $_SESSION[$login->website]['yearID']);
        $_SESSION[$login->website]['yearID'] = $gyrID;
        
		
		/*echo '<li class="dropdown user user-menu">';
		echo '<div class="btn-group" style="margin-top:8px; margin-right:10px;">';
		echo '<div class="row">';
			echo '<div class="col-xs-12">';
			echo '<input type="text" id="dashCD" class="form-control" placeholder="Search By Code/Name ...." />';

			echo '<div id="divResult"></div>';
			echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</li>';*/
		
        /*echo '<li class="dropdown user user-menu">';
        echo '<div class="btn-group" style="margin-top:8px; margin-right:10px;">';
        echo '<button type="button" class="btn btn-success btn-flat" id="pyrID" data-rel="'.$gyrID.'">Chart - '.$gyrID.'</button>';
        echo '<button type="button" class="btn btn-success btn-flat dropdown-toggle" data-toggle="dropdown">';
        echo '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
        echo '</button>';
        echo '<ul class="dropdown-menu" role="menu">';            
            for($syrID = 2011; $syrID <= date('Y'); $syrID++)
            {
                echo '<li><a href="'.$login->home.''.$pageURL.'?yrID='.$login->Encrypt($syrID).'">Chart - '.$syrID.'</a></li>';
            }                
        echo '</ul>';
        echo '</div>';
        echo '</li>';*/
    } 
	
		echo '<li class="dropdown user user-menu">';
        echo '<div class="btn-group" style="margin-top:8px; margin-right:10px;">';
        echo '<button type="button" class="btn btn-success btn-flat">'.$_SESSION[$login->website]['compNM'].'</button>';
        echo '<button type="button" class="btn btn-success btn-flat dropdown-toggle" data-toggle="dropdown">';
        echo '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>';
        echo '</button>';
		echo '<ul class="dropdown-menu" role="menu">';            
			$Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
			$Qry->execute();
			$login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);
			foreach($login->resu as $resu)
			{
				echo '<li><a href="'.$login->home.'dashboard.php?c='.$resu['ID'].'">'.$resu['title'].' - '.$resu['pscode'].'</a></li>';											
			}
		echo '</ul>';
        echo '</div>';
        echo '</li>';
		
	if($_SESSION[$login->website]['empDN'] <> '')
	{
		echo '<li class="dropdown user user-menu">';
		echo '<div class="btn-group" style="margin-top:8px; margin-right:8px;">';
		echo '<button type="button" class="btn btn-success btn-flat">Designation : '.($_SESSION[$login->website]['empDN']).'</button>';
		echo '</div>';
		echo '</li>';
	}
	
    echo '<li class="dropdown user user-menu">';
    echo '<div class="btn-group" style="margin-top:8px; margin-right:8px;">';
    echo '<button type="button" class="btn btn-success btn-flat">'.$_SESSION[$login->website]['fullNM'].'</button>';
    echo '</div>';
    echo '</li>';
	
    $Qry = $login->DB->prepare("SELECT * FROM employee WHERE ID > 0 AND status = 1 AND (MONTH(dob) = '".date('m')."' AND DAY(dob) = '".date('d')."') Order By full_name ASC ");
    if($Qry->execute())
    {
        $login->rows_0 = $Qry->fetchAll(PDO::FETCH_ASSOC);
        if(is_array($login->rows_0) && count($login->rows_0) > 0)			
        {
			echo '<li class="dropdown messages-menu">';
			echo '<a class="dropdown-toggle" data-toggle="dropdown">';
			echo '<i class="fa fa-envelope"></i>';
			echo '<span class="label label-danger blinkingTX">'.count($login->rows_0).'</span>';
			echo '</a>';
			echo '<ul class="dropdown-menu" style="border:#7BCBD9 1px solid;">';
			echo '<li class="header"><b style="color:#7BCBD9;">Today Birthday : '.count($login->rows_0).' Employee</b></li>';
			echo '<li>';
				echo '<ul class="menu">';
				foreach($login->rows_0 as $rows_0)
				{
					$arrCMP  = $rows_0['companyID'] > 0  ? $login->select('company',array("*"), " WHERE ID = ".$rows_0['companyID']." ") : '';
					$arrMST  = $rows_0['desigID'] > 0  ? $login->select('master',array("*"), " WHERE ID = ".$rows_0['desigID']." ") : '';
					
					echo '<li>';
						echo '<a>';
							echo '<div class="pull-left">';
								echo '<img src="'.$login->home.'img/birthday.jpg" class="img-circle" alt="User Image"/>';
							echo '</div>';
							echo '<h4>'.$arrCMP[0]['title'].' - '.$arrMST[0]['title'].'</h4>';
							echo '<p>'.$rows_0['code'].' - '.$rows_0['full_name'].'</p>';
						echo '</a>';
					echo '</li>';
				}				
				echo '</ul>';
			echo '</li>';				
			echo '</ul>';
			echo '</li>';
        }
    }
	
    ?>

						
    <li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="glyphicon glyphicon-user"></i>
	    <span><?=$_SESSION[$login->website]['userNM']?><i class="caret"></i></span>
    </a>
    <ul class="dropdown-menu">
    <li class="user-footer">    
        <div class="pull-right">
            <a href="<?=$login->home?>logout.php" class="btn btn-default btn-flat">Sign out</a>
        </div>
    </li>
    </ul>
    </li>    
    </ul>
    </div>
	</nav>
</header>