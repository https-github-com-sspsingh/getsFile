<?PHP
    include_once '../includes.php';

    $reqID     =    isset($_POST['ID'])         ?   $_POST['ID']        : '' ;
    $frmID     =    isset($_POST['frmID'])      ?   $_POST['frmID']     : '' ;
    $request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;

	if($request == 'POPUP_fieldsID')
    {
		$file = '';
		$file  = '<div class="box box-primary" style="margin:auto">';
		$file .= '<div class="row">&nbsp;</div>';
		$file .= '<div class="row" style="margin:auto">';

		if($frmID == 37)	/* EMPLOYEE - FORM */
		{ 
			$retID = $AIndex->form_Employee($reqID);
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 41)	/* INCIDENT - FORM */
		{ 
			$retID = $AIndex->form_Incident($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 40)	/* COMMENT-LINE - FORM */
		{ 
			$retID = $AIndex->form_CommentLine($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 42)	/* ACCIDENTS - FORM */
		{ 
			$retID = $AIndex->form_Accident($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 43)	/* INFRINGMENTS - FORM */
		{ 
			$retID = $AIndex->form_Infringment($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 44)	/* INSPECTION - FORM */
		{ 
			$retID = $AIndex->form_Inspection($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 45)	/* MANAGER - COMMENTS */
		{ 
			$retID = $AIndex->form_ManangerComments($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 131)	/* HIZ - REGISTER */
		{ 
			$retID = $AIndex->form_HealthSafetyEnvironmental($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		if($frmID == 130)	/* SIR - REGISTER */
		{ 
			$retID = $AIndex->form_SystemImporvmentRequest($reqID);				
			$file .= $retID <> '' ? $retID : '<div class="col-xs-3"></div><div class="col-xs-9"><b style="color:green;">No, Fields are Missing.</b></div>';
		}
		
		$file .= '</div>';
		$file .= '<br />';
		$file .= '</div>';
		$file .= '</div>';
		
		$arr = array('file_info'=>$file);
		echo json_encode($arr); 
    }
	
    if($request == 'POPUP_uslogsID' && !empty($frmID) && !empty($reqID))
    {
        $file = '';

        if($frmID == 'TR-LOG')
        {
            $file  = '<div class="box box-primary" style="margin:auto">';
            $file .= '<div class="row">&nbsp;</div>';
            $file .= '<div class="row" style="margin:auto">';

            $file .= $SIndex->employee_transfer_logs($reqID);

            $file .= '</div>';

            $arr = array('file_info'=>$file);
            echo json_encode($arr);
        }
        else
        {
            $file  = '<div class="box box-primary" style="margin:auto">';
            $file .= '<div class="row">&nbsp;</div>';
            $file .= '<div class="row" style="margin:auto">';

            $file .= $SIndex->uslogs_info($frmID,$reqID);

            $file .= '</div>';

            $arr = array('file_info'=>$file);
            echo json_encode($arr);
        }
    } 

    if($request == 'INCIDENT_API_LOGS' && !empty($reqID))
    {
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $Qry = $login->DB->prepare("SELECT * FROM api_senders_logs WHERE refID = ".$reqID." Order By typeID,dateID,timeID DESC ");
        $Qry->execute();
        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
        $Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
        if(is_array($login->rows) && count($login->rows) > 0)
        {
            $file .= '<table id="dataTable" class="table table-bordered ">';				
            $file .= '<thead><tr><th '.$Set.' colspan="6">API - Log - Infos</th></tr></thead>';

            $file .= '<thead><tr>';
            $file .= '<th '.$Set.'>Sr. No.</th>';
            $file .= '<th '.$Set.'>API</th>';
            $file .= '<th '.$Set.'>User Name</th>';
            $file .= '<th '.$Set.'>Status</th>';
            $file .= '<th '.$Set.'>Action Date</th>';
            $file .= '<th '.$Set.'>Action Time</th>';
            $file .= '</tr></thead>'; 

            $srID = 1;
            foreach($login->rows as $row)			
            { 
                $US_Array = ($row['userID'] > 0 ? $login->select('users',array("*"), " WHERE ID = ".$row['userID']." ") : '');

                $file .= '<tr>'; 
                    $file .= '<td align="center">'.$srID++.'</td>';  
                    $file .= '<td align="center"><b>'.$row['typeID'].'</b></td>';
                    $file .= '<td align="center">'.$US_Array[0]['username'].'</td>';  
                    $file .= '<td><b>'.$row['statusTX'].'</b></td>';
                    $file .= '<td align="center">'.$login->VdateFormat($row['dateID']).'</td>';  
                    $file .= '<td align="center">'.$row['timeID'].'</td>'; 
                $file .= '</tr>';
            } 
        }

        $file .= '</div>';

        $arr = array('file_info'=>$file);
        echo json_encode($arr); 
    }        
?>