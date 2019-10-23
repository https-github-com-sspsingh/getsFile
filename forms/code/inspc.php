<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		

		$this->basefile	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = 'inspc';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->frmID		= '44';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view($fd,$td,$searchbyID,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	=	explode("/",$fd);
				list($tdt,$tm,$ty)	=	explode("/",$td);
			
				$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			if($auditID <> '')
			{
				$src .= " AND inspc.ID In(".$auditID.") ";
			}
			else
			{
				/* DATE - SEARCHING */
				if($fd <> '' && $td <> '' )		 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
				else                             $str .= " AND (statusID = 0 || statusID = 2)";
				
				/* SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);
				
				if($tsystemID <> '')
				{
					$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
				}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);				
					$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".rptno LIKE '%".$searchbyID."%'" : "");
					$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
				}
			}
			
		
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".empID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".rptno ASC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Report No</th>';
				echo '<th>Report Date</th>';
				echo '<th>Due Date</th>';
				echo '<th>Driver Name</th>';                    
				echo '<th>Inspection Result</th>';
				echo '<th>Service No</th>';
				echo '<th>Service Info</th>';
				echo '<th>Service Time Point</th>';
				echo '<th>Bus No</th>';
				echo '<th>TRIS Status</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '</tr></thead>';
				$dueDate = '';
				foreach($this->rows as $row)			
				{
					$dueDate = date('d-m-Y', strtotime($row['dateID'].'+7 Days'));
					$daysID = ((strtotime(date('Y-m-d', strtotime($row['dateID'].'+7 Days'))) - strtotime(date('Y-m-d'))) / 86400);
					
					$arrEM  = $row['empID'] > 0       ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['empID']." ") : '';
					$arrIR  = $row['insrypeID'] > 0   ? $this->select('master',array("title"), " WHERE ID = ".$row['insrypeID']." ") : '';
					$arrSN  = $row['servicenoID'] > 0 ? $this->select('srvdtls',array("codeID"), " WHERE ID = ".$row['servicenoID']." ") : '';
					$arrST  = $row['srtpointID'] > 0  ? $this->select('cstpoint_dtl',array("fileID_1"), " WHERE recID = ".$row['srtpointID']." ") : '';
					
					echo '<tr>'; 
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;">'.$row['rptno'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td align="center" style="color:'.($daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green')).'; font-weight:bold;">'.$dueDate.'</td>';
					echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].'</td>';
					echo '<td>'.$arrIR[0]['title'].'</td>';
					echo '<td>'.$arrSN[0]['codeID'].'</td>';
					echo '<td>'.$row['serviceinfID'].'</td>';
					echo '<td>'.$arrST[0]['fileID_1'].'</td>';
					echo '<td align="center">'.$row['busID'].'</td>';
					echo '<td align="center"><b style="color:'.($row['trisID'] == 1 ? 'green' : 'red').'">'.($row['trisID'] == 1 ? 'Complete' : 'Pending').'</b></td>';
					
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
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
	
					echo '</tr>';
				
				}
				echo '</table>';			
			} 
		}
		else {echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Inspection Register</b> Page</div></div>';}
	}

	public function filter_view($fd,$td,$searchbyID,$passSTR)
	{ 
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	=	explode("/",$fd);
				list($tdt,$tm,$ty)	=	explode("/",$td);
			
				$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			/* DATE - SEARCHING */
			if($fd <> '' && $td <> '' )	 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
			else                             $str .= " AND (statusID = 0 || statusID = 2) ";
			
			/* SEARCH BY  -  OPTIONS */
			$src = "";
			$tsystemID  = $this->filter_employee_systemID($searchbyID);
			 
			if($tsystemID <> '')
			{
				$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
			}
			else
			{
				$retID = $this->CheckIntOrStrings($searchbyID);				
				$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".rptno LIKE '%".$searchbyID."%'" : "");
				$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".empID WHERE ".$this->tableName.".trisID <= 0 AND ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".rptno ASC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Report No</th>';
				echo '<th>Report Date</th>';
				echo '<th>Due Date</th>';
				echo '<th>Driver Name</th>';                    
				echo '<th>Inspection Result</th>';
				echo '<th>Service No</th>';
				echo '<th>Service Info</th>';
				echo '<th>Service Time Point</th>';
				echo '<th>Bus No</th>';
				echo '<th>Tris Status</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '</tr></thead>';
				$dueDate = '';
				foreach($this->rows as $row)			
				{
					$dueDate = date('d-m-Y', strtotime($row['dateID'].'+7 Days'));
					$daysID = ((strtotime(date('Y-m-d', strtotime($row['dateID'].'+7 Days'))) - strtotime(date('Y-m-d'))) / 86400);
					$colorID = $daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green');
					
					$arrEM  = $row['empID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['empID']." ") : '';
					$arrIR  = $row['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['insrypeID']." ") : '';
					$CNT_Array  = $row['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['contractID']." ") : '';
					$arrSN  = $row['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$row['servicenoID']." ") : '';
					$arrST  = $row['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$row['srtpointID']." ") : '';
					$CTN_Array  = $row['contractorID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['contractorID']." ") : '';
					
					if(in_array($passSTR,(array($colorID))))
					{
						echo '<tr>'; 
						echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;">'.$row['rptno'].'</a></td>';
						echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
						echo '<td align="center" style="color:'.($colorID).'; font-weight:bold;">'.$dueDate.'</td>';
						echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].'</td>';
						echo '<td>'.$arrIR[0]['title'].'</td>';
						echo '<td>'.$arrSN[0]['codeID'].'</td>';
						echo '<td>'.$row['serviceinfID'].'</td>';
						echo '<td>'.$arrST[0]['fileID_1'].'</td>';
						echo '<td align="center">'.$row['busID'].'</td>';
						echo '<td><b>'.($row['trisID'] == 1 ? 'Complete' : 'Pending').'</b></td>';
						
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
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
							}
							else	{echo '<td></td>';}
						}  
						
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Inspection Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
		
						echo '</tr>';
					}
				}
				echo '</table>';			
			} 
		}
		else
		{
			echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
				  Sorry....you don\'t have permission to view <b>Inspection Register</b> Page</div></div>';
		}
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
                
	/* Start - Contractor - Contract - Session Details */
		$CMP_Array = $_SESSION[$this->website]['compID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$_SESSION[$this->website]['compID']." ") : '';
		$contractorID = !empty($id) && ($id > 0) ? $this->result['contractorID'] : $CMP_Array[0]['contractorID'];
		$contractID   = !empty($id) && ($id > 0) ? $this->result['contractID']   : $CMP_Array[0]['contractID'];
	/* End - Contractor - Contract - Session Details */
        
	echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body inspection_forms" id="fg_membersite">';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Report No <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="rptno" id="rptno" placeholder="Enter Report No" style="text-align:center;" value="'.(!empty($id) ? ($this->result['rptno']) : $this->safeDisplay('rptno')).'">';
			echo '<span id="register_rptno_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Report Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker ins_report_date" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2"></div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Driver Name</label>';
			$staffID = !empty($id) ? $this->result['empID'] : $this->safeDisplay['empID'];
			$arrDB = $staffID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$staffID." ") : '';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$staffID.'">';
				echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
			}
			else
			{
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="empID" name="empID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';                        
				echo $this->GET_Employees11($staffID,"AND status = 1");
				echo '</select>';
				echo '<span id="register_empID_errorloc" class="errors"></span>';
			}
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver ID</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="ecodeID" placeholder="Driver ID" readonly="readonly" 
			style="text-align:center;" value="'.(!empty($id) ? $this->result['ecodeID'] : $this->safeDisplay['ecodeID']).'">';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Inspection Result</label>';
			echo '<select name="insrypeID" onchange="changes=true;" class="form-control" id="insrypeID">';
				$insrtypeID = (!empty($id) ? $this->result['insrypeID'] : $this->safeDisplay['insrypeID']);
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters($insrtypeID,'27');
			echo '</select>';
			echo '<span id="register_insrypeID_errorloc" class="errors"></span>';
		echo '</div>';
		
		$fnID = (($insrtypeID == 300 || $insrtypeID == 268 || $insrtypeID == 261|| $insrtypeID == 271 || $insrtypeID == 301|| $insrtypeID == 377 ||$insrtypeID == 381|| $insrtypeID == 388 || $insrtypeID == 390 || $insrtypeID == 396  ||$insrtypeID == 398 ||$insrtypeID == 399) ? '' : 'disabled="disabled"');
                
		echo '<div class="col-xs-2">';
			echo '<label for="section">Fine</label>';
			echo '<select name="fineID" onchange="changes=true;" class="form-control" id="fineID" '.$fnID.'>';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['fineID'] : $this->safeDisplay['fineID']),'61');
			echo '</select>';
			echo '<span id="register_fineID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Inspected By </label>';
			echo '<select name="inspectedby" onchange="changes=true;" class="form-control" id="inspectedby">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['inspectedby'] : $this->safeDisplay['inspectedby']),'66');
			echo '</select>';
			echo '<span id="register_inspectedby_errorloc" class="errors"></span>';
		echo '</div>';  
                
		echo '<div class="col-xs-2">';
			echo '<label for="section">Date Inspected</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker ins_inspect_date" data-datable="ddmmyyyy" id="dateID1" name="dateID1" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID_1']) : '').'">';
			echo '<span id="register_dateID1_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	/* START - HIDDEN VARIABLES */
		echo '<input type="hidden" name="contractorID" id="contractorID" value="'.$contractorID.'" />';
		echo '<input type="hidden" name="contractID" id="contractID" value="'.$contractID.'" />';
	/* END - HIDDEN VARIABLES */
            
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Service No</label>';
			echo '<select name="servicenoID" onchange="changes=true;" class="form-control select2" style="width: 100%;" id="servicenoID">';
			echo '<option value="0" selected="selected"> --- Select --- </option>';
			if(!empty($contractorID) && !empty($contractID))
			{
				//AND contractID In(".$contractID.")
				$Qry = $this->DB->prepare("SELECT * FROM cnserviceno WHERE ID > 0 AND contractorID In(".$contractorID.") AND companyID In ('".$_SESSION[$this->website]['compID']."') Order By ID ASC");
				if($Qry->execute())
				{
					$this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->crow as $mrow)	
					{
						if($mrow['serviceID'] <> '')
						{
							$mrowID = explode(",",$mrow['serviceID']);
							foreach($mrowID as $lastID)
							{
								$EM_Array = $lastID > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$lastID." ") : '';
								echo '<option value="'.$lastID.'" aria-sort="'.$EM_Array[0]['title'].'" '.($this->result['servicenoID'] == $lastID ? 'selected="selected"' : '').'>'.$EM_Array[0]['codeID'].'</option>';
							}
						}				
					}
				}	
			}
			echo '</select>';
			echo '<span id="register_servicenoID_errorloc" class="errors"></span>';
		echo '</div>';
                
		echo '<div class="col-xs-6">';
			echo '<label for="section">Service Info</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="serviceinfID" name="serviceinfID" placeholder="Enter Service Info" readonly="readonly" value="'.(!empty($id) ? $this->result['serviceinfID'] : $this->result['serviceinfID']).'">';
			echo '<span id="register_serviceinfID_errorloc" class="errors"></span>';
			echo '</select>';
		echo '</div>'; 
                
		echo '<div class="col-xs-4">';
			echo '<label for="section">Service Time Point</label>';
			echo '<select name="srtpointID" onchange="changes=true;" class="form-control" id="srtpointID">';
			echo '<option value="0" selected="selected"> --- Select --- </option>';
			$srtpointID = !empty($id) ? $this->result['srtpointID'] : $this->result['srtpointID'];
			if(!empty($this->result['servicenoID']) && !empty($this->result['contractID']))
			{
				/* CHANGE DATE : 11-06-2019 */
				/* SELECT * FROM cstpoint_dtl WHERE ID > 0 AND contractID = ".$this->result['contractID']." AND serviceID = ".$this->result['servicenoID']." Order By recID ASC */
				$Qry = $this->DB->prepare("SELECT * FROM cstpoint_dtl WHERE recID > 0 AND serviceID = ".$this->result['servicenoID']." Order By recID ASC");
				if($Qry->execute())
				{
					$this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->crow as $mrow)	
					{ 
						echo '<option value="'.$mrow['recID'].'" '.($srtpointID == $mrow['recID'] ? 'selected="selected"' : '').'>'.$mrow['fileID_1'].'</option>';
					}
				}	
			}
			echo '</select>';
			echo '<span id="register_srtpointID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Shift No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="shiftID" name="shiftID" placeholder="Enter Shift No" style="text-align:center;" value="'.(!empty($id) ? $this->result['shiftID'] : $this->safeDisplay['shiftID']).'">';
			echo '<span id="register_shiftID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Bus No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="busID" name="busID" placeholder="Enter Bus No" style="text-align:center;" value="'.(!empty($id) ? $this->result['busID'] : $this->safeDisplay['busID']).'">';
			echo '<span id="register_busID_errorloc" class="errors"></span>';
		echo '</div>'; 
                
		echo '<div class="col-xs-1"></div>';
                
		echo '<div class="col-xs-3">';
			echo '<label for="section">Scheduled Depature Time</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID1" name="timeID1" placeholder="hh:mm" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID_1'] : $this->safeDisplay['timeID1']).'">';
			echo '<span id="register_timeID1_errorloc" class="errors"></span>';
		echo '</div>';
                
		echo '<div class="col-xs-2">';
			echo '<label for="section">Timing Point Time</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID2" name="timeID2" placeholder="hh:mm" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID_2'] : $this->safeDisplay['timeID2']).'">';
			echo '<span id="register_timeID2_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Actual Time</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID3" name="timeID3" placeholder="hh:mm" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID_3'] : $this->safeDisplay['timeID3']).'">';
			echo '<span id="register_timeID3_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	echo '<div class="row">';  
		echo '<div class="col-xs-6">';
			echo '<label for="section">Description</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" placeholder="Enter Description">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Investigated By</label>';						
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invstID" name="invstID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Investigated By --</option>';
				$invstID = !empty($id) ? $this->result['invstID'] : $this->safeDisplay['invstID'];
				echo $this->ReportingBundels($invstID," AND desigID In (208) ");
			echo '</select>';
			echo '<span id="register_invstID_errorloc" class="errors"></span>';
		echo '</div>';
		
		$trisID = (!empty($id) ? $this->result['trisID'] : $this->safeDisplay['trisID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="color:blue;">Responded to PTA</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="trisID" id="trisID" value="1" '.($trisID == 1 ? 'checked="checked"' : '').' />';
			echo '<span id="register_trisID_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-6">';
			echo '<label for="section">PTA Response</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description2" name="description2" placeholder="Enter PTA Report">'.(!empty($id) ? $this->result['description_2'] : $this->safeDisplay['description2']).'</textarea>';
			echo '<span id="register_description2_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-6">';
			echo '<label for="section">Further Action</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description_3" placeholder="Enter Further Outcome">'.(!empty($id) ? $this->result['description_3'] : $this->safeDisplay['description_3']).'</textarea>';
		echo '</div>'; 
	echo '</div>';

	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Discipline Required</label>';
			echo '<select name="cmdiscID" onchange="changes=true;" class="form-control" id="cmdiscID" >';
			$disciplineID = (!empty($id) ? ($this->result['disciplineID']) : $this->safeDisplay('cmdiscID'));
			echo '<option value="0" selected="selected"> --- Select --- </option>';
			echo '<option value="1" '.($disciplineID == 1 ? 'selected="selected"' : '').'>Yes</option>';
			echo '<option value="2" '.($disciplineID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_cmdiscID_errorloc" class="errors"></span>';
		echo '</div>';
	
        if($this->GET_SinglePermission('1') == 1)
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
            echo '<select name="INSPCstatusID" onchange="changes=true;" class="form-control" id="INSPCstatusID">';
                $statusID = (!empty($id) ? ($this->result['statusID']) : $this->safeDisplay('INSPCstatusID'));
                $statusID = $statusID > 0 ? $statusID  : '2';
                echo '<option value="0" selected="selected"> --- Select --- </option>';
                echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Yes</option>';
                echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>No</option>';
            echo '</select>';
			echo '<span id="register_INSPCstatusID_errorloc" class="errors"></span>';
        echo '</div>';
	echo '</div>'; 
	
	echo '<div class="row">';
		echo '<div class="col-xs-2"></div>';
	
        if($this->GET_SinglePermission('1') == 1)
        {
            echo '<div class="col-xs-6">';
                echo '<label for="section">Manager Comments</label>';
                echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="mcomments" id="mcomments" '.($disciplineID == 1 ? '' : 'disabled="disabled"').' placeholder="Enter Manager Comments">'.(!empty($id) ? $this->result['mcomments'] : $this->safeDisplay['mcomments']).'</textarea>';
				echo '<span id="register_mcomments_errorloc" class="errors"></span>';
            echo '</div>';
        }
        
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
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>'; 
	
	echo '<div class="row">';	
		echo '<div class="col-xs-2">';	
		if(!empty($id))
		echo '<input name="ID" value="'.$id.'" type="hidden">';
		echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" id="inspectionSubmit" name="Submit" type="submit">'.(!empty($id) ? 'Update Inspection Register' : 'Save Inspection Register').'</button>';
		echo '</div>';
          
        echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
        echo '</div>';
	echo '</div>';
	
	echo '<div id="InspcValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;

			if($rptno == '') 		   $errors .= "Enter The Inspection Report No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Inspection Report Date.<br>";
			
			if(!empty($dateID) && !empty($dateID1))
			{
				if($this->dateFormat($dateID) < $this->dateFormat($dateID1))
				{
					$errors .= "Enter The Valid Report Date.<br>";
				}
			}
			
			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			}
			else
			{	 
				$_POST['companyID'] = $_SESSION[$this->website]['compID'];		$_POST['dateID']    = $this->dateFormat($_POST['dateID']);
				$_POST['dateID_1']  = $this->dateFormat($_POST['dateID1']);		$_POST['intvDate']  = $this->dateFormat($_POST['intvDate']);
				$_POST['userID']    = $_SESSION[$this->website]['userID'];		$_POST['trisID'] 	= $trisID > 0 ? $trisID : 0;
				$_POST['timeID_1']  = $_POST['timeID1'];						$_POST['timeID_2']  = $_POST['timeID2'];
				$_POST['timeID_3']  = $_POST['timeID3'];						$_POST['description_2'] = $_POST['description2'];
				$_POST['statusID']  = $_POST['INSPCstatusID'];					$_POST['disciplineID'] = $_POST['cmdiscID'];
				
				unset($_POST['Submit'],$_POST['cmdiscID'],$_POST['dateID1'],$_POST['timeID1'],$_POST['timeID2'],$_POST['timeID3'],$_POST['description2'],$_POST['INSPCstatusID']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['systemID']  = $this->get_systemID($empID);
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;				
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['empID'],$array['ecodeID'],$array['rptno'],'A',$array['description'],$array);
					
					$this->msg = urlencode(' Inspection Record is created successfully . <br /> Inspection Report No : '.$array['rptno']);
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
		if($this->Form_Variables() == true)		//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$errors	=	'';

			if($rptno == '') 		    $errors .= "Enter The Inspection Report No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Inspection Report Date.<br>";
			
			if(!empty($dateID) && !empty($dateID1))
			{
				if($this->dateFormat($dateID) < $this->dateFormat($dateID1))
				{
					$errors .= "Enter The Valid Report Date.<br>";
				}
			}
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$_POST['dateID']    = $this->dateFormat($_POST['dateID']);		$_POST['trisID'] = $trisID > 0 ? $trisID : 0;
				$_POST['dateID_1']  = $this->dateFormat($_POST['dateID1']);		$_POST['intvDate']  = $this->dateFormat($_POST['intvDate']);				
				$_POST['timeID_1']  = $_POST['timeID1'];						$_POST['timeID_2']  = $_POST['timeID2'];
				$_POST['timeID_3']  = $_POST['timeID3'];						$_POST['description_2'] = $_POST['description2'];
				$_POST['statusID']  = $_POST['INSPCstatusID'];					$_POST['disciplineID'] = $_POST['cmdiscID'];
				
				unset($_POST['Submit'],$_POST['cmdiscID'],$_POST['ID'],$_POST['dateID1'],$_POST['timeID1'],$_POST['timeID2'],$_POST['timeID3'],$_POST['description2'],$_POST['INSPCstatusID']);				
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($empID);
				$array['trisID']    = $_POST['trisID'] > 0 ? $_POST['trisID'] : 0;
				$on['ID'] = $ID;
				//echo '<pre>'; echo print_r($array); exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['empID'],$array['ecodeID'],$array['rptno'],'E',$array['description'],$array);
										
					$this->msg = urlencode(' Inspection is Updated Successfully . <br /> Inspection Report No : '.$array['rptno']);
					$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$on['ID']);
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