<?PHP
class Masters extends SFunctions
{
    function __construct()
    {
		parent::__construct();

		$this->basefile	  	= basename($_SERVER['PHP_SELF']);		
		$this->tableName    = $this->getTableName(basename($_SERVER['PHP_SELF']));	
		$this->frmID	    = '50';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }

    public function view()
    {
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
        {    
            $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 ".($_SESSION[$this->website]['userTY'] == 'AD' ? "" : "AND ID <> 5")." ORDER BY ID DESC ");
            if($Qry->execute())
            {
                $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTable" class="table table-bordered table-striped">';			
                echo '<thead><tr>'; 
                echo '<th>User Role</td>';
                echo '<th>Single Permission</td>';
                echo '<th>No of Forms</td>';
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
                echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
                echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
                echo '</thead></tr>';                
                foreach($this->rows as $row)			
                {
                    echo '<tr>'; 
                    echo '<td>'.$row['title'].'</td>'; 
                    echo '<td>'.$this->ViewPermissions($row['spermissionID']).'</td>';                    
                    echo '<td align="center">'.($this->count_rows('urole_dtl', " WHERE ID = ".$row['ID']." AND noID <= 0 ")).'</td>';

                    if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

                    if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$this->frmID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';}
					
					if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
					{
						if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
						{
							echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_User Role\'s').'" style="text-decoration:none; cursor:pointer;"></a></td>';
						}
						else	{echo '<td></td>';}
					}	
                }
                echo '</table>';			
            } 
        }
        else	{echo '<div class="row"><div class="col-xs-12" style="min-height:200px;margin-top:150px; text-align:center">Sorry....you haven\'t permission\'s to view <b>User Role\'s</b> Page</div></div>';}
    } 
	
    public function ViewPermissions($permissionID)
    {
        if($permissionID <> '')
        {
            $strID = '';
            $csID = 1;
            $cdID = '';
            foreach((explode(",",$permissionID)) as $day_ID)
            {
                $cdID = $day_ID == 1 ? 'Manager Comments' :($day_ID == 2 ? 'Warning Types' : '');                
                $strID .= $csID == 1 ? '<b>'.$csID.'</b> : '.$cdID : '<br /><b>'.$csID.'</b> : '.$cdID;
                $csID++;
            }
        }
        return $strID;
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

		echo '<form method="post" name="PUSHFormsData" id="register"  action="?a='.$this->Encrypt($this->action).'" enctype="multipart/form-data" >';
		echo '<div class="box-body" id="fg_membersite">';

		echo '<div class="row">';		
			echo '<div class="col-xs-4">';
					echo '<label for="section">User Role\'s Name <span class="Maindaitory">*</span></label>';
					echo '<input type="text" class="form-control" id="title" name="title" placeholder="Enter User Role\'s Name" 
					value="'.(!empty($id) ? ($this->result['title']) : $this->safeDisplay('title')).'">';
					echo '<span id="register_title_errorloc" class="errors"></span>';
			echo '</div>';	
			
			echo '<div class="col-xs-4">';
				echo '<label for="section">Single Permission</label>';
				echo '<select class="form-control" name="spermissionID[]" id="mcompanyID" multiple="multiple">';
				$spermissionID = (!empty($id) ? $this->result['spermissionID'] : $this->safeDisplay('spermissionID'));
				$permissionsID = explode(",",$spermissionID);
					echo '<option value="1" '.(in_array(1,$permissionsID) ? 'selected="selected"' : '').'>Manager Comments</option>';
					echo '<option value="2" '.(in_array(2,$permissionsID) ? 'selected="selected"' : '').'>Warning Types</option>';
				echo '</select>';
			echo '</div>';
			
			echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
		echo '</div>';
		
		$this->createChildForm($id);
		
		echo '<div class="row">';
		  echo '<div class="col-xs-2">';	
		  if(!empty($id))
			echo '<input name="ID" value="'.$id.'" type="hidden">';
			echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update User Role\'s' : 'Save User Role\'s').'</button>';
		  echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</form>';
	}
	
    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);		 //echo '<PRE>'; echo print_r($_POST); exit;

            if($title == '') 		$errors .= "Enter The User Role Name.<br>";

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
				$rowCount = $this->result['resultRows'];
				if($rowCount > 0 ) 
				{
					$this->printMessage('danger','Already exist');
					$this->createForm();
				}
				else
				{
					unset($_POST['Submit']);
					$array = array();
					$array['title']     = ucfirst(strtolower($_POST['title']));
					$array['slug']	  = $slug;
					$array['tpermissionID'] = implode(",", $tpermissionID);
					$array['spermissionID'] = implode(",", $spermissionID);
					$array['companyID'] = '1';
					$array['status']	= '1';
					$array['userID'] 	= $_SESSION[$this->website]['userID'];
					$array['logID']	 = date('Y-m-d H:i:s');
					if($this->BuildAndRunInsertQuery($this->tableName,$array))
					{
						$stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
						$lastID = $stmt->fetch(PDO::FETCH_NUM);
						
						$this->PUSH_userlogsID($this->frmID,$lastID[0],'',0,'','','A');

						if(is_array($code) and count($code) > 0 && ($lastID[0] > 0))
						{							
							foreach($code as $form)
							{	
								$nopID  = 0;
								$nopID  = (((isset($_POST[$form.'-del'])  ? $_POST[$form.'-del']  : 0) == 0) && 
										   ((isset($_POST[$form.'-edit']) ? $_POST[$form.'-edit'] : 0) == 0) && 
										   ((isset($_POST[$form.'-add'])  ? $_POST[$form.'-add']  : 0) == 0) && 
										   ((isset($_POST[$form.'-view']) ? $_POST[$form.'-view'] : 0) == 0) && 
										   ((isset($_POST[$form.'-all'])  ? $_POST[$form.'-all']  : 0) == 0) 
										   ? 1: 0);

								if($form > 0)
								{
									$arr = array();
									$arr['ID'] 	 = $lastID[0];
									$arr['frmID']  = $form;
									$arr['addID']  = (isset($_POST[$form.'-add'])	 ?	$_POST[$form.'-add']	 :	0);
									$arr['editID'] = (isset($_POST[$form.'-edit'])	?	$_POST[$form.'-edit']	:	0);
									$arr['delID']  = (isset($_POST[$form.'-del'])	 ?	$_POST[$form.'-del']	 :	0);
									$arr['viewID'] = (isset($_POST[$form.'-view'])	?	$_POST[$form.'-view']	:	0);
									$arr['allID']  = (isset($_POST[$form.'-all'])	 ?	$_POST[$form.'-all']	 :	0);
									$arr['noID']   = $nopID;
									$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);	
								}
							}
						}

						$this->msg = urlencode(' User Role Master Is Created (s) Successfully . <br /> User Role : '.$array['title']);
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

            if($title == '') 		$errors .= "Enter The User Role Name.<br>";

            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm($ID);
            }
            else
            {
                $slug = $this->URLSlugs($title);
                $Qry = $this->DB->prepare("SELECT count(*) as resultRows FROM ".$this->tableName." WHERE slug =:slug AND ID <>:ID "); 
                $Qry->bindParam(':slug',$slug);
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
                        unset($_POST['Submit']);
                        $array = array();
                        $array['title']     = ucfirst(strtolower($_POST['title']));
                        $array['slug']	  = $slug;
						$array['tpermissionID'] = implode(",", $tpermissionID);
                        $array['spermissionID'] = implode(",", $spermissionID);
                        $array['companyID'] = '1';
                        $array['status']	= '1';
                        $on['ID'] = $ID;
                        if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                        {
							$this->PUSH_userlogsID($this->frmID,$ID,'',0,'','','E');
							
                            if(is_array($code) and count($code) > 0 && ($ID > 0))
                            {
                                $this->delete($this->tableName.'_dtl', " WHERE ID = ".$ID." ");

                                foreach($code as $form)
                                {	
                                    $nopID  = 0;
                                    $nopID  = (((isset($_POST[$form.'-del'])  ? $_POST[$form.'-del']  : 0) == 0) && 
                                               ((isset($_POST[$form.'-edit']) ? $_POST[$form.'-edit'] : 0) == 0) && 
                                               ((isset($_POST[$form.'-add'])  ? $_POST[$form.'-add']  : 0) == 0) && 
                                               ((isset($_POST[$form.'-view']) ? $_POST[$form.'-view'] : 0) == 0) && 
                                               ((isset($_POST[$form.'-all'])  ? $_POST[$form.'-all']  : 0) == 0) 
                                               ? 1: 0);

                                    if($form > 0)
                                    {
										$arrs = array();
										$arrs['ID'] 	 = $ID;
										$arrs['frmID']  = $form;
										$arrs['addID']  = (isset($_POST[$form.'-add'])	 ?	$_POST[$form.'-add']	 :	0);
										$arrs['editID'] = (isset($_POST[$form.'-edit'])	?	$_POST[$form.'-edit']	:	0);
										$arrs['delID']  = (isset($_POST[$form.'-del'])	 ?	$_POST[$form.'-del']	 :	0);
										$arrs['viewID'] = (isset($_POST[$form.'-view'])	?	$_POST[$form.'-view']	:	0);
										$arrs['allID']  = (isset($_POST[$form.'-all'])	 ?	$_POST[$form.'-all']	 :	0);
										$arrs['noID']   = $nopID;
										$this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs);	
                                    }
                                }
                            }

                            $this->msg = urlencode(' User Role Master Is Updated (s) Successfully . <br /> User Role : '.$array['title']);
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
	 
	
    public function createChildForm($ID)
    {
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<div class="nav-tabs-custom">';
        echo '<ul class="nav nav-tabs">';
        echo '<li class="active"><a href="#tab_1" data-toggle="tab"><b>Settings</b></a></li>';
        echo '<li><a href="#tab_2" data-toggle="tab"><b>LOV</b></a></li>';
        echo '<li><a href="#tab_3" data-toggle="tab"><b>Masters</b></a></li>';
        echo '<li><a href="#tab_4" data-toggle="tab"><b>Employee</b></a></li>';
        echo '<li><a href="#tab_5" data-toggle="tab"><b>Driver Details</b></a></li>';		
        echo '<li><a href="#tab_6" data-toggle="tab"><b>Rostering</b></a></li>';
        echo '<li><a href="#tab_7" data-toggle="tab"><b>All Set Reports</b></a></li>';
        echo '<li><a href="#tab_8" data-toggle="tab"><b>Driver Performance</b></a></li>';
		echo '<li><a href="#tab_9" data-toggle="tab"><b>Driver SignOn</b></a></li>';
		echo '<li><a href="#tab_10" data-toggle="tab"><b>Health & Safety</b></a></li>';
        echo '</ul>';
		
        echo '<div class="tab-content">';
			echo '<div class="tab-pane active" id="tab_1">'.$this->urolesData($ID,"1,10").'</div>';			
			echo '<div class="tab-pane" id="tab_2">'.$this->urolesData($ID,"2").'</div>';			
			echo '<div class="tab-pane" id="tab_3">'.$this->urolesData($ID,"3").'</div>';			
			echo '<div class="tab-pane" id="tab_4">'.$this->urolesData($ID,"4").'</div>';			
			echo '<div class="tab-pane" id="tab_5">'.$this->urolesData($ID,"5").'</div>';			
			echo '<div class="tab-pane" id="tab_6">'.$this->urolesData($ID,"6").'</div>';			
			echo '<div class="tab-pane" id="tab_7">'.$this->urolesData($ID,"7").'</div>';			
			echo '<div class="tab-pane" id="tab_8">'.$this->urolesData($ID,"8").'</div>';			
			echo '<div class="tab-pane" id="tab_9">'.$this->urolesData($ID,"9").'</div>';			
			echo '<div class="tab-pane" id="tab_10">'.$this->urolesData($ID,"11").'</div>';				
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } 
    
    public function urolesData($ID,$frmID)
    { 
		$fileRT = '';
		
        $fileRT .= '<div class="row">';
            $Qry = $this->DB->prepare("SELECT * FROM frmset WHERE companyID = 1 AND ftypeID In(".$frmID.") ORDER BY code ASC ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);			
            if(is_array($this->rows) && count($this->rows) > 0) 
            {
                $srID = 1;
                foreach($this->rows as $row)
                {
                    $this->form_PER  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$row['ID']);
                    
                    if($this->form_PER['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
                    {
                        $per = $this->UserPermissions($ID,$row['ID']);
                        $fileRT .= '<div class="row" style="border-top:1px solid #F4F4F4;padding:10px 0 0; margin:0;">';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<label for="'.$row['code'].'" style="margin:0">'.$row['code'].'</label>';
                        $fileRT .= '<input type="hidden" name="code[]" id="'.$row['code'].'" value="'.$row['ID'].'" >';
                        $fileRT .= '</div>';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<input type="checkbox" name="'.$row['ID'].'-del" id="'.$row['code'].'-del" value="1" '.($per['delID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
                        $fileRT .= '<label for="'.$row['code'].'-del" style="margin:0"  >Delete</label>';
                        $fileRT .= '</div>';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<input type="checkbox" name="'.$row['ID'].'-add" id="'.$row['code'].'-add" value="1" '.($per['addID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
                        $fileRT .= '<label for="'.$row['code'].'-add" style="margin:0"  >Add</label>';
                        $fileRT .= '</div>';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<input type="checkbox" name="'.$row['ID'].'-edit" id="'.$row['code'].'-edit" value="1" '.($per['editID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
                        $fileRT .= '<label for="'.$row['code'].'-edit" style="margin:0"  >Edit</label>';
                        $fileRT .= '</div>';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<input type="checkbox" name="'.$row['ID'].'-view" id="'.$row['code'].'-view" value="1" '.($per['viewID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
                        $fileRT .= '<label for="'.$row['code'].'-view" style="margin:0"  >View</label>';
                        $fileRT .= '</div>';

                        $fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
                        $fileRT .= '<input type="checkbox" name="'.$row['ID'].'-all" id="'.$row['code'].'-all" value="1" '.($per['allID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
                        $fileRT .= '<label for="'.$row['code'].'-all" style="margin:0"  >Full Access</label>';
                        $fileRT .= '</div>';					
                        $fileRT .= '</div>';	// row End

                        $srID++;
                    }
                }
            }
            $fileRT .= '<div class="col-xs-12"><hr style="border:#3c8dbc 1px solid;" /></div>';
        $fileRT .= '</div>';
		
		return $fileRT;
    } 
}
?>