<?PHP
class Reports extends CFunctions
{
    private $tableName = '';
    private $basefile  = '';

    function __construct()
    {	
        parent::__construct();
        
        $this->basefile = basename($_SERVER['PHP_SELF']);
		
		$this->frmID		= '80';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }
	
    public function ReportDisplay($filters)
    {
        if(($this->dateFormat($filters['fromID'])) < date('Y-m-d'))
        {
            $this->msg = urlencode('Go to reports to check Back Date Allocation.');
            $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }
        else
        { 
			$dayID = '';
			$dayID = date('l',strtotime(date($this->dateFormat($filters['fromID']))));

			$arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($filters['fromID'])."' Order By ID DESC LIMIT 1 ");

			$shiftID = 0;	$stypeID = 0;		
			if($arraySE[0]['ID'] > 0)	
			{
				$shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
				$stypeID = ($arraySE[0]['stypeID'] > 0 ? $arraySE[0]['stypeID'] : 0);
			}
			else if($dayID == 'Saturday')
			{
				$array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

				$shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
				$stypeID = ($array_2[0]['stypeID'] > 0 ? $array_2[0]['stypeID'] : 0);
			}
			else if($dayID == 'Sunday')
			{
				$array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

				$shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
				$stypeID = ($array_3[0]['stypeID'] > 0 ? $array_3[0]['stypeID'] : 0);
			}		
			else
			{
				$array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

				$shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
				$stypeID = ($array_4[0]['stypeID'] > 0 ? $array_4[0]['stypeID'] : 0);
			}

			/*echo '<br /> shiftID : '.$shiftID;*/

           echo $this->regenerateReportData($filters,$shiftID,$stypeID);
           echo $this->PreviewReportData($filters,$shiftID,$stypeID);
        }
    }
	
    public function regenerateReportData($request,$shiftID,$stypeID)
    {
        $dayID = '';
        $dayID = date('l',strtotime($this->dateFormat($request['fromID'])));

        if($shiftID > 0 && $stypeID > 0)
        {
            $file = '';

            $SQL = "SELECT * FROM imp_shift_daily WHERE recID > 0 AND DATE(dateID) = '".$this->dateFormat($request['fromID'])."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") Order By dateID, fID_1 ASC ";
            
            /*echo $SQL;*/
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$op_dayID = '';	$returnID = 0;
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['imp_statusID'] == 1)
                        {
                            break;
                        }
                        else
                        {
                            $arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE usedBY = 'A' AND ID = ".$shiftID." AND companyID In(".$_SESSION[$this->website]['compID'].") AND fID_1 = '".$rows_1['fID_1']."' Order By recID DESC LIMIT 1 ");
							
                            //echo '<br /> tagCD : '.$rows_1['tagCD'];
                            //echo '<pre>'; echo print_r($arrayRT);	exit;
							
                            /* START - CHECKING - DAY */
                            if($stypeID == 9)	{$returnID = 1;}
                            else
                            {	
                                $returnID = $this->GET_DAY_NAME($arrayRT[0]['fID_18'],$request['fromID']);
                                $returnID = ($returnID > 0 ? $returnID : 0);
                            }
                            /* END - CHECKING - DAY */
				
						/*echo/ '<br /> return ID : '.$returnID; exit;*/
						if($returnID == 1)
					   {
                            /*if($rows_1['tagCD'] == 'B')
                            {
                                if($arrayRT[0]['fID_019'] == 'N')	
                                {
                                    $update = array();
                                    $update['shiftID']      = ($arrayRT[0]['ID']);
                                    $update['shift_recID']  = ($arrayRT[0]['recID']);
                                    $update['imp_statusID'] = ($arrayRT[0]['ID'] > 0 ? 1 : 2);
                                    $update['status_ynID']  = 0;
									$update['fID_14']  = '';
									$update['fID_014'] = 0;
                                    $update['logID'] = date('Y-m-d H:i:s');
                                    $on['recID'] = $rows_1['recID'];
                                    $this->BuildAndRunUpdateQuery('imp_shift_daily',$update,$on);
                                }
                                else
                                {
                                    $update = array();
                                    $update['shiftID']      = ($arrayRT[0]['ID']);
                                    $update['shift_recID']  = ($arrayRT[0]['recID']);
                                    $update['imp_statusID'] = ($arrayRT[0]['ID'] > 0 ? 1 : 2);
                                    $update['status_ynID']  = 1;
                                    $update['logID'] = date('Y-m-d H:i:s');
                                    $on['recID'] = $rows_1['recID'];
                                    $this->BuildAndRunUpdateQuery('imp_shift_daily',$update,$on);
                                }
                            }
                            else
                            {*/
                                $update = array();
								$update['usedBY']  		= ($arrayRT[0]['recID'] > 0 ? 'A' : '');
                                $update['shiftID']      = ($arrayRT[0]['ID']);
                                $update['shift_recID']  = ($arrayRT[0]['recID']);
                                $update['imp_statusID'] = ($arrayRT[0]['ID'] > 0 ? 1 : 2);
                                $update['status_ynID']  = 1;
                                $update['logID'] = date('Y-m-d H:i:s');
                                $on['recID'] = $rows_1['recID'];
                                $this->BuildAndRunUpdateQuery('imp_shift_daily',$update,$on);
                            /*}*/
                            }
                        }
                    }
                }		
            } 
            return $file;
        }
        else	{$return = 'No Shift\'s are available as per matching............';}
    }
	
    public function PreviewReportData($request,$shiftID,$stypeID)
    {
        $dayID = '';
        $dayID = date('l',strtotime(date('D')));

        if($shiftID > 0 && $stypeID > 0)
        { 
            $file = '';
			
            $SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND 
            shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = '".$this->dateFormat($request['fromID'])."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") 
            AND imp_shift_daily.imp_statusID In(1) ORDER BY Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_3, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_10,'')))) ASC ";
			
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b style="color:white;">Date : '.$request['fromID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="16" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Allocation Sheet '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th><div align="center"><strong>Sr. No</strong></div></th>';
                $file .= '<th><div align="center"><strong>SHIFT</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON DUTY</strong></div></th>';
                $file .= '<th><div align="center"><strong>EX DEPOT</strong></div></th>';
                $file .= '<th '.(($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? 'colspan="2"' : '').'><div align="center"><strong>OPERATOR</strong></div></th>';
                $file .= '<th '.(($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? 'colspan="2"' : '').'><div align="center"><strong>BUS NO</strong></div></th>';
                //$file .= '<th><div align="center"><strong>STOW ROAD</strong></div></th>';
                $file .= '<th><div align="center"><strong>BUS TYPE</strong></div></th>';                
                $file .= '<th><div align="center"><strong>MEAL BREAK</strong></div></th>';
                $file .= '<th><div align="center"><strong>COMMENTS</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON ROAD C/O</strong></div></th>';
                $file .= '<th><div align="center"><strong>LAST TRIP</strong></div></th>';
                $file .= '<th><div align="center"><strong>LOC.</strong></div></th>';
				$file .= '<th><div align="center"><strong>STOW</strong></div></th>';

                /*$file .= $arrID[0]['recID'] > 0 ? '<th><div align="center"><strong>2nd Half <br />Signon  <br />Reqd.</strong></div></th>' : '';*/
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$op_dayID = '';	$returnID = 0;
                    $row_colorID = '';
                    $empID = 0; $emp_reqID = 0;
                    $busID = 0; $bus_reqID = 0;
                    foreach($this->rows_1 as $rows_1)
                    {
                        $arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE ID = ".$rows_1['shiftID']." AND recID = ".$rows_1['shift_recID']." Order By recID DESC ");

                        /* START - DRIVER - SHIFT - CODE */
                        $row_colorID = (empty($rows_1['fID_14']) && empty($rows_1['fID_014']) ? 'style="background:yellow !important; color:black !important;"' : '');

                        $emp_reqID = ($rows_1['fID_018'] > 0 ? 2 : 1);
                        $empID     = ($rows_1['fID_018'] > 0 ? $rows_1['fID_018'] : $rows_1['fID_013']);
						
                        $bus_reqID = ($rows_1['fID_014'] > 0 ? 2 : 1);
                        $busID     = ($rows_1['fID_014'] > 0 ? $rows_1['fID_014'] : $rows_1['fID_14']);
						
                        $arrayID  = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';
						
                        $arrayBS  = $rows_1['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows_1['fID_014']." ") : '';
                        /* ENDS - DRIVER - SHIFT - CODE */
						
						if((($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9'])) <> '')
						{	
							$file .= '<tr>';
							$file .= '<td '.$styleID.' align="center"><b>'.$srID++.'</b></td>';
							$file .= '<td align="center" style="width:80px;"><b>'.$rows_1['fID_1'].' - '.$rows_1['tagCD'].'</b></td>';
							$file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9']).'</b></td>';
							$file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_3'] : $arrayRT[0]['fID_10']).'</b></td>';
							/************** START - SWAPPING - WORK - AREAS  *****************/

							/* EMPLOYEE  - DETAILS */
							$file .= '<td '.$row_colorID.'>';
							//$file .= '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($emp_reqID == 2 ? 'blue' : 'black').';" class="swipe_modelID" aria-sort="EMPLOYEE_'.$rows_1['recID'].'_'.$emp_reqID.'">'.(strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</a>';

							if($rows_1['choppedID'] == 1)
							{
								$file .= (strtoupper('<b style="color:red; font-weight:bold;">0000 - CHOPPED </b><br />.'));
							}
							else
							{
								$file .= '<b>'.(strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</b>';
							}

							$file .= '</td>';

							/* EMPLOYEE - SPARE ADD/UNDO OPTIONS */							
							if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
							{
								$file .= '<td '.$row_colorID.'>';
								if($rows_1['choppedID'] == 1)
								{
									$file .= '<a class="fa fa-undo chopped_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Chopped Staff" aria-sort="'.$rows_1['recID'].'"></a>';
								}
								else
								{											
									if((int)$rows_1['fID_018'] > 0)
									{
										$file .= '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" aria-sort="EMPLOYEE_'.$rows_1['dateID'].'_'.$emp_reqID.'_'.$empID.'_'.$rows_1['recID'].'"></a>';
									}
									else
									{
										$file .= '<a class="fa fa-th swipe_modelID" style="text-decoration:none;cursor:pointer;" title="Spare Staff" aria-sort="EMPLOYEE_'.$rows_1['recID'].'_'.$emp_reqID.'"></a>';
									}
								}
								$file .= '</td>';
							}
							
							/* BUS NO - DETAILS */
							$file .= '<td align="center" '.$row_colorID.'>';
								if((empty($rows_1['fID_14']) && empty($rows_1['fID_014'])))
								{
									/*onkeydown="updateMASTERSoptions(this.value,'.$rows_1['recID'].',1)" onkeyup="updateMASTERSoptions(this.value,'.$rows_1['recID'].',1)"*/
									$file .= '<input type="text" style="width:80px;" class="form-control" onfocusout="updateMASTERSoptions(this.value,'.$rows_1['recID'].',1)" />';
								}
								else
								{
									$file .= '<b>'.($bus_reqID == 2 ? strtoupper($arrayBS[0]['busno']) : $rows_1['fID_14']).'</b>';
								}
							$file .= '</td>';

							/* BUS - SPARE ADD/UNDO OPTIONS */							
							if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
							{
								$file .= '<td '.$row_colorID.'>';
									if((int)$rows_1['fID_014'] > 0)
									{
										$file .= '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" aria-sort="BUSES_'.$rows_1['dateID'].'_'.$bus_reqID.'_'.$busID.'_'.$rows_1['recID'].'"></a>';
									}
									else
									{
										$file .= '<a class="fa fa-th swipe_modelID" style="text-decoration:none;cursor:pointer;" title="Spare Staff" aria-sort="BUSES_'.$rows_1['recID'].'_'.$bus_reqID.'"></a>';
									}
								$file .= '</td>'; 
							}
							
							/************** ENDSS - SWAPPING - WORK - AREAS  *****************/

							$file .= '<td align="center"><b>'.strtoupper($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_20'] : $arrayRT[0]['fID_21']).'</b></td>';						
							$file .= '<td>'.strtoupper($arrayRT[0]['fID_19']).'</td>';

							if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
							{
								$file .= '<td><textarea class="form-control shiftcomments" onfocusout="updateMASTERSoptions(this.value,'.$rows_1['recID'].',2)" style="resize:none;">'.$rows_1['fID_4'].'</textarea></td>';
							}
							else	{$file .= '<td>'.$rows_1['fID_4'].'</td>';}
							
							if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
							{
								$file .= '<td><textarea class="form-control othersinfos" onfocusout="updateMASTERSoptions(this.value,'.$rows_1['recID'].',3)" style="resize:none;">'.$rows_1['fID_6'].'</textarea></td>';
							}
							else	{$file .= '<td>'.$rows_1['fID_6'].'</td>';}
							
							$file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_5'] : $arrayRT[0]['fID_13']).'</b></td>';
							$file .= '<td>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_6'] : $arrayRT[0]['fID_14']).'</td>';
							
							/*if($arrID[0]['recID'] > 0)
							{
								$file .= '<td align="center">';
								if($rows_1['cuttoffID'] == 1)
									{
										$file .= '<input type="checkbox" class="update-signon-cuttoff iCheck-helper" aria-sort="'.$rows_1['recID'].'" />';
									}
								$file .= '</td>';
							}*/
							
							$file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_4'] : $arrayRT[0]['fID_11']).'</b></td>';
							$file .= '</tr>';
                        }
                    }
                }
                $file .= '</table>';			
            }
            return $file;
        }
        else	{$return = 'No Shift\'s are available as per record...';}
    }    
}
?>