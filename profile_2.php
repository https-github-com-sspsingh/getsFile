<?PHP
	include 'includes.php';
	$login->NotLogin_Redi();
	include 'header.php';
	include 'sidebar.php';	
	    
	$EM_Array   = $_GET['dashCD'] > 0  ? $login->select('employee',array("*"), " WHERE code = ".$_GET['dashCD']." AND companyID IN (".$_SESSION[$login->website]['compID'].") ") : '';
	$fdateID    = isset($_GET['fdateID'])    ?   ($_GET['fdateID'])   :   '';
	$tdateID    = isset($_GET['tdateID'])    ?   ($_GET['tdateID'])   :   '';

echo '<aside class="right-side strech">';
echo '<section class="content">';

echo '<form method="get" action="?filterID='.$login->Encrypt(1).'" enctype="multipart/form-data">';
echo '<input type="hidden" id="memberID" value="'.$EM_Array[0]['ID'].'" />';
echo '<input type="hidden" id="base_fileID" value="profile_2.php" />';

echo '<div class="row">';    
echo '<div class="col-xs-2">';
echo '<label for="section" style="color:#00A65A;">From Date</label>';
echo '<input type="datable" name="fdateID" id="fdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter From Date" value="'.$fdateID.'" />';
echo '</div>';
    
echo '<div class="col-xs-2">';
echo '<label for="section" style="color:#00A65A;">To Date</label>';
echo '<input type="datable" name="tdateID" id="tdateID" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter To Date" value="'.$tdateID.'" />';
echo '</div>';
    
echo '<div class="col-xs-2" id="profile_ecodeID">';
echo '<label for="section" style="color:#00A65A;">Employee Code</label>';
echo '<input type="text" name="dashCD" id="dashCD" style="text-align:center;" class="form-control" value="'.$EM_Array[0]['code'].'" placeholder="Enter E. Code" value="" />';
        
echo '<div id="divResult"></div>';
echo '</div>';

echo '<div class="col-xs-1">';
echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
echo '<input type="submit" class="btn bg-maroon btn-flat" value="Filter Data" />';
echo '</div>';
    
echo '<div class="col-xs-1">';
echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
echo '<a href="profile_2.php?=dashCD='.$_GET['dashCD'].'&fdateID='.date("d/m/Y", strtotime("-12 months")).'&tdateID='.date('d/m/Y').'" class="btn bg-maroon btn-flat">Reset Filter</a>';
echo '</div>';
echo '</div>';


echo '<div class="raw" id="profile_gridID">';

	echo '<div class="col-xs-12"><marquee behavior="alternate" class="blinkingTX" scrollamount="10" style="font-size:16px; color:#F56954; padding-bottom:10px; font-weight:bold;">
	'.strtoupper($EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].' ('.$EM_Array[0]['code'].')').'</marquee></div>';
    echo '<div class="col-xs-3">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">';
		echo '<li class="active"><a href="#sick_leaveID" data-toggle="tab"><b>Sick Leave</b></a></li>';
	echo '</ul>';
    echo '<div class="tab-content no-padding">';
    echo '<div class="chart tab-pane active" id="sick_leaveID" style="position: relative; height: 430px; overflow-y: scroll; overflow-x: hidden; ">';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
            
	echo '<div class="col-xs-3">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">';
	echo '<li class="active"><a href="#comlaintsID" data-toggle="tab"><b>Complaints</b></a></li>';
	echo '</ul>';
	echo '<div class="tab-content no-padding">';
	echo '<div class="chart tab-pane active" id="comlaintsID" style="position: relative; height: 430px; overflow-y: scroll; overflow-x: hidden; ">';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
            
	echo '<div class="col-xs-3">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">';
	echo '<li class="active"><a href="#accidentsID" data-toggle="tab"><b>Accidents</b></a></li>';
	echo '</ul>';
	echo '<div class="tab-content no-padding">';
	echo '<div class="chart tab-pane active" id="accidentsID" style="position: relative; height: 430px; overflow-y: scroll; overflow-x: hidden; ">';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
            
	echo '<div class="col-xs-3">';
	echo '<div class="nav-tabs-custom">';
	echo '<ul class="nav nav-tabs pull-right">';
	echo '<li class="active"><a href="#incidentsID_ER" data-toggle="tab"><b>Incidents - E.R</b></a></li>';
	echo '<li><a href="#incidentsID_LF" data-toggle="tab"><b>Incidents - L.F</b></a></li>';
	echo '</ul>';
	echo '<div class="tab-content no-padding">';
	echo '<div class="chart tab-pane" id="incidentsID_LF" style="position: relative; height: 430px; overflow-y: scroll; overflow-x: hidden;"></div>';
	echo '<div class="chart tab-pane active" id="incidentsID_ER" style="position: relative; height: 430px; overflow-y: scroll; overflow-x: hidden;"></div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	echo '</div>';
            
            
	echo '</section>';
	echo '</aside>';
	echo '</div>';

	include 'footer.php'; 
?>