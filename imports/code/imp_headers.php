<?PHP
require_once 'PHPExcel/IOFactory.php';
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
        parent::__construct();
        $this->basefile = basename($_SERVER['PHP_SELF']);
        $this->tableName = 'shift_masters';
    }
    
    public function GoToInnserSheet($Status = 1)
    {
      extract($_POST);      //echo '<pre>';  echo print_r($_POST); exit;

      if(!empty($sheetID) && ($optionID == 1))
      {
        $inputFileName = $_FILES['upload']['tmp_name'];

        try {$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);}
        catch(Exception $e) {die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());}

        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $arrayCount = count($allDataInSheet);
        $counter = 0;
        
        /* SHEET - 1 */
        if(($this->dateFormat($availDT)) < date('Y-m-d'))
        {
            $this->msg = urlencode('Back Date Entry is Not Allowed!');
            $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }
        else
        {
            if(!empty($sheetID) && ($sheetID == 1) && ($optionID == 1))
            {
                $statusID = 0;
                /*$statusID += $this->count_rows('shift_masters'," WHERE createDT <> '' AND createDT = '".$this->dateFormat($createDT)."' AND companyID = ".$_SESSION[$this->website]['compID']." AND stypeID = ".$stypeID." AND statusID = 1 ");	*/						 
                
		if($stypeID >= 1 && $stypeID <= 6)	
		{
			$statusID += $this->count_rows('shift_masters'," WHERE usedBY = 'A' AND availDT <> '' AND availDT = '".$this->dateFormat($availDT)."' AND companyID = ".$_SESSION[$this->website]['compID']." AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 ");
		}
		else
		{
			$statusID += $this->count_rows('shift_masters'," WHERE usedBY = 'A' AND availDT <> '' AND availDT = '".$this->dateFormat($availDT)."' AND companyID = ".$_SESSION[$this->website]['compID']." AND stypeID = ".$stypeID." AND statusID = 1 ");
		}
					 
                $statusID = $statusID > 0 ? $statusID : 0;

                //echo '<br /> statusID : '.$statusID;    exit;

                if($statusID == 0 || empty($statusID))
                { 
                    $setID = 0;                    
                    for($srID = 1; $srID <= $arrayCount; $srID++)
                    {
                        if($srID == 1)
                        {
                            if(trim($allDataInSheet[$srID]["A"]) == 'SHIFT' && trim($allDataInSheet[$srID]["B"]) == 'ON' && trim($allDataInSheet[$srID]["C"]) == 'EX DEPOT')
                            {
/* && trim($allDataInSheet[$srID]["D"]) == 'STOW A' && trim($allDataInSheet[$srID]["E"]) == 'LAST TRIP' && trim($allDataInSheet[$srID]["F"]) == 'LAST LOC' && trim($allDataInSheet[$srID]["G"]) == 'OFF' && trim($allDataInSheet[$srID]["H"]) == 'HOURS'*/
                                $setID = 1;
                            }
                        }
                        else    {break;}
                    }
                    
                    if($setID == 1)
                    {
                        $srNO = 0;  $returnID = 0;
                        $srNO = $this->count_rows('shift_masters'," WHERE usedBY = 'A' AND companyID = ".$_SESSION[$this->website]['compID']." AND stypeID = ".$stypeID." ");
                        $srNO = $srNO > 0 ? $srNO + 1001 : 1001;

                        $array = array();
                        $array['srNO']      = $srNO;
                        $array['stypeID']   = $stypeID;
                        $array['usedBY']    = 'A';
                        $array['createDT']  = $this->dateFormat($createDT);
                        $array['availDT']   = $this->dateFormat($availDT);
                        $array['companyID'] = $_SESSION[$this->website]['compID'];
                        $array['userID']    = $_SESSION[$this->website]['userID'];
                        $array['statusID']  = 1;
                        $array['logID']     = date('Y-m-d H:i:s');
                        if($this->BuildAndRunInsertQuery('shift_masters',$array))
                        {
                            $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                            $lastID = $stmt->fetch(PDO::FETCH_NUM);
                            $returnID  = $lastID[0];                                
                        }
                    
                        for($sheetID = 2; $sheetID <= $arrayCount; $sheetID++)
                        {
                            if($returnID > 0)
                            {
                                $insert = array();
                                $insert['ID']        = $returnID;
                                $insert['srNO']      = $array['srNO'];
                                $insert['stypeID']   = $array['stypeID'];
                                $insert['usedBY']    = 'A';
                                $insert['createDT']  = $array['createDT'];
                                $insert['availDT']   = $array['availDT'];
                                $insert['companyID'] = $array['companyID'];
                                $insert['statusID']  = 1;
                                $insert['fID_1']  = trim($allDataInSheet[$sheetID]["A"]);
                                $insert['fID_2']  = trim($allDataInSheet[$sheetID]["B"]);                            
                                $insert['fID_3']  = trim($allDataInSheet[$sheetID]["C"]);
                                $insert['fID_4']  = trim($allDataInSheet[$sheetID]["D"]);
                                $insert['fID_5']  = trim($allDataInSheet[$sheetID]["E"]);
                                $insert['fID_6']  = trim($allDataInSheet[$sheetID]["F"]);
                                $insert['fID_7']  = strtoupper(trim($allDataInSheet[$sheetID]["G"]));
                                $insert['fID_8']  = trim($allDataInSheet[$sheetID]["H"]);
                                $insert['fID_9']  = trim($allDataInSheet[$sheetID]["I"]);
                                $insert['fID_10'] = trim($allDataInSheet[$sheetID]["J"]);
                                $insert['fID_11'] = trim($allDataInSheet[$sheetID]["K"]);
                                $insert['fID_12'] = trim($allDataInSheet[$sheetID]["L"]);
                                $insert['fID_13'] = trim($allDataInSheet[$sheetID]["M"]);                            
                                $insert['fID_14'] = strtoupper(trim($allDataInSheet[$sheetID]["N"]));
                                $insert['fID_15'] = trim($allDataInSheet[$sheetID]["O"]);
                                $insert['fID_16'] = trim($allDataInSheet[$sheetID]["P"]);
                                $insert['fID_17'] = trim($allDataInSheet[$sheetID]["Q"]);
                                $insert['fID_18'] = strtoupper(trim($allDataInSheet[$sheetID]["R"]));
                                $insert['fID_019'] = trim($allDataInSheet[$sheetID]["S"]);
				$insert['fID_19'] = trim($allDataInSheet[$sheetID]["T"]);
                                $insert['fID_20'] = trim($allDataInSheet[$sheetID]["U"]);
                                $insert['fID_21'] = strtoupper(trim($allDataInSheet[$sheetID]["V"]));
                                /*$insert['fID_22'] = strtoupper(trim($allDataInSheet[$sheetID]["V"]));
                                $insert['fID_23'] = trim($allDataInSheet[$sheetID]["W"]);*/
                                $this->BuildAndRunInsertQuery('shift_masters_dtl',$insert);
                                $counter++;
                            }
                        }
                        
                        $this->msg = urlencode($counter. ' records have been imported from Header Sheet.');
                        $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
                        $this->Print_Redirect($param,$this->basefile.'?');
                    }
                    else
                    {
                        $this->msg = urlencode('Kindly Import the valid Header Sheet.....');
                        $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
                        $this->Print_Redirect($param,$this->basefile.'?');
                    }                    
                }
                else
                {
                    $this->msg = urlencode('Data for : '.$availDT.' already exists, Please Delete Previous Data to Import');
                    $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
                    $this->Print_Redirect($param,$this->basefile.'?');
                }
            } 
        }
      }
      else
      {
          $this->msg = urlencode('Please specify the required options. And Try Again...!!!');
          $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
          $this->Print_Redirect($param,$this->basefile.'?');
      }
    }
}
?>