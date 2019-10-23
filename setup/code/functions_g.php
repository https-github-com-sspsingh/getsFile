<?PHP
class GFunctions extends CFunctions
{
    function __construct()
    {	
        parent::__construct();
        $this->companyID = $_SESSION[$this->website]['compID'];
    }
	
    public function GET_SpareBusesNO($dateID,$companyID)
    {
        $arrID  = $this->select('imp_shift_daily',array("*"), " WHERE dateID >= '".$this->dateFormat($dateID)."' AND companyID = ".$companyID." AND statusID = 2 Order By dateID ASC LIMIT 1 ");
        
        if($arrID[0]['dateID'] <> '')
        {
            $SQL = "SELECT * FROM imp_shift_daily WHERE recID > 0 AND dateID = '".$arrID[0]['dateID']."' AND companyID = ".$companyID." AND statusID = 2 Order By tagCD ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $srID = 1;
                foreach($this->row as $rows)
                {
                    $arrayBS  = (int)$rows['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".((int)$rows['fID_014'])." ") : '';
                    
                    if((int)$rows['fID_014'] > 0 && ($arrayBS[0]['busno'] <> ''))   {$return .= " AND busno <>  '".$arrayBS[0]['busno']."'";}
                    else if($rows['fID_14'] <> '')
                    {
                        $return .= " AND busno <>  '".$rows['fID_14']."'";                        
                        $srID++;
                    }
                }
            }
        }
        return $return;
    }
	
