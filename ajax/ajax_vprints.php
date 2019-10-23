<?PHP
    include_once '../includes.php';

    $request   =	isset($_POST['request'])	?	$_POST['request']       : '' ;	
    $data      =	array(); 

    if(($request == 'SPARE_DRIVERS'))
    {
        $file = '';
        $rand_series = rand(5, 15);
		
        /* PART - 1*/
        $file .= '<tr id="'.$rand_series.'">';
        $file .= '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o SpareGridID_1" aria-atomic="'.$rand_series.'"></span></td>';

        $file .= '<td width="250">';
        $file .= '<select class="form-control select2 SP_driverID" style="width:100%;" name="fieldID_1[]">';
        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select Driver --</option>';
        $file .= $CIndex->GET_Spares_Employees(0,"AND status = 1 AND desigID In(9,208,209) ");
        $file .= '</select>';
        $file .= '</td>';

        $file .= '<input type="hidden" class="SP_phoneNO" name="fieldID_2[]" />';
        $file .= '<input type="hidden" class="SP_phoneNO_1" name="fieldID_3[]" />';
        $file .= '<input type="hidden" class="SP_locationID" name="fieldID_4[]" />';
        $file .= '<input type="hidden" class="SP_suburbsID" name="fieldID_6[]" />';

        $file .= '<td width="140">';
        $file .= '<select class="form-control SP_avaiableID" style="width:100%;" name="fieldID_8[]">';
        $file .= '<option value="0" selected="selected" disabled="disabled">-- Select --</option>';                
        $file .= '<option value="1">After</option>';
        $file .= '<option value="2" selected="selected">Any Time</option>';
        $file .= '<option value="3">Available Untill</option>';
        $file .= '</select>';
        $file .= '</td>';

        $file .= '<td width="130"><input type="text" class="form-control SP_timeID TPicker" name="fieldID_7[]" readonly="readonly" placeholder="Time" style="text-align:center;"></td>';

        $file .= '</tr>';
			
        $data['result'] = $file;
    }
	
    if($request == 'SPARE_BUSNO')
    {
        $file = '';
        $rand_series = rand(5, 15);

        $file .= '<tr id="'.$rand_series.'">';
        $file .= '<td width="30" align="center"><span style="cursor:pointer;" class="fa fa-trash-o SpareGridID_2" aria-atomic="'.$rand_series.'"></span></td>';

        $file .= '<td width="200">';
        $file .= '<select class="form-control select2" style="width:100%;" name="fieldID_5[]">';
        /* AND companyID In(".$_SESSION[$Index->website]['compID'].") */
        $Qry = $Index->DB->prepare("SELECT * FROM buses WHERE ID > 0 Order By busno ASC");
        if($Qry->execute())
        {
            $Index->crow = $Qry->fetchAll(PDO::FETCH_ASSOC);
            $file .= '<option value="0" selected="selected" disabled="disabled"> --- Select Bus No --- </option>';
            foreach($Index->crow as $mrow)	
            {
                $file.= '<option value="'.$mrow['ID'].'">'.strtoupper($mrow['busno'].' - '.$mrow['modelno'].' - '.$mrow['title']).'</option>';
            }
        }		
        $file .= '</select>';
        $file .= '</td>';

        $file .= '</tr>';

        $data['result'] = $file;
    }
	
    echo json_encode($data);	
?>