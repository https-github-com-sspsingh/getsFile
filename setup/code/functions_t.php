<?PHP
class TFunctions extends EFunctions
{
    public function GenerateSignOnAllocation($fdateID,$companyID)
    {
        $companyID = $companyID <> '' ? $companyID : $_SESSION[$this->website]['compID'];
        
        if($fdateID <> '' && $companyID <> '')
        {
            foreach((explode(",",$companyID)) as $compID)
            {
                if(trim($compID) > 0)   {$this->GET_ShiftID_forAllocations($fdateID,$compID);}
            }
        }
    }

    public function GET_ShiftID_forAllocations($fdateID,$companyID)
    {
		$countTB = 0;
		$countTB = $this->count_rows('imp_shift_daily', " WHERE imp_statusID = 1 AND DATE(dateID) = '".$fdateID."' AND companyID In(".$companyID.") ");
		$countTB = $countTB > 0 ? $countTB : 0;
		
		if($countTB <= 0)
		{
			$dayID = '';
			$dayID = date('l',strtotime(date($fdateID)));
			
			$arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$companyID.") AND DATE(availDT) = '".$fdateID."' Order By ID DESC LIMIT 1 ");

			$shiftID = 0;	$stypeID = 0;		
			if($arraySE[0]['ID'] > 0)	
			{
				$shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
				$stypeID = ($arraySE[0]['stypeID'] > 0 ? $arraySE[0]['stypeID'] : 0);
			}
			else if($dayID == 'Saturday')
			{
				$array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$companyID.") AND DATE(availDT) <= '".$fdateID."' Order By availDT DESC LIMIT 1 ");
				
				$shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
				$stypeID = ($array_2[0]['stypeID'] > 0 ? $array_2[0]['stypeID'] : 0);
			}
			else if($dayID == 'Sunday')
			{
				$array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$companyID.") AND DATE(availDT) <= '".$fdateID."' Order By availDT DESC LIMIT 1 ");

				$shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
				$stypeID = ($array_3[0]['stypeID'] > 0 ? $array_3[0]['stypeID'] : 0);
			}		
			else
			{
				$array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$companyID.") AND DATE(availDT) <= '".$fdateID."' Order By availDT DESC LIMIT 1 ");

				$shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
				$stypeID = ($array_4[0]['stypeID'] > 0 ? $array_4[0]['stypeID'] : 0);
			}
			
			//  echo '<br /> shiftID : '.$shiftID;
			//  echo '<br /> stypeID : '.$stypeID;
			
			$this->recallImportShiftsData($fdateID,$shiftID,$stypeID,$companyID);
		}
    }
    
    public function recallImportShiftsData($fdateID,$shiftID,$stypeID,$companyID)
    {
        $dayID = '';
        $dayID = date('l',strtotime($fdateID));

        if($shiftID > 0 && $stypeID > 0)
        {
            $file = '';

            $SQL = "SELECT * FROM imp_shift_daily WHERE recID > 0 AND DATE(dateID) = '".$fdateID."' AND imp_shift_daily.companyID In(".$companyID.") Order By dateID, fID_1 ASC ";
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
                            $arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE usedBY = 'A' AND ID = ".$shiftID." AND companyID In(".$companyID.") AND fID_1 = '".$rows_1['fID_1']."' Order By recID DESC LIMIT 1 ");
							
                            /* START - CHECKING - DAY */
                            if($stypeID == 9)	{$returnID = 1;}
                            else
                            {	
                                $returnID = $this->GET_DAY_NAME($arrayRT[0]['fID_18'],$fdateID);
                                $returnID = ($returnID > 0 ? $returnID : 0);
                            }
                            /* END - CHECKING - DAY */
							
                            if($returnID == 1)
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
                    }
                }		
            } 
        }
    }
    
}
?>