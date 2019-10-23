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
        if(!empty($filters['fromID']) && ($filters['fromID'] <> ''))
        {
            echo $this->PreviewReportData($filters);
        }
        else
        {
            $this->msg = urlencode('Kindly fill the date.');
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
                $prID = (($request['fromID'] <> '') ? '-  (<b style="color:white;">Date : '.$request['fromID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="16" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Allocation Sheet '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';

                $file .= '<thead><tr>'; 
                $file .= '<th><div align="center"><strong>SHIFT</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON DUTY</strong></div></th>';
                $file .= '<th><div align="center"><strong>EX DEPOT</strong></div></th>';
                $file .= '<th><div align="center"><strong>OPERATOR</strong></div></th>';
                $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
                //$file .= '<th><div align="center"><strong>STOW ROAD</strong></div></th>';
                $file .= '<th><div align="center"><strong>BUS TYPE</strong></div></th>';
                $file .= '<th><div align="center"><strong>DAY</strong></div></th>';
                $file .= '<th><div align="center"><strong>MEAL BREAK</strong></div></th>';
                $file .= '<th><div align="center"><strong>COMMENTS</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON ROAD C/O</strong></div></th>';
                $file .= '<th><div align="center"><strong>STOW</strong></div></th>';
                
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$op_dayID = '';	$returnID = 0;
                    $row_colorID = '';
                    $empID = 0; $emp_reqID = 0;
                    $busID = 0; $bus_reqID = 0;
                    foreach($this->rows_1 as $rows_1)
                    {
                        $arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE ID = ".$rows_1['shiftID']." AND recID = ".$rows_1['shift_recID']." 
                        Order By recID DESC ");

                        /* START - DRIVER - SHIFT - CODE */
                        $row_colorID = (empty($rows_1['fID_14']) && empty($rows_1['fID_014']) ? 
                                                'style="background:yellow !important; color:black !important;"' : '');

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
                        $file .= '<td align="center" style="width:80px;"><b>'.$rows_1['fID_1'].' - '.$rows_1['tagCD'].'</b></td>';

                        $file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9']).'</b></td>';
                        $file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_3'] : $arrayRT[0]['fID_10']).'</b></td>';

                        /************** START - SWAPPING - WORK - AREAS  *****************/

                        /* EMPLOYEE  - DETAILS */
                        $file .= '<td '.$row_colorID.'>';
                            //$file .= '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($emp_reqID == 2 ? 'blue' : 'black').';" class="swipe_modelID" aria-sort="EMPLOYEE_'.$rows_1['recID'].'_'.$emp_reqID.'">'.(strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</a>';

                        $file .= '<b>'.(strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</b>';

                        $file .= '</td>';
                        
                        /* BUS NO - DETAILS */
                        $file .= '<td align="center" '.$row_colorID.'>';
							$file .= '<b>'.($bus_reqID == 2 ? strtoupper($arrayBS[0]['busno']) : $rows_1['fID_14']).'</b>';
                        $file .= '</td>';

                        

                        /************** ENDSS - SWAPPING - WORK - AREAS  *****************/

                        $file .= '<td align="center"><b>'.strtoupper($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_20'] : $arrayRT[0]['fID_21']).'</b></td>';
                        $file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_18'] : $arrayRT[0]['fID_18']).'</b></td>';
                        $file .= '<td>'.strtoupper($arrayRT[0]['fID_19']).'</td>';

                        $file .= '<td>'.$rows_1['fID_4'].'</td>';
                        $file .= '<td>'.$rows_1['fID_4'].'</td>';
                        $file .= '<td align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_4'] : $arrayRT[0]['fID_11']).'</b></td>';
                        $file .= '</tr>';
                        }

                    }
                }
                $file .= '</table>';			
            } 
            
            return $file;
        }
        else
        {
                $return = 'No Shift\'s are available as per record...';
        }
    }
    
}
?>