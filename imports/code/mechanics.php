<?PHP
class Masters extends SFunctions
{
    private $tableName  =   '';
    private $basefile   =  '';

    function __construct()
    {	
        parent::__construct();		

        $this->basefile     = basename($_SERVER['PHP_SELF']);		
        $this->tableName    = 'mechanic_mst';
		
        $this->frmID        = '82';
        $this->permissions  = $this->GET_formPermissions($_SESSION[$this->website]['userRL'],$this->frmID);
    }
    
    public function createForm()
    {
        echo '<div class="box-body" style="margin-top: -23px;" id="fg_membersite">';
		
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<div class="nav-tabs-custom">';
        echo '<ul class="nav nav-tabs">';
        echo '<li class="active"><a href="#tab_1" data-toggle="tab"><b>Beenyup, Karrinyup & Shenton Park</b></a></li>';
        echo '<li><a href="#tab_2" data-toggle="tab"><b>Midvale & Beckenham</b></a></li>';
        echo '<li><a href="#tab_3" data-toggle="tab"><b>Canning Vale & Southern River</b></a></li>';
		echo '<li><a href="#tab_4" data-toggle="tab"><b>Bunbury & Busselton</b></a></li>';
		echo '<li><a href="#tab_5" data-toggle="tab"><b>Albany</b></a></li>';
        echo '</ul>';
		
		echo '<div class="tab-content">';
			
			/* Beenyup, Karrinyup & Shenton Park */
			echo '<div class="tab-pane active" id="tab_1">';
				echo '<div class="row" id="genMCH_FORM_1">';
				echo '<div class="col-xs-12">';
					echo '<table id="NSdataTables_1" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="7" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Beenyup, Karrinyup & Shenton Park - Mechanic Lists</th></tr></thead>';
					echo '<thead><tr>';
					echo '<th style="text-align:center !important;" colspan="2"></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Day</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mechanic Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Telepone</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile</th>';
					echo '</tr></thead>';
						$this->displayListing('1');		$this->createChildForm('1');
					echo '</table>';
				echo '</div>';
				
				echo '</div>';
            echo '</div>';
			
			/* Midvale & Beckenham */
			echo '<div class="tab-pane" id="tab_2">';
				echo '<div class="row" id="genMCH_FORM_2">';
				echo '<div class="col-xs-12">';
					echo '<table id="NSdataTables_2" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="7" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Midvale & Beckenham - Mechanic Lists</th></tr></thead>';

					echo '<thead><tr>';
					echo '<th style="text-align:center !important;" colspan="2"></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Day</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mechanic Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Telepone</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile</th>';
					echo '</tr></thead>';
						$this->displayListing('2');		$this->createChildForm('2');
					echo '</table>';
				echo '</div>';         
 
				echo '</div>';
            echo '</div>';
			
			/* Canning Vale & Southern River */
			echo '<div class="tab-pane" id="tab_3">';
				echo '<div class="row" id="genMCH_FORM_3">';
				echo '<div class="col-xs-12">';
					echo '<table id="NSdataTables_3" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="7" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Canning Vale & Southern River - Mechanic Lists</th></tr></thead>';

					echo '<thead><tr>';
					echo '<th style="text-align:center !important;" colspan="2"></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Day</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mechanic Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Telepone</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile</th>';
					echo '</tr></thead>';
						$this->displayListing('3');		$this->createChildForm('3');
					echo '</table>';
				echo '</div>'; 

				 
				echo '</div>'; 
            echo '</div>';
			
			/* Bunbury & Busselton */
			echo '<div class="tab-pane" id="tab_4">';
				echo '<div class="row" id="genMCH_FORM_4">';
				echo '<div class="col-xs-12">';
					echo '<table id="NSdataTables_4" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="7" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Bunbury & Busselton - Mechanic Lists</th></tr></thead>';

					echo '<thead><tr>';
					echo '<th style="text-align:center !important;" colspan="2"></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Day</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mechanic Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Telepone</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile</th>';
					echo '</tr></thead>';
						$this->displayListing('4');		$this->createChildForm('4');
					echo '</table>';
				echo '</div>'; 

				 
				echo '</div>'; 
            echo '</div>';
			
			/* Albany */
			echo '<div class="tab-pane" id="tab_5">';
				echo '<div class="row" id="genMCH_FORM_5">';
				echo '<div class="col-xs-12">';
					echo '<table id="NSdataTables_5" class="table table-bordered table-striped">';				
					echo '<thead><tr><th colspan="7" style="background-color:#367FA9 !important; text-align:center !important; color:white;">Albany - Mechanic Lists</th></tr></thead>';

					echo '<thead><tr>';
					echo '<th style="text-align:center !important;" colspan="2"></th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Day</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mechanic Name</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Telepone</th>';
					echo '<th style="text-align:center !important; color:#2F6F95;">Mobile</th>';
					echo '</tr></thead>';
						$this->displayListing('5');		$this->createChildForm('5');
					echo '</table>';
				echo '</div>'; 

				 
				echo '</div>'; 
            echo '</div>';
			
        echo '</div>';
		
        echo '</div>';
        echo '</div>';
        echo '</div>';  
    }

