<?PHP
	include_once '../includes.php';	
	$index = new Functions();
	
	$request	 = isset($_POST['request'])			   ?	$_POST['request']	    : '' ;	
	$timeID_1	= isset($_POST['timeID_1'])			  ?	$_POST['timeID_1']	   : '' ;	
	$timeID_2	= isset($_POST['timeID_2'])			  ?	$_POST['timeID_2']	   : '' ;
	
	$hoursID	 = isset($_POST['hoursID'])			   ?	$_POST['hoursID']	    : '' ;	
	$fdayIID	 = isset($_POST['fdayIID'])			   ?	$_POST['fdayIID']	    : '' ;	
	$tdayIID	 = isset($_POST['tdayIID'])			   ?	$_POST['tdayIID']	    : '' ;	
	$reqID	   = isset($_POST['reqID'])			     ?	$_POST['reqID']	      : '' ;
	
	if($request == 'GenerateWeeklyScheduler' && ($timeID_1 <> '') && ($timeID_2 <> '') && ($reqID > 0))
	{
		$file = '';
		$styleID = 'style="color:white;background:#317299;text-align:center;"';
		
		$dateID_1 = $index->dateFormat($timeID_1);
		$dateID_2 = $index->dateFormat($timeID_2);
		$diffID = strtotime($dateID_2) - strtotime($dateID_1);
		$lasID = floor($diffID / (60 * 60 * 24)) + 1;
		
		if($lasID == 7)
		{
		
		  $file .= '<br /><br /><table id="dataTables" class="table table-bordered table-striped">';				
		  $file .= '<thead><tr>';
			  $file .= '<th '.$styleID.' colspan="4">&nbsp;</th>';		  
			  $file .= '<th '.$styleID.' colspan="3">Half - 1</th>';		  
			  $file .= '<th '.$styleID.' colspan="3">Half - 2</th>';		  
			  $file .= '<th '.$styleID.' colspan="2">&nbsp;</th>'; 
		  $file .= '</tr></thead>';
		  
		  $file .= '<thead><tr>';
			  $file .= '<th '.$styleID.' rowspan="2">Sr. No.</th>';
			  $file .= '<th '.$styleID.'>Date</th>';
			  $file .= '<th '.$styleID.'>Day</th>';
			  $file .= '<th '.$styleID.'>Shift No</th>';		  
			  $file .= '<th '.$styleID.'>Sign On</th>';
			  $file .= '<th '.$styleID.'>Sign Out</th>';
			  $file .= '<th '.$styleID.'>Hours</th>';		  
			  $file .= '<th '.$styleID.'>Sign On</th>';
			  $file .= '<th '.$styleID.'>Sign Out</th>';
			  $file .= '<th '.$styleID.'>Hours</th>';		  
			  $file .= '<th '.$styleID.'>Total Hours</th>';		  
			  $file .= '<th '.$styleID.'>&nbsp;</th>';
		  $file .= '</tr></thead>';
		  
		  $SET_1 = 'style="color:white; background-color:#317299;"';
		  
		  $Qry = $index->DB->prepare("SELECT * FROM employee WHERE status <> 3 AND status <> 4 Order By code ASC ");
		  $Qry->execute();
		  $index->result = $Qry->fetchAll(PDO::FETCH_ASSOC);
		  if(is_array($index->result) && (count($index->result) > 0))
		  {
			  $counterID = 1;
			  $spaceID = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			  foreach($index->result as $rows)
			  { 
			  	  $SH_Array = $index->select('shifts',array("*"), " WHERE srID = ".($counterID)." AND stypeID = ".$reqID." ");
				  
				  if($SH_Array[0]['ID'] > 0)
				  {
				  	$file .= '<tr>';
				  $file .= '<td colspan="12"><b>Employee : '.$spaceID.$rows['fname'].' '.$rows['lname'].'</b></td>';
				  $file .= '</tr>';
				  
				  $cuID = 0;
				  $totID_1 = '';	$totID_2 = '';	$totID_3 = '';
				  $totID_4 = '';	$totID_5 = '';	$totID_6 = '';
				  $totID_7 = '';
				  for($srID = 0; $srID < $lasID; $srID++)
				  {
					  $dateID = date('d-m-Y',strtotime($dateID_1.'+'.$srID.'Days'));
					  $dayID  = date('l',strtotime($dateID_1.'+'.$srID.'Days'));
					  
					  $cuID++;
					  if($dayID <> 'Saturday' && $dayID <> 'Sunday')
					  {   
						  /* START PUSH - UP - DATA */
						  $file .= '<tr>';
						  	$file .= '<input type="hidden" name="empID[]" value="'.$rows['ID'].'" />';
							$file .= '<input type="hidden" name="segID[]" value="'.$counterID.'" />';
							
							$file .= '<input type="hidden" name="srID[]" value="'.$cuID.'" />';
							$file .= '<input type="hidden" name="dateID[]" value="'.date('Y-m-d',strtotime($dateID)).'" />';							
							$file .= '<input type="hidden" name="dayID[]" value="'.$dayID.'" />';							
							$file .= '<input type="hidden" name="shiftID[]" value="'.$SH_Array[0]['ID'].'" />';
							$file .= '<input type="hidden" name="signinID_1[]" value="'.$SH_Array[0]['stime_1'].'" />';
							$file .= '<input type="hidden" name="signoutID_1[]" value="'.$SH_Array[0]['etime_1'].'" />';
							$file .= '<input type="hidden" name="hoursID_1[]" value="'.$SH_Array[0]['hours_1'].'" />';							
							$file .= '<input type="hidden" name="signinID_2[]" value="'.$SH_Array[0]['stime_2'].'" />';
							$file .= '<input type="hidden" name="signoutID_2[]" value="'.$SH_Array[0]['etime_2'].'" />';
							$file .= '<input type="hidden" name="hoursID_2[]" value="'.$SH_Array[0]['hours_2'].'" />';							
							$file .= '<input type="hidden" name="shiftcode[]" value="'.$SH_Array[0]['code'].'" />';
							$file .= '<input type="hidden" name="thoursID[]" value="'.$SH_Array[0]['hours_days'].'" />';							
						  $file .= '</tr>';
						  /* END PUSH - UP - DATA */
						  
						  $file .= '<tr>';
						  $file .= '<td align="center">'.$cuID.'</td>';
						  $file .= '<td align="center">'.$dateID.'</td>';
						  $file .= '<td align="center">'.$dayID.'</td>';
						  $file .= '<td align="center">'.$SH_Array[0]['code'].'</td>';
						  $file .= '<td align="center">'.$SH_Array[0]['stime_1'].'</td>';
						  $file .= '<td align="center">'.$SH_Array[0]['etime_1'].'</td>';
						  $file .= '<td align="center" '.$SET_1.'><b>'.$SH_Array[0]['hours_1'].'</b></td>';
						  $file .= '<td align="center">'.$SH_Array[0]['stime_2'].'</td>';
						  $file .= '<td align="center">'.$SH_Array[0]['etime_2'].'</td>';
						  $file .= '<td align="center" '.$SET_1.'><b>'.$SH_Array[0]['hours_2'].'</b></td>';
						  $file .= '<td align="center" '.$SET_1.'><b>'.$SH_Array[0]['hours_days'].'</b></td>';
						  $file .= '<td align="center"><a class="fa fa-refresh">&nbsp;</a></td>';
						  $file .= '</tr>';
					  }
					  else
					  {
						  /* START PUSH - UP - DATA */
						  $file .= '<tr>';
						  	$file .= '<input type="hidden" name="empID[]" value="'.$rows['ID'].'" />';
							$file .= '<input type="hidden" name="segID[]" value="'.$counterID.'" />';
							
							$file .= '<input type="hidden" name="srID[]" value="'.$cuID.'" />';
							$file .= '<input type="hidden" name="dateID[]" value="'.date('Y-m-d',strtotime($dateID)).'" />';							
							$file .= '<input type="hidden" name="dayID[]" value="'.$dayID.'" />';							
							$file .= '<input type="hidden" name="shiftID[]"/>';
							$file .= '<input type="hidden" name="signinID_1[]"/>';
							$file .= '<input type="hidden" name="signoutID_1[]"/>';
							$file .= '<input type="hidden" name="hoursID_1[]"/>';
							$file .= '<input type="hidden" name="signinID_2[]"/>';
							$file .= '<input type="hidden" name="signoutID_2[]"/>';
							$file .= '<input type="hidden" name="hoursID_2[]"/>';
							$file .= '<input type="hidden" name="shiftcode[]"/>';
							$file .= '<input type="hidden" name="thoursID[]"/>';
						  $file .= '</tr>';
						  /* END PUSH - UP - DATA */ 
					  }
				  }
				  
		/******************** GEN - TOTALS ********************/
		
		/*** TOTAL - HOURS - 1. ***/			
				  list($hrID_5,$minID_5,$secID_5) = explode(':', $SH_Array[0]['hours_1']);				  
				  for($srID = 0; $srID < ($lasID - 2); $srID++)
				  {
					  $setID_5 += $hrID_5*3600;
					  $setID_5 += $minID_5*60;
					  $setID_5 += $secID_5;
				  }
					$set_hrID_5   = floor($setID_5/3600);
					$setID_5 	 -= $set_hrID_5*3600;
					$set_minID_5  = floor($setID_5/60);
					$setID_5 	 -= $set_minID_5*60;
					
		/*** TOTAL - HOURS - 2. ***/			
				  list($hrID_6,$minID_6,$secID_6) = explode(':', $SH_Array[0]['hours_2']);				  
				  for($srID = 0; $srID < ($lasID - 2); $srID++)
				  {
					  $setID_6 += $hrID_6 * 3600;
					  $setID_6 += $minID_6 * 60;
					  $setID_6 += $secID_6;
				  }
					$set_hrID_6   = floor($setID_6 / 3600);
					$setID_6 	 -= $set_hrID_6 * 3600;
					$set_minID_6  = floor($setID_6 / 60);
					$setID_6 	 -= $set_minID_6 * 60;
					
					
		/*** GENEARTE - TOTAL - HOURS. ***/			
				  list($hrID_1,$minID_1,$secID_1) = explode(':', $SH_Array[0]['hours_days']);				  
				  for($srID = 0; $srID < ($lasID - 2); $srID++)
				  {
					  $setID_1 += $hrID_1 * 3600;
					  $setID_1 += $minID_1 * 60;
					  $setID_1 += $secID_1;
				  }
					$set_hrID_1   = floor($setID_1 / 3600);
					$setID_1 	 -= $set_hrID_1 * 3600;
					$set_minID_1  = floor($setID_1 / 60);
					$setID_1 	 -= $set_minID_1 * 60;
				  
					  $file .= '<tr>';
						  $file .= '<td align="right" colspan="6"><b>Employee Wise Totals : </b>&nbsp;&nbsp;&nbsp;</td>';
						  $file .= '<td align="center"><b>'.$set_hrID_5.':'.$set_minID_5.'</b></td>'; 
						  $file .= '<td colspan="2"><b></b></td>';
						  $file .= '<td align="center"><b>'.$set_hrID_6.':'.$set_minID_6.'</b></td>';
						  $file .= '<td align="center"><b>'.$set_hrID_1.':'.$set_minID_1.'</b></td>';
						  $file .= '<td><b></b></td>';
					  $file .= '</tr>';	
				  }
				  else
				  {
					$file .= '<tr><td colspan="12"><b style="color:red;">Sorry, No Shifts Data Are Available As Per Your Specifications. !...</b></td></tr>';					
					break;
				  }
				  $counterID++;
			  }
		  }
		  
		  $file .= '</table>';
		}
		else	{$file .= '<b style="color:red;">Date Range Is Valid For Only 7 Days !...</b>';}
		
		$data['result'] = $file;
	}
	
	echo json_encode($data);
?>