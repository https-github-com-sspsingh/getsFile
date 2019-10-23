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
      extract($_POST);      //echo '<pre>';   echo print_r($_POST); exit;

      if(!empty($sheetID) && ($optionID == 1))
      {
        $inputFileName = $_FILES['upload']['tmp_name'];

        try {$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);}
        catch(Exception $e) {die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());}

        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $arrayCount = count($allDataInSheet);
        $counter = 0;
        
        /* SHEET - 1 */
        if(($this->dateFormat($fdateID)) < date('Y-m-d'))
        {
            $this->msg = urlencode('Back Date Entry is Not Allowed.!!!');
            $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
            $this->Print_Redirect($param,$this->basefile.'?');
        }
        else
        {
            if(!empty($sheetID) && ($sheetID == 1) && ($optionID == 1))
            {
                $statusID = 0;
                $statusID = $this->count_rows('imp_shifts'," WHERE dateID <> '' AND dateID = '".$this->dateFormat($fdateID)."' AND companyID = ".$_SESSION[$this->website]['compID']." ");
                $statusID = $statusID > 0 ? $statusID : 0;

                //echo '<br /> statusID : '.$statusID;

                if($statusID == 0 || empty($statusID))
                {
                    for($i = 3; $i <= $arrayCount; $i++)
                    {	 
                        $empcodeID = trim($allDataInSheet[$i]["M"]); 
                        $EM_Array = $this->select('employee',array("*"), " WHERE ID > 0 AND code = '".$empcodeID."' ");

                        if(!empty($fdateID) && !empty(trim($allDataInSheet[$i]["A"])) && !empty(trim($allDataInSheet[$i]["M"])))
                        {
                            $array = array();
                            $array['dateID']    = $this->dateFormat($fdateID);
                            $array['companyID'] = $_SESSION[$this->website]['compID'];                            
                            $array['fielID_1']  = trim($allDataInSheet[$i]["A"]);
                            $array['fielID_2']  = trim($allDataInSheet[$i]["B"]);                            
                            $array['fielID_3']  = trim($allDataInSheet[$i]["C"]);
                            $array['fielID_4']  = trim($allDataInSheet[$i]["D"]);
                            $array['fielID_5']  = trim($allDataInSheet[$i]["E"]);
                            $array['fielID_6']  = trim($allDataInSheet[$i]["F"]);
                            $array['fielID_7']  = trim($allDataInSheet[$i]["G"]);
                            $array['fielID_8']  = trim($allDataInSheet[$i]["H"]);
                            $array['fielID_9']  = trim($allDataInSheet[$i]["I"]);
                            $array['fielID_10'] = trim($allDataInSheet[$i]["J"]);
                            $array['fielID_11'] = trim($allDataInSheet[$i]["K"]);
                            $array['fielID_12'] = trim($allDataInSheet[$i]["L"]);
                            $array['fielID_13'] = trim($allDataInSheet[$i]["M"]);                            
                            $array['fielID_14'] = trim($allDataInSheet[$i]["N"]);
                            $array['fielID_15'] = trim($allDataInSheet[$i]["O"]);
                            $array['fielID_16'] = trim($allDataInSheet[$i]["P"]);
                            $array['fielID_17'] = trim($allDataInSheet[$i]["Q"]);
                            
                            $array['fielID_013'] = trim($EM_Array[0]['ID']);
                            $array['fielID_0']  = $this->TimeAddMinues($array['fielID_2'],"+15");
                            $array['check_timeID']  = $this->TimeAddMinues($array['fielID_2'],"-5");
                            
                            $array['statusID']  = 2;

                            //echo '<pre>';   echo print_r($array); exit;

                            $this->BuildAndRunInsertQuery('imp_shifts',$array);
                            $counter++;
                        } 
                    }

                    $this->msg = urlencode($counter. ' Records from Driver Shifts Sheet are Updated............');
                    $param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID);
                    $this->Print_Redirect($param,$this->basefile.'?');
                }
                else
                {
                        $this->msg = urlencode('Data for : '.$fdateID.' already exists, Please Delete Previous Data Import ..!!!');
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
	
    public function Display_Shifts()
    {
      extract($_POST);							//echo '<pre>';   echo print_r($_POST); exit;

      if($sheetID == 2)
      {
			$fdateID = $this->dateFormat($fdateID);
			
			$SQL = "SELECT * FROM imp_shifts WHERE recID > 0 AND dateID = '".$fdateID."' AND companyID = ".$_SESSION[$this->website]['compID']." 
			Order By recID ASC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTables" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
					echo '<th colspan="18" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
					Driver Shits - Lists ('.date('d - M - Y',strtotime($fdateID)).')</strong></div></th>'; 
				echo '</tr></thead>';
			
				echo '<thead><tr>';            
				echo '<th><div align="center"><strong>SHIFT ID</strong></div></th>';
				echo '<th><div align="center"><strong>ON</strong></div></th>';
				echo '<th><div align="center"><strong>OFF</strong></div></th>';
				echo '<th><div align="center"><strong>HOURS</strong></div></th>';
				echo '<th><div align="center"><strong>ON</strong></div></th>';
				echo '<th><div align="center"><strong>OFF</strong></div></th>';
				echo '<th><div align="center"><strong>HOURS</strong></div></th>';
				echo '<th><div align="center"><strong>TOTAL</strong></div></th>';
				echo '<th><div align="center"><strong>WEEK</strong></div></th>'; 
				echo '<th><div align="center"><strong>DAY</strong></div></th>'; 
				echo '<th><div align="center"><strong>TYPE</strong></div></th>'; 
				
				echo '<th><div align="center"><strong>MEAL BREAK</strong></div></th>';
				echo '<th><div align="center"><strong>STAFF ID</strong></div></th>';
				echo '<th><div align="center"><strong>BUS NUMBER</strong></div></th>';
				echo '<th><div align="center"><strong>BUS TYPE</strong></div></th>'; 
				echo '<th><div align="center"><strong>SHIFT COMMENTS</strong></div></th>'; 
				echo '<th><div align="center"><strong>OTHER INFO</strong></div></th>'; 
			
				echo '</tr></thead>';
				if(is_array($this->row) && count($this->row) > 0)			
				{
					$srID = 1;
					foreach($this->row as $rows)
					{
						$arrayID  = $rows['fielID_013'] <> '' ? $this->select('employee',array("*"), " WHERE ID = ".$rows['fielID_013']." ") : '';
						
						echo '<tr>';
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_1'].'</b></td>';
							echo '<td align="center">'.$rows['fielID_2'].'</td>';
							echo '<td align="center">'.$rows['fielID_3'].'</td>';
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_7'].'</b></td>';
							echo '<td align="center">'.$rows['fielID_5'].'</td>';
							echo '<td align="center">'.$rows['fielID_6'].'</td>';
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_7'].'</b></td>';
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_8'].'</b></td>';
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_9'].'</b></td>';
							
							echo '<td align="center">'.(date('D',strtotime($rows['dateID']))).'</td>';
							
							echo '<td align="center"><b style="color:#367FA9;">'.$rows['fielID_11'].'</b></td>';
							echo '<td align="center">'.$rows['fielID_12'].'</td>';
							echo '<td>'.$arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'].'</td>';
							echo '<td align="center">'.$rows['fielID_14'].'</td>';
							echo '<td align="center">'.$rows['fielID_15'].'</td>';
							echo '<td align="center">'.$rows['fielID_16'].'</td>';
							echo '<td align="center">'.$rows['fielID_17'].'</td>';
							
						echo '</tr>';
					}
				}
				else
				{
					echo '<tr>';
						echo '<td align="center" colspan="17"><b style="color:red;">Sorry, No Data Available as per specification...</b></td>';
					echo '</tr>';
				}
				echo '</table>';			
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