<?PHP
class ChangePassword extends SFunctions
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function createForm($id='')
	{
		$this->action = 'edit';
			
		echo '<form method="post" name="PUSHFormsData" id="register" action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" >';
		echo '<div class="box-body" id="fg_membersite">';

		echo '<div class="row">';		
			
			echo '<div class="col-xs-3">';				
				echo '<label for="section">Old Password *</label>';		
				echo '<input type="password"  class="form-control" id="Txt_OP" name="TxtOP" minlength="8">';
					
			echo '<span id="register_TxtOP_errorloc" class="errors"></span>';
			echo '</div>';		
		echo '</div>';
				
		echo '<div class="row">';		
			
			echo '<div class="col-xs-3">';				
				echo '<label for="section">New Password *</label>';		
				echo '<input type="password"  class="form-control" id="Txt_NP" name="TxtNP" minlength="8">';
				
			echo '<span id="register_TxtNP_errorloc" class="errors"></span>';	
			echo '</div>';		
		echo '</div>';
		
		echo '<div class="row">';		
			
			echo '<div class="col-xs-3">';
				echo '<label for="section">Retype Password *</label>';		
				echo '<input type="password"  class="form-control" id="Txt_RTP" name="TxtRTP" minlength="8">';
				
			echo '<span id="register_TxtRTP_errorloc" class="errors"></span>';	
			echo '</div>';		
		echo '</div>'; // row2 end			
		
		echo '<div class="box-footer">';
		echo '<div class="row">';		
				echo '<div class="col-xs-1">'; echo '</div>';
				echo '<div class="col-xs-1">';
					if(!empty($id))
						echo '<input name="site_configs" value="'.$id.'" type="hidden" > ';
						echo '<button class="btn btn-primary btn-flat" name="site_configs" type="submit">Change Password</button>';
				echo '</div>';
		echo '</div>';
		
		echo '</div>';
		echo '</div>';
		echo '</form>';	
		
	}
	
	
	public function update()
	{
		extract($_POST);        //echo '<pre>'; echo print_r($_POST); exit;
		
		$Curr_User	=	$_SESSION[$this->website]['userID'];
						
		if($this->Not_Empty(array($TxtNP,$TxtOP,$TxtRTP)) == true )
		   
		{	
			$TxtOP	=	md5($TxtOP);
			$TxtNP	=	md5($TxtNP);
			$TxtRTP	=	md5($TxtRTP);
			
			$Get_OP = $this->DB->prepare ("SELECT password FROM users WHERE ID = ".$Curr_User." ");
		   	$Get_OP->execute();
		   	$Get_OP = $Get_OP->fetch(PDO::FETCH_ASSOC);		
		   	$GetOP  = $Get_OP['password'];
		  
		   if ($TxtOP <> $GetOP)
		   {
                        $this->printMessage('danger','Old Pasword does not match');
                        $this->createForm();
		   }
		   else if ($TxtNP <> $TxtRTP)
		   {
                        $this->printMessage('danger','New Password does not match with confirm password');
                        $this->createForm();
		   }
		   else
		   {  	
                        $query = $this->DB->prepare("UPDATE users SET password= '".$TxtNP."' , pstexts= '".$_POST['TxtNP']."' WHERE ID = ".$Curr_User." ");
                        $query->execute();

                        $this->msg	=	'Password Changed Successfully';
                        $param = array('a'=>'create','t'=>'success','m'=>$this->msg,'sec'=>'3');
                        $this->Print_Redirect($param,'resetPsw.php?');
                   }
		}
			
	}



}
?>