<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
            parent::__construct();		
		
            $this->basefile     =	basename($_SERVER['PHP_SELF']);		
            $this->tableName    =	'hiz_regis';
            $this->companyID	= $_SESSION[$this->website]['compID'];
            $this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
            $this->frmID		= '131';
            $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
        
	public function view($fd,$td,$searchbyID,$passSTR,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if($auditID <> '')
			{
				$str .= " AND hiz_regis.ID In(".$auditID.") ";
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
				if($fd <> '' && $td <> '')     $str .= " AND DATE(rdateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
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
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY From_UnixTime(".$this->tableName.".dateID) DESC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>HZ No</th>';
				echo '<th>Report Date</th>';
				echo '<th>Reported By</th>';
				echo '<th>Location</th>';
				echo '<th>Hazard Type</th>';								
				echo '<th width="350">Description</th>';
				echo ($_SESSION[$this->website]['scompID'] <> '' ? '<th>Depot</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '</tr></thead>'; 
				foreach($this->rows as $row)			
				{
					$arrRP  = $row['reportBY'] > 0 ? $this->select('employee',array("code,full_name"), " WHERE ID = ".$row['reportBY']." ") : '';
					$arrHT  = $row['hztypeID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['hztypeID']." ") : '';
					
					echo '<tr>';
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Hiz Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['rdateID']).'</td>';
					echo '<td>'.$arrRP[0]['full_name'].' ('.($arrRP[0]['code']).')</td>';
					echo '<td>'.$row['location'].'</td>';
					echo '<td>'.($arrHT[0]['title']).'</td>';						
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
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_HIZ Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_HIZ Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
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
	echo '<div class="box-body hazards_forms" id="fg_membersite">';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">HZ No <span class="Maindaitory">*</span></label>';
			$refNO = (!empty($id) ? ($this->result['refno']) : $this->safeDisplay['refno']);
			$refNO = ($refNO <> '' ? $refNO : $srnoID);
			echo '<input type="text" class="form-control" onchange="changes=true;" id="HIZrefno" name="HIZrefno" placeholder="Enter HZ No" style="text-align:center;" required="required" value="'.($refNO).'">';
			echo '<span id="register_HIZrefno_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Report Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" class="form-control datepicker hiz_report_date" onchange="changes=true;" data-datable="ddmmyyyy" id="rdateID" name="rdateID" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['rdateID']) : '').'">';			
			echo '<span id="register_rdateID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Date of Occurance </label>';
			echo '<input type="datable" class="form-control datepicker hiz_occurance_date" onchange="changes=true;" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Time </label>';
			echo '<input type="text" class="form-control TPicker" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" onchange="changes=true;" id="timeID" name="timeID" placeholder="hh:mm" value="'.(!empty($id) ? $this->result['timeID'] : $this->safeDisplay['timeID']).'">';
			echo '<span id="register_timeID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-1"></div>';
		
		echo $this->GET_SubDepotLists((!empty($id) ? $this->result['scompanyID'] : $this->safeDisplay['scompanyID']));
		
	echo '</div>';
	
	echo '<div class="row">';  
		echo '<div class="col-xs-4">';
			echo '<label for="section">Reported By <span class="Maindaitory">*</span></label>';
			$reportBY = (!empty($id) ? $this->result['reportBY'] : $this->safeDisplay['reportBY']);
			$arrDB = $reportBY > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$reportBY." ") : '';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" class="form-control" readonly="readonly" name="reportBY" value="'.$reportBY.'">';
				echo '<input type="text" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].' - '.$arrDB[0]['code'].'">';
			}
			else
			{
				echo '<select class="form-control select2" onchange="changes=true;" style="width: 100%;" id="reportBY" name="reportBY">';
				echo '<option value="0" selected="selected" onchange="changes=true;" disabled="disabled">-- Select Reported By --</option>';				
				echo $this->GET_Employees11($reportBY,"AND status = 1 ");
				echo '</select>'; 
				echo '<span id="register_reportBY_errorloc" class="errors"></span>';
			}
		echo '</div>'; 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Location <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" onchange="changes=true;" name="location" placeholder="Enter Location" value="'.(!empty($id) ? ($this->result['location']) : $this->safeDisplay('location')).'">';
			echo '<span id="register_location_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-1"></div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Job Title <span class="Maindaitory">*</span></label>';
			echo '<select name="jobID" class="form-control" onchange="changes=true;" id="jobID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['jobID'] : $this->safeDisplay['jobID']),'127');
			echo '</select>';
			echo '<span id="register_jobID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-5">';
			echo '<label for="section">Description <span class="Maindaitory">*</span></label>';
			echo '<textarea style="resize:none;" class="form-control" onchange="changes=true;" rows="2" name="description" id="description" placeholder="Enter Reason">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-5">';
			echo '<label for="section">Action Taken</label>';
			echo '<textarea style="resize:none;" class="form-control" onchange="changes=true;" rows="2" name="descriptionACT" id="descriptionACT" placeholder="Enter Reason">'.(!empty($id) ? $this->result['descriptionACT'] : $this->safeDisplay['descriptionACT']).'</textarea>';
			echo '<span id="register_descriptionACT_errorloc" class="errors"></span>';
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Hazard Type <span class="Maindaitory">*</span></label>';
			echo '<select name="hztypeID" class="form-control" onchange="changes=true;" id="hztypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['hztypeID'] : $this->safeDisplay['hztypeID']),'128');
			echo '</select>';
			echo '<span id="register_hztypeID_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-5">';
			echo '<label for="section">Staff Name <span class="Maindaitory">*</span></label>';
			$staffID = !empty($id) ? $this->result['fstaffID'] : $this->safeDisplay['empID'];
			$arrDB = $staffID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$staffID." ") : '';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" class="form-control" readonly="readonly" name="empID" value="'.$staffID.'">';
				echo '<input type="text" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
			}
			else
			{
				echo '<select class="form-control select2" onchange="changes=true;" style="width: 100%;" id="empID" name="empID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Staff --</option>';                        				
				echo $this->ReportingBundels($staffID," AND employee.desigID In (209,208) ");
				echo '</select>';
				echo '<span id="register_empID_errorloc" class="errors"></span>';
			}
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Staff ID <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" onchange="changes=true;" id="ecodeID" name="fscodeID" placeholder="Staff Code" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['fscodeID'] : $this->safeDisplay['fscodeID']).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Designation <span class="Maindaitory">*</span></label>';
			echo '<select name="fdesigID" class="form-control" onchange="changes=true;" id="fdesigID" >';
				$fdesigID = (!empty($id) ? ($this->result['fdesigID']) : $this->safeDisplay('fdesigID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($fdesigID == 1 ? 'selected="selected"' : '').'>Operations Manager</option>';
				echo '<option value="2" '.($fdesigID == 2 ? 'selected="selected"' : '').'>Workshop Manager</option>';
				echo '<option value="3" '.($fdesigID == 3 ? 'selected="selected"' : '').'>Area Manager</option>';
				echo '<option value="4" '.($fdesigID == 4 ? 'selected="selected"' : '').'>General Manager</option>';
				echo '<option value="5" '.($fdesigID == 5 ? 'selected="selected"' : '').'>Satefy & Quality Office</option>';
			echo '</select>';
			echo '<span id="register_fdesigID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Reciept Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="rcdateID" name="rcdateID" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['rcdateID']) : '').'">';
			echo '<span id="register_rcdateID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Unmanaged Likelihood</label>';
			echo '<select name="optIDu1" class="form-control" onchange="changes=true;" id="optIDu1" >';
				$optIDu1 = (!empty($id) ? ($this->result['optID_u1']) : $this->safeDisplay('optIDu1'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="10" '.($optIDu1 == 10 ? 'selected="selected"' : '').'>May well be expected</option>';
				echo '<option value="6"  '.($optIDu1 == 6 ? 'selected="selected"' : '').'>Quite possible</option>';
				echo '<option value="3"  '.($optIDu1 == 3 ? 'selected="selected"' : '').'>Unusual but possible</option>';
				echo '<option value="1"  '.($optIDu1 == 1 ? 'selected="selected"' : '').'>Only remotely possible</option>';
			echo '</select>';
			echo '<span id="register_optIDu1_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Unmanaged Exposure</label>';
			echo '<select name="optIDu3" class="form-control" onchange="changes=true;" id="optIDu3" >';
				$optIDu3 = (!empty($id) ? $this->result['optID_u3'] : $this->safeDisplay['optIDu3']);
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="10" '.($optIDu3 == 10 ? 'selected="selected"' : '').'>Continuous</option>';
				echo '<option value="6" '.($optIDu3 == 6 ? 'selected="selected"' : '').'>Daily</option>';
				echo '<option value="3" '.($optIDu3 == 3 ? 'selected="selected"' : '').'>Weekly</option>';				
				echo '<option value="1" '.($optIDu3 == 1 ? 'selected="selected"' : '').'>Few per year</option>';
			echo '</select>';
			echo '<span id="register_optIDu3_errorloc" class="errors"></span>';
		echo '</div>';
	
		echo '<div class="col-xs-3">';
			echo '<label for="section">Consequence/Impact</label>';
			echo '<select name="optIDu4" class="form-control" onchange="changes=true;" id="optIDu4" >';
				$optIDu4 = (!empty($id) ? ($this->result['optID_u4']) : $this->safeDisplay('optIDu4'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($optIDu4 == 1 ? 'selected="selected"' : '').'>Safety</option>';
				echo '<option value="2" '.($optIDu4 == 2 ? 'selected="selected"' : '').'>Environmental</option>';
			echo '</select>';
			echo '<span id="register_optIDu4_errorloc" class="errors"></span>';
		echo '</div>'; 
	
		echo '<div class="col-xs-3">';
			echo '<label for="section">Secondary Choice</label>';
			echo '<select name="optIDu5" class="form-control" onchange="changes=true;" id="optIDu5" >';
				$optIDu5 = (!empty($id) ? $this->result['optID_u5'] : $this->safeDisplay['optIDu5']);
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				if($optIDu4 == 1)
				{
					echo '<option value="10" '.($optIDu5 == 10 ? 'selected="selected"' : '').'>Fatality or Permanent Disability</option>';
					echo '<option value="6"  '.($optIDu5 == 6 ? 'selected="selected"' : '').'>Serious Injury/Loss Time Injury or Disease</option>';
					echo '<option value="3"  '.($optIDu5 == 3 ? 'selected="selected"' : '').'>Medical Treated Injury or Disease</option>';
					echo '<option value="1"  '.($optIDu5 == 1 ? 'selected="selected"' : '').'>First Aid Treatment (on site) or Work Injury or Disease Report</option>';
				}
				else if($optIDu4 == 2)
				{
					echo '<option value="10" '.($optIDu5 == 10 ? 'selected="selected"' : '').'>Serious Environmental Harm</option>';
					echo '<option value="6"  '.($optIDu5 == 6 ? 'selected="selected"' : '').'>Moderate Environmental Impact</option>';
					echo '<option value="3"  '.($optIDu5 == 3 ? 'selected="selected"' : '').'>Minimal Environmental Harm</option>';
					echo '<option value="1"  '.($optIDu5 == 1 ? 'selected="selected"' : '').'>No Environmental Harm</option>';
				}
			echo '</select>';
			echo '<span id="register_optIDu5_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';

	echo '<div class="row">';
		echo '<div class="col-xs-3">';
			echo '<label for="section">Unmanaged Risk Category</label>';
			
			$optIDu6 = (!empty($id) ? $this->result['optID_u6'] : $this->safeDisplay['optIDu6']);
			echo '<input type="hidden" class="form-control" name="optIDu6" id="optIDu6" value="'.($optIDu6).'">';
			echo '<input type="text" class="form-control" onchange="changes=true;" name="optIDu6TX" id="optIDu6TX" readonly="readonly" style="text-align:center;" value="'.($optIDu6 == 1 ? 'VERY HIGH' :($optIDu6 == 2 ? 'HIGH' :($optIDu6 == 3 ? 'MEDIUM' : ($optIDu6 == 4 ? 'LOW' :($optIDu6 == 5 ? 'VERY LOW' : ''))))).'">';
			echo '<span id="register_optIDu6TX_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size:13px;">Unmanaged Risk Score</label>';
			echo '<input type="text" class="form-control" onchange="changes=true;" id="optIDu7" name="optIDu7" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['optID_u7'] : $this->safeDisplay['optIDu7']).'">';
			echo '<span id="register_optIDu7_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';

	echo '<div class="row">';
		echo '<div class="col-xs-7">';
			echo '<label for="section">Investigation </label>';
			echo '<textarea style="resize:none;" class="form-control" onchange="changes=true;" rows="2" name="descriptionINV" id="descriptionINV" placeholder="Enter Investigation Details">'.(!empty($id) ? $this->result['descriptionINV'] : $this->safeDisplay['descriptionINV']).'</textarea>';
			echo '<span id="register_descriptionINV_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Investigation By</label>';
			$invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']);
			echo '<select class="form-control select2" onchange="changes=true;" style="width: 100%;" id="invID" name="invID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Investigation By --</option>';				
			echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_invID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Investigation Date</label>';
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="invDate" name="invDate" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['invDate']) : '').'">';
			echo '<span id="register_invDate_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-7">';
			echo '<label for="section">Action </label>';
			echo '<textarea style="resize:none;" class="form-control" onchange="changes=true;" rows="2" name="descriptionACD" id="descriptionACD" placeholder="Enter Action Details">'.(!empty($id) ? $this->result['descriptionACD'] : $this->safeDisplay['descriptionACD']).'</textarea>';
			echo '<span id="register_descriptionACD_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Action By</label>';
			$actID = (!empty($id) ? $this->result['actID'] : $this->safeDisplay['actID']);
			echo '<select class="form-control select2" onchange="changes=true;" style="width: 100%;" id="actID" name="actID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Action By --</option>';				
			echo $this->ReportingBundels($actID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_actID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Action Date</label>';
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="actDate" name="actDate" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['actDate']) : '').'">';
			echo '<span id="register_actDate_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Managed Likelihood </label>';
			echo '<select name="optIDm1" class="form-control" onchange="changes=true;" id="optIDm1" >';
				$optIDm1 = (!empty($id) ? ($this->result['optID_m1']) : $this->safeDisplay('optIDm1'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="10" '.($optIDm1 == 10 ? 'selected="selected"' : '').'>May well be expected</option>';
				echo '<option value="6"  '.($optIDm1 == 6 ? 'selected="selected"' : '').'>Quite possible</option>';
				echo '<option value="3"  '.($optIDm1 == 3 ? 'selected="selected"' : '').'>Unusual but possible</option>';
				echo '<option value="1"  '.($optIDm1 == 1 ? 'selected="selected"' : '').'>Only remotely possible</option>';
			echo '</select>';
			echo '<span id="register_optIDm1_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Managed Exposure </label>';
			echo '<select name="optIDm3" class="form-control" onchange="changes=true;" id="optIDm3" >';
				$optIDm3 = (!empty($id) ? $this->result['optID_m3'] : $this->safeDisplay['optIDm3']);
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="10" '.($optIDm3 == 10 ? 'selected="selected"' : '').'>Continuous</option>';
				echo '<option value="6" '.($optIDm3 == 6 ? 'selected="selected"' : '').'>Daily</option>';
				echo '<option value="3" '.($optIDm3 == 3 ? 'selected="selected"' : '').'>Weekly</option>';
				echo '<option value="1" '.($optIDm3 == 1 ? 'selected="selected"' : '').'>Few per day</option>';
			echo '</select>';
			echo '<span id="register_optIDm3_errorloc" class="errors"></span>';
		echo '</div>';
	
		echo '<div class="col-xs-3">';
			echo '<label for="section">Consequence/Impact </label>';
			echo '<select name="optIDm4" class="form-control" onchange="changes=true;" id="optIDm4" >';
				$optIDm4 = (!empty($id) ? ($this->result['optID_m4']) : $this->safeDisplay('optIDm4'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($optIDm4 == 1 ? 'selected="selected"' : '').'>Safety</option>';
				echo '<option value="2" '.($optIDm4 == 2 ? 'selected="selected"' : '').'>Environmental</option>';
			echo '</select>';
			echo '<span id="register_optIDm4_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Secondary Choice </label>';
			echo '<select name="optIDm5" class="form-control" onchange="changes=true;" id="optIDm5" >';
				$optIDm5 = (!empty($id) ? $this->result['optID_m5'] : $this->safeDisplay['optIDm5']);
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				if($optIDm4 == 1)
				{
					echo '<option value="10" '.($optIDm5 == 10 ? 'selected="selected"' : '').'>Fatality or Permanent Disability</option>';
					echo '<option value="6"  '.($optIDm5 == 6 ? 'selected="selected"' : '').'>Serious Injury/Loss Time Injury or Disease</option>';
					echo '<option value="3"  '.($optIDm5 == 3 ? 'selected="selected"' : '').'>Medical Treated Injury or Disease</option>';
					echo '<option value="1"  '.($optIDm5 == 1 ? 'selected="selected"' : '').'>First Aid Treatment (on site) or Work Injury or Disease Report</option>';
				}
				else if($optIDm4 == 2)
				{
					echo '<option value="10" '.($optIDm5 == 10 ? 'selected="selected"' : '').'>Serious Environmental Harm</option>';
					echo '<option value="6"  '.($optIDm5 == 6 ? 'selected="selected"' : '').'>Moderate Environmental Impact</option>';
					echo '<option value="3"  '.($optIDm5 == 3 ? 'selected="selected"' : '').'>Minimal Environmental Harm</option>';
					echo '<option value="1"  '.($optIDm5 == 1 ? 'selected="selected"' : '').'>No Environmental Harm</option>';
				}
			echo '</select>';
			echo '<span id="register_optIDm5_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';

	echo '<div class="row">';	
		echo '<div class="col-xs-3">';
			echo '<label for="section">Managed Risk Category </label>';
			
			$optIDm6 = (!empty($id) ? $this->result['optID_m6'] : $this->safeDisplay['optIDm6']);
			echo '<input type="hidden" class="form-control" name="optIDm6" id="optIDm6" value="'.($optIDm6).'">';
			echo '<input type="text" class="form-control" onchange="changes=true;" name="optIDm6TX" id="optIDm6TX" readonly="readonly" style="text-align:center;" value="'.($optIDm6 == 1 ? 'VERY HIGH' :($optIDm6 == 2 ? 'HIGH' :($optIDm6 == 3 ? 'MEDIUM' : ($optIDm6 == 4 ? 'LOW' :($optIDm6 == 5 ? 'VERY LOW' : ''))))).'">';
			echo '<span id="register_optIDm6TX_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Managed Risk Score</label>';
			echo '<input type="text" class="form-control" onchange="changes=true;" id="optIDm7" name="optIDm7" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['optID_m7'] : $this->safeDisplay['optIDm7']).'">';
			echo '<span id="register_optIDm7_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';	
		echo '<div class="col-xs-2">';
			echo '<label for="section">Action Effective</label>';
			echo '<select name="acteffID" class="form-control" onchange="changes=true;" id="acteffID" >';
				$acteffID = (!empty($id) ? $this->result['act_effID'] : $this->safeDisplay('acteffID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($acteffID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($acteffID == 2 ? 'selected="selected"' : '').'>No</option>';				
			echo '</select>';
			echo '<span id="register_acteffID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Followup Required</label>';
			echo '<select name="fupreqID" class="form-control" onchange="changes=true;" id="fupreqID" >';
				$fupreqID = (!empty($id) ? $this->result['fupreqID'] : $this->safeDisplay('fupreqID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($fupreqID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($fupreqID == 2 ? 'selected="selected"' : '').'>No</option>';
				echo '<option value="3" '.($fupreqID == 3 ? 'selected="selected"' : '').'>NA</option>';
			echo '</select>';
			echo '<span id="register_fupreqID_errorloc" class="errors"></span>';
		echo '</div>';
		
		$fupcaseRD = (empty($fupreqID) || $fupreqID == 1 ? '' : 'readonly="readonly"');
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Proposed Followup Date</label>';			
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="fupreqDT" name="fupreqDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['fupreqDT']) : '').'">';
			echo '<span id="register_fupreqDT_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Followup Completed By</label>';
			$fupcmpID = (!empty($id) ? $this->result['fupcmpID'] : $this->safeDisplay['fupcmpID']);
			echo '<select class="form-control select2" onchange="changes=true;" style="width: 100%;" id="fupcmpID" name="fupcmpID">';
			echo '<option value="0" selected="selected">-- Select Followup Completed By --</option>';				
			echo $this->ReportingBundels($fupcmpID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_fupcmpID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Followup Completed Date</label>';			
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="fupcmpDT" name="fupcmpDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['fupcmpDT']) : '').'">';
			echo '<span id="register_fupcmpDT_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">'; 		
		echo '<div class="col-xs-6">';
			echo '<label for="section">Follow Up Details </label>';
			echo '<textarea style="resize:none;" class="form-control" onchange="changes=true;" '.$fupcaseRD.' rows="2" name="fupDesc" id="fupDesc" placeholder="Enter Follow Up Details">'.(!empty($id) ? $this->result['fupDesc'] : $this->safeDisplay['fupDesc']).'</textarea>';
			echo '<span id="register_fupDesc_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Hazard Corrected Date</label>';			
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="hzrconDT" name="hzrconDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['hzrconDT']) : '').'">';
			echo '<span id="register_hzrconDT_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Date Employee Advised</label>';			
			echo '<input type="datable" class="form-control datepicker" onchange="changes=true;" data-datable="ddmmyyyy" id="empadvDT" name="empadvDT" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['empadvDT']) : '').'">';
			echo '<span id="register_empadvDT_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Status</label>';
			echo '<select name="statusID" class="form-control" onchange="changes=true;" id="HIZstatusID" >';
				$statusID = (!empty($id) ? $this->result['statusID'] : $this->safeDisplay('statusID'));
				$statusID = ($statusID > 0 ? $statusID : 1);
				echo '<option value="0" selected="selected"> --- Select --- </option>';				
				echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Pending</option>';
				echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>Complete</option>';
			echo '</select>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div><br />';
	
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
            echo '<input name="ID" value="'.$id.'" type="hidden">';
            echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" id="hizregisSubmit" type="submit">'.(!empty($id) ? 'Update Hazard Register' : 'Save Hazard Register').'</button>';
	  echo '</div>';
          
      echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
      echo '</div>';
	echo '</div>';
	
	echo '<div id="HizRegisValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;

			if($HIZrefno == '') 		   $errors .= "Enter The HZ No.<br>";

			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			} 
			else
			{	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE refno =:cID AND companyID = :companyID "); 
				$Qry->bindParam(':cID',$HIZrefno);
				$Qry->bindParam(':companyID',($this->companyID));
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];

				if($rowCount > 0 ) 
				{
					$this->printMessage('danger',' HZ No : '.$HIZrefno.'<br />Already exist !...');
					$this->createForm();
				}
				else
				{
					$_POST['refno'] = $HIZrefno;
					$_POST['fstaffID'] = $empID;
					$_POST['act_effID'] = $acteffID;
					
					unset($_POST['empID'],$_POST['acteffID']);
					
					for($srID = 1; $srID <= 7; $srID++)
					{
						$_POST['optID_u'.$srID] = $_POST['optIDu'.$srID];
						$_POST['optID_m'.$srID] = $_POST['optIDm'.$srID];
						
						unset($_POST['optIDu'.$srID]);
						unset($_POST['optIDm'.$srID]);
					}
					
					unset($_POST['optIDu6TX'],$_POST['optIDm6TX'],$_POST['HIZrefno']);
					unset($_POST['sstatusID']);
					
					$_POST['rdateID']  = $this->dateFormat($_POST['rdateID']);
					$_POST['dateID']   = $this->dateFormat($_POST['dateID']);
					$_POST['rcdateID'] = $this->dateFormat($_POST['rcdateID']);
					$_POST['invDate']  = $this->dateFormat($_POST['invDate']);
					$_POST['actDate']  = $this->dateFormat($_POST['actDate']);
					$_POST['fupreqDT'] = $this->dateFormat($_POST['fupreqDT']);
					$_POST['fupcmpDT'] = $this->dateFormat($_POST['fupcmpDT']);
					$_POST['hzrconDT'] = $this->dateFormat($_POST['hzrconDT']);
					$_POST['empadvDT'] = $this->dateFormat($_POST['empadvDT']);					
					$_POST['userID']  = $_SESSION[$this->website]['userID'];
					$_POST['companyID'] = $this->companyID;
					
					unset($_POST['Submit']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['systemID']  = $this->get_systemID($_POST['reportBY']);
					$array['logID'] = date('Y-m-d H:i:s');
					//echo '<PRE>'; echo print_r($array);exit;
					//echo '<PRE>'; echo print_r($_POST); exit;
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);

						$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['fstaffID'],$array['fscodeID'],$refno,'A',$array['description'],$array);

						$this->msg = urlencode('Hazard Register is Created Successfully . <br /> HZ No : '.$array['refno']);
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

			if($HIZrefno == '') 		   $errors .= "Enter The HZ No.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE refno =:cID AND companyID = :companyID AND ID <> :ID ");
				$Qry->bindParam(':cID',$HIZrefno);
				$Qry->bindParam(':companyID',($this->companyID));
				$Qry->bindParam(':ID',$ID);				
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];

				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','HZ No : '.$HIZrefno.'<br />Already exist !...');
					$this->createForm($ID);
				}
				else
				{
					$_POST['refno'] = $HIZrefno;
					$_POST['fstaffID'] = $empID;
					$_POST['act_effID'] = $acteffID;
					
					unset($_POST['empID'],$_POST['acteffID']);
					unset($_POST['sstatusID']);
					
					for($srID = 1; $srID <= 7; $srID++)
					{
						$_POST['optID_u'.$srID] = $_POST['optIDu'.$srID];
						$_POST['optID_m'.$srID] = $_POST['optIDm'.$srID];
						
						unset($_POST['optIDu'.$srID]);
						unset($_POST['optIDm'.$srID]);
					}
					unset($_POST['optIDu6TX'],$_POST['optIDm6TX'],$_POST['HIZrefno']);
					
					$_POST['rdateID']  = $this->dateFormat($_POST['rdateID']);
					$_POST['dateID']   = $this->dateFormat($_POST['dateID']);
					$_POST['rcdateID'] = $this->dateFormat($_POST['rcdateID']);
					$_POST['invDate']  = $this->dateFormat($_POST['invDate']);
					$_POST['actDate']  = $this->dateFormat($_POST['actDate']);
					$_POST['fupreqDT'] = $this->dateFormat($_POST['fupreqDT']);
					$_POST['fupcmpDT'] = $this->dateFormat($_POST['fupcmpDT']);
					$_POST['hzrconDT'] = $this->dateFormat($_POST['hzrconDT']);
					$_POST['empadvDT'] = $this->dateFormat($_POST['empadvDT']);					
					
					unset($_POST['Submit'],$_POST['ID']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$array['systemID']  = $this->get_systemID($_POST['reportBY']);
					$on['ID'] = $ID;
					//echo '<pre>'; echo print_r($array); exit;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 		
						$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['fstaffID'],$array['fscodeID'],$refno,'E',$array['description'],$array);

						$this->msg = urlencode('Hazard Register is Updated Successfully . <br /> HZ No : '.$array['refno']);
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