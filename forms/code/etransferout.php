<?PHP
class Masters extends SFunctions
{
    private $tableName = '';
    private $basefile  = '';
    
    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'employee';
        $this->companyID	= $_SESSION[$this->website]['compID'];
        $this->frmID	    = '73';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($searchbyID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {
            /* SEARCH BY  -  OPTIONS */
            $src = "";
            $src = (!empty($searchbyID) && ($searchbyID <> '') ? (((!is_numeric((substr($searchbyID, 0,2)))) ? " AND Concat(employee.fname, ' ', employee.lname) LIKE '%".$searchbyID."%' " : " AND ".$this->tableName.".code LIKE '%".$searchbyID."%' ")) : ("AND status = 2 AND refID > 0 "));
            
            $query = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ".$src." ORDER BY code DESC ");
            if($query->execute())
            {
                $this->rows = $query->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                /*echo '<th>Sr. No.</th>';*/
                echo '<th>E. Code</th>';
                echo '<th>Employee Name</th>';
                echo '<th>Address</th>';
                echo '<th>Suburb</th>';
                echo '<th>PostCode</th>';
                echo '<th>Mobile No</th>';
                echo '<th align="center">Casual/Part Time/Full Time</th>';                
                echo '<th style="text-align:center;">E. Code <br /><b style="color:red;">(New)<b/></th>';
                echo '<th style="text-align:center;">E. Company <br /><b style="color:red;">(New)<b/></th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '<th style="text-align:center;"><b style="color:blue;">Transfer<br />Log<b/></th>';
                echo '</tr></thead>';
                $Start = 1;
                foreach($this->rows as $row)			
                {
                    $arrTE  = ($row['refID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$row['refID']." ") : '');
                    $arrTC  = ($arrTE[0]['companyID'] > 0 ? $this->select('company',array("title"), " WHERE ID = ".$arrTE[0]['companyID']." ") : '');                    
                    $arrSU  = $row['sid'] > 0 ? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['sid']." ") : '';
                    
                    $adds = '';
                    $adds .= $row['address_1'] <> '' ? $row['address_1'] : '';
                    $adds .= $row['address_2'] <> '' ? ' , '.$row['address_2'] : '';

                    $phon  = '';
                    $phon .= $row['phone'] <> '' ? $row['phone'] : '';
                    $phon .= ($row['phone_1'] && $row['phone']) ? ',   ' : '';
                    $phon .= $row['phone_1'] <> '' ? $row['phone_1'] : '';

                    echo '<tr>';
                    /*echo '<td align="center">'.$Start++.'</td>';*/
                    echo '<td align="center">'.$row['code'].'</td>';
                    echo '<td>'.$row['full_name'].'</td>';
                    echo '<td>'.$adds.'</td>';
                    echo '<td>'.($row['sid'] > 0 ? $arrSU[0]['title'].'('.$arrSU[0]['pscode'].')' : '').'</td>';
                    echo '<td align="center">'.$row['pincode'].'</td>';
                    echo '<td>'.$phon.'</td>';                     
                    echo '<td>'.($row['casualID'] == 1 ? 'Full Time'  :($row['casualID'] == 2 ? 'Part Time' :($row['casualID'] == 3 ? 'Casual' : ''))).'</td>';
                    echo '<td align="center" style="color:green;"><b>'.$arrTE[0]['code'].'</b></td>';
                    echo '<td align="center" style="color:green;"><b>'.$arrTC[0]['title'].'</b></td>';
                    
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
                        $uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['refID']." AND frmID = ".$this->frmID." ") : '';

                        if($uscountsID > 0)
                        {
                            echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['refID'].'_Employee Transfer Masters').'" style="text-decoration:none; cursor:pointer;"></a></td>';
                        }
                        else
                        {
                            echo '<td></td>';
                        }
                    }
                    
                    echo '<td align="center"><a class="fa fa-copy POPUP_uslogsID" aria-sort="'.('TR-LOG_'.$row['systemID'].'_Employee Transfer').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					
                    echo '</tr>';
                }
                echo '</table>';			
            } 
        }
        else
        {
                echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you dont have permission to view <b>Employee Master</b> Page</div></div>';
        }
    } 

    public function createForm($id='')
    {
        $this->action = 'add';
        if(!empty($id))
        {
            $query = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID=:ID ");
            $query->bindParam(':ID',$id);
            $query->execute();
            $this->result = $query->fetch(PDO::FETCH_ASSOC);			
            $this->action = 'edit';
        }

    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';

    echo '<div class="row">'; 
        echo '<div class="col-xs-7">';
            echo '<label for="section">Employee Name / Employee Code <span class="Maindaitory">*</span></label>';
            echo '<select class="form-control select2" style="width: 100%;" name="o_codeID" id="o_codeID">';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select Employee Name / Employee Code --</option>';
                echo $this->GET_Employees11(0,"AND status = 1 AND companyID In(".($this->companyID).") ");
                echo '<span id="register_o_codeID_errorloc" class="errors"></span>';
            echo '</select>';
        echo '</div>';
    echo '</div><br />';	

    echo '<div class="row">';
        echo '<div class="col-xs-1"></div>';
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">New Code <span class="Maindaitory">*</span></label>';
            echo '<input type="text" class="form-control" name="n_codeID" id="n_codeID" placeholder="E. New Code" style="text-align:center;" required="required">';
            echo '<span id="register_n_codeID_errorloc" class="errors"></span>';
        echo '</div>'; 

        echo '<div class="col-xs-4">';
            echo '<label for="section">Transfer To Depot</label>';
            echo '<select class="form-control select2" style="width: 100%;" id="transferID" name="transferID">';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select Transfer To Depot --</option>';
                $Qry = $this->DB->prepare("SELECT * FROM company WHERE ID > 0 AND status = 1 AND ID <> ".$this->companyID." Order By title ASC ");
                $Qry->execute();
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                foreach($this->rows as $row)
                {
                    echo '<option value="'.$row['ID'].'">'.$row['title'].' - '.$row['pscode'].'</option>';
                }
            echo '</select>';
            echo '<span id="register_transferID_errorloc" class="errors"></span>';
        echo '</div>'; 
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';		
    echo '</div>';
    
    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
        echo '<input name="ID" value="'.$id.'" type="hidden">';
        echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">Transfer Employee Master</button>';
      echo '</div>';
      
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
            extract($_POST);         //echo '<PRE>'; echo print_r($_POST); exit;

            if($o_codeID == '')     $errors .= "Select The Employee Name / Employee Code.<br>";
            if($n_codeID == '')     $errors .= "Enter The Employee New Code.<br>";                    
            if($transferID == 0)    $errors .= "Select The Transfer To Depot.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }
            else
            {
                /* UPDATE - SQL */
                $SQL = "INSERT INTO employee (systemID, code, fname, lname, rfID, dob, genderID, desigID, full_name, phone, phone_1, emailID, address_1, address_2, sid, suburb, pincode, ddlcno, wwcprno, ddlcdt, wwcprdt, ftextID, lardt, arkno, kinname, kincno, casualID, lockerno, esdate, csdate, ftsdate, enddate, drvrightID, rleavingID, resonrgID, status, userID, logID, companyID) 
				SELECT '".$this->get_systemID($o_codeID)."' as smID, '".$n_codeID."' as codeID, employee.fname, employee.lname, employee.rfID, employee.dob, employee.genderID, employee.desigID, employee.full_name, employee.phone, employee.phone_1, employee.emailID, employee.address_1, employee.address_2, employee.sid, employee.suburb, employee.pincode, employee.ddlcno, employee.wwcprno, employee.ddlcdt, employee.wwcprdt, employee.ftextID, employee.lardt, employee.arkno, employee.kinname, employee.kincno, employee.casualID, employee.lockerno, employee.esdate, employee.csdate, employee.ftsdate, employee.enddate, employee.drvrightID, employee.rleavingID, employee.resonrgID, employee.status, ".$_SESSION[$this->website]['userID']." AS userID, '".date('Y-m-d H:i:s')."' AS logID, ".$transferID." AS cID FROM employee WHERE employee.ID = ".$o_codeID." AND employee.companyID = ".$this->companyID." ";
                $Qry = $this->DB->prepare($SQL); 
                $Qry->execute();
				
                /* GET LAST GENERATED EMPLOYEE */                
                $EM_Array  = ($o_codeID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$o_codeID." ") : '');
                $arrTE  = ($n_codeID <> ''  ? $this->select('employee',array("*"), " WHERE code = '".$n_codeID."' AND systemID = ".$EM_Array[0]['systemID']." ") : '');
				
				$array = array();
                $array['refDT']  = date('Y-m-d');
                $array['refID']  = $arrTE[0]['ID'];
                $array['status'] = 2;
                $array['rleavingID'] = 3;
                $array['tsystemID']  = $EM_Array[0]['systemID'];
                $on['ID'] = $o_codeID;
                //echo '<PRE>'; echo print_r($array);     
				//echo '<PRE>'; echo print_r($on);
                if($this->BuildAndRunUpdateQuery('employee',$array,$on))
                {
                    /* EMPLOYEE */
                    $arr_1 = array();
                    $arr_1['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_1['ID']    = $o_codeID;
                    $this->BuildAndRunUpdateQuery('employee',$arr_1,$ons_1);
                    
                    /* SICK-LEAVE */
                    $arr_2 = array();
                    $arr_2['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_2['empID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('sicklv',$arr_2,$ons_2);
                    
                    /* PARKING-PERMITS */
                    $arr_3 = array();
                    $arr_3['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_3['empID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('prpermits',$arr_3,$ons_3);
                    
                    /* COMMENT-LINE */
                    $arr_4 = array();
                    $arr_4['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_4['driverID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('complaint',$arr_4,$ons_4);
                    
                    /* INCIDENT-REGISTER */
                    $arr_5 = array();
                    $arr_5['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_5['driverID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('incident_regis',$arr_5,$ons_5);
                    
                    /* ACCIDENT-REGISTER */
                    $arr_6 = array();
                    $arr_6['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_6['staffID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('accident_regis',$arr_6,$ons_6);
                    
                    /* INFIRNGMENTS-REGISTER */
                    $arr_7 = array();
                    $arr_7['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_7['staffID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('infrgs',$arr_7,$ons_7);
                    
                    /* INSPECTION-REGISTER */
                    $arr_8 = array();
                    $arr_8['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_8['empID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('inspc',$arr_8,$ons_8);
                    
                    /* MANAGER-COMMENTS */
                    $arr_9 = array();
                    $arr_9['tsystemID'] = $EM_Array[0]['systemID'];
                    $ons_9['staffID'] = $o_codeID;
                    $this->BuildAndRunUpdateQuery('mng_cmn',$arr_9,$ons_9);
                    
                    /* USER-LOGS */
                    $this->PUSH_userlogsID($this->frmID,$arrTE[0]['ID'],date('Y-m-d'),$arrTE[0]['ID'],$arrTE[0]['code'],'','E');
					
					$this->msg = urlencode(' Employee Transfered Successfully .<br /><br /> Employee New Code : '.$n_codeID);                    
                    $param = array('a'=>'view','t'=>'success','m'=>$this->msg);
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
?>