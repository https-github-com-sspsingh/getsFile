<?PHP
require_once 'PHPExcel/IOFactory.php';
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
        parent::__construct();
        $this->basefile = basename($_SERVER['PHP_SELF']);
    } 

    public function GoToInnserSheet($Status = 1)
    {
      extract($_POST);     //echo '<pre>';   echo print_r($_POST); exit;

      if(!empty($sheetID) && ($optionID == 1))
      {
        $inputFileName = $_FILES['upload']['tmp_name'];

        try {$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);}
        catch(Exception $e) {die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());}

        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $arrayCount = count($allDataInSheet);
        $counter = 0;

        /* SHEET - 1 */
        if(!empty($sheetID) && ($sheetID == 1) && ($optionID == 1))
        {
            $this->delete('ts_data'," WHERE recID > 0 ");
            
            $monthID = 0;   $yearID = 0;
            $sheet1 = array();
            for($i = 2; $i <= $arrayCount; $i++)
            {	
                $sheet1  = $this->GetMonthYears(trim($allDataInSheet[$i]["C"]),'1');
                $monthID = $sheet1[0]['monthID'];
                $yearID  = $sheet1[0]['yearID'];

                $empcodeID = $this->GET_ecodeID(trim($allDataInSheet[$i]["A"]));                    
                $EM_Array = $this->select('employee',array("*"), " WHERE ID > 0 AND code = '".$empcodeID."' ");
                
                if(!empty($empcodeID) && !empty($EM_Array[0]['ID']) && ($EM_Array[0]['ID'] > 0) && (!empty($monthID)) && (!empty($yearID)))
                {
                    $array = array();
                    $array['dateID']    = date('Y-m-d');
                    $array['monID']     = $monthID;
                    $array['yrID']      = $yearID;
                    $array['empID']     = $EM_Array[0]['ID'];
                    $array['empCD']     = $empcodeID;
                    $array['companyID'] = $EM_Array[0]['companyID'];
                    $array['prID']      = ($yearID.'-'.$monthID.'-01');
                    $array['fielID_1']  = trim($allDataInSheet[$i]["E"]);
                    $array['fielID_2']  = trim($allDataInSheet[$i]["F"]);
                    $array['fielID_3']  = trim($allDataInSheet[$i]["G"]);
                    $array['fielID_4']  = trim($allDataInSheet[$i]["H"]);
                    $array['fielID_5']  = trim($allDataInSheet[$i]["I"]);
                    $array['fielID_6']  = trim($allDataInSheet[$i]["J"]);
                    $array['fielID_7']  = trim($allDataInSheet[$i]["K"]);
                    $array['fielID_8']  = trim($allDataInSheet[$i]["L"]);
                    $array['fielID_9']  = trim($allDataInSheet[$i]["M"]);
                    $array['fielID_10'] = trim($allDataInSheet[$i]["N"]);
                    $array['fielID_11'] = trim($allDataInSheet[$i]["O"]);

                    $array['fielID_12'] = ($array['fielID_1'] + $array['fielID_2'] + $array['fielID_3'] + $array['fielID_4'] + $array['fielID_5'] + 
                                           $array['fielID_6'] + $array['fielID_7'] + $array['fielID_8'] + $array['fielID_9'] + $array['fielID_10'] + 
                                           $array['fielID_11']);
                    
                    $array['fielID_13'] = ($array['fielID_1'] + $array['fielID_2'] + $array['fielID_3'] + $array['fielID_4'] + $array['fielID_5'] + $array['fielID_6']);

                    $array['punctualityID'] = round(($array['fielID_13'] / $array['fielID_12']) * 100,2); 

                    $array['totaltpID'] = ($array['fielID_1'] + $array['fielID_2'] + $array['fielID_3'] + $array['fielID_4'] + $array['fielID_5'] + 
                                           $array['fielID_6'] + $array['fielID_7'] + $array['fielID_8'] + $array['fielID_9'] + $array['fielID_10'] + 
                                           $array['fielID_11']);
                    
                    $this->BuildAndRunInsertQuery('ts_data',$array);
                    $counter++;
                }
            }
            
            $this->DataSheetTransfer('1');
            
            $this->msg = urlencode($counter. ' Records from Punctuality Sheet are Updated');
            $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        } 

        /* SHEET - 2 */
        if(!empty($sheetID) && ($sheetID == 2) && ($optionID == 1))
        {
            $this->delete('ts_data'," WHERE recID > 0 ");
            
            $monthID = 0;   $yearID = 0;
            $sheet2 = array();
            for($i = 2; $i <= $arrayCount; $i++)
            {
                $sheet2  = $this->GetMonthYears(trim($allDataInSheet[$i]["C"]),'2');
                $monthID = $sheet2[0]['monthID'];
                $yearID  = $sheet2[0]['yearID'];
                
                $empcodeID = $this->GET_ecodeID(trim($allDataInSheet[$i]["A"]));
                $EM_Array  = $this->select('employee',array("*"), " WHERE ID > 0 AND code = '".$empcodeID."' ");
                
                if(!empty($empcodeID) && !empty($EM_Array[0]['ID']) && ($EM_Array[0]['ID'] > 0) && (!empty($monthID)) && (!empty($yearID)))
                {
                    $array = array();
                    $array['dateID']    = date('Y-m-d');
                    $array['monID']     = $monthID;
                    $array['yrID']      = $yearID;
                    $array['empID']     = $EM_Array[0]['ID'];
                    $array['empCD']     = $empcodeID;
                    $array['companyID'] = $EM_Array[0]['companyID'];
                    $array['prID']      = ($yearID.'-'.$monthID.'-01');
                    $array['fielID_1']  = trim($allDataInSheet[$i]["D"]);
                    $array['fielID_2']  = trim($allDataInSheet[$i]["E"]);
                    $array['fielID_3']  = trim($allDataInSheet[$i]["F"]);
                    $array['fielID_4']  = trim($allDataInSheet[$i]["G"]);
                    $array['fielID_5']  = trim($allDataInSheet[$i]["H"]);
                    
                    $array['earlyID'] = ($array['fielID_1'] + $array['fielID_2'] + $array['fielID_3'] + $array['fielID_4'] + $array['fielID_5']);
                    
                    $this->BuildAndRunInsertQuery('ts_data',$array);
                    $counter++;
                }
            }
            
            $this->DataSheetTransfer('2');
            
            $this->msg = urlencode($counter. ' Records from Early Running are Updated');
            $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }    

        /* SHEET - 3 */
        if(!empty($sheetID) && ($sheetID == 3) && ($optionID == 1))
        {
            $this->delete('ts_data'," WHERE recID > 0 ");
            
            $monthID = 0;   $yearID = 0;
            $sheet3 = array();
            for($i = 2;$i <= $arrayCount; $i++)
            {
                $sheet3  = $this->GetMonthYears(trim($allDataInSheet[$i]["C"]),'3');
                $monthID = $sheet3[0]['monthID'];
                $yearID  = $sheet3[0]['yearID'];
                
                $empcodeID  = $this->GET_ecodeID(trim($allDataInSheet[$i]["A"]));
                $EM_Array = $this->select('employee',array("*"), " WHERE ID > 0 AND code = '".$empcodeID."' "); 
                
                if(!empty($empcodeID) && !empty($EM_Array[0]['ID']) && ($EM_Array[0]['ID'] > 0) && (!empty($monthID)) && (!empty($yearID)))
                {
                    $array = array();
                    $array['dateID']    = date('Y-m-d');
                    $array['monID']     = $monthID;
                    $array['yrID']      = $yearID;
                    $array['empID']     = $EM_Array[0]['ID'];
                    $array['empCD']     = $empcodeID;
                    $array['companyID'] = $EM_Array[0]['companyID'];
                    $array['prID']      = ($yearID.'-'.$monthID.'-01');
                    $array['fielID_1']  = trim($allDataInSheet[$i]["D"]);
                    $array['fielID_2']  = trim($allDataInSheet[$i]["E"]);
                    $array['fielID_3']  = trim($allDataInSheet[$i]["F"]);
                    $array['fielID_4']  = trim($allDataInSheet[$i]["G"]);
                    $array['fielID_5']  = trim($allDataInSheet[$i]["H"]);

                    $array['latefirstID'] = ($array['fielID_1'] + $array['fielID_2'] + $array['fielID_3'] + $array['fielID_4'] + $array['fielID_5']);
                    
                    $this->BuildAndRunInsertQuery('ts_data',$array);
                    $counter++;
                }                
            }
            
            $this->DataSheetTransfer('3');
            
            $this->msg = urlencode($counter. ' Records from Late First Sheet are Updated');
            $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }    

        /* SHEET - 4 */
        if(!empty($sheetID) && ($sheetID == 4) && ($optionID == 1))
        {
            $monthID = 0;   $yearID = 0;
            $sheet4 = array();
            for($i = 2;$i <= $arrayCount; $i++)
            {	
                $sheet4  = $this->GetMonthYears(trim($allDataInSheet[$i]["D"]),'4');
                $monthID = $sheet4[0]['monthID'];
                $yearID  = $sheet4[0]['yearID'];

                $empcodeID = trim($allDataInSheet[$i]["C"]);
                $EM_Array = $this->select('employee',array("*"), " WHERE ID > 0 AND code = '".$empcodeID."' ");
				
                /* CHECK - COUNTS */
                $statusID = 0;
                $statusID = $this->Checkrecordcounts($sheetID,$monthID,$yearID,$empcodeID);

                if(!empty($empcodeID) && !empty($EM_Array[0]['ID']) && ($EM_Array[0]['ID'] > 0) && (!empty($monthID)) && (!empty($yearID)) && (empty($statusID) || ($statusID == 0)))
                {
                    $array = array();
                    $array['dateID']    = date('Y-m-d');
                    $array['monID']     = $monthID;
                    $array['yrID']      = $yearID;
                    $array['empID']     = $EM_Array[0]['ID'];
                    $array['empCD']     = $empcodeID;
                    $array['companyID'] = $EM_Array[0]['companyID'];
                    $array['prID']      = ($yearID.'-'.$monthID.'-01');
                    $array['fielID_1']  = trim($allDataInSheet[$i]["E"]);
                    /*$array['fielID_2']  = (trim($allDataInSheet[$i]["F"]) * 100);*/

                    $array['fielID_2']  = (trim($allDataInSheet[$i]["F"]));


                    $array['fielID_3']  = trim($allDataInSheet[$i]["G"]);
                    $array['fielID_4']  = trim($allDataInSheet[$i]["H"]);
                    $array['fielID_5']  = trim($allDataInSheet[$i]["I"]);
                    $array['fielID_6']  = trim($allDataInSheet[$i]["J"]);
                    $array['fielID_7']  = trim($allDataInSheet[$i]["K"]);
                    $array['fielID_8']  = trim($allDataInSheet[$i]["L"]);
                    $array['calID_1']  = (round(trim($allDataInSheet[$i]["H"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['calID_2']  = (round(trim($allDataInSheet[$i]["I"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['calID_3']  = (round(trim($allDataInSheet[$i]["J"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['calID_4']  = (round(trim($allDataInSheet[$i]["K"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['calID_5']  = (round(trim($allDataInSheet[$i]["L"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['sfscoreID'] = (round(trim($allDataInSheet[$i]["H"]+$allDataInSheet[$i]["I"]+$allDataInSheet[$i]["J"]+$allDataInSheet[$i]["K"]+$allDataInSheet[$i]["L"]) / trim($allDataInSheet[$i]["E"]) * 10,0));
                    $array['engtimeID'] = $this->timeCalculations(trim($allDataInSheet[$i]["G"]),(trim($allDataInSheet[$i]["F"]) ),trim($allDataInSheet[$i]["E"]));
		/*$array['engtimeID'] = $this->timeCalculations(trim($allDataInSheet[$i]["G"]),(trim($allDataInSheet[$i]["F"]) * 100),trim($allDataInSheet[$i]["E"]));
		*/
                    $this->BuildAndRunInsertQuery('imp_persheets_S',$array);
                    $counter++;
                }
                
            }

            $this->msg = urlencode($counter. ' Records from DriveRight Sheet are Updated');
            $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }
      }
      
      elseif(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID)  && !empty($tyearID) && ($optionID == 2))
      {
          /*if(($fmonthID == $tmonthID) && ($fyearID == $tyearID))
          {
              $this->GenerateReportData($fmonthID,$tmonthID,$fyearID,$tyearID);
          }
          else
          {*/
              $this->GeneratePastlyReportData($fmonthID,$tmonthID,$fyearID,$tyearID,$ecodeID);
          /*}*/
      }
	  
      else
      {
          $this->msg = urlencode('Please specify the required options. And Try Again...!!!');
          $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
          $this->Print_Redirect($param,$this->basefile.'?');
      }
    }

    public function Checkrecordcounts($sheetID,$monthID,$yearID,$empcodeID)
    {
        $statusID = 0;
        if(!empty($sheetID) && !empty($monthID) && !empty($yearID))
        {
          $statusID = $this->count_rows(($sheetID == 1 ? 'imp_persheets_P' :($sheetID == 2 ? 'imp_persheets_E' 
                                       :($sheetID == 3 ? 'imp_persheets_L' :($sheetID == 4 ? 'imp_persheets_S' : ''))))," WHERE monID = '".$monthID."' 
									   AND yrID = '".$yearID."' AND empCD = '".$empcodeID."' ");
          $statusID = $statusID > 0 ? $statusID : 0;
        }
        return $statusID;
    }
    
    public function GET_ecodeID($strID)
    {
        $result = '';
        if(!empty($strID))
        {
            $str    = $strID;
            $start  = strpos($str, '(');
            $end    = strpos($str, ')', $start + 1);
            $length = $end - $start;
            $result = trim(substr($str, $start + 1, $length - 1));
        }

        return $result;
    }
	
    public function GenerateReportData($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        $this->delete('imp_persheets'," WHERE recID > 0 ");
        
        $Qry = $this->DB->prepare("SELECT * FROM imp_persheets_P WHERE monID >= :fmnID AND monID <= :tmnID AND yrID >= :fyrID AND yrID <= :tyrID 
		Order By recID ASC ");
        $Qry->bindParam(':fmnID',$fmonthID);
        $Qry->bindParam(':tmnID',$tmonthID);
        $Qry->bindParam(':fyrID',$fyearID);
        $Qry->bindParam(':tyrID',$tyearID);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_1)
                {
					$EMP_Array = $rows_1['empID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = '".$rows_1['empID']."' ") : '';
					
					if($EMP_Array[0]['status'] == 1)
					{
                    $ER_Array = $rows_1['empID'] > 0 ? $this->select('imp_persheets_E',array("*"), " WHERE recID > 0 AND empID = '".$rows_1['empID']."' 
					AND monID = ".$rows_1['monID']." AND yrID = ".$rows_1['yrID']." ") : '';
                    $LT_Array = $rows_1['empID'] > 0 ? $this->select('imp_persheets_L',array("*"), " WHERE recID > 0 AND empID = '".$rows_1['empID']."' 
					AND monID = ".$rows_1['monID']." AND yrID = ".$rows_1['yrID']." ") : '';
                    $SF_Array = $rows_1['empID'] > 0 ? $this->select('imp_persheets_S',array("*"), " WHERE recID > 0 AND empID = '".$rows_1['empID']."' 
					AND monID = ".$rows_1['monID']." AND yrID = ".$rows_1['yrID']." ") : '';
					
                    $array = array();
                    $array['dateID']    = date('Y-m-d');
                    $array['monID']     = $rows_1['monID'];
                    $array['yrID']      = $rows_1['yrID'];
                    $array['empID']     = $rows_1['empID'];
                    $array['empCD']     = $rows_1['empCD'];
                    $array['fielID_1']  = $rows_1['punctualityID'];
                    $array['fielID_2']  = $rows_1['totaltpID'];
                    $array['fielID_3']  = $ER_Array[0]['earlyID'];
                    $array['fielID_4']  = $LT_Array[0]['latefirstID'];
                    $array['fielID_5']  = $SF_Array[0]['fielID_2'];
                    $array['fielID_6']  = $SF_Array[0]['sfscoreID'];
                    $array['fielID_7']  = ($SF_Array[0]['fielID_3'] / $SF_Array[0]['engtimeID']);
                    $array['restID'] 	= 1;
                        //echo '<pre>'; echo print_r($array); exit;
                    $this->BuildAndRunInsertQuery('imp_persheets',$array);
					}
                } 
            }
			
            $this->AvergaeRating($fmonthID,$tmonthID,$fyearID,$tyearID);
            $this->Generate_SafetyScoreID($fmonthID,$tmonthID,$fyearID,$tyearID);
            $this->Generate_RankingID($fmonthID,$tmonthID,$fyearID,$tyearID);
            //$this->Final_RankingID($fmonthID,$tmonthID,$fyearID,$tyearID);
            //$this->Push_RankingID($fmonthID,$tmonthID,$fyearID,$tyearID);
            $this->displayReport($fmonthID,$tmonthID,$fyearID,$tyearID);
        }  
    }

    public function Generate_SafetyScoreID($fmonthID,$tmonthID,$fyrID,$tyrID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyrID) && !empty($tyrID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND monID >= :fmnID AND monID <= :tmnID 
			AND yrID >= :fyID AND yrID <= :tyID Order By empID ASC ");
            $Qry->bindParam(':fmnID',$fmonthID);
            $Qry->bindParam(':tmnID',$tmonthID);
            $Qry->bindParam(':fyID',$fyrID);
            $Qry->bindParam(':tyID',$tyrID);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                foreach($this->rows as $rows)
                {  
                    /* NOW WE HAVE FIXED THESE PARAMETERS - OTHERWISE WE CAN FETCH THESE PARAMETERS FROM SLAB-PERFORMANCE TABLE */ 
                    if($rows['fielID_6'] > 30 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.5 * $rows['fielID_6'] - 9.17);
                        $arr['calyID_2'] = $rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.5 * $rows['fielID_6'] - 9.17)) : $arr['calyID_1'];
                        $onr['recID']    = $rows['recID']; 
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] > 20 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.333 * $rows['fielID_6'] - 4.16);
                        $arr['calyID_2'] = $rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.333 * $rows['fielID_6'] - 4.16)) : $arr['calyID_1'];
                        $onr['recID']    = $rows['recID'];     
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] > 15 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.2 * $rows['fielID_6'] - 1.5);
                        $arr['calyID_2'] = $rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.2 * $rows['fielID_6'] - 1.5)) : $arr['calyID_1'];
                        $onr['recID']    = $rows['recID'];      
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] >= 0 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.1 * $rows['fielID_6']);
                        $arr['calyID_2'] = $rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.1 * $rows['fielID_6'])) : $arr['calyID_1'];
                        $onr['recID']    = $rows['recID'];  
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }  
                }
            }
        }
    }
	
    public function Generate_RankingID($fmonthID,$tmonthID,$fyrID,$tyrID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyrID) && !empty($tyrID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND monID >= :fmnID AND monID <= :tmnID 
            AND yrID >= :fyID AND yrID <= :tyID Order By calyID_2 DESC ");
            $Qry->bindParam(':fmnID',$fmonthID);
            $Qry->bindParam(':tmnID',$tmonthID);
            $Qry->bindParam(':fyID',$fyrID);
            $Qry->bindParam(':tyID',$tyrID);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $lastID = '';
                $rankID = 0;
                foreach($this->rows as $rows)
                {   
                    $arr = array();
                    $arr['calyID_3'] = ($lastID == $rows['calyID_2'] ? $rankID++ : ++$rankID);
                    $onr['recID']    = $rows['recID'];
                    if($this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr))
                    {
                        $lastID  = $rows['calyID_2'];
                    }
                }
            }
        } 
    }
	
    public function Final_RankingID($fmonthID,$tmonthID,$fyrID,$tyrID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyrID) && !empty($tyrID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND monID >= :fmnID AND monID <= :tmnID 
			AND yrID >= :fyID AND yrID <= :tyID Order By calyID_3 ASC ");
            $Qry->bindParam(':fmnID',$fmonthID);
            $Qry->bindParam(':tmnID',$tmonthID);
            $Qry->bindParam(':fyID',$fyrID);
			$Qry->bindParam(':tyID',$tyrID);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $lastID = '';
                $lastrID = '';

                $rankID = 1;
                $pasteID = 0;
                foreach($this->rows as $rows)
                {   
                    $pasteID = ($lastID == $rows['calyID_3'] ? $lastrID : $rankID++);

                    $arr = array();
                    $arr['calyID_4'] = $pasteID;
                    $onr['recID']    = $rows['recID'];
                    if($this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr))
                    {
                        $lastID  = $rows['calyID_2'];
                        $lastrID = $pasteID;
                    }
                }
            }
        } 
    }
	
    public function Push_RankingID($fmonthID,$tmonthID,$fyrID,$tyrID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyrID) && !empty($tyrID))
        {
            $Qry = $this->DB->prepare("UPDATE `imp_persheets` SET `calyID_4` = `calyID_3` WHERE tickID = 0 AND monID >= :fmnID AND monID <= :tmnID 
			AND yrID >= :fyID AND yrID <= :tyID ");
            $Qry->bindParam(':fmnID',$fmonthID);
            $Qry->bindParam(':tmnID',$tmonthID);
            $Qry->bindParam(':fyID',$fyrID);
			$Qry->bindParam(':tyID',$tyrID);
            $Qry->execute(); 
        } 
    }
	
    public function displayReport($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
                $SQL = "SELECT * FROM imp_persheets WHERE recID > 0 AND monID >= ".$fmonthID." AND monID <= ".$tmonthID." 
                AND yrID >= ".$fyearID." AND yrID <= ".$tyearID." Order By calyID_3 ASC ";
                $Qry = $this->DB->prepare($SQL);
                if($Qry->execute())
                {
                        $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
 
                        echo '<table id="dataTables" class="table table-bordered table-striped">';				
                        echo '<thead><tr>';
                        echo '<th colspan="14" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Performance Report 
                        for the months  : '.(date("F", mktime(0, 0, 0, $fmonthID, 10))).'-'.$tyearID.'</strong></div></th>'; 
                        echo '</tr></thead>';

                        echo '<thead><tr>';
                        echo '<th><div align="center"><strong>Sr. No.</strong></div></th>';
                       // echo '<th><div align="center"><strong>Fiscal Month</strong></div></th>';
                        echo '<th><div align="center"><strong>Code</strong></div></th>';
                        echo '<th><div align="center"><strong>Name</strong></div></th>';
                        echo '<th><div align="center"><strong>Punc %</strong></div></th>';
                        echo '<th><div align="center"><strong>Total TP</strong></div></th>';
                        echo '<th><div align="center"><strong>Early</strong></div></th>';
                        echo '<th><div align="center"><strong>Late First</strong></div></th>';
                        echo '<th><div align="center"><strong>Safety</strong></div></th>';
                        echo '<th><div align="center"><strong>Idle Rate</strong></div></th>';
                        echo '<th><div align="center"><strong>Safety Score %</strong></div></th>';
                        echo '<th><div align="center"><strong>Weigheted %</strong></div></th>';
                        echo '<th><div align="center"><strong>Rank</strong></div></th>';
                        echo '</tr></thead>';
                        if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                        {
                                $srID = 1;
                                foreach($this->rows_1 as $rows_1)
                                {  
                                        $EM_Array  = $rows_1['empID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['empID']." ") : '';

                                        echo '<tr>';
                                          echo '<td align="center">'.$srID++.'</td>';
                                      //    echo '<td align="center">'.date("F", strtotime(sprintf("%02s",$rows_1['monID']))).' - '.($rows_1['yrID']).'</td>';
                                          echo '<td align="center">'.$rows_1['empCD'].'</td>';
                                          echo '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                                          echo '<td align="center" '.($this->GetColourPicker('P',$rows_1['fielID_1'])).'>'.$rows_1['fielID_1'].'</td>';
                                          echo '<td align="center">'.$rows_1['fielID_2'].'</td>';
                                          echo '<td align="center" '.($this->GetColourPicker('E',$rows_1['fielID_3'])).'>'.$rows_1['fielID_3'].'</td>';
                                          echo '<td align="center">'.$rows_1['fielID_4'].'</td>';
                                          echo '<td align="center" '.($this->GetColourPicker('S',$rows_1['fielID_6'])).'>'.$rows_1['fielID_6'].'</td>';
                                          //echo '<td align="right">'.$rows_1['fielID_7'].'</td>';
                                          echo '<td align="center" '.($this->GetColourPicker('I',$rows_1['fielID_5'])).'>'.$rows_1['fielID_5'].'</td>';
                                          echo '<td align="center">'.$rows_1['calyID_1'].' %</td>';
                                          echo '<td align="center">'.$rows_1['calyID_2'].' %</td>';
                                          echo '<td align="center">'.$rows_1['calyID_3'].'</td>';
                                        echo '</tr>'; 
                                } 
                        }
                        echo '</table>';			
                }  
        }
    } 
	
    public function GetMonthYears($refID,$sheetID = 0)
    {
        $return = '';	$refID  = trim($refID);

        if(!empty($refID) && (($sheetID == 1) || ($sheetID == 2) || ($sheetID == 3)))
        {
            $month = '';
            $month = (substr($refID, -3) == 'Jan' ? 1  :(substr($refID, -3) == 'Feb' ? 2  :(substr($refID, -3) == 'Mar' ? 3 
                    :(substr($refID, -3) == 'Apr' ? 4  :(substr($refID, -3) == 'May' ? 5  :(substr($refID, -3) == 'Jun' ? 6 
                    :(substr($refID, -3) == 'Jul' ? 7  :(substr($refID, -3) == 'Aug' ? 8  :(substr($refID, -3) == 'Sep' ? 9 
                    :(substr($refID, -3) == 'Oct' ? 10 :(substr($refID, -3) == 'Nov' ? 11 :(substr($refID, -3) == 'Dec' ? 12
                    : 0))))))))))));

            $return[] = array('monthID'=>$month,'yearID'=>(substr($refID, 0, 4)));
        }
        else if(!empty($refID) && ($sheetID == 4))
        { 
            $return[] = array('monthID'=>(date('m', strtotime($refID))),'yearID'=>(substr($refID, -4)));
        }
        return $return;
    } 

    public function timeCalculations($timeID,$floatID,$gtimeID)
    {
        $return = '';
        if(empty($timeID) || ($timeID == '0:00:00') || ($timeID == ''))
        { 
            $getsID = explode('.',$gtimeID);
            $return = trim($getsID[0].':'.(($getsID[1] * 0.6)).':00');
        }
        else if(!empty($timeID) && !empty($floatID))
        {
            $timesID = explode(':', $timeID);

            $t1 = '';   $t2 = '';   $t3 = '';   $t4 = '';
            $c1 = '';   $c2 = '';   $c3 = '';   $c4 = '';

            $t2 = $timesID[0];        $t3 = $timesID[1];        $t4 = $timesID[2];
            $c2 = ($t2 / $floatID);   $c3 = ($t3 / $floatID);   $c4 = ($t4 / $floatID);

            if($c2!= round($c2))    {$c3 += ($c2 - floor($c2)) * 60;         $c2  = floor($c2);}                
            if($c3 != round($c3))   {$c4 += ($c3 - floor($c3)) * 60;         $c3  = floor($c3);}

            while($c4 > 59)     {$c3 += 1;  $c4 -= 60;}                
            while($c3 > 59)     {$c2 += 1;  $c3 -= 60;}
            while($c2 > 23)     {$c1 += 1;  $c2 -= 24;}

            while($c4 < 0 && ($c3 > 0 || $c2 > 0 || $c1 > 0))       {$c3 -= 1;   $c4 += 60;}        
            while($c3 < 0 && ($c2 > 0 || $c1 > 0))                  {$c2 -=1;    $c3 += 60;}
            while($c2 < 0 && $c1 > 0)                               {$c1 -= 1;   $c2 += 24;}

            while($c1 < 0 && $c2 > 0)   {$c2 -= 24; $c1 += 1;}                
            while($c2 < 0 && $c3 > 0)   {$c3 -= 60; $c2 += 1;} 
            while($c3 < 0 && $c4 > 0)   {$c4 -= 60; $c3 += 1;}

            $c4 *= 100;
            $c4  = round($c4);
            $c4 /= 100;

            $return = trim($c2.':'.$c3.':'.$c4);
        }        
        return $return;
    }    
    
    public function GeneratePastlyReportData($fmonthID,$tmonthID,$fyearID,$tyearID,$ecodeID)
    {
        $this->delete('imp_persheets'," WHERE recID > 0 AND companyID = ".$_SESSION[$this->website]['compID']." ");
        
        $Qry = $this->DB->prepare("SELECT empID FROM imp_persheets_P WHERE companyID In (".$_SESSION[$this->website]['compID'].") AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' Group By empID Order By empID ASC ");
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_1)
                {
                    $ER_Array = $rows_1['empID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = '".$rows_1['empID']."' AND companyID = ".$_SESSION[$this->website]['compID']." ") : '';

                    if($ER_Array[0]['status'] == 1)
                    {
                        $array = array();
                        $array['dateID']    = date('Y-m-d');
                        $array['yrID']      = $fyearID;
                        $array['empID']     = $rows_1['empID'];
                        $array['empCD']     = $ER_Array[0]['code'];
                        $array['companyID'] = $_SESSION[$this->website]['compID'];
                        
                        $array['fielID_1']  = $this->GetPastlyPunctualty($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'1');
                        $array['fielID_2']  = $this->GetPastlyPunctualty($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'2');
                        
                        $array['fielID_3']  = $this->GetPastlyEarlyLateFirst($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'1');
                        $array['fielID_4']  = $this->GetPastlyEarlyLateFirst($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'2');
                        
                        $array['fielID_6'] = $this->GetPastlySafetyScore($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID);
                        
                        $array['fielID_7']  = $this->GetPastlyIdleRate($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'3');
                        $array['timeID_1']  = $this->GetPastlyIdleRate($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'1');
                        $array['timeID_2']  = $this->GetPastlyIdleRate($rows_1['empID'],$fmonthID,$tmonthID,$fyearID,$tyearID,'2');
                        
                        $array['restID']    = 1;
                        $this->BuildAndRunInsertQuery('imp_persheets',$array);
                    }
                } 
            } 
        }  
        
        $this->AvergaeRating($fmonthID,$tmonthID,$fyearID,$tyearID);
        $this->GetSSPMonthly($fmonthID,$tmonthID,$fyearID,$tyearID);     
        $this->GetPastlyRaking($fmonthID,$tmonthID,$fyearID,$tyearID);
        $this->displayPastlyReport($fmonthID,$tmonthID,$fyearID,$tyearID,$ecodeID);      
    }
    
    public function GetSSPMonthly($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND yrID <= :fyID AND yrID <= :tyID AND companyID = :cmpID Order By empID ASC ");
            $Qry->bindParam(':fyID',$fyearID);
            $Qry->bindParam(':tyID',$tyearID);
            $Qry->bindParam(':cmpID',$_SESSION[$this->website]['compID']);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                foreach($this->rows as $rows)
                {  
                    /* NOW WE HAVE FIXED THESE PARAMETERS - OTHERWISE WE CAN FETCH THESE PARAMETERS FROM SLAB-PERFORMANCE TABLE */ 
                    if($rows['fielID_6'] > 30 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.5 * $rows['fielID_6'] - 9.17);
                        $arr['calyID_2'] = round($rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.5 * $rows['fielID_6'] - 9.17)) : $arr['calyID_1'],3);
                        $onr['recID']    = $rows['recID']; 
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] > 20 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.333 * $rows['fielID_6'] - 4.16);
                        $arr['calyID_2'] = round($rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.333 * $rows['fielID_6'] - 4.16)) : $arr['calyID_1'],3);
                        $onr['recID']    = $rows['recID'];     
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] > 15 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.2 * $rows['fielID_6'] - 1.5);
                        $arr['calyID_2'] = round($rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.2 * $rows['fielID_6'] - 1.5)) : $arr['calyID_1'],3);
                        $onr['recID']    = $rows['recID'];      
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                    else if($rows['fielID_6'] >= 0 && ($rows['recID'] > 0))
                    {
                        $arr = array();
                        $arr['calyID_1'] = (0.1 * $rows['fielID_6']);
                        $arr['calyID_2'] = round($rows['fielID_1'] > 0 ? ($rows['fielID_1'] - (0.1 * $rows['fielID_6'])) : $arr['calyID_1'],3);
                        $onr['recID']    = $rows['recID'];  
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }  
                }
            }
        }
    }
	
    public function GetPastlyPunctualty($empID,$fmonthID,$tmonthID,$fyearID,$tyearID,$catID)
    {
        $return = 0;
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
           if($catID == 1) 
           {
               $fielID_1 = 0;   $fielID_2 = 0;			   
               $fielID_1 = $this->Sum_files('fielID_12','imp_persheets_P'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
               $fielID_2 = $this->Sum_files('fielID_13','imp_persheets_P'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
               $return = (($fielID_2 / $fielID_1) * 100);
           }
           else
           {
               $fielID_1 = 0;
               $fielID_1 = $this->Sum_files('totaltpID','imp_persheets_P'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
               $return = ($fielID_1);
           }
        }
        return $return;
    }
    
    public function GetPastlyEarlyLateFirst($empID,$fmonthID,$tmonthID,$fyearID,$tyearID,$catID)
    {
        $return = 0;
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            if($catID == 1)
            {
                $return = $this->Sum_files('earlyID','imp_persheets_E'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            }
            else if($catID == 2)
            {
                $return = $this->Sum_files('latefirstID','imp_persheets_L'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            }
        }
        return $return;
    } 
    
    public function GetPastlySafetyScore($empID,$fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        $return = 0;
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $fielID_E = 0;  $fielID_H = 0;  $fielID_I = 0;  $fielID_J = 0;  $fielID_K = 0;  $fielID_L = 0;
            
            $fielID_E = $this->Sum_files('fielID_1','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");			
            $fielID_H = $this->Sum_files('fielID_4','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");			
            $fielID_I = $this->Sum_files('fielID_5','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");			
            $fielID_J = $this->Sum_files('fielID_6','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            $fielID_K = $this->Sum_files('fielID_7','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");            
            $fielID_L = $this->Sum_files('fielID_8','imp_persheets_S'," WHERE companyID = ".$_SESSION[$this->website]['compID']." AND empID = '".$empID."' AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            
 	    $return = round((($fielID_H+ $fielID_I + $fielID_J + $fielID_K + $fielID_L  )/ $fielID_E * 10),0) ;
        }
        return $return;
    } 
	
    public function GetPastlyRaking($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND yrID >= :fyID AND yrID <= :tyID AND companyID = :cmpID Order By calyID_2 DESC ");
            $Qry->bindParam(':fyID',$fyearID);
            $Qry->bindParam(':tyID',$tyearID);
            $Qry->bindParam(':cmpID',$_SESSION[$this->website]['compID']);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $lastID = '';   $rankID = 0;
                foreach($this->rows as $rows)
                {   
                    $arr = array();
                    $arr['calyID_3'] = ($lastID == $rows['calyID_2'] ? $rankID++ : ++$rankID);
                    $onr['recID']    = $rows['recID'];
                    if($this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr))    {$lastID  = $rows['calyID_2'];}
                }
            }
        } 
    }

    public function GetPastlyFNRankingID($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND yrID >= :fyID AND yrID <= :tyID Order By calyID_3 ASC ");
            $Qry->bindParam(':fyID',$fyearID);
			$Qry->bindParam(':tyID',$tyearID);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $lastID = '';	$lastrID = '';
                $rankID = 1;	 $pasteID = 0;
                foreach($this->rows as $rows)
                {   
                    $pasteID = ($lastID == $rows['calyID_3'] ? $lastrID : $rankID++);

                    $arr = array();
                    $arr['calyID_4'] = $pasteID;
                    $onr['recID']    = $rows['recID'];
                    if($this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr))
                    {
                        $lastID  = $rows['calyID_2'];
                        $lastrID = $pasteID;
                    }
                }
            }
        } 
    }	
	
    public function GetPastlyPushRankingID($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $Qry = $this->DB->prepare("UPDATE `imp_persheets` SET `calyID_4` = `calyID_3` WHERE tickID = 0 AND yrID >= :fyID AND yrID <= :tyID ");
            $Qry->bindParam(':fyID',$fyearID);
			$Qry->bindParam(':tyID',$tyearID);
            $Qry->execute();
        } 
    }
	
    public function GetPastlyIdleRate($empID,$fmonthID,$tmonthID,$fyearID,$tyearID,$catID)
    {
        $return = '';
        if(!empty($empID) && !empty($fmonthID) && !empty($tmonthID) && !empty($fyearID)  && !empty($tyearID) && !empty($catID))
        {
            $engtimeID = '';	$idltimeID = '';

            /* QUERY - ENGINE TIME */
            $EQry = $this->DB->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC(engtimeID))) AS lID FROM imp_persheets_S WHERE empID = :eID AND companyID = ".$_SESSION[$this->website]['compID']." AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            $EQry->bindParam(':eID',$empID); 
            $EQry->execute();
            $this->rowsE = $EQry->fetch(PDO::FETCH_ASSOC);
            $engtimeID  = $this->rowsE['lID'] <> '' ? $this->rowsE['lID'] : '00:00:00';

            /* QUERY - IDLE TIME */
            $IQry = $this->DB->prepare("SELECT SEC_TO_TIME( SUM( TIME_TO_SEC(fielID_3))) AS lID FROM imp_persheets_S WHERE empID = :eID AND companyID = ".$_SESSION[$this->website]['compID']." AND DATE(prID) >= '".($fyearID.'-'.$fmonthID.'-01')."' AND DATE(prID) <= '".($tyearID.'-'.$tmonthID.'-01')."' ");
            $IQry->bindParam(':eID',$empID);
            $IQry->execute();
            $this->rowsI = $IQry->fetch(PDO::FETCH_ASSOC);
            $idltimeID  = $this->rowsI['lID'] <> '' ? $this->rowsI['lID'] : '00:00:00';

            /* CALCULATIONS - SECONDS OF IDLE TIME */			
            $timesID_1 = array($idltimeID);
            $secID_1 = 0;
            foreach ($timesID_1 as $time1)
            {
              list($hour,$minute,$second) = explode(':',$time1);
              $secID_1 += $hour * 3600;
              $secID_1 += $minute * 60;
              $secID_1 += $second;
            }

            /* CALCULATIONS - SECONDS OF ENGINE TIME */			
            $timesID_2 = array($engtimeID);
            $secID_2 = 0;
            foreach ($timesID_2 as $time2)
            {
              list($hour,$minute,$second) = explode(':',$time2);
              $secID_2 += $hour * 3600;
              $secID_2 += $minute * 60;
              $secID_2 += $second;
            }

                if($catID == 1)	/* IDLE - TIME */		{$return = $idltimeID;}
                else if($catID == 2)	/* ENGINE - TIME */	{$return = $engtimeID;}
                else if($catID == 3)	/* IDLE RATE - TIME */	{$return = round($secID_1 / $secID_2,4);}
        } 
        return $return;
    }
	
    public function displayPastlyReport($fmonthID,$tmonthID,$fyearID,$tyearID,$ecodeID)
    { 
        $crtID  = "";
        $crtID .= ($ecodeID > 0 ? " AND empCD = '".$ecodeID."' "  : '');
        
        $SQL = "SELECT * FROM imp_persheets WHERE recID > 0 AND yrID >= ".$fyearID." AND yrID <= ".$tyearID." AND companyID = ".$_SESSION[$this->website]['compID']." ".$crtID." Order By calyID_3 ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
                echo '<th colspan="14" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Performance Report for the months  ( '.(date("F", mktime(0, 0, 0, $fmonthID, 10))).'-'.$fyearID.' to  '.(date("F", mktime(0, 0, 0,$tmonthID, 10))).'-'.$tyearID.')</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th><div align="center"><strong>Sr. No.</strong></div></th>';
            echo '<th><div align="center"><strong>Code</strong></div></th>';
            echo '<th><div align="center"><strong>Name</strong></div></th>';
            echo '<th><div align="center"><strong>Punc %</strong></div></th>';
            echo '<th><div align="center"><strong>Total TP</strong></div></th>';
            echo '<th><div align="center"><strong>Early</strong></div></th>';
            echo '<th><div align="center"><strong>Late First</strong></div></th>';
            echo '<th><div align="center"><strong>Safety</strong></div></th>';
            echo '<th><div align="center"><strong>Idle Rate %</strong></div></th>';
            echo '<th><div align="center"><strong>Safety Score %</strong></div></th>'; 
            echo '<th><div align="center"><strong>Weighted %</strong></div></th>'; 
            echo '<th><div align="center"><strong>Rank</strong></div></th>'; 

            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {  
                    $EM_Array  = $rows_1['empID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['empID']." AND companyID = ".$_SESSION[$this->website]['compID']." ") : '';

                    echo '<tr>';
                        echo '<td align="center">'.$srID++.'</td>';
                        echo '<td align="center">'.$rows_1['empCD'].'</td>';
                        echo '<td>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        echo '<td align="center" '.($this->GetColourPicker('P',$rows_1['fielID_1'])).'>'. round($rows_1['fielID_1'],2).' %</td>';
                        echo '<td align="center">'.$rows_1['fielID_2'].'</td>';
                        echo '<td align="center" '.($this->GetColourPicker('E',$rows_1['fielID_3'])).'>'.$rows_1['fielID_3'].'</td>';
                        echo '<td align="center" '.($this->GetColourPicker('E',$rows_1['fielID_4'])).'>'.$rows_1['fielID_4'].'</td>';
                        
                        echo '<td align="center" '.($this->GetColourPicker('S',$rows_1['fielID_6'])).'>'.$rows_1['fielID_6'].'</td>';
                        echo '<td align="center" '.($this->GetColourPicker('I',$rows_1['fielID_7'])).'>'.$rows_1['fielID_7'].'</td>';
                        echo '<td align="center">'.$rows_1['calyID_1'].' %</td>';
                        echo '<td align="center">'.$rows_1['calyID_2'].' %</td>';
                        echo '<td align="center">'.$rows_1['calyID_3'].'</td>';
                    echo '</tr>'; 
                } 
            }
            echo '</table>';			
        }  
    } 
	
    public function AvergaeRating($fmonthID,$tmonthID,$fyearID,$tyearID)
    {
        if(!empty($fmonthID) && !empty($tmonthID) && !empty($fyearID) && !empty($tyearID))
        {
            $AvgID = 0;
            $Qry = $this->DB->prepare("SELECT Avg(fielID_2) as avgID FROM imp_persheets WHERE yrID >= :fyID AND yrID <= :tyID AND companyID = :cmpID ");
            $Qry->bindParam(':fyID',$fyearID);
            $Qry->bindParam(':tyID',$tyearID);
            $Qry->bindParam(':cmpID',$_SESSION[$this->website]['compID']);
            $Qry->execute();
            $this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
            $AvgID = (round(($this->rows['avgID']  * 10) / 100,2));

            if(($AvgID <> '') && !empty($AvgID))
            {
                $Qry = $this->DB->prepare("SELECT * FROM imp_persheets WHERE recID > 0 AND yrID >= :fyID AND yrID <= :tyID AND companyID = :cmpID Order By recID ASC ");
                $Qry->bindParam(':fyID',$fyearID);
                $Qry->bindParam(':tyID',$tyearID);
                $Qry->bindParam(':cmpID',$_SESSION[$this->website]['compID']);
                $Qry->execute();
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                if(is_array($this->rows) && count($this->rows) > 0)
                {
                    foreach($this->rows as $rows)
                    {
                        $arr = array();
                        $arr['restID'] = $rows['fielID_2'] < $AvgID ? 0 : 1;
                        $onr['recID']  = $rows['recID'];
                        $this->BuildAndRunUpdateQuery('imp_persheets',$arr,$onr);
                    }
                }
            }
            
            /* DELETE - REST - DATA */
            $this->delete('imp_persheets'," WHERE recID > 0 AND restID = 0 AND companyID = ".$_SESSION[$this->website]['compID']." ");
        }
    }
    
    public function DataSheetTransfer($sheetID)
    {
        if(($sheetID == 1) && !empty($sheetID))
        {
            $Qry = $this->DB->prepare("CALL TRANSFER_SHEET_1 ('".date('Y-m-d')."') ");
            $Qry->execute();
        }
        else if(($sheetID == 2) && !empty($sheetID))
        {
            $Qry = $this->DB->prepare("CALL TRANSFER_SHEET_2 ('".date('Y-m-d')."') ");
            $Qry->execute();
        }        
        else if(($sheetID == 3) && !empty($sheetID))
        {
            $Qry = $this->DB->prepare("CALL TRANSFER_SHEET_3 ('".date('Y-m-d')."') ");
            $Qry->execute();
        }
		
        $this->delete('ts_data'," WHERE recID > 0 ");
    }
    
    public function GetColourPicker($caseID,$refID)
    {
        $return = '';
        if(($caseID == 'P') && !empty($refID))  /* Punc % */
        {
            $return = ($refID > 90 ? 'style="background-color:#006D34 !important; color:white !important;"' 
                     :(($refID >= 85 && $refID <= 90) ? 'style="background-color:#FFFF15 !important; color:black !important;"' 
                     :($refID >= 0 && $refID < 85 ? 'style="background-color:#FF0000 !important; color:black !important;"' 
					 : '')));
        }
        else if(($caseID == 'E') )  /* Early Running % */
        {
            $return = (($refID >= 0 && $refID <= 3) ? 'style="background-color:#006D34 !important; color:white !important;"' 
                     :(($refID >= 4 && $refID <= 6) ? 'style="background-color:#FFFF15 !important; color:black !important;"' 
                     :($refID >= 7 ? 'style="background-color:#FF0000 !important; color:black !important;"' : '')));
        }
        else if(($caseID == 'S'))  /* Safety  % */
        {
            $return = (($refID >= 0 && $refID <= 20) ? 'style="background-color:#006D34 !important; color:white !important;"' 
                     :(($refID > 20 && $refID < 51) ? 'style="background-color:#FFFF15 !important; color:black !important;"' 
                     :($refID >= 51 ? 'style="background-color:#FF0000 !important; color:black !important;"' : '')));
        }
        else if(($caseID == 'I'))  /* Punc % */
        {
            $return = (($refID >= 0 && $refID <= 3.5) || empty($refID) ? 'style="background-color:#006D34 !important; color:white !important;"' 
                     :(($refID > 3.5 && $refID <= 5.5) ? 'style="background-color:#FFFF15 !important; color:black !important;"' 
                     :($refID > 5.5 ? 'style="background-color:#FF0000 !important; color:black !important;"' : '')));
        }
        
        return $return;
    }
}
?>