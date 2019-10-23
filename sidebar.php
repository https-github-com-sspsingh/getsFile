<div class="wrapper row-offcanvas row-offcanvas-left">
<aside class="left-side sidebar-offcanvas <?=((substr((basename($_SERVER['PHP_SELF'])), 0, 3) == 'rpt')  || 
													  (basename($_SERVER['PHP_SELF']) == 'drvshifts.php') || 
													  (basename($_SERVER['PHP_SELF']) == 'profile_5.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'profile_6.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'profile_4.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'drvsigon.php') ||													  
													  (basename($_SERVER['PHP_SELF']) == 'missings.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'signon.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'datasender.php') ||
													  (basename($_SERVER['PHP_SELF']) == 'profile_1.php' || 
													   basename($_SERVER['PHP_SELF']) == 'profile_2.php' ||
													   basename($_SERVER['PHP_SELF']) == 'shroster.php' ||													   
													   basename($_SERVER['PHP_SELF']) == 'apiresponse.php' || 
													   basename($_SERVER['PHP_SELF']) == 'profile_3.php' || 
													   ($_REQUEST['optionID'] == 3) ||
													  (basename($_SERVER['PHP_SELF']) == 'users.php' && $login->Decrypt($_GET['a']) == 'create') || 
													  (basename($_SERVER['PHP_SELF']) == 'urole.php' && $login->Decrypt($_GET['a']) == 'create')) ? 'collapse-left' : '')?>">
													   
