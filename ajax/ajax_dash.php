<?PHP
    include_once '../includes.php';
    
    $request   =    isset($_POST['request'])    ?	$_POST['request']   : '' ;	
    $dateID    =    isset($_POST['dateID'])     ?	$_POST['dateID']    : '' ;
    $yrID      =    isset($_POST['yrID'])	?	$_POST['yrID']      : '' ;
    
    if($request == 'GET_WEEKLY_ROSTER')
    {
        $arr = array();

        $FL_Array = $Index->select('w_shifts',array("*"), " Order By ID DESC LIMIT 1 ");
        $dateID_1 = $dateID;

        $file = '';

        $styleID = 'style="color:white;background:#317299;text-align:center;"'; 
        $file .= '<table id="dataTables" class="table table-bordered table-striped">'; 
        $file .= '<thead><tr>';
        $file .= '<th '.$styleID.' colspan="11">Weekly Roster Sheet 
        (From : '.date('d-M-Y',strtotime($dateID_1)).' / To : '.date('d-M-Y',strtotime($dateID_1.'+6Days')).')</th>';
        $file .= '</tr></thead>'; 
        $file .= '<thead><tr>';
        $file .= '<th '.$styleID.'>Sr. No.</th>';
        $file .= '<th '.$styleID.'>Emp - ID</th>';
        $file .= '<th '.$styleID.'>Emp - Name</th>';
        for($srID = 0; $srID < 7; $srID++)
        {
                $dateID = strtotime($dateID_1.'+'.$srID.'Days');
                $file .= '<th '.$styleID.'>'.date('D',$dateID).'<br />'.date('d-M-Y',$dateID).'</th>';
        }
        $file .= '</tr></thead>';

        $Qry = $Index->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$FL_Array[0]['ID']." AND sdateID = '".$dateID_1."' Order By recID ASC ");
        $Qry->execute();
        $Index->result = $Qry->fetchAll(PDO::FETCH_ASSOC);

        $counterID = 1;
        if(is_array($Index->result) && (count($Index->result) > 0))
        {
            $scodeID = '';
            foreach($Index->result as $rows)
            {
                $EMPL = $Index->select('employee',array("*"), " WHERE ID = ".$rows['empID']." ");

                $SH_1 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_1']." ");
                $SH_2 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_2']." ");
                $SH_3 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_3']." ");
                $SH_4 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_4']." ");
                $SH_5 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_5']." ");
                $SH_6 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_6']." ");
                $SH_7 = $Index->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_7']." ");

                $file .= '<tr>';
                $file .= '<td align="center">'.$counterID++.'</td>';
                $file .= '<td align="center">'.$EMPL[0]['code'].'</td>';
                $file .= '<td>'.strtoupper($EMPL[0]['fname'].' '.$EMPL[0]['lname']).'</td>';

                $file .= '<td align="center">'.$SH_1[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_2[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_3[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_4[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_5[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_6[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_7[0]['code'].'</td>';
                $file .= '</tr>';
            }
        }

        $file .= '</table>';

        $arr = array('wrosterID'=>$file);	
        echo json_encode($arr);	
    }

    if($request == 'GET_DASHBOARDS')
    {
        $arr = array();
        $optionID_1 = array();	$optionID_2 = array();	$optionID_3 = array();	$optionID_4 = array();	$optionID_5 = array();

        /* Full Time */
        $countID = 0;
        $countID_1 = $login->count_rows('employee'," WHERE desigID In(9,209) AND status = 1 AND casualID = 1 AND companyID In (".$_SESSION[$Index->website]['compID'].") ");
        $optionID_1[] = array('titleID'=>'Full Time - Drivers','countID'=>$countID_1);

        $countID_2 += $login->count_rows('employee'," WHERE desigID In(9,209) AND status = 1 AND casualID = 2 AND companyID In (".$_SESSION[$Index->website]['compID'].") ");
        $optionID_1[] = array('titleID'=>'Part Time - Drivers','countID'=>$countID_2);

        $countID_3 += $login->count_rows('employee'," WHERE desigID In(9,209) AND status = 1 AND casualID = 3 AND companyID In (".$_SESSION[$Index->website]['compID'].") ");
        $optionID_1[] = array('titleID'=>'Casual - Drivers','countID'=>$countID_3);

        $countID_4 += $login->count_rows('employee'," WHERE status = 1 AND (desigID = 9 <> desigID = 209) AND companyID In (".$_SESSION[$Index->website]['compID'].") ");
        $optionID_1[] = array('titleID'=>'Other - Drivers','countID'=>$countID_4);
        
        /* SIGN On Graphs */
        $signonID_1 += $login->count_rows('imp_shift_daily'," WHERE dateID = '".date('Y-m-d')."' AND companyID In (".$_SESSION[$Index->website]['compID'].") AND cuttoffID <= 0 ");
        $optionID_5[] = array('titleID'=>'Total Drivers','countID'=>$signonID_1);
        
        $signonID_2 += $login->count_rows('imp_shift_daily'," WHERE dateID = '".date('Y-m-d')."' AND companyID In (".$_SESSION[$Index->website]['compID'].") AND cuttoffID <= 0 AND colorID <= 0 AND statusID = 1 ");
        $optionID_5[] = array('titleID'=>'On Time SignOn','countID'=>$signonID_2);
        
        $signonID_3 += $login->count_rows('imp_shift_daily'," WHERE dateID = '".date('Y-m-d')."' AND companyID In (".$_SESSION[$Index->website]['compID'].") AND cuttoffID <= 0 AND colorID = 1 ");
        $optionID_5[] = array('titleID'=>'Late SignOn','countID'=>$signonID_3);
        
//            for($Start = 1; $Start <= 3; $Start++) /* EMPLOYEE - STATUS */
//            {
//                $countID = $login->count_rows('employee'," WHERE desigID In(9,209) AND status = 1 AND casualID = ".$Start." AND companyID In (".$_SESSION[$Index->website]['compID'].") ");
//                $countID = $countID > 0 ? $countID : '0';
//
//                $estatusID = $Start == 1 ? 'Full Time - Drivers' :($Start == 2 ? 'Part Time - Drivers' :($Start == 3 ? 'Casual - Drivers' :($Start == 4 ? 'Other' : 'Other')));
//
//                $optionID_1[] = array('titleID'=>$estatusID,'countID'=>$countID);
//            } 

//		$Qry = $Index->DB->prepare("SELECT * FROM stype Order By ID ASC ");
//		$Qry->execute();
//		$Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
//		if(is_array($Index->rows) && (count($Index->rows) > 0))
//		{
//			foreach($Index->rows as $rows)
//			{
//				$countID = $login->count_rows('shifts'," WHERE stypeID = ".$rows['ID']." ");
//				$countID = $countID > 0 ? $countID : '0';
//				
//				$AH_Arry = $login->select('stype',array("*")," WHERE ID = ".$rows['ID']." ");
//				
//				$estatusID = $AH_Arry[0]['title'];
//				$optionID_2[] = array('titleID'=>$estatusID,'countID'=>$countID);			
//			}
//		}

//		$Qry = $Index->DB->prepare("SELECT frmID FROM uslogs WHERE dateID = '".date('Y-m-d')."' AND companyID In(".$_SESSION[$Index->website]['compID'].") Group By frmID Order By frmID ASC ");
//		$Qry->execute();
//		$Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
//		if(is_array($Index->rows) && (count($Index->rows) > 0))
//		{
//                    $AcountID = 0;	$EcountID = 0;	$DcountID = 0;
//                    foreach($Index->rows as $rows)
//                    {
//                        $AcountID = $login->count_rows('uslogs'," WHERE frmID = ".$rows['frmID']." AND dateID = '".date('Y-m-d')."' AND companyID In(".$_SESSION[$Index->website]['compID'].") AND actionID = 'A' ");
//                        $AcountID = $AcountID > 0 ? $AcountID : '0';
//
//                        $EcountID = $login->count_rows('uslogs'," WHERE frmID = ".$rows['frmID']." AND dateID = '".date('Y-m-d')."' AND companyID In(".$_SESSION[$Index->website]['compID'].") AND actionID = 'E' ");
//                        $EcountID = $EcountID > 0 ? $EcountID : '0';
//
//                        $DcountID = $login->count_rows('uslogs'," WHERE frmID = ".$rows['frmID']." AND dateID = '".date('Y-m-d')."' AND companyID In(".$_SESSION[$Index->website]['compID'].") AND actionID = 'D' ");
//                        $DcountID = $DcountID > 0 ? $DcountID : '0';
//
//                        $FR_Array = $login->select('frmset',array("*")," WHERE ID = ".$rows['frmID']." ");
//                        $frmNM = $FR_Array[0]['title'];
//                        $optionID_4[] = array('frmID'=>($frmNM),'country'=>($FIndex->GET_FirstCodes($frmNM)),'A'=>$AcountID,'E'=>$EcountID,'D'=>$DcountID);			
//                    }
//		}


//		$query = $Index->DB->prepare("CALL CHART_DATA_TR ('".($yrID)."' , '".($_SESSION[$Index->website]['compID'])."') ");		
//		$query->execute();
//		$Index->rows = $query->fetchAll(PDO::FETCH_ASSOC);	
//		if(is_array($Index->rows) && count($Index->rows) > 0) 
//		{
//			foreach($Index->rows as $row)
//			{
//                          $optionID_3[] = array('dateID'=>$row['serDT'],'Compliment'=>$row['cmpID'],'Complaint'=>$row['clnID'],'Accident'=>$row['actID'],'Incident'=>$row['incID'],'Infringement'=>$row['infID'],'Inspection'=>$row['insID']);
//			}
//		}

            $arr = array('optionID_1'=>$optionID_1,'optionID_2'=>$optionID_2,'optionID_3'=>$optionID_3,'optionID_4'=>$optionID_4,'optionID_5'=>$optionID_5);
            echo json_encode($arr);
    }
	
?>