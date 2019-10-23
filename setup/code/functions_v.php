<?PHP
class VFunctions extends DFunctions
{
    public function GET_Weekly_Roset_Sheet_Data()
    {
        $FL_Array = $this->select('wshifts',array("*"), " WHERE ID > 0 Order By ID DESC  ");

        $file = '';

        $file .= 'sdgsdfsfdgfdgfdgfdgfdgfdg';
        $styleID = 'style="color:white;background:#317299;text-align:center;"';

        $dateID_1 = $FL_Array[0]['fdateID'];
        $dateID_2 = $FL_Array[0]['tdateID'];
        $diffID = strtotime($dateID_2) - strtotime($dateID_1);
        $lasID = floor($diffID / (60 * 60 * 24)) + 1;

        $file .= '<br /><br /><table id="dataTables" class="table table-bordered table-striped">';				
        $file .= '<thead><tr>';
        $file .= '<th '.$styleID.'>Sr. No.</th>';
        $file .= '<th '.$styleID.'>Emp - ID</th>';
        $file .= '<th '.$styleID.'>Emp - Name</th>';
        for($srID = 0; $srID < 7; $srID++)
        {
            $dateID = strtotime($dateID_1.'+'.$srID.'Days');
            $file .= '<th '.$styleID.'>'.date('D',$dateID).'<br />'.date('d-M-Y',$dateID).'</th>';
        }		
        $file .= '<th '.$styleID.'>Total Hours</th>';
        $file .= '</tr></thead>';

        $Qry = $this->DB->prepare("SELECT * FROM w_shifts_grader WHERE reqID = ".$FL_Array[0]['ID']." AND sdateID >= '".$FL_Array[0]['fdateID']."' 
		Order By recID ASC ");
        $Qry->execute();
        $this->result = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $counterID = 1;
        if(is_array($this->result) && (count($this->result) > 0))
        {
            $scodeID = '';
            foreach($this->result as $rows)
            {
                $EMPL = $this->select('employee',array("*"), " WHERE ID = ".$rows['empID']." ");

                $SH_1 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_1']." ");
                $SH_2 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_2']." ");
                $SH_3 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_3']." ");
                $SH_4 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_4']." ");
                $SH_5 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_5']." ");
                $SH_6 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_6']." ");
                $SH_7 = $this->select('shifts',array("*"), " WHERE ID = ".$rows['shiftID_7']." ");

                $file .= '<tr>';
                $file .= '<td align="center">'.$counterID++.'</td>';
                $file .= '<td align="center">'.$EMPL[0]['code'].'</td>';
                $file .= '<td>'.$EMPL[0]['fname'].' '.$EMPL[0]['lname'].'</td>';

                $file .= '<td align="center">'.$SH_1[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_2[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_3[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_4[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_5[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_6[0]['code'].'</td>';
                $file .= '<td align="center">'.$SH_7[0]['code'].'</td>';
                $file .= '<td align="center">'.$rows['hoursID'].'</td>';
                $file .= '</tr>';
            }
        }

        $file .= '</table>';

        return $file;
    }
	
    public function frms_QryBuilders($frmID,$passID = 0)
    {
        $inner = "";    $outer = "";    $prmID = "";    $joinID = "";   $parellID = ""; $sub_outer = "";
        if(!empty($frmID))
        {
            $tableName = "";
            $Qry = $this->DB->prepare("SELECT * FROM frm_fields WHERE frmID In(".$frmID.") Order By ID ASC ");
            $Qry->execute();
            $this->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $srID = 1;
            foreach($this->rows as $rows)
            {
                $tableName = $rows['tableFR'];
                
                $inner .= ($srID == 1 ? $tableName.".".$rows['filedNM'] : ', '.$tableName.".".$rows['filedNM']);
				
                if($rows['filedNM'] == 'ftsdate' || $rows['filedNM'] == 'esdate' || $rows['filedNM'] == 'casualid' || $rows['filedNM'] == 'csdate')
                {
                }
                else
                {
                    $outer .= ($srID == 1 ? ($rows['visibleID'] == 1 && $rows['tcaseID'] == 1  ? "(".$tableName.".".$rows['filedNM']." ".$rows['filedCR']." Or ".$tableName.".".$rows['filedNM']." = '')"     :($rows['visibleID'] == 1 && $rows['tcaseID'] == 0 ? $tableName.".".$rows['filedNM']." ".$rows['filedCR'] : '')) 
                                          : ($rows['visibleID'] == 1 && $rows['tcaseID'] == 1  ? " Or (".$tableName.".".$rows['filedNM']." ".$rows['filedCR']." Or ".$tableName.".".$rows['filedNM']." = '')" :($rows['visibleID'] == 1 && $rows['tcaseID'] == 0 ? " Or ".$tableName.".".$rows['filedNM']." ".$rows['filedCR'] : '')));
                }
						
                $prmID .= ($rows['paramID'] == 2 ? " AND ".$rows['filedNM']." ".$rows['filedCR'] : "");                
                $srID++;
            }
        }
		
        if($frmID == 1)
        {
            //AND employee.ddlcdt <= '".date('Y-m-d')."' AND employee.wwcprdt <= '".date('Y-m-d')."'
            $prmID .= " AND employee.status = 1 ".($passID > 0 ? " AND employee.ID = ".$passID : "");
        }
		
        if($frmID == 4)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".driverID ";
            $prmID .= " AND employee.status = 1 ".($passID > 0 ? " AND complaint.ID = ".$passID : "");
        }
		
        if($frmID == 5)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".driverID ";
            $prmID .= " AND employee.status = 1 AND incident_regis.sincID = 2 ".($passID > 0 ? " AND incident_regis.ID = ".$passID : "");
        }
        
        if($frmID == 6)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".staffID ";
            $prmID .= " AND employee.status = 1 ".($passID > 0 ? " AND accident_regis.ID = ".$passID : "");
        }
        
        if($frmID == 7)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".staffID ";
            $prmID .= " AND employee.status = 1 ".($passID > 0 ? " AND infrgs.ID = ".$passID : "");
        }
        
        if($frmID == 8)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".empID ";
            $prmID .= " AND employee.status = 1 ".($passID > 0 ? " AND inspc.ID = ".$passID : "");
        }        
        
        if($frmID == 1)
        {
            $sub_outer .= " Or (If(employee.casualID = 1, (If(employee.ftsdate <> '' AND employee.ftsdate <> '0000-00-00', 0, 1)),
			(If(employee.casualID = 3, (If(employee.csdate <> '' AND employee.csdate <> '0000-00-00', 0, 1)), 0))) = 1)";
        }
        
