<?PHP
    include_once '../includes.php';
        
    $reqID     =    isset($_POST['ID'])         ?   $_POST['ID']        : '' ;
    $request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;
    
    
    if($request == 'FILTER_ALL_BUSES' && $reqID <> '')
    {
        $Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
        
        $countID = 0;
        $Qry = $login->DB->prepare("SELECT * FROM buses WHERE ID > 0 ".($reqID <> '' ? " AND busno LIKE '%".$reqID."%' " : "")." Order By busno ASC ");
        if($Qry->execute())
        {
            $login->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($login->crow) && count($login->crow) > 0)
            {
                //$spare_arrayID = $arrayID[0]['dateID'] <> '' ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$arrayID[0]['dateID']."' ")     : '';
                
                $file .= '<table id="dataTable" class="table table-bordered ">';				
                $file .= '<thead><tr><th colspan="6" '.$Set.'>Replace Bus</th></tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th width="150" '.$Set.'>Replace</th>';
                $file .= '<th '.$Set.'>Bus No</th>';
                $file .= '<th '.$Set.'>License NO</th>';
                $file .= '<th '.$Set.'>N.O.S</th>';
                $file .= '<th '.$Set.'>Model</th>';
                $file .= '<th '.$Set.'>Manufacturer</th>';
                $file .= '</tr></thead>';                
                foreach($login->crow as $rows)
                {
                    $countID += 1;
                    
                    //$spareDT  = $spare_arrayID[0]['ID'] > 0 ? $login->select('spare_regis_dtl',array("*"), " WHERE ID = ".$spare_arrayID[0]['ID']." AND fieldID_1 = ".$rows['ID']." AND forID = 2 ") : '';
                    
                    //$reqID.'_'.$arrayID[0]['dateID'].'_'.$mrow['ID'].'_'.$tag_statusID.'_'.$arrayID[0]['fID_1'].'_'.$arrayID[0]['companyID']
                    
                    $file .= '<tr>'; 
                    $file .= '<td align="center">';
                    $file .= '<input type="checkbox" aria-busy="NEW_BUSES" aria-sort="'.$_POST['spareID'].'_'.$_POST['dateID'].'_'.$rows['ID'].'_'.$_POST['tag_statusID'].'_'.$_POST['shiftNO'].'_'.$_POST['companyID'].'" class="selection-temp-modal" />';
                    $file .= '</td>';

                    $file .= '<td align="center">'.$rows['busno'].'</td>';
                    $file .= '<td align="center">'.$rows['lcsno'].'</td>';
                    $file .= '<td align="center">'.$rows['nos'].'</td>';
                    $file .= '<td align="center">'.$rows['modelno'].'</td>';
                    $file .= '<td align="center">'.$rows['title'].'</td>';
                    $file .= '</tr>';
                }
                $file .= '</table>';
            }
        }
        
        $arr = array('countID'=>$countID,'filterDATA'=>$file);
		
        echo json_encode($arr);
    }
    
    if($request == 'FILTER_ALL_EMPLOYEES' && $reqID <> '')
    {
        extract($_POST);
        
        $Set = 'class="knob-labels notices" style="font-weight:600; font-size:14px;"';
        
        $crtID = "";
        $crtID = $reqID <> '' ? " AND (code LIKE '%".$reqID."%' Or full_name LIKE '%".$reqID."%') " : "";
        
        $countID = 0;
        /* AND companyID In(".($_POST['companyID']).") */
        $Qry = $login->DB->prepare("SELECT * FROM employee WHERE ID > 0 AND status = 1 AND desigID in(9,209,208)  ".$crtID." Order By fname,lname ASC ");
        if($Qry->execute())
        {
            $login->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($login->crow) && count($login->crow) > 0)
            {
                $arrSP = ($dateID <> '' && $companyID > 0 ? $login->select('spare_regis',array("*"), " WHERE dateID = '".$dateID."' AND companyID = ".$companyID." ") : '');
                
                $file .= '<table id="dataTable" class="table table-bordered ">';				
                $file .= '<thead><tr><th colspan="6" '.$Set.'>Replace Drivers</th></tr></thead>';

                $file .= '<thead><tr>';
                $file .= '<th width="150" '.$Set.'>Replace</th>';
                $file .= '<th width="120" '.$Set.'>Driver Code</th>';
                $file .= '<th '.$Set.'>Driver Name</th>';
                $file .= '<th '.$Set.'>Available</th>';
                $file .= '<th '.$Set.'>Time</th>';
                $file .= '</tr></thead>';         


				/* START - CHOPPED - CASE */
					$file .= '<tr>'; 
						$file .= '<td align="center"><input type="checkbox" aria-sort="'.$_POST['spareID'].'" class="selection-chopped-modal" /></td>';
						$file .= '<td align="center">0000</td>';
						$file .= '<td>Chopped</td>';
						$file .= '<td></td>';
						$file .= '<td></td>';
					$file .= '</tr>';
				/* END - CHOPPED - CASE */
				
                foreach($login->crow as $rows)
                {
                   /* $arrSPD = $arrSP[0]['ID'] > 0 ? $login->select('spare_regis_dtl',array("*"), " WHERE ID = ".$arrSP[0]['ID']." AND fieldID_1 = ".$rows['ID']." ")     : '';
                    
                    if($arrSPD[0]['recID'] > 0)  { }   else
                    {*/
                        $countID += 1;

                        $file .= '<tr>'; 
                        $file .= '<td align="center">';
                            $file .= '<input type="checkbox" aria-busy="NEW_EMPLOYEES" aria-sort="'.$_POST['spareID'].'_'.$_POST['dateID'].'_'.$rows['ID'].'_'.$_POST['tag_statusID'].'_'.$_POST['shiftNO'].'_'.$_POST['companyID'].'" class="selection-temp-modal" />';
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
                    /*}*/
                }
                $file .= '</table>';
            }
        }
        
        $arr = array('countID'=>$countID,'filterDATA'=>$file);
		
        echo json_encode($arr);
    }
        
?>