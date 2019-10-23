<?PHP
class Masters extends SFunctions
{
    private $tableName	= '';
    private $basefile	= '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	    = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));
        $this->companyID	= $_SESSION[$this->website]['compID']; 
        $this->frmID	    = '38';
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($fd,$td,$searchbyID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {  
            $str = "";
            if(!empty($fd) || !empty($td))
            {
                list($fdt,$fm,$fy) = explode("/",$fd);
                list($tdt,$tm,$ty) = explode("/",$td);

                $fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
                $td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
            }

            if($fd <> '' && $td <> '')	$str .= " AND DATE(sldateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
            elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
            else                        $str .= " AND DATE(sldateID) BETWEEN '".(date("Y-m-d", strtotime(date('Y-m-d').'-10Days')))."' AND '".date("Y-m-d")."' ";
			
            /* SEARCH BY  -  OPTIONS */
            $src = "";
            $tsystemID  = $this->filter_employee_systemID($searchbyID);
			
            if($tsystemID <> '')
            {
                $src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
            }
            else
            {
                $retID = $this->CheckIntOrStrings($searchbyID);

                $src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " :($retID == 2 ? "AND ".$this->tableName.".ecodeID LIKE '%".$searchbyID."%'" : "");
                $src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
            }
			
            $SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".empID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".sldateID DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
                echo '<th>E. Code</th>';
                echo '<th>Employee Name</th>';
                echo '<th>Leave Date</th>';
                echo '<th>Leave Day</th>';
                echo '<th>Leave Type</th>';
                echo '<th>Duration</th>'; 
                echo '<th>Reason</th>';
                echo '<th>Doctor Certificate</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</tr></thead>';
                foreach($this->rows as $row)			
                { 
                    $arrEM  = $row['empID'] > 0    ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['empID']." ") : '';
                    $arrLT  = $row['lvtypeID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$row['lvtypeID']." ") : '';

                    echo '<tr>'; 
                    echo '<td align="center">'.$arrEM[0]['code'].'</td>';
                    echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].'</td>';
                    echo '<td align="center">'.$this->VdateFormat($row['sldateID']).'</td>';
                    echo '<td align="center">'.$this->GetDayLists($row['dayID']).'</td>';
                    echo '<td>'.$arrLT[0]['title'].'</td>';
                    echo '<td align="center">'.$row['duration'].'</td>';
                    echo '<td>'.$this->Word_Wraping($row['reason'],30).'</td>'; 
                    echo '<td align="center"><b>'.($row['doccertID'] == 1 ? 'Yes' : '').'</b></td>';
					
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
						{echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Personal Leave - Application').'" style="text-decoration:none; cursor:pointer;"></a></td>';}
						else	{echo '<td></td>';}
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
    echo '<div class="box-body" id="fg_membersite">';

    echo '<div class="row">'; 
            echo '<div class="col-xs-2">';
				echo '<label for="section">Application Date <span class="Maindaitory">*</span></label>';
				echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
				echo '<span id="register_dateID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<input type="hidden" name="timeID" value="'.(!empty($id) ? $this->result['timeID'] : date('h : i : A')).'" />';
			
            echo '<div class="col-xs-4">';
                    echo '<label for="section">Employee Name <span class="Maindaitory">*</span></label>';
                    $empID = !empty($id) ? $this->result['empID'] : $this->safeDisplay['empID'];
                    $arrDB = $empID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';
                    if($arrDB[0]['status'] == 2)
                    {
                        echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="empID" value="'.$empID.'">';
                        echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
                    }
                    else
                    {
                        echo '<select onchange="changes=true;" class="form-control select2" style="width:100%;" id="empID" name="empID">';
                        echo '<option value="0" selected="selected" disabled="disabled">-- Select Employee --</option>';                                        
                        echo $this->GET_Employees($empID,'');
                        echo '</select>';
                        echo '<span id="register_empID_errorloc" class="errors"></span>';
                    }
            echo '</div>'; 

            echo '<div class="col-xs-2">';
				echo '<label for="section">Employee Code <span class="Maindaitory">*</span></label>';
				echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="ecodeID" placeholder="Employee Code" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['ecodeID'] : $this->safeDisplay['ecodeID']).'">';
				echo '<span id="register_ecodeID_errorloc" class="errors"></span>';
            echo '</div>';
    echo '</div>';

    echo '<div class="row">'; 
		echo '<div class="col-xs-12">';
			echo '<h3 class="knob-labels notices" style="font-weight:600; font-size:14px; text-align:left;">Personal Leave Details : </h3>';
		echo '</div>'; 	
    echo '</div><br />';

    echo '<div class="row">'; 
            echo '<div class="col-xs-2">';
				echo '<label for="section">Commencement Date <span class="Maindaitory">*</span></label>';
				echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="sldateID" name="sldateID" placeholder="Enter Date" required="required" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['sldateID']) : $this->VdateFormat($this->safeDisplay['sldateID'])).'">';
				echo '<span id="register_sldateID_errorloc" class="errors"></span>';
            echo '</div>'; 		

            $dayID = !empty($id) ? ($this->result['dayID']) : $this->safeDisplay('dayID');
            echo '<div class="col-xs-2">';
                    echo '<label for="section">Commencement Day <span class="Maindaitory">*</span></label>';					
                    $dayNM = $dayID == '1' ? 'Monday' :($dayID == '2' ? 'Tuesday'  :($dayID == '3' ? 'Wednesday' :($dayID == '4' ? 'Thursday' 
                            :($dayID == '5' ? 'Friday' :($dayID == '6' ? 'Saturday' :($dayID == '7' ? 'Sunday' 	: ''))))));

