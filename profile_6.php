<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
    
    $fdateID    = isset($_GET['fdateID'])    ?   ($_GET['fdateID'])   :   date("d/m/Y", strtotime(date('Y-m-d').'-1Days'));
    $tdateID    = isset($_GET['tdateID'])    ?   ($_GET['tdateID'])   :   date('d/m/Y');
	$ecodeID    = isset($_GET['ecodeID'])    ?   ($_GET['ecodeID'])   :   '';
	
    $companyID = "";    
	$companyID = implode(',',$_REQUEST['filterID']);    
    $companyID = ($companyID > 0 ? $companyID : $_SESSION[$login->website]['ecomID']);
	$arrEM = $_GET['empID'] > 0 ? $login->select('employee',array("*")," WHERE ID = ".$_GET['empID']." ") : '';
	
	$arrDATA = array();
	$arrDATA['fdateID'] = $fdateID;
	$arrDATA['tdateID'] = $tdateID;
	$arrDATA['companyID'] = $companyID; 
	$arrDATA['ecodeID']   = $ecodeID; 
	$arrDATA['fID_1'] = $_GET['fID_1'] > 0 ? $_GET['fID_1'] : 0;
	$arrDATA['fID_2'] = $_GET['fID_2'] > 0 ? $_GET['fID_2'] : 0;
	$arrDATA['fID_3'] = $_GET['fID_3'] > 0 ? $_GET['fID_3'] : 0;
	$arrDATA['fID_4'] = $_GET['fID_4'] > 0 ? $_GET['fID_4'] : 0;
	$arrDATA['filterID'] = $_GET['filterID']; 
	
    echo '<aside class="right-side strech">';
    echo '<section class="content">';
        
    echo '<form method="get" action="?filterID='.$login->Encrypt(1).'" enctype="multipart/form-data">';
	
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

    echo '<div class="col-xs-6" style="width:200px; margin-top: -13px; margin-left:25px;">';
		echo '<label for="section" style="color:#00A65A;">Driver Code</label>';
		echo '<input type="text" name="ecodeID" class="form-control" placeholder="Driver Code" value="'.$ecodeID.'" />';
    echo '</div>';
	
    echo '<div class="col-xs-2" style="width:150px; margin-top: -13px;">';
    echo '<label for="section" style="color:#00A65A;">From Date</label>';
    echo '<input type="datable" name="fdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" value="'.$fdateID.'" />';
    echo '</div>';

    echo '<div class="col-xs-2" style="width:150px; margin-top: -13px; margin-left: -18px;">';
    echo '<label for="section" style="color:#00A65A;">To Date</label>';
    echo '<input type="datable" name="tdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" value="'.$tdateID.'" />';
    echo '</div>';
	
    echo '<div class="col-xs-1" style="margin-top: -13px;"><input type="submit" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;" value="Filter Data" /></div>';
    echo '<div class="col-xs-1" style="margin-top: -14px;"><a href="profile_6.php?fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y').'&filterID='.$companyID.'" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;">Clear Filter</a></div>';
	echo '<div class="col-xs-1" style="margin-top: -13px;"><a href="exportCSV.php?s=SIGNON_LATE_SHEET&empID='.$ecodeID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&typeID=4&companyID='.$companyID.'&fID_1='.$_GET['fID_1'].'&fID_2='.$_GET['fID_2'].'&fID_3='.$_GET['fID_3'].'&fID_4='.$_GET['fID_4'].'" class="btn bg-navy btn-flat margin" style="border: #F56954 1px solid; margin-top:23px;">Export Excel</a></div>';
	
    echo '</div>'; 
    echo '</form>';    
    echo '<br />';
     
	echo '<div class="col-xs-12">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">';
	
	$responseID = $SIndex->SignOn_Listsing_4($arrDATA);
	
	echo '<li class="active"><a href="#gridID_1" data-toggle="tab"><b>Sign On </b></a></li>';
	echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Driver Detail Report : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.') </b></li>';
	echo '</ul>';
	echo '<div class="tab-content no-padding">';            
	echo '<div class="chart tab-pane active" id="gridID_1" style="position: relative; height: 700px; overflow-y: scroll; overflow-x: scroll;">'.$responseID['fileID'].'</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
    echo '</section>';
    echo '</aside>';
    echo '</div>';
	
    include 'footer.php'; 
?>