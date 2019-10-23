<?PHP
class Imports extends SFunctions
{
    private $basefile = '';	
    function __construct()
    {
		parent::__construct();
		$this->basefile = basename($_SERVER['PHP_SELF']);
		$this->tableName = 'imp_hastus';
    }

    public function GoToInnserSheet()
    {
        extract($_POST);		
		/*
		echo '<pre>';   echo print_r($_POST); 
		echo '<pre>';   echo print_r($_FILES); 
		exit;
		*/
		
		$logDT = date('Y-m-d H:i:s');
		$logUS = $_SESSION[$this->website]['userID'];
		
        if($optionID == 1 && $optionID > 0)
        {
			$fileName = $_FILES["upload"]["tmp_name"];
			$name 	  = $_FILES["upload"]["name"];
			
			if(trim($name) <> '' && trim($fileName) <> '')
			{
				$file = fopen($fileName, "r");
				$returnDATA = 1;
				while (($column = fgetcsv($file, 50000, ",")) !== FALSE) 
				{
					if($column[0] <> '' && strlen($column[0]) >= 35)
					{
						$dataROW = explode(";",trim($column[0]));					
						$this->prepareHastus($_POST,$dataROW);
						
						$returnDATA++;
					}
				}			
				//exit;
				
				$Qry = $this->DB->prepare("UPDATE temp_hastus INNER JOIN company ON temp_hastus.fID_1 = company.code SET temp_hastus.companyID = company.ID WHERE temp_hastus.randID = ".$randID." ");
				$Qry->execute();
				
				$Qry = $this->DB->prepare("SELECT * FROM temp_hastus WHERE randID = ".$randID." Order By recID DESC ");
				$Qry->execute();
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				$insertCount = 0;
				$updateCount = 0;
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					foreach($this->rows as $rows)
					{
						$countID  = 0;
						$countID += $this->count_rows($this->tableName," WHERE companyID = ".$rows['companyID']." AND fID_0 = '".$rows['fID_0']."' AND fID_2 = '".$rows['fID_2']."' AND fID_3 = '".$rows['fID_3']."' AND fID_4 = '".$rows['fID_4']."' AND fID_5 = '".$rows['fID_5']."' AND 
																		 fID_6 = '".$rows['fID_6']."' AND fID_7 = '".$rows['fID_7']."' AND fID_8 = '".$rows['fID_8']."' AND fID_9 = '".$rows['fID_9']."' AND fID_10 = '".$rows['fID_10']."' AND fID_11 = '".$rows['fID_11']."' AND 
																		 fID_12 = '".$rows['fID_12']."' AND fID_13 = '".$rows['fID_13']."' AND fID_14 = '".$rows['fID_14']."' AND fID_15 = '".$rows['fID_15']."' AND fID_16 = '".$rows['fID_16']."' AND fID_17 = '".$rows['fID_17']."' AND fID_18 = '".$rows['fID_18']."' ");
						$countID  = $countID > 0 ? $countID : 0;
						if($countID <= 0)
						{
							$Qry = $this->DB->prepare("INSERT INTO imp_hastus ( companyID, fID_0, fID_1, fID_2, fID_3, fID_4, fID_5, fID_6, fID_7, fID_8, fID_9, fID_10, fID_11, fID_12, fID_13, fID_14, fID_15, fID_16, fID_17, fID_18, fID_19, fID_20, fID_21, userID, logID ) 
							SELECT companyID, fID_0, fID_1, fID_2, fID_3, fID_4, fID_5, fID_6, fID_7, fID_8, fID_9, fID_10, fID_11, fID_12, fID_13, fID_14, fID_15, fID_16, fID_17, fID_18, fID_19, fID_20, '".($hastusTYPE == 'on' ? 'Charter' : 'Routes')."' as hsTYPE, 
							'".$logUS."' AS userID, '".$logDT."' AS logID FROM temp_hastus WHERE temp_hastus.recID = ".$rows['recID']." ");
							$Qry->execute();
							
							$insertCount++;
						}
						else
						{
							$Qry = $this->DB->prepare("UPDATE temp_hastus SET statusID = 1 WHERE recID = ".$rows['recID']." ");
							$Qry->execute();
							
							$updateCount++;
						}
					}
				}
				
				$Qry = $this->DB->prepare("DELETE FROM temp_hastus WHERE randID = ".$randID." AND randDT = '".date('Y-m-d')."' AND statusID <= 0 ");
				$Qry->execute();
				
				$this->msg = urlencode($insertCount.' Records have been imported from the Hastus Sheet. '.($updateCount > 0 ? '<br /> <b style="color:red;"> Total Duplicate records : '.$updateCount.'<b/>' : ''));
				$param = array('a'=>'view','t'=>'success','m'=>$this->msg,'rID'=>$randID);
				$this->Print_Redirect($param,$this->basefile.'?');
			}
			else
			{
				$this->msg = urlencode('Please select the CSV sheet....');
				$param = array('a'=>'view','t'=>'danger','m'=>$this->msg);
				$this->Print_Redirect($param,$this->basefile.'?');
			}
        } 
		 
		else if($optionID == 3 && $optionID > 0)
		{	
			$SQL = "SELECT * FROM imp_hastus WHERE recID > 0 AND DATE(fID_0) BETWEEN '".$this->dateFormat($hfdateID)."' AND '".$this->dateFormat($htdateID)."' AND companyID In(".implode(',',$_REQUEST['filterID']).") Order By companyID, fID_0 ASC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTables" class="table table-bordered table-striped">';
				echo '<thead><tr>';
				echo '<th colspan="23" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Hastus Sheet : Period - ('.$hfdateID.' - '.$htdateID.')</strong></div></th>';
				echo '</tr></thead>';				

				echo '<thead><tr>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong></div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong>Date</strong></div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong>Depot Code</strong></div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Charter</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Start Shift</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Start Whrs</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Start Eslf</div></th>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Split Shift</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Split Whrs</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Split Eslf</div></th>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Casual Shift</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Casual Whrs</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Casual Eslf</div></th>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>PT Shift</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>PT Whrs</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>PT Eslf</div></th>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Inverse Time</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Inverse KMS</div></th>';				
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Total KMS</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Inverse Artic KMS</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Total Artic KM</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Sched Detail</div></th>';
				echo '<th style="padding-left:3px; padding-right:2px;"><div align="center"><strong></strong>Sched Description</div></th>';
				echo '</tr></thead>';
				if(is_array($this->rows_1) && count($this->rows_1) > 0)			
				{
					$totA = array();		$totB = array();		$totC = array();		$totD = array();		$totE = array();		$totF = array();		
					$totG = array();		$totH = array();		$totI = array();		$totJ = array();		$totK = array();		$totL = array();		
					$totM = array();
					
					foreach($this->rows_1 as $rows_1)
					{ 
						echo '<tr>';
								echo '<td align="center"><b><a class="fa fa fa-trash-o Delete_Confirm" data-title="'.$this->tableName.'" data-ajax="'.$rows_1['recID'].'" style="text-decoration:none; cursor:pointer;"></a></b></td>';
								echo '<td style="padding-left:3px; padding-right:2px;" align="center"><b>'.date('d-m-Y',strtotime($rows_1['fID_0'])).'</b></td>';
								echo '<td style="padding-left:3px; padding-right:2px;" align="center"><b>'.$rows_1['fID_1'].'</b></td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_21'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_2'].'</td>';								
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_3'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_4'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_5'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_6'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_7'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_8'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_9'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_10'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_11'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_12'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_13'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_14'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_15'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_16'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_17'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_18'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_19'].'</td>';
								echo '<td style="padding-left:3px; padding-right:2px;">'.$rows_1['fID_20'].'</td>';
						echo '</tr>';	
						
						
						$totA[] = str_replace("h",":",$rows_1['fID_3']);
						$totB[] = str_replace("h",":",$rows_1['fID_4']);
						$totC[] = str_replace("h",":",$rows_1['fID_5']);
						$totD[] = str_replace("h",":",$rows_1['fID_6']);
						$totE[] = str_replace("h",":",$rows_1['fID_7']);
						$totF[] = str_replace("h",":",$rows_1['fID_8']);
						$totG[] = str_replace("h",":",$rows_1['fID_9']);
						$totH[] = str_replace("h",":",$rows_1['fID_10']);
						$totI[] = str_replace("h",":",$rows_1['fID_11']);
						$totJ[] = str_replace("h",":",$rows_1['fID_12']);
						$totK[] = str_replace("h",":",$rows_1['fID_13']);
						$totL[] = str_replace("h",":",$rows_1['fID_14']);
						$totM[] = str_replace("h",":",$rows_1['fID_15']);
						$totN[] = str_replace("h",":",$rows_1['fID_16']);
						$totO[] = str_replace("h",":",$rows_1['fID_17']);
						$totP[] = str_replace("h",":",$rows_1['fID_18']);
					}
					
					echo '<tr>';
							echo '<td class="knob-labels notices" align="center" colspan="5"><b>Grand Total : </b></td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totA).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totB).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totC).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totD).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totE).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totF).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totG).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totH).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totI).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totJ).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totK).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totL).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totM).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totN).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totO).'</td>';
							echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px; padding-left:3px; padding-right:2px;">'.$this->AddPlayTime($totP).'</td>'; 
							echo '<td class="knob-labels notices" colspan="2"></td>';
					echo '</tr>';
					
				}
				echo '</table>';
			}

		}
	}
	
