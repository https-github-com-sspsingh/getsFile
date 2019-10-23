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
        $dayID = '';
		$dayID = date('l',strtotime(date($this->dateFormat($filters['fromID']))));

		$arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($filters['fromID'])."' Order By ID DESC LIMIT 1 ");

		$shiftID = 0; $createDT = '';
		if($arraySE[0]['ID'] > 0)	
		{
			$shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
			$createDT = $arraySE[0]['createDT'];
		}
		else if($dayID == 'Saturday')
		{
			$array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

			$shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
			$createDT = $array_2[0]['createDT'];
		}
		else if($dayID == 'Sunday')
		{
			$array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

			$shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
			$createDT = $array_3[0]['createDT'];
		}		
		else
		{
			$array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($filters['fromID'])."' Order By availDT DESC LIMIT 1 ");

			$shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
			$createDT = $array_4[0]['createDT'];
		}
		
		echo $this->PreviewReportData($filters,$shiftID,$createDT);
    }

    public function PreviewReportData($request,$shiftID,$createDT)
    {
        $dayID = '';
        $dayID = date('l',strtotime(date('D')));

        if($shiftID > 0)
        {
            $file = '';
            $SQL = "SELECT * FROM shift_masters_dtl WHERE usedBY = 'A' AND ID = ".$shiftID." AND fID_1 <> '' Order By Time(fID_2) ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($request['fromID'] <> '') ? '-  (<b>Date : '.$request['fromID'].' , Version Date : '.$createDT.')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Admin Header Sheet '.$prID.'</strong></div></th>';
                $file .= '</tr></thead>';


                $file .= '<thead><tr>'; 
                $file .= '<th><div align="center"><strong>SHIFT</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON</strong></div></th>';
                $file .= '<th><div align="center"><strong>OFF</strong></div></th>';
                $file .= '<th><div align="center"><strong>HOURS</strong></div></th>';
                $file .= '<th><div align="center"><strong>ON</strong></div></th>';
                $file .= '<th><div align="center"><strong>OFF</strong></div></th>';
                $file .= '<th><div align="center"><strong>HOURS</strong></div></th>';

                $file .= '<th><div align="center"><strong>TOTAL</strong></div></th>';
                $file .= '<th><div align="center"><strong>WEEK</strong></div></th>';
                $file .= '<th><div align="center"><strong>DAY</strong></div></th>';
                $file .= '<th><div align="center"><strong>TYPE</strong></div></th>';
                $file .= '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;  $typeNAME = '';                    
                    foreach($this->rows_1 as $rows_1)
                    {
						if(strtotime(str_replace('h',':',$rows_1['fID_16'])) < strtotime('7:30'))
                        {
                            $typeNAME = '<td align="center"><b style="color:#0DA23E;">CASUAL</b></td>';                            
                        }
                        else if((strtotime($rows_1['fID_9']) - strtotime($rows_1['fID_7'])) > (90 * 60))
                        {                            
                            $typeNAME = '<td align="center"><b style="color:#F56954;">SPREAD</b></td>';
                        }
                        else if(strtotime($rows_1['fID_2']) < strtotime('8:30'))
                        {
                            $typeNAME = '<td align="center"><b style="color:#1656A5;">EARLY</b></td>';
                        }
                        else
                        {
                            $typeNAME = '<td align="center"><b style="color:#FF2222;">LATE</b></td>';
                        }
                        
                        $file .= '<tr>';
                        $file .= '<td align="center"><b>'.$rows_1['fID_1'].'</b></td>';
                        $file .= '<td align="center">'.$rows_1['fID_2'].'</td>';
                        $file .= '<td align="center">'.$rows_1['fID_7'].'</td>';
                        $file .= '<td align="center">'.str_replace('h',':',$rows_1['fID_8']).'</td>';
                        $file .= '<td align="center">'.$rows_1['fID_9'].'</td>';
                        $file .= '<td align="center">'.$rows_1['fID_12'].'</td>';
                        $file .= '<td align="center">'.str_replace('h',':',$rows_1['fID_15']).'</td>';

                        $file .= '<td align="center">'.str_replace('h',':',$rows_1['fID_16']).'</td>';
                        $file .= '<td align="center">'.$this->RunTimeCalculate((str_replace('h',':',$rows_1['fID_16'])),(strlen($rows_1['fID_18']))).'</td>';
                        $file .= '<td align="center">'.$rows_1['fID_18'].'</td>';
                        $file .= $typeNAME;
                        $file .= '</tr>';
                    }
                }
                $file .= '</table>';			
            } 
            return $file;
        }
        else
        {
            $return = 'No Shift is available as per matching............';
        }
    }

    public function GET_DAY_NAME($valID,$fromID)
    {
		$return = '';

		$lengthID = 0;
		$lengthID = strlen($valID);

		$dayID = '';
		$dayID = date('l',strtotime($this->dateFormat($fromID)));

		if($valID <> '')
		{
			$dayNM = '';
			for($srsID = 0; $srsID <= $lengthID; $srsID++)
			{
				$dayNM =  ($valID[$srsID] == 'M' ? 'Monday'	:($valID[$srsID] == 'U' ? 'Tuesday' :($valID[$srsID] == 'W' ? 'Wednesday' :($valID[$srsID] == 'T' ? 'Thursday' :($valID[$srsID] == 'F' ? 'Friday' 	: '')))));

				if($dayID == $dayNM)	{$return = 1;	break;}
			}
		}
		return $return;
    }
}
?>