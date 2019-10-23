<?PHP
class Reports extends CFunctions
{
    private $tableName = '';
    private $basefile  = '';

    function __construct()
    {	
        parent::__construct();        
        $this->basefile = basename($_SERVER['PHP_SELF']);
    }

    public function ReportDisplay($filters)
    {
        if(($this->dateFormat($filters['fromID'])) < date('Y-m-d'))
        {
            echo $this->PreviewReportData($filters);
        }
        else
        {
            $this->msg = urlencode('report is not valid for today or future date.');
            $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        } 
    }
    
    public function PreviewReportData($request)
    {
        $dayID = '';
        $dayID = date('l',strtotime(date('D')));

        if($request['fromID'] <> '')
        {
            $arrID = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$this->dateFormat($request['fromID'])."' AND cuttoffID = 1 ");
            
            $file = '';

            /* AND shiftID > 0 AND shift_recID > 0 */
            $SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND 
            shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = 
            '".$this->dateFormat($request['fromID'])."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") 
            AND imp_shift_daily.imp_statusID In(1) ORDER BY Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_3, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_10,'')))) ASC ";
			
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b>Date : '.$request['fromID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="16" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Prior Allocation Sheet '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';

                $file .= '<thead><tr>'; 
                $file .= '<th><div align="center"><strong>SHIFT</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON DUTY</strong></div></th>';
                $file .= '<th><div align="center"><strong>EX DEPOT</strong></div></th>';
                $file .= '<th><div align="center"><strong>OPERATOR</strong></div></th>';
                $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
                $file .= '<th><div align="center"><strong>BUS TYPE</strong></div></th>';
                $file .= '<th><div align="center"><strong>MEAL BREAK</strong></div></th>';
                $file .= '<th><div align="center"><strong>COMMENTS</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON ROAD C/O</strong></div></th>';
                $file .= '<th><div align="center"><strong>STOW</strong></div></th>';
				$file .= '<th><div align="center"><strong>SIGON TIME</strong></div></th>';
				$file .= '<th><div align="center"><strong>SIGON BY</strong></div></th>';
                
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$op_dayID = '';	$returnID = 0;	$row_colorID = '';	$styleID = '';
                    $empID = 0; $emp_reqID = 0;		$busID = 0; $bus_reqID = 0;		$pasteID = '';
                    foreach($this->rows_1 as $rows)
                    { 
                        $arrRT = $this->select('shift_masters_dtl',array("*"), " WHERE ID = ".$rows['shiftID']." AND recID = ".$rows['shift_recID']." 
                        Order By recID DESC ");

						$pasteID = (strtoupper($arrRT[0]['fID_019']) == 'N' ? 'style="background:#FFF2F9; vertical-align: middle;' : 'style="background:white; vertical-align: middle; ');
						
						$styleID = $rows['colorID'] == 1 ? 'style="background:#E10000 !important; color:#FFF !important; vertical-align: middle;' 
								 :($rows['colorID'] == 2 ? 'style="background:#F24F00 !important; color:#FFF !important; vertical-align: middle;' : $pasteID);
								 
                        /* START - DRIVER - SHIFT - CODE */
                        $row_colorID = (empty($rows['fID_14']) && empty($rows['fID_014']) ? 'style="background:yellow !important; color:black !important;"' 
																						  : $pasteID.'"');

                        $emp_reqID = ($rows['fID_018'] > 0 ? 2 : 1);
                        $empID     = ($rows['fID_018'] > 0 ? $rows['fID_018'] : $rows['fID_013']);

                        $bus_reqID = ($rows['fID_014'] > 0 ? 2 : 1);
                        $busID     = ($rows['fID_014'] > 0 ? $rows['fID_014'] : $rows['fID_14']);

                        $arrID  = $empID > 0 ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$empID." ") : '';
                        $arrBS  = $rows['fID_014'] > 0 ? $this->select('buses',array("busno"), " WHERE ID = ".$rows['fID_014']." ") : '';
						
                        /* ENDS - DRIVER - SHIFT - CODE */
						if((($rows['tagCD'] == 'A' ? $arrRT[0]['fID_2'] : $arrRT[0]['fID_9'])) <> '')
						{
							$file .= '<tr>'; 
							$file .= '<td align="center" '.$styleID.'width:80px;"><b>'.$rows['fID_1'].' - '.$rows['tagCD'].'</b></td>';
							$file .= '<td align="center" '.$styleID.'width:75px;"><b>'.($rows['tagCD'] == 'A' ? $arrRT[0]['fID_2'] : $arrRT[0]['fID_9']).'</b></td>';
							$file .= '<td align="center" '.$styleID.'width:80px;"><b>'.($rows['tagCD'] == 'A' ? $arrRT[0]['fID_3'] : $arrRT[0]['fID_10']).'</b></td>';

							/************** START - SWAPPING - WORK - AREAS  *****************/

							/* EMPLOYEE  - DETAILS */
							$file .= '<td '.$row_colorID.'>';
								//$file .= '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($emp_reqID == 2 ? 'blue' : 'black').';" class="swipe_modelID" aria-sort="EMPLOYEE_'.$rows['recID'].'_'.$emp_reqID.'">'.(strtoupper($arrID[0]['code'].' - '.$arrID[0]['fname'].' '.$arrID[0]['lname'])).'</a>';

							$file .= '<b>'.(strtoupper($arrID[0]['code'].' - '.$arrID[0]['fname'].' '.$arrID[0]['lname'])).'</b>';

							$file .= '</td>';
							
							/* BUS NO - DETAILS */
							$file .= '<td align="center" '.$row_colorID.'>';
								$file .= '<b>'.($bus_reqID == 2 ? strtoupper($arrBS[0]['busno']) : $rows['fID_14']).'</b>';
							$file .= '</td>';
							
							/************** ENDSS - SWAPPING - WORK - AREAS  *****************/

							$file .= '<td '.$pasteID.'" align="center"><b>'.strtoupper($rows['tagCD'] == 'A' ? $arrRT[0]['fID_20'] : $arrRT[0]['fID_21']).'</b></td>';
							$file .= '<td '.$pasteID.'">'.strtoupper($arrRT[0]['fID_19']).'</td>';
							$file .= '<td '.$pasteID.'">'.$rows['fID_4'].'</td>';
							$file .= '<td '.$pasteID.'">'.$rows['fID_6'].'</td>';
							$file .= '<td '.$pasteID.'" align="center"><b>'.($rows['tagCD'] == 'A' ? $arrRT[0]['fID_4'] : $arrRT[0]['fID_11']).'</b></td>';
							
							$file .= '<td align="center" '.$styleID.'">'.$rows['singinID'].'</td>';							
							$file .= '<td align="center" '.$styleID.'">'.($rows['singinFR'] == 'DESKTOP' ? 'Coordinator' :($rows['singinFR'] == 'TOUCHPAD' ? 'Driver' : '')).'</td>';
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