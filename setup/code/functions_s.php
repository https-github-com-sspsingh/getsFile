<?PHP
class SFunctions extends RFunctions
{ 
	public function CurlService($ID)
	{
		if(!empty($ID) && ($ID > 0))
		{
			$arrIN = $ID > 0 ? $this->select('incident_regis',array("*"), " WHERE sincID = 1 AND ID = ".$ID." ") : '';
			
			if($arrIN[0]['ID'] > 0)
			{
				$dataINC = '';
				$dataOFC = '';
				
				$urlAPI  = "http://services.transperth.wa.gov.au/businc.cfc?";
				$dataINC = $this->RUN_Incident_API($ID);
				$dataOFC = $this->RUN_Offence_API($ID);
				
				if($dataINC <> '')	/* SEND METHOD BY - INCIDENT - DATA */
				{
					$retA = array();
					$retA['refID'] = $arrIN[0]['ID'];
					$retA['refNO'] = $arrIN[0]['refno'];
					
					$curlTL = curl_init();
					curl_setopt($curlTL,CURLOPT_URL,$urlAPI);
					curl_setopt($curlTL,CURLOPT_RETURNTRANSFER,true);
					curl_setopt($curlTL,CURLOPT_HEADER, false);
					curl_setopt($curlTL,CURLOPT_NOBODY, false);
					curl_setopt($curlTL, CURLOPT_POST, count($dataINC));
					curl_setopt($curlTL, CURLOPT_POSTFIELDS, $dataINC);
					$returnRES = curl_exec($curlTL);
					$returnERR = curl_error($curlTL);
					
					if($returnRES == 'Bad Request')
					{
						$retA['statusTX'] = file_get_contents(preg_replace("/ /", "%20", ($urlAPI.$dataINC)));
						$retA['urlTX'] 	  = base64_encode('FILE - '.$urlAPI.$dataINC);
					}
					else
					{
						$retA['statusTX'] = $returnRES <> '' ? $returnRES : $returnERR;
						$retA['urlTX'] 	  = base64_encode('CURL - '.$urlAPI.$dataINC);
					}
					
					$retA['typeID'] = 'INCIDENT';					
					$this->CurlServiceLog($retA);					
					curl_close($curlTL);
				}
				
				if($dataOFC <> '')	/* SEND METHOD BY - OFFENCE - DATA */
				{
					$retB = array();
					$retB['refID'] = $arrIN[0]['ID'];
					$retB['refNO'] = $arrIN[0]['refno'];
					
					$curlTL = curl_init();  
					curl_setopt($curlTL,CURLOPT_URL,$urlAPI);
					curl_setopt($curlTL,CURLOPT_RETURNTRANSFER,true);
					curl_setopt($curlTL,CURLOPT_HEADER, false);
					curl_setopt($curlTL,CURLOPT_NOBODY, false);
					curl_setopt($curlTL, CURLOPT_POST, count($dataOFC));
					curl_setopt($curlTL, CURLOPT_POSTFIELDS, $dataOFC);
					$returnRES = curl_exec($curlTL);
					$returnERR = curl_error($curlTL);
					
					if($returnRES == 'Bad Request')
					{ 
						$retB['statusTX'] = file_get_contents(preg_replace("/ /", "%20", ($urlAPI.$dataOFC)));
						$retB['urlTX'] 	  = base64_encode('FILE - '.$urlAPI.$dataOFC);
					}
					else
					{
						$retB['statusTX'] = $returnRES <> '' ? $returnRES : $returnERR;
						$retB['urlTX'] 	  = base64_encode('CURL - '.$urlAPI.$dataOFC);
					}
					
					$retB['typeID'] = 'OFFENCE';														
					$this->CurlServiceLog($retB);
					
					curl_close($curlTL);
				}
			}
		}
	}
	
	public function CurlServiceLog($arrTL)
	{
		$arr = array();
		$arr['dateID'] = date('Y-m-d');
		$arr['timeID'] = date('h : i :s: A');
		$arr['userID'] = $_SESSION[$this->website]['userID'];
		$arr['companyID'] = $_SESSION[$this->website]['compID'];		
		foreach($arrTL as $key=>$value)	{$arr[$key]	=	$value;}
		$this->BuildAndRunInsertQuery('api_senders_logs',$arr);
	}
	
