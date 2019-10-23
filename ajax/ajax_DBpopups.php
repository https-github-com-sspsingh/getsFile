<?PHP
	include_once '../includes.php';
	
	if($_POST['request'] == 'INSERT_PENDING_SHIFTS')
	{
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
		
		$explodeID = explode("_",$reqID);
		
		$shiftID		= $explodeID[0];
		$shift_recID	= $explodeID[1];
		$tagCD			= $explodeID[2];
		$dateID			= $explodeID[3];
		
		if($shiftID > 0 && $shift_recID > 0 && $dateID <> '' && $tagCD <> '')
		{
			$arrSH = $login->select('shift_masters_dtl',array("*"), " WHERE recID = ".$shift_recID." ");
			
			if($tagCD == 'A')
			{
				$array_A = array();
				$array_A['usedBY']  = $arrSH[0]['usedBY'];
				$array_A['dateID']  = $login->dateFormat($dateID);
				$array_A['companyID'] = $_SESSION[$login->website]['compID'];
				$array_A['tagCD']   = 'A';
				$array_A['fID_1']   = trim($arrSH[0]['fID_1']);
				$array_A['fID_13']  = '';
				$array_A['fID_013'] = '';
				$array_A['fID_14']  = '';
				$array_A['fID_4']   = '';
				$array_A['cuttoffID'] = 0;
				$array_A['statusID']  = 2;
				$array_A['fID_6']     = '';
				$array_A['shiftID']   = ($arrSH[0]['ID']);
				$array_A['shift_recID']  = ($arrSH[0]['recID']);
				$array_A['imp_statusID'] = ($arrSH[0]['ID'] > 0 ? 1 : 2);
				$array_A['status_ynID']  = 1;
				$array_A['logID'] = date('Y-m-d H:i:s');
				$login->BuildAndRunInsertQuery('imp_shift_daily',$array_A);
			}
			
			if($tagCD == 'B')
			{
				$array_B = array();
				$array_B['usedBY']  = $arrSH[0]['usedBY'];
				$array_B['dateID']  = $login->dateFormat($dateID);
				$array_B['companyID'] = $_SESSION[$login->website]['compID'];
				$array_B['tagCD']   = 'B';
				$array_B['fID_1']   = trim($arrSH[0]['fID_1']);
				$array_B['fID_13']  = '';
				$array_B['fID_013'] = '';
				$array_B['fID_14']  = '';
				$array_B['fID_4']   = '';
				$array_B['cuttoffID'] = 0;
				$array_B['statusID']  = 2;
				$array_B['fID_6']     = '';
				$array_B['shiftID']   = ($arrSH[0]['ID']);
				$array_B['shift_recID']  = ($arrSH[0]['recID']);
				$array_B['imp_statusID'] = ($arrSH[0]['ID'] > 0 ? 1 : 2);
				$array_B['status_ynID']  = 1;
				$array_B['logID'] = date('Y-m-d H:i:s');
				$login->BuildAndRunInsertQuery('imp_shift_daily',$array_B);
			}
		}
	}
	
	if($_POST['request'] == 'UPDATE_CHOPPED' && $_POST['recID'] > 0)
	{
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
		
		if($recID > 0)
		{
			$arr = array();
			$arr['choppedID'] = 1;
			$on['recID'] = $recID;
			$Index->BuildAndRunUpdateQuery('imp_shift_daily',$arr,$on);                    
		}
		echo json_encode(array('success'=>TRUE));
	}
	
	if($_POST['request'] == 'UNDO_CHOPPED' && $_POST['recID'] > 0)
	{
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
		
		if($recID > 0)
		{
			$arr = array();
			$arr['choppedID'] = 0;
			$on['recID'] = $recID; 
			$Index->BuildAndRunUpdateQuery('imp_shift_daily',$arr,$on);                    
		}
		echo json_encode(array('success'=>TRUE));
	}
	
	if($_POST['request'] == 'UPDATE_SHIFT_CUTTOFF' && $_POST['recID'] > 0)
	{
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

		if($recID > 0)
		{
			$arrID = $recID > 0 ? $login->select('shift_masters_dtl',array("*"), " WHERE recID = ".$recID." ") : '';
			
			$arr = array();
			$arr['tickID'] = ($arrID[0]['tickID'] == 1 ? 0 : 1);
			$on['recID'] = $recID; 
			$Index->BuildAndRunUpdateQuery('shift_masters_dtl',$arr,$on);                    
		}
		
		echo json_encode(array('success'=>TRUE));
	}
	
	if($_POST['request'] == 'UPDATE_API_STATUS')
	{
            extract($_POST);
            
            if($API_1_statusID > 0)
            {
                $arr_1 = array();
                $arr_1['dateID'] = date('Y-m-d');
                $arr_1['timeID'] = date('h : i : A');
                $arr_1['userID'] = $_SESSION[$login->website]['userID'];
                $arr_1['companyID'] = $_SESSION[$login->website]['compID'];
                $arr_1['refID'] = $recID;
                $arr_1['refNO'] = $refNO;
                $arr_1['statusID'] = ($API_1_statusID == 1 ? 'SUCCESS' : 'FAILED');
                $arr_1['typeID'] = 'INCIDENT';
                $Index->BuildAndRunInsertQuery('api_senders',$arr_1);
            }
            
            if($API_2_statusID > 0)
            {
                $arr_2 = array();
                $arr_2['dateID'] = date('Y-m-d');
                $arr_2['timeID'] = date('h : i : A');
                $arr_2['userID'] = $_SESSION[$login->website]['userID'];
                $arr_2['companyID'] = $_SESSION[$login->website]['compID'];
                $arr_2['refID'] = $recID;
                $arr_2['refNO'] = $refNO;
                $arr_2['statusID'] = ($API_2_statusID == 1 ? 'SUCCESS' : 'FAILED');
                $arr_2['typeID'] = 'OFFENCE';
                $Index->BuildAndRunInsertQuery('api_senders',$arr_2);
            }
            
            echo json_encode(array('success'=>0));
	}
	
	if($_POST['request'] == 'UPDATE_MASTERS_FIELDS' && $_POST['fieldNM'] <> '1')
	{
            extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

            if($recID > 0)
            {
                $arr_A = array();

                if($fieldNM == '2') {$arr_A['fID_4'] = $valueTEXT;}
                if($fieldNM == '3') {$arr_A['fID_6'] = $valueTEXT;}

                $on_A['recID'] = $recID; 
                $Index->BuildAndRunUpdateQuery('imp_shift_daily',$arr_A,$on_A);                    
            }

            echo json_encode(array('success'=>TRUE));
	}
        
	if($_POST['request'] == 'UPDATE_MASTERS_FIELDS' && $_POST['fieldNM'] == '1')
	{
		extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

		if($recID > 0 && $valueTEXT <> '')
		{
			$arrIMP = $recID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$recID." ") : '';
			
			$recID_B = 0;                
			if($arrIMP[0]['tagCD'] == 'A')
			{
				$arrSHF = $arrIMP[0]['shift_recID'] > 0 ? $login->select('shift_masters_dtl',array("*"), " WHERE recID = ".$arrIMP[0]['shift_recID']." ") : '';
				if($arrSHF[0]['fID_019'] == 'N')
				{
					$arrB = ($arrIMP[0]['fID_1'] <> '' ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$arrIMP[0]['fID_1']."' AND recID > ".$recID." AND shift_recID = ".$arrIMP[0]['shift_recID']." AND tagCD = 'B' ") : '');
					$recID_B = ($arrB[0]['recID'] > 0  ? $arrB[0]['recID'] : 0);
				}
			}
			
			if($recID > 0)
			{
				$arr_A = array();
				$arr_A['fID_14'] = $valueTEXT;
				$on_A['recID'] = $recID; 
				$Index->BuildAndRunUpdateQuery('imp_shift_daily',$arr_A,$on_A);
			}
			
			if($recID_B > 0)
			{
				$arr_B = array();
				$arr_B['fID_14'] = $valueTEXT;
				$on_B['recID']   = $recID_B; 
				$Index->BuildAndRunUpdateQuery('imp_shift_daily',$arr_B,$on_B);
			}
		}

		echo json_encode(array('success'=>TRUE));
	}
        
	if($_POST['request'] == 'Update_Users_Permissions')
	{
            extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

            if(is_array($code) and count($code) > 0 && ($userID > 0))
            {
                $array = array();
                $array['lgtypeID'] = 2;
                if(count($spermissionID) > 0)
                {
                    $array['spermissionID'] = implode(",",$spermissionID);
                }
                $array['tdateID'] = $login->dateFormat($tdateID);
                $on['ID'] = $userID; 
                if($Index->BuildAndRunUpdateQuery('users',$array,$on))
                {
                    $Index->delete('users_dtl', " WHERE ID = ".$userID." ");

                    foreach($code as $form)
                    {	
                            $nopID  = 0;
                            $nopID  = (((isset($_POST[$form.'-del'])  ? $_POST[$form.'-del']  : 0) == 0) && 
                                        ((isset($_POST[$form.'-edit']) ? $_POST[$form.'-edit'] : 0) == 0) && 
                                        ((isset($_POST[$form.'-add'])  ? $_POST[$form.'-add']  : 0) == 0) && 
                                        ((isset($_POST[$form.'-view']) ? $_POST[$form.'-view'] : 0) == 0) && 
                                        ((isset($_POST[$form.'-all'])  ? $_POST[$form.'-all']  : 0) == 0) 
                                        ? 1: 0);

                            if($form > 0)
                            {
                                $arrs = array();
                                $arrs['ID'] 	 = $userID;
                                $arrs['frmID']  = $form;
                                $arrs['addID']  = (isset($_POST[$form.'-add'])	 ?	$_POST[$form.'-add']	 :	0);
                                $arrs['editID'] = (isset($_POST[$form.'-edit'])	?	$_POST[$form.'-edit']	:	0);
                                $arrs['delID']  = (isset($_POST[$form.'-del'])	 ?	$_POST[$form.'-del']	 :	0);
                                $arrs['viewID'] = (isset($_POST[$form.'-view'])	?	$_POST[$form.'-view']	:	0);
                                $arrs['allID']  = (isset($_POST[$form.'-all'])	 ?	$_POST[$form.'-all']	 :	0);
                                $arrs['noID']   = $nopID;
                                $Index->BuildAndRunInsertQuery('users_dtl',$arrs);	
                            }
                    }

                    if($userID > 0)
                    {
                        $Index->delete('users_dtl', " WHERE ID = ".$userID." AND (addID = 0 AND editID = 0 AND delID = 0 AND viewID = 0) ");
                    }
                }
            }

            echo json_encode(array('success'=>TRUE));
	}
	
	if($_POST['request'] == 'Accidents_Register_Insert')
	{
            extract($_POST);
            
            /* DELETE - DATA */
            $Index->delete('accident_regis_dtl', " WHERE ID = ".$reqID." ");
            
            /* INSERT - DATA */
            if(is_array($fieldID_1) && count($fieldID_1) > 0)
            {
                foreach ($fieldID_1 as $key=>$fieldID)
                {
                    if(!empty($fieldID) && ($fieldID <> ''))
                    {
                        $array = array();
                        $array['ID'] = $reqID;
                        $array['fieldID_1'] = $Index->dateFormat($fieldID);
                        $array['fieldID_2'] = $fieldID_2[$key];
                        $Index->BuildAndRunInsertQuery('accident_regis_dtl',$array);
                    }
                }
                
                echo json_encode(array('success'=>TRUE));
            }            
	}
	
	if($_POST['request'] == 'UPDATE_TEMP_EMPLOYEES')
	{
		extract($_POST);					//echo '<pre>';	echo print_r($_POST);	exit;

		$returnID = 0;
		$arraySP  = $dateID <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$dateID."' AND companyID = ".$companyID." ") : '';

		$arrayEM  = $temp_empID > 0 ? $login->select('employee',array("*"), " WHERE ID = ".$temp_empID." ") : '';
		$arraySB  = $arrayEM[0]['sid'] > 0 ? $login->select('suburbs',array("*"), " WHERE ID = ".$arrayEM[0]['sid']." ") : '';

		/* make master of spares */
		$spare_MASTERID = 0;
		if($arraySP[0]['ID'] > 0)
		{
			$spare_MASTERID = $arraySP[0]['ID'];
		}
		else
		{
			/* INSERT - SPARE - DATA */
			$Insert = array();
			$Insert['dateID'] = date('Y-m-d');
			$Insert['timeID'] = date('h : i : A');
			$Insert['companyID'] = $companyID;
			$Insert['hiddenID']  = 1;
			$Insert['userID']  = $_SESSION[$login->website]['userID'];
			$Insert['logID']   = date('Y-m-d H:i:s');
			if($Index->BuildAndRunInsertQuery('spare_regis',$Insert))
			{
				$stmt = $Index->DB->query("SELECT LAST_INSERT_ID()");
				$lastID = $stmt->fetch(PDO::FETCH_NUM);

				$spare_MASTERID = $lastID[0];
			}
		}
		/* ends - make master of spares */

		if($temp_empID > 0 && $spare_MASTERID > 0)
		{
			/* INSERT - DATA */
			$fields = array();
			$fields['fieldID_1'] = $temp_empID;
			$fields['fieldID_2'] = $arrayEM[0]['phone'];
			$fields['fieldID_3'] = $arrayEM[0]['phone_1'];
			$fields['fieldID_4'] = $arrayEM[0]['address_1'];
			$fields['fieldID_5'] = $arraySB[0]['title'].' - '.$arraySB[0]['pscode'];
			$fields['fieldID_6'] = $temp_timeID;
			$fields['fieldID_8'] = ($temp_avaiableID > 0  ? $temp_avaiableID : 0);
			$fields['forID']     = '1';
			$fields['statusID']  = '0';
			$fields['hiddenID']  = 1;
			$fields['ID'] = $spare_MASTERID;
			if($Index->BuildAndRunInsertQuery('spare_regis_dtl',$fields))
			{
				$lastID = $login->select('spare_regis_dtl',array("*"), " Order By recID DESC LIMIT 1 ");

				/* UPDATE - SHIFTS */				
				$update = array();
				$update['fID_18'] = $arrayEM[0]['code'];
				$update['fID_018'] = $temp_empID;
				$on['recID'] = $spareID;
				if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$update,$on))	  
				{
					$returnID += 1;

					$Insert = array();
					$Insert['ID'] = $spareID;
					$Insert['dateID'] = date('Y-m-d');
					$Insert['timeID'] = date('h : i : A');
					$Insert['fromID'] = 'DESKTOP';
					$Insert['forID']  = 'UPDATE_SPARE_NEW_EMPLOYEE';
					$Insert['userID'] = $_SESSION[$login->website]['userID'];
					$Index->BuildAndRunInsertQuery('signon_logs',$Insert);
				}

				/* UPDATE - SPARES*/
				$arr = array();
				$arr['statusID'] = 1;
				$ons['recID'] = $lastID[0]['recID'];
				if($Index->BuildAndRunUpdateQuery('spare_regis_dtl',$arr,$ons))	{$returnID += 1;}
				
				if($statusID == 2)
				{
						$swapSH = $spareID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$spareID." ") : '';

						$tagB = $swapSH[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$swapSH[0]['fID_1']."' 
						AND dateID = '".$swapSH[0]['dateID']."' AND companyID = ".$swapSH[0]['companyID']." AND tagCD = 'B' AND cuttoffID <>  1 ") : '';

						if($tagB[0]['recID'] > 0)
						{
								$fields_B = array();
								$fields_B['fID_18']  = $arrayEM[0]['code'];
								$fields_B['fID_018'] = $temp_empID;
								$ons_B['recID'] = $tagB[0]['recID'];
								if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields_B,$ons_B))
								{
									$Insert = array();
									$Insert['ID'] = $tagB[0]['recID'];
									$Insert['dateID'] = date('Y-m-d');
									$Insert['timeID'] = date('h : i : A');
									$Insert['fromID'] = 'DESKTOP';
									$Insert['forID']  = 'UPDATE_SPARE_NEW_EMPLOYEE (B PART)';
									$Insert['userID'] = $_SESSION[$login->website]['userID'];
									$Index->BuildAndRunInsertQuery('signon_logs',$Insert);
								}
						} 
				}

			}

				if($returnID >= 1)	{echo json_encode(array('success'=>1));}
				else				  {echo json_encode(array('success'=>0));}
		}
		else	{echo json_encode(array('success'=>0));}
	}
        
	if($_POST['request'] == 'UPDATE_TEMP_BUSES')
	{
		extract($_POST);		//echo '<pre>';	echo print_r($_POST);	exit;

		$returnID = 0;
		$arraySP  = $dateID <> '' && $companyID > 0 ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$dateID."' AND companyID In(".$companyID.") ") : '';

		/* make master of spares */
		$spare_MASTERID = 0;
		if($arraySP[0]['ID'] > 0)
		{
			$spare_MASTERID = $arraySP[0]['ID'];
		}
		else
		{
			/* INSERT - SPARE - DATA */
			$Insert = array();
			$Insert['dateID'] = date('Y-m-d');
			$Insert['timeID'] = date('h : i : A');
			$Insert['companyID'] = $companyID;
			$Insert['hiddenID']  = 1;
			$Insert['userID']  = $_SESSION[$login->website]['userID'];
			$Insert['logID']   = date('Y-m-d H:i:s');
			if($Index->BuildAndRunInsertQuery('spare_regis',$Insert))
			{
				$stmt = $Index->DB->query("SELECT LAST_INSERT_ID()");
				$lastID = $stmt->fetch(PDO::FETCH_NUM);

				$spare_MASTERID = $lastID[0];
			}
		}
		/* ends - make master of spares */

		if($temp_busID > 0 && $spare_MASTERID > 0)
		{
			/* INSERT - DATA */
			$fields = array();
			$fields['fieldID_1'] = $temp_busID;
			$fields['forID']	 = '2';
			$fields['statusID']  = '0';
			$fields['hiddenID']  = 1;
			$fields['ID'] = $spare_MASTERID;
			if($Index->BuildAndRunInsertQuery('spare_regis_dtl',$fields))
			{
				$lastID = $login->select('spare_regis_dtl',array("*"), " Order By recID DESC LIMIT 1 ");

				/* UPDATE - SHIFTS */				
				$update = array();
				$update['fID_014'] = $temp_busID;
				$on['recID'] = $spareID;
				if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$update,$on))
				{
					$returnID += 1;

					$Insert = array();
					$Insert['ID'] = $spareID;
					$Insert['dateID'] = date('Y-m-d');
					$Insert['timeID'] = date('h : i : A');
					$Insert['fromID'] = 'DESKTOP';
					$Insert['forID']  = 'UPDATE_SPARE_NEW_BUS';
					$Insert['userID'] = $_SESSION[$login->website]['userID'];
					$Index->BuildAndRunInsertQuery('signon_logs',$Insert);
				}

				/* UPDATE - SPARES*/
				$arr = array();
				$arr['statusID'] = 1;
				$ons['recID'] = $lastID[0]['recID'];
				if($Index->BuildAndRunUpdateQuery('spare_regis_dtl',$arr,$ons))	{$returnID += 1;}

				if($statusID == 2)
				{
					$swapSH = $spareID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$spareID." ") : '';

					$tagB = $swapSH[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$swapSH[0]['fID_1']."' 
					AND dateID = '".$swapSH[0]['dateID']."' AND companyID = ".$swapSH[0]['companyID']." AND tagCD = 'B' AND cuttoffID <>  1 ") : '';

					if($tagB[0]['recID'] > 0)
					{
						$fields_B = array();
						$fields_B['fID_014'] = $temp_busID;
						$ons_B['recID'] = $tagB[0]['recID'];
						if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields_B,$ons_B))
						{
							$Insert = array();
							$Insert['ID'] = $tagB[0]['recID'];
							$Insert['dateID'] = date('Y-m-d');
							$Insert['timeID'] = date('h : i : A');
							$Insert['fromID'] = 'DESKTOP';
							$Insert['forID']  = 'UPDATE_SPARE_NEW_BUS (B-PART)';
							$Insert['userID'] = $_SESSION[$login->website]['userID'];
							$Index->BuildAndRunInsertQuery('signon_logs',$Insert);
						}
					} 
				}

			}

			if($returnID >= 1)	{echo json_encode(array('success'=>1));}
			else				  {echo json_encode(array('success'=>0));}
		}
		else	{echo json_encode(array('success'=>0));}
	}
	
	if($_POST['request'] == 'UNDO_BUSES')
	{
            extract($_POST);

            if($changesID  > 0)
            {
                $Qry = $login->DB->prepare("UPDATE spare_regis INNER JOIN spare_regis_dtl ON spare_regis.ID = spare_regis_dtl.ID SET spare_regis_dtl.statusID = 0
                WHERE DATE(spare_regis.dateID) = '".$dateID."' AND spare_regis_dtl.fieldID_1 = ".$empID." AND spare_regis_dtl.forID = 2 ");
                if($Qry->execute())
                { 
                    $fields = array();
                    $fields['fID_014'] = 0;
                    $ons['recID'] = $recID;
                    if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))
                    {
                        $Insert = array();
                        $Insert['ID'] = $recID;
                        $Insert['dateID'] = date('Y-m-d');
                        $Insert['timeID'] = date('h : i : A');
                        $Insert['fromID'] = 'DESKTOP';
                        $Insert['forID']  = 'UNDO_SPARE_BUS';
                        $Insert['userID'] = $_SESSION[$login->website]['userID'];
                        $Index->BuildAndRunInsertQuery('signon_logs',$Insert);
                    }
                }

                echo json_encode(array('success'=>1));
            }
            else	{echo json_encode(array('success'=>0));}
	}
	
	if($_POST['request'] == 'UNDO_EMPLOYEE')
	{
            extract($_POST);

            if($changesID  > 0)
            {
                $Qry = $login->DB->prepare("UPDATE spare_regis INNER JOIN spare_regis_dtl ON spare_regis.ID = spare_regis_dtl.ID SET spare_regis_dtl.statusID = 0
                WHERE DATE(spare_regis.dateID) = '".$dateID."' AND spare_regis_dtl.fieldID_1 = ".$empID." AND spare_regis_dtl.forID = 1 ");
                if($Qry->execute())
                {
                    //recID
                    $fields = array();
                    $fields['fID_18']  = '';
                    $fields['fID_018'] = 0;
                    $ons['recID'] = $recID;
                    if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))
                    {
                        $Insert = array();
                        $Insert['ID'] = $recID;
                        $Insert['dateID'] = date('Y-m-d');
                        $Insert['timeID'] = date('h : i : A');
                        $Insert['fromID'] = 'DESKTOP';
                        $Insert['forID']  = 'UNDO_SPARE_EMPLOYEE';
                        $Insert['userID'] = $_SESSION[$login->website]['userID'];
                        $Index->BuildAndRunInsertQuery('signon_logs',$Insert);
                    }
                }

                echo json_encode(array('success'=>1));
            }
            else	{echo json_encode(array('success'=>0));}
	}
	
	if($_POST['request'] == 'UPDATE_BUSES')
	{
            extract($_POST);    //echo '<pre>';	echo print_r($_POST);	exit;

            $arraySP  = $spareID > 0  ? $login->select('spare_regis_dtl',array("*"), " WHERE recID = ".$spareID." ")     : '';

            $fields = array();
            $fields['fID_014'] = $empID;
            $ons['recID'] = $shiftsID;
            //echo '<pre>';	echo print_r($fields);	exit;
            if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))
            {
                $Insert = array();
                $Insert['ID'] = $shiftsID;
                $Insert['dateID'] = date('Y-m-d');
                $Insert['timeID'] = date('h : i : A');
                $Insert['fromID'] = 'DESKTOP';
                $Insert['forID']  = 'UPDATE_SPARE_BUS';
                $Insert['userID'] = $_SESSION[$login->website]['userID'];
                $Index->BuildAndRunInsertQuery('signon_logs',$Insert);

                if($statusID == 2)
                {
                    $swapSH = $shiftsID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$shiftsID." ") : '';
                    $tagB = $swapSH[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$swapSH[0]['fID_1']."' AND dateID = '".$swapSH[0]['dateID']."' AND companyID = ".$swapSH[0]['companyID']." AND tagCD = 'B' ") : '';

                    if($tagB[0]['recID'] > 0)
                    {
                        $fields_B = array();
                        $fields_B['fID_014'] = $empID;
                        $ons_B['recID'] = $tagB[0]['recID'];
                        $Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields_B,$ons_B);
                    } 
                }

                /* START - UPDATE - TABLES */
                if($spr_empID > 0)
                {
                    $Qry = $login->DB->prepare("UPDATE spare_regis INNER JOIN spare_regis_dtl ON spare_regis.ID = spare_regis_dtl.ID SET spare_regis_dtl.statusID = 0 WHERE DATE(spare_regis.dateID) = '".$dateID."' AND spare_regis_dtl.fieldID_1 = ".$spr_empID." AND spare_regis_dtl.forID = 2 ");
                    $Qry->execute();
                }
                /* ENDS - UPDATE - TABLES */

                $arr = array();
                $arr['statusID'] = 1;
                $ons['recID'] = $spareID;
                if($Index->BuildAndRunUpdateQuery('spare_regis_dtl',$arr,$ons))
                                {echo json_encode(array('success'=>1));}
                else	{echo json_encode(array('success'=>0));}
            }
            else	{echo json_encode(array('success'=>0));}
	}
	
	if($_POST['request'] == 'UPDATE_EMPLOYEE')
	{
            extract($_POST);

            $arraySP  = $spareID > 0  ? $login->select('spare_regis_dtl',array("*"), " WHERE recID = ".$spareID." ")     : '';
            $arrayEM  = $empID > 0    ? $login->select('employee',array("*"), " WHERE ID = ".$empID." ")     : '';

            $fields = array();
            $fields['fID_18']  = $arrayEM[0]['code'];
            $fields['fID_018'] = $empID;
            $ons['recID'] = $shiftsID;
            if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))
            {
                $Insert = array();
                $Insert['ID'] = $shiftsID;
                $Insert['dateID'] = date('Y-m-d');
                $Insert['timeID'] = date('h : i : A');
                $Insert['fromID'] = 'DESKTOP';
                $Insert['forID']  = 'UPDATE_SPARE_EMPLOYEE ('.$fields['fID_18'].')';
                $Insert['userID'] = $_SESSION[$login->website]['userID'];
                $Index->BuildAndRunInsertQuery('signon_logs',$Insert);

                if($statusID == 2)
                {
                    $swapSH = $shiftsID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$shiftsID." ") : '';
                    $tagB = $swapSH[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$swapSH[0]['fID_1']."' AND dateID = '".$swapSH[0]['dateID']."' AND companyID = ".$swapSH[0]['companyID']." AND tagCD = 'B' ") : '';
					
                    if($tagB[0]['recID'] > 0)
                    {
                        $fields_B = array();
                        $fields_B['fID_18']  = $arrayEM[0]['code'];
                        $fields_B['fID_018'] = $empID;
                        $ons_B['recID'] = $tagB[0]['recID'];
                        $Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields_B,$ons_B);
                    }
                }

                /* START - UPDATE - TABLES */
                if($spr_empID > 0)
                {
                    $Qry = $login->DB->prepare("UPDATE spare_regis INNER JOIN spare_regis_dtl ON spare_regis.ID = spare_regis_dtl.ID SET spare_regis_dtl.statusID = 0 WHERE DATE(spare_regis.dateID) = '".$dateID."' AND spare_regis_dtl.fieldID_1 = ".$spr_empID." AND spare_regis_dtl.forID = 1 ");
                    $Qry->execute();
                }
                /* ENDS - UPDATE - TABLES */

                $arr = array();
                $arr['statusID'] = 1;
                $ons['recID'] = $spareID;
                if($Index->BuildAndRunUpdateQuery('spare_regis_dtl',$arr,$ons))
                                {echo json_encode(array('success'=>1));}
                else	{echo json_encode(array('success'=>0));}
            }
            else	{echo json_encode(array('success'=>0));}
	}
	
	if($_POST['request'] == 'SHIFT_STATUS_CONFIRM')
	{
            extract($_POST);

            $fields = array();
            $fields['singinID'] = date('G:i');
            $fields['statusID'] = ($_POST['stsID'] == 2 ? 1 :($_POST['stsID'] == 1 ? 2 : 2));
			$fields['singinFR'] = 'DESKTOP';
			$fields['singinUS'] = $_SESSION[$login->website]['userID'];			
            $ons['recID'] = $recID;
            if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))	
            {
                $Insert = array();
                $Insert['ID'] = $recID;
                $Insert['dateID'] = date('Y-m-d');
                $Insert['timeID'] = date('h : i : A');
                $Insert['fromID'] = 'DESKTOP';
                $Insert['forID']  = ($fields['statusID'] == 1 ? 'SHIFT_STATUS_CONFIRM' :($fields['statusID'] == 2 ? 'SHIFT_STATUS_UNDO' : ''));
                $Insert['userID'] = $_SESSION[$login->website]['userID'];
                $Index->BuildAndRunInsertQuery('signon_logs',$Insert);

                echo json_encode(array('success'=>1));                   
            }
            else    {echo json_encode(array('success'=>0));} 		
	}
	
	if($_POST['request'] == 'UPDATE_DRIVER_SHIFTS')
	{
            extract($_POST);

            if($_POST['fieldID'] == '13' || $_POST['fieldID'] == '18')
            {
                $arrayID = $Index->select('employee',array("*"), " WHERE ID = ".$_POST['valueID']." ");

                $fields = array();				
                if($_POST['fieldID'] == '18')
                {
                        $fields['fID_18']  = $arrayID[0]['code'];
                        $fields['fID_018'] = $arrayID[0]['ID'];
                }
                else
                {
                        $fields['fID_13']  = $arrayID[0]['code'];
                        $fields['fID_013'] = $arrayID[0]['ID'];
                }
                $ons['recID'] = $_POST['recID'];
                if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))	{echo json_encode(array('success'=>1));}
                else															 {echo json_encode(array('success'=>0));}
            }
            else if($_POST['fieldID'] == '19')
            {
                $fields = array();
                $fields['fID_014'] = $_POST['valueID'];
                $ons['recID'] = $_POST['recID'];
                if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))	{echo json_encode(array('success'=>1));}
                else															 {echo json_encode(array('success'=>0));}
            }
            else
            {
                $fields = array();
                $fields['fID_'.$_POST['fieldID']] = $_POST['valueID'];
                $ons['recID'] = $_POST['recID'];
                if($Index->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$ons))	{echo json_encode(array('success'=>1));}
                else															 {echo json_encode(array('success'=>0));}
            }
	}
      
?>