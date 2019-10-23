<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  =	basename($_SERVER['PHP_SELF']);		
		$this->tableName    =	'cnserviceno';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->frmID	    = '35';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In('".($this->companyID)."') ORDER BY ID DESC ");
		if($Qry->execute())
		{
                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>';
                    echo '<th>Sr. No</th>';
                    echo '<th>Contractor Name</th>';
                    echo '<th>Contract Name</th>';
                    echo '<th>Contract - Service Details</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                    echo '</tr></thead>';
                    $Start = 1; $uscountsID = 0; 
                    foreach($this->rows as $row)			
                    { 
                        $CN_Array  = $row['contractorID'] > 0  ? $this->select('master',array("title"), " WHERE ID = ".$row['contractorID']." ") : '';
                        $MS_Array  = $row['contractID'] > 0  ? $this->select('master',array("title"), " WHERE ID = ".$row['contractID']." ") : '';

                        /* VOUCHER - DETAILS */
                        $recsID = explode(",",$row['serviceID']);
                        $srID = 1;
                        $stID = '';
                        foreach($recsID as $lastID)
                        {
                            $SR_Array  = $lastID > 0  ? $this->select('srvdtls',array("*"), " WHERE ID = ".$lastID." ") : '';
                            $stID .= $srID == 15 ? '<br />'.$SR_Array[0]['codeID'] : '&nbsp;&nbsp;,&nbsp;&nbsp;'.$SR_Array[0]['codeID'];
                            if($srID == 15) {$srID = 1;} else {$srID++;}
                        }

                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td>'.$CN_Array[0]['title'].'</td>';
                        echo '<td>'.$MS_Array[0]['title'].'</td>';
                        echo '<td>'.$stID.'</td>';

                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
						 
                    
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							$uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ") : '';
							if($uscountsID > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Contract - Service Details').'" 
						  style="text-decoration:none; cursor:pointer;"></a></td>';
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
                      Sorry....you haven\'t permission\'s to view <b>Contract - Service Details</b> Page</div></div>';
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
			echo '<label for="section">Company/Depot Name</label>';
			echo '<select name="companyID[]" class="form-control" id="serviceID" multiple="multiple" required="required">';
			$companyID = (!empty($id) ? $this->result['companyID'] : $this->safeDisplay('companyID'));
			$compID = explode(",",$companyID);
			{
                            $Qry = $this->DB->prepare("Select ID, title From company ORDER BY ID ASC ");
                            if($Qry->execute())	
                            {	
                                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                foreach($this->rows as $row)
                                {
                                    echo '<option value="'.$row['ID'].'" '.(in_array($row['ID'],$compID) ? 'selected="selected"' : '' ).' >'.$row['title'].'</option>';
                                }
                            }						
			}
			echo '</select>';		
		echo '</div>';  
	echo '</div><br />';
        
	echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Contractor Name</label>';
			echo '<select class="form-control" id="contractorID" name="contractorID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['contractorID'] : $this->safeDisplay['contractorID']),'29');
					echo '<span id="register_contractorID_errorloc" class="errors"></span>';
			echo '</select>';			
		echo '</div>'; 
                
		echo '<div class="col-xs-4">';
			echo '<label for="section">Contract</label>';
			echo '<select class="form-control" id="contractID" name="contractID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['contractID'] : $this->safeDisplay['contractID']),'28');
					echo '<span id="register_contractID_errorloc" class="errors"></span>';
			echo '</select>';			
		echo '</div>'; 
	echo '</div><br />';
	
	echo '<div class="row">';	
		echo '<div class="col-xs-4">';
			echo '<label for="section">Service No <span class="Maindaitory">*</span></label>';
			echo '<select name="serviceID[]" class="form-control" id="serviceID" multiple="multiple" required="required" >';
			$serviceID = (!empty($id) ? $this->result['serviceID'] : $this->safeDisplay('serviceID'));
			$srvsID = explode(",",$serviceID);
			{
				$Qry = $this->DB->prepare("Select ID, codeID From srvdtls ORDER BY codeID ASC ");
				if($Qry->execute())	
				{	
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					foreach($this->rows as $row)
					{
						 echo '<option value="'.$row['ID'].'" '.(in_array($row['ID'],$srvsID) ? 'selected="selected"' : '' ).' >'.$row['codeID'].'</option>';
					}
				}						
			}
			echo '</select>';
		echo '</div>';	
	echo '</div>';
	
	echo '<div class="row">';
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
		
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Contract - Service' : 'Save Contract - Service').'</button>';
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
			
			if($contractorID == '')	$errors .= "Enter The Contractor Name.<br>";
                        if($contractID == '')	$errors .= "Enter The Contract Name.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{ 
			  $_POST['serviceID'] = implode(",",$serviceID);
			  $_POST['companyID'] = implode(",",$companyID);
			  unset($_POST['Submit']);
			  
			  $array = array();					
			  foreach($_POST as $key=>$value)	{$array[$key] = $value;}
			  $array['status']		 = 1;
			  $array['logID']	= date('Y-m-d H:i:s');
			  if($this->BuildAndRunInsertQuery($this->tableName,$array))
			  {
				  $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
				  $lastID = $stmt->fetch(PDO::FETCH_NUM);
				  
				  $this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A'); 
		  
				  $this->msg = urlencode(' Contract - Service Details Is Created (s) Successfully.');
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
			
                        if($contractorID == '')	$errors .= "Enter The Contractor Name.<br>";
			if($contractID == '')   $errors .= "Enter The Contract Name.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 
				$array = array();
                                $array['contractorID']  = $contractorID;
				$array['contractID']    = $contractID;
                                $array['serviceID']     = implode(",",$serviceID);
                                $array['companyID']     = implode(",",$companyID);
				$on['ID'] = $ID; 
				if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
				{ 
				
				 $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
				  
		
					$this->msg = urlencode('  Contract - Service Details Is Updated (s) Successfully .');
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