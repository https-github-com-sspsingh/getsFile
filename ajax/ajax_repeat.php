<?PHP
	include_once '../includes.php';
    
	extract($_POST);
	
	
	$crtID .= " AND companyID = ".$_SESSION[$login->website]['compID'];
	$crtID .= ($arrID[0]['transID'] > 0 ? " AND ID <> ".$arrID[0]['transID'] : "");
	
	if($request == 'Check_EmployeeCode')
	{		
		$countID = 0;		
		if($arrID[0]['ecodeID'] <> '')
		{
			$crtID .= " AND code = '".$arrID[0]['ecodeID']."' ";			
			
			$countID = $login->count_rows('employee', " WHERE status = 1 AND ID > 0 ".$crtID." ");			
			$data['textBX'] = 'EcodeID';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_SickLeave')
	{
		$countID = 0;		
		if($arrID[0]['empID'] > 0 && $arrID[0]['lvtypeID'] > 0 && $arrID[0]['sldateID'] <> '')
		{			
			$crtID .= " AND empID = ".$arrID[0]['empID'];
			$crtID .= " AND lvtypeID = ".$arrID[0]['lvtypeID'];
			$crtID .= " AND sldateID = '".$login->dateFormat($arrID[0]['sldateID'])."' ";
			
			$countID = $login->count_rows('sicklv', " WHERE ID > 0 ".$crtID." ");			
			$data['textBX'] = 'sldateID,dateID,ecodeID,dayNM,dayID';
			$data['dropBX'] = 'empID,lvtypeID';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Inspection')
	{
		$countID = 0;		
		if($arrID[0]['rptno'] <> '')
		{			
			$crtID .= " AND rptno = '".$arrID[0]['rptno']."' ";
			
			$countID = $login->count_rows('inspc', " WHERE ID > 0 ".$crtID." ");			
			$data['textBX'] = 'rptno';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Infringment')
	{
		$countID = 0;		
		if($arrID[0]['infrefno'] <> '')
		{			
			$crtID .= " AND refno = '".$arrID[0]['infrefno']."' ";
			
			$countID = $login->count_rows('infrgs', " WHERE ID > 0 ".$crtID." ");			
			$data['textBX'] = 'infrefno';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Accident')
	{
		$countID = 0;		
		if($arrID[0]['accrefno'] <> '')
		{			
			$crtID .= " AND refno = '".$arrID[0]['accrefno']."' ";
			
			$countID = $login->count_rows('accident_regis', " WHERE ID > 0 ".$crtID." ");			
			$data['textBX'] = 'accrefno';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Complaint')
	{
		$countID = 0;		
		if($arrID[0]['cmprefno'] <> '')
		{			
			$crtID .= " AND refno = '".$arrID[0]['cmprefno']."' ";
			
			$countID = $login->count_rows('complaint', " WHERE ID > 0 ".$crtID." ");			
			$data['textBX'] = 'cmprefno';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	if($request == 'Check_Incident')
	{
		$countID = 0;		
		if(($arrID[0]['increfno'] <> '') || ($arrID[0]['inccmrno'] <> '') || ($arrID[0]['plrefnoID'] <> '') || ($arrID[0]['ptarefNO'] <> ''))
		{	
			$countID += ($arrID[0]['increfno'] <> ''  ? $login->count_rows('incident_regis', " WHERE ID > 0 AND refno = '".$arrID[0]['increfno']."' ".$crtID." ") : '');
			$countID += ($arrID[0]['inccmrno'] <> ''  ? $login->count_rows('incident_regis', " WHERE ID > 0 AND cmrno = '".$arrID[0]['inccmrno']."' ".$crtID." ") : '');
			$countID += ($arrID[0]['plrefnoID'] <> '' ? $login->count_rows('incident_regis', " WHERE ID > 0 AND plrefno = '".$arrID[0]['plrefnoID']."' ".$crtID." ") : '');
			$countID += ($arrID[0]['ptarefNO'] <> ''  ? $login->count_rows('incident_regis', " WHERE ID > 0 AND pta_refNO = '".$arrID[0]['ptarefNO']."' ".$crtID." ") : '');
			
			$data['textBX'] = 'increfno,inccmrno,plrefnoID,ptarefNO';
		}
		
		$data['result'] = ($countID > 0 ? $countID : 0);
	}
	
	echo json_encode($data);
?>