<?PHP
    include 'includes.php';
    $login->NotLogin_Redi();
    include 'header.php';
    include 'sidebar.php';
	
    $ID     =   isset($_GET['i'])   ?	$login->Decrypt($_GET['i']) : '';
    $ID     =   $ID > 0				?   $ID							: '786';
	
	$EXP_60Days = $CIndex->GET_LicensesExpiryCounts('60');
	//$EXP_07Days = $CIndex->GET_LicensesExpiryCounts('7');
	$EXP_01Days = $CIndex->GET_LicensesExpiryCounts('1');

/* DASHBOARD - NOTIFICATIONS - API */
	$expiry_notifyID = 0;
    $expiry_notifyID = ($EXP_01Days['DLC'] + $EXP_01Days['WWC']);
	
	echo '<input type="hidden" name="dateID" id="dateID" value="'.$dateID.'" />';
    echo '<input type="hidden" name="getsID" id="getsID" value="'.$ID.'" />';
    echo '<input type="hidden" name="expiry_notifyID" id="expiry_notifyID" value="'.$expiry_notifyID.'" />';
	
	$crtID = " AND DATE(All_Data.lcnoDT) <= '".date('Y-m-d',strtotime('+1Days'))."' ";
    $SQL = "SELECT All_Data.ID, All_Data.code, All_Data.full_name, All_Data.desigID, All_Data.companyID, All_Data.lcnoID, All_Data.lcnoDT, All_Data.typeID FROM (SELECT ID, code, full_name, desigID, companyID, ddlcno AS lcnoID, ddlcdt AS lcnoDT, 'Driver Licence' AS typeID FROM employee WHERE status = 1 
    UNION ALL SELECT ID, code, full_name, desigID, companyID, wwcprno AS lcnoID, wwcprdt AS lcnoDT, 'WWC Permit' AS typeID FROM employee WHERE status = 1 AND desigID In(9,208,209)) AS All_Data WHERE All_Data.companyID In (".$_SESSION[$login->website]['compID'].") ".$crtID." Order By All_Data.code ASC ";
    $Qry = $login->DB->prepare($SQL);
    if($Qry->execute())
    {
        $login->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
        if(is_array($login->rows_1) && count($login->rows_1) > 0)			
        {
            $resID = 1;
            foreach($login->rows_1 as $rows_1)
            {
                echo '<input type="hidden" id="expiry_name_'.$resID.'" value="'.$rows_1['full_name'].' - '.$rows_1['code'].'" />';
                echo '<input type="hidden" id="expiry_date_'.$resID.'" value="'.$login->VdateFormat($rows_1['lcnoDT']).'" />';
                echo '<input type="hidden" id="expiry_type_'.$resID.'" value="'.$rows_1['typeID'].'" />';

                $resID++;
            }
        }
    }
	
