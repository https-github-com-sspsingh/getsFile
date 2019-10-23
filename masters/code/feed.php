<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		

		$this->basefile	  =	basename($_SERVER['PHP_SELF']);		
		$this->tableName     =	'feed';
                
                $this->frmID	    = '68';
                $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
            if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
            {    
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName."  ORDER BY statusID,dateID desc   ");
		
		if($Qry->execute())
		{
                    $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                    echo '<table id="dataTable" class="table table-bordered table-striped">';				
                    echo '<thead><tr>'; 
                    echo '<th>Date</th>';
                    echo '<th>Feedback Type</th>';
                    echo '<th>Comments</th>';
                    echo '<th>Status</th>';
                    echo '<th>Response</th>';
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">User</th>' : '');
                    echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : ''); 

                    echo '</tr></thead>';
                    $srID = 1;
                    foreach($this->rows as $row)			
                    { 
                        $US_Array  = $row['userID'] > 0     ? $this->select('users',array("username"), " WHERE ID = ".$row['userID']." ") : '';
                        $FB_Array  = $row['ftypeID'] > 0    ? $this->select('master',array("title"), " WHERE ID = ".$row['ftypeID']." ") : '';

                        echo '<tr>';
                        echo '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';
                        echo '<td>'.$FB_Array[0]['title'].'</td>';
                        echo '<td>'.$row['comments'].'</td>';
                        echo '<td '.($row['statusID'] == 1 ? 'style="color:red;"' :($row['statusID'] == 2 ? 'style="color:green;"' : '')).'><b>'.($row['statusID'] == 1 ? 'Pending' :($row['statusID'] == 2 ? 'Complete' : '')).'</b></td>';
                        echo '<td>'.$row['response'].'</td>';

                        $eclass = 'fa fa-edit'; 	 $elink   = '?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'';
                        echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? ('<td>'. strtoupper($US_Array[0]['username']).'</td>') : '');
                        echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? ('<td align="center"><a href="'.$elink.'" class="fa '.$eclass.'"></a></td>') : '');
                    }
                    echo '</table>';			
		} 
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
		echo '<div class="col-xs-2">';
			echo '<label for="section">Date </label>';
			echo '<input type="text" class="form-control datepicker" id="dateID" name="dateID" placeholder="Enter Date" 
			style="text-align:center;" value="'.(!empty($id) ? $this->VdateFormat($this->result['dateID']) : ' ').'">';
		echo '</div>'; 
		
		echo '<div class="col-xs-2">';
			echo '<label for="section">Time </label>';
			echo '<input type="text" class="form-control" id="timeID" name="timeID" placeholder="Enter Time" 
			style="text-align:center;" value="'.(!empty($id) ? $this->result['timeID'] : date('h : i : A')).'">';
		echo '</div>'; 
		
	echo '</div><br />';
	
		
	echo '<div class="row">';
		echo '<div class="col-xs-4">';
			echo '<label for="section">Please select the type of feedback:<span class="Maindaitory">*</span></label>';
			echo '<select name="ftypeID" class="form-control" id="ftypeID">';
				echo '<option value="0" selected="selected"> --- Select --- </option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['ftypeID'] : $this->safeDisplay['ftypeID']),'30');
			echo '</select>';
		echo '</div>'; 
	echo '</div><br />';
        
	echo '<div class="row">';		
		echo '<div class="col-xs-8">';
			echo '<label for="section">Please enter the comments here</label>';
			echo '<textarea style="resize:none;" class="form-control" rows="2" name="comments" 
			placeholder="Enter Comments">'.(!empty($id) ? $this->result['comments'] : $this->safeDisplay['comments']).'</textarea>';
		echo '</div>';		
	echo '</div><br />';
        
        if($_SESSION[$this->website]['userTY'] == 'AD' || ($_SESSION[$this->website]['userID'] == 3) || $_SESSION[$this->website]['userRL'] == 9)
        {
            echo '<div class="row">';
		echo '<div class="col-xs-2">';
                    echo '<label for="section">Status</label>';
                    echo '<select name="statusID" class="form-control" id="statusID">';
                        $statusID = (!empty($id) ? ($this->result['statusID']) : $this->safeDisplay('statusID'));
                        echo '<option value="0" selected="selected"> --- Select --- </option>';
                        echo '<option value="1" '.($statusID == 1 ? 'selected="selected"' : '').'>Pending</option>';
                        echo '<option value="2" '.($statusID == 2 ? 'selected="selected"' : '').'>Complete</option>';
                    echo '</select>';
		echo '</div>';
                
		echo '<div class="col-xs-6">';
                    echo '<label for="section">Response</label>';
                    echo '<textarea style="resize:none;" class="form-control" rows="2" name="response" id="response" placeholder="Enter Response">'.(!empty($id) ? $this->result['response'] : $this->safeDisplay['response']).'</textarea>';
		echo '</div>';
            echo '</div><br />';	
        }
        
	echo '<div class="row">';
	  echo '<div class="col-xs-2">';
	  if(!empty($id))												
                  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Report Details' : 'Save Report Details').'</button>';
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
			
			if($ftypeID == '') 		  $errors .= "Select The Feed Back Type.<br>";
			if($comments == '') 	 	  $errors .= "Enter Your Comments.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{	
                            $_POST['dateID']   = $this->dateFormat($_POST['dateID']);
                            $_POST['userID']   = $_SESSION[$this->website]['userID'];
                            $_POST['statusID'] = $_POST['statusID'] > 0 ? $_POST['statusID'] : 1;
                            unset($_POST['Submit']);
                            
                            $array = array();
                            foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                            $array['logID'] = date('Y-m-d H:i:s');
                            //echo '<PRE>'; echo print_r($array);exit;
                            //echo '<PRE>'; echo print_r($_POST); exit;
                            if($this->BuildAndRunInsertQuery($this->tableName,$array))
                            {
                                    $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                                    $lastID = $stmt->fetch(PDO::FETCH_NUM);
                                    $this->msg = urlencode(' Feed Back Is Created (s) Successfully.');
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
	
                        if($ftypeID == '') 		  $errors .= "Select The Feed Back Type.<br>";
			if($comments == '') 	 	  $errors .= "Enter Your Comments.<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{
                                $_POST['dateID']  = $this->dateFormat($_POST['dateID']);
                                $_POST['statusID'] = $_POST['statusID'] > 0 ? $_POST['statusID'] : 1;
                                unset($_POST['Submit'],$_POST['ID']);

                                $array = array();
                                foreach($_POST as $key=>$value)	{$array[$key] = $value;}
                                $on['ID'] = $ID;
                                if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                                { 					
                                        $this->msg = urlencode(' Feedback Is submitted Successfully.');
                                        $param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$ID);						
                                        $this->Print_Redirect($param,$this->basefile.'?');													}
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