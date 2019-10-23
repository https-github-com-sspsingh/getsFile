<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  =	basename($_SERVER['PHP_SELF']);		
		$this->tableName     =	'suburbs';
		
		$this->frmID	    = '34';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
            if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
            {
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY ID DESC ");
		if($Qry->execute())
		{
                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>'; 
                    echo '<th>Suburb</th>';
                    echo '<th>Postal Code</th>';
                    echo '<th>Police StationID</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                    echo '</tr></thead>';
                    $Start = 1; $uscountsID = 0; 
                    foreach($this->rows as $row)			
                    { 
                        echo '<tr>'; 
                        echo '<td>'.$row['title'].'</td>';
                        echo '<td>'.$row['pscode'].'</td>';
                        echo '<td>'.$row['plstationID'].'</td>';

                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
						
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{
							$uscountsID  = $row['ID'] > 0 ? $this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ") : '';
							if($uscountsID > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Suburbs Master').'" 
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
                      Sorry....you haven\'t permission\'s to view <b>Suburbs Master</b> Page</div></div>';
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
		echo '<div class="col-xs-5">';
			echo '<label for="section">Suburb Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Suburb Name"
			style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
				echo '<span id="register_title_errorloc" class="errors"></span>';
		echo '</div>';
	echo '</div><br />';	
        
	echo '<div class="row">';
		echo '<div class="col-xs-2">';
			echo '<label for="section">Postal Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control decimal_places_1 numeric positive" maxlength="4" id="pincode" name="pscode" placeholder="Enter PostCode" maxlength="4" 
			style="text-transform:uppercase !important;" value="'.(!empty($id) ? ($this->result['pscode']) : $this->safeDisplay('pscode')).'">';
				echo '<span id="register_pscode_errorloc" class="errors"></span>';
		echo '</div>'; 
                
		echo '<div class="col-xs-3">';
			echo '<label for="section">Police StationID <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="plstationID" name="plstationID" placeholder="Enter Police StationID"
			value="'.(!empty($id) ? ($this->result['plstationID']) : $this->safeDisplay('plstationID')).'">';
			echo '<span id="register_plstationID_errorloc" class="errors"></span>';
		echo '</div>'; 
                
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
        
	
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Suburbs Master' : 'Save Suburbs Master').'</button>';
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
                        
			if($title == '') 	 $errors .= "Enter The Suburb Name.<br>";
                        
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}			
			else
			{
                                $slug = $this->URLSlugs($title);
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug "); 
				$Qry->bindParam(':slug',$slug);
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Already exist !...');
					$this->createForm();
				}
				else
				{
					unset($_POST['Submit']);
                                        $_POST['slug'] = $slug;
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
						
						$this->msg = urlencode(' Suburb Master Is Created successfully .<br /> Suburb : '.$array['title']);
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
			
			if($title == '') 	 $errors .= "Enter The Suburb Name.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$slug = $this->URLSlugs($title);
                                
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND ID <> :ID ");
				$Qry->bindParam(':slug',$slug);
				$Qry->bindParam(':ID',$ID);				
				$Qry->execute();
				$this->result = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowCount	 = $this->result['resultRows'];
				
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
						 
						$this->msg = urlencode(' Suburb Master Is Updated successfully .<br /> Suburb : '.$array['title']);
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