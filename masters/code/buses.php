<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = 'buses';
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->frmID	    = '33';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
		if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In(".$this->companyID.") ORDER BY ID DESC ");
		if($Qry->execute())
		{
                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>';
                    echo '<th>Sr. No.</th>';
                    echo '<th>Bus No</th>';
                    echo '<th>Licence No</th>';
                    echo '<th>N.O.S</th>';
                    echo '<th>Model</th>';
                    echo '<th>Manufacturer</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                    echo '</tr></thead>';
                    $Start = 1; $uscountsID = 0; 
                    foreach($this->rows as $row)			
                    { 
                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td align="center">'.$row['busno'].'</td>';
                        echo '<td align="center">'.$row['lcsno'].'</td>';
                        echo '<td align="center">'.$row['nos'].'</td>';
                        echo '<td>'.$row['modelno'].'</td>';
                        echo '<td>'.$row['title'].'</td>';
                        
                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
						
						
						if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
						{ 
							if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
							{
								echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Bus Details').'" style="text-decoration:none; cursor:pointer;"></a></td>';
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
                      Sorry....you haven\'t permission\'s to view <b>Buses Master</b> Page</div></div>';
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
		echo '<div class="col-xs-3">';
			echo '<label for="section">Bus No <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" name="busno" placeholder="Enter Bus No" style="text-align:center;" required="required"	
					value="'.(!empty($id) ? ($this->result['busno']) : $this->safeDisplay('busno')).'">';
					echo '<span id="register_busno_errorloc" class="errors"></span>';
		echo '</div>'; 

		echo '<div class="col-xs-3">';
			echo '<label for="section">Licence <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" name="lcsno" placeholder="Enter Licence No" style="text-align:center;" required="required"	
					value="'.(!empty($id) ? ($this->result['lcsno']) : $this->safeDisplay('lcsno')).'">';
					echo '<span id="register_lcsno_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-3">';
			echo '<label for="section">Model</label>';
			echo '<input type="text" class="form-control" id="modelno" name="modelno" placeholder="Enter Model Name" 
			value="'.(!empty($id) ? $this->result['modelno'] : $this->safeDisplay('modelno')).'">';
		echo '</div>';
		
		echo '<div class="col-xs-1"></div>';
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Seats <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control numeric" name="nos" placeholder="Enter Seats" style="text-align:center;" required="required"	
					value="'.(!empty($id) ? ($this->result['nos']) : $this->safeDisplay('nos')).'">';
					echo '<span id="register_nos_errorloc" class="errors"></span>';
		echo '</div>'; 
	echo '</div>';
	
	echo '<div class="row">'; 
		echo '<div class="col-xs-6">';
			echo '<label for="section">Manufacturer</label>';
			echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Manufacturer" 
			value="'.(!empty($id) ? $this->result['title'] : $this->safeDisplay('title')).'">';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
	echo '</div><br />';
	
        echo '<input type="hidden" name="companyID" value="'.($this->companyID).'" />';
        
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Service Details' : 'Save Service Details').'</button>';
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
			
			if($busno == '') 		 $errors .= "Enter The Bus No.<br />";
			if($lcsno == '') 		 $errors .= "Enter The Licence No.<br />";
			if($modelno == '') 	 	 $errors .= "Enter The Model Name.<br />";
			if($nos == '') 	 		 $errors .= "Enter The No of Seats.<br />";
			if($title == '') 	 	 $errors .= "Enter The Manufacturer Name.<br />";
                        
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			} 
			else
			{
				$slug = $this->URLSlugs($title);
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND busno =:busno AND companyID = :companyID "); 
				$Qry->bindParam(':busno',$busno);
				$Qry->bindParam(':companyID',$companyID);
				$Qry->bindParam(':slug',$slug);
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
					$_POST['slug'] = $slug;					
					$_POST['userID']  = $_SESSION[$this->website]['userID'];
					unset($_POST['Submit']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['logID'] = date('Y-m-d H:i:s');
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A'); 

					
						$this->msg = urlencode(' Bus Details Is Created (s) Successfully . <br /> Bus No  : '.$array['busno']);
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
			
			$errors	=	'';
			
			if($busno == '') 		 $errors .= "Enter The Bus No.<br />";
			if($lcsno == '') 		 $errors .= "Enter The Licence No.<br />";
			if($modelno == '') 	 	 $errors .= "Enter The Model Name.<br />";
			if($nos == '') 	 		 $errors .= "Enter The No of Seats.<br />";
			if($title == '') 	 	 $errors .= "Enter The Manufacturer Name.<br />";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$slug = $this->URLSlugs($title);
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND companyID = :companyID AND busno = :busno AND ID <> :ID ");
				$Qry->bindParam(':busno',$busno);
                                $Qry->bindParam(':companyID',$companyID);
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
					    $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
						 					
						$this->msg = urlencode(' Bus Details Is Updated (s) Successfully . <br /> Bus No  : '.$array['busno']);
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