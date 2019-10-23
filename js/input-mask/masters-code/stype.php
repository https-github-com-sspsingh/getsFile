<?PHP
class Masters extends Functions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	  	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));
		$this->companyID	= $_SESSION[$this->website]['compID'];
		$this->frmID	    = '52';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
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
                    echo '<th>Shift Type</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                    echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                    echo '</tr></thead>';
                    $Start = 1;
                    foreach($this->rows as $row)			
                    {
                        echo '<tr>';
                        echo '<td align="center">'.$Start++.'</td>';
                        echo '<td>'.$row['title'].'</td>';
                        
                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
                        
                    }
                    echo '</table>';			
		} 
            }
            else
            {
                    echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">
                      Sorry....you haven\'t permission\'s to view <b>Employee Master</b> Page</div></div>';
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
			echo '<label for="section">Shift Type <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Shift Type" 
			value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
		echo '</div>';	
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
	echo '</div>';
	
        echo '<input type="hidden" name="companyID" value="'.($this->companyID).'" />';
        
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Master' : 'Save Master').'</button>';
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
			
			if($title == '') 		$errors .= "Enter The Shift Type.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			
			else
			{	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND companyID = :companyID "); 
                                $Qry->bindParam(':companyID',$companyID);
				$Qry->bindParam(':title',$title);
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
					$_POST['companyID'] = $this->companyID;
					$_POST['status'] = 1;
					$_POST['title'] = ucfirst($_POST['title']);
					unset($_POST['Submit']);
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['insert_date']	= date('Y-m-d H:i:s');

					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);

						$this->msg = urlencode(' Shift Type Master Is Created (s) Successfully . <br /> Shift Type : '.$array['title']);
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
			
			if($title == '') 		$errors .= "Enter The Shift Type.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND companyID  = :companyID AND ID <> :ID ");
				$Qry->bindParam(':title',$title);
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
					$_POST['title'] = ucfirst($_POST['title']);
					unset($_POST['Submit'],$_POST['ID']);
					
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$on['ID'] = $ID;
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 					
						$this->msg = urlencode(' Shift Type Master Is Updated (s) Successfully . <br /> Shift Type : '.$array['title']);						
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