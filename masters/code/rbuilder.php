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

        $this->frmID	    = '72';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY frmID,ID ASC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>Sr. No.</th>';
                echo '<th>Form</th>';
                echo '<th>Field Name</th>';
                echo '<th>Field Caption</th>';
                echo '<th>Field Type</th>';
                echo '<th>Form Table</th>';
                echo '<th>Joint Table</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th>Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th>Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th>Log</th>' : '');
                echo '</tr></thead>';
                $Start = 1; $uscountsID = 0;
                foreach($this->rows as $row)			
                {
                    echo '<tr>';
                    echo '<td align="center">'.$Start++.'</td>';
                    echo '<td>'.($row['frmID'] == 1 ? 'Employee' 
                               :($row['frmID'] == 2 ? 'Sick Leave' 
                               :($row['frmID'] == 3 ? 'Parking Permits'
                               :($row['frmID'] == 4 ? 'Comment Line' 
                               :($row['frmID'] == 5 ? 'Incident'
                               :($row['frmID'] == 6 ? 'Accident' 
                               :($row['frmID'] == 7 ? 'Infringment'
                               :($row['frmID'] == 8 ? 'Inspection' 
                               :($row['frmID'] == 9 ? 'Manager Comments' 
							   :($row['frmID'] == 10 ? 'HIZ Register' 
							   :($row['frmID'] == 11 ? 'SIR Register' 
							   :($row['frmID'] == 12 ? 'ST Fare Register' 
							   : '')))))))))))).'</td>';
                    echo '<td>'.$row['filedNM'].'</td>';
                    echo '<td>'.$row['filedCP'].'</td>';
                    echo '<td>'.($row['ftypeID'] == 1 ? 'Text-Box' 
                               :($row['ftypeID'] == 2 ? 'Select-Box' 
                               :($row['ftypeID'] == 3 ? 'Check-Box' 
                               :($row['ftypeID'] == 4 ? 'Date-Field' 
                               :($row['ftypeID'] == 5 ? 'Text-Area'
							   :($row['ftypeID'] == 6 ? 'Yes/No' 
							   :($row['ftypeID'] == 7 ? 'Default - Yes/No' 
							   :($row['ftypeID'] == 8 ? 'Yes/No/NA' 
							   : '')))))))).'</td>';
                    echo '<td>'.$row['tableFR'].'</td>';
                    echo '<td>'.$row['tableFL'].'</td>';
                    
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
                        if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
                        {
                            echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Audit-Builders-Form').'" style="text-decoration:none; cursor:pointer;"></a></td>';
                        }
                        else	{echo '<td></td>';}
                    }					
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

    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';

    echo '<div class="row">';		
        echo '<div class="col-xs-4">';
            echo '<label for="section">Form Name <span class="Maindaitory">*</span></label>';
            echo '<select name="frmID" id="FB_frmID" class="form-control">';
            $frmID = !empty($id) ? ($this->result['frmID']) : $this->safeDisplay('frmID');
            echo '<option value="0" selected="selected">-- Select Form Name --</option>';
            echo '<option aria-title="emp" aria-sort="employee" value="1" '.($frmID == 1 ? 'selected="selected"' : '').'>Employee</option>';
            echo '<option aria-title="sicklv" aria-sort="sicklv" value="2" '.($frmID == 2 ? 'selected="selected"' : '').'>Sick Leave</option>';
            echo '<option aria-title="prpermits" aria-sort="prpermits" value="3" '.($frmID == 3 ? 'selected="selected"' : '').'>Parking Permits</option>';
            echo '<option aria-title="cmplnt" aria-sort="complaint" value="4" '.($frmID == 4 ? 'selected="selected"' : '').'>Comment Line</option>';
            echo '<option aria-title="incident" aria-sort="incident_regis" value="5" '.($frmID == 5 ? 'selected="selected"' : '').'>Incident</option>';
            echo '<option aria-title="accident" aria-sort="accident_regis" value="6" '.($frmID == 6 ? 'selected="selected"' : '').'>Accident</option>';
            echo '<option aria-title="infrgs" aria-sort="infrgs" value="7" '.($frmID == 7 ? 'selected="selected"' : '').'>Infringment</option>';
            echo '<option aria-title="inspc" aria-sort="inspc" value="8" '.($frmID == 8 ? 'selected="selected"' : '').'>Inspection</option>';
            echo '<option aria-title="mng_cmn" aria-sort="mng_cmn" value="9" '.($frmID == 9 ? 'selected="selected"' : '').'>Manager Comments</option>';
			
			echo '<option aria-title="hiz_regis" aria-sort="hiz_regis" value="10" '.($frmID == 10 ? 'selected="selected"' : '').'>HIZ Register</option>';
			echo '<option aria-title="sir_regis" aria-sort="sir_regis" value="11" '.($frmID == 11 ? 'selected="selected"' : '').'>SIR Register</option>';
			echo '<option aria-title="stfare_regis" aria-sort="stfare_regis" value="12" '.($frmID == 12 ? 'selected="selected"' : '').'>ST Fare Register</option>';
			
			
            echo '</select>';
            echo '<span id="register_frmID_errorloc" class="errors"></span>';
        echo '</div>';
        
        echo '<div class="col-xs-3">';
                echo '<label for="section">Form Name <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="formNM" name="formNM" placeholder="Enter Form Name" readonly="readonly" 
                value="'.(!empty($id) ? ($this->result['formNM']) : $this->safeDisplay('formNM')).'">';
				echo '<span id="register_formNM_errorloc" class="errors"></span>';
        echo '</div>';
        
        echo '<div class="col-xs-3">';
                echo '<label for="section">Form Table <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="tableFR" name="tableFR" placeholder="Enter Form Table" readonly="readonly" 
                value="'.(!empty($id) ? ($this->result['tableFR']) : $this->safeDisplay('tableFR')).'">';
				echo '<span id="register_tableFR_errorloc" class="errors"></span>';
        echo '</div>';
    echo '</div>';

    echo '<div class="row">';		
        echo '<div class="col-xs-1"></div>';
        
        echo '<div class="col-xs-3">';
                echo '<label for="section">Field Name <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="filedNM" name="filedNM" placeholder="Enter Field Name" 
                value="'.(!empty($id) ? ($this->result['filedNM']) : $this->safeDisplay('filedNM')).'">';
				echo '<span id="register_filedNM_errorloc" class="errors"></span>';
        echo '</div>';
        
        echo '<div class="col-xs-3">';
                echo '<label for="section">Field Caption <span class="Maindaitory">*</span></label>';
                echo '<input type="text" class="form-control" id="filedCP" name="filedCP" placeholder="Enter Field Caption" 
                value="'.(!empty($id) ? ($this->result['filedCP']) : $this->safeDisplay('filedCP')).'">';
				echo '<span id="register_filedCP_errorloc" class="errors"></span>';
        echo '</div>';	
		
        echo '<div class="col-xs-3">';
                echo '<label for="section">Field Type <span class="Maindaitory">*</span></label>';
                echo '<select name="ftypeID" id="ftypeID" class="form-control" >';
                echo '<option value="0" selected="selected">-- Select Field Type --</option>';
                    echo '<option value="1" '.($this->result['ftypeID'] == 1 ? 'selected="selected"' : '').'>Text-Box</option>';
                    echo '<option value="2" '.($this->result['ftypeID'] == 2 ? 'selected="selected"' : '').'>Select-Box</option>';
                    echo '<option value="3" '.($this->result['ftypeID'] == 3 ? 'selected="selected"' : '').'>Check-Box</option>';
                    echo '<option value="4" '.($this->result['ftypeID'] == 4 ? 'selected="selected"' : '').'>Date-Field</option>';
                    echo '<option value="5" '.($this->result['ftypeID'] == 5 ? 'selected="selected"' : '').'>Text-Area</option>';
					echo '<option value="6" '.($this->result['ftypeID'] == 6 ? 'selected="selected"' : '').'>Yes/No</option>';
					echo '<option value="8" '.($this->result['ftypeID'] == 8 ? 'selected="selected"' : '').'>Yes/No/NA</option>';
					echo '<option value="7" '.($this->result['ftypeID'] == 7 ? 'selected="selected"' : '').'>Default Yes/No</option>';					
                echo '</select>';
				echo '<span id="register_ftypeID_errorloc" class="errors"></span>';
        echo '</div>';		
    echo '</div>';

    echo '<div class="row">';		
        echo '<div class="col-xs-1"></div>';
		
        echo '<div class="col-xs-3">';
            echo '<label for="section">Joint Table Name</label>';
            echo '<input type="text" class="form-control" id="tableFL" name="tableFL" placeholder="Enter Joint Table Name" 
            value="'.(!empty($id) ? ($this->result['tableFL']) : $this->safeDisplay('tableFL')).'">';
        echo '</div>';
        
        echo '<div class="col-xs-3">';
            echo '<label for="section">Joint Field Name</label>';
            echo '<input type="text" class="form-control" id="tableFN" name="tableFN" placeholder="Enter Joint Field Name" 
            value="'.(!empty($id) ? ($this->result['tableFN']) : $this->safeDisplay('tableFN')).'">';
        echo '</div>';        
        
        echo '<div class="col-xs-3">';
                echo '<label for="section">Field Alignment</label>';
                echo '<select name="alignFL" id="alignFL" class="form-control" >';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select Field Alignment --</option>';
                    $alignFL = (!empty($id) ? ($this->result['alignFL']) : 1);
                    echo '<option value="1" '.($alignFL == 1 ? 'selected="selected"' : '').'>SET To LEFT SIDE</option>';
                    echo '<option value="2" '.($alignFL == 2 ? 'selected="selected"' : '').'>SET To RIGHT SIDE</option>';
                    echo '<option value="2" '.($alignFL == 2 ? 'selected="selected"' : '').'>SET To CENTER SIDE</option>';
                echo '</select>';
            echo '<span id="register_visibleID_errorloc" class="errors"></span>';
        echo '</div>';
    echo '</div>';
	
	echo '<div class="row">';		
        echo '<div class="col-xs-1"></div>';
		
        echo '<div class="col-xs-9">';
            echo '<label for="section">SQL SYNTAX - Any Special Criteria</label>';
			echo '<textarea style="resize:none;" class="form-control" rows="2" name="sytaxID" id="sytaxID" placeholder="Enter SQL SYNTAX - Any Special Criteria">'.(!empty($id) ? $this->result['sytaxID'] : $this->safeDisplay['sytaxID']).'</textarea>';			
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
            extract($_POST);                    //echo '<PRE>'; echo print_r($_POST); exit;

            if($frmID == '')        $errors .= "Enter The Form Name.<br />";
            if($filedNM == '')      $errors .= "Enter The Field Name.<br />";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }
            else
            {
                unset($_POST['Submit']);
                $array = array();
                foreach($_POST as $key=>$value)
                {
                    $array[$key] = $value;
                }
				$array['srID'] = $this->count_rows($this->tableName, " WHERE ID > 0 AND frmID = ".$frmID." ") + 1;				
                $array['companyID'] = $_SESSION[$this->website]['compID'];
                $array['userID']    = $_SESSION[$this->website]['userID'];
                $array['status']    = 1;
                $array['logID']	= date('Y-m-d H:i:s');
                if($this->BuildAndRunInsertQuery($this->tableName,$array))
                {
                    $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                    $lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A'); 


                    $this->msg = urlencode(' Form-Set Master Is Created (s) Successfully .');
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
            if($this->Form_Variables() == true)     // echo '<pre>'; echo print_r($_POST); exit;
            {	
                    extract($_POST);

                    $errors	=	'';

                    if($frmID == '')        $errors .= "Enter The Form Name.<br />";
                    if($filedNM == '')      $errors .= "Enter The Field Name.<br />";

                    if(!empty($errors))
                    {
                            $this->printMessage('danger',$errors);
                            $this->createForm($ID);
                    }
                    else
                    {						
                        unset($_POST['Submit'],$_POST['ID']);

                        $array = array();
                        foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                        $on['ID'] = $ID;
                        if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                        { 
						
						       $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
							   					
                                $this->msg = urlencode(' Form Name Master Is Updated (s) Successfully .');
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
?>