    public function RUN_Incident_API($ID)
    {
        if(!empty($ID) && ($ID > 0))
        {
            $arrayIN = $ID > 0 ? $this->select('incident_regis',array("*"), " WHERE sincID = 1 AND ID = ".$ID." ") : '';

            $SubUrbID = '';	$urlID = "";	
            $FileAuthor = ($_SESSION[$this->website]['empNM'] <> '' ? $_SESSION[$this->website]['empNM'] : $_SESSION[$this->website]['fullNM']);
            $timeID = '';

            if($arrayIN[0]['ID'] > 0)
            { 
                $urlID .= "method=InsertBusIncidents";
                $timeID = explode(":",str_replace(" ","",$arrayIN[0]['timeID']));

                $DR_Array  = ($arrayIN[0]['driverID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$arrayIN[0]['driverID']." ")   : '');
                $IN_Array  = ($arrayIN[0]['inctypeID'] > 0   ? $this->select('master',array("*"), " WHERE ID = ".$arrayIN[0]['inctypeID']." ")    : '');
                $SB_Array  = ($arrayIN[0]['suburb'] > 0      ? $this->select('suburbs',array("*"), " WHERE ID = ".$arrayIN[0]['suburb']." ")  	  : '');
                $PA_Array  = ($arrayIN[0]['plcactionID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$arrayIN[0]['plcactionID']." ")  : '');				
                $typeID_3  = ($arrayIN[0]['companyID'] > 0   ? $this->select('api_mappings',array("*"), " WHERE dbaID = ".$arrayIN[0]['companyID']." AND typeID = 3 ")  : '');
                $typeID_4  = ($arrayIN[0]['weaponsID'] > 0   ? $this->select('api_mappings',array("*"), " WHERE dbaID = ".$arrayIN[0]['weaponsID']." AND typeID = 4 ")  : '');
                $SubUrbID  = ($arrayIN[0]['suburb'] > 0      ? ($SB_Array[0]['title']) : '');

                if($arrayIN[0]['refno'] <> '')
                {
                    if($arrayIN[0]['refno'] <> '')	 {$urlID .= "&TransPerthRef=".$arrayIN[0]['refno'];}				
                    if($typeID_3[0]['apiID'] > 0)	 {$urlID .= "&Depotid=".$typeID_3[0]['apiID'];}

                    $urlID .= "&Location=".str_replace("&","-",$arrayIN[0]['location']);
                    $urlID .= "&CrossStreet=".($arrayIN[0]['crossst'] <> '' ? $arrayIN[0]['crossst'] : null);
                    $urlID .= "&SuburbName=".($SubUrbID <> '' ? $SubUrbID : '');
                    $urlID .= "&ReportDate=".($this->VdateFormat($arrayIN[0]['rpdateID']));
                    $urlID .= "&IncidentDate=".($this->VdateFormat($arrayIN[0]['dateID']));					
                    $urlID .= "&IncidentTime=".($arrayIN[0]['timeID'] <> '' ? trim($timeID[0].''.$timeID[1]) : null);
                    $urlID .= "&BusRouteNo=".($arrayIN[0]['routeID'] <> '' ? $arrayIN[0]['routeID'] : null);
                    $urlID .= "&VehicleNo=".($arrayIN[0]['busID'] <> '' ? $arrayIN[0]['busID'] : null);					
                    $urlID .= "&Description=".($arrayIN[0]['description'] <> '' ? str_replace('|','',(str_replace('\/','',(str_replace('"','',$arrayIN[0]['description']))))) : null);					
                    $urlID .= (($arrayIN[0]['shiftID']) > 0 ? "&ShiftNo=".$arrayIN[0]['shiftID'] : "");
                    $urlID .= "&PoliceNotified=".(!empty($arrayIN[0]['plrefID']) && ($arrayIN[0]['plrefID'] >= 1) ? $arrayIN[0]['plrefID'] : '0');
                    $urlID .= "&PoliceAttended=".($arrayIN[0]['attendedID_2'] >= 1 ? '1' : '0');					
                    $urlID .= "&PoliceVehicle=".($arrayIN[0]['plcvehicle'] <> '' ? $arrayIN[0]['plcvehicle'] : null);
                    $urlID .= "&PoliceCadno=".($arrayIN[0]['plcadno'] <> '' ? $arrayIN[0]['plcadno'] : null);
                    $urlID .= "&PoliceIRNo=".null;
                    $urlID .= "&PoliceName=".($arrayIN[0]['policename'] <> '' ? $arrayIN[0]['policename'] : null);
                    $urlID .= "&FireBrigade=".($arrayIN[0]['attendedID_8'] == 1 ? '1' : '0');
                    $urlID .= "&Ambulance=".($arrayIN[0]['attendedID_9'] == 1 ? '1' : '0');
                    $urlID .= ($typeID_4[0]['apiID'] > 0 ? "&WeaponsId=".$typeID_4[0]['apiID'] : '');
                    $urlID .= "&DamageInjury=".($arrayIN[0]['dmginjury'] <> '' ? $arrayIN[0]['dmginjury'] : null);					
                    $urlID .= "&VideoAvailable=".($arrayIN[0]['notifiedID_8'] == 1 ? '1' : '0');
                    $urlID .= "&TTOAttended=".($arrayIN[0]['attendedID_7'] == 1 ? '1' : '0');
                    $urlID .= "&TTONotified=".($arrayIN[0]['notifiedID_7'] == 1 ? '1' : '0');					
                    $urlID .= "&SForceReportNo=".null;
                    $urlID .= ((int)$arrayIN[0]['dmvalue'] > 0 ? "&DamageValue=".(int)$arrayIN[0]['dmvalue'] : '');					
                    $urlID .= "&PoliceTimeCalled=".null;
                    $urlID .= "&PoliceTimeArrived=".null;
                    $urlID .= "&TPCalled=".null;				
                    $urlID .= "&TPArrived=".null; 
                    $urlID .= "&TPSecurityID=".($arrayIN[0]['cmrno'] <> '' ? $arrayIN[0]['cmrno'] : null);
                    $urlID .= "&SecurityOfficerNames=".null;
                    $urlID .= "&PatrolType=".null;
                    $urlID .= "&PoliceCalled=".null;				
                    $urlID .= "&PoliceArrived=".null; 
                    $urlID .= "&Statistical=".null;
                    $urlID .= "&StatisticalReason=".null;
                    $urlID .= "&TPSecurityTimeCalled=".null;
                    $urlID .= "&TPSecurityTimeArrived=".null;
                    $urlID .= "&Duplicate=".null; 
                    $urlID .= "&FileTimeStamp=".(string)date('d/m/Y H:i:s');
                    $urlID .= "&FileAuthor=".trim($FileAuthor);
                    $urlID .= "&FileCompany=Swan Transit";
                    $urlID .= "&AuthKey=EN_0510DEVINC1978_327AKY";
					
                    return $urlID;
                }
            }
        }
    }
    
    public function RUN_Offence_API($ID)
    {
        if(!empty($ID) && ($ID > 0))
        {
            $arrayIN = $ID > 0 ? $this->select('incident_regis',array("*"), " WHERE sincID = 1 AND ID = ".$ID." ") : '';

            $SubUrbID = '';	$urlID = "";	
            $FileAuthor = ($_SESSION[$this->website]['empNM'] <> '' ? $_SESSION[$this->website]['empNM'] : $_SESSION[$this->website]['fullNM']);
            $timeID = '';

            if($arrayIN[0]['ID'] > 0)
            { 
                $urlID .= "method=InsertBusIncOffences";
                $timeID = explode(":",$this->rows['timeID']);

                $typeID_1 = ($arrayIN[0]['offdtlsID'] > 0 ? $this->select('api_mappings',array("*"), " WHERE dbaID = ".$arrayIN[0]['offdtlsID']." AND typeID = 1 ") : '');
                $typeID_3 = ($arrayIN[0]['companyID'] > 0 ? $this->select('api_mappings',array("*"), " WHERE dbaID = ".$arrayIN[0]['companyID']." AND typeID = 3 ") : '');

                if($arrayIN[0]['refno'] <> '')
                {
                    if($arrayIN[0]['refno'] <> '')	{$urlID .= "&TransPerthRef=".$arrayIN[0]['refno'];}

                    $urlID .= "&OffenceId=".($typeID_1[0]['apiID']);
                    $urlID .= "&FileTimeStamp=".date('d/m/Y H:i:s');
                    $urlID .= "&FileAuthor=".trim($FileAuthor);
                    $urlID .= "&FileCompany=Swan Transit";
                    $urlID .= "&AuthKey=EN_0510DEVINC1978_327AKY";
					
					return $urlID;
                }
            }
		}
    }
    
    public function ActiveSidebar($caseID)
    {
		$file = '';
		$baseID = str_replace('.php','',basename($_SERVER['PHP_SELF']));
		
		if($caseID == 1)
		{
			$file['cs'] = ($baseID == 'company' || $baseID == 'scompany' || $baseID == 'psw_reset' || $baseID == 'frmset' || $baseID == 'urole' || $baseID == 'users') ? 'active' : '';
			$file['cl'] = ($baseID == 'company' || $baseID == 'scompany' || $baseID == 'psw_reset' || $baseID == 'frmset' || $baseID == 'urole' || $baseID == 'users') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 2)
		{
			$file['cs'] = ($baseID == 'master' || $baseID == 'offence' || $baseID == 'stype') ? 'active' : '';
			$file['cl'] = ($baseID == 'master' || $baseID == 'offence' || $baseID == 'stype') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 3)
		{
			$file['cs'] = ($baseID == 'slabs_perf' || $baseID == 'srvdtls' || $baseID == 'buses' || $baseID == 'suburbs' || $baseID == 'contracts' || $baseID == 'cstmpoint' || $baseID == 'frm_fields' || $baseID == 'rbuilder') ? 'active' : '';
			$file['cl'] = ($baseID == 'slabs_perf' || $baseID == 'srvdtls' || $baseID == 'buses' || $baseID == 'suburbs' || $baseID == 'contracts' || $baseID == 'cstmpoint' || $baseID == 'frm_fields' || $baseID == 'rbuilder') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 4)
		{
			$file['cs'] = ($baseID == 'etransfer' || $baseID == 'etransferin' || $baseID == 'etransferout' || $baseID == 'emp' || $baseID == 'sicklv' || $baseID == 'prpermits') ? 'active' : '';
			$file['cl'] = ($baseID == 'etransfer' || $baseID == 'etransferin' || $baseID == 'etransferout' || $baseID == 'emp' || $baseID == 'sicklv' || $baseID == 'prpermits') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 5)
		{
			$file['cs'] = ($baseID == 'cmplnt' || $baseID == 'incident' || $baseID == 'accident' || $baseID == 'infrgs' || $baseID == 'mng_cmn' || $baseID == 'inspc') ? 'active' : '';
			$file['cl'] = ($baseID == 'cmplnt' || $baseID == 'incident' || $baseID == 'accident' || $baseID == 'infrgs' || $baseID == 'mng_cmn' || $baseID == 'inspc') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 6)
		{
			$file['cs'] = ($baseID == 'wshifts' || $baseID == 'shifts') ? 'active' : '';
			$file['cl'] = ($baseID == 'wshifts' || $baseID == 'shifts') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 7)
		{
			$file['cs'] = ($baseID == 'rpt_emp' || $baseID == 'rpt_sicklv' || $baseID == 'rpt_downloads' || $baseID == 'rpt_userlogs' || $baseID == 'rpt_accident' || $baseID == 'rpt_cmline' || $baseID == 'rpt_incident' || $baseID == 'rpt_infrgs' || $baseID == 'rpt_inspc' || $baseID == 'rpt_mngcmn' || $baseID == 'rpt_signons' || $baseID == 'rpt_prior_alloc' || $baseID == 'rpt_allocsheet' || $baseID == 'profile_5' || $baseID == 'profile_6' || $baseID == 'rpt_headers' || $baseID == 'rpt_stfare' || $baseID == 'rpt_hiz' || $baseID == 'rpt_sir') ? 'active' : '';
		}
		else if($caseID == 8)
		{
			$file['cs'] = ($baseID == 'rpt_performance' || $baseID == 'profile_2' || $baseID == 'profile_1' || $baseID == 'profile_3') ? 'active' : '';
			$file['cl'] = ($baseID == 'rpt_performance' || $baseID == 'profile_2' || $baseID == 'profile_1' || $baseID == 'profile_3') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 9)
		{
			$file['cs'] = ($baseID == 'resetPsw') ? 'class="active"' : '';
		}
		else if($caseID == 10)
		{
			$file['cs'] = ($baseID == 'feed') ? 'class="active"' : '';
		}
		else if($caseID == 11)
		{
			$file['cs'] = ($baseID == 'drvsigon' || $baseID == 'imp_daily' || $baseID == 'imp_headers' || $baseID == 'rpt_allocsheet' || $baseID == 'shift_setter' || $baseID == 'spares' || $baseID == 'mechanics' || $baseID == 'rpt_allocation' || $baseID == 'newshifts' || $baseID == 'profile_4') ? 'active' : '';
			$file['cl'] = ($baseID == 'drvsigon' || $baseID == 'imp_daily' || $baseID == 'imp_headers' || $baseID == 'rpt_allocsheet' || $baseID == 'shift_setter' || $baseID == 'spares' || $baseID == 'mechanics' || $baseID == 'rpt_allocation' || $baseID == 'newshifts' || $baseID == 'profile_4') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 12)
		{
			$file['cs'] = ($baseID == 'hiz' || $baseID == 'sir') ? 'active' : '';
			$file['cl'] = ($baseID == 'hiz' || $baseID == 'sir') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		
		else if($caseID == 13)
		{
			$file['cs'] = ($baseID == 'rpt_emp' || $baseID == 'rpt_sicklv' || $baseID == 'rpt_downloads' || $baseID == 'rpt_userlogs') ? 'active' : '';
			$file['cl'] = ($baseID == 'rpt_emp' || $baseID == 'rpt_sicklv' || $baseID == 'rpt_downloads' || $baseID == 'rpt_userlogs') ? 'style="margin-left: 9px; color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 14)
		{
			$file['cs'] = ($baseID == 'rpt_accident' || $baseID == 'rpt_cmline' || $baseID == 'rpt_incident' || $baseID == 'rpt_infrgs' || $baseID == 'rpt_inspc' || $baseID == 'rpt_mngcmn') ? 'active' : '';
			$file['cl'] = ($baseID == 'rpt_accident' || $baseID == 'rpt_cmline' || $baseID == 'rpt_incident' || $baseID == 'rpt_infrgs' || $baseID == 'rpt_inspc' || $baseID == 'rpt_mngcmn') ? 'style="margin-left: 9px; color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 15)
		{
			$file['cs'] = ($baseID == 'rpt_signons' || $baseID == 'rpt_prior_alloc' || $baseID == 'rpt_allocsheet' || $baseID == 'profile_5' || $baseID == 'profile_6') ? 'active' : '';
			$file['cl'] = ($baseID == 'rpt_signons' || $baseID == 'rpt_prior_alloc' || $baseID == 'rpt_allocsheet' || $baseID == 'profile_5' || $baseID == 'profile_6') ? 'style="margin-left: 9px; color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 16)
		{
			$file['cs'] = ($baseID == 'rpt_headers') ? 'class="active"' : '';
			$file['cl'] = ($baseID == 'rpt_headers') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 17)
		{
			$file['cs'] = ($baseID == 'rpt_stfare') ? 'class="active"' : '';
			$file['cl'] = ($baseID == 'rpt_stfare') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		else if($caseID == 18)
		{
			$file['cs'] = ($baseID == 'rpt_hiz' || $baseID == 'rpt_sir') ? 'active' : '';
			$file['cl'] = ($baseID == 'rpt_hiz' || $baseID == 'rpt_sir') ? 'style="color:#23AD4B; font-weight:bold;"' : '';
		}
		
		return $file;
    }
    
    public function employee_transfer_logs($systemID)
    {
		$file = '';
		if(!empty($systemID) && !empty($systemID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM employee WHERE systemID = ".$systemID." Order By ID ASC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
			$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
			if(is_array($this->rows) && count($this->rows) > 0)
			{
				$file .= '<table id="dataTable" class="table table-bordered ">';				
				$file .= '<thead><tr>';
				$file .= '<th '.$Set.' colspan="5">Employee Company Transfer History !...</th>';
				$file .= '</tr></thead>'; 

				$file .= '<thead><tr>';
				$file .= '<th '.$Set.'>Sr. No.</th>';
				$file .= '<th '.$Set.'>Transfer Date</th>';
				$file .= '<th '.$Set.'>Employee Code</th>';
				$file .= '<th '.$Set.'>Employee Name</th>';
				$file .= '<th '.$Set.'>Transfer Company</th>';
				$file .= '</tr></thead>'; 
				$srID = 1;	$colorID = '';
				foreach($this->rows as $row)			
				{
					$colorID = ($row['refDT'] == '0000-00-00' ? 'style="color:green;"' : 'style="color:red;"');
					$arrayCM = ($row['companyID'] > 0 ? $this->select('company',array("*"), " WHERE ID = ".$row['companyID']." ") : '');

					$file .= '<tr>'; 
							$file .= '<td align="center">'.$srID++.'</td>';  
							$file .= '<td align="center"><b '.$colorID.'>'.($row['refDT'] == '0000-00-00' ? 'CURRENT-EMPLOYEE' : $this->VdateFormat($row['refDT'])).'</b></td>';
							$file .= '<td align="center">'.$row['code'].'</td>';
							$file .= '<td align="center">'.$row['fname'].' '.$row['lname'].'</td>';
							$file .= '<td align="center">'.$arrayCM[0]['title'].'</td>';
					$file .= '</tr>';
				} 
			}
			else	{$file .= '<div class="col-xs-6"><label>No User Logs is Available</label></div>';}
		}

		return $file;
    }
    
    public function uslogs_info($frmID,$vouID)
    {
		$file = '';
		if(!empty($frmID) && !empty($vouID))
		{
			$Qry = $this->DB->prepare("SELECT * FROM uslogs WHERE frmID = ".$frmID." AND vouID = ".$vouID." Order By dateID,timeID DESC ");
			$Qry->execute();
			$this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
			$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
			if(is_array($this->rows) && count($this->rows) > 0)
			{
				$file .= '<table id="dataTable" class="table table-bordered ">';				
				$file .= '<thead><tr>';
				$file .= '<th '.$Set.' colspan="5">Users Log Infos</th>';
				$file .= '</tr></thead>'; 

				$file .= '<thead><tr>';
				$file .= '<th '.$Set.'>Sr. No.</th>';
				$file .= '<th '.$Set.'>Action</th>';
				$file .= '<th '.$Set.'>User Name</th>';
				$file .= '<th '.$Set.'>Action Date</th>';
				$file .= '<th '.$Set.'>Action Time</th>';
				$file .= '</tr></thead>'; 

				$srID = 1;
				foreach($this->rows as $row)			
				{ 
					$arrUS = ($row['userID'] > 0 ? $this->select('users',array("*"), " WHERE ID = ".$row['userID']." ") : '');

					$file .= '<tr>'; 
						$file .= '<td align="center">'.$srID++.'</td>';  
						$file .= '<td align="center">'.(($row['actionID'] == 'A' ? 'NEW ENTRY' :($row['actionID'] == 'E' ? 'EDIT ENTRY' :($row['actionID'] == 'D' ? 'DELETE ENTRY' : '')))).'</td>';
						$file .= '<td align="center">'.$arrUS[0]['username'].'</td>';  
						$file .= '<td align="center">'.$this->VdateFormat($row['dateID']).'</td>';  
						$file .= '<td align="center">'.$row['timeID'].'</td>';  
					$file .= '</tr>';
				} 
			}
			else	{$file .= '<div class="col-xs-6"><label>No User Logs is Available</label></div>';}
		}		
		return $file;
    }
	
	public function Generate_BuilderReport($filters,$frmID)
    {
        $_SENDER = $filters;	//echo '<pre>'; echo print_r($filters);
        $return  = "";
        $dateSTR = "";		
		
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');
		
		if(is_array($filters) && count($filters) > 0)
		{
			if($frmID == 2)					{$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}
			else if($frmID == 4)			{$return .= $this->Create_Reports_Date($filters,'complaint.serDT');}
			else if($frmID == 6)			{$return .= $this->Create_Reports_Date($filters,'accident_regis.dateID');}
			else if($frmID == 7)			{$return .= $this->Create_Reports_Date($filters,'infrgs.dateID');}
			else if($frmID == 8)			{$return .= $this->Create_Reports_Date($filters,'inspc.dateID');}
			else if($frmID == 9)			{$return .= $this->Create_Reports_Date($filters,'mng_cmn.dateID');}
			else if($frmID == 5)			{$return .= $this->Create_Reports_Date($filters,'incident_regis.dateID');}
			else if($frmID == 10)			{$return .= $this->Create_Reports_Date($filters,'hiz_regis.dateID');}
			else if($frmID == 11)			{$return .= $this->Create_Reports_Date($filters,'sir_regis.issuetoDT');}
			else if($frmID == 12)			{$return .= $this->Create_Reports_Date($filters,'stfare_regis.dateID');}
		}
		
        if(!empty($filters['rtpyeID']))
        {
            $Qry = $this->DB->prepare("SELECT * FROM rbuilder WHERE frmID In(".$frmID.") AND ID In(".implode(",",$filters['rpt_fieldID']).") Order By srID ASC ");
            $Qry->execute();
            $this->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $countID = count($this->Hrows);

            if($countID > 0)
            {
				$file .= '<div class="row">';		
				$file .= '<div class="col-xs-12" style="overflow-y: scroll; overflow-x: scroll; width: 99%; margin-left: 10px;">';
                $file .= '<table id="dataTables" class="table table-bordered table-striped">';
				
				$file .= '<thead><tr>';
				$file .= '<th style="background:#23AD4B; color:white; text-align:center;" colspan="'.$countID.'">'.($frmID == 1 ? 'Employee' :($frmID == 2 ? 'Personal Leave' :($frmID == 4 ? 'Comment Line' :($frmID == 6 ? 'Accident' :($frmID == 7 ? 'Infringement' :($frmID == 8 ? 'Inspection' :($frmID == 9 ? 'Manager Comments' :($frmID == 10 ? 'Hazard' :($frmID == 11 ? 'Sir' :($frmID == 12 ? 'ST Fare' : '')))))))))).' Report '.$dateSTR.'</th>';
				$file .= '</tr></thead>';
				
                $file .= '<thead><tr>';
				
                $fieldNM = '';  $formNM = '';   $joinTB = '';   $joinFN = '';   $algnFL = '';				
                $headID = 1;
                foreach($this->Hrows as $Hrows)
                {
                    $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
                    $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';
                    $algnFL  .= $Hrows['alignFL'].', ';
                    
					$file .= '<th style="background:#23AD4B; color:white; text-align:center;">'.$Hrows['filedCP'].'</th>';
							
                    $formNM = $Hrows['formNM'];
                    $headID++;
                }				
                $file .= '</tr></thead>';

                $flID = explode(",",$fieldNM);      $ftID = explode(",",$fieldTY);
                $jtID = explode(",",$joinTB);       $jfID = explode(",",$joinFN);
                $agID = explode(",",$algnFL);
				
                $Qry_D = $this->DB->prepare($this->Report_QueryBuilder($frmID,$filters['rpt_fieldID'],($filters['filterID'] <> '' ? $filters['filterID'] : $_SESSION[$this->website]['compID']),$return));
                $Qry_D->execute();
                $this->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);

                if(is_array($this->Drows) && count($this->Drows) > 0)
                {
					//echo '<br /> : '.count($this->Drows);
                    foreach($this->Drows as $Drow)
                    { 
						//echo '<pre>';	echo print_r($ftID);
						//echo '<pre>';	echo print_r($Drow);	exit;
						
						if($frmID == 10)
						{
							$arrFRM = ($Drow['ID'] > 0 ? $this->select('hiz_regis',array("*"), " WHERE ID = ".$Drow['ID']." ") : '');
						}
						
                        $file .= '<tr>';
                        $fnameID = '';  $tableJT = '';  $testID = '';   $alignID = '';                        
                        for($srID = 1; $srID <= $countID; $srID++)
                        {
                            $alignID = (trim($agID[$srID - 1]) == 1 ? 'align="left"' :(trim($agID[$srID - 1]) == 2 ? 'align="right"' :(trim($agID[$srID - 1]) == 3 ? 'align="center"' : '')));
                            $alignID .= 'style="font-weight:bold; background:white;"';
							
							$fnameID = trim($flID[$srID - 1]);
							
							if(trim($ftID[$srID - 1]) == 2) 
							{
								if($fnameID == 'typeID' || $fnameID == 'tickID_1' || $fnameID == 'tickID_2' || $fnameID == 'plcntID' || $fnameID == 'substanID' || $fnameID == 'statusID')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' :  '')).'</td>';
								}
								else if($fnameID == 'resultsINV' && $Drow[trim($flID[$srID - 1])] == 8000)
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">Other</td>';
								}
								else if($fnameID == 'faultID')
								{
									$arrCP = ($Drow['ID'] > 0 ? $this->select('complaint',array("*"), " WHERE ID = ".$Drow['ID']." ") : '');
									
									if($arrCP[0]['substanID'] == 2)
									{
										$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 4 ? 'Not Applicable' :($Drow[$fnameID] == 5 ? 'Not At Fault' : '')).'</td>';
									}
									else
									{
										$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'At Fault - Driver' :($Drow[$fnameID] == 2 ? 'At Fault - Engineering' :($Drow[$fnameID] == 3 ? 'At Fault - Operations' :($Drow[$fnameID] == 4 ? 'Not At Fault' : '')))).'</td>';
									}
								}
								else if($fnameID == 'optID_u1' || $fnameID == 'optID_m1')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Only remotely possible' :($Drow[$fnameID] == 3 ? 'Unusual but possible' :($Drow[$fnameID] == 6 ? 'Quite possible' :($Drow[$fnameID] == 10 ? 'May well be expected' : '')))).'</td>';
								}
								else if($fnameID == 'optID_u3' || $fnameID == 'optID_m3')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Few per day' :($Drow[$fnameID] == 3 ? 'Weekly' :($Drow[$fnameID] == 6 ? 'Daily' :($Drow[$fnameID] == 10 ? 'Continuous' : '')))).'</td>';
								}
								else if($fnameID == 'optID_u4' || $fnameID == 'optID_m4')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Safety' :($Drow[$fnameID] == 2 ? 'Environmental' : '')).'</td>';
								}
								else if($fnameID == 'optID_u5' || $fnameID == 'optID_m5')
								{
									if($arrFRM[0]['optID_u4'] == 1)
									{
										$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'First Aid Treatment (on site) or Work Injury or Disease Report' :($Drow[$fnameID] == 3 ? 'Medical Treated Injury or Disease' :($Drow[$fnameID] == 6 ? 'Serious Injury/Loss Time Injury or Disease' :($Drow[$fnameID] == 10 ? 'Fatality or Permanent Disability' : '')))).'</td>';
									}
									else if($arrFRM[0]['optID_u4'] == 2)
									{
										$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'No Environmental Harm' :($Drow[$fnameID] == 3 ? 'Minimal Environmental Harm' :($Drow[$fnameID] == 6 ? 'Moderate Environmental Impact' :($Drow[$fnameID] == 10 ? 'Serious Environmental Harm' : '')))).'</td>';
									}
								}
								else if($fnameID == 'optID_u6' || $fnameID == 'optID_m6')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Very High' :($Drow[$fnameID] == 2 ? 'High' :($Drow[$fnameID] == 3 ? 'MEDIUM' :($Drow[$fnameID] == 4 ? 'Low' :($Drow[$fnameID] == 5 ? 'Very Low' : ''))))).'</td>';
								}
								else if($fnameID == 'progressID')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Complete' :($Drow[$fnameID] == 2 ? 'Pending' :($Drow[$fnameID] == 3 ? 'Written Off' : ''))).'</td>';
								}
								else if($fnameID == 'optID_2' || $fnameID == 'optID_3')
								{
									$file .= '<td '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'No' :($Drow[$fnameID] == 2 ? 'Swan' :($Drow[$fnameID] == 3 ? 'Police' :($Drow[$fnameID] == 4 ? 'Both' :  '')))).'</td>';
								}	 
								else if($fnameID == 'casualID')
								{
									$file .= '<td '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Full Time' :($Drow[$fnameID] == 2 ? 'Part Time' :($Drow[$fnameID] == 3 ? 'Casual' : ''))).'</td>';
								}
								else if($fnameID == 'disciplineID')
								{
									$file .= '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' : '')).'</td>';
								}
								else if($fnameID == 'companyID' && ($frmID == 1 || $frmID == 10 || $frmID == 11))
								{
									$tableJT = trim($jtID[$srID - 1]);
									$tableJF = trim($jfID[$srID - 1]); 
									
									if($tableJT <> '')
									{
										$arrTBL  = $Drow['ID'] > 0 ? $this->select(($frmID == 1 ? 'employee' :($frmID == 10 ? 'hiz_regis' :($frmID == 11 ? 'sir_regis' : ''))),array("scompanyID"), " WHERE ID = ".$Drow['ID']." ") : '';
										$arrSBD  = $arrTBL[0]['scompanyID'] > 0 ? $this->select('company_dtls',array("title"), " WHERE ID = ".$arrTBL[0]['scompanyID']." ") : '';
										
										$MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
										$file .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).' '.($arrSBD[0]['title'] <> '' ? '('.$arrSBD[0]['title'].')' : '').'</td>';
									}
									else
									{
										$file .= '<td '.$alignID.'>'.$fnameID.'</td>';
									} 
								}
								else
								{								
									$tableJT = trim($jtID[$srID - 1]);
									$tableJF = trim($jfID[$srID - 1]);
									
									if($tableJT <> '')
									{
										$MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
										$file .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
									}
									else
									{
										$file .= '<td '.$alignID.'>'.$fnameID.'</td>';
									}
								}
							}

