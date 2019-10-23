<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
	
		$this->basefile     =	basename($_SERVER['PHP_SELF']);		
		$this->tableName    =	'accident_regis';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->frmID		= '42';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
        
	public function view($fd,$td,$searchbyID,$passSTR,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if($auditID <> '')
			{
				$str .= " AND accident_regis.ID In(".$auditID.") ";
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
				if($fd <> '' && $td <> '')     $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 		
				else                            $str .= " AND progressID = 2 ";


				/* SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);
						
				/* DASHBOARD - SEARCHING */
				$str .= $passSTR;
		
				if($tsystemID <> '')
				{
					$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
				}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);				
					$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".refno LIKE '%".$searchbyID."%'" : "");
					$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
				}
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".staffID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY From_UnixTime(".$this->tableName.".dateID) DESC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Ref No</th>';
				echo '<th>Date</th>';
				echo '<th>Accident Location</th>';
				echo '<th>Accident Category</th>';
				echo '<th>Driver Name</th>';
				echo '<th width="350">Description</th>';
				echo '<th>Pending</th>';
				echo '<th>Damage Cost</th>';
				echo '<th>Driver Responsible</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
				echo '<th style="background:none !important;">RoA</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">RoA Log</th>' : '');
				echo '</tr></thead>'; 
				$dsID_1 = '';
				$dsID_2 = '';
				foreach($this->rows as $row)			
				{
					$arrSU  = $row['suburb'] > 0 	? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['suburb']." ") : '';
					$arrEM  = $row['staffID'] > 0   ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['staffID']." ") : '';
					$arrAC  = $row['acccatID'] > 0  ? $this->select('master',array("title"), " WHERE ID = ".$row['acccatID']." ") : '';

					$dsID_1 = ('Ref No : '.$row['refno'].' , Date : '.$this->VdateFormat($row['dateID'])).'<br/>'.($row['invno'] <> '' ? 'Invoice No : '.$row['invno'] : '').($row['claimno'] <> '' ? ' Claim No : '.$row['claimno'] : '');
					$dsID_2 = ('Bus No : '.$row['busID'].' , Driver : '.($arrEM[0]['fname'].' '.$arrEM[0]['lname']).'-'.$arrEM[0]['code']);

					echo '<tr>';
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Accidents Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td>'.$this->Word_Wraping($row['location'].' <br /> '.($row['suburb'] > 0 ? $arrSU[0]['title'].'('.$arrSU[0]['pscode'].')' : ''),50).'</td>';
					echo '<td>'.$arrAC[0]['title'].'</td>';
					echo '<td>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : ($arrEM[0]['fname'].' '.$arrEM[0]['lname'])).'</td>';
					echo '<td>'.($row['description']).'</td>'; 
					echo '<td align="center">'.($row['oprdoneID'] == 1 ? '' : 'Operations<br />').''.($row['engdoneID'] == 1 ? '' : 'Engineering<br />').''.($row['progressID'] == 1 ? '' : 'Admin').'</td>';
					echo '<td align="right">'.($row['rprcost'] + $row['othcost']).'</td>';
					echo '<td align="center">'.($row['responsibleID'] == 1 ? 'Yes' :($row['responsibleID'] == 2 ? 'No' : '')).'</td>';

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
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Accidents Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Accidents Details').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
			
					//echo '<td align="center"><a class="fa fa-desktop accpopID" aria-sort="'.$dsID_1.'" aria-busy="'.$dsID_2.'" style="cursor:pointer;" title="Accidents Details" data-title="'.$row['ID'].'"></a></td>';


					if($row['tsystemID'] > 0)	{echo '<td></td>';}
					else
					{
							echo '<td align="center"><a class="fa fa-desktop" href="'.$this->basefile.'?a='.$this->Encrypt('popups').'&i='.$this->Encrypt($row['ID']).'" style="cursor:pointer;" title="Accidents Details"></a></td>';
					}

					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
							$uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = 78 ") : '';
							if($uscountsID > 0)
							{
									echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.('78_'.$row['ID'].'_Record Of Action ').'" 
									style="text-decoration:none; cursor:pointer;"></a></td>';
							}
							else	{echo '<td></td>';}
					}

					echo '</tr>';
				}
				echo '</table>';			
			} 
		}
		else {echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Accidents Register</b> Page</div></div>';}
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
			echo '<label for="section">Ref No <span class="Maindaitory">*</span></label>';
			$refNO = (!empty($id) ? ($this->result['refno']) : $this->safeDisplay['accrefno']);
			$refNO = ($refNO <> '' ? $refNO : $srnoID);
			echo '<input type="text" onchange="changes=true;" class="form-control" name="accrefno" id="accrefno" placeholder="Enter Ref No" style="text-align:center;" required="required" value="'.$refNO.'">';
			echo '<span id="register_accrefno_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Bus No </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="busID" name="busID" placeholder="Enter Bus No" style="text-align:center;" value="'.(!empty($id) ? $this->result['busID'] : $this->safeDisplay('busID')).'">';
			echo '<span id="register_busID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Accident Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="dd/mm/yyyy" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
				echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Accident Time </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID" name="timeID" placeholder="Enter Time" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ?  $this->result['timeID'] : '').'">';
			echo '<span id="register_timeID_errorloc" class="errors"></span>';
		echo '</div>';
                
		echo '<div class="col-xs-2">';
			echo '<label for="section">Trainee Accident</label><br />';
			echo '<select onchange="changes=true;" class="form-control" id="tickID_2" name="tickID_2">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Trainee --</option>';
				$tickID_2 = !empty($id) ? $this->result['tickID_2'] : '2';                                
				echo '<option value="1" '.($tickID_2 == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($tickID_2 == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
		echo '</div>';
                
	echo '</div>';
	
	$tickID_1 = (!empty($id) ? $this->result['tickID_1'] : $this->safeDisplay['tickID_1']);
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-4">';
			echo '<label for="section">Driver Name</label>';
			$staffID = !empty($id) ? $this->result['staffID'] : $this->safeDisplay['empID'];
			$arrDB = $staffID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$staffID." ") : '';
			if($arrDB[0]['status'] == 2)
			{
				echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$staffID.'">';
				echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
			}
			else
			{
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" '.($tickID_1 == 1 ? 'disabled="disabled"' : '').' id="empID" name="empID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';                        
				echo $this->GET_Employees11($staffID,"AND status = 1 ");
				echo '</select>';
				echo '<span id="register_empID_errorloc" class="errors"></span>';
			}
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver ID</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="scodeID" placeholder="Driver ID" readonly="readonly" 
			style="text-align:center;" value="'.(!empty($id) ? $this->result['scodeID'] : $this->safeDisplay['scodeID']).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver Not Applicable</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="tickID_1" id="tickID_1" value="1" '.($tickID_1 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
			
		echo '<div class="col-xs-2">';
			echo '<label for="section">Accident Category</label>';
			echo '<select name="acccatID" onchange="changes=true;" class="form-control" id="acccatID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['acccatID'] : $this->safeDisplay['acccatID']),'21');
			echo '</select>';
			echo '<span id="register_acccatID_errorloc" class="errors"></span>';
		echo '</div>';

		$plcntID = (!empty($id) ? $this->result['plcntID'] : $this->safeDisplay['plcntID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">Police Notified</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="plcntID" id="plcntID" value="1" '.($plcntID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-4">';
			echo '<label for="section">Accident Details</label>';
			echo '<select name="accID" onchange="changes=true;" class="form-control" id="accID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['accID'] : $this->safeDisplay['accID']),'20');
			echo '</select>';
			echo '<span id="register_accID_errorloc" class="errors"></span>';
		echo '</div>';  
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Driver Responsible</label>';
			echo '<select name="responsibleID" onchange="changes=true;" class="form-control" id="responsibleID" >';
				$responsibleID = (!empty($id) ? ($this->result['responsibleID']) : $this->safeDisplay('responsibleID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($responsibleID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($responsibleID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_responsibleID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver Drug Tested</label>';
			echo '<select name="optID3" onchange="changes=true;" class="form-control" id="optID3" >';
				$optID_3 = (!empty($id) ? ($this->result['optID_3']) : $this->safeDisplay('optID3'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($optID_3 == 1 ? 'selected="selected"' : '').'>No</option>';
				echo '<option value="2" '.($optID_3 == 2 ? 'selected="selected"' : '').'>Swan</option>';
				echo '<option value="3" '.($optID_3 == 3 ? 'selected="selected"' : '').'>Police</option>';
				echo '<option value="4" '.($optID_3 == 4 ? 'selected="selected"' : '').'>Both</option>';
			echo '</select>';
			echo '<span id="register_optID3_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver Breath Tested</label>';
			echo '<select name="optID2" onchange="changes=true;" class="form-control" id="optID2" >';
				$optID_2 = (!empty($id) ? ($this->result['optID_2']) : $this->safeDisplay('optID2'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($optID_2 == 1 ? 'selected="selected"' : '').'>No</option>';
				echo '<option value="2" '.($optID_2 == 2 ? 'selected="selected"' : '').'>Swan</option>';
				echo '<option value="3" '.($optID_2 == 3 ? 'selected="selected"' : '').'>Police</option>';
				echo '<option value="4" '.($optID_2 == 4 ? 'selected="selected"' : '').'>Both</option>';
			echo '</select>';
			echo '<span id="register_optID2_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Location</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="location" name="location" placeholder="Enter Location" value="'.(!empty($id) ? $this->result['location'] : $this->safeDisplay('location')).'">';
			echo '<span id="register_location_errorloc" class="errors"></span>';
		echo '</div>';
			
		echo '<div class="col-xs-4">';
			echo '<label for="section">Suburb</label>';
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="suburb" name="suburb">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Suburb --</option>';
				echo $this->GET_SubUrbs((!empty($id) ? $this->result['suburb'] : $this->safeDisplay['suburb']),'');
			echo '</select>';
			echo '<span id="register_suburb_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Damage to Bus</label>';
			echo '<select name="damagetobusID" onchange="changes=true;" class="form-control" id="damagetobusID" >';
				$damagetobusID = (!empty($id) ? ($this->result['damagetobusID']) : $this->safeDisplay('damagetobusID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($damagetobusID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($damagetobusID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_damagetobusID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Photographs of Damage</label>';
			echo '<select name="optID1" onchange="changes=true;" class="form-control" id="optID1" >';
				$optID_1 = (!empty($id) ? ($this->result['optID_1']) : $this->safeDisplay('optID1'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($optID_1 == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($optID_1 == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_optID1_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-6">';
			echo '<label for="section">Reason</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" placeholder="Enter Reason">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">'; 
			echo '<label for="section">Bus Repairs (Cost)</label>';			
			echo '<input type="text" onchange="changes=true;" class="form-control numeric" id="rprcost" name="rprcost" placeholder="Enter Bus Repairs (Cost)" '.($damagetobusID == 1 ? '' : 'readonly="readonly"').' value="'.(!empty($id) ? $this->result['rprcost'] : $this->safeDisplay['rprcost']).'" style="text-align:right;">';
			echo '<span id="register_rprcost_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Other Repairs (Cost)</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control numeric" id="othcost" name="othcost" placeholder="Enter Other Repairs (Cost)" value="'.(!empty($id) ? $this->result['othcost'] : $this->safeDisplay['othcost']).'" style="text-align:right;">';
			echo '<span id="register_othcost_errorloc" class="errors"></span>';
		echo '</div>';
                
		$engdoneID = (!empty($id) ? $this->result['engdoneID'] : $this->safeDisplay['engdoneID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">Engineering Completed</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="engdoneID" id="engdoneID" value="1" '.($engdoneID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';

	echo '<div class="row">'; 
		$thpartyID = (!empty($id) ? $this->result['3partyID'] : $this->safeDisplay['3partyID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">Third Party</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" id="3partyID" name="thpartyID" value="1" '.($thpartyID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>'; 
		
		$thpr_SET = ($thpartyID == 1 ? '' : 'readonly="readonly"');
		echo '<div class="col-xs-3">';
			echo '<label for="section">Third Party Name</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="thpnameID" name="thpnameID" placeholder="Enter Third Party Name" '.$thpr_SET.' value="'.(!empty($id) ? $this->result['thpnameID'] : $this->safeDisplay('thpnameID')).'">';
			echo '<span id="register_thpnameID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Third Party Rego No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="regisnoID" name="regisnoID" placeholder="Enter Registration No" '.$thpr_SET.' value="'.(!empty($id) ? $this->result['regisnoID'] : $this->safeDisplay('regisnoID')).'">';
			echo '<span id="register_regisnoID_errorloc" class="errors"></span>';
		echo '</div>';
                
		echo '<div class="col-xs-4">';
			echo '<label for="section">Third Party Contact Info</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="thcontactID" name="thcontactID" placeholder="Enter Contact Info" '.$thpr_SET.' value="'.(!empty($id) ? $this->result['thcontactID'] : $this->safeDisplay('thcontactID')).'">';
			echo '<span id="register_thcontactID_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		$insinvolvedID = (!empty($id) ? $this->result['insinvolvedID'] : $this->safeDisplay['insinvolvedID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">Insurer Required</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" id="insinvolvedID" name="insinvolvedID" value="1" '.($insinvolvedID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>'; 
		
		$insinv_SET = ($insinvolvedID == 1 ? '' : 'readonly="readonly"');
		$innoSetups = ($thpartyID == 1 || $insinvolvedID == 1 ? '' : 'readonly="readonly"');
		echo '<div class="col-xs-3">';
			echo '<label for="section">Insurer</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="insurerID" name="insurerID" placeholder="Enter Insurer" '.$insinv_SET.' value="'.(!empty($id) ? $this->result['insurer'] : $this->safeDisplay('insurerID')).'">';
			echo '<span id="register_insurerID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Claim No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="claimnoID" name="claimnoID" placeholder="Enter Claim No" '.$insinv_SET.' value="'.(!empty($id) ? $this->result['claimno'] : $this->safeDisplay('claimnoID')).'">';
			echo '<span id="register_claimnoID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Invoice No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="invnoID" name="invnoID" placeholder="Enter Invoice No" '.$innoSetups.' value="'.(!empty($id) ? $this->result['invno'] : $this->safeDisplay('invnoID')).'">';
			echo '<span id="register_invnoID_errorloc" class="errors"></span>';
		echo '</div>';
		
		$admindoneID = (!empty($id) ? $this->result['admindoneID'] : $this->safeDisplay['admindoneID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">Admin Completed</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="admindoneID" id="admindoneID" value="1" '.($admindoneID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="row">';
		$witnessID = (!empty($id) ? $this->result['witnessID'] : $this->safeDisplay['witnessID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">For Accident</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" id="witnessID" name="witnessID" value="1" '.($witnessID == 1 ? 'checked="checked"' : '').' />';
		echo '</div>'; 
		
		$witnessDesc_SET = ($witnessID == 1 ? '' : 'readonly="readonly"');
		echo '<div class="col-xs-3">';
			echo '<label for="section">Witness Name</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="witnessName" name="witnessName" placeholder="Enter Witness Name" '.$witnessDesc_SET.' value="'.(!empty($id) ? $this->result['witnessName'] : $this->safeDisplay('witnessName')).'">';
			echo '<span id="register_witnessName_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Witness Contact No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="witnessContact" name="witnessContact" placeholder="Enter Witness Contact No" '.$witnessDesc_SET.' value="'.(!empty($id) ? $this->result['witnessContact'] : $this->safeDisplay('witnessContact')).'">';
			echo '<span id="register_witnessContact_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-8">';
			echo '<label for="section">Investigation Outcome / Recommendations </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="outcome" id="outcome" placeholder="Enter Outcome">'.(!empty($id) ? $this->result['outcome'] : $this->safeDisplay['outcome']).'</textarea>';
			echo '<span id="register_outcome_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Investigated By</label>';
			$invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invID" name="invID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Investigated By --</option>';				
			echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_invID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
		
	echo '<div class="row">';	
		echo '<div class="col-xs-2">';
			echo '<label for="section">Discipline Related</label>';
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
		
			$oprdoneID = (!empty($id) ? $this->result['oprdoneID'] : $this->safeDisplay['oprdoneID']);
			echo '<div class="col-xs-2">';
				echo '<label for="section">Operations Completed</label><br />';
				echo '<input class="icheckbox_minimal checked" type="checkbox" name="oprdoneID" id="oprdoneID" value="1" '.($oprdoneID == 1 ? 'checked="checked"' : '').' />';
			echo '</div>';
			
			echo '<div class="col-xs-2">';
				echo '<label for="section">Progress</label>';
				echo '<select name="progressID" onchange="changes=true;" class="form-control" id="progressID" >';
					$progressID = (!empty($id) ? ($this->result['progressID']) : '2');
					echo '<option value="0" selected="selected"> --- Select --- </option>';				
					echo '<option value="1" '.($progressID == 1 ? 'selected="selected"' : '').'>Complete</option>';
					echo '<option value="2" '.($progressID == 2 ? 'selected="selected"' : '').'>Pending</option>';
					echo '<option value="3" '.($progressID == 3 ? 'selected="selected"' : '').'>Written Off</option>';
				echo '</select>';
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
	echo '</div><br />';
	
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
            echo '<input name="ID" value="'.$id.'" type="hidden">';
            echo '<button class="btn btn-primary btn-flat" name="Submit" id="accidentsSubmit" type="submit">'.(!empty($id) ? 'Update Accidents Register' : 'Save Accidents Register').'</button>';
	  echo '</div>';
          
      echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
      echo '</div>';
	echo '</div>';
	
	echo '<div id="AccidentValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
        
	public function createPopUpForm($id='')
	{
            $this->action = 'edit_popups';
            
            $FORM_Array = ($id > 0 ? $this->select($this->tableName,array("*"), " WHERE ID = ".$id." ") : '');
            
	echo '<form method="post" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body">';

	echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
                    echo '<label for="section">Ref No <span class="Maindaitory">*</span></label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" style="text-align:center;" readonly="readonly" value="'.($FORM_Array[0]['refno']).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
                    echo '<label for="section">Bus No </label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" style="text-align:center;" readonly="readonly" value="'.($FORM_Array[0]['busID']).'">';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
                    echo '<label for="section">Accident Date </label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" style="text-align:center;" readonly="readonly" value="'.($this->VdateFormat($FORM_Array[0]['dateID'])).'">';
		echo '</div>'; 
                
		echo '<div class="col-xs-4">';
                    echo '<label for="section">Staff Name</label>';
                    echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" disabled="disabled">';
                        echo '<option value="0" selected="selected" disabled="disabled">-- Select Staff --</option>';
                        echo $this->GET_Employees11($FORM_Array[0]['staffID'],"AND status = 1 ");
                    echo '</select>';
		echo '</div>';
                echo '<input type="hidden" name="staffID" value="'.$FORM_Array[0]['staffID'].'" />';
				
                echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div><br />';
	
        echo '<div class="row">';
            echo '<div class="col-xs-9">';
                echo '<table id="dataTablesAC" class="table table-bordered table-striped">';				

                echo '<thead><tr>';
                echo '<th colspan="3" style="text-align:center !important; background:#3C8DBC; color:white;">Accidents (Record of Action)</th>';
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th style="text-align:center !important;"><a style="cursor:pointer; text-decoration:none;" class="fa fa-plus DTaccpopID"></a></th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Accidents Detail/Remarks</th>';
                echo '</tr></thead>';
                if(!empty($id) && ($id > 0))  
                    {
                        echo $this->PopUpsAccidents($id);                       
                    }
                echo '</table>';
            echo '</div>';
        
        echo '<div class="col-xs-3">';
            echo '<a href="'.$this->home.'rpts-c/rpt_accident.php?i='.$this->Encrypt($id).'" target="blank" style="float:right;" class="btn btn-primary btn-flat fa fa-print" id="PrintAccID"> Print Accident Report</a>';
        echo '</div>';
        
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
        echo '</div><br />';
                
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Accidents Register' : 'Save Accidents Register').'</button>';
	  echo '</div>';
	echo '</div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			// echo '<PRE>'; echo print_r($_POST); exit;

			if($accrefno == '') 	   $errors .= "Enter The Accident Ref No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Accident Date.<br>";

			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			} 
			else
			{	 
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);		$_POST['intvDate']  = $this->dateFormat($_POST['intvDate']);
				$_POST['userID']  = $_SESSION[$this->website]['userID'];		$_POST['optID_1'] = $_POST['optID1'];				
				$_POST['optID_2'] = $_POST['optID2'];				$_POST['optID_3'] = $_POST['optID3'];				
				$_POST['insurer'] = $_POST['insurerID'];			$_POST['claimno'] = $_POST['claimnoID'];			
				$_POST['invno']   = $_POST['invnoID'];				$_POST['refno']   = $_POST['accrefno'];
				$_POST['disciplineID'] = $_POST['cmdiscID'];		$_POST['staffID'] = $_POST['empID'];
				
				$_POST['tickID_1']    = $tickID_1 > 0  	 ? $tickID_1  	: 0;		$_POST['tickID_2']    = $tickID_2 > 0  	 ? $tickID_2  	: 0;
				$_POST['plcntID']     = $plcntID > 0   	 ? $plcntID   	: 0;		$_POST['3partyID']    = $thpartyID > 0 	 ? $thpartyID 	: 0;				
				$_POST['engdoneID']   = $engdoneID > 0 	 ? $engdoneID 	: 0;		$_POST['oprdoneID']   = $oprdoneID > 0 	 ? $oprdoneID   : 0;									
				$_POST['witnessID']   = $witnessID > 0 	 ? $witnessID	: 0;		$_POST['admindoneID'] = $admindoneID > 0 ? $admindoneID : 0;
				
				$_POST['rprcost'] = ($rprcost > 0 ? $rprcost :($rprcost == 0 ? '0' :  ''));
				$_POST['othcost'] = ($othcost > 0 ? $othcost :($othcost == 0 ? '0' :  ''));
				$_POST['insinvolvedID'] = $insinvolvedID > 0 ? $insinvolvedID : 0;				
				$_POST['companyID'] = $this->companyID;
				
				unset($_POST['insurerID'],$_POST['claimnoID'],$_POST['invnoID'],$_POST['accrefno']);					
				unset($_POST['Submit'],$_POST['thpartyID'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['initial'],$_POST['optID1'],$_POST['optID2'],$_POST['optID3']);

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

					$this->PUSH_userlogsID($this->frmID,$lastID[0],$_POST['dateID'],$staffID,$scodeID,$accrefno,'A',$array['description'],$array);

					$this->msg = urlencode(' Accident Register is Created Successfully . <br /> Accident Ref No : '.$array['refno']);
					$param = array('a'=>'view','t'=>'success','m'=>$this->msg,'i'=>$lastID[0]);						
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
		if($this->Form_Variables() == true)        // echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$errors	=	'';

			if($accrefno == '') 	   $errors .= "Enter The Accident Ref No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Accident Date.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{  
				$_POST['tickID_1']    = $tickID_1 > 0  	 ? $tickID_1  	: 0;		$_POST['tickID_2']    = $tickID_2 > 0  	 ? $tickID_2  	: 0;
				$_POST['plcntID']     = $plcntID > 0   	 ? $plcntID   	: 0;		$_POST['3partyID']    = $thpartyID > 0 	 ? $thpartyID 	: 0;				
				$_POST['engdoneID']   = $engdoneID > 0 	 ? $engdoneID 	: 0;		$_POST['oprdoneID']   = $oprdoneID > 0 	 ? $oprdoneID   : 0;									
				$_POST['witnessID']   = $witnessID > 0 	 ? $witnessID	: 0;		$_POST['admindoneID'] = $admindoneID > 0 ? $admindoneID : 0;
				
				$_POST['rprcost'] = ($rprcost > 0 ? $rprcost :($rprcost == 0 ? '0' :  ''));
				$_POST['othcost'] = ($othcost > 0 ? $othcost :($othcost == 0 ? '0' :  ''));		
				$_POST['insinvolvedID'] = $insinvolvedID > 0 ? $insinvolvedID : 0;
				$_POST['dateID']    = $this->dateFormat($_POST['dateID']);			$_POST['intvDate']  = $this->dateFormat($_POST['intvDate']);
				$_POST['optID_1']   = $_POST['optID1'];								$_POST['optID_2']   = $_POST['optID2'];
				$_POST['optID_3']   = $_POST['optID3'];								$_POST['disciplineID'] = $_POST['cmdiscID'];
				$_POST['staffID'] = $_POST['empID'];								$_POST['insurer'] = $_POST['insurerID'];
				$_POST['claimno'] = $_POST['claimnoID'];							$_POST['invno']   = $_POST['invnoID'];
				$_POST['refno']   = $_POST['accrefno'];
				
				unset($_POST['insurerID'],$_POST['claimnoID'],$_POST['invnoID'],$_POST['accrefno']);
				unset($_POST['Submit'],$_POST['thpartyID'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['ID'],$_POST['initial'],$_POST['optID1'],$_POST['optID2'],$_POST['optID3']);

				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($_POST['staffID']);
				$on['ID'] = $ID;
				//echo '<pre>'; echo print_r($array); exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{ 		
					$this->PUSH_userlogsID($this->frmID,$ID,$_POST['dateID'],$staffID,$scodeID,$accrefno,'E',$array['description'],$array);

					$this->msg = urlencode(' Accident Register is Updated Successfully . <br /> Accident Ref No : '.$array['refno']);
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
        
	public function add_Popups()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);		//echo '<PRE>'; echo print_r($_POST); exit;				
			
			if(is_array($fieldID_1) && count($fieldID_1) > 0)
			{
				/* DELETE - DATA */
				$this->delete('accident_regis_dtl', " WHERE ID = ".$ID." ");
	
				$statusID = 0;
				foreach ($fieldID_1 as $key=>$fieldID)
				{
					if(!empty($fieldID) && ($fieldID <> ''))
					{
						$array = array();
						$array['ID'] = $ID;
						$array['fieldID_1'] = $this->dateFormat($fieldID);
						$array['fieldID_2'] = $fieldID_2[$key];
						
						if($this->BuildAndRunInsertQuery('accident_regis_dtl',$array))
						{
							$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
							$lastID = $stmt->fetch(PDO::FETCH_NUM);
									
							$this->PUSH_userlogsID('78',$ID,$array['fieldID_1'],$_POST['staffID'],'','','E');							
							$statusID += 1;
						}
						else	{$statusID += 0;}
					}
				}
			}
			
			if($statusID > 0)
			{
				$this->msg = urlencode(' Accident Popup Action is Created Successfully .');
				$param = array('a'=>'popups','t'=>'success','m'=>$this->msg,'i'=>$ID);						
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
?>