<?PHP
    include_once '../includes.php';
    
    $arr = array();
    
    if($_POST['request'] == 'API_RESPONSE')
    {
        //echo '<pre>'; echo print_r($_POST); exit;
		
        extract($_POST);
        
        $fileID = ''; 
        
        $strID = "";
        if($fdateID <> '' && $tdateID <> '')    {$strID .= " AND DATE(incident_regis.dateID) BETWEEN '".$login->dateFormat($fdateID)."' AND '".$login->dateFormat($tdateID)."' ";}
        else                                    {$strID .= " AND DATE(incident_regis.dateID) BETWEEN '".date('Y-m-d', strtotime('first day of last month'))."' AND '".date('Y-m-d', strtotime('last day of last month'))."' ";}
        
		
        /*** API - CATEGORY ***/
        $typeTX = '';
        $typeTX = ($apiID == 1 ? 'INCIDENT' :($apiID == 2 ? 'OFFENCE' : ''));

        if(is_array($companyID) && count($companyID) > 0)
        {
            $srID = 1; $passID = '';
            foreach($companyID as $company_ID)   {$passID .= $srID == 1 ? $company_ID : ','.$company_ID;   $srID++;}
        }
        
        $compID = "";
        $compID .= $passID <> '' ? " AND incident_regis.companyID In(".$passID.") "  : "";
		
        $SQL = "Select incident_regis.ID, incident_regis.refno, incident_regis.rpdateID, incident_regis.dateID,  incident_regis.timeID, incident_regis.companyID, api_senders_logs.statusTX As API_Status, API_Data.recID As apiID From incident_regis Left Join (Select Max(api_senders_logs.recID) As recID, api_senders_logs.refID  From api_senders_logs Where api_senders_logs.typeID = '".$typeTX."' Group By api_senders_logs.typeID, api_senders_logs.refID) API_Data On API_Data.refID = incident_regis.ID Left Join  api_senders_logs On api_senders_logs.recID = API_Data.recID
        Where incident_regis.dateID <> '' And incident_regis.sincID = 1 AND  incident_regis.companyID > 0 ".$compID." ".$strID." Order By incident_regis.rpdateID ASC ";
        $Qry = $CIndex->DB->prepare($SQL);
        if($Qry->execute())
        {
            $CIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $fileID .= '<table id="dataTable" class="table table-bordered table-striped">';				
            $fileID .= '<thead><tr>';
            $fileID .= '<th style="background:#00A65A; color:white;">Depot</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Ref No</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Date</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">API Response</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">Sent On</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Sent by User</th>';
            $fileID .= '</tr></thead>';
		  
            $responseTEXT = '';
            $responseVLUE = '';
          foreach($CIndex->rows as $row)			
          {
			  $responseTEXT = strip_tags(trim(strip_tags($row['API_Status'])), '<br>');
			  $responseVLUE = substr($responseTEXT,0,5);
			  
			  $arrREQ  = ($row['apiID'] > 0    		  ? $login->select('api_senders_logs',array("*"), " WHERE recID = ".$row['apiID']." ")   : '');
			  $arrRUS  = ($arrREQ[0]['userID'] > 0    ? $login->select('users',array("*"), " WHERE ID = ".$arrREQ[0]['userID']." ")   : '');
			  
			 /* if($arrREQ[0]['recID'] > 0)
			  {*/
				  $arrCM  = ($row['companyID'] > 0    ? $login->select('company',array("*"), " WHERE ID = ".$row['companyID']." ")   : '');
				  
				  $fileID .= '<tr>';
					$fileID .= '<td>'.trim($arrCM[0]['title']).'</td>';
					$fileID .= '<td style="color:white; font-weight:bold; background:'.(trim($responseVLUE) == 'Error' ? 'red' 
												  :(trim($responseVLUE) == 'Authe' ? 'green' : 'blue')).'">'.$row['refno'].'</td>';
												  
					$fileID .= '<td>'.($login->VdateFormat($row['dateID'])).'</td>';
					
					$fileID .= '<td>'.$responseTEXT.'</td>';
					$fileID .= '<td>'.$login->VdateFormat($arrREQ[0]['dateID']).' - '.$arrREQ[0]['timeID'].'</td>';
					$fileID .= '<td>'.trim($arrRUS[0]['username']).'</td>';
				  $fileID .= '</tr>';
			  //}
          }
          $fileID .= '</table>';			
        }  
        
        $arr['records'] = $fileID;
    }
	
	if($_POST['request'] == 'GET_INTERVIEWEDBY')
	{
		$dataF = '';
		$dataF .= '';
		$dataF .= '<option value="0" selected="selected" disabled="disabled">-- Select Interviewed By --</option>';
		$dataF .= '<option value="1001">No Interview Required</option>';
		$dataF .= $AIndex->ReportingBundels(0,"AND employee.desigID In (209,208)");
		
		$arr['records'] = $dataF;
	}
	
    if($_POST['request'] == 'API_REQUEST' && $_POST['typeID'] == 1 && $_POST['statusID'] == 0 && count($_POST['companyFL'] > 0))
    {
        extract($_POST);
        
        $fileID = ''; 
        
        $strID = "";
        if($fdateID <> '' && $tdateID <> '')    {$strID .= " AND DATE(incident_regis.dateID) BETWEEN '".$login->dateFormat($fdateID)."' AND '".$login->dateFormat($tdateID)."' ";}
        else                                    {$strID .= " AND DATE(incident_regis.dateID) BETWEEN '".date('Y-m-d', strtotime('first day of last month'))."' AND '".date('Y-m-d', strtotime('last day of last month'))."' ";}
        
        if(is_array($companyFL) && count($companyFL) > 0)
        {
            $srID = 1; $passID = '';
            foreach($companyFL as $companyID)   {$passID .= $srID == 1 ? $companyID : ','.$companyID;   $srID++;}
        }
        
		$compID = "";
        $compID .= $passID <> '' ? " AND incident_regis.companyID In(".$passID.") "  : "";
		
		$SQL = "Select incident_regis.*, API_Data.apiID  From incident_regis Left Join employee On employee.ID = incident_regis.driverID Inner Join (Select api_senders_logs.refID, Max(api_senders_logs.recID) As apiID, Date(api_senders_logs.dateID) As dateID From  api_senders_logs Where api_senders_logs.typeID = 'INCIDENT' And
		api_senders_logs.dateID <> '' ".$strID." Group BY  api_senders_logs.refID, Date(api_senders_logs.dateID)) API_Data On API_Data.refID = incident_regis.ID Where incident_regis.ID > 0 And incident_regis.sincID = 1 ".$compID." Order By incident_regis.refno DESC ";
		
        $Qry = $CIndex->DB->prepare($SQL);
        if($Qry->execute())
        {
            $CIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $fileID .= '<table id="dataTable" class="table table-bordered table-striped">';				
            $fileID .= '<thead><tr>';
            $fileID .= '<th style="background:#00A65A; color:white;">TransPerthRef</th>';
			$fileID .= '<th style="background:#00A65A; color:white;">Depotid</th>';
			$fileID .= '<th style="background:#00A65A; color:white;">Location</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">CrossStreet</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">SuburbName</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">ReportDate</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">IncidentDate</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">IncidentTime</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">BusRouteNo</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">VehicleNo</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">Description</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">ShiftNo</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceNotified</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceAttended</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceVehicle</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceCadno</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceIRNo</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceName</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">FireBrigade</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Ambulance</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">WeaponsId</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">DamageInjury</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">VideoAvailable</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">TTOAttended</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">TTONotified</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">SForceReportNo</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">DamageValue</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceTimeCalled</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceTimeArrived</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">TPCalled</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">TPArrived</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">TPSecurityID</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">SecurityOfficerNames</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">PatrolType</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceCalled</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">PoliceArrived</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Statistical</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">StatisticalReason</th>';        
            $fileID .= '<th style="background:#00A65A; color:white;">TPSecurityTimeCalled</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">TPSecurityTimeArrived</th>';
            $fileID .= '<th style="background:#00A65A; color:white;">Duplicate</th>'; 
            $fileID .= '<th style="background:#00A65A; color:white;">Send On</th>';          
            $fileID .= '<th style="background:#00A65A; color:white;">Sent by User</th>';       
          $fileID .= '</tr></thead>';
          foreach($CIndex->rows as $row)			
          {
              $timeID = explode(":",str_replace(" ","",$row['timeID']));
              
			  $arrREQ  = ($row['apiID'] > 0    		  ? $login->select('api_senders_logs',array("*"), " WHERE recID = ".$row['apiID']." ")   : '');
			  $arrRUS  = ($arrREQ[0]['userID'] > 0    ? $login->select('users',array("*"), " WHERE ID = ".$arrREQ[0]['userID']." ")   : '');
			  
			  /*if($arrREQ[0]['recID'] > 0)
			  {*/
				  $FileAuthor = ($_SESSION[$login->website]['empNM'] <> '' ? $_SESSION[$login->website]['empNM'] : $_SESSION[$login->website]['fullNM']);			  
				  $DR_Array  = ($row['driverID'] > 0    ? $login->select('employee',array("*"), " WHERE ID = ".$row['driverID']." ")   : '');
				  $IN_Array  = ($row['inctypeID'] > 0   ? $login->select('master',array("*"), " WHERE ID = ".$row['inctypeID']." ")    : '');
				  $SB_Array  = ($row['suburb'] > 0      ? $login->select('suburbs',array("*"), " WHERE ID = ".$row['suburb']." ")  	  : '');
				  $PA_Array  = ($row['plcactionID'] > 0 ? $login->select('master',array("*"), " WHERE ID = ".$row['plcactionID']." ")  : '');				
				  $typeID_3  = ($row['companyID'] > 0   ? $login->select('api_mappings',array("*"), " WHERE dbaID = ".$row['companyID']." AND typeID = 3 ")  : '');
				  $typeID_4  = ($row['weaponsID'] > 0   ? $login->select('api_mappings',array("*"), " WHERE dbaID = ".$row['weaponsID']." AND typeID = 4 ")  : '');
				  $SubUrbID  = ($row['suburb'] > 0      ? ($SB_Array[0]['title']) : '');
				  
				  $fileID .= '<tr>';
					$fileID .= '<td>'.$row['refno'].'</td>';
					$fileID .= '<td>'.$typeID_3[0]['apiID'].'</td>';
					$fileID .= '<td>'.str_replace("&","-",$row['location']).'</td>';
					$fileID .= '<td>'.($row['crossst'] <> '' ? $row['crossst'] : null).'</td>';
					$fileID .= '<td>'.($SubUrbID <> '' ? $SubUrbID : '').'</td>';
					$fileID .= '<td>'.($login->VdateFormat($row['rpdateID'])).'</td>';
					$fileID .= '<td>'.($login->VdateFormat($row['dateID'])).'</td>';                
					$fileID .= '<td>'.($row['timeID'] <> '' ? trim($timeID[0].''.$timeID[1]) : null).'</td>';
					$fileID .= '<td>'.($row['routeID'] <> '' ? $row['routeID'] : null).'</td>';
					$fileID .= '<td>'.($row['busID'] <> '' ? $row['busID'] : null).'</td>';
					$fileID .= '<td>'.($row['description'] <> '' ? str_replace('|','',(str_replace('\/','',(str_replace('"','',$row['description']))))) : null).'</td>';
					$fileID .= '<td>'.$row['shiftID'].'</td>';
					$fileID .= '<td>'.(!empty($row['plrefID']) && ($row['plrefID'] >= 1) ? $row['plrefID'] : '0').'</td>';
					$fileID .= '<td>'.($row['attendedID_2'] >= 1 ? '1' : '0').'</td>';
					$fileID .= '<td>'.($row['plcvehicle'] <> '' ? $row['plcvehicle'] : null).'</td>';
					$fileID .= '<td>'.($row['plcadno'] <> '' ? $row['plcadno'] : null).'</td>';
					$fileID .= '<td></td>';
					$fileID .= '<td>'.($row['policename'] <> '' ? $row['policename'] : null).'</td>';
					$fileID .= '<td>'.($row['attendedID_8'] == 1 ? '1' : '0').'</td>';
					$fileID .= '<td>'.($row['attendedID_9'] == 1 ? '1' : '0').'</td>'; 
					$fileID .= '<td>'.$typeID_4[0]['apiID'].'</td>';
					$fileID .= '<td>'.($row['dmginjury'] <> '' ? $row['dmginjury'] : null).'</td>';
					$fileID .= '<td>'.($row['notifiedID_8'] == 1 ? '1' : '0').'</td>';
					$fileID .= '<td>'.($row['attendedID_7'] == 1 ? '1' : '0').'</td>';
					$fileID .= '<td>'.($row['notifiedID_7'] == 1 ? '1' : '0').'</td>';
					$fileID .= '<td></td>';
					$fileID .= '<td>'.(int)$row['dmvalue'].'</td>';				
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';				
					$fileID .= '<td>'.($row['cmrno'] <> '' ? $row['cmrno'] : null).'</td>';				
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					$fileID .= '<td></td>';
					
					$fileID .= '<td>'.$login->VdateFormat($arrREQ[0]['dateID']).' - '.$arrREQ[0]['timeID'].'</td>';
					$fileID .= '<td>'.trim($arrRUS[0]['username']).'</td>';
				  $fileID .= '</tr>';
			  //}
          }
          $fileID .= '</table>';			
        }  
        
        $arr['records'] = $fileID;
    }
     
    if($_POST['request'] == 'API_REQUEST' && $_POST['typeID'] == 2 && $_POST['statusID'] == 0 && count($_POST['companyFL'] > 0))
    {
        extract($_POST);
        
        $fileID = '';		
        /* DATE - SEARCHING */
        $strID = "";
        if($fdateID <> '' && $tdateID <> '')    {$strID .= " AND DATE(api_senders_logs.dateID) BETWEEN '".$login->dateFormat($fdateID)."' AND '".$login->dateFormat($tdateID)."' ";}
        else                                    {$strID .= " AND DATE(api_senders_logs.dateID) BETWEEN '".date('Y-m-d', strtotime('first day of last month'))."' AND '".date('Y-m-d', strtotime('last day of last month'))."' ";}
        
        if(is_array($companyFL) && count($companyFL) > 0)
        {
            $srID = 1; $passID = '';
            foreach($companyFL as $companyID)   {$passID .= $srID == 1 ? $companyID : ','.$companyID;   $srID++;}
        }
        
		$compID = "";
        $compID .= $passID <> '' ? " AND incident_regis.companyID In(".$passID.") "  : "";

		$SQL = "Select incident_regis.*, API_Data.apiID From incident_regis Left Join employee On employee.ID = incident_regis.driverID Inner Join (Select api_senders_logs.refID, Max(api_senders_logs.recID) As apiID, Date(api_senders_logs.dateID) As dateID From
		api_senders_logs Where api_senders_logs.typeID = 'OFFENCE' And api_senders_logs.dateID <> '' ".$strID." Group BY  api_senders_logs.refID, Date(api_senders_logs.dateID)) API_Data On API_Data.refID = incident_regis.ID Where incident_regis.ID > 0 And incident_regis.sincID = 1 ".$compID." Order By incident_regis.refno DESC ";
		
		$Qry = $CIndex->DB->prepare($SQL);
        if($Qry->execute())
        {
          $CIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
          $fileID .= '<table id="dataTable" class="table table-bordered table-striped">';				
          $fileID .= '<thead><tr>';		  
		  $fileID .= '<th style="background:#00A65A; color:white;">TransPerthRef</th>';	
          $fileID .= '<th style="background:#00A65A; color:white;">OffenceId</th>';    
          $fileID .= '<th style="background:#00A65A; color:white;">Send On</th>';
		  $fileID .= '<th style="background:#00A65A; color:white;">Sent by User</th>';
          $fileID .= '</tr></thead>';		  
          foreach($CIndex->rows as $row)			
          {
			  $arrREQ  = ($row['apiID'] > 0    		  ? $login->select('api_senders_logs',array("*"), " WHERE recID = ".$row['apiID']." ")   : '');
			  $arrRUS  = ($arrREQ[0]['userID'] > 0    ? $login->select('users',array("*"), " WHERE ID = ".$arrREQ[0]['userID']." ")   : '');
			  
			  if($arrREQ[0]['recID'] > 0)
			  {				  
				  $typeID_2  = ($row['offdtlsID'] > 0 ? $CIndex->select('api_mappings',array("*"), " WHERE dbaID = ".$row['offdtlsID']." AND typeID = 1 ")  : '');
				  $typeID_3  = ($row['companyID'] > 0 ? $CIndex->select('api_mappings',array("*"), " WHERE dbaID = ".$row['companyID']." AND typeID = 3 ")  : '');
				  
				  $fileID .= '<tr>';
					$fileID .= '<td align="center">'.$row['refno'].'</td>';
					$fileID .= '<td>'.((int)$typeID_2[0]['apiID']).'</td>';					
					$fileID .= '<td>'.$login->VdateFormat($arrREQ[0]['dateID']).' - '.$arrREQ[0]['timeID'].'</td>';
					$fileID .= '<td>'.trim($arrRUS[0]['username']).'</td>';					
				  $fileID .= '</tr>';
			  }
          }
          $fileID .= '</table>';			
        }		
        $arr['records'] = $fileID;
    }
	
    echo json_encode($arr);
?>