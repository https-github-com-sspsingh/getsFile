<?PHP
    include_once '../includes.php';
    
    if($_POST['request'] == 'CHECK_DRAG_DROP_AB_STATUS')
    {
        extract($_POST);
        
        $loopLENGTH = 0;
        $loopLENGTH = count($partLISTS);
        $loopLENGTH = $loopLENGTH > 0 ? (int)$loopLENGTH : 0;
        
        $countID = 0;
        if(is_array($partLISTS) && count($partLISTS) > 0)
        {
            $rowID = '';    $rowTX = '';    $newCD = '';
            $return = array();
            for($srID = 0; $srID <= $loopLENGTH; $srID++)
            {
                $rowID = $partLISTS[$srID]['rowID'];
                $rowTX = $partLISTS[$srID]['rowTX'];
                $newCD = $partLISTS[$srID]['newCD'];
                
                //echo '<br /> : '.$rowID.' - '.$rowTX.' - '.$newCD;
                
                /* CURRENT ROW DATA */
                $curr_row = explode("_",$rowID);
                $curr_recID = $curr_row[0];
                $curr_empID = $curr_row[1];
                $curr_speID = $curr_row[2];
                        
                /* GET NEW ROW INFORMATIONS */
                $arrEM = (trim($newCD) <> '' ? $login->select('employee',array("*"), " WHERE ID > 0 AND code = '".trim($newCD)."' ") : '');
                if(($curr_recID > 0 && ($curr_empID > 0 || $curr_speID > 0)) && ($arrEM[0]['ID'] > 0))
                {
                    /* START - SHIFT - TAGS - DATA */
                    $tagCD = ($curr_recID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID > 0 AND recID = ".$curr_recID." ") : '');
                    $arrB = ($tagCD[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$tagCD[0]['fID_1']."' AND companyID = ".$tagCD[0]['companyID']." AND dateID = '".$tagCD[0]['dateID']."' AND tagCD = 'B' AND cuttoffID <> 1 ") : '');
                    /* ENDSS - SHIFT - TAGS - DATA */

                    $arrSH = ($curr_recID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID > 0 AND recID = ".$curr_recID." AND (fID_13 = '".$newCD."' Or fID_18 = '".$newCD."') ") : '');
                    
                    if($arrSH[0]['recID'] > 0)  {/*$return[$srID]['statusID'] = 0;*/}
                    else
                    {
                        if($arrB[0]['recID'] > 0)
                        {
                            if(strtoupper($tagCD[0]['tagCD']) == 'A')
                            {
                                $return[] = array('statusID'=>1,'tagCD'=>strtoupper($tagCD[0]['tagCD']),'shiftNOS'=>$tagCD[0]['fID_1'],'rowID'=>$rowID,'rowTX'=>$rowTX,'newCD'=>$newCD);
                                $countID++;
                            }
                            else if(strtoupper($tagCD[0]['tagCD']) == 'B')  
                            {
                                $return[] = array('statusID'=>2,'tagCD'=>strtoupper($tagCD[0]['tagCD']),'shiftNOS'=>$tagCD[0]['fID_1'],'rowID'=>$rowID,'rowTX'=>$rowTX,'newCD'=>$newCD);
                                $countID++;
                            }  
                            else    
                            {
                                /*$return[$srID]['statusID'] = 0;*/
                            }
                        }
                        else if($arrEM[0]['ID'] > 0 && $curr_recID > 0) 
                        {
                            $return[] = array('statusID'=>2,'tagCD'=>strtoupper($tagCD[0]['tagCD']),'shiftNOS'=>$tagCD[0]['fID_1'],'rowID'=>$rowID,'rowTX'=>$rowTX,'newCD'=>$newCD);
                            $countID++;
                        }
                    }                                
                }
                
                $data['countID'] = $countID;
                $data['DragDropLISTS'] = $return;
            }
        }
        
        //  echo '<pre>'; echo print_r($return);
    }
    
    if($_POST['request'] == 'UPDATE_DRAG_DROP_AB_STATUS')
    {
        extract($_POST);
        
        $rowID = trim($rowID);
        $rowTX = trim($rowTX);
        
        if(($rowID <> '') && ($rowTX <> ''))
        {
            /* CURRENT ROW DATA */
            $curr_row = explode("_",$rowID);
            $curr_recID = $curr_row[0];
            $curr_empID = $curr_row[1];
            $curr_speID = $curr_row[2];

            /* PREVIOUS ROW DATA */
            $new_codeID = '';
            for($srID = 0; $srID <= 4;  $srID++)
            {
                if($rowTX[$srID] <> '' && is_numeric($rowTX[$srID]))    {$new_codeID .= $rowTX[$srID];} else    {break;}
            }

            /* GET NEW ROW INFORMATIONS */
            $arrEM = (trim($new_codeID) <> '' ? $login->select('employee',array("*"), " WHERE ID > 0 AND code = '".trim($new_codeID)."' ") : '');

            if(($curr_recID > 0 && ($curr_empID > 0 || $curr_speID > 0)) && ($arrEM[0]['ID'] > 0))
            {
                /* START - SHIFT - TAGS - DATA */
                $tagCD = ($curr_recID > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE recID > 0 AND recID = ".$curr_recID." ") : '');
                $arrB = ($tagCD[0]['fID_1'] > 0 ? $login->select('imp_shift_daily',array("*"), " WHERE fID_1 = '".$tagCD[0]['fID_1']."' AND companyID = ".$tagCD[0]['companyID']." AND dateID = '".$tagCD[0]['dateID']."' AND tagCD = 'B' AND cuttoffID <> 1 ") : '');
                /* ENDSS - SHIFT - TAGS - DATA */
                
                /* SET UPDATE ROW - 1 */
                $fields = array();
                $fields['fID_13'] = $arrEM[0]['code'];
                $fields['fID_013'] = $arrEM[0]['ID'];				
                $fields['fID_18']  = '';
                $fields['fID_018'] = 0;
                $on['recID'] = $curr_recID;
                /*echo '<pre>'; echo print_r($fields);*/
                /*echo '<pre>'; echo print_r($arrB[0]['recID']); exit;*/
                if($login->BuildAndRunUpdateQuery('imp_shift_daily',$fields,$on))
                {
                    /* UPDATE B - PART */
                    if($statusID == 2 && $arrB[0]['recID'] > 0)
                    {
                        $fields_B = array();
                        $fields_B['fID_13'] = $arrEM[0]['code'];
                        $fields_B['fID_013'] = $arrEM[0]['ID'];				
                        $fields_B['fID_18']  = '';
                        $fields_B['fID_018'] = 0;
                        $on_B['recID'] = $arrB[0]['recID'];
                        $login->BuildAndRunUpdateQuery('imp_shift_daily',$fields_B,$on_B);
                    }
                    
                    $data['statusID'] = 1;
                }
                else    {$data['statusID'] = 0;}  
            }					
        }
    }

    echo json_encode($data);
?>