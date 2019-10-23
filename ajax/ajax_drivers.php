<?PHP
    include_once '../includes.php';
	
    $request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;
	
	$todayID = date('Y-m-d');
	
	if($request == 'checkSigonUtility')
	{
		extract($_POST);
		
		$crtDEPOT = "";
		for($srID = 0; $srID <= count($depotVAL); $srID++)	{$crtDEPOT .= ($depotVAL[$srID] <> '' ? ($srID == 0 ? $depotVAL[$srID] : ",".$depotVAL[$srID]) : "");}
		
		if($crtDEPOT <> '')
		{
			/**** STARTING -- FILTER LOGS SESSION PREPARE ****/
				$SQL = "DELETE FROM filter_logsid WHERE frmNM = 'DRIVER_SIGN_ON' AND userID = ".$_SESSION[$login->website]['userID']." ";			
				$Qry = $login->DB->prepare($SQL);
				$Qry->execute();

				$_SESSION[$login->website]['filter_compID'] = $crtDEPOT;
				
				$array = array();
				$array['userID'] = $_SESSION[$login->website]['userID'];
				$array['frmNM']  = 'DRIVER_SIGN_ON';
				$array['filterTX'] = $crtDEPOT;
				$login->BuildAndRunInsertQuery('filter_logsid',$array);
			/**** ENDING -- FILTER LOGS SESSION PREPARE ****/

			$last_pendingID  = str_replace(')','',(str_replace('(','',$last_pendingID)));
			$last_completeID = str_replace(')','',(str_replace('(','',$last_completeID)));
			
			$last_pendingID  = $last_pendingID > 0  ?  $last_pendingID  : 0;
			$last_completeID = $last_completeID > 0 ?  $last_completeID : 0;
			
			$nowPendingID  = $GIndex->DriverSignOnSheets($todayID,$crtDEPOT,2,'','');
			$nowCompleteID = $GIndex->DriverSignOnSheets($todayID,$crtDEPOT,1,'','');
			
			/**** STARTING -- SIGNON - PENDING - TAB - UTILITY ****/
				if(($nowPendingID['countID'] <> $last_pendingID) || ($caseTX == 'undoChopped') || ($caseTX == 'undoSpares') || ($caseTX == 'updateSpares'))
				{
					$arr['sigonPending'] = 1;
					$arr['pendingData'] = '('.$nowPendingID['countID'].')';
					$arr['pendingList'] = $nowPendingID['fileID'];
				}
				else	{$arr['sigonPending'] = 0;}
			/**** ENDING -- SIGNON - PENDING - TAB - UTILITY ****/

			/**** STARTING -- SIGNON - ASSIGNED - TAB - UTILITY ****/
			if($nowCompleteID['countID'] <> $last_completeID)
			{
				$arr['sigonAssigned'] = 1;
				$arr['assignedData'] = '('.$nowCompleteID['countID'].')';
				$arr['assignedList'] = $nowCompleteID['fileID'];
			}
			else	{$arr['sigonAssigned'] = 0;}
			/**** ENDING -- SIGNON - ASSIGNED - TAB - UTILITY ****/

		}
	}
	
	echo json_encode($arr);	
?>