echo '<aside class="right-side">';
echo '<section class="content">';
echo '<div class="row" style="margin-top:-10px;">';
	/* SECTOR - 1 */
	echo '<section class="col-lg-4 connectedSortable">';
	if($login->GET_menusPerms('106') == 1)
	{
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-users"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Manager Comments</h3><b class="box-title" style="color:#3E777B;font-size: 16px; font-weight:bold; float:right; margin-right:50px;">&nbsp;</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
			
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<input type="text" id="dashCD" class="form-control" style="height: 43px;" placeholder="Search By Code/Name ...." />';
					echo '<div id="divResult"></div>';
				echo '</li>'; 
				
			echo '</ul>';
		echo '</div>';
		echo '</div>';
	}
	
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Audit Trail</h3>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
				
				$EM_Audits = 0;	$AC_Audits = 0;	$CM_Audits = 0;	$IN_Audits = 0;	$IF_Audits = 0;	$IS_Audits = 0;	$MN_Audits = 0;	$SR_Audits = 0;	$HZ_Audits = 0;
				$EM_Audits = $AIndex->form_Employee_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d")); 
				$GI_Audits = $AIndex->form_GIncident_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d")); 
				$SI_Audits = $AIndex->form_SIncident_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d")); 
				$CM_Audits = $AIndex->form_CommentLine_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				$AC_Audits = $AIndex->form_Accident_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));			
				$IF_Audits = $AIndex->form_Infringment_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				$IS_Audits = $AIndex->form_Inspection_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				$MN_Audits = $AIndex->form_ManangerComments_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				$SR_Audits = $AIndex->form_SIR_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				$HZ_Audits = $AIndex->form_HIZ_Counts(date("Y-m-d", strtotime(date('Y-m-d').'-1 Years')),date("Y-m-d"));
				
				//echo $C_Audits['filterID'];
				
				$urlID_7  = $login->home.'forms/emp.php?auditID='.$login->Encrypt(ltrim($EM_Audits['filterID'],','));
				$urlID_8  = $login->home.'forms/accident.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($AC_Audits['filterID'],','));
				$urlID_9  = $login->home.'forms/incident.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($GI_Audits['filterID'],','));
				$urlID_10 = $login->home.'forms/incident.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($SI_Audits['filterID'],','));
				$urlID_11 = $login->home.'forms/cmplnt.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($CM_Audits['filterID'],','));
				$urlID_12 = $login->home.'forms/infrgs.php?Submit=%C2%A0Filter+Data&&auditID='.$login->Encrypt(ltrim($IF_Audits['filterID'],','));
				$urlID_13 = $login->home.'forms/inspc.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($IS_Audits['filterID'],','));
				$urlID_14 = $login->home.'forms/mng_cmn.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($MN_Audits['filterID'],','));
				$urlID_28 = $login->home.'forms/sir.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($SR_Audits['filterID'],','));
				$urlID_29 = $login->home.'forms/hiz.php?Submit=%C2%A0Filter+Data&auditID='.$login->Encrypt(ltrim($HZ_Audits['filterID'],','));
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_7.'">Employee</a></span>';
					echo '<small style="float:right;padding: 5px;background:#814141; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_7.'"> '.$EM_Audits['countsID'].'</a></small>';								
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_8.'">Accidents</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_8.'"> '.$AC_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_9.'">Incidents - General</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-info"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_9.'"> '.$GI_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_10.'">Incidents - Security</a></span>'; 
					echo '<small style="float:right;padding: 5px; background:#888800;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-info"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_10.'"> '.$SI_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_11.'">Customer Feedback</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-danger"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_11.'"> '.$CM_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_12.'">Infringements</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-warning"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_12.'"> '.$IF_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_13.'">Inspections</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-success"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_13.'"> '.$IS_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_14.'">Manager Comments</a></span>'; 
					echo '<small style="float:right;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-default"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_14.'"> '.$MN_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_29.'">Health, Safety and Hazard</a></span>'; 
					echo '<small style="float:right; background:#BC8F8F;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-default"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_29.'"> '.$HZ_Audits['countsID'].'</a></small>';				
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_28.'">System Improvement Request</a></span>'; 
					echo '<small style="float:right; background:#2F4F4F;padding: 5px;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-default"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_28.'"> '.$SR_Audits['countsID'].'</a></small>';				
				echo '</li>';
			echo '</ul>';
		echo '</div>';
		echo '</div>';	
	echo '</section>';
	
	/* SECTOR - 2 */
	echo '<section class="col-lg-4 connectedSortable">';
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">System Improvement Request</h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';					
				$PEN_Sir1 = 0;	$PEN_Sir2 = 0;
				$PEN_Sir1 = $login->count_rows('sir_regis', " WHERE statusID = 1 AND companyID In(".$_SESSION[$login->website]['compID'].") ");
				$PEN_Sir2 = $login->count_rows('sir_regis', " WHERE statusID = 1 AND Date_Add(issuetoDT, INTERVAL 28 DAY) < '".date("Y-m-d")."' AND companyID In(".$_SESSION[$login->website]['compID'].") ");

				$urlID_32 = $login->home.'forms/Sir.php?dashID='.$login->Encrypt('1');
				$urlID_33 = $login->home.'forms/Sir.php?dashID='.$login->Encrypt('2');
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_33.'">Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_33.'"> '.$PEN_Sir2.'</a></small>';
				echo '</li>'; 
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_32.'">All Pending / Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#814141; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_32.'"> '.$PEN_Sir1.'</a></small>';
				echo '</li>';
			echo '</ul>';
		echo '</div>';
		echo '</div>';


		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Licences Expiring Soon</h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Expiring)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';		
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					$urlID_3 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=60&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('1').'&Submit=%C2%A0Filter+Data';
					$urlID_4 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=60&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('2').'&Submit=%C2%A0Filter+Data';
					$urlID_34 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=60&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('3').'&Submit=%C2%A0Filter+Data';
					
					
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_3.'">Driver Licence</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_3.'"> '.$EXP_60Days['DLC'].'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_4.'">WWC Permit No</a></span>';
					echo '<small style="float:right;padding: 5px;background:#428BCA;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_4.'"> '.$EXP_60Days['WWC'].'</a></small>';
				echo '</li>';

				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_34.'">Engineering License No</a></span>';
					echo '<small style="float:right;padding: 5px;background:#F08080;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_34.'"> '.($EXP_60Days['GLC'] + $EXP_60Days['ALC'] + $EXP_60Days['WLC'] + $EXP_60Days['FLC']).'</a></small>';
				echo '</li>';
				
			echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Customer Feedback </h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(TRIS Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
			
			$CQry = $login->DB->prepare("SELECT * FROM complaint LEFT JOIN employee ON employee.ID = complaint.driverID WHERE statusID = 2 AND trisID <= 0 AND complaint.companyID In(".$_SESSION[$login->website]['compID'].") Order By serDT DESC ");
			$CQry->execute();
			$login->Crows = $CQry->fetchAll(PDO::FETCH_ASSOC);
			$CT_Tris = 0;	$C2_Tris = 0;	$CO_Tris = 0;	$dueDate = '';  $daysID = 0; 
			foreach($login->Crows as $Crows)
			{
				$dueDate = $Crows['cmdueDT'];
				$daysID  = ((strtotime(date('Y-m-d', strtotime($Crows['cmdueDT']))) - strtotime(date('Y-m-d'))) / 86400);
				
				if($daysID < 1)			{$CT_Tris += 1;}
				else if($daysID <=2)	{$C2_Tris += 1;}
				else					{$CO_Tris += 1;}
			}
			
			$urlID_15 = $login->home.'forms/cmplnt.php?dashID='.$login->Encrypt('1');
			$urlID_16 = $login->home.'forms/cmplnt.php?dashID='.$login->Encrypt('2');
			$urlID_17 = $login->home.'forms/cmplnt.php?dashID='.$login->Encrypt('3');
			
			echo '<li style="border-bottom: #CFD3D8 1px inset;">';
				echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
				echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_15.'">Due Today</a></span>';
				echo '<small style="float:right;padding: 5px;background:#D9534F; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_15.'"> '.$CT_Tris.'</a></small>';
			echo '</li>';
			
			echo '<li style="border-bottom: #CFD3D8 1px inset;">';
				echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
				echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_16.'">Due In 2 Days</a></span>';
				echo '<small style="float:right;padding: 5px;background:#DF9200;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_16.'"> '.$C2_Tris.'</a></small>';
			echo '</li>';
			
			echo '<li style="border-bottom: #CFD3D8 1px inset;">';
				echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
				echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_17.'">Other Due</a></span>';
				echo '<small style="float:right;padding: 5px;background:#008000;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-info"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_17.'"> '.$CO_Tris.'</a></small>';
			echo '</li>';
					
			echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Infringement </h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
				
				$IF1_Tris = 0;	$IF2_Tris = 0;
				$IF1_Tris = $login->count_rows('infrgs', " WHERE dateID_4 = '0000-00-00' AND companyID In(".$_SESSION[$login->website]['compID'].")  ");
				$IF2_Tris = $login->count_rows('infrgs', " WHERE dateID_4 = '0000-00-00' AND companyID In(".$_SESSION[$login->website]['compID'].") AND Date_Add(dateID_3, INTERVAL 7 DAY) <= '".date("Y-m-d")."' ");
				
				$urlID_5 = $login->home.'forms/infrgs.php?dashID='.$login->Encrypt('1');
				$urlID_6 = $login->home.'forms/infrgs.php?dashID='.$login->Encrypt('2');
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_6.'">Overdue </a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_6.'"> '.$IF2_Tris.'</a></small>';
				echo '</li>'; 
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_5.'">All Pending / Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#008000; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_5.'"> '.$IF1_Tris.'</a></small>';
				echo '</li>';
			echo '</ul>';
		echo '</div>';
		echo '</div>';
	echo '</section>';
	
	/* SECTOR - 3 */
	echo '<section class="col-lg-4 connectedSortable">';
		/*echo '<div class="box box-primary" style="border: 1px solid #497C7C; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Licences Expiring Soon 7 Days</h3>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';		
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					$urlID_35 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=07&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('1').'&Submit=%C2%A0Filter+Data';
					$urlID_36 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=07&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('2').'&Submit=%C2%A0Filter+Data';
					$urlID_37 = $login->home.'rpts/rpt_emp.php?filterID[]='.$_SESSION[$login->website]['compID'].'&daysID=07&rtpyeID=2&fromID='.date("Y-m-d", strtotime(date('Y-m-d').'+0Days')).'&toID='.date("Y-m-d", strtotime(date('Y-m-d').'+60Days')).'&dashID='.$login->Encrypt('3').'&Submit=%C2%A0Filter+Data';
					
					
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_35.'">Driver Licence</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_35.'"> '.$EXP_07Days['DLC'].'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_36.'">WWC Permit No</a></span>';
					echo '<small style="float:right;padding: 5px;background:#428BCA;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_36.'"> '.$EXP_07Days['WWC'].'</a></small>';
				echo '</li>';

				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_37.'">Engineering License No</a></span>';
					echo '<small style="float:right;padding: 5px;background:#F08080;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_37.'"> '.($EXP_07Days['GLC'] + $EXP_07Days['ALC'] + $EXP_07Days['WLC'] + $EXP_07Days['FLC']).'</a></small>';
				echo '</li>';
				
			echo '</ul>';
		echo '</div>';
		echo '</div>';*/
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Health, Safety and Hazard</h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
				$PEN_Hiz1 = 0;	$PEN_Hiz2 = 0;
				$PEN_Hiz1 = $login->count_rows('hiz_regis', " WHERE statusID = 1 AND companyID In(".$_SESSION[$login->website]['compID'].") ");
				$PEN_Hiz2 = $login->count_rows('hiz_regis', " WHERE statusID = 1 AND Date_Add(rdateID, INTERVAL 28 DAY) < '".date("Y-m-d")."' AND companyID In(".$_SESSION[$login->website]['compID'].") ");

				$urlID_30 = $login->home.'forms/hiz.php?dashID='.$login->Encrypt('1');
				$urlID_31 = $login->home.'forms/hiz.php?dashID='.$login->Encrypt('2');
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_31.'">Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_31.'"> '.$PEN_Hiz2.'</a></small>';
				echo '</li>'; 
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">'; 
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_30.'">All Pending / Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#814141; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_30.'"> '.$PEN_Hiz1.'</a></small>';
				echo '</li>';
			echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Accident</h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
				
				$ACO_Tris = 0;	$AEOTris = 0;	$ACA_Tris = 0;
				$ACO_Tris = $login->count_rows('accident_regis', " WHERE Date_Add(dateID, INTERVAL 7 DAY) < '".date("Y-m-d")."' AND companyID In(".$_SESSION[$login->website]['compID'].") AND progressID = 2 AND (engdoneID <= 0 || oprdoneID <= 0) AND admindoneID <= 0 ");
				$AEOTris  = $login->count_rows('accident_regis', " WHERE DATEDIFF(CURDATE(),dateID) <= 7 AND companyID In(".$_SESSION[$login->website]['compID'].") AND progressID = 2 AND (engdoneID <= 0 || oprdoneID <= 0) AND admindoneID <= 0 ");
				$ACA_Tris = $login->count_rows('accident_regis', " WHERE companyID In(".$_SESSION[$login->website]['compID'].") AND progressID = 2 AND engdoneID = 1 AND oprdoneID = 1 AND admindoneID <= 0 ");
				
				$urlID_21 = $login->home.'forms/accident.php?dashID='.$login->Encrypt('1');
				$urlID_22 = $login->home.'forms/accident.php?dashID='.$login->Encrypt('2');
				$urlID_23 = $login->home.'forms/accident.php?dashID='.$login->Encrypt('3');
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_21.'">Overdue </a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_21.'"> '.$ACO_Tris.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text" style="font-size: 13px;"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_22.'">Engineering & Operations (Not Overdue)</a></span>';
					echo '<small style="float:right;padding: 5px;background:#DF9200;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_22.'"> '.$AEOTris.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_23.'">Pending Admin Only </a></span>';
					echo '<small style="float:right;padding: 5px;background:#008000;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-info"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_23.'"> '.$ACA_Tris.'</a></small>';
				echo '</li>';
				
			echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Inspection </h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(TRIS Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
			
				$IQry = $login->DB->prepare("SELECT * FROM inspc LEFT JOIN employee ON employee.ID = inspc.empID WHERE inspc.companyID In(".$_SESSION[$login->website]['compID'].") AND (inspc.statusID = 0 || inspc.statusID = 2) AND inspc.trisID <= 0 Order By rptno DESC ");
				$IQry->execute();
				$login->Irows = $IQry->fetchAll(PDO::FETCH_ASSOC);
				$INT_Tris = 0;	$IN2_Tris = 0;	$INO_Tris = 0;	$dueDate = '';  $daysID = 0; 
				foreach($login->Irows as $Irows)
				{
					$dueDate = date('d-m-Y', strtotime($Irows['dateID'].'+7 Days'));
					$daysID  = ((strtotime(date('Y-m-d', strtotime($Irows['dateID'].'+7 Days'))) - strtotime(date('Y-m-d'))) / 86400);
					
					if($daysID < 1)			{$INT_Tris += 1;}
					else if($daysID <=2)	{$IN2_Tris += 1;}
					else					{$INO_Tris += 1;}
				}
				
				$urlID_18 = $login->home.'forms/inspc.php?dashID='.$login->Encrypt('1');
				$urlID_19 = $login->home.'forms/inspc.php?dashID='.$login->Encrypt('2');
				$urlID_20 = $login->home.'forms/inspc.php?dashID='.$login->Encrypt('3');
			
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_18.'">Due Today</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_18.'"> '.$INT_Tris.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_19.'">Due In 2 Days</a></span>';
					echo '<small style="float:right;padding: 5px;background:#DF9200;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_19.'"> '.$IN2_Tris.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_20.'">Other Due</a></span>';
					echo '<small style="float:right;padding: 5px;background:#008000;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-info"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_20.'"> '.$INO_Tris.'</a></small>';
				echo '</li>';
				
			echo '</ul>';
		echo '</div>';
		echo '</div>';
		
		echo '<div class="box box-primary" style="border: 1px solid #497C7C; margin-top:-10px; box-shadow:4px 3px #959595;">';
		echo '<div class="box-header">';
		echo '<i class="fa fa-bell-o"></i>';
		echo '<h3 class="box-title" style="font-size:18px;">Incident</h3><b class="box-title" style="color:#0000FF;font-size: 14px; font-weight:bold; float:right; margin-right:10px;">(Pending)</b>';
		echo '</div>';
		echo '<div class="box-body" style="margin-top: -17px;">';
			echo '<ul class="todo-list" style="">';
				
				$PEN_Inc1 = 0;	$PEN_Inc2 = 0;	$PEN_Inc3 = 0;	$PEN_Inc4 = 0;
				$PEN_Inc1 = $login->count_rows('incident_regis', " WHERE inc_statusID = 0 AND companyID In(".$_SESSION[$login->website]['compID'].") ");
				$PEN_Inc2 = $login->count_rows('incident_regis', " WHERE inc_statusID = 0 AND Date_Add(dateID, INTERVAL 7 DAY) < '".date("Y-m-d")."' AND companyID In(".$_SESSION[$login->website]['compID'].") ");
				$PEN_Inc3 = $login->count_rows('incident_regis', " WHERE inc_statusID = 0 AND companyID In(".$_SESSION[$login->website]['compID'].") AND sincID = 1 ");
				$PEN_Inc4 = $login->count_rows('incident_regis', " WHERE inc_statusID = 0 AND Date_Add(dateID, INTERVAL 7 DAY) < '".date("Y-m-d")."' AND companyID In(".$_SESSION[$login->website]['compID'].") AND sincID = 1 ");
				
				$urlID_24 = $login->home.'forms/incident.php?dashID='.$login->Encrypt('1');
				$urlID_25 = $login->home.'forms/incident.php?dashID='.$login->Encrypt('2');
				$urlID_26 = $login->home.'forms/incident.php?dashID='.$login->Encrypt('3');
				$urlID_27 = $login->home.'forms/incident.php?dashID='.$login->Encrypt('4');
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_25.'">Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_25.'"> '.$PEN_Inc2.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_24.'">All Pending / Overdue</a></span>';
					echo '<small style="float:right;padding: 5px;background:#008000; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_24.'"> '.$PEN_Inc1.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_27.'">Overdue Security</a></span>';
					echo '<small style="float:right;padding: 5px;background:#D9534F;padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_27.'"> '.$PEN_Inc4.'</a></small>';
				echo '</li>';
				
				echo '<li style="border-bottom: #CFD3D8 1px inset;">';
					echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
					echo '<span class="text"><a style="color:black; text-decoration:none; cursor:pointer;" target="blank" href="'.$urlID_26.'">Pending Security</a></span>';
					echo '<small style="float:right;padding: 5px;background:#008000; padding-left: 10px;padding-right: 10px; width: 40px; font-size: 13px;" class="label label-primary"><a style="text-decoration:none; color:white; cursor:pointer;" target="blank" href="'.$urlID_26.'"> '.$PEN_Inc3.'</a></small>';
				echo '</li>';
				
			echo '</ul>';
		echo '</div>';
		echo '</div>'; 
		
	echo '</section>';
	
echo '</div>';
echo '</section>';
echo '</aside>';
echo '</div>';

include 'footer.php'; 
?>