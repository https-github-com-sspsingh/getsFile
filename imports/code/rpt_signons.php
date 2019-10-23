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
			
			$SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND 
			shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = '".$this->dateFormat($filters['fromID'])."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") 
            AND imp_shift_daily.imp_statusID In(1) ORDER BY Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_3, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_10,'')))) ASC";
			
			$Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b style="color:white;">Date : '.$request['fromID'].')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="16" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Sign On Detailed Report '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';
				
                $file .= '<thead><tr>'; 
					$file .= '<th><div align="center"><strong>SHIFT</strong></div></th>';
                    $file .= '<th><div align="center"><strong>OPERATOR</strong></div></th>';
                    $file .= '<th><div align="center"><strong>BUS NO</strong></div></th>';
                    $file .= '<th><div align="center"><strong>ON</strong></div></th>';
                    $file .= '<th><div align="center"><strong>OFF</strong></div></th>';
                    $file .= '<th><div align="center"><strong>HOURS</strong></div></th>';
                    $file .= '<th><div align="center"><strong>SIGNED AT</strong></div></th>';
                    $file .= '<th><div align="center"><strong>TIME DIFF</strong></div></th>';
                $file .= '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;  $empID = 0; $diffVL = '';
                    foreach($this->rows_1 as $rows)
                    {
                        $empID   = $rows['fID_018'] > 0 ? $rows['fID_018'] : $rows['fID_013'];
						$shifts = $rows['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$rows['shift_recID']." ") : '';							
                        $emp    = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';                        
                        $bus    = $rows['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fID_014']." ") : '';
						
						$diffVL = $this->SECOND_MINUTES_SUM_CODE_ALL($rows['dateID'],$rows['companyID'],$rows['fID_1'],$rows['tagCD']);
						
						if($diffVL['diffID'] > 0)
						{						
							$file .= '<tr>'; 
							$file .= '<td align="center"><b>'.$rows['fID_1'].'-'.$rows['tagCD'].'</b></td>';
							
							$file .= '<td><b>'.(strtoupper($emp[0]['fname'].' '.$emp[0]['lname'])).' ('.$emp[0]['code'].')</b></td>';
							$file .= '<td align="center"><b>'.($rows['fID_014'] <> '' ? $bus[0]['busno'] : $rows['fID_14']).'</b></td>';
							$file .= '<td align="center">'.($rows['tagCD'] == 'A' ? $shifts[0]['fID_2'] : $shifts[0]['fID_9']).'</td>';
							$file .= '<td align="center">'.($rows['tagCD'] == 'A' ? $shifts[0]['fID_7'] : $shifts[0]['fID_12']).'</td>';						
							$file .= '<td align="center">'.($rows['tagCD'] == 'A' ? str_replace('h',':',$shifts[0]['fID_8']) : str_replace('h',':',$shifts[0]['fID_15'])).'</td>';						
							$file .= '<td align="center">'.$rows['singinID'].'</td>';
							$file .= '<td align="center">'.($diffVL['diffTX']).'</td>';
							
							$file .= '</tr>';
						}
                    }
                }
                $file .= '</table>';			
            } 
            echo $file;
        }
    } 
	

}
?>