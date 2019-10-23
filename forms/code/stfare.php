<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		

		$this->basefile	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = 'stfare_regis';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->cldaysID	    = $_SESSION[$this->website]['cdysID'];
		$this->frmID	    = '129';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view($fd,$td,$searchbyID,$auditID)
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$str = "";
			if(!empty($fd) || !empty($td))
			{
				list($fdt,$fm,$fy)	=	explode("/",$fd);
				list($tdt,$tm,$ty)	=	explode("/",$td);
			
				$fd =	date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
				$td =	date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
			}
			
			if($auditID <> '')
			{
				$src .= " AND ".$this->tableName.".ID In(".$auditID.") ";
			}
			else
			{
				/* DATE - SEARCHING */
				if($fd <> '' && $td <> '' )		 $str .= " AND DATE(dateID) BETWEEN '".$fd."' AND '".$td."' ".$src;		
				elseif(!empty($searchbyID) && (empty($fd) && empty($td)))  $str .= $src;
				else                             $str .= " AND DATE(dateID) BETWEEN '".date('Y-m-d')."' AND '".date('Y-m-d')."' ".$src;
			}
			
			$SQL = "SELECT  ".$this->tableName.".* FROM ".$this->tableName."  WHERE ".$this->tableName.".ID > 0 ".$str." ".$src." ORDER BY ".$this->tableName.".dateID ASC ";
			$Qry = $this->DB->prepare($SQL);
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Shift No</th>';
				echo '<th>Date</th>';
				echo '<th>Depot</th>';
				echo '<th>Route Info</th>';				
				echo '<th>Location</th>';
				echo '<th>Suburb</th>';
				echo '<th>No of Fare Evaders</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">U-Log</th>' : '');
				echo '</tr></thead>';
				$dueDate = '';
				foreach($this->rows as $row)			
				{
					$arrCM  = $row['companyID'] > 0  ? $this->select('company',array("title"), " WHERE ID = ".$row['companyID']." ") : '';
					$arrSB  = $row['suburbID'] > 0 ? $this->select('suburbs',array("pscode,title"), " WHERE ID = ".$row['suburbID']." ") : '';
					$arrST  = $row['stopID'] > 0 ? $this->select('stops',array("title"), " WHERE ID = ".$row['stopID']." ") : '';
					
					echo '<tr>'; 
					echo '<td align="center"><a class="frm_viewID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_ST Fare Evasion Register').'" style="text-decoration:none; cursor:pointer;">'.$row['shiftNO'].'</a></td>';
					echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
					echo '<td>'.$arrCM[0]['title'].'</td>';
					echo '<td>'.$row['routeInfo'].'</td>';					
					echo '<td>'.$arrST[0]['title'].'</td>';
					echo '<td>'.$arrSB[0]['title'].' ('.$arrSB[0]['pscode'].')</td>';					
					echo '<td>'.$row['nooffare'].'</td>';
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
				
					if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if((substr($row['logID'],0,10)) < date('Y-m-d',strtotime(date('Y-m-d').'-'.$this->cldaysID.' Days')))	{echo '<td></td>';}	else
						{echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Fare Evasion Register').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
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
			echo '<label for="section">Date <span class="Maindaitory">*</span></label>';
			$dateID = (!empty($id) ? $this->VdateFormat($this->result['dateID']) : date('d/m/Y'));
			echo '<input type="datable" onchange="changes=true;" class="form-control datepicker" data-datable="ddmmyyyy" id="dateID" name="dateID" placeholder="Enter Date" required="required" style="text-align:center;" value="'.$dateID.'">';
			echo '<span id="register_dateID_errorloc" class="errors"></span>';
		echo '</div>'; 		
		
		echo '<div class="col-xs-2">';
			$dayID = (!empty($id) ? ($this->result['dayID']) : $this->returnDateDayID($dateID));
			echo '<label for="section">Day <span class="Maindaitory">*</span></label>';					
			$dayNM = $dayID == '1' ? 'Monday' :($dayID == '2' ? 'Tuesday'  :($dayID == '3' ? 'Wednesday' :($dayID == '4' ? 'Thursday' 
					:($dayID == '5' ? 'Friday' :($dayID == '6' ? 'Saturday' :($dayID == '7' ? 'Sunday' 	: ''))))));

			echo '<input type="hidden" name="dayID" id="dayID" value="'.$dayID.'" />';
			echo '<input type="text" onchange="changes=true;" class="form-control" id="dayNM" name="dayNM" readonly="readonly" style="text-align:center" value="'.$dayNM.'" />';					
			echo '<span id="register_dayID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Shift No <span class="Maindaitory">*</span></label>';
			$shiftNO = (!empty($id) ? $this->result['shiftNO'] : $this->safeDisplay['shiftNO']);
			echo '<input type="text" onchange="changes=true;" class="form-control" id="shiftNO" name="shiftNO" placeholder="Enter Shift NO" style="text-align:center;" value="'.$shiftNO.'">';
			echo '<span id="register_shiftNO_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Depot Name <span class="Maindaitory">*</span></label>';
			echo '<select name="companyID" onchange="changes=true;" class="form-control" id="companyID">';
			echo '<option value="0" selected="selected"> --- Select Depot --- </option>';
			$companyID = (!empty($id) ? $this->result['companyID'] : $this->result['companyID']);
			$Qry = $this->DB->prepare("SELECT * FROM company WHERE status = 1 Order By ID ASC");
			if($Qry->execute())
			{
				$this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
				foreach($this->crow as $mrow)	
				{ 
					echo '<option value="'.$mrow['ID'].'" '.($companyID == $mrow['ID'] ? 'selected="selected"' : '').'>'.$mrow['title'].'</option>';
				}
			}	 
			echo '</select>';
			echo '<span id="register_companyID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Route No <span class="Maindaitory">*</span></label>';
			echo '<select name="routenoID" onchange="changes=true;" class="form-control select2" style="width: 100%;" id="routenoID">';
			echo '<option value="0" selected="selected"> --- Select --- </option>';			
			$Qry = $this->DB->prepare("SELECT * FROM cnserviceno WHERE ID > 0 Order By ID ASC");
			if($Qry->execute())
			{
				$this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
				foreach($this->crow as $mrow)	
				{
					if($mrow['serviceID'] <> '')
					{
						$mrowID = explode(",",$mrow['serviceID']);
						foreach($mrowID as $lastID)
						{
							$EM_Array = $lastID > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$lastID." ") : '';
							echo '<option value="'.$lastID.'" aria-sort="'.$EM_Array[0]['title'].'" '.($this->result['routenoID'] == $lastID ? 'selected="selected"' : '').'>'.$EM_Array[0]['codeID'].'</option>';
						}
					}				
				}
			}
			echo '</select>';
			echo '<span id="register_routenoID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-6">';
			echo '<label for="section">Route No Info <span class="Maindaitory">*</span></label>';
			$routeInfo = (!empty($id) ? $this->result['routeInfo'] : $this->safeDisplay['routeInfo']);
			echo '<input type="text" onchange="changes=true;" class="form-control" id="routeInfo" readonly="readonly" name="routeInfo" placeholder="Enter Route Info" value="'.$routeInfo.'">';
			echo '<span id="register_routeInfo_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Location <span class="Maindaitory">*</span></label>';
			echo '<select name="stopID" onchange="changes=true;" class="form-control" id="stopID">';
			echo '<option value="0" selected="selected"> --- Select --- </option>';
			$stopID = !empty($id) ? $this->result['stopID'] : $this->result['stopID'];
			if(!empty($this->result['routenoID']))
			{
				$Qry = $this->DB->prepare("SELECT stopID FROM srvdtls_stops WHERE recID > 0 AND serviceID In(".$this->result['routenoID'].") Order By orderID ASC ");
				if($Qry->execute())
				{
					$this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->crow as $mrow)	
					{
						$arrMS = $mrow['stopID'] > 0 ? $this->select('stops',array("*"), " WHERE ID = ".$mrow['stopID']." ") : '';
						echo '<option value="'.$mrow['stopID'].'" '.($stopID == $mrow['stopID'] ? 'selected="selected"' : '').'>'.$arrMS[0]['title'].'</option>';
					}
				}	
			}
			echo '</select>';
			echo '<span id="register_stopID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Time <span class="Maindaitory">*</span></label>';
			$timeSHF = (!empty($id) ? $this->result['timeID'] : date('G:i'));
			echo '<input type="text" onchange="changes=true;" class="form-control TPicker" id="timeID" name="timeID" placeholder="hh:mm" style="text-align:center;" value="'.$timeSHF.'">';
			echo '<span id="register_timeID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Suburb <span class="Maindaitory">*</span></label>';
			echo '<select onchange="changes=true;" class="form-control select2" style="width: 100%;" id="suburbID" name="suburbID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select Suburb --</option>';
				echo $this->GET_SubUrbs((!empty($id) ? $this->result['suburbID'] : $this->safeDisplay['suburbID']),'');				
			echo '</select>';
			echo '<span id="register_suburbID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">No of Fare Evaders <span class="Maindaitory">*</span></label>';
			echo '<input type="text" onchange="changes=true;" class="form-control numeric positive" style="text-align:center;" id="nooffare" name="nooffare" value="'.(!empty($id) ? ($this->result['nooffare']) : $this->safeDisplay('nooffare')).'">';
			echo '<span id="register_nooffare_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Description <span class="Maindaitory">*</span></label>';
			echo '<select onchange="changes=true;" class="form-control" id="descriptionID" name="descriptionID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				$descriptionID = (!empty($id) ? $this->result['descriptionID'] : $this->safeDisplay['descriptionID']);
				echo $this->GET_Masters($descriptionID,'124');				
			echo '</select>';			
			echo '<span id="register_descriptionID_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';

	echo '<div class="row">';  
		echo '<div class="col-xs-6">';
			echo '<label for="section">CMR Ref No </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="cmrrefNO" id="cmrrefNO" placeholder="Enter CMR Ref No">'.(!empty($id) ? $this->result['cmrrefNO'] : $this->safeDisplay['cmrrefNO']).'</textarea>';
			echo '<span id="register_cmrrefNO_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-6">';
			echo '<label for="section">General Comments </label>';
			echo '<textarea style="resize:none;" onchange="changes=true;" class="form-control" rows="2" name="commentsGN" id="commentsGN" placeholder="Enter General Comments">'.(!empty($id) ? $this->result['commentsGN'] : $this->safeDisplay['commentsGN']).'</textarea>';
			echo '<span id="register_commentsGN_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>'; 
	
	echo '<div class="row">';	
		echo '<div class="col-xs-3">';	
		if(!empty($id))
		echo '<input name="ID" value="'.$id.'" type="hidden">';
		echo '<button class="btn btn-primary btn-flat" onclick="changes=false;" name="Submit" type="submit">'.(!empty($id) ? 'Update Fare Evasion Register' : 'Save Fare Evasion Register').'</button>';
		echo '</div>';
          
        echo '<div class="col-xs-2">';
        echo '<a href="'.$this->basefile.'?a='.$this->Encrypt('view').'"><button class="btn btn-primary btn-flat"  style="margin-right:30px; float:right; display:inline-block" type="button">View All Lists</button></a>';
        echo '</div>';
	echo '</div>';
	
	echo '<div id="InspcValidGridID"></div>';
	
	echo '</div>';
	echo '</form>';
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);			//echo '<PRE>'; echo print_r($_POST); exit;
			
			if($dateID == '') 	 	   $errors .= "Enter The Date.<br>";

			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm();
			}
			else
			{	 
				$_POST['dateID']   = $this->dateFormat($_POST['dateID']);
				$_POST['userID']   = $_SESSION[$this->website]['userID'];
				unset($_POST['Submit']);
				
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}				
				$array['logID'] = date('Y-m-d H:i:s');
				//echo '<PRE>'; echo print_r($array);exit;
				if($this->BuildAndRunInsertQuery($this->tableName,$array))
				{
					$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
					$lastID = $stmt->fetch(PDO::FETCH_NUM);
					
					$this->PUSH_userlogsID($this->frmID,$lastID[0],$array['dateID'],'','',$array['shiftNO'],'A',$array['cmrrefNO'],$array);


					$this->msg = urlencode('Fare Evasion Record is created successfully . <br /> Date : '.$dateID);
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
		if($this->Form_Variables() == true)		//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$errors	=	'';

			if($dateID == '') 	 	   $errors .= "Enter The Date.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 
				$_POST['dateID']  = $this->dateFormat($_POST['dateID']);				
				unset($_POST['Submit']); 
				$array = array();
				foreach($_POST as $key=>$value)	{$array[$key] = $value;}
				$on['ID'] = $ID;
				//echo '<pre>'; echo print_r($array); exit;
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{
					$this->PUSH_userlogsID($this->frmID,$ID,$array['dateID'],'','',$array['shiftNO'],'E',$array['cmrrefNO'],$array);
										
					$this->msg = urlencode('Fare Evasion Record is Updated successfully . <br /> Date : '.$dateID);
					$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$on['ID']);
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