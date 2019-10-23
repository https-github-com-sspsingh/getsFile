<?PHP
class Masters extends SFunctions
{
    private	$tableName  =   '';
    private	$basefile   =  '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	=   basename($_SERVER['PHP_SELF']);		
        $this->tableName    =   'incident_regis';
        $this->companyID	= 	$_SESSION[$this->website]['compID'];
        $this->frmID		=   '41';
		$this->cldaysID	    =   $_SESSION[$this->website]['cdysID'];
        $this->permissions  =   $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($fd,$td,$searchbyID,$passSTR,$auditID)
    { 
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
          $str = "";
		  if($auditID <> '')
		  {
			  $str = " AND incident_regis.ID In(".$auditID.") ";
		  }
		  else
		  {
			  if(!empty($fd) || !empty($td))
			  {
					list($fdt,$fm,$fy)	=	explode("/",$fd);
					list($tdt,$tm,$ty)	=	explode("/",$td);

					$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			  }

			  /* DATE - SEARCHING */
			  if($fd <> '' && $td <> '')                          $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
			  elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
			  else                                                $str .= " AND inc_statusID = 0 ";

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
          $SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".driverID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".refno DESC ";
          $Qry = $this->DB->prepare($SQL);
          if($Qry->execute())
          {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>Security<br />Incidents</th>';
                echo '<th>Ref No</th>';			
                echo '<th>Date</th>';
                echo '<th>Driver</th>';
                echo '<th>Incident Location</th>';
                echo '<th>Reported By</th>';
                echo '<th>Incident Type</th>';
                echo '<th>Police Ref</th>';
                echo '<th width="350">Description</th>';
                echo '<th>Pending</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">API<br />Log</th>' : '');
                echo '</tr></thead>';
				$apcountsID = 0; 
                foreach($this->rows as $row)			
                { 
                    $arrEM  = ($row['driverID'] > 0  ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['driverID']." ") : '');
                    $arrIT  = ($row['inctypeID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['inctypeID']." ")  : '');

                    echo '<tr>';
                    echo '<td align="center">'.($row['sincID'] == 1 ? 'Yes' :($row['sincID'] == 2 ? 'No' :'')).'</td>';
                    echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Incident Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
                    echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                    echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].' <br /> ('.$arrEM[0]['code'].')</td>';
                    echo '<td>'.$row['location'].'</td>';
                    echo '<td>'.$row['reportby'].'</td>';
                    echo '<td>'.$arrIT[0]['title'].'</td>';
                    echo '<td>'.$row['plrefno'].'</td>';
                    echo '<td>'.trim($row['description']).'</td>'; 

                    echo '<td align="center"><b style="color:red;">'.($row['actbyID'] == 0 ? 'Operations' : '').'<br />'.($row['inc_statusID'] == 0 ? 'Admin' : '').'</b></td>';

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
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Incident Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
                    }
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Incident Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
                        $apcountsID  = $row['ID'] > 0 ? $this->count_rows('api_senders_logs', " WHERE refID = ".$row['ID']." ") : '';
                        if($apcountsID > 0)
                        {
                            echo '<td align="center"><a class="fa fa fa-desktop POPUP_apilogsID" aria-sort="'.$row['ID'].'" style="text-decoration:none; cursor:pointer;"></a></td>';
                        }
                        else    {echo '<td></td>';}
                    }				
                    echo '</tr>';			
                }
                echo '</table>';
          }
        }
        else    {echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Incident Regiter</b> Page</div></div>';}
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

    $notifiedID_1 = (!empty($id) ? $this->result['notifiedID_1'] : $this->safeDisplay['notifiedID_1']);
    $notifiedID_2 = (!empty($id) ? $this->result['notifiedID_2'] : $this->safeDisplay['notifiedID_2']);
    $notifiedID_3 = (!empty($id) ? $this->result['notifiedID_3'] : $this->safeDisplay['notifiedID_3']);
    $notifiedID_4 = (!empty($id) ? $this->result['notifiedID_4'] : $this->safeDisplay['notifiedID_4']);
    $notifiedID_5 = (!empty($id) ? $this->result['notifiedID_5'] : $this->safeDisplay['notifiedID_5']);
    $notifiedID_6 = (!empty($id) ? $this->result['notifiedID_6'] : $this->safeDisplay['notifiedID_6']);
    $notifiedID_7 = (!empty($id) ? $this->result['notifiedID_7'] : $this->safeDisplay['notifiedID_7']);
    $notifiedID_8 = (!empty($id) ? $this->result['notifiedID_8'] : $this->safeDisplay['notifiedID_8']);

    $attendedID_1 = (!empty($id) ? $this->result['attendedID_1'] : $this->safeDisplay['attendedID_1']);
    $attendedID_2 = (!empty($id) ? $this->result['attendedID_2'] : $this->safeDisplay['attendedID_2']);
    $attendedID_3 = (!empty($id) ? $this->result['attendedID_3'] : $this->safeDisplay['attendedID_3']);
    $attendedID_4 = (!empty($id) ? $this->result['attendedID_4'] : $this->safeDisplay['attendedID_4']);
    $attendedID_5 = (!empty($id) ? $this->result['attendedID_5'] : $this->safeDisplay['attendedID_5']);
    $attendedID_6 = (!empty($id) ? $this->result['attendedID_6'] : $this->safeDisplay['attendedID_6']);
    $attendedID_7 = (!empty($id) ? $this->result['attendedID_7'] : $this->safeDisplay['attendedID_7']);
    $attendedID_8 = (!empty($id) ? $this->result['attendedID_8'] : $this->safeDisplay['attendedID_8']);
    $attendedID_9 = (!empty($id) ? $this->result['attendedID_9'] : $this->safeDisplay['attendedID_9']);

    $classID_1 = '<b style="color:red;">(Notified)</b>';
    $classID_2 = '<b style="color:#00A65B;">(Attended)</b>';
    $gridID = ($this->result['sincID'] == 1 ? '' : 'gridID_2" style="display:none;"');
	
	$srnoID = $this->count_rows($this->tableName, " WHERE ID > 0 AND companyID = ".$this->companyID." ");    
    $srnoID = $_SESSION[$this->website]['compCD'].'-'.sprintf('%02d',($srnoID > 0 ? ($srnoID + 1) : '1'));
	
    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body incidents_forms" id="fg_membersite">';
	
    echo '<div class="row">'; 
            echo '<div class="col-xs-2">';
				$sincID = !empty($id)   ? $this->result['sincID'] : $this->safeDisplay['sincID'];
				$sincID = $sincID > 0   ? $sincID : '2';
				$SI_CS  = (!empty($id) && ($id  > 0)) && ($sincID == 1) ? 'disabled="disabled"' : '';
				echo '<label for="section">Security Incident</label>';
				echo '<select onchange="changes=true;" class="form-control" id="sincID" name="sincID" '.$SI_CS.'>';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';				
					echo '<option value="1" '.($sincID == 1 ? 'selected="selected"' : '').'>Yes</option>';
					echo '<option value="2" '.($sincID == 2 ? 'selected="selected"' : '').'>No</option>';
				echo '</select>';
            echo '</div>';

            echo '<div class="col-xs-2 '.$gridID.'>';
                    echo '<label for="section">CMR No</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="inccmrno" name="inccmrno" placeholder="Enter CMR No" style="text-align:center;" value="'.(!empty($id) ? $this->result['cmrno'] : $this->safeDisplay['inccmrno']).'">';
            echo '</div>';

            echo '<div class="col-xs-2 '.$gridID.'>';
                    echo '<label for="section">Date Driver Reported<span class="Maindaitory">*</span></label>';
                    echo '<input type="text" required="required" onchange="changes=true;" class="form-control datepicker inc_report_date" id="rpdateID" name="rpdateID" placeholder="Enter Date Driver Reported" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['rpdateID']) : '').'">';
                    echo '<span id="register_rpdateID_errorloc" class="errors"></span>';
            echo '</div>'; 
    echo '</div><br />';

    echo '<div class="row">'; 
            echo '<div class="col-xs-2">';
				echo '<label for="section">Ref No <span class="Maindaitory">*</span></label>';
				$refNO = (!empty($id) ? ($this->result['refno']) : $this->safeDisplay['increfno']);
				$refNO = ($refNO <> '' ? $refNO : $srnoID);
				echo '<input type="text" onchange="changes=true;" class="form-control" name="increfno" id="increfno" placeholder="Enter Ref No" style="text-align:center;" required="required" value="'.$refNO.'">';
				echo '<span id="register_increfno_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Incident Date <span class="Maindaitory">*</span></label>';
                    echo '<input type="datable" onchange="changes=true;" class="form-control datepicker inc_incident_date" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Incident Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
                    echo '<span id="register_dateID_errorloc" class="errors"></span>';
            echo '</div>';

    /*	echo '<div class="col-xs-2">';
                    echo '<label for="section">Time Occurred</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="timeID" name="timeID" placeholder="Enter Time" 
                    style="text-align:center;" value="'.(!empty($id) ?  $this->result['timeID'] : date('h : i : A')).'">';
            echo '</div>';
    */

            echo '<div class="col-xs-2">';
				echo '<label for="section">Time Occurred</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID" name="timeID" maxlength="5" placeholder="hh:mm" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID'] : $this->safeDisplay['timeID']).'">';
				echo '<span id="register_timeID_errorloc" class="errors"></span>';
            echo '</div>';


            echo '<div class="col-xs-2"></div>';

            $brs_statusID = (!empty($id) ? ($this->result['brs_statusID']) : 0);
            echo '<div class="col-xs-2">';
                    echo '<label for="section" style="color:green; font-weight:bold;">Bus/Route/Shift Number(Applicable)</label><br />';
                    echo '<input class="icheckbox_minimal checked" type="checkbox" name="brs_statusID" id="brs_statusID" value="1" '.($brs_statusID == 1 ? 'checked="checked"' : '').' />';
            echo '</div>';

            $inc_statusID = (!empty($id) ? ($this->result['inc_statusID']) : $this->safeDisplay('inc_statusID'));
            echo '<div class="col-xs-2">';
                    echo '<label for="section" style="color:green; font-weight:bold;">Incident Status (Complete)</label><br />';
                    echo '<input class="icheckbox_minimal checked" type="checkbox" name="inc_statusID" value="1" '.($inc_statusID == 1 ? 'checked="checked"' : '').' />';
            echo '</div>';
            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';

    echo '<div class="row">'; 
            echo '<div class="col-xs-4">';
                echo '<label for="section">Driver Name</label>';
                $driverID = !empty($id) ? $this->result['driverID'] : $this->safeDisplay['empID'];
                $arrDB = $driverID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$driverID." ") : '';
                if($arrDB[0]['status'] == 2)
                {
                    echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$driverID.'">';
                    echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
                }
                else
                {
                    echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="empID" name="empID">';
                    echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';                            
                    echo $this->GET_Employees11($driverID,"");
                    echo '</select>';
					echo '<span id="register_empID_errorloc" class="errors"></span>';
                }
            echo '</div>';

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Driver ID</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="dcodeID" placeholder="Driver ID" readonly="readonly" 
                    style="text-align:center;" value="'.(!empty($id) ? $this->result['dcodeID'] : '').'">';
            echo '</div>';  

            echo '<div class="col-xs-2 '.$gridID.'>';
                    echo '<label for="section">PTA Ref No </label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="ptarefNO" name="ptarefNO" placeholder="Enter PTA Ref No" style="text-align:center;" value="'.(!empty($id) ? $this->result['pta_refNO'] : $this->safeDisplay('ptarefNO')).'">';
					echo '<span id="register_ptarefNO_errorloc" class="errors"></span>';
            echo '</div>'; 

            echo '<div class="col-xs-2">';
				echo '<label for="section">Bus No </label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="busID" name="busID" placeholder="Enter Bus No"  '.($brs_statusID == 1 ? '' : 'readonly="readonly"').' style="text-align:center;" value="'.(!empty($id) ? $this->result['busID'] : $this->safeDisplay('busID')).'">';
				echo '<span id="register_busID_errorloc" class="errors"></span>';
            echo '</div>'; 

            echo '<div class="col-xs-2">';
				echo '<label for="section">Service No </label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="routeID" name="routeID" placeholder="Enter Service No" '.($brs_statusID == 1 ? '' : 'readonly="readonly"').' style="text-align:center;" value="'.(!empty($id) ? $this->result['routeID'] : $this->safeDisplay('routeID')).'">';
				echo '<span id="register_routeID_errorloc" class="errors"></span>';
            echo '</div>';
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-2">';
				echo '<label for="section">Shift No</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control numeric" id="shiftID" name="shiftID" placeholder="Enter Shift No" '.($brs_statusID == 1 ? '' : 'readonly="readonly"').' style="text-align:center;" value="'.(!empty($id) ? $this->result['shiftID'] : $this->safeDisplay('shiftID')).'">';
				echo '<span id="register_shiftID_errorloc" class="errors"></span>';
            echo '</div>';

            $plrefID = (!empty($id) ? $this->result['plrefID'] : $this->safeDisplay['plrefID']);
            echo '<div class="col-xs-2">';
                    echo '<label for="section"> Police '.$classID_1.'</label><br />';
                    echo '<input class="icheckbox_minimal checked" type="checkbox" name="plrefID" id="plrefID" value="1" 
                    '.($plrefID == 1 ? 'checked="checked"' : '').' />';
            echo '</div>';

            echo '<div class="col-xs-2">';
				echo '<label for="section">Police Ref No</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="plrefnoID" name="plrefnoID" placeholder="Police Ref No"  style="text-align:center;" '.($plrefID == 1 ? '' : 'readonly="readonly"').' value="'.(!empty($id) ? $this->result['plrefno'] : $this->safeDisplay['plrefnoID']).'">';
				echo '<span id="register_plrefnoID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-1"></div>';

            echo '<div class="col-xs-2">';
				echo '<label for="section">Damage Value</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control numeric" style="text-align:center;" name="dmvalue" id="dmvalue" placeholder="Enter Damage Value" value="'.(!empty($id) ? $this->result['dmvalue'] : $this->safeDisplay('dmvalue')).'">';
				echo '<span id="register_dmvalue_errorloc" class="errors"></span>';
            echo '</div>';		

            echo '<div class="col-xs-3">';
				echo '<label for="section">Weapons</label>';
				echo '<select name="weaponsID" onchange="changes=true;" class="form-control" id="weaponsID">';
				echo '<option value="0" selected="selected"> --- Select Weapons --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['weaponsID'] : $this->safeDisplay['weaponsID']),'24');
				echo '</select>';
            echo '</div>';			
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-6">';
				echo '<label for="section">Location</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="location" name="location" placeholder="Enter Location" value="'.(!empty($id) ? $this->result['location'] : $this->safeDisplay('location')).'">';
				echo '<span id="register_location_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-3">';
				echo '<label for="section">Suburb</label>';
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="suburb" name="suburb">';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select Suburb --</option>';
					echo $this->GET_SubUrbs((!empty($id) ? $this->result['suburb'] : $this->safeDisplay['suburb']),'');
				echo '</select>';
				echo '<span id="register_suburb_errorloc" class="errors"></span>';
            echo '</div>';		

            echo '<div class="col-xs-3 '.$gridID.'">';
				echo '<label for="section">Cross Street</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="crossst" name="crossst" placeholder="Enter Cross Street" value="'.(!empty($id) ? $this->result['crossst'] : $this->safeDisplay('crossst')).'">';
				echo '<span id="register_crossst_errorloc" class="errors"></span>';
            echo '</div>';		
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-3">';
				echo '<label for="section">Reported By</label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="reportby" name="reportby" placeholder="Enter Reported By" value="'.(!empty($id) ? $this->result['reportby'] : $this->safeDisplay('reportby')).'">';
				echo '<span id="register_reportby_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-3">';
				echo '<label for="section">Incident Type</label>';
				echo '<select name="inctypeID" onchange="changes=true;" class="form-control" id="inctypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['inctypeID'] : $this->safeDisplay['inctypeID']),'4');
				echo '</select>';
				echo '<span id="register_inctypeID_errorloc" class="errors"></span>';
            echo '</div>';
			
		echo '<div class="col-xs-4"><label for="section" style="color: red;font-size: 13px;font-weight: bold;font-family: Georgia;" id="checkbox_groupID"></label></div>';
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-6">';
			echo '<label for="section">Customer / Witness Details</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="cwdetails" name="cwdetails" placeholder="Enter Customer / Witness Details" value="'.(!empty($id) ? $this->result['cwdetails'] : $this->safeDisplay('cwdetails')).'">';
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
		
		echo '<div class="col-xs-2">';			
			echo '<label for="section">Investigated Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="invDate" name="invDate" placeholder="Enter Investigated Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['invDate']) : '').'">';
			echo '<span id="register_invDate_errorloc" class="errors"></span>';
		echo '</div>'; 
    echo '</div>';	

    echo '<div class="row '.$gridID.'">';
            echo '<div class="col-xs-6">';
                    echo '<label for="section">Damage/Injury</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="dmginjury" name="dmginjury" placeholder="Enter Damage/Injury" value="'.(!empty($id) ? $this->result['dmginjury'] : $this->safeDisplay('dmginjury')).'">';
					echo '<span id="register_dmginjury_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-3 '.$gridID.'">';
				echo '<label for="section">Offence Type</label>';
				echo '<select name="offtypeID" onchange="changes=true;" class="form-control" id="offtypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				$offtypeID = !empty($id) ? $this->result['offtypeID'] : $this->safeDisplay['offtypeID'];
				echo $this->GET_Masters(($offtypeID),'26');
				echo '</select>';
				echo '<span id="register_offtypeID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-3 '.$gridID.'">';
                    echo '<label for="section">Offence Details <span class="Maindaitory">*</span></label>';
                    echo '<select name="offdtlsID" onchange="changes=true;" class="form-control" id="offdtlsID">';
					echo '<option value="0" selected="selected"> --- Select --- </option>';
                            $offdtlsID = !empty($id) ? $this->result['offdtlsID'] : $this->safeDisplay['offdtlsID'];
                            if(!empty($offdtlsID) && ($offdtlsID > 0))
                            {
                                    $Qry = $this->DB->prepare("SELECT * FROM offence WHERE ID > 0 AND typeID = ".$this->result['offtypeID']." Order By title ASC");
                                    $Qry->execute();
                                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    $crtID = '';
                                    foreach($this->rows as $rows)
                                    {
                                            $crtID = ($rows['ID'] == $offdtlsID ? 'selected="selected"' : '');
                                            echo '<option '.$crtID.' value="'.$rows['ID'].'">'.$rows['title'].'</option>';
                                    }
                            }
                    echo '</select>';
                    echo '<span id="register_offdtlsID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';

    echo '<div class="row '.$gridID.'">'; 
            echo '<div class="col-xs-3">';
                    echo '<label for="section">Graffiti Colour</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" name="grfcolour" id="grfcolour" placeholder="Enter Graffiti Colour" '.($offtypeID == 144 ? '' : 'readonly="readonly"').' value="'.(!empty($id) ? $this->result['grfcolour'] : $this->safeDisplay('grfcolour')).'">';
					echo '<span id="register_grfcolour_errorloc" class="errors"></span>';
            echo '</div>';		

            echo '<div class="col-xs-3">';
                    echo '<label for="section">Graffiti Item</label>';
                    echo '<select name="grfitemID" onchange="changes=true;" class="form-control" id="grfitemID" '.($offtypeID == 144 ? '' : 'disabled="disabled"').'>';
					echo '<option value="0" selected="selected"> --- Select Graffiti Item --- </option>';
					echo $this->GET_Masters((!empty($id) ? $this->result['grfitemID'] : $this->safeDisplay['grfitemID']),'2');
                    echo '</select>';
					echo '<span id="register_grfitemID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-6">';
				echo '<label for="section">What has been written</label>';
				echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="whbwdescID"  '.($offtypeID == 144 ? '' : 'readonly="readonly"').' id="whbwdescID"  placeholder="Enter What has been written">'.(!empty($id) ? $this->result['whbwdescription'] : $this->safeDisplay['whbwdescID']).'</textarea>';
				echo '<span id="register_whbwdescID_errorloc" class="errors"></span>';
            echo '</div>'; 
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-6">';
			echo '<label for="section">Description <span class="Maindaitory">*</span></label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" placeholder="Enter Description">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-6">';
			echo '<label for="section">Action</label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" id="action" name="action" placeholder="Enter Action">'.(!empty($id) ? $this->result['action'] : $this->safeDisplay['action']).'</textarea>';
			echo '<span id="register_action_errorloc" class="errors"></span>';
		echo '</div>';  

		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
	
    echo '<div class="row '.$gridID.'">';
            echo '<div class="col-xs-6">';
                    echo '<label for="section">Depot Notes</label>';
                    echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="depotnotes" 
                    placeholder="Enter Depot Notes">'.(!empty($id) ? $this->result['depotnotes'] : $this->safeDisplay['depotnotes']).'</textarea>';
            echo '</div>';  
    echo '</div>';	/* HERE BREAK */

	echo '<div class="breakPOINT"></div>';
	
    echo '<div class="row '.$gridID.'">';
		/*echo '<div class="col-xs-6">';
				echo '<label for="section">Description of Damage</label>';
				echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="dscdamage" 
				placeholder="Enter Description of Damage">'.(!empty($id) ? $this->result['dscdamage'] : $this->safeDisplay['dscdamage']).'</textarea>';
		echo '</div>';*/
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Police '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_2" id="attendedID_2" value="1" '.($attendedID_2 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Police CAD No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="plcadno" id="plcadno" '.($attendedID_2 == 1 ? '' : 'readonly="readonly"').' placeholder="Enter Police CAD No" value="'.(!empty($id) ? $this->result['plcadno'] : $this->safeDisplay('plcadno')).'">';
			echo '<span id="register_plcadno_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Police Vehicle</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="plcvehicle" id="plcvehicle" '.($attendedID_2 == 1 ? '' : 'readonly="readonly"').' placeholder="Enter Police Vehicle" value="'.(!empty($id) ? $this->result['plcvehicle'] : $this->safeDisplay('plcvehicle')).'">';
			echo '<span id="register_plcvehicle_errorloc" class="errors"></span>';
		echo '</div>';

		echo '<div class="col-xs-3">';
			echo '<label for="section">Police Name</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" name="policename" id="policename" placeholder="Enter Police Name" '.($attendedID_2 == 1 ? '' : 'readonly="readonly"').' value="'.(!empty($id) ? $this->result['policename'] : $this->safeDisplay('policename')).'">';
			echo '<span id="register_policename_errorloc" class="errors"></span>';
		echo '</div>';		

		echo '<div class="col-xs-3">';
			echo '<label for="section">Police Action</label>';
			echo '<select name="plcactionID" onchange="changes=true;" class="form-control" id="plcactionID" '.($attendedID_2 == 1 ? '' : 'disabled="disabled"').'>';
			echo '<option value="0" selected="selected"> --- Select Police Action --- </option>';
			echo $this->GET_Masters((!empty($id) ? $this->result['plcactionID'] : $this->safeDisplay['plcactionID']),'25');
			echo '</select>';
			echo '<span id="register_plcactionID_errorloc" class="errors"></span>';
		echo '</div>';
    echo '</div>';	/* HERE BREAK */

	echo '<div class="breakPOINT"></div>';
	
    echo '<div class="row '.$gridID.'">';
		echo '<div class="col-xs-12">';
			echo '<h3 class="knob-labels notices" style="margin-top:-5px !important; font-weight:600; font-size:14px; text-align:left;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NOTIFIED / ATTENDED :</h3><br />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Radio '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_1" id="radioN" value="1" '.($notifiedID_1 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Radio '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_1" id="radioA" value="1" '.($attendedID_1 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Transperth Security '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_3" id="transN" value="1" '.($notifiedID_3 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Transperth Security '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_3" id="transA" value="1" '.($attendedID_3 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Fire Brigade '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_8" id="firebN" value="1" '.($attendedID_8 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Ambulance '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_9" id="ambulN" value="1" '.($attendedID_9 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
    echo '</div>';	/* HERE BREAK */

	echo '<div class="breakPOINT"></div>';

    echo '<div class="row '.$gridID.'">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Duty Ops '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_4" id="dutyoN" value="1" '.($notifiedID_4 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Duty Ops '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_4" id="dutyoA" value="1" '.($attendedID_4 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Depot Manager '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_5" id="depotN" value="1" '.($notifiedID_5 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Depot Manager '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_5" id="depotA" value="1" '.($attendedID_5 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">PTA '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_6" id="ptaopN" value="1" '.($notifiedID_6 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">PTA '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_6" id="ptaopA" value="1" '.($attendedID_6 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
    echo '</div>';	/* HERE BREAK */

	echo '<div class="breakPOINT"></div>';
	
    echo '<div class="row '.$gridID.'">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Westrail '.$classID_1.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_7" id="westrN" value="1" '.($notifiedID_7 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Westrail '.$classID_2.'</label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="attendedID_7" id="westrA" value="1" '.($attendedID_7 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-3">';
			echo '<label for="section">Video Footage Available </label><br />';
			echo '<input class="icheckbox_minimal checked" type="checkbox" name="notifiedID_8" id="vdeofA" value="1" '.($notifiedID_8 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';

		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
	
	echo '<div class="row">';	
		echo '<div class="col-xs-2">';
			echo '<label for="section">Discipline Required</label>';
			echo '<select name="cmdiscID" onchange="changes=true;" class="form-control" id="cmdiscID">';
				$disciplineID = (!empty($id) ? ($this->result['disciplineID']) : $this->safeDisplay('disciplineID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($disciplineID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($disciplineID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_cmdiscID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
		echo '<label for="section">Interviewed By</label>';
				$actbyID = (!empty($id) ? $this->result['actbyID'] : $this->safeDisplay['actbyID']);
				$arrayINV = $actbyID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$actbyID." ") : '';
			echo '<select name="actbyID" onchange="changes=true;" class="form-control select2" style="width: 100%;" id="actbyID">';
			echo '<option value="0" selected="selected"> --- Select Interviewed By --- </option>';
				$actbyID = !empty($id) ? $this->result['actbyID'] : $this->safeDisplay['actbyID'];
				echo $this->ReportingBundels($actbyID," AND desigID In (208,209,8) ");
			echo '</select>';
			echo '<span id="register_actbyID_errorloc" class="errors"></span>';
		echo '</div>';
			
		echo '<div class="col-xs-2">';			
			echo '<label for="section">Interviewed Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="actbyDate" name="actbyDate" placeholder="Enter Interviewed Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['actbyDate']) : '').'">';
			echo '<span id="register_actbyDate_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Closed</label>';
			echo '<select name="INCstatusID" onchange="changes=true;" class="form-control" id="INCstatusID">';
				$statusID = (!empty($id) ? ($this->result['statusID']) : $this->safeDisplay('INCstatusID'));
				$statusID = $statusID > 0 ? $statusID  : '2';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_INCstatusID_errorloc" class="errors"></span>';
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

	echo '<div class="col-xs-1"></div>';

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
			echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" id="incidentSubmit" type="submit">'.(!empty($id) ? 'Update Incident Register' : 'Save Incident Register').'</button>';
      echo '</div>';

    echo '<div class="col-xs-2">';
    echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
    echo '</div>';

    echo '</div>';
	
	echo '<div id="IncidentValidGridID"></div>';
	
    echo '</div>';
    echo '</form>';
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
			extract($_POST);			 //echo '<PRE>'; echo print_r($_POST); exit;

			if($increfno == '') 	   $errors .= "Enter The Incident Ref No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Incident Date.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}			
			else
			{
				$_POST['dateID']    = $this->dateFormat($_POST['dateID']);
				$_POST['invDate']   = $this->dateFormat($_POST['invDate']);
				$_POST['actbyDate'] = $this->dateFormat($_POST['actbyDate']);
				$_POST['rpdateID']  = $this->dateFormat($_POST['rpdateID']);
				$_POST['userID']    = $_SESSION[$this->website]['userID'];
				$_POST['companyID'] = $this->companyID;
				$_POST['statusID']  = $_POST['INCstatusID'];
				$_POST['plrefno']   = $_POST['plrefnoID'];			
				$_POST['pta_refNO'] = $_POST['ptarefNO'];
				$_POST['refno']  	= $_POST['increfno'];
				$_POST['cmrno']     = $_POST['inccmrno'];

				$_POST['brs_statusID'] = $brs_statusID > 0 ? $brs_statusID : 0;
				$_POST['inc_statusID'] = $inc_statusID > 0 ? $inc_statusID : 0;							
				$_POST['plrefID'] 	   = $plrefID > 0 	   ? $plrefID 	   : 0;
				$_POST['notifiedID_1'] = $notifiedID_1 > 0 ? $notifiedID_1 : 0;
				$_POST['notifiedID_2'] = $notifiedID_2 > 0 ? $notifiedID_2 : 0;
				$_POST['notifiedID_3'] = $notifiedID_3 > 0 ? $notifiedID_3 : 0;
				$_POST['notifiedID_4'] = $notifiedID_4 > 0 ? $notifiedID_4 : 0;
				$_POST['notifiedID_5'] = $notifiedID_5 > 0 ? $notifiedID_5 : 0;
				$_POST['notifiedID_6'] = $notifiedID_6 > 0 ? $notifiedID_6 : 0;
				$_POST['notifiedID_7'] = $notifiedID_7 > 0 ? $notifiedID_7 : 0;
				$_POST['notifiedID_8'] = $notifiedID_8 > 0 ? $notifiedID_8 : 0;							
				$_POST['attendedID_1'] = $attendedID_1 > 0 ? $attendedID_1 : 0;
				$_POST['attendedID_2'] = $attendedID_2 > 0 ? $attendedID_2 : 0;
				$_POST['attendedID_3'] = $attendedID_3 > 0 ? $attendedID_3 : 0;
				$_POST['attendedID_4'] = $attendedID_4 > 0 ? $attendedID_4 : 0;
				$_POST['attendedID_5'] = $attendedID_5 > 0 ? $attendedID_5 : 0;
				$_POST['attendedID_6'] = $attendedID_6 > 0 ? $attendedID_6 : 0;
				$_POST['attendedID_7'] = $attendedID_7 > 0 ? $attendedID_7 : 0;
				$_POST['attendedID_8'] = $attendedID_8 > 0 ? $attendedID_8 : 0;
				$_POST['attendedID_9'] = $attendedID_9 > 0 ? $attendedID_9 : 0;
				$_POST['disciplineID'] = $_POST['cmdiscID'];							
				$_POST['driverID'] = $_POST['empID'];
				$_POST['whbwdescription'] = $_POST['whbwdescID'];
				
				unset($_POST['increfno'],$_POST['inccmrno']);
				unset($_POST['Submit'],$_POST['whbwdescID'],$_POST['empID'],$_POST['ptarefNO'],$_POST['cmdiscID'],$_POST['code'],$_POST['offenceID'],$_POST['plrefnoID'],$_POST['INCstatusID']);

				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['offenceID'] = implode(",",$offenceID);
				$array['systemID']  = $this->get_systemID($_POST['driverID']);
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;
				//echo '<PRE>'; echo print_r($_POST); exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					/*RUN API - URL */
					/*$this->CurlService($lastID[0]);*/
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['driverID'],$array['dcodeID'],$array['refno'],'A',$array['description'],$array);
					
					$this->msg = urlencode(' Incident Register Is Created (s) Successfully . <br /> Incident Ref No : '.$array['refno']);
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

			if($increfno == '') 	   $errors .= "Enter The Incident Ref No.<br>";
			if($dateID == '') 	 	   $errors .= "Enter The Incident Date.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 
				$_POST['rpdateID']  = $this->dateFormat($_POST['rpdateID']);
				$_POST['dateID']    = $this->dateFormat($_POST['dateID']);
				$_POST['invDate']   = $this->dateFormat($_POST['invDate']);
				$_POST['actbyDate'] = $this->dateFormat($_POST['actbyDate']);
				$_POST['plrefno']   = $_POST['plrefnoID'];
				$_POST['statusID']  = $_POST['INCstatusID'];
				$_POST['pta_refNO'] = $_POST['ptarefNO'];
				$_POST['refno']  	= $_POST['increfno'];
				$_POST['cmrno']     = $_POST['inccmrno'];
				
				$_POST['brs_statusID'] = $brs_statusID > 0 ? $brs_statusID : 0;
				$_POST['inc_statusID'] = $inc_statusID > 0 ? $inc_statusID : 0;							
				$_POST['plrefID'] 	   = $plrefID > 0 	   ? $plrefID 	   : 0;
				$_POST['notifiedID_1'] = $notifiedID_1 > 0 ? $notifiedID_1 : 0;
				$_POST['notifiedID_2'] = $notifiedID_2 > 0 ? $notifiedID_2 : 0;
				$_POST['notifiedID_3'] = $notifiedID_3 > 0 ? $notifiedID_3 : 0;
				$_POST['notifiedID_4'] = $notifiedID_4 > 0 ? $notifiedID_4 : 0;
				$_POST['notifiedID_5'] = $notifiedID_5 > 0 ? $notifiedID_5 : 0;
				$_POST['notifiedID_6'] = $notifiedID_6 > 0 ? $notifiedID_6 : 0;
				$_POST['notifiedID_7'] = $notifiedID_7 > 0 ? $notifiedID_7 : 0;
				$_POST['notifiedID_8'] = $notifiedID_8 > 0 ? $notifiedID_8 : 0;							
				$_POST['attendedID_1'] = $attendedID_1 > 0 ? $attendedID_1 : 0;
				$_POST['attendedID_2'] = $attendedID_2 > 0 ? $attendedID_2 : 0;
				$_POST['attendedID_3'] = $attendedID_3 > 0 ? $attendedID_3 : 0;
				$_POST['attendedID_4'] = $attendedID_4 > 0 ? $attendedID_4 : 0;
				$_POST['attendedID_5'] = $attendedID_5 > 0 ? $attendedID_5 : 0;
				$_POST['attendedID_6'] = $attendedID_6 > 0 ? $attendedID_6 : 0;
				$_POST['attendedID_7'] = $attendedID_7 > 0 ? $attendedID_7 : 0;
				$_POST['attendedID_8'] = $attendedID_8 > 0 ? $attendedID_8 : 0;
				$_POST['attendedID_9'] = $attendedID_9 > 0 ? $attendedID_9 : 0;
				$_POST['disciplineID'] = $_POST['cmdiscID'];					
				$_POST['whbwdescription'] = $_POST['whbwdescID'];
				$_POST['driverID'] = $_POST['empID'];
				
				unset($_POST['increfno'],$_POST['inccmrno']);
				unset($_POST['Submit'],$_POST['whbwdescID'],$_POST['empID'],$_POST['ptarefNO'],$_POST['cmdiscID'],$_POST['code'],$_POST['ID'],$_POST['offenceID'],$_POST['plrefnoID'],$_POST['INCstatusID']);

				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($_POST['driverID']);
				$array['offenceID'] = implode(",",$offenceID);
				$on['ID'] = $ID;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					/*RUN API - URL */
					//$this->CurlService($ID);
					
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['driverID'],$array['dcodeID'],$array['refno'],'E',$array['description'],$array);
					
					$this->msg = urlencode(' Incident Register Is Updated (s) Successfully . <br /> Incident Ref No : '.$array['refno']);
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