<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
            parent::__construct();		

            $this->basefile	= basename($_SERVER['PHP_SELF']);		
            $this->tableName    = 'offence';

            $this->frmID	= '53';
            $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
            if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
            {
				//AND companyID In (".$_SESSION[$this->website]['compID'].")
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 ORDER BY ID DESC ");
		if($Qry->execute())
		{
                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>';
                    echo '<th>Sr. No.</th>';
                    echo '<th>Offence Type</th>';
                    echo '<th>Offence Details</th>';
                    echo '<th>Reporting Transperth</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : ''); 
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                    echo '</tr></thead>';
                    $Start = 1; $uscountsID = 0; 
                    foreach($this->rows as $row)			
                    {
                        $OF_Array  = $row['typeID'] > 0    ? $this->select('master',array("title"), " WHERE ID = ".$row['typeID']." ") : '';

                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td>'.$OF_Array[0]['title'].'</td>';
                        echo '<td>'.$row['title'].'</td>';
                        echo '<td align="center">'.($row['reportingID'] == 1 ? 'Yes'  :($row['casualID'] == 2 ? 'No' : '')).'</td>';

                        
                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

					
					
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							$uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ") : '';
							if($uscountsID > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Offence Master').'" 
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
                      Sorry....you haven\'t permission\'s to view <b>Offence Details</b> Page</div></div>';
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
		
	echo '<form method="post" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
	echo '<div class="box-body">';

	echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Offence Type</label>';
			echo '<select class="form-control" id="typeID" name="typeID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['typeID'] : $this->safeDisplay['typeID']),'26');
			echo '</select>';
		echo '</div>';	
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Reporting Transperth</label>';
			echo '<select name="reportingID" class="form-control" id="reportingID" >';
				$reportingID = (!empty($id) ? ($this->result['reportingID']) : $this->safeDisplay('reportingID'));
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo '<option value="1" '.($reportingID == 1 ? 'selected="selected"' : '').'>Y</option>';
				echo '<option value="2" '.($reportingID == 2 ? 'selected="selected"' : '').'>N</option>';
			echo '</select>';
		echo '</div>'; 
	echo '</div><br />';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-6">';
			echo '<label for="section">Offence Details</label>';
			echo '<textarea style="resize:none;" class="form-control" rows="2" name="title" 
			placeholder="Enter Offence Details">'.(!empty($id) ? $this->result['title'] : $this->safeDisplay['title']).'</textarea>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div><br />';
	
        
        echo '<input type="hidden" name="companyID" value="'.($_SESSION[$this->website]['compID']).'" />';
        
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Offence' : 'Save Offence ').'</button>';
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
			
			if($typeID == '') 		 $errors .= "Enter The Offence Type.<br>";
			if($reportingID == '') 	$errors .= "Please Specify The Reporting Transpert.<br>";
			if($title == '') 	 	  $errors .= "Please Specify The Offence Details.<br>";
			
			if(!empty($errors))
			{
                            $this->printMessage('danger',$errors);
                            $this->createForm();
			}			
			else
			{
				$slug = $this->URLSlugs($title);
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND typeID =:typeID AND companyID = :companyID "); 
				$Qry->bindParam(':slug',$slug);
				$Qry->bindParam(':typeID',$typeID);
                                $Qry->bindParam(':companyID',$companyID);
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger',' Already exist !...');
					$this->createForm();
				}
				else
				{
					$_POST['status'] = '1';
					$_POST['slug'] = $slug;
					unset($_POST['Submit']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['logID']	= date('Y-m-d H:i:s');
					//echo '<PRE>'; echo print_r($array);
					//echo '<PRE>'; echo print_r($_POST); exit;
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A',$array['title'],$array);
						
						$this->msg = urlencode(' Offence Details Is Created (s) Successfully . <br /> Offence Details : '.$array['title'].'.');						
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
			
			if($typeID == '') 		 $errors .= "Enter The Offence Type.<br>";
			if($reportingID == '') 	$errors .= "Please Specify The Reporting Transpert.<br>";
			if($title == '') 	 	  $errors .= "Please Specify The Offence Details.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$slug = $this->URLSlugs($title);
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND typeID =:typeID AND companyID = :companyID AND ID <> :ID ");
				$Qry->bindParam(':slug',$slug);
				$Qry->bindParam(':typeID',$typeID);
                                $Qry->bindParam(':companyID',$companyID);
				$Qry->bindParam(':ID',$ID);				
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount  = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Already exist !...');
					$this->createForm($ID);
				}
				else
				{
					$_POST['slug'] = $slug;
					unset($_POST['Submit'],$_POST['ID']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key] = $value;}
					$on['ID'] = $ID;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 
					
						$this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E',$array['title'],$array);
											
						$this->msg = urlencode(' Offence Details Is Updated (s) Successfully . <br /> Offence Details : '.$array['title'].'.');
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