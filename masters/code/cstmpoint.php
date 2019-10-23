<?PHP
class Masters extends SFunctions
{
	private	$tableName	=	'';
	private	$basefile	 =	'';
	
	function __construct()
	{	
		parent::__construct();		
		
		$this->basefile	    = basename($_SERVER['PHP_SELF']);		
		$this->tableName    = 'cstpoint';
		$this->companyID	= $_SESSION[$this->website]['compID'];		
		$this->frmID	    = '36';
		$this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
	}
	
	public function view()
	{
        if($this->permissions['viewID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
		{
		$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE ID > 0 AND companyID In(".($this->companyID).")  ORDER BY ID DESC ");
		if($Qry->execute())
		{
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTable" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th>Sr. No</th>';
			echo '<th>Contract Name</th>';
			echo '<th>Service No</th>';
			echo '<th>Time Points</th>';
			echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Edit</th>' : '');
			echo (($this->permissions['delID'] == 1  || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Delete</th>' : '');
			echo (($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD') ? '<th style="background:none !important;">Log</th>' : '');
			echo '</tr></thead>';
			$Start = 1; $uscountsID = 0; 
			foreach($this->rows as $row)			
			{ 
				$CN_Array  = $row['contractID'] > 0  ? $this->select('master',array("title"), " WHERE ID = ".$row['contractID']." ") : '';
				$SR_Array  = $row['serviceID'] > 0   ? $this->select('srvdtls',array("codeID"), " WHERE ID = ".$row['serviceID']." ") : '';

				/* VOUCHER - DETAILS */
				$Qry = $this->DB->prepare("SELECT recID,fileID_1 FROM cstpoint_dtl WHERE ID = :ID ORDER BY ID DESC ");
				$Qry->bindParam(':ID',$row['ID']);
				$Qry->execute();
				$this->row = $Qry->fetchAll(PDO::FETCH_ASSOC);
				$srID = 1;
				$stID = '';
				$send_recID = '';
				foreach($this->row as $lastID)
				{
					$stID .= $srID == 1 ? '<b>'.$srID.'.</b> '.$lastID['fileID_1'] : '<br /><b>'.$srID.'.</b> '.$lastID['fileID_1'];
					$send_recID .= $srID == 1 ? $lastID['recID'] : ','.$lastID['recID'];
					$srID++;
				}

				echo '<tr>';
				echo '<td align="center">'.$Start++.'</td>';
				echo '<td>'.$CN_Array[0]['title'].'</td>';
				echo '<td>'.$SR_Array[0]['codeID'].'</td>';
				echo '<td>'.$stID.'</td>';

				if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
				{echo '<td align="center"><a href="?a='.$this->Encrypt('create').'&i='.$this->Encrypt($row['ID']).'" class="fa fa fa-edit"></a></td>';}

				if($this->permissions['delID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
				{
					if($send_recID <> '')
					{
						echo '<td align="center"><a data-title="'.$this->tableName.'" data-rel="'.$send_recID.'" data-ajax="'.$row['ID'].'" style="cursor:pointer;" class="fa fa fa-trash-o Delete_Confirm"></a></td>';
					}
					else	{echo '<td align="center"></td>';}
				}
				
				
				if($this->permissions['editID'] == 1 || $_SESSION[$this->website]['userTY'] == 'AD')
				{
					if(($this->count_rows('uslogs', " WHERE vouID = ".$row['ID']." AND frmID = ".$this->frmID." ")) > 0)
					{
						echo '<td align="center"><a class="fa fa fa-users POPUP_uslogsID" aria-sort="'.($this->frmID.'_'.$row['ID'].'_Service Time Point\'s').'" style="text-decoration:none; cursor:pointer;"></a></td>';
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
                      Sorry....you haven\'t permission\'s to view <b>Service Time Point\'s Master</b> Page</div></div>';
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
		echo '<div class="col-xs-4">';
			echo '<label for="section">Contract</label>';
			echo '<select class="form-control" id="contractID" name="contractID">';
				echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
				echo $this->GET_Masters((!empty($id) ? $this->result['contractID'] : $this->safeDisplay['contractID']),'28');
				echo '<span id="register_contractID_errorloc" class="errors"></span>';
			echo '</select>';			
		echo '</div>';
                
		echo '<div class="col-xs-3">';
                echo '<label for="section">Service No</label>';
                echo '<select class="form-control" name="serviceID" id="servicenoID">';
                echo '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';
                if(!empty($this->result['serviceID']) && !empty($this->result['contractID']))
                {
                    $Qry = $this->DB->prepare("SELECT * FROM cnserviceno WHERE ID > 0 AND contractID = ".$this->result['contractID']." Order By ID ASC");		
                    if($Qry->execute())
                    {
                        $this->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
                        foreach($this->crow as $mrow)	
                        {
                            if($mrow['serviceID'] <> '')
                            {
                                $mrowID = explode(",",$mrow['serviceID']);

                                foreach($mrowID as $lastID)
                                {
                                    $EM_Array = $lastID > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$lastID." ") : '';

                                    echo '<option value="'.$lastID.'" 
                                    '.($this->result['serviceID'] == $lastID ? 'selected="selected"' : '').'>'.$EM_Array[0]['codeID'].'</option>';		
                                }
                            }				
                        }
                    }	
                }
                echo '</select>';
				echo '<span id="register_serviceID_errorloc" class="errors"></span>';			
		echo '</div>';
	echo '</div><br />';
	
	echo '<div class="row">';
		echo '<div class="col-xs-7">';
			echo '<table id="dataTablesID" class="table table-bordered table-striped">';
                        echo '<thead><tr>';
			echo '<th style="text-align:center !important; background:#367fa9; font-weight:600; font-size:14px;color:white;" colspan="2">Contract - Service - Time Point Details</th>';			
			echo '</tr></thead>';
                        
			echo '<thead><tr>';
			echo '<th style="text-align:center !important;"><a style="cursor:pointer; text-decoration:none;" class="fa fa-plus srvgridID"></a></th>';
			echo '<th style="text-align:center !important; color:#2F6F95;">Time Point Name</th>';
			echo '</tr></thead>';
				if(!empty($id) && ($id > 0))
				{
					$this->create_childForm($id);	
				} 
			echo '</table>';
		echo '</div>';
	echo '</div><br>';
        
        echo '<input type="hidden" name="companyID" value="'.($this->companyID).'" />';
        
	echo '<div class="row">';
		echo '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';	
		
	  echo '<div class="col-xs-2">';	
	  if(!empty($id))
		  echo '<input name="ID" value="'.$id.'" type="hidden">';
		  echo '<button class="btn btn-primary btn-flat" name="Submit" type="submit">'.(!empty($id) ? 'Update Service Time Point\'s' : 
		  'Save Service Time Point\'s').'</button>';
	  echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</form>';
	}
	
	public function create_childForm($ID)
	{
		if(!empty($ID) && ($ID > 0))
		{
			$classsID = 'type="text" class="form-control';
			
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName."_dtl WHERE ID=:ID ");
			$Qry->bindParam(':ID',$ID);
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->rows) && (count($this->rows) > 0))
			{
				foreach($this->rows as $rows)
				{
					echo '<tr>';					
					echo '<td align="center"><span style="cursor:pointer;" class="fa fa-trash-o DLBTN"></span></td>';
                                        echo '<input type="hidden" name="recID[]" value="'.$rows['recID'].'" />';
					echo '<td><input '.$classsID.'" name="fileID_1[]" placeholder="Vehicle Reg No" value="'.$rows['fileID_1'].'"></td>';
					echo '</tr>';
				}
			}
		}
	}
	
	public function add()
	{	
		if($this->Form_Variables() == true)
		{
			extract($_POST);					 // echo '<PRE>'; echo print_r($_POST); exit;
			
			if($contractID == '')				$errors .= "Please Select The Contract Name.<br>";
			if($serviceID == '')				 $errors .= "Please Select The Service No.<br>";
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm();
			}
			else
			{ 
			  unset($_POST['Submit']);
			  
			  $array = array();					
			  foreach($_POST as $key=>$value)	{$array[$key] = $value;}
			  $array['status']		 = 1;
			  $array['logID']	= date('Y-m-d H:i:s');
			  if($this->BuildAndRunInsertQuery($this->tableName,$array))
			  {
				  $stmt = $this->DB->query("SELECT LAST_INSERT_ID()");
				  $lastID = $stmt->fetch(PDO::FETCH_NUM);
				  
				  if($lastID[0] > 0 && (count($fieldID_2) > 0))
				  {
					  foreach($fieldID_2 as $key=>$fileID)
					  {
						  if(!empty($fileID) && ($fileID <> ''))
						  {
							  $arr = array();
							  $arr['ID'] = $lastID[0];
							  $arr['contractID'] = $contractID;							  
							  $arr['serviceID']  = $serviceID;
							  $arr['fileID_1']   = $fileID; 
							  $this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arr);
						  }
					  }
				  }
				  
				  $this->msg = urlencode(' Service Time Point\'s Is Created (s) Successfully.');
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
			
			if($contractID == '')				$errors .= "Please Select The Contract Name.<br>";
			if($serviceID == '')				 $errors .= "Please Select The Service No.<br>";	
			
			if(!empty($errors))
			{
				$this->printMessage('danger',$errors);
				$this->createForm($ID);
			}
			else
			{ 
                            $array = array();
                            $array['serviceID']   = $serviceID;
                            $array['contractID']  = $contractID;
                            $array['companyID']   = $companyID;
                            $on['ID'] = $ID; 
                            if($this->BuildAndRunUpdateQuery($this->tableName,$array,$on))
                            { 
                              if($ID > 0 && (count($fileID_1) > 0))
                              {
                                  if(count($fileID_1) > count($recID))
                                  {
                                      for($i = count($recID); $i < count($fileID_1); $i++)
                                      {
                                          $arrs = array();
                                          $arrs['ID']         = $ID;
                                          $arrs['contractID'] = $contractID;
                                          $arrs['serviceID']  = $serviceID;
                                          $arrs['companyID']   = $companyID;
                                          $arrs['fileID_1']   = $fileID_1[$i]; 
                                          $this->BuildAndRunInsertQuery($this->tableName.'_dtl',$arrs);
                                      }
                                  }

                                  foreach($recID as $key=>$rec_ID)
                                  {
                                      if(!empty($rec_ID) && ($rec_ID <> ''))
                                      {
                                          $ars = array();
                                          $ars['contractID'] = $contractID;							  
                                          $ars['serviceID']  = $serviceID;
                                          $ars['companyID']   = $companyID;
                                          $ars['fileID_1']   = $fileID_1[$key];
                                          $ons['RecID'] 	 = $rec_ID;
                                          $this->BuildAndRunUpdateQuery($this->tableName.'_dtl',$ars,$ons);
                                      }
                                  }
                              }

                                    $this->msg = urlencode('Contract - Service Details Is Updated (s) Successfully .');
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
?>