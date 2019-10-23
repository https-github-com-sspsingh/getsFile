<?PHP
class Masters extends SFunctions
{
    private $tableName = '';
    private $basefile  = '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	  =	basename($_SERVER['PHP_SELF']);		
        $this->tableName     =	$this->getTableName(basename($_SERVER['PHP_SELF']));

        $this->frmID	    = '49';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY code ASC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
                echo '<th>Form ID</th>';
			    echo '<th>Form Type</th>';
                echo '<th>Form Code</th>';
                echo '<th>Form Name</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo '</tr></thead>';
                $Start = 1;
                foreach($this->rows as $row)			
                {
                    echo '<tr>'; 
                    echo '<td align="center">'.$row['ID'].'</td>';
					echo '<td>'.($row['ftypeID'] == 1 ? 'Settings' :
					 ($row['ftypeID'] == 2 ? 'LOV' : 
					 ($row['ftypeID'] == 3 ? 'Masters' :
					 ($row['ftypeID'] == 4 ? 'Employee' :
					 ($row['ftypeID'] == 5 ? 'Driver Details' :
					 ($row['ftypeID'] == 6 ? 'Rostering' :
					 ($row['ftypeID'] == 7 ? 'All Set Reports' :
					 ($row['ftypeID'] == 8 ? 'Driver Performance' :
					 ($row['ftypeID'] == 9 ? 'Driver Sign On' : 
					 ($row['ftypeID'] == 10 ? 'Audit Trial' :
					 ($row['ftypeID'] == 11 ? 'New Scope'	: ''))))))))))).'</td>';
                    echo '<td>'.$row['code'].'</td>';
                    echo '<td>'.$row['title'].'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
                }
                echo '</table>';			
            } 
        }
        else
        {
            echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
                  Sorry....you haven\'t permission\'s to view <b>Form-Set Master</b> Page</div></div>';
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

    echo '<form method="post" name="PUSHFormsData" id="register"  action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';
	
	 echo '<div class="row">';
	echo '<div class="col-xs-4">';
					echo '<label for="section">Form Type *</label>';
					echo '<select name="ftypeID" class="form-control" id="ftypeID" required="required">';
					echo '<option value="0" selected="selected"> --- Select --- </option>';
					$ftypeID = (!empty($id) ? $this->result['ftypeID'] : $this->safeDisplay('ftypeID'));
					echo '<option value="1" '.($ftypeID == 1 ? 'selected="selected"' : '').'>Settings</option>';
					echo '<option value="2" '.($ftypeID == 2 ? 'selected="selected"' : '').'>LOV</option>';
					echo '<option value="3" '.($ftypeID == 3 ? 'selected="selected"' : '').'>Masters</option>';
					echo '<option value="4" '.($ftypeID == 4 ? 'selected="selected"' : '').'>Employee</option>';
					echo '<option value="5" '.($ftypeID == 5 ? 'selected="selected"' : '').'>Driver Details</option>';
					echo '<option value="11" '.($ftypeID == 11 ? 'selected="selected"' : '').'>New Scope</option>';
					echo '<option value="6" '.($ftypeID == 6 ? 'selected="selected"' : '').'>Rostering</option>';
					echo '<option value="7" '.($ftypeID == 7 ? 'selected="selected"' : '').'>All Set Reports</option>';
					echo '<option value="8" '.($ftypeID == 8 ? 'selected="selected"' : '').'>Driver Performance</option>';
					echo '<option value="9" '.($ftypeID == 9 ? 'selected="selected"' : '').'>Driver Sign On</option>';
					echo '<option value="10" '.($ftypeID == 10 ? 'selected="selected"' : '').'>Audit Trial</option>';					
				echo '</select>';
				echo '<span id="register_ftypeID_errorloc" class="errors"></span>';
			echo '</div>'; 
			 echo '</div>';

    echo '<div class="row">';		
        echo '<div class="col-xs-4">';
                echo '<label for="section">Form Code <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="code" name="code" placeholder="Enter Form Code" 
                value="'.(!empty($id) ? ($this->result['code']) : $this->safeDisplay('code')).'">';
				echo '<span id="register_code_errorloc" class="errors"></span>';
        echo '</div>';
    echo '</div>';

    echo '<div class="row">';		
        echo '<div class="col-xs-4">';
                echo '<label for="section">Form Name <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Form Name" 
                value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
				echo '<span id="register_title_errorloc" class="errors"></span>';
        echo '</div>';	

        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
          echo '<input name="ID" value="'.$id.'" type="hidden">';
          echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Master' : 'Save Master').'</button>';
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

                    if($title == '') 		$errors .= "Enter The Form Name.<br>";
					

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
									$_POST['companyID'] = 1;
                                    unset($_POST['Submit']);
                                    $array = array();
                                    foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                                    if($this->BuildAndRunInsertQuery($this->tableName,$array))
                                    {
                                            $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                                            $lastID = $stmt->fetch(PDO::FETCH_NUM);

                                            $this->msg = urlencode(' Form Name Master Is Created (s) Successfully . <br /> Form Name : '.$array['title']);						
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
            if($this->Form_Variables() == true)	//echo '<pre>'; echo print_r($_POST); exit;
            {	
                    extract($_POST);

                    $errors	=	'';

                    if($title == '') 		$errors .= "Enter The Form Name.<br>";

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
                                    unset($_POST['Submit'],$_POST['ID']);
									$_POST['companyID'] = 1;
									
                                    $array = array();
                                    foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                                    $on['ID'] = $ID;
                                    if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                                    { 					
                                            $this->msg = urlencode(' Form Name Master Is Updated (s) Successfully . <br /> Form Name : '.$array['title']);						
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