<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
    
    $fdateID    = isset($_GET['fdateID'])    ?   ($_GET['fdateID'])   :   '';
    $tdateID    = isset($_GET['tdateID'])    ?   ($_GET['tdateID'])   :   '';
    $filterID   = isset($_GET['filterID'])   ?   ($_GET['filterID'])  :   '';

    echo '<aside class="right-side strech">';
    echo '<section class="content">';
        
    echo '<form method="get" action="?filterID='.$login->Encrypt(1).'" enctype="multipart/form-data">';
    echo '<div class="row">'; 
    
    echo '<div class="col-xs-5" style="border-radius:5px; border:#85144B 2px solid;padding: 10px; margin-top:20px; margin-left:30px;">';
    
    echo '<input type="radio" name="filterID" required="required" '.($filterID == 1 ? 'checked="checked"' : '').' value="1"> <b style="padding:10px; font-size:15px; color:#367FA9;">Sick Leave</b>';
    echo '<input type="radio" name="filterID" required="required" '.($filterID == 2 ? 'checked="checked"' : '').' value="2"> <b style="padding:10px; font-size:15px; color:#367FA9;">Early Running</b>';
    echo '<input type="radio" name="filterID" required="required" '.($filterID == 3 ? 'checked="checked"' : '').' value="3"> <b style="padding:10px; font-size:15px; color:#367FA9;">Late First</b>';
    echo '<input type="radio" name="filterID" required="required" '.($filterID == 4 ? 'checked="checked"' : '').' value="4"> <b style="padding:10px; font-size:15px; color:#367FA9;">Accident</b>';
    echo '<input type="radio" name="filterID" required="required" '.($filterID == 5 ? 'checked="checked"' : '').' value="5"> <b style="padding:10px; font-size:15px; color:#367FA9;">Complaints</b>';
    
    echo '</div>';
    
    echo '<div class="col-xs-2">';
    echo '<label for="section" style="color:#00A65A;">From Date</label>';
    echo '<input type="datable" name="fdateID" id="fdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter From Date" value="'.$fdateID.'" />';
    echo '</div>';

    echo '<div class="col-xs-2">';
    echo '<label for="section" style="color:#00A65A;">To Date</label>';
    echo '<input type="datable" name="tdateID" id="tdateID" required="required" style="text-align:center;" class="form-control datepicker" data-datable="ddmmyyyy" placeholder="Enter To Date" value="'.$tdateID.'" />';
    echo '</div>';

    echo '<div class="col-xs-1">';
    echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
    echo '<input type="submit" class="btn bg-maroon btn-flat" value="Filter Data" />';
    echo '</div>';

    echo '<div class="col-xs-1">';
    echo '<label for="section" style="color:#00A65A;">&nbsp;</label><br />';
    echo '<a href="profile_3.php" class="btn bg-maroon btn-flat">Reset Filter</a>';
    echo '</div>'; 
     
    echo '</div>';
    
    echo '</form>';    
    echo '<br />';
    
    if($filterID > 0)
    {
    echo '<div class="col-xs-12">';
    echo '<div class="nav-tabs-custom">';
    echo '<ul class="nav nav-tabs pull-right">';
     
    if($filterID == 1)
    {
        $sck_responseID = $SIndex->Data_Listsing_1($fdateID,$tdateID);
        
        echo '<li class="active"><a href="#gridID_1" data-toggle="tab"><b>Sick Leave (Week Days) - </b>';
        echo '<b style="color:red !important;">('.$sck_responseID['counID'].')</b></a></li>';
        
        echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Data Listing : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.')</b></b></li>';
        echo '</ul>';
        echo '<div class="tab-content no-padding">';
        
        echo '<div class="chart tab-pane active" id="gridID_1" style="position: relative; height: 440px; overflow-y: scroll; overflow-x: scroll;">'.$sck_responseID['fileID'].'</div>';
    }
    
    if($filterID == 2)
    {
        $erl_responseID = $SIndex->Data_Listsing_2($fdateID,$tdateID,'imp_persheets_e','earlyID');        
        echo '<li class="active"><a href="#gridID_2" data-toggle="tab"><b>Early Running - </b>';
        echo '<b style="color:red !important;">('.$erl_responseID['counID'].')</b></a></li>';
        
        echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Data Listing : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.')</b></b></li>';
        echo '</ul>';
        echo '<div class="tab-content no-padding">';
        
        echo '<div class="chart tab-pane active" id="gridID_2" style="position: relative; height: 440px; overflow-y: scroll; overflow-x: scroll;">'.$erl_responseID['fileID'].'</div>';
    }
    
    if($filterID == 3)
    {
        $lte_responseID = $SIndex->Data_Listsing_2($fdateID,$tdateID,'imp_persheets_l','latefirstID');        
        echo '<li class="active"><a href="#gridID_3" data-toggle="tab"><b>Late First - </b>';
        echo '<b style="color:red !important;">('.$lte_responseID['counID'].')</b></a></li>';
        
        echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Data Listing : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.')</b></b></li>';
        echo '</ul>';
        echo '<div class="tab-content no-padding">';
        
        echo '<div class="chart tab-pane active" id="gridID_3" style="position: relative; height: 440px; overflow-y: scroll; overflow-x: scroll;">'.$lte_responseID['fileID'].'</div>';
    }
    
    if($filterID == 4)
    {
        $act_responseID = $SIndex->Data_Listsing_4($fdateID,$tdateID);        
        echo '<li class="active"><a href="#gridID_4" data-toggle="tab"><b>Accident (At Fault) - </b>';
        echo '<b style="color:red !important;">('.$act_responseID['counID'].')</b></a></li>';
        
        echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Data Listing : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.')</b></b></li>';
        echo '</ul>';
        echo '<div class="tab-content no-padding">';
        
        echo '<div class="chart tab-pane active" id="gridID_4" style="position: relative; height: 440px; overflow-y: scroll; overflow-x: scroll;">'.$act_responseID['fileID'].'</div>';
    }
    
    if($filterID == 5)
    {
        $cmp_responseID = $SIndex->Data_Listsing_3($fdateID,$tdateID);
        echo '<li class="active"><a href="#gridID_5" data-toggle="tab"><b>Complaints (At Fault) - </b>';
        echo '<b style="color:red !important;">('.$cmp_responseID['counID'].')</b></a></li>';
        
        echo '<li class="pull-left header"><i class="fa fa-inbox"></i> <b style="color:#00A65A;">Data Listing : <b style="font-size:15px; color:#014F99;">Period - ('.$fdateID.' - '.$tdateID.')</b></b></li>';
        echo '</ul>';
        echo '<div class="tab-content no-padding">';
        
        echo '<div class="chart tab-pane active" id="gridID_5" style="position: relative; height: 440px; overflow-y: scroll; overflow-x: scroll;">'.$cmp_responseID['fileID'].'</div>';
    }
    
    echo '</div>';
    echo '</div>';
    echo '</div>';  
    }
    
    echo '<div class="col-xs-3"></div>  ';
    echo '</section>';
    echo '</aside>';
    echo '</div>';
?>

<?PHP include 'footer.php'; ?>

<script type="text/javascript">
	function exportToExcel()    {window.open('data:application/vnd.ms-excel,' + encodeURIComponent(document.getElementById('dataTables').outerHTML));}
</script>