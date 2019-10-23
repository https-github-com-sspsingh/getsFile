<?PHP
require_once 'PHPExcel/IOFactory.php';
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
		parent::__construct();
		$this->basefile = basename($_SERVER['PHP_SELF']);
		$this->tableName = 'imp_shift_daily';
    }

    public function GoToInnserSheet($Status = 1)
    {
        extract($_POST);		//echo '<pre>';   echo print_r($_POST); exit;

        if(!empty($sheetID) && ($optionID == 1) && ($chooseID == 1))
        {
			$inputFileName = $_FILES['upload']['tmp_name'];

			try {$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);}	
			catch(Exception $e) {die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());}
			
			$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
			$arrayCount = count($allDataInSheet);
			$counter = 0;

			/* SHEET - 1 */
			if(($this->dateFormat($dateID)) < date('Y-m-d'))
			{
				$this->msg = urlencode('Back Date Entry is Not Allowed !');
				$param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
				$this->Print_Redirect($param,$this->basefile.'?');
			}
			else
			{
				if(!empty($sheetID) && ($sheetID == 1) && ($optionID == 1))
				{
					$statusID = 0;
					$statusID += $this->count_rows($this->tableName," WHERE dateID <> '' AND dateID = '".$this->dateFormat($dateID)."' AND companyID = ".$_SESSION[$this->website]['compID']." ");
					$statusID = $statusID > 0 ? $statusID : 0;
					
					if($statusID == 0 || empty($statusID))
					{
						/* START - CHECK SHEET IS VALID OR UN-VALID */
						$setID = 0;
						for($srID = 1; $srID <= $arrayCount; $srID++)
						{
							if($srID == 1)
							{
								if((date('Y-m-d',strtotime(trim($allDataInSheet[$srID]["B"])))) == ($this->dateFormat($dateID)))
										{$setID += 1;}
								else	{$setID += 0;}
							}
							else if($srID == 2)
							{
								if(trim($allDataInSheet[$srID]["A"]) == 'SHIFT' && trim($allDataInSheet[$srID]["B"]) == 'STAFF ID' && trim($allDataInSheet[$srID]["C"]) == 'BUS NUMBER' && trim($allDataInSheet[$srID]["D"]) == 'COMMENTS (A)' && trim($allDataInSheet[$srID]["E"]) == 'COMMENTS (B)' && trim($allDataInSheet[$srID]["F"]) == 'ON ROAD C/O (A)' && trim($allDataInSheet[$srID]["G"]) == 'ON ROAD C/O (B)')
								{$setID += 1;}	else    {$setID += 0;}
							}
							else    {break;}
						}
				  /* END - CHECK SHEET IS VALID OR UN-VALID */

				  /* START - SHIFT SHEET IMPORT DATA */
				  if($setID == 2)
				  {
						$explodeSH = "";	$tagCD_A = '';	$tagCD_B = '';	$shiftID = "";	$returnID = 0;  $returnNM = '';
						for($i = 3; $i <= $arrayCount; $i++)
						{
							$empcodeID = trim($allDataInSheet[$i]["B"]);
							$shiftID   = trim($allDataInSheet[$i]["A"]); 
							$explodeSH = explode("-",$shiftID);
							
							if(strlen($explodeSH[1]) == 1)
							{
								if(trim($explodeSH[1]) == 'A' || trim($explodeSH[1]) == 'a')  		{$tagCD_A = 'A'; $tagCD_B = '';}
								else if(trim($explodeSH[1]) == 'B' || trim($explodeSH[1]) == 'b')   {$tagCD_A = '';  $tagCD_B = 'B';}
							}
							else	{$tagCD_A = 'A'; $tagCD_B = 'B';}

							/* COMMENTS - AB */
							$comments_A = '';   $comments_B = '';
							$comments_A = trim($allDataInSheet[$i]["D"]);   $comments_B = trim($allDataInSheet[$i]["E"]);

							/* ONROADS - AB */
							$onroads_A = '';    $onroads_B = '';
							$onroads_A = trim($allDataInSheet[$i]["F"]);    $onroads_B = trim($allDataInSheet[$i]["G"]);

							$EM_Array = $this->select('employee',array("*"), " WHERE ID > 0 AND status = 1 AND code = '".$empcodeID."' AND companyID In (".$_SESSION[$this->website]['compID'].") ");
							
							$checkID = 0;
							$checkID += ($tagCD_A <> '' ?  $this->count_rows('imp_shift_daily'," WHERE tagCD = '".$tagCD_A."' AND dateID = '".$this->dateFormat($dateID)."' AND companyID In (".$_SESSION[$this->website]['compID'].") AND fID_1 = '".trim($explodeSH[0])."' ") : '');
							$checkID += ($tagCD_B <> '' ?  $this->count_rows('imp_shift_daily'," WHERE tagCD = '".$tagCD_B."' AND dateID = '".$this->dateFormat($dateID)."' AND companyID In (".$_SESSION[$this->website]['compID'].") AND fID_1 = '".trim($explodeSH[0])."' ") : '');

							$resultID = 0; 
							$resultID = (trim($explodeSH[0]) <> '' ?  $this->count_rows('shift_masters_dtl'," WHERE fID_1 = '".trim($explodeSH[0])."' AND companyID In (".$_SESSION[$this->website]['compID'].") AND statusID = 1 ") : '');

							$dayID = '';
							$dayID = date('l',strtotime($this->dateFormat($dateID)));
							
							if(!empty($dateID) && (!empty($tagCD_A) || !empty($tagCD_B)) && (empty($checkID) || $checkID == 0) && ($resultID > 0))
							{
								$arrSH = $this->select('shift_masters_dtl',array("*"), " WHERE fID_1 = '".trim($explodeSH[0])."' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($dateID)."' Order By ID DESC LIMIT 1 ");

								$coloumVALUE = '';  $tickID = 0;
								if($arrSH[0]['ID'] > 0)	
								{
									$coloumVALUE = $arrSH[0]['fID_019'];    $tickID = $arrSH[0]['tickID'];
								}
								else if($dayID == 'Saturday')
								{
									$arrSH = $this->select('shift_masters_dtl',array("*"), " WHERE fID_1 = '".trim($explodeSH[0])."' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
									$coloumVALUE = $arrSH[0]['fID_019'];    $tickID = $arrSH[0]['tickID'];
								}
								else if($dayID == 'Sunday')
								{
									$arrSH = $this->select('shift_masters_dtl',array("*"), " WHERE fID_1 = '".trim($explodeSH[0])."' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
									$coloumVALUE = $arrSH[0]['fID_019'];    $tickID = $arrSH[0]['tickID'];
								}		
								else
								{
									$arrSH = $this->select('shift_masters_dtl',array("*"), " WHERE fID_1 = '".trim($explodeSH[0])."' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
									$coloumVALUE = $arrSH[0]['fID_019'];    $tickID = $arrSH[0]['tickID'];
								}
								
								/* INSERT - TAG - A - CODES */
								if($tagCD_A == 'A')
								{
									$arrA = array();
									$arrA['dateID']  = $this->dateFormat($dateID);
									$arrA['companyID'] = $_SESSION[$this->website]['compID'];
									$arrA['tagCD']   = 'A';
									$arrA['fID_1']   = trim($explodeSH[0]);
									$arrA['fID_13']  = trim($empcodeID);
									$arrA['fID_013'] = $EM_Array[0]['ID'];
									$arrA['fID_14']  = trim($allDataInSheet[$i]["C"]);
									$arrA['fID_4']   = trim($comments_A);
									$arrA['cuttoffID'] = 0;
									/*$arrA['fID_5']   = trim($allDataInSheet[$i]["E"]);*/
									$arrA['fID_6']   = trim($onroads_A);
									$arrA['usedBY']  = '';
									/*$arrA['fID_7']   = trim($allDataInSheet[$i]["G"]);*/
									if($this->BuildAndRunInsertQuery($this->tableName,$arrA))  {$returnID += 1;}
								}

								/* INSERT - TAG - B - CODES */
								if($tagCD_B == 'B')
								{	
									$arrB = array();
									$arrB['dateID']  = $this->dateFormat($dateID);
									$arrB['companyID'] = $_SESSION[$this->website]['compID'];
									$arrB['tagCD']   = 'B';
									$arrB['fID_1']   = trim($explodeSH[0]);
									$arrB['fID_13']  = trim($empcodeID);
									$arrB['fID_013'] = $EM_Array[0]['ID'];
									$arrB['fID_14']  = ($coloumVALUE == 'N' || $coloumVALUE == 'n' ? trim($allDataInSheet[$i]["C"]) : '');
									$arrB['fID_4']   = trim($comments_B);
									/*$arrB['cuttoffID'] = ($day_ID == 1 ? 0 :($day_ID == 2 && $tickID == 1 ? 0 :($day_ID == 2 && $tickID == 0 ? 1 : 0)));*/
									/*$arrB['cuttoffID'] = (($coloumVALUE == 'Y' || $coloumVALUE == 'y' || $tickID == 1) ? 0 : 1);*/
									/*$arrB['fID_5']   = trim($allDataInSheet[$i]["E"]);*/									
									$arrB['cuttoffID'] = (($coloumVALUE == 'Y' || $coloumVALUE == 'y' || $tickID == 1) || (($coloumVALUE == 'N' || $coloumVALUE == 'n')&& $dayID == 'Saturday' && $dayID  == 'Sunday') ? 0 : 1);									
									$arrB['fID_6']   = trim($onroads_B);
									$arrB['usedBY']  = '';
									/*$arrB['fID_7']   = trim($allDataInSheet[$i]["G"]);*/
									if($this->BuildAndRunInsertQuery($this->tableName,$arrB))  {$returnID += 1;}
								}
								
							/*&& !empty($EM_Array[0]['ID'])*/

								if(empty($EM_Array[0]['ID']))				
								{
									$returnNM .= '<br /> Employee Code : '.trim($empcodeID).' , Shift No : '.trim($explodeSH[0]);
								}
							}
							else
							{
								if(trim($empcodeID) <> '' || trim($explodeSH[0]) <> '')
								{
									$returnNM .= '<br /> Employee Code : '.trim($empcodeID).' , Shift No : '.trim($explodeSH[0]);
								}
							}
						}

					$strID = '';
					if($checkID > 0)
					{
						$strID .= '<br /><b style="color:red; font-weight:bold;">Something is wrong with the following records. Kindly rectify.</b>';
					}

					if($returnNM <> '')
					{
						$strID .= '<br /><b style="color:red; font-weight:bold;">Please check, something is wrong with the following records in Daily Import Sheet: '.$returnNM.'</b>';
					}
					
					if($strID <> '' && $sheet_randID > 0)
					{ 
						$arrST = array();
						$arrST['dateID']    = date('Y-m-d');
						$arrST['sheetID'] = $sheet_randID;
						$arrST['strTX']   = base64_encode(trim($strID));
						$this->BuildAndRunInsertQuery('temp_shift_errors',$arrST);
					}				
					
					$this->msg = urlencode($returnID.' Records have been imported from the Daily Import Sheet. ');
					$param = array('a'=>'view','t'=>'success','m'=>$this->msg,'cs'=>$optionID,'srID'=>$sheet_randID);
					$this->Print_Redirect($param,$this->basefile.'?');
				  }
				  else
				  {
						$this->msg = urlencode('Kindly Import the valid Daily Shift Sheets.....');
						$param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
						$this->Print_Redirect($param,$this->basefile.'?');
				  }
				  /* END - SHIFT SHEET IMPORT DATA */
					}
					else
					{
				  $this->msg = urlencode('Data for : '.$dateID.' already exists, Please Delete the previous Data for this date');
				  $param = array('a'=>'view','t'=>'danger','m'=>$this->msg,'cs'=>$optionID);
				  $this->Print_Redirect($param,$this->basefile.'?');
					}
				} 
			}
        }

        else if(!empty($sheetID) && ($optionID == 1) && ($chooseID == 3))
        { 
        $SQL = "SELECT * FROM imp_shift_daily WHERE recID > 0 AND DATE(dateID) = '".$this->dateFormat($dateID)."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") Order By fID_1 ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
            echo '<thead><tr>';
            echo '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Daily Sheet : Date - '.$dateID.'</strong></div></th>';
            echo '</tr></thead>';				

            echo '<thead><tr>';
            echo '<th><div align="center"><strong>Sr. No</strong></div></th>';
            echo '<th><div align="center"><strong>SHIFT NO</strong></div></th>';
            echo '<th><div align="center"><strong>STAFF NAME</strong></div></th>';
            echo '<th><div align="center"><strong>BUS NUMBER</strong></div></th>';
            echo '<th><div align="center"><strong>COMMENTS</strong></div></th>';
            echo '<th><div align="center"><strong>ON ROAD C/O</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;	$empNAME = '';	$busNAME = '';
                foreach($this->rows_1 as $rows_1)
                {
                    if($rows_1['fID_018'] > 0)
                    {
                        $arrayID  = $rows_1['fID_018'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['fID_018']." ") : '';
                        $empNAME = '<b>'.$arrayID[0]['code'].'</b>'.' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'];
                    }
                    else
                    {
                        $arrayID  = $rows_1['fID_013'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['fID_013']." ") : '';
                        $empNAME = '<b>'.$arrayID[0]['code'].'</b>'.' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'];
                    }

                    if($rows_1['fID_014'] > 0)
                    {
                        $arrayBS  = $rows_1['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows_1['fID_014']." ") : '';
                        $busNAME  = strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno']);
                    }
                    else
                    {
                        $busNAME = $rows_1['fID_14'];
                    }

                    echo '<tr>';
                            echo '<td align="center"><b>'.$srID++.'</b></td>';
                            echo '<td align="center">'.$rows_1['fID_1'].' - <b>'.$rows_1['tagCD'].'</b></td>';
                            echo '<td>'.strtoupper($empNAME).'</td>';
                            echo '<td>'.strtoupper($busNAME).'</td>';
                            echo '<td>'.$rows_1['fID_4'].'</td>';
                            echo '<td>'.$rows_1['fID_6'].'</td>';
                    echo '</tr>';	
                }
            }
            echo '</table>';
        }
        }

        else if(!empty($sheetID) && ($optionID == 1) && ($chooseID == 4))
        {
        $dayID = '';
        $dayID = date('l',strtotime(date($this->dateFormat($dateID))));

        $arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($dateID)."' Order By ID DESC LIMIT 1 ");

        $shiftID = 0;	$stypeID = 0;		
        if($arraySE[0]['ID'] > 0)	
        {
            $shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
            $stypeID = ($arraySE[0]['stypeID'] > 0 ? $arraySE[0]['stypeID'] : 0);
        }
        else if($dayID == 'Saturday')
        {
            $array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
            $shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
            $stypeID = ($array_2[0]['stypeID'] > 0 ? $array_2[0]['stypeID'] : 0);
        }
        else if($dayID == 'Sunday')
        {
            $array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
            $shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
            $stypeID = ($array_3[0]['stypeID'] > 0 ? $array_3[0]['stypeID'] : 0);
        }		
        else
        {
            $array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
            $shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
            $stypeID = ($array_4[0]['stypeID'] > 0 ? $array_4[0]['stypeID'] : 0);
        }

        $SQL = "SELECT * FROM shift_masters_dtl WHERE usedBY = 'A' AND companyID = ".$_SESSION[$this->website]['compID']." AND fID_1 <> '' AND ID = ".$shiftID." Order By fID_1 ASC ";
		//echo $SQL;
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
        echo '<table id="dataTables" class="table table-bordered table-striped">';
        echo '<thead><tr>';
        echo '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Daily Sheet : Date - '.$dateID.'</strong></div></th>';
        echo '</tr></thead>';				

        echo '<thead><tr>';
        echo '<th><div align="center"><strong>Sr. No</strong></div></th>';
        echo '<th><div align="center"><strong>SHIFT</strong></div></th>';
        echo '<th><div align="center"><strong>STAFF ID</strong></div></th>';
        echo '<th><div align="center"><strong>BUS NUMBER</strong></div></th>';
        echo '<th><div align="center"><strong>COMMENTS (A)</strong></div></th>';
        echo '<th><div align="center"><strong>COMMENTS (B)</strong></div></th>';
        echo '<th><div align="center"><strong>ON ROAD C/O (A)</strong></div></th>';
        echo '<th><div align="center"><strong>ON ROAD C/O (B)</strong></div></th>';
        echo '</tr></thead>';
        if(is_array($this->rows_1) && count($this->rows_1) > 0)			
        {
            $srID = 1; $returnVAL = 0;

            foreach($this->rows_1 as $rows_1)
            {
                if($stypeID == 9)	{$returnVAL = 1;}
                else
                {	
                      $returnVAL = $this->GET_DAY_NAME($rows_1['fID_18'],$dateID);
                      $returnVAL = ($returnVAL > 0 ? $returnVAL : 0);
                }

                if($returnVAL == 1)
                {
                      echo '<tr>';
                          echo '<td align="center"><b>'.$srID++.'</b></td>';
                          echo '<td>'.$rows_1['fID_1'].'</td>';
                          echo '<td></td>';
                          echo '<td></td>';
                          echo '<td></td>';
                          echo '<td></td>';
                          echo '<td></td>';
                          echo '<td></td>';
                      echo '</tr>';	
                }
            }
        }
        echo '</table>'; 
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