<?PHP
class AFunctions extends TFunctions
{
    function __construct()
    {
		parent::__construct();
		
		$this->fID = '<div class="col-xs-12"><b style="color:red; font-size: 16px;">--';
        $this->tID = '</b></div>';
    }
	
	/*	------------------------------------------ AUDIT TRIAL / FIELDS COUNTS ----------------------------------------------------------  */
	
	public function checkDate_Counts($dateID)
	{
		$return = 0;
		if($dateID == '0000-00-00' || $dateID == '1970-01-01' || empty($dateID))
		{
			$return = 1;
		}
		return $return;
	}
	
	Public function form_Employee_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM employee WHERE ID > 0 AND companyID In(".$_SESSION[$this->website]['compID'].") AND status = 1 ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['code'])	    ? 1 : 0;
						$srID += empty($rows['fname'])		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['dob']);
						$srID += $rows['genderID'] <= 0		? 1 : 0;						
						$srID += $rows['desigID'] <= 0		? 1 : 0;
						$srID += empty($rows['address_1'])	? 1 : 0;
						$srID += $rows['sid'] <= 0			? 1 : 0;
						$srID += empty($rows['phone']) && empty($rows['phone_1'])	? 1 : 0;
						$srID += empty($rows['emailID'])	? 1 : 0;
						$srID += empty($rows['ddlcno'])		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['ddlcdt']);
						
						/*$srID += $rows['lctypeID'] <= 0 	? 1 : 0;
						$srID += $rows['visatypeID'] <= 0 	? 1 : 0;*/
						
						/*if($rows['desigID'] == 9 || $rows['desigID'] == 209)
						{
							$srID += empty($rows['smartcardNO'])	    ? 1 : 0;
						}*/
						
						/*if($rows['desigID'] == 418)
						{
							$srID += empty($rows['gfpermitNO'])	    ? 1 : 0;
							$srID += empty($rows['acpermitNO'])	    ? 1 : 0;
							$srID += empty($rows['wsdpermitNO'])	? 1 : 0;
							$srID += empty($rows['flpermitNO'])	    ? 1 : 0;
							
							$srID += !empty($rows['gfpermitNO'])  ? $this->checkDate_Counts($rows['gfpnexpDT']) : 0;
							$srID += !empty($rows['acpermitNO'])  ? $this->checkDate_Counts($rows['acpnexpDT']) : 0;
							$srID += !empty($rows['wsdpermitNO']) ? $this->checkDate_Counts($rows['wsdpnexpDT']) : 0;
							$srID += !empty($rows['flpermitNO'])  ? $this->checkDate_Counts($rows['flpnexpDT']) : 0;
						}*/

						if($rows['visatypeID'] == 1)
						{
							$srID += empty($rows['visaDetails'])	    ? 1 : 0;
							$srID += empty($rows['workingResc'])	    ? 1 : 0; 
						}
						
						/*if($rows['desigID'] == 9 || $rows['desigID'] == 209 || $rows['desigID'] == 208 || $rows['desigID'] == 418 || $rows['desigID'] == 445)
						{
							$srID += $rows['articID'] <= 0 	? 1 : 0;
						}*/

						if($rows['desigID'] == 9 || $rows['desigID'] == 209 || $rows['desigID'] == 208)
						{ 
							$srID += empty($rows['drvrightID'])	    ? 1 : 0;
							$srID += empty($rows['rfID'])			? 1 : 0;
							$srID += empty($rows['wwcprno'])		? 1 : 0; 
							$srID += $this->checkDate_Counts($rows['wwcprdt']);
							$srID += empty($rows['ftextID'])	    ? 1 : 0;
						}
						
						$srID += ($rows['desigID'] == 9 || $rows['desigID'] == 209) && ($rows['lardt'] == '0000-00-00' || $rows['lardt'] == '1970-01-01' || empty($rows['lardt'])) ? 1 : 0;
						$srID += empty($rows['kinname'])	? 1 : 0;
						$srID += empty($rows['kincno'])		? 1 : 0;						
						$srID += $rows['status'] <= 0 	 	? 1 : 0;	 
						$srID += $rows['casualID'] <= 0 	 	? 1 : 0;	 
						$srID += $this->checkDate_Counts($rows['esdate']);						
						$srID += $rows['casualID'] == 3 && ($rows['csdate'] == '0000-00-00' || $rows['csdate'] == '1970-01-01' || empty($rows['csdate'])) ? 1 : 0;
						$srID += ($rows['casualID'] == 1 || $rows['casualID'] == 2) && ($rows['ftsdate'] == '0000-00-00' || $rows['ftsdate'] == '1970-01-01' || empty($rows['ftsdate'])) ? 1 : 0;
							
						if($srID > 0)	{$countsID += 1;	$srID  = 0;	$retID .= ','.$rows['ID'];}	else	{$srID  = 0;}						
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_GIncident_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM incident_regis WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") AND sincID = 2 ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['refno'])   	? 1 : 0;						
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += empty($rows['timeID'])		? 1 : 0;
						$srID += $rows['driverID'] <= 0		? 1 : 0;
						$srID += empty($rows['location'])	? 1 : 0;
						$srID += $rows['suburb'] <= 0		? 1 : 0;
						$srID += empty($rows['reportby'])	? 1 : 0;
						$srID += $rows['inctypeID'] <= 0		? 1 : 0;
						$srID += $rows['disciplineID'] <= 0	? 1 : 0;
						
						if($rows['disciplineID'] == 1)
						{
							$srID += empty($rows['mcomments'])   ? 1 : 0;
							$srID += $rows['wrtypeID'] <= 0 	 	? 1 : 0;
						}
						
						$srID += empty($rows['description'])  ? 1 : 0;
						$srID += empty($rows['action'])	 	  ? 1 : 0;
						$srID += $rows['actbyID'] <= 0 	 	  ? 1 : 0;
						$srID += $rows['statusID'] <= 0 	  ? 1 : 0;
						
						if($rows['brs_statusID'] == 1)
						{
							$srID += empty($rows['shiftID'])	   ? 1 : 0;
							$srID += empty($rows['routeID'])    ? 1 : 0;
							$srID += empty($rows['busID'])      ? 1 : 0;
						}
						
						if($rows['offtypeID'] == 144)
						{
							$srID += empty($rows['grfcolour'])   		? 1 : 0;
							$srID += empty($rows['whbwdescription'])   		? 1 : 0;
							$srID += $rows['grfitemID']  <= 0	? 1 : 0;
						}
				
						if($rows['plrefID'] == 1)
						{
							$srID += empty($rows['plrefno'])	? 1 : 0;
						}  
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ','.$rows['ID']; }	else	{$srID  = 0;}
						
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_SIncident_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM incident_regis WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") AND sincID = 1 ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{ 
						if($rows['cmrno'] <> '')
						{
							$srID += $this->checkDate_Counts($rows['rpdateID']);
						}
						
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += empty($rows['timeID'])				? 1 : 0;
						$srID += $rows['driverID'] <= 0				? 1 : 0;
						$srID += empty($rows['location'])			? 1 : 0;
						$srID += $rows['suburb'] <= 0				? 1 : 0;						
						$srID += empty($rows['reportby'])			? 1 : 0;
						$srID += $rows['inctypeID'] <= 0			? 1 : 0;						
						$srID += $rows['disciplineID'] <= 0			? 1 : 0;						
						$srID += empty($rows['crossst'])			? 1 : 0;
						$srID += empty($rows['dmginjury'])	 		? 1 : 0;
						$srID += $rows['offtypeID'] <= 0 	 		? 1 : 0;
						$srID += $rows['offdtlsID'] <= 0 	 		? 1 : 0;
						$srID += $rows['statusID'] <= 0 	  		? 1 : 0;
						
						if($rows['plrefID'] == 1 || $rows['attendedID_2'] == 1)
						{
							$srID += empty($rows['plrefno'])		? 1 : 0;
						}
						
						if($rows['attendedID_3'] == 1 || $rows['attendedID_6'] == 1 || $rows['notifiedID_3'] == 1 || $rows['notifiedID_6'] == 1)
						{
							$srID += empty($rows['pta_refNO'])	? 1 : 0;
						}
						
						if($rows['offtypeID'] == 144)
						{
							$srID += empty($rows['grfcolour'])   	? 1 : 0;
							$srID += empty($rows['whbwdescription'])     ? 1 : 0;								
							$srID += $rows['grfitemID'] <= 0 	 	? 1 : 0;
						}

						if($rows['disciplineID'] == 1)
						{
							$srID += empty($rows['mcomments'])   	? 1 : 0;
							$srID += $rows['wrtypeID'] <= 0 	 	? 1 : 0;
						}						
						$srID += empty($rows['description'])  		? 1 : 0;
						$srID += empty($rows['action'])	 	 		? 1 : 0; 
						$srID += $rows['actbyID'] <= 0 	 	 		? 1 : 0;
						
						if($rows['attendedID_2'] == 1)
						{
							$srID += empty($rows['plcadno'])		? 1 : 0;
							$srID += empty($rows['plcvehicle'])		? 1 : 0;
							$srID += empty($rows['policename'])		? 1 : 0;
							$srID += $rows['plcactionID'] <= 0 	 	? 1 : 0;
						}
						
						if(($rows['notifiedID_1'] + $rows['notifiedID_3'] + $rows['notifiedID_6'] + $rows['notifiedID_4'] + $rows['notifiedID_5'] + $rows['notifiedID_7'] + $rows['notifiedID_8'] + $rows['attendedID_1'] + $rows['attendedID_3'] + $rows['attendedID_8'] + $rows['attendedID_9'] + $rows['attendedID_6'] + $rows['attendedID_4'] + $rows['attendedID_5'] + $rows['attendedID_7']) <= 0)
						{
							$srID += 1;
						}
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ','.$rows['ID']; }	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_CommentLine_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM complaint WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += ($rows['cmltypeID'] == 491 || $rows['cmltypeID'] == 492) && empty($rows['refno'])   	? 1 : 0; 
						$srID += $this->checkDate_Counts($rows['serDT']);
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += empty($rows['timeID'])		? 1 : 0;
						
						if($rows['accID'] == 52)
						{
							$srID += empty($rows['busID'])	 	 ? 1 : 0;
							$srID += empty($rows['routeID'])	 ? 1 : 0;
							$srID += empty($rows['location'])	 ? 1 : 0;
						}
						
						$srID += $rows['accID'] <= 0			? 1 : 0;
						$srID += empty($rows['description'])	? 1 : 0;
						$srID += $rows['creasonID'] <= 0		? 1 : 0;
						$srID += $rows['cmltypeID'] <= 0		? 1 : 0;
						
						if($rows['tickID_1'] <= 0)
						{
							$srID += $rows['driverID'] <= 0	? 1 : 0;
						}
						
						$srID += $rows['respID'] <= 0			? 1 : 0;
						$srID += $rows['statusID'] <= 0			? 1 : 0;
						$srID += empty($rows['furaction']) 	? 1 : 0;
						$srID += empty($rows['outcome']) 	? 1 : 0;
						
						if($rows['respID'] <> 46)
						{
							$srID += $this->checkDate_Counts($rows['resdate']);
						}
						
						$srID += ($rows['accID'] == 52 || $rows['accID'] == 221 || $rows['accID'] == 49 || empty($rows['accID'])) ? ($rows['substanID'] <= 0	? 1 : 0) : 0;
						$srID += ($rows['accID'] == 52 || $rows['accID'] == 221 || $rows['accID'] == 49 || empty($rows['accID'])) ? ($rows['faultID'] <= 0	? 1 : 0) : 0;
						
						if($rows['accID'] == 52 && $rows['substanID'] == 2)
						{
							/* DO NOTHING */
						}
						else
						{
							$srID += ($rows['accID'] == 52 || $rows['accID'] == 221 || $rows['accID'] == 49 || $rows['accID'] == 224 || empty($rows['accID'])) ? ($rows['invID'] <= 0		? 1 : 0) : 0;
							$srID += ($rows['accID'] == 52 || $rows['accID'] == 221 || $rows['accID'] == 49 || $rows['accID'] == 224 || empty($rows['accID'])) ? ($rows['invdate'] == '0000-00-00' ? 1 : 0) : 0;
						}
						
						if($rows['accID'] == 48 && $rows['disciplineID'] == 1)
						{
							$srID += ($rows['invID'] <= 0		? 1 : 0);
							$srID += $this->checkDate_Counts($rows['invdate']);
						}
						
						$srID += $rows['trisID'] <= 0			? 1 : 0;
						
						if($rows['location'] <> '')
						{
							$srID += $rows['suburb'] <= 0		? 1 : 0;
						} 
						
						if($rows['accID'] == 52 || $rows['accID'] == 221 || $rows['accID'] == 49 || empty($rows['accID']))
						{
							$srID += $rows['disciplineID'] <= 0		? 1 : 0;

							if($rows['disciplineID'] == 1)
							{
								$srID += empty($rows['mcomments'])	 ? 1 : 0;
								$srID += $rows['wrtypeID'] <= 0		 ? 1 : 0;
								$srID += $rows['intvID'] <= 0		 ? 1 : 0;
								$srID += $this->checkDate_Counts($rows['intvDate']);
							}
						} 
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ','.$rows['ID']; }	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_Accident_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{ 
			$Qry = $this->DB->prepare("SELECT * FROM accident_regis WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				$returnCD = '';
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['refno'])   		? 1 : 0;
						$srID += empty($rows['busID'])   		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += empty($rows['timeID']) 		    ? 1 : 0;
						
						$srID += ($rows['tickID_1'] <= 0 && $rows['staffID'] <= 0		? 1 : 0);
						
						$srID += $rows['acccatID'] <= 0			? 1 : 0;
						$srID += $rows['accID'] <= 0			? 1 : 0;
						$srID += $rows['responsibleID'] <= 0	? 1 : 0;
						
						$srID += ($rows['insinvolvedID'] == 1 && empty($rows['insurer'])   		? 1 : 0);
						$srID += ($rows['insinvolvedID'] == 1 && empty($rows['claimno']) 		? 1 : 0);
						$srID += ($rows['insinvolvedID'] == 1 && empty($rows['invno'])   		? 1 : 0);													
						$srID += ($rows['witnessID'] == 1 && empty($rows['witnessName'])   		? 1 : 0);
						$srID += ($rows['witnessID'] == 1 && empty($rows['witnessContact'])   	? 1 : 0);
						
						$srID += empty($rows['location'])   	? 1 : 0;
						$srID += $rows['suburb'] <= 0			? 1 : 0;
						$srID += $rows['damagetobusID'] <= 0	? 1 : 0;
						$srID += empty($rows['description']) 	? 1 : 0;
						$srID += is_null($rows['rprcost'])   	? 1 : 0;
						$srID += is_null($rows['othcost'])   	? 1 : 0;
						$srID += empty($rows['optID_1']) || is_null($rows['optID_1']) || $rows['optID_1'] <= 0	? 1 : 0;
						$srID += empty($rows['optID_2']) || is_null($rows['optID_2']) || $rows['optID_2'] <= 0	? 1 : 0;
						$srID += empty($rows['optID_3']) || is_null($rows['optID_3']) || $rows['optID_3'] <= 0	? 1 : 0;
						$srID += empty($rows['outcome'])   		? 1 : 0;
						$srID += $rows['invID'] <= 0			? 1 : 0;
						$srID += $rows['disciplineID'] <= 0		? 1 : 0;
						$srID += $rows['progressID'] <= 0		? 1 : 0;
						
						$srID += ($rows['3partyID'] == 1 && empty($rows['thpnameID']) 		? 1 : 0);
						$srID += ($rows['3partyID'] == 1 && empty($rows['regisnoID']) 		? 1 : 0);
						$srID += ($rows['3partyID'] == 1 && empty($rows['thcontactID']) 	? 1 : 0);
							
						if($rows['disciplineID'] == 1)
						{
							$srID += empty($rows['mcomments'])   ? 1 : 0;
							$srID += $rows['wrtypeID'] <= 0 	    ? 1 : 0;
							$srID += $rows['intvID'] <= 0		 ? 1 : 0;
							$srID += $this->checkDate_Counts($rows['intvDate']);
						}
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0;	$retID .= ','.$rows['ID'];}	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}
		}	

		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_Infringment_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM infrgs WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['refno']) 	 ? 1 : 0;
						$srID += empty($rows['vehicle']) ? 1 : 0;
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += empty($rows['timeID'])  ? 1 : 0;
						$srID += $rows['staffID'] <= 0	 ? 1 : 0;						
						$srID += $this->checkDate_Counts($rows['dateID_1']);
						$srID += $this->checkDate_Counts($rows['dateID_2']);
						$srID += $this->checkDate_Counts($rows['dateID_3']);
						$srID += $this->checkDate_Counts($rows['dateID_4']); 
						$srID += $rows['invID'] <= 0		? 1 : 0;
						$srID += $rows['inftypeID'] <= 0	? 1 : 0;
						$srID += $rows['statusID'] <= 0 	  		? 1 : 0;

						if($rows['inftypeID'] == 162)
						{
							$srID += empty($rows['description'])	? 1 : 0;
						}

						$srID += empty($rows['description_1'])	? 1 : 0;

						$srID += $rows['disciplineID'] <= 0		? 1 : 0;

						if($rows['disciplineID'] == 1)
						{
							$srID += empty($rows['mcomments'])   ? 1 : 0;
							$srID += $rows['wrtypeID'] <= 0 	 ? 1 : 0;
							$srID += $rows['intvID'] <= 0		 ? 1 : 0;
							$srID += $this->checkDate_Counts($rows['intvDate']);
						}	
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ($rows['ID'] > 0 ? ','.$rows['ID'] : '');}	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}
		}		
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_Inspection_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM inspc WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['rptno']) 			? 1 : 0;
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += $rows['empID'] <= 0			? 1 : 0;
						$srID += $rows['insrypeID'] <= 0 		? 1 : 0;
						$srID += $rows['inspectedby'] <= 0 		? 1 : 0;
						
						if($rows['insrypeID'] == 300 || $rows['insrypeID'] == 268 || $rows['insrypeID'] == 261|| $rows['insrypeID'] == 271 || $rows['insrypeID'] == 301|| $rows['insrypeID'] == 377 ||$rows['insrypeID'] == 381|| $rows['insrypeID'] == 388 || $rows['insrypeID'] == 390 || $rows['insrypeID'] == 396  || $rows['insrypeID'] == 398 || $rows['insrypeID'] == 399)
						{
							$srID += $rows['fineID'] <= 0 		? 1 : 0;
						}
						
						$srID += $this->checkDate_Counts($rows['dateID_1']); 
						$srID += $rows['servicenoID'] <= 0 		? 1 : 0;
						$srID += empty($rows['serviceinfID'])	? 1 : 0;
						$srID += $rows['srtpointID'] <= 0 		? 1 : 0;
						$srID += empty($rows['shiftID'])   		? 1 : 0;
						$srID += empty($rows['busID'])   		? 1 : 0;
						$srID += empty($rows['timeID_1'])   	? 1 : 0;
						$srID += empty($rows['timeID_3'])   	? 1 : 0;
						$srID += empty($rows['timeID_2'])   	? 1 : 0;
						$srID += empty($rows['description'])    ? 1 : 0;
						$srID += empty($rows['description_2'])   ? 1 : 0;
						$srID += $rows['invstID'] <= 0			? 1 : 0;
						$srID += $rows['trisID'] <= 0 			? 1 : 0;
						$srID += $rows['disciplineID'] <= 0		? 1 : 0;
						$srID += $rows['statusID'] <= 0			? 1 : 0;
						
						if($rows['disciplineID'] == 1)
						{
							$srID += empty($rows['mcomments'])   ? 1 : 0;
							$srID += $rows['wrtypeID'] <= 0 	 ? 1 : 0;
							$srID += $rows['intvID'] <= 0		 ? 1 : 0;
							$srID += $this->checkDate_Counts($rows['intvDate']);
						}
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0;	$retID .= ($rows['ID'] > 0 ? ','.$rows['ID'] : '');}	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_ManangerComments_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM mng_cmn WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;
					foreach($this->rows as $rows)
					{
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += $rows['staffID'] <= 0 		 ? 1 : 0;
						$srID += $rows['invID'] <= 0 		 ? 1 : 0;
						$srID += empty($rows['description']) ? 1 : 0;
						$srID += empty($rows['mcomments'])   ? 1 : 0;
						$srID += $rows['wrtypeID'] <= 0 	 ? 1 : 0;
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0;	$retID .= ($rows['ID'] > 0 ? ','.$rows['ID'] : '');}	else	{$srID  = 0;}
					}
				}
			}
			else	{$countsID = 0;}		
		}		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_HIZ_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM hiz_regis WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['refno'])   	 ? 1 : 0;
						$srID += $this->checkDate_Counts($rows['rdateID']);
						$srID += $this->checkDate_Counts($rows['dateID']);
						$srID += $rows['jobID'] <= 0		 ? 1 : 0;
						$srID += $rows['hztypeID'] <= 0		 ? 1 : 0;						
						$srID += $rows['reportBY'] <= 0		 ? 1 : 0;
						$srID += empty($rows['location'])    ? 1 : 0;
						$srID += empty($rows['description']) ? 1 : 0; 
						$srID += empty($rows['descriptionACT']) ? 1 : 0; 
						$srID += $rows['fstaffID'] <= 0		 ? 1 : 0;
						$srID += $rows['fdesigID'] <= 0		 ? 1 : 0;						
						$srID += $this->checkDate_Counts($rows['rcdateID']);						
						$srID += $rows['optID_u1'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_u3'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_u4'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_u5'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_u6'] <= 0		 ? 1 : 0;
						$srID += (int)$rows['optID_u7'] < 0	 ? 1 : 0;						
						$srID += empty($rows['descriptionINV']) ? 1 : 0; 
						$srID += empty($rows['descriptionACD']) ? 1 : 0; 
						$srID += $rows['invID'] <= 0		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['invDate']);						
						$srID += $rows['actID'] <= 0		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['actDate']);							
						$srID += $rows['optID_m1'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_m3'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_m4'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_m5'] <= 0		 ? 1 : 0;
						$srID += $rows['optID_m6'] <= 0		 ? 1 : 0;
						$srID += $this->checkDate_Counts($rows['hzrconDT']);
						$srID += $this->checkDate_Counts($rows['empadvDT']);						
						$srID += $rows['act_effID'] <= 0	 ? 1 : 0;
						$srID += $rows['fupreqID'] <= 0		 ? 1 : 0;
						
						if($rows['fupreqID'] == 1)
						{
							$srID += $rows['fupcmpID'] <= 0		 ? 1 : 0;
							$srID += empty($rows['fupDesc'])   ? 1 : 0;
							$srID += $this->checkDate_Counts($rows['fupreqDT']);
							$srID += $this->checkDate_Counts($rows['fupcmpDT']);
						}
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ','.$rows['ID']; }	else	{$srID  = 0;}
						
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	Public function form_SIR_Counts($fdateID,$tdateID)
	{
		$file = array();
		$countsID = 0;	$filterID = '';
		if(!empty($tdateID) && !empty($tdateID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM sir_regis WHERE ID > 0 AND logID <> '0000-00-00 00:00:00' AND companyID In(".$_SESSION[$this->website]['compID'].") ");
			if($Qry->execute())
			{
				$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($this->rows) && count($this->rows) > 0)
				{
					$srID = 0;	$retID = '';
					foreach($this->rows as $rows)
					{
						$srID += empty($rows['refno'])   	? 1 : 0;						
						$srID += $this->checkDate_Counts($rows['issuetoDT']);
						$srID += $rows['srtypeID'] <= 0		? 1 : 0;
						$srID += $rows['issuedTO'] <= 0		? 1 : 0;
						$srID += $rows['originatorID'] <= 0	? 1 : 0;
						$srID += $rows['resultsINV'] <= 0	? 1 : 0;						
						$srID += $rows['invID'] <= 0		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['invDate']);						
						$srID += empty($rows['action'])   	? 1 : 0;
						$srID += $rows['actID'] <= 0		? 1 : 0;
						$srID += $this->checkDate_Counts($rows['actDate']);						
						$srID += $rows['acteffID'] <= 0		? 1 : 0;
						$srID += $rows['fupreqID'] <= 0		? 1 : 0;						
						$srID += $this->checkDate_Counts($rows['clsoutDT']);
						$srID += $this->checkDate_Counts($rows['orgadvDT']);
						
						if($rows['resultsINV'] == 8000)
						{
							$srID += empty($rows['otherINV'])	? 1 : 0;
						}
						
						if($rows['fupreqID'] == 1)
						{
							$srID += $rows['fupcmpID'] <= 0		? 1 : 0;						
						    $srID += $this->checkDate_Counts($rows['fupcmpDT']);
							$srID += empty($rows['fupDesc'])   ? 1 : 0;
							$srID += $this->checkDate_Counts($rows['fupreqDT']);
						}
						
						if($srID > 0)	{$countsID += 1;	$srID  = 0; $retID .= ','.$rows['ID']; }	else	{$srID  = 0;}
						
					}
				}
			}
			else	{$countsID = 0;}
		}
		
		$file['countsID'] = $countsID;
		$file['filterID'] = $retID;
		
		return $file;
	}
	
	/*	------------------------------------------ AUDIT TRIAL / FIELDS NAME ----------------------------------------------------------  */
	
	Public function form_Employee($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['code'])	    ? $this->fID.' Employee Code'.$this->tID : '';
			$file .= empty($arrID[0]['fname'])		? $this->fID.' First Name'.$this->tID : '';
			$file .= empty($arrID[0]['dob']) || $arrID[0]['dob'] == '0000-00-00' || $arrID[0]['dob'] == '1970-01-01' ? $this->fID.' Date of Birth'.$this->tID : '';			
			$file .= $arrID[0]['genderID'] <= 0		? $this->fID.' Gender'.$this->tID : '';			
			$file .= $arrID[0]['desigID'] <= 0		? $this->fID.' Designation'.$this->tID : '';			
			$file .= empty($arrID[0]['address_1'])	? $this->fID.' Address - 1'.$this->tID : '';
			$file .= $arrID[0]['sid'] <= 0			? $this->fID.' Suburb'.$this->tID : '';			
			if(empty($arrID[0]['phone']) && empty($arrID[0]['phone_1']))
			{
				$file .= empty($arrID[0]['phone'])	    ? $this->fID.' Telephone No'.$this->tID : '';
				$file .= empty($arrID[0]['phone_1'])	? $this->fID.' Mobile No'.$this->tID : '';
			}
			$file .= empty($arrID[0]['emailID'])	? $this->fID.' Email ID'.$this->tID : '';
			$file .= empty($arrID[0]['ddlcno'])		? $this->fID.' Driver\'s Licence No'.$this->tID : '';
			$file .= empty($arrID[0]['ddlcdt']) || $arrID[0]['ddlcdt'] == '0000-00-00' || $arrID[0]['ddlcdt'] == '1970-01-01' ? $this->fID.' Driver\'s Licence Expiry Date'.$this->tID : '';			
			
			$file .= empty($arrID[0]['kinname'])	? $this->fID.' Name of Kin'.$this->tID : '';
			$file .= empty($arrID[0]['kincno'])		? $this->fID.' Kin Contact No'.$this->tID : '';			
			
			/*$file .= $arrID[0]['lctypeID'] <= 0 	? $this->fID.' License Type'.$this->tID 	   : '';
			$file .= $arrID[0]['visatypeID'] <= 0 	? $this->fID.' VISA Type'.$this->tID 	   : '';*/
			
			$file .= $arrID[0]['status'] <= 0 	 	? $this->fID.' Employee Status'.$this->tID 	   : '';
			$file .= $arrID[0]['casualID'] <= 0 	? $this->fID.' Casual/Full Time/Part Time'.$this->tID 	   : '';
			
			$file .= empty($arrID[0]['esdate']) || $arrID[0]['esdate'] == '0000-00-00' || $arrID[0]['esdate'] == '1970-01-01' ? $this->fID.' Employee Start Date'.$this->tID : '';
			
			/*if($arrID[0]['desigID'] == 9 || $arrID[0]['desigID'] == 209)
			{
				$file .= empty($arrID[0]['smartcardNO'])	    ? $this->fID.' Smart Card No'.$this->tID : '';
			}*/
			
			if($arrID[0]['visatypeID'] == 1)
			{
				$file .= empty($arrID[0]['visaDetails'])	    ? $this->fID.' VISA Details'.$this->tID : '';
				$file .= empty($arrID[0]['workingResc'])	    ? $this->fID.' Working Restrictions'.$this->tID : '';
			}
			
			/*if($arrID[0]['desigID'] == 418)
			{
				$file .= empty($arrID[0]['gfpermitNO'])	    ? $this->fID.' Gas Fitting Permit No'.$this->tID : '';
				$file .= empty($arrID[0]['acpermitNO'])	    ? $this->fID.' A/Con-Refrigerant Licence No'.$this->tID : '';
				$file .= empty($arrID[0]['wsdpermitNO'])	? $this->fID.' Work Safe – Dogging Licence No'.$this->tID : '';
				$file .= empty($arrID[0]['flpermitNO'])	    ? $this->fID.' Forklift Licence No'.$this->tID : '';
				
				$file .= !empty($arrID[0]['gfpermitNO'])  ? ((empty($arrID[0]['gfpnexpDT']) || $arrID[0]['gfpnexpDT'] == '0000-00-00' || $arrID[0]['gfpnexpDT'] == '1970-01-01') ? $this->fID.' Gas Fitting Permit - Expiry Date'.$this->tID : '') : '';
				$file .= !empty($arrID[0]['acpermitNO'])  ? ((empty($arrID[0]['acpnexpDT']) || $arrID[0]['acpnexpDT'] == '0000-00-00' || $arrID[0]['acpnexpDT'] == '1970-01-01') ? $this->fID.' A/Con-Refrigerant Licence - Expiry Date'.$this->tID : '') : '';
				$file .= !empty($arrID[0]['wsdpermitNO']) ? ((empty($arrID[0]['wsdpnexpDT']) || $arrID[0]['wsdpnexpDT'] == '0000-00-00' || $arrID[0]['wsdpnexpDT'] == '1970-01-01') ? $this->fID.' Work Safe – Dogging Licence - Expiry Date'.$this->tID : '') : '';
				$file .= !empty($arrID[0]['flpermitNO'])  ? ((empty($arrID[0]['flpnexpDT']) || $arrID[0]['flpnexpDT'] == '0000-00-00' || $arrID[0]['flpnexpDT'] == '1970-01-01') ? $this->fID.' Forklift Licence - Expiry Date'.$this->tID : '') : '';
			}*/
			
			/*if($arrID[0]['desigID'] == 9 || $arrID[0]['desigID'] == 209 || $arrID[0]['desigID'] == 208 || $arrID[0]['desigID'] == 418 || $arrID[0]['desigID'] == 445)
			{
				$file .= $arrID[0]['articID'] <= 0 	? $this->fID.' Artic Inducted'.$this->tID 	   : '';
			}*/
			
			if($arrID[0]['desigID'] == 9 || $arrID[0]['desigID'] == 209 || $arrID[0]['desigID'] == 208)
			{ 
				$file .= empty($arrID[0]['drvrightID'])	    ? $this->fID.' Driver Right No'.$this->tID : '';
				$file .= empty($arrID[0]['rfID'])			? $this->fID.' RF ID'.$this->tID : '';
				$file .= empty($arrID[0]['wwcprno'])		? $this->fID.' WWC Permit No'.$this->tID : '';		
				$file .= empty($arrID[0]['wwcprdt']) || $arrID[0]['wwcprdt'] == '0000-00-00' || $arrID[0]['wwcprdt'] == '1970-01-01' ? $this->fID.' WWC Permit Expiry Date'.$this->tID : '';				
				$file .= empty($arrID[0]['ftextID'])	    ? $this->fID.' F/T Extension'.$this->tID : '';
			}
			
			if($arrID[0]['desigID'] == 9 || $arrID[0]['desigID'] == 209)
			{ 
				$file .= empty($arrID[0]['lardt']) || $arrID[0]['lardt'] == '0000-00-00' || $arrID[0]['lardt'] == '1970-01-01' ? $this->fID.' Letter of Authority Received Date'.$this->tID : '';
			}
			
			if($arrID[0]['casualID'] == 3)
			{ 
				$file .= empty($arrID[0]['csdate']) || $arrID[0]['csdate'] == '0000-00-00' || $arrID[0]['csdate'] == '1970-01-01' ? $this->fID.' Causal Start Date'.$this->tID : '';
			}
			
			if($arrID[0]['casualID'] == 1 || $arrID[0]['casualID'] == 2)
			{ 
				$file .= empty($arrID[0]['ftsdate']) || $arrID[0]['ftsdate'] == '0000-00-00' || $arrID[0]['ftsdate'] == '1970-01-01' ? $this->fID.' Full Time Start Date'.$this->tID : '';
			}
		}		
		return $file;
	}
	
	Public function form_Incident($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('incident_regis',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			/* SECURITY - INCIDENTS */
			if($arrID[0]['sincID'] == 1)
			{
				if(!empty($arrID[0]['cmrno']))
				{ 
					$file .= (empty($arrID[0]['rpdateID']) || $arrID[0]['rpdateID'] == '0000-00-00' || $arrID[0]['rpdateID'] == '1970-01-01') ? $this->fID.' Date Driver Reported'.$this->tID : '';
				}
				
				$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Incident Date'.$this->tID : '';
				$file .= empty($arrID[0]['timeID'])		? $this->fID.' Incident Time'.$this->tID : '';
				$file .= $arrID[0]['driverID'] <= 0		? $this->fID.' Driver Name'.$this->tID : '';
				$file .= empty($arrID[0]['location'])	? $this->fID.' Location'.$this->tID : '';
				$file .= $arrID[0]['suburb'] <= 0		? $this->fID.' Suburb'.$this->tID : '';
				$file .= empty($arrID[0]['crossst'])	? $this->fID.' Cross Street'.$this->tID : '';
				$file .= empty($arrID[0]['reportby'])	? $this->fID.' Reported By'.$this->tID : '';				
				$file .= $arrID[0]['inctypeID'] <= 0	? $this->fID.' Incident Type'.$this->tID : '';				
				$file .= $arrID[0]['disciplineID'] <= 0	? $this->fID.' Discipline Related'.$this->tID : '';
				
				if($arrID[0]['disciplineID'] == 1)
				{
					$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
					$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID 	   : '';
				}
				
				$file .= empty($arrID[0]['description']) ? $this->fID.' Description'.$this->tID 	: '';
				$file .= empty($arrID[0]['action'])	 	 ? $this->fID.' Action'.$this->tID 	: '';				
				$file .= empty($arrID[0]['dmginjury'])	 ? $this->fID.' Damage/Injury'.$this->tID 	: '';
				$file .= $arrID[0]['offtypeID'] <= 0 	 ? $this->fID.' Offence Type'.$this->tID 	    : '';
				$file .= $arrID[0]['offdtlsID'] <= 0 	 ? $this->fID.' Offence Details'.$this->tID 	: '';
				$file .= $arrID[0]['actbyID'] <= 0 	 	 ? $this->fID.' Action Taken By'.$this->tID 	: '';
				
				if($arrID[0]['attendedID_3'] == 1 || $arrID[0]['attendedID_6'] == 1 || $arrID[0]['notifiedID_3'] == 1 || $arrID[0]['notifiedID_6'] == 1)
				{
					$file .= empty($arrID[0]['pta_refNO'])	? $this->fID.' PTA Ref No '.$this->tID : '';
				}
						
				if($arrID[0]['offtypeID'] == 144)
				{
					$file .= empty($arrID[0]['grfcolour'])   		? $this->fID.' Graffiti Colour'.$this->tID : '';
					$file .= empty($arrID[0]['whbwdescription'])   	? $this->fID.' What has been written'.$this->tID : '';
					$file .= $arrID[0]['grfitemID']  <= 0			? $this->fID.' Graffiti Item'.$this->tID : '';
				}
				
				if($arrID[0]['brs_statusID'] == 1)
				{
					$file .= empty($arrID[0]['shiftID'])   		? $this->fID.' Shift No'.$this->tID : '';
					$file .= empty($arrID[0]['routeID'])   		? $this->fID.' Route No'.$this->tID : '';
					$file .= empty($arrID[0]['busID'])   		? $this->fID.' Bus No'.$this->tID : '';
				}
				
				if($arrID[0]['plrefID'] == 1 || $arrID[0]['attendedID_2'] == 1)
				{
					$file .= empty($arrID[0]['plrefno'])		? $this->fID.' Police Ref No'.$this->tID : '';
				}
				
				if($arrID[0]['attendedID_2'] == 1)
				{
					$file .= empty($arrID[0]['plcadno'])		? $this->fID.' Police CAD No'.$this->tID : '';
					$file .= empty($arrID[0]['plcvehicle'])		? $this->fID.' Police Vehicle'.$this->tID : '';
					$file .= empty($arrID[0]['policename'])		? $this->fID.' Police Name'.$this->tID : '';
					$file .= $arrID[0]['plcactionID'] <= 0 	 	? $this->fID.' Police Action'.$this->tID 	: '';
				}
				
				$file .= ($arrID[0]['statusID'] <= 0 || $arrID[0]['statusID'] == 2)		? $this->fID.' Closed'.$this->tID : '';
				
				if(($arrID[0]['notifiedID_1'] + $arrID[0]['notifiedID_3'] + $arrID[0]['notifiedID_6'] + $arrID[0]['notifiedID_4'] + $arrID[0]['notifiedID_5'] + $arrID[0]['notifiedID_7'] + $arrID[0]['notifiedID_8'] + $arrID[0]['attendedID_1'] + $arrID[0]['attendedID_3'] + $arrID[0]['attendedID_8'] + $arrID[0]['attendedID_9'] + $arrID[0]['attendedID_6'] + $arrID[0]['attendedID_4'] + $arrID[0]['attendedID_5'] + $arrID[0]['attendedID_7']) <= 0)
				{
					$file .= $this->fID.' Notified/Attended Checkbox Group Is Missing'.$this->tID;
				}
			}

			/* NON-SECURITY - INCIDENTS */
			if($arrID[0]['sincID'] == 2)
			{
				$file .= empty($arrID[0]['refno'])   	? $this->fID.' Ref No'.$this->tID : '';
				$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Incident Date'.$this->tID : '';
				$file .= empty($arrID[0]['timeID'])		? $this->fID.' Incident Time'.$this->tID : '';
				$file .= $arrID[0]['driverID'] <= 0		? $this->fID.' Driver Name'.$this->tID : '';
				$file .= empty($arrID[0]['location'])	? $this->fID.' Location'.$this->tID : '';
				$file .= $arrID[0]['suburb'] <= 0		? $this->fID.' Suburb'.$this->tID : '';
				$file .= empty($arrID[0]['reportby'])	? $this->fID.' Reported By'.$this->tID : '';				
				$file .= $arrID[0]['inctypeID'] <= 0	? $this->fID.' Incident Type'.$this->tID : '';				
				$file .= $arrID[0]['disciplineID'] <= 0	? $this->fID.' Discipline Related'.$this->tID : '';
				
				$file .= ($arrID[0]['statusID'] <= 0 || $arrID[0]['statusID'] == 2)		? $this->fID.' Closed'.$this->tID : '';
				
				if($arrID[0]['disciplineID'] == 1)
				{
					$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
					$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID 	   : '';
				}
				
				$file .= empty($arrID[0]['description']) ? $this->fID.' Description'.$this->tID 	: '';
				$file .= empty($arrID[0]['action'])	 	 ? $this->fID.' Action'.$this->tID 	: '';		
				$file .= $arrID[0]['actbyID'] <= 0 	 	 ? $this->fID.' Action Taken By'.$this->tID 	: '';
				
				if($arrID[0]['brs_statusID'] == 1)
				{
					$file .= empty($arrID[0]['shiftID'])   		? $this->fID.' Shift No'.$this->tID : '';
					$file .= empty($arrID[0]['routeID'])   		? $this->fID.' Route No'.$this->tID : '';
					$file .= empty($arrID[0]['busID'])   		? $this->fID.' Bus No'.$this->tID : '';
				}
				
				if($arrID[0]['plrefID'] == 1)
				{
					$file .= empty($arrID[0]['plrefno'])		? $this->fID.' Police Ref No'.$this->tID : '';
				}
			}			
		}		
		return $file;
	}
	
	Public function form_CommentLine($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('complaint',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			if($arrID[0]['cmltypeID'] == 491 || $arrID[0]['cmltypeID'] == 492)
			{
				$file .= empty($arrID[0]['refno'])   	? $this->fID.' Ref No'.$this->tID : '';			
			}
			$file .= (empty($arrID[0]['serDT']) || $arrID[0]['serDT'] == '0000-00-00' || $arrID[0]['serDT'] == '1970-01-01') ? $this->fID.' Comment ReportedOn'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Incident Date'.$this->tID : '';
			$file .= empty($arrID[0]['timeID'])		? $this->fID.' Incident Time'.$this->tID : '';	
			
			if($arrID[0]['tickID_1'] <= 0)
			{
				$file .= $arrID[0]['driverID'] <= 0		? $this->fID.' Driver Name'.$this->tID : '';
			}
			
			$file .= $arrID[0]['creasonID'] <= 0		? $this->fID.' Comment Line Reason'.$this->tID : '';
			$file .= $arrID[0]['cmltypeID'] <= 0		? $this->fID.' Comment Line Type'.$this->tID : '';
			$file .= empty($arrID[0]['description']) 	? $this->fID.' Description'.$this->tID : '';
			$file .= $arrID[0]['accID'] <= 0			? $this->fID.' Comment Line Type'.$this->tID : '';
			$file .= $arrID[0]['respID'] <= 0			? $this->fID.' Response Method'.$this->tID : '';
			if($arrID[0]['respID'] <> 46)
			{ 
				$file .= (empty($arrID[0]['resdate']) || $arrID[0]['resdate'] == '0000-00-00' || $arrID[0]['resdate'] == '1970-01-01') ? $this->fID.' Response Date'.$this->tID : '';
			}
			
			$file .= empty($arrID[0]['furaction']) 	? $this->fID.' Customer Response Details'.$this->tID : '';
			$file .= empty($arrID[0]['outcome']) 	? $this->fID.' Action Taken / Recommendations'.$this->tID : '';
			
			
			
			$file .= ($arrID[0]['accID'] == 52 || $arrID[0]['accID'] == 221 || $arrID[0]['accID'] == 49 || empty($arrID[0]['accID'])) ? ($arrID[0]['substanID'] <= 0	? $this->fID.' Substantiated'.$this->tID : '') : '';
			$file .= ($arrID[0]['accID'] == 52 || $arrID[0]['accID'] == 221 || $arrID[0]['accID'] == 49 || empty($arrID[0]['accID'])) ? ($arrID[0]['faultID'] <= 0	    ? $this->fID.' Fault/Not at Fault'.$this->tID : '') : '';
			
			if($arrID[0]['accID'] == 52 && $arrID[0]['substanID'] == 2)
			{
				/* DO NOTHING */
			}
			else
			{
				$file .= ($arrID[0]['accID'] == 52 || $arrID[0]['accID'] == 221 || $arrID[0]['accID'] == 49 || $arrID[0]['accID'] == 224 || empty($arrID[0]['accID'])) ? ($arrID[0]['invID'] <= 0		? $this->fID.' Investigated By'.$this->tID : '') : '';
				$file .= ($arrID[0]['accID'] == 52 || $arrID[0]['accID'] == 221 || $arrID[0]['accID'] == 49 || $arrID[0]['accID'] == 224 || empty($arrID[0]['accID'])) ? ($arrID[0]['invdate'] == '0000-00-00' || empty($arrID[0]['invdate']) ? $this->fID.' Investigation Date'.$this->tID : '') : '';
			}
			
			$file .= ($arrID[0]['statusID'] <= 0 || $arrID[0]['statusID'] == 2)		? $this->fID.' Closed'.$this->tID : '';
			
			if($arrID[0]['accID'] == 48 && $arrID[0]['disciplineID'] == 1)
			{
				$file .= ($arrID[0]['invID'] <= 0		? $this->fID.' Investigated By'.$this->tID : '');
				$file .= ($arrID[0]['invdate'] == '0000-00-00' || empty($arrID[0]['invdate']) ? $this->fID.' Investigation Date'.$this->tID : '');
			}
			
			if($arrID[0]['accID'] == 52)
			{
				$file .= empty($arrID[0]['busID'])		 ? $this->fID.' Bus No'.$this->tID : '';
				$file .= empty($arrID[0]['routeID'])	 ? $this->fID.' Route'.$this->tID : '';
				$file .= empty($arrID[0]['location'])	 ? $this->fID.' Location'.$this->tID : '';
			}
			
			if($arrID[0]['location'] <> '')
			{
				$file .= $arrID[0]['suburb'] <= 0	 ? $this->fID.' Suburb'.$this->tID : '';
			}
			
			if($arrID[0]['accID'] == 52 || $arrID[0]['accID'] == 221 || $arrID[0]['accID'] == 49 || empty($arrID[0]['accID']))
			{
				$file .= $arrID[0]['disciplineID'] <= 0		? $this->fID.' Discipline Related'.$this->tID : '';
				
				if($arrID[0]['disciplineID'] == 1)
				{
					$file .= empty($arrID[0]['mcomments'])	 ? $this->fID.' Manager Comments'.$this->tID : '';
					$file .= $arrID[0]['wrtypeID'] <= 0		 ? $this->fID.' Warning Type'.$this->tID 	   : '';
					$file .= $arrID[0]['intvID'] <= 0		 ? $this->fID.' Interviewed By'.$this->tID 	   : '';
					$file .= ($arrID[0]['intvDate'] == '0000-00-00' || empty($arrID[0]['intvDate']) ? $this->fID.' Interviewed Date'.$this->tID : '');
				}
			}
		}
		return $file;
	}
	
	Public function form_Accident($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('accident_regis',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['refno'])   		? $this->fID.' Ref No'.$this->tID : '';
			$file .= empty($arrID[0]['busID'])   		? $this->fID.' Bus No'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Accident Date'.$this->tID : '';
			$file .= empty($arrID[0]['timeID']) ? $this->fID.' Accident Time'.$this->tID : '';			
			
			if($arrID[0]['tickID_1'] <= 0)
			{
				$file .= $arrID[0]['staffID'] <= 0			? $this->fID.' Driver Name'.$this->tID : '';
			}
			
			$file .= $arrID[0]['acccatID'] <= 0			? $this->fID.' Accident Category'.$this->tID : '';
			$file .= $arrID[0]['accID'] <= 0			? $this->fID.' Accident Details'.$this->tID : '';
			$file .= $arrID[0]['responsibleID'] <= 0	? $this->fID.' Driver Responsible'.$this->tID : '';
			
			$file .= ($arrID[0]['insinvolvedID'] == 1 && empty($arrID[0]['insurer'])   		? $this->fID.' Insurer'.$this->tID : '');
			$file .= ($arrID[0]['insinvolvedID'] == 1 && empty($arrID[0]['claimno'])   		? $this->fID.' Claim No'.$this->tID : '');
			$file .= ($arrID[0]['insinvolvedID'] == 1 && empty($arrID[0]['invno'])   		? $this->fID.' Invoice No'.$this->tID : '');
				
			$file .= ($arrID[0]['witnessID'] == 1 && empty($arrID[0]['witnessName'])   		? $this->fID.' Witness Name'.$this->tID : '');
			$file .= ($arrID[0]['witnessID'] == 1 && empty($arrID[0]['witnessContact'])   		? $this->fID.' Witness Contact No'.$this->tID : '');
			
			$file .= empty($arrID[0]['location'])   	? $this->fID.' Location'.$this->tID : '';
			$file .= $arrID[0]['suburb'] <= 0			? $this->fID.' Suburb'.$this->tID : '';
			$file .= $arrID[0]['damagetobusID'] <= 0	? $this->fID.' Damage to Bus'.$this->tID : '';
			$file .= empty($arrID[0]['description']) 	? $this->fID.' Reason'.$this->tID : '';
			$file .= is_null($arrID[0]['rprcost'])   		? $this->fID.' Bus Repairs (Cost)'.$this->tID : '';
			$file .= is_null($arrID[0]['othcost'])      	? $this->fID.' Other Repairs (Cost)'.$this->tID : '';			
			$file .= empty($arrID[0]['optID_1']) || is_null($arrID[0]['optID_1']) || $arrID[0]['optID_1'] <= 0	? $this->fID.' Photographs of Damage'.$this->tID : '';
			$file .= empty($arrID[0]['optID_2']) || is_null($arrID[0]['optID_2']) || $arrID[0]['optID_2'] <= 0	? $this->fID.' Driver Breath Tested'.$this->tID  : '';
			$file .= empty($arrID[0]['optID_3']) || is_null($arrID[0]['optID_3']) || $arrID[0]['optID_3'] <= 0	? $this->fID.' Driver Drug Tested'.$this->tID 	 : '';
			$file .= empty($arrID[0]['outcome'])   		? $this->fID.' Investigation Outcome / Recommendations '.$this->tID : '';			
			$file .= $arrID[0]['invID'] <= 0			? $this->fID.' Investigated By'.$this->tID : '';
			$file .= $arrID[0]['disciplineID'] <= 0			? $this->fID.' Discipline Related'.$this->tID : '';			
			$file .= ($arrID[0]['progressID'] <= 0 || $arrID[0]['progressID'] == 2)			? $this->fID.' Progress'.$this->tID : '';			
			
			if($arrID[0]['3partyID'] == 1)
			{
				$file .= empty($arrID[0]['thpnameID']) 		? $this->fID.' Third Party Name'.$this->tID : '';
				$file .= empty($arrID[0]['regisnoID']) 		? $this->fID.' Third Party Rego No'.$this->tID : '';
				$file .= empty($arrID[0]['thcontactID']) 	? $this->fID.' Third Party Contact Info'.$this->tID : '';
			}
			
			if($arrID[0]['disciplineID'] == 1)
			{
				$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
				$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID 	   : '';
				$file .= $arrID[0]['intvID'] <= 0		 ? $this->fID.' Interviewed By'.$this->tID 	   : '';
				$file .= ($arrID[0]['intvDate'] == '0000-00-00' || empty($arrID[0]['intvDate']) ? $this->fID.' Interviewed Date'.$this->tID : '');
			}
		}		
		return $file;
	}
	
	Public function form_Infringment($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('infrgs',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['refno']) ? $this->fID.' Infringement No'.$this->tID : '';
			$file .= empty($arrID[0]['vehicle']) ? $this->fID.' Vehicle Rego'.$this->tID : '';	
			$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Incident Date'.$this->tID : '';
			$file .= empty($arrID[0]['timeID']) ? $this->fID.' Incident Time'.$this->tID : '';			
			$file .= $arrID[0]['staffID'] <= 0	? $this->fID.' Driver Name'.$this->tID : '';
			 
			$file .= (empty($arrID[0]['dateID_1']) || $arrID[0]['dateID_1'] == '0000-00-00' || $arrID[0]['dateID_1'] == '1970-01-01') ? $this->fID.' Issue Date'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID_2']) || $arrID[0]['dateID_2'] == '0000-00-00' || $arrID[0]['dateID_2'] == '1970-01-01') ? $this->fID.' Compliance Date'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID_3']) || $arrID[0]['dateID_3'] == '0000-00-00' || $arrID[0]['dateID_3'] == '1970-01-01') ? $this->fID.' Date Recieved'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID_4']) || $arrID[0]['dateID_4'] == '0000-00-00' || $arrID[0]['dateID_4'] == '1970-01-01') ? $this->fID.' Date Sent'.$this->tID : '';			
			$file .= $arrID[0]['invID'] <= 0		? $this->fID.' Investigated By'.$this->tID : '';			
			$file .= $arrID[0]['inftypeID'] <= 0	? $this->fID.' Infringement Type'.$this->tID : '';
			$file .= ($arrID[0]['statusID'] <= 0 || $arrID[0]['statusID'] == 2)		? $this->fID.' Closed'.$this->tID : '';
			
			if($arrID[0]['inftypeID'] == 162)
			{
				$file .= empty($arrID[0]['description'])	? $this->fID.' If Other Infringement Type (Please Specify)'.$this->tID : '';
			}
			
			$file .= empty($arrID[0]['description_1'])	? $this->fID.' Location of Infringement'.$this->tID : '';			
			
			$file .= $arrID[0]['disciplineID'] <= 0			? $this->fID.' Discipline Related'.$this->tID : '';
			
			if($arrID[0]['disciplineID'] == 1)
			{
				$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
				$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID 	   : '';
				$file .= $arrID[0]['intvID'] <= 0		 ? $this->fID.' Interviewed By'.$this->tID 	   : '';
				$file .= $arrID[0]['intvDate'] == '0000-00-00' || empty($arrID[0]['intvDate']) ? $this->fID.' Interviewed Date'.$this->tID : '';
			}
		}		
		return $file;
	}
	
	Public function form_Inspection($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('inspc',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['rptno']) ? $this->fID.' Report No'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Report Date'.$this->tID : '';
			$file .= $arrID[0]['empID'] <= 0			? $this->fID.' Driver Name'.$this->tID : '';			
			$file .= $arrID[0]['insrypeID'] <= 0 		? $this->fID.' Inspection Result'.$this->tID : '';
			$file .= $arrID[0]['inspectedby'] <= 0 		? $this->fID.' Inspected By'.$this->tID : '';
			$file .= (empty($arrID[0]['dateID_1']) || $arrID[0]['dateID_1'] == '0000-00-00' || $arrID[0]['dateID_1'] == '1970-01-01') ? $this->fID.' Inspected Date'.$this->tID : '';
			$file .= $arrID[0]['servicenoID'] <= 0 		? $this->fID.' Service No'.$this->tID : '';
			$file .= empty($arrID[0]['serviceinfID'])	? $this->fID.' Service Info'.$this->tID : '';
			$file .= $arrID[0]['srtpointID'] <= 0 		? $this->fID.' Service Time Point'.$this->tID : '';			
			
			if($arrID[0]['insrypeID'] == 300 || $arrID[0]['insrypeID'] == 268 || $arrID[0]['insrypeID'] == 261|| $arrID[0]['insrypeID'] == 271 || $arrID[0]['insrypeID'] == 301|| $arrID[0]['insrypeID'] == 377 ||$arrID[0]['insrypeID'] == 381|| $arrID[0]['insrypeID'] == 388 || $arrID[0]['insrypeID'] == 390 || $arrID[0]['insrypeID'] == 396  || $arrID[0]['insrypeID'] == 398 || $arrID[0]['insrypeID'] == 399)
			{
				$file .= $arrID[0]['fineID'] <= 0 		? $this->fID.' Fine'.$this->tID  : '';
			}
						
			$file .= empty($arrID[0]['shiftID'])   		? $this->fID.' Shift No'.$this->tID : '';
			$file .= empty($arrID[0]['busID'])   		? $this->fID.' Bus No'.$this->tID : '';
			$file .= empty($arrID[0]['timeID_1'])   	? $this->fID.' Scheduled Depature Time'.$this->tID : '';
			$file .= empty($arrID[0]['timeID_2'])   	? $this->fID.' Timing Point Time'.$this->tID : '';
			$file .= empty($arrID[0]['timeID_3'])   	? $this->fID.' Actual Time'.$this->tID : '';			
			$file .= empty($arrID[0]['description'])    ? $this->fID.' Description'.$this->tID : '';
			$file .= empty($arrID[0]['description_2'])   ? $this->fID.' PTA Response'.$this->tID : '';
			$file .= $arrID[0]['invstID'] <= 0			? $this->fID.' Investigated By'.$this->tID : '';
			$file .= $arrID[0]['trisID'] <= 0 			? $this->fID.' Tris Complete'.$this->tID : '';
			$file .= $arrID[0]['disciplineID'] <= 0		? $this->fID.' Discipline Required'.$this->tID : '';
			$file .= ($arrID[0]['statusID'] <= 0 || $arrID[0]['statusID'] == 2)			? $this->fID.' Closed'.$this->tID : '';
			
			if($arrID[0]['disciplineID'] == 1)
			{
				$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
				$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID 	   : '';
				$file .= $arrID[0]['intvID'] <= 0		 ? $this->fID.' Interviewed By'.$this->tID 	   : '';
				$file .= $arrID[0]['intvDate'] == '0000-00-00' || empty($arrID[0]['intvDate']) ? $this->fID.' Interviewed Date'.$this->tID : '';
			}
		}		
		return $file;
	}
	
	Public function form_ManangerComments($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('mng_cmn',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= (empty($arrID[0]['dateID']) || $arrID[0]['dateID'] == '0000-00-00' || $arrID[0]['dateID'] == '1970-01-01') ? $this->fID.' Date'.$this->tID : '';			
			$file .= $arrID[0]['staffID'] <= 0 		 ? $this->fID.' Driver Name'.$this->tID : '';
			$file .= $arrID[0]['invID'] <= 0 		 ? $this->fID.' Investigated By'.$this->tID : '';			
			$file .= empty($arrID[0]['description']) ? $this->fID.' Description'.$this->tID : '';
			$file .= empty($arrID[0]['mcomments'])   ? $this->fID.' Manager Comments'.$this->tID : '';
			$file .= $arrID[0]['wrtypeID'] <= 0 	 ? $this->fID.' Warning Type'.$this->tID : '';			
		}		
		return $file;
	}
	
	Public function form_HealthSafetyEnvironmental($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('hiz_regis',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['refno'])   	 ? $this->fID.' HZ No'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['rdateID']) ? $this->fID.' Report Date'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['dateID']) ? $this->fID.' Date of Occurance'.$this->tID : '';
			$file .= empty($arrID[0]['timeID'])    	 ? $this->fID.' Time'.$this->tID : '';
			$file .= $arrID[0]['jobID'] <= 0		 ? $this->fID.' JOB Title'.$this->tID : '';
			$file .= $arrID[0]['hztypeID'] <= 0		 ? $this->fID.' Hazard Type'.$this->tID : '';						
			$file .= $arrID[0]['reportBY'] <= 0		 ? $this->fID.' Reported By'.$this->tID : '';
			$file .= empty($arrID[0]['location'])    ? $this->fID.' Location'.$this->tID : '';
			$file .= empty($arrID[0]['description']) ? $this->fID.' Description'.$this->tID : ''; 
			$file .= empty($arrID[0]['descriptionACT']) ? $this->fID.' Action Taken'.$this->tID : ''; 
			$file .= $arrID[0]['fstaffID'] <= 0		 ? $this->fID.' Staff Name'.$this->tID : '';
			$file .= $arrID[0]['fdesigID'] <= 0		 ? $this->fID.' Staff Designation'.$this->tID : '';						
			$file .= $this->checkDate_Counts($arrID[0]['rcdateID']) ? $this->fID.' Reciept Date'.$this->tID : '';
			$file .= $arrID[0]['optID_u1'] <= 0		 ? $this->fID.' Unmanaged Likelihood'.$this->tID : '';
			$file .= $arrID[0]['optID_u3'] <= 0		 ? $this->fID.' Unmanaged Exposure'.$this->tID : '';
			$file .= $arrID[0]['optID_u4'] <= 0		 ? $this->fID.' Consequence/Impact'.$this->tID : '';
			$file .= $arrID[0]['optID_u5'] <= 0		 ? $this->fID.' Secondary Choice'.$this->tID : '';				
			$file .= empty($arrID[0]['descriptionINV']) ? $this->fID.' Investigation'.$this->tID : ''; 
			$file .= empty($arrID[0]['descriptionACD']) ? $this->fID.' Action'.$this->tID : ''; 
			$file .= $arrID[0]['invID'] <= 0		? $this->fID.' Investigation By'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['invDate']) ? $this->fID.' Investigation Date'.$this->tID : '';
			$file .= $arrID[0]['actID'] <= 0		? $this->fID.' Action By'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['actDate']) ? $this->fID.' Action Date'.$this->tID : '';
			$file .= $arrID[0]['optID_m1'] <= 0		 ? $this->fID.' Managed Likelihood'.$this->tID : '';
			$file .= $arrID[0]['optID_m3'] <= 0		 ? $this->fID.' Managed Exposure '.$this->tID : '';
			$file .= $arrID[0]['optID_m4'] <= 0		 ? $this->fID.' Consequence/Impact '.$this->tID : '';
			$file .= $arrID[0]['optID_m5'] <= 0		 ? $this->fID.' Secondary Choice '.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['hzrconDT']) ? $this->fID.' Hazard Corrected Date'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['empadvDT']) ? $this->fID.' Date Employee Advised'.$this->tID : '';						
			$file .= $arrID[0]['statusID'] <= 1		? $this->fID.' Status'.$this->tID : '';
			
			$file .= $arrID[0]['act_effID'] <= 0	? $this->fID.' Action Effective'.$this->tID : '';
			$file .= $arrID[0]['fupreqID'] <= 0		? $this->fID.' Follow-Up Required'.$this->tID : '';
			
			if(empty($arrID[0]['fupreqID']) || ($arrID[0]['fupreqID'] == 0) || ($arrID[0]['fupreqID'] == 1))
			{
				$file .= empty($arrID[0]['fupDesc'])   ? $this->fID.' Follow-Up Details'.$this->tID : '';
				$file .= $arrID[0]['fupcmpID'] <= 0	   ? $this->fID.' Follow-Up Completed By'.$this->tID : '';
				
				$file .= $this->checkDate_Counts($arrID[0]['fupreqDT']) ? $this->fID.' Proposed Follow-Up Date'.$this->tID : '';
				$file .= $this->checkDate_Counts($arrID[0]['fupcmpDT']) ? $this->fID.' Follow-Up Completed Date'.$this->tID : '';
			}
		}		
		return $file;
	}
	
	Public function form_SystemImporvmentRequest($ID)
	{
		if(!empty($ID))
		{
			$arrID = ($ID > 0  ? $this->select('sir_regis',array("*"), " WHERE ID = ".$ID." ") : '');
			
			$file = '';
			
			$file .= empty($arrID[0]['refno'])   	? $this->fID.' Improvement No'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['issuetoDT']) == 1 ? $this->fID.' Issue Date'.$this->tID : '';
			$file .= $arrID[0]['srtypeID'] <= 0		? $this->fID.' SIR Type'.$this->tID : '';
			$file .= $arrID[0]['issuedTO'] <= 0		? $this->fID.' SIR Issued To'.$this->tID : '';
			$file .= $arrID[0]['originatorID'] <= 0	? $this->fID.' Originator'.$this->tID : '';
			$file .= $arrID[0]['resultsINV'] <= 0	? $this->fID.' Investigation Results'.$this->tID : '';						
			$file .= $arrID[0]['invID'] <= 0		? $this->fID.' Completed By'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['invDate']) == 1 ? $this->fID.' Completed Date'.$this->tID : '';
			$file .= empty($arrID[0]['action'])   	? $this->fID.' Action Details'.$this->tID : '';
			$file .= $arrID[0]['actID'] <= 0		? $this->fID.' Action By'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['actDate']) == 1 ? $this->fID.' Action Date'.$this->tID : '';			
			$file .= $arrID[0]['acteffID'] <= 0		? $this->fID.' Action Effective'.$this->tID : '';
			$file .= $arrID[0]['fupreqID'] <= 0		? $this->fID.' Follow-up Required'.$this->tID : '';
			
			$file .= $this->checkDate_Counts($arrID[0]['clsoutDT']) == 1 ? $this->fID.' Improvement Close Out Date'.$this->tID : '';
			$file .= $this->checkDate_Counts($arrID[0]['orgadvDT']) == 1 ? $this->fID.' Date Originator Advised'.$this->tID : '';
			$file .= $arrID[0]['statusID'] <= 1		? $this->fID.' Status'.$this->tID : '';
			
			if($arrID[0]['resultsINV'] == 8000)
			{
				$file .= empty($arrID[0]['otherINV']) ? $this->fID.' Investigation - Other'.$this->tID : '';
			}
			
			if(empty($arrID[0]['fupreqID']) || ($arrID[0]['fupreqID'] == 0) || ($arrID[0]['fupreqID'] == 1))
			{
				$file .= empty($arrID[0]['fupDesc'])   ? $this->fID.' Follow-Up Details'.$this->tID : '';
				$file .= $arrID[0]['fupcmpID'] <= 0		? $this->fID.' Follow-Up Completed By'.$this->tID : '';
				
			    $file .= $this->checkDate_Counts($arrID[0]['fupcmpDT']) == 1 ? $this->fID.' Follow-Up Completed Date'.$this->tID : '';			
				$file .= $this->checkDate_Counts($arrID[0]['fupreqDT']) == 1 ? $this->fID.' Proposed Follow-Up Date'.$this->tID : '';				
			}
		}		
		return $file;
	}
}
?>