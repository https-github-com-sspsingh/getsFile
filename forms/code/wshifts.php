<?PHP
class Masters extends SFunctions
{
	private	$tableName = '';
	private	$basefile  = '';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile  = basename($_SERVER['PHP_SELF']);
		$this->tableName = 'w_shifts';
                
		$this->frmID	    = '46';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
            if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
            {    
		$query = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY ID DESC ");
		if($query->execute())
		{
                    $this->rows = $query->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>';
                    echo '<th>Sr. No.</th>';
                    echo '<th>From Date</th>';
                    echo '<th>To Date</th>';
                    echo '<th>Shift Days</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th></th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th></th>' : '');
                    echo '</tr></thead>';
                    $Start = 1;
                    foreach($this->rows as $row)			
                    {
                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td align="center">'.date('d-M-Y',strtotime($row['fdateID'])).'</td>';
                        echo '<td align="center">'.date('d-M-Y',strtotime($row['tdateID'])).'</td>';
                        echo '<td align="center">'.$row['daysID'].'</td>';

                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}

                        echo '</tr>';
                    }
                    echo '</table>';			
		} 
            }
            else
            {
                echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
                      Sorry....you haven\'t permission\'s to view <b>Weekly Roster</b> Page</div></div>';
            }
	} 
	
	public function createForm($id='')
	{
		$this->action = 'add';
		if(!empty($id))
		{
			$query = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID=:ID ");
			$query->bindParam(':ID',$id);
			$query->execute();
			$this->result = $query->fetch(PDO::FETCH_ASSOC);
			$this->action = 'edit';
		}
		
	echo '<form method="post" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body">';
	
	$RD = $id > 0 ? 'readonly="readonly" disabled="disabled"' : '';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-4">';
			echo '<label for="section">Shift Type Category <span class="Maindaitory">*</span></label>';
			echo '<select class="form-control" name="scategoryID" id="scategoryID" '.$RD.'>';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
			  $Qry = $this->DB->prepare("SELECT * FROM stype Order By title ASC ");
			  $Qry->execute();
			  $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($this->rows as $rows)
			  {
				  echo '<option value="'.$rows['ID'].'" '.($rows['ID'] == $this->result['scategoryID'] ? 
				  'selected="selected"' : '').'>'.$rows['title'].'</option>';
			  }
			echo '</select>';
		echo '</div>'; 
		
	if(!empty($id) && ($this->result['flagID'] <> 1))
	{
		echo '<div class="col-xs-4">';
			echo '<label for="section">&nbsp;</label><br />';
			echo '<a class="btn btn-success" href="'.$this->home.'wshifts.php?i='.$this->Encrypt($id).'&a='.$this->Encrypt('allocate').'">
			Allot Weekend</a>';
		echo '</div>'; 
	}
	echo '</div><br />';
		
	echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">From Date <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control datepicker" id="fdateID" name="fdateID" placeholder="Enter From Date" 
			style="text-align:center;" required="required" '.$RD.'
			value="'.(!empty($id) ? $this->VdateFormat($this->result['fdateID']) : $this->VdateFormat($this->safeDisplay['fdateID'])).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">To Date <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="tdateID" name="tdateID" placeholder="Enter To Date" 
			style="text-align:center;" required="required" readonly="readonly"
			value="'.(!empty($id) ? $this->VdateFormat($this->result['tdateID']) : $this->VdateFormat($this->safeDisplay['tdateID'])).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">&nbsp;</label><br />';
			echo '<input type="button" class="btn btn-primary btn-flat genscheduleID" '.$RD.' value="Generate Schedule" />';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Valid Till Date <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control datepicker" id="vldateID" name="vldateID" placeholder="Enter Valid Till Date" 
			style="text-align:center;" required="required" '.$RD.'
			value="'.(!empty($id) ? $this->VdateFormat($this->result['vldateID']) : date('d/m/Y',strtotime('sunday +24 week'))).'">';
		echo '</div>';
		
		echo '<div class="col-xs-1"></div>';
		
	  echo '<div class="col-xs-3" '.($id > 0 ? '' : 'id="submit_buttonID" style="display:none;"').'>';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<label for="section">&nbsp;</label><br />';
		  echo '<button class="btn btn-danger" name="Submit" type="submit">
		  '.(!empty($id) ? 'Update Weekly Shift Schedular' : 'Save Weekly Shift Schedular').'</button>';
	  echo '</div>';
	  
	echo '</div>';
	
	echo '<div id="schedularDIV_ID"></div>';
	
	echo $this->createform_Child($id);
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>'; 
	echo '</div>';
	echo '</form>';
	}
	
	public function createform_child($ID)
	{
		if(!empty($ID))
		{
			$FL_Array = $this->select($this->tableName,array("*"), " WHERE ID = ".$ID." ");
			
			$file = '';
			$styleID = 'style="color:white;background:#317299;text-align:center;"';
			
			$dateID_1 = $FL_Array[0]['fdateID'];
			$dateID_2 = $FL_Array[0]['tdateID'];
			$diffID = strtotime($dateID_2) - strtotime($dateID_1);
			$lasID = floor($diffID / (60 * 60 * 24)) + 1;
			
			$file .= '<br /><br /><table id="dataTables" class="table table-bordered table-striped">';				
			$file .= '<thead><tr>';
			$file .= '<th '.$styleID.'>Sr. No.</th>';
			$file .= '<th '.$styleID.'>Emp - ID</th>';
			$file .= '<th '.$styleID.'>Emp - Name</th>';
			for($srID = 0; $srID < 7; $srID++)
			{
				$dateID = strtotime($dateID_1.'+'.$srID.'Days');
				$file .= '<th '.$styleID.'>'.date('D',$dateID).'<br />'.date('d-M-Y',$dateID).'</th>';
			}
			
			$file .= '<th '.$styleID.'>Weekday Hours</th>';
			$file .= '</tr></thead>';
			
			
			$Qry = $this->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$ID." AND rptID = 0 Order By recID ASC ");
			$Qry->execute();
			$this->result = $Qry->fetchAll(PDO::FETCH_ASSOC);
			
			$counterID = 1;
			if(is_array($this->result) && (count($this->result) > 0))
			{
				$scodeID = '';
				foreach($this->result as $rows)
				{
					$EMPL = $this->select('employee',array("*"), " WHERE ID = ".$rows['empID']." ");
					
					$SH_1 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_1']." ");
					$SH_2 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_2']." ");
					$SH_3 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_3']." ");
					$SH_4 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_4']." ");
					$SH_5 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_5']." ");
					$SH_6 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_6']." ");
					$SH_7 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_7']." ");
					
					$scodeID .= $SH_Array[0]['ID'].'_'.$SH_Array[0]['code'].'/';
					
					$file .= '<tr>';
					$file .= '<td align="center">'.$counterID++.'</td>';
					$file .= '<td align="center">'.$EMPL[0]['code'].'</td>';
					$file .= '<td>'.$EMPL[0]['fname'].' '.$EMPL[0]['lname'].'</td>';
					
					$file .= '<td align="center">'.$SH_1[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_2[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_3[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_4[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_5[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_6[0]['code'].'</td>';
					$file .= '<td align="center">'.$SH_7[0]['code'].'</td>'; 
					
					$file .= '<td align="center">'.$rows['hoursID'].'</td>';
					$file .= '</tr>';
				}
			}
			
			$file .= '</table>';
		}
		
		return $file;
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);						 		  //echo '<pre>'; echo print_r($_POST); exit;
			
			if($fdateID == '') 	 	 					   $errors .= "Enter The F.Day.<br>";
			if($tdateID == '') 	 	 					   $errors .= "Enter The T.Day.<br>";
			if(is_array($empID) && (count($empID) == 0))	{$errors .= "Please Specify The Employee Name.<br>";}
			if($vldateID == '') 	 	 				      $errors .= "Enter The Roster Valid Till Date.<br>";
			if($scategoryID == '') 	 	 				   $errors .= "Select The Shift Type Category.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			
			else
			{
				$query = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE fdateID =:fdateID AND tdateID =:tdateID "); 
				$query->bindParam(':fdateID',$this->dateFormat($fdateID));
				$query->bindParam(':tdateID',$this->dateFormat($tdateID));
				$query->execute();
				$this->result = $query->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','This Dates Range Already exist in our Shift Database !..');
					$this->createForm();
				}
				else
				{
					$array = array();
					$array['scategoryID'] = $scategoryID;
					$array['fdateID']	 = $this->dateFormat($fdateID);
					$array['tdateID']	 = $this->dateFormat($tdateID);
					$array['vldateID']	= $this->dateFormat($vldateID);
					$array['daysID']	  = floor((strtotime($this->dateFormat($tdateID)) - strtotime($this->dateFormat($fdateID))) / (60 * 60 * 24)) + 1;				
					$array['status']	  = 1;
					$array['insert_date'] = date('Y-m-d H:i:s');
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						if(is_array($empID) && count($empID) > 0 && $lastID[0] > 0)
						{
							foreach($empID as $key=>$valueID)
							{
								$arr = array();
								$arr['ID'] 		  = $lastID[0];
								$arr['segID'] 	   = $segID[$key];
								$arr['empID'] 	   = $valueID; 
								$arr['srID'] 		= $srID[$key];
								$arr['dateID'] 	  = $dateID[$key];
								$arr['dayID'] 	   = $dayID[$key];
								$arr['shiftID'] 	 = $shiftID[$key];
								$arr['signinID_1']  = $signinID_1[$key];
								$arr['signoutID_1'] = $signoutID_1[$key];
								$arr['hoursID_1']   = $hoursID_1[$key];
								$arr['signinID_2']  = $signinID_2[$key];
								$arr['signoutID_2'] = $signoutID_2[$key];
								$arr['hoursID_2']   = $hoursID_2[$key];
								$arr['thoursID']    = $thoursID[$key];
								$arr['shiftcode']   = $shiftcode[$key]; 
								$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);
							}
						}
						
						$this->generate_SHGraders($lastID[0]);
						$this->msg = urlencode(' Weekly Shifts Roster Is Created (s) Successfully ..');
						$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$lastID[0]);						
						$this->Print_Redirect($param,$this->basefile.'?');							
					}
					else
					{ 
						$this->msg = urlencode('Error In Insertion. Please try again...!!!');
						$this->printMessage('danger',$this->msg);
						$this->createForm();  
					}
				}
			}
		}
	}
	
	public function update()	
	{
		if($this->Form_Variables() == true)	//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);
			
			$errors	=	'';
			 
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 	
				$array = array();
				$array['status'] = '1';
				$on['ID']	  	= $ID;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{ 					
					$this->msg = urlencode(' Weekly Roster Master Is Updated (s) Successfully ..');					
					$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);						
					$this->Print_Redirect($param,$this->basefile.'?');								
				}
				else
				{ 
					$this->msg	=	urlencode('Error In Updation. Please try again...!!!');
					$this->printMessage('danger',$this->msg);
					$this->createForm($ID);						
				}  
			}
		}
	}
	
	public function generate_SHGraders($requestID)
	{	
		if(!empty($requestID))
		{
			$PR_Array = $this->select('w_shifts',array("*"), " WHERE ID = ".$requestID." ");
			$dayID = $PR_Array[0]['daysID'];
			
			$Qry = $this->DB->prepare("SELECT segID,ID,empID FROM w_shifts_dtl WHERE ID > 0 AND ID = ".$requestID." Group By segID,ID,empID 
			Order By segID,ID,empID ASC ");
			$Qry->execute();
			$this->result = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->result) && count($this->result) > 0 && $requestID > 0)
			{
				$counterID = 1;
				$srID = 1;
				foreach($this->result as $row)
				{
					$CH_Array = $this->select('w_shifts_dtl',array("*"), " WHERE ID = ".$requestID." AND empID = ".$row['empID']." AND srID = ".$srID." ");
					
					$arr = array();
					$arr['counterID'] = $counterID++;					
					$arr['empID'] 	 = $row['empID'];
					$arr['reqID']	 = $row['ID'];
					$arr['segID']	 = $row['segID'];
					$arr['vldateID'] = $PR_Array[0]['vldateID'];
					$arr['sdateID']  = $PR_Array[0]['fdateID'];
					$arr['edateID']  = $PR_Array[0]['tdateID'];
					$arr['dayID']    = $PR_Array[0]['daysID'];
					
					for($lasID = 1; $lasID <= $PR_Array[0]['daysID']; $lasID++)
					{
						$arr['shiftID_'.$lasID] = $lasID == 6 || $lasID == 7 ? '0' : $CH_Array[0]['shiftID'];
					} 
					
					$arr['shiftcode'] = $CH_Array[0]['shiftcode'];					
					$arr['hoursID_1'] = $this->getsum_hoursID($CH_Array[0]['hoursID_1'],($PR_Array[0]['daysID'] - 2));
					$arr['hoursID_2'] = $this->getsum_hoursID($CH_Array[0]['hoursID_2'],($PR_Array[0]['daysID'] - 2));
					$arr['hoursID']   = $this->getsum_hoursID($CH_Array[0]['thoursID'],($PR_Array[0]['daysID'] - 2));
					
					$this->BuildAndRunInsertQuery('w_shifts_grader',$arr);
					
					$srID = $srID == $dayID ? 1 : $srID++;
				}
			}
		}
	}
	
	public function getsum_hoursID($hoursID,$lasID)
	{ 
		if(!empty($hoursID) && !empty($lasID))
		{
			$setID_5 = '';
			list($hrID_5,$minID_5,$secID_5) = explode(':',$hoursID);
			for($srID = 0; $srID < $lasID; $srID++)
				{$setID_5 += $hrID_5 * 3600;		$setID_5 += $minID_5 * 60;		$setID_5 += $secID_5;}
					
			  $set_hrID_5   = floor($setID_5 / 3600);		$setID_5  -= $set_hrID_5 * 3600;
			  $set_minID_5  = floor($setID_5 / 60);		  $setID_5  -= $set_minID_5 * 60;			
		} 
		return ($set_hrID_5.':'.$set_minID_5);
	}
	
	public function allocateID($ID)
	{
		if(!empty($ID ))
		{
			$this->Update_SatudaySunday($ID);
			
			$PR_Array = $this->select('w_shifts',array("*"), " WHERE ID = ".$ID." ");
			$vldateID = $PR_Array[0]['vldateID'];
			
			$diffID = (floor((strtotime($PR_Array[0]['vldateID']) - strtotime($PR_Array[0]['fdateID'])) / (60 * 60 * 24)) + 1) / 7;
			if($diffID > 0)
			{
				$ndateID = '';
				for($srID = 1; $srID <= $diffID; $srID++)
				{
					if($ndateID <> '')
					{ 
						if(strtotime($ndateID) < strtotime($vldateID))	{$this->regen_schedule($ID,$ndateID);}
					}
					else
					{
						$ndateID = date('Y-m-d',strtotime($PR_Array[0]['fdateID'].'+7 Days'));
						if(strtotime($ndateID) < strtotime($vldateID))	  {$this->regen_schedule($ID,$ndateID);}
					}
					
					$ndateID = date('Y-m-d',strtotime($ndateID.'+7 Days'));
				}
			} 
			
				$arr = array();
				$arr['flagID'] = '1';
				$ons['ID'] = $ID;
				if($this->BuildAndRunUpdateQuery('w_shifts',$arr,$ons))
				{
					$this->update_lastsegmentID($ID);
				}
				
				$this->msg = urlencode(' Weekly Shifts Roster Pendency Is Updated (s) Successfully ..');
				$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);						
				$this->Print_Redirect($param,$this->basefile.'?');							
		}
	}
	
	public function Update_SatudaySunday($ID)
	{
		if(!empty($ID) && ($ID > 0))
		{
			/* START - UPDATE SHIFTS (6/7) = 0 */
				$Qry = $this->DB->prepare("UPDATE w_shifts_grader SET shiftID_6 = 0 , shiftID_7 = 0 WHERE reqID = ".$ID." ");
				$Qry->execute();
			/* END - UPDATE SHIFTS (6/7) = 0 */
			
			$PR_Array = $this->select('w_shifts',array("*"), " WHERE ID = ".$ID." ");
			
			$Qry = $this->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$ID." AND sdateID = '".$PR_Array[0]['fdateID']."' 
			Order By empID,segID ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			$preID = 1;
			$csID = '';	   $SETimeID = '';
			foreach($this->rows as $rows)
			{
				$SH_Array = $rows['shiftID_1'] > 0 ? $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_1']." ") : '';
				
				$ST_Array = $this->select('shifts',array("*"), " WHERE stypeID In(4) AND flagID = 0 Order By ID DESC ");
				$SU_Array = $this->select('shifts',array("*"), " WHERE stypeID In(5) AND flagID = 0 Order By ID DESC ");

				/* GET - RESUME - VALUES */
				$SETimeID = $SH_Array[0]['etime_2'];
				$resumeID = $SETimeID > '12' ? $this->gettotallessID('12',$SH_Array[0]['etime_2']) : $this->gettotalsumID($SH_Array[0]['etime_2'],'10');
				$resumeID = abs($this->gettotalsumID($resumeID,'10')) - 12;
				
				if(abs($rows['hoursID'])<40) /*> 80)*/
				{ 
					/* ODD - CASINGS */
					if((((integer)$rows['empID'] % 2 != 0)|| $preID2==3) && (abs($ST_Array[0]['stime_1']) >= abs($resumeID)) && ($preID == 1))
					{
						$arr_1 = array();
						$arr_1['shiftID_6'] = $ST_Array[0]['ID'];
						$ons_1['recID'] 	 = $rows['recID'];
						if($this->BuildAndRunUpdateQuery('w_shifts_grader',$arr_1,$ons_1))
						{
							$this->flagged_shiftID($ST_Array[0]['ID']);
							$preID = 1;
							$preID2 = 2;
							
						}
					}
					/* EVEN - CASINGS */
					else if(abs($SU_Array[0]['stime_1']) >= abs($resumeID))
					{
						$arr_3 = array();
						$arr_3['shiftID_7'] = $SU_Array[0]['ID'];
						$ons_3['recID'] 	 = $rows['recID'];
						if($this->BuildAndRunUpdateQuery('w_shifts_grader',$arr_3,$ons_3))
						{
							$this->flagged_shiftID($SU_Array[0]['ID']);
							$preID = 2;
							$preID2=3;
						}
					}
					else if((abs($ST_Array[0]['stime_1']) >= abs($resumeID)) && ($preID == 1))
					{
						$arr_4 = array();
						$arr_4['shiftID_6'] = $ST_Array[0]['ID'];
						$ons_4['recID'] 	 = $rows['recID'];
						if($this->BuildAndRunUpdateQuery('w_shifts_grader',$arr_4,$ons_4))
						{
							$this->flagged_shiftID($ST_Array[0]['ID']);
							$preID = 1;
							$preID2 = 2;
						}
					}	 			
				}
				else 
				{
					$preID2 = 3; 
					$preID = 1;
				}
			}			
			$this->un_flagged_shiftID();
		}
	}
	
	public function gettotalsumID($timeID_1,$timeID_2)
	{
		$return = '';
		if(!empty($timeID_1) && !empty($timeID_2))
		{
		  $times = array($timeID_1, $timeID_2);
		  $seconds = 0;
		  foreach ($times as $time)
		  {
			list($hour,$minute,$second) = explode(':', $time);
			$seconds += $hour*3600;
			$seconds += $minute*60;
			$seconds += $second;
		  }
		  $hours   = floor($seconds/3600);
		  $seconds -= $hours*3600;
		  $minutes  = floor($seconds/60);
		  $seconds -= $minutes*60;
		  
		  $return = ($hours > 0) || ($minutes > 0) ? "{$hours}:{$minutes}" : "00:00";
		}
		
		return $return;
	}
	
	public function gettotallessID($timeID_1,$timeID_2)
	{
		$return = '';
		
		if(!empty($timeID_1) && !empty($timeID_2))
		{
			$totalID = strtotime(trim($timeID_2.':00')) - strtotime(trim($timeID_1.':00'));
			$hoursID = floor($totalID / 60 / 60);
			$minutID = round(($totalID - ($hoursID * 60 * 60)) / 60,2);
			
			$return = (($hoursID > 0) || ($minutID > 0)) <> '' ? (trim($hoursID.':'.$minutID)) : '00:00';
		}
		
		return $return;
	}
	
	public function flagged_shiftID($ID)
	{
		if(!empty($ID))
		{
			$ars = array();
			$ars['flagID'] = 1;
			$ons['ID'] = $ID;
			$this->BuildAndRunUpdateQuery('shifts',$ars,$ons);
		}
	}
	
	public function un_flagged_shiftID()
	{
		$Qry = $this->DB->prepare("UPDATE shifts SET flagID = 0 ");
		$Qry->execute();
	}
	
	public function update_lastsegmentID($ID)
	{
		if(!empty($ID))
		{
			$PR_Array = $this->select('w_shifts',array("*"), " WHERE ID = ".$ID." ");
			$Qry = $this->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$ID." AND sdateID = '".$PR_Array[0]['fdateID']."' Order By recID ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
			foreach($this->rows as $row)
			{
				$this->pre_replaceID($row['sdateID'],$row['shiftID_1'],$row['shiftID_6'],$row['shiftID_7'],$row['reqID']);
			}
		}
	}
	
	public function pre_replaceID($dateID,$shiftID,$fieldID_1,$fieldID_2,$ID)
	{
		$Qry = $this->DB->prepare("UPDATE w_shifts_grader SET shiftID_6 = '".$fieldID_1."' , shiftID_7 = '".$fieldID_2."' 
		WHERE reqID = ".$ID." AND sdateID <> '".$dateID."' AND shiftID_1 = ".$shiftID." ");
		$Qry->execute();
	}
	 
}
?>