<?PHP
class Masters extends SFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'company_dtls';
        $this->frmID    	= '135';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY companyID,ID DESC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>'; 
				echo '<th>Company Name</th>';
                echo '<th>Sub Depot Name</th>';
				echo '<th>Sub Depot Code</th>';
                echo '<th>Address</th>';
                echo '<th>Suburb</th>';
                echo '<th>Postal Code</th>';
                echo '<th>Status</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</tr></thead>';
                $Start = 1; $uscountsID = 0; 
                foreach($this->rows as $row)			
                {
                    $arrCM  = ($row['companyID'] > 0  ? $this->select('company',array("title"), " WHERE ID = ".$row['companyID']." ") : '');
					
                    echo '<tr>'; 
					echo '<td>'.$arrCM[0]['title'].'</td>';
                    echo '<td>'.$row['title'].'</td>';
					echo '<td align="center">'.$row['dcode'].'</td>';
                    echo '<td>'.$row['address'].' , '.$row['address_1'].'</td>';
                    echo '<td>'.$row['suburb'].'</td>';
                    echo '<td align="center">'.$row['pscode'].'</td>';                                            
                    echo '<td align="center" '.($row['status'] == 2 ? 'style="color:red;font-weight:bold;"' :($row['status'] == 1 ? 'style="color:green;font-weight:bold;"' : '')).'>'.($row['status'] == 1 ? 'Active' :($row['status'] == 2 ? 'Sleeping' : '')).'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					
                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Sub Depot Master').'" style="text-decoration:none; cursor:pointer;"></a></td>';
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
                  Sorry....you don\'t have permission to view <b>Company Master</b> Page</div></div>';
        }
    } 

    public function createForm($id='')
    {
        $this->action = 'add';
        if(!empty($id))
        {
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID = :ID ");
            $Qry->bindParam(':ID',$id);
            $Qry->execute();
            $this->result = $Qry->fetch(PDO::FETCH_ASSOC);			
            $this->action = 'edit';
        }

    echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" novalidate >';
    echo '<div class="box-body" id="fg_membersite">';

    echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Company Name <span class="Maindaitory">*</span></label>';
			echo '<select class="form-control select2" style="width: 100%;" id="companyID" name="companyID">';
			echo '<option value="0" selected="selected" disabled="disabled">-- Select Company --</option>';
				$companyID = (!empty($id) ? ($this->result['companyID']) : $this->safeDisplay('companyID'));
				$SQL = "SELECT * FROM company WHERE ID > 0 AND status = 1 Order By title ASC ";
				$Qry = $this->DB->prepare($SQL);
				$Qry->execute();
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				foreach($this->rows as $rows)
				{ 
					echo '<option '.($rows['ID'] == $companyID ? 'selected="selected"' : '').' value="'.$rows['ID'].'">'.$rows['title'].' - '.$rows['pscode'].'</option>';		
				}
			echo '</select>';
			echo '<span id="register_companyID_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-6"></div>';
		
		echo '<div class="col-xs-2">';
		echo '<label for="section">Status</label>';
		echo '<select name="status" class="form-control" id="status" >';
			$status = (!empty($id) ? ($this->result['status']) : $this->safeDisplay('status'));
			$status = $status > 0 ? $status : 1;
			echo '<option value="0" selected="selected"> --- Select --- </option>';
			echo '<option value="1" '.($status == 1 ? 'selected="selected"' : '').'>Active</option>';
			echo '<option value="2" '.($status == 2 ? 'selected="selected"' : '').'>Sleeping</option>';
		echo '</select>';
		echo '</div>';
    echo '</div>';
	
    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Sub Depot Name <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Sub Depot Name" value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
			echo '<span id="register_title_errorloc" class="errors"></span>';
		echo '</div>';	

		echo '<div class="col-xs-2">';
			echo '<label for="section">Sub Depot Code <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control" id="dcode" name="dcode" placeholder="Enter Sub Depot Code" value="'.(!empty($id) ? ($this->result['dcode']) : $this->safeDisplay('dcode')).'">';
			echo '<span id="register_dcode_errorloc" class="errors"></span>';
		echo '</div>';
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';		
		echo '<div class="col-xs-4">';
			echo '<label for="section">Address - 1 <span class="Maindaitory">*</span></label>';
			echo '<textarea class="form-control" id="address" name="address" placeholder="Enter Address - 1" rows="2" 
			style="resize:none;">'.(!empty($id) ? ($this->result['address']) : $this->safeDisplay('address')).'</textarea>';
			echo '<span id="register_address_errorloc" class="errors"></span>';
			
		echo '</div>';	 

		echo '<div class="col-xs-4">';
			echo '<label for="section">Address - 2</label>';
			echo '<textarea class="form-control" id="address_1" name="address_1" placeholder="Enter Address - 2" rows="2" 
			style="resize:none;">'.(!empty($id) ? ($this->result['address_1']) : $this->safeDisplay('address_1')).'</textarea>';
		echo '</div>';   
    echo '</div>';

    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Suburb</label>';
			echo '<input type="text" class="form-control" id="suburb" name="suburb" placeholder="Enter Suburb" value="'.(!empty($id) ? ($this->result['suburb']) : $this->safeDisplay('suburb')).'">';
		echo '</div>'; 

		echo '<div class="col-xs-2">';
			echo '<label for="section">PostCode <span class="Maindaitory">*</span></label>';
			echo '<input type="text" class="form-control decimal_places_1 numeric positive" maxlength="4" id="pscode" name="pscode" placeholder="Enter PostCode" value="'.(!empty($id) ? ($this->result['pscode']) : $this->safeDisplay('pscode')).'">';
			echo '<span id="register_pscode_errorloc" class="errors"></span>';
		echo '</div>'; 
		
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>'; 

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

			if($title == '') 		$errors .= "Enter The Sub Depot Name.<br>";
			if($address == '') 	    $errors .= "Enter The Company Address Line 1 .<br>";
			if($pscode == '') 	    $errors .= "Enter The Company Postal Code.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}

			else
			{	
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND companyID =:companyID "); 
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
					$_POST['userID'] = $_SESSION[$this->website]['userID'];
					$_POST['title'] = ucfirst($_POST['title']);
					unset($_POST['Submit']);
					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$array['logID']	= date('Y-m-d H:i:s');
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A');

						$this->msg = urlencode(' Sub Depot Master Is Created (s) Successfully . <br /> Sub Depot Name : '.$array['title']);
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
		if($this->Form_Variables() == true)				//echo '<pre>'; echo print_r($_POST); exit;
		{	
			extract($_POST);

			$errors	=	'';

			if($title == '') 		$errors .= "Enter The Sub Depot Name.<br>";
			if($address == '') 	  	$errors .= "Enter The Company Address Line 1 .<br>";
			if($pscode == '') 	    $errors .= "Enter The Company Postal Code.<br>";

			if(!empty($errors))
			{
					$this->printMessage('danger',$errors);
					$this->createForm($ID);
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND companyID =:companyID AND ID <> :ID ");
				$Qry->bindParam(':companyID',$companyID);
				$Qry->bindParam(':title',$title);
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
					$_POST['title'] = ucfirst($_POST['title']);
					unset($_POST['Submit'],$_POST['ID']);

					$array = array();
					foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
					$on['ID'] = $ID;					
					//echo '<pre>'; echo print_r($array); exit;					
					if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
					{ 
					    $this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
									
						$this->msg = urlencode(' Sub Depot Master Is Updated (s) Successfully . <br /> Sub Depot Name : '.$array['title']);						
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