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
        if($filters['fromID'] <> '')
        {
            $file = '';
            
            $SQL = "SELECT imp_shift_daily.dateID, imp_shift_daily.fID_1 AS shiftNO, imp_shift_daily.shift_recID AS shiftRID FROM imp_shift_daily WHERE imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(dateID) BETWEEN '".$this->dateFormat($filters['fromID'])."' AND '".$this->dateFormat($filters['fromID'])."' GROUP BY imp_shift_daily.dateID, imp_shift_daily.fID_1, imp_shift_daily.shift_recID, imp_shift_daily.companyID ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b style="color:white;">Date : '.$request['fromID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="16" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Sign On Detailed Report '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';
                
                $file .= '<thead><tr>'; 
                $file .= '<th rowspan="2" ><div align="center"><strong>SHIFT</strong></div></th>';                
                $file .= '<th colspan="6" style="border-right:red 2px solid;"><div align="center"><strong>HALF - 1</strong></div></th>';
                $file .= '<th colspan="6"><div align="center"><strong>HALF - 2</strong></div></th>'; 
                $file .= '</tr>';
                
                $file .= '<tr>';
                    $file .= '<th><div align="center"><strong>DRIVER CODE</strong></div></th>';
                    $file .= '<th><div align="center"><strong>DRIVER NAME</strong></div></th>';
                    $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
					$file .= '<th><div align="center"><strong>ON TIME</strong></div></th>';
					$file .= '<th><div align="center"><strong>SIGNED AT TIME</strong></div></th>';
					$file .= '<th style="border-right:red 2px solid;"><div align="center"><strong>COMMENTS</strong></div></th>';
                    
                    $file .= '<th><div align="center"><strong>DRIVER CODE</strong></div></th>';
                    $file .= '<th><div align="center"><strong>DRIVER NAME</strong></div></th>';
                    $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
					$file .= '<th><div align="center"><strong>ON TIME</strong></div></th>';
					$file .= '<th><div align="center"><strong>SIGNED AT TIME</strong></div></th>';
					$file .= '<th><div align="center"><strong>COMMENTS</strong></div></th>';
                $file .= '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;  $empID_A = 0;  $empID_B = 0;
                    foreach($this->rows_1 as $rows)
                    {
                        $arrA = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$rows['dateID']."' AND fID_1 = ".$rows['shiftNO']." AND tagCD = 'A' ");
                        $arrB = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$rows['dateID']."' AND fID_1 = ".$rows['shiftNO']." AND tagCD = 'B' ");

                        $empID_A = $arrA[0]['fID_018'] > 0 ? $arrA[0]['fID_018'] : $arrA[0]['fID_013'];
                        $empID_B = $arrB[0]['fID_018'] > 0 ? $arrB[0]['fID_018'] : $arrB[0]['fID_013'];
                        
                        $shiftA = $arrA[0]['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$arrA[0]['shift_recID']." ") : '';
                        $shiftB = $arrB[0]['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$arrB[0]['shift_recID']." ") : '';
                        
                        $empA  = $empID_A > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID_A." ") : '';
                        $empB  = $empID_B > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID_B." ") : '';
                        
                        $busA  = $arrA[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$arrA[0]['fID_014']." ") : '';
                        $busB  = $arrB[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$arrB[0]['fID_014']." ") : '';
                        
                        $file .= '<tr>'; 
                        $file .= '<td align="center"><b>'.$rows['shiftNO'].'</b></td>';
                        
                        $file .= '<td align="center"><b>'.ucfirst(strtolower($empA[0]['code'])).'</b></td>';
                        $file .= '<td><b>'.ucfirst(strtolower($empA[0]['fname'].' '.$empA[0]['lname'])).'</b></td>';
                        $file .= '<td align="center"><b>'.($arrA[0]['fID_014'] <> '' ? $busA[0]['busno'] : $arrA[0]['fID_14']).'</b></td>';
						$file .= '<td align="center">'.$shiftA[0]['fID_2'].'</td>';
						$file .= '<td align="center">'.$arrA[0]['singinID'].'</td>';
						$file .= '<td align="center" style="border-right:red 2px solid;">'.$arrA[0]['fID_4'].'</td>';
                        
                        $file .= '<td align="center"><b>'.($shiftB[0]['fID_9'] <> '' ? ucfirst(strtolower($empB[0]['code'])) : '').'</b></td>';
                        $file .= '<td><b>'.($shiftB[0]['fID_9'] <> '' ? ucfirst(strtolower($empB[0]['fname'].' '.$empB[0]['lname'])) : '').'</b></td>';
                        $file .= '<td align="center"><b>'.($shiftB[0]['fID_9'] <> '' ? (($arrB[0]['fID_014'] <> '' ? $busB[0]['busno'] : $arrB[0]['fID_14'])) : '').'</b></td>';
						$file .= '<td align="center">'.($shiftB[0]['fID_9'] <> '' ? $shiftB[0]['fID_2'] : '').'</td>';
						$file .= '<td align="center">'.($shiftB[0]['fID_9'] <> '' ? $arrB[0]['singinID'] : '').'</td>';
						$file .= '<td align="center">'.($shiftB[0]['fID_9'] <> '' ? $arrB[0]['fID_4'] : '').'</td>';
                        
                        $file .= '</tr>';
                    }
                }
                $file .= '</table>';			
            } 
            echo $file;
        }
    } 
}
?>