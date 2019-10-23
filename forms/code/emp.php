<?PHP
class Masters extends SFunctions
{
    private $tableName	=	'';
    private $basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'employee';
		$this->companyID	= $_SESSION[$this->website]['compID'];        
        $this->frmID	    = '37';
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($searchbyID,$auditID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {
            /* SEARCH BY  -  OPTIONS */
            $src = "";
            $src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? 
            " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : 
            " AND ".$this->tableName.".code LIKE '%".$searchbyID."%' ")) : ("AND status = 1 "));
			
			if($auditID <> '')
			{
				$src .= " AND employee.ID In(".$auditID.") ";
			}
			
			$SQL = "SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ".$src." ORDER BY code DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>E. Code</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Address</th>';
                echo '<th>Suburb</th>';
                echo '<th>Mobile No</th>';
				echo '<th>Email ID</th>';
                echo '<th>Designation</th>';
                echo '<th align="center">Casual/Part Time/Full Time</th>';
				echo ($_SESSION[$this->website]['scompID'] <> '' ? '<th>Depot</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U<br />Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">P<br />Log</th>' : '');
                echo '</tr></thead>'; 
                foreach($this->rows as $row)			
                {
                    $arrSU  = $row['sid'] > 0 ? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['sid']." ") : '';
                    $arrDG  = $row['desigID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['desigID']." ") : '';
					
					$adds = '';
                    $adds .= $row['address_1'] <> '' ? $row['address_1'] : '';
                    $adds .= $row['address_2'] <> '' ? ' , '.$row['address_2'] : '';
					
                    $phon  = '';
                    $phon .= ((!empty($row['phone']) && !empty($row['phone_1'])) ? $row['phone'].' , '.$row['phone_1'] : '');
                    $phon .= ((!empty($row['phone']) && empty($row['phone_1']))  ? $row['phone'] : '');
                    $phon .= ((empty($row['phone']) && !empty($row['phone_1']))  ? $row['phone_1'] : '');
					
					echo '<tr>';
                    echo '<td style="padding-left:3px; padding-right:2px;" align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Employee Masters').'" style="text-decoration:none; cursor:pointer;">'.$row['code'].'</a></td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.$row['fname'].'</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.$row['lname'].'</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.$this->Word_Wraping($adds,'5').'</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.($row['sid'] > 0 ? $arrSU[0]['title'] : '').' ('.$row['pincode'].')</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.$phon.'</td>';
					echo '<td style="padding-left:3px; padding-right:2px;">'.$row['emailID'].'</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.$arrDG[0]['title'].'</td>';
                    echo '<td style="padding-left:3px; padding-right:2px;">'.($row['casualID'] == 1 ? 'Full Time'  :($row['casualID'] == 2 ? 'Part Time' :($row['casualID'] == 3 ? 'Casual' : ''))).'</td>';
					
					if($_SESSION[$this->website]['scompID'] <> '')
					{
						$arrSD  = $row['scompanyID'] > 0 ? $this->select('company_dtls',array("title"), " WHERE ID = ".$row['scompanyID']." ") : '';
						$arrCD  = $this->select('company',array("title"), " WHERE ID = ".$row['companyID']." ");
						
						echo '<td style="padding-left:3px; padding-right:2px;">'.$arrCD[0]['title'].' ('.($arrSD[0]['title']).')</td>';
					}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
						if((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days')))	{echo '<td></td>';}
						else
						{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'"  data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    { 
                        if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
                        {echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Employee Masters').'" style="text-decoration:none; cursor:pointer;"></a></td>';}
                        else	{echo '<td></td>';}
                    }
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Employee Details').'" style="text-decoration:none; cursor:pointer;"></a></td>';
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
    
    $RD = ($this->result['status'] == 2 ? 'readonly="readonly"' : '');
    $DS = ($this->result['status'] == 2 ? 'disabled="disabled"' : '');
    
    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';

	echo '<input type="hidden" id="employeeID" value="'.($id > 0 ? $id : 0).'">';
	
    echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="EcodeID" name="code" placeholder="Enter Code" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? ($this->result['code']) : $this->safeDisplay('code')).'">';
			echo '<span id="register_code_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Last Name</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="lname" name="lname" placeholder="Enter Last Name" '.$RD.' style="text--align:center !important;"
							value="'.(!empty($id) ? ($this->result['lname']) : $this->safeDisplay('lname')).'">';
		echo '</div>';  

		echo '<div class="col-xs-4">';
			echo '<label for="section">First Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="fname" name="fname" placeholder="Enter First Name" '.$RD.' style="text--align:center !important;" value="'.(!empty($id) ? ($this->result['fname']) : $this->safeDisplay('fname')).'">';
			echo '<span id="register_fname_errorloc" class="errors"></span>';
		echo '</div>';	 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Full Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="full_name" name="full_name" placeholder=" Full Name" readonly="readonly" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['full_name']) : $this->safeDisplay('full_name')).'">';
			echo '<span id="register_full_name_errorloc" class="errors"></span>';
		echo '</div>';
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-2">';
				echo '<label for="section">Date of Birth </label>';
				echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dob" name="dob" placeholder="Enter D.o.B" '.$RD.' style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dob']) : $this->VdateFormat($this->safeDisplay['dob'])).'">';
            echo '</div>'; 

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Gender</label>';
                    echo '<select onchange="changes=true;" class="form-control" id="genderID" name="genderID" '.$DS.'>';
                            echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                            echo $this->GET_Masters((!empty($id) ? $this->result['genderID'] : $this->safeDisplay['genderID']),'6');
                    echo '</select>';
            echo '</div>';

            echo '<div class="col-xs-4">';
                echo '<label for="section">Designation <span class="Maindaitory">*</span></label>';
                echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="desigID" name="desigID" '.$DS.'>';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select Designation --</option>';
                $desigID = (!empty($id) ? $this->result['desigID'] : $this->safeDisplay['desigID']);
                echo $this->GET_Masters($desigID,'12');
                echo '</select>';
                echo '<span id="register_desigID_errorloc" class="errors"></span>';
            echo '</div>';

			echo '<div class="col-xs-1"></div>';
			
			echo $this->GET_SubDepotLists((!empty($id) ? $this->result['scompanyID'] : $this->safeDisplay['scompanyID']),$this->frmID);
			
            $array_systemID = $this->select($this->tableName, array("*"), " WHERE systemID > 0 Order BY systemID DESC LIMIT 1 ");
            $systemID = $array_systemID[0]['systemID'] > 0 ? ($array_systemID[0]['systemID'] + 1) : '1001';
			echo '<input type="hidden" name="systemID" value="'.(!empty($id) ? $this->result['systemID'] : $systemID).'">';
						
            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';

    echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Address - 1 <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="address_1" name="address_1" placeholder="Enter Address - 1" '.$RD.' value="'.(!empty($id) ? ($this->result['address_1']) : $this->safeDisplay('address_1')).'">';
			echo '<span id="register_address_1_errorloc" class="errors"></span>';
		echo '</div>';	 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Address - 2</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="address_2" name="address_2" placeholder="Enter Address - 2" '.$RD.' value="'.(!empty($id) ? ($this->result['address_2']) : $this->safeDisplay('address_2')).'">';
		echo '</div>'; 

		echo '<div class="col-xs-4">';
		echo '<label for="section">Suburb <span class="Maindaitory">*</span></label>';
		echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="suburbID" name="sid" '.$DS.'>';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Suburb --</option>';
			echo $this->GET_SubUrbs((!empty($id) ? $this->result['sid'] : $this->safeDisplay['sid']),'');
		echo '</select>';
			echo '<span id="register_sid_errorloc" class="errors"></span>';
		echo '</div>'; 
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-2">';
                echo '<label for="section">PostCode</label>';   /* <span class="Maindaitory">*</span>*/
                echo '<input type="text" onchange="changes=true;" class="form-control decimal_places_1 numeric positive" maxlength="4" id="pincode" name="pincode" readonly="readonly" placeholder="Enter PostCode" style="text-transform:uppercase !important; text-align:center;" '.$RD.' value="'.(!empty($id) ? ($this->result['pincode']) : $this->safeDisplay('pincode')).'">';
                //echo '<span id="register_pincode_errorloc" class="errors"></span>';
            echo '</div>'; 

            echo '<div class="col-xs-2"></div>'; 

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Telephone </label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="phone" name="phone" placeholder="Enter Telephone" '.$RD.' value="'.(!empty($id) ? ($this->result['phone']) : $this->safeDisplay('phone')).'">';
            echo '</div>'; 

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Mobile No</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="phone_1" name="phone_1" placeholder="Enter Mobile No" '.$RD.' value="'.(!empty($id) ? ($this->result['phone_1']) : $this->safeDisplay('phone_1')).'">';
            echo '</div>';

            echo '<div class="col-xs-4">';
                    echo '<label for="section">Email ID</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="emailID" name="emailID" placeholder="Enter Email ID" '.$RD.' value="'.(!empty($id) ? ($this->result['emailID']) : $this->safeDisplay('emailID')).'">';
            echo '</div>';

            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';
	
    echo '<div class="row">';		
            echo '<div class="col-xs-4">';
                    echo '<label for="section">Driver\'s Licence No</label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="ddlcno" name="ddlcno" placeholder="Enter Driver\'s Licence No" '.$RD.' style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['ddlcno']) : $this->safeDisplay('ddlcno')).'">';
            echo '</div>';	 

            echo '<div class="col-xs-2">';
                    echo '<label for="section">D-Licence Expiry Date</label>';
                    echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="ddlcdt" name="ddlcdt" placeholder="Enter Expiry Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['ddlcdt']) : $this->VdateFormat($this->safeDisplay['ddlcdt'])).'">';
            echo '</div>'; 

			echo '<div class="col-xs-4">';
				echo '<label for="section">License Type</label>';
				echo '<select onchange="changes=true;" class="form-control" id="lctypeID" name="lctypeID" '.$DS.'>';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select License Type --</option>';
					echo $this->GET_Masters((!empty($id) ? $this->result['lctypeID'] : $this->safeDisplay['lctypeID']),'123');
				echo '</select>';
			echo '</div>';
			
			echo '<div class="col-xs-2">';
				echo '<label for="section">Artic Inducted</label>';
				echo '<select onchange="changes=true;" class="form-control" id="articID" name="articID" '.$DS.'>';
					echo '<option value="0" selected="selected">-- Select --</option>';
					$articID = (!empty($id) ? $this->result['articID'] : $this->safeDisplay['articID']);
					echo '<option value="1" '.($articID == 1 ? 'selected="selected"' : '').'>Yes</option>';
					echo '<option value="2" '.($articID == 2 ? 'selected="selected"' : '').'>No</option>';
				echo '</select>';
			echo '</div>';
			
			echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">WWC Permit No </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="wwcprno" name="wwcprno" placeholder="Enter WWC Permit No" '.$RD.' style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['wwcprno']) : $this->safeDisplay('wwcprno')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-2">';
			echo '<label for="section">WWC Expiry Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="wwcprdt" name="wwcprdt" placeholder="Enter Expiry Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['wwcprdt']) : $this->VdateFormat($this->safeDisplay['wwcprdt'])).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Applied Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="wwcapdt" name="wwcapdt" placeholder="Enter Expiry Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['wwcapdt']) : $this->VdateFormat($this->safeDisplay['wwcapdt'])).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-1"></div>'; 
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Letter of Authority Received Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="lardt" name="lardt" placeholder="Enter Letter of Authority Received Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['lardt']) : $this->VdateFormat($this->safeDisplay['lardt'])).'">';
		echo '</div>'; 

		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

	echo '<div class="row">'; 
		echo '<div class="col-xs-4">';
		echo '<label for="section">VISA Type</label>';
		echo '<select onchange="changes=true;" class="form-control" id="visatypeID" name="visatypeID" '.$DS.'>';
			echo '<option value="0" selected="selected">-- Select VISA Type --</option>';
			$visatypeID = (!empty($id) ? $this->result['visatypeID'] : $this->safeDisplay['visatypeID']);
			echo $this->GET_Masters((!empty($id) ? $this->result['visatypeID'] : $this->safeDisplay['visatypeID']),'122');
			echo '<option value="1" '.($visatypeID == 1 ? 'selected="selected"' : '').'>Other</option>';
		echo '</select>';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
		echo '<label for="section">Smart Card Rider No </label>';
		echo '<input type="text" onchange="changes=true;" class="form-control" id="smartcardNO" name="smartcardNO" placeholder="Enter Smart Card Rider No" value="'.(!empty($id) ? $this->result['smartcardNO'] : 'SR-'.$this->safeDisplay['smartcardNO']).'">';
		echo '</div>';
		
		$drvrightID = (!empty($id) ? $this->result['drvrightID'] : $this->safeDisplay['drvrightID']);
		echo '<div class="col-xs-2">';
			echo '<label for="section">DriveRight No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control numeric" id="drvrightID" name="drvrightID" placeholder="Enter DriveRight No" '.$RD.' value="'.(!empty($id) ? ($this->result['drvrightID']) : $this->safeDisplay('drvrightID')).'">';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">RFID</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="dob" name="rfID" placeholder="Enter RFID" '.$RD.' style="text-align:center;" value="'.(!empty($id) ? $this->result['rfID'] : $this->safeDisplay['rfID']).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">F/T Extension</label>';
			echo '<select onchange="changes=true;" class="form-control" id="ftextID" name="ftextID" '.$DS.'>';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				echo '<option value="F" '.($this->result['ftextID'] == 'F' ? 'selected="selected"' : '').'>F</option>';
				echo '<option value="T" '.($this->result['ftextID'] == 'T' ? 'selected="selected"' : '').'>T</option>';
				echo '<option value="N" '.($this->result['ftextID'] == 'N' ? 'selected="selected"' : '').'>N</option>';
			echo '</select>';
		echo '</div>'; 		
    echo '</div>';
	
	 echo '<div class="row visa_emp_DIV" '.($visatypeID == 1 ? 'style="display:block;"' : 'style="display:none;"').'><br />';
		echo '<div class="col-xs-4">';
			echo '<label for="section">VISA Details</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="visaDetails" name="visaDetails" placeholder="Enter VISA Details" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['visaDetails']) : $this->safeDisplay('visaDetails')).'">';
		echo '</div>';
		
		echo '<div class="col-xs-8">';
			echo '<label for="section">Working Restrictions</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="workingResc" name="workingResc" placeholder="Enter Working Restrictions"  style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['workingResc']) : $this->safeDisplay('workingResc')).'">';
		echo '</div>'; 
    echo '</div>';
	
    echo '<div class="row mch_emp_DIV" '.($desigID == 418 ? 'style="display:block;"' : 'style="display:none;"').'><br />';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Gas Fitting Permit No</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="gfpermitNO" name="gfpermitNO" placeholder="Enter Gas Fitting Permit No" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['gfpermitNO']) : $this->safeDisplay('gfpermitNO')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Expiry Date</label>';
			echo '<input type="datable" onchange="changes=true;" '.$RD.' class="form-control datepicker" data-datable="ddmmyyyy" id="gfpnexpDT" name="gfpnexpDT" placeholder="Enter Expiry Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['gfpnexpDT']) : $this->VdateFormat($this->safeDisplay['gfpnexpDT'])).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">A/Con-Refrigerant Licence No</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="acpermitNO" name="acpermitNO" placeholder="Enter A/Con Licence No"  style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['acpermitNO']) : $this->safeDisplay('acpermitNO')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Expiry Date</label>';
			echo '<input type="datable" onchange="changes=true;" '.$RD.' class="form-control datepicker" data-datable="ddmmyyyy" id="acpnexpDT" name="acpnexpDT" placeholder="Enter Expiry Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['acpnexpDT']) : $this->VdateFormat($this->safeDisplay['acpnexpDT'])).'">';
		echo '</div>'; 
    echo '</div><br />';

    echo '<div class="row mch_emp_DIV" '.($desigID == 418 ? 'style="display:block;"' : 'style="display:none;"').'>';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Work Safe – Dogging Licence No</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="wsdpermitNO" name="wsdpermitNO" placeholder="Enter Work Safe – Dogging Licence No" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['wsdpermitNO']) : $this->safeDisplay('wsdpermitNO')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Expiry Date</label>';
			echo '<input type="datable" onchange="changes=true;" '.$RD.' class="form-control datepicker" data-datable="ddmmyyyy" id="wsdpnexpDT" name="wsdpnexpDT" placeholder="Enter Expiry Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['wsdpnexpDT']) : $this->VdateFormat($this->safeDisplay['wsdpnexpDT'])).'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Forklift Licence No</label>';
			echo '<input type="text" onchange="changes=true;" '.$RD.' class="form-control" id="flpermitNO" name="flpermitNO" placeholder="Enter Forklift Licence No" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['flpermitNO']) : $this->safeDisplay('flpermitNO')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Expiry Date</label>';
			echo '<input type="datable" onchange="changes=true;" '.$RD.' class="form-control datepicker" data-datable="ddmmyyyy" id="flpnexpDT" name="flpnexpDT" placeholder="Enter Expiry Date" style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['flpnexpDT']) : $this->VdateFormat($this->safeDisplay['flpnexpDT'])).'">';
		echo '</div>';	
    echo '</div>';
	
    echo '<div class="row">';		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Air Key No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="arkno" name="arkno" placeholder="Enter Air Key No" '.$RD.' value="'.(!empty($id) ? ($this->result['arkno']) : $this->safeDisplay('arkno')).'">';
		echo '</div>';	 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Name of Kin</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="kinname" name="kinname" placeholder="Enter Name of Kin" '.$RD.' value="'.(!empty($id) ? ($this->result['kinname']) : $this->safeDisplay('kinname')).'">';
		echo '</div>';

		echo '<div class="col-xs-2">';
			echo '<label for="section">Kin Contact No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="kincno" name="kincno" placeholder="Enter Kin Contact No" '.$RD.' value="'.(!empty($id) ? ($this->result['kincno']) : $this->safeDisplay('kincno')).'">';
		echo '</div>';

		echo '<div class="col-xs-2">';
		echo '<label for="section" style="font-size: 12px;">Casual/Full Time/Part Time</label>';
		echo '<select onchange="changes=true;" class="form-control" id="casualID" name="casualID" '.$DS.'>';
		echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
		echo '<option value="1" '.($this->result['casualID'] == 1 ? 'selected="selected"' : '').'>Full Time</option>';
		echo '<option value="2" '.($this->result['casualID'] == 2 ? 'selected="selected"' : '').'>Part Time</option>';
		echo '<option value="3" '.($this->result['casualID'] == 3 ? 'selected="selected"' : '').'>Casual</option>';
		echo '</select>';
		echo '</div>';
    echo '</div><br />';

    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Locker No</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="lockerno" name="lockerno" placeholder="Enter Locker No" '.$RD.' value="'.(!empty($id) ? ($this->result['lockerno']) : $this->safeDisplay('lockerno')).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Employee Start Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="esdate" name="esdate" placeholder="Enter Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['esdate']) : $this->VdateFormat($this->safeDisplay['esdate'])).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Casual Start Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="csdate" name="csdate" placeholder="Enter Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['csdate']) : $this->VdateFormat($this->safeDisplay['csdate'])).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section" style="font-size: 13px;">Full/Part Time Start Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="ftsdate" name="ftsdate" placeholder="Enter Date" '.$RD.' style="text-align:center;" required="required" value="'.(!empty($id) ? $this->VdateFormat($this->result['ftsdate']) : $this->VdateFormat($this->safeDisplay['ftsdate'])).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">End Date</label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="enddate" name="enddate" placeholder="Enter Date" '.$RD.'  style="text-align:center;" required="required" '.($this->result['status'] == 2 ? '' : 'readonly="readonly"').' value="'.(!empty($id) ? $this->VdateFormat($this->result['enddate']) : $this->VdateFormat($this->safeDisplay['enddate'])).'">';
		echo '</div>'; 

		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-1"></div>';

		echo '<div class="col-xs-2">';
				echo '<label for="section">Current Employee <span class="Maindaitory">*</span></label>';
				echo '<select name="statusID" onchange="changes=true;" class="form-control" id="cstatusID">';
					$statusID = (!empty($id) ? ($this->result['status']) : $this->safeDisplay('statusID'));
					$statusID = ($statusID > 0 ? $statusID : 1);
					echo '<option value="0" selected="selected"> --- Select --- </option>';
					echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Y</option>';
					echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>N</option>';
				echo '</select>';
				echo '<span id="register_statusID_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-3">';
			echo '<label for="section">Reason For Job Leaving</label>';
				echo '<select onchange="changes=true;" class="form-control" id="crleavingID" name="rleavingID" '.($statusID == 2 ? 'disabled="disabled"' : 'disabled="disabled"').'>';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Reason For Job Leaving --</option>';
				echo '<option value="1" '.($this->result['rleavingID'] == 1 ? 'selected="selected"' : '').'>Resigned</option>';
				echo '<option value="2" '.($this->result['rleavingID'] == 2 ? 'selected="selected"' : '').'>Terminated</option>';
				echo '<option value="3" '.($this->result['rleavingID'] == 3 ? 'selected="selected"' : '').'>Transferred</option>';
				echo '<option value="4" '.($this->result['rleavingID'] == 4 ? 'selected="selected"' : '').'>Retired</option>';
				echo '<option value="5" '.($this->result['rleavingID'] == 5 ? 'selected="selected"' : '').'>Deceased</option>';
				echo '</select>';
		echo '</div>';

		echo '<div class="col-xs-3">';
			echo '<label for="section">Reason For Resignation</label>';
			echo '<select onchange="changes=true;" class="form-control" id="resonrgID" name="resonrgID" '.($this->result['rleavingID'] <> 1 || empty($this->result['status']) || $this->result['status'] == 2 ? 'disabled="disabled"' : '').'>';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Reason For Resignation --</option>';
			echo $this->GET_Masters((!empty($id) ? $this->result['resonrgID'] : $this->safeDisplay['resonrgID']),'67');
			echo '</select>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3">';
				echo '<label for="section">Reason For Termination</label>';
				echo '<select onchange="changes=true;" class="form-control" id="terminationID" name="terminationID" '.($this->result['rleavingID'] <> 2 || empty($this->result['status']) || $this->result['status'] == 2 ? 'disabled="disabled"' : '').'>';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Termination Reason --</option>';
				$terminationID = (!empty($id) ? $this->result['terminationID'] : $this->safeDisplay['terminationID']);
				echo $this->GET_Masters($terminationID,'121');
				echo '</select>';
		echo '</div>';
    echo '</div>';
	
    echo '<div class="row">';		
		echo '<div class="col-xs-7">';
		
		/****** START - EMPLOYEE TRANSFER DETAILS ******/
		echo '<div id="transferDiv" style="display:none;">';		
			echo '<div style="margin-left:-2px; min-height:90px; border:solid 2px #F56954; width: 595px; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">';
			
			echo '<label style="vertical-align: top; margin-top: -21px; background: black; color: yellow; border: #F56954 2px solid; border-radius: 5px; padding: 2px; padding-right: 2px; padding-left: 2px; padding-left: 11px; padding-right: 11px;">Employee Transfer Details</label>';
			
				echo '<div style="display:inline-block; margin-left: -178px; margin-top: 12px;">';
					echo '<label style="width:120px;">Code Changes <span class="Maindaitory">*</span></label>';
					echo '<select onchange="changes=true;" class="form-control" id="ecodeTY" name="ecodeTY">';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
					echo '<option value="1" selected="selected">Yes</option>';
					echo '<option value="2">No</option>';
					echo '</select>';
					echo '<span id="register_ecodeTY_errorloc" class="errors"></span>';
				echo '</div>';
			
				echo '<div style="display:inline-block; margin-left: 11px; margin-top: 12px;">';
					echo '<label style="width:120px;">New E. Code <span class="Maindaitory">*</span></label>';
					echo '<input type="text" onchange="changes=true;" class="form-control" name="encodeID" id="encodeID" placeholder="Enter E. Code" style="width:150px; text-align:center;" />';
					echo '<span id="register_encodeID_errorloc" class="errors"></span>';
				echo '</div>';

				echo '<div style="display:inline-block; margin-left: 11px; margin-top: 12px;">';
					echo '<label style="width:240px;">Transfer Depot <span class="Maindaitory">*</span></label>';
					echo '<select onchange="changes=true;" class="form-control" id="tdepotID" name="tdepotID">';
					echo '<option value="0" selected="selected" disabled="disabled">-- Select Depot Name --</option>';
			
					$SQL = "Select AllDepots.companyID, AllDepots.scompanyID, AllDepots.title,  AllDepots.pscode From (Select company.ID As companyID,  0 As scompanyID, company.title, company.pscode, 1 As tableID From 
					company Left Join company_dtls On company_dtls.companyID = company.ID Where company_dtls.companyID Is Null UNION All Select company_dtls.companyID, company_dtls.ID, company_dtls.title, 
					company_dtls.pscode, 2 As tableID From company_dtls) As AllDepots Order By AllDepots.companyID, AllDepots.scompanyID ASC ";
					$Qry = $this->DB->prepare($SQL);
					$Qry->execute();
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->rows as $rows)
					{
						echo '<option value="'.($rows['companyID'].'-'.$rows['scompanyID']).'">'.$rows['title'].' - '.$rows['pscode'].'</option>';
					}
			
					echo '</select>';
					
					echo '<input type="hidden" name="tdepotTX" id="tdepotTX" />';
					echo '<span id="register_tdepotTX_errorloc" class="errors"></span>';
				echo '</div>';
				
			echo '</div>';			
		echo '</div>';		
		echo '</div>';
		/****** ENDING - EMPLOYEE TRANSFER DETAILS ******/
		
		echo '<div class="col-xs-5">';
			echo '<label for="section">Termination - Other Details </label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" '.($terminationID == 481 ? $RD : 'readonly="readonly"').' id="termOther" name="termOther" placeholder="Enter Termination - Other Details" style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['termOther']) : $this->safeDisplay('termOther')).'">';
		echo '</div>';
	
	echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
	
    echo '<div class="row">';
	if($id > 0 && $statusID == 2)
	{
		/* Do - Nothing*/
	}
	else
	{
      echo '<div class="col-xs-2">';	
      if(!empty($id))
        echo '<input name="ID" value="'.$id.'" type="hidden">';
		echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Employee Master' : 'Save Employee Master').'</button>';
      echo '</div>';
	}
      
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
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;

			if($fname == '') 	    $errors .= "Enter The Employee First Name.<br>";
			if($code == '') 	   $errors .= "Enter The Employee Code.<br>";
			if($full_name == '')   $errors .= "Enter The Employee Full Name.<br>";
			if($address_1 == '')   $errors .= "Enter The Employee Address.<br>";
			if($pincode == '') 	   $errors .= "Enter The Employee Address PostCode.<br>";			
			if($statusID == 0) 	   $errors .= "Select The Employee Working Status.<br>";
			
			if($statusID == 2)	/* when current employee no */
			{
				if($rleavingID == 0)    $errors .= "Select The Reason For Job Leaving Status.<br>";
				if($enddate == '')      $errors .= "Enter The Employee End Date.<br>";
			}
			if($rleavingID == 1)	/* when select resgined status */
			{
				if($resonrgID == 0)     $errors .= "Select The Reason For Resignation Status.<br>";
			}
			
			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			}

			else
			{	
					$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE code =:code AND companyID = :companyID "); 
					$Qry->bindParam(':code',$code);
					$Qry->bindParam(':companyID',$this->companyID);
					$Qry->execute();
					$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
					$rowCount	 = $this->result['resultRows'];


					if($desigID == 9 || $desigID == 209)
					{
						$rowCount = $rowCount;
					}
					else
					{
						$rowCount = 0;
					}
					
					if($rowCount > 0 ) 
					{
						$this->printMessage('danger','Employee Code Already exist !...');
						$this->createForm();
					}
					else
					{
						unset($_POST['ecodeTY'],$_POST['encodeID'],$_POST['tdepotID'],$_POST['tdepotTX']);
						unset($_POST['sstatusID']);
						
						$_POST['gfpnexpDT']  = $this->dateFormat($_POST['gfpnexpDT']);	$_POST['acpnexpDT']  = $this->dateFormat($_POST['acpnexpDT']);
						$_POST['flpnexpDT']  = $this->dateFormat($_POST['flpnexpDT']);	$_POST['wsdpnexpDT']  = $this->dateFormat($_POST['wsdpnexpDT']);						
						$_POST['dob']     = $this->dateFormat($_POST['dob']);			$_POST['ddlcdt']  = $this->dateFormat($_POST['ddlcdt']);
						$_POST['wwcprdt'] = $this->dateFormat($_POST['wwcprdt']);		$_POST['lardt']   = $this->dateFormat($_POST['lardt']);
						$_POST['esdate']  = $this->dateFormat($_POST['esdate']);		$_POST['csdate']  = $this->dateFormat($_POST['csdate']);
						$_POST['ftsdate'] = $this->dateFormat($_POST['ftsdate']);		$_POST['enddate'] = $this->dateFormat($_POST['enddate']);
						$_POST['status'] = $_POST['statusID'];
						unset($_POST['Submit'],$_POST['statusID']);
						$_POST['companyID'] = $this->companyID;

						$array = array();
						foreach($_POST as $key=>$value)	{$array[$key] = $value;}
						$array['logID']	= date('Y-m-d H:i:s');
						//echo '<PRE>'; echo print_r($array);
						//echo '<PRE>'; echo print_r($array); exit;
						if($this->BuildAndRunInsertQuery($this->tableName,$array))
						{
							$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
							$lastID = $stmt->fetch(PDO::FETCH_NUM);

							$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dob'],$lastID[0],$array['code'],'','A',($array['address_1'].' '.$array['address_2']),$array);

							$this->msg = urlencode(' Employee Master Is Created successfully .<br /> 
							<br /> Employee Code : '.$array['code'].'<br /> Employee Name : '.$array['full_name'].'
							<br /> Telephone : '.$array['phone'].'<br /> Mobile - No : '.$array['phone_1'].'
							<br /> Address : '.$array['address_1'].' '.$array['address_2'].' '.$array['suburb'].'.');
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
        if($this->Form_Variables() == true)		//echo '<pre>'; echo print_r($_POST); exit;
        {	
			extract($_POST);

			$errors	=	'';

			if($fname == '') 	   $errors .= "Enter The Employee First Name.<br>";
			if($code == '') 	   $errors .= "Enter The Employee Code.<br>";
			if($full_name == '')   $errors .= "Enter The Employee Full Name.<br>";
			if($address_1 == '')   $errors .= "Enter The Employee Address.<br>";
			if($pincode == '') 	   $errors .= "Enter The Employee Address PostCode.<br>";			
			if($statusID == 0) 	   $errors .= "Select The Employee Working Status.<br>";

			if($statusID == 2)	/* when current employee no */
			{
				if($rleavingID == 0) 	$errors .= "Select The Reason For Job Leaving Status.<br>";
				if($enddate == '') 		$errors .= "Enter The Employee End Date.<br>";
			}
			if($rleavingID == 1)	/* when select resgined status */
			{
				if($resonrgID == 0) 	$errors .= "Select The Reason For Resignation Status.<br>";
			}

			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE code =:code AND companyID = :companyID AND ID <> :ID ");
				$Qry->bindParam(':code',$code);
				$Qry->bindParam(':companyID',$this->companyID);
				$Qry->bindParam(':ID',$ID);				
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount   = $this->result['resultRows'];

				if($desigID == 9 || $desigID == 209)		{$rowCount = $rowCount;}
				else										{$rowCount = 0;}

				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Employee Code Already exist !...');
					$this->createForm($ID);
				}
				else
				{
					/* STARTING -- EMPLOYEE TRANSFER - VARIABLES */
					$paramVal = array();
					$paramVal['ecodeTY'] = $_POST['ecodeTY'];			$paramVal['encodeID'] = $_POST['encodeID'];
					$paramVal['tdepotID'] = $_POST['tdepotID'];			$paramVal['tdepotTX'] = $_POST['tdepotTX'];
					$paramVal['emplyeID'] = $_POST['ID'];
					/* ENDINGS -- EMPLOYEE TRANSFER - VARIABLES */
					
					$_POST['gfpnexpDT']  = $this->dateFormat($_POST['gfpnexpDT']);	$_POST['acpnexpDT']  = $this->dateFormat($_POST['acpnexpDT']);
					$_POST['flpnexpDT']  = $this->dateFormat($_POST['flpnexpDT']);	$_POST['wsdpnexpDT']  = $this->dateFormat($_POST['wsdpnexpDT']);
					$_POST['dob'] 	  = $this->dateFormat($_POST['dob']);		$_POST['ddlcdt']  = $this->dateFormat($_POST['ddlcdt']);
					$_POST['wwcprdt'] = $this->dateFormat($_POST['wwcprdt']);   $_POST['lardt']   = $this->dateFormat($_POST['lardt']);
					$_POST['esdate']  = $this->dateFormat($_POST['esdate']);	$_POST['csdate']  = $this->dateFormat($_POST['csdate']);
					$_POST['ftsdate'] = $this->dateFormat($_POST['ftsdate']);	$_POST['enddate'] = $this->dateFormat($_POST['enddate']);
					$_POST['status'] = $_POST['statusID']; 
					unset($_POST['Submit'],$_POST['statusID'],$_POST['ID'],$_POST['ecodeTY'],$_POST['encodeID'],$_POST['tdepotID'],$_POST['tdepotTX'],$_POST['sstatusID']);
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$on['ID'] = $ID;
					//echo '<pre>'; echo print_r($array); exit;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{
						$this->PUSH_userlogsID($this->frmID,$ID,$array['dob'],$ID,$array['code'],'','E',($array['address_1'].' '.$array['address_2']),$array);

						if($_POST['status'] == 2)
						{
							$arr = array();
							$arr['isActive'] = 0;
							$ons['driverID'] = $ID;
							$this->BuildAndRunUpdateQuery('users',$arr,$ons);
						}

						if($ID > 0 && $statusID == 2 && ($crleavingID == 1 || $crleavingID == 2 && $crleavingID == 4 && $crleavingID  == 5))
						{
							$arrayUS = $ID > 0 ? $this->select('users',array("*"), " WHERE driverID = ".$ID." ") : '';
							if($arrayUS[0]['ID'] > 0)
							{
									$update = array();
									$update['isActive'] = 0;
									$on['ID'] = $arrayUS[0]['ID'];
									$this->BuildAndRunUpdateQuery('users',$update,$on);
							}
						}
						
						$retrunSTR = '';
						if($_POST['status'] == 2)
						{
							$retrunSTR = $this->transferEmployee($_POST,$paramVal);
						}
						
						$this->msg = urlencode(' Employee Master Is Updated Successfully .<br /> <br /> Employee Code : '.$array['code'].'<br /> Employee Name : '.$array['full_name'].'
						<br /> Telephone No : '.$array['phone_1'].'<br /> Mobile No : '.$array['phone'].'<br /> Address : '.$array['address_1'].' '.$array['address_2'].' '.$array['suburb'].'.'.$retrunSTR);
						
						if($_POST['status'] == 2)		{$param = array('a'=>'create','t'=>'success','m'=>$this->msg);}
						else							{$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);}
						
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
	
	public function transferEmployee($postVAL,$paramVal)
	{
		if(is_array($postVAL) && count($postVAL) > 0 && is_array($paramVal) && count($paramVal) > 0)
		{
			extract($paramVal);
			
			/*
			echo '<pre>'; echo print_r($postVAL);	
			echo '<pre>'; echo print_r($paramVal);
			exit;
			*/
			
			$depotEXP = explode("-",$tdepotTX);
			$companyID  = $depotEXP[0];
			$scompanyID = $depotEXP[1];
			
			/* MAKE A NEW EMPLOYEE  */
			$SQL = "INSERT INTO employee (systemID, code, fname, lname, rfID, dob, genderID, desigID, full_name, phone, phone_1, emailID, address_1, address_2, sid, suburb, pincode, ddlcno, wwcprno, ddlcdt, wwcprdt, ftextID, lardt, arkno, kinname, kincno, casualID, lockerno, esdate, csdate, ftsdate, enddate, drvrightID, rleavingID, resonrgID, status, userID, logID, companyID, scompanyID) 
			SELECT employee.systemID, '".$encodeID."' as codeID, employee.fname, employee.lname, employee.rfID, employee.dob, employee.genderID, employee.desigID, employee.full_name, employee.phone, employee.phone_1, employee.emailID, employee.address_1, employee.address_2, employee.sid, employee.suburb, employee.pincode, employee.ddlcno, employee.wwcprno, 
			employee.ddlcdt, employee.wwcprdt, employee.ftextID, employee.lardt, employee.arkno, employee.kinname, employee.kincno, employee.casualID, employee.lockerno, employee.esdate, employee.csdate, employee.ftsdate, employee.enddate, employee.drvrightID, employee.rleavingID, employee.resonrgID, 1 as statusID, ".$_SESSION[$this->website]['userID']." AS userID, 
			'".date('Y-m-d H:i:s')."' AS logID, ".$companyID." AS cID, ".$scompanyID." AS scID FROM employee WHERE employee.ID = ".$emplyeID." AND employee.companyID = ".$this->companyID." ";			
			$Qry = $this->DB->prepare($SQL); 
			$Qry->execute();

			/* GET LAST GENERATED EMPLOYEE */                
			$EM_Array  = ($emplyeID > 0   ? $this->select('employee',array("*"), " WHERE ID = ".$emplyeID." ") : '');
			$arrTE 	   = $this->select('employee',array("*"), " WHERE systemID = ".$EM_Array[0]['systemID']." Order By ID DESC LIMIT 1 ");
			
			$array = array();
			$array['refDT']  = date('Y-m-d');
			$array['refID']  = $arrTE[0]['ID'];
			$array['status'] = 2;
			$array['rleavingID'] = 3;
			$array['tsystemID']  = $EM_Array[0]['systemID'];
			$on['ID'] = $emplyeID;
			if($this->BuildAndRunUpdateQuery('employee',$array,$on))
			{
				/* EMPLOYEE */
				$arr_1 = array();
				$arr_1['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_1['ID']    = $emplyeID;
				$this->BuildAndRunUpdateQuery('employee',$arr_1,$ons_1);

				/* SICK-LEAVE */
				$arr_2 = array();
				$arr_2['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_2['empID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('sicklv',$arr_2,$ons_2);

				/* PARKING-PERMITS */
				$arr_3 = array();
				$arr_3['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_3['empID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('prpermits',$arr_3,$ons_3);

				/* COMMENT-LINE */
				$arr_4 = array();
				$arr_4['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_4['driverID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('complaint',$arr_4,$ons_4);

				/* INCIDENT-REGISTER */
				$arr_5 = array();
				$arr_5['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_5['driverID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('incident_regis',$arr_5,$ons_5);

				/* ACCIDENT-REGISTER */
				$arr_6 = array();
				$arr_6['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_6['staffID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('accident_regis',$arr_6,$ons_6);

				/* INFIRNGMENTS-REGISTER */
				$arr_7 = array();
				$arr_7['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_7['staffID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('infrgs',$arr_7,$ons_7);

				/* INSPECTION-REGISTER */
				$arr_8 = array();
				$arr_8['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_8['empID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('inspc',$arr_8,$ons_8);

				/* MANAGER-COMMENTS */
				$arr_9 = array();
				$arr_9['tsystemID'] = $EM_Array[0]['systemID'];
				$ons_9['staffID'] = $emplyeID;
				$this->BuildAndRunUpdateQuery('mng_cmn',$arr_9,$ons_9);
				
				/* USER-LOGS */
				$this->PUSH_userlogsID($this->frmID,$arrTE[0]['ID'],date('Y-m-d'),$arrTE[0]['ID'],$arrTE[0]['code'],'','E');
				
				
				
				$arrSD  = $scompanyID > 0 ? $this->select('company_dtls',array("title,pscode"), " WHERE ID = ".$scompanyID." ") : '';
				$arrCD  = $this->select('company',array("title,pscode"), " WHERE ID = ".$companyID." ");
						
				$msgSTR = '';
				$msgSTR .= '<br />-------------------------<br />';
				$msgSTR .= '<b style="color:red;"> Employee Transfer Successfully...</b><br />';
				$msgSTR .= '<b style="color:red;"> New Employee Code : '.$encodeID.'</b><br />';
				$msgSTR .= '<b style="color:red;"> Transfer To Depot : '.$arrCD[0]['title'].' ('.$arrCD[0]['pscode'].')</b><br />';
				$msgSTR .= ($scompanyID > 0 ? '<b style="color:red;"> Transfer To Sub Depot : '.$arrSD[0]['title'].' ('.$arrSD[0]['pscode'].')</b><br />' : '');
			}
		}
		
		return $msgSTR;
	}
}
?>