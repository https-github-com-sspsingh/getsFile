<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
            parent::__construct();		
		
            $this->basefile     =	basename($_SERVER['PHP_SELF']);		
            $this->tableName    =	'sir_regis';
			$this->companyID	= $_SESSION[$this->website]['compID'];
			$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
            $this->frmID		= '130';
            $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
        
	public function view($fd,$td,$searchbyID,$passSTR,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";

			if($auditID <> '')
			{
				$str .= " AND sir_regis.ID In(".$auditID.") ";
			}
			else
			{				
				if(!empty($fd) || !empty($td))
				{
					list($fdt,$fm,$fy)	= explode("/",$fd);
					list($tdt,$tm,$ty)	= explode("/",$td);

					$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
				}
				
				/* DATE - SEARCHING */
				if($fd <> '' && $td <> '')     $str .= " AND DATE(issuetoDT) BETWEEN '".$fd."' AND '".$td."' ".$src;
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 		
				else                            $str .= " AND statusID = 1 ";


				/* SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);

				/* DASHBOARD - SEARCHING */
				$str .= $passSTR;
				
				/* SEARCH BY  -  OPTIONS */
				if($tsystemID <> '')
				{
					$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
				}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);				
					$src .= ($retID == 2 ? "AND ".$this->tableName.".refno LIKE '%".$searchbyID."%'" : "");
					$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
				}
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY From_UnixTime(".$this->tableName.".issuetoDT) DESC ";			
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Improvement No</th>';
				echo '<th>Issue Date</th>';
				echo '<th>Procedure</th>'; 
				echo '<th>SIR Type</th>';
				echo '<th>Originator</th>';				
				echo '<th width="350">Description</th>';
				echo ($_SESSION[$this->website]['scompID'] <> '' ? '<th>Depot</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '</tr></thead>'; 
				foreach($this->rows as $row)			
				{
					$arrRP  = $row['originatorID'] > 0 ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['originatorID']." ") : '';
					$arrMS  = $row['srtypeID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['srtypeID']." ") : '';
					
					echo '<tr>';
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Sir Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['issuetoDT']).'</td>';
					echo '<td>'.$row['prcedure'].'</td>';
					echo '<td>'.($arrMS[0]['title']).'</td>';		
					echo '<td>'.$arrRP[0]['full_name'].' ('.($arrRP[0]['code']).')</td>';					
					echo '<td>'.($row['description']).'</td>';
					
					if($_SESSION[$this->website]['scompID'] <> '')
					{
						$arrSD  = $row['scompanyID'] > 0 ? $this->select('company_dtls',array("title"), " WHERE ID = ".$row['scompanyID']." ") : '';
						$arrCD  = $this->select('company',array("title"), " WHERE ID = ".$row['companyID']." ");
						
						echo '<td style="padding-left:3px; padding-right:2px;">'.$arrCD[0]['title'].' ('.($arrSD[0]['title']).')</td>';
					}

					if(($row['companyID'] == $this->companyID) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
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
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_SIR Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_SIR Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
					
					echo '</tr>';
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
		
	$srnoID = $this->count_rows($this->tableName, " WHERE ID > 0 AND companyID = ".$this->companyID." ");    
    $srnoID = $_SESSION[$this->website]['compCD'].'-'.sprintf('%02d',($srnoID > 0 ? ($srnoID + 1) : '1'));
	
	echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body" id="fg_membersite">';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">Issued Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="issuetoDT" name="issuetoDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['issuetoDT']) : '').'">';			
			echo '<span id="register_issuetoDT_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Improvement No <span class="Maindaitory">*</span></label>';
			$refNO = (!empty($id) ? ($this->result['refno']) : $this->safeDisplay['refno']);
			$refNO = ($refNO <> '' ? $refNO : $srnoID);
			echo '<input type="text" onchange="changes=true;" class="form-control" name="refno" placeholder="Enter Improvement No" style="text-align:center;" required="required" value="'.$refNO.'">';
			echo '<span id="register_refno_errorloc" class="errors"></span>';
		echo '</div>';		
		
		echo '<div class="col-xs-5"></div>';
		
		echo $this->GET_SubDepotLists((!empty($id) ? $this->result['scompanyID'] : $this->safeDisplay['scompanyID']));
	echo '</div>';
		
	echo '<div class="row">';
		echo '<div class="col-xs-6">';
			echo '<label for="section">Procedure(s)</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="prcedure" name="prcedure" placeholder="Enter Procedure" value="'.(!empty($id) ?  $this->result['prcedure'] : '').'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-3"></div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">SIR Type <span class="Maindaitory">*</span></label>';
			echo '<select name="srtypeID" onchange="changes=true;" class="form-control" id="srtypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['srtypeID'] : $this->safeDisplay['srtypeID']),'125');
			echo '</select>';
			echo '<span id="register_srtypeID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
		
	echo '<div class="row">';
		echo '<div class="col-xs-6">';
			echo '<label for="section">Details <span class="Maindaitory">*</span></label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" placeholder="Insert Details of Improvement">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>';		
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">SIR Issued To <span class="Maindaitory">*</span></label>';
			$issuedTO = (!empty($id) ? $this->result['issuedTO'] : $this->safeDisplay['issuedTO']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="issuedTO" name="issuedTO">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Issued To --</option>';				
			echo $this->ReportingBundels($issuedTO," AND employee.desigID In (209,208) ");
			echo '</select>'; 
			echo '<span id="register_issuedTO_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Originator <span class="Maindaitory">*</span></label>';				
				$originatorID = !empty($id) ? $this->result['originatorID'] : $this->safeDisplay['originatorID'];
				$arrDB = $originatorID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$originatorID." ") : '';
				if($arrDB[0]['status'] == 2)
				{
					echo '<input type="hidden" class="form-control" readonly="readonly" name="originatorID" value="'.$originatorID.'">';
					echo '<input type="text" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].' - '.$arrDB[0]['code'].'">';
				}
				else
				{
					echo '<select onchange="changes=true;" class="form-control select2" style="width:100%;" id="originatorID" name="originatorID">';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select Originator --</option>';
					echo $this->GET_Employees($originatorID,'');
					echo '</select>';
					echo '<span id="register_originatorID_errorloc" class="errors"></span>';
				}
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-3">';
			echo '<label for="section">Investigation Results</label>';
			$resultsINV = (!empty($id) ? $this->result['resultsINV'] : $this->safeDisplay['resultsINV']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="resultsINV" name="resultsINV">';
			echo '<option value="0" selected="selected">-- Select Investigation --</option>';
				echo '<option value="8000" '.($resultsINV == 8000 ? 'selected="selected"' : '').'>Other</option>';
				echo $this->GET_Masters($resultsINV,'126');
			echo '</select>'; 
			echo '<span id="register_resultsINV_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Investigation <b style="color:blue;">Other</b></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" '.($resultsINV == 8000 ? '' : 'readonly="readonly"').' id="otherINV" name="otherINV" placeholder="Enter Other" value="'.(!empty($id) ?  $this->result['otherINV'] : '').'">';
			echo '<span id="register_otherINV_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-6">';
			echo '<label for="section">Investigation Details </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="descriptionINV" id="descriptionINV" placeholder="Enter Investigation Details">'.(!empty($id) ? $this->result['descriptionINV'] : $this->safeDisplay['descriptionINV']).'</textarea>';			
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">'; 	
		echo '<div class="col-xs-4">';
			echo '<label for="section">Completed By</label>';
			$invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invID" name="invID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Completed By --</option>';				
			echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
			echo '</select>'; 
			echo '<span id="register_invID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Completed Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="invDate" name="invDate" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['invDate']) : '').'">';
			echo '<span id="register_invDate_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-7">';
			echo '<label for="section">Action </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="action" id="action" placeholder="Enter Action Details">'.(!empty($id) ? $this->result['action'] : $this->safeDisplay['action']).'</textarea>';
			echo '<span id="register_action_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
		echo '<label for="section">Action By</label>';
			$actID = (!empty($id) ? $this->result['actID'] : $this->safeDisplay['actID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="actID" name="actID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Action By --</option>';				
			echo $this->ReportingBundels($actID," AND employee.desigID In (209,208) ");
			echo '</select>'; 
			echo '<span id="register_actID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Action Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="actDate" name="actDate" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['actDate']) : '').'">';
			echo '<span id="register_actDate_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';	
		echo '<div class="col-xs-2">';
			echo '<label for="section">Action Effective</label>';
			echo '<select name="acteffID" onchange="changes=true;" class="form-control" id="acteffID" >';
				$acteffID = (!empty($id) ? $this->result['acteffID'] : $this->safeDisplay('acteffID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($acteffID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($acteffID == 2 ? 'selected="selected"' : '').'>No</option>';				
			echo '</select>';
			echo '<span id="register_acteffID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Follow-up Required</label>';
			echo '<select name="fupreqID" onchange="changes=true;" class="form-control" id="fupreqID" >';
				$fupreqID = (!empty($id) ? $this->result['fupreqID'] : $this->safeDisplay('fupreqID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($fupreqID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($fupreqID == 2 ? 'selected="selected"' : '').'>No</option>';
				echo '<option value="3" '.($fupreqID == 3 ? 'selected="selected"' : '').'>NA</option>';
			echo '</select>';
			echo '<span id="register_fupreqID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Proposed Follow-up Date</label>';			
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="fupreqDT" name="fupreqDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['fupreqDT']) : '').'">';
			echo '<span id="register_fupreqDT_errorloc" class="errors"></span>';
		echo '</div>'; 
			
		
		echo '<div class="col-xs-6">';
			echo '<label for="section">Follow-Up Details </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="fupDesc" id="fupDesc" '.(empty($fupreqID) || $fupreqID == 1 ? '' : 'readonly="readonly"').' placeholder="Enter Follow-Up Details">'.(!empty($id) ? $this->result['fupDesc'] : $this->safeDisplay['fupDesc']).'</textarea>';
			echo '<span id="register_fupDesc_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-4">';
			echo '<label for="section">Follow-up Completed By</label>';
			$fupcmpID = (!empty($id) ? $this->result['fupcmpID'] : $this->safeDisplay['fupcmpID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="fupcmpID" name="fupcmpID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Follow-up Completed By --</option>';				
			echo $this->ReportingBundels($fupcmpID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_fupcmpID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 12px;">Follow-up Completed Date</label>';			
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="fupcmpDT" name="fupcmpDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['fupcmpDT']) : '').'">';
			echo '<span id="register_fupcmpDT_errorloc" class="errors"></span>';
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 11.5px;">Improvement Close Out Date</label>';			
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="clsoutDT" name="clsoutDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['clsoutDT']) : '').'">';
			echo '<span id="register_clsoutDT_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Date Originator Advised</label>';			
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="orgadvDT" name="orgadvDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['orgadvDT']) : '').'">';
			echo '<span id="register_orgadvDT_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Status</label>';
			echo '<select name="statusID" onchange="changes=true;" class="form-control" id="SIRstatusID" >';
				$statusID = (!empty($id) ? $this->result['statusID'] : $this->safeDisplay('statusID'));
				$statusID = ($statusID > 0 ? $statusID : 1);
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Pending</option>';
				echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>Complete</option>';
			echo '</select>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';

	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
            echo '<input name="ID" value="'.$id.'" type="hidden">';
            echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" id="sirregisSubmit" type="submit">'.(!empty($id) ? 'Update SIR Register' : 'Save SIR Register').'</button>';
	  echo '</div>';
          
      echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
      echo '</div>';
	echo '</div>';
	
	echo '<div id="SirRegisValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;

			if($refno == '') 		   $errors .= "Enter The Improvement No.<br>";
			
			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			} 
			else
			{	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE refno =:refno AND companyID = :companyID "); 
				$Qry->bindParam(':refno',$refno);
				$Qry->bindParam(':companyID',($this->companyID));
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];

				if($rowCount > 0 ) 
				{
					$this->printMessage('danger',' Improvement No : '.$refno.'<br />Already exist !...');
					$this->createForm();
				}
				else
				{
					$_POST['issuetoDT'] = $this->dateFormat($_POST['issuetoDT']);
					$_POST['invDate']  = $this->dateFormat($_POST['invDate']);
					$_POST['actDate']  = $this->dateFormat($_POST['actDate']);
					$_POST['fupreqDT'] = $this->dateFormat($_POST['fupreqDT']);
					$_POST['fupcmpDT'] = $this->dateFormat($_POST['fupcmpDT']);
					$_POST['clsoutDT'] = $this->dateFormat($_POST['clsoutDT']);
					$_POST['orgadvDT'] = $this->dateFormat($_POST['orgadvDT']);					
					$_POST['userID']  = $_SESSION[$this->website]['userID'];
					$_POST['companyID'] = $this->companyID;
					
					unset($_POST['Submit']);
					unset($_POST['sstatusID']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['systemID']  = $this->get_systemID($_POST['originatorID']);
					$array['logID'] = date('Y-m-d H:i:s');
					//echo '<PRE>'; echo print_r($array);exit;
					//echo '<PRE>'; echo print_r($_POST); exit;
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$originatorID,'',$refno,'A',$array['description'],$array);

						$this->msg = urlencode(' SIR Register is Created Successfully . <br /> Improvement No : '.$array['refno']);
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
		if($this->Form_Variables() == true)        // echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$errors	=	'';

			if($refno == '') 		    $errors .= "Enter The Improvement No.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE refno =:cID AND companyID = :companyID AND ID <> :ID ");
				$Qry->bindParam(':cID',$refno);
				$Qry->bindParam(':companyID',($this->companyID));
				$Qry->bindParam(':ID',$ID);				
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];

				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Improvement No : '.$refno.'<br />Already exist !...');
					$this->createForm($ID);
				}
				else
				{
					$_POST['issuetoDT'] = $this->dateFormat($_POST['issuetoDT']);
					$_POST['invDate']  = $this->dateFormat($_POST['invDate']);
					$_POST['actDate']  = $this->dateFormat($_POST['actDate']);
					$_POST['fupreqDT'] = $this->dateFormat($_POST['fupreqDT']);
					$_POST['fupcmpDT'] = $this->dateFormat($_POST['fupcmpDT']);
					$_POST['clsoutDT'] = $this->dateFormat($_POST['clsoutDT']);
					$_POST['orgadvDT'] = $this->dateFormat($_POST['orgadvDT']);
					
					unset($_POST['Submit']);
					unset($_POST['sstatusID']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$array['systemID']  = $this->get_systemID($_POST['originatorID']);
					$on['ID'] = $ID;
					//echo '<pre>'; echo print_r($array); exit;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 		
						$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$originatorID,'',$refno,'E',$array['description'],$array);

						$this->msg = urlencode(' SIR Register is Updated Successfully . <br /> Improvement No : '.$array['refno']);
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
}
?>