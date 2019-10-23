<?php
class FFunctions extends VFunctions
{
	public function Driver_Login_Request($ecodeID,$rdateID,$companyID,$requestTX)
	{
		if($ecodeID <> '' & $requestTX <> '')
		{
			$Insert = array();
			$Insert['ecode'] = $ecodeID;
			$Insert['rdate'] = $rdateID;
			$Insert['companyID'] = $companyID;
			$Insert['requestTX'] = $requestTX;		
			$Insert['dateID'] = date('Y-m-d');
			$Insert['timeID'] = date('h : i : A');
			$this->BuildAndRunInsertQuery('driver_login_status',$Insert);
		}
	}
	
	public function urolesSheets($uroleID,$requestID,$srID)
	{
		$fileRT = '';
		
		$Qry = $this->DB->prepare("Select Sum(1) As countRU, frmset.ftypeID From urole_dtl Inner Join frmset On frmset.ID = urole_dtl.frmID Where urole_dtl.ID In(".$uroleID.") And urole_dtl.noID ".$requestID." Group By frmset.ftypeID, urole_dtl.ID Order By frmset.ftypeID ASC ");
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);			
		if(is_array($this->rows) && count($this->rows) > 0) 
		{
			foreach($this->rows as $rowHD)
			{
				if($rowHD['ftypeID'] > 0)
				{
					$srID++;
					
					$fileRT .= '<div class="panel box box-success">';
					$fileRT .= '<div class="box-header">';
					$fileRT .= '<h4 class="box-title" style="font-size:16px;padding: 10px;padding-bottom: 10px;padding-bottom: 1px;">';
					$fileRT .= '<a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$srID.'">
					<b style="color:#F56954; margin-right: 15px;">'.$rowHD['countRU'].'</b> 
					
					<b style="color:#4D4D4D;">'.($rowHD['ftypeID'] == 1  ? 'Settings' 
					 :($rowHD['ftypeID'] == 2  ? 'LOV'
					 :($rowHD['ftypeID'] == 3  ? 'Masters'
					 :($rowHD['ftypeID'] == 4  ? 'Employee'
					 :($rowHD['ftypeID'] == 5  ? 'Driver Details'
					 :($rowHD['ftypeID'] == 6  ? 'Rostering'
					 :($rowHD['ftypeID'] == 7  ? 'All Set Reports'
					 :($rowHD['ftypeID'] == 8  ? 'Driver Performance'
					 :($rowHD['ftypeID'] == 9  ? 'Driver Signon'
					 :($rowHD['ftypeID'] == 10 ? 'Audit Trial'
					 :($rowHD['ftypeID'] == 11 ? 'Health & Safety' : ''))))))))))).'<b></a>';
					$fileRT .= '</h4>';
					$fileRT .= '</div>';
					
						$QryD = $this->DB->prepare("Select frmset.title From urole_dtl Inner Join frmset On frmset.ID = urole_dtl.frmID Where frmset.ftypeID In (".$rowHD['ftypeID'].") And urole_dtl.ID In(".$uroleID.") And urole_dtl.noID ".$requestID." Order By frmset.title ASC ");
						$QryD->execute();
						$this->rowsD = $QryD->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rowsD) && count($this->rowsD) > 0) 
						{
							$fileRT .= '<div id="collapse'.$srID.'" class="panel-collapse collapse">';
							$fileRT .= '<div class="box-body">';
							$partID = 1;
							foreach($this->rowsD as $rowsDTL)
							{
								$fileRT .= '<b style="color:green;">  -- </b><b style="color:black;">'.str_replace("_"," ",$rowsDTL['title']).'</b><br />';								
								$partID++;
							}
							$fileRT .= '</div>';
							$fileRT .= '</div>';
						}					
					$fileRT .= '</div>';
				}
			}
		}
		
		return $fileRT;
	}
	
	public function urolesformsSheets($uroleID,$ftypeID,$displayTX,$srID,$userID)
    {
		$fileRT = '';
		
        $fileRT .= '<div class="row">';
			$SQL = "Select frmset.ID, frmset.title, frmset.code From frmset Inner Join urole_dtl On frmset.ID = urole_dtl.frmID Where urole_dtl.ID In(".$uroleID.") And frmset.ftypeID In(".$ftypeID.") And urole_dtl.noID = 1 Order By frmset.code ASC ";
			$Qry = $this->DB->prepare($SQL);
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);			
            if(is_array($this->rows) && count($this->rows) > 0) 
            {
                foreach($this->rows as $row)
                {
					$arrPER = ($userID > 0 ? $this->select('users_sub_dtl',array("*"), " WHERE ID = ".$userID." AND frmID = ".$row['ID']." ") : '');
					
					$fileRT .= '<div class="row" style="border-top:1px solid #F4F4F4;padding:10px 0 0; margin:0;">';

					$fileRT .= '<div class="col-xs-2" style="text-align:left; ">';
					$fileRT .= '<label style="margin:0; color:blue;">'.($srID == 0 ? $displayTX : '').'</label>';
					$fileRT .= '</div>';
					
					$fileRT .= '<div class="col-xs-2" style="text-align:left; width: 220px;">';
					$fileRT .= '<label for="'.$row['code'].'" style="margin:0; color:'.($arrPER[0]['RecID'] > 0 ? 'red' : 'black').';">'.$row['code'].'</label>';
					$fileRT .= '<input type="hidden" name="code[]" id="'.$row['code'].'" value="'.$row['ID'].'" >';
					$fileRT .= '</div>';
					
					$fileRT .= '<div class="col-xs-2" style="text-align:left; width: 120px;">';
					$fileRT .= '<input type="checkbox" name="'.$row['ID'].'-add" id="'.$row['code'].'-add" value="1" '.($arrPER[0]['addID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
					$fileRT .= '<label for="'.$row['code'].'-add" style="margin:0; color:'.($arrPER[0]['RecID'] > 0 ? 'red' : 'black').';"  >Add</label>';
					$fileRT .= '</div>';

					$fileRT .= '<div class="col-xs-2" style="text-align:left; width: 120px;">';
					$fileRT .= '<input type="checkbox" name="'.$row['ID'].'-edit" id="'.$row['code'].'-edit" value="1" '.($arrPER[0]['editID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
					$fileRT .= '<label for="'.$row['code'].'-edit" style="margin:0; color:'.($arrPER[0]['RecID'] > 0 ? 'red' : 'black').';"  >Edit</label>';
					$fileRT .= '</div>';

					$fileRT .= '<div class="col-xs-2" style="text-align:left; width: 120px;">';
					$fileRT .= '<input type="checkbox" name="'.$row['ID'].'-view" id="'.$row['code'].'-view" value="1" '.($arrPER[0]['viewID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
					$fileRT .= '<label for="'.$row['code'].'-view" style="margin:0; color:'.($arrPER[0]['RecID'] > 0 ? 'red' : 'black').';"  >View</label>';
					$fileRT .= '</div>';

					$fileRT .= '<div class="col-xs-2" style="text-align:left; width: 120px;">';
					$fileRT .= '<input type="checkbox" name="'.$row['ID'].'-all" id="'.$row['code'].'-all" value="1" '.($arrPER[0]['allID'] == 1 ? 'checked="checked"' : '').' >&nbsp;';
					$fileRT .= '<label for="'.$row['code'].'-all" style="margin:0; color:'.($arrPER[0]['RecID'] > 0 ? 'red' : 'black').';"  >Full Access</label>';
					$fileRT .= '</div>';					
					$fileRT .= '</div>';	// row End

					$srID++; 
                }
				
				$fileRT .= '<div class="col-xs-12"><hr style="border:#4D4D4D 1px dotted;" /></div>';
            }
            
        $fileRT .= '</div>';
		
		return $fileRT;
    }
	
    public function RunTimeCalculate($hoursID,$tdayIID)
    {
        if($tdayIID > 0)
        {
            $setID = '';
            list($hour,$minute,$second) = explode(':', $hoursID);

            for($fdayIID = 1; $fdayIID <= $tdayIID; $fdayIID++)
            {
                $seconds += $hour * 3600;
                $seconds += $minute * 60;
                $seconds += $second;
            }

            $hours    = floor($seconds / 3600);
            $seconds -= $hours * 3600;
            $minutes  = floor($seconds /60);
            $seconds -= $minutes * 60;

            $minutes = $minutes > 0 ? ($minutes <= 9 && $minutes >= 1 ? "0".$minutes : $minutes) : "00";
            return (($hours > 0) || ($minutes > 0) ? "{$hours}:{$minutes}" : "00:00");
        }
        else	{return $hoursID;}
    }

    public function TimeAddMinues($time,$caseID) 
    {
        $timesID = strtotime($caseID." minutes", strtotime($time));            
        return date('H:i',$timesID);
    }

    public function CheckArrayDuplicacy($array)
    {
        $return = '';
        $array_temp = array();
        foreach($array as $val)    {if (!in_array($val, $array_temp))    {$array_temp[] = $val;} else {$return .= ($val <> '' ? ','.$val : '');}}             
        return $return;
    }

    public function GET_FirstCodes($valID)
    {
        $returnID = '';
        if($valID <> '')
        {
            foreach((explode(" ",$valID)) as $retID)
            {
                $returnID .= $retID[0];
            }
        }
        return strtoupper(str_replace("-","",$returnID));
    }

    public function GET_UserRoleforms($roleID)
    {
        $returnID = '';
        if(!empty($roleID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM urole_dtl WHERE ID = ".$roleID." AND (addID = 1 Or editID = 1 Or delID = 1 Or viewID = 1) ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);			
            if(is_array($this->rows) && count($this->rows) > 0) 
            {
                $srID = 1;
                foreach($this->rows as $row)
                {
                    $returnID .= $srID == 1 ? $row['frmID'] : ','.$row['frmID'];
                    $srID++;
                }
            }
        }
        return $returnID;
    }
	
    public function GET_ExtraPermissions($roleID,$frmID,$userID)
    {
        $uroleID  = $roleID;
        $roleID   = ($_SESSION[$this->website]['userRL'] > 0 ? $_SESSION[$this->website]['userRL'] : $roleID);
        $returnID = '';

        $returnID .= '<div class="row">';
        $Qry = $this->DB->prepare("SELECT * FROM frmset WHERE companyID = 1 AND ftypeID In(".$frmID.") ORDER BY title ASC ");
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);			
        if(is_array($this->rows) && count($this->rows) > 0) 
        {
            $srID = 1;  $recID = 0;  $upartID = 0;
			$setCOL = '';
			$setACT = '';
            foreach($this->rows as $row)
            {
                $UROLE = ($uroleID > 0 	 ? $this->select('urole_dtl',array("*"), " WHERE ID = ".$uroleID." AND frmID = ".$row['ID']." AND (addID = 1 Or editID = 1 Or delID = 1 Or viewID = 1) ") : '');
                $CHECK = ($row['ID'] > 0 ? $this->select('urole_dtl',array("*"), " WHERE ID = ".$roleID." AND frmID = ".$row['ID']." AND (addID = 1 Or editID = 1 Or delID = 1 Or viewID = 1) ")  : '');
                
                $recID = ($_SESSION[$this->website]['userTY'] == 'AD' ? 101 : $CHECK[0]['RecID']);
                $upartID = $UROLE[0]['RecID'] > 0 ? $UROLE[0]['RecID'] : 0;
                
                if($recID > 0 && (empty($upartID) || $upartID == 0))
                {
                    $perID = $this->GET_Extra_users_Permissions($userID,$row['ID']);
					
					$arrPER = ($userID > 0 ? $this->select('users_sub_dtl',array("*"), " WHERE ID = ".$userID." AND frmID = ".$row['ID']." ") : '');
					if($arrPER[0]['RecID'] > 0)
					{
						/* Already Assigned For Long Time*/
					}
					else
					{
						$returnID .= '<div class="row" style="border-top:1px solid #F4F4F4;padding:10px 0 0; margin:0;">';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<label for="'.$row['code'].'" style="margin:0">'.$row['code'].'</label>';
						$returnID .= '<input type="hidden" name="code[]" id="'.$row['code'].'" value="'.$row['ID'].'" >';
						$returnID .= '</div>';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<input type="checkbox" name="'.$row['ID'].'-del" id="'.$row['code'].'-del" value="1" '.($perID['delID'] == 1 ? 
						'checked="checked"' : '').'>&nbsp;';
						$returnID .= '<label for="'.$row['code'].'-del" style="margin:0"  >Delete</label>';
						$returnID .= '</div>';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<input type="checkbox" name="'.$row['ID'].'-add" id="'.$row['code'].'-add" value="1" '.($perID['addID'] == 1 ? 
						'checked="checked"' : '').'>&nbsp;';
						$returnID .= '<label for="'.$row['code'].'-add" style="margin:0"  >Add</label>';
						$returnID .= '</div>';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<input type="checkbox" name="'.$row['ID'].'-edit" id="'.$row['code'].'-edit" value="1" '.($perID['editID'] == 1 ? 
						'checked="checked"' : '').'>&nbsp;';
						$returnID .= '<label for="'.$row['code'].'-edit" style="margin:0"  >Edit</label>';
						$returnID .= '</div>';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<input type="checkbox" name="'.$row['ID'].'-view" id="'.$row['code'].'-view" value="1" '.($perID['viewID'] == 1 ? 
						'checked="checked"' : '').'>&nbsp;';
						$returnID .= '<label for="'.$row['code'].'-view" style="margin:0"  >View</label>';
						$returnID .= '</div>';

						$returnID .= '<div class="col-xs-2" style="text-align:left; ">';
						$returnID .= '<input type="checkbox" name="'.$row['ID'].'-all" id="'.$row['code'].'-all" value="1" '.($perID['allID'] == 1 ? 
						'checked="checked"' : '').'>&nbsp;';
						$returnID .= '<label for="'.$row['code'].'-all" style="margin:0"  >Full Access</label>';
						$returnID .= '</div>';					
						$returnID .= '</div>';	// row End		
						$srID++;
					}
                }
            }
        }
        $returnID .= '<div class="col-xs-12"><hr style="border:#3c8dbc 1px solid;" /></div>';
        $returnID .= '</div>';

        return $returnID;
    } 
	
    public function GET_SinglePermission($ID)
    {
        if($ID <> '' && !empty($ID))
        {
            $return = 0;
            
            $uroleID  = $_SESSION[$this->website]['userRL'];
            $levelID  = $_SESSION[$this->website]['userTY'];
            $ptypeID  = $_SESSION[$this->website]['userPR'];
            $usersID  = $_SESSION[$this->website]['userID'];
            $lgtypeID = $_SESSION[$this->website]['userLT'];
			
            if($levelID == 'AD')    {$return = 1;}
            else
            {
                if($uroleID > 0 && $levelID <> '')
                {
                    if($lgtypeID == 2)   {$SQL = "SELECT spermissionID FROM users WHERE ID > 0 AND DATE(tdateID) >= '".date('Y-m-d')."' AND ID = ".$usersID;}
                    else                {$SQL = "SELECT spermissionID FROM urole WHERE ID > 0 AND ID = ".$uroleID;}
                    
                    $Qry = $this->DB->prepare($SQL);
                    $Qry->execute();
                    $this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
                    if(count($this->rows['spermissionID']) > 0)
                    {
                        foreach((explode(",", $this->rows['spermissionID'])) as $refID)
                        {
                            if($refID > 0 && ($refID <> '') && !empty($refID))
                            {
                                if($refID == $ID)       {$return = 1;break;}    
                                else                    {$return = 0;}
                            }
                        }
                    }
                    else        {$return = 0;}
                }
                else            {$return = 0;}
            }
            
            return $return;
        }        
    }

    public function ReportingBundels($ID,$WHERE)
    { 
        $return = '';		
        $companyID = $_SESSION[$this->website]['compID'];
        $mnrolesID = 2;
		
		/*AND uroleID = 2*/
        $SQL = "SELECT * FROM users WHERE driverID > 0 Order By dcodeID ASC ";
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';	$returnID = 0;
		$statusID = 0;	$empID = array();
		
        foreach($this->rows as $rows)
        {
            foreach((explode(",",$rows['reportingID'])) as $rcompanyID)
            {
                if(($rcompanyID == $companyID) && ($rows['driverID'] > 0))
                {
					$empID[] = $rows['driverID'];
					
					$arrEM = $rows['driverID'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows['driverID']."  ") : '';
					$crtID = ($rows['driverID'] == $ID ? 'selected="selected"' : '');
					$return .= '<option '.$crtID.' aria-sort="'.$arrEM[0]['code'].'" value="'.$rows['driverID'].'">'.strtoupper($arrEM[0]['fname'].' '.$arrEM[0]['lname'].' - '.$rows['dcodeID']).'</option>';
                }
            }
        }
		
		if(in_array($ID,$empID))
			{$returnID = 1;}
		else
			{$returnID = 2;}
		
		if($returnID == 2 && $ID > 0 && $ID <> 1001)
		{		
			$arrEM = $ID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$ID." ") : '';
			$return .= '<option selected="selected" value="'.$arrEM[0]['ID'].'">'.strtoupper($arrEM[0]['fname'].' '.$arrEM[0]['lname'].' - '.$arrEM[0]['code']).'</option>';
		}
		else
		{
			if($ID > 0 && $ID == 1001)
{
			$return .= '<option value="1001" selected="selected">No Interview Required </option>';
}
		}
			
        return $return;
    }

    public function GET_Mechanic_Employees($ID,$WHERE)
    {
        $return = '';
		$SQL = "SELECT * FROM employee WHERE ID > 0 ".($ID > 0 ? "" : "AND status = 1")." ".$WHERE." Order By fname,lname ASC ";
		$Qry = $this->DB->prepare($SQL);
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		$crtID = '';
		foreach($this->rows as $rows)
		{
			$crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
			$return .= '<option aria-title="'.$rows['phone'].'" aria-busy="'.$rows['phone_1'].'" aria-sort="'.$rows['code'].'" '.$crtID.' value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].' - '.$rows['code'].'</option>';
		}
		return $return;
    }
	
    public function GET_Spares_Employees($ID,$WHERE)
    {
        $return = '';
        $SQL = "SELECT * FROM employee WHERE ID > 0 ".($ID > 0 ? "" : "AND status = 1")." ".$WHERE." Order By fname,lname ASC ";
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            $crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
            $return .= '<option aria-sort="'.$rows['code'].'" '.$crtID.' value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].' - '.$rows['code'].'</option>';
            
        }
        return $return;
    }
		
    public function GET_Employees($ID,$WHERE)
    {
        $return = '';

        $SQL = "SELECT * FROM employee WHERE ID > 0 ".($ID > 0 ? "" : "AND status = 1")." AND companyID In(".($_SESSION[$this->website]['compID']).") ".$WHERE." Order By fname,lname ASC ";
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            $crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
           // $return .= '<option aria-sort="'.$rows['code'].'" '.$crtID.' value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].'</option>';
            $return .= '<option aria-sort="'.$rows['code'].'" '.$crtID.' value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].' - '.$rows['code'].'</option>';
            
        }
        return $return;
    }

    public function GET_SubDepotLists($ID,$frmID)
    {
        $return = '';
		
		if($_SESSION[$this->website]['scompID'] <> '')
		{
			$return .= '<div class="col-xs-3">';
			$return .= '<label for="section">Sub Depot <span class="Maindaitory">*</span></label>';
			if($frmID == 37 && $ID > 0)
			{
				$arrSD   = $this->select('company_dtls',array("title,pscode"), " WHERE ID = ".$ID." ");
				$return .= '<input type="text" class="form-control" readonly="readonly" value="'.$arrSD[0]['title'].' - '.$arrSD[0]['pscode'].'">';
			}
			else
			{
				$return .= '<input type="hidden" name="sstatusID" id="sstatusID" value="1" />';
				
				$return .= '<select name="scompanyID" class="form-control" id="scompanyID">';
				$return .= '<option value="0" selected="selected"> --- Sub Depot --- </option>';
				
					$SQL = "SELECT * FROM company_dtls WHERE companyID > 0 AND status = 1 AND ID In(".$_SESSION[$this->website]['scompID'].") Order By title ASC ";        
					$Qry = $this->DB->prepare($SQL);
					$Qry->execute();
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					$crtID = '';		
					foreach($this->rows as $rows)
					{
						$crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
						$return .= '<option '.$crtID.' value="'.$rows['ID'].'">'.$rows['title'].' - '.$rows['pscode'].'</option>';
						
					}
				
				$return .= '</select>';
				$return .= '<span id="register_scompanyID_errorloc" class="errors"></span>';
			}
			$return .= '</div>';
		}
		
        return $return;
    }
	
    public function GET_Employees11($ID,$WHERE)
    {
        $return = '';
		$SQL = "SELECT * FROM employee WHERE ID > 0 ".($ID > 0 ? "" : "AND status = 1")." ".$WHERE." Order By fname,lname ASC ";        
        $Qry = $this->DB->prepare($SQL);
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            $crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
            $return .= '<option aria-sort="'.$rows['code'].'" '.$crtID.' value="'.$rows['ID'].'">'.$rows['fname'].' '.$rows['lname'].' - '.$rows['code'].'</option>';
            
        }
        return $return;
    }

    public function GET_Masters($ID,$frmID)
    {
        $return = '';

        $WHERE .= " AND frmID = ".$frmID;
        $Qry = $this->DB->prepare("SELECT * FROM master WHERE ID > 0 ".$WHERE." Order By title ASC ");
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            $crtID = $ID > 0 ? ($rows['ID'] == $ID ? 'selected="selected"' : '') :($rows['defaultID'] == 1 ? 'selected="selected"' : "");
            $return .= '<option value="'.$rows['ID'].'" '.$crtID.'>'.$rows['title'].'</option>';
        }
        return $return;
    }

    public function GET_SubUrbs($ID,$WHERE)
    {
        $return = ''; 

        $Qry = $this->DB->prepare("SELECT * FROM suburbs WHERE ID > 0 ".$WHERE." Order By title ASC ");
        $Qry->execute();
        $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $crtID = '';
        foreach($this->rows as $rows)
        {
            $crtID = ($rows['ID'] == $ID ? 'selected="selected"' : '');
            $return .= '<option aria-sort="'.$rows['pscode'].'" value="'.$rows['ID'].'" '.$crtID.'>'.$rows['title'].' - '.$rows['pscode'].'</option>';
        }
        return $return;
    }

    public function PopUpsAccidents($ID)
    {
        $return = '';
        if(!empty($ID) && ($ID > 0))
        {
            $Qry = $this->DB->prepare("SELECT * FROM accident_regis_dtl WHERE ID > 0 AND ID = ".$ID." Order By recID ASC ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
            foreach($this->rows as $rows)
            {
                $return .= '<tr>';
                    $return .= '<td align="center"><span style="cursor:pointer;" aria-sort="'.$rows['recID'].'" class="fa fa-trash-o remove_acpop"></span></td>';
                    $return .= '<td width="140"><input type="text" class="form-control datepicker" name="fieldID_1[]" style="text-align:center;" required="required" placeholder="Date" value="'.($this->VdateFormat($rows['fieldID_1'])).'"></td>';
                    $return .= '<td width="890"><input type="text" class="form-control" name="fieldID_2[]" required="required" placeholder="Accidents Detail/Remarks" value="'.$rows['fieldID_2'].'"></td>';
                $return .= '</tr>';

                $srID++;
            }   
        }
        return $return;
    }
        
	public function GET_Extra_users_Permissions($userID,$frmID = 0)
	{
            $return	=	array();
            if($frmID > 0)
                    //$SQL = "SELECT * FROM users_dtl WHERE ID = :userID AND frmID = :frmID";
                    $SQL = "SELECT users_dtl.* FROM users INNER JOIN users_dtl ON users_dtl.ID = users.ID WHERE DATE(users.tdateID) >= '".date('Y-m-d')."' AND users.lgtypeID = 2 AND users_dtl.ID = :userID AND users_dtl.frmID = :frmID ";
            else
                    $SQL = "SELECT users_dtl.* FROM users INNER JOIN users_dtl ON users_dtl.ID = users.ID WHERE DATE(users.tdateID) >= '".date('Y-m-d')."' AND users.lgtypeID = 2 AND users_dtl.ID = :userID ";

            $query = $this->DB->prepare($SQL);
            $query->bindParam(':userID',$userID);		
            if($frmID > 0 ) $query->bindParam(':frmID',$frmID);		
            if($query->execute())
            {	
                    if($frmID > 0)	$this->results	= $query->fetch(PDO::FETCH_ASSOC);
                    else		$this->results	= $query->fetchAll(PDO::FETCH_ASSOC);			
                    $return	=	$this->results;
            }		
            return $return;
	}
	
}
?>
