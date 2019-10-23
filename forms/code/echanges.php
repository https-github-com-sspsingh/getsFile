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
		echo '<div class="col-xs-4">';
			echo '<label for="section">Employee Name <span class="Maindaitory">*</span></label>';
			echo '<select onchange="changes=true;" class="form-control select2" style="width:100%;" id="empID" name="empID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Employee --</option>';                                        
			
			$SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 1 AND companyID In(".($_SESSION[$this->website]['compID']).") Order By fname,lname ASC ";
			$Qry = $this->DB->prepare($SQL);
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			foreach($this->rows as $rows)
			{
				$arrDG  = $rows['desigID'] > 0 ? $this->select('master',array("title"), " WHERE ID = ".$rows['desigID']." ") : '';
				
				echo '<option aria-sort="'.$rows['code'].'" aria-scroll="'.$arrDG[0]['title'].'" aria-title="'.($this->VdateFormat($rows['enddate'])).'" aria-busy="'.($this->VdateFormat(date('Y-m-d',strtotime($rows['enddate'].'+1 Days')))).'" value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].' - '.$rows['code'].'</option>';				
			}
			
			echo '</select>';
			echo '<span id="register_empID_errorloc" class="errors"></span>'; 
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Employee Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="ecodeID" placeholder="Employee Code" readonly="readonly" style="text-align:center;">';
			echo '<span id="register_ecodeID_errorloc" class="errors"></span>';
		echo '</div>';
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Previous Posithion Title</label>';
			echo '<input type="text" class="form-control" name="desigTT" id="desigTT" placeholder="Previous Posithion Title" readonly="readonly">';
			echo '<span id="register_desigTT_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">Final Date of Previous Posithion</label>';
			echo '<input type="text" class="form-control" name="endDT" id="endDT" placeholder="Final Date of Previous Posithion" readonly="readonly" style="text-align:center;">';
			echo '<span id="register_endDT_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-3">';
			echo '<label for="section">First Date of New Posithion <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" name="startDT" id="startDT" placeholder="Final Date of Previous Posithion" style="text-align:center;">';
			echo '<span id="register_startDT_errorloc" class="errors"></span>';
		echo '</div>';
    echo '</div><br />';
	
	echo '<div class="row">';
		echo '<div class="col-xs-7">';
		echo '<div style="margin-left:-2px; min-height:50px; border:solid 2px #F56954; width: 595px; border-radius: 5px; padding:2px; padding-left: 11px; padding-right: 11px;  float:left; margin-right:20px; display:inline-block;">';
		echo '<label style="vertical-align: top; margin-top: -21px; background: black; color: yellow; border: #F56954 2px solid; border-radius: 5px; padding: 2px; padding-right: 2px; padding-left: 2px; padding-left: 11px; padding-right: 11px;">Change of Posithion</label>';
			echo '<div style="display:inline-block; margin-left: -145px; margin-top: 12px;">';
				echo '<span style="font-size: 13px;padding: 12px;"><input style="cursor:pointer;" type="radio" name="rleavingID" class="rleavingCLASS" aria-sort="1" value="1"> <b style="font-size: 14px; color: #804000;">Resigned</b> </span>';
				echo '<span style="font-size: 13px;padding: 12px;"><input style="cursor:pointer;" type="radio" name="rleavingID" class="rleavingCLASS" aria-sort="2" value="2"> <b style="font-size: 14px; color: #804000;">Terminated</b> </span>';
				echo '<span style="font-size: 13px;padding: 12px;"><input style="cursor:pointer;" type="radio" name="rleavingID" class="rleavingCLASS" aria-sort="3" value="3"> <b style="font-size: 14px; color: #804000;">Transferred</b> </span>';
				echo '<span style="font-size: 13px;padding: 12px;"><input style="cursor:pointer;" type="radio" name="rleavingID" class="rleavingCLASS" aria-sort="4" value="4"> <b style="font-size: 14px; color: #804000;">Retired</b> </span>';
				echo '<span style="font-size: 13px;padding: 12px;"><input style="cursor:pointer;" type="radio" name="rleavingID" class="rleavingCLASS" aria-sort="5" value="5"> <b style="font-size: 14px; color: #804000;">Deceased</b> </span>';
			echo '</div>';
			
			echo '<input type="hidden" class="form-control" name="rleavingTX" id="rleavingTX">';			
		echo '</div>';
			echo '<span id="register_rleavingTX_errorloc" class="errors"></span>';
		echo '</div>';
    echo '</div>';
	
	echo '<div id="rleavingDIV"></div>';
	
    echo '<div class="row">';
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
		
      echo '<div class="col-xs-2">';	
      if(!empty($id))
        echo '<input name="ID" value="'.$id.'" type="hidden">';
        echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">Update - Employee Posithion</button>';
      echo '</div>'; 
    echo '</div>';
    echo '</div>';
    echo '</form>';
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);        		//echo '<PRE>'; echo print_r($_POST); exit;
			
			
			if($rleavingID == 1 || $rleavingID == 2 || $rleavingID == 4 || $rleavingID == 5)	/* RESIGNED - TERMINATED - RETIRED - DECEASED */
			{
				$array = array();
				$array['status'] = 2;
				$array['rleavingID'] 	= ($rleavingID > 0 	  ? $rleavingID    : 0);
				$array['terminationID'] = ($terminationID > 0 ? $terminationID : 0);
				$array['termOther']  	= $termOther;
				$on['ID'] = $empID;
				$this->BuildAndRunUpdateQuery('employee',$array,$on);
			}
			else if($rleavingID == 3)	/* TRANSFERRED */
			{
				/* STARTING -- EMPLOYEE TRANSFER - VARIABLES */
					$paramVal = array();
					$paramVal['ecodeTY'] = $_POST['ecodeTY'];			$paramVal['encodeID'] = $_POST['encodeID'];
					$paramVal['tdepotID'] = $_POST['tdepotID'];			$paramVal['tdepotTX'] = $_POST['tdepotID'];
					$paramVal['emplyeID'] = $_POST['empID'];			$paramVal['estartDT'] = $_POST['startDT'];
				/* ENDINGS -- EMPLOYEE TRANSFER - VARIABLES */

				$array = array();
				$array['status'] = 2;
				$array['rleavingID'] 	= ($rleavingID > 0 	  ? $rleavingID    : 0);
				$array['terminationID'] = ($terminationID > 0 ? $terminationID : 0);
				$array['termOther']  	= $termOther;
				$on['ID'] = $empID;
				//echo '<PRE>'; echo print_r($array); exit;
				$this->BuildAndRunUpdateQuery('employee',$array,$on);
				
				$retrunSTR = '';
				$retrunSTR = $this->transferEmployee($paramVal);
			}
			
			$this->msg = urlencode(' Employee Change Posithion Is Updated Successfully .'.$retrunSTR);                    
			$param = array('a'=>'create','t'=>'success','m'=>$this->msg);
			$this->Print_Redirect($param,$this->basefile.'?');	 
        }
    } 
	
	public function transferEmployee($paramVal)
	{
		if(is_array($paramVal) && count($paramVal) > 0)
		{
			extract($paramVal);						//	echo '<pre>'; echo print_r($paramVal);	exit;	
			
			$depotEXP = explode("-",$tdepotTX);
			$companyID  = $depotEXP[0];
			$scompanyID = $depotEXP[1];
			
			/* MAKE A NEW EMPLOYEE  */
			$SQL = "INSERT INTO employee (systemID, code, fname, lname, rfID, dob, genderID, desigID, full_name, phone, phone_1, emailID, address_1, address_2, sid, suburb, pincode, ddlcno, wwcprno, ddlcdt, wwcprdt, ftextID, lardt, arkno, kinname, kincno, casualID, lockerno, esdate, csdate, ftsdate, enddate, drvrightID, rleavingID, resonrgID, status, userID, logID, companyID, scompanyID) 
			SELECT employee.systemID, '".$encodeID."' as codeID, employee.fname, employee.lname, employee.rfID, employee.dob, employee.genderID, employee.desigID, employee.full_name, employee.phone, employee.phone_1, employee.emailID, employee.address_1, employee.address_2, employee.sid, employee.suburb, employee.pincode, employee.ddlcno, employee.wwcprno, 
			employee.ddlcdt, employee.wwcprdt, employee.ftextID, employee.lardt, employee.arkno, employee.kinname, employee.kincno, employee.casualID, employee.lockerno, '".$this->dateFormat($estartDT)."' as esdateDT, employee.csdate, employee.ftsdate, employee.enddate, employee.drvrightID, employee.rleavingID, employee.resonrgID, 1 as statusID, ".$_SESSION[$this->website]['userID']." AS userID, 
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