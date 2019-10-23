<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		

		$this->basefile	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->frmID		= '39';
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view($searchbyID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			/* SEARCH BY  -  OPTIONS */
			$src = "";			
			$src = (!empty($searchbyID) && ($searchbyID <> '') ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' Or (".$this->tableName.".ecodeID LIKE '%".$searchbyID."%' Or prpermits_dtl.fileID_1 LIKE '%".$searchbyID."%' Or prpermits_dtl.fileID_2 LIKE '%".$searchbyID."%') " : "");

			$inc = "";
			$inc = (!empty($searchbyID) && ($searchbyID <> '') ? " LEFT JOIN prpermits_dtl ON prpermits_dtl.ID = prpermits.ID " : "");
			/* SEARCH BY  -  OPTIONS */

			$ins = "";
			$ins = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? "" : " GROUP BY prpermits.ID ")) : (""));

			$empsID = "";
			$empsID = (!empty($searchbyID) && ($searchbyID <> '') ? "" : "AND employee.status = 1 ");

			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".empID ".$inc." 
			WHERE ".$this->tableName.".ID > 0 ".$empsID." AND (".$this->tableName.".companyID In (".$this->companyID.")) ".$src." ".$ins." 
			ORDER BY ".$this->tableName.".ID DESC ";			
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>'; 
				echo '<th>Employee Code</th>';
				echo '<th>Employee Name</th>';
				echo '<th>Contractor Name</th>';
				echo '<th>Parking Permit Details</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
				echo '</tr></thead>'; 
				foreach($this->rows as $row)			
				{ 
					$arrEM  = ($row['empID'] > 0  ? $this->select('employee',array("ID,fname,lname,code"), " WHERE ID = ".$row['empID']." ") : '');
					
					// && ($arrEM[0]['status'] == 1)
				
					if($row['empID'] > 0)
					{
						/* VOUCHER - DETAILS */
						if($row['ID'] > 0)
						{
							$Qry = $this->DB->prepare("SELECT * FROM prpermits_dtl WHERE ID = :ID AND (fileID_4 = '0000-00-00' Or fileID_4 IS NULL) ORDER BY ID DESC ");
							$Qry->bindParam(':ID',$row['ID']);
							$Qry->execute();
							$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
							$srID = 1;
							$stID = '';
							foreach($this->row as $rows)
							{
								$stID .= rtrim(trim(($srID == 1 ? '<b style="color:#DF0070;">'.$srID.'.</b> '.$rows['fileID_1'].' , '.$rows['fileID_2'].' , '.$this->PRdateFormat($rows['fileID_3']).' , '.$this->PRdateFormat($rows['fileID_4']) : '<br /><b style="color:#DF0070;">'.$srID.'.</b> '.$rows['fileID_1'].' , '.$rows['fileID_2'].' , '.$this->PRdateFormat($rows['fileID_3']).' , '.$this->PRdateFormat($rows['fileID_4']))),',');
								$srID++;
							}
						}

						echo '<tr>'; 
						echo '<td align="center">'.$row['ecodeID'].'</td>';
						echo '<td>'.($row['tempID'] == 1 ? ($arrEM[0]['fname'].' '.$arrEM[0]['lname']) : $row['empname']).'</td>';
						echo '<td>'.$row['contractorID'].'</td>';
						echo '<td>'.$stID.'</td>';
						
						if(($row['companyID'] == $_SESSION[$this->website]['compID']) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
						{
							echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';
						}
						else    {echo '<td></td>';}
							
						if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							if((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days')))	{echo '<td></td>';}
							else
							{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
						}
						
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{ 
							if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Parking Permits').'" style="text-decoration:none; cursor:pointer;"></a></td>';
							}
							else	{echo '<td></td>';}
						}
					}
				}
				echo '</table>';
			} 
		}
		else	{echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>this</b> Page</div></div>';}
	} 
	
	public function createForm($id='')
	{
		$this->action = 'add';
		if(!empty($id))
		{
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID=:ID ");
			$Qry->bindParam(':ID',$id);
			$Qry->execute();
			$this->result = $Qry->fetch(PDO::FETCH_ASSOC);			
			$this->action = 'edit';
		}
		
	echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body" id="fg_membersite">';
	
	echo '<div class="row">';  
		$tempID = (!empty($id) ? $this->result['tempID'] : '1');		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Swan Transit Employee </label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="tempID" id="tempID" value="1" '.(($tempID == 1) ? 'checked="checked"' : '').' />';
		echo '</div>';
	echo '</div><br />';
	
	echo '<div class="row" id="gridID_1" '.($tempID == 1 ? '' :'style="display:none;"').'>'; 
		echo '<div class="col-xs-4">';
			echo '<label for="section">Employee Name <span class="Maindaitory">*</span></label>';
			$empID = !empty($id) ? $this->result['empID'] : $this->safeDisplay['empID'];
			$arrDB = $empID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$empID.'">';
				echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
			}
			else
			{
				if($empID > 0)
				{
					echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$empID.'">';
					echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';	
				}
				else
				{
					echo '<select onchange="changes=true;" class="form-control select2" style="width:100%" id="empID" name="empID">';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select Employee --</option>';                    
					echo $this->GET_Employees($empID,"AND status = 1"); 
					echo '</select>';
					echo '<span id="register_empID_errorloc" class="errors"></span>';
				}
			}
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Employee Code</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="ecodeID" placeholder="Employee Code" readonly="readonly" 
			style="text-align:center;" value="'.(!empty($id) ? $this->result['ecodeID'] : '').'">';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Contractor</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="contractorID" name="contractorID" placeholder="Enter Contractor" '.($id > 0 ? 'readonly="readonly"' : '').' value="'.(!empty($id) ? $this->result['contractorID'] : 'Swan Transit').'">';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row" id="gridID_2"  '.($tempID == 1 ? 'style="display:none;"' :'').'>'; 
		  echo '<div class="col-xs-4">';
			  echo '<label for="section">Employee Name <span class="Maindaitory">*</span></label>';
			  echo '<input type="text" onchange="changes=true;" class="form-control" required="required" id="empname" name="empname" placeholder="Employee Name" value="'.(!empty($id) ? $this->result['empname'] : '').'">';                          
			  echo '<span id="register_empname_errorloc" class="errors"></span>';
		  echo '</div>';
		  
		echo '<div class="col-xs-2">';
			echo '<label for="section">Employee Code</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="ecodeID_1" placeholder="Employee Code" readonly="readonly"
			style="text-align:center;" value="'.(!empty($id) ? $this->result['ecodeID'] : '').'">';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Contractor</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="contractorID_1" name="contractorID_1" placeholder="Enter Contractor" 
			value="'.(!empty($id) ? $this->result['contractorID'] : '').'">';
                        
                        echo '<span id="register_contractorID_1_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
            echo '<div class="col-xs-2">';
                echo '<input type="button" style="margin-top: 25px;" class="btn bg-orange btn-flat margin" id="prpmgridID" value="ADD - New Row" />';
            echo '</div>';
            
            echo '<div class="col-xs-9">';
                    echo '<h3 class="knob-labels notices" style="font-weight:600; font-size:14px; text-align:left;">Issue Permit Details : </h3>';
            echo '</div>'; 	
	echo '</div>';
        
	
	echo '<div class="row" id="dataTablesID">';
		if(!empty($id) && ($id > 0))
		{
			$this->create_childForm($id);	
		}
	echo '</div>';
	
	echo '<div class="row">';
          echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
          
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Parking Permits' : 'Save Parking Permits').'</button>';
	  echo '</div>';
           
        echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
        echo '</div>';
        
	echo '</div>';
	echo '</div>';
	echo '</form>';
	}
	
	public function create_childForm($ID)
	{
		if(!empty($ID) && ($ID > 0))
		{
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName."_dtl WHERE ID=:ID ");
			$Qry->bindParam(':ID',$ID);
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->rows) && (count($this->rows) > 0))
			{
				$retrunDT = 0;
				foreach($this->rows as $rows)
				{ 
					echo '<div class="col-xs-12"><br />';					
						echo '<div class="col-xs-1"><br />';
						echo '<input type="button" aria-sort="'.$rows['recID'].'" class="btn bg-olive btn-flat margin remove_ppermits" value="X" />';
						echo '</div>';
						
						echo '<div class="col-xs-3">';
						echo '<label for="section">Vehicle Reg No</label>';
						echo '<input type="text" onchange="changes=true;" class="form-control" '.(!empty($rows['fileID_1']) ? 'readonly="readonly"' : '').' name="fileID_1[]" placeholder="Enter Vehicle Reg No" value="'.$rows['fileID_1'].'">';
						echo '</div>';
						
						echo '<div class="col-xs-3">';
						echo '<label for="section">Permit No</label>';
						echo '<input type="text" onchange="changes=true;" class="form-control" '.(!empty($rows['fileID_2']) ? 'readonly="readonly"' : '').' name="fileID_2[]" placeholder="Enter Permit No" value="'.$rows['fileID_2'].'">';
						echo '</div>';
						
						echo '<div class="col-xs-2">';
						echo '<label for="section">Issue Date</label>';
						echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" '.(!empty($this->VdateFormat($rows['fileID_3'])) ? 'readonly="readonly"' : '').' data-datable="ddmmyyyy" name="fileID_3[]" style="text-align:center;" value="'.$this->VdateFormat($rows['fileID_3']).'"  placeholder="Enter Issue Date">';
						echo '</div>';
						
						echo '<div class="col-xs-2">';
						echo '<label for="section">Returned Date</label>';
						echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" name="fileID_4[]" style="text-align:center;" value="'.$this->VdateFormat($rows['fileID_4']).'"  placeholder="Enter Returned Date">';
						echo '</div>';
								
					echo '</div>'; 
				}
			}
		}
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);                 // echo '<PRE>'; echo print_r($_POST); exit;
			
			$arrPOST = $_POST;
			foreach($typeID as $type_ID)	{unset($_POST[$type_ID.'_vehicleNO'],$_POST[$type_ID.'_permitNO'],$_POST[$type_ID.'_issueDATE'],$_POST[$type_ID.'_returnDATE']);}
			
			$tempID = ($tempID > 0 ? $tempID : '0');

			if($tempID == 1)    {if($empID == '')       $errors .= "Please Select The Employee Name.<br>";}
			else                {if($empname == '')     $errors .= "Please Select The Employee Name.<br>";}

			$_POST['ecodeID'] 	  = ($tempID == 1 ? $ecodeID : $ecodeID_1);
			$_POST['contractorID'] = ($tempID == 1 ? $contractorID : $contractorID_1);

			if(is_array($fileID_1) && count($fileID_1) == 0 ) 	$errors .= "Enter The Vehicle Reg No.<br>";									
			if(is_array($fileID_2) && count($fileID_2) == 0 ) 	$errors .= "Enter The Permit No.<br>";									 
			if(is_array($fileID_3) && count($fileID_3) == 0 ) 	$errors .= "Enter The Date Issued.<br>";
			//echo '<br /> row counts : '.$rowCount;
			//echo '<br /> row counts : '.$errors;

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{
				$_POST['dateID']  = date('Y-m-d');
				$_POST['timeID']  = date('h : i : A');
				$_POST['tempID']  = $tempID;
				$_POST['companyID'] = $this->companyID;

				unset($_POST['Submit'],$_POST['ecodeID_1'],$_POST['contractorID_1']);
				for($Start = 1; $Start <= 4; $Start++)	{unset($_POST['fileID_'.$Start]);}

				$array = array();					
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($empID);
				$array['status']    = 1;
				$array['logID']	= date('Y-m-d H:i:s');
				 //echo '<PRE>'; echo print_r($array);exit;
				 //echo '<PRE>'; echo print_r($_POST); exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					  $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					  $lastID = $stmt->fetch(PDO::FETCH_NUM);
					  
					  if($lastID[0] > 0 && (count($fileID_1) > 0))
					  {
						  foreach($fileID_1 as $key=>$fileID)
						  {
							  if(!empty($fileID) && ($fileID <> ''))
							  {
								  $dateID_1 = $fileID_3[$key];
								  $dateID_2 = $fileID_4[$key];

								  $arr = array();
								  $arr['ID'] = $lastID[0];
								  $arr['fileID_1'] = $fileID;
								  $arr['fileID_2'] = $fileID_2[$key];
								  $arr['fileID_3'] = !empty($fID_3) && ($fID_3 <> '') && ($fID_3 <> '01-01-1970') && ($fID_3 <> '1970-01-01') ? $this->dateFormat($fID_3) : '0000-00-00';
								  $arr['fileID_4'] = !empty($fID_4) && ($fID_4 <> '') && ($fID_4 <> '01-01-1970') && ($fID_4 <> '1970-01-01') ? $this->dateFormat($fID_4) : '0000-00-00';                                                
								  //echo '<PRE>'; echo print_r($arr); exit;
								  $this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);
							  }
						  }
					  }
					  
					if(is_array($typeID) && count($typeID) > 0)
					{
						foreach($typeID as $type_ID)
						{
							if($type_ID > 0)
							{
								$fID_1 = $arrPOST[$type_ID.'_vehicleNO'];
								$fID_2 = $arrPOST[$type_ID.'_permitNO'];
								$fID_3 = $arrPOST[$type_ID.'_issueDATE'];
								$fID_4 = $arrPOST[$type_ID.'_returnDATE'];

								$arrs = array();
								$arrs['ID'] = $lastID[0];
								$arrs['fileID_1'] = $fID_1;
								$arrs['fileID_2'] = $fID_2;
								$arrs['fileID_3'] = !empty($fID_3) && ($fID_3 <> '') && ($fID_3 <> '01-01-1970') && ($fID_3 <> '1970-01-01') ? $this->dateFormat($fID_3) : '0000-00-00';
								$arrs['fileID_4'] = !empty($fID_4) && ($fID_4 <> '') && ($fID_4 <> '01-01-1970') && ($fID_4 <> '1970-01-01') ? $this->dateFormat($fID_4) : '0000-00-00';
								$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs);
							}
						}
					}
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$_POST['dateID'],$empID,$ecodeID,'','A','',$array);
					
					$this->msg = urlencode(' Parking Permits Is Created (s) Successfully .<br /> Employee Code : '.$array['ecodeID']);
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
	
	public function update()	
	{
		if($this->Form_Variables() == true)	//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);
                        
			$arrPOST = $_POST;
			foreach($typeID as $type_ID)
			{
				unset($_POST[$type_ID.'_vehicleNO'],$_POST[$type_ID.'_permitNO'],$_POST[$type_ID.'_issueDATE'],$_POST[$type_ID.'_returnDATE']);
			}
			
			$errors	=	'';
			
			$tempID = ($tempID > 0 ? $tempID : '0');
			
			if($tempID == 1)
			{
				if($empID == '')	  $errors .= "Please Select The Employee Name.<br>";
			}
			else
			{
				if($empname == '')	$errors .= "Please Select The Employee Name.<br>";
			}
			
			$_POST['ecodeID'] 	  = ($tempID == 1 ? $ecodeID : $ecodeID_1);
			$_POST['contractorID'] = ($tempID == 1 ? $contractorID : $contractorID_1);
			
			if(is_array($fileID_1) && count($fileID_1) == 0 ) 	$errors .= "Enter The Vehicle Reg No.<br>";									
			if(is_array($fileID_2) && count($fileID_2) == 0 ) 	$errors .= "Enter The Permit No.<br>";									 
			if(is_array($fileID_3) && count($fileID_3) == 0 ) 	$errors .= "Enter The Date Issued.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$array = array();
				$array['empID']   = $empID;
				$array['ecodeID'] = $ecodeID;
				$array['contractorID'] = $contractorID;
				$array['systemID']  = $this->get_systemID($empID);
				$array['empname'] = $empname;
				$array['tempID'] = ($tempID > 0 ? $tempID : '0');
				$on['ID'] = $ID; 
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
				   if($ID > 0 && (count($fileID_1) > 0))
				   {
					   $this->delete($this->tableName.'_dtl'," WHERE ID = ".$ID." ");
					   foreach($fileID_1 as $key=>$fileID)
					   {
						   $dateID_1 = $fileID_3[$key];
						   $dateID_2 = $fileID_4[$key];

						   if(!empty($fileID) && ($fileID <> ''))
						   {
							   $arrs = array();
							   $arrs['ID'] = $ID;
							   $arrs['fileID_1'] = $fileID;
							   $arrs['fileID_2'] = $fileID_2[$key];
							   $arrs['fileID_3'] = !empty($dateID_1) && ($dateID_1 <> '') && ($dateID_1 <> '01-01-1970') && ($dateID_1 <> '1970-01-01') ? $this->dateFormat($dateID_1) : '0000-00-00';
							   $arrs['fileID_4'] = !empty($dateID_2) && ($dateID_2 <> '') && ($dateID_2 <> '01-01-1970') && ($dateID_2 <> '1970-01-01') ? $this->dateFormat($dateID_2) : '0000-00-00';
							   $this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs);
						   }
					   }
				   }

					if(is_array($typeID) && count($typeID) > 0)
					{
						foreach($typeID as $type_ID)
						{
							if($type_ID > 0)
							{
								$fID_1 = $arrPOST[$type_ID.'_vehicleNO'];
								$fID_2 = $arrPOST[$type_ID.'_permitNO'];
								$fID_3 = $arrPOST[$type_ID.'_issueDATE'];
								$fID_4 = $arrPOST[$type_ID.'_returnDATE'];

								$arrs = array();
								$arrs['ID'] = $ID;
								$arrs['fileID_1'] = $fID_1;
								$arrs['fileID_2'] = $fID_2;
								$arrs['fileID_3'] = !empty($fID_3) && ($fID_3 <> '') && ($fID_3 <> '01-01-1970') && ($fID_3 <> '1970-01-01') ? $this->dateFormat($fID_3) : '0000-00-00';
								$arrs['fileID_4'] = !empty($fID_4) && ($fID_4 <> '') && ($fID_4 <> '01-01-1970') && ($fID_4 <> '1970-01-01') ? $this->dateFormat($fID_4) : '0000-00-00';
								$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs);
							}
						}
					}
					
					$this->PUSH_userlogsID($this->frmID,$ID,'',$empID,$ecodeID,'','E','',$array);
					
					$this->msg = urlencode('  Parking Permits Is Updated (s) Successfully .<br /> Employee Code : '.$array['ecodeID']);													 					
					$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);						
					$this->Print_Redirect($param,$this->basefile.'?');								
				}
				else
				{ 
					$this->msg = urlencode('Error In Updation. Please try again...!!!');
					$this->printMessage('danger',$this->msg);
					$this->createForm($ID);						
				}  
			}
		}
	}
        
	public function submit_update()
	{
		//echo '<pre>';   echo print_r($_POST);   exit;            
		if($this->Form_Variables() == true)	//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$arrPOST = $_POST;
			foreach($typeID as $type_ID)	{unset($_POST[$type_ID.'_vehicleNO'],$_POST[$type_ID.'_permitNO'],$_POST[$type_ID.'_issueDATE'],$_POST[$type_ID.'_returnDATE']);}
			
			$errors = '';				
			if(is_array($fileID_1) && count($fileID_1) == 0 )   $errors .= "Enter The Vehicle Reg No.<br>";
			if(is_array($fileID_2) && count($fileID_2) == 0 )   $errors .= "Enter The Permit No.<br>";						 
			if(is_array($fileID_3) && count($fileID_3) == 0 )   $errors .= "Enter The Date Issued.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$stausID = 0;
				if($ID > 0 && (count($fileID_1) > 0))
				{
					foreach($fileID_1 as $key=>$fileID)
					{
						$dateID_1 = $fileID_3[$key];
						$dateID_2 = $fileID_4[$key];

						if(!empty($fileID) && ($fileID <> ''))
						{
							$arrs = array();
							$arrs['ID'] = $ID;
							$arrs['fileID_1'] = $fileID;
							$arrs['fileID_2'] = $fileID_2[$key];
							$arrs['fileID_3'] = !empty($dateID_1) && ($dateID_1 <> '') && ($dateID_1 <> '01-01-1970') && ($dateID_1 <> '1970-01-01') ? $this->dateFormat($dateID_1) : '0000-00-00';
							$arrs['fileID_4'] = !empty($dateID_2) && ($dateID_2 <> '') && ($dateID_2 <> '01-01-1970') && ($dateID_2 <> '1970-01-01') ? $this->dateFormat($dateID_2) : '0000-00-00';							
							//echo '<pre>';   echo print_r($arrs);   exit;							
							if($this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs))
							{
								$stausID += 1;
							}
							else
							{
								$stausID = 0;
							}
						}
					}
				}
				
				if(is_array($typeID) && count($typeID) > 0)
				{
					foreach($typeID as $type_ID)
					{
						if($type_ID > 0)
						{
							$fID_1 = $arrPOST[$type_ID.'_vehicleNO'];
							$fID_2 = $arrPOST[$type_ID.'_permitNO'];
							$fID_3 = $arrPOST[$type_ID.'_issueDATE'];
							$fID_4 = $arrPOST[$type_ID.'_returnDATE'];

							$arrs = array();
							$arrs['ID'] = $ID;
							$arrs['fileID_1'] = $fID_1;
							$arrs['fileID_2'] = $fID_2;
							$arrs['fileID_3'] = !empty($fID_3) && ($fID_3 <> '') && ($fID_3 <> '01-01-1970') && ($fID_3 <> '1970-01-01') ? $this->dateFormat($fID_3) : '0000-00-00';
							$arrs['fileID_4'] = !empty($fID_4) && ($fID_4 <> '') && ($fID_4 <> '01-01-1970') && ($fID_4 <> '1970-01-01') ? $this->dateFormat($fID_4) : '0000-00-00';
							if($this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs))
							{
								$stausID += 1;
							}
							else
							{
								$stausID = 0;
							}
						}
					}
				}
				
				/*if($stausID > 0)
				{*/
					$this->msg = urlencode('  Parking Permits Is Updated (s) Successfully .');
					$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);
					$this->Print_Redirect($param,$this->basefile.'?');
				/*}
				else
				{ 
					$this->msg = urlencode('Error In Updation. Please try again...!!!');
					$this->printMessage('danger',$this->msg);
					$this->createForm($ID);
				}  */
			}
		}
	}
        
	public function PRdateFormat($dateString)
	{
		if(!empty($dateString) && ($dateString <> '') && ($dateString <> '01-01-1970') && ($dateString <> '1970-01-01') && ($dateString <> '0000-00-00'))
			{$return = date('d/m/Y',strtotime($dateString));}
		else	
			{$return = '';}		
		return $return;		
	}
}
?>