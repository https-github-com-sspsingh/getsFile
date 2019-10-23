<?PHP
	include_once '../includes.php';
        
	$request	 =	isset($_POST['request'])	?	$_POST['request']	    : '' ;	
	$reqID       =	isset($_POST['reqID'])  	?	$_POST['reqID']             : '' ;	
	$timeID_1	 =	isset($_POST['timeID_1'])	?	$_POST['timeID_1']	    : '' ;	
	$timeID_2	 =	isset($_POST['timeID_2'])	?	$_POST['timeID_2']	    : '' ;
	$hoursID	 =	isset($_POST['hoursID'])	?	$_POST['hoursID']	    : '' ;	
	$fdayIID	 =	isset($_POST['fdayIID'])	?	$_POST['fdayIID']	    : '' ;	
	$tdayIID	 =	isset($_POST['tdayIID'])    ?	$_POST['tdayIID']	    : '' ;
	
	if($request == 'transferDepots')
	{  
		$listsTX = '';
		
		$SQL = "Select AllDepots.companyID, AllDepots.scompanyID, AllDepots.title,  AllDepots.pscode From (Select company.ID As companyID,  0 As scompanyID, company.title, company.pscode, 1 As tableID From 
		company Left Join company_dtls On company_dtls.companyID = company.ID Where company_dtls.companyID Is Null UNION All Select company_dtls.companyID, company_dtls.ID, company_dtls.title, 
		company_dtls.pscode, 2 As tableID From company_dtls) As AllDepots Order By AllDepots.companyID, AllDepots.scompanyID ASC ";
		$Qry = $login->DB->prepare($SQL);
		$Qry->execute();
		$login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		foreach($login->rows as $rows)
		{
			$listsTX .= '<option value="'.($rows['companyID'].'-'.$rows['scompanyID']).'">'.$rows['title'].' - '.$rows['pscode'].'</option>';
		}
		
		$data['listsTX'] = $listsTX;
	}
	
	if($request == 'regisnationLists')
	{  
		$listsTX = '';
		$listsTX = $FIndex->GET_Masters(0,'67');
		$data['listsTX'] = $listsTX;
	}
	
	if($request == 'terminationLists')
	{  
		$listsTX = '';
		$listsTX = $FIndex->GET_Masters(0,'121');
		$data['listsTX'] = $listsTX;
	}
	
	if($request == 'Update_MechanicData')
	{
		extract($_POST);
		
		$arrEMP = ($staffID > 0 ? $login->select('employee',array("phone,phone_1"), " WHERE ID = ".$staffID." ") : '');
		
		$ars = array();
		$ars['typeID']  = $depotID;
		$ars['dateID']  = $login->dateFormat($dateID);
		$ars['dayID']   = date('l',strtotime($ars['dateID']));
		$ars['empID']   = $staffID;
		$ars['phone_1'] = $arrEMP[0]['phone'];
		$ars['phone_2'] = $arrEMP[0]['phone_1'];
		$ons['recID']  = $rowID;
		if($login->BuildAndRunUpdateQuery('mechanic_mst',$ars,$ons))
				{$data['statusID'] = 2;}
		else	{$data['statusID'] = 3;}
	}
	
	if($request == 'Insert_MechanicData')
	{
		extract($_POST);
		
		if($login->dateFormat($fdateID) < date('Y-m-d'))	/* Check From Date */	
		{
			$data['statusID'] = 1;	
		}
		else if($login->dateFormat($tdateID) < $login->dateFormat($fdateID))
		{
			$data['statusID'] = 1;
		}
		else if($login->dateFormat($fdateID) > $login->dateFormat($tdateID))
		{
			$data['statusID'] = 1;
		}
		else if($login->dateFormat($tdateID) >= $login->dateFormat($fdateID))
		{
			$daysID = 0;			
			$daysID = (abs(round((strtotime($login->dateFormat($tdateID)) - strtotime($login->dateFormat($fdateID))) / 86400)) + 1);
			
			if($daysID > 0)
			{
				$topID = 0;
				for($srID = 1; $srID <= $daysID; $srID++)
				{
					$arrEMP = ($staffID > 0 ? $login->select('employee',array("phone,phone_1"), " WHERE ID = ".$staffID." ") : '');
					
					$arr = array();
					$arr['typeID']  = $depotID;
					$arr['dateID']  = date('Y-m-d',strtotime($login->dateFormat($fdateID).'+'.$topID.' Days'));
					$arr['dayID']   = date('l',strtotime($arr['dateID']));
					$arr['empID']   = $staffID;
					$arr['phone_1'] = $arrEMP[0]['phone'];
					$arr['phone_2'] = $arrEMP[0]['phone_1'];
					$arr['userID']  = $_SESSION[$login->website]['userID'];
					$arr['logID']   = date('Y-m-d H:i:s');
					$login->BuildAndRunInsertQuery('mechanic_mst',$arr);
					
					$topID++;
				}
			}
			
			$data['statusID'] = 2;
		}
		else	{$data['statusID'] = 0;}
	}
	
	if($request == 'GET_companylists')
	{
		if($_POST['counterID'] > 0)
		{
			$listsTX = '';
			
			for($startID = 1; $startID <= $_POST['counterID']; $startID++)
			{
				$Qry = $login->DB->prepare("SELECT ID, title, pscode FROM company WHERE status = 1 Order By title ASC ");
				if($Qry->execute())	
				{	
					$login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);

					$listsTX .= '<div style="display:inline-block; width: 351px; margin-top:5px;">';
					$listsTX .= '<select class="form-control" id="deportID_'.$startID.'" name="deportID_'.$startID.'">';
					$listsTX .= '<option value="0" selected="selected" disabled="disabled">-- Select Depots '.$startID.' --</option>';


					foreach($login->rows as $row)
					{
						$listsTX .= '<option value="'.$row['ID'].'">'.$row['title'].' - '.$row['pscode'].'</option>';
					}
					
					$listsTX .= '</select>';
					$listsTX .= '</div>';				
					$listsTX .= '<br />';
				}	
			}
			
			$data['listsTX'] = $listsTX;
		} 	
	}
	
	if($request == 'audit-date-check')
	{
		if($login->dateFormat($_POST['sDate']) >= $login->dateFormat($_POST['eDate']))
				{$data['countID'] = 0;}
		else	{$data['countID'] = 1;}
	}
		
	if($request == 'date-check')
	{
		if($login->dateFormat($_POST['rpt_dateID']) < $login->dateFormat($_POST['ins_dateID']))
				{$data['countID'] = 1;}
		else	{$data['countID'] = 0;}
	}
	
	if($request == 'GET_userroles_sheet')
	{
		$fileRT = '';
		
		
		$fileRT .= '<div class="row">';
		
		$fileRT .= '<div class="col-md-3" style="border:#4D4D4D 2px solid; background:#F56954; color:white; border-bottom:none;border-right:none;">'; 
			$fileRT .= '<h4 style="font-size: 18px; vertical-align: middle; font-family:georgia; text-align: center;">Already Assigned</h4>';
		$fileRT .= '</div>';
		
		$fileRT .= '<div class="col-md-9" style="border:#4D4D4D 2px solid; background:#F56954; color:white; border-bottom:none;">'; 
			$fileRT .= '<h4 style="font-size: 18px; vertical-align: middle; font-family:georgia; text-align: center;">Pending For Allocation</h4>';
        $fileRT .= '</div>';
		
        $fileRT .= '<div class="col-md-3" style="padding: 0px; overflow-y: scroll; border:#4D4D4D 2px solid; border-right:none; overflow-x: hidden; height: 550px; ">';
		
			$fileRT .= '<div class="box box-solid">';
			$fileRT .= '<div class="box-body">';
			$fileRT .= '<div class="box-group" id="accordion">';			
				$fileRT .= $FIndex->urolesSheets($_POST['uroleID']," <= 0 ",201);			
			$fileRT .= '</div>';
			$fileRT .= '</div>';
			$fileRT .= '</div>';
			
		$fileRT .= '</div>';
		
		$fileRT .= '<div class="col-md-9" style="overflow-y: scroll; border:#4D4D4D 2px solid; overflow-x: hidden; height: 550px; ">';
		
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"1,10","Settings",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"2","LOV",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"3","Masters",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"4","Employee",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"5","Driver Details",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"6","Rostering",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"7","All Set Reports",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"8","Driver Performance",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"9","Driver Signon",0);
			$fileRT .= $FIndex->urolesformsSheets($_POST['uroleID'],"11","Health & Safety",0);
        $fileRT .= '</div>';
		$fileRT .= '</div>';
		
		$data['formFields'] = $fileRT;
	}
	
	if($request == 'GetReportFields')
	{
		$RBuilders = array();
		
		$Qry = $login->DB->prepare("SELECT * FROM rbuilder WHERE frmID In(".($reqID == 11 ? 10 :($reqID == 12 ? 5 : '')).") Order By ID ASC ");
		if($Qry->execute())	
		{	
			$login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			foreach($login->rows as $row)
			{
				$RBuilders[$row['ID']] = $row['filedCP'];	
			}
			$data['RBuilders'] = $RBuilders;
		}	
	}
	
	if($request == 'Getform_fields')
	{
		$Fields = array();
		
		$Qry = $login->DB->prepare("SELECT * FROM rbuilder WHERE frmID = ".$reqID." Order By ID ASC ");								  
		if($Qry->execute())	
		{	
		  $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		  foreach($login->rows as $row)
		  {
			  $Fields[$row['ID']] = $row['filedCP'];	
		  }		  
		}
		$data['formFields'] = $Fields;
	}
	
	if($request == 'DBbakcup_log')
	{
		$countID = 0;
		$countID = $login->count_rows('dbbakcup_log', " WHERE dateID = '".date('Y-m-d')."' ");
		$data['countID']= ($countID > 0 ? $countID : 0);
	}
        
	if($request == 'UPDATE_BUSTYPE')
	{
            extract($_POST);    //$fileRT .= '<pre>'; echo print_r($_POST); exit;

            if($recID > 0 && $tagID == 1)
            {
                $arrayID = $recID > 0 ? $login->select('shift_masters_dtl',array("*"), " WHERE recID = ".$recID." ") : '';
				
				//echo print_r($arrayID);
				
                if($arrayID[0]['fID_019'] == 'Y' || $arrayID[0]['fID_019'] == 'y')
                {
                    $arr_A = array();
                    $arr_A['fID_20'] = $busTYPE;
                    $on_A['recID'] = $recID; 
                    $login->BuildAndRunUpdateQuery('shift_masters_dtl',$arr_A,$on_A);
                }

                if($arrayID[0]['fID_019'] == 'N' || $arrayID[0]['fID_019'] == 'n')
                {
                    $arr_B = array();
                    $arr_B['fID_20'] = $busTYPE;
                    $arr_B['fID_21'] = $busTYPE;
                    $on_B['recID'] = $recID;
                    $login->BuildAndRunUpdateQuery('shift_masters_dtl',$arr_B,$on_B);
                }
            }			
			else if($recID > 0 && $tagID == 2)
            {
				$arr_B = array();
				$arr_B['fID_21'] = $busTYPE;
				$on_B['recID'] = $recID;
				$login->BuildAndRunUpdateQuery('shift_masters_dtl',$arr_B,$on_B);
            }
			
			$data['success']= true;			
            /*echo json_encode(array('success'=>TRUE));*/
	}
 
	if($request == 'GET_EMP_FIGURES')
	{
		$arrayID = $login->select('employee',array("*"), " WHERE ID = ".$reqID." ");
		$arraySB = $arrayID[0]['sid'] > 0 ? $login->select('suburbs',array("*"), " WHERE ID = ".$arrayID[0]['sid']." ") : '';
		
		$data = array('code'=>$arrayID[0]['code'],'phone'=>$arrayID[0]['phone'],'phone_1'=>$arrayID[0]['phone_1']
		,'suburb'=>($arrayID[0]['address_1'].' , '.$arrayID[0]['suburb']),'suburbs'=>strtoupper($arraySB[0]['title'].' - '.$arraySB[0]['pscode']));
		$data['result']= $data;
	}
	
	if($request == 'GET_DayID')
	{
            $dateID = $login->dateFormat($reqID);
            $dayID = date('l',strtotime($dateID));

            $day = array();

            $day[($dayID == 'Monday' ? '1' :($dayID == 'Tuesday' ? '2' :($dayID == 'Wednesday' ? '3' :($dayID == 'Thursday' ? '4' :($dayID == 'Friday' ? '5' :($dayID == 'Saturday' ? '6' :($dayID == 'Sunday' ? '7' : '')))))))] = $dayID;

            $data['result']= $day;
	}
	
	if($request == 'CHECK_WeekStartDay' && !empty($reqID))
	{
		$fdateID = $login->dateFormat($reqID);
		$dateID = date('d-m-Y',strtotime($fdateID));
		$dayID  = date('l',strtotime($dateID));
		
		$lastID = date('d/m/Y',strtotime($fdateID.'+6Days'));
		
		if($dayID == 'Monday')	{$data = array('countID'=>0,'tdateID'=>$lastID);}
		else					  {$data = array('countID'=>1);}
	}
	
	if($request == 'Check_EmployeeCode' && !empty($reqID))
	{ 
		$countID = 0;
		$countID = $login->count_rows('employee', " WHERE code = ".$reqID." ");
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Shifts_Code' && !empty($reqID))
	{ 
            $countID = 0;
            $countID = $login->count_rows('shifts', " WHERE code = ".$reqID." ");
		
            $data['result'] = ($countID > 0 ? $countID : 0);
	}
		
	if($request == 'CalculateHours')
	{ 
		$totalID = strtotime(trim($timeID_2.':00')) - strtotime(trim($timeID_1.':00'));
		$hoursID = floor($totalID / 60 / 60);
		$minutID = round(($totalID - ($hoursID * 60 * 60)) / 60,2);
		
		$data['result'] = (($hoursID > 0) || ($minutID > 0)) <> '' ? (trim($hoursID.':'.$minutID)) : '00:00';
	}
	
	if($request == 'CalculatePerDayHours')
	{ 
		$times = array($timeID_1, $timeID_2);
		  $seconds = 0;
		  foreach ($times as $time)
		  {
			list($hour,$minute,$second) = explode(':', $time);
			$seconds += $hour*3600;
			$seconds += $minute*60;
			$seconds += $second;
		  }
		  $hours   = floor($seconds/3600);
		  $seconds -= $hours*3600;
		  $minutes  = floor($seconds/60);
		  $seconds -= $minutes*60;
		  
		$data['result'] = ($hours > 0) || ($minutes > 0) ? "{$hours}:{$minutes}" : "00:00";
	}
	
	if($request == 'CalculateWeeklyHours')
	{
		$setID = '';
		list($hour,$minute,$second) = explode(':', $hoursID);

		for($fdayIID >= 0; $fdayIID <= $tdayIID; $fdayIID++)
		{
			$seconds += $hour * 3600;
			$seconds += $minute * 60;
			$seconds += $second;
		}

		$hours    = floor($seconds / 3600);
		$seconds -= $hours * 3600;
		$minutes  = floor($seconds /60);
		$seconds -= $minutes * 60;

		$data['result'] = ($hours > 0) || ($minutes > 0) ? "{$hours}:{$minutes}" : "00:00";
	}
	
	if($request == 'CalculateSpecificDayHours')
	{
		$setID = '';
		$countID = count($fdayIID);
		
		list($hour,$minute,$second) = explode(':', $hoursID);
		
		if($countID > 0)
		{
			for($srdayID = 1; $srdayID <= $countID; $srdayID++)
			{
				$seconds += $hour * 3600;
				$seconds += $minute * 60;
				$seconds += $second;
			}
			
			$hours    = floor($seconds/3600);
			$seconds -= $hours*3600;
			$minutes  = floor($seconds/60);
			$seconds -= $minutes*60;
		}
		
		$data['result'] = $countID > 0 ? "{$hours}:{$minutes}" : "00:00";
	}
        
	echo json_encode($data);
?>