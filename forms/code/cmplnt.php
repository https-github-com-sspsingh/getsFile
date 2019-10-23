<?PHP
class Masters extends SFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     =	basename($_SERVER['PHP_SELF']);		
        $this->tableName    =   'complaint';
        $this->companyID	=   $_SESSION[$this->website]['compID'];
        $this->frmID	    =   '40';
		$this->cldaysID	    =   $_SESSION[$this->website]['cdysID'];
        $this->permissions  =   $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($fd,$td,$searchbyID,$auditID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $str = "";
            if($auditID <> '')
            {
				$str .= " AND complaint.ID In(".$auditID.") ";
            }
            else
            {
                if(!empty($fd) || !empty($td))
                {
                    list($fdt,$fm,$fy)  =   explode("/",$fd);
                    list($tdt,$tm,$ty)  =   explode("/",$td);

                    $fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
                    $td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
                }

                /* DATE - SEARCHING */
                if($fd <> '' && $td <> '' )     $str .= " AND DATE(serDT) BETWEEN '".$fd."' AND '".$td."' ".$src;
                elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
                else                            $str .= " AND statusID = 2 ";

                /* SEARCH BY  -  OPTIONS */
                $src = "";
                $tsystemID  = $this->filter_employee_systemID($searchbyID);

                if($tsystemID <> '')    {$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";}
                else
                {
                    $retID = $this->CheckIntOrStrings($searchbyID);
                    $src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".refno LIKE '%".$searchbyID."%'" : "");
                    $src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
                }
            }
			
            $SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".driverID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
                echo '<th>Ref No</th>';
                echo '<th>Comment Reported On</th>';
                echo '<th>Driver Name</th>';
                echo '<th>Due Date</th>';
                echo '<th>Comment Type</th>'; 
                echo '<th>Fault/Not at Fault</th>';
                echo '<th>Comment Line Reason</th>';
                echo '<th>TRIS Status</th>';
                echo '<th width="350">Description</th>';			
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
                echo '</tr></thead>';
				$dueDate = '';  $daysID = 0;	$colorID = '';
                foreach($this->rows as $row)			
                { 
                    $daysID  = ((strtotime(date('Y-m-d', strtotime($row['cmdueDT']))) - strtotime(date('Y-m-d'))) / 86400);
					$colorID = $daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green');
					
					$arrEM  = $row['driverID'] > 0  ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['driverID']." ") : '';
					$arrCR  = $row['creasonID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['creasonID']." ") : '';
					$arrCT  = $row['accID'] > 0 	? $this->select('master',array("title"), " WHERE ID = ".$row['accID']." ") : '';
					
					echo '<tr>'; 
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['serDT']).'</td>';				
					echo '<td>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : ($arrEM[0]['fname'].' '.$arrEM[0]['lname'].' <br /> ('.$arrEM[0]['code'].')')).'</td>';
					echo '<td align="center" style="color:'.$colorID.'; font-weight:bold;">'.$this->VdateFormat($row['cmdueDT']).'</td>';
					echo '<td>'.$arrCT[0]['title'].'</td>';

					echo '<td align="center">'.($row['substanID'] == 1 && $row['faultID'] == 1 ? 'At Fault - Driver' :($row['substanID'] == 1 && $row['faultID'] == 2 ? 'At Fault - Engineering' :($row['substanID'] == 1 && $row['faultID'] == 3 ? 'At Fault - Operations' :($row['substanID'] == 1 && $row['faultID'] == 4 ? 'Not At Fault' :($row['substanID'] == 2 && $row['faultID'] == 4 ? 'Not Applicable' :($row['substanID'] == 2 && $row['faultID'] == 5 ? 'Not At Fault' : '')))))).'</td>';
					echo '<td>'.$arrCR[0]['title'].'</td>';
					echo '<td align="center"><b style="color:'.($row['trisID'] == 1 ? 'green' : 'red').'">'.($row['trisID'] == 1 ? 'Complete' : 'Pending').'</b></td>';
					echo '<td>'.($row['description']).'</td>';
					
					if(($row['companyID'] == $_SESSION[$this->website]['compID']) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
					{
						echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';
					}
					else    {echo '<td></td>';}

										
					if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if($row['tsystemID'] > 0 || ((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days'))))	{echo '<td></td>';}	else
						{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					}

					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{ 
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
						
                    echo '</tr>';
                }
                echo '</table>';			
            }
        }
        else	{echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>this</b> Page</div></div>';}
    } 
	
    public function filter_view($fd,$td,$searchbyID,$passSTR)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $str = "";
            if(!empty($fd) || !empty($td))
            {
                list($fdt,$fm,$fy)  =   explode("/",$fd);
                list($tdt,$tm,$ty)  =   explode("/",$td);

                $fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
                $td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
            }

            /* DATE - SEARCHING */
            if($fd <> '' && $td <> '' )     $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
            elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
            else                            $str .= " AND statusID = 2 ";

            /* SEARCH BY  -  OPTIONS */
            $src = "";
            $tsystemID  = $this->filter_employee_systemID($searchbyID);
			
			if($tsystemID <> '')    {$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";}
            else
            {
                $retID = $this->CheckIntOrStrings($searchbyID);
                $src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".refno LIKE '%".$searchbyID."%'" : "");
                $src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
            }
			
            $SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".driverID WHERE ".$this->tableName.".ID > 0 AND ".$this->tableName.".trisID <= 0 ".$str." ".$src." ORDER BY ".$this->tableName.".serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
                echo '<th>Ref No</th>';
                echo '<th>Comment Reported On</th>';
                echo '<th>Driver Name</th>';
                echo '<th>Due Date</th>';
                echo '<th>Comment Type</th>'; 
                echo '<th>Fault/Not at Fault</th>';
                echo '<th>Comment Line Reason</th>';
                echo '<th>Tris Status</th>';
                echo '<th width="350">Description</th>';			
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P-Log</th>' : '');
                echo '</tr></thead>';
				$dueDate = '';  $daysID = 0; $drnameID = '';		$colorID = '';
                foreach($this->rows as $row)			
                {  
                    $daysID  = ((strtotime(date('Y-m-d', strtotime($row['cmdueDT']))) - strtotime(date('Y-m-d'))) / 86400);
					$colorID = $daysID < 1 ? 'red' :($daysID <= 2 ? 'orange' : 'green');
					
					$arrEM  = $row['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$row['driverID']." ") : '';
					$arrCR  = $row['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$row['creasonID']." ") : '';
					$arrCT  = $row['accID'] > 0 	 ? $this->select('master',array("*"), " WHERE ID = ".$row['accID']." ") : '';
					$drnameID = $arrEM[0]['fname'].' '.$arrEM[0]['lname'].' <br /> ('.$arrEM[0]['code'].')';
						
					if(in_array($passSTR,(array($colorID))))
					{
						echo '<tr>'; 
						echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;">'.$row['refno'].'</a></td>';
						echo '<td align="center">'.$this->VdateFormat($row['serDT']).'</td>';				
						echo '<td>'.($row['tickID_1'] == 1 ? '<b>Driver Not Applicable<b/>' : $drnameID).'</td>';
						echo '<td align="center" style="color:'.$colorID.'; font-weight:bold;">'.$this->VdateFormat($row['cmdueDT']).'</td>';
						echo '<td>'.$arrCT[0]['title'].'</td>';

						echo '<td align="center">'.($row['substanID'] == 1 && $row['faultID'] == 1 ? 'At Fault - Driver' :($row['substanID'] == 1 && $row['faultID'] == 2 ? 'At Fault - Engineering' :($row['substanID'] == 1 && $row['faultID'] == 3 ? 'At Fault - Operations' :($row['substanID'] == 1 && $row['faultID'] == 4 ? 'Not At Fault' :($row['substanID'] == 2 && $row['faultID'] == 4 ? 'Not Applicable' :($row['substanID'] == 2 && $row['faultID'] == 5 ? 'Not At Fault' : '')))))).'</td>';
						echo '<td>'.$arrCR[0]['title'].'</td>';
						echo '<td><b>'.($row['trisID'] == 1 ? 'Complete' : 'Pending').'</b></td>';
						//echo '<td>'.$this->Word_Wraping($row['description'],95).'</td>';					
						echo '<td>'.($row['description']).'</td>';
						
						if(($row['companyID'] == $_SESSION[$this->website]['compID']) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
						{
							echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';
						}
						else    {echo '<td></td>';}
						
						if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							if($row['tsystemID'] > 0)	{echo '<td></td>';}	else
							{echo '<td align="center"><a data-title="'.$this->tableName.'" data-title="'.$this->frmID.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
						}

						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{ 
							if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
							}
							else	{echo '<td></td>';}
						}
						
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Comment Line Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
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
		
    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body complaint_forms" id="fg_membersite">';
	
    echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
            echo '<label for="section" style="font-size: 13px;">Customer feedback type <span class="Maindaitory">*</span></label>';
            echo '<select onchange="changes=true;" class="form-control" id="cmltypeID" name="cmltypeID">';
            echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				$cmltypeID = (!empty($id) ? $this->result['cmltypeID'] : $this->safeDisplay['cmltypeID']);
                echo $this->GET_Masters((!empty($id) ? $this->result['cmltypeID'] : $this->safeDisplay['cmltypeID']),'135');
            echo '</select>';
			echo '<span id="register_cmltypeID_errorloc" class="errors"></span>';
        echo '</div>';
		
        echo '<div class="col-xs-2">';
            echo '<label for="section">Comment Line Ref No <span class="Maindaitory">*</span></label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" name="cmprefno" id="cmprefno" '.($cmltypeID == 491 || $cmltypeID == 492 ? '' : 'readonly="readonly"').' placeholder="Comment Line Ref No" style="text-align:center;" required="required" value="'.(!empty($id) ? ($this->result['refno']) : $this->safeDisplay['cmprefno']).'">';
			echo '<span id="register_cmprefno_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">Comment ReportedOn <span class="Maindaitory">*</span></label>';
            echo '<input type="datable" onchange="changes=true;" class="form-control datepicker cmp_report_date" data-datable="ddmmyyyy" id="serDT" name="serDT" placeholder="Comment Reported On" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['serDT']) : '').'">';
			echo '<span id="register_serDT_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">Incident Date <span class="Maindaitory">*</span></label>';
            echo '<input type="datable" onchange="changes=true;" class="form-control datepicker cmp_incident_date" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Date" required="required" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
        echo '</div>'; 

        echo '<div class="col-xs-2">';
        echo '<label for="section">Incident Time </label>';
            echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID" name="timeID" placeholder="hh:mm" style="text-align:center; cursor:no-drop;" onkeypress="javascript:return false;" value="'.(!empty($id) ? $this->result['timeID'] : $this->safeDisplay['timeID']).'">';
            echo '<span id="register_timeID_errorloc" class="errors"></span>';
        echo '</div>';
		
        echo '<div class="col-xs-2">';
        echo '<label for="section">Due Date <span class="Maindaitory">*</span></label>';
            echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="cmdueDT" name="cmdueDT" placeholder="Enter Date" required="required" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['cmdueDT']) : '').'">';
            echo '<span id="register_cmdueDT_errorloc" class="errors"></span>';
        echo '</div>';		
    echo '</div>';

    echo '<div class="row">';
        echo '<div class="col-xs-2">';
            echo '<label for="section">Bus No </label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="busID" name="busID" placeholder="Enter Bus No" style="text-align:center;" value="'.(!empty($id) ? $this->result['busID'] : $this->safeDisplay('busID')).'">';
			echo '<span id="register_busID_errorloc" class="errors"></span>';
        echo '</div>'; 

        echo '<div class="col-xs-2">';
            echo '<label for="section">Service No </label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="routeID" name="routeID" placeholder="Enter Service No" style="text-align:center;" value="'.(!empty($id) ? $this->result['routeID'] : $this->safeDisplay('routeID')).'">';
            echo '<span id="register_routeID_errorloc" class="errors"></span>';
        echo '</div>'; 

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
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
	
	echo '<div class="row">'; 
        echo '<div class="col-xs-4">';
            echo '<label for="section">Customer\'s Name</label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="cmp_name" name="cmp_name" placeholder="Enter Customer\'s Name" value="'.(!empty($id) ? $this->result['cmp_name'] : $this->safeDisplay('cmp_name')).'">';
        echo '</div>';  

        echo '<div class="col-xs-3">';
            echo '<label for="section">Telephone No</label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="mobile" name="mobile" placeholder="Enter Telephone No" value="'.(!empty($id) ? $this->result['mobile'] : $this->safeDisplay('mobile')).'">';
        echo '</div>';  

        echo '<div class="col-xs-5">';
            echo '<label for="section">Customer Email ID</label>';
            echo '<input type="email" onchange="changes=true;" class="form-control" id="cmemailID" name="cmemailID" placeholder="Enter Customer Email ID" value="'.(!empty($id) ? $this->result['cmemailID'] : $this->safeDisplay('cmemailID')).'">';
        echo '</div>';
    echo '</div><br />';

    echo '<div class="row">';
        echo '<div class="col-xs-4">';
            echo '<label for="section">Address</label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="address" name="address" placeholder="Enter Location" value="'.(!empty($id) ? $this->result['address'] : $this->safeDisplay('address')).'">';
        echo '</div>';  

        echo '<div class="col-xs-3">';
            echo '<label for="section">Comment Line Reason</label>'; 
            echo '<select onchange="changes=true;" class="form-control" id="creasonID" name="creasonID">';
            echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                echo $this->GET_Masters((!empty($id) ? $this->result['creasonID'] : $this->safeDisplay['creasonID']),'15');
            echo '</select>';
			echo '<span id="register_creasonID_errorloc" class="errors"></span>';
        echo '</div>';   

		$tickID_1 = (!empty($id) ? $this->result['tickID_1'] : $this->safeDisplay['tickID_1']);
		echo '<div class="col-xs-4">';
				echo '<label for="section">Driver Not Applicable (In Comment Line)</label><br />';
				echo '<input class="icheckbox_minimal checked" type="checkbox" name="tickID_1" id="tickID_1" value="1" '.($tickID_1 == 1 ? 'checked="checked"' : '').' />';
		echo '</div>';
    echo '</div><br />';

    echo '<div class="row">';		
        echo '<div class="col-xs-7">';
            echo '<label for="section">Description</label>';
            echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="description" id="description" placeholder="Enter Description">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-3">';
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
                echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" '.($tickID_1 == 1 ? 'disabled="disabled"' : '').' id="empID" name="empID">';
                echo '<option value="0" selected="selected">-- Select Driver --</option>';                    
                echo $this->GET_Employees11($driverID,"AND status = 1");
                echo '</select>';
                echo '<span id="register_empID_errorloc" class="errors"></span>';
            } 
        echo '</div>';  

        echo '<div class="col-xs-2">';
            echo '<label for="section">Driver ID</label>';
            echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="dcodeID" placeholder="Driver ID" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['dcodeID'] : '').'">';
        echo '</div>';  		
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';
        echo '<div class="col-xs-2">';
            $acctID = (!empty($id) ? $this->result['accID'] : $this->safeDisplay['accID']);
            echo '<label for="section">Comment Line Type</label>';
            echo '<select onchange="changes=true;" class="form-control" id="accID" name="accID">';
                    echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                    echo $this->GET_Masters((!empty($id) ? $this->result['accID'] : $this->safeDisplay['accID']),'17');
            echo '</select>';
			echo '<span id="register_accID_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2 '.(($acctID == 224 || $acctID == 48 || $acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_1"').'>';
            echo '<label for="section">Substantiated</label>';
            echo '<select onchange="changes=true;" class="form-control" id="substanID" name="substanID" '.($this->result['accID'] == 52 ? '' : 'disabled="disabled"').'>';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				$substanID = (!empty($id) ? $this->result['substanID'] : $this->safeDisplay['substanID']);
				echo '<option value="1" '.($substanID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($substanID == 2 ? 'selected="selected"' : '').'>No</option>';
            echo '</select>';
			echo '<span id="register_substanID_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2 '.(($acctID == 224 || $acctID == 48 || $acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_1"').'>';
            echo '<label for="section">Fault/Not at Fault</label>';
            echo '<select onchange="changes=true;" class="form-control" id="faultID" name="faultID" '.($this->result['accID'] == 52 ? '' : 'disabled="disabled"').'>';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                $faultID = (!empty($id) ? $this->result['faultID'] : $this->safeDisplay['faultID']);
                if($this->result['accID'] == 52)
                {
                    //if($substanID == 1 || $substanID == 2)
                    if($substanID == 1 )
                    {
                        echo '<option value="1" '.($faultID == 1 ? 'selected="selected"' : '').'>At Fault - Driver</option>';
                        echo '<option value="2" '.($faultID == 2 ? 'selected="selected"' : '').'>At Fault - Engineering</option>';
                        echo '<option value="3" '.($faultID == 3 ? 'selected="selected"' : '').'>At Fault - Operations</option>';
                        echo '<option value="4" '.($faultID == 4 ? 'selected="selected"' : '').'>Not At Fault</option>';
                    }
                    else if($substanID == 2)
                    {
						echo '<option value="4" '.($faultID == 4 ? 'selected="selected"' : '').'>Not Applicable</option>';
						echo '<option value="5" '.($faultID == 5 ? 'selected="selected"' : '').'>Not At Fault</option>';
                    }
                }
            echo '</select>';
			echo '<span id="register_faultID_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-4 '.(($acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_3"').'>';
            echo '<label for="section">Investigated By</label>';
				$invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']); 
				//'.(($this->result['disciplineID'] == 2 || empty($this->result['disciplineID'])) && $acctID == 48 ? 'disabled="disabled"' : '').'
				echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invID" name="invID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Investigated By --</option>';				
				echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
				echo '</select>'; 
				echo '<span id="register_invID_errorloc" class="errors"></span>';
        echo '</div>';
		
        echo '<div class="col-xs-2 '.(($acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_3"').'>';
			//(($this->result['disciplineID'] == 2 || empty($this->result['disciplineID'])) && $acctID == 48 ? 'readonly="readonly"' : '')
            echo '<label for="section">Investigated Date</label>';
            echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="invdate" name="invdate" '.($this->result['invID'] == 1001 ? 'readonly="readonly"' : '').' placeholder="Enter Investigated Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['invdate']) : '').'">';
			echo '<span id="register_invdate_errorloc" class="errors"></span>';
        echo '</div>'; 
    echo '</div><br />';

    echo '<div class="row">';
        echo '<div class="col-xs-6">';
            echo '<label for="section">Customer Response Details</label>';
            echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="furaction" id="furaction" placeholder="Enter Customer Response Details">'.(!empty($id) ? $this->result['furaction'] : $this->safeDisplay['furaction']).'</textarea>';
			echo '<span id="register_furaction_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-4">';
            echo '<label for="section">Response Method</label>';
            echo '<select onchange="changes=true;" class="form-control" id="respID" name="respID">';
                echo '<option value="0" selected="selected">-- Select --</option>';
				$respID = !empty($id) ? $this->result['respID'] : $this->safeDisplay['respID'];
                echo $this->GET_Masters($respID,'14');
            echo '</select>';
			echo '<span id="register_respID_errorloc" class="errors"></span>';
        echo '</div>';

		echo '<div class="col-xs-2">';
            echo '<label for="section">Response Date</label>';
            echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" '.($respID <= 0 || $respID == 46 ? 'readonly="readonly"' : '').' id="resdate" name="resdate" placeholder="Enter Response Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['resdate']) : '').'">';
			echo '<span id="register_resdate_errorloc" class="errors"></span>';
        echo '</div>'; 			
    echo '</div><br />';

    echo '<div class="row">';			
        echo '<div class="col-xs-6">';
            echo '<label for="section">Action Taken / Recommendations</label>';
            echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="outcome" id="outcome" placeholder="Enter Action Taken">'.(!empty($id) ? $this->result['outcome'] : $this->safeDisplay['outcome']).'</textarea>';
			echo '<span id="register_outcome_errorloc" class="errors"></span>';
        echo '</div>';

        $trisID = (!empty($id) ? $this->result['trisID'] : $this->safeDisplay['trisID']);
        echo '<div class="col-xs-2">';
            echo '<label for="section" style="color:blue;">Responded to PTA</label><br />';
            echo '<input class="icheckbox_minimal checked" type="checkbox" name="trisID" id="trisID" value="1" '.($trisID == 1 ? 'checked="checked"' : '').' />';
            echo '<span id="register_trisID_errorloc" class="errors"></span>';
        echo '</div>';	
 
        echo '<div class="col-xs-2'.(($acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54 || $acctID == 224) ? '" style="display:none;"' : ' partID_4"').'">';
            echo '<label for="section">Discipline Required</label>';
            echo '<select name="cmdiscID" onchange="changes=true;" class="form-control" id="cmdiscID" >';
            $disciplineID = (!empty($id) ? ($this->result['disciplineID']) : $this->safeDisplay('disciplineID'));
			$disciplineID = $disciplineID > 0 ? $disciplineID :($acctID == 224 ? 1 : $disciplineID);
            echo '<option value="0" selected="selected"> --- Select --- </option>';
            echo '<option value="1" '.($disciplineID == 1 ? 'selected="selected"' : '').'>Yes</option>';
            echo '<option value="2" '.($disciplineID == 2 ? 'selected="selected"' : '').'>No</option>';
            echo '</select>';
			echo '<span id="register_cmdiscID_errorloc" class="errors"></span>';
        echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Closed</label>';
			echo '<select name="CMPLstatusID" onchange="changes=true;" class="form-control" id="CMPLstatusID">';
				$statusID = (!empty($id) ? ($this->result['statusID']) : $this->safeDisplay('CMPLstatusID'));
				$statusID = $statusID > 0 ? $statusID  : '2';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Yes</option>';
				echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>No</option>';
			echo '</select>';
			echo '<span id="register_CMPLstatusID_errorloc" class="errors"></span>';
		echo '</div>';
		
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row '.(($acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_3"').'>';		
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

        echo '<div class="col-xs-2"></div>';
    } 
    echo '</div>';
	
    echo '<div class="row '.(($acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? '" style="display:none;"' : ' partID_3"').'>';		
    if($this->GET_SinglePermission('1') == 1)
    {
        echo '<div class="col-xs-8">';
            echo '<label for="section">Manager Comments</label>';
            echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="mcomments" id="mcomments" '.($disciplineID == 1 ? '' : 'disabled="disabled"').' placeholder="Enter Manager Comments">'.(!empty($id) ? $this->result['mcomments'] : $this->safeDisplay['mcomments']).'</textarea>';
			echo '<span id="register_mcomments_errorloc" class="errors"></span>';
        echo '</div>';
    }

    if($this->GET_SinglePermission('2') == 1)
    {
        echo '<div class="col-xs-4">';
            echo '<label for="section">Warning Type</label>';
            echo '<select name="wrtypeID" onchange="changes=true;" class="form-control" id="wrtypeID" '.($disciplineID == 1 && $acctID <> 224 ? '' : 'disabled="disabled"').'>';
                echo '<option value="0" selected="selected"> --- Select --- </option>';
				$wrtypeID = (!empty($id) ? $this->result['wrtypeID'] : $this->safeDisplay['wrtypeID']);
				$wrtypeID = ($wrtypeID > 0 ? $wrtypeID :($acctID == 224 ? 373 : $wrtypeID));					
                echo $this->GET_Masters($wrtypeID,'23');
            echo '</select>';
			echo '<span id="register_wrtypeID_errorloc" class="errors"></span>';
        echo '</div>'; 
    }

    echo ($this->GET_SinglePermission('1') == 1 || $this->GET_SinglePermission('2') == 1) ? '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>' : '';

    echo '</div>';

    echo '<div '.(($acctID == 224 || $acctID == 48 || $acctID == 50 || $acctID == 51 || $acctID == 220 || $acctID == 54) ? 'class="row partID_2"' : 'style="display:none;" class="row partID_2"').'><div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div></div>';

    echo '<div class="row">';
      echo '<div class="col-xs-3">';	
      if(!empty($id))
              echo '<input name="ID" value="'.$id.'" type="hidden">';
              echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" id="complaintSubmit" type="submit">'.(!empty($id) ? 'Update Comment Line Register' : 'Save Comment Line Register').'</button>';
      echo '</div>';

    echo '<div class="col-xs-2">';
    echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:20px; float:right; display:inline-block" type="button">View All Lists</button></a>';
    echo '</div>';
    echo '</div>';
	
	echo '<div id="ComplaintValidGridID"></div>';
	
    echo '</div>';
    echo '</form>';
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);		//echo '<PRE>'; echo print_r($_POST); exit;
			
			if($cmltypeID == 491 || $cmltypeID == 492)
			{
				if($cmprefno == '')     $errors .= "Enter The Comment Line Ref No.<br>";
			}
			
            if($dateID == '')       $errors .= "Enter The Comment Line Date.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }			
            else
            {    
				if($accID == 224)
				{
					$_POST['substanID'] = 0;		$_POST['faultID'] = 0;		$_POST['disciplineID'] = 1;
				}
				
				if($accID == 48 || $accID == 221 || $accID == 49 || $accID == 50 || $accID == 51 || $accID == 220 || $accID == 54)
				{
					$_POST['substanID'] = 2;        $_POST['faultID'] = 4;
					/*$_POST['invID'] = 0;			$_POST['invdate'] = '0000-00-00';*/
					$_POST['disciplineID'] = 0;
				}
				
				$_POST['dateID']   = $this->dateFormat($_POST['dateID']);    $_POST['serDT']    = $this->dateFormat($_POST['serDT']);
				$_POST['invdate']  = $this->dateFormat($_POST['invdate']);   $_POST['resdate']  = $this->dateFormat($_POST['resdate']);
				$_POST['userID']   = $_SESSION[$this->website]['userID'];	 $_POST['intvDate'] = $this->dateFormat($_POST['intvDate']);
				$_POST['cmdueDT']  = $this->dateFormat($_POST['cmdueDT']);
				$_POST['refno']    = $_POST['code'];			$_POST['serno'] = $_POST['code'];
				$_POST['statusID'] = $_POST['CMPLstatusID'];	$_POST['disciplineID'] = $_POST['cmdiscID'];
				$_POST['driverID'] = $_POST['empID'];			$_POST['refno']	= $_POST['cmprefno'];
				
				unset($_POST['Submit'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['CMPLstatusID'],$_POST['cmprefno']);
				$_POST['companyID'] = $this->companyID;
				$_POST['tickID_1'] = $tickID_1 > 0 ? $tickID_1 : 0;
				$_POST['trisID']   = $trisID > 0   ? $trisID   : 0;
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['systemID'] = $this->get_systemID($_POST['driverID']);
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					if($cmltypeID <> 491 && $cmltypeID <> 492)
					{
						$updateARR = array();
						$updateARR['refno'] = 'PH-'.$lastID[0];
						$updateONS['ID'] 	= $lastID[0];
						$this->BuildAndRunUpdateQuery($this->tableName,$updateARR,$updateONS);
					}
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['driverID'],$array['dcodeID'],$array['refno'],'A',$array['description'],$array);

					$this->msg = urlencode(' Comment Line Register is Created Successfully . <br /> Comment Line Ref No : '.$array['refno']);
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

            if($cmltypeID == 491 || $cmltypeID == 492)
			{
				if($cmprefno == '')     $errors .= "Enter The Comment Line Ref No.<br>";
			}
            if($dateID == '')           $errors .= "Enter The Comment Line Date.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            { 
				if($accID == 224)
				{
					$_POST['substanID'] = 0;		$_POST['faultID'] = 0;		$_POST['disciplineID'] = 1;
				}
				if($accID == 48 || $accID == 221 || $accID == 49 || $accID == 50 || $accID == 51 || $accID == 220 || $accID == 54)
				{
					$_POST['substanID'] = 2;        $_POST['faultID'] = 4;
					/*$_POST['invID'] = 0;			$_POST['invdate'] = '0000-00-00';*/
					$_POST['disciplineID'] = 0;
				}
				
				$_POST['dateID']   = $this->dateFormat($_POST['dateID']);    $_POST['serDT']   = $this->dateFormat($_POST['serDT']);
				$_POST['invdate']  = $this->dateFormat($_POST['invdate']);   $_POST['resdate'] = $this->dateFormat($_POST['resdate']);
				$_POST['intvDate'] = $this->dateFormat($_POST['intvDate']);	 $_POST['cmdueDT']  = $this->dateFormat($_POST['cmdueDT']);
				$_POST['refno']    = $_POST['code'];			$_POST['serno'] = $_POST['code'];
				$_POST['statusID'] = $_POST['CMPLstatusID'];	$_POST['disciplineID'] = $_POST['cmdiscID'];
				$_POST['driverID'] = $_POST['empID'];			$_POST['refno'] = $_POST['cmprefno'];
				
				unset($_POST['Submit'],$_POST['empID'],$_POST['cmdiscID'],$_POST['code'],$_POST['ID'],$_POST['CMPLstatusID'],$_POST['cmprefno']);
				$_POST['tickID_1'] = $tickID_1 > 0 ? $tickID_1 : 0;
				$_POST['trisID']   = $trisID > 0   ? $trisID   : 0;

				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($_POST['driverID']);
				$array['trisID']    = $_POST['trisID'] > 0 ? $_POST['trisID'] : 0;
				$on['ID'] = $ID;
				//echo '<pre>'; echo print_r($array); exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					if($cmltypeID <> 491 && $cmltypeID <> 492)
					{
						$updateARR = array();
						$updateARR['refno'] = 'PH-'.$ID;
						$updateONS['ID'] 	= $ID;
						$this->BuildAndRunUpdateQuery($this->tableName,$updateARR,$updateONS);
					}
					
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['driverID'],$array['dcodeID'],$array['refno'],'E',$array['description'],$array);
					
					$this->msg = urlencode(' Comment Line Register is Updated Successfully . <br /> Comment Line Ref No : '.$array['refno']);
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