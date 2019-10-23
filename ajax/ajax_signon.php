<?PHP
    include_once '../includes.php';
	
    $request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;
	
	$todayID = date('Y-m-d');
	
	if($request == 'API_SignonShifts')
	{
		$companyID = 1;
		
		/* duplicacy-scheme */
		$duplicateDATA = $GIndex->SignOn_Duplicacy($todayID,$companyID);
		
		/* pending/signed counts */
		$pendingID = $GIndex->DriverSignOnSheets($todayID,$companyID,2,$duplicateDATA['empNO'],$duplicateDATA['busNO']);
		$singonsID = $GIndex->DriverSignOnSheets($todayID,$companyID,1,$duplicateDATA['empNO'],$duplicateDATA['busNO']);
		$busalloID = $GIndex->DriverBusAlcSheets($todayID,$companyID,2);
		$mechansID = $GIndex->DriverMechanicsSheets($todayID);
		
		/* colour-scheme */
		$GIndex->SET_DriverSignOn_ColorID($todayID,$companyID,2);
		
		$arrFE = '';
		
		$arrFE .= '<div class="row">';
		$arrFE .= '<div class="col-md-12">';
		$arrFE .= '<div class="nav-tabs-custom">';
			$arrFE .= '<ul class="nav nav-tabs pull-right">';                
			$arrFE .= '<li><a href="#tab_8-8" data-toggle="tab"><b style="color:#367FA9;">Reverse Allocation</b> <b style="color:#D9006C !important;">('.$busalloID['countID'].')</b></a></li>';
			$arrFE .= '<li><a href="#tab_9-9" data-toggle="tab"><b style="color:#367FA9;">After Hour Mechanic</b> <b style="color:#D9006C !important;">('.$mechansID['countID'].')</b></a></li>';
			$arrFE .= '<li><a href="#tab_7-7" data-toggle="tab"><b style="color:#367FA9;">Assigned</b> <b style="color:#D9006C !important;">('.$singonsID['countID'].')</b></a></li>';
			$arrFE .= '<li class="active"><a href="#tab_6-6" data-toggle="tab"><b style="color:#367FA9;">Pending</b> <b style="color:#D9006C !important;">('.$pendingID['countID'].')</b></a></li>';                
			$arrFE .= '<li class="pull-left header"><i class="fa fa-th"></i> Driver SignOn </li>';
			$arrFE .= '</ul>';

			$arrFE .= '<div class="tab-content">';
			$arrFE .= '<div class="tab-pane active" id="tab_6-6" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
				$arrFE .= ($pendingID['fileID']);
			$arrFE .= '</div>';

			$arrFE .= '<div class="tab-pane" id="tab_7-7" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
				$arrFE .= ($singonsID['fileID']);
			$arrFE .= '</div>';

			$arrFE .= '<div class="tab-pane" id="tab_9-9" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
				$arrFE .= ($mechansID['fileID']);
			$arrFE .= '</div>';

			$arrFE .= '<div class="tab-pane" id="tab_8-8" style="width:100%; position: relative; height:800px; overflow-y: scroll; overflow-x: scroll;">';                
				$arrFE .= ($busalloID['fileID']);
			$arrFE .= '</div>';
			$arrFE .= '</div>';
		$arrFE .= '</div>';
		$arrFE .= '</div>';
		$arrFE .= '</div>';


		$arr = array('file_info'=>$arrFE);
		
	}
	
	echo json_encode($arr);	
?>