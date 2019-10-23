<?PHP
class EFunctions extends GFunctions
{
    function __construct()
    {	
        parent::__construct();
        
        $this->companyID = $_SESSION[$this->website]['compID'];
		
        $this->HB = 'style="border:solid 1px #006400;"'; 
        $this->FB = 'style="border:solid 1px #006400; font-weight:200; font-style:inherit;"'; 
    }
	
    public function EXPORT_CUSTOMIZED_FIELDS($filters)
    { 
		if(!empty($filters['rtpyeID']))
        {
			$return = "";
			
			if($filters['frmID'] >= 2)
			{
				$dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');
			}
			
			if(is_array($filters) && count($filters) > 0)
			{
				if($filters['frmID'] == 2)				{$return = $this->Create_Reports_Date($filters,'sicklv.sldateID');}
				else if($filters['frmID'] == 4)			{$return = $this->Create_Reports_Date($filters,'complaint.serDT');}
				else if($filters['frmID'] == 6)			{$return = $this->Create_Reports_Date($filters,'accident_regis.dateID');}
				else if($filters['frmID'] == 7)			{$return = $this->Create_Reports_Date($filters,'infrgs.dateID');}
				else if($filters['frmID'] == 8)			{$return = $this->Create_Reports_Date($filters,'inspc.dateID');}
				else if($filters['frmID'] == 9)			{$return = $this->Create_Reports_Date($filters,'mng_cmn.dateID');}
				else if($filters['frmID'] == 5)			{$return = $this->Create_Reports_Date($filters,'incident_regis.dateID');}
				else if($filters['frmID'] == 10)		{$return = $this->Create_Reports_Date($filters,'hiz_regis.dateID');}
				else if($filters['frmID'] == 11)		{$return = $this->Create_Reports_Date($filters,'sir_regis.issuetoDT');}
				else if($filters['frmID'] == 12)		{$return = $this->Create_Reports_Date($filters,'stfare_regis.dateID');}
			}
			
            $Qry = $this->DB->prepare("SELECT * FROM rbuilder WHERE frmID = ".$filters['frmID']." AND ID In(".$filters['rpt_fieldID'].") Order By srID ASC ");
            $Qry->execute();
            $this->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $countID = count($this->Hrows);

            if($countID > 0)
            {
				echo '<div class="row">';		
				echo '<div class="col-xs-12" style="overflow-y: scroll; overflow-x: scroll; width: 99%; margin-left: 10px;">';
                echo '<table id="dataTables" class="table table-bordered table-striped">';
				echo '<thead><tr>';
				echo '<th style="background:#3C8DBC; color:white; text-align:center;" colspan="'.$countID.'">'.($filters['frmID'] == 1 ? 'Employee' :($filters['frmID'] == 2 ? 'Personal Leave' :($filters['frmID'] == 4 ? 'Customer Feedback' :($filters['frmID'] == 6 ? 'Accident' :($filters['frmID'] == 7 ? 'Infringement' :($filters['frmID'] == 8 ? 'Inspection' :($filters['frmID'] == 9 ? 'Manager Comments' :($filters['frmID'] == 10 ? 'Hazard' :($filters['frmID'] == 11 ? 'SIR' :($filters['frmID'] == 12 ? 'ST Fare' : '')))))))))).' Report '.$dateSTR.'</th>';
				echo '</tr></thead>';
                echo '<thead><tr>';
				
                $fieldNM = '';  $formNM = '';   $joinTB = '';   $joinFN = '';   $algnFL = '';				
                $headID = 1;
                foreach($this->Hrows as $Hrows)
                {
                    $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
                    $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';
                    $algnFL  .= $Hrows['alignFL'].', ';
                    
					echo '<th '.$this->HB.' style="background:#3C8DBC; color:white; text-align:center;">'.$Hrows['filedCP'].'</th>';
							
                    $formNM = $Hrows['formNM'];
                    $headID++;
                }				
                echo '</tr></thead>';

                $flID = explode(",",$fieldNM);      $ftID = explode(",",$fieldTY);
                $jtID = explode(",",$joinTB);       $jfID = explode(",",$joinFN);
                $agID = explode(",",$algnFL);
				
                $Qry_D = $this->DB->prepare($this->ExportReport_QueryBuilder($filters['frmID'],$filters['rpt_fieldID'],$filters['compID'],$return));
                $Qry_D->execute();
                $this->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);

                if(is_array($this->Drows) && count($this->Drows) > 0)
                {
                    foreach($this->Drows as $Drow)
                    {
						if($filters['frmID'] == 10)
						{
							$arrFRM = ($Drow['ID'] > 0 ? $this->select('hiz_regis',array("*"), " WHERE ID = ".$Drow['ID']." ") : '');
						}

                        echo '<tr>';
                        $fnameID = '';  $tableJT = '';  $testID = '';   $alignID = '';
                        
                        for($srID = 1; $srID <= $countID; $srID++)
                        {
                            $alignID .= 'style="font-weight:bold; background:white; text-align:'.(trim($agID[$srID - 1]) == 1 ? 'align="left"' :(trim($agID[$srID - 1]) == 2 ? 'align="right"' :(trim($agID[$srID - 1]) == 3 ? 'align="center"' : ''))).'; "';
							$fnameID = trim($flID[$srID - 1]);
							
							if(trim($ftID[$srID - 1]) == 2) 
							{ 
								if($fnameID == 'typeID' || $fnameID == 'tickID_1' || $fnameID == 'tickID_2' || $fnameID == 'plcntID' || $fnameID == 'substanID' || $fnameID == 'statusID')
								{
									echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' :  '')).'</td>';
								}	
								else if($fnameID == 'faultID')
								{
									$arrCP = ($Drow['ID'] > 0 ? $this->select('complaint',array("*"), " WHERE ID = ".$Drow['ID']." ") : '');
									
									if($arrCP[0]['substanID'] == 2)
									{
										echo '<td '.$this->FB.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 4 ? 'Not Applicable' :($Drow[$fnameID] == 5 ? 'Not At Fault' : '')).'</td>';
									}
									else
									{
										echo '<td '.$this->FB.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'At Fault - Driver' :($Drow[$fnameID] == 2 ? 'At Fault - Engineering' :($Drow[$fnameID] == 3 ? 'At Fault - Operations' :($Drow[$fnameID] == 4 ? 'Not At Fault' :($Drow[$fnameID] == 5 ? 'Not Applicable' :($Drow[$fnameID] == 6 ? 'Not At Fault' : '')))))).'</td>';
									}
								}
								else if($fnameID == 'progressID')
								{
									echo '<td style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Complete' :($Drow[$fnameID] == 2 ? 'Pending' :($Drow[$fnameID] == 3 ? 'Written Off' : ''))).'</td>';
								}
								else if($fnameID == 'optID_2' || $fnameID == 'optID_3')
								{
									echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'No' :($Drow[$fnameID] == 2 ? 'Swan' :($Drow[$fnameID] == 3 ? 'Police' :($Drow[$fnameID] == 4 ? 'Both' :  '')))).'</td>';
								}	
								else if($fnameID == 'optID_u1' || $fnameID == 'optID_m1')
								{
									echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Only remotely possible' :($Drow[$fnameID] == 3 ? 'Unusual but possible' :($Drow[$fnameID] == 6 ? 'Quite possible' :($Drow[$fnameID] == 10 ? 'May well be expected' : '')))).'</td>';
								}
								else if($fnameID == 'optID_u3' || $fnameID == 'optID_m3')
								{
									echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Few per day' :($Drow[$fnameID] == 3 ? 'Weekly' :($Drow[$fnameID] == 6 ? 'Daily' :($Drow[$fnameID] == 10 ? 'Continuous' : '')))).'</td>';
								}
								else if($fnameID == 'optID_u4' || $fnameID == 'optID_m4')
								{
									echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Safety' :($Drow[$fnameID] == 2 ? 'Environmental' : '')).'</td>';
								}
								else if($fnameID == 'optID_u5' || $fnameID == 'optID_m5')
								{
									if($arrFRM[0]['optID_u4'] == 1)
									{
										echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'First Aid Treatment (on site) or Work Injury or Disease Report' :($Drow[$fnameID] == 3 ? 'Medical Treated Injury or Disease' :($Drow[$fnameID] == 6 ? 'Serious Injury/Loss Time Injury or Disease' :($Drow[$fnameID] == 10 ? 'Fatality or Permanent Disability' : '')))).'</td>';
									}
									else if($arrFRM[0]['optID_u4'] == 2)
									{
										echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'No Environmental Harm' :($Drow[$fnameID] == 3 ? 'Minimal Environmental Harm' :($Drow[$fnameID] == 6 ? 'Moderate Environmental Impact' :($Drow[$fnameID] == 10 ? 'Serious Environmental Harm' : '')))).'</td>';
									}
								}
								else if($fnameID == 'optID_u6' || $fnameID == 'optID_m6')
								{
									echo '<td '.$this->FB.' '.$alignID.' style="font-weight:bold; background:white;" align="center">'.($Drow[$fnameID] == 1 ? 'Very High' :($Drow[$fnameID] == 2 ? 'High' :($Drow[$fnameID] == 3 ? 'MEDIUM' :($Drow[$fnameID] == 4 ? 'Low' :($Drow[$fnameID] == 5 ? 'Very Low' : ''))))).'</td>';
								}
								else if($fnameID == 'casualID')
								{
									echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Full Time' :($Drow[$fnameID] == 2 ? 'Part Time' :($Drow[$fnameID] == 3 ? 'Casual' : ''))).'</td>';
								}
								else if($fnameID == 'disciplineID')
								{
									echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' : '')).'</td>';
								}
								else if($fnameID == 'companyID' && ($filters['frmID'] == 1 || $filters['frmID'] == 10 || $filters['frmID'] == 11))
								{
									$tableJT = trim($jtID[$srID - 1]);
									$tableJF = trim($jfID[$srID - 1]); 
									
									if($tableJT <> '')
									{
										$arrTBL  = $Drow['ID'] > 0 ? $this->select(($filters['frmID'] == 1 ? 'employee' :($filters['frmID'] == 10 ? 'hiz_regis' :($filters['frmID'] == 11 ? 'sir_regis' : ''))),array("scompanyID"), " WHERE ID = ".$Drow['ID']." ") : '';
										$arrSBD  = $arrTBL[0]['scompanyID'] > 0 ? $this->select('company_dtls',array("title"), " WHERE ID = ".$arrTBL[0]['scompanyID']." ") : '';
										
										$MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
										echo '<td '.$this->FB.' '.$alignID.'>'.($MS_Array[0][$tableJF]).' '.($arrSBD[0]['title'] <> '' ? '('.$arrSBD[0]['title'].')' : '').'</td>';
									}
									else
									{
										echo '<td '.$this->FB.' '.$alignID.'>'.$fnameID.'</td>';
									} 
								}
								else
								{
									$tableJT = trim($jtID[$srID - 1]);
									$tableJF = trim($jfID[$srID - 1]);
									
									if($tableJT <> '')
									{
										$MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
									}
									
									echo '<td '.$this->FB.' '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
								}
							}
							
							else if(trim($ftID[$srID - 1]) == 3) 
							{
								echo '<td '.$this->FB.' align="center" style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' : '').'</td>';
							}
							
							else if(trim($ftID[$srID - 1]) == 4) 
							{
								$fnameID = trim($flID[$srID - 1]);
								echo '<td '.$this->FB.' '.$alignID.'>'.$this->VISUAL_dateID(($Drow[trim($flID[$srID - 1])])).'</td>';
							}

							else if(trim($ftID[$srID - 1]) == 5) 
							{
								echo '<td '.$this->FB.' '.$alignID.'>'.(strlen($Drow[trim($flID[$srID - 1])]) > 0 ? $Drow[trim($flID[$srID - 1])] : '').'</td>';
							}
							
							else if(trim($ftID[$srID - 1]) == 6) 
							{
								echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' : '')).'</td>';
							}
							
							else if(trim($ftID[$srID - 1]) == 7) 
							{
								echo '<td '.$this->FB.' style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' : 'No').'</td>';
							}
							else if(trim($ftID[$srID - 1]) == 8) 
							{
								echo '<td '.$this->FB.' style="font-weight:bold; background:white;">'.($Drow[$fnameID] == 1 ? 'Yes' :($Drow[$fnameID] == 2 ? 'No' :($Drow[$fnameID] == 3 ? 'NA' : ''))).'</td>';
							}
							
							else                                
							{
								$fnameID = trim($flID[$srID - 1]);

								if(trim($ftID[$srID - 1]) == 1)
								{
									if($fnameID == 'refno')
									{
										echo '<td '.$this->FB.' '.$alignID.'>'.var_export($Drow[$fnameID],true).'</td>';
									}
									else if($fnameID == 'code')
									{
										echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID]).'</td>';
									}
									else if($fnameID == 'casualid')
									{
										echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Full Time' :($Drow[$fnameID] == 2 ? 'Part Time' :($Drow[$fnameID] == 3 ? 'Casual' : ''))).'</td>';
									}
									else
									{
										echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID]).'</td>';                                                    
									}
								} 
								else	{echo '<td '.$this->FB.' '.$alignID.'>'.($Drow[$fnameID]).'</td>';}
							}
                        }
						
                        echo '</tr>';
                    } 
                }
                echo '</table>'; 
				echo '</div>';
				echo '</div>';
            }
        }
    }
	
    public function EXPORT_SIGNON_LATE_1_SHEET($filters)
    { 
        $SQL = "SELECT * FROM company WHERE ID > 0 AND ID In(".$filters['companyID'].") Order By title ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="3" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Depot Signon Summary : '.$filters['fdateID'].' - '.$filters['tdateID'].'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Late Sign on Incidents</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Late Sign on Precentage</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totD = 0; $lateD = 0; $perD = 0;
                foreach($this->rows_1 as $rows_1)
                {
					$totD = $this->count_rows('imp_shift_daily'," LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE If(shift_masters_dtl.fID_019 = 'N' AND imp_shift_daily.tagCD = 'B', 0, 1) = 1 AND imp_shift_daily.singinID <> '' AND imp_shift_daily.companyID = ".$rows_1['ID']." AND If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) > 0 AND imp_shift_daily.choppedID <= 0 AND imp_shift_daily.singinFR = 'TOUCHPAD' AND (DATE(imp_shift_daily.dateID) BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."') ");
					
					$QryL = $this->DB->prepare("SELECT Sum(FO.countID) As countID From (Select Profile_2.empID, Count(1) As countID, Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm))) As timeDF From (Select imp_shift_daily.recID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID,
					TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID
					Where imp_shift_daily.companyID = ".$rows_1['ID']." And imp_shift_daily.choppedID <= 0 And imp_shift_daily.dateID BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."' And If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B',
					shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%' And imp_shift_daily.singinFR = 'TOUCHPAD') As Profile_2 Group By Profile_2.empID Order By countID Desc) As FO ");
					$QryL->execute();
					$this->rowsL = $QryL->fetch(PDO::FETCH_ASSOC);					
					$lateD = ($this->rowsL['countID'] > 0 ? $this->rowsL['countID'] : 0);
                    $perD = round($lateD / $totD * 100,2);
					
                    echo '<tr>';
                        echo '<td '.$this->FB.'><b>'.strtoupper($rows_1['title'].' - '.$rows_1['pscode']).'</b></td>';
                        echo '<td '.$this->FB.' align="center"><b>'.($lateD > 0 ? $lateD : '').'</b></td>';
                        echo '<td '.$this->FB.' align="center"><b>'.($perD > 0 ? $perD.' %' : '').'</b></td>';                     
                    echo '</tr>';
                    
                    $fID_1 += $totD;
                    $fID_2 += $lateD;
                }
            }
            
            echo '<tr>';
            echo '<td style="background:#367FA9; color:white;" align="right"><b>Grand Total : </b></td>';
            echo '<td style="background:#367FA9; color:white;" align="center"><b>'.$fID_2.'</b></td>';
            echo '<td style="background:#367FA9; color:white;" align="center"><b>'.round($fID_2 / $fID_1 * 100,2).' %</b></td>';
            echo '</tr>';
            
            echo '</table>';					
        } 
    }
	
	public function EXPORT_SIGNON_LATE_2_SHEET($filters)
	{
		$sortFILTER = "";
		if($filters['fID_1'] == 211)	/* Depot Filter ASC */
		{
			$sortFILTER = " Order By Profile_2.companyID ASC ";
		}
		else if($filters['fID_1'] == 212)	/* Depot Filter DESC */
		{
			$sortFILTER = " Order By Profile_2.companyID DESC ";
		}
		else if($filters['fID_2'] == 221)	/* Number of Late Sign ons Filter ASC */
		{
			$sortFILTER = " Order By countID ASC ";
		}
		else if($filters['fID_2'] == 222)	/* Number of Late Sign ons Filter DESC */
		{
			$sortFILTER = " Order By countID DESC ";
		}
		else if($filters['fID_3'] == 231)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) DESC ";
		}
		else if($filters['fID_3'] == 232)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) ASC ";
		}
		else
		{ 
			$sortFILTER = " ORDER BY countID DESC";
		}
		
		$SQL = "Select Profile_2.empID, Count(1) As countID, Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm))) As timeDF, Profile_2.companyID From (Select  imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) As ontimeID,
		If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID Where
		imp_shift_daily.choppedID <= 0 And imp_shift_daily.companyID In(".$filters['companyID'].") And imp_shift_daily.dateID BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."' And If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%' And
		imp_shift_daily.singinFR = 'TOUCHPAD') As Profile_2 Group By Profile_2.empID ".$sortFILTER;
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="5" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Depot Detail : '.$filters['fdateID'].' - '.$filters['tdateID'].'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Number of Late Sign ons</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accumulative Late Minutes</strong></div></th>';
            echo '</tr></thead>';
			
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;  $late_B = '';
                foreach($this->rows_1 as $rows_1)
                {
                    $arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					$arrCM = $rows_1['companyID'] > 0 ? $this->select('company',array("*")," WHERE ID = ".$rows_1['companyID']." ") : '';
					
					echo '<tr>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.($arrCM[0]['title']).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					echo '<td '.$this->FB.' style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.($rows_1['countID'] > 0 ? $rows_1['countID'] : '').'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.str_replace("-","",$rows_1['timeDF']).'</b></td>';
					echo '</tr>';
                }
            }
            
            echo '<tr height="25"><td style="background:#367FA9; color:white;" colspan="5"></td></tr>';            
            echo '</table>';	
		}
	}
	
	public function EXPORT_SIGNON_LATE_02_SHEET($filters)
	{
		//echo '<pre>'; echo print_r($filters);
		
		$sortFILTER = "";
		if($filters['fID_1'] == 211)	/* Depot Filter ASC */
		{
			$sortFILTER = " Order By Profile_2.companyID ASC ";
		}
		else if($filters['fID_1'] == 212)	/* Depot Filter DESC */
		{
			$sortFILTER = " Order By Profile_2.companyID DESC ";
		}
		else if($filters['fID_2'] == 221)	/* Number of Late Sign ons Filter ASC */
		{
			$sortFILTER = " Order By countID ASC ";
		}
		else if($filters['fID_2'] == 222)	/* Number of Late Sign ons Filter DESC */
		{
			$sortFILTER = " Order By countID DESC ";
		}
		else if($filters['fID_3'] == 231)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) DESC ";
		}
		else if($filters['fID_3'] == 232)	/* Accumulative Late Minutes */
		{
			$sortFILTER = " Order By TIME(Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm)))) ASC ";
		}
		else
		{ 
			$sortFILTER = " ORDER BY countID DESC";
		}
		
		$SQL = "Select Profile_2.empID, Count(1) As countID, Sec_To_Time(Sum(Time_To_Sec(Profile_2.diffTm))) As timeDF, Profile_2.companyID From (Select  imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) As ontimeID,
		If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID, TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID Where
		imp_shift_daily.choppedID <= 0 And imp_shift_daily.companyID In(".$filters['companyID'].") And imp_shift_daily.dateID BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."' And If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%' And
		imp_shift_daily.singinFR = 'TOUCHPAD') As Profile_2 Group By Profile_2.empID ".$sortFILTER;
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="5" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Depot Detail : '.$filters['fdateID'].' - '.$filters['tdateID'].'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Number of Late Sign ons</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accumulative Late Minutes</strong></div></th>';
            echo '</tr></thead>';
			
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;  $late_B = '';
                foreach($this->rows_1 as $rows_1)
                {
                    $arrCM = $rows_1['companyID'] > 0 ? $this->select('company',array("*")," WHERE ID = ".$rows_1['companyID']." ") : '';
                    $arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					
					echo '<tr>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.($arrCM[0]['title']).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					echo '<td '.$this->FB.' style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.($rows_1['countID'] > 0 ? $rows_1['countID'] : '').'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.str_replace("-","",$rows_1['timeDF']).'</b></td>';
					echo '</tr>';
                }
            }
            
            echo '<tr height="25"><td style="background:#367FA9; color:white;" colspan="5"></td></tr>';            
            echo '</table>';	
		}
	}
	
	public function EXPORT_SIGNON_LATE_3_SHEET($filters)
	{ 
		$arrEM = $filters['empCD'] <> '' ? $this->select('employee',array("*")," WHERE code = '".$filters['empCD']."' AND status = 1 ") : '';
		$empID = $arrEM[0]['ID'] > 0 ? $arrEM[0]['ID'] : $filters['empID'];
		
		$sortFILTER = "";
		$sortFILTER = " Order By DATE(imp_shift_daily.dateID), imp_shift_daily.tagCD,imp_shift_daily.fID_1 ASC ";
			
		
		$SQL = "Select imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, imp_shift_daily.fID_1, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) As ontimeID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID,
		TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID Where
		imp_shift_daily.choppedID <= 0 And imp_shift_daily.companyID In(".$filters['companyID'].") And imp_shift_daily.dateID BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."' And If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%' And imp_shift_daily.singinFR = 'TOUCHPAD' AND 
		If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) = ".$empID." ".$sortFILTER;
		
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Driver Detail : '.$filters['fdateID'].' - '.$filters['tdateID'].'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Shift No</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Shift Tag</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Code</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>On Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>SignOn Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Diff Time</strong></div></th>';
            echo '</tr>'; 
            echo '</thead>'; 
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;
                $lateA = '';    $lateB = '';
                foreach($this->rows_1 as $rows_1)
                {
					$arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					
					echo '<tr>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.date('d-M-Y',strtotime($rows_1['dateID'])).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['fID_1'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['tagCD'].'</b></td>';
					
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					echo '<td '.$this->FB.' style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['ontimeID'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['singinID'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.substr((str_replace("-","",$rows_1['diffTm'])), 0, 8).'</b></td>';
					echo '</tr>';
                }
            }
			
            echo '<tr height="35"><td '.$this->FB.' style="background:#367FA9; color:white;" colspan="8" ></td></tr>';
			echo '</table>';
		}
	}
	
	public function EXPORT_SIGNON_LATE_4_SHEET($filters)
	{
		$crtID = "";
		$crtID = ($filters['empID'] <> '' ? " AND If(imp_shift_daily.fID_18 <> '', imp_shift_daily.fID_18, imp_shift_daily.fID_13) = '".$filters['empID']."' " : "");
		
		$sortFILTER = "";
		if($passRT['fID_1'] == 211)	/* Depot Filter ASC */
		{
			$sortFILTER = " Order By imp_shift_daily.companyID ASC ";
		}
		else if($passRT['fID_1'] == 212)	/* Depot Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.companyID DESC ";
		}
		else if($passRT['fID_2'] == 221)	/* SHIFT DATE Filter ASC  */
		{
			$sortFILTER = " Order By imp_shift_daily.dateID ASC ";
		}
		else if($passRT['fID_2'] == 222)	/* SHIFT DATE Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.dateID DESC ";
		}
		else if($passRT['fID_3'] == 231)	/* SHIFT NO Filter ASC */
		{
			$sortFILTER = " Order By imp_shift_daily.fID_1 ASC ";
		}
		else if($passRT['fID_3'] == 232)	/* SHIFT NO Filter DESC */
		{
			$sortFILTER = " Order By imp_shift_daily.fID_1 DESC ";
		}
		else if($passRT['fID_4'] == 241)	/* Accumulative Late Minutes Filter ASC */
		{
			$sortFILTER = " Order By TIME(TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID)))) DESC ";
		}
		else if($passRT['fID_4'] == 242)	/* Accumulative Late Minutes Filter DESC */
		{
			$sortFILTER = " Order By TIME(TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID)))) ASC ";
		}
		else
		{
			$sortFILTER = " Order By DATE(imp_shift_daily.dateID), imp_shift_daily.tagCD,imp_shift_daily.fID_1 ASC ";
		}
		
		$SQL = "Select imp_shift_daily.recID, imp_shift_daily.companyID, imp_shift_daily.dateID, imp_shift_daily.tagCD, imp_shift_daily.singinID, imp_shift_daily.fID_1, If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, shift_masters_dtl.fID_9) As ontimeID, If(imp_shift_daily.fID_018 > 0, imp_shift_daily.fID_018, imp_shift_daily.fID_013) As empID,
		TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) As diffTm From imp_shift_daily Inner Join shift_masters_dtl On shift_masters_dtl.recID = imp_shift_daily.shift_recID
		Where imp_shift_daily.companyID In(".$filters['companyID'].") And imp_shift_daily.choppedID <= 0 And imp_shift_daily.dateID BETWEEN '".$this->dateFormat($filters['fdateID'])."' AND '".$this->dateFormat($filters['tdateID'])."' And imp_shift_daily.singinFR = 'TOUCHPAD' AND If(shift_masters_dtl.fID_019 = 'N' And imp_shift_daily.tagCD = 'B', 0, 1) = 1 And
		TimeDiff((Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9, '')))), (Time(imp_shift_daily.singinID))) Like '%-%' ".$crtID." ".$sortFILTER;
		
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';
			echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Driver Detail Report : '.$filters['fdateID'].' - '.$filters['tdateID'].'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Shift No</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Shift Tag</strong></div></th>';			
			
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Code</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';			
			
            echo '<th '.$this->HB.'><div align="center"><strong>On Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Signed at</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Late by Time</strong></div></th>';
            echo '</tr>';
            
            echo '</thead>';
            
            if(is_array($this->rows_1) && count($this->rows_1) > 0)
            {
                $srsID = 1; $fID_1 = 0;  $fID_2 = 0; $totS = 0; $lateD = 0; $perD = 0;
                $lateA = '';    $lateB = '';
                foreach($this->rows_1 as $rows_1)
                {
					$arrCM = $rows_1['companyID'] > 0 ? $this->select('company',array("*")," WHERE ID = ".$rows_1['companyID']." ") : '';
					$arrEM = $rows_1['empID'] > 0 ? $this->select('employee',array("*")," WHERE ID = ".$rows_1['empID']." ") : '';
					
					echo '<tr>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$arrCM[0]['title'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.date('d-M-Y',strtotime($rows_1['dateID'])).'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['fID_1'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['tagCD'].'</b></td>';
					
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['code']).'</b></td>';
					echo '<td '.$this->FB.' style="font-size:13px; background:white;"><b>'.strtoupper($arrEM[0]['fname'].' - '.$arrEM[0]['lname']).'</b></td>';
					
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['ontimeID'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.$rows_1['singinID'].'</b></td>';
					echo '<td '.$this->FB.' align="center" style="font-size:13px; background:white;"><b>'.substr((str_replace("-","",$rows_1['diffTm'])), 0, 8).'</b></td>';
					echo '</tr>';
                }
            }
            
            echo '<tr height="35">';
                echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="6" ></td>';
                echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="3" ></td>';
            echo '</tr>';
            
            echo '</table>';					
        }             
	}
	
    public function EXPORT_REPORT_MANAGER_COMMENTS($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        
        if($filters['rtpyeID'] == 1 || $filters['rtpyeID'] == 3 || $filters['rtpyeID'] == 5)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'FOS.dateID');}}

        $SQL = "SELECT FOS.ID, FOS.dateID, FOS.rptno, FOS.empID, FOS.description, FOS.mcomments, FOS.frmID, FOS.wrtypeID,  FOS.companyID, employee.fname, employee.lname, employee.code FROM (SELECT
		inspc.ID, inspc.dateID, inspc.rptno, inspc.empID, inspc.description, inspc.disciplineID, inspc.mcomments, 5 AS frmID, inspc.wrtypeID, inspc.companyID FROM inspc WHERE inspc.disciplineID = 1 AND inspc.mcomments <> '' AND
		inspc.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND inspc.empID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND inspc.wrtypeID = ".$filters['fltID_2'] : "")." UNION ALL SELECT accident_regis.ID,
		accident_regis.dateID, accident_regis.refno, accident_regis.staffID, accident_regis.description, accident_regis.disciplineID, accident_regis.mcomments, 3 AS frmID, accident_regis.wrtypeID, accident_regis.companyID FROM accident_regis WHERE accident_regis.disciplineID = 1 AND accident_regis.mcomments <> '' AND accident_regis.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND accident_regis.staffID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND accident_regis.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT complaint.ID, complaint.dateID, complaint.refno, complaint.driverID, complaint.description, complaint.disciplineID, complaint.mcomments, 1 AS frmID, complaint.wrtypeID, complaint.companyID FROM complaint WHERE complaint.disciplineID = 1 AND complaint.mcomments <> '' AND complaint.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND complaint.driverID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND complaint.wrtypeID = ".$filters['fltID_2'] : "")." UNION ALL SELECT
		incident_regis.ID, incident_regis.dateID, incident_regis.refno, incident_regis.driverID, incident_regis.description, incident_regis.disciplineID, incident_regis.mcomments, 2 AS frmID,
		incident_regis.wrtypeID, incident_regis.companyID FROM incident_regis WHERE incident_regis.disciplineID = 1 AND incident_regis.mcomments <> '' AND incident_regis.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND incident_regis.driverID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND incident_regis.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT infrgs.ID, infrgs.dateID, infrgs.refno, infrgs.staffID, If(infrgs.description <> '', infrgs.description, master.title) AS description, infrgs.disciplineID, infrgs.mcomments, 4 AS frmID, infrgs.wrtypeID, infrgs.companyID FROM infrgs LEFT JOIN master ON master.ID = infrgs.inftypeID
		WHERE infrgs.disciplineID = 1 AND infrgs.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND infrgs.staffID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND infrgs.wrtypeID = ".$filters['fltID_2'] : "")."
		UNION ALL SELECT mng_cmn.ID, mng_cmn.dateID, Concat('MN', '-', mng_cmn.ID) as vcodeID, mng_cmn.staffID, mng_cmn.description, mng_cmn.disciplineID, mng_cmn.mcomments, 6 AS frmID, mng_cmn.wrtypeID, mng_cmn.companyID FROM mng_cmn WHERE mng_cmn.mcomments <> '' AND mng_cmn.companyID In (".$filters['compID'].") ".($filters['fltID_1'] > 0 ? " AND mng_cmn.staffID = ".$filters['fltID_1'] : "")." ".($filters['fltID_2'] > 0 ? " AND mng_cmn.wrtypeID = ".$filters['fltID_2'] : "").") AS FOS INNER JOIN employee ON employee.ID = FOS.empID WHERE FOS.rptno <> '' ".$return." ORDER BY FOS.dateID DESC ";
        
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');
        
        if($filters['rtpyeID'] == 1 || $filters['rtpyeID'] == 3)         {echo $this->EXPORT_REPORT_MANAGER_COMMENTS_1($return,$dateSTR,$_SENDER,$SQL);}
		if($filters['rtpyeID'] == 2)         							 {echo $this->EXPORT_REPORT_MANAGER_COMMENTS_2($filters);}
		if($filters['rtpyeID'] == 5)         							 {echo $this->EXPORT_REPORT_MANAGER_COMMENTS_1($return,$dateSTR,$_SENDER,$SQL);}
    } 
    
    public function EXPORT_REPORT_MANAGER_COMMENTS_1($filters,$dateSTR,$_SENDER,$SQL)
    {    
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Manager Comments Report : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref NO</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Comment Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Managers Comments</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Warning Type</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                { 
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';                    
                    $WR_Array  = $rows_2['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ") : '';
                    
                    echo '<tr>';
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2['rptno'].'</td>';
                        echo '<td '.$this->FB.' align="center"><b>'.($rows_2['frmID'] == 1 ? 'Complaint' :($rows_2['frmID'] == 2 ? 'Incident' :($rows_2['frmID'] == 3 ? 'Accident' :($rows_2['frmID'] == 4 ? 'Infringement' :($rows_2['frmID'] == 5 ? 'Inspection' : 'Manager Comments'))))).'</b></td>';                        
                        echo '<td '.$this->FB.' align="center">'.$rows_2['code'].'</td>';
                        echo '<td '.$this->FB.' >'.$rows_2['fname'].' '.$rows_2['lname'].'</td>';                        
                        echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['mcomments'].'</td>';
                        echo '<td '.$this->FB.' class="d-set">'.$WR_Array[0]['title'].'</td>';                        
                    echo '</tr>';

                }
            }
            echo '</table>';			
        }  
    }
	
	public function EXPORT_REPORT_MANAGER_COMMENTS_2($request)
    {    
		if(is_array($request) && count($request) > 0)   {$filters .= $this->Create_Reports_Date($request,'FOS.dateID');}
	
        $SQL = "SELECT FOS.ID, FOS.dateID, FOS.rptno, FOS.empID, FOS.description, FOS.mcomments, FOS.frmID, FOS.wrtypeID, employee.fname, employee.lname, employee.code, FOS.companyID FROM (SELECT inspc.ID, inspc.dateID, inspc.rptno, inspc.empID, inspc.description,inspc.disciplineID, inspc.mcomments,  5 AS frmID, inspc.wrtypeID, inspc.companyID FROM inspc LEFT JOIN employee ON employee.ID = inspc.empID WHERE inspc.disciplineID = 1 AND ".($request['driverID'] > 0 ? "" : " AND inspc.companyID In(".$request['compID'].") ")." inspc.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." 
		UNION ALL SELECT accident_regis.ID,accident_regis.dateID,  accident_regis.refno, accident_regis.staffID,accident_regis.description,  accident_regis.disciplineID, accident_regis.mcomments, 3 AS frmID,  accident_regis.wrtypeID, accident_regis.companyID FROM accident_regis LEFT JOIN employee ON employee.ID = accident_regis.staffID WHERE accident_regis.disciplineID = 1 AND accident_regis.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND accident_regis.companyID In(".$request['compID'].") ")." UNION ALL SELECT complaint.ID, complaint.dateID, complaint.refno, complaint.driverID, complaint.description, complaint.disciplineID, complaint.mcomments,1 AS frmID, complaint.wrtypeID, complaint.companyID FROM complaint LEFT JOIN employee ON employee.ID = complaint.driverID WHERE complaint.disciplineID = 1 AND complaint.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND complaint.companyID In(".$request['compID'].") ")." 
		UNION ALL SELECT incident_regis.ID, incident_regis.dateID, incident_regis.refno,incident_regis.driverID, incident_regis.description,  incident_regis.disciplineID, incident_regis.mcomments, 2 AS frmID, incident_regis.wrtypeID,incident_regis.companyID FROM incident_regis LEFT JOIN employee ON employee.ID = incident_regis.driverID WHERE incident_regis.disciplineID = 1 AND incident_regis.mcomments <> '' ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND incident_regis.companyID In(".$request['compID'].") ")." 
		UNION ALL SELECT infrgs.ID, infrgs.dateID, infrgs.refno,infrgs.staffID, If(infrgs.description <> '', infrgs.description, master.title) AS description, infrgs.disciplineID, infrgs.mcomments, 4 AS frmID, infrgs.wrtypeID, infrgs.companyID FROM infrgs LEFT JOIN master ON master.ID = infrgs.inftypeID LEFT JOIN employee ON employee.ID = infrgs.staffID WHERE infrgs.disciplineID = 1  ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND infrgs.companyID In(".$request['compID'].") ")."
		UNION ALL SELECT mng_cmn.ID, mng_cmn.dateID, Concat('MN', '-', mng_cmn.ID) as vcodeID, mng_cmn.staffID, mng_cmn.description,mng_cmn.disciplineID, mng_cmn.mcomments, 6 AS frmID, mng_cmn.wrtypeID, mng_cmn.companyID FROM mng_cmn LEFT JOIN employee ON employee.ID = mng_cmn.staffID WHERE mng_cmn.mcomments <> ''  ".($request['driverID'] > 0 ? " AND employee.systemID In(".$request['driverID'].") " : "")." ".($request['driverID'] > 0 ? "" : " AND mng_cmn.companyID In(".$request['compID'].") ")." ) AS FOS INNER JOIN employee ON employee.ID = FOS.empID WHERE FOS.rptno <> '' ".$filters." ORDER BY FOS.dateID DESC ";		
        $Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
        { 
			$prID = (($request['fromID'] <> '') ? '-  (<b>From : '.$request['fromID'].' - To : '.$request['toID'].')</b>' : '');
	
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Driver Name/ID - Manager Comments Report  '.$prID.'</strong></div></th>';
            echo '</tr></thead>';
			
            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Sr. No.</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Comment Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Warning Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Manager Comments</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                { 
                    $WR_Array  = $rows_2['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ") : '';
					$CM_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    
					echo '<tr>';
						echo '<td '.$this->FB.' align="center">'.$srID++.'</td>';
						echo '<td '.$this->FB.' align="center">'.$CM_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';                    
						echo '<td '.$this->FB.' align="center">'.$rows_2['rptno'].'</td>';
						echo '<td '.$this->FB.' align="center"><b>'.($rows_2['frmID'] == 1 ? 'Complaint' :($rows_2['frmID'] == 2 ? 'Incident' :($rows_2['frmID'] == 3 ? 'Accident' :($rows_2['frmID'] == 4 ? 'Infringement' :($rows_2['frmID'] == 5 ? 'Inspection' : 'Manager Comments'))))).'</b></td>';
						
						echo '<td '.$this->FB.'>'.$rows_2['fname'].' '.$rows_2['lname'].' ('.$rows_2['code'].')</td>';
						echo '<td '.$this->FB.'>'.$WR_Array[0]['title'].'</td>';
						// echo '<td '.$this->FB.'>'.$rows_2['wrtypeID'].'</td>';
						echo '<td '.$this->FB.' width="400">'.($rows_2['description']).'</td>';
						echo '<td '.$this->FB.' width="400">'.($rows_2['mcomments']).'</td>';
                    echo '</tr>'; 

                }
            }
            echo '</table>';			
        }  
    }
		
    public function EXPORT_REPORT_COMMENTLINE($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        $return .= $filters['fltID_1'] > 0    ? " AND complaint.accID = ".$filters['fltID_1'] : "";		
        $return .= $filters['fltID_2'] > 0    ? " AND complaint.substanID = ".$filters['fltID_2'] : "";
        $return .= $filters['compID'] <> ''   ? " AND complaint.companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 9)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'serDT');}}
        
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->EXPORT_REPORT_COMMENTLINE_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->EXPORT_REPORT_COMMENTLINE_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->EXPORT_REPORT_COMMENTLINE_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)         {echo $this->EXPORT_REPORT_COMMENTLINE_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 5)         {echo $this->EXPORT_REPORT_COMMENTLINE_5($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)         {echo $this->EXPORT_REPORT_COMMENTLINE_6($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 7)         {echo $this->EXPORT_REPORT_COMMENTLINE_7($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 8)         {echo $this->EXPORT_REPORT_COMMENTLINE_8($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 9)         {echo $this->EXPORT_REPORT_COMMENTLINE_9($return,$dateSTR,$_SENDER);}
    } 
    
    public function EXPORT_REPORT_COMMENTLINE_1($filters,$dateSTR,$_SENDER)
    { 
        $SQL = "SELECT * FROM complaint WHERE ID > 0 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="13" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Customer Feedback Report : '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Serco Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Incident Time</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Type</strong></div></th>';
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Substantiated</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Fault/Not at Fault</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Route</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Location</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Customer Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                { 
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $RS_Array  = $rows_2['creasonID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                    $TY_Array  = $rows_2['accID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['accID']." ") : '';
                    $EM_Array  = $rows_2['driverID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                    $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
                    
                    echo '<tr>';
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2['dcodeID'].'</td>';
                        echo '<td '.$this->FB.' >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        echo '<td '.$this->FB.' class="d-set">'.var_export($rows_2['refno'],true).'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['timeID'].'</td>';
                        echo '<td '.$this->FB.'>'.$TY_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' class="d-set"align="center">'.($rows_2['substanID'] == 1 ? 'Yes' :($rows_2['substanID'] == 2 ? 'No' : '')).'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['faultID'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['routeID'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['cmp_name'].'</td>';
                        echo '<td '.$this->FB.'>'.$RS_Array[0]['title'].'</td>';
                    echo '</tr>'; 
                }
            }
            echo '</table>';			
        }  
   }

	public function EXPORT_REPORT_COMMENTLINE_2($filters,$dateSTR,$_SENDER)
	{       
		
		$SQL =  "SELECT accID FROM complaint WHERE ID > 0 AND accID <> '' ".$filters." Group By accID Order By accID DESC "; 
		
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>All Customer Feedback Type - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';
			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Customer Feedback Type</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
					foreach($this->rows_1 as $rows_1)
					{      
						$LT_Array  = $rows_1['accID'] > 0	? $this->select('master',array("*"), " WHERE ID = ".$rows_1['accID']." ") : '';
						
						//echo '<tr><td colspan="17" style="padding-left:35px;"><b>Customer Feedback Type : '.$LT_Array[0]['title'].'</b></td></tr>';
						
						if($rows_1['accID'] > 0)
						{
							$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = '".$rows_1['accID']."' ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								$srID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
									$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
									//	$IN_Array  = $rows_2['invID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invID']." ") : '';
									
									echo '<tr>';
									echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
									echo '<td '.$this->FB.'>'.($rows_2['accID'] == 52 ? ($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')) : '').'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
									echo '</tr>';
								}
							}
						 }
					} 
			}
			echo '</table>';			
		} 

		echo $file;
	}
	
 	public function EXPORT_REPORT_COMMENTLINE_3($filters,$dateSTR,$_SENDER)
	{	
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>All Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								echo '<tr>';
									echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								echo '</tr>';

						   }
						}
					}
				}
			}
			echo '</table>';			
		} 

		echo $file;
	}
        
	public function EXPORT_REPORT_COMMENTLINE_4($filters,$dateSTR,$_SENDER)
	{
		
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND statusID = 1 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{   
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Completed Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND statusID = 1 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								echo '<tr>';
									echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								echo '</tr>';

						   }
						}
					}
				}
			}
			echo '</table>';			
		} 

		echo $file;
	}
	
	public function EXPORT_REPORT_COMMENTLINE_5($filters,$dateSTR,$_SENDER)
	{
		
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND statusID <> 1 AND accID <> '' AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="13" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Pending Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Time</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Route No</strong></div></th>';			
			echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND statusID <> 1 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								echo '<tr>';
									echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									
									echo '<td '.$this->FB.'>'.$rows_2['timeID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['busID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['routeID'].'</td>';
									
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
								echo '</tr>';

						   }
						}
					}
				}
			}
			echo '</table>';			
		} 

		echo $file;
	} 
	
	public function EXPORT_REPORT_COMMENTLINE_6($filters,$dateSTR,$_SENDER)
	{
		
		
		$SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND accID <> '' AND accID = 224 ".$filters." Group By serDT Order By serDT DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Commendations - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Date</strong></div></th>';                
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';                
			echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Route No</strong></div></th>';                
			echo '<th '.$this->HB.'><div align="center"><strong>Location</strong></div></th>';                
			echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
			
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$srID = 1;
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['serDT'] <> '')
					{
						$SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND accID = 224 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
								$CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
								
								echo '<tr>';
									echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';                                         
									echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
									echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';                                        
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['routeID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
								echo '</tr>';

						   }
						}
					}
				}
			}
			echo '</table>';			
		} 

		echo $file;
	}
        
	public function EXPORT_REPORT_COMMENTLINE_7($filters,$dateSTR,$_SENDER)
	{
            
			
            $SQL =  "SELECT companyID, creasonID FROM complaint WHERE ID > 0 AND creasonID <> '' AND accID In(52,48,221,49,50,51,220,54) ".$filters." Group By companyID, creasonID Order By creasonID ASC ";            
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Customer Feedback Summary - Customer Feedback Report'.$dateSTR.'</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Customer Feedback Reasons / All Customer Feedback Types </strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>At Fault - Driver</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>At Fault - Engineering</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>At Fault - Operations</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>Not Applicable</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Not At Fault</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>G.Total</strong></div></th>';                   
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;  $returnID = '';
                    $fID_1; $fID_2; $fID_3; $fID_4; $fID_5; $fID_6;
                    foreach($this->rows_1 as $rows_1)
                    {
						$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                        $CM_Array  = $rows_1['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['creasonID']." ") : '';
                        
                        $returnID = $this->GETCountComplaints($filters,$rows_1['companyID'],$rows_1['creasonID']);
                        
                        //echo '<pre>'; echo print_r($returnID);
                        
                        echo '<tr>';
                            echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>'; 
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID1'] > 0 ? $returnID[0]['faultID1'] : '').'</td>';
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID2'] > 0 ? $returnID[0]['faultID2'] : '').'</td>';
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID3'] > 0 ? $returnID[0]['faultID3'] : '').'</td>';
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID4'] > 0 ? $returnID[0]['faultID4'] : '').'</td>';
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID5'] > 0 ? $returnID[0]['faultID5'] : '').'</td>';
                            echo '<td '.$this->FB.' align="center">'.($returnID[0]['faultID1'] + $returnID[0]['faultID2'] + $returnID[0]['faultID3'] + $returnID[0]['faultID4'] + $returnID[0]['faultID5']).'</td>';
                            
                        echo '</tr>';
                        
                        $fID_1 += $returnID[0]['faultID1'];
                        $fID_2 += $returnID[0]['faultID2'];
                        $fID_3 += $returnID[0]['faultID3'];
                        $fID_4 += $returnID[0]['faultID4'];
                        $fID_5 += $returnID[0]['faultID5'];
                        $fID_6 += ($returnID[0]['faultID1'] + $returnID[0]['faultID2'] + $returnID[0]['faultID3'] + $returnID[0]['faultID4'] + $returnID[0]['faultID5']);
                    }
                    
                        echo '<tr>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" colspan="2" align="center">GTotal : </td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_1.'</td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_2.'</td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_3.'</td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_4.'</td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_5.'</td>';
                            echo '<td class="knob-labels notices" style="font-weight:600; font-size:14px;" align="center">'.$fID_6.'</td>';
                            
                        echo '</tr>';
                }
                echo '</table>';			
            } 

            echo $file;
	}
        
	public function EXPORT_REPORT_COMMENTLINE_8($filters,$dateSTR,$_SENDER)
	{
            
			
            $SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND substanID=1 AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Substantiated Complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
                echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
                echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['serDT'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND substanID = 1 AND accID = 52 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                foreach($this->rows_2 as $rows_2)
                                {
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                                    $CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                                    
                                    echo '<tr>';
                                        echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
                                        echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
                                        echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
                                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
                                        echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                                        echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
                                        echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
                                        echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
                                        echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
                                        echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
                                    echo '</tr>';

                               }
                            }
                        }
                    }
                }
                echo '</table>';			
            } 

            echo $file;
	}
		
	public function EXPORT_REPORT_COMMENTLINE_9($filters,$dateSTR,$_SENDER)
	{
            $SQL =  "SELECT serDT FROM complaint WHERE ID > 0 AND substanID = 2 AND accID = 52 ".$filters." Group By serDT Order By serDT DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Unsubstantiated complaints - Customer Feedback Report '.$dateSTR.'</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ID</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Serco Ref No</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
                echo '<th '.$this->HB.' width="250"><div align="center"><strong>Description</strong></div></th>';
                echo '<th '.$this->HB.' width="250"><div align="center"><strong>Outcome</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Accountability</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Fault</strong></div></th>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['serDT'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM complaint WHERE ID > 0 AND substanID = 2 AND accID = 52 AND serDT = '".$rows_1['serDT']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                foreach($this->rows_2 as $rows_2)
                                {
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $DR_Array  = $rows_2['driverID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
                                    $CM_Array  = $rows_2['creasonID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['creasonID']." ") : '';
                                    
                                    echo '<tr>';
                                        echo '<td '.$this->FB.' align="center"><a target="blank" href="'.$this->home.'cmplnt.php?a='.$this->Encrypt('create').'&i='.$this->Encrypt($rows_2['ID']).'">'.$srID++.'</a></td>';
                                        echo '<td '.$this->FB.'>'.($rows_2['driverID'] > 0 ? $DR_Array[0]['fname'].' '.$DR_Array[0]['lname'] : 'Driver Not Applicable').'</td>';
                                        echo '<td '.$this->FB.'>'.$DR_Array[0]['code'].'</td>';
                                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['serDT']).'</td>'; 
                                        echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                                        echo '<td '.$this->FB.'>'.$CM_Array[0]['title'].'</td>';
                                        echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
                                        echo '<td '.$this->FB.'>'.$rows_2['outcome'].'</td>';                                        
                                        echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? 'Substantiated' :($rows_2['substanID'] == 2 ? 'UnSubstantiated' : '')).'</td>';
                                        echo '<td '.$this->FB.'>'.($rows_2['substanID'] == 1 ? ($rows_2['faultID'] == 1 ? 'At Fault - Driver' :($rows_2['faultID'] == 2 ? 'At Fault - Engineering' :($rows_2['faultID'] == 4 ? 'Not At Fault' :($rows_2['faultID'] == 3 ? 'At Fault - Operations' : '')))) :($rows_2['substanID'] == 2 ? ($rows_2['faultID'] == 4 ? 'Not Applicable' :($rows_2['faultID'] == 5 ? 'Not At Fault' : '')) : '')).'</td>';
                                    echo '</tr>';
                               }
                            }
                        }
                    }
                }
                echo '</table>';
            }			
		echo $file;
	}
	
	public function EXPORT_REPORT_INCIDENTS($filters)
	{
		$_SENDER = $filters;

		$return  = "";
		$return .= $filters['compID'] <> ''   ? " AND companyID In (".$filters['compID'].") " : "";

		if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 10)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}

		$dateSTR = "";
		$dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

		$pageID = 'EXPORT_REPORT_INCIDENTS_'.$filters['rtpyeID'];
		echo $this->$pageID($return,$dateSTR,$_SENDER);
	} 

	public function EXPORT_REPORT_INCIDENTS_1($filters,$dateSTR,$_SENDER)
	{		
		$SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>All Incident Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th width="350"><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						if($rows_1['dateID'] <> '')
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								
								foreach($this->rows_2 as $rows_2)
								{
									$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
									$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
									
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';

									echo '<tr>';
									echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
									echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '</tr>';

								}
							}
						} 
				} 
			}
			echo '</table>';			
		}  
	} 
        
	public function EXPORT_REPORT_INCIDENTS_2($filters,$dateSTR,$_SENDER)
	{
		$SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND sincID = 2 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Non Security Incidents Report - Date  '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
				foreach($this->rows_1 as $rows_1)
				{
					if($rows_1['dateID'] <> '')
					{
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0 AND sincID = 2  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							
							foreach($this->rows_2 as $rows_2)
							{
								$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
								$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

								echo '<tr>';
								echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
								echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
								echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
								echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
								echo '</tr>';

							}
						}
					} 
				} 
			}
			echo '</table>';			
		} 

		
	} 

	public function EXPORT_REPORT_INCIDENTS_3($filters,$dateSTR,$_SENDER)
	{
            $SQL =   "SELECT dateID FROM incident_regis WHERE ID > 0 AND sincID = 1 AND dateID <> '' ".$filters." Group By dateID Order By dateID DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Security Incidents Report - Date  '.$dateSTR.'</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    foreach($this->rows_1 as $rows_1)
                    {
                        if($rows_1['dateID'] <> '')
                        {
                            $SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0 AND sincID = 1  AND dateID = '".$rows_1['dateID']."' ".$filters." Order By ID ASC ";
                            $Qry_2 = $this->DB->prepare($SQL_2);
                            $Qry_2->execute();
                            $this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
                            if(is_array($this->rows_2) && count($this->rows_2) > 0)
                            {
                                
                                foreach($this->rows_2 as $rows_2)
                                {
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                                    $IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
                                    $EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

                                    echo '<tr>';
                                    echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
                                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                                    echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                                    echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
                                    echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
                                    echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
                                    echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
                                    echo '</tr>';

                                }
                            }
                        } 
                    } 
                }
                echo '</table>';			
            } 

            
	} 

	public function EXPORT_REPORT_INCIDENTS_4($filters,$dateSTR,$_SENDER)
	{
		$SQL =   "SELECT suburb FROM incident_regis WHERE ID > 0  AND suburb > 0 ".$filters." Group By suburb Order By suburb DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="9" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Incidents Report - By Suburb  '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Security Incidents</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center">Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
				foreach($this->rows_1 as $rows_1)
				{
					$SUB_Array  = $rows_1['suburb'] > 0   ?  $this->select('suburbs',array("*"), " WHERE ID = ".$rows_1['suburb']." ") : '';
					
					if($rows_1['suburb'] > 0 && ($SUB_Array[0]['title'] <> ''))
					{
						echo '<tr>';
							echo '<td colspan="9" class="d-set" style="padding-left:35px;"><b>Suburb : '.$SUB_Array[0]['title'].'</b></td>';
						echo '</tr>';
					
					
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND suburb = ".$rows_1['suburb']." ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							
							foreach($this->rows_2 as $rows_2)
							{
								$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$IN_Array  = $rows_2['inctypeID'] > 0   ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';
								$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

								echo '<tr>';
								echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
								echo '<td '.$this->FB.' align="center">'.($rows_2['sincID'] == 1 ? 'Yes' :($rows_2['sincID'] == 2 ? 'NO' : '')).'</td>';
								echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
								echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
								echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
								echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
								echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
								echo '</tr>';

							}
						}
					} 
				} 
			}
			echo '</table>';			
		}  
	} 

	public function EXPORT_REPORT_INCIDENTS_5($filters,$dateSTR,$_SENDER)
	{ 
		$SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0 AND inctypeID > 0 ".$filters." Group By inctypeID Order By inctypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Incident Report - Incident Type '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center">Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';                
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';
						
							echo '<tr>';
									echo '<td colspan="7" class="d-set" style="padding-left:35px;"><b>Incident Category : '.$IN_Array[0]['title'].'</b></td>';
							echo '</tr>';

						if($rows_1['inctypeID'] > 0)
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = ".$rows_1['inctypeID']." ".$filters." Order By ID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
													

									echo '<tr>';
									echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
									echo '<td '.$this->FB.'>'.(($rows_2['description'])).'</td>';
									echo '</tr>';

								}
							}
						} 
				} 
			}
			echo '</table>';			
		} 

		
	} 

	public function EXPORT_REPORT_INCIDENTS_6($filters,$dateSTR,$_SENDER)
	{ 
		$SQL =   "SELECT driverID FROM incident_regis WHERE ID > 0 AND driverID > 0 ".$filters." Group By driverID Order By driverID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Incident Report - Driver  '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
						$EM_Array  = $rows_1['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_1['driverID']." ") : '';
						
						echo '<tr>';
							echo '<td colspan="7" class="d-set" style="padding-left:35px;"><b>Driver : '.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</b></td>';
						echo '</tr>';

						if($rows_1['driverID'] > 0)
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND driverID = ".$rows_1['driverID']." ".$filters." Order By dateID ASC ";
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : '';

									echo '<tr>';
									echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
									echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '</tr>';

								}
							}
						} 
					} 
			}
			echo '</table>';			
		} 

		
	} 
	
	public function EXPORT_REPORT_INCIDENTS_7($filters,$dateSTR,$_SENDER)
	{
		$SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0 AND inctypeID <> '' ".$filters." Group By inctypeID Order By inctypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Incident Report - Incident Type '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{
							$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';

							echo '<tr>';
									echo '<td colspan="7" class="d-set" style="padding-left:35px;"><b>Incident Type: '.$IN_Array[0]['title'].'</b></td>';
							echo '</tr>';

					if($rows_1['inctypeID'] <> '')
				    {
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = '".$rows_1['inctypeID']."' ".$filters." Order By ID ASC ";
							//echo  $SQL_2;
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
								
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM     = $rows_2['companyID'] > 0	?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
									
									echo '<tr>';
									echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '</tr>';
								}
							}
						} 
					} 
			}
			echo '</table>';			
		} 

		
	} 
        
	public function EXPORT_REPORT_INCIDENTS_8($filters,$dateSTR,$_SENDER)
	{
		$SQL = "SELECT busID FROM incident_regis WHERE ID > 0 AND busID <> '' ".$filters." Group By busID Order By busID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - By Bus No  '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
					foreach($this->rows_1 as $rows_1)
					{ 
							echo '<tr>';
									echo '<td colspan="8" class="d-set" style="padding-left:35px;"><b> Bus No : '.$rows_1['busID'].'</b></td>';
							echo '</tr>';

							if($rows_1['busID'] <> '')
			   {
						$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND busID = '".$rows_1['busID']."' ".$filters." Order By ID ASC ";
									//echo  $SQL_2;
									$Qry_2 = $this->DB->prepare($SQL_2);
									$Qry_2->execute();
									$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
									if(is_array($this->rows_2) && count($this->rows_2) > 0)
									{
											
											foreach($this->rows_2 as $rows_2)
											{
												$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
													$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';
													$IN_Array  = $rows_2['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_2['inctypeID']." ") : ''; 

													  echo '<tr>';
													 echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
													 echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
													 echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
													 echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
													 echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
													 echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
													 echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
													 echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
													  echo '</tr>';

											}
									}
							} 
					} 
			}
			echo '</table>';			
		} 

		
	} 

	public function EXPORT_REPORT_INCIDENTS_9($filters,$dateSTR,$_SENDER)
	{
		$SQL = "SELECT * FROM incident_regis WHERE ID > 0  AND plrefID = 1 ".$filters." Order By ID ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{
			
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;">
			<div align="center"><strong>Incident Report - Date  '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';                
			echo '<th '.$this->HB.'><div align="center"><strong>Security Incident</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident Type</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{ 
				
					foreach($this->rows_1 as $rows_1)
					{ 
						$arrCM  = $rows_1['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
						$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';
						$EM_Array  = $rows_1['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_1['driverID']." ") : '';

						echo '<tr>';
						echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
						echo '<td '.$this->FB.' align="center">'.($rows_1['sincID'] == 1 ? 'YES' : 'NO').'</td>';
						echo '<td '.$this->FB.' align="center">'.$rows_1['refno'].'</td>';
						echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
						echo '<td '.$this->FB.'>'.$rows_1['location'].'</td>';
						echo '<td '.$this->FB.'>'.$rows_1['reportby'].'</td>';
						echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
						echo '</tr>';
					}
			}
			echo '</table>';			
		} 

		
	} 
        
	public function EXPORT_REPORT_INCIDENTS_10($filters,$dateSTR,$_SENDER)
	{ 
            $SQL =   "SELECT inctypeID FROM incident_regis WHERE ID > 0  AND inctypeID In (248,249) ".$filters." Group By inctypeID Order By inctypeID DESC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
				
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="7" class="knob-labels notices" style="font-weight:600; font-size:14px;">
                <div align="center"><strong>Incident Report - Passenger Injury  '.$dateSTR.'</strong></div></th>'; 
                echo '</tr></thead>';

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center">Ref No</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Driver</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Incident Location</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Reported By</strong></div></td>';
                echo '<th '.$this->HB.'><div align="center"><strong>Description</strong></div></td>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
					foreach($this->rows_1 as $rows_1)
					{
						$IN_Array  = $rows_1['inctypeID'] > 0    ?  $this->select('master',array("*"), " WHERE ID = ".$rows_1['inctypeID']." ") : '';

						echo '<tr><td colspan="7" class="d-set" style="padding-left:35px;"><b>Incident Type: '.$IN_Array[0]['title'].'</b></td></tr>';

						if($rows_1['inctypeID'] <> '')
						{
							$SQL_2 = "SELECT * FROM incident_regis WHERE ID > 0  AND inctypeID = '".$rows_1['inctypeID']."' ".$filters." Order By ID ASC ";
							//echo  $SQL_2;
							$Qry_2 = $this->DB->prepare($SQL_2);
							$Qry_2->execute();
							$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
							if(is_array($this->rows_2) && count($this->rows_2) > 0)
							{
									
								foreach($this->rows_2 as $rows_2)
								{
									$arrCM  = $rows_2['companyID'] > 0    ?  $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$EM_Array  = $rows_2['driverID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['driverID']." ") : '';

									echo '<tr>';
									echo '<td '.$this->FB.' align="center">'.$arrCM[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['location'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['reportby'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description'].'</td>';
									echo '</tr>';
								}
							}
						} 
					} 
                }
                echo '</table>';			
            } 

            
	} 
	
    public function EXPORT_REPORT_INFRINGEMENT($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        $return .= $filters['fltID_1'] > 0    ? " AND inftypeID = ".$filters['fltID_1'] : "";		
        $return .= $filters['fltID_2'] > 0    ? " AND wrtypeID = ".$filters['fltID_2'] : "";
        $return .= $filters['compID'] <> ''   ? " AND companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] == 1)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
		if($filters['rtpyeID'] == 2)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
		if($filters['rtpyeID'] == 3)    {if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->EXPORT_REPORT_INFRINGEMENT_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->EXPORT_REPORT_INFRINGEMENT_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->EXPORT_REPORT_INFRINGEMENT_3($return,$dateSTR,$_SENDER);}
    } 

    public function EXPORT_REPORT_INFRINGEMENT_1($filters,$dateSTR,$_SENDER)
    {       

       $SQL = "SELECT * FROM infrgs WHERE ID > 0 ".$filters." Order By ID ASC ";
       $Qry = $this->DB->prepare($SQL);
       if($Qry->execute())
       {  
           $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
           echo '<table id="dataTables" class="table table-bordered table-striped">';				
           echo '<thead><tr>';
           echo '<th '.$this->HB.' colspan="14" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Infringement Report : '.$dateSTR.'</strong></div></th>'; 
           echo '</tr></thead>';

           echo '<thead><tr>';
           echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Infringement No</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Time</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Location</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Demerit Points Lost</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Date Received</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Date Sent</strong></div></th>';
           echo '<th '.$this->HB.' width="120"><div align="center"><strong>Infringement Type</strong></div></th>';
           echo '<th '.$this->HB.'><div align="center"><strong>Managers Comments</strong></div></th>';
           echo '<th '.$this->HB.' width="120"><div align="center"><strong>Warning Type</strong></div></th>';

           echo '</tr></thead>';
           if(is_array($this->rows_1) && count($this->rows_1) > 0)			
           {
               $srID = 1;
               foreach($this->rows_1 as $rows_2)
               { 
                   $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                   $WP_Array  = $rows_2['wrtypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['wrtypeID']." ") : '';
                   $IN_Array  = $rows_2['inftypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inftypeID']." ") : '';
                   $EM_Array  = $rows_2['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
                   
                   echo '<tr>';
                       echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                       echo '<td '.$this->FB.' align="center">'.$rows_2['stcodeID'].'</td>';
                       echo '<td '.$this->FB.' >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                       echo '<td '.$this->FB.' class="d-set">'.var_export($rows_2['refno'],true).'</td>';
                       echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                       echo '<td '.$this->FB.'>'.$rows_2['timeID'].'</td>';
                       echo '<td '.$this->FB.'>'.$rows_2['description_1'].'</td>';
                       echo '<td '.$this->FB.'>'.($rows_2['dplostID'] <> '' ? $rows_2['dplostID'] : 0).'</td>';
                       echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
                       echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
                       echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
                       echo '<td '.$this->FB.'>'.$rows_2['mcomments'].'</td>';
                       echo '<td '.$this->FB.'>'.$WP_Array[0]['title'].'</td>';
                   echo '</tr>'; 
               }
           }
           echo '</table>';			
       }  
   }
        
	public function EXPORT_REPORT_INFRINGEMENT_2($filters,$dateSTR,$_SENDER)
	{
		
		
		$SQL = "SELECT inftypeID FROM infrgs WHERE ID > 0 AND inftypeID <> '' ".$filters." Group By inftypeID Order By inftypeID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Infringement Type - Infringement Report '.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Infringement No.</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Vehicle Rego</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Occurred</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Demerit Points</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No.</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Issue Date </strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Compliance Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Received</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Sent</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Infringement Type</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Location Of Infringement</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $rows_1)
				{
					$INF_Array  = $rows_1['inftypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['inftypeID']." ") : '';

					//echo '<tr><td colspan="16" style="padding-left:35px;"><b>Type: '.$INF_Array[0]['title'].'</b></td></tr>';

					if($rows_1['inftypeID'] <> '')
					{
						$SQL_2 = "SELECT * FROM infrgs WHERE ID > 0 AND inftypeID = '".$rows_1['inftypeID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
								
								echo '<tr>';
									echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['vehicle'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['dplostID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['busID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['stcodeID'].'</td>';
									echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_2']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
									echo '<td '.$this->FB.'>'.$INF_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description_1'].'</td>';
								echo '</tr>';
							}
						}
					}
				} 
			}
			echo '</table>';			
		} 

		return $file;
	}

	public function EXPORT_REPORT_INFRINGEMENT_3($filters,$dateSTR,$_SENDER)
	{
		$SQL = "SELECT dplostID FROM infrgs WHERE ID > 0 AND dplostID <> '' ".$filters." Group By dplostID Order By dplostID DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="14" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Demerit Points - Infringement Report</ Wise'.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>';
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Infringement No.</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Vehicle Rego</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Occurred</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No.</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver ID</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Driver Name</strong></div></th>';
				echo '<th '.$this->HB.'><div align="center"><strong>Demerit Points</strong></div></th>';
				
			echo '<th '.$this->HB.'><div align="center"><strong>Issue Date </strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Compliance Date</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Received</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Date Sent</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Infringement Type</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Location Of Infringement</strong></div></th>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				foreach($this->rows_1 as $rows_1)
				{
					/*echo '<tr>';
							echo '<td colspan="15" style="padding-left:35px;"><b>Demerit Points : '.($rows_1['dplostID']).'</b></td>';
					echo '</tr>';*/

					if($rows_1['dplostID'] <> '')
					{
						$SQL_2 = "SELECT * FROM infrgs WHERE ID > 0 AND dplostID = '".$rows_1['dplostID']."' ".$filters." Order By ID ASC ";
						$Qry_2 = $this->DB->prepare($SQL_2);
						$Qry_2->execute();
						$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
						if(is_array($this->rows_2) && count($this->rows_2) > 0)
						{
							foreach($this->rows_2 as $rows_2)
							{
								$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
								$ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
								$INF_Array  = $rows_2['inftypeID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inftypeID']." ") : '';
							   
								echo '<tr>';
									echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['vehicle'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['busID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['stcodeID'].'</td>';
									echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['dplostID'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_2']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_3']).'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_4']).'</td>';
									echo '<td '.$this->FB.'>'.$INF_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['description_1'].'</td>';
								echo '</tr>';
							}
						}
					}
				} 
			}
			echo '</table>';			
		} 

		return $file;
	} 

    public function EXPORT_REPORT_INSPECTION($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        $return .= $filters['fltID_1'] > 0    ? " AND inspc.insrypeID = ".$filters['fltID_1'] : "";		
        $return .= $filters['fltID_2'] > 0    ? " AND inspc.fineID = ".$filters['fltID_2'] : "";
        $return .= $filters['fltID_3'] > 0    ? " AND inspc.inspectedby = ".$filters['fltID_3'] : "";
        $return .= $filters['compID'] <> ''   ? " AND inspc.companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] == 1)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}
        if($filters['rtpyeID'] == 2)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}
		if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID_1');}}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->EXPORT_REPORT_INSPECTION_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->EXPORT_REPORT_INSPECTION_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->EXPORT_REPORT_INSPECTION_3($return,$dateSTR,$_SENDER);}
    } 

    public function EXPORT_REPORT_INSPECTION_1($filters,$dateSTR,$_SENDER)
    { 
        $SQL = "SELECT * FROM inspc WHERE ID > 0 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Inspection Report : '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Report No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Report Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date Inspected</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Inspection Result</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Fine</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Inspected By</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Service No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Service Info</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Service Timing Point</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Departure Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Timing Point Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Actual Time</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
                    $STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
                    $CTN_Array  = $rows_2['contractorID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractorID']." ") : '';
                        
                    $NP_Array  = $rows_2['inspectedby'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['inspectedby']." ") : '';
                    $FN_Array  = $rows_2['fineID'] > 0       ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['fineID']." ") : '';
                    $IN_Array  = $rows_2['insrypeID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0        ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';

                    echo '<tr>';
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2['ecodeID'].'</td>';
                        echo '<td '.$this->FB.' >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        echo '<td '.$this->FB.' class="d-set">'.$rows_2['rptno'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
                        echo '<td '.$this->FB.'>'.$IN_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.'>'.$FN_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.'>'.$NP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.'>'.$SRN_Array[0]['codeID'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['serviceinfID'].'</td>';
                        echo '<td '.$this->FB.'>'.$STP_Array[0]['fileID_1'].'</td>';                            
                        echo '<td '.$this->FB.'>'.$rows_2['timeID_1'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['timeID_2'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['timeID_3'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }  
    }
	
	public function EXPORT_REPORT_INSPECTION_2($filters,$dateSTR,$_SENDER)
    {
		$SQL = "SELECT inspectedby FROM inspc WHERE ID > 0 AND inspectedby > 0 ".$filters." Group By inspectedby Order By inspectedby DESC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Inspected By - Inspection Report'.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>'; 
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Inspected By</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Report No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Report Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Inspection Result</strong></div></td>'; 
			echo '<th '.$this->HB.'><div align="center"><strong>Contract</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service Info</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service Time Point</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
					foreach($this->rows_1 as $rows_1)
					{
						$INSP_Array  = $rows_1['inspectedby'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['inspectedby']." ") : '';
						
						if($rows_1['inspectedby'] > 0)
						{
								$SQL_2 = "SELECT * FROM inspc WHERE ID > 0 AND inspectedby = ".$rows_1['inspectedby']." ".$filters." Order By ID ASC ";
								$Qry_2 = $this->DB->prepare($SQL_2);
								$Qry_2->execute();
								$this->rows_2 = $Qry_2->fetchAll(PDO::FETCH_ASSOC);			
								if(is_array($this->rows_2) && count($this->rows_2) > 0)
								{
								$srID = 1;
								foreach($this->rows_2 as $rows_2)
								{
									$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
									$INS_Array  = $rows_2['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
									$CNT_Array  = $rows_2['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractID']." ") : '';
									$SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
									$STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
									$INV_Array  = $rows_2['invstID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invstID']." ") : '';

									echo '<tr>';
									echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$INSP_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['rptno'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
									echo '<td '.$this->FB.'>'.$INS_Array[0]['title'].'</td>'; 
									echo '<td '.$this->FB.'>'.$CNT_Array[0]['title'].'</td>';
									echo '<td '.$this->FB.'>'.$SRN_Array[0]['codeID'].'</td>';
									echo '<td '.$this->FB.'>'.$rows_2['serviceinfID'].'</td>';
									echo '<td '.$this->FB.'>'.$STP_Array[0]['fileID_1'].'</td>';
									echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
									echo '</tr>';
								}
								}
						}
					} 
			}
			echo '</table>';			
		} 
			
    }
	
	public function EXPORT_REPORT_INSPECTION_3($filters,$dateSTR,$_SENDER)
    {
		$SQL = "SELECT master.title, inspc.* FROM inspc Inner Join master On master.ID = inspc.fineID WHERE inspc.ID > 0 AND inspc.fineID > 0 AND inspc.fineID <> 299 ".$filters." Order By inspc.companyID, master.title, inspc.dateID_1 ASC ";
		$Qry = $this->DB->prepare($SQL);
		if($Qry->execute())
		{ 
			$this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
			echo '<table id="dataTables" class="table table-bordered table-striped">';				
			echo '<thead><tr>';
			echo '<th colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Fine - Inspection Report'.$dateSTR.'</strong></div></th>'; 
			echo '</tr></thead>';

			echo '<thead><tr>'; 
			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Report No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Report Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Inspected Date</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Inspection Result</strong></div></td>'; 
			echo '<th '.$this->HB.'><div align="center"><strong>Contract</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service Info</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Service Time Point</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></td>';
			echo '<th '.$this->HB.'><div align="center"><strong>Fine</strong></div></td>';
			echo '</tr></thead>';
			if(is_array($this->rows_1) && count($this->rows_1) > 0)			
			{
				$fine_tot = 0;
				foreach($this->rows_1 as $rows_2)
				{
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					$INS_Array  = $rows_2['insrypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['insrypeID']." ") : '';
					
					$CNT_Array  = $rows_2['contractID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['contractID']." ") : '';
					$SRN_Array  = $rows_2['servicenoID'] > 0 ? $this->select('srvdtls',array("*"), " WHERE ID = ".$rows_2['servicenoID']." ") : '';
					$STP_Array  = $rows_2['srtpointID'] > 0 ? $this->select('cstpoint_dtl',array("*"), " WHERE recID = ".$rows_2['srtpointID']." ") : '';
					$INV_Array  = $rows_2['invstID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['invstID']." ") : '';

					echo '<tr>';							
					echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_2['rptno'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
					echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID_1']).'</td>';
					echo '<td '.$this->FB.'>'.$INS_Array[0]['title'].'</td>'; 
					echo '<td '.$this->FB.'>'.$CNT_Array[0]['title'].'</td>';
					echo '<td '.$this->FB.'>'.$SRN_Array[0]['codeID'].'</td>';
					echo '<td '.$this->FB.'>'.$rows_2['serviceinfID'].'</td>';
					echo '<td '.$this->FB.'>'.$STP_Array[0]['fileID_1'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
					echo '<td '.$this->FB.' align="center">'.$rows_2['title'].'</td>';
					echo '</tr>';
					
					$fine_tot += (float)$rows_2['title']; 
 
				} 
				
					echo '<tr>';							
						echo '<td colspan="10" align="right" '.$this->FB.'><b>Grand Total : </b></td>';
						echo '<td align="center" '.$this->FB.'><b>'.$fine_tot.'</b></td>';
					echo '</tr>';
			}
			echo '</table>';			
		} 
			
    }
        
    public function EXPORT_REPORT_ACCIDENT($filters)
    {
        $_SENDER = $filters;    //  echo '<pre>'; echo print_r($_SENDER);
        
        $return  = "";
        $return .= ($filters['fltID_1'] == 1 || $filters['fltID_1'] == 0) && $filters['fltID_1'] <> '' ? " AND tickID_2 = ".$filters['fltID_1'] : "";		
        $return .= ($filters['fltID_2'] == 1 || $filters['fltID_2'] == 0) && $filters['fltID_2'] <> '' ? " AND tickID_1 = ".$filters['fltID_2'] : "";
        
        $return .= $filters['fltID_3'] > 0 ? " AND acccatID = ".$filters['fltID_3'] : "";
        $return .= $filters['fltID_4'] > 0 ? " AND accID = ".$filters['fltID_4'] : "";
        $return .= $filters['fltID_5'] > 0 ? " AND responsibleID = ".$filters['fltID_5'] : "";
        $return .= $filters['fltID_6'] > 0 ? " AND progressID = ".$filters['fltID_6'] : "";
        $return .= $filters['compID'] <> ''   ? " AND companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] >= 1 && $filters['rtpyeID'] <= 6)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'dateID');}}
		
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)         {echo $this->EXPORT_REPORT_ACCIDENT_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)         {echo $this->EXPORT_REPORT_ACCIDENT_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)         {echo $this->EXPORT_REPORT_ACCIDENT_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)         {echo $this->EXPORT_REPORT_ACCIDENT_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 5)         {echo $this->EXPORT_REPORT_ACCIDENT_5($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)         {echo $this->EXPORT_REPORT_ACCIDENT_6($return,$dateSTR,$_SENDER);}
    } 

    public function EXPORT_REPORT_ACCIDENT_1($filters,$dateSTR,$_SENDER)
    {
        
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="15" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Accidents Report : '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reference</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Time</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus</strong></div></th>';
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Training Accident</strong></div></th>';
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Driver Not Applicable</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Accident Category</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Accident Details</strong></div></th>'; 
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Responsible</strong></div></th>';            
            echo '<th '.$this->HB.'><div align="center"><strong>Bus Repairs</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Other Repairs</strong></div></th>';
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Status</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                { 
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AD_Array  = $rows_2['accID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['accID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $EM_Array  = $rows_2['staffID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';
                    $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';

                    echo '<tr>';
					echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
					
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.' >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' class="d-set">'.var_export($rows_2['refno'],true).'</td>';					
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.'>'.$rows_2['timeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' class="d-set"align="center">'.($rows_2['tickID_2'] == 1 ? 'Yes' :($rows_2['tickID_2'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['tickID_1'] == 1 ? 'Yes' : '').'</td>';
                    echo '<td '.$this->FB.'>'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.'>'.$AD_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' class="d-set"align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.$rows_2['rprcost'].'</td>';
                    echo '<td '.$this->FB.'>'.$rows_2['othcost'].'</td>';
                    echo '<td '.$this->FB.' class="d-set"align="center">'.($rows_2['progressID'] == 1 ? 'Complete'  :($rows_2['progressID'] == 2 ? 'Pending' :($rows_2['progressID'] == 3 ? 'Written Off' : ''))).'</td>';
                    echo '</tr>'; 
                }
            }
            echo '</table>';
        }  
    }
    
	public function EXPORT_REPORT_ACCIDENT_2($filters,$dateSTR,$_SENDER)
    {
        
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>All Greater than $1000 - Accident Report'.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';			
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accident Details</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Cost</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Responsible</strong></div></th>';
            echo '<th '.$this->HB.' width="250"><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
                    echo '</tr>';
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    echo '<tr>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="2"></td>';
                    echo '</tr>';   
                    
            echo '</table>';			
        } 

        return $file;
    }
	
	public function EXPORT_REPORT_ACCIDENT_3($filters,$dateSTR,$_SENDER)
    {
        
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 AND responsibleID = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Responsible Greater than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accident Details</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Cost</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Responsible</strong></div></th>';
            echo '<th '.$this->HB.' width="250"><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
                    echo '</tr>';
                    
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    echo '<tr>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="2"></td>';
                    echo '</tr>';   
                    
            echo '</table>';			
        } 

        return $file;
    }
    
    public function EXPORT_REPORT_ACCIDENT_4($filters,$dateSTR,$_SENDER)
    {
        
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) <= 1000 AND responsibleID = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Responsible Less than $1000 - Accident Report'.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accident Details</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Cost</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Responsible</strong></div></th>';
            echo '<th '.$this->HB.' width="250"><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                foreach($this->rows_1 as $rows_2)
                { 
				$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
                    echo '</tr>';
					
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    echo '<tr>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="2"></td>';
                    echo '</tr>';   
                    
            echo '</table>';			
        } 

        return $file;
    }
    
    public function EXPORT_REPORT_ACCIDENT_5($filters,$dateSTR,$_SENDER)
    {
        
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) >= 1000 AND responsibleID = 2 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Not Responsible Greater than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accident Details</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Cost</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Responsible</strong></div></th>';
            echo '<th '.$this->HB.' width="250"><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                { 
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
                    echo '</tr>';
					
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    echo '<tr>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="2"></td>';
                    echo '</tr>';   
                    
            echo '</table>';			
        } 

        return $file;
    }
    
    public function EXPORT_REPORT_ACCIDENT_6($filters,$dateSTR,$_SENDER)
    {
        
		
        $SQL = "SELECT * FROM accident_regis WHERE ID > 0 AND Coalesce(accident_regis.rprcost, 0) + Coalesce(accident_regis.othcost, 0) <= 1000 AND responsibleID = 2 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;">
            <div align="center"><strong>Not Responsible Less than $1000 - Accident Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Ref No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Bus No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Accident Details</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Cost</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Driver Responsible</strong></div></th>';
            echo '<th '.$this->HB.' width="250"><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            
            $costID = 0;
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                { 
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $AC_Array  = $rows_2['acccatID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['acccatID']." ") : '';
                    $ST_Array  = $rows_2['staffID'] > 0  ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['staffID']." ") : '';

                    echo '<tr>';
                    echo '<td '.$this->FB.'>'.($rows_2['tickID_2'] == 1 ? 'Training' : $CP_Array[0]['title']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['scodeID'].'</td>';
                    echo '<td '.$this->FB.'>'.$ST_Array[0]['fname'].' '.$ST_Array[0]['lname'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['dateID']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.var_export($rows_2['refno'],true).'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_2['busID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$AC_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['rprcost'] + $rows_2['othcost']).'</td>';
                    echo '<td '.$this->FB.' align="center">'.($rows_2['responsibleID'] == 1 ? 'Yes' :($rows_2['responsibleID'] == 2 ? 'No' : '')).'</td>';
                    echo '<td '.$this->FB.'>'.($rows_2['description']).'</td>';
                    echo '</tr>';
					
                    $costID += ($rows_2['rprcost'] + $rows_2['othcost']);
                }
            }
            
                    echo '<tr>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="7" align="right"><b>GTotal : </b></td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" align="right"><b>'.$costID.'</b>&nbsp;&nbsp;&nbsp;</td>';
                    echo '<td '.$this->FB.' style="background:#367FA9; color:white;" colspan="2"></td>';
                    echo '</tr>';   
                    
            echo '</table>';			
        } 

        return $file;
    }
	
    public function EXPORT_REPORT_SICKLEAVE($filters)
    {
        $_SENDER = $filters;

        $return  = "";
        $return .= $filters['fltID_1'] > 0    ? " AND employee.desigID = ".$filters['fltID_1'] : "";		
        $return .= $filters['fltID_3'] > 0    ? " AND sicklv.lvtypeID = ".$filters['fltID_3'] : "";
        $return .= $filters['fltID_2'] > 0    ? " AND sicklv.dayID = ".$filters['fltID_2'] : "";
        $return .= $filters['compID'] <> ''   ? " AND sicklv.companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] == 1)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
        if($filters['rtpyeID'] == 2)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 4)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 6)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 7)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}
		if($filters['rtpyeID'] == 8)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'sicklv.sldateID');}}

        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');

        if($filters['rtpyeID'] == 1)    {echo $this->EXPORT_REPORT_SICKLEAVE_1($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 2)    {echo $this->EXPORT_REPORT_SICKLEAVE_2($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 3)    {echo $this->EXPORT_REPORT_SICKLEAVE_3($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 4)    {echo $this->EXPORT_REPORT_SICKLEAVE_4($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 6)    {echo $this->EXPORT_REPORT_SICKLEAVE_6($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 7)    {echo $this->EXPORT_REPORT_SICKLEAVE_7($return,$dateSTR,$_SENDER);}
		if($filters['rtpyeID'] == 8)    {echo $this->EXPORT_REPORT_SICKLEAVE_8($return,$dateSTR,$_SENDER);}
    }
    
    public function EXPORT_REPORT_SICKLEAVE_1($filters,$dateSTR,$_SENDER)
    { 
        $SQL = "SELECT * FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.ID > 0 ".$filters." Order By sicklv.ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="11" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Personal Leave Report : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Start Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong>Leave Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Duration</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Category</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Doctor Certificate</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $SU_Array  = $rows_2['sid'] > 0  ? $this->select('suburbs',array("*"), " WHERE ID = ".$rows_2['sid']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ? $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
                    $DS_Array  = $EM_Array[0]['desigID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
                    
                    echo '<tr>';
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2['ecodeID'].'</td>';
                        echo '<td '.$this->FB.' >'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
                        echo '<td '.$this->FB.' class="d-set">'.$DS_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                        echo '<td '.$this->FB.'>'.($rows_2['dayID'] == 1 ? 'Monday' :($rows_2['dayID'] == 2 ? 'Tuesday' :($rows_2['dayID'] == 3 ? 'Wednesday' :($rows_2['dayID'] == 4 ? 'Thursday'  :($rows_2['dayID'] == 5 ? 'Friday'  :($rows_2['dayID'] == 6 ? 'Saturday' :($rows_2['dayID'] == 7 ? 'Sunday'  :''))))))).'</td>';
                        echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2['duration'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_2[''].'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                        echo '<td '.$this->FB.'>'.$rows_2['reason'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }
    }
    
	public function EXPORT_REPORT_SICKLEAVE_2($filters,$dateSTR,$_SENDER)
    {
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration <= '1' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>One Day - Personal Leave Report : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Employee Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Day</strong></div></th>';            
            echo '<th '.$this->HB.'><div align="center"><strong>Duration</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Leave Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Doctor Certificate</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
                    $DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
					
                    echo '<tr>';
                            echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.$rows_2['ecodeID'].'</td>';
                            echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
							echo '<td '.$this->FB.'>'.$DG_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                            echo '<td '.$this->FB.'>'.($rows_2['dayID'] == 1 ? 'Monday'  
                                                     :($rows_2['dayID'] == 2 ? 'Tuesday' 
                                                     :($rows_2['dayID'] == 3 ? 'Wednesday' 
                                                     :($rows_2['dayID'] == 4 ? 'Thursday'  
                                                     :($rows_2['dayID'] == 5 ? 'Friday'  
                                                     :($rows_2['dayID'] == 6 ? 'Saturday'
                                                     :($rows_2['dayID'] == 7 ? 'Sunday'  
                                                      :''))))))).'</td>';
                            echo '<td '.$this->FB.' align="center">'.$rows_2['duration'].'</td>';
                            echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                            echo '<td '.$this->FB.'>'.$rows_2['reason'].'</td>';


                    echo '</tr>';

                }
            }
            echo '</table>';			
        }   
    } 
    
	public function EXPORT_REPORT_SICKLEAVE_3($filters,$dateSTR,$_SENDER)
    {
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration > '1' AND duration <= '2' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Two Day - Personal Leave Report : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Day</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Duration</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Leave Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Doctor Certificate</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					$DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
					
                    echo '<tr>';
                            echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.$rows_2['ecodeID'].'</td>';
                            echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
							echo '<td '.$this->FB.'>'.$DG_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
                            echo '<td '.$this->FB.'>'.($rows_2['dayID'] == 1 ? 'Monday'  
                                                     :($rows_2['dayID'] == 2 ? 'Tuesday' 
                                                     :($rows_2['dayID'] == 3 ? 'Wednesday' 
                                                     :($rows_2['dayID'] == 4 ? 'Thursday'  
                                                     :($rows_2['dayID'] == 5 ? 'Friday'  
                                                     :($rows_2['dayID'] == 6 ? 'Saturday'
                                                     :($rows_2['dayID'] == 7 ? 'Sunday'  
													 :''))))))).'</td>';
                            echo '<td '.$this->FB.' align="center">'.$rows_2['duration'].'</td>';
                            echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
                            echo '<td '.$this->FB.' align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
                            echo '<td '.$this->FB.'>'.$rows_2['reason'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }   
    } 
    
	public function EXPORT_REPORT_SICKLEAVE_4($filters,$dateSTR,$_SENDER)
    {
        $SQL = "SELECT * FROM sicklv WHERE ID > 0 AND duration >= '3' ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Three Day - Personal Leave Report : '.$dateSTR.'</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee Code</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Employee Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Commencement Day</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Duration</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Leave Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Doctor Certificate</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
                foreach($this->rows_1 as $rows_2)
                {
                    $CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
                    $LT_Array  = $rows_2['lvtypeID'] > 0 ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['lvtypeID']." ") : '';
                    $EM_Array  = $rows_2['empID'] > 0    ?  $this->select('employee',array("*"), " WHERE ID = ".$rows_2['empID']." ") : '';
					$DG_Array  = $EM_Array[0]['desigID'] > 0    ? $this->select('master',array("*"), " WHERE ID = ".$EM_Array[0]['desigID']." ") : '';
					
                    echo '<tr>';
						echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.' align="center">'.$rows_2['ecodeID'].'</td>';
						echo '<td '.$this->FB.'>'.$EM_Array[0]['fname'].' '.$EM_Array[0]['lname'].'</td>';
						echo '<td '.$this->FB.'>'.$DG_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_2['sldateID']).'</td>';
						echo '<td '.$this->FB.'>'.($rows_2['dayID'] == 1 ? 'Monday' :($rows_2['dayID'] == 2 ? 'Tuesday' 
												 :($rows_2['dayID'] == 3 ? 'Wednesday' 
												 :($rows_2['dayID'] == 4 ? 'Thursday'  
												 :($rows_2['dayID'] == 5 ? 'Friday'  
												 :($rows_2['dayID'] == 6 ? 'Saturday'
												 :($rows_2['dayID'] == 7 ? 'Sunday'  
												  :''))))))).'</td>';
						echo '<td '.$this->FB.' align="center">'.$rows_2['duration'].'</td>';
						echo '<td '.$this->FB.'>'.$LT_Array[0]['title'].'</td>';
						echo '<td '.$this->FB.' align="center">'.($rows_2['doccertID'] == 1 ? 'Yes' : '').'</td>';
						echo '<td '.$this->FB.'>'.$rows_2['reason'].'</td>';
                    echo '</tr>'; 
                }
            }
            echo '</table>';			
        }   
    } 
    
	public function EXPORT_REPORT_SICKLEAVE_6($filters,$dateSTR,$_SENDER)
    {
        
        
        $SQL = "SELECT FO.companyID, FO.monthID, FO.yearID, Sum(FO.DY_C1) AS DY_C1,  Sum(FO.DY_D1) AS DY_D1, Sum(FO.DY_C2) AS DY_C2,  Sum(FO.DY_D2) AS DY_D2, Sum(FO.DY_C3) AS DY_C3, Sum(FO.DY_D3) AS DY_D3 FROM
		(SELECT sicklv.ID, Month(sicklv.sldateID) AS monthID, Year(sicklv.sldateID) AS yearID, sicklv.sldateID, sicklv.empID, employee.desigID, sicklv.companyID,  If(Coalesce(sicklv.duration, 0) <= 1, 1, 0) AS DY_C1, If(Coalesce(sicklv.duration, 0) <= 1, Coalesce(sicklv.duration, 0), 0) AS DY_D1,
		If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), 1, 0) AS DY_C2, If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), Coalesce(sicklv.duration, 0), 0) AS DY_D2, If((Coalesce(sicklv.duration, 0) > 2), 1, 0) AS DY_C3,
		If((Coalesce(sicklv.duration, 0) > 2), Coalesce(sicklv.duration, 0), 0) AS DY_D3 FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.sldateID <> '' AND employee.desigID In(9,209) AND sicklv.lvtypeID In(1,7) ".$filters." ORDER BY sicklv.sldateID) AS FO WHERE FO.monthID > 0 AND FO.yearID > 0 GROUP BY FO.monthID, FO.yearID ORDER BY FO.monthID, FO.yearID DESC ";  
		
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.' rowspan="2"><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.' rowspan="2"><div align="center"><strong>Month Year</strong></div></th>';
            echo '<th '.$this->HB.' colspan="6"><div align="center"><strong>Only - Drivers</strong></div></th>'; 
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>1 Days</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>2 Days</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>3 Days Plus</strong></div></th>'; 
            echo '</tr></thead>';
            
           echo '<thead><tr>';
                echo '<th '.$this->HB.'>&nbsp;</th>';
                echo '<th '.$this->HB.'>&nbsp;</th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>'; 
            echo '</tr></thead>';
			
			$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					
					echo '<tr>';
						echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';						
                        echo '<td '.$this->FB.' align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearID'].'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C1'] > 0 ? $rows_2['DY_C1'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D1'] > 0 ? $rows_2['DY_D1'] : '').'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C2'] > 0 ? $rows_2['DY_C2'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D2'] > 0 ? $rows_2['DY_D2'] : '').'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C3'] > 0 ? $rows_2['DY_C3'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D3'] > 0 ? $rows_2['DY_D3'] : '').'</td>'; 
                    echo '</tr>'; 
                    
                    
                    $incID += ($rows_2['DY_C1']) + ($rows_2['DY_C2']) + ($rows_2['DY_C3']);
                    $dayID += ($rows_2['DY_D1']) + ($rows_2['DY_D2']) + ($rows_2['DY_D3']);
                }
            }
            echo '</table>';			
        }   
    } 
	
	public function EXPORT_REPORT_SICKLEAVE_7($filters,$dateSTR,$_SENDER)
    {
        
        
        $SQL = "SELECT FO.companyID, FO.monthID, FO.yearID, Sum(FO.DY_C1) AS DY_C1,  Sum(FO.DY_D1) AS DY_D1, Sum(FO.DY_C2) AS DY_C2,  Sum(FO.DY_D2) AS DY_D2, Sum(FO.DY_C3) AS DY_C3, Sum(FO.DY_D3) AS DY_D3 FROM
		(SELECT sicklv.ID, Month(sicklv.sldateID) AS monthID, Year(sicklv.sldateID) AS yearID, sicklv.sldateID, sicklv.empID, employee.desigID, sicklv.companyID,  If(Coalesce(sicklv.duration, 0) <= 1, 1, 0) AS DY_C1, If(Coalesce(sicklv.duration, 0) <= 1, Coalesce(sicklv.duration, 0), 0) AS DY_D1,
		If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), 1, 0) AS DY_C2, If((Coalesce(sicklv.duration, 0) > 1 AND Coalesce(sicklv.duration, 0) <= 2), Coalesce(sicklv.duration, 0), 0) AS DY_D2, If((Coalesce(sicklv.duration, 0) > 2), 1, 0) AS DY_C3,
		If((Coalesce(sicklv.duration, 0) > 2), Coalesce(sicklv.duration, 0), 0) AS DY_D3 FROM sicklv LEFT JOIN employee ON employee.ID = sicklv.empID WHERE sicklv.sldateID <> '' AND employee.desigID <> 9 AND sicklv.lvtypeID In(1,7) ".$filters." ORDER BY sicklv.sldateID) AS FO WHERE FO.monthID > 0 AND FO.yearID > 0 GROUP BY FO.companyID, FO.monthID, FO.yearID ORDER BY FO.monthID, FO.yearID DESC ";  
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>';
            echo '<th '.$this->HB.' rowspan="2"><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.' rowspan="2"><div align="center"><strong>Month Year</strong></div></th>';
            echo '<th '.$this->HB.' colspan="6"><div align="center"><strong>Only - Drivers</strong></div></th>'; 
            echo '</tr></thead>';
            
            echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>1 Days</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>2 Days</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>3 Days Plus</strong></div></th>'; 
            echo '</tr></thead>';
            
           echo '<thead><tr>';
                echo '<th '.$this->HB.'>&nbsp;</th>';
                echo '<th '.$this->HB.'>&nbsp;</th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>';                
                echo '<th '.$this->HB.'><div align="center"><strong>Incidence</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>'; 
            echo '</tr></thead>';
			
			$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$CP_Array  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					
					echo '<tr>';
						echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';						
                        echo '<td '.$this->FB.' align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearID'].'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C1'] > 0 ? $rows_2['DY_C1'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D1'] > 0 ? $rows_2['DY_D1'] : '').'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C2'] > 0 ? $rows_2['DY_C2'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D2'] > 0 ? $rows_2['DY_D2'] : '').'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_C3'] > 0 ? $rows_2['DY_C3'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['DY_D3'] > 0 ? $rows_2['DY_D3'] : '').'</td>'; 
                    echo '</tr>'; 
                    
                    
                    $incID += ($rows_2['DY_C1']) + ($rows_2['DY_C2']) + ($rows_2['DY_C3']);
                    $dayID += ($rows_2['DY_D1']) + ($rows_2['DY_D2']) + ($rows_2['DY_D3']);
                }
            }
            echo '</table>';			
        }   
    } 
	
	public function EXPORT_REPORT_SICKLEAVE_8($filters,$dateSTR,$_SENDER)
    {
$SQL = " Select
    FO.companyID,
    FO.monthID,
    FO.yearNM,
    FO.desigID,
    FO.category,
    FO.incidentID,
    FO.daysID
From
    (Select
         CategoryONE.companyID,
         CategoryONE.monthID,
         CategoryONE.yearNM,
         CategoryONE.desigID,
         CategoryONE.category,
         CategoryONE.incidentID,
         CategoryONE.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              1 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
					sicklv.lvtypeID In(1,7) AND
                   sicklv.duration <= 1 And
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              1,
              SLSummary.companyID
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As CategoryONE
     UNION All
     Select
         categoryTWO.companyID,
         categoryTWO.monthID,
         categoryTWO.yearNM,
         categoryTWO.desigID,
         categoryTWO.category,
         categoryTWO.incidentID,
         categoryTWO.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              2 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
                   sicklv.duration > 1 And
				   sicklv.lvtypeID In(1,7) AND
                   sicklv.duration <= 2 And
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              SLSummary.companyID,
              1
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As categoryTWO
     UNION All
     Select
         categoryTHREE.companyID,
         categoryTHREE.monthID,
         categoryTHREE.yearNM,
         categoryTHREE.desigID,
         categoryTHREE.category,
         categoryTHREE.incidentID,
         categoryTHREE.daysID
     From
         (Select
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              3 As category,
              Sum(SLSummary.countID) As incidentID,
              Sum(Coalesce(SLSummary.duration)) As daysID,
              SLSummary.companyID
          From
              (Select
                   sicklv.ID,
                   sicklv.sldateID,
                   Month(sicklv.sldateID) As monthID,
                   Year(sicklv.sldateID) As yearNM,
                   If(employee.desigID = 209, 9, employee.desigID) As desigID,
                   sicklv.duration,
                   1 As countID,
                   sicklv.companyID
               From
                   sicklv Inner Join
                   employee On employee.ID = sicklv.empID
               Where
                   sicklv.duration >= 3 And
				   sicklv.lvtypeID In(1,7) AND
                   sicklv.sldateID <> '' And
                   sicklv.sldateID <> '0000-00-00' And
                   sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary
          Where
              SLSummary.yearNM > 0
          Group By
              SLSummary.monthID,
              SLSummary.yearNM,
              SLSummary.desigID,
              SLSummary.companyID,
              1
          Order By
              SLSummary.yearNM,
              SLSummary.monthID) As categoryTHREE) As FO
Order By
    FO.companyID,
    FO.yearNM,
    FO.monthID,
    FO.category ASC ";
	
		/*$SQL = "Select FO.companyID, FO.monthID, FO.yearNM, FO.desigID, FO.category, FO.incidentID, FO.daysID From (Select CategoryONE.companyID, CategoryONE.monthID, CategoryONE.yearNM, CategoryONE.desigID, CategoryONE.category, CategoryONE.incidentID, CategoryONE.daysID From (Select SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 1 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID
		From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID,  Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID From sicklv Inner Join employee On employee.ID = sicklv.empID Where sicklv.duration <= 1 AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary Where SLSummary.yearNM > 0 Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 1, SLSummary.companyID Order By SLSummary.yearNM,  SLSummary.monthID) As CategoryONE UNION All Select
		categoryTWO.companyID, categoryTWO.monthID, categoryTWO.yearNM, categoryTWO.desigID, categoryTWO.category, categoryTWO.incidentID, categoryTWO.daysID From (Select SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, 2 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID, Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID 
		From sicklv Inner Join employee On employee.ID = sicklv.empID  Where (sicklv.duration > 1 And sicklv.duration <= 2) AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") As SLSummary Where SLSummary.yearNM > 0 Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, SLSummary.companyID, 1 Order By SLSummary.yearNM, SLSummary.monthID) As categoryTWO UNION All Select categoryTHREE.companyID, categoryTHREE.monthID, categoryTHREE.yearNM, categoryTHREE.desigID, categoryTHREE.category, categoryTHREE.incidentID, 
		categoryTHREE.daysID From (Select SLSummary.monthID, SLSummary.yearNM,  SLSummary.desigID, 3 As category, Sum(SLSummary.countID) As incidentID, Sum(Coalesce(SLSummary.duration)) As daysID, SLSummary.companyID From (Select sicklv.ID, sicklv.sldateID, Month(sicklv.sldateID) As monthID, Year(sicklv.sldateID) As yearNM, employee.desigID, sicklv.duration, 1 As countID, sicklv.companyID From sicklv Inner Join employee On employee.ID = sicklv.empID Where sicklv.duration >= 3 AND sicklv.sldateID <> '' AND sicklv.sldateID <> '0000-00-00' AND sicklv.sldateID <> '1970-01-01' ".$filters.") 
		As SLSummary Where SLSummary.yearNM > 0  Group By SLSummary.monthID, SLSummary.yearNM, SLSummary.desigID, SLSummary.companyID, 1 Order By SLSummary.yearNM, SLSummary.monthID) As categoryTHREE) As FO Order By FO.companyID, FO.yearNM, FO.monthID, FO.category ASC";*/
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong> Sick Leave Summary Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

			echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Category</strong></div></th>'; 			
			echo '<th '.$this->HB.'><div align="center"><strong>Designation</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Incident</strong></div></th>'; 
			echo '<th '.$this->HB.'><div align="center"><strong>Days</strong></div></th>'; 
			
			
			$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            { 
				$incID = 0;	$dayID = 0;
                foreach($this->rows_1 as $rows_2)
                {
					$arrCMP  = $rows_2['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_2['companyID']." ") : '';
					$arrMST  = $rows_2['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_2['desigID']." ") : '';
					
					echo '<tr>';
						echo '<td '.$this->FB.'>'.$arrCMP[0]['title'].'</td>';
						
                        echo '<td '.$this->FB.' align="center">'.$months[(int)$rows_2['monthID']].' - '.$rows_2['yearNM'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['category'] == 1 ? 'ONE' :($rows_2['category'] == 2 ? 'TWO' :($rows_2['category'] == 3 ? 'THREE++' : 'N-M'))).'</td>';
                        echo '<td '.$this->FB.'>'.($rows_2['desigID'] == 9 ? 'Driver / Coordinator' : $arrMST[0]['title']).'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['incidentID'] > 0 ? $rows_2['incidentID'] : '').'</td>';
                        echo '<td '.$this->FB.' align="center">'.($rows_2['daysID'] > 0 ? $rows_2['daysID'] : '').'</td>'; 
                    echo '</tr>';
					
                    $incID += ($rows_2['incidentID']);
                    $dayID += ($rows_2['daysID']);
                }
				
				echo '<tr>';
                    echo '<td style="background:#367FA9; color:white;" class="d-set" colspan="4" align="right"><b>GTotal : </b></td>';
                    echo '<td style="background:#367FA9; color:white;" class="d-set" colspan="2" align="center"><b> Total Incidence : '.$incID.'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Total Days : '.$dayID.'</b></td>';
				echo '</tr>';  
            }
            echo '</table>';			
        }   
    } 
	
    public function EXPORT_REPORT_EMPLOYEE($filters)
    {
        $_SENDER = $filters;

        $return  = "";		
        $return .= $filters['fltID_3'] > 0    ? " AND employee.genderID = ".$filters['fltID_3'] : "";
        $return .= $filters['fltID_5'] > 0    ? " AND employee.desigID = ".$filters['fltID_5'] : "";
        $return .= $filters['fltID_2'] > 0    ? " AND employee.sid = ".$filters['fltID_2'] : "";
        $return .= $filters['fltID_1'] <> '0' && $filters['fltID_1'] <> '' ? " AND employee.ftextID = '".$filters['fltID_1']."' " : "";
        $return .= $filters['fltID_4'] > 0    ? " AND employee.casualID = ".$filters['fltID_4'] : "";
        $return .= $filters['fltID_6'] > 0    ? " AND employee.rleavingID = ".$filters['fltID_6'] : "";
        $return .= $filters['fltID_7'] > 0    ? " AND employee.desigID = ".$filters['fltID_7'] : "";
        $return .= $filters['fltID_8'] > 0    ? " AND employee.desigID = ".$filters['fltID_8'] : "";
        $return .= $filters['compID'] <> ''   ? " AND employee.companyID In (".$filters['compID'].") " : "";

        if($filters['rtpyeID'] == 3)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'esdate');}}
        if($filters['rtpyeID'] == 4)	{if(is_array($filters) && count($filters) > 0)   {$return .= $this->Create_Reports_Date($filters,'enddate');}}

		$passID = '';
		if($filters['rtpyeID'] == 2)
		{
			$passID = ($filters['dashID'] == 1 ? 'Drivers' : ($filters['dashID'] == 2 ? 'WWC' :($filters['dashID'] == 3 ? 'EnggLicense' : '')));
		}
		
        $dateSTR = "";
        $dateSTR = (($filters['fromID'] <> '') ? '-  (<b>From : '.$filters['fromID'].' - To : '.$filters['toID'].')</b>' : '');
		
		$_SENDER['daysID'] = $_SENDER['daysID'] > 0 ? $_SENDER['daysID'] : 60;
		
        if($filters['rtpyeID'] == 1)        {echo $this->EXPORT_REPORT_EMPLOYEE_1($return,$_SENDER);} 
        else if($filters['rtpyeID'] == 2)	{echo $this->EXPORT_REPORT_EMPLOYEE_2($return,$_SENDER,$passID);} 
        else if($filters['rtpyeID'] == 3)	{echo $this->EXPORT_REPORT_EMPLOYEE_3($return,$_SENDER,$dateSTR);} 
        else if($filters['rtpyeID'] == 4)	{echo $this->EXPORT_REPORT_EMPLOYEE_4($return,$_SENDER,$dateSTR);}    
    }

    public function EXPORT_REPORT_EMPLOYEE_1($filters,$_SENDER)
    {
        

        $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 1  ".$filters." Order By code ASC ";
        
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {		    
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="10" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Transactions Data - Employee Report</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Air Key No.</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Drive Right No</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Locker No.</strong></div></th>';            
            echo '<th '.$this->HB.' width="150"><div align="center"><strong>Suburb</strong></div></th>';
            echo '<th '.$this->HB.' width="120"><div align="center"><strong> Gender</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Casual Full Time</strong></div></th>';
            echo '<th '.$this->HB.' width="180"><div align="center"><strong>Designation</strong></div></th>';
            echo '</tr></thead>';

            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    $GN_Array  = $rows_1['genderID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['genderID']." ") : '';
                    $DE_Array  = $rows_1['desigID'] > 0   ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
                    $SU_Array  = $rows_1['sid'] > 0       ? $this->select('suburbs',array("*"), " WHERE ID = ".$rows_1['sid']." ") : '';

                    echo '<tr>'; 
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                        echo '<td '.$this->FB.' align="center" >'.$rows_1['arkno'].'</td>';	
                        echo '<td '.$this->FB.' align="center">'.$rows_1['drvrightID'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['lockerno'].'</td>';
                        echo '<td '.$this->FB.' >'.$SU_Array[0]['title'].' - '.$SU_Array[0]['pscode'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$GN_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' class="d-set"align="center">'.($rows_1['casualID'] == 1 ? 'Full Time'  :($rows_1['casualID'] == 2 ? 'Part Time' :($rows_1['casualID'] == 3 ? 'Casual' : ''))).'</td>';
                        echo '<td '.$this->FB.'>'.$DE_Array[0]['title'].'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }  
    } 

    public function EXPORT_REPORT_EMPLOYEE_2($filters,$_SENDER,$passID)
    {        		
        $crtID = " AND DATE(All_Data.lcnoDT) <= '".date('Y-m-d',strtotime('+'.$_SENDER['daysID'].'Days'))."' ";
		$crtID .= ($passID == 'EnggLicense' ? " AND All_Data.typeID <> 'Drivers' AND All_Data.typeID <> 'WWC' " 
				 :($passID <> '' ? " AND All_Data.typeID = '".$passID."' "
				 : "AND All_Data.typeID In('Drivers','WWC')"));
			
        $SQL = "SELECT All_Data.ID, All_Data.code, All_Data.full_name, All_Data.desigID, All_Data.companyID, All_Data.lcnoID, All_Data.lcnoDT, All_Data.typeID
        FROM (SELECT ID, code, full_name, desigID, companyID, ddlcno AS lcnoID, ddlcdt AS lcnoDT, 'Drivers' AS typeID FROM employee	WHERE status = 1 ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, wwcprno AS lcnoID, wwcprdt AS lcnoDT, 'WWC' AS typeID FROM employee WHERE status = 1 AND desigID In(9,208,209) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, gfpermitNO AS lcnoID, gfpnexpDT AS lcnoDT, 'GasFittingNO' AS typeID FROM employee WHERE gfpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, acpermitNO AS lcnoID, acpnexpDT AS lcnoDT, 'AConRefNO' AS typeID FROM employee WHERE acpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, wsdpermitNO AS lcnoID, wsdpnexpDT AS lcnoDT, 'WorkSafeDoggingNO' AS typeID FROM employee WHERE wsdpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters." UNION ALL 
		SELECT ID, code, full_name, desigID, companyID, flpermitNO AS lcnoID, flpnexpDT AS lcnoDT, 'ForliftLcNO' AS typeID FROM employee WHERE flpermitNO <> '' AND status = 1 AND desigID In(418) ".$filters.") 
		AS All_Data WHERE All_Data.companyID > 0 ".$crtID." Order By All_Data.lcnoDT DESC ";
		$Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Licences Expiring within '.$_SENDER['daysID'].' Days - Employee Report</strong></div></th>';
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Staff Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Licence Type</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Expiry Date</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    $DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
					
                    echo '<tr>'; 
                    echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>';
                    echo '<td '.$this->FB.'>'.$rows_1['full_name'].'</td>';
					echo '<td '.$this->FB.'>'.$DE_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' class="d-set"align="center">'.$rows_1['typeID'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_1['lcnoDT']).'</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }  
    } 
        
    public function EXPORT_REPORT_EMPLOYEE_3($filters,$_SENDER,$dateSTR)
    {
        

        $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 1 ".$filters." Order By ID ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        { 
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="5" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>New Employee Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.' width="180"><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Start Date</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;
                foreach($this->rows_1 as $rows_1)
                {
                    $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    $DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';

                    echo '<tr>'; 
                    echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>';
                    echo '<td '.$this->FB.'>'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                    echo '<td '.$this->FB.'>'.$DE_Array[0]['title'].'</td>';
                    echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_1['esdate']).'</td>';						
                    echo '</tr>';
                }
            }
            echo '</table>';			
        }  
    } 
    
    public function EXPORT_REPORT_EMPLOYEE_4($filters,$_SENDER,$dateSTR)
    {
        

        $SQL = "SELECT * FROM employee WHERE ID > 0 AND status = 2  AND rleavingID <> 3 ".$filters." Order By code ASC ";
        $Qry = $this->DB->prepare($SQL);
        if($Qry->execute())
        {
            $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
            echo '<table id="dataTables" class="table table-bordered table-striped">';				
            echo '<thead><tr>';
            echo '<th '.$this->HB.' colspan="8" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Ex Employee Report '.$dateSTR.'</strong></div></th>'; 
            echo '</tr></thead>';

            echo '<thead><tr>'; 
            echo '<th '.$this->HB.'><div align="center"><strong>Depot</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff ID</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Staff Name</strong></div></th>';
            echo '<th '.$this->HB.' width="180"><div align="center"><strong>Designation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>End Date</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Reason</strong></div></th>';
			echo '<th '.$this->HB.'><div align="center"><strong>Reason For Resignation</strong></div></th>';
            echo '<th '.$this->HB.'><div align="center"><strong>Retention Period</strong></div></th>';
            echo '</tr></thead>';
            if(is_array($this->rows_1) && count($this->rows_1) > 0)			
            {
                $srID = 1;  $sdateID = '';  $edateID = '';  $retentionID = '';
                foreach($this->rows_1 as $rows_1)
                {
                    $CP_Array  = $rows_1['companyID'] > 0  ? $this->select('company',array("*"), " WHERE ID = ".$rows_1['companyID']." ") : '';
                    $DE_Array  = $rows_1['desigID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['desigID']." ") : '';
					$RS_Array  = $rows_1['resonrgID'] > 0  ? $this->select('master',array("*"), " WHERE ID = ".$rows_1['resonrgID']." ") : '';

                    $sdateID = $rows_1['esdate'];
					$edateID = $rows_1['enddate'];
					
					$date1 = strtotime($sdateID);
					$date2 = strtotime($edateID);
					$diff  = abs($date2 - $date1);
								
					/*$diff   = abs(strtotime($edateID) - strtotime($sdateID));
					$years  = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
					$days   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
					
					$retentionID = (($years > 0 ? $years.' Years, ' : '').($months > 0 ? $months.' Months, ' : '').($days > 0 ? $days.' Days ' : ''));*/
								
                    echo '<tr>'; 
                        echo '<td '.$this->FB.'>'.$CP_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['code'].'</td>';
                        echo '<td '.$this->FB.'>'.$rows_1['fname'].' '.$rows_1['lname'].'</td>';
                        echo '<td '.$this->FB.'>'.$DE_Array[0]['title'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->VdateFormat($rows_1['enddate']).'</td>';
                        echo '<td '.$this->FB.' style="width:180px;" align="center">'.($rows_1['rleavingID'] == 1 ? 'Resigned'  
                         :($rows_1['rleavingID'] == 2 ? 'Terminated' 
                         :($rows_1['rleavingID'] == 3 ? 'Transferred'
                         :($rows_1['rleavingID'] == 4 ? 'Retired'
                        :($rows_1['rleavingID'] == 5 ? 'Deceased' : ''))))).'</td>';
                         echo '<td '.$this->FB.'>'.$RS_Array[0]['title'].'</td>';
						 echo '<td '.$this->FB.' align="center">'.round((($diff/86400) / 365),1).'</td>';
                    echo '</tr>';

                }
            }
            echo '</table>';			
        }  
    }	
        
    public function EXPORT_ALLOCATION_SHEET($fromID)
    {
        if($fromID <> '')
        {
            
            $SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND 
            shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = 
            '".$this->dateFormat($fromID)."' AND imp_shift_daily.companyID In(".$this->companyID.") 
            AND imp_shift_daily.imp_statusID In(1) 
            ORDER BY Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_2, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_9,'')))) ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($fromID <> '') ? '-  (<b style="color:black;">Date : '.$fromID.')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th colspan="15" class="knob-labels notices" style="font-weight:600; font-size:18px;"><div align="center"><strong>
                Allocation Sheet '.$prID.'</strong></div></th>';
                echo '</tr></thead>';
 
                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Sr. No</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>SHIFT</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON DUTY</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>EX DEPOT</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>OPERATOR</strong></div></th>';
                echo '<th '.$this->HB.' colspan="2"><div align="center"><strong>BUS NO</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>BUS TYPE</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>MEAL BREAK</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>COMMENTS</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON ROAD C/O</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>STOW</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>LAST TRIP</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>LOC.</strong></div></th>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$op_dayID = '';	$returnID = 0;
                    $row_colorID = '';
                    $empID = 0; $emp_reqID = 0;
                    $busID = 0; $bus_reqID = 0;
                    foreach($this->rows_1 as $rows_1)
                    {
                        $arrayRT = $this->select('shift_masters_dtl',array("*"), " WHERE ID = ".$rows_1['shiftID']." AND recID = ".$rows_1['shift_recID']." 
                        Order By recID DESC ");

                        /* START - DRIVER - SHIFT - CODE */
                        $row_colorID = (empty($rows_1['fID_14']) && empty($rows_1['fID_014']) ? 
                                                'style="background:#F56954 !important; color:white !important;"' : '');

                        $emp_reqID = ($rows_1['fID_018'] > 0 ? 2 : 1);
                        $empID     = ($rows_1['fID_018'] > 0 ? $rows_1['fID_018'] : $rows_1['fID_013']);

                        $bus_reqID = ($rows_1['fID_014'] > 0 ? 2 : 1);
                        $busID     = ($rows_1['fID_014'] > 0 ? $rows_1['fID_014'] : $rows_1['fID_14']);

                        $arrayID  = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';

                        $arrayBS  = $rows_1['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows_1['fID_014']." ") : '';
                        /* ENDS - DRIVER - SHIFT - CODE */

						if((($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9'])) <> '')
						{
							echo '<tr>';
							echo '<td '.$this->FB.' align="center"><b>'.$srID++.'</b></td>';
							echo '<td '.$this->FB.' align="center"><b>'.$rows_1['fID_1'].' - '.$rows_1['tagCD'].'</b></td>';

							echo '<td '.$this->FB.' align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_2'] : $arrayRT[0]['fID_9']).'</b></td>';
							echo '<td '.$this->FB.' align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_3'] : $arrayRT[0]['fID_10']).'</b></td>';

							/************** START - SWAPPING - WORK - AREAS  *****************/

							/* EMPLOYEE  - DETAILS */
							echo '<td '.$this->FB.'>';
								echo '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($emp_reqID == 2 ? '#F56954' : 'black').';" 
								class="swipe_modelID" aria-sort="EMPLOYEE_'.$rows_1['recID'].'_'.$emp_reqID.'">'.
								(strtoupper($arrayID[0]['code'].' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'])).'</a>';
							echo '</td>';

							echo '<td '.$this->FB.'>';
								if((int)$rows_1['fID_018'] > 0)
								{
									echo '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" 
									aria-sort="EMPLOYEE_'.$rows_1['dateID'].'_'.$emp_reqID.'_'.$empID.'_'.$rows_1['recID'].'"></a>';
								}
							echo '</td>';


							/* BUS NO - DETAILS */
							echo '<td align="center" '.$this->FB.'>';
								echo '<a style="text-decoration:none;cursor:pointer; font-weight:bold; color:'.($bus_reqID == 2 ? '#F56954' : 'black').';" 
								class="swipe_modelID" aria-sort="BUSES_'.$rows_1['recID'].'_'.$bus_reqID.'">';
									if((empty($rows_1['fID_14']) && empty($rows_1['fID_014'])))
									{
										echo '&nbsp; - &nbsp;';   
									}
									else
									{
										echo ($bus_reqID == 2 ? strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno']) : $rows_1['fID_14']);
									}
								echo '</a>';
							echo '</td>';

							echo '<td '.$this->FB.'>';
								if((int)$rows_1['fID_014'] > 0)
								{
									echo '<a class="fa fa-undo swipe_undoID" style="text-decoration:none;cursor:pointer;" title="Undo Staff" 
									aria-sort="BUSES_'.$rows_1['dateID'].'_'.$bus_reqID.'_'.$busID.'_'.$rows_1['recID'].'"></a>';
								}
							echo '</td>'; 

							/************** ENDSS - SWAPPING - WORK - AREAS  *****************/

							echo '<td '.$this->FB.' align="center"><b>'.strtoupper($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_20'] : $arrayRT[0]['fID_21']).'</b></td>';
							echo '<td '.$this->FB.'>'.strtoupper($arrayRT[0]['fID_19']).'</td>';
							echo '<td '.$this->FB.'>'.$rows_1['fID_4'].'</td>';
							echo '<td '.$this->FB.'>'.$rows_1['fID_6'].'</td>';
							echo '<td '.$this->FB.' align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_4'] : $arrayRT[0]['fID_11']).'</b></td>';
							echo '<td '.$this->FB.' align="center"><b>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_5'] : $arrayRT[0]['fID_13']).'</b></td>';
							echo '<td '.$this->FB.'>'.($rows_1['tagCD'] == 'A' ? $arrayRT[0]['fID_6'] : $arrayRT[0]['fID_14']).'</td>';

							echo '</tr>';
						}
                    }
                }
                echo '</table>';			
            } 
            echo $file;
        }
    }	
	
    public function EXPORT_DAILY_SHEET($dateID)
    {
		if($dateID <> '')
        { 
            $SQL = "SELECT * FROM imp_shift_daily WHERE recID > 0 AND DATE(dateID) = '".$this->dateFormat($dateID)."' 
			AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") Order By fID_1 ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';
                echo '<thead><tr>';
				echo '<th colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
				Daily Sheet : Date - '.$dateID.'</strong></div></th>';				
                echo '</tr></thead>';				

                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>Sr. No</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>SHIFT NO</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>STAFF NAME</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>BUS NAME</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>COMMENTS</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON ROAD C/O</strong></div></th>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
					$srID = 1;	$empNAME = '';	$busNAME = '';
					foreach($this->rows_1 as $rows_1)
					{
						if($rows_1['fID_018'] > 0)
						{
							$arrayID  = $rows_1['fID_018'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['fID_018']." ") : '';
							$empNAME = '<b>'.$arrayID[0]['code'].'</b>'.' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'];
						}
						else
						{
							$arrayID  = $rows_1['fID_013'] > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$rows_1['fID_013']." ") : '';
							$empNAME = '<b>'.$arrayID[0]['code'].'</b>'.' - '.$arrayID[0]['fname'].' '.$arrayID[0]['lname'];
						}
						
						if($rows_1['fID_014'] > 0)
						{
							$arrayBS  = $rows_1['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows_1['fID_014']." ") : '';
							$busNAME  = strtoupper($arrayBS[0]['busno'].' - '.$arrayBS[0]['modelno']);
						}
						else	{$busNAME = $rows_1['fID_14'];}
						
						echo '<tr>';
							echo '<td '.$this->FB.' align="center"><b>'.$srID++.'</b></td>';
							echo '<td '.$this->FB.' align="center">'.$rows_1['fID_1'].' - <b>'.$rows_1['tagCD'].'</b></td>';
							echo '<td '.$this->FB.'>'.strtoupper($empNAME).'</td>';
							echo '<td '.$this->FB.'>'.strtoupper($busNAME).'</td>';
							echo '<td '.$this->FB.'>'.$rows_1['fID_4'].'</td>';
							echo '<td '.$this->FB.'>'.$rows_1['fID_6'].'</td>';
						echo '</tr>';	
					}
				}
				echo '</table>';
			}
        }
    }
    
    public function EXPORT_SETTER_SHEET($dateID,$sheetID)
    {
        if($dateID <> '')
        { 
            $arrayST = ($sheetID > 0 ? $this->select('shift_masters',array("*"), " WHERE ID = ".$sheetID." ") : '');	
            
            $SQL = "SELECT * FROM shift_masters_dtl WHERE ID = ".$sheetID." AND fID_1 <> '' Order By recID ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';
                echo '<thead><tr>';
                echo '<th colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Shift Setter Sheet : Date - '.$dateID.'</strong></div></th>';				
                echo '</tr></thead>';				

                echo '<tr class="heading">';
                echo '<th '.$this->HB.' colspan="9"><div align="center"><strong>Version Date : '.date('d-M-Y',strtotime($arrayST[0]['createDT'])).'</strong></div></th>';
                echo '<th '.$this->HB.' colspan="9"><div align="center"><strong>Applicable Date : '.date('d-M-Y',strtotime($arrayST[0]['availDT'])).'</strong></div></th>';
                echo '</tr>';
                
                echo '<tr class="heading">';
                  echo '<td height="35" rowspan="2" '.$this->HB.'><div align="center">Sr. No.</div></td>';
                  echo '<td height="35" rowspan="2" '.$this->HB.'><div align="center">Shift No</div></td>';
                  echo '<td height="35" colspan="7" '.$this->HB.'><div align="center">SHIFT - FIRST HALF</div></td>';
                  echo '<td height="35" colspan="7" '.$this->HB.'><div align="center">SHIFT - SECOND HALF</div></td>';
                  echo '<td height="35" rowspan="2" '.$this->HB.'><div align="center">Total</div></td>';
                  echo '<td height="35" rowspan="2" '.$this->HB.'><div align="center">OP Day</div></td>';
                echo '</tr>';

                echo '<tr class="heading">';
                  echo '<td height="35" '.$this->HB.'><div align="center">On</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Ex Depot</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Stow</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Off</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Last Trip</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Last Loc</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Hours</div></td>';

                  echo '<td height="35" '.$this->HB.'><div align="center">On</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Ex Depot</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Stow</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Off</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Last Trip</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Last Loc</div></td>';
                  echo '<td height="35" '.$this->HB.'><div align="center">Hours</div></td>';
				  echo '<td height="35" '.$this->HB.'><div align="center">Bus Type</div></td>';
                echo '</tr>'; 
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;	$empNAME = '';	$busNAME = '';
                    foreach($this->rows_1 as $rows)
                    {
						echo '<tr>';
							echo '<td '.$this->FB.' align="center"><b>'.$srID++.'</b></td>';
							echo '<td '.$this->FB.'>'.$rows['fID_1'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_2'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_3'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_4'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_5'].'</td>';                            
							echo '<td '.$this->FB.' align="center">'.$rows['fID_7'].'</td>';
							echo '<td '.$this->FB.'>'.strtoupper($rows['fID_6']).'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_8'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_20'].'</td>';

							echo '<td '.$this->FB.' align="center">'.$rows['fID_9'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_10'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_11'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_12'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_13'].'</td>';
							echo '<td '.$this->FB.'>'.strtoupper($rows['fID_14']).'</td>';            
							echo '<td '.$this->FB.' align="center">'.$rows['fID_15'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_21'].'</td>';
							echo '<td '.$this->FB.'>'.$rows['fID_16'].'</td>';			
							// echo '<td '.$this->FB.' align="center">'.$rows['fID_16'].'</td>';
							echo '<td '.$this->FB.' align="center">'.$rows['fID_18'].'</td>';
						echo '</tr>';	
                    }
                }
                echo '</table>';
            }
        }
    }
    
    public function EXPORT_PRINT_HEADER_SHEET($fromID,$stypeID)
    {
        $dayID = '';
        $dayID = date('l',strtotime(date($this->dateFormat($fromID))));
		
        $arrayCM = $this->select('company',array("*"), " WHERE ID = ".$_SESSION[$this->website]['compID']." "); 
        $arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($fromID)."' Order By ID DESC LIMIT 1 ");

		$shiftID = 0;	$createDT = '';
		if($arraySE[0]['ID'] > 0)	
		{
			$shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
			$createDT = $arraySE[0]['createDT'];
		}
		else if($dayID == 'Saturday')
		{
			$array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($fromID)."' Order By availDT DESC LIMIT 1 ");
			$shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
			$createDT = $array_2[0]['createDT'];
		}
		else if($dayID == 'Sunday')
		{
			$array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($fromID)."' Order By availDT DESC LIMIT 1 ");
			$shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
			$createDT = $array_3[0]['createDT'];
		}		
		else
		{
			$array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($fromID)."' Order By availDT DESC LIMIT 1 ");
			$shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
			$createDT = $array_4[0]['createDT'];
		}
        
        $dayID = '';
        $dayID = date('l',strtotime(date('D')));

        if($shiftID > 0)
        {
            $SQL = "SELECT * FROM shift_masters_dtl WHERE ID = ".$shiftID." AND fID_1 <> '' Order By Time(fID_2) ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $prID = (($fromID <> '') ? '-  (<b style="color:white;">Date : '.$fromID.')</b>' : '');
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';				
                echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                '.$arrayCM[0]['title'].' Header Sheet </strong></div></th>';
                echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>
                Version Date : '.$this->VdateFormat($createDT).'</strong></div></th>';
                echo '</tr></thead>';


                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>SR. No.</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>SHIFT</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>OFF</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>HOURS</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>OFF</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>HOURS</strong></div></th>';

                echo '<th '.$this->HB.'><div align="center"><strong>TOTAL</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>WEEK</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>DAY</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>TYPE</strong></div></th>';
                echo '</tr></thead>';
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                { 
                    $srID = 1;	$timeID = '';   $typeNAME = '';
                    foreach($this->rows_1 as $rows_1)
                    {
                        if(strtotime(str_replace('h',':',$rows_1['fID_16'])) < strtotime('7:30'))
                        {
                            $typeNAME = '<td align="center"><b style="color:#0DA23E;">CASUAL</b></td>';                            
                        }
                        else if((strtotime($rows_1['fID_9']) - strtotime($rows_1['fID_7'])) > (90*60))
                        {    
                            $typeNAME = '<td align="center"><b style="color:#F56954;">SPREAD</b></td>';
                        }
                        else if(strtotime($rows_1['fID_2']) < strtotime('8:30'))
                        {
                            $typeNAME = '<td align="center"><b style="color:#1656A5;">EARLY</b></td>';
                        }
                        else
                        {
                            $typeNAME = '<td align="center"><b style="color:#FF2222;">LATE</b></td>';
                        }
                        
                        echo '<tr>';
                        echo '<td '.$this->FB.' align="center"><b>'.$srID++.'</b></td>';
                        echo '<td '.$this->FB.' align="center"><b>'.$rows_1['fID_1'].'</b></td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['fID_2'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['fID_7'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.str_replace('h',':',$rows_1['fID_8']).'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['fID_9'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.$rows_1['fID_12'].'</td>';
                        echo '<td '.$this->FB.' align="center">'.str_replace('h',':',$rows_1['fID_15']).'</td>';

                        echo '<td '.$this->FB.' align="center">'.str_replace('h',':',$rows_1['fID_16']).'</td>';
                        echo '<td '.$this->FB.' align="center">'.$this->RunTimeCalculate((str_replace('h',':',$rows_1['fID_16'])),(strlen($rows_1['fID_18']))).'</td>';                            
                        echo '<td '.$this->FB.' align="center">'.$rows_1['fID_18'].'</td>';
                        echo $typeNAME;	
                        echo '</tr>';
                    }
                }
                echo '</table>';			
            } 
        }
    }
    
    public function EXPORT_DAILY_SHEET_GENERATOR($dateID)
    {
        if($dateID <> '')
        { 
            $dayID = '';
            $dayID = date('l',strtotime(date($this->dateFormat($dateID))));

            $arraySE = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 9 AND stypeID <= 9) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) = '".$this->dateFormat($dateID)."' Order By ID DESC LIMIT 1 ");
             
            $shiftID = 0;	$stypeID = 0;		
            if($arraySE[0]['ID'] > 0)	
            {
                $shiftID = ($arraySE[0]['ID'] > 0 	  ? $arraySE[0]['ID'] : 0);
                $stypeID = ($arraySE[0]['stypeID'] > 0 ? $arraySE[0]['stypeID'] : 0);
            }
            else if($dayID == 'Saturday')
            {
                $array_2 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 7 AND stypeID <= 7) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
                $shiftID = ($array_2[0]['ID'] > 0	  ? $array_2[0]['ID'] : 0);
                $stypeID = ($array_2[0]['stypeID'] > 0 ? $array_2[0]['stypeID'] : 0);
            }
            else if($dayID == 'Sunday')
            {
                $array_3 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 8 AND stypeID <= 8) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
                $shiftID = ($array_3[0]['ID'] > 0 	  ? $array_3[0]['ID'] : 0);
                $stypeID = ($array_3[0]['stypeID'] > 0 ? $array_3[0]['stypeID'] : 0);
            }		
            else
            {
                $array_4 = $this->select('shift_masters',array("*"), " WHERE usedBY = 'A' AND (stypeID >= 1 AND stypeID <= 6) AND statusID = 1 AND companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(availDT) <= '".$this->dateFormat($dateID)."' Order By availDT DESC LIMIT 1 ");
                $shiftID = ($array_4[0]['ID'] > 0 	  ? $array_4[0]['ID'] : 0);
                $stypeID = ($array_4[0]['stypeID'] > 0 ? $array_4[0]['stypeID'] : 0);
            }
            
            
            $SQL = "SELECT * FROM shift_masters_dtl WHERE usedBY = 'A' AND companyID = ".$_SESSION[$this->website]['compID']." AND ID = ".$shiftID." AND fID_1 <> '' Order By fID_1 ASC ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';
                echo '<thead><tr>';
                echo '<th '.$this->HB.' class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Daily Upload </strong></div></th>';
                echo '<th '.$this->HB.'>'.date('d/M/Y',strtotime($this->dateFormat($dateID))).'</th>';
                echo '<th '.$this->HB.' colspan="5"></th>';
                echo '</tr></thead>';
                
                echo '<thead><tr>';
                echo '<th '.$this->HB.'><div align="center"><strong>SHIFT</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>STAFF ID</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>BUS NUMBER</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>COMMENTS (A)</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>COMMENTS (B)</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON ROAD C/O (A)</strong></div></th>';
                echo '<th '.$this->HB.'><div align="center"><strong>ON ROAD C/O (B)</strong></div></th>';
                echo '</tr></thead>';
            
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;   $returnVAL = 0;
                    foreach($this->rows_1 as $rows)
                    {
                        if($stypeID == 9)	{$returnVAL = 1;}
                        else
                        {
                            $returnVAL = $this->GET_DAY_NAME($rows['fID_18'],$dateID);
                            $returnVAL = ($returnVAL > 0 ? $returnVAL : 0);
                        }
						
                        if($returnVAL == 1)
                        {
                            echo '<tr>'; 
                                echo '<td '.$this->FB.'>'.$rows['fID_1'].'</td>';
                                echo '<td '.$this->FB.'></td>';
                                echo '<td '.$this->FB.'></td>';
                                echo '<td '.$this->FB.'></td>';
                                echo '<td '.$this->FB.'></td>';
                                echo '<td '.$this->FB.'></td>';
                                echo '<td '.$this->FB.'></td>';
                            echo '</tr>';	
                        }
                    }
                }
                echo '</table>';
            }
        }
    }

    public function EXPORT_SIGNONINFO_DETAIL_SHEET($dateID)
    {
        if($dateID <> '')
        { 
            $SQL = "SELECT imp_shift_daily.dateID, imp_shift_daily.fID_1 AS shiftNO, imp_shift_daily.shift_recID AS shiftRID FROM imp_shift_daily WHERE imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") AND DATE(dateID) BETWEEN '".$this->dateFormat($dateID)."' AND '".$this->dateFormat($dateID)."' GROUP BY imp_shift_daily.dateID, imp_shift_daily.fID_1, imp_shift_daily.shift_recID, imp_shift_daily.companyID ";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';
                echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="2" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Date : '.date('d/M/Y',strtotime($this->dateFormat($dateID))).' </strong></div></th>';
                echo '<th '.$this->HB.' colspan="5" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Sign-On Info Report </strong></div></th>';
                echo '</tr></thead>';
                
                
                echo '<thead><tr>'; 
                echo '<th rowspan="2" '.$this->HB.'><div align="center"><strong>SHIFT</strong></div></th>';                
                echo '<th colspan="3" '.$this->HB.'><div align="center"><strong>HALF - 1</strong></div></th>';
                echo '<th colspan="3" '.$this->HB.'><div align="center"><strong>HALF - 2</strong></div></th>'; 
                echo '</tr>';
                
                echo '<tr>';
                    echo '<th '.$this->HB.'><div align="center"><strong>DRIVER CODE</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>DRIVER NAME</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>BUS NO</strong></div></th>';
                    
                    echo '<th '.$this->HB.'><div align="center"><strong>DRIVER CODE</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>DRIVER NAME</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>BUS NO</strong></div></th>';
                echo '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;  $empID_A = 0;  $empID_B = 0;
                    foreach($this->rows_1 as $rows)
                    {
                        $arrA = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$rows['dateID']."' AND fID_1 = ".$rows['shiftNO']." AND tagCD = 'A' ");
                        $arrB = $this->select('imp_shift_daily',array("*"), " WHERE dateID = '".$rows['dateID']."' AND fID_1 = ".$rows['shiftNO']." AND tagCD = 'B' ");

                        $empID_A = $arrA[0]['fID_018'] > 0 ? $arrA[0]['fID_018'] : $arrA[0]['fID_013'];
                        $empID_B = $arrB[0]['fID_018'] > 0 ? $arrB[0]['fID_018'] : $arrB[0]['fID_013'];
                        
                        $shiftA = $arrA[0]['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$arrA[0]['shift_recID']." ") : '';
                        $shiftB = $arrB[0]['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$arrB[0]['shift_recID']." ") : '';
                    
                        $empA  = $empID_A > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID_A." ") : '';
                        $empB  = $empID_B > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID_B." ") : '';
                        
                        $busA  = $arrA[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$arrA[0]['fID_014']." ") : '';
                        $busB  = $arrB[0]['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$arrB[0]['fID_014']." ") : '';
                        
                        echo '<tr>'; 
                            echo '<td '.$this->FB.' align="center"><b>'.$rows['shiftNO'].'</b></td>';

                            echo '<td '.$this->FB.' align="center"><b>'.$empA[0]['code'].'</b></td>';                        
                            echo '<td '.$this->FB.'><b>'.($empA[0]['fname'].' '.$empA[0]['lname']).'</b></td>';                            
                            echo '<td '.$this->FB.' align="center"><b>'.($arrA[0]['fID_014'] <> '' ? $busA[0]['busno'] : $arrA[0]['fID_14']).'</b></td>';

                            echo '<td '.$this->FB.' align="center"><b>'.($shiftB[0]['fID_9'] <> '' ? $empB[0]['code'] : '').'</b></td>';
                            echo '<td '.$this->FB.'><b>'.($shiftB[0]['fID_9'] <> '' ? ($empB[0]['fname'].' '.$empB[0]['lname']): '').'</b></td>';                            
                            echo '<td '.$this->FB.' align="center"><b>'.($shiftB[0]['fID_9'] <> '' ? (($arrB[0]['fID_014'] <> '' ? $busB[0]['busno'] : $arrB[0]['fID_14'])) : '').'</b></td>';
                        echo '</tr>';                        
                    }
                }
                echo '</table>';
            }
        }
    }
    
    public function EXPORT_SIGNON_DETAIL_SHEET($dateID)
    {
        if($dateID <> '')
        {
			$SQL = "SELECT imp_shift_daily.* FROM imp_shift_daily LEFT JOIN shift_masters_dtl ON shift_masters_dtl.ID = imp_shift_daily.shiftID AND 
            shift_masters_dtl.recID = imp_shift_daily.shift_recID WHERE imp_shift_daily.recID > 0 AND DATE(imp_shift_daily.dateID) = '".$this->dateFormat($dateID)."' AND imp_shift_daily.companyID In(".$_SESSION[$this->website]['compID'].") 
            AND imp_shift_daily.imp_statusID In(1) ORDER BY Time(If(imp_shift_daily.tagCD = 'A', shift_masters_dtl.fID_3, (If(imp_shift_daily.tagCD = 'B', shift_masters_dtl.fID_10,'')))) ASC";
            $Qry = $this->DB->prepare($SQL);
            if($Qry->execute())
            {
                $this->rows_1 = $Qry->fetchAll(PDO::FETCH_ASSOC);
                echo '<table id="dataTables" class="table table-bordered table-striped">';
                echo '<thead><tr>';
                echo '<th '.$this->HB.' colspan="2" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Date : '.date('d/M/Y',strtotime($this->dateFormat($dateID))).' </strong></div></th>';
                echo '<th '.$this->HB.' colspan="6" class="knob-labels notices" style="font-weight:600; font-size:14px;"><div align="center"><strong>Sign-On Detail Report </strong></div></th>';
                echo '</tr></thead>';
                
                
                echo '<thead><tr>'; 
					echo '<th '.$this->HB.'><div align="center"><strong>SHIFT</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>OPERATOR</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>BUS NO</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>ON</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>OFF</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>HOURS</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>SIGNED AT</strong></div></th>';
                    echo '<th '.$this->HB.'><div align="center"><strong>TIME DIFF</strong></div></th>';
                echo '</tr></thead>';
                
                if(is_array($this->rows_1) && count($this->rows_1) > 0)			
                {
                    $srID = 1;  $empID = 0; $diffVL = '';
                    foreach($this->rows_1 as $rows)
                    {
                        $empID   = $rows['fID_018'] > 0 ? $rows['fID_018'] : $rows['fID_013'];
						$shifts = $rows['shift_recID'] > 0 ? $this->select('shift_masters_dtl',array("*"), " WHERE recID = ".$rows['shift_recID']." ") : '';							
                        $emp    = $empID > 0 ? $this->select('employee',array("*"), " WHERE ID = ".$empID." ") : '';                        
                        $bus    = $rows['fID_014'] > 0 ? $this->select('buses',array("*"), " WHERE ID = ".$rows['fID_014']." ") : '';
                        
						$diffVL = $this->SECOND_MINUTES_SUM_CODE_ALL($rows['dateID'],$rows['companyID'],$rows['fID_1'],$rows['tagCD']);
						
						if($diffVL['diffID'] > 0)
						{
							echo '<tr>'; 
							echo '<td '.$this->FB.' align="center"><b>'.$rows['fID_1'].'</b></td>';
							
							echo '<td '.$this->HB.'><b>'.(strtoupper($emp[0]['fname'].' '.$emp[0]['lname'])).' ('.$emp[0]['code'].')</b></td>';
							echo '<td '.$this->HB.' align="center"><b>'.($rows['fID_014'] <> '' ? $bus[0]['busno'] : $rows['fID_14']).'</b></td>';
							echo '<td '.$this->HB.' align="center">'.($rows['tagCD'] == 'A' ? $shifts[0]['fID_2'] : $shifts[0]['fID_9']).'</td>';
							echo '<td '.$this->HB.' align="center">'.($rows['tagCD'] == 'A' ? $shifts[0]['fID_7'] : $shifts[0]['fID_12']).'</td>';						
							echo '<td '.$this->HB.' align="center">'.($rows['tagCD'] == 'A' ? str_replace('h',':',$shifts[0]['fID_8']) : str_replace('h',':',$shifts[0]['fID_15'])).'</td>';						
							echo '<td '.$this->HB.' align="center">'.$rows['singinID'].'</td>';
							echo '<td '.$this->HB.' align="center">'.($diffVL['diffTX']).'</td>';							
							echo '</tr>';
                        }
                    }
                }
                echo '</table>';
            }
        }
    }
    
    
}
?>