<?PHP
class Masters extends SFunctions
{
    private $tableName  =   '';
    private $basefile   =   '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	= basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'spare_regis';
        
        $this->frmID        = '82';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($fd,$td,$searchbyID)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $str = "";
            if(!empty($fd) || !empty($td))
            {
                list($fdt,$fm,$fy)	= explode("/",$fd);
                list($tdt,$tm,$ty)	= explode("/",$td);

                $fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
                $td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
            }
            /* DATE - SEARCHING */
            if($fd <> '' && $td <> '')     $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;                
            else							$str .= " AND DATE(dateID) >= '".date('Y-m-d')."' ";

            $SQL = "SELECT  * FROM ".$this->tableName." WHERE ID > 0 AND hiddenID <= 0 AND (companyID In (".$_SESSION[$this->website]['compID'].")) ".$str." ORDER BY dateID DESC ";				
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>Date</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
				echo '<th>Preview</th>';
                echo '</tr></thead>';
                $srID = 1; $uscountsID = 0;
                foreach($this->rows as $row)			
                {
                    echo '<tr>';
                    echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}


                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
                        if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
                        {
                            echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Spare Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
                        }
                        else	{echo '<td></td>';}
                    } 
					
					echo '<td align="center"><a target="_blank" href="'.$this->home.'rpts-c/rpt_spares.php?i='.$this->Encrypt($row['ID']).'" class="fa fa fa-print" style="text-decoration:none; cursor:pointer;"></a></td>';
                    echo '</tr>';
                }
                echo '</table>';			
            } 
        }
        else    {echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Accidents Register</b> Page</div></div>';}
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
                    echo '<label for="section">Date </label>';
                    echo '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="dateID" id="dateID" placeholder="dd/mm/yyyy" style="text-align:center;" 
                    value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
                    echo '<span id="register_dateID_errorloc" class="errors"></span>';
            echo '</div>';

            echo '<div class="col-xs-8"></div>';

            echo '<div class="col-xs-2">';	
                echo '<label for="section">&nbsp;</label><br />';
                if(!empty($id))
                echo '<input name="ID" value="'.$id.'" type="hidden">';
                echo '<button class="btn btn-warning" name="Submit" type="submit">'.(!empty($id) ? 'Update Spare Master' : 'Save Spare Master').'</button>';
            echo '</div>';
    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-12">';
    echo '<div class="nav-tabs-custom">';
    echo '<ul class="nav nav-tabs">';
    echo '<li class="active"><a href="#tab_1" data-toggle="tab"><b>Spare Drivers Lists</b></a></li>';
    echo '<li><a href="#tab_2" data-toggle="tab"><b>Spare Bus No Lists</b></a></li>';
    echo '</ul>';

    /* EMPLOYEE - GRID - DETAILS */
    echo '<div class="tab-content">';                
        echo '<div class="tab-pane active" id="tab_1">';
        echo '<div class="row">';
            echo '<div class="col-xs-8">';
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="13" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Spare Drivers Lists</th>';
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th style="text-align:center !important;"><a style="cursor:pointer; text-decoration:none;" class="fa fa-plus spdrgridID"></a></th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Driver Name</th>';
                /*echo '<th style="text-align:center !important; color:#2F6F95;">Phone NO-1</th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Phone NO-2</th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Location</th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Suburb</th>';*/
                echo '<th style="text-align:center !important; color:#2F6F95;">Available</th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Time</th>';
                echo '</tr></thead>';
                if(!empty($id) && ($id > 0))	
                {
                        $this->createChildForm($id);
                }
                echo '</table>';
            echo '</div>';	  
        echo '</div>';

        echo '</div>';

    /* BUSES - GRID - DETAILS */
        echo '<div class="tab-pane" id="tab_2">';
        echo '<div class="row">';
            echo '<div class="col-xs-5">';
                echo '<table id="dataTables_1" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="2" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Spare Bus No Lists</th>';
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th width="30" style="text-align:center !important;"><a style="cursor:pointer; text-decoration:none;" class="fa fa-plus spbsgridID"></a></th>';
                echo '<th style="text-align:center !important; color:#2F6F95;">Bus No</th>';
                echo '</tr></thead>';
                if(!empty($id) && ($id > 0))	
                {
                        $this->createChildForm_1($id);
                }
                echo '</table>';
            echo '</div>';	 
        echo '</div>';

        echo '</div>';

    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>'; 

    echo '</div>';
    echo '</form>';
    }

    public function createChildForm($ID)
    {
        if(!empty($ID) && ($ID > 0))
        {
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName."_dtl WHERE forID = 1 AND hiddenID <= 0 AND ID = ".$ID." Order By recID ASC  ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                foreach($this->rows as $row)
                {
                    echo '<tr id="'.$row['recID'].'">';
                    echo '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o Delete_Confirm" data-rel="'.$this->frmID.'" data-title="SpareRows" data-ajax="'.$row['recID'].'"></span></td>';

                    echo '<td width="250">';
                    echo '<select class="form-control select2 SP_driverID" style="width:100%;" name="fieldID_1[]">';
                    echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';
                    echo $this->GET_Spares_Employees($row['fieldID_1'],"AND status = 1 AND desigID = 9  ");
                    echo '</select>';
                    echo '</td>';
					
					echo '<input type="hidden" name="emp_signon_statusID[]" value="'.$row['statusID'].'" />';
					
                    echo '<input type="hidden" class="SP_phoneNO" name="fieldID_2[]" placeholder="Phone No-1" value="'.$row['fieldID_2'].'" />';
                    echo '<input type="hidden" class="SP_phoneNO_1" name="fieldID_3[]" placeholder="Phone No-2" value="'.$row['fieldID_3'].'" />';
                    echo '<input type="hidden" class="SP_locationID" name="fieldID_4[]" placeholder="Location" value="'.$row['fieldID_4'].'" />';
                    echo '<input type="hidden" class="SP_suburbsID" name="fieldID_6[]" placeholder="Suburb" value="'.$row['fieldID_5'].'" />';

                    echo '<td width="140">';
                    echo '<select class="form-control SP_avaiableID" style="width:100%; '.(empty($row['fieldID_8']) || ($row['fieldID_8'] == 0) ? 'color:red;' : '').'" name="fieldID_8[]">';
                    echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';                
                    echo '<option value="1" '.($row['fieldID_8'] == 1 ? 'selected="selected"' : '').'>After</option>';
                    echo '<option value="2" '.($row['fieldID_8'] == 2 ? 'selected="selected"' : '').'>Any Time</option>';
                    echo '<option value="3" '.($row['fieldID_8'] == 3 ? 'selected="selected"' : '').'>Available Untill</option>';
                    echo '</select>';
                    echo '</td>';

                    echo '<td width="130"><input type="text" '.($row['fieldID_8'] == 1 || $row['fieldID_8'] == 3 ? '' : 'readonly="readonly"').' class="form-control SP_timeID TPicker" name="fieldID_7[]" placeholder="Time" value="'.$row['fieldID_6'].'"></td>';

                    echo '</tr>';
                }
            }
        }
    }

    public function createChildForm_1($ID)
    {
        if(!empty($ID) && ($ID > 0))
        {
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName."_dtl WHERE forID = 2 AND hiddenID <= 0 AND ID = ".$ID." Order By recID ASC  ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                foreach($this->rows as $row)
                {
                    echo '<tr id="'.$row['recID'].'">';
                    echo '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o Delete_Confirm" data-rel="'.$this->frmID.'" data-title="SpareRows" data-ajax="'.$row['recID'].'"></span></td>';
					
					echo '<input type="hidden" name="bus_signon_statusID[]" value="'.$row['statusID'].'" />';
					
                    echo '<td width="200">';
                    echo '<select class="form-control select2" style="width:100%;" name="fieldID_5[]">';
                    $Qry = $this->DB->prepare("SELECT * FROM buses WHERE ID > 0 Order By busno ASC");
                    if($Qry->execute())
                    {
                        $this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
                        echo '<option value="0" selected="selected" disabled="disabled"> --- Select Bus No --- </option>';
                        foreach($this->crow as $mrow)	
                        {
                            echo '<option value="'.$mrow['ID'].'" '.($row['fieldID_1'] == $mrow['ID'] ? 'selected="selected"' : '').'>'.strtoupper($mrow['busno'].' - '.$mrow['modelno'].' - '.$mrow['title']).'</option>';
                        }
                    }		
                    echo '</select>';
                    echo '</td>';

                    echo '</tr>';
                }
            }
        }
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);                //echo '<PRE>'; echo print_r($_POST); exit;

            if($dateID == '')   $errors .= "Enter The Date.<br>";

            /* CHECK THE DUPLICATE DRIVERS NAME */
            $return_empID = ''; $emp_srID = 1;
            $validID_1 = "";
            foreach(explode(",",($this->CheckArrayDuplicacy($fieldID_1))) as $ret_empID)
            {
                if($ret_empID > 0)
                {
                    $arrayEM = $ret_empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$ret_empID."  ") : '';
                    $validID_1 .= ($emp_srID == 1 ? '-------------- List of Driver Are --------------<br /> ('.$arrayEM[0]['code'].') '.$arrayEM[0]['fname'].' '.$arrayEM[0]['lname'] : '<br /> ('.$arrayEM[0]['code'].') '.$arrayEM[0]['fname'].' '.$arrayEM[0]['lname']);
                    $emp_srID++;
                }
            }

            /* CHECK THE DUPLICATE BUSES NAME */
            $return_busID = ''; $bus_srID = 1;
            $validID_2 = "";
            foreach(explode(",",($this->CheckArrayDuplicacy($fieldID_5))) as $ret_busID)
            {
                if($ret_busID > 0)
                {
                    $arrayBS = $ret_busID > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$ret_busID."  ") : '';
                    $validID_2 .= ($bus_srID == 1 ? '-------------- List of Buses Are --------------<br /> '.strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno'].' - '.$arrayBS[0]['title']) : '<br /> '.strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno'].' - '.$arrayBS[0]['title']));
                    $bus_srID++;
                }
            }

            if($validID_1 <> '')   $errors .= "Sorry, you enter duplicate driver name.<br>".$validID_1;
            if($validID_2 <> '')   $errors .= "Sorry, you enter duplicate bus no.<br>".$validID_2;

            $returnID = "";
            if(is_array($fieldID_1) && count($fieldID_1) > 0)
            {
                $esrID = 1;
                foreach($fieldID_1 as $key=>$driverID)
                {
                    if($driverID > 0)
                    {
                        if((($fieldID_8[$key]) == 1 || ($fieldID_8[$key]) == 3) && empty($fieldID_7[$key]))  
                        {
                            $returnID = $esrID == 1 ? "<b style='color:red;'>Parameter Missings : </b>" : "";
                            $returnID .= "<br />Please make sure to fill the after or until time ";
                            $esrID++;
                        }
                    }
                }
            }

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            } 
            else
            {	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE hiddenID <= 0 AND dateID =:dateID AND companyID = :companyID ");				
				$Qry->bindParam(':dateID',$this->dateFormat($dateID));
				$Qry->bindParam(':companyID',($_SESSION[$this->website]['compID']));
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount = $this->result['resultRows'];
				//echo '<br /> rowCount : '.$rowCount;
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger',' Already exist !...');
					$this->createForm();
				}
				else
				{
					for($srID = 1; $srID <= 10; $srID++)    {unset($_POST['fieldID_'.$srID]);}

					$_POST['companyID'] = $_SESSION[$this->website]['compID'];
					$_POST['dateID']    = $this->dateFormat($_POST['dateID']);
					$_POST['timeID']    = date('h : i : A');
					$_POST['userID']    = $_SESSION[$this->website]['userID'];
					unset($_POST['Submit']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$array['logID'] = date('Y-m-d H:i:s');

					//echo '<PRE>'; echo print_r($array); exit;
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);

						if(is_array($fieldID_1) && count($fieldID_1) > 0 && ($lastID[0] > 0))
						{
							foreach($fieldID_1 as $key=>$fID)
							{
								if($fID > 0)
								{
									$arr = array();
									$arr['ID'] = $lastID[0];
									$arr['fieldID_1'] = $fID;
									$arr['fieldID_2'] = $fieldID_2[$key];
									$arr['fieldID_3'] = $fieldID_3[$key];
									$arr['fieldID_4'] = $fieldID_4[$key];
									$arr['fieldID_5'] = $fieldID_6[$key];
									$arr['fieldID_6'] = $fieldID_7[$key];
									$arr['fieldID_8'] = $fieldID_8[$key];
									$arr['forID'] 	 = 1;									
									$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);
								}
							}
						}

						if(is_array($fieldID_5) && count($fieldID_5) > 0 && ($lastID[0] > 0))
						{
							foreach($fieldID_5 as $key=>$fID)
							{
								if($fID <> '')
								{
									$arr = array();
									$arr['ID'] = $lastID[0];
									$arr['fieldID_1'] = $fID;
									$arr['forID'] 	 = 2;									
									$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);
								}
							}
						}
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],$_POST['dateID'],$lastID[0],$_POST['dateID'],$lastID[0],'A');
						
						$this->msg = urlencode('Spare Master is Created Successfully .<br />'.$returnID);
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
        if($this->Form_Variables() == true)
        {	
            extract($_POST);			//echo '<pre>'; echo print_r($_POST); exit;

            $errors	= '';

            if($dateID == '')   $errors .= "Enter The Date.<br>";

            /* CHECK THE DUPLICATE DRIVERS NAME */
            $return_empID = ''; $emp_srID = 1;
            $validID_1 = "";
            foreach(explode(",",($this->CheckArrayDuplicacy($fieldID_1))) as $ret_empID)
            {
                if($ret_empID > 0)
                {
                    $arrayEM = $ret_empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$ret_empID."  ") : '';
                    $validID_1 .= ($emp_srID == 1 ? '-------------- List of Driver Are --------------<br /> ('.$arrayEM[0]['code'].') '.$arrayEM[0]['fname'].' '.$arrayEM[0]['lname'] : '<br /> ('.$arrayEM[0]['code'].') '.$arrayEM[0]['fname'].' '.$arrayEM[0]['lname']);
                    $emp_srID++;
                }
            }

            /* CHECK THE DUPLICATE BUSES NAME */
            $return_busID = ''; $bus_srID = 1;
            $validID_2 = "";
            foreach(explode(",",($this->CheckArrayDuplicacy($fieldID_5))) as $ret_busID)
            {
                if($ret_busID > 0)
                {
                    $arrayBS = $ret_busID > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$ret_busID."  ") : '';
                    $validID_2 .= ($bus_srID == 1 ? '-------------- List of Buses Are --------------<br /> '.strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno'].' - '.$arrayBS[0]['title']) : '<br /> '.strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno'].' - '.$arrayBS[0]['title']));
                    $bus_srID++;
                }
            }

            if($validID_1 <> '')   $errors .= "Sorry, you enter duplicate driver name.<br>".$validID_1;
            if($validID_2 <> '')   $errors .= "Sorry, you enter duplicate bus no.<br>".$validID_2;

            $returnID = "";
            if(is_array($fieldID_1) && count($fieldID_1) > 0)
            {
                $esrID = 1;
                foreach($fieldID_1 as $key=>$driverID)
                {
                    if($driverID > 0)
                    {
                        if((($fieldID_8[$key]) == 1 || ($fieldID_8[$key]) == 3) && empty($fieldID_7[$key]))
                        {
                            $returnID = $esrID == 1 ? "<b style='color:red;'>Parameter Missings : </b>" : "";
                            $returnID .= "<br />Please make sure to fill after or until time";
                            $esrID++;
                        }
                    }
                }
            }

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            {
                $Qry = $this->DB->prepare("SELECT count(*) as result FROM ".$this->tableName." WHERE hiddenID <= 0 AND dateID =:dateID AND companyID = :companyID AND ID <> :ID ");
                $Qry->bindParam(':dateID',$this->dateFormat($dateID));
                $Qry->bindParam(':companyID',$_SESSION[$this->website]['compID']);
                $Qry->bindParam(':ID',$ID);				
                $Qry->execute();
                $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                $rowCount	 = $this->result['result'];

                if($rowCount > 0 ) 
                {
					$this->printMessage('danger','Already exist !...');
					$this->createForm($ID);
                }
                else
                {
					unset($_POST['bus_signon_statusID'],$_POST['emp_signon_statusID']);
					
                    for($srID = 1; $srID <= 10; $srID++)    {unset($_POST['fieldID_'.$srID]);}

					$_POST['dateID'] = $this->dateFormat($_POST['dateID']);
					unset($_POST['Submit']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$on['ID'] = $ID;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{
						if(is_array($fieldID_1) && count($fieldID_1) > 0 && ($ID > 0))
						{
							$this->delete($this->tableName.'_dtl', " WHERE ID = ".$ID." AND forID = 1 ");

							foreach($fieldID_1 as $key=>$fID)
							{
								if($fID > 0)
								{
									$arr_1 = array();
									$arr_1['ID'] = $ID;
									$arr_1['fieldID_1'] = $fID;
									$arr_1['fieldID_2'] = $fieldID_2[$key];
									$arr_1['fieldID_3'] = $fieldID_3[$key];
									$arr_1['fieldID_4'] = $fieldID_4[$key];
									$arr_1['fieldID_5'] = $fieldID_6[$key];
									$arr_1['fieldID_6'] = $fieldID_7[$key];
									$arr_1['fieldID_8'] = $fieldID_8[$key];
									$arr_1['statusID']  = ($emp_signon_statusID[$key] > 0 ? $emp_signon_statusID[$key] : 0);
									$arr_1['forID'] 	= 1;									
									$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr_1);
								}
							}
						}

						if(is_array($fieldID_5) && count($fieldID_5) > 0 && ($ID > 0))
						{
							$this->delete($this->tableName.'_dtl', " WHERE ID = ".$ID." AND forID = 2 ");

							foreach($fieldID_5 as $key=>$fID)
							{
								if($fID <> '')
								{
									$arr_2 = array();
									$arr_2['ID'] = $ID;
									$arr_2['fieldID_1'] = $fID;
									$arr_2['statusID']  = ($bus_signon_statusID[$key] > 0 ? $bus_signon_statusID[$key] : 0);
									$arr_2['forID'] 	= 2;									
									$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr_2);
								}
							}
						}
						
						$this->PUSH_userlogsID($this->frmID,$ID,$_POST['dateID'],$ID,$_POST['dateID'],$ID,'E');
						
						$this->msg = urlencode(' Spare Master is Updated Successfully .<br />'.$returnID);
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
}
?>