                    echo '<input type="hidden" name="dayID" id="dayID" value="'.$dayID.'" />';
                    echo '<input type="text" onchange="changes=true;" class="form-control" id="dayNM" readonly="readonly" style="text-align:center" value="'.$dayNM.'" />';					
                    echo '<span id="register_dayID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-3">';
                    echo '<label for="section">Leave Type <span class="Maindaitory">*</span></label>';
                    echo '<select onchange="changes=true;" class="form-control" id="lvtypeID" name="lvtypeID">';
                            echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                            $lvtypeID = (!empty($id) ? $this->result['lvtypeID'] : $this->safeDisplay['lvtypeID']);
                            echo $this->GET_Masters($lvtypeID,'11');
                    echo '</select>';
					echo '<span id="register_lvtypeID_errorloc" class="errors"></span>';
            echo '</div>';  
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-2">';
                    echo '<label for="section">Duration <span class="Maindaitory">*</span></label>';
                    echo '<input type="text" onchange="changes=true;" class="form-control decimal_places_2 numeric positive" maxlength="4"  id="duration" name="duration" placeholder="Enter Duration" required="required" 
                    style="text-align:center;" value="'.(!empty($id) ? $this->result['duration'] : $this->safeDisplay['duration']).'">';
					echo '<span id="register_duration_errorloc" class="errors"></span>';
            echo '</div>'; 		

            $doccertID = (!empty($id) ? $this->result['doccertID'] : $this->safeDisplay['doccertID']);
            echo '<div class="col-xs-2">';
                    echo '<label for="section">Doctor Certificate</label><br />';
                    echo '<input class="icheckbox_minimal checked" type="checkbox" name="doccertID" value="1" '.($doccertID == 1 ? 'checked="checked"' : '').' />';
            echo '</div>';

            echo '<div class="col-xs-6"></div>';

            echo '<div class="col-xs-2">';
                    echo '<label for="section">Work Related</label>';
                    echo '<select onchange="changes=true;" class="form-control" id="typeID" name="typeID">';
                            $typeID = (!empty($id) ? $this->result['typeID'] : '2');
                            echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                                    echo '<option value="1" '.($typeID == 1 ? 'selected="selected"' : '').'>Yes</option>';
                                    echo '<option value="2" '.($typeID == 2 ? 'selected="selected"' : '').'>No</option>';
                    echo '</select>';
            echo '</div>';
    echo '</div>';

    echo '<div class="row">';
            echo '<div class="col-xs-7">';
                    echo '<label for="section">Reason <span class="Maindaitory">*</span></label>';
                    echo '<textarea onchange="changes=true;" class="form-control" name="reason" rows="2" style="resize:none;" required="required" 
                    placeholder="Reason (Sick Leave) Remarks">'.(!empty($id) ? $this->result['reason'] : $this->safeDisplay['reason']).'</textarea>';
					echo '<span id="register_reason_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
              echo '<input name="ID" value="'.$id.'" type="hidden">';
              echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Sick Leave' : 'Save Sick Leave').'</button>';
      echo '</div>';
      
      echo '<div class="col-xs-2">';
      echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:70px; float:right; display:inline-block" type="button">View All Lists</button></a>';
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

            if($empID == '') 		 $errors .= "Please Select The Employee Name.<br>";
            if($sldateID == '') 	 $errors .= "Enter The Date of Personal-Leave.<br>";
            if($duration == '') 	 $errors .= "Enter The Duration.<br>";
            if($reason == '') 	     $errors .= "Enter The Reason of Leave.<br>";

            if(!empty($errors))
            {
                    $this->printMessage('danger',$errors);
                    $this->createForm();
            } 
            else
            {	 
				$_POST['dayID'] 	= $this->returnDateDayID($_POST['sldateID']);
				$_POST['dateID']   = $this->dateFormat($_POST['dateID']);
				$_POST['sldateID'] = $this->dateFormat($_POST['sldateID']);
				$_POST['companyID'] = $this->companyID;
				
				unset($_POST['Submit']);
				$array = array();					
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['systemID']    = $this->get_systemID($empID);
				$array['status']		 = 1;
				$array['logID']	= date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);	exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['sldateID'],$array['empID'],$array['empID'],'','A',$array['reason'],$array);
					
					$this->msg = urlencode(' Personal Leave Is Created (s) Successfully .
					<br /> Employee Code : '.$array['ecodeID'].'                            
					<br /> Personal Leave Date : '.$sldateID.'
					<br /> Personal Leave Duration : '.$array['duration']);						
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

            if($empID == '') 		  $errors .= "Please Select The Employee Name.<br>";
            if($sldateID == '') 	  $errors .= "Enter The Date of Personal-Leave.<br>";
            if($duration == '') 	  $errors .= "Enter The Duration.<br>";
            if($reason == '')             $errors .= "Enter The Reason of Leave.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            {    
				$_POST['dayID']    = $this->returnDateDayID($_POST['sldateID']);
				$_POST['dateID']   = $this->dateFormat($_POST['dateID']);
				$_POST['sldateID'] = $this->dateFormat($_POST['sldateID']);
				
				unset($_POST['Submit'],$_POST['ID']);

				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']    = $this->get_systemID($empID);
				$on['ID'] = $ID;
				//echo '<PRE>'; echo print_r($array);	exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					$this->PUSH_userlogsID($this->frmID,$ID,$array['sldateID'],$array['empID'],$array['empID'],'','E',$array['reason'],$array);
											
					$this->msg = urlencode('Personal Leave Is Created (s) Successfully .<br /> Employee Code : '.$array['ecodeID'].'<br /> Personal Leave Date : '.$sldateID.'<br /> Personal Leave Duration : '.$array['duration']);
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