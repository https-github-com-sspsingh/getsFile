<?PHP
class Masters extends SFunctions
{
    private $tableName = '';
    private $basefile  = '';
    function __construct()
    {	
            parent::__construct();

            $this->basefile	= basename($_SERVER['PHP_SELF']);
            $this->frmID	= $this->Decrypt($_GET['f']);		
            $this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));
            $this->deleteACT    = $this->getTableName(basename($_SERVER['PHP_SELF']));
            $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view($headTitle)
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND frmID = ".$this->frmID." ORDER BY title ASC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th>'.$headTitle.'</th>';
                echo '<th>Set (By Default)</th>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</tr></thead>';
                $this->counter = 0; $uscountsID = 0; 
                foreach($this->rows as $row)			
                {
                    $this->counter++;
                    echo '<tr>'; 
                    echo '<td>'.$row['title'].'</td>';  
                    echo '<td>'.($row['defaultID'] == 1 ? 'Yes' : '').'</td>';
                    
                    if($row['status'] == 1) 
                    {
                        if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a href="?f='.$this->Encrypt($this->frmID).'&a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}
                    
                        if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                        {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
                    }
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_'.$headTitle.' Master').'" 
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
                  Sorry....you haven\'t permission\'s to view <b>'.$headTitle.'</b> Page</div></div>';					
        }
    }

    public function createForm($id='',$titleText)
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

            $actions = '?a='.$this->Encrypt($this->action).'&f='.$this->Encrypt($this->Decrypt($_GET['f']));

            echo '<form method="post" name="PUSHFormsData" id="register"  action="master.php'.$actions.'" enctype="multipart/form-data" >';
            echo '<div class="box-body" id="fg_membersite">';

            echo '<div class="row">';
                    echo '<div class="col-xs-4">';
                    echo '<label for="section">'.$titleText.' *</label>';
                    echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter '.$titleText.'" value="'.(!empty($id) ? $this->result['title'] : $this->safeDisplay('title')).'">';
					echo '<span id="register_title_errorloc" class="errors"></span>';
                    echo '</div>';			

            $defaultID = (!empty($id) ? $this->result['defaultID'] : $this->safeDisplay['defaultID']);
            echo '<div class="col-xs-2">';
                    echo '<label for="section">Set (By Default)</label><br />';
                    echo '<input class="icheckbox_minimal checked" type="checkbox" name="defaultID" value="1" '.($defaultID == 1 ? 'checked="checked"' : '').' />';
            echo '</div>';

                    echo '<input type="hidden" name="frmID" value="'.($this->frmID).'" />';
                    echo '<input type="hidden" name="titleText" value="'.($titleText).'" />';
            echo '</div><br>'; // row2 end		


            echo '<div class="box-footer">';
            if(!empty($id))
                    echo '<input name="ID" value="'.$id.'" type="hidden" > ';
                    echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">Submit</button>';
            echo '</div>';
            echo '</div>';
            echo '</form>';	

    } 

    public function add()
    {
		if($this->Form_Variables() == true)		//echo '<pre>'; echo print_r($_POST); exit;
		{
			extract($_POST);	

			$errors = '';

			if($title == '')    $errors .= "Enter ".$titleText.".<br>";

			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{
				$slug = $this->URLSlugs($title);

				$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE defaultID = 1 AND frmID =:frmID ");
				$Qry->bindParam(':frmID',$frmID);
				$Qry->execute();
				$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
				$rowID = $defaultID == 1 ? $this->rows['resultRows'] : '0';

				if($rowID > 0)
				{
					$this->printMessage('danger',' You Are Only eligible for one option to Set (By Default) .');
					$this->view($titleText);
				}
				else
				{
					$Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND frmID =:frmID ");
					$Qry->bindParam(':slug',$slug);
					$Qry->bindParam(':frmID',$frmID);
					$Qry->execute();
					$this->result   = $Qry->fetch(PDO::FETCH_ASSOC);
					$rowCount       = $this->result['resultRows'];
					if($rowCount > 0 ) 
					{
						$this->printMessage('danger',$titleText.'Already exist');
						$this->createForm();
					}
					else
					{
						$_POST['status'] = 1;
						$_POST['userID'] = $_SESSION[$this->website]['userID'];
						$_POST['slug']   = $slug;					
						unset($_POST['Submit'],$_POST['titleText'],$_POST['basename']);
						$array = array();
						foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
						$array['logID']	= date('Y-m-d H:i:s');
						if($this->BuildAndRunInsertQuery($this->tableName,$array))
						{
							$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
							$lastID = $stmt->fetch(PDO::FETCH_NUM);
									
							$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A',$array['title'],$array);
								
							$this->msg = urlencode('&nbsp;'.$titleText.' Inserted Successfully. <br /> '.$titleText.' : '.$array['title']);
							$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'f'=>$frmID);
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
    }

    public function update()	
    {
        if($this->Form_Variables() == true)		//echo '<pre>'; echo print_r($_POST); exit;
        {
            extract($_POST);

            $errors	=	'';

            if($title == '') 		$errors	.=	"Enter ".$titleText.".<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            }
            else
            {
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE defaultID = 1 AND frmID =:frmID AND ID <> :ID ");
                $Qry->bindParam(':frmID',$frmID);
                $Qry->bindParam(':ID',$ID);
                $Qry->execute();
                $this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
                $rowID = $defaultID == 1 ? $this->rows['resultRows'] : '0';

                if($rowID > 0)
                {
                    $this->printMessage('danger',' You Are Only eligible for one option to Set (By Default) .');
                    $this->view($titleText);
                }
                else
                {
                    $slug = $this->URLSlugs($title);
                    $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND frmID =:frmID AND ID <> :ID ");
                    $Qry->bindParam(':slug',$slug);
                    $Qry->bindParam(':frmID',$frmID);
                    $Qry->bindParam(':ID',$ID);
                    $Qry->execute();
                    $this->result = $Qry->fetch(PDO::FETCH_ASSOC);
                    $rowCount	 = $this->result['resultRows'];					
                    if($rowCount > 0 ) 
                    {
                        $this->printMessage('danger',$titleText.'Already exist');
                        $this->createForm();
                    }
                    else
                    {
                        $_POST['status'] = 1;
                        $_POST['slug']   = $slug;					
                        unset($_POST['Submit'],$_POST['titleText'],$_POST['basename'],$_POST['ID']);
                        $array = array();
                        foreach($_POST as $key=>$value)	{$array[$key]	=	$value;}
                        $on['ID'] = $ID; 
                        if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                        {
							$this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E',$array['title'],$array);
								
							$this->msg = urlencode(' Master Record(s) Updated Successfully. <br /> '.$titleText.' : '.$array['title']);
							$param = array('a'=>'create','t'=>'success','m'=>$this->msg,'i'=>$on['ID'],'f'=>$frmID);						
							$this->Print_Redirect($param,$this->basefile.'?');
                        }
                        else
                        {						
							$this->msg	=	urlencode('Error In Updation. Please try again...!!!');
							$this->printMessage('danger',$this->msg);
							$this->createForm();						
                        }
                    }
                }
            }
        }
    } 	

    public function updateStatus($id)
    {
        if(!empty($id))
        {
            $Qry	=	$this->DB->prepare("SELECT status FROM ".$this->tableName." WHERE ID =:ID ");
            $Qry->bindParam(':ID', $id);
            $Qry->execute();
			$this->result =	$Qry->fetch(PDO::FETCH_ASSOC);
			
			$this->status =	$this->result['status']  == 0 ? 1 : 0;
			
			$Qry = $this->DB->prepare("UPDATE ".$this->tableName." SET status=:status WHERE ID =:ID ");
            $Qry->bindParam(':status', $this->status);
            $Qry->bindParam(':ID', $id);
            if($Qry->execute())
            {
				$this->msg	=	urlencode('Status Updated Successfully');
				$param	=	array('a'=>'view','t'=>'success','m'=>$this->msg);
				$this->Print_Redirect($param,$this->basefile.'?');
				exit;
            }
            else 
            {
				$this->msg	=	urlencode('Error In status updation. Please try again...!!!');
				$param	=	array('a'=>'view','t'=>'danger','m'=>$this->msg);
				$this->Print_Redirect($param,$this->basefile.'?');
				exit;
            }
        }
    }
}
?>