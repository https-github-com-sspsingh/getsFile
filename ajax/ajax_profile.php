<?PHP
	include_once '../includes.php';
        
	
	$request    =	isset($_POST['request'])        ?	$_POST['request']   : '' ;	
	$fdateID    =	isset($_POST['fdateID'])	?	$_POST['fdateID']   : '' ;
	$tdateID    =	isset($_POST['tdateID'])	?	$_POST['tdateID']   : '' ;
	$memberID   =	isset($_POST['memberID'])	?	$_POST['memberID']   : '' ;
        
        $fmonthID = date('m',strtotime($Index->dateFormat($fdateID)));
        $tmonthID = date('m',strtotime($Index->dateFormat($tdateID)));        
        $fyearID  = date('Y',strtotime($Index->dateFormat($fdateID)));
        $tyearID  = date('Y',strtotime($Index->dateFormat($tdateID)));
        
	if($request == 'GET_PROFILE') 
	{
            $arr = array();
            $optID_1 = '';  $optID_2 = '';  $optID_3 = '';  $optID_4 = '';  $optID_5 = '';
            
            /* Sick-Leaves */
            $Qry = $Index->DB->prepare("SELECT All_Data.srID, All_Data.sdayID, Sum(All_Data.duration) AS totID FROM (SELECT sicklv.ID, sicklv.empID,
            sicklv.ecodeID, sicklv.sldateID, DayName(sicklv.sldateID) AS sdayID, sicklv.duration, If(DayName(sicklv.sldateID) = 'Monday', 1, 
            If(DayName(sicklv.sldateID) = 'Tuesday', 2, If(DayName(sicklv.sldateID) = 'Wednesday', 3, If(DayName(sicklv.sldateID) = 'Thursday', 4, 
            If(DayName(sicklv.sldateID) = 'Friday', 5, If(DayName(sicklv.sldateID) = 'Saturday', 6, 
            If(DayName(sicklv.sldateID) = 'Sunday', 7, 0))))))) AS srID FROM sicklv WHERE sicklv.ID > 0 AND sicklv.empID = ".$memberID." 
            AND sicklv.companyID IN (".$_SESSION[$login->website]['compID'].") AND sicklv.duration <= '1' AND Date(sicklv.sldateID) 
			BETWEEN '".$Index->dateFormat($fdateID)."' AND '".$Index->dateFormat($tdateID)."' ORDER BY srID) AS All_Data GROUP BY All_Data.srID, All_Data.sdayID 
			ORDER BY All_Data.srID ASC ");
            if($Qry->execute())
            {
                $Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $optID_1 .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $optID_1 .= '<thead><tr>';
                $optID_1 .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
                $optID_1 .= '<th style="background:#3C8DBC; color:white;">Week Day</th>';
                $optID_1 .= '<th style="background:#3C8DBC; color:white;">Durations</th>';
                $optID_1 .= '</tr></thead>';
                if(is_array($Index->rows) && count($Index->rows) > 0)
                {
                    $totID = 0; $Start = 1;
                    foreach($Index->rows as $row)
                    {
                        $optID_1 .= '<tr>';
                        $optID_1 .= '<td align="center">'.$Start++.'</td>';
                        $optID_1 .= '<td>'.$row['sdayID'].'</td>';
                        $optID_1 .= '<td align="center">'.$row['totID'].'</td>'; 
                        $optID_1 .= '</tr>';
                        
                        $totID += $row['totID'];
                    }
                        $optID_1 .= '<tr>';
                            $optID_1 .= '<td colspan="2" align="right"><b>Totals :</b></td>';
                            $optID_1 .= '<td align="center"><b>'.$totID.'</b></td>';
                        $optID_1 .= '</tr>';
                }
                $optID_1 .= '</table>';			
            } 
            
            
            /* Complaints-Data */
            $Qry = $Index->DB->prepare("SELECT All_Data.creasonID, Sum(All_Data.countID) AS countID, master.title AS reasonID FROM (SELECT complaint.ID,
            complaint.driverID, complaint.dcodeID, complaint.creasonID, 1 AS countID FROM complaint WHERE complaint.accID = 52 AND complaint.substanID = 1 
            AND complaint.faultID = 1 AND complaint.driverID = ".$memberID." AND Date(complaint.dateID) BETWEEN '".$Index->dateFormat($fdateID)."' AND '".$Index->dateFormat($tdateID)."' AND complaint.companyID IN (".$_SESSION[$login->website]['compID'].")) AS All_Data
            INNER JOIN master ON master.ID = All_Data.creasonID GROUP BY All_Data.creasonID, master.title ORDER BY reasonID ASC ");
            if($Qry->execute())
            {
                $Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $optID_2 .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $optID_2 .= '<thead><tr>';
                $optID_2 .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
                $optID_2 .= '<th style="background:#3C8DBC; color:white;">Comment Line Reason</th>';
                $optID_2 .= '<th style="background:#3C8DBC; color:white;">Counts</th>';
                $optID_2 .= '</tr></thead>';
                if(is_array($Index->rows) && count($Index->rows) > 0)
                {
                    $totID = 0; $Start = 1;
                    foreach($Index->rows as $row)
                    {
                        $optID_2 .= '<tr>';
                        $optID_2 .= '<td width="60" align="center">'.$Start++.'</td>';
                        $optID_2 .= '<td>'.$row['reasonID'].'</td>';
                        $optID_2 .= '<td align="center">'.$row['countID'].'</td>'; 
                        $optID_2 .= '</tr>';
                        
                        $totID += $row['countID'];
                    }
                        $optID_2 .= '<tr>';
                            $optID_2 .= '<td colspan="2" align="right"><b>Totals :</b></td>';
                            $optID_2 .= '<td align="center"><b>'.$totID.'</b></td>';
                        $optID_2 .= '</tr>';
                }
                $optID_2 .= '</table>';			
            } 
            
            
            /* Accidents-Data */
            $Qry = $Index->DB->prepare("SELECT
  Sum(All_Data.responsibleID) AS responsibleID,
  Sum(All_Data.tot_costID) AS tot_costID,
  All_Data.accID,
  All_Data.accNM
FROM
  (SELECT
      accident_regis.ID,
      accident_regis.staffID,
      accident_regis.scodeID,
      accident_regis.responsibleID,
      Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) AS tot_costID,
      accident_regis.dateID,
      accident_regis.accID AS accID,
      master.title AS accNM
    FROM
      accident_regis
      LEFT JOIN master ON master.ID = accident_regis.accID
    WHERE
      accident_regis.responsibleID = 1 AND
      Date(accident_regis.dateID) BETWEEN '".$Index->dateFormat($fdateID)."' AND '".$Index->dateFormat($tdateID)."' AND
      accident_regis.staffID = ".$memberID." AND
      accident_regis.companyID IN (".$_SESSION[$login->website]['compID'].")) AS All_Data
GROUP BY
  All_Data.accID,
  All_Data.accNM");
			
            if($Qry->execute())
            { 
                $Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $optID_3 .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $optID_3 .= '<thead><tr>';
                $optID_3 .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
                $optID_3 .= '<th style="background:#3C8DBC; color:white;">Accident Details</th>';
                $optID_3 .= '<th style="background:#3C8DBC; color:white;">Counts</th>';
                $optID_3 .= '<th style="background:#3C8DBC; color:white;">Costs</th>';
                $optID_3 .= '</tr></thead>';
                if(is_array($Index->rows) && count($Index->rows) > 0)
                {
                    $totID_1 = 0; $totID_2 = 0; $Start = 1;
                    foreach($Index->rows as $row)
                    {
                        $optID_3 .= '<tr>';
                        $optID_3 .= '<td width="60" align="center">'.$Start++.'</td>';
                        $optID_3 .= '<td>'.$row['accNM'].'</td>';
                        $optID_3 .= '<td align="center">'.$row['responsibleID'].'</td>'; 
                        $optID_3 .= '<td align="center">'.round($row['tot_costID'],2).'</td>'; 
                        $optID_3 .= '</tr>';
                        
                        $totID_1 += $row['responsibleID'];
                        $totID_2 += $row['tot_costID'];
                    }
                        $optID_3 .= '<tr>';
                            $optID_3 .= '<td colspan="2" align="right"><b>Totals :</b></td>';
                            $optID_3 .= '<td align="center"><b>'.$totID_1.'</b></td>';
                            $optID_3 .= '<td align="center"><b>'.round($totID_2,2).'</b></td>';
                        $optID_3 .= '</tr>';
                }
                $optID_3 .= '</table>';			
            } 
            
			
			
            /* Incidents-ER-Data */
            $Qry = $Index->DB->prepare("SELECT All_Data.monID, Sum(All_Data.totID) AS totID FROM (SELECT imp_persheets_e.empID, imp_persheets_e.empCD,
            imp_persheets_e.yrID, imp_persheets_e.earlyID AS totID, imp_persheets_e.monID FROM imp_persheets_e WHERE imp_persheets_e.monID > 0 AND imp_persheets_e.empID = ".$memberID." AND Date(imp_persheets_e.prID) BETWEEN '".$Index->dateFormat($fdateID)."' AND '".$Index->dateFormat($tdateID)."') AS All_Data GROUP BY All_Data.monID ORDER BY All_Data.monID ASC ");
            if($Qry->execute())
            { 
                $Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $optID_4 .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $optID_4 .= '<thead><tr>';
                $optID_4 .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
                $optID_4 .= '<th style="background:#3C8DBC; color:white;">Month</th>';
                $optID_4 .= '<th style="background:#3C8DBC; color:white;">E.R. (Count)</th>';
                $optID_4 .= '</tr></thead>';
                if(is_array($Index->rows) && count($Index->rows) > 0)
                {
                    $totID_1 = 0; $Start = 1;
                    foreach($Index->rows as $row)
                    {
                        if($row['totID'] > 0)
                        {
                            $optID_4 .= '<tr>';
                            $optID_4 .= '<td width="60" align="center">'.$Start++.'</td>';
                            $optID_4 .= '<td>'.date('F', mktime(0, 0, 0, $row['monID'], 10)).'</td>';
                            $optID_4 .= '<td align="center">'.$row['totID'].'</td>'; 
                            $optID_4 .= '</tr>';

                            $totID_1 += $row['totID'];
                        }
                    }
                        if($totID_1 > 0)
                        {
                            $optID_4 .= '<tr>';
                                $optID_4 .= '<td colspan="2" align="right"><b>Totals :</b></td>';
                                $optID_4 .= '<td align="center"><b>'.$totID_1.'</b></td>';
                            $optID_4 .= '</tr>';
                        }
                }
                $optID_4 .= '</table>';			
            } 
            
            /* Incidents-LF-Data */
            $Qry = $Index->DB->prepare("SELECT All_Data.monID, Sum(All_Data.totID) AS totID FROM (SELECT imp_persheets_l.empID, imp_persheets_l.empCD,
            imp_persheets_l.yrID, imp_persheets_l.latefirstID AS totID, imp_persheets_l.monID FROM imp_persheets_l WHERE imp_persheets_l.monID > 0 AND imp_persheets_l.empID = ".$memberID." AND Date(imp_persheets_l.prID) BETWEEN '".$Index->dateFormat($fdateID)."' AND '".$Index->dateFormat($tdateID)."') AS All_Data GROUP BY All_Data.monID ORDER BY All_Data.monID ASC ");
            if($Qry->execute())
            {
                $Index->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                $optID_5 .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $optID_5 .= '<thead><tr>';
                $optID_5 .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
                $optID_5 .= '<th style="background:#3C8DBC; color:white;">Month</th>';
                $optID_5 .= '<th style="background:#3C8DBC; color:white;">L.F. (Count)</th>';
                $optID_5 .= '</tr></thead>';
                if(is_array($Index->rows) && count($Index->rows) > 0)
                {
                    $totID_1 = 0; $Start = 1;
                    foreach($Index->rows as $row)
                    {
                        if($row['totID'] > 0)
                        {
                            $optID_5 .= '<tr>';
                            $optID_5 .= '<td width="60" align="center">'.$Start++.'</td>';
                            $optID_5 .= '<td>'.date('F', mktime(0, 0, 0, $row['monID'], 10)).'</td>';
                            $optID_5 .= '<td align="center">'.$row['totID'].'</td>'; 
                            $optID_5 .= '</tr>';

                            $totID_1 += $row['totID'];
                        }
                    }
                        if($totID_1 > 0)
                        {
                            $optID_5 .= '<tr>';
                                $optID_5 .= '<td colspan="2" align="right"><b>Totals :</b></td>';
                                $optID_5 .= '<td align="center"><b>'.$totID_1.'</b></td>';
                            $optID_5 .= '</tr>';
                        }
                }
                $optID_5 .= '</table>';			
            } 
            
            
            $arr = array('optionID_1'=>$optID_1,'optionID_2'=>$optID_2,'optionID_3'=>$optID_3,'optionID_4'=>$optID_4,'optionID_5'=>$optID_5);
            echo json_encode($arr);
	}
	
?>