							else if(trim($ftID[$srID - 1]) == 3) 
							{
								$file .= '<td align="center" style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' : '').'</td>';
							}
							
							else if(trim($ftID[$srID - 1]) == 4) 
							{
								$file .= '<td '.$alignID.'>'.$this->VISUAL_dateID(($Drow[trim($flID[$srID - 1])])).'</td>';
							}

							else if(trim($ftID[$srID - 1]) == 5) 
							{
								$file .= '<td '.$alignID.'>'.(strlen($Drow[trim($flID[$srID - 1])]) > 0 ? $Drow[trim($flID[$srID - 1])] : '').'</td>';
							}
							else if(trim($ftID[$srID - 1]) == 6) 
							{
								$file .= '<td align="center" style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' : '')).'</td>';
							}
							else if(trim($ftID[$srID - 1]) == 7) 
							{
								$file .= '<td align="center" style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' : 'No').'</td>';
							}
							else if(trim($ftID[$srID - 1]) == 8) 
							{
								$file .= '<td align="center" style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' :($Drow[$fnameID] == 3 ? 'NA' : ''))).'</td>';
							}
							else                                
							{
								$fnameID = trim($flID[$srID - 1]);

								if(trim($ftID[$srID - 1]) == 1)
								{
									if($fnameID == 'code')
									{
										$file .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
									}
									else
									{
										$file .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';                                                    
									}
								} 
								else
								{
									$file .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';	
								}
							} 
                        }
						
						$file .= '</tr>';
                    } 
                }
                $file .= '</table>'; 
				$file .= '</div>';
				$file .= '</div>';
            }
        }
		 
		echo $file;
    }
}
?>