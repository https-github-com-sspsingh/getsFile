<?PHP

	include_once '../includes.php';
        
	$request  = isset($_POST['request'])	?	$_POST['request']	:	'' ;
	$ID  	   = isset($_POST['ID']) 	  	 ?	$_POST['ID'] 	 	 :	'' ;
	
	$data = array();
	$count = 0;

	if($request == 'SpareRows')
	{
 		$Qry = $login->DB->prepare("UPDATE spare_regis_dtl SET hiddenID = 1, statusID = 1 WHERE recID = ".$ID." ");
		if($Qry->execute())
		{
			$data = array('Status'=>'1','Counts'=>$count);
		}
		else
		{
			$data = array('Status'=>'0','Counts'=>'5','Msg'=>'error In Code !.');
		}
	}
	
	if($request == 'Shift_Setter_Log')
	{
		$arrayST = ($ID > 0 ? $login->select('shift_masters',array("stypeID"), " WHERE usedBY = 'A' AND ID = ".$ID." ") : '');
		
 		$Qry = $login->DB->prepare("UPDATE shift_masters SET statusID = 0 WHERE usedBY = 'A' AND ID = ".$ID." ");
		if($Qry->execute())
		{
			$Qry = $login->DB->prepare("UPDATE shift_masters_dtl SET statusID = 0 WHERE usedBY = 'A' AND ID = ".$ID." ");
			$Qry->execute();
			
			$data = array('Status'=>'1','Counts'=>$count,'Msg'=>'Selected records have been deleted from the database ');
		}
		else
		{
			$data = array('Status'=>'0','Counts'=>'5','Msg'=>'error In Code !.');
		}
	}
	
	if($request == 'Import_Sheet_Log')
	{
		if($login->dateFormat($ID) >= date('Y-m-d'))
		{
			$SNCount = $Index->count_rows('imp_shift_daily'," WHERE statusID = 1 AND dateID = '".$login->dateFormat($ID)."' AND companyID = ".$_SESSION[$login->website]['compID']." ");
			$SNCount = $SNCount > 0 ? $SNCount : 0 ;
			
			if($SNCount > 0)
			{
				$data = array('Status'=>'1','Counts'=>$SNCount,'Msg'=>'enterted date : '.$ID.' , This Daily Import Sheet cannot be deleted as Sign On process is going on !.');
			}
			else
			{
				$count += $Index->count_rows('imp_shift_daily'," WHERE dateID = '".$login->dateFormat($ID)."' AND companyID = ".$_SESSION[$login->website]['compID']." ");
				
				if($count > 0)
				{
					if($Index->delete('imp_shift_daily'," WHERE dateID = '".$login->dateFormat($ID)."' AND companyID = ".$_SESSION[$login->website]['compID']." "))
					{
						$data = array('Status'=>'1','Counts'=>$count,'Msg'=>'enterted date : '.$ID.' records deleted from database !.');
					}
					else
					{
						$data = array('Status'=>'0','Counts'=>'5','Msg'=>'error In Code !.');
					}
				}
				else
				{
					$data = array('Status'=>'0','Counts'=>'5','Msg'=>'no records are available for given date : '.$ID.' ');
				}
			}
		}
		else
		{
			$data = array('Status'=>'0','Counts'=>'5','Msg'=>'You cann\'t delete the records from back date!.');
		}
	}
	
	if($request == 'mechanic_mst')
	{
            if($Index->delete('mechanic_mst_dtl'," WHERE ID = '".$ID."' "))
            {
                $Index->delete('mechanic_mst'," WHERE ID = '".$ID."' ");
                $data = array('Status'=>'1','Counts'=>$count);
            }
	}
        
	if($request == 'DELETE_MECHANIC_ROW')
	{
		$arrTL = ($ID > 0 ? $login->select('mechanic_mst',array("*"), " WHERE recID = ".$ID." ") : '');
		
		if($Index->delete('mechanic_mst'," WHERE recID = '".$ID."' "))
		{
			
			$Index->PUSH_userlogsID(82,$ID,'',$arrTL[0]['empID'],'','','D',$arrTL[0]['empID'],$arrTL);
			
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'imp_hastus')
	{
		if($Index->delete('imp_hastus'," WHERE recID = '".$ID."' "))
		{
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'rbuilder')
	{
		if($Index->delete('rbuilder'," WHERE ID = '".$ID."' "))
		{
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
        
	if($request == 'spare_regis')
	{
		if($Index->delete('spare_regis_dtl'," WHERE ID = '".$ID."' "))
		{
			$Index->PUSH_userlogsID('82',$ID,'','','','','D');
			
			$Index->delete('spare_regis'," WHERE ID = '".$ID."' ");
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}

	if($request == 'cstpoint' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('inspc',array("ID"), " WHERE srtpointID In(".$_POST['frmID'].") ") : '');
		  
		if($arrTL[0]['ID'] > 0)
		{
			$data = array('Status'=>'0','Counts'=>$arrTL[0]['ID'],'Msg'=>'This Record already linked with Inspection !.');
		}
		else
		{
			$Index->delete('cstpoint_dtl'," WHERE ID = '".$ID."' ");
			$Index->delete('cstpoint'," WHERE ID = '".$ID."' ");
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'imp_shifts' && !empty($ID))
	{
		if($Index->delete('imp_shifts'," WHERE dateID = '".$login->dateFormat($ID)."' "))
		{
			//$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'frm_fields' && !empty($ID))
	{
		if($Index->delete('frm_fields'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}

	if($request == 'PrkPermits_rows' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('prpermits_dtl',array("*"), " WHERE recID = ".$ID." ") : '');
		if($Index->delete('prpermits_dtl'," WHERE recID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D',$arrTL[0]['fileID_1'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}

	if($request == 'Accident_rows' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('accident_regis_dtl',array("*"), " WHERE recID = ".$ID." ") : '');
		if($Index->delete('accident_regis_dtl'," WHERE recID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D',$arrTL[0]['fieldID_2'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'suburbs' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('master',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('suburbs'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D',$arrTL[0]['title'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'stfare_regis')
	{
		$arrTL = ($ID > 0 ? $login->select('stfare_regis',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('stfare_regis'," WHERE ID = '".$ID."' "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],'','',$arrTL[0]['shiftNO'],'D',$arrTL[0]['cmrrefNO'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'hiz_regis' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('hiz_regis',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('hiz_regis'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['fstaffID'],$arrTL[0]['fscodeID'],$arrTL[0]['refno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'sir_regis' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('sir_regis',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('sir_regis'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['originatorID'],'',$arrTL[0]['refno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'mng_cmn' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('mng_cmn',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('mng_cmn'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['staffID'],$arrTL[0]['scodeID'],'','D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
        
	if($request == 'incident_regis' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('incident_regis',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('incident_regis'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['driverID'],$arrTL[0]['dcodeID'],$arrTL[0]['refno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}

	if($request == 'inspc' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('inspc',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('inspc'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['empID'],$arrTL[0]['ecodeID'],$arrTL[0]['rptno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'master' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('master',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('master'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D',$arrTL[0]['title'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'sicklv' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('sicklv',array("*"), " WHERE ID = ".$ID." ") : '');
		
		if($Index->delete('sicklv'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['sldateID'],$arrTL[0]['empID'],$arrTL[0]['ecodeID'],'','D',$arrTL[0]['reason'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'complaint' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('complaint',array("*"), " WHERE ID = ".$ID." ") : '');
		
		if($Index->delete('complaint'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['driverID'],$arrTL[0]['dcodeID'],$arrTL[0]['refno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'infrgs' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('infrgs',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('infrgs'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['staffID'],$arrTL[0]['stcodeID'],$arrTL[0]['refno'],'D',($arrTL[0]['description'].''.$arrTL[0]['description_1']),$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'accident_regis' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('accident_regis',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('accident_regis'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,$arrTL[0]['dateID'],$arrTL[0]['staffID'],$arrTL[0]['scodeID'],$arrTL[0]['refno'],'D',$arrTL[0]['description'],$arrTL);
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
	
	if($request == 'prpermits' && !empty($ID))
	{
		$arrTL = ($ID > 0 ? $login->select('prpermits',array("*"), " WHERE ID = ".$ID." ") : '');
		if($Index->delete('prpermits'," WHERE ID = ".$ID." "))
		{
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D','',$arrTL);
			
			$Index->delete('prpermits_dtl'," WHERE ID = ".$ID." ");			
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
		
	if($request == 'employee' && !empty($ID))
	{
		$count += $Index->count_rows('accident_regis'," WHERE staffID = ".$ID." ");
		$count += $Index->count_rows('complaint'," WHERE driverID = ".$ID." ");
		$count += $Index->count_rows('incident_regis'," WHERE staffID = ".$ID." ");
		$count += $Index->count_rows('infrgs'," WHERE staffID = ".$ID." ");
		$count += $Index->count_rows('inspc'," WHERE empID = ".$ID." ");
		
		if($count > 0)	{$data = array('Status'=>'0','Counts'=>$count,'Msg'=>'This Record already linked with master !.');}
		else			  
		{
			$arrTL = ($ID > 0 ? $login->select('employee',array("*"), " WHERE ID = ".$ID." ") : '');
			
			if($Index->delete('employee'," WHERE ID = ".$ID." "))
			{ 
				$Index->PUSH_userlogsID($_POST['frmID'],$ID,'',$ID,$arrTL[0]['code'],'','D',($arrTL[0]['address_1'].' '.$arrTL[0]['address_2']),$arrTL);
				$data = array('Status'=>'1','Counts'=>$count);
			}
		}
	}
	
	if($request == 'stype' && !empty($ID))
	{
		$count = $Index->count_rows('shifts'," WHERE stypeID = ".$ID." ");
		
		if($count > 0)	{$data = array('Status'=>'0','Counts'=>$count,'Msg'=>'This Record already linked with master !.');}
		else			  {if($Index->delete('stype'," WHERE ID = ".$ID." "))	{
                    $Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
                    $data = array('Status'=>'1','Counts'=>$count);}}
	}
	
	if($request == 'shifts' && !empty($ID))
	{
		$count = $Index->count_rows('w_shifts_dtl'," WHERE shiftID = ".$ID." ");
		
		if($count > 0)	{$data = array('Status'=>'0','Counts'=>$count,'Msg'=>'This Record already linked with master !.');}
		else			  {if($Index->delete('shifts'," WHERE ID = ".$ID." "))	{
                    $Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
                    $data = array('Status'=>'1','Counts'=>$count);}}
	}
	
	if($request == 'w_shifts' && !empty($ID))
	{
		if($Index->delete('w_shifts'," WHERE ID = ".$ID." "))
		{
			$Index->delete('w_shifts_dtl'," WHERE ID = ".$ID." ");
			$Index->delete('w_shifts_grader'," WHERE reqID = ".$ID." ");
			$Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
                        
			$data = array('Status'=>'1','Counts'=>$count);
		}
	}
        
        if($request == 'urole' && !empty($ID))
        {
            $count = $Index->count_rows('users'," WHERE uroleID = ".$ID." ");
		
            if($count > 0)	
            {
                $data = array('Status'=>'0','Counts'=>$count,'Msg'=>'This Record already linked with master !.');
            }
            else			  
            {
                if($Index->delete('urole'," WHERE ID = ".$ID." "))
                {
                    $Index->PUSH_userlogsID($_POST['frmID'],$ID,'','','','','D');
                    $Index->delete('urole_dtl'," WHERE ID = ".$ID." ");
                    $data = array('Status'=>'1','Counts'=>$count);
                }
            }
        }
	
	echo json_encode($data);	
?>