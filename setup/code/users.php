<?PHP
class Masters extends SFunctions
{
    function __construct()
    {
        parent::__construct();

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));   
        $this->frmID	    = '51';             
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],'51');
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 ".($_SESSION[$this->website]['userTY'] == 'AD' ? "" : "AND user_type <> 'AD'")." ORDER BY username ASC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';			
                echo '<thead><tr>';
                echo '<th>User Name</td>';
                echo '<th>Depot</td>';
                echo '<th>User Role</td>';
                echo '<th>S. Permission</td>';
                echo '<th>SP. Till Date</td>';
                echo '<th>Status</td>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</thead></tr>'; 
                foreach($this->rows as $row)			
                { 
                    $arrUS  = $row['uroleID'] > 0   ? $this->select('urole',array("title"), " WHERE ID = ".$row['uroleID']." ")     : '';

                    /* COMPANY - DETAILS */
                    if($row['ID'] > 0 && ($row['companyID'] <> '')) 
                    {
                        $srID = 1;
                        $stID = '';
                        foreach((explode(",", $row['companyID'])) as $companyID)
                        {
							$arrCM  = $companyID > 0 ? $this->select('company',array("title"), " WHERE ID = ".$companyID." ") : '';                                
							$stID .= $srID == 1 ? '<b>'.$srID.'.</b> '.$arrCM[0]['title'] : '<br /><b>'.$srID.'.</b> '.$arrCM[0]['title'];
							$srID++;
                        }
                    }
					
                    foreach((explode(",", $row['companyID'])) as $fall_companyID)
                    {
                        if($_SESSION[$this->website]['compID'] == $fall_companyID)
                        {
                            echo '<tr>';
                            echo '<td>'.$row['username'].'</td>';
                            echo '<td>'.$stID.'</td>'; 
                            echo '<td>'.$arrUS[0]['title'].'</td>';
                            
                            echo '<td align="center">'.($this->VdateFormat($row['tdateID']) <> '00-00-0000' ? $this->ViewPermissions($row['spermissionID']) : '').'</td>';
                            echo '<td align="center"><b>'.($this->VdateFormat($row['tdateID']) <> '00-00-0000' ? $this->VdateFormat($row['tdateID']) : '').'</b></td>';

                            echo '<td align="center" '.($row['isActive'] == 0 ? 'style="color:red;"' :($row['isActive'] == 1 ? 'style="color:green;"' : '')).'><b>'.($row['isActive'] == 0 ? 'InActive' :($row['isActive'] == 1 ? 'Active' : '')).'</b></td>'; 

                            if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                            {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                            if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                            {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}

                            if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                            { 
                                if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
                                {
                                    echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_User Master').'" 
                                    style="text-decoration:none; cursor:pointer;"></a></td>';
                                }
                                else	{echo '<td></td>';}
                            } 
                        }
                    }
				}
				echo '</table>';				
            }
        }
        else	{echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>User\'s Regiter</b> Page</div></div>';}
    }

    public function createForm($id='')
    {
		$this->action = 'add';
		if(!empty($id))
		{
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID =:userID ");
			$Qry->bindParam(':userID',$id);
			$Qry->execute();
			$this->result = $Qry->fetch(PDO::FETCH_ASSOC);						
			$this->action = 'edit';
		}

		echo '<form method="post" name="PUSHFormsData" id="register"  action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" >';
		echo '<div class="box-body" id="fg_membersite">';

		echo '<div class="row">';		
			echo '<div class="col-xs-4">';
				echo '<label for="section">Staff Name</label>';
				echo '<select class="form-control select2" style="width: 100%;" id="driverID" name="driverID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Staff Name --</option>';
				$driverID = !empty($id) ? $this->result['driverID'] : $this->safeDisplay['driverID'];
				$Qry = $this->DB->prepare("SELECT * FROM employee WHERE desigID <> 9 AND status = 1 Order By fname,lname ASC ");
				$Qry->execute();
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				$crtID = '';
				foreach($this->rows as $rows)
				{
					$crtID = ($rows['ID'] == $driverID ? 'selected="selected"' : '');
					echo '<option aria-sort="'.$rows['code'].'" aria-busy="'.$rows['fname'].'"  aria-setsize="'.$rows['lname'].'" aria-valuenow="'.($rows['phone'] <> '' ? $rows['phone'] : $rows['phone_1']).'" aria-disabled="'.$rows['emailID'].'" '.$crtID.' value="'.$rows['ID'].'">'.strtoupper($rows['fname'].' '.$rows['lname']).'</option>';
				}
				echo '</select>';
			echo '</div>';

			echo '<div class="col-xs-2">';
				echo '<label for="section">Staff ID</label>';
				echo '<input type="text" class="form-control" id="dcodeID" name="dcodeID" placeholder="Staff ID" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['dcodeID'] : '').'">';
			echo '</div>'; 			

			echo '<div class="col-xs-2"></div>'; 			

			echo '<div class="col-xs-4">';
			echo '<label for="section">Reporting Depot <span class="Maindaitory">*</span></label>';
			echo '<select class="form-control" name="reportingID[]" id="rpcompanyID" multiple="multiple">';
			$reportingID = (!empty($id) ? $this->result['reportingID'] : $this->safeDisplay('reportingID'));
			$SEC_reportingID = explode(",",$reportingID);
			  $Qry = $this->DB->prepare("SELECT * FROM company WHERE status = 1 Order By title ASC ");
			  $Qry->execute();
			  $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($this->rows as $rows)
			  {
				  echo '<option value="'.$rows['ID'].'" '.(in_array($rows['ID'],$SEC_reportingID) ? 'selected="selected"' : '').'>'.$rows['title'].' - '.$rows['pscode'].'</option>';
			  }
			echo '</select>';
			echo '</div>';
		echo '</div><br />';	

		echo '<div class="row">';	
			echo '<div class="col-xs-4">';
			echo '<label for="section">User Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" name="usernameID" id="usernameID" placeholder="Enter User Name" value="'.(!empty($id) ? $this->result['username'] : $this->safeDisplay('usernameID')).'">';
			echo '<span id="register_usernameID_errorloc" class="errors"></span>';			
			echo '</div>';

			if(empty($id))
			{	
				echo '<div class="col-xs-4">';
				echo '<label for="section">Password <span class="Maindaitory">*</span></label>';
				echo '<input type="text" class="form-control" required="required" id="password" name="password" minlength="8" placeholder="Enter Password" value="'.(!empty($id) ? ($this->result['password']) :$this->safeDisplay('password')).'">';
				echo '</div>';
			}	

			$ia	=	!empty($id) ? ($this->result['isActive']) : $this->safeDisplay('isActive');
			echo '<div class="col-xs-2">';
			echo '<label for="section">User Status</label>';
			echo '<select name="isActive" id="isActive" class="form-control" >';
				echo '<option value="0" '.($ia == 0 ? 'selected="selected"' : '').'>InActive</option>';
				echo '<option value="1" '.($ia == 1 ? 'selected="selected"' : '').'>Active</option>';			
			echo '</select>';			
			echo '</div>';

			echo '<div class="col-xs-2">';
			echo '<label for="section">Mobile No <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" readonly="readonly" id="mobileno" name="mobileno"
			placeholder="Enter Mobile No (max. 15 digits)" value="'.(!empty($id) ? ($this->result['mobileno']) :$this->safeDisplay('mobileno')).'">';
			echo '</div>';
		echo '</div>'; // row2 end		

		echo '<div class="row">';	
			echo '<div class="col-xs-4">';
			echo '<label for="section">First Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" readonly="readonly" id="first_name" maxlength="20" name="first_name" placeholder="Enter First Name" value="'.(!empty($id) ? $this->result['first_name'] : $this->safeDisplay('first_name')).'">';
			echo '<span id="register_first_name_errorloc" class="errors"></span>';			
			echo '</div>';

			echo '<div class="col-xs-4">';
			echo '<label for="section">Last Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" readonly="readonly" id="last_name" maxlength="20" name="last_name" placeholder="Enter Last Name" value="'.(!empty($id) ? ($this->result['last_name']) :$this->safeDisplay('last_name')).'">';
			echo '</div>';

			echo '<div class="col-xs-4">';
			echo '<label for="section">Email ID <span class="Maindaitory">*</span></label>';
			echo '<input type="email" class="form-control" readonly="readonly" id="email" name="email" placeholder="Enter Email ID" value="'.(!empty($id) ? ($this->result['email']) :$this->safeDisplay('email')).'">';
			echo '</div>';			
		echo '</div>'; // row2 end	

		echo '<div class="row">';	 
			$prptypeID = !empty($id) ? $this->result['prtypeID'] : 2;

			echo '<input type="hidden" name="prtypeID" id="prtypeID" value="'.$prptypeID.'" />'; 

			echo '<div class="col-xs-4">';
			echo '<label for="section">User Role Name </label>';
			echo '<select class="form-control" name="uroleID" id="uroleID" '.($prptypeID == 1 || $prptypeID == 2 ? '' : 'disabled="disabled"').'>';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select User Role --</option>';
			  $uroleID = !empty($id) ? $this->result['uroleID'] : $this->safeDisplay['uroleID'];
			  $Qry = $this->DB->prepare("SELECT * FROM urole WHERE ID > 0 ".($_SESSION[$this->website]['userTY'] == 'AD' ? "" : "AND ID <> 5")." 
			  Order By title ASC ");
			  $Qry->execute();
			  $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			  foreach($this->rows as $rows)
			  {
				  echo '<option value="'.$rows['ID'].'" '.($rows['ID'] == $uroleID ? 'selected="selected"' : '').'>'.$rows['title'].'</option>';
			  }
			echo '</select>';
			echo '</div>';

			echo '<div class="col-xs-2">';
			if($id > 0)
			{
				echo '<label for="section">&nbsp;</label><br />';
				echo '<input type="button" class="btn btn-'.($prptypeID == 1 || empty($prptypeID) ? 'disabled' : 'danger').'" aria-busy="'.$uroleID.'" aria-owns="'.'User : '.$this->result['username'].' - '.strtoupper($this->result['first_name'].' '.$this->result['last_name']).'" aria-sort="'.$id.'" id="exprtypeID" value="ADDL. RESPONSIBILITY" />';
			}
			echo '</div>';
			
			echo '<div class="col-xs-2"></div>';
			
			echo '<div class="col-xs-4">';
			echo '<label for="section">Depot Access <span class="Maindaitory">*</span></label>';
			echo '<select class="form-control" name="companyID[]" id="mcompanyID" multiple="multiple">';
			$companyID = (!empty($id) ? $this->result['companyID'] : $this->safeDisplay('companyID'));
			$ecompanyID = explode(",",$companyID);

			$Qry = $this->DB->prepare("SELECT * FROM company WHERE status = 1 Order By title ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			foreach($this->rows as $rows)
			{
				echo '<option value="'.$rows['ID'].'" '.(in_array($rows['ID'],$ecompanyID) ? 'selected="selected"' : '').'>'.$rows['title'].' - '.$rows['pscode'].'</option>';
			}
			echo '</select>';
			echo '</div>';
		echo '</div><br />'; // row2 end
		
		echo '<div id="users_forms_detailsID"></div>';
		
		$this->createChildform($id,$this->result['uroleID']);
		
		echo '<div class="row"><div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div></div>';
		
		echo '<div class="box-footer">';
		if(!empty($id))
			echo '<input name="ID" value="'.$id.'" type="hidden" > ';
			echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">Submit</button>';
		echo '</div>';	

		echo '</div>';
		echo '</form>';	
    }
	
	public function createChildform($userID,$uroleID)
	{
		if($userID > 0 && $uroleID > 0)
		{
			echo '<div class="row">';

			echo '<div class="col-md-3" style="border:#4D4D4D 2px solid; background:#F56954; color:white; border-bottom:none;border-right:none;">'; 
				echo '<h4 style="font-size: 18px; vertical-align: middle; font-family:georgia; text-align: center;">Already Assigned</h4>';
			echo '</div>';

			echo '<div class="col-md-9" style="border:#4D4D4D 2px solid; background:#F56954; color:white; border-bottom:none;">'; 
				echo '<h4 style="font-size: 18px; vertical-align: middle; font-family:georgia; text-align: center;">Pending For Allocation</h4>';
			echo '</div>';

			echo '<div class="col-md-3" style="padding: 0px; overflow-y: scroll; border:#4D4D4D 2px solid; border-right:none; overflow-x: hidden; height: 550px; ">';

				echo '<div class="box box-solid">';
				echo '<div class="box-body">';
				echo '<div class="box-group" id="accordion">';			
					echo $this->urolesSheets($uroleID," <= 0 ",201);			
				echo '</div>';
				echo '</div>';
				echo '</div>';
				
			echo '</div>';

			echo '<div class="col-md-9" style="overflow-y: scroll; border:#4D4D4D 2px solid; overflow-x: hidden; height: 550px; ">';

				echo $this->urolesformsSheets($uroleID,"1,10","Settings",0,$userID);
				echo $this->urolesformsSheets($uroleID,"2","LOV",0,$userID);
				echo $this->urolesformsSheets($uroleID,"3","Masters",0,$userID);
				echo $this->urolesformsSheets($uroleID,"4","Employee",0,$userID);
				echo $this->urolesformsSheets($uroleID,"5","Driver Details",0,$userID);
				echo $this->urolesformsSheets($uroleID,"6","Rostering",0,$userID);
				echo $this->urolesformsSheets($uroleID,"7","All Set Reports",0,$userID);
				echo $this->urolesformsSheets($uroleID,"8","Driver Performance",0,$userID);
				echo $this->urolesformsSheets($uroleID,"9","Driver Signon",0,$userID);
				echo $this->urolesformsSheets($uroleID,"11","Health & Safety",0,$userID);
			echo '</div>';
			echo '</div>';

		}
	}

    public function add()
    {
        if($this->Form_Variables() == true)
        {
            extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;

            $errors	=	'';
            if($usernameID == '')       $errors .= "Enter username.<br>";
            if($first_name == '') 		$errors .= "Enter first name.<br>";
            if(strlen($password) < 8)   $errors .= "Enter The Password Length Minimumm 8 Characters.<br>";
            
            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            } 
            else
            {
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM users WHERE username =:username ");
                $Qry->bindParam(':username',$usernameID);
                $Qry->execute();
                $this->result	= $Qry->fetch(PDO::FETCH_ASSOC);
                $rowCount	= $this->result['resultRows'];

                if($rowCount > 0 ) 
                {
                    $this->printMessage('danger','Username already exist');
                    $this->createForm();
                }
                else
                { 
					 
					$_POST['username'] = $usernameID;
					unset($_POST['usernameID']);
					
                    if($uroleID == 9)
                    {
                        $_POST['companyID'] = '';
                        $_POST['companyID'] = $_SESSION[$this->website]['AllCompID'];
                    }
                    else    {$_POST['companyID'] = implode(",", $_POST['companyID']);}
                    
                    $_POST['reportingID'] = implode(",",$reportingID);                    
                    $_POST['pstexts']  = $_POST['password'];
                    $_POST['password'] = md5($_POST['password']);
                    //$_POST['tdateID']   = $this->dateFormat($_POST['tdateID']);
                    $_POST['user_type']  = 'RT';
                    $_POST['lgtypeID']   = 1;
                    unset($_POST['Submit']);
                    $array = array();
                    foreach($_POST as $key=>$value)	{$array[$key] = $value;}
                    $array['logID']	= date('Y-m-d H:i:s');
					
					if(is_array($code) and count($code) > 0)
					{
						foreach($code as $form)	{unset($array[$form.'-edit'],$array[$form.'-add'],$array[$form.'-view'],$array[$form.'-all']);}						
						unset($array['code']);
					}
					
					//echo '<PRE>'; echo print_r($array); exit;
                    if($this->BuildAndRunInsertQuery($this->tableName,$array))
                    { 
                        $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                        $lastID = $stmt->fetch(PDO::FETCH_NUM);

                        $this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A');
						
						if(is_array($code) and count($code) > 0 && ($lastID[0] > 0))
						{
							foreach($code as $form)
							{	
								$nopID  = 0;
								$nopID  = (((isset($_POST[$form.'-edit']) ? $_POST[$form.'-edit'] : 0) == 0) && 
										   ((isset($_POST[$form.'-add'])  ? $_POST[$form.'-add']  : 0) == 0) && 
										   ((isset($_POST[$form.'-view']) ? $_POST[$form.'-view'] : 0) == 0) && 
										   ((isset($_POST[$form.'-all'])  ? $_POST[$form.'-all']  : 0) == 0) 
										   ? 1: 0);

								if($form > 0 && $nopID <= 0)
								{
									$arrD = array();
									$arrD['ID'] 	 = $lastID[0];
									$arrD['uroleID'] = $uroleID;
									$arrD['frmID']   = $form;
									$arrD['addID']   = (isset($_POST[$form.'-add'])	 ?	$_POST[$form.'-add']	 :	0);
									$arrD['editID']  = (isset($_POST[$form.'-edit'])	?	$_POST[$form.'-edit']	:	0);
									$arrD['delID']   = ($nopID > 0 ? 0 : 1);
									$arrD['viewID']  = (isset($_POST[$form.'-view'])	?	$_POST[$form.'-view']	:	0);
									$arrD['allID']   = (isset($_POST[$form.'-all'])	 ?	$_POST[$form.'-all']	 :	0);
									$arrD['noID']    = $nopID;
									$this->BuildAndRunInsertQuery($this->tableName.'_sub_dtl',$arrD);	
								}
							}
						}
						
                        $this->msg = urlencode('User Is Created(s) Successfully. <br /> User Name : '.$array['username']);
                        $param = array('a'=>'create','t'=>'success','m'=>$this->msg);
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
        if($this->Form_Variables() == true)
        {
            extract($_POST);		//echo '<PRE>'; echo print_r($_POST); exit;

            $errors	=	'';
            if($usernameID == '')          $errors .= "Enter username.<br>";
            if($first_name == '')        $errors .= "Enter first name.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($UsID);
            }
            else
            {
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM users WHERE username =:username AND ID <> :ID ");
                $Qry->bindParam(':username',$usernameID);
                $Qry->bindParam(':ID',$ID);
                $Qry->execute();				
                $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                $rowCount = $this->result['resultRows'];

                if($rowCount > 0)
                {
                    $this->msg  = urlencode('User Name already exists in our Database');
                    $this->printMessage('danger',$this->msg);
                    $this->createForm($UsID);		
                }
                else
                {
					$_POST['username'] = $usernameID;
					unset($_POST['usernameID']);
					
                    /* START -  SET - UNDO PERMISSIONS - DATA */
                    if($uroleID > 0)
                    {
                        $arrUSER  = $ID > 0 ? $this->select('users',array("*"), " WHERE ID = ".$ID." ") : '';
						
						$currents_uroleID = $uroleID;
                        $database_uroleID = $arrUSER[0]['uroleID'];
                        
                        if($currents_uroleID <> $database_uroleID)
                        {
                            $arr = array();
                            $arr['tdateID'] = '0000-00-00';
                            $arr['spermissionID'] = '0';
                            $ons['ID'] = $ID;
                            if($this->BuildAndRunUpdateQuery($this->tableName,$arr,$ons))   {$this->delete('users_dtl', " WHERE ID = ".$ID." ");}
                        }
                    }
                    /* END - SET - UNDO PERMISSIONS - DATA */
                    
                    if($uroleID == 9)
                    {
                        $_POST['companyID'] = '';
                        $_POST['companyID'] = $_SESSION[$this->website]['AllCompID'];
                    }
                    else    {$_POST['companyID'] = implode(",", $_POST['companyID']);}
                    
                    if($isActive == 1)  {$_POST['reportingID'] = implode(",",$reportingID);}   else    {$_POST['reportingID'] = '';}
                    
                    unset($_POST['Submit'],$_POST['ID']);					
                    $array = array();
                    foreach($_POST as $key=>$value)	{$array[$key] = $value;}
                    $on['ID'] = $ID;
					
					if(is_array($code) and count($code) > 0)
					{
						foreach($code as $form)	{unset($array[$form.'-edit'],$array[$form.'-add'],$array[$form.'-view'],$array[$form.'-all']);}						
						unset($array['code']);
					}
					
                    if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                    {		
                        $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
						
						if(is_array($code) and count($code) > 0 && ($ID > 0))
						{
							$this->delete($this->tableName.'_sub_dtl', " WHERE ID = ".$ID." ");
							
							foreach($code as $form)
							{	
								$nopID  = 0;
								$nopID  = (((isset($_POST[$form.'-edit']) ? $_POST[$form.'-edit'] : 0) == 0) && 
										   ((isset($_POST[$form.'-add'])  ? $_POST[$form.'-add']  : 0) == 0) && 
										   ((isset($_POST[$form.'-view']) ? $_POST[$form.'-view'] : 0) == 0) && 
										   ((isset($_POST[$form.'-all'])  ? $_POST[$form.'-all']  : 0) == 0) 
										   ? 1: 0);

								if($form > 0 && $nopID <= 0)
								{
									$arrD = array();
									$arrD['ID'] 	 = $ID;
									$arrD['uroleID'] = $uroleID;
									$arrD['frmID']   = $form;
									$arrD['addID']   = (isset($_POST[$form.'-add'])	 ?	$_POST[$form.'-add']	: 0);
									$arrD['editID']  = (isset($_POST[$form.'-edit']) ?	$_POST[$form.'-edit']	: 0);
									$arrD['delID']   = ($nopID > 0 ? 0 : 1);
									$arrD['viewID']  = (isset($_POST[$form.'-view']) ?	$_POST[$form.'-view']	: 0);
									$arrD['allID']   = (isset($_POST[$form.'-all'])	 ?	$_POST[$form.'-all']	: 0);
									$arrD['noID']    = $nopID;
									$this->BuildAndRunInsertQuery($this->tableName.'_sub_dtl',$arrD);	
								}
							}
						}
						
                        $this->msg = urlencode(' Record(s) Updated Successfully');
                        $param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);
                        $this->Print_Redirect($param,$this->basefile.'?');
                    }
                    else 
                    {
						$this->msg = urlencode('Error In Insertion. Please try again...!!!');
						$this->printMessage('danger',$this->msg);
						$this->createForm($ID);
                    }				
                }				
            }			
        }		
    }	
	
    public function ViewPermissions($permissionID)
    {
        if($permissionID <> '')
        {
            $strID = '';
            $csID = 1;
            $cdID = '';
            foreach((explode(",",$permissionID)) as $day_ID)
            {
				$cdID = $day_ID == 1 ? 'Manager Comments' :($day_ID == 2 ? 'Warning Types' : '');
				
				if($cdID <> '')
				{
					$strID .= $csID == 1 ? '<b>'.$csID.'</b> : '.$cdID : '<br /><b>'.$csID.'</b> : '.$cdID;
					$csID++;
				}
            }
        }
        return $strID;
    }
}
?>