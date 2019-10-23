<?PHP
class Masters extends SFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));
        $this->frmID    	= '48';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY ID DESC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
				echo '<th>Company Code</th>';
                echo '<th>Company Name</th>';				
                echo '<th>Address</th>';
                echo '<th>Suburb</th>';
                echo '<th>Postal Code</th>';
                echo '<th>Contractor</th>';
                echo '<th>Contract</th>';
                echo '<th>Status</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</tr></thead>';
                $Start = 1; $uscountsID = 0; 
                foreach($this->rows as $row)			
                {
                    $CN_Array  = ($row['contractorID'] > 0  ? $this->select('master',array("title"), " WHERE ID = ".$row['contractorID']." ") : '');
                    $CT_Array  = ($row['contractID'] > 0    ? $this->select('master',array("title"), " WHERE ID = ".$row['contractID']." ") : '');

                    echo '<tr>'; 
					echo '<td align="center">'.$row['code'].'</td>';
                    echo '<td>'.$row['title'].'</td>';					
                    echo '<td>'.$row['address'].' , '.$row['address_1'].'</td>';
                    echo '<td>'.$row['suburb'].'</td>';
                    echo '<td align="center">'.$row['pscode'].'</td>';                        
                    echo '<td>'.$CN_Array[0]['title'].'</td>';
                    echo '<td>'.$CT_Array[0]['title'].'</td>';
                    echo '<td align="center" '.($row['status'] == 2 ? 'style="color:red;font-weight:bold;"' :($row['status'] == 1 ? 'style="color:green;font-weight:bold;"' : '')).'>'.($row['status'] == 1 ? 'Active' :($row['status'] == 2 ? 'Sleeping' : '')).'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Company Master').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
                    } 
                    
                }
                echo '</table>';			
            } 
        }
        else
        {
            echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Company Master</b> Page</div></div>';
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

    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';

    echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Company Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="title" name="title" placeholder="Enter Company Name" value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
			echo '<span id="register_title_errorloc" class="errors"></span>';
		echo '</div>';	

		echo '<div class="col-xs-2">';
			echo '<label for="section">Depot Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="code" name="code" placeholder="Enter Depot Code" value="'.(!empty($id) ? ($this->result['code']) : $this->safeDisplay('code')).'">';
			echo '<span id="register_code_errorloc" class="errors"></span>';
		echo '</div>';

            echo '<div class="col-xs-4">';
                echo '<label for="section">Shift Type (Sign On)</label>';
                echo '<select onchange="changes=true;" class="form-control" name="stypeID[]" id="mcompanyID" multiple="multiple">';
                $stypeID = (!empty($id) ? $this->result['stypeID'] : $this->safeDisplay('stypeID'));
                $shtypeID = explode(",",$stypeID);
                    echo '<option value="1" '.(in_array(1,$shtypeID) ? 'selected="selected"' : '').'>SIUO - School In University Out</option>';
                    echo '<option value="2" '.(in_array(2,$shtypeID) ? 'selected="selected"' : '').'>SOUI - School Out University In</option>';
                    echo '<option value="3" '.(in_array(3,$shtypeID) ? 'selected="selected"' : '').'>SOUO - School Out University Out</option>';
                    echo '<option value="4" '.(in_array(4,$shtypeID) ? 'selected="selected"' : '').'>SIUI - School In University In</option>';
                    echo '<option value="5" '.(in_array(5,$shtypeID) ? 'selected="selected"' : '').'>School IN</option>';
                    echo '<option value="6" '.(in_array(6,$shtypeID) ? 'selected="selected"' : '').'>School OUT</option>';
                    echo '<option value="7" '.(in_array(7,$shtypeID) ? 'selected="selected"' : '').'>Saturday</option>';
                    echo '<option value="8" '.(in_array(8,$shtypeID) ? 'selected="selected"' : '').'>Sunday</option>';
                    echo '<option value="9" '.(in_array(9,$shtypeID) ? 'selected="selected"' : '').'>Special Event</option>';
                echo '</select>';
            echo '</div>';
        
            echo '<div class="col-xs-2">';
                    echo '<label for="section">Status</label>';
                    echo '<select onchange="changes=true;" name="status" class="form-control" id="status" >';
                            $status = (!empty($id) ? ($this->result['status']) : $this->safeDisplay('status'));
                            echo '<option value="0" selected="selected"> --- Select --- </option>';
                            echo '<option value="1" '.($status == 1 ? 'selected="selected"' : '').'>Active</option>';
                            echo '<option value="2" '.($status == 2 ? 'selected="selected"' : '').'>Sleeping</option>';
                    echo '</select>';
            echo '</div>';

            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';		
            echo '<div class="col-xs-4">';
                    echo '<label for="section">Address - 1 <span class="Maindaitory">*</span></label>';
                    echo '<textarea onchange="changes=true;" class="form-control" id="address" name="address" placeholder="Enter Address - 1" rows="2" style="resize:none;">'.(!empty($id) ? ($this->result['address']) : $this->safeDisplay('address')).'</textarea>';
					echo '<span id="register_address_errorloc" class="errors"></span>';
					
            echo '</div>';	 

            echo '<div class="col-xs-4">';
                    echo '<label for="section">Address - 2</label>';
                    echo '<textarea onchange="changes=true;" class="form-control" id="address_1" name="address_1" placeholder="Enter Address - 2" rows="2" style="resize:none;">'.(!empty($id) ? ($this->result['address_1']) : $this->safeDisplay('address_1')).'</textarea>';
            echo '</div>'; 

            echo '<div class="col-xs-1"></div>';

            echo '<div class="col-xs-3">';
                    echo '<label for="section">Contractor Name</label>';
                    echo '<select onchange="changes=true;" class="form-control" name="contractorID">';
                    echo '<option value="0" selected="selected" disabled="disabled">-- Select Contractor --</option>';
                    echo $this->GET_Masters((!empty($id) ? $this->result['contractorID'] : $this->safeDisplay['contractorID']),'29');
                    echo '</select>';			
            echo '</div>'; 
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Suburb</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="suburb" name="suburb" placeholder="Enter Suburb" value="'.(!empty($id) ? ($this->result['suburb']) : $this->safeDisplay('suburb')).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">PostCode <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control decimal_places_1 numeric positive" maxlength="4" id="pscode" name="pscode" placeholder="Enter PostCode" value="'.(!empty($id) ? ($this->result['pscode']) : $this->safeDisplay('pscode')).'">';
			echo '<span id="register_pscode_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-3"></div>';

		echo '<div class="col-xs-3">';
			echo '<label for="section">Contract Name</label>';
			echo '<select onchange="changes=true;" class="form-control" name="contractID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Contract --</option>';
			echo $this->GET_Masters((!empty($id) ? $this->result['contractID'] : $this->safeDisplay['contractID']),'28');
			echo '</select>';			
		echo '</div>';
    echo '</div>'; 

    echo '<div class="row">'; 
		echo '<div class="col-xs-2">';
			echo '<label for="section">Date Closer Days <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control decimal_places_1 numeric positive" style="text-align:center;" maxlength="2" id="dcdaysID" name="dcdaysID" placeholder="Enter Date Closer Days" value="'.(!empty($id) ? ($this->result['dcdaysID']) : $this->safeDisplay('dcdaysID')).'">';
			echo '<span id="register_dcdaysID_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Dashboard Slips</label>';
			echo '<select onchange="changes=true;" class="form-control" name="dsnotID" id="dsnotID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				$dsnotID = (!empty($id) ? ($this->result['dsnotID']) : $this->safeDisplay('dsnotID'));
				$dsnotID = ($dsnotID > 0 ? $dsnotID : 2);
				echo '<option value="1" '.($dsnotID == 1 ? 'selected="selected"' : '').'>Active</option>';
				echo '<option value="2" '.($dsnotID == 2 ? 'selected="selected"' : '').'>In-Active</option>';
			echo '</select>';			
			echo '<span id="register_dsnotID_errorloc" class="errors"></span>';
		echo '</div>';  
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>'; 
	
    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
			echo '<input name="ID" value="'.$id.'" type="hidden">';
			echo '<button onclick="changes=false;" class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Master' : 'Save Master').'</button>';
      echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    }

    public function add()
    {	
            if($this->Form_Variables() == true)
            {
                    extract($_POST);		//echo '<PRE>'; echo print_r($_POST); exit;

                    if($title == '') 		$errors .= "Enter The Company Name.<br>";
                    if($address == '') 	  $errors .= "Enter The Company Address Line 1 .<br>";
                    if($pscode == '') 	   $errors .= "Enter The Company Postal Code.<br>";

                    if(!empty($errors))
                    {
                            $this->printMessage('danger',$errors);
                            $this->createForm();
                    }

                    else
                    {	
                            $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title "); 
                            $Qry->bindParam(':title',$title);
                            $Qry->execute();
                            $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                            $rowCount	 = $this->result['resultRows'];

                            if($rowCount > 0 ) 
                            {
                                    $this->printMessage('danger','Already exist');
                                    $this->createForm();
                            }
                            else
                            {
                                    $_POST['stypeID'] = implode(",",$stypeID);
                                
                                    $_POST['userID'] = $_SESSION[$this->website]['userID'];
                                    $_POST['title'] = ucfirst($_POST['title']);
                                    unset($_POST['Submit']);
                                    $array = array();
                                    foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                                    $array['logID']	= date('Y-m-d H:i:s');
                                    if($this->BuildAndRunInsertQuery($this->tableName,$array))
                                    {
                                            $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                                            $lastID = $stmt->fetch(PDO::FETCH_NUM);
											
											$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A');

                                            $this->msg = urlencode(' Company Name Master Is Created (s) Successfully . <br /> Company Name : '.$array['title']);

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
            if($this->Form_Variables() == true)				//echo '<pre>'; echo print_r($_POST); exit;
            {	
                    extract($_POST);

                    $errors	=	'';

                    if($title == '') 		$errors .= "Enter The Company Name.<br>";
                    if($address == '') 	  $errors .= "Enter The Company Address Line 1 .<br>";
                    if($pscode == '') 	   $errors .= "Enter The Company Postal Code.<br>";

                    if(!empty($errors))
                    {
                            $this->printMessage('danger',$errors);
                            $this->createForm($ID);
                    }
                    else
                    {
                            $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND ID <> :ID ");
                            $Qry->bindParam(':title',$title);
                            $Qry->bindParam(':ID',$ID);				
                            $Qry->execute();
                            $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                            $rowCount	 = $this->result['resultRows'];

                            if($rowCount > 0 ) 
                            {
                                    $this->printMessage('danger','Already exist');
                                    $this->createForm($ID);
                            }
                            else
                            {       
                                    $_POST['stypeID'] = implode(",",$stypeID);	
                                    $_POST['title'] = ucfirst($_POST['title']);
                                    unset($_POST['Submit'],$_POST['ID']);

                                    $array = array();
                                    foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                                    $on['ID'] = $ID;																											
                                    if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                                    { 
									       $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
									 					
                                            $this->msg = urlencode(' Company Name Master Is Updated (s) Successfully . <br /> Company Name : '.$array['title']);						
                                            $param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);						
                                            $this->Print_Redirect($param,$this->basefile.'?');								
                                    }
                                    else
                                    { 
                                            $this->msg	=	urlencode('Error In Updation. Please try again...!!!');
                                            $this->printMessage('danger',$this->msg);
                                            $this->createForm($ID);						
                                    } 
                            }
                    }
            }
    }
}
?>