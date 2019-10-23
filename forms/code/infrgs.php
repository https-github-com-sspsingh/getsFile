<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	    = basename($_SERVER['PHP_SELF']);		
		$this->tableName    = 'infrgs';
		$this->companyID	= $_SESSION[$this->website]['compID'];		
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->frmID	    = '43';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view($fd,$td,$searchbyID,$passSTR,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{    
			$str = "";
			
			if($auditID <> '')
			{
				$str .= " AND infrgs.ID In(".$auditID.") ";
			}
			else
			{
				if(!empty($fd) || !empty($td))
				{
					list($fdt,$fm,$fy)	=	explode("/",$fd);
					list($tdt,$tm,$ty)	=	explode("/",$td);
				
					$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
				}
				
				/* DATE - SEARCHING */
				if($fd <> '' && $td <> '' )	 $str .= " AND DATE(infrgs.dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
				else                            $str .= " AND dateID_4 = '0000-00-00' ";
				
				/* SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);
				
				/* DASHBOARD - SEARCHING */
				$str .= $passSTR;
				
				if($tsystemID <> '')	{$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);				
					$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".refno LIKE '%".$searchbyID."%'" : "");
					$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
				}
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".staffID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".dateID DESC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';		
				echo '<th>Date</th>';
				echo '<th>Infringement No</th>';
				echo '<th>Employee Name</th>';
				echo '<th>Demerit Points Lost</th>';
				echo '<th>Vehicle No</th>';
				echo '<th>Infringement Type</th>';			
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '</tr></thead>'; 
				foreach($this->rows as $row)			
				{ 
					$arrEM  = $row['staffID'] > 0    ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['staffID']." ") : '';
					$arrIT  = $row['inftypeID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['inftypeID']." ") : '';

					echo '<tr>';
					echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Infringement Details').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
					echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].' <br /> ('.$arrEM[0]['code'].')</td>';
					echo '<td align="center">'.($row['dplostID'] <> '' ? $row['dplostID'] : 0).'</td>';
					echo '<td align="center">'.$row['vehicle'].'</td>';
					echo '<td>'.$this->Word_Wraping($arrIT[0]['title'],30).'</td>';
					
					if(($row['companyID'] == $_SESSION[$this->website]['compID']) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
					{
						echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';
					}
					else	{echo '<td></td>';}
					
					if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if($row['tsystemID'] > 0 || ((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days'))))	{echo '<td></td>';}	else
						{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Infringement Details').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}  
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Infringement Details').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
						
					echo '</tr>';
				
				}
				echo '</table>';			
			} 
		}
		else	{echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Infringement Details</b> Page</div></div>';}
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
		echo '<div class="col-xs-3">';
			echo '<label for="section">Infringement No <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="infrefno" id="infrefno" placeholder="Enter Infringement No" style="text-align:center;" required="required" value="'.(!empty($id) ? ($this->result['refno']) : $this->safeDisplay('infrefno')).'">';
			echo '<span id="register_infrefno_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Vehicle Rego <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="vehicle" placeholder="Enter Vechile Rego" style="text-align:center;" required="required"	value="'.(!empty($id) ? ($this->result['vehicle']) : $this->safeDisplay('vehicle')).'">';
			echo '<span id="register_vehicle_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2"></div>';
		
		echo '<div class="col-xs-4">';
			$staffID = !empty($id) ? $this->result['staffID'] : $this->safeDisplay['empID'];
			$arrDB = $staffID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$staffID." ") : '';
			
			echo '<label for="section">Employee Name '.($arrDB[0]['status'] == 2 ? '' : '<a style="margin-left:35px; cursor:pointer;" class="fa fa-desktop empInfo"></a>').'</label>';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$staffID.'">';
				echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
			}
			else
			{
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="empID" name="empID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Employee --</option>';                        
				echo $this->GET_Employees11($staffID,"AND status = 1 ");
				echo '</select>';
				echo '<span id="register_empID_errorloc" class="errors"></span>';
			}
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Incident Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Time </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID" name="timeID" placeholder="Enter Time" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID'] : '').'">';			
			echo '<span id="register_timeID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Demerit Points Lost </label>';
				echo '<select onchange="changes=true;" class="form-control" id="dplostID" name="dplostID">';
				echo '<option value="" selected="selected">-- Select Points --</option>';
				$dpID = (!empty($id) ? $this->result['dplostID'] : $this->safeDisplay['dplostID']);
				for($dpsID = 0; $dpsID <= 12; $dpsID++)
				{
					echo '<option value="'.$dpsID.'" '.($dpsID == $dpID ? 'selected="selected"' : '').'>'.$dpsID.'</option>';
				}
			echo '</select>';
			echo '<span id="register_dplostID_errorloc" class="errors"></span>';
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Bus No </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="busID" name="busID" placeholder="Enter Bus No" style="text-align:center;" value="'.(!empty($id) ? $this->result['busID'] : $this->safeDisplay('busID')).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2"></div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Employee ID</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="stcodeID" placeholder="Staff ID" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['stcodeID'] : '').'">';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Issue Date </label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID1" name="dateID1" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID_1']) : '').'">';
			echo '<span id="register_dateID1_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Compliance Date </label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID2" name="dateID2" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID_2']) : '').'">';
			echo '<span id="register_dateID2_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Date Recieved</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID3" name="dateID3" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID_3']) : '').'">';
			echo '<span id="register_dateID3_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Date Sent</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID4" name="dateID4" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID_4']) : '').'">';
			echo '<span id="register_dateID4_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2"></div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Investigated By</label>';
			$invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invID" name="invID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Investigated By --</option>';				
			echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_invID_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div><br />';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Infringement Type</label>';
			echo '<select name="inftypeID" onchange="changes=true;" class="form-control" id="inftypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['inftypeID'] : $this->safeDisplay['inftypeID']),'22');
			echo '</select>';
			echo '<span id="register_inftypeID_errorloc" class="errors"></span>';
		echo '</div>';
		
		$csID = '';
		$csID = $this->result['inftypeID'] == 162 ? '' : 'disabled="disabled"';
		
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">If Other Infringement Type (Please Specify)</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" '.$csID.' placeholder="Enter If Other Infringement Type">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Location of Infringement</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description1" id="description1" placeholder="Enter Location of Infringement">'.(!empty($id) ? $this->result['description_1'] : $this->safeDisplay['description1']).'</textarea>';
			echo '<span id="register_description1_errorloc" class="errors"></span>';
		echo '</div>'; 
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">Discipline Required</label>';
			echo '<select name="cmdiscID" onchange="changes=true;" class="form-control" id="cmdiscID" >';
				$disciplineID = (!empty($id) ? ($this->result['disciplineID']) : '1');//$this->safeDisplay('disciplineID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($disciplineID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($disciplineID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_cmdiscID_errorloc" class="errors"></span>';
		echo '</div>';
            
		if($this->GET_SinglePermission('2') == 1)    
		{
			echo '<div class="col-xs-4">';
			echo '<label for="section">Interviewed By</label>';
				$intvID = (!empty($id) ? $this->result['intvID'] : $this->safeDisplay['intvID']); 				
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="intvID" name="intvID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Interviewed By --</option>';				
				echo $this->ReportingBundels($intvID," AND employee.desigID In (209,208) ");
				echo '</select>'; 
				echo '<span id="register_intvID_errorloc" class="errors"></span>';
			echo '</div>';

			echo '<div class="col-xs-2">';			
				echo '<label for="section">Interviewed Date</label>';
				echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="intvDate" name="intvDate" placeholder="Enter Interviewed Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['intvDate']) : '').'">';
				echo '<span id="register_intvDate_errorloc" class="errors"></span>';
			echo '</div>';
		}
		
        echo '<div class="col-xs-2"></div>';
		
		echo '<div class="col-xs-2">';
            echo '<label for="section">Closed</label>';
            echo '<select name="INFRGstatusID" onchange="changes=true;" class="form-control" id="INFRGstatusID">';
                $statusID = (!empty($id) ? ($this->result['statusID']) : $this->safeDisplay('INFRGstatusID'));
                $statusID = $statusID > 0 ? $statusID  : '2';
                echo '<option value="0" selected="selected"> --- Select --- </option>';
                echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Yes</option>';
                echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>No</option>';
            echo '</select>';
			echo '<span id="register_INFRGstatusID_errorloc" class="errors"></span>';
        echo '</div>';
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-2"></div>';
            
		if($this->GET_SinglePermission('2') == 1)    
		{
			echo '<div class="col-xs-4">';
				echo '<label for="section">Warning Type</label>';
				echo '<select name="wrtypeID" onchange="changes=true;" class="form-control" id="wrtypeID" '.($disciplineID == 1 ? '' : 'disabled="disabled"').'>';
					echo '<option value="0" selected="selected"> --- Select --- </option>';
					echo $this->GET_Masters((!empty($id) ? $this->result['wrtypeID'] : $this->safeDisplay['wrtypeID']),'23');
				echo '</select>';
				echo '<span id="register_wrtypeID_errorloc" class="errors"></span>';
			echo '</div>';
		}
            
		if($this->GET_SinglePermission('1') == 1)    
		{
			echo '<div class="col-xs-6">';
				echo '<label for="section">Manager Comments</label>';
				echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="mcomments" id="mcomments" '.($disciplineID == 1 ? '' : 'disabled="disabled"').' placeholder="Enter Manager Comments">'.(!empty($id) ? $this->result['mcomments'] : $this->safeDisplay['mcomments']).'</textarea>';
				echo '<span id="register_mcomments_errorloc" class="errors"></span>';
			echo '</div>';
		} 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';	
			if(!empty($id))
			echo '<input name="ID" value="'.$id.'" type="hidden">';
			echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" id="infringmentSubmit" name="Submit" type="submit">'.(!empty($id) ? 'Update Infringement' : 'Save Infringement').'</button>';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:50px; float:right; display:inline-block" type="button">View All Lists</button></a>';
		echo '</div>';        
	echo '</div>';
	
	echo '<div id="INFRGValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			 //echo '<PRE>'; echo print_r($_POST); exit;
			
			if($infrefno == '')		$errors .= "Enter The Infringement Ref No.<br>";
			if($dateID == '') 	 	$errors .= "Enter The Infringement Date.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{	 
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);		$_POST['dateID_1'] = $this->dateFormat($_POST['dateID1']);		
				$_POST['dateID_2'] = $this->dateFormat($_POST['dateID2']);		$_POST['dateID_3'] = $this->dateFormat($_POST['dateID3']);		
				$_POST['dateID_4'] = $this->dateFormat($_POST['dateID4']);		$_POST['intvDate'] = $this->dateFormat($_POST['intvDate']);
				$_POST['companyID'] = $this->companyID;							$_POST['userID']  = $_SESSION[$this->website]['userID'];
				$_POST['description_1'] = $_POST['description1'];				$_POST['statusID'] = $_POST['INFRGstatusID'];
				$_POST['disciplineID']  = $_POST['cmdiscID'];					$_POST['staffID'] = $_POST['empID'];
				$_POST['refno'] = $_POST['infrefno'];
				unset($_POST['Submit'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['dateID1'],$_POST['dateID2'],$_POST['dateID3'],$_POST['dateID4'],$_POST['description1'],$_POST['INFRGstatusID'],$_POST['infrefno']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['systemID']  = $this->get_systemID($_POST['staffID']);
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;
				//echo '<PRE>'; echo print_r($_POST); exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['staffID'],$array['stcodeID'],$array['refno'],'A',($array['description'].' '.$array['description_1']),$array);

					$this->msg = urlencode(' Infringement is Created Successfully . <br /> Infringement No : '.$array['refno']);
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
			
			$errors	=	'';
			
			if($infrefno == '')		   $errors .= "Enter The Infringement Ref No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Infringement Date.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);		$_POST['dateID_1'] = $this->dateFormat($_POST['dateID1']);		
				$_POST['dateID_2'] = $this->dateFormat($_POST['dateID2']);		$_POST['dateID_3'] = $this->dateFormat($_POST['dateID3']);		
				$_POST['dateID_4'] = $this->dateFormat($_POST['dateID4']);		$_POST['intvDate'] = $this->dateFormat($_POST['intvDate']);				
				$_POST['description_1'] = $_POST['description1'];				$_POST['statusID'] = $_POST['INFRGstatusID'];
				$_POST['disciplineID']  = $_POST['cmdiscID'];					$_POST['staffID'] = $_POST['empID'];
				$_POST['refno'] = $_POST['infrefno'];
				
				unset($_POST['Submit'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['ID'],$_POST['dateID1'],$_POST['dateID2'],$_POST['dateID3'],$_POST['dateID4'],$_POST['description1'],$_POST['INFRGstatusID'],$_POST['infrefno']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($_POST['staffID']);
				$on['ID'] = $ID;
				//echo '<pre>'; echo print_r($array); exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['staffID'],$array['stcodeID'],$array['refno'],'E',($array['description'].' '.$array['description_1']),$array);				
									
					$this->msg = urlencode(' Infringement is Updated Successfully . <br /> Infringement No : '.$array['refno']);
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
        
}
?>