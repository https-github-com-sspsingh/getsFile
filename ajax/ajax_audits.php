<?PHP
    include_once '../includes.php';
    
    $reqID     =    isset($_POST['ID'])         ?   $_POST['ID']        :   '' ;
    $request   =    isset($_POST['request'])    ?   $_POST['request']   :   '' ;
    
	if($request == 'viewEmployeeInfos' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
        
        $arrDB  = ($reqID > 0  			 ? $login->select('employee',array("*"), " WHERE ID = ".$reqID." ")     : '');
		$arrSB  = ($arrDB[0]['sid'] > 0  ? $login->select('suburbs',array("title"), " WHERE ID = ".$arrDB[0]['sid']." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
 
        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-12"><label><b style="color:red;">Employee Code : </b>'.$arrDB[0]['code'].'</label></div><br /><br />';
        $file .= '<div class="col-md-12"><label><b style="color:red;">Employee Name : </b>'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'</label></div><br /><br />';
		$file .= '<div class="col-md-12"><label><b style="color:red;">Date of Birth : </b>'.$login->VdateFormat($arrDB[0]['dob']).'</label></div><br /><br />';
		$file .= '<div class="col-md-12"><label><b style="color:red;">Address : </b>'.$arrDB[0]['address_1'].' '.$arrDB[0]['address_2'].'</label></div><br /><br />';
		$file .= '<div class="col-md-12"><label><b style="color:red;">Suburb : </b>'.$arrSB[0]['title'].' ('.$arrDB[0]['pincode'].')</label></div><br /><br />';		
		$file .= '<div class="col-md-12"><label><b style="color:red;">Phone No : </b>'.$arrDB[0]['phone'].' , '.$arrDB[0]['phone_1'].'</label></div><br /><br />';				
		$file .= '<div class="col-md-12"><label><b style="color:red;">Driver License No : </b>'.$arrDB[0]['ddlcno'].'</label></div><br /><br />';		
          			
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
		
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }



	if($request == 'updateWWCAppliedDate' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
        
        $arrDB  = ($reqID > 0  ? $login->select('employee',array("*"), " WHERE ID = ".$reqID." ")     : ''); 

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_ACC_APPLIED_DATE" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="employee" />';
        $file .= '<input type="hidden" name="frmID" value="1" />';
        
        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-12"><label><b>Employee Code : </b>'.$arrDB[0]['code'].'</label></div><br /><br />';
        $file .= '<div class="col-md-12"><label><b>Employee Name : </b>'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'</label></div><br /><br />';
		$file .= '<div class="col-md-12"><label><b>WWC Permit No : </b>'.$arrDB[0]['wwcprno'].'</label></div><br /><br />';
		$file .= '<div class="col-md-12"><label><b>WWC Expry Date : </b>'.$arrDB[0]['wwcprdt'].'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';
 
		 
			$file .= '<div class="col-md-6" style="margin-bottom:7px;">';
			$file .= '<label>WWC Permit No - Applied Date</label>';
			
			$file .= '<div class="input-group">';
			$file .= '<div class="input-group-addon"><i class="fa fa-calendar"></i></div>';						
			$file .= '<input id="input4" type="datable" class="form-control" name="wwcapDT" required="required" placeholder="Enter Date" data-datable="ddmmyyyy" />';

			$file .= '</div>';
			$file .= '</div>';
			$file .= '</div>';
			
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

		$file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Applied Date</button></div>';
			 
        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }


    if($request == 'AUDIT_EMPLOYEE' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
        
        $arrDB  = ($reqID > 0  ? $login->select('employee',array("*"), " WHERE ID = ".$reqID." ")     : '');
        $desigID = $arrDB[0]['desigID'];
        $casualID = $arrDB[0]['casualID'];

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="employee" />';
        $file .= '<input type="hidden" name="frmID" value="1" />';
        
        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-12"><label><b>Employee Code : </b>'.$arrDB[0]['code'].'</label></div><br /><br />';
        $file .= '<div class="col-md-12"><label><b>Employee Name : </b>'.$arrDB[0]['fname'].' '.$arrDB[0]['lname'].'</label></div>';        
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 1 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';
            $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(1,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                if(($fnameID == 'drvrightID' || $fnameID == 'rfID' || $fnameID == 'wwcprno'))
                                {
                                    if($desigID == 209 || $desigID == 9 || $desigID == 10 || $desigID == 208)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                        $file .= '</div>';
                                        
                                        $returnID += 1;
                                    }
                                }
                                else if(($fnameID == 'ftextID'))
                                {
                                    if($desigID == 209 || $desigID == 9 || $desigID == 10 || $desigID == 208)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<select class="form-control" name="'.$fnameID.'">';
                                            $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';

                                            $file .= '<option value="F">F</option>';
                                            $file .= '<option value="T">T</option>';
                                            $file .= '<option value="N">N</option>';                                            
                                            $file .= '</select>';
                                        $file .= '</div>';
                                        
                                        $returnID += 1;
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                    $file .= '</div>';
                                    
                                    $returnID += 1;
                                }
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'genderID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 6 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                    
                                    $returnID += 1;
                                }                                
                                else if($fnameID == 'casualid')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">Full Time</option>';
                                        $file .= '<option value="2">Part Time</option>';
                                        $file .= '<option value="3">Casual</option>';										
                                        $file .= '</select>';
                                    $file .= '</div>';
                                    
                                    $returnID += 1;
                                }
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 4) 	/* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                if($fnameID == 'wwcprdt')
                                {
                                    if($desigID == 209 || $desigID == 9 || $desigID == 208)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1;
                                    }
                                }								
                                else if(($fnameID == 'lardt'))
                                {
                                    if($desigID == 209 || $desigID == 9 || $desigID == 10)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1;                                                 
                                    }
                                }
                                else if(($fnameID == 'ftsdate') && ($casualID == 1))
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1;
                                }
                                else if(($fnameID == 'csdate') && ($casualID == 3))
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" 
                                            placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1;
                                }
                                else
                                {
                                    if($fnameID == 'csdate')
                                    {
                                        /* FULL TIME START DATE */
                                    }
                                    else if($fnameID == 'ftsdate')
                                    {
                                        /* FULL TIME START DATE */
                                    }
                                    else
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';	
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1;
                                    }
                                }
                            }
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Employee</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }

    if($request == 'AUDIT_ACCIDENT' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        $arrDB  = ($reqID > 0 	 ? $login->select('accident_regis',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="accident_regis" />';
        $file .= '<input type="hidden" name="frmID" value="6" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Accident No : </b>'.$arrDB[0]['refno'].'</label></div>';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Accident Date : </b>'.$login->VdateFormat($arrDB[0]['dateID']).'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 6 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(6,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'accID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 20 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'wrtypeID' && ($FIndex->GET_SinglePermission('2') == 1))
                                {
                                    if($arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<select class="form-control" name="'.$fnameID.'">';
                                            $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                            $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 23 Order By title ASC ");
                                            $Qry->execute();
                                            $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($login->rows as $row)
                                            {
                                                $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                            }
                                            $file .= '</select>';
                                        $file .= '</div>';
                                    }
                                }
                                else if($fnameID == 'staffID' || $fnameID == 'invID')
                                {

                                }
                                else if($fnameID == 'optID_2' || $fnameID == 'optID_3')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">No</option>';
                                        $file .= '<option value="2">Swan</option>';
                                        $file .= '<option value="3">Police</option>';
                                        $file .= '<option value="4">Both</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'optID_1')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">Yes</option>';
                                        $file .= '<option value="2">No</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }


                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1 && $arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1; 
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                            $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                            $file .= '</div>';

                            
                            
                            $returnID += 1;
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Accident Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
    
    if($request == 'AUDIT_INFRINGMENT' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        $arrDB  = ($reqID > 0 ? $login->select('infrgs',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="infrgs" />';
        $file .= '<input type="hidden" name="frmID" value="7" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Infringement No : </b>'.$arrDB[0]['refno'].'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 7 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(7,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'inftypeID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 20 Order By title ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'wrtypeID' && ($FIndex->GET_SinglePermission('2') == 1))
                                {
                                    if($arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<select class="form-control" name="'.$fnameID.'">';
                                            $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                            $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 23 Order By title ASC ");
                                            $Qry->execute();
                                            $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($login->rows as $row)
                                            {
                                                $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                            }
                                            $file .= '</select>';
                                        $file .= '</div>';
                                    }
                                }
                                
                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1 && $arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1; 
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                            $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                            $file .= '</div>';
                            
                            
                            
                            $returnID += 1;
                        }
                    } 
                }
            }
        }
        
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Infringement Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
    
    if($request == 'AUDIT_INSPECTION' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        $arrDB  = ($reqID > 0 ? $login->select('inspc',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="inspc" />';
        $file .= '<input type="hidden" name="frmID" value="8" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Report No : </b>'.$arrDB[0]['rptno'].'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 8 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(8,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'fineID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 61 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'insrypeID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 27 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'inspectedby')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 66 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'wrtypeID' && ($FIndex->GET_SinglePermission('2') == 1))
                                {
                                    if($arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<select class="form-control" name="'.$fnameID.'">';
                                            $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                            $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 23 Order By title ASC ");
                                            $Qry->execute();
                                            $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($login->rows as $row)
                                            {
                                                $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                            }
                                            $file .= '</select>';
                                        $file .= '</div>';
                                    }
                                }
                                else if($fnameID == 'disciplineID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                            $file .= '<option value="1">Yes</option>';
                                            $file .= '<option value="2">No</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1 && $arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1; 
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                            $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                            $file .= '</div>';

                            
                            
                            $returnID += 1;
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Inspection Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
    
    if($request == 'AUDIT_COMMENT-LINE' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        $arrDB  = ($reqID > 0 ? $login->select('complaint',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="complaint" />';
        $file .= '<input type="hidden" name="frmID" value="4" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Ref No : </b>'.$arrDB[0]['refno'].'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 4 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(4,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        //$file .= '<br /> typeID : '.trim($ftID[$srID - 1]).' - '.$fnameID;
                        
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'creasonID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 15 Order By title ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'accID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 17 Order By title ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'respid')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 14 Order By title ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }

                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 3)    /* CHECK - BOX - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <= 0)
                            {
                                if($fnameID == 'tickID_1')
                                {
                                    if($arrDB[0]['driverID'] <> '')
                                    {
                                        $file .= '<div class="col-md-6">';
                                        $file .= '<label style="color:red;">'.$arrayFT[0]['filedCP'].'</label><br />';
                                        $file .= '<input class="icheckbox_minimal checked" type="checkbox" name="'.$fnameID.'" value="1" />';
                                        $file .= '</div>';

                                        $returnID += 1; 
                                    }
                                }
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                                if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXTAREA - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1 && $arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1; 
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1;
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Comment Line Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
    
    if($request == 'AUDIT_INCIDENT_GENERAL' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;

        $arrDB  = ($reqID > 0 ?   $login->select('incident_regis',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="incident_regis" />';
        $file .= '<input type="hidden" name="frmID" value="5" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Incident Ref No : </b>'.$arrDB[0]['refno'].'</label></div>';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Incident Date : </b>'.$login->VdateFormat($arrDB[0]['dateID']).'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 5 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(5,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) 	/* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'inctypeID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 4 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'suburb')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT ID, slug FROM suburbs WHERE ID > 0 Order By slug ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        //$file .= '<option value="'.$row['ID'].'">'.$row['slug'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'wrtypeID' && ($FIndex->GET_SinglePermission('2') == 1))
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 23 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'staffID' || $fnameID == 'invID')
                                {

                                } 
                                else if($fnameID == 'disciplineID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">Yes</option>';
                                        $file .= '<option value="2">No</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                
                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 3)    /* CHECK - BOX - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <= 0)
                            {
                                $file .= '<div class="col-md-6">';
                                $file .= '<label style="color:red;">'.$arrayFT[0]['filedCP'].'</label><br />';
                                $file .= '<input class="icheckbox_minimal checked" type="checkbox" name="'.$fnameID.'" value="1" />';
                                $file .= '</div>';
                                
                                

                                
                                
                                $returnID += 1; 
                            }
                        }
                        
                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';

                                
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';

                                        
                                        
                                        $returnID += 1; 
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';

                                    
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                            $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                            $file .= '</div>';

                            
                            
                            $returnID += 1;
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Incident Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
    
    if($request == 'AUDIT_INCIDENT' && $reqID > 0)
    {
        extract($_POST);    //echo '<pre>'; echo print_r($_POST); exit;
        
        $arrDB  = ($reqID > 0 ?   $login->select('incident_regis',array("*"), " WHERE ID = ".$reqID." ")     : '');

        $file = "";
        $file  = '<form id="gen_form"><div class="box box-primary row" style="margin:auto">';			
        $file .= '<div class="row">&nbsp;</div>';		
        $file .= '<div class="alert alert-success alert-dismissable" id="successDiv" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';
        $file .= '<div class="alert alert-danger alert-dismissable" id="danger" style="display:none"><i class="fa fa-check"></i><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><b></b></div>';

        $file .= '<input type="hidden" name="request" value="UPDATE_AUDIT_TRIAL" />';
        $file .= '<input type="hidden" name="ID" value="'.$reqID.'" />';
        $file .= '<input type="hidden" name="tableNM" value="incident_regis" />';
        $file .= '<input type="hidden" name="frmID" value="10" />';

        $file .= '<div class="row" style="margin:auto;">';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Incident Ref No : </b>'.$arrDB[0]['refno'].'</label></div>';
        $file .= '<div class="col-md-6" style="margin-bottom:7px;"><label><b>Incident Date : </b>'.$login->VdateFormat($arrDB[0]['dateID']).'</label></div>';
        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        /* COUNT TABLE - FIELDS */
        $Qry = $CIndex->DB->prepare("SELECT * FROM frm_fields WHERE frmID = 10 AND visibleID = 1 Order By ID ASC ");
        $Qry->execute();
        $CIndex->Hrows = $Qry->fetchAll(PDO::FETCH_ASSOC);
        $countID = count($CIndex->Hrows);
        foreach($CIndex->Hrows as $Hrows)
        {
            $fieldNM .= $Hrows['filedNM'].', ';     $fieldTY .= $Hrows['ftypeID'].', ';
            $joinTB  .= $Hrows['tableFL'].', ';     $joinFN  .= $Hrows['tableFN'].', ';     $algnFL  .= $Hrows['alignFL'].', ';
            $fileID .= '<th style="background:#3C8DBC; color:white;">'.$Hrows['filedCP'].'</th>';
            $formNM = $Hrows['formNM'];
        }

        $flID = explode(",",$fieldNM);  $ftID = explode(",",$fieldTY);
        $jtID = explode(",",$joinTB);   $jfID = explode(",",$joinFN);
        $agID = explode(",",$algnFL);

        $returnID = 0;

        /* PRINT - GRID - MEDIAS */
        $Qry_D = $CIndex->DB->prepare($CIndex->frms_QryBuilders(10,$reqID));
        $Qry_D->execute();
        $CIndex->Drows = $Qry_D->fetchAll(PDO::FETCH_ASSOC);		
        if(is_array($CIndex->Drows) && count($CIndex->Drows) > 0)
        {
            foreach($CIndex->Drows as $Drow)
            {
                $startID = 1;
                for($srID = 1; $srID <= $countID; $srID++)
                {
                    $fnameID = trim($flID[$srID - 1]);				
                    $arrayFT  = ($fnameID <> '' ? $login->select('frm_fields',array("*"), " WHERE filedNM = '".$fnameID."' ")     : '');

                    if($Drow['ID'] == $reqID)
                    {
                        if(trim($ftID[$srID - 1]) == 1) /* TEXT - BOX */
                        {
                            if($Drow[$fnameID] <> '')   {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control" name="'.$fnameID.'" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';
                                
                                $returnID += 1;
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 2) 	/* SELECT - BOX - FIELDS  */
                        {
                            if(trim($Drow[$fnameID]) <= 0 || trim($Drow[$fnameID]) == '' || empty(trim($Drow[$fnameID])))
                            {
                                if($fnameID == 'weaponsID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 24 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'plcactionID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 25 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'accID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 20 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'creasonID')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 15 Order By title ASC ");
                                        $Qry->execute();
                                        $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($login->rows as $row)
                                        {
                                            $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                        }
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'suburb')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<select class="form-control" name="'.$fnameID.'">';
                                    $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                    $Qry = $login->DB->prepare("SELECT ID, slug FROM suburbs WHERE ID > 0 Order By slug ASC ");
                                    $Qry->execute();
                                    $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($login->rows as $row)
                                    {
                                        //$file .= '<option value="'.$row['ID'].'">'.$row['slug'].'</option>';
                                    }
                                    $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'wrtypeID' && ($FIndex->GET_SinglePermission('2') == 1))
                                {
                                    if($arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                            $file .= '<select class="form-control" name="'.$fnameID.'">';
                                            $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                            $Qry = $login->DB->prepare("SELECT * FROM master WHERE ID > 0 AND frmID = 23 Order By title ASC ");
                                            $Qry->execute();
                                            $login->rows = $Qry->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($login->rows as $row)
                                            {
                                                $file .= '<option value="'.$row['ID'].'">'.$row['title'].'</option>';
                                            }
                                            $file .= '</select>';
                                        $file .= '</div>';
                                    }
                                }
                                else if($fnameID == 'staffID' || $fnameID == 'invID')
                                {

                                }
                                else if($fnameID == 'optID_2' || $fnameID == 'optID_3')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">No</option>';
                                        $file .= '<option value="2">Swan</option>';
                                        $file .= '<option value="3">Police</option>';
                                        $file .= '<option value="4">Both</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                else if($fnameID == 'optID_1')
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<select class="form-control" name="'.$fnameID.'">';
                                        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select '.$arrayFT[0]['filedCP'].' --</option>';
                                        $file .= '<option value="1">Yes</option>';
                                        $file .= '<option value="2">No</option>';
                                        $file .= '</select>';
                                    $file .= '</div>';
                                }
                                
                                $returnID += 1; 
                            }
                        } 
                        
                        else if(trim($ftID[$srID - 1]) == 3)    /* CHECK - BOX - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <= 0)
                            {
                                $file .= '<div class="col-md-6">';
                                $file .= '<label style="color:red;">'.$arrayFT[0]['filedCP'].'</label><br />';
                                $file .= '<input class="icheckbox_minimal checked" type="checkbox" name="'.$fnameID.'" value="1" />';
                                $file .= '</div>';
                                
                                $returnID += 1; 
                            }
                        }
                        
                        else if(trim($ftID[$srID - 1]) == 4)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '' && $Drow[trim($flID[$srID - 1])] <> '0000-00-00')    {}  else
                            {
                                $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                $file .= '<input type="text" class="form-control datepicker" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                                $file .= '</div>';
                                
                                $returnID += 1; 
                            }
                        }

                        else if(trim($ftID[$srID - 1]) == 5)    /* TEXT - BOX - DATE - FIELDS  */
                        {
                            if($Drow[trim($flID[$srID - 1])] <> '')    {}  else
                            {
                                if($fnameID == 'mcomments')
                                {
                                    if($FIndex->GET_SinglePermission('1') == 1 && $arrDB[0]['disciplineID'] == 1)
                                    {
                                        $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                        $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                        $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                        $file .= '</div>';
                                        
                                        $returnID += 1;
                                    }
                                }
                                else
                                {
                                    $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                                    $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                                    $file .= '<textarea class="form-control" style="resize:none;" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'">'.$Drow[$fnameID].'</textarea>';
                                    $file .= '</div>';
                                    
                                    $returnID += 1; 
                                }
                            }
                        }

                        else 
                        {
                            $file .= '<div class="col-md-6" style="margin-bottom:7px;">';
                            $file .= '<label>'.$arrayFT[0]['filedCP'].'</label>';
                            $file .= '<input type="text" class="form-control" name="'.$fnameID.'" style="text-align:center;" placeholder="Enter '.$arrayFT[0]['filedCP'].'" value="'.$Drow[$fnameID].'" />';
                            $file .= '</div>';
                            
                            $returnID += 1;
                        }
                    } 
                }
            }
        }

        $file .= '<div class="col-xs-12"><hr style="border:#337AB7 1px solid;" /></div>';

        if($returnID > 0)
        {
            /* START - 1MODEL UPDAET BUTTONS */
            $file .= '<div class="col-md-6" style="margin-bottom:7px;"><button type="submit" class="btn btn-primary" id="gen_user">Update Incident Record</button></div>';			
            /* ENDSS - 1MODEL UPDAET BUTTONS */
        }
        else    {$file .= '<div class="col-xs-12"><b style="color:red;">No, Update are avilable</b></div>';}

        $file .= '<div class="col-md-12"><br /></div>';
        $file .= '</div>';
        $file .= '</div></form>';
        $FormHandle = '';
        $arr = array('file_info'=>$file,'form_handle'=>$FormHandle);
        echo json_encode($arr); 
    }
?>