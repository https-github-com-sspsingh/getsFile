<?PHP
class Masters extends Functions
{
    private 	$tableName  = '';
    private	$basefile   = '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile	  = basename($_SERVER['PHP_SELF']);		
        $this->tableName  = 'currency';
    }

    public function view()
    { 
        $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." ORDER BY ID DESC ");
        if($Qry->execute())
        {
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTable" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th>Sr. No.</th>';
            echo '<th>Currency</th>';
            echo '<th></th>';
            echo '<th></th>';
            echo '</tr></thead>';
            $Start = 1;
            foreach($this->rows as $row)			
            {
                echo '<tr>';
                echo '<td align="center">'.$Start++.'</td>';
                echo '<td>'.$row['code'].'&nbsp;'.$row['title'].'</td>';

                $eclass = 'fa fa-edit'; 	 $elink   = '?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'';
                $rclass = 'fa fa-trash-o'; $Del_Opt = ' data-title="'.$this->tableName.'" data-ajax="'.$row['ID'].'"';

                echo '<td align="center"><a href="'.$elink.'" class="fa '.$eclass.'"></a></td>';				
                echo '<td align="center"><a '.$Del_Opt.' style="cursor:pointer;" class="fa '.$rclass.' Delete_Confirm"></a></td>'; 
            }
            echo '</table>';			
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
                    echo '<label for="section">Currency Sign <span class="Maindaitory">*</span></label>';
                    echo '<input type="text" class="form-control" id="code" name="code" placeholder="Enter Currency Sign" style="text-align:center;" 
                    value="'.(!empty($id) ? ($this->result['code']) : $this->safeDisplay('code')).'">';
            echo '</div>';	
    echo '</div><br />';	

    echo '<div class="row">';
            echo '<div class="col-xs-4">';
                    echo '<label for="section">Currency Name <span class="Maindaitory">*</span></label>';
                    echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter Currency Name" 
                    value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
            echo '</div>';	

            echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
    echo '</div>';

    echo '<div class="row">';
      echo '<div class="col-xs-2">';	
      if(!empty($id))
              echo '<input name="ID" value="'.$id.'" type="hidden">';
              echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Currency Master' : 'Save Currency Master').'</button>';
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

            if($code == '')     $errors .= "Enter The Currency Sign.<br>";
            if($title == '')    $errors .= "Enter The Currency Name.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }
            else
            {	
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title "); 
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
                    $_POST['status'] = 1;
                    $_POST['userID'] = $_SESSION[$this->website]['userID'];
                    $_POST['title'] = ucfirst($_POST['title']);
                    unset($_POST['Submit']);
                    $array = array();
                    foreach($_POST as $key=>$value)	{$array[$key] = $value;}
                    $array['logID']	= date('Y-m-d H:i:s');
                    if($this->BuildAndRunInsertQuery($this->tableName,$array))
                    {
                        $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
                        $lastID = $stmt->fetch(PDO::FETCH_NUM);

                        $this->msg = urlencode(' Currency Name Master Is Created (s) Successfully . <br /> Currency Name : '.$array['title']);						
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

            $errors = '';

            if($code == '')     $errors .= "Enter The Currency Sign.<br />";
            if($title == '')    $errors .= "Enter The Currency Name.<br />";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            {
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE title =:title AND ID <> :ID ");
                $Qry->bindParam(':title',$title);
                $Qry->bindParam(':ID',$ID);
                $Qry->execute();
                $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                $rowCount = $this->result['resultRows'];
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
                    foreach($_POST as $key=>$value)	{$array[$key] = $value;}
                    $on['ID'] = $ID;
                    if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                    { 					
                        $this->msg = urlencode('Currency Name Master Is Updated (s) Successfully . <br /> Currency Name : '.$array['title']);						
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