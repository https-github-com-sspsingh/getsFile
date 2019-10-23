<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  =	basename($_SERVER['PHP_SELF']);
		$this->tableName     =	$this->getTableName(basename($_SERVER['PHP_SELF']));
                
		$this->frmID	    = '47';
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
                    echo '<th>Shift Code</th>';
                    echo '<th>Half - 1</th>';
                    echo '<th>Half - 2</th>';
                    echo '<th>Hours (Day)</th>';
                    echo '<th>Hours (Weekly)</th>';
                    echo '<th>F/T Days</th>'; 
                    echo '<th>Type</th>'; 
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th></th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th></th>' : '');
                    echo '</tr></thead>';
                    $Start = 1;
                    foreach($this->rows as $row)			
                    {
                        $ST_Array  = $row['stypeID'] > 0 ? $this->select('stype',array("*"), " WHERE ID = ".$row['stypeID']." ") : '';

                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td align="center">'.$row['code'].'</td>';
                        echo '<td align="center">'.$row['stime_1'].' - '.$row['etime_1'].'</td>';
                        echo '<td align="center">'.$row['stime_2'].' - '.$row['etime_2'].'</td>';
                        echo '<td align="center">'.$row['hours_days'].'</td>';
                        echo '<td align="center">'.$row['hours_week'].'</td>';
                        if($row['wtypeID'] == 2)
                        {
                                $dayID = explode(",",$row['wdayID']);
                                $strID = '';
                                $csID = 1;
                                foreach($dayID as $day_ID)
                                {
                                        $strID .= $csID == 1 ? '<b>'.$csID.'</b> : '.$this->GetDayLists($day_ID) : '<br /><b>'.$csID.'</b> : '.$this->GetDayLists($day_ID);
                                        $csID++;
                                }
                                echo '<td>'.$strID.'</td>'; 
                        }
                        else    {echo '<td>'.$this->GetDayLists($row['fdayID']).' - '.$this->GetDayLists($row['tdayID']).'</td>'; }
                        
                        echo '<td>'.$ST_Array[0]['title'].'</td>'; 

                        
                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}

                        echo '</tr>';
                    }
                    echo '</table>';			
		} 
            }
            else
            {
                echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
                      Sorry....you haven\'t permission\'s to view <b>Shift Master</b> Page</div></div>';
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
		
	$countID = 0;
	$countID = $this->max_seriesID('code',$this->tableName, " ");
	$countID = $countID > 0 ? $countID + 1 : '1001';	
	
	echo '<form method="post" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body">';
	
	echo '<div class="row">';
		echo '<div class="col-xs-3">';
			echo '<label for="section">Shift Type <span class="Maindaitory">*</span></label>';
			echo '<select class="form-control" id="stypeID" name="stypeID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
					$Qry = $this->DB->prepare("SELECT * FROM stype Order By title ASC ");
					$Qry->execute();
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->rows as $rows)
					{
						echo '<option value="'.$rows['ID'].'" '.($rows['ID'] == $this->result['stypeID'] ? 'selected="selected"' : '').'>'.$rows['title'].'</option>';
					}
			echo '</select>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Shift Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="ScodeID" name="code" placeholder="Enter Shift Code" style="text-align:center;" required="required"	
					value="'.(!empty($id) ? ($this->result['code']) : $countID).'">';
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Shift Variant</label>';
			echo '<select class="form-control" id="svariantID" name="svariantID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
					echo '<option value="1" '.($this->result['svariantID'] == 1 ? 'selected="selected"' : '').'>Early</option>';
					echo '<option value="2" '.($this->result['svariantID'] == 2 ? 'selected="selected"' : '').'>Late</option>';
					echo '<option value="3" '.($this->result['svariantID'] == 3 ? 'selected="selected"' : '').'>Spilt</option>';
			echo '</select>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row"><br />';

		echo '<div class="col-xs-1"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<h3 class="knob-labels notices" style="font-weight:600; font-size:14px; text-align:center;" >Half 1 : </h3>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Sign On <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="stime_1" name="stime_1" placeholder="Enter Sign On Time" required="required"
			style="text-align:center;" maxlength="5" value="'.(!empty($id) ? ($this->result['stime_1']) : $this->safeDisplay('stime_1')).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Sign Out <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="etime_1" name="etime_1" placeholder="Enter Sign Out Time" required="required"
			style="text-align:center;" maxlength="5" value="'.(!empty($id) ? ($this->result['etime_1']) : $this->safeDisplay('etime_1')).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Hours <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="hours_1" name="hours_1" placeholder="Enter Hours" readonly="readonly"
			style="text-align:center;" value="'.(!empty($id) ? ($this->result['hours_1']) : $this->safeDisplay('hours_1')).'">';
		echo '</div>';
	echo '</div><br />';
	
	echo '<div class="row">';

		echo '<div class="col-xs-1"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<h3 class="knob-labels notices" style="font-weight:600; font-size:14px; text-align:center;" >Half 2 : </h3>';
		echo '</div>';
				
		echo '<div class="col-xs-2">';
			echo '<label for="section">Sign On <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="stime_2" name="stime_2" placeholder="Enter Sign On Time" required="required"
			style="text-align:center;" maxlength="5" value="'.(!empty($id) ? ($this->result['stime_2']) : $this->safeDisplay('stime_2')).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Sign Out <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="etime_2" name="etime_2" placeholder="Enter Sign Out Time" required="required"
			style="text-align:center;" maxlength="5" value="'.(!empty($id) ? ($this->result['etime_2']) : $this->safeDisplay('etime_2')).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Hours <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="hours_2" name="hours_2" placeholder="Enter Hours" readonly="readonly"
			style="text-align:center;" maxlength="5" value="'.(!empty($id) ? ($this->result['hours_2']) : $this->safeDisplay('hours_2')).'">';
		echo '</div>';
	echo '</div><br />';
	
	
	echo '<div class="row">';

		echo '<div class="col-xs-1"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<h3 class="knob-labels notices" style="font-weight:600; font-size:14px; text-align:center;">Working Days : </h3>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Working Type <span class="Maindaitory">*</span></label>';
			$wtypeID = (!empty($id) ? ($this->result['wtypeID']) : $this->safeDisplay('wtypeID'));
			echo '<select name="wtypeID" class="form-control" required="required" id="wtypeID" '.($wtypeID > 0 ? 'disabled="disabled"' : '').'>';
				echo '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
				echo '<option value="1" '.($wtypeID == 1 ? 'selected="selected"' : '').'>Select Range</option>';
				echo '<option value="2" '.($wtypeID == 2 ? 'selected="selected"' : '').'>Specific Days</option>';
			echo '</select>';
		echo '</div>';
				
		echo '<div id="working_rangeID"></div>';
		
		if($wtypeID == 1 && ($id > 0))
		{
			echo '<div class="col-xs-2">';
				echo '<label for="section">F. Day</label>';
				echo '<select name="fdayID" class="form-control" id="fdayID" >';
					$fdayID = (!empty($id) ? ($this->result['fdayID']) : $this->safeDisplay('fdayID'));
					echo '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
					echo '<option value="1" '.($fdayID == 1 ? 'selected="selected"' : '').'>Monday</option>';
					echo '<option value="2" '.($fdayID == 2 ? 'selected="selected"' : '').'>Tuesday</option>';
					echo '<option value="3" '.($fdayID == 3 ? 'selected="selected"' : '').'>Wednesday</option>';
					echo '<option value="4" '.($fdayID == 4 ? 'selected="selected"' : '').'>Thursday</option>';
					echo '<option value="5" '.($fdayID == 5 ? 'selected="selected"' : '').'>Friday</option>';
				echo '</select>';
			echo '</div>'; 
			
			echo '<div class="col-xs-2">';
				echo '<label for="section">T. Day</label>';
				echo '<select name="tdayID" class="form-control" id="tdayID" >';
					$tdayID = (!empty($id) ? ($this->result['tdayID']) : $this->safeDisplay('tdayID'));
					echo '<option value="0" selected="selected" disabled="disabled"> --- Select --- </option>';
					for($fdayID > 0; $fdayID <= 5; $fdayID++)
					{
						$fdayTX = $fdayID == 1 ? 'Monday' :($fdayID == 2 ? 'Tuesday' :($fdayID == 3 ? 'Wednesday' 
								:($fdayID == 4 ? 'Thursday' :($fdayID == 5 ? 'Friday' : ''))));
						
						echo '<option value="'.$fdayID.'" '.($tdayID == $fdayID ? 'selected="selected"' : '').'>'.$fdayTX.'</option>';
					}
				echo '</select>';
			echo '</div>'; 
		}
		
		if($wtypeID == 2 && ($id > 0))
		{
			$wdayID = (!empty($id) ? $this->result['wdayID'] : $this->safeDisplay('wdayID'));
			$exp_wdayID = explode(",",$wdayID);
			
			echo '<div class="col-xs-4">';
				echo '<label for="section">Week Day <span class="Maindaitory">*</span></label>';
				echo '<select name="wdayID[]" class="form-control" id="wdayID" multiple="multiple">';
					echo '<option value="1" '.(in_array(1,$exp_wdayID) ? 'selected="selected"' : '' ).'>Monday</option>';
					echo '<option value="2" '.(in_array(2,$exp_wdayID) ? 'selected="selected"' : '' ).'>Tuesday</option>';
					echo '<option value="3" '.(in_array(3,$exp_wdayID) ? 'selected="selected"' : '' ).'>Wednesday</option>';
					echo '<option value="4" '.(in_array(4,$exp_wdayID) ? 'selected="selected"' : '' ).'>Thursday</option>';
					echo '<option value="5" '.(in_array(5,$exp_wdayID) ? 'selected="selected"' : '' ).'>Friday</option>';
					echo '<option value="6" '.(in_array(6,$exp_wdayID) ? 'selected="selected"' : '' ).'>Saturday</option>';
					echo '<option value="7" '.(in_array(7,$exp_wdayID) ? 'selected="selected"' : '' ).'>Sunday</option>';
				echo '</select>';	
			echo '</div>';	
		}
		
	echo '</div><br />';
	
	echo '<div class="row">';	 
		echo '<div class="col-xs-3"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Hours(per day)</label>';
			echo '<input type="text" class="form-control" id="hours_days" name="hours_days" placeholder="Enter Per Day (Hours)" readonly="readonly"
			style="text-align:center;" value="'.(!empty($id) ? ($this->result['hours_days']) : $this->safeDisplay('hours_days')).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Weekly (Hours)</label>';
			echo '<input type="text" class="form-control" id="hours_week" name="hours_week" placeholder="Enter Weekly (Hours)" style="text-align:center;" 
			readonly="readonly"	value="'.(!empty($id) ? ($this->result['hours_week']) : $this->safeDisplay('hours_week')).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			$apID = (!empty($id) ? ($this->result['apID']) : $this->safeDisplay('apID'));
			echo '<label for="section">MEAL AT &nbsp;';
			echo '<span class="Maindaitory">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Not - Applicable &nbsp; 
			<input name="apID" id="apID" type="checkbox" value="1" '.($apID == 1 ? 'checked="checked"' : '').'/></span></label>';
			echo '<input type="text" class="form-control" id="mealID" name="mealID" placeholder="Enter MEAL at"	
				  '.(($apID == 1 ? 'readonly="readonly"' : '')).'	value="'.(!empty($id) ? ($this->result['mealID']) : $this->safeDisplay('mealID')).'">';
		echo '</div>'; 
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Master' : 'Save Master').'</button>';
	  echo '</div>';
          
        echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
        echo '</div>';
        
	echo '</div>';
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);						   //echo '<PRE>'; echo print_r($_POST); exit;
			
			if($stypeID == '') 	 	 				$errors .= "Enter The Shift Catgeory Type.<br>";
			if($code == '') 	 	 				   $errors .= "Enter The Shift Code.<br>";
			if($stime_1 == '') 	 	 				$errors .= "Enter The Half - 1 Sign On Time.<br>";
			if($etime_1 == '') 	 	 				$errors .= "Enter The Half - 1 Sign Out Time.<br>";
			if($stime_2 == '') 	 	 				$errors .= "Enter The Half - 2 Under Sign On Time.<br>";
			if($etime_2 == '') 	 	 				$errors .= "Enter The Half - 2 Under Sign Out Time.<br>";
			if($wtypeID == 0 ) 						$errors .= "Select The Working Type.<br>";
									
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			
			else
			{	 
				$query = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE code =:code "); 
				$query->bindParam(':code',$code);
				$query->execute();
				$this->result = $query->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Shift Code Is Already exist !..');
					$this->createForm();
				}
				else
				{
					$_POST['status'] = 1;
					unset($_POST['Submit'],$_POST['statusID'],$_POST['wdayID']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['wdayID'] = implode(",",$wdayID);
					$array['insert_date']	= date('Y-m-d H:i:s');
					
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$Qry = $this->DB->prepare("SELECT stypeID FROM ".$this->tableName." Group By stypeID Order By stypeID ASC ");
						$Qry->execute();
						$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
						foreach($this->row as $row)
						{
							$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND stypeID = ".$row['stypeID']." Order By ID ASC ");
							$Qry->execute();
							$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
							$cuID = 1;
							foreach($this->rows as $rows)
							{
								$ars = array();
								$ars['srID'] = $cuID++;
								$ons['ID'] = $rows['ID'];
								$this->BuildAndRunUpdateQuery($this->tableName,$ars,$ons);
							}
						}
						
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
						$this->msg = urlencode(' Shifts Master Is Created (s) Successfully . <br /> Code : '.$array['code']);
						
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
			
			if($stypeID == '') 	 	 				$errors .= "Enter The Shift Catgeory Type.<br>";
			if($code == '') 	 	 				   $errors .= "Enter The Shift Code.<br>";
			if($stime_1 == '') 	 	 				$errors .= "Enter The Half - 1 Sign On Time.<br>";
			if($etime_1 == '') 	 	 				$errors .= "Enter The Half - 1 Sign Out Time.<br>";
			if($stime_2 == '') 	 	 				$errors .= "Enter The Half - 2 Under Sign On Time.<br>";
			if($etime_2 == '') 	 	 				$errors .= "Enter The Half - 2 Under Sign Out Time.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 	
				$query = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE code =:code AND ID <> :ID ");
				$query->bindParam(':code',$code);
				$query->bindParam(':ID',$ID);				
				$query->execute();
				$this->result = $query->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Shift Code Is Already exist !..');
					$this->createForm($ID);
				}
				else
				{ 
					unset($_POST['Submit'],$_POST['ID'],$_POST['wdayID']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['wdayID'] = implode(",",$wdayID);
					$on['ID']	  			= $ID;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 					
						$Qry = $this->DB->prepare("SELECT stypeID FROM ".$this->tableName." Group By stypeID Order By stypeID ASC ");
						$Qry->execute();
						$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
						foreach($this->row as $row)
						{
							$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND stypeID = ".$row['stypeID']." Order By ID ASC ");
							$Qry->execute();
							$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
							$cuID = 1;
							foreach($this->rows as $rows)
							{
								$ars = array();
								$ars['srID'] = $cuID++;
								$ons['ID'] = $rows['ID'];
								$this->BuildAndRunUpdateQuery($this->tableName,$ars,$ons);
							}
						}
						
						$this->msg = urlencode(' Shifts Master Is Updated (s) Successfully . <br /> Code : '.$array['code']);					
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
	}
}
?>