        if($frmID == 10)
        {
            $joinID = " LEFT JOIN employee ON employee.ID = ".$tableName.".driverID ";
            $prmID .= " AND employee.status = 1 AND incident_regis.sincID = 1 ".($passID > 0 ? " AND incident_regis.ID = ".$passID : "");
        }
        
        return ("SELECT ".$tableName.".ID, ".$inner." FROM ".$tableName." ".$joinID." WHERE ".$tableName.".ID > 0 AND 
		".$tableName.".companyID = ".$_SESSION[$this->website]['compID'].$prmID." AND (".$outer." ".$sub_outer." ) ");
    }
    
    public function MISSINGS_DASHBOARD($frmID)
    {
        $return = '';
        if(!empty($frmID))
        {
            $Qry = $this->DB->prepare("SELECT * FROM frm_fields WHERE frmID = ".$frmID." AND visibleID = 1 Order By ID ASC ");
            $Qry->execute();
            $this->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $countID = count($this->Hrows);

            if($countID > 0)
            {
                $fileID .= '<table id="dataTables" class="table table-bordered table-striped">';				
                $fileID .= '<thead><tr>';
                $fileID .= '<th style="background:#3C8DBC; color:white; text-align:center;">Sr. No.</th>';
				
                $fieldNM = '';  $formNM = '';   $joinTB = '';   $joinFN = '';   $algnFL = '';				
                $headID = 1;
                foreach($this->Hrows as $Hrows)
                {
                    $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
                    $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';
                    $algnFL  .= $Hrows['alignFL'].', ';
                    
                    if($frmID == 1)
                    {
			/*|| $headID == 13 || $headID == 14*/
                        if($headID == 1 || $headID == 2 )
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    else if($frmID == 6)
                    {
                        if($Hrows['filedCP'] == 'Staff Code' || $Hrows['filedCP'] == 'Accident No' || $Hrows['filedCP'] == 'Accident Date' || $Hrows['filedCP'] == 'Staff Name')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    else if($frmID == 4)
                    {
                        if($Hrows['filedCP'] == 'Comment Line Ref No' || $Hrows['filedCP'] == 'Driver Code' || $Hrows['filedCP'] == 'Driver Name')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    else if($frmID == 5)
                    {
                        if($Hrows['filedCP'] == 'Driver Name' || $Hrows['filedCP'] == 'Driver Code' || $Hrows['filedCP'] == 'Ref No')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    else if($frmID == 7)
                    {
                        if($Hrows['filedCP'] == 'Infringement No' || $Hrows['filedCP'] == 'Employee Code' || $Hrows['filedCP'] == 'Employee Name')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    else if($frmID == 8)
                    {
                        if($Hrows['filedCP'] == 'Report No' || $Hrows['filedCP'] == 'Driver ID' || $Hrows['filedCP'] == 'Driver Name')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                     else if($frmID == 10)
                    {
                        if($Hrows['filedCP'] == 'CRM No' || $Hrows['filedCP'] == 'Report Date' || $Hrows['filedCP'] == 'Incident Ref No')
                        {
                            $fileID .= '<th style="background:#3C8DBC; text-align:center; color:white;">'.$Hrows['filedCP'].'</th>';
                        }
                    }
                    
                    $formNM = $Hrows['formNM'];
                    $headID++;
                }

                //  if($frmID == 1) {$fileID .= '<th style="background:#3C8DBC; color:white;">Missing Fields - Name</th>';}                
                //  if($frmID == 6) {$fileID .= '<th style="background:#3C8DBC; color:white;">&nbsp</th>';}
                
                /*$fileID .= '<th style="background:#3C8DBC; color:white;">&nbsp;</th>';*/
                $fileID .= '</tr></thead>';

                $flID = explode(",",$fieldNM);      $ftID = explode(",",$fieldTY);
                $jtID = explode(",",$joinTB);       $jfID = explode(",",$joinFN);
                $agID = explode(",",$algnFL);
				
                //echo '<br /> : '.$this->frms_QryBuilders($frmID);
                
                /*  echo '<br /> : '.$this->frms_QryBuilders($frmID); */				
                $Qry_D = $this->DB->prepare($this->frms_QryBuilders($frmID));
                $Qry_D->execute();
                $this->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);

                if(is_array($this->Drows) && count($this->Drows) > 0)
                {
                    $Start = 1;     $urlID = '';
                    foreach($this->Drows as $Drow)
                    {
                        $urlID = $this->home.'forms/'.$formNM.'?a='.$this->Encrypt('create').'&i='.$this->Encrypt($Drow['ID']);

                        $fileID .= '<tr>';
                        $fileID .= '<td align="center" style="background:#3C8DBC; color:white;"><b>'.$Start++.'</b></td>';
                        $fnameID = '';  $tableJT = '';  $testID = '';   $alignID = '';
                        
                        for($srID = 1; $srID <= $countID; $srID++)
                        {
                            $alignID = (trim($agID[$srID - 1]) == 1 ? 'align="left"' :(trim($agID[$srID - 1]) == 2 ? 'align="right"' :(trim($agID[$srID - 1]) == 3 ? 'align="center"' : '')));
                            
                            $alignID .= 'style="font-weight:bold; background:white; text-align: center; "';
							
                            if($frmID == 1)
                            {
                                /* || $srID == 13 || $srID == 14*/
                                if($srID == 1 || $srID == 2)
                                {
                                    if(trim($ftID[$srID - 1]) == 2) 
                                    {
                                        $tableJT = trim($jtID[$srID - 1]);
                                        $tableJF = trim($jfID[$srID - 1]);

                                        if($tableJT <> '')
                                        {
                                            $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                        }
                                        
                                        $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                    }

                                    else if(trim($ftID[$srID - 1]) == 4) 
                                    {
                                        $fnameID = trim($flID[$srID - 1]);
                                        $fileID .= '<td '.$alignID.'>'.$this->VISUAL_dateID(($Drow[trim($flID[$srID - 1])])).'</td>';
                                    }

                                    else if(trim($ftID[$srID - 1]) == 5) 
                                    {
                                        $fileID .= '<td '.$alignID.'>'.(strlen($Drow[trim($flID[$srID - 1])]) > 0 ? substr($Drow[trim($flID[$srID - 1])],0,30).'....' : '').'</td>';
                                    }

                                    else                                
                                    {
                                        $fnameID = trim($flID[$srID - 1]);

                                        if(trim($ftID[$srID - 1]) == 1)
                                        {
                                            if($fnameID == 'code')
                                            {
                                                $fileID .= '<td '.$alignID.'><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="EMPLOYEE" aria-title="Employee" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                            }
                                            else if($fnameID == 'casualID')
                                            {
                                                $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID] == 1 ? 'Full Time' :($Drow[$fnameID] == 2 ? 'Part Time' :($Drow[$fnameID] == 3 ? 'Casual' : ''))).'</td>';
                                            }
                                            else
                                            {
                                                $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';                                                    
                                            }
                                        } 
                                        else
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';	
                                        }
                                    }
                                }
                            }
                            
                            else if($frmID == 6)
                            {
                                if(trim($ftID[$srID - 1]) == 2 && (trim($flID[$srID - 1]) == 'staffID')) 
                                {
                                    $tableJT = trim($jtID[$srID - 1]);
                                    $tableJF = trim($jfID[$srID - 1]);

                                    if($tableJT <> '')
                                    {
                                        $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                    }

                                    $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                }
                                else if(trim($ftID[$srID - 1]) == 4) 
                                {
                                    $fnameID = trim($flID[$srID - 1]);
                                    
                                    if(trim($flID[$srID - 1]) == 'dateID')
                                    {
                                        $fileID .= '<td '.$alignID.'>'.$this->VISUAL_dateID(($Drow[trim($flID[$srID - 1])])).'</td>';
                                    }
                                }
                                
                                else                                
                                {
                                    $fnameID = trim($flID[$srID - 1]);

                                    if(trim($ftID[$srID - 1]) == 1)
                                    {
                                        if($fnameID == 'scodeID')
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                        }
                                        else if($fnameID == 'refno')
                                        {
                                            $fileID .= '<td '.$alignID.'><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="ACCIDENT" aria-title="Accident" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                        }
                                    }  
                                }
                            }
                            
                            else if($frmID == 4)
                            {
                                if(trim($ftID[$srID - 1]) == 2 && (trim($flID[$srID - 1]) == 'driverID')) 
                                {
                                    $tableJT = trim($jtID[$srID - 1]);
                                    $tableJF = trim($jfID[$srID - 1]);

                                    if($tableJT <> '')
                                    {
                                        $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                    }
                                    
                                    $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                }                                
                                else                                
                                {
                                    $fnameID = trim($flID[$srID - 1]);

                                    if(trim($ftID[$srID - 1]) == 1)
                                    {
                                        if($fnameID == 'dcodeID')
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                        }
                                        else if($fnameID == 'refno')
                                        {
                                            $fileID .= '<td align="center" style="font-weight:bold; background:white;"><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="COMMENT-LINE" aria-title="CommentLine" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                        }
                                    }  
                                }
                            }
                            
                            else if($frmID == 5)
                            {
                                if(trim($ftID[$srID - 1]) == 2 && (trim($flID[$srID - 1]) == 'driverID')) 
                                {
                                    $tableJT = trim($jtID[$srID - 1]);
                                    $tableJF = trim($jfID[$srID - 1]);

                                    if($tableJT <> '')
                                    {
                                        $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                    }
                                    
                                    $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                }                                
                                else                                
                                {
                                    $fnameID = trim($flID[$srID - 1]);

                                    if(trim($ftID[$srID - 1]) == 1)
                                    {
                                        if($fnameID == 'dcodeID')
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                        }
                                        else if($fnameID == 'refno')
                                        {
                                            $fileID .= '<td align="center" style="font-weight:bold; background:white;"><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="INCIDENT_GENERAL" aria-title="Incident" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                        }
                                    }  
                                }
                            }
                            
                            else if($frmID == 7)
                            {
                                if(trim($ftID[$srID - 1]) == 2 && (trim($flID[$srID - 1]) == 'staffID')) 
                                {
                                    $tableJT = trim($jtID[$srID - 1]);
                                    $tableJF = trim($jfID[$srID - 1]);

                                    if($tableJT <> '')
                                    {
                                        $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                    }

                                    $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                }                                
                                else                                
                                {
                                    $fnameID = trim($flID[$srID - 1]);

                                    if(trim($ftID[$srID - 1]) == 1)
                                    {
                                        if($fnameID == 'stcodeID')
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                        }
                                        else if($fnameID == 'refno')
                                        {
                                            $fileID .= '<td align="center" style="font-weight:bold; background:white;"><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="INFRINGMENT" aria-title="Infringment" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                        }
                                    }  
                                }
                            }
                            
                            else if($frmID == 8)
                            {
                                if(trim($ftID[$srID - 1]) == 2 && (trim($flID[$srID - 1]) == 'empID')) 
                                {
                                    $tableJT = trim($jtID[$srID - 1]);
                                    $tableJF = trim($jfID[$srID - 1]);

                                    if($tableJT <> '')
                                    {
                                        $MS_Array = $Drow[trim($flID[$srID - 1])] > 0 ? $this->select($tableJT,array("*"), " WHERE ID = ".$Drow[trim($flID[$srID - 1])]." ") : '';
                                    }

                                    $fileID .= '<td '.$alignID.'>'.($MS_Array[0][$tableJF]).'</td>';
                                }                                
                                else                                
                                {
                                    $fnameID = trim($flID[$srID - 1]);

                                    if(trim($ftID[$srID - 1]) == 1)
                                    {
                                        if($fnameID == 'ecodeID')
                                        {
                                            $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                        }
                                        else if($fnameID == 'rptno')
                                        {
                                            $fileID .= '<td align="center" style="font-weight:bold; background:white;"><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="INSPECTION" aria-title="Inspection" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                        }
                                    }  
                                }
                            }  
                            
                            else if($frmID == 10)
                            { 
                                $fnameID = trim($flID[$srID - 1]);
 
                                if($fnameID == 'rpdateID')
                                {
                                    $fileID .= '<td '.$alignID.'>'.($this->VdateFormat($Drow[$fnameID])).'</td>';
                                }
                                else if($fnameID == 'cmrno')
                                {
                                    $fileID .= '<td '.$alignID.'>'.($Drow[$fnameID]).'</td>';
                                } 
                                else if($fnameID == 'refno')
                                {
                                    $fileID .= '<td align="center" style="font-weight:bold; background:white;"><a style="cursor:pointer; text-decoration:none;" class="autditTRIAL" aria-busy="INCIDENT" aria-title="Incident" aria-sort="'.$Drow['ID'].'">'.($Drow[$fnameID]).'</a></td>';
                                } 
                            }  
                        }
                        
                        //  if($frmID == 6) {$fileID .= '<td align="center" style="background:white;"><a target="blank" href="'.$urlID.'" class="fa fa-plane" style="text-declaration:none;cursor:pointer;"></a></td>';}
                        
                        $fileID .= '</tr>';
                    } 
                }
                $fileID .= '</table>'; 
            }
        }
        
        $return['fileID'] = $fileID;
        $return['counID'] = count($this->Drows);
        return $return;
    }
    
    public function UpdateCodes()
    {
        /* employee - conditions */
        $Qry = $this->DB->prepare("UPDATE employee SET employee.phone = '-' WHERE employee.phone Is Null AND (employee.phone_1 <> '' or employee.phone <> null)");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE employee SET employee.phone_1 = '-' WHERE employee.phone <> '' AND employee.phone <> Null ");
        $Qry->execute();

        /*$Qry = $this->DB->prepare("UPDATE employee SET ddlcno = 'N-A' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (ddlcno IS NULL Or ddlcno = '') ");
        $Qry->execute();*/
      
        $Qry = $this->DB->prepare("UPDATE employee SET rfID = 'N-A' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (rfID IS NULL Or rfID = '') ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE employee SET wwcprno = 'N-A' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (wwcprno IS NULL Or wwcprno = '') ");
        $Qry->execute();
        
        /*$Qry = $this->DB->prepare("UPDATE employee SET drvrightID = '' WHERE desigID In(9,208,209) AND drvrightID = '0' ");
        $Qry->execute();*/
        
        $Qry = $this->DB->prepare("UPDATE employee SET wwcprdt = '0000-00-00' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (wwcprdt IS NULL Or wwcprdt = '') ");
        $Qry->execute();

	$Qry = $this->DB->prepare("UPDATE employee SET lardt = '0000-00-00' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (lardt IS NULL Or lardt = '') ");
        $Qry->execute();
		
        $Qry = $this->DB->prepare("UPDATE employee SET ftextID = 'N-A' WHERE (desigID <> 9 AND desigID <> 208 AND desigID <> 209) AND (ftextID IS NULL Or ftextID = '') ");
        $Qry->execute();
        
        /* inspections - conditions */
        $Qry = $this->DB->prepare("UPDATE inspc SET fineID = 0 WHERE (insrypeID <> 261 AND insrypeID <> 300 AND insrypeID <> 301 AND insrypeID <> 271 AND insrypeID <> 268)");
        $Qry->execute();
        
        /* comments - line - conditions */
        $Qry = $this->DB->prepare("UPDATE complaint SET driverID = 0, dcodeID = 'N-A' WHERE tickID_1 = 1 ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE complaint SET respID = 0, outcome = 'N-A' WHERE (accID = 224 AND accID = 48 AND accID = 220 AND accID = 54 AND accID = 50 AND accID = 51)");
        $Qry->execute();
        
        /* accidents - line - conditions */
        $Qry = $this->DB->prepare("UPDATE accident_regis SET staffID = 0, scodeID = 'N-A' WHERE tickID_1 = 1 ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE accident_regis SET thpnameID = 'N-A', regisnoID = 'N-A', thcontactID = 'N-A' WHERE 3partyID <> 1 ");
        $Qry->execute();
		
        /* Infringement Details */
        $Qry = $this->DB->prepare("UPDATE infrgs SET vcodeID = Concat('IF-',ID) WHERE companyID = ".$_SESSION[$this->website]['compID']." AND vcodeID IS NULL  ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE infrgs SET description = '.' WHERE companyID = ".$_SESSION[$this->website]['compID']." AND inftypeID <> 162  ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE infrgs SET dplostID = '0' WHERE companyID = ".$_SESSION[$this->website]['compID']." AND dplostID IS NULL  ");
        $Qry->execute();
        
        $Qry = $this->DB->prepare("UPDATE infrgs SET dplostID = '0' WHERE companyID = ".$_SESSION[$this->website]['compID']." AND dplostID < 1  ");
        $Qry->execute();
		
    }
    
    public function VISUAL_dateID($dateString)
    {
        if(!empty($dateString) && ($dateString <> '') && ($dateString <> '01-01-1970') && ($dateString <> '1970-01-01') && ($dateString <> '0000-00-00'))
            {$return = date('d/m/Y',strtotime($dateString));}
        else
            {$return = '';}		
        return $return;		
    }    
}
?>