<section class="sidebar">
<ul class="sidebar-menu">
<?PHP
	$settingID = $SIndex->ActiveSidebar(1);		$lovsID     = $SIndex->ActiveSidebar(2);		$mastersID = $SIndex->ActiveSidebar(3);
	$employeID = $SIndex->ActiveSidebar(4);		$ddetailID  = $SIndex->ActiveSidebar(5);		$rostersID = $SIndex->ActiveSidebar(6);
	$reportsID  = $SIndex->ActiveSidebar(7);	$performID  = $SIndex->ActiveSidebar(8);	    $resetpsID = $SIndex->ActiveSidebar(9);			
	$feedsID   = $SIndex->ActiveSidebar(10);    $drhisftsID = $SIndex->ActiveSidebar(11);		$newscpeID = $SIndex->ActiveSidebar(12);			
	$emprptID  = $SIndex->ActiveSidebar(13);    $drvrptID = $SIndex->ActiveSidebar(14);		    $signrptID = $SIndex->ActiveSidebar(15);		
	$adhdrpID = $SIndex->ActiveSidebar(16);		$stfareID = $SIndex->ActiveSidebar(17);	    $hzstrptID = $SIndex->ActiveSidebar(18);

	$prightID = '<i class="fa fa-angle-left pull-right"></i> <span ';			   
	$ancID = '<li><a href="'.$login->home;
	$closeID = '</span></a></li>';
	 
	if($login->GET_menusPerms('48,49,50,51,96') == 1)  
	{
		echo '<li class="treeview '.($settingID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Settings</span>'.$prightID.'</i></a>';
			echo '<ul class="treeview-menu">';
				echo ($login->GET_menusPerms('48') == 1 ? $ancID.'setup/company.php">'.$prightID.($settingID['cl']).'>Company'.$closeID : '');
				echo ($login->GET_menusPerms('48') == 1 ? $ancID.'setup/scompany.php">'.$prightID.($settingID['cl']).'>Company - Sub Depots'.$closeID : '');
				echo ($login->GET_menusPerms('49') == 1 ? $ancID.'setup/frmset.php">'.$prightID.($settingID['cl']).'>Forms - List'.$closeID : '');
				echo ($login->GET_menusPerms('50') == 1 ? $ancID.'setup/urole.php">'.$prightID.($settingID['cl']).'>User Roles'.$closeID : '');
				echo ($login->GET_menusPerms('51') == 1 ? $ancID.'setup/users.php">'.$prightID.($settingID['cl']).'>Users'.$closeID : '');
				echo ($login->GET_menusPerms('96') == 1 ? $ancID.'setup/psw_reset.php">'.$prightID.($settingID['cl']).'>Password Reset'.$closeID : '');
			echo '</ul>';
		echo '</li>';
	}

	if($login->GET_menusPerms('20,21,17,13,19,3,15,29,28,12,1,30,6,2,25,4,22,27,11,26,53,7,8,9,14,52,5,23,24,128,125,124,127,126,123,135') == 1)
	{
	   $urlID = '';
	   $urlID = '<li><a href="'.$login->home.'setup/master.php?f=';

	   echo '<li class="treeview '.($lovsID['cs']).'">';
	   echo '<a href="#"><i class="fa fa-edit"></i> <span>Modify Drop-Downs</span>'.$prightID.'</i></a>';
	   echo '<ul class="treeview-menu">';

		echo ($login->GET_menusPerms('20') == 1 ? $urlID.$login->Encrypt('20').'">'.$prightID.($lovsID['cl']).'>Accident Details'.$closeID   : '');
		echo ($login->GET_menusPerms('21') == 1 ? $urlID.$login->Encrypt('21').'">'.$prightID.($lovsID['cl']).'>Accident Category'.$closeID  : '');
		echo ($login->GET_menusPerms('17') == 1 ? $urlID.$login->Encrypt('17').'">'.$prightID.($lovsID['cl']).'>Accountability'.$closeID     : '');
		echo ($login->GET_menusPerms('13') == 1 ? $urlID.$login->Encrypt('13').'">'.$prightID.($lovsID['cl']).'>Agreement Type'.$closeID     : '');
		echo ($login->GET_menusPerms('19') == 1 ? $urlID.$login->Encrypt('19').'">'.$prightID.($lovsID['cl']).'>Action Taken By'.$closeID    : '');
		echo ($login->GET_menusPerms('3') == 1  ? $urlID.$login->Encrypt('3').'">'.$prightID.($lovsID['cl']).'>Body Parts'.$closeID          : '');
		echo ($login->GET_menusPerms('15') == 1 ? $urlID.$login->Encrypt('15').'">'.$prightID.($lovsID['cl']).'>Complaint Reasons'.$closeID  : '');
		echo ($login->GET_menusPerms('135') == 1 ? $urlID.$login->Encrypt('135').'">'.$prightID.($lovsID['cl']).'>Complaint Type'.$closeID   : '');		
		echo ($login->GET_menusPerms('29') == 1 ? $urlID.$login->Encrypt('29').'">'.$prightID.($lovsID['cl']).'>Contractor'.$closeID         : '');
		echo ($login->GET_menusPerms('28') == 1 ? $urlID.$login->Encrypt('28').'">'.$prightID.($lovsID['cl']).'>Contracts'.$closeID          : '');
		echo ($login->GET_menusPerms('12') == 1 ? $urlID.$login->Encrypt('12').'">'.$prightID.($lovsID['cl']).'>Designation'.$closeID        : '');
		echo ($login->GET_menusPerms('1') == 1  ? $urlID.$login->Encrypt('1').'">'.$prightID.($lovsID['cl']).'>Depots Action'.$closeID       : '');    
		echo ($login->GET_menusPerms('30') == 1 ? $urlID.$login->Encrypt('30').'">'.$prightID.($lovsID['cl']).'>Feed Back Type'.$closeID     : '');
		echo ($login->GET_menusPerms('6') == 1  ? $urlID.$login->Encrypt('6').'">'.$prightID.($lovsID['cl']).'>Gender Master'.$closeID       : '');    
		echo ($login->GET_menusPerms('2') == 1  ? $urlID.$login->Encrypt('2').'">'.$prightID.($lovsID['cl']).'>Graffiti Items'.$closeID      : '');
		echo ($login->GET_menusPerms('128') == 1 ? $urlID.$login->Encrypt('128').'">'.$prightID.($lovsID['cl']).'>Hazard Type'.$closeID      : '');		
		echo ($login->GET_menusPerms('66') == 1 ? $urlID.$login->Encrypt('66').'">'.$prightID.($lovsID['cl']).'>Inspected By'.$closeID       : '');
		echo ($login->GET_menusPerms('61') == 1 ? $urlID.$login->Encrypt('61').'">'.$prightID.($lovsID['cl']).'>Inspection Fines'.$closeID   : '');
		echo ($login->GET_menusPerms('25') == 1 ? $urlID.$login->Encrypt('25').'">'.$prightID.($lovsID['cl']).'>Incident Actions'.$closeID   : '');
		echo ($login->GET_menusPerms('4') == 1  ? $urlID.$login->Encrypt('4').'">'.$prightID.($lovsID['cl']).'>Incident Status'.$closeID     : '');    
		echo ($login->GET_menusPerms('22') == 1 ? $urlID.$login->Encrypt('22').'">'.$prightID.($lovsID['cl']).'>Infringement Type'.$closeID  : '');
		echo ($login->GET_menusPerms('27') == 1 ? $urlID.$login->Encrypt('27').'">'.$prightID.($lovsID['cl']).'>Inspection Result'.$closeID  : '');		
		echo ($login->GET_menusPerms('126') == 1 ? $urlID.$login->Encrypt('126').'">'.$prightID.($lovsID['cl']).'>Investigation Results'.$closeID  : '');		
		echo ($login->GET_menusPerms('127') == 1 ? $urlID.$login->Encrypt('127').'">'.$prightID.($lovsID['cl']).'>JOB Title'.$closeID        : '');		
		echo ($login->GET_menusPerms('123') == 1 ? $urlID.$login->Encrypt('123').'">'.$prightID.($lovsID['cl']).'>License Type'.$closeID        : '');
		echo ($login->GET_menusPerms('11') == 1 ? $urlID.$login->Encrypt('11').'">'.$prightID.($lovsID['cl']).'>Leave Types'.$closeID        : '');
		echo ($login->GET_menusPerms('26') == 1 ? $urlID.$login->Encrypt('26').'">'.$prightID.($lovsID['cl']).'>Offence Types'.$closeID      : '');    
		echo ($login->GET_menusPerms('53') == 1 ? $ancID.'masters/offence.php">'.$prightID.($lovsID['cl']).'>Offence Details'.$closeID 	     : '');    
		echo ($login->GET_menusPerms('7') == 1  ? $urlID.$login->Encrypt('7').'">'.$prightID.($lovsID['cl']).'>Police District'.$closeID     : '');
		echo ($login->GET_menusPerms('8') == 1  ? $urlID.$login->Encrypt('8').'">'.$prightID.($lovsID['cl']).'>Police Stations'.$closeID     : '');
		echo ($login->GET_menusPerms('9') == 1  ? $urlID.$login->Encrypt('9').'">'.$prightID.($lovsID['cl']).'>Police Suburb'.$closeID       : '');    
		echo ($login->GET_menusPerms('14') == 1 ? $urlID.$login->Encrypt('14').'">'.$prightID.($lovsID['cl']).'>Response Methods'.$closeID   : '');
		echo ($login->GET_menusPerms('67') == 1 ? $urlID.$login->Encrypt('67').'">'.$prightID.($lovsID['cl']).'>Resigned Master'.$closeID    : ''); 
		echo ($login->GET_menusPerms('125') == 1 ? $urlID.$login->Encrypt('125').'">'.$prightID.($lovsID['cl']).'>SIR Type'.$closeID    	 : '');
		echo ($login->GET_menusPerms('124') == 1 ? $urlID.$login->Encrypt('124').'">'.$prightID.($lovsID['cl']).'>STFare Description'.$closeID    	 : '');		
		echo ($login->GET_menusPerms('52') == 1 ? $ancID.'masters/stype.php">'.$prightID.($lovsID['cl']).'>Shift Types'.$closeID       	     : '');
		echo ($login->GET_menusPerms('5') == 1  ? $urlID.$login->Encrypt('5').'">'.$prightID.($lovsID['cl']).'>Staff Position'.$closeID      : '');    
		echo ($login->GET_menusPerms('121') == 1  ? $urlID.$login->Encrypt('121').'">'.$prightID.($lovsID['cl']).'>Termination Reason'.$closeID      : '');
		echo ($login->GET_menusPerms('122') == 1  ? $urlID.$login->Encrypt('122').'">'.$prightID.($lovsID['cl']).'>Visa Type'.$closeID      : '');    
		echo ($login->GET_menusPerms('23') == 1 ? $urlID.$login->Encrypt('23').'">'.$prightID.($lovsID['cl']).'>Warning Types'.$closeID      : '');
		echo ($login->GET_menusPerms('24') == 1 ? $urlID.$login->Encrypt('24').'">'.$prightID.($lovsID['cl']).'>Weapons'.$closeID            : '');

	   echo '</ul>';
	   echo '</li>';
	}

	if($login->GET_menusPerms('31,32,33,34,35,36,72') == 1)
	{
		echo '<li class="treeview '.($mastersID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Masters</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';

			echo ($login->GET_menusPerms('31') == 1 ? $ancID.'masters/slabs_perf.php">'.$prightID.($mastersID['cl']).'>Slabs - Performance'.$closeID  : '');
			echo ($login->GET_menusPerms('32') == 1 ? $ancID.'masters/srvdtls.php">'.$prightID.($mastersID['cl']).'>Service Details'.$closeID	: '');
			echo ($login->GET_menusPerms('33') == 1 ? $ancID.'masters/buses.php">'.$prightID.($mastersID['cl']).'>Bus Master'.$closeID	: '');
			echo ($login->GET_menusPerms('34') == 1 ? $ancID.'masters/suburbs.php">'.$prightID.($mastersID['cl']).'>Suburbs Master'.$closeID 	: '');
			echo ($login->GET_menusPerms('35') == 1 ? $ancID.'masters/contracts.php">'.$prightID.($mastersID['cl']).'>Contract Service Details'.$closeID : '');
			echo ($login->GET_menusPerms('36') == 1 ? $ancID.'masters/cstmpoint.php">'.$prightID.($mastersID['cl']).'>Service Timepoint'.$closeID : '');
			echo ($login->GET_menusPerms('72') == 1 ? $ancID.'setup/frm_fields.php">'.$prightID.($mastersID['cl']).'>Audit-Builder-Form'.$closeID : '');
			echo ($login->GET_menusPerms('72') == 1 ? $ancID.'masters/rbuilder.php">'.$prightID.($mastersID['cl']).'>Report-Builder-Form'.$closeID : '');
		echo '</ul>';
		echo '</li>';
	}

	if($login->GET_menusPerms('37,38,39,73') == 1)
	{
		echo '<li class="treeview '.($employeID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Employee</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';
			
			/*echo ($login->GET_menusPerms('73') == 1 ? $ancID.'forms/etransferin.php">'.$prightID.($employeID['cl']).'>Employee TransferedIn'.$closeID : '');
			echo ($login->GET_menusPerms('73') == 1 ? $ancID.'forms/etransferout.php">'.$prightID.($employeID['cl']).'>Employee TransferedOut'.$closeID : '');*/
			
			echo ($login->GET_menusPerms('73') == 1 ? $ancID.'forms/echanges.php">'.$prightID.($employeID['cl']).'>Change Position'.$closeID : '');
			echo ($login->GET_menusPerms('73') == 1 ? $ancID.'forms/etransfer.php">'.$prightID.($employeID['cl']).'>Employee Transfer'.$closeID : '');			
			echo ($login->GET_menusPerms('37') == 1 ? $ancID.'forms/emp.php">'.$prightID.($employeID['cl']).'>Employee Details'.$closeID      : '');
			echo ($login->GET_menusPerms('38') == 1 ? $ancID.'forms/sicklv.php">'.$prightID.($employeID['cl']).'>Personal Leave'.$closeID         : '');
			echo ($login->GET_menusPerms('39') == 1 ? $ancID.'forms/prpermits.php">'.$prightID.($employeID['cl']).'>Parking Permits'.$closeID : '');

		echo '</ul>';
		echo '</li>';   
	}

	if($login->GET_menusPerms('40,41,42,43,44,45') == 1)
	{
		echo '<li class="treeview '.($ddetailID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Driver Details</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';

			echo ($login->GET_menusPerms('40') == 1  ? $ancID.'forms/cmplnt.php">'.$prightID.($ddetailID['cl']).'>Customer Feedback Register'.$closeID      : '');
			echo ($login->GET_menusPerms('41') == 1  ? $ancID.'forms/incident.php">'.$prightID.($ddetailID['cl']).'>Incident Register'.$closeID        : '');
			echo ($login->GET_menusPerms('42') == 1  ? $ancID.'forms/accident.php">'.$prightID.($ddetailID['cl']).'>Accident Register'.$closeID        : '');
			echo ($login->GET_menusPerms('43') == 1  ? $ancID.'forms/infrgs.php">'.$prightID.($ddetailID['cl']).'>Infringement Register'.$closeID      : '');
			echo ($login->GET_menusPerms('44') == 1  ? $ancID.'forms/inspc.php">'.$prightID.($ddetailID['cl']).'>Inspection Register'.$closeID         : '');
			echo ($login->GET_menusPerms('45') == 1  ? $ancID.'forms/mng_cmn.php">'.$prightID.($ddetailID['cl']).'>Manager Comments Register'.$closeID : '');  

		echo '</ul>';
		echo '</li>';  
	}

	if($login->GET_menusPerms('130,131') == 1)
	{
		echo '<li class="treeview '.($newscpeID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Health & Safety</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';
			echo ($login->GET_menusPerms('130') == 1 ? $ancID.'forms/hiz.php">'.$prightID.($newscpeID['cl']).'>Hazard Register'.$closeID      : '');
			echo ($login->GET_menusPerms('131') == 1 ? $ancID.'forms/sir.php">'.$prightID.($newscpeID['cl']).'>SIR Register'.$closeID      : '');
		echo '</ul>';
		echo '</li>';  
	}
	
	if($login->GET_menusPerms('64,65,74') == 1)
	{
		$fdateID = date("d/m/Y", strtotime("-12 months"));
		$tdateID = date('d/m/Y');
			
		echo '<li class="treeview '.($performID['cs']).'">';
		echo '<a href="#"><i class="fa fa-clipboard"></i> <span>Performance</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';

		echo ($login->GET_menusPerms('65') == 1 ? $ancID.'imports/rpt_performance.php?cs='.$login->Encrypt('1').'">'.$prightID.($performID['cl']).'>Import Excel Sheets'.$closeID : '');
		echo ($login->GET_menusPerms('64') == 1 ? $ancID.'imports/rpt_performance.php?cs='.$login->Encrypt('2').'">'.$prightID.($performID['cl']).'>Performance Report'.$closeID : '');
		
		if($login->GET_menusPerms('74') == 1)
		{        

			echo '<li><a target="blank" href="'.$login->home.'profile_2.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'"><i class="fa fa-angle-left pull-right"></i> <span '.($performID['cl']).'>Individual Driver </span></a></li>';
			echo '<li><a target="blank" href="'.$login->home.'profile_1.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'"><i class="fa fa-angle-left pull-right"></i> <span '.($performID['cl']).'>Depot Performance </span></a></li>';
			echo '<li><a target="blank" href="'.$login->home.'profile_3.php"><i class="fa fa-angle-left pull-right"></i> <span '.($performID['cl']).'>Depot Comparative</span></a></li>';    
		}
		
		echo '</ul>';
		echo '</li>';
	}

	if($login->GET_menusPerms('93,79,94,82,107,80,97,81') == 1)
	{    
		echo '<li class="treeview '.($drhisftsID['cs']).'">';
		echo '<a href="#"><i class="fa fa-truck"></i> <span>Driver - Sign On</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';

		echo ($login->GET_menusPerms('93') == 1 ? $ancID.'imports/imp_daily.php">'.$prightID.($drhisftsID['cl']).'>Import Daily'.$closeID : '');
		echo ($login->GET_menusPerms('79') == 1 ? $ancID.'imports/imp_headers.php">'.$prightID.($drhisftsID['cl']).'>Header Sheet'.$closeID : '');
		echo ($login->GET_menusPerms('94') == 1 ? $ancID.'imports/shift_setter.php">'.$prightID.($drhisftsID['cl']).'>Shift Setter'.$closeID : '');
		echo ($login->GET_menusPerms('82') == 1 ? $ancID.'imports/spares.php">'.$prightID.($drhisftsID['cl']).'>Spare Master'.$closeID : '');
		echo ($login->GET_menusPerms('107') == 1 ? $ancID.'imports/mechanics.php">'.$prightID.($drhisftsID['cl']).'>Mechanic Master'.$closeID : '');	
		echo ($login->GET_menusPerms('80') == 1 ? $ancID.'imports/rpt_allocation.php">'.$prightID.($drhisftsID['cl']).'>Allocation Sheet'.$closeID : '');
		echo ($login->GET_menusPerms('97') == 1 ? $ancID.'imports/newshifts.php">'.$prightID.($drhisftsID['cl']).'>Manual Shift'.$closeID : '');        
		echo ($login->GET_menusPerms('81') == 1 ? $ancID.'profile_4.php">'.$prightID.($drhisftsID['cl']).'>Sign On Status'.$closeID : '');		
		
		echo ($login->GET_menusPerms('81') == 1 ? $ancID.'drvsigon.php">'.$prightID.($drhisftsID['cl']).'><b style="color:yellow;">Driver Signon</b>'.$closeID : '');
		
		echo ($login->GET_menusPerms('93') == 1 ? $ancID.'imports/imp_hastus.php">'.$prightID.($drhisftsID['cl']).'>Import Hastus'.$closeID : '');		
		echo '</ul>';
		echo '</li>';
	}

	if($login->GET_menusPerms('46,47') == 1)
	{
		echo '<li class="treeview '.($rostersID['cs']).'">';
		echo '<a href="#"><i class="fa fa-edit"></i> <span>Rostering</span>'.$prightID.'</a>';
		echo '<ul class="treeview-menu">';

			echo ($login->GET_menusPerms('46') == 1 ? $ancID.'forms/wshifts.php">'.$prightID.($rostersID['cl']).'>Weekly Shifts'.$closeID : '');
			echo ($login->GET_menusPerms('47') == 1 ? $ancID.'forms/shifts.php">'.$prightID.($rostersID['cl']).'>Shifts'.$closeID : '');

		echo '</ul>';
		echo '</li>';  
	}

	if($login->GET_menusPerms('129') == 1)
	{
		echo '<li '.($resetpsID['cs']).'><a href="'.$login->home.'forms/stfare.php"><i class="fa fa-users"></i> <span>Fare Evasion Register'.$closeID;  
	}
	
	
	if($login->GET_menusPerms('95,98,77,99,100,101,102,103,108,92,93,104,105,91,134,132,133') == 1)
	{ 
		echo '<li class="treeview '.$reportsID['cs'].'">';
		echo '<a href="#"><i class="fa fa-print"></i>  Reports<i class="fa fa-angle-left pull-right"></i></a>';    
		echo '<ul class="treeview-menu">';
		
		if($login->GET_menusPerms('95,98,77') == 1)
		{
			echo '<li class="treeview '.$emprptID['cs'].'">';
				echo '<a href="#"><i class="fa fa-book"></i>Employee Reports<i class="fa fa-angle-left pull-right"></i></a>';
				echo '<ul class="treeview-menu">';
					echo ($login->GET_menusPerms('95') == 1 ?  $ancID.'rpts/rpt_emp.php" '.$emprptID['cl'].'>Employee Reports</a></li>' : '');
					echo ($login->GET_menusPerms('98') == 1 ?  $ancID.'rpts/rpt_sicklv.php" '.$emprptID['cl'].'>Personal Leave Reports</a></li>' : '');
					echo ($login->GET_menusPerms('95') == 1 ?  $ancID.'rpts/rpt_downloads.php" '.$emprptID['cl'].'>DL/WWC Reports</a></li>' : '');
					echo ($login->GET_menusPerms('77') == 1 ?  $ancID.'rpts/rpt_userlogs.php" '.$emprptID['cl'].'>User Log Reports</a></li>' : '');
				echo '</ul>';	
			echo '</li>';
		}
		
		if($login->GET_menusPerms('99,100,101,102,103,108') == 1)
		{
			echo '<li class="treeview '.$drvrptID['cs'].'">';
				echo '<a href="#"><i class="fa fa-book"></i>Driver Detail Reports<i class="fa fa-angle-left pull-right"></i></a>';
				echo '<ul class="treeview-menu">';
					echo ($login->GET_menusPerms('99') == 1  ?  $ancID.'rpts/rpt_accident.php" '.$drvrptID['cl'].'>Accident Reports</a></li>' : '');
					echo ($login->GET_menusPerms('103') == 1 ?  $ancID.'rpts/rpt_cmline.php" '.$drvrptID['cl'].'>Customer Feedback Reports</a></li>' : '');
					echo ($login->GET_menusPerms('108') == 1 ?  $ancID.'rpts/rpt_incident.php" '.$drvrptID['cl'].'>Incident Reports</a></li>' : '');
					echo ($login->GET_menusPerms('101') == 1 ?  $ancID.'rpts/rpt_infrgs.php" '.$drvrptID['cl'].'>Infringement Reports</a></li>' : '');
					echo ($login->GET_menusPerms('100') == 1 ?  $ancID.'rpts/rpt_inspc.php" '.$drvrptID['cl'].'>Inspection Reports</a></li>' : '');
					echo ($login->GET_menusPerms('102') == 1 ?  $ancID.'rpts/rpt_mngcmn.php" '.$drvrptID['cl'].'>M.Comments Reports</a></li>' : '');
				echo '</ul>';	
			echo '</li>';
		}
		
		if($login->GET_menusPerms('92,93,104,105') == 1)
		{
			echo '<li class="treeview '.$signrptID['cs'].'">';
				echo '<a href="#"><i class="fa fa-book"></i>Driver Signon Reports<i class="fa fa-angle-left pull-right"></i></a>';
				echo '<ul class="treeview-menu">';
					echo ($login->GET_menusPerms('92') == 1  ?  $ancID.'profile_5.php?typeID=1&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y').'" '.$signrptID['cl'].'>Depot Summary Reports</a></li>' : '');
					echo ($login->GET_menusPerms('92') == 1  ?  $ancID.'profile_5.php?typeID=2&sidebarID=1&filterID[]='.$_SESSION[$login->website]['compID'].'&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y').'" '.$signrptID['cl'].'>Depot Detail Reports</a></li>' : '');
					echo ($login->GET_menusPerms('92') == 1  ?  $ancID.'profile_6.php?filterID[]='.$_SESSION[$login->website]['compID'].'&fdateID='.date('d/m/Y',strtotime(date('Y-m-d').'-1 Months')).'&tdateID='.date('d/m/Y').'" '.$signrptID['cl'].'>Driver Detail Reports</a></li>' : '');
					echo ($login->GET_menusPerms('93') == 1  ?  $ancID.'imports/rpt_signons.php" '.$signrptID['cl'].'>Detailed Reports</a></li>' : '');
					echo ($login->GET_menusPerms('105') == 1 ?  $ancID.'imports/rpt_prior_alloc.php" '.$signrptID['cl'].'>Prior Allocation Reports</a></li>' : '');
					echo ($login->GET_menusPerms('104') == 1 ?  $ancID.'imports/rpt_allocsheet.php" '.$signrptID['cl'].'>Allocation Sheet Reports</a></li>' : '');					
				echo '</ul>';	
			echo '</li>';
		}
		
		if($login->GET_menusPerms('91') == 1)
		{
			echo '<li '.$adhdrpID['cs'].'><a href="'.$login->home.'imports/rpt_headers.php"><i class="fa fa-book"></i> <span '.$adhdrpID['cl'].'>Admin Header Report</span></a></li>';
		}
		
		if($login->GET_menusPerms('134') == 1)
		{
			echo '<li '.$stfareID['cs'].'><a href="'.$login->home.'rpts/rpt_stfare.php"><i class="fa fa-book"></i> <span '.$stfareID['cl'].'>Fare Evasion Report</span></a></li>';
		}
		
		if($login->GET_menusPerms('132,133') == 1)
		{
			echo '<li class="treeview '.$hzstrptID['cs'].'">';
				echo '<a href="#"><i class="fa fa-book"></i>Hazard & Safety Reports<i class="fa fa-angle-left pull-right"></i></a>';
				echo '<ul class="treeview-menu">';
					echo ($login->GET_menusPerms('132') == 1  ?  $ancID.'rpts/rpt_hiz.php" '.$hzstrptID['cl'].'> Hazard Reports</a></li>' : '');
					echo ($login->GET_menusPerms('133') == 1  ?  $ancID.'rpts/rpt_sir.php" '.$hzstrptID['cl'].'> Safety Reports</a></li>' : '');
				echo '</ul>';	
			echo '</li>';
		}
		
		echo '</ul>';
		echo '</li>';
	}
	
	
	if($login->GET_menusPerms('54') == 1)
	{
		echo '<li '.($resetpsID['cs']).'><a href="'.$login->home.'setup/resetPsw.php"><i class="fa fa-users"></i> <span>Change Password'.$closeID;  
	}

		echo '<li '.($feedsID['cs']).'><a href="'.$login->home.'masters/feed.php"><i class="fa fa-phone-square"></i> <span>TRANSformIT Feedback'.$closeID;
		
	if($login->GET_menusPerms('90') == 1)
	{    
		echo '<li '.($feedsID['cs']).'><a href="'.$login->home.'apiresponse.php"><i class="fa fa-building-o"></i> <span>API - response'.$closeID;
	}
		
	if($login->GET_menusPerms('75') == 1)
	{
		echo '<li '.($resetpsID['cs']).'><a href="'.$login->home.'DBbackup.php"><i class="fa fa-book"></i> <span>Database Backup'.$closeID;		
		echo '<li '.($resetpsID['cs']).'><a target="_blank" href="'.$login->home.'SQL_DBbackup.php"><i class="fa fa-book"></i> <span style="color:yellow;">Database Backup'.$closeID;
	}
	
?>
</ul>                    
</ul>
</section>
</aside>