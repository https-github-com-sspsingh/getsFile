<?PHP
    session_start();
    include	'includes.php'; 

	$filters = array();
	foreach($_GET as $key=>$value)  {$filters[$key] = $value;}
		
    $fdateID	 =	isset($_GET['fdateID'])     ?   $_GET['fdateID']	:   '';
    $tdateID	 =	isset($_GET['tdateID'])     ?	$_GET['tdateID']	:   '';
	$typeID    	 =	isset($_GET['typeID'])      ?	$_GET['typeID']		:   '';
	$dashID    	 =	isset($_GET['dashID'])      ?	$_GET['dashID']		:   '';
	$auditID	 =	isset($_GET['auditID'])     ?	$_GET['auditID']	:   ''; 		
    $searchbyID	 =	isset($_GET['searchbyID'])  ?	$_GET['searchbyID'] :   '';    
    $fromID	 	 =	isset($_GET['fromID'])	 	?	$_GET['fromID']		:   '';
    $sheetID     =	isset($_GET['i'])			?	$_GET['i']			:   '';
	$sidebarID	 =	isset($_GET['sidebarID'])   ?	$_GET['sidebarID']	:   '';
	$daysID      =	isset($_GET['daysID'])		?	$_GET['daysID']		:   '';
	$sidebarID	 =  $sidebarID > 0 				?   $sidebarID 			:   0;	
	
    $SHEET_TITLE = $_GET['s'].($fromID <> '' ? '-('.$fromID.')' : '');

    header("Content-Type:application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=".$SHEET_TITLE.".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
	
    if($_GET['s'] == 'SIGNON_LATE_SHEET' && $typeID == 1)
    { 
        $EIndex->EXPORT_SIGNON_LATE_1_SHEET($filters);
    }	
	
    if($_GET['s'] == 'SIGNON_LATE_SHEET' && $typeID == 2 && $sidebarID == 0)
    { 
        $EIndex->EXPORT_SIGNON_LATE_2_SHEET($filters);
    }	
	
	if($_GET['s'] == 'SIGNON_LATE_SHEET' && $typeID == 2 && $sidebarID == 1)
    {  
        $EIndex->EXPORT_SIGNON_LATE_02_SHEET($filters);
    }	
	
    if($_GET['s'] == 'SIGNON_LATE_SHEET' && $typeID == 3)
    {  
		$EIndex->EXPORT_SIGNON_LATE_3_SHEET($filters);
    }	
	
	if($_GET['s'] == 'SIGNON_LATE_SHEET' && $typeID == 4)
    { 
		$EIndex->EXPORT_SIGNON_LATE_4_SHEET($filters);
    }	
	
    if($_GET['s'] == 'REPORT_EMPLOYEE')
    {
        $filters['frmID'] = 1;
		
		if($_GET['rtpyeID'] == 5)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_EMPLOYEE($filters);}
    }

    if($_GET['s'] == 'REPORT_SICKLEAVE')
    {
		$filters['frmID'] = 2;
		
		if($_GET['rtpyeID'] == 5)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_SICKLEAVE($filters);}
    }

    if($_GET['s'] == 'REPORT_ACCIDENT')
    {
		$filters['frmID'] = 6;
		
		if($_GET['rtpyeID'] == 7)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_ACCIDENT($filters);} 
    }

    if($_GET['s'] == 'REPORT_INCIDENTS')
    { 
		if($_GET['rtpyeID'] == 11)
		{
			$filters['frmID'] = 5;
			$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);
		}
		else	{$EIndex->EXPORT_REPORT_INCIDENTS($filters);}  
    }
	
	if($_GET['s'] == 'REPORT_HIZ')
    { 
		if($_GET['rtpyeID'] == 2)
		{
			$filters['frmID'] = 10;
			$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);
		}
		else	{$EIndex->EXPORT_REPORT_SIR($filters);}  
    }
	
    if($_GET['s'] == 'REPORT_SIR')
    { 
		if($_GET['rtpyeID'] == 2)
		{
			$filters['frmID'] = 11;
			$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);
		}
		else	{$EIndex->EXPORT_REPORT_SIR($filters);}  
    }
	
    if($_GET['s'] == 'REPORT_STFARE')
    { 
		if($_GET['rtpyeID'] == 2)
		{
			$filters['frmID'] = 12;
			$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);
		}
		else	{$EIndex->EXPORT_REPORT_SIR($filters);}  
    }

    if($_GET['s'] == 'REPORT_INSPECTION')
    {
		$filters['frmID'] = 8;
		
		if($_GET['rtpyeID'] == 4)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_INSPECTION($filters);}  
    }

    if($_GET['s'] == 'REPORT_INFRINGEMENT')
    {
		$filters['frmID'] = 7;
		
		if($_GET['rtpyeID'] == 4)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_INFRINGEMENT($filters);}   
    }
    
    if($_GET['s'] == 'REPORT_COMMENTLINE')
    {
		$filters['frmID'] = 4;
		
		if($_GET['rtpyeID'] == 10)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_COMMENTLINE($filters);}  
    }
    
    if($_GET['s'] == 'REPORT_MANAGER_COMMENTS')
    {
		$filters['frmID'] = 9;
		
		if($_GET['rtpyeID'] == 4)
				{$EIndex->EXPORT_CUSTOMIZED_FIELDS($filters);}
		else	{$EIndex->EXPORT_REPORT_MANAGER_COMMENTS($filters);}  
    }
    
    if($_GET['s'] == 'ALLOCATION_SHEET')
    {
        $EIndex->EXPORT_ALLOCATION_SHEET($fromID);
    }

    if($_GET['s'] == 'DAILY_SHEET')
    {
        $EIndex->EXPORT_DAILY_SHEET($fromID);
    }

    if($_GET['s'] == 'SHIFT_SETTER_SHEET')
    {
        $EIndex->EXPORT_SETTER_SHEET($fromID,$sheetID);
    }

    if($_GET['s'] == 'PRINT_HEADER_SHEET')
    {
        $EIndex->EXPORT_PRINT_HEADER_SHEET($fromID);
    }

    if($_GET['s'] == 'DAILY_SHEET_GENERATOR')
    {
        $EIndex->EXPORT_DAILY_SHEET_GENERATOR($fromID);
    }

    if($_GET['s'] == 'SIGNONINFO_DETAIL_SHEET')
    {
        $EIndex->EXPORT_SIGNONINFO_DETAIL_SHEET($fromID);
    }
    
    if($_GET['s'] == 'SIGNON_DETAIL_SHEET')
    {
        $EIndex->EXPORT_SIGNON_DETAIL_SHEET($fromID);
    }
?>