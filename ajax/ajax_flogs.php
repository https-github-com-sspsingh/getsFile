<?PHP
    include_once '../includes.php';

    $reqID     =    isset($_POST['ID'])         ?   $_POST['ID']        : '' ;
    $frmID     =    isset($_POST['frmID'])      ?   $_POST['frmID']     : '' ;
    $request   =    isset($_POST['request'])    ?   $_POST['request']   : '' ;

    $styleID = '<div class="col-xs-6 row"><label class="col-xs-5 modal-label">';

    /* EMPLOYEE - DATA */
    if($request == 'FORM_logsID' && ($frmID == 37) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0    ? $login->select('employee',array("*"), " WHERE ID = ".$reqID." ") : '';
        $DS_arrayID = $PR_arrayID[0]['desigID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['desigID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['sid'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['sid']." ") : '';

        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'E. Code : </label><span class="col-xs-7">'.$PR_arrayID[0]['code'].'</span></div>';
        $file .= $styleID.'E. Name: </label><span class="col-xs-7">'.$PR_arrayID[0]['fname'].' '.$arrayID[0]['lname'].'</span></div>';
        $file .= $styleID.'D.O.B  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dob']).'</span></div>';
        $file .= $styleID.'Designation : </label><span class="col-xs-7">'.$DS_arrayID[0]['title'].'</span></div>';
        $file .= $styleID.'Address : </label><span class="col-xs-7">'.$PR_arrayID[0]['address_1'].' '.$PR_arrayID[0]['address_2'].'</span></div>';
        $file .= $styleID.'Suburb : </label><span class="col-xs-7">'.$SUB_Array[0]['title'].' '.$SUB_Array[0]['title'].'</span></div>';
        $file .= $styleID.'PostCode  : </label><span class="col-xs-7">'.$PR_arrayID[0]['pincode'].'</span></div>';
        $file .= $styleID.'Telephone  : </label><span class="col-xs-7">'.$PR_arrayID[0]['phone'].'</span></div>';
        $file .= $styleID.'Mobile No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['phone_1'].'</span></div>';
        $file .= $styleID.'Email ID  : </label><span class="col-xs-7">'.$PR_arrayID[0]['emailID'].'</span></div>';
        $file .= $styleID.'Driver\'s Licence No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['ddlcno'].'</span></div>';
        $file .= $styleID.'D-Licence Expiry Date  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['ddlcdt']).'</span></div>';
        $file .= $styleID.'DriveRight No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['drvrightID'].'</span></div>';
        $file .= $styleID.'RFID : </label><span class="col-xs-7">'.$PR_arrayID[0]['rfID'].'</span></div>';
        $file .= $styleID.'WWC Permit No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['wwcprno'].'</span></div>';
        $file .= $styleID.'WWC Expiry Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['wwcprdt']).'</span></div>';
        $file .= $styleID.'F/T Extension  : </label><span class="col-xs-7">'.$PR_arrayID[0]['ftextID'].'</span></div>';
        $file .= $styleID.'Letter of Authority Received Date  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['lardt']).'</span></div>';
        $file .= $styleID.'Air Key No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['arkno'].'</span></div>';
        $file .= $styleID.'Name of Kin  : </label><span class="col-xs-7">'.$PR_arrayID[0]['kinname'].'</span></div>';
        $file .= $styleID.'Kin Contact No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['kincno'].'</span></div>';
        $file .= $styleID.'Casual Full Time  : </label><span class="col-xs-7">'.($PR_arrayID[0]['casualID'] == 1 ? 'Full Time'  :($PR_arrayID[0]['casualID'] == 2 ? 'Part Time' :($PR_arrayID[0]['casualID'] == 3 ? 'Casual' : ''))).'</span></div>';
        $file .= $styleID.'Locker No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['lockerno'].'</span></div>';
        $file .= $styleID.'Employee Start Date  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['esdate']).'</span></div>';
        $file .= $styleID.'Casual Start Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['csdate']).'</span></div>';
        $file .= $styleID.'Full Time Start Date  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['ftsdate']).'</span></div>';
        $file .= $styleID.'End Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['enddate']).'</span></div>';
        $file .= '</div>';

        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    } 

    /* Comment Line Register */
    if($request == 'FORM_logsID' && ($frmID == 40) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0                    ? $login->select('complaint',array("*"), " WHERE ID = ".$reqID." ") : '';
        $CM_Array = $PR_arrayID[0]['creasonID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['creasonID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['suburb'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburb']." ") : '';
        $DR_Array = $PR_arrayID[0]['driverID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['driverID']." ") : '';
		$IV_Array = $PR_arrayID[0]['invID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['invID']." ") : '';
		
        $RS_Array = $PR_arrayID[0]['respID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['respID']." ") : '';
        $LT_Array = $PR_arrayID[0]['accID'] > 0         ? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['accID']." ") : '';
        $WR_Array = $PR_arrayID[0]['wrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['wrtypeID']." ") : '';

        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'Comment Line Ref No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
        $file .= $styleID.'Comment Received On: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['serDT']).'</span></div>';
        $file .= $styleID.'Incident Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';
        $file .= $styleID.'Incident Time : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';
        $file .= $styleID.'Bus No : </label><span class="col-xs-7">'.$PR_arrayID[0]['busID'].'</span></div>';
        $file .= $styleID.'Route No : </label><span class="col-xs-7">'.$PR_arrayID[0]['routeID'].'</span></div>';
        $file .= $styleID.'Location  : </label><span class="col-xs-7">'.$PR_arrayID[0]['location'].'</span></div>';
        $file .= $styleID.'Suburb  : </label><span class="col-xs-7">'.$SUB_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Customer\'s Name  : </label><span class="col-xs-7">'.$PR_arrayID[0]['cmp_name'].'</span></div>';
        $file .= $styleID.'Telephone No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['mobile'].'</span></div>';
        $file .= $styleID.'Customer Email ID  : </label><span class="col-xs-7">'.$PR_arrayID[0]['cmemailID'].'</span></div>';
        $file .= $styleID.'Address : </label><span class="col-xs-7">'.$PR_arrayID[0]['address'].'</span></div>';
        $file .= $styleID.'Comment Line Reason  : </label><span class="col-xs-7">'.$CM_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Driver Not Applicable : </label><span class="col-xs-7">'.($PR_arrayID[0]['tickID_1'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Description  : </label><span class="col-xs-7">'.$PR_arrayID[0]['description'].'</span></div>';
        $file .= $styleID.'Driver Name: </label><span class="col-xs-7">'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Driver ID  : </label><span class="col-xs-7">'.$PR_arrayID[0]['dcodeID'].'</span></div>';
        $file .= $styleID.'Comment Line Type  : </label><span class="col-xs-7">'.$LT_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Substantiated  : </label><span class="col-xs-7">'.($PR_arrayID[0]['substanID'] == 1 ? 'Yes'  :($PR_arrayID[0]['substanID'] == 2 ? 'No'  : '')).'</span></div>';

        $file .= $styleID.'Fault/Not at Fault : </label><span class="col-xs-7">'.(
          $PR_arrayID[0]['substanID'] == 1 ? ($PR_arrayID[0]['faultID'] == 1 ? 'At Fault - Driver'  
        :($PR_arrayID[0]['faultID'] == 2 ? 'At Fault - Engineering' :($PR_arrayID[0]['faultID'] == 3 ? 'At Fault - Operations' 
        :($PR_arrayID[0]['faultID'] == 4 ? 'Not At Fault' : '')))) :($PR_arrayID[0]['substanID'] == 2 ? ($PR_arrayID[0]['faultID'] == 4 ? 'Not Applicable' 
        :($PR_arrayID[0]['faultID'] == 5 ? 'Not At Fault' : '')) : '')
        ).'</span></div>';

        
		$file .= $styleID.'Interviewed By  : </label><span class="col-xs-7">'.$IV_Array[0]['fname'].' '.$IV_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Interviewed Date  : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['invdate']).'</span></div>';
        $file .= $styleID.'Customer Response Details: </label><span class="col-xs-7">'.$PR_arrayID[0]['furaction'].'</span></div>';
        $file .= $styleID.'Response Method  : </label><span class="col-xs-7">'.$RS_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Response Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['resdate']).'</span></div>';
        $file .= $styleID.'Action Taken / Recommendations  : </label><span class="col-xs-7">'.$PR_arrayID[0]['outcome'].'</span></div>';
        $file .= $styleID.'Closed : </label><span class="col-xs-7">'.($PR_arrayID[0]['statusID'] == 1 ? 'Y'  :($PR_arrayID[0]['statusID'] == 2 ? 'N'  : '')).'</span></div>';
        $file .= $styleID.'Discipline Required : </label><span class="col-xs-7">'.($PR_arrayID[0]['disciplineID'] == 1 ? 'Yes'  :($PR_arrayID[0]['disciplineID'] == 2 ? 'No'  : '')).'</span></div>';

        if($SIndex->GET_SinglePermission('1') == 1)
        {
            $file .= $styleID.'Manager Comments: </label><span class="col-xs-7">'.$PR_arrayID[0]['mcomments'].'</span></div>';
        }

        if($SIndex->GET_SinglePermission('2') == 1)
        {
            $file .= $styleID.'Warning Type  : </label><span class="col-xs-7">'.$WR_Array[0]['title'].'</span></div>';
        }
        $file .= '</div>';


        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    }

    /* Incident Register */
    if($request == 'FORM_logsID' && ($frmID == 41) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0    ? $login->select('incident_regis',array("*"), " WHERE ID = ".$reqID." ") : '';
        $WP_Array = $PR_arrayID[0]['weaponsID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['weaponsID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['suburb'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburb']." ") : '';
        $WR_Array = $PR_arrayID[0]['wrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['wrtypeID']." ") : '';
        $PL_Array = $PR_arrayID[0]['plcactionID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['plcactionID']." ") : '';
        $DR_Array = $PR_arrayID[0]['driverID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['driverID']." ") : '';
        $IN_Array = $PR_arrayID[0]['inctypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['inctypeID']." ") : '';
        $OF_Array = $PR_arrayID[0]['offtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['offtypeID']." ") : '';
        $GR_Array = $PR_arrayID[0]['grfitemID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['grfitemID']." ") : '';
        $FO_Array = $PR_arrayID[0]['offdtlsID'] > 0	? $login->select('offence',array("*"), " WHERE ID = ".$PR_arrayID[0]['offdtlsID']." ") : '';

		$AT_Array = $PR_arrayID[0]['actbyID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['actbyID']." ") : '';
		
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'Security Incident : </label><span class="col-xs-7">'.($PR_arrayID[0]['sincID'] == 1 ? 'Yes'  :($PR_arrayID[0]['sincID'] == 2 ? 'No'  : '')).'</span></div>';
        $file .= $styleID.'CMR No : </label><span class="col-xs-7">'.$PR_arrayID[0]['cmrno'].'</span></div>';
        $file .= $styleID.'Report Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['rpdateID']).'</span></div>';
        $file .= $styleID.'Ref No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
        $file .= $styleID.'Date Occured : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';
        $file .= $styleID.'Time Occured  : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';
        $file .= $styleID.'Driver Name  : </label><span class="col-xs-7">'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Driver ID  : </label><span class="col-xs-7">'.$PR_arrayID[0]['dcodeID'].'</span></div>';
        $file .= $styleID.'PTA Ref No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['pta_refNO'].'</span></div>';
        $file .= $styleID.'Bus No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['busID'].'</span></div>';
        $file .= $styleID.'Route No : </label><span class="col-xs-7">'.$PR_arrayID[0]['routeID'].'</span></div>';
        $file .= $styleID.'Shift No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['shiftID'].'</span></div>';
        $file .= $styleID.'Police : </label><span class="col-xs-7">'.($PR_arrayID[0]['plrefID'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Police Ref No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['plrefno'].'</span></div>';
        $file .= $styleID.'Damage Value: </label><span class="col-xs-7">'.$PR_arrayID[0]['dmvalue'].'</span></div>';
        $file .= $styleID.'Weapons : </label><span class="col-xs-7">'.$WP_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Location  : </label><span class="col-xs-7">'.$PR_arrayID[0]['location'].'</span></div>';
        $file .= $styleID.'Suburb : </label><span class="col-xs-7">'.$SUB_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Cross Station : </label><span class="col-xs-7">'.$PR_arrayID[0]['crossst'].'</span></div>';
        $file .= $styleID.'Reported By  : </label><span class="col-xs-7">'.$PR_arrayID[0]['reportby'].'</span></div>';
        $file .= $styleID.'Discipline Required  : </label><span class="col-xs-7">'.($PR_arrayID[0]['disciplineID'] == 1 ? 'Yes'  :($PR_arrayID[0]['disciplineID'] == 2 ? 'No'  : '')).'</span></div>';
        $file .= $styleID.'Damage/Injury  : </label><span class="col-xs-7">'.$PR_arrayID[0]['dmginjury'].'</span></div>';
        $file .= $styleID.'Offence Type  : </label><span class="col-xs-7">'.$OF_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Offence Details  : </label><span class="col-xs-7">'.$FO_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Graffiti Colour  : </label><span class="col-xs-7">'.$PR_arrayID[0]['grfcolour'].'</span></div>';
        $file .= $styleID.'Graffiti Item  : </label><span class="col-xs-7">'.$GR_Array[0]['title'].'</span></div>';
        $file .= $styleID.'What has been written  : </label><span class="col-xs-7">'.$PR_arrayID[0]['whbwdescription'].'</span></div>';
        $file .= $styleID.'Description : </label><span class="col-xs-7">'.$PR_arrayID[0]['description'].'</span></div>';
        $file .= $styleID.'Action: </label><span class="col-xs-7">'.$PR_arrayID[0]['action'].'</span></div>';

        if($SIndex->GET_SinglePermission('1') == 1)
        {
            $file .= $styleID.'Manager Comments: </label><span class="col-xs-7">'.$PR_arrayID[0]['mcomments'].'</span></div>';
        }

        if($SIndex->GET_SinglePermission('2') == 1)
        {
            $file .= $styleID.'Warning Type  : </label><span class="col-xs-7">'.$WR_Array[0]['title'].'</span></div>';
        }

        $file .= $styleID.'Action Taken By  : </label><span class="col-xs-7">'.$AT_Array[0]['fname'].' '.$AT_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Depot Notes : </label><span class="col-xs-7">'.$PR_arrayID[0]['depotnotes'].'</span></div>';
        $file .= $styleID.'Police CAD No : </label><span class="col-xs-7">'.$PR_arrayID[0]['plcadno'].'</span></div>';
        $file .= $styleID.'Police : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_2'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Police Vehicle  : </label><span class="col-xs-7">'.$PR_arrayID[0]['plcvehicle'].'</span></div>';
        $file .= $styleID.'Description of Damage  : </label><span class="col-xs-7">'.$PR_arrayID[0]['dscdamage'].'</span></div>';
        $file .= $styleID.'Police Name  : </label><span class="col-xs-7">'.$PR_arrayID[0]['policename'].'</span></div>';
        $file .= $styleID.'Police Action  : </label><span class="col-xs-7">'.$PL_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Radio : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_1'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Radio : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_1'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Transperth Security : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_3'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Transperth Security : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_3'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Fire Brigade : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_8'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Ambulance : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_9'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Duty Ops : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_4'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Duty Ops : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_4'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Depot Manager : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_5'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Depot Manager : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_5'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'PTA : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_6'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'PTA : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_6'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Westrail : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_7'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Westrail : </label><span class="col-xs-7">'.($PR_arrayID[0]['attendedID_7'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Video Footage Available : </label><span class="col-xs-7">'.($PR_arrayID[0]['notifiedID_8'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= '</div>';

        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    } 

    /* Accidents Register */
    if($request == 'FORM_logsID' && ($frmID == 42) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0    ? $login->select('accident_regis',array("*"), " WHERE ID = ".$reqID." ") : '';
        $AC_Array = $PR_arrayID[0]['acccatID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['acccatID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['suburb'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburb']." ") : '';
        $DR_Array = $PR_arrayID[0]['staffID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['staffID']." ") : '';
        $RS_Array = $PR_arrayID[0]['respID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['respID']." ") : '';
        $LT_Array = $PR_arrayID[0]['accID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['accID']." ") : '';
        $WR_Array = $PR_arrayID[0]['wrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['wrtypeID']." ") : '';
		$IV_Array = $PR_arrayID[0]['invID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['invID']." ") : '';

        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'Ref No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
        $file .= $styleID.'Bus No : </label><span class="col-xs-7">'.$PR_arrayID[0]['busID'].'</span></div>';
        $file .= $styleID.'Accident Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';
        $file .= $styleID.'Accident Time  : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';
        $file .= $styleID.'Trainee Accident  : </label><span class="col-xs-7">'.($PR_arrayID[0]['tickID_2'] == 1 ? 'Yes'  :($PR_arrayID[0]['tickID_2'] == 2 ? 'No'  : '')).'</span></div>';
        $file .= $styleID.'Driver Name: </label><span class="col-xs-7">'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Driver ID : </label><span class="col-xs-7">'.$PR_arrayID[0]['scodeID'].'</span></div>';
        $file .= $styleID.'Driver Not Applicable : </label><span class="col-xs-7">'.($PR_arrayID[0]['tickID_1'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Accident Category : </label><span class="col-xs-7">'.$AC_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Police Notified  : </label><span class="col-xs-7">'.($PR_arrayID[0]['plcntID'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Third Party : </label><span class="col-xs-7">'.($PR_arrayID[0]['3partyID'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Third Party Name  : </label><span class="col-xs-7">'.$PR_arrayID[0]['thpnameID'].'</span></div>';
        $file .= $styleID.'Third Party Rego No: </label><span class="col-xs-7">'.$PR_arrayID[0]['regisnoID'].'</span></div>';
        $file .= $styleID.'Third Party Contact Info : </label><span class="col-xs-7">'.$PR_arrayID[0]['thcontactID'].'</span></div>';
        $file .= $styleID.'Accident Details  : </label><span class="col-xs-7">'.$LT_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Driver Responsible  : </label><span class="col-xs-7">'.($PR_arrayID[0]['responsibleID'] == 1 ? 'Yes'  :($PR_arrayID[0]['responsibleID'] == 2 ? 'No'  : '')).'</span></div>';

        $file .= $styleID.'Insurer : </label><span class="col-xs-7">'.$PR_arrayID[0]['insurer'].'</span></div>';
        $file .= $styleID.'Claim No  : </label><span class="col-xs-7">'.$PR_arrayID[0]['claimno'].'</span></div>';
        $file .= $styleID.'Invoice No: </label><span class="col-xs-7">'.$PR_arrayID[0]['invno'].'</span></div>';
        $file .= $styleID.'Location  : </label><span class="col-xs-7">'.$PR_Array[0]['location'].'</span></div>';
        $file .= $styleID.'Suburb : </label><span class="col-xs-7">'.$SUB_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Reason : </label><span class="col-xs-7">'.$PR_arrayID[0]['description'].'</span></div>';
        $file .= $styleID.'Bus Repairs (Cost): </label><span class="col-xs-7">'.$PR_arrayID[0]['rprcost'].'</span></div>';
        $file .= $styleID.'Other Repairs (Cost): </label><span class="col-xs-7">'.$PR_arrayID[0]['othcost'].'</span></div>';
        $file .= $styleID.'Engineering Done  : </label><span class="col-xs-7">'.($PR_arrayID[0]['engdoneID'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Driver Drug Tested: </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_3'] == 1 ? 'No'  :($PR_arrayID[0]['optID_3'] == 2 ? 'Swan'  : ($PR_arrayID[0]['optID_3'] == 3 ? 'Police'  : ($PR_arrayID[0]['optID_3'] == 4 ? 'Both'  : '')))).'</span></div>';
        $file .= $styleID.'Photographs of Damage: </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_1'] == 1 ? 'Yes'  :($PR_arrayID[0]['optID_1'] == 2 ? 'No'  : '')).'</span></div>';
        $file .= $styleID.'Driver Drug Tested: </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_2'] == 1 ? 'No'  :($PR_arrayID[0]['optID_2'] == 2 ? 'Swan'  : ($PR_arrayID[0]['optID_2'] == 3 ? 'Police'  : ($PR_arrayID[0]['optID_2'] == 4 ? 'Both'  : '')))).'</span></div>';
        $file .= $styleID.'Investigation Outcome / Recommendations: </label><span class="col-xs-7">'.$PR_arrayID[0]['outcome'].'</span></div>';
        $file .= $styleID.'Interviewed BY : </label><span class="col-xs-7">'.$IV_Array[0]['fname'].' '.$IV_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Discipline Related: </label><span class="col-xs-7">'.($PR_arrayID[0]['disciplineID'] == 1 ? 'Yes'  :($PR_arrayID[0]['disciplineID'] == 2 ? 'No'  : '')).'</span></div>';
        if($SIndex->GET_SinglePermission('1') == 1)
        {
            $file .= $styleID.'Manager Comments: </label><span class="col-xs-7">'.$PR_arrayID[0]['mcomments'].'</span></div>';
        }

        if($SIndex->GET_SinglePermission('2') == 1)
        {
            $file .= $styleID.'Warning Type  : </label><span class="col-xs-7">'.$WR_Array[0]['title'].'</span></div>';
        }
        $file .= $styleID.'Operations Done: </label><span class="col-xs-7">'.($PR_arrayID[0]['oprdoneID'] == 1 ? 'YES' : 'No').'</span></div>';
        $file .= $styleID.'Progress: </label><span class="col-xs-7">'.($PR_arrayID[0]['progressID'] == 1 ? 'Complete'  :($PR_arrayID[0]['progressID'] == 2 ? 'Pending'  : ($PR_arrayID[0]['progressID'] == 3 ? 'Written Off'  : ''))).'</span></div>';
        $file .= '</div>';


        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    } 

    /* Infringement Details*/
    if($request == 'FORM_logsID' && ($frmID == 43) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0    ? $login->select('infrgs',array("*"), " WHERE ID = ".$reqID." ") : '';
        $AC_Array = $PR_arrayID[0]['acccatID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['acccatID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['suburb'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburb']." ") : '';
        $DR_Array = $PR_arrayID[0]['staffID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['staffID']." ") : '';
        $RS_Array = $PR_arrayID[0]['respID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['respID']." ") : '';
        $IN_Array = $PR_arrayID[0]['inftypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['inftypeID']." ") : '';
        $WR_Array = $PR_arrayID[0]['wrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['wrtypeID']." ") : '';
		$IV_Array = $PR_arrayID[0]['invID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['invID']." ") : '';
		
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'Infringement No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
        $file .= $styleID.'Vehicle Rego : </label><span class="col-xs-7">'.$PR_arrayID[0]['vehicle'].'</span></div>';
        $file .= $styleID.'Date Occur: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';
        $file .= $styleID.'Time  : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';
        $file .= $styleID.'Demerit Points Lost  : </label><span class="col-xs-7">'.$PR_arrayID[0]['dplostID'].'</span></div>';
        $file .= $styleID.'Bus No: </label><span class="col-xs-7">'.$PR_arrayID[0]['busID'].'</span></div>';
        $file .= $styleID.'Employee Name : </label><span class="col-xs-7">'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Issue Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID_1']).'</span></div>';
        $file .= $styleID.'Compliance Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID_2']).'</span></div>';
        $file .= $styleID.'Date Recieved : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID_3']).'</span></div>';
        $file .= $styleID.'Date Sent : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID_4']).'</span></div>';
        $file .= $styleID.'Employee ID: </label><span class="col-xs-7">'.$PR_arrayID[0]['stcodeID'].'</span></div>';
        $file .= $styleID.'Infringement Type : </label><span class="col-xs-7">'.$IN_Array[0]['title'].'</span></div>';
        $file .= $styleID.'If Other Infringement Type (Please Specify)  : </label><span class="col-xs-7">'.$PR_Array[0]['description'].'</span></div>';
        $file .= $styleID.'Location of Infringement  : </label><span class="col-xs-7">'.$PR_arrayID[0]['description_1'] .'</span></div>';

        $file .= $styleID.'Interviewed By : </label><span class="col-xs-7">'.$IV_Array[0]['fname'].' '.$IV_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Discipline Related: </label><span class="col-xs-7">'.($PR_arrayID[0]['disciplineID'] == 1 ? 'Yes'  :($PR_arrayID[0]['disciplineID'] == 2 ? 'No'  : '')).'</span></div>';
        if($SIndex->GET_SinglePermission('1') == 1)
        {
            $file .= $styleID.'Manager Comments: </label><span class="col-xs-7">'.$PR_arrayID[0]['mcomments'].'</span></div>';
        }

        if($SIndex->GET_SinglePermission('2') == 1)
        {
            $file .= $styleID.'Warning Type  : </label><span class="col-xs-7">'.$WR_Array[0]['title'].'</span></div>';
        }

        $file .= '</div>';


        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    } 

    /* Inspection Register*/
    if($request == 'FORM_logsID' && ($frmID == 44) && !empty($reqID))
    {
        $PR_arrayID = $reqID > 0    ? $login->select('inspc',array("*"), " WHERE ID = ".$reqID." ") : '';
        $INS_Array = $PR_arrayID[0]['insrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['insrtypeID']." ") : '';
        $SUB_Array = $PR_arrayID[0]['suburb'] > 0	? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburb']." ") : '';
        $DR_Array = $PR_arrayID[0]['empID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['empID']." ") : '';
        $FN_Array = $PR_arrayID[0]['fineID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['fineID']." ") : '';
        $IN_Array = $PR_arrayID[0]['inspectedby'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['inspectedby']." ") : '';
        $WR_Array = $PR_arrayID[0]['wrtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['wrtypeID']." ") : '';

        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

        $file .= $styleID.'Report No: </label><span class="col-xs-7">'.$PR_arrayID[0]['rptno'].'</span></div>';
        $file .= $styleID.'Report Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';
        $file .= $styleID.'Driver Name : </label><span class="col-xs-7">'.$DR_Array[0]['fname'].' '.$DR_Array[0]['lname'].'</span></div>';
        $file .= $styleID.'Driver ID  : </label><span class="col-xs-7">'.$PR_arrayID[0]['ecodeID'].'</span></div>';
        $file .= $styleID.'Inspection Result  : </label><span class="col-xs-7">'.$INS_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Fine: </label><span class="col-xs-7">'.$FN_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Inspected By : </label><span class="col-xs-7">'.$IN_Array[0]['title'].'</span></div>';
        $file .= $styleID.'Date Inspected : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID_1']).'</span></div>';
        $file .= $styleID.'Service Info : </label><span class="col-xs-7">'.$PR_arrayID[0]['serviceinfID'].'</span></div>';
        $file .= $styleID.'Shift No : </label><span class="col-xs-7">'.$PR_arrayID[0]['shiftID'].'</span></div>';
        $file .= $styleID.'Bus No : </label><span class="col-xs-7">'.$PR_arrayID[0]['busID'].'</span></div>';
        $file .= $styleID.'Scheduled Depature Time: </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID_1'].'</span></div>';
        $file .= $styleID.'Timing Point Time : </label><span class="col-xs-7">'.$PR_Array[0]['timeID_2'].'</span></div>';
        $file .= $styleID.'Description: </label><span class="col-xs-7">'.$PR_Array[0]['description'].'</span></div>';
        $file .= $styleID.'PTA Response: </label><span class="col-xs-7">'.$PR_arrayID[0]['description_2'] .'</span></div>';

        $file .= $styleID.'Further Action : </label><span class="col-xs-7">'.$PR_arrayID[0]['description_3'].'</span></div>';
        $file .= $styleID.'Discipline Related: </label><span class="col-xs-7">'.($PR_arrayID[0]['disciplineID'] == 1 ? 'Yes'  :($PR_arrayID[0]['disciplineID'] == 2 ? 'No'  : '')).'</span></div>';

        if($SIndex->GET_SinglePermission('1') == 1)
        {
            $file .= $styleID.'Manager Comments: </label><span class="col-xs-7">'.$PR_arrayID[0]['mcomments'].'</span></div>';
        }

        if($SIndex->GET_SinglePermission('2') == 1)
        {
            $file .= $styleID.'Warning Type  : </label><span class="col-xs-7">'.$WR_Array[0]['title'].'</span></div>';
        }

        $file .= $styleID.'Closed: </label><span class="col-xs-7">'.($PR_arrayID[0]['statusID'] == 1 ? 'Y'  :($PR_arrayID[0]['statusID'] == 2 ? 'N'  : '')).'</span></div>';


        $file .= '</div>';


        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    }
	
	/* SIR Register */
    if($request == 'FORM_logsID' && ($frmID == 130) && !empty($reqID))
    {
        $PR_arrayID  = $reqID > 0                    ? $login->select('sir_regis',array("*"), " WHERE ID = ".$reqID." ") : '';
        $SR_Array    = $PR_arrayID[0]['srtypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['srtypeID']." ") : '';
        $resultsINV  = $PR_arrayID[0]['resultsINV'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['resultsINV']." ") : '';
		$SIRIssuedTo = $PR_arrayID[0]['issuedTO'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['issuedTO']." ") : '';
		$Originator  = $PR_arrayID[0]['originatorID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['originatorID']." ") : '';
		
		$CompletedBy = $PR_arrayID[0]['invID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['invID']." ") : '';
		$ActionBy    = $PR_arrayID[0]['actID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['actID']." ") : '';
		$FollowupCompletedBy = $PR_arrayID[0]['fupcmpID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['fupcmpID']." ") : '';
		
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

		$file .= $styleID.'Issue Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['issuetoDT']).'</span></div>';	
        $file .= $styleID.'Improvement No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
        $file .= $styleID.'Procedure(s) : </label><span class="col-xs-7">'.$PR_arrayID[0]['prcedure'].'</span></div>';
		$file .= $styleID.'SIR Type  : </label><span class="col-xs-7">'.$SR_Array[0]['title'].'</span></div>';
		$file .= $styleID.'Description  : </label><span class="col-xs-7">'.$PR_arrayID[0]['description'].'</span></div>';		
		$file .= $styleID.'SIR Issue To : </label><span class="col-xs-7">'.$SIRIssuedTo[0]['fname'].' '.$SIRIssuedTo[0]['lname'].'</span></div>';
		$file .= $styleID.'Originator : </label><span class="col-xs-7">'.$Originator[0]['fname'].' '.$Originator[0]['lname'].'</span></div>';
		$file .= $styleID.'Investigation Results  : </label><span class="col-xs-7">'.($PR_arrayID[0]['resultsINV'] == 8000 ? 'Other' : $resultsINV[0]['title']).'</span></div>';
		$file .= $styleID.'Investigation Other  : </label><span class="col-xs-7">'.$PR_arrayID[0]['otherINV'].'</span></div>';
		$file .= $styleID.'Investigation Details : </label><span class="col-xs-7">'.$PR_arrayID[0]['descriptionINV'].'</span></div>';		
		$file .= $styleID.'Completed By : </label><span class="col-xs-7">'.$CompletedBy[0]['fname'].' '.$CompletedBy[0]['lname'].'</span></div>';
		$file .= $styleID.'Completed Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['invDate']).'</span></div>';			
		$file .= $styleID.'Action By : </label><span class="col-xs-7">'.$ActionBy[0]['fname'].' '.$ActionBy[0]['lname'].'</span></div>';
		$file .= $styleID.'Action Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['actDate']).'</span></div>';	
		$file .= $styleID.'Action  : </label><span class="col-xs-7">'.$PR_arrayID[0]['action'].'</span></div>';				
        $file .= $styleID.'Action Effective : </label><span class="col-xs-7">'.($PR_arrayID[0]['acteffID'] == 1 ? 'Yes' :($PR_arrayID[0]['acteffID'] == 2 ? 'No' : '')).'</span></div>';
		$file .= $styleID.'Follow-up Required : </label><span class="col-xs-7">'.($PR_arrayID[0]['fupreqID'] == 1 ? 'Yes' :($PR_arrayID[0]['fupreqID'] == 2 ? 'No' :($PR_arrayID[0]['fupreqID'] == 3 ? 'NA' : ''))).'</span></div>';
		$file .= $styleID.'Proposed Follow-up Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['fupreqDT']).'</span></div>';	
		$file .= $styleID.'Follow-Up Details   : </label><span class="col-xs-7">'.$PR_arrayID[0]['fupDesc'].'</span></div>';		
		$file .= $styleID.'Follow-up Completed By : </label><span class="col-xs-7">'.$FollowupCompletedBy[0]['fname'].' '.$FollowupCompletedBy[0]['lname'].'</span></div>';
		
		$file .= $styleID.'Follow-up Completed Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['fupcmpDT']).'</span></div>';	
		$file .= $styleID.'Improvement Close Out Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['clsoutDT']).'</span></div>';	
		$file .= $styleID.'Date Originator Advised : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['orgadvDT']).'</span></div>';	
		
        $file .= $styleID.'Status : </label><span class="col-xs-7">'.($PR_arrayID[0]['statusID'] == 1 ? 'Pending'  :($PR_arrayID[0]['statusID'] == 2 ? 'Complete'  : '')).'</span></div>';
        $file .= '</div>';


        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    }

	/* ST Fare Evasion Register */
    if($request == 'FORM_logsID' && ($frmID == 129) && !empty($reqID))
    {
        $PR_arrayID  = $reqID > 0                   	? $login->select('stfare_regis',array("*"), " WHERE ID = ".$reqID." ") : '';        
        $arrCM = $PR_arrayID[0]['companyID'] > 0		? $login->select('company',array("*"), " WHERE ID = ".$PR_arrayID[0]['companyID']." ") : '';		
		$arrDC = $PR_arrayID[0]['descriptionID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['descriptionID']." ") : '';		
		$arrSB = $PR_arrayID[0]['suburbID'] > 0			? $login->select('suburbs',array("*"), " WHERE ID = ".$PR_arrayID[0]['suburbID']." ") : '';
		$arrST = $PR_arrayID[0]['stopID'] > 0 			? $login->select('stops',array("title"), " WHERE ID = ".$PR_arrayID[0]['stopID']." ") : '';
		$arrRU = $PR_arrayID[0]['routenoID'] > 0 		? $login->select('srvdtls',array("*"), " WHERE ID = ".$PR_arrayID[0]['routenoID']." ") : '';
		
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

		$file .= $styleID.'Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';	
        $file .= $styleID.'Day : </label><span class="col-xs-7">'.$PR_arrayID[0]['dayNM'].'</span></div>';
		$file .= $styleID.'Shift NO : </label><span class="col-xs-7">'.$PR_arrayID[0]['shiftNO'].'</span></div>';
        $file .= $styleID.'Depot : </label><span class="col-xs-7">'.$arrCM[0]['title'].'</span></div>';		
		$file .= $styleID.'Route No : </label><span class="col-xs-7">'.$arrRU[0]['codeID'].'</span></div>';
		$file .= $styleID.'Route No Info : </label><span class="col-xs-7">'.$PR_arrayID[0]['routeInfo'].'</span></div>';
		$file .= $styleID.'Location : </label><span class="col-xs-7">'.$arrST[0]['title'].'</span></div>';		
		$file .= $styleID.'Time  : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';
		$file .= $styleID.'Suburb  : </label><span class="col-xs-7">'.$arrSB[0]['title'].' - '.$arrSB[0]['pscode'].'</span></div>';
		$file .= $styleID.'No of Fare Evaders : </label><span class="col-xs-7">'.$PR_arrayID[0]['nooffare'].'</span></div>';
		$file .= $styleID.'Description  : </label><span class="col-xs-7">'.$arrDC[0]['title'].'</span></div>';
		$file .= $styleID.'CMR Ref No   : </label><span class="col-xs-7">'.$PR_arrayID[0]['cmrrefNO'].'</span></div>';		
		$file .= $styleID.'General Comments  : </label><span class="col-xs-7">'.$PR_arrayID[0]['commentsGN'].'</span></div>';		
		
        $file .= '</div>';
		
        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    }
	
	/* HIZ Register */
    if($request == 'FORM_logsID' && ($frmID == 131) && !empty($reqID))
    {
        $PR_arrayID  = $reqID > 0                    ? $login->select('hiz_regis',array("*"), " WHERE ID = ".$reqID." ") : '';
        
		$arrJOB = $PR_arrayID[0]['jobID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['jobID']." ") : '';
		$arrSIR = $PR_arrayID[0]['hztypeID'] > 0	? $login->select('master',array("*"), " WHERE ID = ".$PR_arrayID[0]['hztypeID']." ") : '';
		$arrRPR	= $PR_arrayID[0]['reportBY'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['reportBY']." ") : '';
		$arrSTF	= $PR_arrayID[0]['fstaffID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['fstaffID']." ") : '';
		
		
		$arrINV	= $PR_arrayID[0]['invID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['invID']." ") : '';
		$arrABY	= $PR_arrayID[0]['actID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['actID']." ") : '';
		
		$FollowupCompletedBy = $PR_arrayID[0]['fupcmpID'] > 0	? $login->select('employee',array("*"), " WHERE ID = ".$PR_arrayID[0]['fupcmpID']." ") : '';
		
        $file = '';

        $file  = '<div class="box box-primary" style="margin:auto">';
        $file .= '<div class="row">&nbsp;</div>';
        $file .= '<div class="row" style="margin:auto">';

		$file .= $styleID.'HZ No : </label><span class="col-xs-7">'.$PR_arrayID[0]['refno'].'</span></div>';
		$file .= $styleID.'Report Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['rdateID']).'</span></div>';	        
		$file .= $styleID.'Date of Occurance : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['dateID']).'</span></div>';	        
		$file .= $styleID.'Time : </label><span class="col-xs-7">'.$PR_arrayID[0]['timeID'].'</span></div>';		
		$file .= $styleID.'JOB Title  : </label><span class="col-xs-7">'.$arrJOB[0]['title'].'</span></div>';
		$file .= $styleID.'Hazard Type  : </label><span class="col-xs-7">'.$arrSIR[0]['title'].'</span></div>';		
        $file .= $styleID.'Location : </label><span class="col-xs-7">'.$PR_arrayID[0]['location'].'</span></div>';
		$file .= $styleID.'Reported By : </label><span class="col-xs-7">'.$arrRPR[0]['fname'].' '.$arrRPR[0]['lname'].' - '.$arrRPR[0]['code'].'</span></div>';		
		$file .= $styleID.'Description  : </label><span class="col-xs-7">'.$PR_arrayID[0]['description'].'</span></div>';		
		$file .= $styleID.'Action Taken  : </label><span class="col-xs-7">'.$PR_arrayID[0]['descriptionACT'].'</span></div>';				
		$file .= $styleID.'Staff Name : </label><span class="col-xs-7">'.$arrRPR[0]['fname'].' '.$arrRPR[0]['lname'].' - '.$arrRPR[0]['code'].'</span></div>';		
		$file .= $styleID.'Designation  : </label><span class="col-xs-7">'.($PR_arrayID[0]['fdesigID'] == 1 ? 'Operations Manager' :($PR_arrayID[0]['fdesigID'] == 2 ? 'Workshop Manager' :($PR_arrayID[0]['fdesigID'] == 3 ? 'Area Manager' :($PR_arrayID[0]['fdesigID'] == 4 ? 'General Manager' :($PR_arrayID[0]['fdesigID'] == 5 ? 'Satefy & Quality Office' : ''))))).'</span></div>';
		$file .= $styleID.'Reciept Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['rcdateID']).'</span></div>';	        
		
		$file .= $styleID.'Unmanaged Likelihood  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u1'] == 10 ? 'May well be expected' :($PR_arrayID[0]['optID_u1'] == 6 ? 'Quite possible' :($PR_arrayID[0]['optID_u1'] == 3 ? 'Unusual but possible' :($PR_arrayID[0]['optID_u1'] == 1 ? 'Only remotely possible' : '')))).'</span></div>';		
		$file .= $styleID.'Unmanaged Exposure  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u3'] == 10 ? 'Continuous' 		  :($PR_arrayID[0]['optID_u3'] == 6 ? 'Daily' 		   :($PR_arrayID[0]['optID_u3'] == 3 ? 'Weekly' 			  :($PR_arrayID[0]['optID_u3'] == 1 ? 'Few per day' : '')))).'</span></div>';		
		$file .= $styleID.'Consequence/Impact  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u4'] == 1 ? 'Safety' :($PR_arrayID[0]['optID_u4'] == 2 ? 'Environmental' : '')).'</span></div>';
		
		if($PR_arrayID[0]['optID_u4'] == 1)
		{
			$file .= $styleID.'Secondary Choice  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u5'] == 10 ? 'Fatality or Permanent Disability' 
																				   :($PR_arrayID[0]['optID_u5'] == 6  ? 'Serious Injury/Loss Time Injury or Disease' 
																				   :($PR_arrayID[0]['optID_u5'] == 3  ? 'Medical Treated Injury or Disease' 
																				   :($PR_arrayID[0]['optID_u5'] == 1  ? 'First Aid Treatment (on site) or Work Injury or Disease Report' : '')))).'</span></div>';
		}
		else if ($PR_arrayID[0]['optID_u4'] == 2)
		{
			$file .= $styleID.'Secondary Choice  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u5'] == 10 ? 'Serious Environmental Harm' 
																				   :($PR_arrayID[0]['optID_u5'] == 6  ? 'Moderate Environmental Impact' 
																				   :($PR_arrayID[0]['optID_u5'] == 3  ? 'Minimal Environmental Harm' 
																				   :($PR_arrayID[0]['optID_u5'] == 1  ? 'No Environmental Harm' : '')))).'</span></div>';
		}
		
		$file .= $styleID.'Unmanaged Risk Category  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_u6'] == 1 ? 'Very High' :($PR_arrayID[0]['optID_u6'] == 2 ? 'High' :($PR_arrayID[0]['optID_u6'] == 3 ? 'MEDIUM' :($PR_arrayID[0]['optID_u6'] == 4 ? 'Low' :($PR_arrayID[0]['optID_u6'] == 5 ? 'Very Low' : ''))))).'</span></div>';
		$file .= $styleID.'Unmanaged Risk Score  : </label><span class="col-xs-7">'.$PR_arrayID[0]['optID_u7'].'</span></div>';

		$file .= $styleID.'Investigation : </label><span class="col-xs-7">'.$PR_arrayID[0]['descriptionINV'].'</span></div>';		
		$file .= $styleID.'Investigation By : </label><span class="col-xs-7">'.$arrINV[0]['fname'].' '.$arrINV[0]['lname'].' - '.$arrINV[0]['code'].'</span></div>';		
		$file .= $styleID.'Investigation Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['invDate']).'</span></div>';			
		
		$file .= $styleID.'Action : </label><span class="col-xs-7">'.$PR_arrayID[0]['descriptionACD'].'</span></div>';		
		$file .= $styleID.'Action By : </label><span class="col-xs-7">'.$arrABY[0]['fname'].' '.$arrABY[0]['lname'].' - '.$arrABY[0]['code'].'</span></div>';		
		$file .= $styleID.'Action Date: </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['actDate']).'</span></div>';			
		
		$file .= $styleID.'Managed Likelihood  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m1'] == 10 ? 'May well be expected' :($PR_arrayID[0]['optID_m1'] == 6 ? 'Quite possible' :($PR_arrayID[0]['optID_m1'] == 3 ? 'Unusual but possible' :($PR_arrayID[0]['optID_m1'] == 1 ? 'Only remotely possible' : '')))).'</span></div>';		
		$file .= $styleID.'Managed Exposure  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m3'] == 10 ? 'Continuous' 		  :($PR_arrayID[0]['optID_m3'] == 6 ? 'Daily' 		   :($PR_arrayID[0]['optID_m3'] == 3 ? 'Weekly' 			  :($PR_arrayID[0]['optID_m3'] == 1 ? 'Few per day' : '')))).'</span></div>';		
		$file .= $styleID.'Consequence/Impact  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m4'] == 1 ? 'Safety' :($PR_arrayID[0]['optID_m4'] == 2 ? 'Environmental' : '')).'</span></div>';

		if($PR_arrayID[0]['optID_m4'] == 1)
		{
			$file .= $styleID.'Secondary Choice  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m5'] == 10 ? 'Fatality or Permanent Disability' 
																				   :($PR_arrayID[0]['optID_m5'] == 6  ? 'Serious Injury/Loss Time Injury or Disease' 
																				   :($PR_arrayID[0]['optID_m5'] == 3  ? 'Medical Treated Injury or Disease' 
																				   :($PR_arrayID[0]['optID_m5'] == 1  ? 'First Aid Treatment (on site) or Work Injury or Disease Report' : '')))).'</span></div>';
		}
		else if ($PR_arrayID[0]['optID_m4'] == 2)
		{
			$file .= $styleID.'Secondary Choice  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m5'] == 10 ? 'Serious Environmental Harm' 
																				   :($PR_arrayID[0]['optID_m5'] == 6  ? 'Moderate Environmental Impact' 
																				   :($PR_arrayID[0]['optID_m5'] == 3  ? 'Minimal Environmental Harm' 
																				   :($PR_arrayID[0]['optID_m5'] == 1  ? 'No Environmental Harm' : '')))).'</span></div>';
		}

		$file .= $styleID.'Managed Risk Category  : </label><span class="col-xs-7">'.($PR_arrayID[0]['optID_m6'] == 1 ? 'Very High' :($PR_arrayID[0]['optID_m6'] == 2 ? 'High' :($PR_arrayID[0]['optID_m6'] == 3 ? 'MEDIUM' :($PR_arrayID[0]['optID_m6'] == 4 ? 'Low' :($PR_arrayID[0]['optID_m6'] == 5 ? 'Very Low' : ''))))).'</span></div>';
		$file .= $styleID.'Managed Risk Score  : </label><span class="col-xs-7">'.$PR_arrayID[0]['optID_m7'].'</span></div>';


        $file .= $styleID.'Action Effective : </label><span class="col-xs-7">'.($PR_arrayID[0]['act_effID'] == 1 ? 'Yes' :($PR_arrayID[0]['act_effID'] == 2 ? 'No' : '')).'</span></div>';
		$file .= $styleID.'Follow-up Required : </label><span class="col-xs-7">'.($PR_arrayID[0]['fupreqID'] == 1 ? 'Yes' :($PR_arrayID[0]['fupreqID'] == 2 ? 'No' :($PR_arrayID[0]['fupreqID'] == 3 ? 'NA' : ''))).'</span></div>';
		$file .= $styleID.'Proposed Follow-up Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['fupreqDT']).'</span></div>';	
		$file .= $styleID.'Follow-Up Details   : </label><span class="col-xs-7">'.$PR_arrayID[0]['fupDesc'].'</span></div>';		
		$file .= $styleID.'Follow-up Completed By : </label><span class="col-xs-7">'.$FollowupCompletedBy[0]['fname'].' '.$FollowupCompletedBy[0]['lname'].'</span></div>';
		
		$file .= $styleID.'Follow-up Completed Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['fupcmpDT']).'</span></div>';	
		$file .= $styleID.'Hazard Corrected Date : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['hzrconDT']).'</span></div>';	
		$file .= $styleID.'Date Employee Advised : </label><span class="col-xs-7">'.$login->VdateFormat($PR_arrayID[0]['empadvDT']).'</span></div>';	
		
        $file .= $styleID.'Status : </label><span class="col-xs-7">'.($PR_arrayID[0]['statusID'] == 1 ? 'Pending'  :($PR_arrayID[0]['statusID'] == 2 ? 'Complete'  : '')).'</span></div>';
        $file .= '</div>';
		
        $arr = array('file_info'=>$file);
        echo json_encode($arr);
    }
	
?>