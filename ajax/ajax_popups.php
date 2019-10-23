<?PHP
	include_once '../includes.php';
        
	$reqID     =    isset($_POST['ID'])         ?   $_POST['ID']        : '' ;
	$request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;
        
	if($request == 'GET_PENDING_SHIFTS')
	{
		$arrID  = $reqID <> '' ? $login->select('imp_shift_daily',array("*"), " WHERE dateID = '".$login->dateFormat($reqID)."' AND companyID In(".$_SESSION[$login->website]['compID'].") ")     : '';
		
		$file = "";
		$file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
		$file .= '<div class="row">&nbsp;</div>';		
		$file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';		

		$file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

		$file .= '<div class="row" style="margin:auto;">';		
		$file .= '<div class="row">';
		
		$file .= '<div class="col-xs-12">';
		
		/* SPARE - BUS - INFORMATIONS */
		
		$SQL = "Select UnionAllShifts.recID, UnionAllShifts.ID, UnionAllShifts.companyID, UnionAllShifts.stypeID, UnionAllShifts.tagCD, UnionAllShifts.OPDay, UnionAllShifts.shiftNO, UnionAllShifts.fID_2, UnionAllShifts.fID_3, UnionAllShifts.fID_7 From (Select shift_masters_dtl.recID, shift_masters_dtl.ID, shift_masters_dtl.companyID, shift_masters_dtl.stypeID, 'A' As tagCD, shift_masters_dtl.fID_18 As OPDay, shift_masters_dtl.fID_1 As shiftNO, shift_masters_dtl.fID_2, shift_masters_dtl.fID_3, shift_masters_dtl.fID_7 From shift_masters_dtl Where  shift_masters_dtl.recID > 0 And shift_masters_dtl.ID = ".$arrID[0]['shiftID']." And
		shift_masters_dtl.companyID In (".$_SESSION[$login->website]['compID'].") UNION All Select shift_masters_dtl.recID, shift_masters_dtl.ID, shift_masters_dtl.companyID, shift_masters_dtl.stypeID, 'B' As tagCD, shift_masters_dtl.fID_18 As OPDay, shift_masters_dtl.fID_1 As shiftNO, shift_masters_dtl.fID_9, shift_masters_dtl.fID_10, shift_masters_dtl.fID_12 From shift_masters_dtl Where shift_masters_dtl.recID > 0 And shift_masters_dtl.ID = ".$arrID[0]['shiftID']." And  shift_masters_dtl.companyID In (".$_SESSION[$login->website]['compID'].")) UnionAllShifts Left Join (Select imp_shift_daily.fID_1, imp_shift_daily.shift_recID, imp_shift_daily.shiftID,  imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.recID, 'A' As tagCD From  imp_shift_daily Where imp_shift_daily.shift_recID > 0 And imp_shift_daily.shiftID = ".$arrID[0]['shiftID']." And imp_shift_daily.companyID In (".$_SESSION[$login->website]['compID'].") And  imp_shift_daily.dateID = '".$login->dateFormat($reqID)."' And imp_shift_daily.tagCD = 'A'
		UNION All Select imp_shift_daily.fID_1, imp_shift_daily.shift_recID, imp_shift_daily.shiftID, imp_shift_daily.companyID, imp_shift_daily.dateID,  imp_shift_daily.recID, 'B' As tagCD From imp_shift_daily Where imp_shift_daily.shift_recID > 0 And imp_shift_daily.shiftID = ".$arrID[0]['shiftID']." And  imp_shift_daily.companyID In (".$_SESSION[$login->website]['compID'].") And imp_shift_daily.dateID = '".$login->dateFormat($reqID)."' And imp_shift_daily.tagCD = 'B') UnionImportSheet On UnionImportSheet.shift_recID = UnionAllShifts.recID And UnionImportSheet.tagCD = UnionAllShifts.tagCD And UnionImportSheet.fID_1 = UnionAllShifts.shiftNO Where UnionImportSheet.tagCD Is Null And UnionImportSheet.fID_1 Is Null And UnionImportSheet.shift_recID Is Null Order By UnionAllShifts.shiftNO, UnionAllShifts.tagCD ASC ";
		
		//echo $SQL;
		$Qry = $SIndex->DB->prepare($SQL);
		$Qry->execute();
		$SIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
		$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
		if(is_array($SIndex->rows) && count($SIndex->rows) > 0)
		{
			$file .= '<table id="dataTable" class="table table-bordered ">';				
			$file .= '<thead><tr>';
			$file .= '<th class="knob-labels notices" style="font-weight:600; font-size:14px; background:#85144B !important;" rowspan="2">Select</th>';
			$file .= '<th '.$Set.'>SHIFT No</th>';			
			$file .= '<th '.$Set.'>ON TIME</th>';
			$file .= '<th '.$Set.'>EX DEPOT</th>';
			$file .= '<th '.$Set.'>OFF TIME</th>';			
			$file .= '</tr></thead>';
			$returnID = 0;
			foreach($SIndex->rows as $row)			
			{				
				if($row['stypeID'] == 9)	{$returnID = 1;}
				else
				{	
					$returnID = $login->GET_DAY_NAME($row['OPDay'],$reqID);
					$returnID = ($returnID > 0 ? $returnID : 0);
				}
				
				if($returnID > 0)
				{
					$file .= '<tr>'; 
					$file .= '<td align="center"><input type="checkbox" aria-sort="'.$row['ID'].'_'.$row['recID'].'_'.$row['tagCD'].'_'.$reqID.'" class="select-pending-modal" /></td>';
					$file .= '<td align="center">'.$row['shiftNO'].' - '.$row['tagCD'].'</td>';
					$file .= '<td align="center">'.$row['fID_2'].'</td>';
					$file .= '<td align="center">'.$row['fID_3'].'</td>';
					$file .= '<td align="center">'.$row['fID_7'].'</td>';
					$file .= '</tr>';
				}
			}
			$file .= '</table>';
		}
		$file .= '</div>';
		$file .= '</div><br>';
		$file .= '</div></form>';
		
		$FormHandle = '';
		$arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
		echo json_encode($arr);
	}

		
	if($request == 'SWAP_BUSES')
	{
		$arrayID  = $reqID > 0   ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$reqID." ")     : '';

		$arrCHK = $arrayID[0]['dateID'] <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$arrayID[0]['dateID']."' AND hiddenID <= 0 ") : '';

		$arrayBS  = $arrayID[0]['fID_014'] > 0  ? $login->select('buses',array("*"), " WHERE ID = ".$arrayID[0]['fID_014']." ")     : '';

		$spare_empID = 0;
		if($_POST['changesID'] == 2)	{$spare_empID = $arrayID[0]['fID_014'];}

		/* START - CHECK A-B ID */
		$tagA = $reqID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$reqID." AND cuttoffID <>  1 ") : '';
                
                $tag_statusID = 0;
                if($tagA[0]['tagCD'] == 'A' || $tagA[0]['tagCD'] == 'a')    {$tag_statusID = 2;}    else    {$tag_statusID = 1;}			
		/* ENDSS - CHECK A-B ID */
	
		$file = "";
		$file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
		$file .= '<div class="row">&nbsp;</div>';		
		$file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';		

		$file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

		$file .= '<div class="row" style="margin:auto;">';

		$file .= '<div class="col-md-3">';
		$file .= '<label style="font-size:16px;">Shift Date : '.$login->VdateFormat($arrayID[0]['dateID']).'</label>';
		$file .= '</div>';

		$file .= '<div class="col-md-9">';
		$file .= '<label style="font-size:16px;">BUS NO (Current) : '.($arrayID[0]['fID_014'] > 0 ? strtoupper($arrayEM[0]['busno'].' - '.$arrayEM[0]['modelno'].' '.$arrayEM[0]['title']) : $arrayID[0]['fID_14']).'</label>';
		$file .= '</div>';

		$file .= '<div class="col-md-12"><br /></div>';

		/* SPARE - OR NEW BUSSES SYSTEM */                
		$file .= '<div class="col-md-12">';
		$file .= '<div class="nav-tabs-custom">';
		$file .= '<ul class="nav nav-tabs pull-left">';
		$file .= '<li class="active"><a href="#tab_1-1" data-toggle="tab"><b>Spare Buses</b></a></li>';
		$file .= '<li><a href="#tab_2-2" data-toggle="tab"><b>All Buses</b></a></li>';
		$file .= '</ul>';
		$file .= '<div class="tab-content">';

		/** GRID - 1 **/
		$file .= '<div class="tab-pane active" id="tab_1-1">';
		$file .= '<div class="row">';    

		/* SPARE - BUS - INFORMATIONS */                        
		$file .= '<div class="col-md-12">';
		$crtID = "";
		$crtID .= " AND DATE(spare_regis.dateID) BETWEEN '".($arrayID[0]['dateID'])."' AND '".($arrayID[0]['dateID'])."' AND spare_regis.companyID = ".$arrayID[0]['companyID']." AND spare_regis_dtl.statusID = 0 ";

		$Qry = $SIndex->DB->prepare("SELECT spare_regis.ID, spare_regis.dateID, spare_regis_dtl.recID, spare_regis.dateID, spare_regis.companyID, 
		spare_regis_dtl.fieldID_1 AS busID, buses.busno, buses.lcsno, buses.nos, buses.modelno, buses.title FROM spare_regis INNER JOIN spare_regis_dtl ON 
		spare_regis_dtl.ID = spare_regis.ID INNER JOIN buses ON buses.ID = spare_regis_dtl.fieldID_1 AND buses.companyID = spare_regis.companyID WHERE
		spare_regis.dateID <> '' AND spare_regis_dtl.hiddenID <= 0 AND spare_regis_dtl.forID = 2 ".$crtID." ORDER BY spare_regis_dtl.recID ASC ");
		$Qry->execute();
		$SIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
		$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
		if(is_array($SIndex->rows) && count($SIndex->rows) > 0)
		{
			$file .= '<table id="dataTable" class="table table-bordered ">';				
			$file .= '<thead><tr>';
			$file .= '<th '.$Set.'>Replace</th>';
			$file .= '<th '.$Set.'>Bus No</th>';
			$file .= '<th '.$Set.'>License NO</th>';
			$file .= '<th '.$Set.'>N.O.S</th>';
			$file .= '<th '.$Set.'>Model</th>';
			$file .= '<th '.$Set.'>Manufacturer</th>';
			$file .= '</tr></thead>'; 
			foreach($SIndex->rows as $row)			
			{ 
                            $file .= '<tr>'; 
                            $file .= '<td align="center">';
                                    $file .= '<input type="checkbox" aria-sort="'.$row['recID'].'_'.$reqID.'_'.$row['busID'].'_'.$row['dateID'].'_'.$spare_empID.'_'.$tag_statusID.'_'.$arrayID[0]['fID_1'].'_'.$arrayID[0]['companyID'].'" aria-busy="BUSES" class="selection-modal" />';
                            $file .= '</td>';

                            $file .= '<td align="center">'.$row['busno'].'</td>';
                            $file .= '<td>'.strtoupper($row['lcsno']).'</td>';
                            $file .= '<td align="center">'.$row['nos'].'</td>';
                            $file .= '<td>'.$row['modelno'].'</td>';
                            $file .= '<td>'.$row['title'].'</td>';
                            $file .= '</tr>';
			}
			$file .= '</table>';
		}
		$file .= '</div>';

		$file .= '</div><br>'; // row-end 
		$file .= '</div>';

		/** GRID - 2 **/
		$file .= '<div class="tab-pane" id="tab_2-2">';
			$file .= '<div class="row">';	 	
			/* NEW - BUSS ADD - INFORMATIONS*/
                        
                        $file .= '<div class="col-md-12"></div>';

                        $file .= '<input type="hidden" id="requestID" value="FILTER_ALL_BUSES">';
                        $file .= '<input type="hidden" id="companyID" value="'.$arrayID[0]['companyID'].'">';
                        $file .= '<input type="hidden" id="date_REQID" value="'.$arrayID[0]['dateID'].'">';
                        $file .= '<input type="hidden" id="spare_REQID" value="'.$reqID.'">';
                        $file .= '<input type="hidden" id="tag_statusID" value="'.$tag_statusID.'">';
                        $file .= '<input type="hidden" id="shiftNO" value="'.$arrayID[0]['fID_1'].'">';

                        $file .= '<div class="col-md-3"><input type="text" id="searchbyID" class="form-control" placeholder="Search By Bus No...." /></div>';

                        $file .= '<div class="col-md-12"></div>';

                        $file .= '<div class="col-md-12" style="position: relative; height: 280px; overflow-y: scroll;" id="filters_data">';
                        $file .= '<table id="dataTable" class="table table-bordered ">';				
                        $file .= '<thead><tr>';
                        $file .= '<th colspan="6" '.$Set.'>Replace Bus</th>';
                        $file .= '</tr></thead>';

                        $file .= '<thead><tr>';
                        $file .= '<th width="150" '.$Set.'>Replace</th>';
                        $file .= '<th '.$Set.'>Bus No</th>';
                        $file .= '<th '.$Set.'>License NO</th>';
                        $file .= '<th '.$Set.'>N.O.S</th>';
                        $file .= '<th '.$Set.'>Model</th>';
                        $file .= '<th '.$Set.'>Manufacturer</th>';
                        $file .= '</tr></thead>';

                        /*$Qry = $login->DB->prepare("SELECT * FROM buses WHERE ID > 0 AND companyID In(".$arrayID[0]['companyID'].") ".($bus_crtID <> '' ? $bus_crtID : '')." Order By busno ASC");
                        if($Qry->execute())
                        {
                            $spare_arrayID  = $arrayID[0]['dateID'] <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$arrayID[0]['dateID']."' AND companyID In(".$arrayID[0]['companyID'].") ")     : '';
                            $login->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);

                            foreach($login->crow as $mrow)
                            {
                                $file .= '<tr>'; 
                                $file .= '<td align="center">';
                                $file .= '<input type="checkbox" aria-busy="NEW_BUSES" aria-sort="'.$reqID.'_'.$arrayID[0]['dateID'].'_'.$mrow['ID'].'_'.$tag_statusID.'_'.$arrayID[0]['fID_1'].'_'.$arrayID[0]['companyID'].'" class="selection-temp-modal" />';
                                $file .= '</td>';

                                $file .= '<td align="center">'.$mrow['busno'].'</td>';
                                $file .= '<td align="center">'.$mrow['lcsno'].'</td>';
                                $file .= '<td align="center">'.$mrow['nos'].'</td>';
                                $file .= '<td align="center">'.$mrow['modelno'].'</td>';
                                $file .= '<td align="center">'.$mrow['title'].'</td>';
                                $file .= '</tr>';
                            }
                        }*/

                        $file .= '</table>';
                        $file .= '</div>';
                                
			$file .= '</div>';
		$file .= '</div>';

		$file .= '</div>';
		$file .= '</div>';
		$file .= '</div>'; 



		$file .= '</div>';
		$file .= '</div>';
		$file .= '</div>';

		$file .= '</div>';

		$file .= '</div></form>';

		$FormHandle = '';

		$arr = array('file_info'=>$file,'form_handle'=>$FormHandle);

		echo json_encode($arr);
	}

	if($request == 'SWAP_EMPLOYEE')
	{
		$arrayID  = $reqID > 0   ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$reqID." ")     : '';		
		$arrCHK = $arrayID[0]['dateID'] <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$arrayID[0]['dateID']."' ") : '';		
		$empID = 0;
		$empID = $arrayID[0]['fID_018'] > 0 ? $arrayID[0]['fID_018'] : $arrayID[0]['fID_013'];		
		$arrayEM  = $empID > 0   ? $login->select('employee',array("*"), " WHERE ID = ".$empID." ")     : '';
		
		$spare_empID = 0;
		if($_POST['changesID'] == 2)    {$spare_empID = $arrayID[0]['fID_018'];}
		
		/* START - CHECK A-B ID */
		$tagA = $reqID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID = ".$reqID." AND cuttoffID <>  1 ") : '';
                
		$tag_statusID = 0;
		if($tagA[0]['tagCD'] == 'A' || $tagA[0]['tagCD'] == 'a')    {$tag_statusID = 2;}    else    {$tag_statusID = 1;}
		/* ENDSS - CHECK A-B ID */
		
		$file = "";

		$file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
		$file .= '<div class="row">&nbsp;</div>';		
		$file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';		

		$file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

		$file .= '<div class="row" style="margin:auto;">';
			$file .= '<div class="col-md-3">';
			$file .= '<label style="font-size:16px;">Shift Date : '.$login->VdateFormat($arrayID[0]['dateID']).'</label>';
			$file .= '</div>';

			$file .= '<div class="col-md-9">';
			$file .= '<label style="font-size:16px;">Staff ID (Current) : '.strtoupper($arrayEM[0]['code'].' - '.$arrayEM[0]['fname'].' '.$arrayEM[0]['lname']).'</label>';
			$file .= '</div>';
		$file .= '</div>';
		
		
		$file .= '<div class="col-md-12"><br /></div>';
            
		/* SPARE - EMPLOYEE - SEARCHING EMPLOYEE  */
		$file .= '<div class="col-md-12">';
		$file .= '<div class="nav-tabs-custom">';
		$file .= '<ul class="nav nav-tabs pull-left">';
		$file .= '<li class="active"><a href="#tab_1-1" data-toggle="tab"><b>Spare Drivers</b></a></li>';
		$file .= '<li><a href="#tab_2-2" data-toggle="tab"><b>All Drivers</b></a></li>';
		$file .= '</ul>';
		$file .= '<div class="tab-content">';

		/** GRID - 1 **/
		$file .= '<div class="tab-pane active" id="tab_1-1">';
			$file .= '<div class="row">';
			
			$file .= '<div class="col-md-12"><br />'; 

			$crtID = "";
			$crtID .= " AND DATE(spare_regis.dateID) BETWEEN '".($arrayID[0]['dateID'])."' AND '".($arrayID[0]['dateID'])."' AND spare_regis.companyID = ".$arrayID[0]['companyID']." AND spare_regis_dtl.statusID = 0 ";

			$Qry = $SIndex->DB->prepare("SELECT spare_regis.ID, spare_regis.dateID, spare_regis_dtl.recID, spare_regis.dateID, spare_regis.companyID, 
			spare_regis_dtl.fieldID_1 AS empID, spare_regis_dtl.fieldID_2 AS phoneID_1, spare_regis_dtl.fieldID_3 AS phoneID_2, spare_regis_dtl.fieldID_4 AS 
			locationID, spare_regis_dtl.fieldID_5 AS suburbID, spare_regis_dtl.fieldID_8 AS avaiableID, spare_regis_dtl.fieldID_6 AS timeID FROM spare_regis 
			INNER JOIN spare_regis_dtl ON spare_regis_dtl.ID = spare_regis.ID WHERE spare_regis_dtl.hiddenID <= 0 AND spare_regis.dateID <> '' AND spare_regis_dtl.forID = 1 ".$crtID." 
			Order BY spare_regis_dtl.recID ASC ");
			$Qry->execute();
			$SIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
			$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
			if(is_array($SIndex->rows) && count($SIndex->rows) > 0)
			{
				$file .= '<table id="dataTable" class="table table-bordered ">';				
				$file .= '<thead><tr>';
				$file .= '<th '.$Set.'>Repalce</th>';
				$file .= '<th '.$Set.'>Staff ID</th>';
				$file .= '<th '.$Set.'>Staff Name</th>';
				$file .= '<th '.$Set.'>Phone No</th>';
				$file .= '<th '.$Set.'>Phone No - 2</th>';
				$file .= '<th '.$Set.'>Location</th>';
				$file .= '<th '.$Set.'>Suburb</th>';
				$file .= '<th '.$Set.'>Avail. Time</th>';
				$file .= '</tr></thead>'; 
				$row_colourID = '';
				foreach($SIndex->rows as $row)			
				{
					$row_colourID = $row['avaiableID'] > 0 ? '' : 'style="background:red !important; color:white;"';

					$arrayST  = $row['empID'] > 0  ? $login->select('employee',array("*"), " WHERE ID = ".$row['empID']." ")     : '';

					$file .= '<tr>'; 
					$file .= '<td align="center">';
					if($row['avaiableID'] > 0)
					{
							$file .= '<input type="checkbox" aria-sort="'.$row['recID'].'_'.$reqID.'_'.$row['empID'].'_'.$row['dateID'].'_'.$spare_empID.'_'.$tag_statusID.'_'.$arrayID[0]['fID_1'].'_'.$arrayID[0]['companyID'].'" aria-busy="EMPLOYEE" class="selection-modal" />';
					}
					else    {}
					$file .= '</td>';  


					$file .= '<td '.$row_colourID.' align="center">'.$arrayST[0]['code'].'</td>';
					$file .= '<td '.$row_colourID.'>'.strtoupper($arrayST[0]['fname'].' '.$arrayST[0]['lname']).'</td>';
					$file .= '<td '.$row_colourID.'>'.$row['phoneID_1'].'</td>';
					$file .= '<td '.$row_colourID.'>'.$row['phoneID_2'].'</td>';
					$file .= '<td '.$row_colourID.'>'.$row['locationID'].'</td>';
					$file .= '<td '.$row_colourID.'>'.$row['suburbID'].'</td>';
					$file .= '<td '.$row_colourID.' align="center">'.($row['avaiableID'] == 2 ? 'ANY-TIME' : $row['timeID']).'</td>';
					$file .= '</tr>';
				}
				$file .= '</table>';
			}
			$file .= '</div>';
			
			$file .= '</div><br>';
		$file .= '</div>';

		/** GRID - 2 **/
		$file .= '<div class="tab-pane" id="tab_2-2">';
		$file .= '<div class="row">';
                
                /* NEW - EMPLOYEE ADD - INFORMATIONS*/		
                
                $file .= '<input type="hidden" id="requestID" value="FILTER_ALL_EMPLOYEES">';
                $file .= '<input type="hidden" id="companyID" value="'.$arrayID[0]['companyID'].'">';
                $file .= '<input type="hidden" id="date_REQID" value="'.$arrayID[0]['dateID'].'">';
                $file .= '<input type="hidden" id="spare_REQID" value="'.$reqID.'">';
                $file .= '<input type="hidden" id="tag_statusID" value="'.$tag_statusID.'">';
                $file .= '<input type="hidden" id="shiftNO" value="'.$arrayID[0]['fID_1'].'">';

                $file .= '<div class="col-md-12"></div>';

                $file .= '<div class="col-md-5"><input type="text" id="searchbyID" class="form-control" placeholder="Search By Employee Name / Code ...." /></div>';

                $file .= '<div class="col-md-12"></div>';

                $file .= '<div class="col-md-12" style="position: relative; height: 280px; overflow-y: scroll;" id="filters_data">'; 		
                $file .= '<table id="dataTable" class="table table-bordered ">';				
                $file .= '<thead><tr>';
                $file .= '<th colspan="5" '.$Set.'>Replace Driver </th>';
                $file .= '</tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th width="150" '.$Set.'>Replace</th>';
                $file .= '<th width="120" '.$Set.'>Driver Code</th>';
                $file .= '<th '.$Set.'>Driver Name</th>';
                $file .= '<th '.$Set.'>Available</th>';
                $file .= '<th '.$Set.'>Time</th>';
                $file .= '</tr></thead>';

				/* START - CHOPPED - CASE */
					$file .= '<tr>'; 
						$file .= '<td align="center"><input type="checkbox" aria-sort="'.$reqID.'" class="selection-chopped-modal" /></td>';
						$file .= '<td align="center">0000</td>';
						$file .= '<td>Chopped</td>';
						$file .= '<td></td>';
						$file .= '<td></td>';
					$file .= '</tr>';
				/* END - CHOPPED - CASE */
				
								
                /*$Qry = $login->DB->prepare("SELECT * FROM employee WHERE ID > 0 AND status = 1 AND desigID = 9 Order By fname,lname ASC ");
                if($Qry->execute())
                {
                    $spare_arrayID  = $arrayID[0]['dateID'] <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$arrayID[0]['dateID']."' AND companyID In(".$arrayID[0]['companyID'].") ")     : '';
                    $login->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);

                    if(is_array($login->crow) && count($login->crow) > 0)
                    {
                        foreach($login->crow as $rows)
                        {
                            $spareDT  = $spare_arrayID[0]['ID'] > 0 ? $login->select('spare_regis_dtl',array("*"), " WHERE ID = ".$spare_arrayID[0]['ID']." AND fieldID_1 = ".$rows['ID']." AND forID = 1 ") : '';

                            if($spareDT[0]['recID'] > 0)
                            {} else
                            {
                                $file .= '<tr>'; 
                                $file .= '<td align="center">';
                                    $file .= '<input type="checkbox" aria-busy="NEW_EMPLOYEES" aria-sort="'.$reqID.'_'.$arrayID[0]['dateID'].'_'.$rows['ID'].'_'.$tag_statusID.'_'.$arrayID[0]['fID_1'].'_'.$arrayID[0]['companyID'].'" class="selection-temp-modal" />';
                                $file .= '</td>';

                                $file .= '<td align="center">'.strtoupper($rows['code']).'</td>';

                                $file .= '<td>'.strtoupper($rows['fname'].' '.$rows['lname']).'</td>';

                                $file .= '<td width="140">';
                                    $file .= '<select class="form-control" style="width:100%;" id="temp_avaiableID_'.$rows['ID'].'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';                
                                    $file .= '<option value="1">After</option>';
                                    $file .= '<option value="2" selected="selected">Any Time</option>';
                                    $file .= '<option value="3">Available Untill</option>';
                                    $file .= '</select>';
                                $file .= '</td>';

                                $file .= '<td width="130"><input type="text" readonly="readonly" class="form-control TPicker" id="temp_timeID_'.$rows['ID'].'" style="text-align:center;" placeholder="Time"></td>';

                                $file .= '</tr>';
                            }
                        }
                    }
                }*/

                $file .= '</table>';			

                $file .= '</div>';
                        
                $file .= '</div><br>';
            $file .= '</div>';

            $file .= '</div>';
            $file .= '</div>';
            $file .= '</div>'; 
    
            
            $file .= '</div></form>';

            $FormHandle = '';

            $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);

            echo json_encode($arr);
	}

	if($request == 'GET_FORMS_LIST')
	{
		$UR_Array  = $_POST['roleID'] > 0   ? $login->select('urole',array("*"), " WHERE ID = ".$_POST['roleID']." ")     : '';
		$US_Array  = $_POST['vousID'] > 0   ? $login->select('users',array("*"), " WHERE ID = ".$_POST['vousID']." ")     : '';
		
		$file = "";
		
		$file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
		$file .= '<div class="row">&nbsp;</div>';		
		$file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';		
		
		$file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i>
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
		
		$file .= '<div class="row" style="margin:auto;">';
		
		$file .= '<div class="col-md-4">';
		$file .= '<label style="font-size:16px;">User Role Name : '.$UR_Array[0]['title'].'</label>';
		$file .= '</div>';
		
		$file .= '<div class="col-md-8"></div>';
		
		$file .= '<div class="col-md-12"><br /></div>';
		
                $file .= '<div class="col-md-12">';
                $file .= '<div class="nav-tabs-custom">';
                $file .= '<ul class="nav nav-tabs">';
                $file .= '<li class="active"><a href="#tab_1" data-toggle="tab"><b>Settings</b></a></li>';
                $file .= '<li><a href="#tab_2" data-toggle="tab"><b>LOV</b></a></li>';
                $file .= '<li><a href="#tab_3" data-toggle="tab"><b>Masters</b></a></li>';
                $file .= '<li><a href="#tab_4" data-toggle="tab"><b>Employee</b></a></li>';
                $file .= '<li><a href="#tab_5" data-toggle="tab"><b>Driver Details</b></a></li>';
                $file .= '<li><a href="#tab_6" data-toggle="tab"><b>Rostering</b></a></li>';
                $file .= '<li><a href="#tab_7" data-toggle="tab"><b>All Set Reports</b></a></li>';
                $file .= '<li><a href="#tab_8" data-toggle="tab"><b>Driver Performance</b></a></li>';
				$file .= '<li><a href="#tab_9" data-toggle="tab"><b>Driver SignOn</b></a></li>';
				$file .= '<li><a href="#tab_10" data-toggle="tab"><b>Health & Safety</b></a></li>';
                $file .= '</ul>';

                /* Settings - DETAILS */
                $file .= '<div class="tab-content">';                
                    $file .= '<div class="tab-pane active" id="tab_1">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"1,10",$_POST['vousID']);
                    $file .= '</div>';

                    /* LOV - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_2">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"2",$_POST['vousID']);
                    $file .= '</div>';

                    /* Masters - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_3">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"3",$_POST['vousID']);
                    $file .= '</div>';

                    /* Employee- DETAILS */
                    $file .= '<div class="tab-pane" id="tab_4">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"4",$_POST['vousID']);
                    $file .= '</div>';

                    /* Driver Details - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_5">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"5",$_POST['vousID']);
                    $file .= '</div>';

                    /* Rostering - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_6">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"6",$_POST['vousID']);
                    $file .= '</div>';

                    /* All Set Reports - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_7">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"7",$_POST['vousID']);
                    $file .= '</div>';
                    
                    /* Driver Performance - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_8">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"8",$_POST['vousID']);
                    $file .= '</div>';
					
                    /* Driver Sign On - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_9">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"9",$_POST['vousID']);
                    $file .= '</div>';
					
                    /* Health & Safety - DETAILS */
                    $file .= '<div class="tab-pane" id="tab_10">';
                    $file .= $FIndex->GET_ExtraPermissions($_POST['roleID'],"11",$_POST['vousID']);
                    $file .= '</div>';
                    
                    $file .= '<input type="hidden" name="userID" value="'.$_POST['vousID'].'" />';
                    $file .= '<input type="hidden" class="form-control" name="request" value="Update_Users_Permissions" />';
                     
                    $file .= '<div class="col-xs-2">';
                        $file .= '<label for="section"><span class="Maindaitory"> Single Permissions</span></label>';
                    $file .= '</div>';
                    
                    $file .= '<div class="col-xs-4">';
                        $file .= '<select class="form-control" name="spermissionID[]" id="spermissionsID" multiple="multiple">';
                        $spermissionID = $US_Array[0]['spermissionID'];
                        $permissionsID = explode(",",$spermissionID);
                            $file .= '<option value="1" '.(in_array(1,$permissionsID) ? 'selected="selected"' : '').'>Manager Comments</option>';
                            $file .= '<option value="2" '.(in_array(2,$permissionsID) ? 'selected="selected"' : '').'>Warning Types</option>';
                        $file .= '</select>';
                    $file .= '</div>';
                    
                    $file .= '<div class="col-xs-1">';
                        $file .= '<label for="section"><span class="Maindaitory"> Till Date </span></label>';
                    $file .= '</div>';
                    
                    $file .= '<div class="col-xs-3">';
						$file .= '<div class="input-group">';
						$file .= '<div class="input-group-addon"><i class="fa fa-calendar"></i></div>';						
						$file .= '<input id="input4" type="datable" class="form-control" name="tdateID" required="required" data-datable="ddmmyyyy" />';
						
                        /*$file .= '<input type="text" class="form-control datepicker" name="tdateID" value="'.$login->VdateFormat($US_Array[0]['tdateID']).'" 
						placeholder="Enter Till Date" required="required" style="text-align:center;">';*/
						
						$file .= '</div>';
                    $file .= '</div>';
                    
                    $file .= '<div class="col-xs-2">';
                        $file .= '<button type="submit" class="btn btn-primary" id="gen_user">Update Permissions </button>';		
                    $file .= '</div><br /><br />';

                $file .= '</div>';
                $file .= '</div>';
                $file .= '</div>';
		
		$file .= '</div>';
		
		$file .= '</div></form>';
		
		$FormHandle = '';
		
		$arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
		
		echo json_encode($arr);
	}
	        
	if($request == 'Accidents_Register')
	{
            if(!empty($reqID))
            {   
             $file = "";

            $Qry = $Index->DB->prepare("SELECT count(*) as rows FROM accident_regis_dtl WHERE ID > 0 AND ID = ".$reqID." ");
            $Qry->execute();
            $Index->rows = $Qry->fetch(PDO::FETCH_ASSOC);

              if(!empty($reqID))
              {
                    $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
                    $file .= '<div class="row">&nbsp;</div>';		
                    $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';		

                    $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

                    $file .= '<div class="row" style="margin:auto;">';
                    $file .= '<div class="col-xs-12">';
                            $file .= '<table id="dataTablesAC" class="table table-bordered table-striped">';				
                            $file .= '<thead><tr>';
                            $file .= '<th style="text-align:center !important;"><a style="cursor:pointer; text-decoration:none;" class="fa fa-plus DTaccpopID"></a></th>';
                            $file .= '<th style="text-align:center !important; color:#2F6F95;">Date</th>';
                            $file .= '<th style="text-align:center !important; color:#2F6F95;">Accidents Detail/Remarks</th>';
                            $file .= '</tr></thead>';
                                    if(!empty($reqID) && ($reqID > 0))  {$file .= $SIndex->PopUpsAccidents($reqID);}
                            $file .= '</table>';
                    $file .= '</div>';
                    $file .= '</div>';

                    $file .= '<div class="row" style="margin:auto;">';

                    $file .= '<input type="hidden" class="form-control" name="request" value="Accidents_Register_Insert" />';
                    $file .= '<input type="hidden" class="form-control" name="reqID" value="'.$reqID.'" />';

                    $file .= '<div class="col-xs-2">';                    
                            $file .= '<button type="submit" class="btn btn-primary" id="gen_user">Save Accident Details</button>';		
                    $file .= '</div>';

                    $file .= '<div class="col-xs-7"></div>';

                    if($Index->rows['rows'] > 0)
                    {
                            $file .= '<div class="col-xs-3">';
                                    $file .= '<a href="'.$Index->home.'rpts-c/rpt_accident.php?i='.$Index->Encrypt($reqID).'" target="blank" style="float:right;" class="btn btn-primary fa fa-print" id="PrintAccID"> Print Accident Report</a>';
                            $file .= '</div>';
                    }

                    $file .= '</div><br />';

                    $file .= '</div></form>';

                    $FormHandle = '';
              } 

              $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);

              echo json_encode($arr);			
            }
	}
	
	if($request == 'imp_persheets_e')
	{
		$arID = '';
		$arID = explode("/",$reqID);
		
		$EM_Array = (trim($arID[4]) > 0 ? $login->select('employee',array("*"), " WHERE ID = ".(trim($arID[4]))." ") : '');
		
		$file = '';
	
		$file  = '<div class="box box-primary" style="margin:auto">';
		$file .= '<div class="row">&nbsp;</div>';
		$file .= '<div class="row" style="margin:auto">';
	
		$file .= '<div class="col-xs-6 row"><label class="col-xs-5 modal-label">Employee Code :</label><span class="col-xs-7">'.$EM_Array[0]['code'].'</span></div>';
		$file .= '<div class="col-xs-6 row"><label class="col-xs-5 modal-label">Employee Name :</label><span class="col-xs-7">'.$EM_Array[0]['full_name'].'</span></div>';
		
		$file .= '<br/><br/>';
		
		$crtID = "";
		$crtID .= " AND DATE(".trim($request).".prID) BETWEEN '".($arID[2])."' AND '".($arID[3])."' ";
		
		$Qry = $SIndex->DB->prepare("SELECT * FROM ".trim($request)." WHERE recID > 0 AND earlyID > 0 AND empID = ".$arID[4]." ".$crtID." ");
		$Qry->execute();
		$SIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
		$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
		if(is_array($SIndex->rows) && count($SIndex->rows) > 0)
		{
		$file .= '<table id="dataTable" class="table table-bordered ">';				
		$file .= '<thead><tr>';
		$file .= '<th '.$Set.'>Sr. No.</th>';
		$file .= '<th '.$Set.'>Month / Year Name</th>';
		$file .= '<th '.$Set.'>Early Running</th>';
		$file .= '</tr></thead>'; 
		$srID = 1;  $TOT_1 = 0;
		foreach($SIndex->rows as $row)			
		{ 
			$file .= '<tr>'; 
				$file .= '<td align="center">'.$srID++.'</td>';  
				$file .= '<td align="center">'.(date("F", mktime(0, 0, 0, $row['monID'], 10))).' - '.($row['yrID']).'</td>';
				$file .= '<td align="center">'.($row['earlyID']).'</td>';
			$file .= '</tr>';
	
			$TOT_1 += $row['earlyID'];
		}
	
			$file .= '<tr>'; 
				$file .= '<td align="right" colspan="2"><b>Total : </b></td>';  
				$file .= '<td align="center"><b>'.$TOT_1.'</b></td>';
			$file .= '</tr>';
		}
		else
		{
			$file .= '<div class="col-xs-6"><label>No Record is Available</label></div>';
		}
		$file .= '</div>';
	
		$arr = array('file_info'=>$file);
		echo json_encode($arr);
	}
	
	if($request == 'imp_persheets_l')
	{
		$arID = '';
		$arID = explode("/",$reqID);
		
		$EM_Array = (trim($arID[4]) > 0 ? $login->select('employee',array("*"), " WHERE ID = ".(trim($arID[4]))." ") : '');
		
		$file = '';
	
		$file  = '<div class="box box-primary" style="margin:auto">';
		$file .= '<div class="row">&nbsp;</div>';
		$file .= '<div class="row" style="margin:auto">';
	
		$file .= '<div class="col-xs-6 row"><label class="col-xs-5 modal-label">Employee Code :</label><span class="col-xs-7">'.$EM_Array[0]['code'].'</span></div>';
		$file .= '<div class="col-xs-6 row"><label class="col-xs-5 modal-label">Employee Name :</label><span class="col-xs-7">'.$EM_Array[0]['full_name'].'</span></div>';
		
		$file .= '<br/><br/>';
		
		$crtID = "";
		$crtID .= " AND DATE(".trim($request).".prID) BETWEEN '".($arID[2])."' AND '".($arID[3])."' ";
		
		$Qry = $SIndex->DB->prepare("SELECT * FROM ".trim($request)." WHERE recID > 0 AND latefirstID > 0 AND empID = ".$arID[4]." ".$crtID." ");
		$Qry->execute();
		$SIndex->rows = $Qry->fetchAll(PDO::FETCH_ASSOC); 
		$Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
		if(is_array($SIndex->rows) && count($SIndex->rows) > 0)
		{
		$file .= '<table id="dataTable" class="table table-bordered ">';				
		$file .= '<thead><tr>';
		$file .= '<th '.$Set.'>Sr. No.</th>';
		$file .= '<th '.$Set.'>Month / Year Name</th>';
		$file .= '<th '.$Set.'>Late First Running</th>';
		$file .= '</tr></thead>'; 
		$srID = 1;  $TOT_1 = 0;
		foreach($SIndex->rows as $row)			
		{ 
			$file .= '<tr>'; 
				$file .= '<td align="center">'.$srID++.'</td>';  
				$file .= '<td align="center">'.(date("F", mktime(0, 0, 0, $row['monID'], 10))).' - '.($row['yrID']).'</td>';
				$file .= '<td align="center">'.($row['latefirstID']).'</td>';
			$file .= '</tr>';
	
			$TOT_1 += $row['latefirstID'];
		}
	
			$file .= '<tr>'; 
				$file .= '<td align="right" colspan="2"><b>Total : </b></td>';  
				$file .= '<td align="center"><b>'.$TOT_1.'</b></td>';
			$file .= '</tr>';
		}
		else
		{
			$file .= '<div class="col-xs-6"><label>No Record is Available</label></div>';
		}
		$file .= '</div>';
	
		$arr = array('file_info'=>$file);
		echo json_encode($arr);
	}
?>