    public function createChildForm($typeID)
    {
		
        $Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE dateID >= '".date('Y-m-d')."' AND typeID = ".$typeID." Order By DATE(dateID) ASC ");
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		if(is_array($this->rows) && count($this->rows) > 0)
		{
			foreach($this->rows as $rows)    
			{
				$arrID = ($rows['empID'] > 0 ? $this->select('employee',array("full_name"), " WHERE ID = ".$rows['empID']." ") : '');

                echo '<tr>';
                    echo '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o mechrowID" aria-sort="'.$rows['recID'].'"></span></td>';
					echo '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-edit mecheditID" aria-sort="'.$rows['recID'].'"></span></td>';
					
                    echo '<td width="110" align="center">'.$this->VdateFormat($rows['dateID']).'</td>';
                    echo '<td width="110" align="center">'.$rows['dayID'].'</td>';                        
                    echo '<td>'.$arrID[0]['full_name'].'</td>';                        
                    echo '<td width="110" align="center">'.$rows['phone_1'].'</td>';
                    echo '<td width="110" align="center">'.$rows['phone_2'].'</td>';
                echo '</tr>';
			}
		}
    }
			
    public function displayListing($typeID)
    {
        if($typeID > 0)
        {
			$Qry = $this->DB->prepare("SELECT * FROM ".$this->tableName." WHERE (DATE(dateID) BETWEEN '".date('Y-m-d',strtotime('last monday'))."' AND '".date('Y-m-d',strtotime(date('Y-m-d').'-1 Days'))."') AND typeID = ".$typeID." Order By DATE(dateID) ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            foreach($this->rows as $rows)    
            {
                $arrID = ($rows['empID'] > 0 ? $this->select('employee',array("full_name"), " WHERE ID = ".$rows['empID']." ") : '');

                echo '<tr>';
                    echo '<td colspan="2" width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o mechrowID" aria-sort="'.$rows['recID'].'"></span></td>';
                    echo '<td width="110" align="center">'.$this->VdateFormat($rows['dateID']).'</td>';
                    echo '<td width="110" align="center">'.$rows['dayID'].'</td>';                        
                    echo '<td>'.$arrID[0]['full_name'].'</td>';                        
                    echo '<td width="110" align="center">'.$rows['phone_1'].'</td>';
                    echo '<td width="110" align="center">'.$rows['phone_2'].'</td>';
                echo '</tr>';
            }
        }
    }

    public function add()
    {	
        if($this->Form_Variables() == true)
        {
            extract($_POST);		//echo '<PRE>'; echo print_r($_POST); exit;
			
			$arrPOST = $_POST;
			
			$count_recsID = count($recID);
			$count_typeID = count($typeID);
			
			$count_recsID = $count_recsID > 0 ? $count_recsID : 0;
			
            //if(is_array($typesID) && count($typesID) > 0)  {} else {$errors .= "Kindly Select Depot For Mechanic Master.<br>";}
			
            if(!empty($errors))
            {
                $this->printMessage('danger',$errors);
                $this->createForm();
            } 
            else
            {
				$returnID = 0;
				
				if(is_array($rollID) && count($rollID) > 0)
				{
					$types_ID	 = 0;
					foreach ($rollID as $key=>$roll_ID)
					{
						$types_ID = $typesID[$key];
						
						if($roll_ID > 0 && $typesID > 0)
						{
							$fID_1 = $arrPOST[$roll_ID.'_dateID_'.$types_ID];
							$fID_2 = $arrPOST[$roll_ID.'_dayID_'.$types_ID];
							$fID_3 = $arrPOST[$roll_ID.'_empID_'.$types_ID];
							$fID_4 = $arrPOST[$roll_ID.'_phone_'.$types_ID];
							$fID_5 = $arrPOST[$roll_ID.'_mobile_'.$types_ID];
							
							$arr = array();
							$arr['typeID']  = $types_ID;
							$arr['dateID']  = $this->dateFormat(trim($fID_1));
							$arr['dayID']   = $fID_2;
							$arr['empID']   = $fID_3;
							$arr['phone_1'] = $fID_4;
							$arr['phone_2'] = $fID_5;
							$arr['userID']  = $_SESSION[$this->website]['userID'];
							$arr['logID']   = date('Y-m-d H:i:s');
							if($this->BuildAndRunInsertQuery($this->tableName,$arr))
									{$returnID += 1;}
							else	{$returnID += 0;}
						}
					}
				}
				
				if($count_recsID > 0)
				{
					if(is_array($recID)  && (count($recID) > 0))
					{
						foreach ($recID as $key=>$rec_ID)
						{
							if(!empty($rec_ID) && ($rec_ID > 0))
							{
								$ars = array();
								$ars['typeID']  = $typeID[$key];
								$ars['dateID']  = $this->dateFormat(trim($dateID[$key]));
								$ars['dayID']   = $dayID[$key];
								$ars['empID']   = $empID[$key];
								$ars['phone_1'] = $phone[$key];
								$ars['phone_2'] = $mobile[$key];
								$ons['recID']   = $rec_ID;
								if($this->BuildAndRunUpdateQuery($this->tableName,$ars,$ons))
										{$returnID += 1;}
								else	{$returnID += 0;}
							}
						}
						
						if($count_typeID > $count_recsID) // NEW - INSERT
						{
							for($i = $count_recsID; $i < $count_typeID; $i++)
							{
								$ass = array();
								$ass['typeID']  = $typeID[$i];
								$ass['dateID']  = $this->dateFormat(trim($dateID[$i]));
								$ass['dayID']   = $dayID[$i];
								$ass['empID']   = $empID[$i];
								$ass['phone_1'] = $phone[$i];
								$ass['phone_2'] = $mobile[$i];
								$ass['userID']  = $_SESSION[$this->website]['userID'];
								$ass['logID']   = date('Y-m-d H:i:s');
								if($this->BuildAndRunInsertQuery($this->tableName,$ass))
										{$returnID += 1;}
								else	{$returnID += 0;}
							}
						}
					}
				}
				
				
                if($returnID > 0)
                {
					$Qry = $this->DB->prepare("UPDATE mechanic_mst INNER JOIN employee ON mechanic_mst.empID = employee.ID SET mechanic_mst.phone_1 = employee.phone WHERE mechanic_mst.phone_1 = '' ");
					$Qry->execute();
					
					$Qry = $this->DB->prepare("UPDATE mechanic_mst INNER JOIN employee ON mechanic_mst.empID = employee.ID SET mechanic_mst.phone_2 = employee.phone_1 WHERE mechanic_mst.phone_2 = '' ");
					$Qry->execute();
					
					$this->msg = urlencode('Mechanic Master is Created/Updated Successfully .');
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