	public function AddPlayTime($times) 
	{
		//echo print_r($times);
		
		$i = 0;
        foreach ($times as $time) 
		{
            sscanf($time, '%d:%d', $hour, $min);
            $i += $hour * 60 + $min;
        }
		
        if($h = floor($i / 60)) 
		{
            $i %= 60;
        }
		
        return sprintf('%02dh%02d', $h, $i);
	}
	
	public function prepareHastus($arrDATA,$dataROWS)
	{
		//echo '<pre>';   echo print_r($arrDATA);		echo '<pre>';   echo print_r($dataROWS);		echo  '<br /> countROWS : '.$countROWS.'<br />';
		
		$countROWS = count($dataROWS);
		$SQL_STRING = "";	$topsID = 1;
		for($startID = 0; $startID <= $countROWS; $startID++)
		{
			if(trim($dataROWS[$startID]) <> '' && ($topsID - 1) <= 20)
			{				
				$SQL_STRING .= ($topsID == 1 ? "fID_0 = '".$this->dateFormat(trim($dataROWS[$startID]))."' ,  " : "");
				$SQL_STRING .= ($topsID >= 2 ? "fID_".($topsID - 1)." = '".trim($dataROWS[$startID])."' , " : "");
				$topsID++;
			}
		}
		
		if($SQL_STRING <> '')
		{
			$SQL_STRING = substr_replace(trim(" INSERT INTO temp_hastus SET randDT = '".date('Y-m-d')."' , randID = ".$arrDATA['randID']." , ".$SQL_STRING),"",-1)."; ";
			
			$Qry = $this->DB->prepare($SQL_STRING);
			$Qry->execute();
			
			$returnSQL += 1;
		}
	}
	
}
?>