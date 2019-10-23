<?PHP
    include_once '../includes.php';

    $request  =	isset($_POST['request'])        ?	$_POST['request']	: '';	
    $reqID    =	isset($_POST['reqID'])		?	$_POST['reqID']         : '';	
    $reqID_1  =	isset($_POST['reqID_1'])	?	$_POST['reqID_1']	: '';	

    $data = array();
    
    if($request == 'TR_DUPLICACY')
    {
        $arrayEM = ($_POST['codeID'] <> '' ? $Index->select('employee',array("*"), " WHERE code = '".$_POST['codeID']."' ") : '');
        $data['countID'] = ($arrayEM[0]['ID'] > 0 ? $arrayEM[0]['ID'] : 0);
    }
	
    if($request == 'UPDATE_SHIFT_SETTERS' && $reqID > 0)
    {
        extract($_POST);
        
        $arrayST = ($reqID > 0 ? $login->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND ID = ".$reqID." AND statusID = 1 ") : '');	

        $return = 0;
        if($arrayST[0]['stypeID'] >= 1 && $arrayST[0]['stypeID'] <= 6)
        {
            $arrSH = ($reqID > 0 ? $login->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND availDT = '".$login->dateFormat($changeDT)."' AND companyID = ".$_SESSION[$login->website]['compID']." AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 ") : '');
            $return = $arrSH[0]['ID'] > 0 ? $arrSH[0]['ID'] : 0;
        }
        else {$return = 0;}
        
        $statusID = 0;
        if($changeDT <> '' && $return == 0)
        {
            $update = array();
            $update['lastDT']  = $currentDT;
            $update['availDT'] = $login->dateFormat($changeDT);
            $on['ID'] = $reqID;
            if($login->BuildAndRunUpdateQuery('shift_masters',$update,$on))
            {
                $fields = array();
                $fields['lastDT']  = $currentDT;
                $fields['availDT'] = $login->dateFormat($changeDT);
                $ons['ID'] = $reqID;
                if($login->BuildAndRunUpdateQuery('shift_masters_dtl',$fields,$ons))
                        {$statusID = 1;}
                else	{$statusID = 0;}
            }
        }

        $data['statusID'] = ($statusID > 0 ? $statusID :($return > 0 ? 2 : 0));
    }
	
    if($request == 'GET_SHIFT_SETTERS')
    { 	
		$login->permissions  = $login->GET_formPermissions($_SESSION[$login->website]['userRL'],'94');
		
		if($login->permissions['editID'] == 1 || $_SESSION[$login->website]['userTY'] == 'AD')
		{
			$arrayST = ($_POST['stypeID'] > 0 ? $login->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND stypeID = ".$_POST['stypeID']." AND statusID = 1 
			AND companyID In(".$_SESSION[$login->website]['compID'].") Order By ID DESC LIMIT 1 ") : '');

			if(!empty($arrayST[0]['createDT']) && ($arrayST[0]['createDT'] <> '0000-00-00' && ($arrayST[0]['createDT'] <> '01-01-1970')))
			{ 
				$stypeNM = $_POST['stypeID'] == 1 ? 'SIUO - School In University Out' 
						 :($_POST['stypeID'] == 2 ? 'SOUI - School Out University In'
						 :($_POST['stypeID'] == 3 ? 'SOUO - School Out University Out'
						 :($_POST['stypeID'] == 4 ? 'SIUI - School In University In'
						 :($_POST['stypeID'] == 5 ? 'School IN'
						 :($_POST['stypeID'] == 6 ? 'School OUT'
						 :($_POST['stypeID'] == 7 ? 'Saturday'
						 :($_POST['stypeID'] == 8 ? 'Sunday' 
						 :($_POST['stypeID'] == 9 ? 'Special Event' : ''))))))));

				$fileID .= '<div class="col-xs-11" style="border:#3C8DBC 2px solid; padding-top:15px; padding-bottom:15px; margin-left:35px;">';
					$fileID .= '<div class="col-xs-4"><label for="section">Day Type</label></div>';
					$fileID .= '<div class="col-xs-8">';
							$fileID .= '<input type="text" class="form-control" readonly="readonly" value="'.$stypeNM.'">';
					$fileID .= '</div>';

					$fileID .= '<div class="col-xs-12"><br /></div>';

					$fileID .= '<div class="col-xs-4"><label for="section">Version Date</label></div>';
					$fileID .= '<div class="col-xs-5">';
							$fileID .= '<input type="text" class="form-control" readonly="readonly" style="text-align:center;" value="'.($login->VdateFormat($arrayST[0]['createDT'])).'">';
					$fileID .= '</div>';

					$fileID .= '<div class="col-xs-12"><br /></div>';

					$fileID .= '<div class="col-xs-4"><label for="section">Applicable Date</label></div>';
					$fileID .= '<div class="col-xs-5">';
							$fileID .= '<input type="text" class="form-control" readonly="readonly" style="text-align:center;" value="'.($login->VdateFormat($arrayST[0]['availDT'])).'">';
					$fileID .= '</div>';

					$fileID .= '<div class="col-xs-12"><br /></div>';

					$fileID .= '<input type="hidden" id="currentDT_'.$_POST['stypeID'].'" value="'.$arrayST[0]['availDT'].'" /></td>';

					$fileID .= '<div class="col-xs-4"><label for="section">Change Date</label></div>';
					$fileID .= '<div class="col-xs-5">';
							$fileID .= '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" id="changeDT_'.$_POST['stypeID'].'" style="text-align:center;" placeholder="Change Date">';
					$fileID .= '</div>';

					$fileID .= '<div class="col-xs-12"><br /></div>';

					$fileID .= '<div class="col-xs-4"></div>';
					$fileID .= '<div class="col-xs-8">';
							$fileID .= '<input aria-sort="'.$_POST['stypeID'].'" aria-busy="'.$arrayST[0]['ID'].'" type="button" id="buttonST_'.$_POST['stypeID'].'" class="btn btn-flat btn-round btn-danger updateSetterID" value="Update Shift Date Info" />';
					$fileID .= '</div>';            
				$fileID .= '</div>';

				$fileID .= ($srID % 2 == 0) ? '' : '<div class="col-xs-1"></div>';

				$fileID .= ($srID % 2 == 0) ? '<div class="col-xs-12"><br /></div>' : '';

				$srID++; 
			}
			else
			{
				$fileID .= '<div class="col-xs-5" style="border:#F56954 2px solid; padding-top:15px; padding-bottom:15px; margin-left:35px;">';
						$fileID .= '<div class="col-xs-12"><label for="section">No Data, Available As per your specification</label></div>';
				$fileID .= '</div>';
			}
		}
		
        $data['records'] = $fileID;
    }
	
    if($request == 'GET_ALL_SHIFT_SETTERS')
    {
		$login->permissions  = $login->GET_formPermissions($_SESSION[$login->website]['userRL'],'94');
			
        $arrayCM = $login->select('company',array("*")," WHERE ID = ".$_SESSION[$login->website]['compID']." ");
        
        $fileID = '';
        
        $fileID .= '<div class="col-xs-12">';
        $fileID .= '<table id="dataTables" class="table table-bordered table-striped">';
        $fileID .= '<thead><tr>';
        $fileID .= '<th colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Day Type - Shift Setter</strong></div></th>'; 
        $fileID .= '</tr></thead>';
        $fileID .= '<thead><tr>';            
        $fileID .= '<th><div align="center"><strong>Sr. No.</strong></div></th>';        
        $fileID .= '<th><div align="center"><strong>Version Date</strong></div></th>';
        $fileID .= '<th><div align="center"><strong>Applicable Date</strong></div></th>';
        $fileID .= '<th><div align="center"><strong>&nbsp;</strong></div></th>';
        $fileID .= '<th><div align="center"><strong>&nbsp;</strong></div></th>'; 
        $fileID .= '</tr></thead>';

        $styleID = '';
        
        $statusID = 1;
        
        //$fileID .= '<div style="position: relative; height:400px; overflow-y: scroll; overflow-x: scroll;">';
        foreach((explode(",",$arrayCM[0]['stypeID'])) as $stypesID)
        {
            if($stypesID > 0)
            {
                $srID = 1;
                $Qry = $login->DB->prepare("SELECT * FROM shift_masters WHERE usedBY = 'A' AND stypeID = ".$stypesID." AND statusID = 1 AND companyID In(".$_SESSION[$Index->website]['compID'].") Order By availDT ASC ");
                $Qry->execute();
                $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                if(is_array($login->rows) && count($login->rows) > 0)
                {
                    $fileID .= '<tr>';
                        $fileID .= '<td colspan="5" style="background:#367FA9; color:white;"><b>'.($stypesID == 1 ? 'SIUO - School In University Out'   :($stypesID == 2 ? 'SOUI - School Out University In' :($stypesID == 3 ? 'SOUO - School Out University Out' :($stypesID == 4 ? 'SIUI - School In University In' :($stypesID == 5 ? 'School IN' :($stypesID == 6 ? 'School OUT' :($stypesID == 7 ? 'Saturday'  :($stypesID == 8 ? 'Sunday' :($stypesID == 9 ? 'Special Event'  : ''))))))))).'</b></td>';
                    $fileID .= '</tr>';
                    
                    foreach($login->rows as $rows)
                    {
                        $styleID = ($srID % 2 == 0 ? 'style="background:white;"' : 'style="background:#DDEBF7;"');
                        
                        $fileID .= '<tr>';
                            $fileID .= '<td align="center" '.($styleID).'><b>'.$srID.'</b></td>';
                            $fileID .= '<td '.($styleID).' align="center">'.($login->VdateFormat($rows['createDT'])).'</td>';
                            $fileID .= '<td '.($styleID).' align="center">'.($login->VdateFormat($rows['availDT'])).'</td>';
							
                            if($login->permissions['delID'] == 1 || $_SESSION[$login->website]['userTY'] == 'AD')
                            {
                                $fileID .= '<td '.($styleID).' align="center"><a aria-sort="'.$rows['ID'].'" class="fa fa-trash-o delete_setter_log" style="text-decoration:none; cursor:pointer;"></a></td>';
                            }
                            else
                            {
                                $fileID .= '<td></td>';
                            }

                            $fileID .= '<td '.($styleID).' align="center"><a target="_blank" class="fa fa-print" href="'.$login->home.'rpts-c/rpt_shift_setter.php?i='.$login->Encrypt($rows['ID']).'" style="text-decoration:none; cursor:pointer;"></a></td>';
                        $fileID .= '</tr>';

                        $srID++;
                        $statusID++;
                    }
                }
            }
        }
        
        //$fileID .= '</div>';
            
        if($statusID > 1)   {}  else
        {
            $fileID .= '<tr><td colspan="6"><b style="color:red;">No Shifts Data, Available</b></td></tr>';
        }

        $fileID .= '<tr height="35"><td colspan="6" style="background:white;"></td></tr>';

        $fileID .= '</table>';
        $fileID .= '</div>';

        $data['records'] = $fileID;
    }

    if($request == 'GET_OFFENCE_DETAILS')
    {
        $masters = array();
        $Qry = $Index->DB->prepare("SELECT * FROM offence WHERE ID > 0 AND typeID = ".$reqID." Order By title ASC");		
        if($Qry->execute())
        {
            $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $masters .= '<option value="0" selected="selected" disabled="disabled"> --- Select Offence Details --- </option>';
            foreach($Index->crow as $mrow)	
            {
                $masters .= '<option value="'.$mrow['ID'].'">'.$mrow['title'].'</option>';
            }
        }		
        $data['result'] = $masters;
    }

	if($request == 'GET_StopsPoints')
    {
        $masters = array();
        $Qry = $Index->DB->prepare("SELECT stopID FROM srvdtls_stops WHERE recID > 0 AND serviceID In(".$_POST['serviceID'].") AND statusID > 0  Order By orderID ASC ");		
        if($Qry->execute())
        {
            $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $masters .= '<option value="0" selected="selected" disabled="disabled"> --- Select Route Stop Point --- </option>';
            foreach($Index->crow as $mrow)	
            {
				$arrMS = $mrow['stopID'] > 0 ? $Index->select('stops',array("*"), " WHERE ID = ".$mrow['stopID']." ") : '';
                $masters .= '<option value="'.$mrow['stopID'].'">'.$arrMS[0]['title'].'</option>';
            }
        }		
        $data['result'] = $masters;
    }

    if($request == 'GET_Contracts_Agst_Contractor')
    {
            $masters = array();

            $Qry = $Index->DB->prepare("SELECT contractID FROM cnserviceno WHERE ID > 0 AND contractorID = ".$reqID." AND companyID In(".$_SESSION[$Index->website]['compID'].") Group By contractID Order By contractID ASC");		
            if($Qry->execute())
            {
                    $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    $masters .= '<option value="0" selected="selected" disabled="disabled"> --- Select Contract Name --- </option>';
                    foreach($Index->crow as $mrow)	
                    {
                        $arrMS = $mrow['contractID'] > 0 ? $Index->select('master',array("*"), " WHERE ID = ".$mrow['contractID']." ") : '';

                        $masters .= '<option value="'.$mrow['contractID'].'">'.$arrMS[0]['title'].'</option>';
                    }
            }		
            $data['result'] = $masters;
    }

    if($request == 'GET_ServicenoTimePoints')
    {
            $masters = array();
            //AND contractID = ".$reqID_1."
            
            $Qry = $Index->DB->prepare("SELECT * FROM cstpoint_dtl WHERE recID > 0  AND serviceID = ".$reqID." Order By recID ASC");		
            if($Qry->execute())
            {
                $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $masters .= '<option value="0" selected="selected" disabled="disabled"> --- Select Time Point --- </option>';
                foreach($Index->crow as $mrow)	
                {
                    $masters .= '<option value="'.$mrow['recID'].'">'.$mrow['fileID_1'].'</option>';
                }
            }		
            $data['result'] = $masters;
    }

    if($request == 'GET_Serviceno_Agst_ContractID')
    {
            $masters = array();

            $Qry = $Index->DB->prepare("SELECT * FROM cnserviceno WHERE ID > 0 AND contractID = ".$reqID." AND companyID In(".$_SESSION[$Index->website]['compID'].") Order By ID ASC");
            if($Qry->execute())
            {
                    $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    $masters .= '<option value="0" selected="selected" disabled="disabled"> --- Select Service No --- </option>';
                    foreach($Index->crow as $mrow)	
                    {
                            if($mrow['serviceID'] <> '')
                            {
                                    $mrowID = explode(",",$mrow['serviceID']);

                                    foreach($mrowID as $lastID)
                                    {
                                            $EM_Array = $lastID > 0 ? $Index->select('srvdtls',array("*"), " WHERE ID = ".$lastID." ") : '';

                                            $masters .= '<option aria-sort="'.$EM_Array[0]['title'].'" value="'.$lastID.'">'.$EM_Array[0]['codeID'].'</option>';		
                                    }
                            }				
                    }
            }		
            $data['result'] = $masters;
    }
	
    echo json_encode($data);	
?>