    public function DriverSignOnSheets($fdateID,$filterID,$statusID,$duplicacyEMP,$duplicacyBUS)
    {
        $fileID = '';	//echo '<pre>'; echo  print_r($duplicacyEMP);	echo '<pre>'; echo  print_r($duplicacyBUS);
		
        $passID = "";
        $passID = ($statusID == 2 ? " AND (If((Concat(imp_shift_daily.fID_14, '', imp_shift_daily.fID_014)) <> '', imp_shift_daily.statusID, 2) = ".$statusID." Or If((Concat(imp_shift_daily.fID_13, '', imp_shift_daily.fID_18)) <> '', imp_shift_daily.statusID, 2) = ".$statusID.") " : 
                                    " AND (If((Concat(imp_shift_daily.fID_14, '', imp_shift_daily.fID_014)) <> '', imp_shift_daily.statusID, 2) In(0,1) AND If((Concat(imp_shift_daily.fID_13, '', imp_shift_daily.fID_18)) <> '', imp_shift_daily.statusID, 2) In(0,1)) ");
								   
        $crtID = "";
        $crtID = ($filterID <> '' ? " AND imp_shift_daily.companyID In (".$filterID.") " : " ");	//AND statusID = ".$statusID."
        $arrID  = $this->select('imp_shift_daily',array("dateID"), " WHERE dateID >= '".$fdateID."' ".$passID." ".$crtID." Order By dateID ASC LIMIT 1 ");
        
        if($arrID[0]['dateID'] == date('Y-m-d'))
        {
			$SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = 
            '".$arrID[0]['dateID']."' ".$passID." ".$crtID." AND (If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9)) <> '' AND imp_shift_daily.status_ynID = 1 AND imp_shift_daily.imp_statusID In(1) ORDER BY 
            Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_3, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_10,'')))) ".($statusID == 2 ? "ASC" : "DESC ");
            $Qry = $this->DB->prepare($SQL);
            $Qry->execute();
			
			$dragCL = 'class="redips-mark"';
			$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);			
			$fileID .= '<table id="table1" class="table table-bordered table-striped">';                
			$fileID .= '<thead><tr>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>DEPOT</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>SHIFT ID</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>ON</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>EX Depot</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>OFF</strong></div></th>';			
			$fileID .= '<th style="background:#367FA9; color:white;" '.($statusID == 2 ? 'colspan="2"' : '').'><div align="center"><strong>STAFF NAME</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.' '.($statusID == 2 ? 'colspan="2"' : '').'><div align="center"><strong>BUS NO</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>BUS TYPE</strong></div></th>'; 
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>SHIFT COMMENTS</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>ON ROAD C/O</strong></div></th>';
			$fileID .= $statusID == 1 ? '<th style="background:#367FA9; color:white;"><div align="center"><strong>SIGN ON TIME</strong></div></th>' : '';
			$fileID .= '<th style="background:#367FA9; color:white;" '.$dragCL.'><div align="center"><strong>STOW TIME</strong></div></th>';
			$fileID .= '</tr></thead>';
			if(is_array($this->row) && count($this->row) > 0)
			{
				$srID = 1;	$styleID = '';	$thumbID = '';  $row_colID = '';	$textBUS = '';	$textEMP = '';	$empNAME = '';	$empTHUMB = '';
				$empID = 0; $emp_reqID = 0;	$busID = 0; $bus_reqID = 0;	$responseID = '';   $pasteID = '';	$setEMP = 0;	$setBUS = 0;				
				//$countID += count($this->row);				
				foreach($this->row as $rows)
				{
					$arrayBS  = $rows['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fID_014']." ") : '';
					
					/* match employee */
					foreach(explode(",",$duplicacyEMP) as $dataEmpID)
					{
						if($dataEmpID == ($rows['fID_018'] > 0  ? $rows['fID_018']  : $rows['fID_013']))
						{
							$setEMP  = 1;
							$textEMP = '<b style="float:right; color:red;"> R</b>';
							break;
						}	else	{$setEMP = 0;	$textEMP = '';}
					}
					
					/* match buses no */
					foreach(explode(",",$duplicacyBUS) as $dataBusID)
					{
						if($dataBusID == ($rows['fID_014'] <> '' ? $arrayBS[0]['busno'] : $rows['fID_14']))
						{
							$setBUS  = 1;
							$textBUS = '<b style="float:right; color:red; margin-top: 49px;"> R</b>';
							break;
						}	else	{$setBUS = 0;	$textBUS = '';}
					}
					
					$arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE ID = ".$rows['shiftID']." AND recID = ".$rows['shift_recID']." Order By recID DESC ");					
					$pasteID = (strtoupper($arrayRT[0]['fID_019']) == 'N' ? 'style="background:#FFF2F9; vertical-align: middle;"' : 'style="background:white; vertical-align: middle; "');
					$row_colID = ((empty($rows['fID_13']) && empty($rows['fID_18'])) || (empty($rows['fID_14']) && empty($rows['fID_014'])) && ($rows['statusID'] == 1) ? 'style="background:yellow !important; color:black !important; vertical-align: middle;"' : '');
					
					if($rows['fID_018'] > 0)    {$emp_reqID = 2; $empID = $rows['fID_018'];}    else    {$emp_reqID = 1;	 $empID = $rows['fID_013'];}
					if($rows['fID_014'] > 0)    {$bus_reqID = 2; $busID = $rows['fID_014'];}    else    {$bus_reqID = 1;     $busID = $rows['fID_14'];}
					
					$arrayID  = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';                        
					$arraySB = $arrayID[0]['sid'] > 0 ? $this->select('suburbs',array("*"), " WHERE ID = ".$arrayID[0]['sid']." ") : '';
					$arrayCM = $rows['companyID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$rows['companyID']." ") : '';
					
					if($rows['choppedID'] == 1)
					{
						$empNAME  = (strtoupper('<b style="color:red; font-weight:bold;">0000 - CHOPPED </b>.'));
						$empTHUMB = '0000 - CHOPPED';
					}
					else
					{
						$empNAME  = (strtoupper('<b style="color:#9C2A4D; font-weight:bold;">'.$arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'].'</b> , ('.$arraySB[0]['title'].' - '.$arraySB[0]['pscode'].') <br />'.$arrayID[0]['phone'].''.(strlen(str_replace('-','',$arrayID[0]['phone_1'])) > 0 ? ','.$arrayID[0]['phone_1'] : '').' '));
						$empTHUMB = (strtoupper($arrayID[0]['fname'].' '.$arrayID[0]['lname']));
					}
					 

					if($rows['statusID'] == 2)
					{
						$styleID = ((strtotime(date('G:i')) >= strtotime(($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] :($rows['tagCD'] == 'B' ? $arrayRT[0]['fID_9'] : '')))) ? 'style="background:#E10000 !important; color:#FFF !important; vertical-align: middle;"' 
								 :((strtotime(date('G:i')) >= strtotime($this->TimeAddMinues((($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] :($rows['tagCD'] == 'B' ? $arrayRT[0]['fID_9'] : ''))),'-5'))) == 2 ? 'style="background:#F24F00 !important; color:#FFF !important; vertical-align: middle;"' : $pasteID));
					}
					else
					{
						$styleID = ((strtotime($rows['singinID']) >= strtotime(($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] :($rows['tagCD'] == 'B' ? $arrayRT[0]['fID_9'] : '')))) ? 'style="background:#E10000 !important; color:#FFF !important; vertical-align: middle;"' 
								 :((strtotime($rows['singinID']) >= strtotime($this->TimeAddMinues((($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] :($rows['tagCD'] == 'B' ? $arrayRT[0]['fID_9'] : ''))),'-5'))) == 2 ? 'style="background:#F24F00 !important; color:#FFF !important; vertical-align: middle;"' : $pasteID));
					}
					
					$thumbID = ($rows['statusID'] == 2 ? 'up' :($rows['statusID'] == 1 ? 'down' : 'up'));
					$responseID = $bus_reqID == 2 ? strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno']) : $rows['fID_14'];
					

				if(!($rows['tagCD'] == 'B' && $arrayRT[0]['fID_019'] == 'N' && (strtotime(date('G:i')) >= strtotime(($arrayRT[0]['fID_9'])))))
				{
					$countID++;
					
					/* BEGIN : Print-DataTable */						
					$fileID .= '<tr id="'.$rows['recID'].'">';
						$fileID .= '<td '.$dragCL.' '.$row_colID.' '.$styleID.' align="center">'.$arrayCM[0]['title'];
						$fileID .= '<br /><a class="fa fa-thumbs-'.$thumbID.' shifts_doneID" style="cursor:pointer; margin-top:22px; text-decoration:none;" aria-sort="'.$rows['recID'].'" aria-label="'.$rows['fID_1'].'" aria-title="'.$empTHUMB.'" aria-busy="'.$rows['statusID'].'"></a>';
						$fileID .= '</td>';

						$fileID .= '<td '.$dragCL.' '.$row_colID.' '.$styleID.' align="center">'.$rows['fID_1'].' <br />'.$rows['tagCD'].'</td>';

						$fileID .= '<td '.$dragCL.' '.$row_colID.' '.$styleID.' align="center">'.($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9']).'</td>';
						$fileID .= '<td '.$dragCL.' '.$row_colID.' '.$pasteID.' align="center">'.($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_3'] : $arrayRT[0]['fID_10']).'</td>';
						$fileID .= '<td '.$dragCL.' '.$row_colID.' '.$pasteID.'  align="center">'.($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_7'] : $arrayRT[0]['fID_12']).'</td>';
						$fileID .= '<td '.$pasteID.' '.($statusID == 2 ? 'class="metup" aria-sort="'.($rows['recID'].'_'.$rows['fID_1'].'_'.$rows['fID_013']).'" id="'.$rows['recID'].'"' : '').'><div '.($statusID == 2 ? 'style="border: 2px grey solid; border-style: outset;border-radius: 5px;padding: 5px;"' : 'style="border: 2px groove #C6C6FB; border-radius: 5px; padding: 5px;"').' id="link1" class="redips-drag t1" aria-sort="'.$rows['recID'].'">';

						$fileID .= '<input type="hidden" value="'.$rows['recID'].'" name="recID" />';
						$fileID .= $empNAME.$textEMP;
						$fileID .= '</div></td>';

						if($statusID == 2)				
						{
							$fileID .= '<td align="center" '.$dragCL.' '.$pasteID.'>';
								if($rows['choppedID'] == 1)
								{
									$fileID .= '<a class="fa fa-undo chopped_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Chopped Staff" aria-sort="'.$rows['recID'].'"></a>';
								}
								else
								{
									if((int)$rows['fID_018'] > 0)   {$fileID .= '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Spare Staff" aria-sort="EMPLOYEE_'.$rows['dateID'].'_'.$emp_reqID.'_'.$empID.'_'.$rows['recID'].'"></a>';}
									else                            {$fileID .= '<a class="fa fa-th swipe_modelID"  style="text-decoration:none;cursor:pointer;" title="Spare Staff" aria-sort="EMPLOYEE_'.$rows['recID'].'_'.$emp_reqID.'"></a>';}
								}
							$fileID .= '</td>';
						}
						
						/* BUS NO - DETAILS */
						$fileID .= '<td align="center" '.$dragCL.' '.$pasteID.'>';
							if(!empty($responseID))		{$fileID .= ($bus_reqID == 2 ? strtoupper($arrayBS[0]['busno']) : strtoupper($rows['fID_14'])).$textBUS;}
							else                        {$fileID .= '<input type="text" style="width:120px;" maxlength="4" class="form-control" onkeydown="updateMASTERSoptions(this.value,'.$rows['recID'].',1)" onkeyup="updateMASTERSoptions(this.value,'.$rows['recID'].',1)" />';}
						$fileID .= '</td>';
						
						if($statusID == 2)
						{
						$fileID .= '<td align="center" '.$dragCL.' '.$pasteID.'>';
							if((int)$rows['fID_014'] > 0)   {$fileID .= '<a class="fa fa-undo swipe_undoID"  style="text-decoration:none;cursor:pointer;" title="Undo Spare Bus" aria-sort="BUSES_'.$rows['dateID'].'_'.$bus_reqID.'_'.$busID.'_'.$rows['recID'].'"></a>';}
							else                            {$fileID .= '<a class="fa fa-th swipe_modelID"  style="text-decoration:none;cursor:pointer;" title="Spare Bus" aria-sort="BUSES_'.$rows['recID'].'_'.$bus_reqID.'"></a>';}
						$fileID .= '</td>';
						}
						
						$fileID .= '<td '.$row_colID.' '.$pasteID.' width="80" align="center" '.$dragCL.'>'.strtoupper($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_20'] : $arrayRT[0]['fID_21']).'</td>';

						$fileID .= '<td '.$row_colID.' '.$pasteID.' '.$dragCL.'><textarea class="form-control shiftcomments" onkeydown="updateMASTERSoptions(this.value,'.$rows['recID'].',2)"  onkeyup="updateMASTERSoptions(this.value,'.$rows['recID'].',2)" style="resize:none;">'.$rows['fID_4'].'</textarea></td>';
						$fileID .= '<td '.$row_colID.' '.$pasteID.' '.$dragCL.'><textarea class="form-control shiftcomments" onkeydown="updateMASTERSoptions(this.value,'.$rows['recID'].',3)"  onkeyup="updateMASTERSoptions(this.value,'.$rows['recID'].',3)" style="resize:none;">'.$rows['fID_6'].'</textarea></td>';
						
						$fileID .= $statusID == 1 ? '<td '.$row_colID.' '.$pasteID.' '.$dragCL.' align="center">'.$rows['singinID'].'</td>' : '';
						
						$fileID .= '<td '.$dragCL.' '.$pasteID.' '.$row_colID.' align="center">'.($rows['tagCD'] == 'A' ? $arrayRT[0]['fID_4'] : $arrayRT[0]['fID_11']);
						
						$fileID .= '</td>';
						
					$fileID .= '</tr>';							
					/* END : Print-DataTable */
					}
				}
				
			}
			$fileID .= '</table>';
			
            $return['countID'] = $countID;
            $return['fileID'] = $fileID;        
        }
        else
        {
            
        }
        return $return;
    } 
    
    public function GET_Shifts_Master($ID,$dateID,$categoryID)
    {
        $return = '';
		
        $SQL = "SELECT spare_regis_dtl.fieldID_1 FROM spare_regis INNER JOIN spare_regis_dtl ON spare_regis_dtl.ID = spare_regis.ID WHERE 
        spare_regis.dateID = '".$dateID."' AND spare_regis_dtl.forID = ".$categoryID." AND spare_regis.companyID In(".$this->companyID.")  
        Order By recID ASC ";
			
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            if($categoryID == 1)
            {
                $arrayID = $rows['fieldID_1'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows['fieldID_1']." ") : '';

                $crtID = ($rows['fieldID_1'] == $ID ? 'selected="selected"' : '');
                $return .= '<option '.$crtID.' value="'.$rows['fieldID_1'].'">
                '.strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'].' - ('.$arrayID[0]['phone']).')</option>';
            }

            if($categoryID == 2)
            {
                $arrayID = $rows['fieldID_1'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fieldID_1']." ") : '';

                $crtID = ($rows['fieldID_1'] == $ID ? 'selected="selected"' : '');
                $return .= '<option '.$crtID.' value="'.$rows['fieldID_1'].'">'.
                strtoupper($arrayID[0]['busno'].' - '.$arrayID[0]['modelno'].' - '.$arrayID[0]['title']).'</option>';
            }
        }
        return $return;
    }
    
    public function DriverBusAlcSheets($fdateID,$filterID,$statusID)
    { 
        $fileID = '';
        $countID = 0;
		
        $crtID = "";
        $crtID = ($filterID <> '' ? " AND imp_shift_daily.companyID In (".$filterID.") " : " ");
        $arrID  = $this->select('imp_shift_daily',array("dateID"), " WHERE dateID >= '".$fdateID."' ".$passID." ".$crtID." Order By dateID ASC LIMIT 1 ");
        
        if($arrID[0]['dateID'] <> '' && ($arrID[0]['dateID'] == date('Y-m-d')))
        {  
			$SQL = "Select imp_shift_daily.recID, imp_shift_daily.companyID, company.title As companyNM, imp_shift_daily.dateID, imp_shift_daily.fID_1 As shiftNO, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID, If(imp_shift_daily.fID_014 > 0, buses.busno, imp_shift_daily.fID_14) As busNO,
			Time(If(UCase(shift_masters_dtl.fID_019) = 'N', (shift_masters_dtl.fID_11), (If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_4, shift_masters_dtl.fID_11)))) As stowTM, If(UCase(shift_masters_dtl.fID_019) = 'N', (shift_masters_dtl.fID_13), (If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_5, shift_masters_dtl.fID_13))) As lastTR, If(UCase(shift_masters_dtl.fID_019) = 'N', (shift_masters_dtl.fID_14), (If(imp_shift_daily.tagCD = 'A',
			shift_masters_dtl.fID_6, shift_masters_dtl.fID_14))) As lastLC From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID Left Join buses On buses.ID = imp_shift_daily.fID_014 Inner Join company On company.ID = imp_shift_daily.companyID Where imp_shift_daily.companyID In (".$filterID.") And imp_shift_daily.dateID = '".$arrID[0]['dateID']."' And imp_shift_daily.statusID = 1 And If(imp_shift_daily.fID_014 > 0, buses.busno, imp_shift_daily.fID_14) <> '' And If(UCase(shift_masters_dtl.fID_019) = 'N', (If(imp_shift_daily.tagCD = 'A', 1, 0)), 1) = 1 Order By stowTM ASC ";
			$Qry = $this->DB->prepare($SQL);
            $Qry->execute();
			$fileID .= '<table id="datatablesRSV" class="table table-bordered table-striped">';
			$fileID .= '<thead><tr>';
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><a id="PrintPage" class="fa fa-print" style="color:white !important; float:left;"></a> &nbsp; &nbsp;<strong>DEPOT</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>SHIFT ID</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>STAFF NAME</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>BUS NO</strong></div></th>';
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>STOW TIME</strong></div></th>'; 
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>LAST TRIP</strong></div></th>'; 
			$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>LAST LOCATION</strong></div></th>'; 
			$fileID .= '</tr></thead>';
			
			$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->row) && count($this->row) > 0)
			{
				foreach($this->row as $rows)
				{ 
					if(strtotime(substr($rows['stowTM'],0,5)) > strtotime(date('G:i')))
					{
						$countID += 1;
						
						$arrEM = $rows['empID'] > 0 ? $this->select('employee',array("code,full_name"), " WHERE ID = ".$rows['empID']." ") : '';
						
						$fileID .= '<tr>';
							$fileID .= '<td align="center">'.$rows['companyNM'].'</td>';
							$fileID .= '<td align="center">'.$rows['shiftNO'].'</td>';                            
							$fileID .= '<td>'.(strtoupper('<b style="color:#9C2A4D; font-weight:bold;">'.$arrEM[0]['code'].' - '.$arrEM[0]['full_name'].'</b>')).'</td>';
							$fileID .= '<td align="center">'.$rows['busNO'].'</td>';                            
							$fileID .= '<td align="center">'.substr($rows['stowTM'],0,5).'</td>';
							$fileID .= '<td align="center">'.$rows['lastTR'].'</td>';
							$fileID .= '<td>'.$rows['lastLC'].'</td>';
						$fileID .= '</tr>';
					}
				}
			}
			$fileID .= '</table>';
			
            $return['countID'] = $countID;
            $return['fileID'] = $fileID;        
            
            return $return;
        }    
    }
	
    public function DriverMechanicsSheets($fdateID)
    {
        $fileID = '';
		$countID = 0;
		
		$SQL = "SELECT * FROM mechanic_mst WHERE dateID = '".$fdateID."' Order By typeID ASC ";
		$Qry = $this->DB->prepare($SQL);
		$Qry->execute();
		$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
		$fileID .= '<table id="table1" class="table table-bordered table-striped">';                
		$fileID .= '<thead><tr>';
		$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>DEPOT</strong></div></th>';
		$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>MECHANIC NAME</strong></div></th>';
		$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>PHONE NO - 1</strong></div></th>';
		$fileID .= '<th style="background:#367FA9; color:white;"><div align="center"><strong>PHONE NO - 2</strong></div></th>';			
		$fileID .= '</tr></thead>';
		if(is_array($this->row) && count($this->row) > 0)
		{
			$countID += count($this->row);				
			foreach($this->row as $rows)
			{
				$arrayID  = $rows['empID'] > 0 ? $this->select('employee',array("code,full_name"), " WHERE ID = ".$rows['empID']." ") : '';

				$fileID .= '<tr>';
					$fileID .= '<td align="center" style="color: red;font-weight: bold;font-size: 14px;">'.($rows['typeID'] == 1 ? 'Beenyup, Karrinyup & Shenton Park' :($rows['typeID'] == 2 ? 'Midvale & Beckenham' 
													:($rows['typeID'] == 3 ? 'Canning Vale & Southern River' :($rows['typeID'] == 4 ? 'Bunbury & Busselton' 
													:($rows['typeID'] == 5 ? 'Albany' : ''))))).'</td>';
													
					$fileID .= '<td align="center">'.$arrayID[0]['full_name'].' - ('.$arrayID[0]['code'].')</td>';
					$fileID .= '<td align="center">'.$rows['phone_1'].'</td>';
					$fileID .= '<td align="center">'.$rows['phone_2'].'</td>';
				$fileID .= '</tr>'; 
			} 
		}
		$fileID .= '</table>';
		
		$return['countID'] = $countID;
		$return['fileID'] = $fileID;  
			
        return $return;
    } 
	
	public function SignOn_Duplicacy($fdateID,$companyID)
    {
		$data = array();
		
		$SQL_A = "Select Group_Concat(DuplicateEmpNM.empID) As empNO From (Select DuplicateEmp.empID From (Select imp_shift_daily.fID_1 As shiftNO, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID From
		imp_shift_daily Where If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) > 0 And imp_shift_daily.imp_statusID = 1 And imp_shift_daily.companyID In (".$companyID.") And imp_shift_daily.dateID = '".$fdateID."' Group By
		imp_shift_daily.fID_1,  If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013)) As DuplicateEmp Group By DuplicateEmp.empID Having Count(1) >= 2 Order By DuplicateEmp.empID) As DuplicateEmpNM ";		
		$Qry_A = $this->DB->prepare($SQL_A);
		$Qry_A->execute();
		$this->row_A = $Qry_A->fetch(PDO::FETCH_ASSOC);
		$data['empNO'] = $this->row_A['empNO'];
		
		$SQL_B = "Select Group_Concat(DuplicateBusNO.busID) As busID From (Select DuplicateBus.busID As busID From (Select If(buses.busno <> '', buses.busno, imp_shift_daily.fID_14) As busID, imp_shift_daily.fID_1 As shiftNO, imp_shift_daily.companyID From
		imp_shift_daily Left Join buses On buses.ID = imp_shift_daily.fID_014 Where imp_shift_daily.imp_statusID = 1 And imp_shift_daily.companyID In (".$companyID.") And imp_shift_daily.dateID = '".$fdateID."' And If(buses.busno <> '', buses.busno, imp_shift_daily.fID_14) <> '' 
		Group By If(buses.busno <> '', buses.busno, imp_shift_daily.fID_14), imp_shift_daily.fID_1, imp_shift_daily.companyID) As DuplicateBus Group By DuplicateBus.busID Having Count(1) >= 2) As DuplicateBusNO ";
		$Qry_B = $this->DB->prepare($SQL_B);
		if($Qry_B->execute())
		{
			$this->row_B = $Qry_B->fetch(PDO::FETCH_ASSOC);
			$data['busNO'] = $this->row_B['busID'];
		}
		
        return $data;
    }  
}
?>