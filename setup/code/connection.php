<?php
class DB extends Defines
{
	public $DB 		= '';
	function __construct()
	{
		try 
		{		  
		  $this->DB = new PDO("mysql:host=$this->DB_Host;dbname=$this->DB_Name", $this->DB_Username, $this->DB_Password);
		  $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		  $this->DB->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);
		}
		catch(PDOException $e) 
		{
			echo $e->getMessage();
			//$this->Print_Redirect($URL = null,'Connection Failed with database','Error');
		}
	}
		
	public function PUSH_userlogsID($frmID,$vouID,$vouDT,$empID,$empCD,$refNO,$actionID,$descTX,$transLOG)
	{
		if(!empty($frmID) && !empty($vouID) && !empty($actionID))
		{
			$array = array();
			$array['frmID'] = $frmID;
			$array['vouID'] = $vouID;
			$array['vouDT'] = $vouDT;
			$array['empID'] = $empID;
			$array['empCD'] = $empCD;
			$array['refNO'] = $refNO;				
			$array['actionID'] = $actionID;
			$array['companyID'] = $_SESSION[$this->website]['compID'];
			$array['userID'] = $_SESSION[$this->website]['userID'];
			$array['dateID'] = date('Y-m-d');
			$array['timeID'] = date('h : i : s : A');
			$array['descTX']   = base64_encode($descTX);
			$array['transLOG'] = base64_encode(print_r($transLOG,true));
			$this->BuildAndRunInsertQuery('uslogs',$array);
		}
	}
			
	public function CheckIntOrStrings($varID)
	{
		$return = 0;
		if(!empty($varID))
		{
			$lenID = strlen($varID);
			$strID = 0; $intID = 0;
			for($csID = 0; $csID <= $lenID; $csID++)
			{
				$strID += (!is_numeric($varID[$csID]) ? 1 : 0);
				$intID += (is_numeric($varID[$csID])  ? 1 : 0);
			}
		}
		// 1. String , 2. Interger            
		$return = (($strID > $intID) ? '1' :($intID > $strID ? '2' : '0'));            
		return $return;
	}

	public function max_seriesID($field,$table, $where)
	{ 
            $results  =	''; 
            $Qry    =	"SELECT MAX(".$field.") as fieldID FROM ".$table." ".$where."";
            $Qry    =	$this->DB->prepare($Qry);		
            if($Qry->execute())
            {
                $this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
                if(is_array($this->rows) && count($this->rows) > 0)
                {
                    $row = $this->rows['fieldID'];
                    $results = !empty($row) || ($row > 0) ? $row : 0;
                }
            }
            return $results;		 
        }
        
	public function Sum_files($field,$table, $where)
	{ 
		$results   =	''; 
		$Qry	 =	"SELECT SUM(".$field.") as RW FROM ".$table." ".$where."";		
		$Qry	 =	$this->DB->prepare($Qry);
		
		if($Qry->execute())
		{
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
			if(is_array($this->rows) && count($this->rows) > 0)
			{
				$row	= $this->rows['RW'];
				$results	=	!empty($row) || ($row > 0) ? $row : 0;
			}
		}
		return $results;		 
	}
	
	public function count_rows($table, $where)
	{ 
		$results  =	''; 
		$Qry = "SELECT count(*) as rows FROM ".$table." ".$where."";		
		//echo '<br /> : '.$Qry;	
		$Qry = $this->DB->prepare($Qry);
		if($Qry->execute())
		{
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
			if(is_array($this->rows) && count($this->rows) > 0)
			{
				$row	 = $this->rows['rows'];
				$results = !empty($row) || ($row > 0) ? $row : 0;
			}
		}
		return $results;		 
	}
	
	public function delete($table, $where)
	{ 
		$results   = ''; 
		$Qry = "DELETE FROM ".$table." ".$where."";
		$Qry = $this->DB->prepare($Qry);
		
		if($Qry->execute())
			return true;
						
		return false;	 
	}
	
	public function select($table, $fields, $where)
	{
		$results = array();
		$fields	 = implode(", ",$fields);	
		$Qry	 = "SELECT ".$fields." FROM ".$table." ".$where."";
		$Qry	 = $this->DB->prepare($Qry);		
		if($Qry->execute())
		{
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($this->rows) && count($this->rows) > 0)
			{
				foreach($this->rows as $row)
				{
					$results[] = $row;
				}
			}
		}
		return $results;		
	}
	
	public function BuildAndRunInsertQuery($tablename,$fields)
	{
		$sql	= "INSERT INTO ".$tablename." SET ";		
		$params	= array();		
		foreach($fields as $key=>$value)
		{
			$value	= trim($value);
			if(!empty($value))
			{	
				$sql.= $key . " = ". ":".$key.', ';
				$params[':'.$key] = $value;
			}
		}
		$sql = trim($sql);
		$sql = substr($sql,0,(strlen($sql)-1));
		
		$Qry = $this->DB->prepare($sql);
		if($Qry->execute($params))
			return true;
			
		return false;		
	}
	
	public function BuildAndRunUpdateQuery($tablename,$fields,$on)
	{
		$sql	=	"UPDATE ".$tablename." SET ";		
		$params	=	array();
		foreach($fields as $key=>$value)
		{
			$value	=	($value > 0 || !empty($value) || $value == 0) ? $value : NULL;
			if(is_null($value) || !empty($value) || $value == 0)
			{ 
				$sql.=	$key." = ". ":".$key.', ';
				$params[':'.$key] =	$value;
			}
		}
		$sql = trim($sql);
		$sql = substr($sql,0,(strlen($sql)-1));		
		if(is_array($on) && count($on) > 0)
		{
			$starter =	0;
			$sql .=	" WHERE ";	
			foreach($on as $key=>$value)
			{
				$value = trim($value);
				if(!empty($value))
				{	
					$sql.=	($starter > 0) ? ' AND ' : '';
					$sql.=	$key . " = ". ":".$key.', ';		
					$params[':'.$key]	=	$value;
				}
				$starter++;
			}
		}
		$sql = trim($sql);
		$sql = substr($sql,0,(strlen($sql)-1));
		$Qry = $this->DB->prepare($sql);
		if($Qry->execute($params))
			return true;
			
		return false;		
	}
	
	public function GetDayLists($dayID)
	{
		$return = '';
		if (!empty($dayID))
		{
			$return = $dayID == 1 ? 'Monday' :($dayID == 2 ? 'Tuesday' :($dayID == 3 ? 'Wednesday' :($dayID == 4? 'Thursday' :($dayID == 5 ? 'Friday' :($dayID == 6 ? 'Saturday' :($dayID == 7 ? 'Sunday' : ''))))));
		}
		return $return;	
	} 
        
	public function UserPermissions($roleID,$frmID = 0)
	{
		$return	=	array();
		
		$SQL = "SELECT * FROM urole_dtl WHERE ID = :roleID ".($frmID > 0 ? "AND frmID = :frmID" : "" )." ";
		$Qry = $this->DB->prepare($SQL);
		$Qry->bindParam(':roleID',$roleID);		
		if($frmID > 0 ) $Qry->bindParam(':frmID',$frmID);		
		if($Qry->execute())
		{	
			if($frmID > 0)	$this->results	= $Qry->fetch(PDO::FETCH_ASSOC);
			else		$this->results	= $Qry->fetchAll(PDO::FETCH_ASSOC);			
			$return	=	$this->results;
		}		
		return $return;
	}
        
	public function GET_formPermissions($roleID,$frmID)
	{
		$userID  = $_SESSION[$this->website]['userID'];
		
		if(!empty($roleID) && !empty($frmID))
		{
			if($_SESSION[$this->website]['userPR'] == 2 || $_SESSION[$this->website]['userPR'] == 3)
			{
				/* CHECK Login-Life Permissions */
				$countLF = $userID > 0  ? $this->count_rows('users_sub_dtl', " WHERE ID = ".$userID." AND frmID = ".$frmID." ") : '';
				$countLF = $countLF > 0 ? $countLF : 0;
				if($countLF > 0)
				{
					$Qry = $this->DB->prepare("SELECT * FROM users_sub_dtl WHERE ID = :userID AND frmID = :frmID ");
					$Qry->bindParam(':userID',$userID);		
					$Qry->bindParam(':frmID',$frmID);
				}
				else
				{
					/* CHECK Actual Permissions */
					$countID = $userID > 0  ? $this->count_rows('users_dtl', " WHERE ID = ".$userID." AND frmID = ".$frmID." ") : '';
					$countID = $countID > 0 ? $countID : 0;
					if($countID > 0)
					{
						$Qry = $this->DB->prepare("SELECT * FROM users_dtl WHERE ID = :userID AND frmID = :frmID ");
						$Qry->bindParam(':userID',$userID);		
						$Qry->bindParam(':frmID',$frmID);
					}
					else
					{
						$Qry = $this->DB->prepare("SELECT * FROM urole_dtl WHERE ID = :roleID AND frmID = :frmID ");
						$Qry->bindParam(':roleID',$roleID);		
						$Qry->bindParam(':frmID',$frmID);
					}
				}
			}
			else
			{
				$Qry = $this->DB->prepare("SELECT * FROM urole_dtl WHERE ID = :roleID AND frmID = :frmID ");
				$Qry->bindParam(':roleID',$roleID);		
				$Qry->bindParam(':frmID',$frmID);
			}
			
			$Qry->execute();		
			$this->result = $Qry->fetch(PDO::FETCH_ASSOC);			
			return $this->result;
		}
	}
        
	public function GET_menusPerms($frmID)
	{
		$return = '' ;
		if(!empty($frmID))
		{
			$roleID = $_SESSION[$this->website]['userRL'];
			$utyeID = $_SESSION[$this->website]['userTY'];
			$userID = $_SESSION[$this->website]['userID'];
			
			if($utyeID == 'AD') {$return = 1;}
			else
			{
				//if($_SESSION[$this->website]['userPR'] == 2 && $_SESSION[$this->website]['userLT'] == 2)					
				if($_SESSION[$this->website]['userPR'] == 2 || $_SESSION[$this->website]['userPR'] == 3)
				{
					/* CHECK Login-Life Permissions */
					$countLF = $userID > 0  ? $this->count_rows('users_sub_dtl', " WHERE ID = ".$userID." AND frmID In(".$frmID.") ") : '';
					$countLF = $countLF > 0 ? $countLF : 0;
					if($countLF > 0)
					{
						$Qry = $this->DB->prepare("Select Sum(Final.View) As View From (Select Sum(users_sub_dtl.viewID) As View From users_sub_dtl Where users_sub_dtl.ID = ".$userID." And users_sub_dtl.frmID In (".$frmID.") Group By users_sub_dtl.ID, users_sub_dtl.frmID) As Final");
						$Qry->execute();
						$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
						$return = !empty($this->rows['View']) || ($this->rows['View'] > 0) ?  1 : 0 ;
					}
					else
					{						
						$usersID  = $this->count_rows('users', " WHERE ID = ".$userID." AND DATE(tdateID) >= '".date('Y-m-d')."' ");
						$countID = $userID > 0  ? $this->count_rows('users_dtl', " WHERE ID = ".$userID." AND frmID In (".$frmID.") ")  : '';
						$countID = $countID > 0 ? $countID : 0;
						if($countID > 0 && $usersID > 0)
						{
							$Qry = $this->DB->prepare("Select Sum(Final.View) As View From (Select Sum(users_dtl.viewID) As View From users_dtl Where users_dtl.ID = ".$userID." And users_dtl.frmID In (".$frmID.") Group By users_dtl.ID, users_dtl.frmID) As Final");
							$Qry->execute();
							$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
							$return = !empty($this->rows['View']) || ($this->rows['View'] > 0) ?  1 : 0 ;		
						}
						else
						{
							$Qry = $this->DB->prepare("Select Sum(Final.View) As View From (Select Sum(urole_dtl.viewID) As View From urole_dtl Where urole_dtl.ID = ".$roleID." And urole_dtl.frmID In (".$frmID.") Group By urole_dtl.ID, urole_dtl.frmID) As Final");
							$Qry->execute();
							$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
							$return = !empty($this->rows['View']) || ($this->rows['View'] > 0) ?  1 : 0 ;		
						}
					}
				}
				else
				{
					$Qry = $this->DB->prepare("Select Sum(Final.View) As View From (Select Sum(urole_dtl.viewID) As View From urole_dtl Where urole_dtl.ID = ".$roleID." And urole_dtl.frmID In (".$frmID.") Group By urole_dtl.ID, urole_dtl.frmID) As Final");
					$Qry->execute();
					$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
					$return = !empty($this->rows['View']) || ($this->rows['View'] > 0) ?  1 : 0 ;		
				}
			}
		}
		return $return;
	}
        
	public function Create_Reports_Date($params,$fieldID)
	{
		$str = "";
		if(!empty($params['fromID']) || !empty($params['toID']))
		{
			list($fdt,$fm,$fy)  =   explode("/",$params['fromID']);
			list($tdt,$tm,$ty)  =   explode("/",$params['toID']);
			
			$fd = date('Y-m-d', strtotime(date($fy.'-'.$fm.'-'.$fdt)));
			$td = date('Y-m-d', strtotime(date($ty.'-'.$tm.'-'.$tdt)));
		}
		else
		{
			$fd = date('Y-m-d', strtotime('first day of last month'));
			$td = date('Y-m-d', strtotime('last day of last month'));
		}

		if($params['fromID'] <> '' && $params['toID'] <> '')    {$str .= " AND DATE(".$fieldID.") BETWEEN '".$fd."' AND '".$td."' ";}
		
		return $str;
	}
        
	public function RunIndexPageThoughts()
	{
		$TH_Array  = $this->select('thoughts',array("*"), " WHERE defaultID = 1 Order By ID ASC LIMIT 1 ");
		
		/* GET MAX ID */
		$LQry = $this->DB->prepare("SELECT ID FROM thoughts Order By ID DESC LIMIT 1 ");
		$LQry->execute();
		$this->Lrows = $LQry->fetch(PDO::FETCH_ASSOC);
		$maxID = $this->Lrows['ID'];
		
		/* GET FIRST ID */
		$FQry = $this->DB->prepare("SELECT ID FROM thoughts Order By ID ASC LIMIT 1 ");
		$FQry->execute();
		$this->Frows = $FQry->fetch(PDO::FETCH_ASSOC);
		$firstID = $this->Frows['ID'];
		
		$crtID = "";
		$crtID = ($TH_Array[0]['ID'] > 0 ? ($maxID == $TH_Array[0]['ID'] ? " AND ID >= ".$firstID : " AND ID > ".$TH_Array[0]['ID']) : "AND ID >= ".$firstID ) ;
		
		$Qry = $this->DB->prepare("SELECT * FROM thoughts WHERE defaultID = 0 ".$crtID." Order By ID ASC LIMIT 1 ");
		$Qry->execute();
		$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
		foreach($this->rows as $row)
		{
			$array = array();
			$array['defaultID'] = 1;
			$ons['ID'] = $row['ID'];
			$this->BuildAndRunUpdateQuery('thoughts',$array,$ons);

			$Qry = $this->DB->prepare("UPDATE thoughts SET defaultID = 0  WHERE ID <> ".$row['ID']." ");
			$Qry->execute();
		}
		return $row['ID'];
	}
	
	public function get_systemID($empID)
	{
		$returnID = 0;
		if($empID > 0)
		{
			$Qry_1 = $this->DB->prepare("UPDATE sicklv INNER JOIN employee ON sicklv.empID = employee.ID SET sicklv.systemID = employee.systemID WHERE sicklv.systemID <=0 ");
			$Qry_1->execute();
			
			$Qry_2 = $this->DB->prepare("UPDATE complaint INNER JOIN employee ON complaint.driverID = employee.ID SET complaint.systemID = employee.systemID WHERE complaint.systemID <=0 ");
			$Qry_2->execute();
			
			$Qry_3 = $this->DB->prepare("UPDATE incident_regis INNER JOIN employee ON incident_regis.driverID = employee.ID SET incident_regis.systemID = employee.systemID WHERE incident_regis.systemID <=0 ");
			$Qry_3->execute();
			
			$Qry_4 = $this->DB->prepare("UPDATE accident_regis INNER JOIN employee ON accident_regis.staffID = employee.ID SET accident_regis.systemID = employee.systemID WHERE accident_regis.systemID <=0 ");
			$Qry_4->execute();
			
			$Qry_5 = $this->DB->prepare("UPDATE infrgs INNER JOIN employee ON infrgs.staffID = employee.ID SET infrgs.systemID = employee.systemID WHERE infrgs.systemID <= 0 ");
			$Qry_5->execute();
			
			$Qry_6 = $this->DB->prepare("UPDATE inspc INNER JOIN employee ON inspc.empID = employee.ID SET inspc.systemID = employee.systemID WHERE inspc.systemID<=0 ");
			$Qry_6->execute();
			
			$Qry_7 = $this->DB->prepare("UPDATE mng_cmn INNER JOIN employee ON mng_cmn.staffID = employee.ID SET mng_cmn.systemID = employee.systemID WHERE mng_cmn.systemID<=0 ");
			$Qry_7->execute();
			
			$Qry_8 = $this->DB->prepare("UPDATE hiz_regis INNER JOIN employee ON hiz_regis.reportBY = employee.ID SET hiz_regis.systemID = employee.systemID ");
			$Qry_8->execute();
			
			$Qry_9 = $this->DB->prepare("UPDATE sir_regis INNER JOIN employee ON sir_regis.issuedTO = employee.ID SET sir_regis.systemID = employee.systemID ");
			$Qry_9->execute();
			
			$arraySM  = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';
			$returnID = ($arraySM[0]['systemID'] > 0 ? $arraySM[0]['systemID'] : 0);
		}
		return $returnID;
	}

	public function filter_employee_systemID($passID)
	{
		if($passID <> '')
		{
			if(!empty($passID) && ($passID <> '')) 
			{
				if(!is_numeric((substr($passID, 0,2))))
				{
					$Qry = $this->DB->prepare("SELECT systemID FROM employee WHERE ID > 0 AND Concat(full_name, ' ', fname) LIKE '%".$passID."%' AND companyID In (".$_SESSION[$this->website]['compID'].") Order By systemID ASC ");
					$Qry->execute();
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					$srID = 1;
					foreach($this->rows as $row)
					{
						$systemID .= $srID == 1 ? $row['systemID'] : " , ".$row['systemID'];
						$srID++;
					}
				}
				else
				{
					$Qry = $this->DB->prepare("SELECT systemID FROM employee WHERE ID > 0 AND code LIKE '%".$passID."%' AND companyID In (".$_SESSION[$this->website]['compID'].") Order By systemID ASC ");
					$Qry->execute();
					$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
					$srID = 1;
					foreach($this->rows as $row)
					{
						$systemID .= $srID == 1 ? $row['systemID'] : " , ".$row['systemID'];
						$srID++;
					}
				}
			}
		}
		return $systemID;
	} 

	public function SECOND_MINUTES_SUM_CODE($fdateID,$companyID,$shiftID,$tagCD)
	{
		$return = '';
		if($companyID > 0 && $shiftID > 0 && $tagCD <> '')
		{
			$SQL = "SELECT CalTimeDifferences.companyID, CalTimeDifferences.empID, Sec_To_Time(Sum(Time_To_Sec(CalTimeDifferences.diffTM))) AS Sum_diffTM FROM (SELECT imp_shift_daily.recID, imp_shift_daily.companyID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) AS empID, Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, ''))) AS ontimeID, Time(Trim(Replace((Replace(Replace(imp_shift_daily.singinID, ': PM', ''), ': AM', '')), ' ', ''))) AS intimeID,
			TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(Trim(Replace((Replace(Replace(imp_shift_daily.singinID, ': PM', ''), ': AM', '')), ' ', ''))))) AS diffTM
			FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND shift_masters_dtl.recID = imp_shift_daily.shift_recID 				
			Inner Join (Select signon_logs.ID As signID From signon_logs Where signon_logs.fromID = 'TOUCHPAD' Group By signon_logs.ID Order By signID) SignonTouchPAD On SignonTouchPAD.signID = imp_shift_daily.recID
			WHERE DATE(imp_shift_daily.dateID) = '".$fdateID."' AND tagCD = '".$tagCD."' AND imp_shift_daily.companyID = ".$companyID." AND imp_shift_daily.fID_1 = '".$shiftID."') AS CalTimeDifferences ";
			
			$Qry = $this->DB->prepare($SQL);
			$Qry->execute();
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
			
			if((strpos(($this->rows['Sum_diffTM']), '-') !== false))    {$return = '<b style="color:red;">'.substr((str_replace("-","",$this->rows['Sum_diffTM'])),0,5).'</b>';}
			else                                                        {$return = '<b style="color:green;">'.($this->rows['Sum_diffTM'] == '00:00:00' ? '' :  substr($this->rows['Sum_diffTM'],0,5)).'</b>';}
		}
		return $return;
	}

	public function SECOND_MINUTES_SUM_CODE_ALL($fdateID,$companyID,$shiftID,$tagCD)
	{
		$return = '';
		if($companyID > 0 && $shiftID > 0 && $tagCD <> '')
		{
			$SQL = "SELECT CalTimeDifferences.companyID, CalTimeDifferences.empID, Sec_To_Time(Sum(Time_To_Sec(CalTimeDifferences.diffTM))) AS Sum_diffTM FROM (SELECT imp_shift_daily.recID, imp_shift_daily.companyID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) AS empID, Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, ''))) AS ontimeID, Time(Trim(Replace((Replace(Replace(imp_shift_daily.singinID, ': PM', ''), ': AM', '')), ' ', ''))) AS intimeID,
			TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(Trim(Replace((Replace(Replace(imp_shift_daily.singinID, ': PM', ''), ': AM', '')), ' ', ''))))) AS diffTM
			FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND shift_masters_dtl.recID = imp_shift_daily.shift_recID 				
			WHERE DATE(imp_shift_daily.dateID) = '".$fdateID."' AND tagCD = '".$tagCD."' AND imp_shift_daily.companyID = ".$companyID." AND imp_shift_daily.fID_1 = '".$shiftID."') AS CalTimeDifferences ";
			
			$Qry = $this->DB->prepare($SQL);
			$Qry->execute();
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
			
			if((strpos(($this->rows['Sum_diffTM']), '-') !== false))    
			{
				$return['diffTX'] = '<b style="color:red;">'.substr((str_replace("-","",$this->rows['Sum_diffTM'])),0,5).'</b>';
				$return['diffID'] = 1;
			}
			else                                                        
			{
				$return['diffTX'] = '<b style="color:green;">'.($this->rows['Sum_diffTM'] == '00:00:00' ? '' :  substr($this->rows['Sum_diffTM'],0,5)).'</b>';
				$return['diffID'] = 0;
			}
		}
		return $return;
	}
	
	public function GET_DAY_NAME($valID,$fromID)
	{
		$return = '';

		$lengthID = 0;
		$lengthID = strlen($valID);

		$dayID = '';
		$dayID = date('l',strtotime($this->dateFormat($fromID)));

		if($valID <> '')
		{
			$dayNM = '';
			for($srsID = 0; $srsID <= $lengthID; $srsID++)
			{
				$dayNM =  ($valID[$srsID] == 'M' ? 'Monday'	:($valID[$srsID] == 'U' ? 'Tuesday' :($valID[$srsID] == 'W' ? 'Wednesday' :($valID[$srsID] == 'T' ? 'Thursday' :($valID[$srsID] == 'F' ? 'Friday' :($valID[$srsID] == 'A' ? 'Saturday':($valID[$srsID] == 'S' ? 'Sunday' : '')))))));
				if($dayID == $dayNM)    {$return = 1;    break;}
			}
		}
		return $return;
	}       
	
	public function GETCountComplaints($crtID,$companyID,$inID)
	{
		if(!empty($crtID) || !empty($inID))
		{
			$Qry = $this->DB->prepare("SELECT FOS.creasonID, Sum(FOS.faultID1) AS faultID1, Sum(FOS.faultID2) AS faultID2, Sum(FOS.faultID3) AS faultID3,
			Sum(FOS.faultID4) AS faultID4, Sum(FOS.faultID5) AS faultID5 , Sum(FOS.faultID6) AS faultID6 FROM (SELECT complaint.ID, complaint.dateID, complaint.accID, complaint.creasonID, 				
			If(complaint.faultID = 1, 1, 0) AS faultID1, If(complaint.faultID = 2, 1, 0) AS faultID2, If(complaint.faultID = 3, 1, 0) AS faultID3, If(complaint.substanID = 2 And complaint.faultID = 4, 1, 0) AS faultID4, 
			If(complaint.substanID = 1 And complaint.faultID = 4, 1, 0) AS faultID5, If(complaint.substanID = 2 And complaint.faultID = 5, 1, 0) AS faultID6 FROM
			complaint WHERE complaint.serDT <> '' AND complaint.accID In(52,48,221,49,50,51,220,54) AND complaint.creasonID = ".$inID."  AND complaint.companyID In (".$companyID.") ".$crtID.") AS FOS GROUP BY FOS.creasonID ");
			$Qry->execute();
			$this->rows = $Qry->fetch(PDO::FETCH_ASSOC);
			$return = array();
			$return[] = array('faultID1'=>$this->rows['faultID1'],'faultID2'=>$this->rows['faultID2'],'faultID3'=>$this->rows['faultID3'],'faultID4'=>$this->rows['faultID4'],'faultID5'=>($this->rows['faultID5'] + $this->rows['faultID6']));
		}
		
		return $return;
	}
	
	public function returnDateDayID($reqID)
	{
		$dateID = $this->dateFormat($reqID);
		$dayID = date('l',strtotime($dateID));

		$day = array();

		$day = ($dayID == 'Monday'    ? '1' 
			  :($dayID == 'Tuesday'   ? '2' 
			  :($dayID == 'Wednesday' ? '3' 
			  :($dayID == 'Thursday'  ? '4' 
			  :($dayID == 'Friday'    ? '5' 
			  :($dayID == 'Saturday'  ? '6' 
			  :($dayID == 'Sunday'    ? '7' : '')))))));

		return ($day > 0 ? $day : 0);
	}
}