<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
    
    $fdateID    = isset($_GET['fdateID'])    ?   ($_GET['fdateID'])   :   date("d/m/Y", strtotime(date('Y-m-d').'-7Days'));
    $tdateID    = isset($_GET['tdateID'])    ?   ($_GET['tdateID'])   :   date('d/m/Y');		
	$fID_1		= isset($_GET['fID_1'])	 	 ?   ($_GET['fID_1']) 	  :   '';
	$fID_2		= isset($_GET['fID_2'])	 	 ?   ($_GET['fID_2']) 	  :   '';
	$fID_3		= isset($_GET['fID_3'])	 	 ?   ($_GET['fID_3']) 	  :   '';	
	$sidebarID	= isset($_GET['sidebarID'])	 ?   ($_GET['sidebarID']) :   '';
	$sidebarID	= $sidebarID > 0             ? 	  $sidebarID          :   0;	
	$typeID     = isset($_GET['typeID'])     ?   ($_GET['typeID'])    :   '';
    $typeID     = $typeID > 0                ?   $typeID              :   1;
	
	$companyID = (implode(',',$_REQUEST['filterID']) <> '' ? implode(',',$_REQUEST['filterID']) :  $_SESSION[$login->website]['ecomID']);
	
	$arrDATA = array();	
	$arrDATA['fdateID'] = $fdateID;
	$arrDATA['tdateID'] = $tdateID;
	$arrDATA['companyID'] = $companyID;
	$arrDATA['sidebarID'] = $sidebarID;
	$arrDATA['employeeID'] = $_GET['empID'] > 0 ? $_GET['empID'] : 0;	
	$arrDATA['employeeCD'] = $_GET['empCD'] > 0 ? $_GET['empCD'] : 0;	
	$arrDATA['fID_1']	   = $_GET['fID_1'] > 0 ? $_GET['fID_1'] : 0;
	$arrDATA['fID_2'] 	   = $_GET['fID_2'] > 0 ? $_GET['fID_2'] : 0;
	$arrDATA['fID_3'] 	   = $_GET['fID_3'] > 0 ? $_GET['fID_3'] : 0;
	$arrDATA['filterID']   = $_GET['filterID'];
		
    $captionTX = '';	$heightTX = '';		$urlTX = '';
    if($typeID == 1)
    { 
		$captionTX = 'Depot Signon Summary';
		$heightTX  = '460';
		$addressTX = 'profile_5.php?typeID=1&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y');
		$responseDIV = $SIndex->SignOn_Listsing_1($arrDATA);
    }
    else if($typeID == 2)
    {
		$captionTX = 'Depot Detail';
		$heightTX  = '700';
		$addressTX = 'profile_5.php?typeID=1&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y');
		$responseDIV = $SIndex->SignOn_Listsing_2($arrDATA);
    }
	else if($typeID == 3)
    {
		if($_GET['empCD'] > 0)
		{
			$arrEM = $_GET['empCD'] > 0 ? $login->select('employee',array("*")," WHERE code = '".$_GET['empCD']."' AND status = 1 ") : '';
		}
		else if($_GET['empID'] > 0)
		{
			$arrEM = $_GET['empID'] > 0 ? $login->select('employee',array("*")," WHERE ID = ".$_GET['empID']." ") : '';
		}
		
		$captionTX = 'Driver Detail';
		$heightTX  = '460';
		$addressTX = 'profile_5.php?typeID=2&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y');
		$responseDIV = $SIndex->SignOn_Listsing_3($arrDATA);
    }
	
    echo '<aside class="right-side strech">';
    echo '<section class="content">';
        
    echo '<form method="get" action="?filterID='.$login->Encrypt(1).'" enctype="multipart/form-data">';	
		echo '<input type="hidden" name="typeID" value="'.$typeID.'" />';
		echo '<input type="hidden" name="sidebarID" value="'.$sidebarID.'" />';
	
    echo '<div class="row">';     
		echo '<div class="col-xs-12" style="border-radius:5px; border:#F56954 2px solid;padding:5px; margin-top:-8px; width:85%; margin-left:25px;">';
			$explodeID = explode(",",$companyID);
			$Qry = $login->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID In(".$_SESSION[$login->website]['ecomID'].") Order By title ASC ");
			$Qry->execute();
			$login->resu = $Qry->fetchAll(PDO::FETCH_ASSOC);
			foreach($login->resu as $res)
			{ 
				echo '<input style="margin-left:18px;" type="checkbox" name="filterID[]" value="'.$res['ID'].'" '.(in_array($res['ID'],$explodeID) ? 'checked="checked"' : '' ).' > <b style="font-size:15px; color:#367FA9;">'.$res['title'].'</b>';
			}    
		echo '</div>';

		echo '<div class="col-xs-12"><br /></div>';
		
	if($typeID == 3)
	{
		echo '<div class="col-xs-2" style="width:150px; margin-top: -14px; margin-left:25px;">';
		echo '<label for="section" style="color:#00A65A;">Driver Code</label>';
		echo '<input type="text" name="empCD" class="form-control" placeholder="Driver Code" value="'.$_GET['empCD'].'" />';
		echo '</div>';
	}
	
		echo '<div class="col-xs-2" style="width:150px; margin-top: -14px; margin-left:18px;">';
		echo '<label for="section" style="color:#00A65A;">From Date</label>';
		echo '<input type="datable" name="fdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" value="'.$fdateID.'" />';
		echo '</div>';

		echo '<div class="col-xs-2" style="width:150px; margin-top: -14px; margin-left: -18px;">';
		echo '<label for="section" style="color:#00A65A;">To Date</label>';
		echo '<input type="datable" name="tdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" value="'.$tdateID.'" />';
		echo '</div>';
		
		echo '<input type="hidden" name="empID" value="'.($_REQUEST['empID'] > 0 ? $_REQUEST['empID'] : 0).'"/ >';
	
		echo '<div class="col-xs-1" style="margin-top: -14px;"><input type="submit" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;" value="Filter Data" /></div>';
		echo '<div class="col-xs-1" style="margin-top:-14px;"><a href="'.$addressTX.'" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;">Reset Filter</a></div>';
		echo '<div class="col-xs-1" style="margin-top:-14px;"><a href="exportCSV.php?s=SIGNON_LATE_SHEET&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&typeID='.$typeID.'&companyID='.$companyID.'&empCD='.$_GET['empCD'].'&empID='.$_GET['empID'].'&fID_1='.$_GET['fID_1'].'&fID_2='.$_GET['fID_2'].'&fID_3='.$_GET['fID_3'].'" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;">Export Excel</a></div>';	
    echo '</div>'; 
    echo '</form>';    
    echo '<br />';
     
	echo '<div class="col-xs-12" style="margin-top:-18px;">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">'; 
	
	echo '<li class="active"><a href="#gridID_1" data-toggle="tab"><b>Sign On </b></a></li>';
	echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">'.$captionTX.' : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.') </b> '.($_GET['empID'] > 0 ? '<b style="color:#00A65A; margin-left: 200px;">Driver : '.strtoupper($arrEM[0]['code']).' - '.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b>' : '').'</li>';
	echo '</ul>';
	echo '<div class="tab-content no-padding">';            
	echo '<div class="chart tab-pane active" id="gridID_1" style="position: relative; height: '.$heightTX.'px; overflow-y: scroll; overflow-x: scroll;">'.$responseDIV['fileID'].'</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
    echo '</section>';
    echo '</aside>';
    echo '</div>';
	
    include 'footer.php'; 
?>