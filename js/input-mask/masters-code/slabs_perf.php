<?PHP
class Masters extends Functions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	     = basename($_SERVER['PHP_SELF']);		
		$this->tableName     = 'slabs_perf';
		$this->frmID	     = '31';
		$this->companyID	 = $_SESSION[$this->website]['compID'];
		$this->permissions   = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In (".$this->companyID.") ORDER BY ID DESC ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				echo '<table id="dataTable" class="table table-bordered table-striped">';				
				echo '<thead><tr>';
				echo '<th>Sr. No.</th>';
				echo '<th>Slab Range</th>';
				echo '<th>Punctuality Penalty %</th>';
				echo '<th>Safety Score Penalty %</th>';
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
				echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
				echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
				echo '</tr></thead>';
				$Start = 1; $uscountsID = 0; 
				foreach($this->rows as $row)			
				{
					echo '<tr>';
					echo '<td align="center">'.$Start++.'</td>';
					echo '<td align="center">'.$row['frmID'].' - '.$row['toID'].'</td>';
					echo '<td align="center">'.$row['pun_penalty'].' %</td>';
					echo '<td align="center">'.$row['saf_penalty'].' %</td>';
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

					if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{echo '<td align="center"><a data-title="'.$this->tableName.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						$uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ") : '';
						if($uscountsID > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Slabs - Performance').'" 
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
				  Sorry....you haven\'t permission\'s to view <b>Slabs - Performance</b> Page</div></div>';					
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
		echo '<div class="col-xs-2">';
			echo '<label for="section">From Slab <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control numeric" name="frmID" placeholder="Enter From Slab" style="text-align:center;" 
			value="'.(!empty($id) ? ($this->result['frmID']) : $this->safeDisplay('frmID')).'">';
			echo '<span id="register_frmID_errorloc" class="errors"></span>';
		echo '</div>';	
                
		echo '<div class="col-xs-2">';
			echo '<label for="section">To Slab <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control numeric" name="toID" placeholder="Enter To Slab" style="text-align:center;" 
			value="'.(!empty($id) ? ($this->result['toID']) : $this->safeDisplay('toID')).'">';
			echo '<span id="register_toID_errorloc" class="errors"></span>';
		echo '</div>';	
	echo '</div><br />';	
		
	echo '<div class="row">';
                echo '<div class="col-xs-1"></div>';	
                
                echo '<div class="col-xs-3">';
			echo '<label for="section">Punctuality Penalty % <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control numeric" name="pun_penalty" placeholder="Enter Punctuality Penalty %"  style="text-align:center;"
			value="'.(!empty($id) ? ($this->result['pun_penalty']) : $this->safeDisplay('pun_penalty')).'">';
			echo '<span id="register_pun_penalty_errorloc" class="errors"></span>';
		echo '</div>';	
	echo '</div><br />';
	
	echo '<div class="row">';
                echo '<div class="col-xs-1"></div>';	
                
                echo '<div class="col-xs-3">';
			echo '<label for="section">Safety Score Penalty % <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control numeric" name="saf_penalty" placeholder="Enter Safety Score Penalty %" style="text-align:center;"
			value="'.(!empty($id) ? ($this->result['saf_penalty']) : $this->safeDisplay('saf_penalty')).'">';
			echo '<span id="register_saf_penalty_errorloc" class="errors"></span>';
		echo '</div>';	
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
        
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Slabs Master' : 'Save Slabs Master').'</button>';
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
			
			if($frmID == '')             $errors .= "Enter The From Slab Value.<br>";
			if($toID == '') 	     $errors .= "Enter The To Slab Value.<br>";
			if($pun_penalty == '')       $errors .= "Enter The Punctuality Penalty %.<br>";
			if($saf_penalty == '') 	     $errors .= "Enter The Safety Score Penalty %.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			
			else
			{	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE frmID =:frmID AND toID =:toID  AND companyID =:companyID "); 
				$Qry->bindParam(':frmID',$frmID);
                                $Qry->bindParam(':toID',$toID);
                                $Qry->bindParam(':companyID',$companyID);
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
					$_POST['status'] = 1;
					$_POST['userID'] = $_SESSION[$this->website]['userID'];
                                        $_POST['companyID'] = $this->companyID;
                                        unset($_POST['Submit']);
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['logID']	= date('Y-m-d H:i:s');
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A'); 
 
					
						$this->msg = urlencode(' Slabs - Performance Master Is Created (s) Successfully . <br /> Slab : '.$array['frmID'].' - '.$array['toID']);						
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
			
			if($frmID == '')             $errors .= "Enter The From Slab Value.<br>";
			if($toID == '') 	     $errors .= "Enter The To Slab Value.<br>";
			if($pun_penalty == '')       $errors .= "Enter The Punctuality Penalty %.<br>";
			if($saf_penalty == '') 	     $errors .= "Enter The Safety Score Penalty %.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE frmID =:frmID AND toID =:toID AND companyID = :companyID AND ID <> :ID ");
				$Qry->bindParam(':frmID',$frmID);
                                $Qry->bindParam(':toID',$toID);
                                $Qry->bindParam(':companyID',$companyID);
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
					$_POST['status'] = 1;
					unset($_POST['Submit'],$_POST['ID']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$on['ID'] = $ID;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 	
					    $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
									
						$this->msg = urlencode(' Slabs - Performance Master Is Updated (s) Successfully . <br /> Slab : '.$array['frmID'].' - '.$array['toID']);
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