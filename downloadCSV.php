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
	$sidebarID	 =  $sidebarID > 0 				?   $sidebarID 			:   0;	
	
    $SHEET_TITLE = $_GET['s'];

    header("Content-Type:application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=".$SHEET_TITLE.".xls");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
	
	if($_GET['s'] == 'INCIDENT_REGISTER')
    {
        $LIndex->EXPORT_INCIDENT_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }

    if($_GET['s'] == 'EMPLOYEE_REGISTER')
    {
        $LIndex->EXPORT_EMPLOYEE_REGISTER($searchbyID,$auditID);
    }

    if($_GET['s'] == 'COMMENTLINE_REGISTER')
    {
        $LIndex->EXPORT_COMMENTLINE_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }

    if($_GET['s'] == 'HIZ_REGISTER')
    {
        $LIndex->EXPORT_HIZ_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }
	
    if($_GET['s'] == 'SIR_REGISTER')
    {
        $LIndex->EXPORT_SIR_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }
	
    if($_GET['s'] == 'STFARE_REGISTER')
    {
        $LIndex->EXPORT_STFARE_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }
	
    if($_GET['s'] == 'ACCIDENT_REGISTER')
    {
        $LIndex->EXPORT_ACCIDENT_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }

    if($_GET['s'] == 'INFRINGMENT_REGISTER')
    {
        $LIndex->EXPORT_INFRINGMENT_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }

    if($_GET['s'] == 'INSPECTION_REGISTER')
    {
        $LIndex->EXPORT_INSPECTION_REGISTER($fdateID,$tdateID,$searchbyID,$dashID,$auditID);
    }
	
	if($_GET['s'] == 'MNGCOMMENTS_REGISTER')
    {
        $LIndex->EXPORT_MNGCOMMENTS_REGISTER($fdateID,$tdateID,$searchbyID,$auditID);
    }
	
    if($_GET['s'] == 'WWC_EXPIRY' || $_GET['s'] == 'LICENSE_EXPIRY' || $_GET['s'] == 'GAS_FITTING_EXPIRY' || $_GET['s'] == 'ACON_REFRIGERANT_EXPIRY' || $_GET['s'] == 'WORKSAFE_DOGGING_EXPIRY' || $_GET['s'] == 'FORKLIFT_EXPIRY')
    { 
		$LIndex->EXPORT_REPORT_EMPLOYEE($filters);
    }	
?>