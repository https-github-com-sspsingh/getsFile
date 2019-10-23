<?PHP
class Masters extends SFunctions
{
    private	$tableName	=	'';
    private	$basefile	 =	'';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'users';

        $this->frmID        = '96';
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
		echo '<label for="section">User Name<span class="Maindaitory">*</span></label>';
		echo '<select class="form-control select2" style="width: 100%;" id="userID" name="userID">';
		echo '<option value="0" selected="selected" disabled="disabled">-- Select User Name --</option>';
		$Qry = $this->DB->prepare("SELECT * FROM users WHERE isActive = 1 Order By username ASC ");
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		foreach($this->rows as $rows)
		{
			foreach((explode(",", $rows['companyID'])) as $companyID)
			{	
				if($_SESSION[$this->website]['compID'] == $companyID)
				{
					echo '<option value="'.$rows['ID'].'">'.strtoupper($rows['username']).' - '.strtoupper($rows['dcodeID']).'</option>';
				}
			}
		}
		echo '</select>';
		echo '<span id="register_userID_errorloc" class="errors"></span>';
		echo '</div>';            
    echo '</div>';
    
    echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">New Password </label>';
			echo '<input type="text" class="form-control" id="newPSW" name="newPSW" minlength="8" placeholder="Enter New Password">';
			echo '<span id="register_newPSW_errorloc" class="errors"></span>';
		echo '</div>'; 
    echo '</div>';
    
    echo '<div class="row">';
            echo '<div class="col-xs-4">';
                echo '<label for="section">Confirm Password </label>';
                echo '<input type="text" class="form-control" id="newCNF" name="newCNF" minlength="8" placeholder="Enter Confirm Password">';
				echo '<span id="register_newCNF_errorloc" class="errors"></span>';
            echo '</div>'; 
            
            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>'; 
                
    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
              echo '<input name="ID" value="'.$id.'" type="hidden">';
              echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">Change Password</button>';
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

            if($userID == '')      $errors .= "Plz Select The User Name.<br>";
            if($newPSW == '') 	  $errors .= "Enter The New Password .<br>";
            if($newCNF == '') 	  $errors .= "Enter The Confirm Password .<br>";

            if($newPSW <> $newCNF)
            {
                $errors .= "New Password or Confirm Password Does Not Match .<br>";
            }
            
            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }
            else
            {
                
                $array = array();
                $array['password'] = md5($newPSW);
                $array['pstexts']  = $newCNF;
                $on['ID'] = $userID;
                if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                {
                    $this->msg = urlencode(' User Password Is Reset Successfully. <br />Kindly Check & Login to User.<br />');
                    $param = array('a'=>'create','t'=>'success','m'=>$this->msg);
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
?>