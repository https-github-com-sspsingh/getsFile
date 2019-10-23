<?PHP
class Masters extends SFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile		= basename($_SERVER['PHP_SELF']);		
        $this->tableName	= 'shift_masters_dtl';
		$this->frmID		= '82';
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
            if($fd <> '' && $td <> '')     $str .= " AND DATE(createDT) BETWEEN '".$fd."' AND '".$td."' ".$src;                
            else                           $str .= " AND DATE(createDT) BETWEEN '".date('Y-m-d',strtotime(date('Y-m-d').'-7 Days'))."' AND '".date('Y-m-d')."' ";
			
			if($searchbyID <> '' )         $str .= " AND fID_1 LIKE '%".$searchbyID."%' ";
			
            $SQL = "SELECT  * FROM ".$this->tableName." WHERE ID > 0 AND usedBY = 'M' AND companyID In (".$_SESSION[$this->website]['compID'].") ".$str." ORDER BY recID DESC ";
			$Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>Sr. NO.</th>';
                echo '<th>Create Date</th>';
				echo '<th>Applicable Date</th>';
                echo '<th>Shift No</th>';
                echo '<th>Staff</th>';
                echo '<th>Bus No</th>';
                echo '<th>Ontime - A</th>';
                echo '<th>Offtime - A</th>';
                echo '<th>Ontime - B</th>';
                echo '<th>Offtime - B</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Copy To</th>' : '');
                echo '</tr></thead>';
                $srID = 1;$empID = 0; $busNO = 0;
                foreach($this->rows as $row)			
                {
                    $arrIMP  = $this->select('imp_shift_daily',array("*"), " WHERE shiftID = ".$row['ID']." AND shift_recID = ".$row['recID']." 
					AND dateID = '".$row['createDT']."' ");
                    $empID = $arrIMP[0]['fID_018'] > 0 ? $arrIMP[0]['fID_018'] : $arrIMP[0]['fID_013'];
                    $arrEMP  = $empID > 0 ? $this->select('employee',array("*"), " WHERE  ID = ".$empID."  ") : '';
                    
                    $arrayBS  = $arrIMP[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$arrIMP[0]['fID_014']." ") : '';
                    $busNO = $arrIMP[0]['fID_014'] > 0 ? $arrayBS[0]['busno'] : $arrIMP[0]['fID_14'];
                     
                    echo '<tr>';
                    echo '<td align="center">'.$srID++.'</td>';
                    echo '<td align="center">'.$this->VdateFormat($row['createDT']).'</td>';
					echo '<td align="center">'.$this->VdateFormat($row['availDT']).'</td>';

                    echo '<td align="center">'.$row['fID_1'].'</td>';
                    echo '<td>'.($arrEMP[0]['fname'].' '.$arrEMP[0]['lname']).' - '.($arrEMP[0]['code']).'</td>';
                    echo '<td align="center">'.$busNO.'</td>';
                    echo '<td align="center">'.$row['fID_2'].'</td>';
                    echo '<td align="center">'.$row['fID_7'].'</td>';
                    echo '<td align="center">'.$row['fID_9'].'</td>';
                    echo '<td align="center">'.$row['fID_12'].'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['recID']).'" class="fa fa fa-edit"></a></td>';}

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create_copy').'&i='.$this->Encrypt($row['recID']).'" class="fa fa-copy"></a></td>';}

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
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE recID=:ID ");
            $Qry->bindParam(':ID',$id);
            $Qry->execute();
            $this->result = $Qry->fetch(PDO::FETCH_ASSOC);			
            $this->action = 'edit';
        }  


    $IMParr  = $id > 0 ? $this->select('imp_shift_daily',array("*"), " WHERE shift_recID = ".$id."  ") : '';
    $arrayBS = $IMParr[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$IMParr[0]['fID_014']." ") : '';
    $busNO   = $IMParr[0]['fID_014'] > 0 ? $arrayBS[0]['busno'] : $IMParr[0]['fID_14'];
    
    //echo '<h3 class="knob-labels notices" style="margin-top:-5px !important; font-weight:600; font-size:14px; text-align:left;" >Personal Details :</h3>';
    
    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body" id="fg_membersite">';
    
    echo '<input type="hidden" name="shiftID" value="'.$IMParr[0]['shiftID'].'" />';
    echo '<input type="hidden" name="shift_recID" value="'.$IMParr[0]['shift_recID'].'" />';
    echo '<input type="hidden" name="importID" value="'.$IMParr[0]['recID'].'" />';
    

    echo '<div class="row">';
            echo '<div class="col-xs-2">';
				echo '<label for="section">Applicable Date </label>';
				echo '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="dateID" id="dateID" placeholder="dd/mm/yyyy" style="text-align:center;" readonly="readonly" value="'.(!empty($id) ? $this->VdateFormat($this->result['createDT']) : $this->Decrypt($_GET['date'])).'">';
				echo '<span id="register_dateID_errorloc" class="errors"></span>';
            echo '</div>';
            
            echo '<div class="col-xs-2">';
				echo '<label for="section">Shift NO <span class="Maindaitory">*</span></label>';
				echo '<input type="text" class="form-control" name="shiftNO" id="shiftNO" required="required" placeholder="Shift No" value="'.$this->result['fID_1'].'" />';
				echo '<span id="register_shiftNO_errorloc" class="errors"></span>';
            echo '</div>';

            if($this->Decrypt($_GET['companyID']) > 0)
            {
                echo '<input name="companyID" value="'.$this->Decrypt($_GET['companyID']).'" type="hidden">';
            }
            
            if(empty($id))                
            {
                echo '<div class="col-xs-3">';
                    echo '<label for="section">Signon Required For </label>';
                    echo '<select class="form-control" id="day_ID" name="day_ID">';
                    echo '<option value="1">First & Second Half</option>';
                    echo '<option value="2">First Half Only</option>';
                    echo '</select>';
                echo '</div>';
                
                echo '<div class="col-xs-2"></div>';
            }
            else    {echo '<div class="col-xs-5"></div>';}

            echo '<div class="col-xs-3">';	
                    echo '<label for="section">&nbsp;</label><br />';
            if(!empty($id))
              echo '<input name="ID" value="'.$id.'" type="hidden">';
              echo '<button class="btn btn-warning" name="Submit" type="submit">'.(!empty($id) ? 'Update New Shifts' : 'Save New Shifts').'</button>';
            echo '</div>';
    echo '</div><br />';
	
    echo '<h3 class="knob-labels notices" style="margin-top:-5px !important; font-weight:600; font-size:14px; text-align:left;" >First HALF - A</h3>';

    echo '<div class="row">';
        echo '<div class="col-xs-2">';
            echo '<label for="section">ON TIME <span class="Maindaitory">*</span></label>';
            echo '<input type="text" class="form-control" required="required" name="AontimeID" id="AontimeID" placeholder="ON TIME" value="'.$this->result['fID_2'].'" />';
			echo '<span id="register_AontimeID_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">EX DEPOT</label>';
            echo '<input type="text" class="form-control" required="required" name="fID_3" placeholder="EX DEPOT" value="'.$this->result['fID_3'].'" />';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">STOW TIME <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" required="required" name="AstowimeID" id="AstowimeID" placeholder="STOW A" value="'.$this->result['fID_4'].'" />';
			echo '<span id="register_AstowimeID_errorloc" class="errors"></span>';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">OFF TIME</label>';
            echo '<input type="text" class="form-control" required="required" name="fID_7" placeholder="OFF TIME" value="'.$this->result['fID_7'].'" />';
        echo '</div>';
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">LAST TRIP TIME</label>';
            echo '<input type="text" class="form-control" name="fID_5" placeholder="LAST TRIP TIME" value="'.$this->result['fID_5'].'" />';
        echo '</div>';
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">BUS TYPE</label>';
            echo '<input type="text" class="form-control" required="required" name="fID_20" placeholder="BUS TYPE" value="'.$this->result['fID_20'].'" />';
        echo '</div>';
    echo '</div>';
    
    echo '<h3 class="knob-labels notices" style="margin-top:-5px !important; font-weight:600; font-size:14px; text-align:left;" >Second HALF - B</h3>';
    
    echo '<div class="row">'; 
        echo '<div class="col-xs-2">';
			echo '<label for="section">ON TIME</label>';
            echo '<input type="text" class="form-control" name="fID_9" id="fID_9" placeholder="ON TIME" value="'.$this->result['fID_9'].'" />';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">EX DEPOT</label>';
            echo '<input type="text" class="form-control" name="fID_10" placeholder="EX DEPOT" value="'.$this->result['fID_10'].'" />';
        echo '</div>';

        echo '<div class="col-xs-2">';
			echo '<label for="section">STOW TIME</label>';
            echo '<input type="text" class="form-control" name="fID_11" id="fID_11" placeholder="STOW B" value="'.$this->result['fID_11'].'" />';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">OFF TIME</label>';
            echo '<input type="text" class="form-control" name="fID_14" placeholder="OFF TIME" value="'.$this->result['fID_14'].'" />';
        echo '</div>';

        echo '<div class="col-xs-2">';
            echo '<label for="section">LAST TRIP TIME</label>';
            echo '<input type="text" class="form-control" name="fID_12" placeholder="LAST TRIP TIME" value="'.$this->result['fID_12'].'" />';
        echo '</div>';
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">BUS TYPE</label>';
            echo '<input type="text" class="form-control" name="fID_21" placeholder="BUS TYPE" value="'.$this->result['fID_21'].'" />';
        echo '</div>';
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
    
    echo '<div class="row">'; 
        echo '<div class="col-xs-4">';
            echo '<label for="section">Driver Name</label>';
            echo '<select class="form-control select2" style="width: 100%;" id="empID" name="staffID">';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';
                $staffID = !empty($id) ? ($IMParr[0]['fID_018'] > 0 ? $IMParr[0]['fID_018'] : $IMParr[0]['fID_013']) : $this->safeDisplay['staffID'];
                echo $this->GET_Employees11($staffID,"AND status = 1 AND (desigID <> 8 AND desigID <> 302) ");
            echo '</select>';
        echo '</div>';
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">Driver ID</label>';
            echo '<input type="text" class="form-control" id="ecodeID" name="scodeID" placeholder="Driver ID" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? ($IMParr[0]['fID_18'] > 0 ? $IMParr[0]['fID_18'] : $IMParr[0]['fID_13']) : $this->safeDisplay['scodeID']).'">';
        echo '</div>'; 
        
        echo '<div class="col-xs-2">';
            echo '<label for="section">BUS NO</label>';
            echo '<input type="text" class="form-control" name="busID" maxlength="4" placeholder="BUS NO" value="'.$busNO.'" />';
        echo '</div>';
        
        if($id  > 0)
        {
            echo '<div class="col-xs-2"></div>';

            echo '<div class="col-xs-2">';
                echo '<label for="section">Copy Shift To Date </label>';
                echo '<input type="datable" class="form-control datepicker" data-datable="ddmmyyyy" name="copyTO" id="copyTO" placeholder="dd/mm/yyyy" style="text-align:center;">';
                echo '<span id="register_copyTO_errorloc" class="errors"></span>';
            echo '</div>';
        }
        echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
    echo '</div>';
    
    echo '</div>';
    echo '</form>';
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);        //echo '<PRE>'; echo print_r($_POST); exit;

            if($shiftNO == '')       	$errors .= "Enter The Shift No.<br>";
            if($AontimeID == '')        $errors .= "Enter The ON Time.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            } 
            else
            { 
                $_POST['fID_18']  = date('l',strtotime(date('Y-m-d'))) == 'Monday' ? 'M' :(date('l',strtotime(date('Y-m-d'))) == 'Tuesday' ? 'U' :(date('l',strtotime(date('Y-m-d'))) == 'Wednesday' ? 'W' :(date('l',strtotime(date('Y-m-d'))) == 'Thursday' ? 'T' :(date('l',strtotime(date('Y-m-d'))) == 'Friday' ? 'F' :(date('l',strtotime(date('Y-m-d'))) == 'Saturday' ? 'A' :(date('l',strtotime(date('Y-m-d'))) == 'Sunday' ? 'S' : ''))))));
                $_POST['fID_019'] = $day_ID == 1 ? 'Y' : 'N';
                
                $shiftID = 0;   $shift_recID = 0;
                $arrIMP  = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$this->dateFormat($dateID)."' AND companyID = ".$_SESSION[$this->website]['compID']." AND shiftID > 0 AND shift_recID > 0 Order By recID DESC LIMIT 1 ");
                $arrSHF  = $arrIMP[0]['shiftID'] > 0 ? $this->select('shift_masters',array("*"), " WHERE ID = ".$arrIMP[0]['shiftID']." ") : 0;
                
                /* START -  MAKE SHIFT MASTER - DATA */
                $srNO = 0;
                $srNO = $arrSHF[0]['stypeID'] > 0 ? $this->count_rows('shift_masters'," WHERE usedBY = 'M' AND companyID = ".$_SESSION[$this->website]['compID']." AND stypeID = ".$arrSHF[0]['stypeID']." ") : '';
                $srNO = $srNO > 0 ? $srNO + 1001 : 1001;

                $arr_A = array();
                $arr_A['srNO']      = $srNO;
                $arr_A['stypeID']   = ($arrSHF[0]['stypeID'] > 0 ? $arrSHF[0]['stypeID'] : 1);
                $arr_A['usedBY']    = 'M';
                $arr_A['createDT']  = date('Y-m-d');
                $arr_A['availDT']   = $this->dateFormat($dateID);
                $arr_A['companyID'] = $_SESSION[$this->website]['compID'];
                $arr_A['userID']    = $_SESSION[$this->website]['userID'];
                $arr_A['statusID']  = 1;
                $arr_A['logID']     = date('Y-m-d H:i:s');
                //echo '<PRE>'; echo print_r($arr_A); exit;
                if($this->BuildAndRunInsertQuery('shift_masters',$arr_A))
                {
                    $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                    $lastID = $stmt->fetch(PDO::FETCH_NUM);
                    $shiftID = $lastID[0];

                    if($shiftID > 0)
                    {
                        $arr_B = array();
                        $arr_B['ID']        = $shiftID;
                        $arr_B['srNO']      = $arr_A['srNO'];
                        $arr_B['stypeID']   = $arr_A['stypeID'];
                        $arr_B['usedBY']    = 'M';
                        $arr_B['createDT']  = $arr_A['createDT'];
                        $arr_B['availDT']   = $arr_A['availDT'];
                        $arr_B['companyID'] = $arr_A['companyID'];
                        $arr_B['statusID']  = 1;                            
                        $arr_B['fID_1']  = $shiftNO;
                        $arr_B['fID_2']  = $AontimeID;
                        $arr_B['fID_3']  = $fID_3;
                        $arr_B['fID_4']  = $AstowimeID;
                        $arr_B['fID_5']  = $fID_5;
                        $arr_B['fID_6']  = $fID_6;
                        $arr_B['fID_7']  = strtoupper($fID_7);
                        $arr_B['fID_8']  = $fID_8;
                        $arr_B['fID_9']  = $fID_9;
                        $arr_B['fID_10'] = $fID_10;
                        $arr_B['fID_11'] = $fID_11;
                        $arr_B['fID_12'] = $fID_12;
                        $arr_B['fID_13'] = $fID_13;
                        $arr_B['fID_14'] = $fID_14;
                        $arr_B['fID_15'] = $fID_15;
                        $arr_B['fID_16'] = $fID_16;
                        $arr_B['fID_17'] = $fID_17;
                        $arr_B['fID_18'] = strtoupper($fID_18);
                        $arr_B['fID_19'] = $fID_19;
                        $arr_B['fID_20'] = $fID_20;
                        $arr_B['fID_21'] = strtoupper($fID_21);                            
                        $arr_B['fID_019'] = $fID_019;
                        if($this->BuildAndRunInsertQuery('shift_masters_dtl',$arr_B))
                        {
                            $stmtB = $this->DB->query("SELECT LAST_INSERT_ID()");
                            $lastID_B = $stmtB->fetch(PDO::FETCH_NUM);
                            $shift_recID = $lastID_B[0];
                        }
                    }
                }
                /* ENDSS -  MAKE SHIFT MASTER - DATA */
                
                /* START -  MAKE IMPORT DAILY SHEET MASTER - DATA */
                if(($day_ID == 1 || $day_ID == 2) && ($shiftID > 0 &&  $shift_recID > 0 )) /* SET-UP FIRST & SECOND HALF */
                {
                    $Insert_A = array();					
                    $Insert_A['dateID']  = $this->dateFormat($dateID);
                    $Insert_A['companyID'] = $_SESSION[$this->website]['compID'];
                    $Insert_A['tagCD']   = 'A';
                    $Insert_A['fID_1']   = $shiftNO;
                    $Insert_A['fID_13']  = $scodeID;
                    $Insert_A['fID_013'] = $staffID;
                    $Insert_A['fID_14']  = $busID;
                    $Insert_A['fID_4']   = '';
                    $Insert_A['cuttoffID'] = 0;
                    $Insert_A['fID_6']   = '';                        
                    $Insert_A['shiftID'] = $shiftID;
                    $Insert_A['shift_recID'] = $shift_recID;
                    $Insert_A['imp_statusID'] = ($shift_recID > 0 ? 1 : 2);
					$Insert_A['usedBY']  = 'M';
                    $Insert_A['status_ynID']  = 1;
					$Insert_A['statusID']  = 2;
                    $Insert_A['logID'] = date('Y-m-d H:i:s');
                    $this->BuildAndRunInsertQuery('imp_shift_daily',$Insert_A);
                }

                if(($day_ID == 1)  && ($shiftID > 0 &&  $shift_recID > 0)) /* SET-UP SECOND HALF */
                {
                    $Insert_B = array();
                    $Insert_B['dateID']  = $this->dateFormat($dateID);
                    $Insert_B['companyID'] = $_SESSION[$this->website]['compID'];
                    $Insert_B['tagCD']   = 'B';
                    $Insert_B['fID_1']   = $shiftNO;
                    $Insert_B['fID_13']  = $scodeID;
                    $Insert_B['fID_013'] = $staffID;
                    $Insert_B['fID_14']  = $busID;
                    $Insert_B['fID_4']   = '';
                    $Insert_B['cuttoffID'] = ($day_ID == 2 ? 1 : 0);
                    $Insert_B['fID_6']   = '';
                    $Insert_B['shiftID'] = $shiftID;
                    $Insert_B['shift_recID'] = $shift_recID;
                    $Insert_B['imp_statusID'] = ($shift_recID > 0 ? 1 : 2);
					$Insert_B['usedBY']  = 'M';
                    $Insert_B['status_ynID']  = 1;
					$Insert_B['statusID']  = 2;
                    $Insert_B['logID'] = date('Y-m-d H:i:s');
                    $this->BuildAndRunInsertQuery('imp_shift_daily',$Insert_B);
                }

                 /* ENDSS -  MAKE IMPORT DAILY SHEET MASTER - DATA */
                   
                if($shiftID > 0 && $shift_recID > 0)
                {
                    $this->msg = urlencode(' Shifts Data is Created Successfully . <br /> Shift No : '.$shiftNO);
                    $param = array('a'=>'create','t'=>'success','m'=>$this->msg,'date'=>$dateID,'companyID'=>$companyID);
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
        if($this->Form_Variables() == true)         //echo '<pre>'; echo print_r($_POST); exit;
        {	
            extract($_POST);

            $errors	=	'';
            if($shiftNO == '') 		  	    $errors .= "Enter The Shift No.<br>";
            if($AontimeID == '') 	 	    $errors .= "Enter The ON Time.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            { 
                if($shift_recID > 0)
                {
                    $arr_B = array();
                    $arr_B['fID_1']  = $shiftNO;
                    $arr_B['fID_2']  = $AontimeID;
                    $arr_B['fID_3']  = $fID_3;
                    $arr_B['fID_4']  = $AstowimeID;
                    $arr_B['fID_5']  = $fID_5;
                    $arr_B['fID_6']  = $fID_6;
                    $arr_B['fID_7']  = strtoupper($fID_7);
                    $arr_B['fID_8']  = $fID_8;
                    $arr_B['fID_9']  = $fID_9;
                    $arr_B['fID_10'] = $fID_10;
                    $arr_B['fID_11'] = $fID_11;
                    $arr_B['fID_12'] = $fID_12;
                    $arr_B['fID_13'] = $fID_13;
                    $arr_B['fID_14'] = $fID_14;
                    $arr_B['fID_15'] = $fID_15;
                    $arr_B['fID_16'] = $fID_16;
                    $arr_B['fID_17'] = $fID_17;
                    $arr_B['fID_18'] = strtoupper($fID_18);
                    $arr_B['fID_19'] = $fID_19;
                    $arr_B['fID_20'] = $fID_20;
                    $arr_B['fID_21'] = strtoupper($fID_21);                            
                    $arr_B['fID_019'] = $fID_019;
                    $on_B['recID'] = $shift_recID;
					//echo '<pre>'; echo print_r($arr_B); exit;
                    if($this->BuildAndRunUpdateQuery('shift_masters_dtl',$arr_B,$on_B))
                    {
                        if($copyTO <> '' && $importID > 0 && $shiftID > 0 && $shift_recID > 0)
                        {
                            $this->AutoCopyToShifts($copyTO,$importID,$shiftID,$shift_recID);
                        }

                        $this->msg = urlencode(' Shifts Data is Updated Successfully . <br /> Shift No : '.$shiftNO);
                        $param = array('a'=>'view','t'=>'success','m'=>$this->msg);
                        $this->Print_Redirect($param,$this->basefile.'?');								
                    }
                    else
                    {
                        $this->msg = urlencode('Error In Updation. Please try again...!!!');
                        $this->printMessage('danger',$this->msg);
                        $this->createForm($ID);						
                    }
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
    
    
    public function AutoCopyToShifts($copyTO,$importID,$shiftID,$shift_recID)
    {
        if($copyTO <> '' && $importID > 0 && $shiftID > 0 && $shift_recID > 0)
        {
            $arrA  = $shift_recID > 0 ? $this->select('imp_shift_daily',array("*"), " WHERE shift_recID = ".$shift_recID." AND tagCD = 'A' ") : '';
            $arrB  = $shift_recID > 0 ? $this->select('imp_shift_daily',array("*"), " WHERE shift_recID = ".$shift_recID." AND tagCD = 'B' ") : '';
            
            /* GENERATE - SHIFT - MASTERS */
            $arrSHF  = $shiftID > 0 ? $this->select('shift_masters',array("*"), " WHERE ID = ".$shiftID." ") : '';
            $arrSHD  = $shift_recID > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$shift_recID." ") : '';
            
            $srNO = 0;
            $srNO = $this->count_rows('shift_masters'," WHERE usedBY = 'M' AND companyID = ".$_SESSION[$this->website]['compID']." AND stypeID = ".$arrSHF[0]['stypeID']." ");
            $srNO = $srNO > 0 ? $srNO + 1001 : 1001;
            
            $arr_A = array();
            $arr_A['srNO']      = $srNO;
            $arr_A['stypeID']   = $arrSHF[0]['stypeID'];
            $arr_A['usedBY']    = 'M';
            $arr_A['createDT']  = $this->dateFormat($copyTO);
            $arr_A['availDT']   = $this->dateFormat($copyTO);
            $arr_A['companyID'] = $_SESSION[$this->website]['compID'];
            $arr_A['userID']    = $_SESSION[$this->website]['userID'];
            $arr_A['statusID']  = 1;
            $arr_A['logID']     = date('Y-m-d H:i:s');
            //echo '<PRE>'; echo print_r($arr_A); exit;
            if($this->BuildAndRunInsertQuery('shift_masters',$arr_A))
            {
                $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                $lastID = $stmt->fetch(PDO::FETCH_NUM);
                $GEN_shiftID = $lastID[0];
                        
                if($GEN_shiftID > 0)
                {
                    $arr_B = array();
                    $arr_B['ID']        = $GEN_shiftID;
                    $arr_B['srNO']      = $arr_A['srNO'];
                    $arr_B['stypeID']   = $arr_A['stypeID'];
                    $arr_B['usedBY']    = 'M';
                    $arr_B['createDT']  = $this->dateFormat($copyTO);
                    $arr_B['availDT']   = $this->dateFormat($copyTO);
                    $arr_B['companyID'] = $arr_A['companyID'];
                    $arr_B['statusID']  = 1;                            
                    $arr_B['fID_1']  = $arrSHD[0]['fID_1'];
                    $arr_B['fID_2']  = $arrSHD[0]['fID_2'];
                    $arr_B['fID_3']  = $arrSHD[0]['fID_3'];
                    $arr_B['fID_4']  = $arrSHD[0]['fID_4'];
                    $arr_B['fID_5']  = $arrSHD[0]['fID_5'];
                    $arr_B['fID_6']  = $arrSHD[0]['fID_6'];
                    $arr_B['fID_7']  = $arrSHD[0]['fID_7'];
                    $arr_B['fID_8']  = $arrSHD[0]['fID_8'];
                    $arr_B['fID_9']  = $arrSHD[0]['fID_9'];
                    $arr_B['fID_10'] = $arrSHD[0]['fID_10'];
                    $arr_B['fID_11'] = $arrSHD[0]['fID_11'];
                    $arr_B['fID_12'] = $arrSHD[0]['fID_12'];
                    $arr_B['fID_13'] = $arrSHD[0]['fID_13'];
                    $arr_B['fID_14'] = $arrSHD[0]['fID_14'];
                    $arr_B['fID_15'] = $arrSHD[0]['fID_15'];
                    $arr_B['fID_16'] = $arrSHD[0]['fID_16'];
                    $arr_B['fID_17'] = $arrSHD[0]['fID_17'];
                    $arr_B['fID_18'] = $arrSHD[0]['fID_18'];
                    $arr_B['fID_19'] = $arrSHD[0]['fID_19'];
                    $arr_B['fID_20'] = $arrSHD[0]['fID_20'];
                    $arr_B['fID_21'] = $arrSHD[0]['fID_21'];
                    $arr_B['fID_019'] = $arrSHD[0]['fID_019'];
                    if($this->BuildAndRunInsertQuery('shift_masters_dtl',$arr_B))
                    {
                        $stmtB = $this->DB->query("SELECT LAST_INSERT_ID()");
                        $lastID_B = $stmtB->fetch(PDO::FETCH_NUM);
                        $GEN_shift_recID = $lastID_B[0];
                    }
                }
            }
            
            
            if($arrA[0]['recID'] > 0)
            {
                $shift_A = array();
                $shift_A['dateID']  = $this->dateFormat($copyTO);
                $shift_A['companyID'] = $arrA[0]['companyID'];
                $shift_A['tagCD']   = $arrA[0]['tagCD'];
                $shift_A['fID_1']   = $arrA[0]['fID_1'];
                $shift_A['fID_13']  = $arrA[0]['fID_13'];
                $shift_A['fID_013'] = $arrA[0]['fID_013'];
                $shift_A['fID_14']  = $arrA[0]['fID_14'];
                $shift_A['fID_014'] = $arrA[0]['fID_014'];
                $shift_A['fID_4']  = $arrA[0]['fID_4'];
                $shift_A['fID_5']  = $arrA[0]['fID_5'];
                $shift_A['fID_6']  = $arrA[0]['fID_6'];
                $shift_A['fID_7']  = $arrA[0]['fID_7']; 
                $shift_A['shiftID']  = $GEN_shiftID;
                $shift_A['shift_recID']  = $GEN_shift_recID;
				$shift_A['usedBY']  	 = 'M';
                $shift_A['drag_dropID']  = 0;
                $shift_A['drag_statusID'] = 0;                
                $shift_A['imp_statusID'] = 1;
                $shift_A['status_ynID']  = 1;
                $shift_A['statusID']   = 2;
                $shift_A['colorID']    = 0;
                $shift_A['cuttoffID']  = 0;
                $shift_A['busallocID'] = 2;                
                $shift_A['logID']      = date('Y-m-d H:i:s');
                $this->BuildAndRunInsertQuery('imp_shift_daily',$shift_A);
            }
            
            if($arrB[0]['recID'] > 0)
            {
                $shift_B = array();
                $shift_B['dateID']  = $this->dateFormat($copyTO);
                $shift_B['companyID'] = $arrB[0]['companyID'];
                $shift_B['tagCD']   = $arrB[0]['tagCD'];
                $shift_B['fID_1']   = $arrB[0]['fID_1'];
                $shift_B['fID_13']  = $arrB[0]['fID_13'];
                $shift_B['fID_013'] = $arrB[0]['fID_013'];
                $shift_B['fID_14']  = $arrB[0]['fID_14'];
                $shift_B['fID_014'] = $arrB[0]['fID_014'];
                $shift_B['fID_4']   = $arrB[0]['fID_4'];
                $shift_B['fID_5']   = $arrB[0]['fID_5'];
                $shift_B['fID_6']   = $arrB[0]['fID_6'];
                $shift_B['fID_7']   = $arrB[0]['fID_7']; 
                $shift_B['shiftID'] = $GEN_shiftID;
                $shift_B['shift_recID']   = $GEN_shift_recID;
				$shift_B['usedBY']  	  = 'M';
                $shift_B['drag_dropID']   = 0;
                $shift_B['drag_statusID'] = 0;
                $shift_B['imp_statusID']  = 1;
                $shift_A['status_ynID']   = 1;
                $shift_B['statusID']   = 2;
                $shift_B['colorID']    = 0;
                $shift_B['cuttoffID']  = 0;
                $shift_B['busallocID'] = 2;
                $shift_B['logID']      = date('Y-m-d H:i:s');
                $this->BuildAndRunInsertQuery('imp_shift_daily',$shift_B);
            }
        }
    }   
}
?>