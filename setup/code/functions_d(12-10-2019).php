<?php
class DFunctions extends Functions
{
    function __construct()
    {	
        parent::__construct();
        
        $this->greenID = 'style="font-size:13px; font-family:Source Sans Pro, sans-serif; font-weight:bold; background:green; color:white;"';
        $this->redID   = 'style="font-size:13px; font-family:Source Sans Pro, sans-serif; font-weight:bold; background:red; color:white;"';
    }
        
    public function Data_Listsing_1($fdateID,$tdateID)
    {
		$returnID = 1;
        $file = '';
        $filters = "";
		
        $request['fromID'] = $fdateID;
        $request['toID']   = $tdateID;
        
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'sicklv.sldateID');}
        
        $SQL = "SELECT All_Outs.companyID, Sum(If(All_Outs.week_day = 'Monday', 1, 0)) AS Monday, Sum(If(All_Outs.week_day = 'Tuesday', 1, 0)) AS Tuesday,
        Sum(If(All_Outs.week_day = 'Wednesday', 1, 0)) AS Wednesday, Sum(If(All_Outs.week_day = 'Thursday', 1, 0)) AS Thursday, Sum(If(All_Outs.week_day = 'Friday', 1, 0)) AS Friday,
        Sum(If(All_Outs.week_day = 'Monday', 1, 0) + If(All_Outs.week_day = 'Tuesday', 1, 0) + If(All_Outs.week_day = 'Wednesday', 1, 0) + If(All_Outs.week_day = 'Thursday', 1, 0) + If(All_Outs.week_day = 'Friday', 1, 0)) AS Total
        FROM (SELECT sicklv.ID, sicklv.sldateID, sicklv.companyID, sicklv.empID, sicklv.ecodeID, sicklv.duration, DayName(sicklv.sldateID) AS week_day, Year(sicklv.sldateID) AS yearID
        FROM sicklv WHERE sicklv.duration <= 1 AND sicklv.sldateID <> '' ".$filters.") AS All_Outs GROUP BY All_Outs.companyID ORDER BY Total ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
            $file .= '<thead><tr>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Year</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Monday</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Tuesday</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Wednesday</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Thrusday</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Friday</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="130"><div align="center"><strong>Total</strong></div></th>';
            $file .= '</tr></thead>';
			
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $couID = count($this->rows_1); $fID_1 = 0;  $fID_2 = 0;  $fID_3 = 0;  $fID_4 = 0;  $fID_5 = 0;  $fID_6 = 0;
                foreach($this->rows_1 as $rows_1)
                {
                    $srsID++;
                    
                    $Qry_1 = $this->DB->prepare("SELECT All_Outs.companyID, All_Outs.yearID, Sum(If(All_Outs.week_day = 'Monday', 1, 0)) AS Monday, Sum(If(All_Outs.week_day = 'Tuesday', 1, 0)) AS Tuesday,
                    Sum(If(All_Outs.week_day = 'Wednesday', 1, 0)) AS Wednesday, Sum(If(All_Outs.week_day = 'Thursday', 1, 0)) AS Thursday, Sum(If(All_Outs.week_day = 'Friday', 1, 0)) AS Friday, Sum(If(All_Outs.week_day = 'Monday', 1, 0) + If(All_Outs.week_day = 'Tuesday', 1, 0) + If(All_Outs.week_day = 'Wednesday', 1, 0) + If(All_Outs.week_day = 'Thursday', 1, 0) + If(All_Outs.week_day = 'Friday', 1, 0)) AS Total
                    FROM (SELECT sicklv.ID, sicklv.sldateID, sicklv.companyID, sicklv.empID, sicklv.ecodeID, sicklv.duration, DayName(sicklv.sldateID) AS week_day, Year(sicklv.sldateID) AS yearID FROM sicklv WHERE sicklv.duration <= 1 AND
                    sicklv.sldateID <> '' ".$filters.") AS All_Outs WHERE All_Outs.companyID = ".$rows_1['companyID']." GROUP BY All_Outs.companyID, All_Outs.yearID ORDER BY All_Outs.yearID ASC ");
                    $Qry_1->execute();
                    $this->rows_2 = $Qry_1->fetchAll(PDO::FETCH_ASSOC);
                    if(is_array($this->rows_2) && count($this->rows_2) > 0)
                    {
                        $srID = 1;  $cousID = count($this->rows_2);
                        foreach($this->rows_2 as $rows_2)
                        {
							$returnID++;
                            $CM_Array  = $rows_2['companyID'] > 0   ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ")     : '';
                            
                            $file .= '<tr>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($cousID == 1 ? ($srsID - 1) : $srID++).'</td>';
                                if(($srID - 1) == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                else if($cousID == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['yearID'].'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Monday']    > 0 ? $rows_2['Monday']    : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Tuesday']   > 0 ? $rows_2['Tuesday']   : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Wednesday'] > 0 ? $rows_2['Wednesday'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Thursday']  > 0 ? $rows_2['Thursday']  : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Friday']    > 0 ? $rows_2['Friday']    : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Total']     > 0 ? $rows_2['Total']     : '').'</td>';
                            $file .= '</tr>';
                            
                            $fID_1 += $rows_2['Monday'];        $fID_2 += $rows_2['Tuesday'];
                            $fID_3 += $rows_2['Wednesday'];     $fID_4 += $rows_2['Thursday'];
                            $fID_5 += $rows_2['Friday'];        $fID_6 += $rows_2['Total'];
                        }
                    }
                    
                    if($cousID > 1)
                    {
                        $file .= '<tr>';
                        $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>Company Total : </b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Monday'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Tuesday'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Wednesday'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Thursday'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Friday'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.round($rows_1['Total'],2).'</b></td>';
                        $file .= '</tr>';
                    }
                }
            }
            
            $file .= '<tr>';
            $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>GTotal : </b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_1.'</b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_2.'</b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_3.'</b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_4.'</b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_5.'</b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_6,2).'</b></td>';
            $file .= '</tr>';
            
            $file .= '</table>';					
        } 
            
       $return['fileID'] = $file;
        $return['counID'] = $returnID;
        return $return; 
    }
    
    public function Data_Listsing_2($fdateID,$tdateID,$tableName,$fieldID)
    {
        $file = '';
        $filters = "";
		
        $request['fromID'] = $fdateID;
        $request['toID'] = $tdateID;
		
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,$tableName.'.prID');}
        
        $SQL = "SELECT All_Outputs.companyID, Sum(All_Outputs.Jan) AS Jan, Sum(All_Outputs.Feb) AS Feb, Sum(All_Outputs.Mar) AS Mar, Sum(All_Outputs.Apr) AS Apr,
        Sum(All_Outputs.May) AS May, Sum(All_Outputs.Jun) AS Jun, Sum(All_Outputs.Jul) AS Jul, Sum(All_Outputs.Aug) AS Aug, Sum(All_Outputs.Sep) AS Sep, Sum(All_Outputs.Ocb) AS Ocb,
        Sum(All_Outputs.Nov) AS Nov, Sum(All_Outputs.Dcm) AS Dcm, Sum(All_Outputs.totID) AS totID FROM (SELECT ".$tableName.".companyID, ".$tableName.".prID, If(".$tableName.".monID = 1, ".$tableName.".".$fieldID.", 0) AS Jan,
        If(".$tableName.".monID = 2, ".$tableName.".".$fieldID.", 0) AS Feb, If(".$tableName.".monID = 3, ".$tableName.".".$fieldID.", 0) AS Mar, If(".$tableName.".monID = 4, ".$tableName.".".$fieldID.", 0) AS Apr, If(".$tableName.".monID = 5, ".$tableName.".".$fieldID.", 0) AS May,
        If(".$tableName.".monID = 6, ".$tableName.".".$fieldID.", 0) AS Jun, If(".$tableName.".monID = 7, ".$tableName.".".$fieldID.", 0) AS Jul, If(".$tableName.".monID = 8, ".$tableName.".".$fieldID.", 0) AS Aug, If(".$tableName.".monID = 9, ".$tableName.".".$fieldID.", 0) AS Sep,
        If(".$tableName.".monID = 10, ".$tableName.".".$fieldID.", 0) AS Ocb, If(".$tableName.".monID = 11, ".$tableName.".".$fieldID.", 0) AS Nov, If(".$tableName.".monID = 12, ".$tableName.".".$fieldID.", 0) AS Dcm, ".$tableName.".".$fieldID." AS totID
        FROM ".$tableName." WHERE ".$tableName.".prID <> '' ".$filters.") AS All_Outputs GROUP BY All_Outputs.companyID HAVING Sum(All_Outputs.totID) > 0 Order BY totID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
            $file .= '<thead><tr>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Year</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>January</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>February</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>March</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>April</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>May</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>June</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>July</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>August</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>September</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>October</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>November</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>December</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;" width="80"><div align="center"><strong>Total</strong></div></th>';
            $file .= '</tr></thead>';
			
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {		 
                $srsID = 1; $couID = count($this->rows_1); 
                $fID_1 = 0;  $fID_2 = 0;  $fID_3 = 0;  $fID_4 = 0;  $fID_5 = 0;  $fID_6 = 0;
                $fID_7 = 0;  $fID_8 = 0;  $fID_9 = 0;  $fID_10 = 0;  $fID_11 = 0;  $fID_12 = 0; $fID_13 = 0;
                foreach($this->rows_1 as $rows_1)
                {
                    $srsID++;
                    $Qry_1 = $this->DB->prepare("SELECT All_Outputs.companyID, All_Outputs.yrID, Sum(All_Outputs.Jan) AS Jan, Sum(All_Outputs.Feb) AS Feb, Sum(All_Outputs.Mar) AS Mar, Sum(All_Outputs.Apr) AS Apr,
                    Sum(All_Outputs.May) AS May, Sum(All_Outputs.Jun) AS Jun, Sum(All_Outputs.Jul) AS Jul, Sum(All_Outputs.Aug) AS Aug, Sum(All_Outputs.Sep) AS Sep, Sum(All_Outputs.Ocb) AS Ocb, Sum(All_Outputs.Nov) AS Nov,
                    Sum(All_Outputs.Dcm) AS Dcm, Sum(All_Outputs.totID) AS totID FROM (SELECT ".$tableName.".companyID, ".$tableName.".prID, ".$tableName.".yrID, If(".$tableName.".monID = 1, ".$tableName.".".$fieldID.", 0) AS Jan, 
                    If(".$tableName.".monID = 2, ".$tableName.".".$fieldID.", 0) AS Feb, If(".$tableName.".monID = 3, ".$tableName.".".$fieldID.", 0) AS Mar, If(".$tableName.".monID = 4, ".$tableName.".".$fieldID.", 0) AS Apr, If(".$tableName.".monID = 5, ".$tableName.".".$fieldID.", 0) AS May,
                    If(".$tableName.".monID = 6, ".$tableName.".".$fieldID.", 0) AS Jun, If(".$tableName.".monID = 7, ".$tableName.".".$fieldID.", 0) AS Jul, If(".$tableName.".monID = 8, ".$tableName.".".$fieldID.", 0) AS Aug, If(".$tableName.".monID = 9, ".$tableName.".".$fieldID.", 0) AS Sep,
                    If(".$tableName.".monID = 10, ".$tableName.".".$fieldID.", 0) AS Ocb, If(".$tableName.".monID = 11, ".$tableName.".".$fieldID.", 0) AS Nov, If(".$tableName.".monID = 12, ".$tableName.".".$fieldID.", 0) AS Dcm, ".$tableName.".".$fieldID." AS totID
                    FROM ".$tableName." WHERE ".$tableName.".prID <> '' ".$filters.") AS All_Outputs WHERE All_Outputs.companyID = ".$rows_1['companyID']." GROUP BY All_Outputs.companyID, All_Outputs.yrID HAVING Sum(All_Outputs.totID) > 0 ORDER BY totID ASC ");
                    $Qry_1->execute();
                    $this->rows_2 = $Qry_1->fetchAll(PDO::FETCH_ASSOC);
                    if(is_array($this->rows_2) && count($this->rows_2) > 0)
                    {
                        $srID = 1;  $cousID = count($this->rows_2);	$returnID += count($this->rows_2);
                        foreach($this->rows_2 as $rows_2)
                        {
                            $CM_Array  = $rows_2['companyID'] > 0   ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ")     : '';
                            
                            $file .= '<tr>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($cousID == 1 ? ($srsID - 1) : $srID++).'</td>';
                                if(($srID - 1) == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                else if($cousID == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['yrID'].'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Jan'] > 0 ? $rows_2['Jan'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Feb'] > 0 ? $rows_2['Feb'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Mar'] > 0 ? $rows_2['Mar'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Apr'] > 0 ? $rows_2['Apr'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['May'] > 0 ? $rows_2['May'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Jun'] > 0 ? $rows_2['Jun'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Jul'] > 0 ? $rows_2['Jul'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Aug'] > 0 ? $rows_2['Aug'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Sep'] > 0 ? $rows_2['Sep'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Ocb'] > 0 ? $rows_2['Ocb'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Nov'] > 0 ? $rows_2['Nov'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['Dcm'] > 0 ? $rows_2['Dcm'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['totID'] > 0 ? $rows_2['totID'] : '').'</td>';
                            $file .= '</tr>'; 
                            
                            
                            $fID_1 += $rows_2['Jan'];        $fID_2 += $rows_2['Feb'];   $fID_3 += $rows_2['Mar'];     $fID_4 += $rows_2['Apr'];
                            $fID_5 += $rows_2['May'];        $fID_6 += $rows_2['Jun'];   $fID_7 += $rows_2['Jul'];     $fID_8 += $rows_2['Aug'];
                            $fID_9 += $rows_2['Sep'];        $fID_10 += $rows_2['Ocb'];  $fID_11 += $rows_2['Nov'];    $fID_12 += $rows_2['Dcm'];
                            $fID_13 += $rows_2['totID'];
                        }
                    }
                    
                    if($cousID > 1)
                    {
                        $file .= '<tr>';
                            $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>Company Total : </b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Jan'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Feb'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Mar'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Apr'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['May'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Jun'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Jul'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Aug'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Sep'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Ocb'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Nov'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['Dcm'].'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.round($rows_1['totID'],2).'</b></td>';
                        $file .= '</tr>';      
                    }
                } 
            }
            
                    $file .= '<tr>';
                            $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>GTotal : </b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_1.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_2.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_3.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_4.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_5.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_6.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_7.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_8.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_9.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_10.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_11.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_12.'</b></td>';
                            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_13,2).'</b></td>';
                        $file .= '</tr>';    
                    
            $file .= '</table>';					
        } 
            
       $return['fileID'] = $file;
        $return['counID'] = $returnID;
        return $return; 
    }
    
    public function Data_Listsing_3($fdateID,$tdateID)
    {
		$returnID = 1;
		
        $file = '';
        $filters = "";
		
        $request['fromID'] = $fdateID;
        $request['toID'] = $tdateID;
		
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'complaint.dateID');}
        
        $SQL = "SELECT All_Outs.companyID, Sum(All_Outs.fID_1) AS fID_1, Sum(All_Outs.fID_2) AS fID_2,
        Sum(All_Outs.fID_3) AS fID_3, Sum(All_Outs.fID_4) AS fID_4, Sum(All_Outs.fID_5) AS fID_5, Sum(All_Outs.fID_6) AS fID_6 FROM (SELECT
        complaint.ID,complaint.dateID, complaint.accID, complaint.substanID, complaint.faultID, complaint.driverID, complaint.dcodeID, If(complaint.creasonID = 19, 1, 0) AS fID_1,
		If(complaint.creasonID = 20, 1, 0) AS fID_2, If(complaint.creasonID = 21, 1, 0) AS fID_3, If(complaint.creasonID = 22, 1, 0) AS fID_4,
		If(complaint.creasonID = 23, 1, 0) AS fID_5, If(complaint.creasonID = 24, 1, 0) AS fID_6, complaint.companyID FROM complaint
		WHERE complaint.accID = 52 AND complaint.substanID = 1 AND complaint.faultID = 1 AND complaint.dateID <> '' ".$filters.") AS All_Outs
        LEFT JOIN employee ON employee.ID = All_Outs.driverID WHERE employee.status = 1 GROUP BY All_Outs.companyID 
        Order BY Sum((Coalesce(All_Outs.fID_1, 0) + Coalesce(All_Outs.fID_2, 0) + Coalesce(All_Outs.fID_3, 0) + Coalesce(All_Outs.fID_4, 0) + Coalesce(All_Outs.fID_5, 0) + Coalesce(All_Outs.fID_6, 0))) ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
            $file .= '<thead><tr>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Year</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - Assistance</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - Attitude</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - Driving Standard</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - Other</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - Presentation</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver - System Knowledge</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Total</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srsID = 1; $couID = count($this->rows_1); $fID_1 = 0;	$fID_2 = 0; $fID_3 = 0;	$fID_4 = 0; $fID_5 = 0;	$fID_6 = 0; $fID_7 = 0;
                foreach($this->rows_1 as $rows_1)
                {
                    $srsID++;
                    
                    $SQL = "SELECT All_Outs.companyID, All_Outs.yearID, Sum(All_Outs.fID_1) AS fID_1, Sum(All_Outs.fID_2) AS fID_2,
                    Sum(All_Outs.fID_3) AS fID_3, Sum(All_Outs.fID_4) AS fID_4, Sum(All_Outs.fID_5) AS fID_5, Sum(All_Outs.fID_6) AS fID_6 FROM (SELECT
                    complaint.ID,complaint.dateID, Year(complaint.dateID) AS yearID, complaint.accID, complaint.substanID, complaint.faultID, complaint.driverID, complaint.dcodeID, If(complaint.creasonID = 19, 1, 0) AS fID_1,
                    If(complaint.creasonID = 20, 1, 0) AS fID_2, If(complaint.creasonID = 21, 1, 0) AS fID_3, If(complaint.creasonID = 22, 1, 0) AS fID_4,
                    If(complaint.creasonID = 23, 1, 0) AS fID_5, If(complaint.creasonID = 24, 1, 0) AS fID_6, complaint.companyID FROM complaint
                    WHERE complaint.accID = 52 AND complaint.substanID = 1 AND complaint.faultID = 1 AND complaint.dateID <> '' ".$filters.") AS All_Outs
                    LEFT JOIN employee ON employee.ID = All_Outs.driverID WHERE employee.status = 1 AND All_Outs.companyID = ".$rows_1['companyID']." GROUP BY All_Outs.companyID, All_Outs.yearID
                    Order BY Sum((Coalesce(All_Outs.fID_1, 0) + Coalesce(All_Outs.fID_2, 0) + Coalesce(All_Outs.fID_3, 0) + Coalesce(All_Outs.fID_4, 0) + Coalesce(All_Outs.fID_5, 0) + Coalesce(All_Outs.fID_6, 0))) ASC ";
                    
                    $Qry_1 = $this->DB->prepare($SQL);
                    $Qry_1->execute();
                    $this->rows_2 = $Qry_1->fetchAll(PDO::FETCH_ASSOC);
                    if(is_array($this->rows_2) && count($this->rows_2) > 0)
                    {
                        $srID = 1; $cousID = count($this->rows_2);	
                        foreach($this->rows_2 as $rows_2)
                        {
							$returnID++;
                            $CM_Array  = $rows_2['companyID'] > 0   ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ")     : '';
                            
                            $file .= '<tr>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($cousID == 1 ? ($srsID - 1) : $srID++).'</td>';
                                if(($srID - 1) == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                else if($cousID == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['yearID'].'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_1'] > 0 ? $rows_2['fID_1'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_2'] > 0 ? $rows_2['fID_2'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_3'] > 0 ? $rows_2['fID_3'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_4'] > 0 ? $rows_2['fID_4'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_5'] > 0 ? $rows_2['fID_5'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_6'] > 0 ? $rows_2['fID_6'] : '').'</td>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($rows_2['fID_1'] + $rows_2['fID_2'] + $rows_2['fID_3'] + 
                                $rows_2['fID_4'] + $rows_2['fID_5'] + $rows_2['fID_6']).'</td>';
                            $file .= '</tr>';   
                            
                            $fID_1 += $rows_2['fID_1'];		$fID_2 += $rows_2['fID_2'];		$fID_3 += $rows_2['fID_3'];
                            $fID_4 += $rows_2['fID_4'];		$fID_5 += $rows_2['fID_5'];		$fID_6 += $rows_2['fID_6'];
                            $fID_7 += $rows_2['fID_1'] + $rows_2['fID_2'] + $rows_2['fID_3'] + $rows_2['fID_4'] + $rows_2['fID_5'] + $rows_2['fID_6'];
                        }
                    }
                    
                    if($cousID > 1)
                    {
                        $file .= '<tr>';
                        $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>Company Total : </b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_1'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_2'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_3'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_4'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_5'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$rows_1['fID_6'].'</b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.($rows_1['fID_1'] + $rows_1['fID_2'] + $rows_1['fID_3'] + $rows_1['fID_4'] + $rows_1['fID_5'] + $rows_1['fID_6']).'</b></td>';
                        $file .= '</tr>';
                    }
                    
                }  
			   
                    $file .= '<tr>';
                    $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>GTotal : </b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_1.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_2.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_3.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_4.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_5.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_6.'</b></td>';
                    $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_7.'</b></td>';
                    $file .= '</tr>';
            }
            $file .= '</table>';			
        } 
            
        $return['fileID'] = $file;
        $return['counID'] = $returnID;
        return $return; 
    }
    
    public function Data_Listsing_4($fdateID,$tdateID)
    {
		$returnID = 1;
		
        $file = '';
        $filters = "";
		
        $request['fromID'] = $fdateID;  $request['toID'] = $tdateID;		
        if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'accident_regis.dateID');}
        $tableNM = '';  $tableNM = 'accident_regis.dateID';
        $tableFL = '';  $tableFL = 'Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0),0)';
        
        $SQL = "SELECT All_Outputs.companyID, Sum(All_Outputs.Jan_F) AS Jan_F, Sum(All_Outputs.Jan_C) AS Jan_C, Sum(All_Outputs.Feb_F) AS Feb_F, Sum(All_Outputs.Feb_C) AS Feb_C,
        Sum(All_Outputs.Mar_F) AS Mar_F, Sum(All_Outputs.Mar_C) AS Mar_C, Sum(All_Outputs.Apr_F) AS Apr_F, Sum(All_Outputs.Apr_C) AS Apr_C, Sum(All_Outputs.May_F) AS May_F,
        Sum(All_Outputs.May_C) AS May_C, Sum(All_Outputs.Jun_F) AS Jun_F, Sum(All_Outputs.Jun_C) AS Jun_C, Sum(All_Outputs.Jul_F) AS Jul_F, Sum(All_Outputs.Jul_C) AS Jul_C,
        Sum(All_Outputs.Aug_F) AS Aug_F, Sum(All_Outputs.Aug_C) AS Aug_C, Sum(All_Outputs.Sep_F) AS Sep_F, Sum(All_Outputs.Sep_C) AS Sep_C, Sum(All_Outputs.Oct_F) AS Oct_F,
        Sum(All_Outputs.Oct_C) AS Oct_C, Sum(All_Outputs.Nov_F) AS Nov_F, Sum(All_Outputs.Nov_C) AS Nov_C, Sum(All_Outputs.Dec_F) AS Dec_F, Sum(All_Outputs.Dec_C) AS Dec_C,
        Sum(All_Outputs.tot_F) AS tot_F, Sum(All_Outputs.tot_C) AS tot_C FROM (SELECT accident_regis.ID, accident_regis.dateID, accident_regis.responsibleID,
        If(Month(".$tableNM.") = 1, 1, 0) AS Jan_F, If(Month(".$tableNM.") = 1, ".$tableFL." AS Jan_C, If(Month(".$tableNM.") = 2, 1, 0) AS Feb_F, If(Month(".$tableNM.") = 2, ".$tableFL." AS Feb_C,
        If(Month(".$tableNM.") = 3, 1, 0) AS Mar_F, If(Month(".$tableNM.") = 3, ".$tableFL." AS Mar_C, If(Month(".$tableNM.") = 4, 1, 0) AS Apr_F, If(Month(".$tableNM.") = 4, ".$tableFL." AS Apr_C,
        If(Month(".$tableNM.") = 5, 1, 0) AS May_F, If(Month(".$tableNM.") = 5, ".$tableFL." AS May_C, If(Month(".$tableNM.") = 6, 1, 0) AS Jun_F, If(Month(".$tableNM.") = 6, ".$tableFL." AS Jun_C,
        If(Month(".$tableNM.") = 7, 1, 0) AS Jul_F, If(Month(".$tableNM.") = 7, ".$tableFL." AS Jul_C, If(Month(".$tableNM.") = 8, 1, 0) AS Aug_F, If(Month(".$tableNM.") = 8, ".$tableFL." AS Aug_C,
        If(Month(".$tableNM.") = 9, 1, 0) AS Sep_F, If(Month(".$tableNM.") = 9, ".$tableFL." AS Sep_C, If(Month(".$tableNM.") = 10, 1, 0) AS Oct_F,If(Month(".$tableNM.") = 10, ".$tableFL." AS Oct_C,
        If(Month(".$tableNM.") = 11, 1, 0) AS Nov_F, If(Month(".$tableNM.") = 11, ".$tableFL." AS Nov_C, If(Month(".$tableNM.") = 12, 1, 0) AS Dec_F, If(Month(".$tableNM.") = 12, ".$tableFL." AS Dec_C,
        accident_regis.companyID, 1 AS tot_F, (Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0)) AS tot_C FROM accident_regis WHERE accident_regis.dateID <> '' 
        AND accident_regis.responsibleID = 1 ".$filters.") AS All_Outputs GROUP BY All_Outputs.companyID ORDER BY tot_F ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $prID = '<th colspan="2" class="knob-labels notices" style="color:white;"><div align="center"><strong>';
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
            $file .= '<thead><tr>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Sr. No.</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Year</strong></div></th>';
            $file .= $prID.'January</strong></div></th>';
            $file .= $prID.'February</strong></div></th>';
            $file .= $prID.'March</strong></div></th>';
            $file .= $prID.'April</strong></div></th>';
            $file .= $prID.'May</strong></div></th>';
            $file .= $prID.'June</strong></div></th>';
            $file .= $prID.'July</strong></div></th>';
            $file .= $prID.'August</strong></div></th>';
            $file .= $prID.'September</strong></div></th>';
            $file .= $prID.'October</strong></div></th>';
            $file .= $prID.'November</strong></div></th>';
            $file .= $prID.'December</strong></div></th>';
            $file .= $prID.'Total</strong></div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srsID = 1; $couID = count($this->rows_1); 
                $fID_F_1 = 0;	$fID_C_1 = 0;   $fID_F_2 = 0;	$fID_C_2 = 0;   $fID_F_3 = 0;	$fID_C_3 = 0;
                $fID_F_4 = 0;	$fID_C_4 = 0;   $fID_F_5 = 0;	$fID_C_5 = 0;   $fID_F_5 = 0;	$fID_C_5 = 0;
                $fID_F_7 = 0;	$fID_C_7 = 0;   $fID_F_8 = 0;	$fID_C_8 = 0;   $fID_F_9 = 0;	$fID_C_9 = 0;
                $fID_F_10 = 0;	$fID_C_10 = 0;  $fID_F_11 = 0;	$fID_C_11 = 0;  $fID_F_12 = 0;	$fID_C_12 = 0;  $fID_F_13 = 0;	$fID_C_13 = 0;
                foreach($this->rows_1 as $rows_1)
                {
                     $srsID++;
                    
                    $SQL_1 = "SELECT All_Outputs.companyID, All_Outputs.yearID, Sum(All_Outputs.Jan_F) AS Jan_F, Sum(All_Outputs.Jan_C) AS Jan_C, Sum(All_Outputs.Feb_F) AS Feb_F, Sum(All_Outputs.Feb_C) AS Feb_C,
                    Sum(All_Outputs.Mar_F) AS Mar_F, Sum(All_Outputs.Mar_C) AS Mar_C, Sum(All_Outputs.Apr_F) AS Apr_F, Sum(All_Outputs.Apr_C) AS Apr_C, Sum(All_Outputs.May_F) AS May_F,
                    Sum(All_Outputs.May_C) AS May_C, Sum(All_Outputs.Jun_F) AS Jun_F, Sum(All_Outputs.Jun_C) AS Jun_C, Sum(All_Outputs.Jul_F) AS Jul_F, Sum(All_Outputs.Jul_C) AS Jul_C,
                    Sum(All_Outputs.Aug_F) AS Aug_F, Sum(All_Outputs.Aug_C) AS Aug_C, Sum(All_Outputs.Sep_F) AS Sep_F, Sum(All_Outputs.Sep_C) AS Sep_C, Sum(All_Outputs.Oct_F) AS Oct_F,
                    Sum(All_Outputs.Oct_C) AS Oct_C, Sum(All_Outputs.Nov_F) AS Nov_F, Sum(All_Outputs.Nov_C) AS Nov_C, Sum(All_Outputs.Dec_F) AS Dec_F, Sum(All_Outputs.Dec_C) AS Dec_C,
                    Sum(All_Outputs.tot_F) AS tot_F, Sum(All_Outputs.tot_C) AS tot_C FROM (SELECT accident_regis.ID, accident_regis.dateID, Year(accident_regis.dateID) AS yearID, accident_regis.responsibleID,
                    If(Month(".$tableNM.") = 1, 1, 0) AS Jan_F, If(Month(".$tableNM.") = 1, ".$tableFL." AS Jan_C, If(Month(".$tableNM.") = 2, 1, 0) AS Feb_F, If(Month(".$tableNM.") = 2, ".$tableFL." AS Feb_C,
                    If(Month(".$tableNM.") = 3, 1, 0) AS Mar_F, If(Month(".$tableNM.") = 3, ".$tableFL." AS Mar_C, If(Month(".$tableNM.") = 4, 1, 0) AS Apr_F, If(Month(".$tableNM.") = 4, ".$tableFL." AS Apr_C,
                    If(Month(".$tableNM.") = 5, 1, 0) AS May_F, If(Month(".$tableNM.") = 5, ".$tableFL." AS May_C, If(Month(".$tableNM.") = 6, 1, 0) AS Jun_F, If(Month(".$tableNM.") = 6, ".$tableFL." AS Jun_C,
                    If(Month(".$tableNM.") = 7, 1, 0) AS Jul_F, If(Month(".$tableNM.") = 7, ".$tableFL." AS Jul_C, If(Month(".$tableNM.") = 8, 1, 0) AS Aug_F, If(Month(".$tableNM.") = 8, ".$tableFL." AS Aug_C,
                    If(Month(".$tableNM.") = 9, 1, 0) AS Sep_F, If(Month(".$tableNM.") = 9, ".$tableFL." AS Sep_C, If(Month(".$tableNM.") = 10, 1, 0) AS Oct_F,If(Month(".$tableNM.") = 10, ".$tableFL." AS Oct_C,
                    If(Month(".$tableNM.") = 11, 1, 0) AS Nov_F, If(Month(".$tableNM.") = 11, ".$tableFL." AS Nov_C, If(Month(".$tableNM.") = 12, 1, 0) AS Dec_F, If(Month(".$tableNM.") = 12, ".$tableFL." AS Dec_C,
                    accident_regis.companyID, 1 AS tot_F, (Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0)) AS tot_C FROM accident_regis WHERE accident_regis.dateID <> '' 
                    AND accident_regis.responsibleID = 1 AND accident_regis.companyID = ".$rows_1['companyID']." ".$filters.") AS All_Outputs GROUP BY All_Outputs.companyID, All_Outputs.yearID ORDER BY tot_F ASC ";
                    $Qry_1 = $this->DB->prepare($SQL_1);
                    $Qry_1->execute();
                    $this->rows_2 = $Qry_1->fetchAll(PDO::FETCH_ASSOC);
                    if(is_array($this->rows_2) && count($this->rows_2) > 0)
                    {
                        $srID = 1;  $cousID = count($this->rows_2);	
                        foreach($this->rows_2 as $rows_2)
                        {
							$returnID++;
							
                            $CM_Array  = $rows_2['companyID'] > 0   ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ")     : '';
                            
                            $file .= '<tr>';
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.($cousID == 1 ? ($srsID - 1) : $srID++).'</td>';
                                if(($srID - 1) == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                else if($cousID == 1)
                                {
                                    $file .= '<td '.($cousID > 1 ? 'rowspan="'.$cousID.'"' : '').' '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).'><b>'.strtoupper($CM_Array[0]['title']).'</b></td>';
                                }
                                
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.($rows_2['yearID']).'</td>';
                                
								$file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Jan_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Jan_C'],2).'</td>';
								
								                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Feb_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Feb_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Mar_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Mar_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Apr_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Apr_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['May_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['May_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Jun_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Jun_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Jul_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Jul_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Aug_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Aug_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Sep_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Sep_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Oct_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Oct_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Nov_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Nov_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['Dec_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['Dec_C'],2).'</td>';                    
                                $file .= '<td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="center">'.$rows_2['tot_F'].'</td><td '.($srsID == 2 ? $this->greenID :($srsID == ($couID + 1) ? $this->redID : 'style="font-size:13px; background:white;"')).' align="right">'.round($rows_2['tot_C'],2).'</td>';
                            $file .= '</tr>';
                            
                            $fID_F_1  += $rows_2['Jan_F'];   $fID_C_1 += $rows_2['Jan_C'];      $fID_F_2  += $rows_2['Feb_F'];   $fID_C_2 += $rows_2['Feb_C'];
                            $fID_F_3  += $rows_2['Mar_F'];   $fID_C_3 += $rows_2['Mar_C'];      $fID_F_4  += $rows_2['Apr_F'];   $fID_C_4 += $rows_2['Apr_C'];
                            $fID_F_5  += $rows_2['May_F'];   $fID_C_5 += $rows_2['May_C'];      $fID_F_6  += $rows_2['Jun_F'];   $fID_C_6 += $rows_2['Jun_C'];
                            $fID_F_7  += $rows_2['Jul_F'];   $fID_C_7 += $rows_2['Jul_C'];      $fID_F_8  += $rows_2['Aug_F'];   $fID_C_8 += $rows_2['Aug_C'];
                            $fID_F_9  += $rows_2['Sep_F'];   $fID_C_9 += $rows_2['Sep_C'];      $fID_F_10 += $rows_2['Oct_F'];   $fID_C_10 += $rows_2['Oct_C'];
                            $fID_F_11 += $rows_2['Nov_F'];   $fID_C_11 += $rows_2['Nov_C'];     $fID_F_12 += $rows_2['Dec_F'];   $fID_C_12 += $rows_2['Dec_C'];
                            $fID_F_13 += $rows_2['tot_F'];   $fID_C_13 += $rows_2['tot_C'];
                    
                        }
                    }
                    
                    if($cousID > 1)
                    {
                        $file .= '<tr>';
                        $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>Company Total : </b></td>';
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Jan_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Jan_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Feb_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Feb_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Mar_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Mar_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Apr_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Apr_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['May_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['May_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Jun_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Jun_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Jul_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Jul_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Aug_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Aug_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Sep_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Sep_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Oct_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Oct_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Nov_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Nov_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['Dec_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['Dec_C'],2).'</td>';                    
                        $file .= '<td style="background:#367FA9; color:white;" align="center">'.$rows_1['tot_F'].'</td><td style="background:#367FA9; color:white;" align="right">'.round($rows_1['tot_C'],2).'</td>';
                        $file .= '</tr>';
                    }                    
                }
            }

            $file .= '<tr>';
            $file .= '<td style="background:#367FA9; color:white;" colspan="3" align="right"><b>GTotal : </b></td>';
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_1.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_1,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_2.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_2,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_3.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_3,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_4.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_4,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_5.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_5,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_6.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_6,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_7.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_7,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_8.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_8,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_9.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_9,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_10.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_10,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_11.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_11,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_12.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_12,2).'</b></td>';                    
            $file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_F_13.'</b></td><td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_C_13,2).'</b></td>';
            $file .= '</tr>';                        
            $file .= '</table>';			
        } 
            
        $return['fileID'] = $file;
        $return['counID'] = $returnID;
        return $return; 
    }
	
    public function Incidents_ER($fdateID,$tdateID)
    {
        $crtID = "";
        $crtID .= " AND DATE(imp_persheets_e.prID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' ";
		
        $file = '';
        $Qry = $this->DB->prepare("SELECT FO.companyID, FO.empID, FO.empCD, FO.full_name, Sum(FO.earlyID) AS totID, Sum(FO.countID) AS countID FROM (SELECT
        employee.companyID, imp_persheets_e.empID, imp_persheets_e.empCD, imp_persheets_e.monID, imp_persheets_e.yrID, imp_persheets_e.earlyID, 
        employee.full_name, 1 AS countID FROM imp_persheets_e INNER JOIN employee ON employee.ID = imp_persheets_e.empID WHERE 
        employee.companyID = ".$_SESSION[$this->website]['compID']." AND imp_persheets_e.companyID = ".$_SESSION[$this->website]['compID']." AND imp_persheets_e.earlyID > 0 ".$crtID.") 
		AS FO GROUP BY FO.companyID, FO.empID, FO.empCD, FO.full_name ORDER BY totID DESC ");
        if($Qry->execute())
        {
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
            $file .= '<th style="background:#3C8DBC; color:white;">E. Code</th>';
            $file .= '<th style="background:#3C8DBC; color:white;">E. Name</th>';
            //$file .= '<th style="background:#3C8DBC; color:white; text-align:center;">Count</th>';
            $file .= '<th style="background:#3C8DBC; color:white; text-align:center;">No of Instances</th>';            
            $file .= '<th style="background:#3C8DBC; color:white; text-align:center;" width="50%">Monthly Details</th>';
            $file .= '<th style="background:#3C8DBC; color:white;"></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $totID_1 = 0; $totID_2 = 0; $Start = 1; $urlID = '';
                foreach($this->rows as $row)
                {
                    $urlID = 'imp_persheets_e/earlyID/'.$this->dateFormat($fdateID).'/'.$this->dateFormat($tdateID).'/'.$row['empID'];
                    
                    $file .= '<tr>';
                    $file .= '<td width="60" align="center">'.$Start++.'</td>';
                    $file .= '<td align="center" width="65">'.$row['empCD'].'</td>';
                    $file .= '<td>'.strtoupper($row['full_name']).'</td>';
                    $file .= '<td align="center">'.$row['totID'].'</td>'; 
                    //$file .= '<td align="center">'.$row['countID'].'</td>'; 
                    $file .= '<td>'.($this->GET_ER_Values('imp_persheets_e','earlyID',$fdateID,$tdateID,$row['empID'])).'</td>'; 
                    $file .= '<td align="center"><a style="text-declaration:none; cursor:pointer;" aria-sort="'.$urlID.'" class="fa fa-desktop dashboard_viewID"></a></td>';
                    $file .= '</tr>';

                    $totID_1 += $row['totID'];  $totID_2 += $row['countID'];
                }
                    $file .= '<tr>';
                        $file .= '<td colspan="3" align="right"><b>Totals :</b></td>';
                        $file .= '<td align="center"><b>'.$totID_1.'</b></td>';
                        $file .= '<td align="center"><b>'.$totID_2.'</b></td>';
                        $file .= '<td colspan="2"></td>';
                    $file .= '</tr>';
            }
            $file .= '</table>';			
        } 
            
        return $file;
    }
    
    public function Incidents_LF($fdateID,$tdateID)
    {
        $crtID = "";
        $crtID .= " AND DATE(imp_persheets_l.prID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' ";
		
        $file = '';
                
        $Qry = $this->DB->prepare("SELECT FO.companyID, FO.empID, FO.empCD, FO.full_name, Sum(FO.latefirstID) AS totID, Sum(FO.countID) AS countID FROM
        (SELECT employee.companyID, imp_persheets_l.empID, imp_persheets_l.empCD, imp_persheets_l.monID, imp_persheets_l.yrID, imp_persheets_l.latefirstID,
        employee.full_name, 1 AS countID FROM imp_persheets_l INNER JOIN employee ON employee.ID = imp_persheets_l.empID WHERE employee.companyID = 
		".$_SESSION[$this->website]['compID']." AND imp_persheets_l.companyID = ".$_SESSION[$this->website]['compID']." AND imp_persheets_l.latefirstID > 0 ".$crtID.") AS FO GROUP BY FO.companyID, FO.empID, FO.empCD, 
		FO.full_name ORDER BY totID DESC ");
        if($Qry->execute())
        {
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';				
            $file .= '<thead><tr>';
            $file .= '<th style="background:#3C8DBC; color:white;">Sr. No.</th>';
            $file .= '<th style="background:#3C8DBC; color:white;">E. Code</th>';
            $file .= '<th style="background:#3C8DBC; color:white;">E. Name</th>';
            //$file .= '<th style="background:#3C8DBC; color:white; text-align:center;">Count</th>';
            $file .= '<th style="background:#3C8DBC; color:white; text-align:center;">No of Instances</th>';            
            $file .= '<th style="background:#3C8DBC; color:white; text-align:center;" width="50%">Monthly Details</th>';
            $file .= '<th style="background:#3C8DBC; color:white;">&nbsp</th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $totID_1 = 0; $totID_2 = 0; $Start = 1; $urlID = '';
                foreach($this->rows as $row)
                {
                    $urlID = 'imp_persheets_l/latefirstID/'.$this->dateFormat($fdateID).'/'.$this->dateFormat($tdateID).'/'.$row['empID'];
                    
                    $file .= '<tr>';
                    $file .= '<td width="60" align="center">'.$Start++.'</td>';
                    $file .= '<td align="center" width="65">'.$row['empCD'].'</td>';
                    $file .= '<td>'.strtoupper($row['full_name']).'</td>';
                    $file .= '<td align="center">'.$row['totID'].'</td>'; 
                    //$file .= '<td align="center">'.$row['countID'].'</td>'; 
                    $file .= '<td>'.($this->GET_ER_Values('imp_persheets_l','latefirstID',$fdateID,$tdateID,$row['empID'])).'</td>'; 
                    $file .= '<td align="center"><a style="text-declaration:none; cursor:pointer;" aria-sort="'.$urlID.'" class="fa fa-desktop dashboard_viewID"></a></td>';
                    $file .= '</tr>';

                    $totID_1 += $row['totID'];  $totID_2 += $row['countID'];
                }
                    $file .= '<tr>';
                        $file .= '<td colspan="3" align="right"><b>Totals :</b></td>';
                        $file .= '<td align="center"><b>'.$totID_1.'</b></td>';
                        $file .= '<td align="center"><b>'.$totID_2.'</b></td>';
                        $file .= '<td colspan="2"></td>';
                    $file .= '</tr>';
            }
            $file .= '</table>';			
        } 

        return $file;
    }
    
    public function GET_ER_Values($tableName,$fieldID,$fdateID,$tdateID,$empID)
    {
        $return = '';
        if(!empty($tableName) && !empty($fdateID) && !empty($tdateID) && !empty($empID))
        {
			$crtID  = "";
			$crtID .= " AND DATE(".$tableName.".prID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' ";
			
            $Qry = $this->DB->prepare("SELECT ".$fieldID." , monID FROM ".$tableName." INNER JOIN employee ON employee.ID = empID 
            WHERE employee.companyID = ".$_SESSION[$this->website]['compID']." AND empID = ".$empID." ".$crtID." AND ".$fieldID." > 0 ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $countID = count($this->rows);
            
            if(is_array($this->rows) && count($this->rows) > 0)
            {
                $srID = 1;
                foreach($this->rows as $rows)
                {
                    /*if($countID > 1)
                    {  */  
                        $return .= ($srID == 1 ? $rows[$fieldID].' ('.date("F", mktime(0, 0, 0, $rows['monID'], 10)).')'	
									     : ' , '.$rows[$fieldID].' ('.date("F", mktime(0, 0, 0, $rows['monID'], 10)).')');                    
                    /*}
                    else
                    {
                        $return .= $rows[$fieldID];
                    }*/
                    $srID++;
                }
            }
            
        }        
        return $return;
    }

    public function SignOn_Listsing_1($arrDATA)
    {
        $file = '';
		extract($arrDATA);
        
        $SQL = "SELECT * FROM company WHERE ID > 0 AND ID In(".$companyID.") Order By title ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
            $file .= '<thead><tr>';            
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot</strong></div></th>';            
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Late Sign on Incidents</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Late Sign on Precentage</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center">&nbsp;</div></th>';
            $file .= '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totD = 0; $lateD = 0; $perD = 0;
                foreach($this->rows_1 as $rows_1)
                {
                    $totD   = $this->count_rows('imp_shift_daily'," LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND shift_masters_dtl.recID = imp_shift_daily.shift_recID INNER JOIN (Select ID As signID From signon_logs Where fromID = 'TOUCHPAD' AND DATE(signon_logs.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' Group By ID Order By signID) SignonTouchPAD On signID = imp_shift_daily.recID WHERE If(shift_masters_dtl.fID_019 = 'N' AND imp_shift_daily.tagCD = 'B', 0, 1) = 1 AND imp_shift_daily.singinID <> '' AND imp_shift_daily.companyID = ".$rows_1['ID']." AND If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) > 0 AND imp_shift_daily.choppedID <= 0 AND (DATE(imp_shift_daily.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."') ");

					$SQL = "Select Sum(FO.countID) As countID From (Select Profile_2.empID, Count(1) As countID, Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm))) As timeDF From (Select imp_shift_daily.recID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join
					shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID Inner Join (Select signon_logs.ID As dailyID From signon_logs Where signon_logs.fromID = 'TOUCHPAD' AND DATE(signon_logs.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' Group By signon_logs.ID) SignOn_Logs On SignOn_Logs.dailyID = imp_shift_daily.recID
					Where imp_shift_daily.companyID = ".$rows_1['ID']." AND imp_shift_daily.choppedID <= 0 AND imp_shift_daily.dateID BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' And If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%') As Profile_2 Group By Profile_2.empID Order By countID Desc) As FO ";
					$Qry = $this->DB->prepare($SQL);
					$Qry->execute();
					$this->rowsL = $Qry->fetch(PDO::FETCH_ASSOC);
					$lateD = $this->rowsL['countID'];
					
                    $perD = round($lateD / $totD * 100,2);
                    
                    $file .= '<tr>';                        
                        $file .= '<td style="font-size:13px; background:white;"><b>'.strtoupper($rows_1['title'].' - '.$rows_1['pscode']).'</b></td>';                        
                        $file .= '<td align="center" style="font-size:13px; background:white;"><b>'.($lateD > 0 ? $lateD : '').'</b></td>';
                        $file .= '<td align="center" style="font-size:13px; background:white;"><b>'.($perD > 0 ? $perD.' %' : '').'</b></td>';                    
                        $file .= '<td align="center" style="font-size:13px; background:white;"><a target="blank" href="'.$this->home.'profile_5.php?typeID=2&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$rows_1['ID'].'" class="fa fa-mail-forward"></a></td>';
                    $file .= '</tr>';
                    
                    $fID_1 += $totD;
                    $fID_2 += $lateD;
                }
            }
            
            $file .= '<tr>';
				$file .= '<td style="background:#367FA9; color:white;" align="right"><b>Grand Total : </b></td>';            
				$file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_2.'</b></td>';
				$file .= '<td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_2 / $fID_1 * 100,2).' %</b></td>';
				$file .= '<td style="background:#367FA9; color:white;" align="center"></td>';
            $file .= '</tr>';
            
            $file .= '</table>';					
        }
        
        $return['fileID'] = $file;
        return $return; 
    }
    
    public function SignOn_Listsing_2($arrDATA)
    {
		extract($arrDATA);
		
        $file = '';
        $filters = "";
		
		$urlID_211 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=211&fID_2=0&fID_3=0';
		$urlID_212 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=212&fID_2=0&fID_3=0';
		 
		$urlID_221 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=0&fID_2=221&fID_3=0';
		$urlID_222 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=0&fID_2=222&fID_3=0';
		
		$urlID_231 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=0&fID_2=0&fID_3=231';
		$urlID_232 = $this->home.'profile_5.php?typeID=2&sidebarID='.$sidebarID.'&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&fID_1=0&fID_2=0&fID_3=232';
		
		$sortFILTER = "";
		if($fID_1 == 211)	/* Depot Filter ASC */
		{
			$sortFILTER = " Order By Profile_2.companyID ASC ";
		}
		else if($fID_1 == 212)	/* Depot Filter DESC */
		{
			$sortFILTER = " Order By Profile_2.companyID DESC ";
		}
		else if($fID_2 == 221)	/* Number of Late Sign ons Filter ASC */
		{
			$sortFILTER = " Order By countID ASC ";
		}
		else if($fID_2 == 222)	/* Number of Late Sign ons Filter DESC */
		{
			$sortFILTER = " Order By countID DESC ";
		}
		else if($fID_3 == 231)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) DESC ";
		}
		else if($fID_3 == 232)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) ASC ";
		}
		else
		{ 
			$sortFILTER = " ORDER BY countID DESC";
		}
		
		$SQL = "SELECT Profile_2.empID, Count(1) AS countID, Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm))) AS timeDF, Profile_2.companyID FROM  (SELECT imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) AS ontimeID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) AS empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B',
		shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) AS diffTm FROM imp_shift_daily INNER JOIN shift_masters_dtl ON shift_masters_dtl.recID = imp_shift_daily.shift_recID INNER JOIN (SELECT signon_logs.ID AS dailyID FROM signon_logs WHERE signon_logs.fromID = 'TOUCHPAD' AND DATE(signon_logs.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' GROUP BY signon_logs.ID) SignOn_Logs ON SignOn_Logs.dailyID = imp_shift_daily.recID WHERE imp_shift_daily.choppedID <= 0 AND imp_shift_daily.companyID In(".$companyID.") AND imp_shift_daily.dateID BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' AND
		If(shift_masters_dtl.fID_019 = 'N' AND imp_shift_daily.tagCD = 'B', 0, 1) = 1 AND TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) LIKE '%-%') AS Profile_2 GROUP BY Profile_2.empID ".$sortFILTER;         
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
            $file .= '<thead><tr>';
			
			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot'; 
			$file .= '<a href="'.$urlID_211.'" style="margin-left:10px; cursor:pointer; color:'.($fID_1 == 211 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_212.'" style="margin-left:10px; cursor:pointer; color:'.($fID_1 == 212 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';


			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver Code</strong></div></th>';
			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver Name</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Number of Late Sign ons'; 
			$file .= '<a href="'.$urlID_221.'" style="margin-left:10px; cursor:pointer; color:'.($fID_2 == 221 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_222.'" style="margin-left:10px; cursor:pointer; color:'.($fID_2 == 222 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Accumulative Late Minutes'; 
			$file .= '<a href="'.$urlID_231.'" style="margin-left:10px; cursor:pointer; color:'.($fID_3 == 231 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_232.'" style="margin-left:10px; cursor:pointer; color:'.($fID_3 == 232 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center">&nbsp;</div></th>';
			$file .= '</tr></thead>';
			
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;  $late_B = '';
                foreach($this->rows_1 as $rows_1)
                {
					$arrCM = $rows_1['companyID'] > 0 ? $this->select('company',array("*")," WHERE ID = ".$rows_1['companyID']." ") : '';
                    $arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					
					$file .= '<tr>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$arrCM[0]['title'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					$file .= '<td style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.($rows_1['countID'] > 0 ? $rows_1['countID'] : '').'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.str_replace("-","",$rows_1['timeDF']).'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><a target="new" href="'.$this->home.'profile_5.php?typeID=3&fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$rows_1['companyID'].'&empID='.$rows_1['empID'].'" class="fa fa-mail-forward"></a></td>';
					$file .= '</tr>';
                }
            }
            
            $file .= '<tr height="25"><td style="background:#367FA9; color:white;" colspan="6"></td></tr>';            
            $file .= '</table>';					
        } 
            
       $return['fileID'] = $file;
        return $return; 
    }
    
    public function SignOn_Listsing_3($arrDATA)
    {
		extract($arrDATA);
		
		$arrEM = $arrDATA['employeeCD'] <> '' ? $this->select('employee',array("*")," WHERE code = '".$arrDATA['employeeCD']."' AND status = 1 ") : '';
		$employeeID = $arrEM[0]['ID'] > 0 ? $arrEM[0]['ID'] : $employeeID;
		
        $file = '';
        $filters = "";
		
		$sortFILTER = "";
		$sortFILTER = " Order By DATE(imp_shift_daily.dateID), imp_shift_daily.tagCD,imp_shift_daily.fID_1 ASC "; 
		
		$SQL = "SELECT imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, imp_shift_daily.fID_1, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) AS ontimeID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) AS empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) AS diffTm
		FROM imp_shift_daily INNER JOIN shift_masters_dtl ON shift_masters_dtl.recID = imp_shift_daily.shift_recID INNER JOIN (SELECT signon_logs.ID AS dailyID FROM signon_logs WHERE signon_logs.fromID = 'TOUCHPAD' AND DATE(signon_logs.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' GROUP BY signon_logs.ID) SignOn_Logs ON SignOn_Logs.dailyID = imp_shift_daily.recID WHERE imp_shift_daily.choppedID <= 0 AND imp_shift_daily.companyID In(".$companyID.") AND imp_shift_daily.dateID BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' AND If(shift_masters_dtl.fID_019 = 'N' AND imp_shift_daily.tagCD = 'B', 0, 1) = 1 AND
		TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) LIKE '%-%' AND If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) = ".$employeeID." ".$sortFILTER;
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
            $file .= '<thead>';
            $file .= '<tr>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Date</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Shift No</strong></div></th>';
			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Shift Tag</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>On Time</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>SignOn Time</strong></div></th>';
            $file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Late by Time</strong></div></th>';
            $file .= '</tr>';            
            $file .= '</thead>';
            
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;
                $lateA = '';    $lateB = '';
                foreach($this->rows_1 as $rows_1)
                { 
					$file .= '<tr>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.date('d-M-Y',strtotime($rows_1['dateID'])).'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['fID_1'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['tagCD'].'</b></td>';
					
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['ontimeID'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['singinID'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.substr((str_replace("-","",$rows_1['diffTm'])), 0, 8).'</b></td>';
					$file .= '</tr>';
                }
            }
            
            $file .= '<tr height="35">';
                $file .= '<td style="background:#367FA9; color:white;" colspan="3" ></td>';
                $file .= '<td style="background:#367FA9; color:white;" colspan="3" ></td>';
            $file .= '</tr>';
            
            $file .= '</table>';					
        }             
        $return['fileID'] = $file;
        return $return; 
    }
	
	public function SignOn_Listsing_4($arrDATA)
    {
		extract($arrDATA);
		
        $file = '';
		
		$crtID = "";
		$crtID = ($ecodeID <> '' ? " AND If(imp_shift_daily.fID_18 <> '', imp_shift_daily.fID_18, imp_shift_daily.fID_13) = '".$ecodeID."' " : "");
		 
		$urlID_211 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=211&fID_2=0&fID_3=0&fID_4=0';
		$urlID_212 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=212&fID_2=0&fID_3=0&fID_4=0';
		 
		$urlID_221 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=221&fID_3=0&fID_4=0';
		$urlID_222 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=222&fID_3=0&fID_4=0';
		
		$urlID_231 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=0&fID_3=231&fID_4=0';
		$urlID_232 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=0&fID_3=232&fID_4=0';
		
		$urlID_241 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=0&fID_3=0&fID_4=241';
		$urlID_242 = $this->home.'profile_6.php?fdateID='.$fdateID.'&tdateID='.$tdateID.'&filterID[]='.$companyID.'&ecodeID='.$ecodeID.'&fID_1=0&fID_2=0&fID_3=0&fID_4=242';
		 
		$sortFILTER = "";
		if($fID_1 == 211)	/* Depot Filter ASC */
		{
			$sortFILTER = " Order By imp_shift_daily.companyID ASC ";
		}
		else if($fID_1 == 212)	/* Depot Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.companyID DESC ";
		}
		else if($fID_2 == 221)	/* SHIFT DATE Filter ASC  */
		{
			$sortFILTER = " Order By imp_shift_daily.dateID ASC ";
		}
		else if($fID_2 == 222)	/* SHIFT DATE Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.dateID DESC ";
		}
		else if($fID_3 == 231)	/* SHIFT NO Filter ASC */
		{
			$sortFILTER = " Order By imp_shift_daily.fID_1 ASC ";
		}
		else if($fID_3 == 232)	/* SHIFT NO Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.fID_1 DESC ";
		}
		else if($fID_4 == 241)	/* Accumulative Late Minutes Filter ASC */
		{
			$sortFILTER = " Order By TIME(TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID)))) DESC ";
		}
		else if($fID_4 == 242)	/* Accumulative Late Minutes Filter DESC */
		{
			$sortFILTER = " Order By TIME(TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID)))) ASC ";
		}
		else
		{
			$sortFILTER = " Order By DATE(imp_shift_daily.dateID), imp_shift_daily.tagCD,imp_shift_daily.fID_1 ASC ";
		}
		
        $filters = "";
		
		$SQL = "SELECT imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, imp_shift_daily.fID_1, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) AS ontimeID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) AS empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) AS diffTm
		FROM imp_shift_daily INNER JOIN shift_masters_dtl ON shift_masters_dtl.recID = imp_shift_daily.shift_recID INNER JOIN (SELECT signon_logs.ID AS dailyID FROM signon_logs WHERE signon_logs.fromID = 'TOUCHPAD' AND DATE(signon_logs.dateID) BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' GROUP BY signon_logs.ID) SignOn_Logs ON SignOn_Logs.dailyID = imp_shift_daily.recID WHERE imp_shift_daily.choppedID <= 0 AND imp_shift_daily.companyID In(".$companyID.") AND imp_shift_daily.dateID BETWEEN '".$this->dateFormat($fdateID)."' AND '".$this->dateFormat($tdateID)."' AND If(shift_masters_dtl.fID_019 = 'N' AND imp_shift_daily.tagCD = 'B', 0, 1) = 1 AND
		TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) LIKE '%-%' ".$crtID." ".$sortFILTER;
		
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<table id="dataTables" class="table table-bordered table-striped">';
			
			$file .= '<thead>';
			$file .= '<tr>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Depot'; 
			$file .= '<a href="'.$urlID_211.'" style="margin-left:10px; cursor:pointer; color:'.($fID_1 == 211 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_212.'" style="margin-left:10px; cursor:pointer; color:'.($fID_1 == 212 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';


			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Date'; 
			$file .= '<a href="'.$urlID_221.'" style="margin-left:10px; cursor:pointer; color:'.($fID_2 == 221 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_222.'" style="margin-left:10px; cursor:pointer; color:'.($fID_2 == 222 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';


			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Shift NO'; 
			$file .= '<a href="'.$urlID_231.'" style="margin-left:10px; cursor:pointer; color:'.($fID_3 == 231 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_232.'" style="margin-left:10px; cursor:pointer; color:'.($fID_3 == 232 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Shift Tag</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver Code</strong></div></th>';
			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Driver Name</strong></div></th>';


			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>On Time</strong></div></th>';
			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Signed at</strong></div></th>';

			$file .= '<th class="knob-labels notices" style="color:white;"><div align="center"><strong>Late by Time'; 
			$file .= '<a href="'.$urlID_241.'" style="margin-left:10px; cursor:pointer; color:'.($fID_4 == 241 ? 'white' : 'red').';" class="fa fa-sort-up">&nbsp;</a>';
			$file .= '<a href="'.$urlID_242.'" style="margin-left:10px; cursor:pointer; color:'.($fID_4 == 242 ? 'white' : 'red').';" class="fa fa-sort-down">&nbsp;</a>';
			$file .= '</strong></div></th>';

			$file .= '</tr>';            
			$file .= '</thead>';
            
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;
                $lateA = '';    $lateB = '';
                foreach($this->rows_1 as $rows_1)
                {
					$arrCM = $rows_1['companyID'] > 0 ? $this->select('company',array("*")," WHERE ID = ".$rows_1['companyID']." ") : '';
					$arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					
					$file .= '<tr>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$arrCM[0]['title'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.date('d-M-Y',strtotime($rows_1['dateID'])).'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['fID_1'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['tagCD'].'</b></td>';
					
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					$file .= '<td style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['ontimeID'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.$rows_1['singinID'].'</b></td>';
					$file .= '<td align="center" style="font-size:13px; background:white;"><b>'.substr((str_replace("-","",$rows_1['diffTm'])), 0, 8).'</b></td>';
					$file .= '</tr>';
                }
            }
            
            $file .= '<tr height="35">';
                $file .= '<td style="background:#367FA9; color:white;" colspan="6" ></td>';
                $file .= '<td style="background:#367FA9; color:white;" colspan="3" ></td>';
            $file .= '</tr>';
            
            $file .= '</table>';					
        }             
        $return['fileID'] = $file;
        return $return; 
    }
}
?>