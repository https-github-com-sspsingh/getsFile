<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		

		$this->basefile	  	= basename($_SERVER['PHP_SELF']);		
		$this->tableName  	= 'mng_cmn';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->frmID	    = '45';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view($fd,$td,$searchbyID,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if($auditID <> '')
			{
				$str = " AND mng_cmn.ID In(".$auditID.") ";
			}
			else
			{
				if(!empty($fd) || !empty($td))
				{
					list($fdt,$fm,$fy)	=	explode("/",$fd);
					list($tdt,$tm,$ty)	=	explode("/",$td);
					
					$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
					$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
				}
				
				/* START - DATE - SEARCHING */		
				if($fd <> '' && $td <> '')                          		 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src; 
				else     $str .= " AND DATE(dateID) BETWEEN '".(date("Y-m-d", strtotime(date('Y-m-d').'-10Days')))."' AND '".date("Y-m-d")."' ";
				/* END - DATE - SEARCHING */
				
				/* START - SEARCH BY  -  OPTIONS */
				$src = "";
				$tsystemID  = $this->filter_employee_systemID($searchbyID);
				
				if($tsystemID <> '')
				{
					$src .= " AND (".$this->tableName.".tsystemID In(".$tsystemID.") Or ".$this->tableName.".systemID In(".$tsystemID.")) ";
				}
				else
				{
					$retID = $this->CheckIntOrStrings($searchbyID);
					
					$src .= $retID == 1 ? "AND (Concat(employee.fname,' ', employee.lname) LIKE '%".$searchbyID."%' " 
							:($retID == 2 ? "AND ".$this->tableName.".scodeID LIKE '%".$searchbyID."%'" : "");
					$src .= " AND ".$this->tableName.".companyID In (".$this->companyID.") ";
				}
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName." LEFT JOIN employee ON employee.ID = ".$this->tableName.".staffID WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".dateID DESC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Sr. No.</th>';
				echo '<th>Date</th>';
				echo '<th>Driver Name</th>';
				echo '<th>Driver ID</th>';
				echo '<th>Interviewed By</th>';
				echo '<th>Description</th>';
				echo '<th>Manager Comments</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">T-Log</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">F-Log</th>' : '');
				echo '</tr></thead>'; 
				foreach($this->rows as $row)			
				{ 
					$arrEM  = $row['staffID'] > 0    ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['staffID']." ") : '';
					$arrIN  = $row['invID'] > 0    ? $this->select('employee',array("fname,lname,code"), " WHERE ID = ".$row['invID']." ") : '';

					echo '<tr>';
					echo '<td>'.$row['ID'].'</td>';
					echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td>'.$arrEM[0]['fname'].' '.$arrEM[0]['lname'].'</td>';
					echo '<td>'.$row['scodeID'].'</td>';
					echo '<td>'.$arrIN[0]['fname'].' '.$arrIN[0]['lname'].' <br /> ('.$arrIN[0]['code'].')</td>';
					echo '<td>'.$this->Word_Wraping($row['description'],95).'</td>';
					echo '<td>'.$this->Word_Wraping($row['mcomments'],85).'</td>';
					
					if(($row['companyID'] == $_SESSION[$this->website]['compID']) && ($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD'))
					{
						echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';
					}
					else	{echo '<td></td>';}

					if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if($row['tsystemID'] > 0 || ((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days'))))	{echo '<td></td>';}	else
						{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Manager Comments Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						echo '<td align="center"><a class="fa fa fa-clipboard POPUP_fieldsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Manager Comments Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
					}
					
					echo '</tr>';
					
				}
		  
				echo '</table>';			
			} 
		}
		else {echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you don\'t have permission to view <b>Manager Comments Register</b> Page</div></div>';}
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
			echo '<label for="section">Date <span class="Maindaitory">*</span></label>';
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Date" style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : '').'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">Time <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="timeID" name="timeID" placeholder="Enter Time" style="text-align:center;" value="'.(!empty($id) ? $this->result['timeID'] : date('h : i : A')).'">';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-4">';
		echo '<label for="section">Driver Name <span class="Maindaitory">*</span></label>';
		$staffID = !empty($id) ? $this->result['staffID'] : $this->safeDisplay['staffID'];
		$arrDB = $staffID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$staffID." ") : '';
		if($arrDB[0]['status'] == 2)
		{
			echo '<input type="hidden" onchange="changes=true;" class="form-control" readonly="readonly" name="staffID" value="'.$staffID.'">';
			echo '<input type="text" onchange="changes=true;" class="form-control" readonly="readonly" value="'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'">';
		}
		else
		{
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="empID" name="staffID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';                        
			echo $this->GET_Employees($staffID," "); 
			echo '</select>';
			echo '<span id="register_staffID_errorloc" class="errors"></span>';
		}
		echo '</div>';  
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Driver ID</label>';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="ecodeID" name="scodeID" placeholder="Driver ID" readonly="readonly" style="text-align:center;" value="'.(!empty($id) ? $this->result['scodeID'] : $this->safeDisplay['scodeID']).'">';
		echo '</div>';

		echo '<div class="col-xs-2"></div>';

		echo '<div class="col-xs-4">';
            echo '<label for="section">Interviewed By <span class="Maindaitory">*</span></label>';
            $invID = (!empty($id) ? $this->result['invID'] : $this->safeDisplay['invID']);
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="invID" name="invID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Interviewed By --</option>';				
			echo $this->ReportingBundels($invID," AND employee.desigID In (209,208) ");
			echo '</select>';
			echo '<span id="register_invID_errorloc" class="errors"></span>';
		echo '</div>';	

		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div>';
	
	echo '<div class="row">';		
		echo '<div class="col-xs-8">';
			echo '<label for="section">Description <span class="Maindaitory">*</span></label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="4" name="description" id="description" placeholder="Enter Description">'.(!empty($id) ? $this->result['description'] : $this->safeDisplay['description']).'</textarea>';
			echo '<span id="register_description_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2"></div>';
                
            if($this->GET_SinglePermission('2') == 1)
            {
		echo '<div class="col-xs-4">';
			echo '<label for="section">Warning Type <span class="Maindaitory">*</span></label>';
			echo '<select name="wrtypeID" onchange="changes=true;" class="form-control" id="wrtypeID" '.($disciplineID = 1 ? '' : 'disabled="disabled"').'>';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['wrtypeID'] : $this->safeDisplay['wrtypeID']),'23');
			echo '</select>';
			echo '<span id="register_wrtypeID_errorloc" class="errors"></span>';
		echo '</div>';
            }
	echo '</div><br />';
	
        
	echo '<div class="row">';	
        if($this->GET_SinglePermission('1') == 1)
        {
            echo '<div class="col-xs-8">';
			echo '<label for="section">Manager Comments <span class="Maindaitory">*</span></label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="4" name="mcomments" required="required" id="mcomments" placeholder="Enter Manager Comments">'.(!empty($id) ? $this->result['mcomments'] : $this->safeDisplay['mcomments']).'</textarea>';
			echo '<span id="register_mcomments_errorloc" class="errors"></span>';
		echo '</div>';
        }       
            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div><br />';
	
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Report Details' : 'Save Report Details').'</button>';
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
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;
			
			if($staffID == '') 		  $errors .= "Select The Driver Name.<br>";
			if($dateID == '') 	 	  $errors .= "Enter The Date.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{	 
				$_POST['companyID'] = $this->companyID;
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);
				$_POST['userID']  = $_SESSION[$this->website]['userID'];
				unset($_POST['Submit'],$_POST['code']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
				$array['systemID']  = $this->get_systemID($staffID);
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;
				//echo '<PRE>'; echo print_r($_POST); exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],$array['staffID'],$array['scodeID'],'','A',$array['description'],$array);
					
					$this->msg = urlencode(' Report Details Form is Created Successfully.');
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
	
			if($staffID == '') 		  $errors .= "Select The Driver Name.<br>";
			if($dateID == '') 	 	  $errors .= "Enter The Date.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{  
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);
				unset($_POST['Submit'],$_POST['code'],$_POST['ID']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$array['systemID']  = $this->get_systemID($staffID);
				$on['ID'] = $ID;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],$array['staffID'],$array['scodeID'],'','E',$array['description'],$array);
									
					$this->msg = urlencode(' Report Details Form is Updated Successfully.');
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