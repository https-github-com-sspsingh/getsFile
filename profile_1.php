<?PHP
	include 'includes.php';
	$login->NotLogin_Redi();
	include 'header.php';
	include 'sidebar.php';	
	
	//$url_1  = $login->home.'dashboard.php?i='.$login->Encrypt('1');	
	$ID     	 = isset($_GET['i'])	?	$login->Decrypt($_GET['i']) : '';        
	$EM_Array   = $ID > 0    ? $login->select('employee',array("*"), " WHERE ID = ".$ID." ") : '';
	$fdateID    = isset($_GET['fdateID'])    ?   ($_GET['fdateID'])   :   '';
	$tdateID    = isset($_GET['tdateID'])    ?   ($_GET['tdateID'])   :   '';

echo '<aside class="right-side strech">';
echo '<section class="content">';

echo '<form method="get" action="?filterID='.$login->Encrypt(1).'" enctype="multipart/form-data">';

echo '<div class="row">';    
echo '<div class="col-xs-2">';
echo '<label for="section" style="color:#00A65A;">From Date</label>';
echo '<input type="datable" name="fdateID" id="fdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter From Date" value="'.$fdateID.'" />';
echo '</div>';
    
echo '<div class="col-xs-2">';
echo '<label for="section" style="color:#00A65A;">To Date</label>';
echo '<input type="datable" name="tdateID" id="tdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter To Date" 
value="'.$tdateID.'" />';
echo '</div>';
    
echo '<div class="col-xs-1">';
echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
echo '<input type="submit" class="btn bg-maroon btn-flat" value="Filter Data" />';
echo '</div>';
    
echo '<div class="col-xs-1">';
echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
echo '<a href="profile_1.php?fdateID='.date("d/m/Y", strtotime("-12 months")).'&tdateID='.date('d/m/Y').'" class="btn bg-maroon btn-flat">Reset Filter</a>';
echo '</div>';
echo '</div>';


	echo '<div class="raw" id="profile_gridID">';
	
	echo '<div class="col-xs-6">';
	echo '<h4 style="color:#3C8DBC; font-family:Georgia, Times New Roman, Times, serif; font-size:16px;">Incidents - Early Running</h4>';
	echo '<div class="tab-content no-padding" style="border:#3C8DBC 2px solid;">';
	echo '<div class="chart tab-pane active" id="sick_leaveID" style="position: relative; height: 560px; overflow-y: scroll; overflow-x: hidden; ">';
		echo $SIndex->Incidents_ER($fdateID,$tdateID);
	echo '</div>';
	echo '</div>';
	echo '</div>';
					
	echo '<div class="col-xs-6">';
	echo '<h4 style="color:#3C8DBC; font-family:Georgia, Times New Roman, Times, serif; font-size:16px;">Incidents - Late First</h4>';
	echo '<div class="tab-content no-padding" style="border:#3C8DBC 2px solid;">';
	echo '<div class="chart tab-pane active" id="sick_leaveID" style="position: relative; height: 560px; overflow-y: scroll; overflow-x: hidden; ">';
		echo $SIndex->Incidents_LF($fdateID,$tdateID);
	echo '</div>';
	echo '</div>';
	echo '</div>';	
	
	echo '</div><br />';
	
	/*echo '<div class="raw">';
	
	echo '<div class="col-xs-4">';
	$sickID = $SIndex->Data_Listsing_1($fdateID,$tdateID);
	$accidentID = $SIndex->Data_Listsing_4($fdateID,$tdateID);
	$complaintsID = $SIndex->Data_Listsing_3($fdateID,$tdateID);
	
	echo '<h4 style="color:red; font-family:Georgia, Times New Roman, Times, serif; font-size:16px;"><b>No of 1 Day Sick Leave : '.$sickID['counID'].'</b></h4>';
	echo '</div>';
					
	echo '<div class="col-xs-4">';
	echo '<h4 style="color:red; font-family:Georgia, Times New Roman, Times, serif; font-size:16px;"><b>No of At Fault Accidents : '.$accidentID['counID'].'</b></h4>';
	echo '</div>';

	echo '<div class="col-xs-4">';
	echo '<h4 style="color:red; font-family:Georgia, Times New Roman, Times, serif; font-size:16px;"><b>No of At Fault Complaint : '.$complaintsID['counID'].'</b></h4>';
	echo '</div>';
	
	echo '</div>';*/
            
            
	echo '</section>';
	echo '</aside>';
	echo '</div>';

	include 'footer.php'; 
?>